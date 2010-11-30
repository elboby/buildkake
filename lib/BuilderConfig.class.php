<?php

class BuilderConfig
{
  protected $file;
  protected $project_path, $config;
  protected $name, $version, $deps;
  
  public function __construct($file)
  {
    $this->file = $file;
  }
  
  public function init()
  {
    //test if file is readable
    if(!is_readable($this->file)) throw new Exception('BuildkakeConfig: file path not readable: '.$this->file);
    $this->project_path = dirname(realpath($this->file));
    
    //include the configuration
    require($this->file);
    
    //check values inside are ok
    if(!isset($config)) throw new Exception('BuildkakeConfig: missing "$config" variable in configuration file');
    $this->config = $config;
    
    //mandatory params
    if(!isset($this->config["deps"])) throw new Exception('BuildkakeConfig: missing dependencies definition in configuration (key "deps")');
    $this->deps = $this->config["deps"];
    if(!isset($this->config["name"])) throw new Exception('BuildkakeConfig: missing dependencies definition in configuration (key "name")');
    $this->name = $this->config["name"];
    if(!isset($this->config["version"])) throw new Exception('BuildkakeConfig: missing dependencies definition in configuration (key "version")');
    $this->version = $this->config["version"];
  }
  
  public function getConfigArray(){ return $this->config; }
  public function getProjectPath(){ return $this->project_path; }
  public function getDeps(){        return $this->deps; }
  public function getName(){        return $this->name; }
  public function getVersion(){     return $this->version; }
}