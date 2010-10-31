<?php

include('../lib/Adapter.class.php');
include('../lib/adapters/SvnAdapter.class.php');


class SvnAdapterTest extends PHPUnit_Framework_TestCase
{
  protected static $rootpath;

  protected static function _system($cmd, &$return_var=0)
  {
    //echo "$cmd\n";
    return system($cmd."", $return_var);
  }

  public static function setUpBeforeClass()
  {
    self::$rootpath = dirname(__FILE__).'/field';
    
    //clean test field
    self::_system('rm -rf '.self::$rootpath.'/*');
    
    //creating repository for tests
    self::_system('mkdir -p '.self::$rootpath.'/repo/test');
    self::_system('svnadmin create '.self::$rootpath.'/repo/test/');
    self::_system('svn co file://'.self::$rootpath.'/repo/test '.self::$rootpath.'/test');
    self::_system('svn mkdir '.self::$rootpath.'/test/trunk');
    self::_system('svn mkdir '.self::$rootpath.'/test/branches');
    self::_system('svn commit -m "adding folders" '.self::$rootpath.'/test/*');
    self::_system('echo "1" >> '.self::$rootpath.'/test/trunk/a');
    self::_system('svn add '.self::$rootpath.'/test/trunk/a');
    self::_system('svn commit -m "initial commit for trunk" '.self::$rootpath.'/test/trunk/a');
    self::_system('rm -rf '.self::$rootpath.'/test');
    self::_system('svn copy file://'.self::$rootpath.'/repo/test/trunk file://'.self::$rootpath.'/repo/test/branches/number2 -m "create new branch"');
    self::_system('svn co file://'.self::$rootpath.'/repo/test/branches/number2 '.self::$rootpath.'/number2');
    self::_system('echo "2" > '.self::$rootpath.'/number2/a');
    self::_system('svn commit -m "change in branch" '.self::$rootpath.'/number2/a');
    self::_system('rm -rf '.self::$rootpath.'/number2');
  }
  
  protected function setUp()
  {
     //clean test field
    self::_system('rm -rf '.self::$rootpath.'/lib/*');
  }
      
  protected function tearDown()
  {
    
  }

  public static function tearDownAfterClass()
  {
    //clean test field
    self::_system('rm -rf '.self::$rootpath.'/*');
  }
    
  public function testDownload()
  {
    //prepare
    $c = new SvnAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test', 'branch'=>'trunk'));
    $c->init();
    $c->download();
    
    //check if data is correct
    $output = file_get_contents(self::$rootpath.'/lib/test/a');
    $this->assertEquals(1, (integer)$output);
  }

  public function testDownloadFromAnotherBranch()
  {
    //prepare
    $c = new SvnAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test', 'branch'=>'branches/number2'));
    $c->init();
    $c->download();
   
    //check if data is correct
    $output = file_get_contents(self::$rootpath.'/lib/test/a');
    $this->assertEquals(2, (integer)$output);
  }

  public function testIgnoreWhenConfigurationIsNotChanged()
  {
    //prepare
    self::_system('svn co file://'.self::$rootpath.'/repo/test/branches/number2 '.self::$rootpath.'/lib/test');
    //leave the configuration
    $c = new SvnAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test', 'branch'=>'branches/number2'));
    $c->init();
    
    //test
    $this->assertEquals(false, $c->checkConfigChanged());
  }

  public function testDetectWhenConfigurationChanged()
  {
    //prepare
    self::_system('svn co file://'.self::$rootpath.'/repo/test/trunk '.self::$rootpath.'/lib/test');
    //change the configuration
    $c = new SvnAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test', 'branch'=>'branches/number2'));
    $c->init();
    
    //test
    $this->assertEquals(true, $c->checkConfigChanged());
  }
  
  public function testDetectConfigurationChangesWhenTheFolderIsGone()
  {
    //prepare
    $c = new SvnAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test', 'branch'=>'branches/number2'));
    $c->init();
    
    //test
    $this->assertEquals(true, $c->checkConfigChanged());
  }

  public function testIgnoreIfUpdateNeededWhenNoChanges()
  {
    //run first test
    $c = new SvnAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test', 'branch'=>'trunk'));
    $c->init();
    $c->download();
    
    //test
    $this->assertEquals(false, $c->checkUpdateNeeded());
  }
  
  public function testDetectUpdateIsNeededWhenRepositoryHasNewCommit()
  {
    //prepare
    $c = new SvnAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test', 'branch'=>'trunk'));
    $c->init();
    $c->download();
    //commit some changes in the repository
    self::_system('svn co file://'.self::$rootpath.'/repo/test/trunk '.self::$rootpath.'/trunktest');
    self::_system('echo "1" >> '.self::$rootpath.'/trunktest/a');
    self::_system('svn commit -m "another change" '.self::$rootpath.'/trunktest/a');
    self::_system('rm -rf '.self::$rootpath.'/trunktest');
    
    //test
    $this->assertEquals(true, $c->checkUpdateNeeded());    
  }

  public function testNoUpdateNeededAfterUpdateDone()
  {
    //prepare
    $c = new SvnAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test', 'branch'=>'trunk'));
    $c->init();
    $c->download();
    //commit some changes in the repository
    self::_system('svn co file://'.self::$rootpath.'/repo/test/trunk '.self::$rootpath.'/trunktest');
    self::_system('echo "1" >> '.self::$rootpath.'/trunktest/a');
    self::_system('svn commit -m "another change" '.self::$rootpath.'/trunktest/a');
    self::_system('rm -rf '.self::$rootpath.'/trunktest');
    //call the update
    $c->update();
    
    //test
    $this->assertEquals(false, $c->checkUpdateNeeded()); 
  }
}