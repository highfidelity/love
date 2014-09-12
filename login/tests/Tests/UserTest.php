<?php
/**
 * User tests
 *
 * @category   LoveMachineLogin
 * @package    UnitTests
 * @subpackage User
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version    SVN: $Id: UserTest.php 105 2010-10-10 21:45:37Z yani $
 * @link       http://www.lovemachineinc.com
 */
/**
 * User tests
 *
 * @category   LoveMachineLogin
 * @package    UnitTests
 * @subpackage User
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link       http://www.lovemachineinc.com
 * @group      User
 */
$app_root_path = dirname(__FILE__);
$app_root_path = str_replace('Tests','',$app_root_path);
ini_set('error_log',$app_root_path . 'errors.log');

require_once ($app_root_path . 'bootstrap.php');

class UserTest extends PHPUnit_Framework_TestCase {
    /**
     * @var User User instance
     */
    protected $user;
    
    public function setUp() {
        try {
            $this->user = new User();
        } catch ( Exception $e ) {
            throw $e;
        }
    }
    
    public function testCanAccessSettersAndGetters() {
        $failureAffects = "Cannot create, update, and authenticate users";
        
        $this->user->setId(43)->setUsername('username')->setNickname('nickname')->setConfirmed(User::USER_NOT_CONFIRMED)->setToken('abc123')->setDateAdded('1970-01-01 00:00:00')->setDateModified('1970-01-01 13:37:00');
        
        $this->assertEquals('43',$this->user->getId(),$failureAffects);
        $this->assertEquals('username',$this->user->getUsername(),$failureAffects);
        $this->assertEquals('nickname',$this->user->getNickname(),$failureAffects);
        $this->assertEquals(User::USER_NOT_CONFIRMED,$this->user->getConfirmed(),$failureAffects);
        $this->assertEquals('abc123',$this->user->getToken(),$failureAffects);
        $this->assertEquals('1970-01-01 00:00:00',$this->user->getDateAdded(),$failureAffects);
        $this->assertEquals('1970-01-01 13:37:00',$this->user->getDateModified(),$failureAffects);
    }
    
    public function testCanSetValidIds() {
        $failureAffects = "Cannot create or update users";
        $this->user->setId(43);
        $this->assertEquals('43',$this->user->getId(),$failureAffects);
        $this->user->setId(PHP_INT_MAX);
        $this->assertEquals(( string ) PHP_INT_MAX,$this->user->getId(),$failureAffects);
        $this->user->setId('44');
        $this->assertEquals('44',$this->user->getId(),$failureAffects);
        $this->user->setId('0xffffffff');
        $this->assertEquals('0xffffffff',$this->user->getId(),$failureAffects);
    }
    
    public function testSetInvalidIdThrowsException() {
        $failureAffects = "Creating, updating, and authenticating new users will produce errors";
        try {
            $this->user->setId('test');
            $this->fail('Expecting exception for value "test".');
        } catch ( User_Exception $e ) {
            $this->assertContains('Invalid id',$e->getMessage(),$failureAffects);
        }
        try {
            $this->user->setId(- 1);
            $this->fail('Expecting exception for value "-1".');
        } catch ( User_Exception $e ) {
            $this->assertContains('Invalid id',$e->getMessage(),$failureAffects);
        }
    }
    
    public function testCanSetValidConfirmedValues() {
        $failureAffects = "Cannot confirm or unconfirm users";
        $this->user->setConfirmed(User::USER_CONFIRMED);
        $this->assertEquals(User::USER_CONFIRMED,$this->user->getConfirmed(),$failureAffects);
        
        $this->user->setConfirmed(User::USER_NOT_CONFIRMED);
        $this->assertEquals(User::USER_NOT_CONFIRMED,$this->user->getConfirmed(),$failureAffects);
        
        $this->user->setConfirmed(1);
        $this->assertEquals(User::USER_CONFIRMED,$this->user->getConfirmed(),$failureAffects);
        
        $this->user->setConfirmed(true);
        $this->assertEquals(User::USER_CONFIRMED,$this->user->getConfirmed(),$failureAffects);
        
        $this->user->setConfirmed(0);
        $this->assertEquals(User::USER_NOT_CONFIRMED,$this->user->getConfirmed(),$failureAffects);
        
        $this->user->setConfirmed(false);
        $this->assertEquals(User::USER_NOT_CONFIRMED,$this->user->getConfirmed(),$failureAffects);
    }
    
    public function testSetInvalidConfirmedThrowsException() {
        $failureAffects = "If invalid confirmed flags are set the login will not return an error";
        try {
            $this->user->setConfirmed(- 1);
            $this->fail('Expecting exception for value "-1".');
        } catch ( User_Exception $e ) {
            $this->assertContains('Invalid value',$e->getMessage(),$failureAffects);
        }
        try {
            $this->user->setConfirmed(2);
            $this->fail('Expecting exception for value "2".');
        } catch ( User_Exception $e ) {
            $this->assertContains('Invalid value',$e->getMessage(),$failureAffects);
        }
    }
    
    public function testColumnsAreDeterminedCorrectly() {
        $failureAffects = "Database schema is invalid";
        $expectedColumns = array('id', 'username', 'password', 'nickname', 'confirmed', 'active', 'token', 'date_added', 'date_modified', 'removed', 'admin');
        $this->assertEquals($expectedColumns,$this->user->getColumns(),$failureAffects);
    }
    
    public function testAuthenticateWithoutPasswordThrowsException() {
        $failureAffects = "Login will authenticate users with no password provided";
        try {
            $this->user->authenticate('efewfef');
            $this->fail('Expection exception.');
        } catch ( User_Exception $e ) {
            $this->assertContains('no password set',$e->getMessage(),$failureAffects);
        }
    }
    
    public function testCanAuthenticate() {
        $failureAffects = "Login cannot authenticate users";
        $password = 'fefkjJKEFKEJ .faewf$%3<fm5%$';
        $this->user->setPassword($password);
        $this->assertTrue($this->user->authenticate($password),$failureAffects);
        $this->assertFalse($this->user->authenticate('d'),$failureAffects);
    }
    
    public function testCanAuthenticateWithOldMethod() {
        $failureAffects = "Login cannot authenticate users using the old method";
        $password = 'def hjJFJKEHJFEKJ$§$55543$§..§$';
        $this->user->setPassword(sha1($password),false);
        $this->assertTrue($this->user->authenticate($password),$failureAffects);
    }
    
    public function testCanLoad() {
        $failureAffects = "Login cannot load users";
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        // load existingUser@domain.com
        $userId = 2;
        $user = new User(new mysqli($dbConfig['host'],$dbConfig['username'],$dbConfig['password'],$dbConfig['dbname']));
        $user->loadById($userId);
        
        $this->assertEquals($userId,$user->getId(),$failureAffects);
        $this->assertEquals('existingUser@domain.com',$user->getUsername(),$failureAffects);
        $this->assertTrue($user->authenticate('9*NvF6rU'),$failureAffects);
        $this->assertEquals('existingUser',$user->getNickname(),$failureAffects);
        $this->assertEquals(User::USER_CONFIRMED,$user->getConfirmed(),$failureAffects);
        $this->assertEquals('2010-09-15 17:38:53',$user->getDateAdded(),$failureAffects);
        $this->assertEquals('0000-00-00 00:00:00',$user->getDateModified(),$failureAffects);
    }
    public function testCanLoadByUsername() {
        $failureAffects = "Login cannot load users by username";
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        // load existingUser@domain.com
        $userId = 2;
        $user = new User(new mysqli($dbConfig['host'],$dbConfig['username'],$dbConfig['password'],$dbConfig['dbname']));
        $user->loadByUsername('existingUser@domain.com');
        
        $this->assertEquals($userId,$user->getId(),$failureAffects);
        $this->assertEquals('existingUser@domain.com',$user->getUsername(),$failureAffects);
        $this->assertTrue($user->authenticate('9*NvF6rU'),$failureAffects);
        $this->assertEquals('existingUser',$user->getNickname(),$failureAffects);
        $this->assertEquals(User::USER_CONFIRMED,$user->getConfirmed(),$failureAffects);
        $this->assertEquals('2010-09-15 17:38:53',$user->getDateAdded(),$failureAffects);
        $this->assertEquals('0000-00-00 00:00:00',$user->getDateModified(),$failureAffects);
    }
    
    public function testLoadByUsernameIsCaseInsensitive() {
        $failureAffects = "Login cannot load users case insensitive";
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $userId = 2;
        
        // load by username test case insensitivity
        $user = new User(new mysqli($dbConfig['host'],$dbConfig['username'],$dbConfig['password'],$dbConfig['dbname']));
        $user->loadByUsername('ExistingUser@domain.com');
        
        $this->assertEquals($userId,$user->getId(),$failureAffects);
        $this->assertEquals('existingUser@domain.com',$user->getUsername(),$failureAffects);
        $this->assertTrue($user->authenticate('9*NvF6rU'),$failureAffects);
        $this->assertEquals('existingUser',$user->getNickname(),$failureAffects);
        $this->assertEquals(User::USER_CONFIRMED,$user->getConfirmed(),$failureAffects);
        $this->assertEquals('2010-09-15 17:38:53',$user->getDateAdded(),$failureAffects);
        $this->assertEquals('0000-00-00 00:00:00',$user->getDateModified(),$failureAffects);
    }
    
    public function testLoadByNonexistentUsernameYieldsFalse() {
        $failureAffects = "Login will load nonexistent usernames";
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $user = new User(new mysqli($dbConfig['host'],$dbConfig['username'],$dbConfig['password'],$dbConfig['dbname']));
        
        $this->assertFalse($user->loadByUsername('idontexist@domain.com'));
    }
    
}
