<?php

class GitAdapter extends Adapter
{
  protected $url;
  protected $branch, $tag;
  
  public function __construct($name, $path, $params)
  {
    parent::__construct($name, $path, $params);
      
    if(isset($this->params['url']))     $this->url = $this->params['url'];
    if(isset($this->params['branch']))  $this->branch = $this->params['branch'];
    
    //TODO handle git tags
    //if(isset($this->params['tag']))     $this->tag = $this->params['tag'];
  }
  
  protected function getRequiredParams()
  {
    return array("url");
  }
  
  protected function isRemote()
  {
    return (strpos($this->url, '@')!==false);
  }
  
  public function download()
  {
    $cmd = '';
    $cmd .= 'git clone '.$this->url.' '.$this->path.'/'.$this->name.' &&'."\n"; 
    
    $cmd .= 'cd '.$this->path.'/'.$this->name.' &&'."\n";

    if( $this->branch != "master")//$this->isRemote())
    {
    	$cmd .= 'git fetch origin &&'."\n";
    	$cmd .= 'git branch --track '.$this->branch.' origin/'.$this->branch.' &&'."\n";
    }
    
    $cmd .= 'git checkout '.$this->branch.' &&'."\n";
  	$cmd .= 'git pull;';
    	
    $cmd .= 'git submodule init &&'."\n";
    $cmd .= 'git submodule update'."\n";
    
    $this->log($cmd);
    
    $output = system($cmd, $return);
    var_dump($output);
    var_dump($return);
    echo "-----\n";
  }
  
  public function update()
  {
    $cmd = 'cd '.$this->path.'/'.$this->name.' &&'."\n";

    $cmd .= 'git checkout '.$this->branch.' &&'."\n";
    $cmd .= 'git fetch &&'."\n";
    
    $origin = 'origin';
    if( $this->branch != "master")
    {
      $origin .= '/'.$this->branch;
    }
    
    $cmd .= 'git merge '.$origin."\n";
    
    $this->log($cmd);
    
    $output = system($cmd, $return);
    var_dump($output);
    var_dump($return);
    echo "-----\n";
  }
  
  public function checkConfigChanged()
  {
    //check if it is a git working copy
    if(!file_exists($this->path.'/'.$this->name.'/.git'))
    {
      return true;
    }
    
    //check if url is the same
    $cmd = 'cd '.$this->path.'/'.$this->name.' &&'."\n";
    $cmd .= 'git remote -v | grep "(fetch)"'."\n";
    $output = system($cmd, $return);
    preg_match('/origin ?(.*) \(fetch\)/', $output, $matches);
    var_dump(trim($matches[1]));
    echo "-----\n";
    if( $this->url != trim($matches[1]) )
    {
      return true;
    }
        
    //check if branch/tag is the same
    $cmd = 'cd '.$this->path.'/'.$this->name.' &&'."\n";
    $cmd .= 'git branch | grep "*"'."\n";
    $output = system($cmd, $return);
    preg_match('/\* ?(.*)/', $output, $matches);
    var_dump(trim($matches[1]));
    echo "-----\n";
    if( $this->branch != trim($matches[1]) )
    {
      return true;
    }
        
    return false;
  }
  
  public function checkUpdateNeeded()
  {
    $cmd = 'cd '.$this->path.'/'.$this->name.' &&'."\n";

    $cmd .= 'git checkout '.$this->branch.' &&'."\n";
    $cmd .= 'git fetch &&'."\n";
    
    $origin = 'origin';
    if( $this->branch != "master")
    {
      $origin .= '/'.$this->branch;
    }
    
    $cmd .= 'git diff HEAD..'.$origin.' --name-only'."\n";
    
    $this->log($cmd);
    
    $output = system($cmd, $return);
    var_dump($output);
    var_dump($return);
    echo "-----\n";
    
    if($output!="") return true;
    
    return false;
  }
}