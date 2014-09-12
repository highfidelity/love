<?php
/**
 * Application bootstrapping
 *
 * @category   LoveMachineLogin
 * @package    Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version    SVN: $Id: bootstrap.php 46 2010-04-17 08:53:45Z seong $
 * @edited     26-MAY-2010 <Yani>
 * @link       http://www.lovemachineinc.com
 */
if (!defined('APPLICATION_BASE')) {
    $appBase = dirname(dirname(__FILE__));
    /**
     * The application base directory
     */
    define('APPLICATION_BASE', $appBase);
} else {
    $appBase = APPLICATION_BASE;
}
$libDir = $appBase . '/lib';
if (!file_exists($libDir . '/loadlib.php')) {
    die('Application base error.');
}

if (!defined('APPLICATION_DIR')) {
    /**
     * Main application dir
     */
    define('APPLICATION_DIR', dirname(__FILE__));
}

/**
 * Load library
 */
require_once $libDir . '/loadlib.php';
unset($appBase, $libDir);

/**
 * Config
 */
if (file_exists(APPLICATION_DIR . '/config/config.php')) {
    require_once APPLICATION_DIR . '/config/config.php';
} elseif (file_exists(APPLICATION_DIR . '/config/config.dist.php')) {
    require_once APPLICATION_DIR . '/config/config.dist.php';
} else {
    throw new Exception('Config file not found.');
}

/**
 * Application
 */
require_once APPLICATION_DIR . '/loadapp.php';

/**
 * Front controller
 */
$frontController = Controller::getInstance();

// unset global vars
unset($frontController);
