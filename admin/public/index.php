<?php

if (is_dir("../../htdocs")) { 
	$bootstrap = "../../htdocs/autoconfig.php";
} elseif ( file_exists( dirname(dirname(dirname(__FILE__))) . '/autoconfig.php') ) {
	$bootstrap = dirname(dirname(dirname(__FILE__))) . '/autoconfig.php';
} else {
	$bootstrap = "../../public_html/autoconfig.php";
}

if (file_exists($bootstrap)) {
    require($bootstrap);
} else if (file_exists("../application/configs/static.php")) {
    require("../application/configs/static.php");
} else {
    die("Welcome To the SendLove. Please setup your configuration environment");
}

defined('GOOGLE_USER') or define('GOOGLE_USER', $cupid_arrays['mail_auth']['gmail-ssl']['username']);
defined('GOOGLE_PWD') or define('GOOGLE_PWD', $cupid_arrays['mail_auth']['gmail-ssl']['password']);
defined('GOOGLE_HOST') or define('GOOGLE_HOST', $cupid_arrays['mail_auth']['gmail-ssl']['host']);
defined('GOOGLE_PORT') or define('GOOGLE_PORT', $cupid_arrays['mail_auth']['gmail-ssl']['port']);
defined('GOOGLE_SSL') or define('GOOGLE_SSL', false);
defined('GOOGLE_AUTH') or define('GOOGLE_AUTH', $cupid_arrays['mail_auth']['gmail-ssl']['auth']);


// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
if ($_SERVER['SERVER_ADDR'] == '10.242.46.112') {
	$app_env = 'production';
} else {
	if (strpos($_SERVER['PHP_SELF'], '~') === false) {
		$app_env = 'staging';
	} else {
		$app_env = 'development';
	}
}
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', $app_env);
unset($app_env);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();
