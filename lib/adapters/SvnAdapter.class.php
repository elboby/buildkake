<?php

class SvnAdapter extends Adapter
{
  protected function getRequiredParams()
  {
    return array("url", "branch");
  }
  
  public function download()
  {
    $url = $this->params['url'].'/'.$this->params['branch'];
    
    $cmd = '';
    $cmd .= 'svn checkout '.$url.' '.$this->path.'/'.$this->name.';';
    
    $this->log($cmd);
    system($cmd);
  }
  
  public function update(){}
  public function checkConfigChanged(){}
  public function checkUpdateNeeded(){}
}