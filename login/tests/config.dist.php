<?php
/**
 * Unit test default config
 *
 * @category   LoveMachine
 * @package    UnitTests
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version    SVN: $Id: config.dist.php 105 2010-10-10 21:45:37Z yani $
 * @link       http://www.lovemachineinc.com
 */

// user config
// whether to test db capabilities
if (!defined('TESTS_USER_MYSQLI_TEST')) {
    define('TESTS_USER_MYSQLI_TEST', false);
}
// db host
if (!defined('TESTS_USER_MYSQLI_TEST_DB_HOST')) {
    define('TESTS_USER_MYSQLI_TEST_DB_HOST', '127.0.0.1');
}
// db username
if (!defined('TESTS_USER_MYSQLI_TEST_DB_USERNAME')) {
    define('TESTS_USER_MYSQLI_TEST_DB_USERNAME', 'username');
}
// db password
if (!defined('TESTS_USER_MYSQLI_TEST_DB_PASSWORD')) {
    define('TESTS_USER_MYSQLI_TEST_DB_PASSWORD', 'password');
}
// db name
if (!defined('TESTS_USER_MYSQLI_TEST_DB_DATABASE')) {
    define('TESTS_USER_MYSQLI_TEST_DB_DATABASE', 'mydatabase');
}

if(!defined('LOGIN_USER_TABLE')) define("LOGIN_USER_TABLE","login_users");
if(!defined("LDAP_ENABLED")) define("LDAP_ENABLED", false);

 