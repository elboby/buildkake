<?php

class SvnAdapter extends Adapter
{
  protected $url, $branch;
  
  public function __construct($name, $path, $params, $logger)
  {
    parent::__construct($name, $path, $params, $logger);
      
    if(isset($this->params['url']))     $this->url = $this->params['url'];
    if(isset($this->params['branch']))  $this->branch = $this->params['branch'];
  }
  
  protected function getRequiredParams()
  {
    return array("url", "branch");
  }
  
  public function download()
  {
    $url = $this->params['url'].'/'.$this->params['branch'];
    self::_system('svn checkout '.$url.' '.$this->path.'/'.$this->name);
  }
  
  public function checkConfigChanged()
  {
    //check if svn repo or not
    if(!file_exists($this->path.'/'.$this->name.'/.svn'))
    {
      return true;
    }
    
    //url from config
    $url_config = $this->params['url'].'/'.$this->params['branch'];
    //url from file
    $out = self::_system('svn info '.$this->path.'/'.$this->name.' | grep "URL"');
    $url_file = substr($out, 5);
    if($url_file != $url_config)
    { 
      return true;
    }
    
    return false;
  }
  
  
  public function checkUpdateNeeded()
  {
    $rev_head = self::_system('svn info -r HEAD '.$this->params['url'].'/'.$this->params['branch'].' | grep -i "Last Changed Rev"');
    $rev_wc = self::_system('svn info '.$this->path.'/'.$this->name.' | grep -i "Last Changed Rev"');
    
    if($rev_head != $rev_wc)
    {
      return true;
    }    
    
    return false;
  }
  
  public function update()
  {
    self::_system('svn update '.$this->path.'/'.$this->name);
  }
}