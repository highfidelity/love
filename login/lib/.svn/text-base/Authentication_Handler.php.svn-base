<?php
/**
 * Authentication_Handler class
 * 
 * Base class for Authentication Handlers
 *
 * @category   LoveMachine
 * @package    Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link       http://www.lovemachineinc.com
 */

abstract class Authentication_Handler {
    
    /**
     * @var Controller_Action
     * @var Username
     * @var Password
     */
    
    var $user = null;
    
    function __construct($db = null) {
        if (is_null($db)) {
            $this->user = new LoveUser();
        } else {
            $this->user = new LoveUser($db);
        }
    }
    
    abstract public function authenticate(Controller_Action $controller, $username, $password);
    abstract public function getuserdata(Controller_Action $controller, $userid, $adminid);
    abstract public function setuserdata(Controller_Action $controller, $user_data, $admin_id);
    abstract public function adminresettoken(Controller_Action $controller, $userid, $adminid);
    abstract public function update(Controller_Action $controller, $userdata);
}

?>
