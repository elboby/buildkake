<?php

class SvnAdapter extends Adapter
{
  protected $url, $branch;
  
  public function __construct($name, $path, $params, $logger)
  {
    parent::__construct($name, $path, $params, $logger);
      
    if(isset($this->params['url']))     $this->url = $this->params['url'];
    if(isset($this->params['branch']))  $this->branch = $this->params['branch'];
    
    $this->url = $this->params['url'].'/'.$this->params['branch'];
  }
  
  protected function getRequiredParams()
  {
    return array("url", "branch");
  }
  
  public function download()
  {
    $this->logger->info('SvnAdapter: downloading from '.$this->url);
    self::_system('svn checkout '.$this->url.' '.$this->path.'/'.$this->name);
  }
  
  public function checkConfigChanged()
  {
    //check if svn repo or not
    if(!file_exists($this->path.'/'.$this->name.'/.svn'))
    {
      $this->logger->info('SvnAdapter: config changed, not a svn repo');
      return true;
    }
    
    //url from file
    $out = self::_system('svn info '.$this->path.'/'.$this->name.' | grep "URL"');
    $url_file = substr($out, 5);
    if($url_file != $this->url)
    { 
      $this->logger->info('SvnAdapter: config changed, not the same url repo');
      return true;
    }
    
    $this->logger->info('SvnAdapter: config NOT changed');
    return false;
  }
  
  
  public function checkUpdateNeeded()
  {
    $rev_head = self::_system('svn info -r HEAD '.$this->params['url'].'/'.$this->params['branch'].' | grep -i "Last Changed Rev"');
    $rev_wc = self::_system('svn info '.$this->path.'/'.$this->name.' | grep -i "Last Changed Rev"');
    
    if($rev_head != $rev_wc)
    {
      $this->logger->info('SvnAdapter: update needed, between '.$rev_wc.' and '.$rev_head);
      return true;
    }    
    
    $this->logger->info('SvnAdapter: update NOT needed');
    return false;
  }
  
  public function update()
  {
    $this->logger->info('SvnAdapter: updating');
    self::_system('svn update '.$this->path.'/'.$this->name);
  }
}