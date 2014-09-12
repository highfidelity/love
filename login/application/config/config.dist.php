<?php
/**
 * Login application distribution config
 *
 * @category   LoveMachineLogin
 * @package    Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version    SVN: $Id: config.dist.php 46 2010-04-17 08:53:45Z seong $
 * @edited     26-MAY-2010 <Yani>
 * @link       http://www.lovemachineinc.com
 */
// The application environment
// suggested values:
// - development
// - testing
// - production
$applicationEnvironment = 'development';
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/autoconfig.php');

if(!defined('LOGIN_USER_TABLE')) define('LOGIN_USER_TABLE','users');
if(!defined('SANDBOX_URL_BASE')) define('SANDBOX_URL_BASE','');

$applicationEnvironment = defined('APP_ENV')?APP_ENV:'development';

/* Modify */
 $dbConfig = array();
 $dbConfig['adapter']  = defined('DB_ENGINE')?DB_ENGINE:'mysqli';
 $dbConfig['host']     = defined('DB_SERVER')?DB_SERVER:'mysql.dev.sendlove.us';
 $dbConfig['dbname']   = defined('DB_NAME')?DB_NAME:'login_dev';
 $dbConfig['username'] = defined('DB_USER')?DB_USER:'project_stage';
 $dbConfig['password'] = defined('DB_PASSWORD')?DB_PASSWORD:'test30';

/* Registred Applications for push function */
$regApps = array(
	'lovemachine'		=> false,
	'reviewmachine'	=> false,
	'worklistmachine'		=> false,
  'journalmachine'   => false
);

if (!empty($cupid_arrays['regApps'])) {
    $regApps=array_merge($regApps,$cupid_arrays['regApps']);
} else {

    $regApps['lovemachine'] = array(
	'endpoint'		=> 'https://' . SERVER_URL . SANDBOX_URL_BASE . '/love/api.php',
	'key'			=> API_KEY
    );

    $regApps['reviewmachine'] = array(
	'endpoint'		=> 'https://' . SERVER_URL . SANDBOX_URL_BASE . '/review/api.php',
    'key'           => REVIEW_API_KEY
    );

    $regApps['worklistmachine'] = array(
	'endpoint'		=> 'https://' . SERVER_URL . SANDBOX_URL_BASE . '/worklist/api.php',
    'key'           => WORKLIST_API_KEY
    );

    $regApps['journalmachine'] = array(
	'endpoint'		=> 'https://' . SERVER_URL . SANDBOX_URL_BASE . '/journal/api.php',
    'key'           => JOURNAL_API_KEY
    );

}

/**
 * Internal
 */
if (!defined('APPLICATION_ENV')) {
    /**
     * The application environment
     */
    define('APPLICATION_ENV', $applicationEnvironment);
}

if (!defined("SESSION_EXPIRE")) define("SESSION_EXPIRE", 1440);
if (!defined("LDAP_ENABLED")) define("LDAP_ENABLED", false);

if (!defined("LDAP_SERVER_HOSTNAME")) define("LDAP_SERVER_HOSTNAME" , "184.73.32.218");
if (!defined("LDAP_SERVER_PORT")) define("LDAP_SERVER_PORT" , 389);
if (!defined("LDAP_USER_DN")) define("LDAP_USER_DN" , "ou=people,dc=sendlove,dc=us");
if (!defined("LDAP_COMMON_NAME_ATTRIBUTE")) define("LDAP_COMMON_NAME_ATTRIBUTE" , "cn");
if (!defined("LDAP_USERNAME_ATTRIBUTE")) define("LDAP_USERNAME_ATTRIBUTE" , "mail");
unset($applicationEnvironment);
