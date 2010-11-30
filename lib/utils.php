<?php

function errorMessage($msg)
{
  echo "Error: ".$msg."\n";
  printHelp();
  exit -1;
}

function printHelp()
{
  echo "usage is: buildkake configfile.php\n";
}

function parseParameters($noopt = array()) 
{
  $result = array();
  $params = $GLOBALS['argv'];
  // could use getopt() here (since PHP 5.3.0), but it doesn't work relyingly
  reset($params);
  while (list($tmp, $p) = each($params)) {
      if ($p{0} == '-') {
          $pname = substr($p, 1);
          $value = true;
          if ($pname{0} == '-') {
              // long-opt (--<param>)
              $pname = substr($pname, 1);
              if (strpos($p, '=') !== false) {
                  // value specified inline (--<param>=<value>)
                  list($pname, $value) = explode('=', substr($p, 2), 2);
              }
          }
          // check if next parameter is a descriptor or a value
          $nextparm = current($params);
          if (!in_array($pname, $noopt) && $value === true && $nextparm !== false && $nextparm{0} != '-') list($tmp, $value) = each($params);
          $result[$pname] = $value;
      } else {
          // param doesn't belong to any option
          $result[] = $p;
      }
  }
  return $result;
}

function is__writable($path)
{

   if ($path{strlen($path)-1}=='/') //Start function again with tmp file...
     
       return is__writable($path.uniqid(mt_rand()).'.tmp');
 
   elseif (ereg('.tmp', $path))
   { //Check tmp file for read/write capabilities
     
       if (!($f = @fopen($path, 'w+')))
           return false;
       fclose($f);
       unlink($path);
       return true;

   }
   else //We have a path error.
     
       return 0; // Or return error - invalid path...

}