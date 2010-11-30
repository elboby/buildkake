<?php

include(__DIR__.'/Adapter.class.php');
include(__DIR__.'/adapters/SvnAdapter.class.php');
include(__DIR__.'/adapters/GitAdapter.class.php');

class AdapterFactory
{
  public static function getInstanceFor($type, $name, $path, $params, $logger)
  {
    if($type=="svn")
    {
      $logger->info('AdapterFactory: creating instance of SvnAdapter for '.$name);
      return new SvnAdapter($name, $path, $params, $logger);
    }
    elseif($type=="git")
    {
      $logger->info('AdapterFactory: creating instance of GitAdapter for '.$name);
      return new GitAdapter($name, $path, $params, $logger);
    }
    else
    {
      throw new Exception("AdapterFactory: no adapter found for $type");
    }
  }
}