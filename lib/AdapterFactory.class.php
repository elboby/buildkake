<?php

include(__DIR__.'/Adapter.class.php');
include(__DIR__.'/adapters/SvnAdapter.class.php');
include(__DIR__.'/adapters/GitAdapter.class.php');

class AdapterFactory
{
  public static function getInstanceFor($type, $name, $path, $params)
  {
    if($type=="svn")     return new SvnAdapter($name, $path, $params);
    elseif($type=="git") return new GitAdapter($name, $path, $params);
    else throw new Exception("AdapterFactory: no adapter found for $type");
  }
}