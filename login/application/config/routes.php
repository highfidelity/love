<?php
/**
 * Routes
 *
 * @category LoveMachine
 * @package  Login
 * @author   LoveMachine Inc. <all@lovemachineinc.com>
 * @license  Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version  SVN: $Id: routes.php 2010-05-26 2:22:22Z yani $
 * @link     http://www.lovemachineinc.com
 */

/*
 * Below is a list of actions and the controllers
 * these actions are assigned to.
 * 
 * To add a new action:
 * $router['test'] = "LoginController"
 * When the browser points to:
 * dev.sendlove.us/login/index.php/test
 * the test function in LoginController will be called.
 */
$route['default'] = "IndexController";
$route['index']   = "IndexController";
$route['login']   = "LoginController";
$route['logout']  = "LoginController";
$route['create']  = "LoginController";
$route['update']  = "LoginController";
$route['delete']  = "LoginController";
$route['confirm']  = "LoginController";
$route['resettoken'] = "LoginController";
$route['changepassword'] = "LoginController";
$route['notify'] = "LoginController";
$route['getuserdata'] = "LoginController";
$route['setuserdata'] = "LoginController";
$route['adminresettoken'] = "LoginController";
$route['getuserlist'] = 'LoginController';
$route['admincreateuser'] = 'LoginController';
$route['admincreateusers'] = 'LoginController';
$route['pushadminuser'] = 'LoginController';
