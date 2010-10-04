<?php

class BuildkakeCore
{
  protected $deps, $project_path;
  
  public function __construct($project_path, $deps)
  {
    $this->deps = $deps;
    $this->project_path = $project_path;
  }
  
  public function init()
  {}
  
  public function process()
  {
    //going through the dependencies and call right adapter
    foreach($this->deps as $key=>$group)
    {
      echo "Building the group: $key...\n";
  
      //building the path
      if(!isset($group["path"])) errorMessage("missing path definition in configuration for $key (key 'path')");
      $group_path = $this->project_path."/".$group["path"];
      echo ("mkdir -p ".$group_path."\n");      
      system("mkdir -p ".$group_path);
  
      //retrieving data for those inside
      if(!isset($group["items"])) errorMessage("missing items definition in configuration for $key (key 'items')");
      foreach($group["items"] as $i=>$item)
      {
        if(!isset($item["type"])) errorMessage("missing type definition in configuration for $key/$i (key 'type')");
        $adapter = AdapterFactory::getInstanceFor($item["type"], $item['name'], $group_path, $item);
        $adapter->init();
    
        //TODO add path to ignore file for git/svn
        $ignorefile = $this->project_path."/".".gitignore";
        $path = "/".$group["path"]."/".$item['name']."/*";
        system("echo \"$path\" >> $ignorefile");
  
        //check for updates
    
        //download updates
        $adapter->download();
    
        //var_dump($item);
      }
    }
  }
}