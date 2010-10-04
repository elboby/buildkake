<?php

abstract class Adapter
{
  protected $name, $path, $params;
  
  public function __construct($name, $path, $params)
  {
    $this->name = $name;
    $this->path = $path;
    $this->params = $params;
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
  
  protected function log($text)
  {
    echo "$text\n";
  }
  
  abstract protected function getRequiredParams();
  abstract public function download();
  abstract public function check();
  abstract public function update();
  
}