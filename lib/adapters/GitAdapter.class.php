<?php

class GitAdapter extends Adapter
{
  protected $url;
  protected $branch, $tag, $is_tag;
  
  public function __construct($name, $path, $params, $logger)
  {
    parent::__construct($name, $path, $params, $logger);
      
    //mandatory: repo url
    if(isset($this->params['url']))     $this->url = $this->params['url'];
    
    //mandatory: branch or tag 
    $this->is_tag = false;
    if(isset($this->params['branch']))
    {
      $this->branch = $this->params['branch'];
    }
    elseif(isset($this->params['tag']))
    {
      $this->branch = 'tag/'.$this->params['tag'];
      $this->tag = $this->params['tag'];
      $this->is_tag = true;
    }
    else
    {
      $this->branch = 'master';
    }
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
    self::_system('git clone '.$this->url.' '.$this->full_path); 
    
    if( $this->is_tag )
    {
      $this->logger->info('GitAdapter: tracking a tag');
      self::_system_cd($this->full_path, 'git fetch');
      self::_system_cd($this->full_path, 'git fetch --tags');
    	self::_system_cd($this->full_path, 'git checkout -b '.$this->branch.' '.$this->tag);
    }
    else
    {
      if( $this->branch != "master" )
      {
      	$this->logger->info('GitAdapter: tracking remote branch: '.$this->branch);
        self::_system_cd($this->full_path, 'git fetch origin');
      	self::_system_cd($this->full_path, 'git branch --track '.$this->branch.' origin/'.$this->branch);
      }
    
      self::_system_cd($this->full_path, 'git checkout '.$this->branch);
    	self::_system_cd($this->full_path, 'git pull');
    }
    	
    self::_system_cd($this->full_path, 'git submodule init');
    self::_system_cd($this->full_path, 'git submodule update');
  }
  
  public function update()
  {
    $this->logger->info('GitAdapter: updating branch: '.$this->branch);
    self::_system_cd($this->full_path, 'git checkout '.$this->branch);
    self::_system_cd($this->full_path, 'git fetch');
    
    $origin = 'origin';
    if( $this->branch != "master")
    {
      $origin .= '/'.$this->branch;
    }
    
    self::_system_cd($this->full_path, 'git merge '.$origin);
  }
  
  public function checkConfigChanged()
  {
    //check if it is a git working copy
    if( !file_exists($this->path.'/'.$this->name.'/.git') )
    {
      $this->logger->info('GitAdapter: config changed, not a git repo');
      return true;
    }
    
    //check if url is the same
    $output = self::_system_cd($this->full_path, 'git remote -v | grep "(fetch)"');
    preg_match('/origin ?(.*) \(fetch\)/', $output, $matches);
    if( $this->url != trim($matches[1]) )
    {
      $this->logger->info('GitAdapter: config changed, not the same repo url');
      return true;
    }
        
    //check if branch/tag is the same
    $output = self::_system_cd($this->full_path, 'git branch | grep "*"');
    preg_match('/\* ?(.*)/', $output, $matches);
    if( $this->branch != trim($matches[1]) )
    {
      $this->logger->info('GitAdapter: config changed, not the same branch');
      return true;
    }
        
    $this->logger->info('GitAdapter: config NOT changed');
    return false;
  }
  
  public function checkUpdateNeeded()
  {
    self::_system_cd($this->full_path, 'git checkout '.$this->branch);
    self::_system_cd($this->full_path, 'git fetch');
    
    $origin = 'origin';
    if( $this->branch != "master")
    {
      $origin .= '/'.$this->branch;
    }
    
    $output = self::_system_cd($this->full_path, 'git diff HEAD..'.$origin.' --name-only');
    if($output!="")
    {
      $this->logger->info('GitAdapter: update needed, late: '.$output);
      return true;
    }
    
    $this->logger->info('GitAdapter: update NOT needed');
    return false;
  }
}