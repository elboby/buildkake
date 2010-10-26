<?php

include('../lib/Adapter.class.php');
include('../lib/adapters/SvnAdapter.class.php');

$testfield_root = dirname(__FILE__).'/field';

function _s($cmd, &$return_var=0)
{
  //echo "$cmd\n";
  return system($cmd, $return_var);
}

//clean test field
_s('rm -rf '.$testfield_root.'/*');
//creating context for first test
_s('mkdir -p '.$testfield_root.'/repo/test');
_s('svnadmin create '.$testfield_root.'/repo/test/');
_s('svn co file://'.$testfield_root.'/repo/test '.$testfield_root.'/test');
_s('svn mkdir '.$testfield_root.'/test/trunk');
_s('svn mkdir '.$testfield_root.'/test/branches');
_s('svn commit -m "adding folders" '.$testfield_root.'/test/*');
_s('echo "1" >> '.$testfield_root.'/test/trunk/a');
_s('svn add '.$testfield_root.'/test/trunk/a');
_s('svn commit -m "initial commit for trunk" '.$testfield_root.'/test/trunk/a');
_s('rm -rf '.$testfield_root.'/test');
_s('svn copy file://'.$testfield_root.'/repo/test/trunk file://'.$testfield_root.'/repo/test/branches/number2 -m "create new branch"');
_s('svn co file://'.$testfield_root.'/repo/test/branches/number2 '.$testfield_root.'/number2');
_s('echo "2" > '.$testfield_root.'/number2/a');
_s('svn commit -m "change in branch" '.$testfield_root.'/number2/a');
_s('rm -rf '.$testfield_root.'/number2');


//clean test field
_s('rm -rf '.$testfield_root.'/lib/*');
//run first test
$c = new SvnAdapter('test', $testfield_root.'/lib', array('url'=>'file://'.$testfield_root.'/repo/test', 'branch'=>'trunk'));
$c->init();
$c->download();
//check if data is correct
$output = _s('cat '.$testfield_root.'/lib/test/a');
if($output == '1') echo "#1: success!\n";
else echo "#1: failed \n";


//clean test field
_s('rm -rf '.$testfield_root.'/lib/*');
//run first test
$c = new SvnAdapter('test', $testfield_root.'/lib', array('url'=>'file://'.$testfield_root.'/repo/test', 'branch'=>'branches/number2'));
$c->init();
$c->download();
//check if data is correct
$output = _s('cat '.$testfield_root.'/lib/test/a');
if($output == '2') echo "#2: success!\n";
else echo "#2: failed \n";


//clean test field
_s('rm -rf '.$testfield_root.'/lib/*');
_s('svn co file://'.$testfield_root.'/repo/test/branches/number2 '.$testfield_root.'/lib/test');
//run first test
$c = new SvnAdapter('test', $testfield_root.'/lib', array('url'=>'file://'.$testfield_root.'/repo/test', 'branch'=>'branches/number2'));
$c->init();
if($c->checkConfigChanged() === false) echo "#3: success!\n";
else echo "#3: failed \n";


//clean test field
_s('rm -rf '.$testfield_root.'/lib/*');
_s('svn co file://'.$testfield_root.'/repo/test/trunk '.$testfield_root.'/lib/test');
//run first test
$c = new SvnAdapter('test', $testfield_root.'/lib', array('url'=>'file://'.$testfield_root.'/repo/test', 'branch'=>'branches/number2'));
$c->init();
if($c->checkConfigChanged() === true) echo "#4: success!\n";
else echo "#4: failed \n";

//clean test field
_s('rm -rf '.$testfield_root.'/lib/*');
//run first test
$c = new SvnAdapter('test', $testfield_root.'/lib', array('url'=>'file://'.$testfield_root.'/repo/test', 'branch'=>'branches/number2'));
$c->init();
if($c->checkConfigChanged() === true) echo "#5: success!\n";
else echo "#5: failed \n";


//clean test field
_s('rm -rf '.$testfield_root.'/lib/*');
//run first test
$c = new SvnAdapter('test', $testfield_root.'/lib', array('url'=>'file://'.$testfield_root.'/repo/test', 'branch'=>'trunk'));
$c->init();
$c->download();
if($c->checkUpdateNeeded() === false) echo "#6: success!\n";
else echo "#6: failed \n";

//clean test field
_s('rm -rf '.$testfield_root.'/lib/*');
//run first test
$c = new SvnAdapter('test', $testfield_root.'/lib', array('url'=>'file://'.$testfield_root.'/repo/test', 'branch'=>'trunk'));
$c->init();
$c->download();
_s('svn co file://'.$testfield_root.'/repo/test/trunk '.$testfield_root.'/trunktest');
_s('echo "1" >> '.$testfield_root.'/trunktest/a');
_s('svn commit -m "another change" '.$testfield_root.'/trunktest/a');
_s('rm -rf '.$testfield_root.'/trunktest');
if($c->checkUpdateNeeded() === true) echo "#7: success!\n";
else echo "#7: failed \n";

//clean test field
_s('rm -rf '.$testfield_root.'/lib/*');
//run first test
$c = new SvnAdapter('test', $testfield_root.'/lib', array('url'=>'file://'.$testfield_root.'/repo/test', 'branch'=>'trunk'));
$c->init();
$c->download();
_s('svn co file://'.$testfield_root.'/repo/test/trunk '.$testfield_root.'/trunktest');
_s('echo "1" >> '.$testfield_root.'/trunktest/a');
_s('svn commit -m "another change" '.$testfield_root.'/trunktest/a');
_s('rm -rf '.$testfield_root.'/trunktest');
$c->update();
if($c->checkUpdateNeeded() === false) echo "#7: success!\n";
else echo "#7: failed \n";

