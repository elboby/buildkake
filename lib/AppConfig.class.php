<?php

class AppConfig
{
  protected 
    $user_file,
    $default_file,
    $values;
  
  public function __construct($default_file, $user_file=null)
  {
    $this->default_file = $default_file;
    $this->user_file = $user_file;
    $this->values = array();
  }
  
  public function init()
  {
    //load user local file if exists
    if( $this->user_file && is_readable($this->user_file) )
    {
      $this->loadIniFile($this->user_file);
    }
    
    //load default files
    $this->loadIniFile($this->default_file);
  }
  
  protected function loadIniFile($file)
  {
    $this->values = array_merge( parse_ini_file($file, true), $this->values );
    //var_dump($array);die;
  }
  
  public function getArrayFor($category)
  {
    if( isset($this->values[$category]) )
    {
      return $this->values[$category];
    }
    
    return array();
  }
}