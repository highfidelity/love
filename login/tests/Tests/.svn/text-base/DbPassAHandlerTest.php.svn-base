<?php
$app_root_path = dirname(__FILE__);
$app_root_path = str_replace('Tests','',$app_root_path);

require_once ($app_root_path . 'bootstrap.php');
require_once (dirname(__FILE__) . "/DbPassAuthHandlerTest/TestControllerAction.php");

class DbPassAHandlerTest extends PHPUnit_Framework_TestCase {
    
    protected $controller;
    protected $aHandler;
    
    public function setUp() {
        try {
            $dbConfig = array();
            $dbConfig['adapter'] = 'mysqli';
            $dbConfig['host'] = 'mysql.dev.sendlove.us';
            $dbConfig['dbname'] = 'LM_logintest';
            $dbConfig['username'] = 'LM_logintest';
            $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
            $this->controller = new TestControllerAction();
            $this->aHandler = new Db_Password_Authentication_Handler($dbConfig);
        } catch ( Exception $e ) {
            throw $e;
        }
    }
    
    public function testAdminResetTokenAdminUserNotExists() {
        $failureAffects = "Login will no longer allow admins to reset user tokens";
        
        //Admin user doesn't exist.
        $admin_id = - 1;
        $user_id = - 1;
        $user = $this->aHandler->adminresettoken($this->controller,$user_id,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("Admin user doesn't exist.",$err[0],$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);
    }
    public function testAdminResetTokenAdminUserDeactivated() {
        $failureAffects = "Login will no longer allow admins to reset user tokens";
        
        //Admin user is deactivated.
        $admin_id = 8;
        $user_id = - 1;
        $user = $this->aHandler->adminresettoken($this->controller,$user_id,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("Admin user is deactivated.",$err[0],$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);
    }
    public function testAdminResetTokenAdminUserRemoved() {
        $failureAffects = "Login will no longer allow admins to reset user tokens";
        
        //Admin user is removed.
        $admin_id = 7;
        $user_id = - 1;
        $user = $this->aHandler->adminresettoken($this->controller,$user_id,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("Admin user is removed.",$err[0],$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);
    }
    public function testAdminResetTokenAdminUserNotAdmin() {
        $failureAffects = "Login will no longer allow admins to reset user tokens";
        
        //You are not admin user.
        $admin_id = 2;
        $user_id = - 1;
        $user = $this->aHandler->adminresettoken($this->controller,$user_id,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("You are not admin user.",$err[0],$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);
    }
    public function testAdminResetTokenUserNotExists() {
        $failureAffects = "Login will no longer allow admins to reset user tokens";
        
        //User doesn't exist.
        $admin_id = 6;
        $user_id = - 1;
        $user = $this->aHandler->adminresettoken($this->controller,$user_id,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("User doesn't exist.",$err[0],$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);
    }
    public function testAdminResetToken() {
        $failureAffects = "Login will no longer allow admins to reset user tokens";
        
        //Correct settings, verify that update is successful
        $admin_id = 6;
        $user_id = 2;
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $u = new LoveUser($dbConfig);
        $u->loadById($user_id);
        $currentToken = $u->getToken();
        
        $user = $this->aHandler->adminresettoken($this->controller,$user_id,$admin_id);
        // error flag is NOT set
        $this->assertEquals(false,$this->controller->getErrorFlag(),$failureAffects);
        // user is object
        $this->assertEquals(true,is_object($user),$failureAffects);
        // token has been updated
        $this->assertEquals(true,($currentToken != $user->getToken()),$failureAffects);
    }
    
    public function testGetUserListAdminUserNotExists() {
        $failureAffects = "Login will no longer return a list of all users";
        
        //Admin user doesn't exist.
        $admin_id = - 1;
        $user = $this->aHandler->getuserlist($this->controller,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("Admin user doesn't exist.",$err[0],$failureAffects);
        // user list is not array
        $this->assertEquals(false,is_array($user),$failureAffects);
    }
    
    public function testGetUserListAdminUserDeactivated() {
        $failureAffects = "Login will no longer return a list of all users";
        
        //Admin user is deactivated.
        $admin_id = 8;
        $user = $this->aHandler->getuserlist($this->controller,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("Admin user is deactivated.",$err[0],$failureAffects);
        // user list is not array
        $this->assertEquals(false,is_array($user),$failureAffects);
    }
    
    public function testGetUserListAdminUserRemoved() {
        $failureAffects = "Login will no longer return a list of all users";
        
        //Admin user is removed.
        $admin_id = 7;
        $user = $this->aHandler->getuserlist($this->controller,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("Admin user is removed.",$err[0],$failureAffects);
        // user list is not array
        $this->assertEquals(false,is_array($user),$failureAffects);
    }
    
    public function testGetUserListAdminUserNotAdmin() {
        $failureAffects = "Login will no longer return a list of all users";
        
        //You are not admin user.
        $admin_id = 2;
        $user = $this->aHandler->getuserlist($this->controller,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("You are not admin user.",$err[0],$failureAffects);
        // user list is not array
        $this->assertEquals(false,is_array($user),$failureAffects);
    }
    
    public function testGetUserList() {
        $failureAffects = "Login will no longer return a list of all users";
        
        //Correct
        $admin_id = 6;
        $user = $this->aHandler->getuserlist($this->controller,$admin_id);
        // error flag is NOT set
        $this->assertEquals(false,$this->controller->getErrorFlag(),$failureAffects);
        // user list IS array
        $this->assertEquals(true,is_array($user),$failureAffects);
    }
    
	public function testGetUserDataAdminUserNotExists(){
        $failureAffects = "Login will no longer return a user data";
        
        //Admin user doesn't exist.
        $admin_id = - 1;
        $user_id = - 1;
        $user = $this->aHandler->getuserdata($this->controller,$user_id,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("Admin user doesn't exist.",$err[0],$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);    
    }
    
    public function testGetUserDataAdminUserDeactivated(){
        $failureAffects = "Login will no longer return a user data";
        
        //Admin user is deactivated.
        $admin_id = 8;
        $user_id = - 1;
        $user = $this->aHandler->getuserdata($this->controller,$user_id,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("Admin user is deactivated.",$err[0],$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);
    }
    
    public function testGetUserDataAdminUserRemoved(){
        $failureAffects = "Login will no longer return a user data";

        //Admin user is removed.
        $admin_id = 7;
        $user_id = - 1;
        $user = $this->aHandler->getuserdata($this->controller,$user_id,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("Admin user is removed.",$err[0],$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);
    }
    
    public function testGetUserDataAdminUserNotAdmin(){
        $failureAffects = "Login will no longer return a user data";
        
        //You are not admin user.
        $admin_id = 2;
        $user_id = - 1;
        $user = $this->aHandler->getuserdata($this->controller,$user_id,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("You are not admin user.",$err[0],$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);
    }
    
    public function testGetUserDataUserNotExists(){
        $failureAffects = "Login will no longer return a user data";
        
        //User doesn't exist.
        $admin_id = 6;
        $user_id = - 1;
        $user = $this->aHandler->getuserdata($this->controller,$user_id,$admin_id);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $err = $this->controller->getErrorMessage();
        $this->assertEquals("User doesn't exist.",$err[0],$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);
    }
    
    public function testGetUserData(){
        $failureAffects = "Login will no longer return a user data";
        
        //Correct
        $admin_id = 6;
        $user_id = 2;
        $user = $this->aHandler->getuserdata($this->controller,$user_id,$admin_id);
        // error flag is NOT set
        $this->assertEquals(false,$this->controller->getErrorFlag(),$failureAffects);
        // user IS object
        $this->assertEquals(true,is_object($user),$failureAffects);
        // id of the user object is 2
        $this->assertEquals(2,$user->getId(),$failureAffects);
    }
    
    public function testUpdateUsername(){
        $failureAffects = "Login will no longer update users' username";
        $uid = 9;
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $u = new LoveUser($dbConfig);
        $u->loadById($uid);
        $currentUsername = $u->getUsername();
        
        $user_data = array(
            "userid" => $uid,
            "username" => $currentUsername.$uid
        );
        $this->assertEquals(true, ($currentUsername != $user_data["username"]),$failureAffects);
        
        //updating the username
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
        
        //making sure that the username has been updated
        $u->loadById($uid);
        $updatedUsername = $u->getUsername();
        $this->assertEquals(true, ($currentUsername != $updatedUsername),$failureAffects);
        
        //updating the username to the original value
        $user_data["username"] = $currentUsername;
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
    }
    
    public function testUpdateNickname(){
        $failureAffects = "Login will no longer update users' nickname";
        $uid = 9;
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $u = new LoveUser($dbConfig);
        $u->loadById($uid);
        $currentNickname = $u->getNickname();
        
        $user_data = array(
            "userid" => $uid,
            "nickname" => $currentNickname.$uid
        );
        $this->assertEquals(true, ($currentNickname != $user_data["nickname"]),$failureAffects);
        
        //updating the nickname
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
        
        //making sure that the nickname has been updated
        $u->loadById($uid);
        $updatedNickname = $u->getNickname();
        $this->assertEquals(true, ($currentNickname != $updatedNickname),$failureAffects);
        
        //updating the nickname to the original value
        $user_data["nickname"] = $currentNickname;
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
    }
    
    public function testUpdatePassword(){
        $failureAffects = "Login will no longer update users' password";
        $uid = 9;
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $u = new LoveUser($dbConfig);
        $u->loadById($uid);
        $currentPassword = $u->getPassword();
        
        $user_data = array(
            "userid" => $uid,
            "newpassword" => $currentPassword.$uid
        );
        $this->assertEquals(true, ($currentPassword != $user_data["newpassword"]),$failureAffects);
        
        //updating the password
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
        
        //making sure that the password has been updated
        $u->loadById($uid);
        $updatedPassword = $u->getPassword();
        $this->assertEquals(true, ($currentPassword != $updatedPassword),$failureAffects);
        
        //updating the password to the original value
        $user_data["newpassword"] = $currentPassword;
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
    }
    
    public function testUpdateConfirmed(){
        $failureAffects = "Login will no longer update users' confirmed flag";
        $uid = 9;
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $u = new LoveUser($dbConfig);
        $u->loadById($uid);
        $currentConfirmed = $u->getConfirmed();
        
        $user_data = array(
            "userid" => $uid,
            "confirmed" => 0
        );
        $this->assertEquals(true, ($currentConfirmed != $user_data["confirmed"]),$failureAffects);
        
        //updating the confirmed
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
        
        //making sure that the confirmed has been updated
        $u->loadById($uid);
        $updatedConfirmed = $u->getConfirmed();
        $this->assertEquals(true, ($currentConfirmed != $updatedConfirmed),$failureAffects);
        
        //updating the confirmed to the original value
        $user_data["confirmed"] = $currentConfirmed;
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
    }
    
    public function testUpdateActive(){
        $failureAffects = "Login will no longer update users' active flag";
        $uid = 9;
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $u = new LoveUser($dbConfig);
        $u->loadById($uid);
        $currentActive = $u->getActive();
        
        $user_data = array(
            "userid" => $uid,
            "active" => 0
        );
        $this->assertEquals(true, ($currentActive != $user_data["active"]),$failureAffects);
        
        //updating the active
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
        
        //making sure that the active has been updated
        $u->loadById($uid);
        $updatedActive = $u->getActive();
        $this->assertEquals(true, ($currentActive != $updatedActive),$failureAffects);
        
        //updating the active to the original value
        $user_data["active"] = $currentActive;
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
    }
    
    public function testUpdateAdmin(){
        $failureAffects = "Login will no longer update users' admin flag";
        $uid = 9;
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $u = new LoveUser($dbConfig);
        $u->loadById($uid);
        $currentAdmin = $u->getAdmin();
        
        $user_data = array(
            "userid" => $uid,
            "admin" => 1
        );
        $this->assertEquals(true, ($currentAdmin != $user_data["admin"]),$failureAffects);
        
        //updating the admin flag
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
        
        //making sure that the admin flag has been updated
        $u->loadById($uid);
        $updatedAdmin = $u->getAdmin();
        $this->assertEquals(true, ($currentAdmin != $updatedAdmin),$failureAffects);
        
        //updating the admin flag to the original value
        $user_data["admin"] = $currentAdmin;
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
    }
    
    public function testUpdateRemoved(){
        $failureAffects = "Login will no longer update users' removed flag";
        $uid = 9;
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $u = new LoveUser($dbConfig);
        $u->loadById($uid);
        $currentRemoved = $u->getRemoved();
        
        $user_data = array(
            "userid" => $uid,
            "removed" => 1
        );
        $this->assertEquals(true, ($currentRemoved != $user_data["removed"]),$failureAffects);
        
        //updating the removed flag
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
        
        //making sure that the removed flag has been updated
        $u->loadById($uid);
        $updatedRemoved = $u->getRemoved();
        $this->assertEquals(true, ($currentRemoved != $updatedRemoved),$failureAffects);
        
        //updating the removed flag to the original value
        $user_data["removed"] = $currentRemoved;
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
    }
    
    public function testUpdateToken(){
        $failureAffects = "Login will no longer update users' token";
        $uid = 9;
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $u = new LoveUser($dbConfig);
        $u->loadById($uid);
        $currentToken = $u->getToken();
        
        $user_data = array(
            "userid" => $uid,
            "token" => uniqid()
        );
        $this->assertEquals(true, ($currentToken != $user_data["token"]),$failureAffects);
        
        //updating the token
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
        
        //making sure that the token has been updated
        $u->loadById($uid);
        $updatedToken = $u->getToken();
        $this->assertEquals(true, ($currentToken != $updatedToken),$failureAffects);
        
        //updating the token to the original value
        $user_data["token"] = $currentToken;
        $this->assertEquals(true, $this->aHandler->update($this->controller, $user_data),$failureAffects);
    }
    
    public function testAuthenticate() {
        $failureAffects = "Login will no longer authenticate users properly";
        
        // Login with existing user and correct password
        $username = "existingUser@domain.com";
        $password = "9*NvF6rU";
        
        $user = $this->aHandler->authenticate($this->controller,$username,$password);
        // no error flag is set
        $this->assertEquals(false,$this->controller->getErrorFlag(),$failureAffects);
        // user is object (not null)
        $this->assertEquals(true,is_object($user),$failureAffects);
        
        // Login with existing user BUT wrong password
        $password = "wrongPassword";
        $user = $this->aHandler->authenticate($this->controller,$username,$password);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $this->assertEquals("Invalid password.",$this->controller->getError(),$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);
        
        // Login with non-existing user
        $username = "nonExisting@noneexisting.com";
        $user = $this->aHandler->authenticate($this->controller,$username,$password);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $this->assertEquals("Username doesn't exist.",$this->controller->getError(),$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);
        
        // Login with existing user, correct password, BUT user is NOT ACTIVE
        $username = "notActiveUser@domain.com";
        $user = $this->aHandler->authenticate($this->controller,$username,$password);
        // error flag is set
        $this->assertEquals(true,$this->controller->getErrorFlag(),$failureAffects);
        // error message is valid
        $this->assertEquals("User is deactivated.",$this->controller->getError(),$failureAffects);
        // user is not object
        $this->assertEquals(false,is_object($user),$failureAffects);
    }
    
    public function testSetUserData(){
        $failureAffects = "Login will no longer allow admins to update users data";
        
        // admin ID
        $aid = 6;
        // user ID
        $uid = 9;
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $u = new LoveUser($dbConfig);
        $u->loadById($uid);
        
        // current user info
        $username = $u->getUsername();
        $nickname = $u->getNickname();
        $password = $u->getPassword();
        $confirmed = $u->getConfirmed();
        $active = $u->getActive();
        $token = $u->getToken();
        $admin = $u->getAdmin();
        $removed = $u->getRemoved();
        
        // updated user info
        $user_data = array(
            "userid" => $uid,
            "username" => $username.$uid,
            "nickname" => $nickname.$uid,
            "password"	=> $password.$uid,
            "confirmed" => 0,
            "active" => 0,
            "admin" => 1,
            "removed" => 1,
            "token" => uniqid()
        );
        
        // update user
        $this->assertEquals(true,$this->aHandler->setuserdata($this->controller, $user_data, $aid),$failureAffects);
        // verify that no error is returned
        $this->assertEquals(false,$this->controller->getErrorFlag(),$failureAffects);
        
        // restore previous user info
        $user_data["username"] = $username;
        $user_data["nickname"] = $nickname;
        $user_data["password"] = $password;
        $user_data["confirmed"] = 1;
        $user_data["active"] = 1;
        $user_data["admin"] = 0;
        $user_data["removed"] = 0;
        $user_data["token"] = $token; 
        $this->assertEquals(true,$this->aHandler->setuserdata($this->controller, $user_data, $aid),$failureAffects);
    }
    
    public function testAdminCreateUser(){
        $failureAffects = "Login will no longer allow admins to create new users";
        
        // admin ID
        $aid = 6;
        
        $dbConfig = array();
        $dbConfig['adapter'] = 'mysqli';
        $dbConfig['host'] = 'mysql.dev.sendlove.us';
        $dbConfig['dbname'] = 'LM_logintest';
        $dbConfig['username'] = 'LM_logintest';
        $dbConfig['password'] = 'a8f0bfedef741c7285f88012c126d06e';
        
        $u = new LoveUser($dbConfig);
        
        $username = uniqid()."@domain.com";
        $user_data = array(
            "username" => $username,
            "nickname" => uniqid(),
            "password"	=> "sample",
            "confirmed" => 1,
            "active" => 1,
            "admin" => 0,
            "removed" => 0,
            "token" => uniqid()
        );
        
        // create user
        $uid = $this->aHandler->admincreateuser($this->controller, $user_data, $aid,$dbConfig);
        
        // verify that no error is returned
        $this->assertEquals(false,$this->controller->getErrorFlag(),$failureAffects);
        
        // verify that a new user has been created and we are able to load it
        $this->assertEquals(true,(false != $u->loadByUsername($username)),$failureAffects);
        
        // verify that the uid is correct
        $this->assertEquals($uid,$u->getId(),$failureAffects);
    }
}