<?php

class Logger
{
  const ERROR   = 'ERROR';
  const WARNING = 'WARNING';
  const DEBUG   = 'DEBUG';
  const INFO    = 'INFO';
  
  protected $options;
  protected $file_path;
  private $fp;
  
  public function __construct($options)
  {
    $this->options = $options;
  }

  public function init()
  {
    //check if present
    if( !isset($this->options['file_path']) )
    {
      throw new Exception('Logger: missing configuration for file_path');
    }
    $this->file_path = $this->options['file_path'];
    
    //check if writeable
    if( !is__writable($this->file_path) )
    {
      throw new Exception('Logger: unable to write log to '.$this->file_path);
    }
  }
  
  public function write($type, $text)
  {
    if(!$this->fp)
    {
      $this->fp = fopen($this->file_path, 'a+');
    }
    
    $text = date('Y-m-d H:i:s').' '.$type.' '.$text."\n";
    fwrite($this->fp, $text);
  }
  
  public function error($text)
  {
    $this->write(self::ERROR, $text);
  }
  
  public function warning($text)
  {
    $this->write(self::WARNING, $text);
  }
  
  public function debug($text)
  {
    $this->write(self::DEBUG, $text);
  }
  
  public function info($text)
  {
    $this->write(self::INFO, $text);
  }
  
  public function __destruct()
  {
    if($this->fp)
    {
      fclose($this->fp);
    }
  }
}