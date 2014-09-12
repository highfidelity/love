<?php
/**
 * Load application files
 *
 * @category LoveMachineLogin
 * @package  Core
 * @author   LoveMachine Inc. <all@lovemachineinc.com>
 * @license  Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version  SVN: $Id: loadapp.php 105 2010-10-10 21:45:37Z yani $
 * @edited   26-MAY-2010 <Yani>
 * @link     http://www.lovemachineinc.com
 */
if (!defined('APPLICATION_BASE')) {
    /**
     * Application base directory
     */
    define('APPLICATION_BASE', dirname(__FILE__));
}
if (!defined('APPLICATION_DIR')) {
    /**
     * Main application dir
     */
    define('APPLICATION_DIR', dirname(__FILE__));
}

// login controller
Loader::registerClass('LoginController', APPLICATION_DIR . '/controller/LoginController.php');
// Love user
Loader::registerClass('LoveUser', APPLICATION_DIR . '/model/LoveUser.php');
// user
Loader::registerClass('User', APPLICATION_DIR . '/model/User.php');
Loader::registerClass('User_Exception', APPLICATION_DIR . '/model/User/User_Exception.php');
Loader::registerClass('User_DbException', APPLICATION_DIR . '/model/User/User_DbException.php');
