<?php

class BuilderCore
{
  protected $deps, $project_path;
  
  public function __construct($project_path, $deps)
  {
    $this->deps = $deps;
    $this->project_path = $project_path;
  }
  
  public function init()
  {}
  
  protected function log($text)
  {
    echo "BuildkakeCore: $text\n";
  }
  
  public function process()
  {
    //going through the dependencies and call right adapter
    foreach($this->deps as $key=>$group)
    {
      $this->log("Building the group: $key...");
  
      //building the path
      if(!isset($group["path"])) throw new Exception("missing path definition in configuration for $key (key 'path')");
      $group_path = $this->project_path."/".$group["path"];
      $this->log("mkdir -p ".$group_path);      
      system("mkdir -p ".$group_path);
  
      //retrieving data for those inside
      if(!isset($group["items"])) throw new Exception("missing items definition in configuration for $key (key 'items')");
      foreach($group["items"] as $i=>$item)
      {
        if(!isset($item["type"])) throw new Exception("missing type definition in configuration for $key/$i (key 'type')");
        $adapter = AdapterFactory::getInstanceFor($item["type"], $item['name'], $group_path, $item);
        $adapter->init();
    
        //TODO add path to ignore file for git/svn
        $ignorefile = $this->project_path."/".".gitignore";
        $path = "/".$group["path"]."/".$item['name']."/*";
        system("echo \"$path\" >> $ignorefile");
  
        //check if present or not
        if(!$adapter->checkIfPresent())
        {
          $this->log($group["path"]."/".$item['name'].' not present, downloading...');
          $adapter->download();
        }
        //check if config changed
        elseif($adapter->checkConfigChanged())
        {
          $this->log($group["path"]."/".$item['name'].' configuration changed, downloading...');
          $adapter->cleanUp();
          $adapter->download();
        }
        //check if updates are available
        elseif($adapter->checkUpdateNeeded())
        {
          $this->log($group["path"]."/".$item['name'].' update needed, updating...');
          $adapter->update();
        }
        else
        {
          $this->log($group["path"]."/".$item['name'].' up to date, nothing to be done');
        }  
        
        //var_dump($item);
      }
    }
  }
}