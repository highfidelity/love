<?php
/**
 * Loader test
 *
 * @category   Lmlib
 * @package    UnitTests
 * @subpackage Loader
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version    SVN: $Id: LoaderTest.php 105 2010-10-10 21:45:37Z yani $
 * @link       http://www.lovemachineinc.com
 */
/**
 * Loader testcase
 *
 * @category   Lmlib
 * @package    UnitTests
 * @subpackage Loader
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link       http://www.lovemachineinc.com
 * @group      Core
 */
$app_root_path = dirname(__FILE__);
$app_root_path = str_replace('Tests','',$app_root_path);

require_once ($app_root_path . 'bootstrap.php');

class LoaderTest extends PHPUnit_Framework_TestCase {
    /**
     * Name of the test class
     *
     * @var string
     */
    protected $testClassName;
    /**
     * Filename of the test class
     *
     * @var string
     */
    protected $testClassFile;
    
    /**
     * Set up
     */
    protected function setUp() {
        $this->testClassFile = dirname(__FILE__) . '/LoaderTest/TestClass.php';
        if (! file_exists($this->testClassFile) || ! is_readable($this->testClassFile)) {
            $this->markTestSkipped('Test class error.');
        }
        $this->testClassName = 'LoaderTest_TestClass';
    }
    
    /**
     * Data provider: nonexisting class
     */
    private function provideNonexistingClass() {
        $nonexistingClass = 'ThisClassDoesNotExist';
        if (class_exists($nonexistingClass,false)) {
            $this->markTestSkipped('Nonexisting class exists.');
        }
        $invalidFile = '/i/dont/exist';
        if (file_exists($invalidFile)) {
            $this->markTestSkipped('Invalid file does exist.');
        }
        return array(array($nonexistingClass, $invalidFile));
    }
    
    /**
     * Registering a non-existent or non-readable file should throw an exception
     */
    public function testRegisterInvalidFileThrowsException() {
        $failureAffects = "Full login failure";
        $data = $this->provideNonexistingClass();
        $invalidFile = $data[0][1];
        try {
            Loader::registerClass('LoaderTest_TestClass',$invalidFile);
            $this->fail('Exception expected.');
        } catch ( Loader_Exception $e ) {
            $this->assertContains('not accessible',$e->getMessage(),$failureAffects);
        }
    }
    
    /**
     * Registering test class
     *
     * @depends testRegisterInvalidFileThrowsException
     */
    public function testCanRegisterTestClass() {
        $failureAffects = "Full login failure";
        Loader::registerClass($this->testClassName,$this->testClassFile);
        
        Loader::load($this->testClassName);
        $this->assertTrue(class_exists($this->testClassName),$failureAffects);
    }
    
    /**
     * Registering an already registered class name should throw an exception
     */
    public function testRegisterRegisteredClassThrowsException() {
        $failureAffects = "Full login failure";
        Loader::registerClass($this->testClassName,$this->testClassFile);
        Loader::load($this->testClassName);
        try {
            Loader::registerClass($this->testClassName,$this->testClassFile);
            $this->fail('Exception expected.');
        } catch ( Loader_Exception $e ) {
            $this->assertContains('already registered',$e->getMessage(),$failureAffects);
        }
    }
    
    /**
     * Try to load an unregistered class
     *
     * @dataProvider provideNonexistingClass
     */
    public function testLoadUnregisteredClassThrowsException() {
        $failureAffects = "Full login failure";
        $data = $this->provideNonexistingClass();
        $nonexistingClass = $data[0][0];
        try {
            Loader::load($nonexistingClass);
            $this->fail('Exception should have been thrown.');
        } catch ( Loader_Exception $e ) {
            $this->assertContains('not found',$e->getMessage(),$failureAffects);
        }
    }
    
    /**
     * Try to load invalid class
     */
    public function testLoadRegisteredClassWithWrongClassnameThrowsException() {
        $failureAffects = "Full login failure";
        $data = $this->provideNonexistingClass();
        $invalidClassname = $data[0][0];
        Loader::registerClass($invalidClassname,$this->testClassFile);
        try {
            Loader::load($invalidClassname);
            $this->fail('Exception should have been thrown.');
        } catch ( Loader_Exception $e ) {
            $this->assertContains('was not found in registered location',$e->getMessage(),$failureAffects);
        }
    }
}
