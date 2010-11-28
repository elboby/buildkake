<?php

include('../lib/AppConfig.class.php');


class GitAdapterTest extends PHPUnit_Framework_TestCase
{
  public static function setUpBeforeClass()
  {

  }
  
  protected function setUp()
  {

  }
      
  protected function tearDown()
  {
    
  }

  public static function tearDownAfterClass()
  {

  }
    
  public function testRetrieveValuesFromSingleConfig()
  {
    //prepare
    $o = new AppConfig('fixtures/test_1.ini', null);
    $o->init();

    //test
    $this->assertEquals(
                    array('foo'=>'bar'), 
                    $o->getArrayFor('A')
                  );
  }

  public function testRetrieveValuesFromMultipleConfig()
  {
    //prepare
    $o = new AppConfig('fixtures/test_2.ini', null);
    $o->init();

    //test
    $this->assertEquals(
                    array('fooB'=>'barB'), 
                    $o->getArrayFor('B')
                  );
  }

  public function testRetrieveValuesFromMergedConfigs()
  {
    //prepare
    $o = new AppConfig('fixtures/test_3_a.ini', 'fixtures/test_3_b.ini');
    $o->init();

    //test
    $this->assertEquals(
                    array('foo'=>'BB'), 
                    $o->getArrayFor('A')
                  );
  }
}