<?php

class GitAdapter extends Adapter
{
  protected $url;
  protected $branch, $tag;
  
  public function __construct($name, $path, $params, $logger)
  {
    parent::__construct($name, $path, $params, $logger);
      
    if(isset($this->params['url']))     $this->url = $this->params['url'];
    if(isset($this->params['branch']))  $this->branch = $this->params['branch'];
    $this->branch_path = $this->path.'/'.$this->name;
    
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
    $this->logger->info('GitAdapter: downloading url: '.$this->url);
    self::_system('git clone '.$this->url.' '.$this->branch_path); 
    
    if( $this->branch != "master" )
    {
    	$this->logger->info('GitAdapter: tracking remote branch: '.$this->branch);
      self::_system_cd($this->branch_path, 'git fetch origin');
    	self::_system_cd($this->branch_path, 'git branch --track '.$this->branch.' origin/'.$this->branch);
    }
    
    self::_system_cd($this->branch_path, 'git checkout '.$this->branch);
  	self::_system_cd($this->branch_path, 'git pull');
    	
    self::_system_cd($this->branch_path, 'git submodule init');
    self::_system_cd($this->branch_path, 'git submodule update');
  }
  
  public function update()
  {
    self::_system_cd($this->branch_path, 'git checkout '.$this->branch);
    self::_system_cd($this->branch_path, 'git fetch');
    
    $origin = 'origin';
    if( $this->branch != "master")
    {
      $origin .= '/'.$this->branch;
    }
    
    self::_system_cd($this->branch_path, 'git merge '.$origin);
  }
  
  public function checkConfigChanged()
  {
    //check if it is a git working copy
    if( !file_exists($this->path.'/'.$this->name.'/.git') )
    {
      return true;
    }
    
    //check if url is the same
    $output = self::_system_cd($this->branch_path, 'git remote -v | grep "(fetch)"');
    preg_match('/origin ?(.*) \(fetch\)/', $output, $matches);
    if( $this->url != trim($matches[1]) )
    {
      return true;
    }
        
    //check if branch/tag is the same
    $output = self::_system_cd($this->branch_path, 'git branch | grep "*"');
    preg_match('/\* ?(.*)/', $output, $matches);
    if( $this->branch != trim($matches[1]) )
    {
      return true;
    }
        
    return false;
  }
  
  public function checkUpdateNeeded()
  {
    self::_system_cd($this->branch_path, 'git checkout '.$this->branch);
    self::_system_cd($this->branch_path, 'git fetch');
    
    $origin = 'origin';
    if( $this->branch != "master")
    {
      $origin .= '/'.$this->branch;
    }
    
    $output = self::_system_cd($this->branch_path, 'git diff HEAD..'.$origin.' --name-only');
    if($output!="")
    {
      return true;
    }
    
    return false;
  }
}