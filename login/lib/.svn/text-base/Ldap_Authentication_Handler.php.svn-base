<?php
/**
 * Db_Password_Authentication_Handler
 *
 * @category   LoveMachine
 * @package    Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version    SVN: $Id: Ldap_Authentication_Handler.php 2010-06-9 yani $
 * @link       http://www.lovemachineinc.com
 */
/**
 * Ldap_Authentication_Handler
 * 
 * Handles the LDAP backed authentication. If the given userid / password is valid in LDAP, returns User object
 *
 * @category   LoveMachine
 * @package    Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link       http://www.lovemachineinc.com
 */

class Ldap_Authentication_Handler extends Authentication_Handler {

    var $ldap_connection = NULL;
    
    public function update(Controller_Action $controller, $user_data){
        $result = NULL;
        if($this->user->loadById($user_data["userid"])){
	    //we should not receive this field under ldap
            if(isset($user_data["oldpassword"])){
                    $controller->setError("Invalid password.");
        	    return $result;
            }
            if(!$controller->getErrorFlag()){
                foreach($user_data as $key => $value){
                    if($key != "userid" && $key != "oldpassword" && $key != "newpassword" ){
                        $method = 'set' . DataObject::camelize($key);
                        $this->user->$method($value);
                    }
	    	    //we should not receive this field under ldap
                    if($key == "newpassword"){
                        $controller->setError("Invalid password.");
        		return $result;
                    }
                }
                try{
                    if($this->user->save()){
                        $result = true;
                    }else{
                        $controller->setError("Unable to save new user data.");
                    }
                }catch(Exception $e){
                    $controller->setError($e->getMessage());
                }
            }
        }else{
            $controller->setError("User doesn't exist.");
        }
        return $result;
    }

    
    public function setuserdata(Controller_Action $controller, $user_data, $admin_id){
        return NULL;
    }
    
    public function adminresettoken(Controller_Action $controller, $user_id, $admin_id){
        return NULL;
    }
    
    public function getuserdata(Controller_Action $controller, $user_id, $admin_id){
        return NULL;
    }

    /**
    * Authenticate the user
    *
    * @var Controller_Action
    * @var Username
    * @var Password
    * @return if success user's object otherwise NULL.
    */
  
    public function authenticate (Controller_Action $controller, $username, $password) {
        $user = NULL;
	if(!$this->ldapConnect()) {
            $controller->setError('Could not connect to the LDAP Server.');
	    return $user;
	}
	//If the login id is not the COMMON_NAME_ATTRIBUTE, find the value to bind
	if (defined('LDAP_ALT_LOGIN_ATTRIBUTE') && LDAP_ALT_LOGIN_ATTRIBUTE!==false) {
	    $ldap_commonName=$this->getLdapCommonName(LDAP_ALT_LOGIN_ATTRIBUTE . '=' . $username);
	} else {
	    $ldap_commonName=$username;
	}
	$ldap_username = $this->getLdapUsername($ldap_commonName);
error_log("Ldap: $ldap_commonName : $ldap_username");
        
	$login_status = $this->ldapBind($ldap_username, $password);  
	 
	if($login_status) {
//             if ($sr=ldap_read($this->ldap_connection, LDAP_USER_DN,"(objectclass=*)",array('samaccountname'))) {
//		error_log("LdapDump: ".json_encode(ldap_get_entries($this->ldap_connection,$sr)));
//	     }
           $ldapEmail = $this->getLdapEmail(LDAP_COMMON_NAME_ATTRIBUTE . "=" . $ldap_commonName);
           if($this->user->loadByUsername($ldapEmail)){
                if($this->user->isActive()){
                    $user = $this->user;
                }else{
                    $controller->setError("User is deactivated.");
                }
            }else{
		  //Ldap users are already authenticated. If they don't exist yet, take care of it.
		   $user = new LoveUser();
                   $data = array("Username" => $ldapEmail, "Password" => 'LDAP', "Nickname" => array_shift(split('@',$ldapEmail)), "Active" => 1, "Confirmed" => 1, "Removed" => 0, "Admin" => 0, "Token" => '', "DateAdded" => 0, "DateModified" => 0);
                   $user->loadData($data);
                   try{
                        $id = $user->save();
                        $result = $id;
                   } catch(Exception $e){
                        $controller->setError($e->getMessage());
                   }
            }
        } else {
            $controller->setError("Invalid login");
        }      
        $this->ldapClose();
	return $user;

    }

    /**
    * Builds username for LDAP query
    *
    * @var Username
    * @var Password
    * @return username
    */

    private function getLdapUsername($username) {
	return  LDAP_COMMON_NAME_ATTRIBUTE. "=" . $username . "," . LDAP_USER_DN;
    }

    /**
    * Connects to the LDAP Server
    *
    * @return LDAP connect string
    */
    private function ldapConnect() {

	return $this->ldap_connection = ldap_connect(LDAP_SERVER_HOSTNAME, LDAP_SERVER_PORT);
    }
    
    /**
    * Bind the user 
    *
    * @var Username
    * @var Password
    * @return if success resource otherwise false/null.
    */

    private function ldapBind($username, $password) {
      
	return @ldap_bind($this->ldap_connection ,$username, $password);
    }

    /**
    * UnBind the user 
    *
    * @return if success resource otherwise false/null.
    */

    private function ldapUnBind() {
      
	return @ldap_unbind($this->ldap_connection);
    }

    /**
    * Close the LDAP connection
    */
    private function ldapClose() {

	return ldap_close($this->ldap_connection);
    }

    /**
    * Get LDAP users email field
    *
    * @var Username
    * @return if success user's email otherwise null.
    */
    private function getLdapEmail($commonName) {
          $userEmail = null;
          $sr=ldap_search($this->ldap_connection, LDAP_USER_DN,$commonName);
          if($sr) {
              $info = ldap_get_entries($this->ldap_connection, $sr);
              $userEmail = $info[0][LDAP_USERNAME_ATTRIBUTE][0];  
          }
         return $userEmail;
    }

    /**
    * Get LDAP users common name by alternate login id
    *
    * @var email
    * @return if success user's common name null.
    */
    private function getLdapCommonName($loginId) {
          $userEmail = null;
	  $login_status = $this->ldapBind('cn=admin-lm,ou=service accounts,ou=administrators,dc=windows,dc=organic,dc=com', 'KEYb0ard');  
          $sr=ldap_search($this->ldap_connection, LDAP_USER_DN,$loginId,array(LDAP_USERNAME_ATTRIBUTE,LDAP_COMMON_NAME_ATTRIBUTE));
          if($sr) {
              $info = ldap_get_entries($this->ldap_connection, $sr);
              $commonName = $info[0][LDAP_COMMON_NAME_ATTRIBUTE][0];  
          }
         return $commonName;
    }
}

?>
