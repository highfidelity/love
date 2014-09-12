<?php
/**
 * Dataobject test
 *
 * @category   LoveMachine
 * @package    UnitTests
 * @subpackage Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version    SVN: $Id: DataObjectTest.php 105 2010-10-10 21:45:37Z yani $
 * @link       http://www.lovemachineinc.com
 */
/**
 * DataObjectTest_TestClass
 */
$app_root_path = dirname(__FILE__);
$app_root_path = str_replace('Tests','',$app_root_path);

require_once ($app_root_path . 'bootstrap.php');
require_once dirname(__FILE__) . '/DataObjectTest/TestClass.php';
/**
 * DataObjectTest_TestClassChild
 */
require_once dirname(__FILE__) . '/DataObjectTest/TestClassChild.php';
/**
 * Dataobject test
 *
 * @category   LoveMachine
 * @package    UnitTests
 * @subpackage Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link       http://www.lovemachineinc.com
 * @group      Core
 */
class DataObjectTest extends PHPUnit_Framework_TestCase {
    /**
     * @var DataObjectTest_TestClass
     */
    protected $testClass;
    /**
     * @var DataObjectTest_TestClassChild
     */
    protected $testClassChild;
    
    public function setUp() {
        $this->testClass = new DataObjectTest_TestClass();
        $this->testClassChild = new DataObjectTest_TestClassChild();
    }
    
    public function testReflectedClassNameIsChildClass() {
        $failureAffects = "Full login failure";
        $className = 'DataObjectTest_TestClass';
        $this->assertEquals($className,$this->testClass->retrieveClassName(),$failureAffects);
        $childName = 'DataObjectTest_TestClassChild';
        $this->assertEquals($childName,$this->testClassChild->retrieveClassName(),$failureAffects);
    }
    
    public function testReflectedPropertiesMatchTestClass() {
        $failureAffects = "Full login failure";
        $this->assertEquals(DataObjectTest_TestClass::$properties,$this->testClass->retrieveProperties(),$failureAffects);
    }
    
    public function testIgnorePropertyIsIgnored() {
        $failureAffects = "Full login failure";
        try {
            $this->testClass->setIgnoreMe('test');
            $this->fail('Expecting exception when setting ignore col through setter method.');
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            throw $e;
        } catch ( Exception $e ) {
            $this->assertContains('undefined function',$e->getMessage(),$failureAffects);
        }
        try {
            $this->testClass->getIgnoreMe();
            $this->fail('Expecting exception when retrieving ignore col through getter method.');
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            throw $e;
        } catch ( Exception $e ) {
            $this->assertContains('undefined function',$e->getMessage(),$failureAffects);
        }
        try {
            $this->testClass->ignore_me = 'test';
            $this->fail('Expecting exception when setting ignore col through __set.');
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            throw $e;
        } catch ( Exception $e ) {
            $this->assertContains('undefined function',$e->getMessage(),$failureAffects);
        }
        try {
            $test = $this->testClass->ignore_me;
            $this->fail('Expecting exception when retrieving ignore col through __get.');
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            throw $e;
        } catch ( Exception $e ) {
            $this->assertContains('undefined function',$e->getMessage(),$failureAffects);
        }
    }
    
    public function testThrowsErrorsOnUndefinedProperties() {
        $failureAffects = "Full login failure";
        try {
            $this->testClass->undefined = 'test';
            $this->fail('Expecting exception when setting undefined property');
        } catch ( PHPUnit_Framework_AssertionFailedError $e ) {
            throw $e;
        } catch ( Exception $e ) {
            $this->assertContains('undefined function',$e->getMessage(),$failureAffects);
        }
    }
    
    public function testCanUseMagicMethods() {
        $failureAffects = "Full login failure";
        $this->assertEquals(false,$this->testClass->hasPropertyOne(),$failureAffects);
        $this->assertEquals(false,$this->testClass->hasPropertyInt(),$failureAffects);
        $this->assertEquals(false,$this->testClass->hasPropertyString(),$failureAffects);
        
        $this->testClass->setPropertyOne('propertyOne')->setPropertyInt('propertyInt')->setPropertyString('propertyString')->setLongPropertyNameToMakeItAnnoying('annoying');
        
        $this->assertEquals('propertyOne',$this->testClass->getPropertyOne(),$failureAffects);
        $this->assertEquals('propertyOne',$this->testClass->property_one,$failureAffects);
        $this->assertEquals(0,$this->testClass->getPropertyInt(),$failureAffects);
        $this->assertEquals(0,$this->testClass->property_int,$failureAffects);
        
        $this->testClass->property_int = 43;
        $this->assertEquals(43,$this->testClass->getPropertyInt(),$failureAffects);
        
        $this->testClass->property_int = 'test';
        $this->assertEquals(0,$this->testClass->getPropertyInt(),$failureAffects);
        $this->assertEquals(true,$this->testClass->hasPropertyInt(),$failureAffects);
        
        $this->testClass->property_string = 43;
        $this->assertEquals('43',$this->testClass->getPropertyString(),$failureAffects);
        
        $this->assertEquals('annoying',$this->testClass->long_property_name_to_make_it_annoying,$failureAffects);
        
        $this->assertEquals(false,$this->testClass->hasPropertyTwo(),$failureAffects);
    }
    /**
     * Test decamelize method
     *
     * @dataProvider provideDecamelize
     */
    public function testDecamelizesCorrectly() {
        $failureAffects = "Full login failure";
        foreach ( $this->provideDecamelize() as $test ) {
            $this->assertEquals($test[1],DataObject::decamelize($test[0]),$failureAffects);
        }
    }
    
    private function provideDecamelize() {
        return array(array('test', 'test'), array('testParameterOne', 'test_parameter_one'), array('testAnother1', 'test_another1'));
    }
}
