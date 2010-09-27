<?php

class SvnAdapter extends Adapter
{
  protected function getRequiredParams()
  {
    return array("url", "branch");
  }
  
  public function process()
  {
    $url = $this->params['url'].'/'.$this->params['branch'];
    
    $cmd = '';
    $cmd .= 'svn checkout '.$url.' '.$this->path.'/'.$this->name.';';
    
    $this->log($cmd);
    system($cmd);
  }
}