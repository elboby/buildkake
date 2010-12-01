<?php

abstract class Adapter
{
  protected 
    $name, 
    $path, 
    $params,
    $logger;
  
  public function __construct($name, $path, $params, $logger)
  {
    $this->name = $name;
    $this->path = $path;
    $this->params = $params;
    $this->logger = $logger;
    
    $this->full_path = $this->path.'/'.$this->name;
  }
  
  public function init()
  {
    $this->validateParams($this->getRequiredParams());
  }
  
  protected function validateParams($requiredParams)
  {
    $present_options = array_keys($this->params);
    
    $nb_intersect = count(array_intersect($requiredParams, $present_options));
    $nb = count($requiredParams);
    
    if( $nb_intersect > $nb )
    {
      throw new Exception('Adapter: missing params');
    }
  }
  
  public function checkIfPresent()
  {
    return file_exists($this->full_path);
  }
  
  public function cleanUp()
  {
    self::_system('rm -rf '.$this->full_path);
  }
  
  abstract protected function getRequiredParams();
  abstract public function download();
  abstract public function update();
  abstract public function checkConfigChanged();
  abstract public function checkUpdateNeeded();
     
  protected static function _system($cmd, &$return_var=0)
  {
    return system($cmd."", $return_var);
  }

  protected static function _system_cd($location, $cmd, &$return_var=0)
  {
    return self::_system('cd '.$location.' && '.$cmd."", $return_var);
  }
}