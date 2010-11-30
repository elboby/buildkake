<?php

include(dirname(__FILE__).'/includeTest.php');
include(dirname(__FILE__).'/../lib/Adapter.class.php');
include(dirname(__FILE__).'/../lib/adapters/GitAdapter.class.php');


class GitAdapterTest extends PHPUnit_Framework_TestCase
{
  protected static $rootpath, $logger;

  protected static function _system($cmd, &$return_var=0)
  {
    return system($cmd."", $return_var);
  }

  protected static function _system_cd($location, $cmd, &$return_var=0)
  {
    return self::_system('cd '.$location.' && '.$cmd."", $return_var);
  }

  public static function setUpBeforeClass()
  { 
    self::$rootpath = dirname(__FILE__).'/field';
    
    //clean test field
    self::_system('rm -rf '.self::$rootpath.'/*');
    
    //creating repository for tests
    self::_system('mkdir -p '.self::$rootpath.'/repo/test.git');
    self::_system('git init '.self::$rootpath.'/repo/test.git');
    self::_system('echo 1 > '.self::$rootpath.'/repo/test.git/a');
    self::_system_cd(self::$rootpath.'/repo/test.git', 'git add .');
    self::_system_cd(self::$rootpath.'/repo/test.git', 'git commit -m "initial commit"');
    self::_system_cd(self::$rootpath.'/repo/test.git', 'git checkout -b number2');
    self::_system('echo 2 > '.self::$rootpath.'/repo/test.git/a');
    self::_system_cd(self::$rootpath.'/repo/test.git', 'git add .');
    self::_system_cd(self::$rootpath.'/repo/test.git', 'git commit -m "initial commit"');
    self::_system_cd(self::$rootpath.'/repo/test.git', 'git checkout master');
    
    //mock logger
    self::$logger = new Logger();
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
     $c = new GitAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test.git', 'branch'=>'master'), self::$logger);
     $c->init();
     $c->download();
     
     //check if data is correct
     $output = file_get_contents(self::$rootpath.'/lib/test/a');
     $this->assertEquals(1, (integer)$output);
   }
  
   public function testDownloadFromAnotherBranch()
   {
     //prepare
     $c = new GitAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test.git', 'branch'=>'number2'), self::$logger);
     $c->init();
     $c->download();
    
     //check if data is correct
     $output = file_get_contents(self::$rootpath.'/lib/test/a');
     $this->assertEquals(2, (integer)$output);
   }
  
  public function testIgnoreWhenConfigurationIsNotChanged()
  {
    //prepare
    self::_system('git clone file://'.self::$rootpath.'/repo/test.git '.self::$rootpath.'/lib/test');
    self::_system_cd(self::$rootpath.'/lib/test', 'git branch -t number2 origin/number2');
    self::_system_cd(self::$rootpath.'/lib/test', 'git checkout number2');
    
    //leave the configuration
    $c = new GitAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test.git', 'branch'=>'number2'), self::$logger);
    $c->init();

    //test
    $this->assertEquals(false, $c->checkConfigChanged());
  }

  public function testDetectWhenConfigurationChanged()
  {
    //prepare
    self::_system('git clone file://'.self::$rootpath.'/repo/test.git '.self::$rootpath.'/lib/test');
    
    //change the configuration
    $c = new GitAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test.git', 'branch'=>'number2'), self::$logger);
    $c->init();

    //test
    $this->assertEquals(true, $c->checkConfigChanged());
  }
  
  public function testDetectConfigurationChangesWhenTheFolderIsGone()
  {
    //prepare
    $c = new GitAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test.git', 'branch'=>'number2'), self::$logger);
    $c->init();
    
    //test
    $this->assertEquals(true, $c->checkConfigChanged());
  }
  
  public function testIgnoreIfUpdateNeededWhenNoChanges()
  {
    //run first test
    $c = new GitAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test.git', 'branch'=>'master'), self::$logger);
    $c->init();
    $c->download();
    
    //test
    $this->assertEquals(false, $c->checkUpdateNeeded());
  }
  
  public function testDetectUpdateIsNeededWhenRepositoryHasNewCommit()
  {
    //prepare
    $c = new GitAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test.git', 'branch'=>'master'), self::$logger);
    $c->init();
    $c->download();
    
    //commit some changes in the repository
    self::_system_cd(self::$rootpath.'/repo/test.git', 'git checkout master');
    self::_system('echo 1 >> '.self::$rootpath.'/repo/test.git/a');
    self::_system_cd(self::$rootpath.'/repo/test.git', 'git add .');
    self::_system_cd(self::$rootpath.'/repo/test.git', 'git commit -m "another change"');
    
    //test
    $this->assertEquals(true, $c->checkUpdateNeeded());    
  }
  
  public function testNoUpdateNeededAfterUpdateDone()
  {
    //prepare
    $c = new GitAdapter('test', self::$rootpath.'/lib', array('url'=>'file://'.self::$rootpath.'/repo/test.git', 'branch'=>'master'), self::$logger);
    $c->init();
    $c->download();
    
    //commit some changes in the repository
    self::_system_cd(self::$rootpath.'/repo/test.git', 'git checkout master');
    self::_system('echo 1 >> '.self::$rootpath.'/repo/test.git/a');
    self::_system_cd(self::$rootpath.'/repo/test.git', 'git add .');
    self::_system_cd(self::$rootpath.'/repo/test.git', 'git commit -m "another change again"');
    //call the update
    $c->update();
    
    //test
    $this->assertEquals(false, $c->checkUpdateNeeded()); 
  }
}