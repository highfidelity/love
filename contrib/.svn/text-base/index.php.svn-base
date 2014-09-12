<?php

// Allow to load a local config file,
// server.local.php needs to load before anything might be defined.
if (file_exists(dirname(__FILE__). '/server.local.php')) {
    include_once(dirname(__FILE__). '/server.local.php');
}

if (!defined('CONTRIB_PATH')) define('CONTRIB_PATH', dirname(__FILE__));

// Js compressor default date
if (!defined("LIB_DEFAULT_DATE")) define("LIB_DEFAULT_DATE", "false");

function __autoload($class_name) {
    require_once CONTRIB_PATH . '/classes/' . $class_name . '.php';
}

$app = new Application();
$app->run();
