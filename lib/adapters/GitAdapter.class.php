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
    if(isset($this->params['tag']))     $this->tag = $this->params['tag'];
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

    if($this->isRemote())
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
  
  public function check(){}
  public function update(){}
}