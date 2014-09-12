<?php
/**
 * Application index
 *
 * @category   LoveMachineLogin
 * @package    Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version    SVN: $Id: index.php 46 2010-04-17 08:53:45Z seong $
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

if (!defined('APPLICATION_DIR')) {
    /**
     * Main application dir
     */
    define('APPLICATION_DIR', $appBase . '/application');
}

if (!defined('APPLICATION_NAME')) {
    $appName = "application";
    /**
     * The application name
     */
    define('APPLICATION_NAME', $appName);
} else {
    $appName = APPLICATION_NAME;
}

try {
    /**
     * Bootstrapping
     */
    require_once APPLICATION_DIR . '/bootstrap.php';
} catch (Exception $e) {
    $msg  = 'An error occured while bootstrapping:' . "\n";
    $msg .= "\n" . ' ' . $e->getMessage();
    if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'development') {
        $msg .= "\n\nFile: " . $e->getFile();
        $msg .= "\nLine: "   . $e->getLine();
        $msg .= "\n" . $e->getTraceAsString();
    } else if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'testing') {
        $msg .= "<br /><br />File: " . $e->getFile();
        $msg .= "<br />Line: "   . $e->getLine();
        $msg .= "<br />" . $e->getTraceAsString();
    }
    echo $msg;
    exit();
}
// dispatch
Controller::getInstance()->dispatch();
exit(0);
