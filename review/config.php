<?php
//
//  Copyright (c) 2009-2010, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com
//

if (file_exists('server.local.php')) {
    include_once('server.local.php');
}
else if (file_exists('../server.local.php')) {
    include_once('../server.local.php');
}

if (!defined('APP_NAME'))       define('APP_NAME','Rewarder');
if (!defined('APP_LOCATION'))   define('APP_LOCATION',substr($_SERVER['SCRIPT_NAME'], 1, strrpos($_SERVER['SCRIPT_NAME'], '/')));
if (!defined('APP_BASE'))       define('APP_BASE',substr(APP_LOCATION, 0, strrpos(APP_LOCATION, '/', -2)));
if (!defined('APP_PATH'))	    define('APP_PATH', realpath(dirname(__FILE__)));
if (!defined('UPLOAD_PATH'))	define('UPLOAD_PATH', realpath(APP_PATH . '/uploads'));
if (!defined('LOVE'))           define('LOVE', 'love_dev.love');

if (!defined('APP_ENV'))	    define('APP_ENV', 'production');

// this is the name of the app that will be used when
// authenticating with login service.
// change it per app.
if (!defined("REVIEW_SERVICE_NAME"))   define("REVIEW_SERVICE_NAME", 'reviewmachine');

//http[s]://[[SECURE_]SERVER_NAME]/[LOCATION/]index.php   #Include a TRAILING / if LOCATION is defined
if (!defined('SERVER_NAME'))    define('SERVER_NAME','dev.sendlove.us');

//Include [:port] for standard http traffic if not :80
if (!defined('SERVER_URL'))     define('SERVER_URL','http://'.SERVER_NAME.'/'.APP_LOCATION); 
if (!defined('SERVER_BASE'))    define('SERVER_BASE','http://'.SERVER_NAME.'/'.APP_BASE);

if (!defined('LOVE_LOCATION'))    define('LOVE_LOCATION','http://'.SERVER_NAME.'/'.LOVE);

//SSL Not enabled on development
//define("SECURE_SERVER_URL",'https://'.SERVER_NAME.'/'.APP_LOCATION); 
//Secure domain defaults to standard; Include [:port] for secure https traffic if not :443
//So clone the standard URL
if (!defined('SECURE_SERVER_URL')) define('SECURE_SERVER_URL',SERVER_URL); //Secure domain defaults to standard; Include [:port] for secure https traffic if not :443

// Define Review Application url
if (strpos(SECURE_SERVER_URL,'https')) {
    define ('SECURE_PROTOCOL','https://');
} else { 
    define ('SECURE_PROTOCOL','http://');
}
if (!defined("REVIEW_URL"))     define("REVIEW_URL",SECURE_PROTOCOL.SERVER_NAME."/review");
if (!defined("LOVE_URL"))     define("LOVE_URL",SECURE_PROTOCOL.SERVER_NAME."/love");

if (!defined("APP_LOGIN")) define("APP_LOGIN", '/login/index.php/');
if (!defined("LOGIN_APP_URL"))  define("LOGIN_APP_URL",'https://'.SERVER_NAME.APP_LOGIN);

if (!defined('FEEDBACK_EMAIL')) define('FEEDBACK_EMAIL', 'feedback@lovemachineinc.com');

// Define the contrib libs repo
if(isset($_SERVER["HTTPS"])){
    if (!defined("CONTRIB_URL"))     define("CONTRIB_URL",'https://' . $_SERVER['SERVER_NAME'] . '/contrib/');
} else {
    if (!defined("CONTRIB_URL"))     define("CONTRIB_URL",'http://' . $_SERVER['SERVER_NAME'] . '/contrib/');
}

// key to identificate api users
if (!defined("REVIEW_API_KEY"))    define("REVIEW_API_KEY", "dhfsfdhgdhsfg7g5fyg73ff23545f32fwd");

if (!defined('DB_SERVER'))      define('DB_SERVER', 'mysql.dev.sendlove.us');
if (!defined('DB_USER'))        define('DB_USER', 'project_stage');
if (!defined('DB_PASSWORD'))    define('DB_PASSWORD', 'test30');
if (!defined('DB_NAME'))        define('DB_NAME', 'review_dev');

//if (!defined('USERS'))          define('USERS', 'users');
// Fixes wrong love count when getting loved users for period
if (!defined('LOVE_USERS'))        define("LOVE_USERS", "love_users");
if (!defined('REVIEW_WS_SESSIONS'))    define('REVIEW_WS_SESSIONS', 'ws_sessions');
if (!defined('REVIEW_REWARDER'))	    define('REVIEW_REWARDER', 'rewarder_distribution');
if (!defined('REVIEW_REWARDER_LOG'))	define('REVIEW_REWARDER_LOG', 'rewarder_log');
if (!defined('REVIEW_PERIODS'))	define('REVIEW_PERIODS', 'periods');
if (!defined('REVIEW_USERS'))	define('REVIEW_USERS', 'users');
if (!defined('LOVE_LOVE'))	define('LOVE_LOVE', 'love.love');
if (!defined('REVIEW_TOKENS'))		define('REVIEW_TOKENS', 'tokens');

if (!defined('SALT'))           define('SALT', 'REWARDER');
if (!defined('SESSION_EXPIRE')) define('SESSION_EXPIRE', 365*24*60*60);
if (!defined('REQUIRELOGINAFTERCONFIRM')) define('REQUIRELOGINAFTERCONFIRM', 1);


if (!defined("LOVE_API_URL")) define("LOVE_API_URL", LOVE_URL."/api.php");
if (!defined("LOVE_API_KEY")) define("LOVE_API_KEY", "uierbycur4yt73467t6trtycff3rt");

if (!defined("MAILAUTHSERVER")) define("MAILAUTHSERVER",'localhost');
if (empty($mail_user) || !is_array($mail_user)) {
  $mail_user = array (
  'authuser' => array (
    'from' => 'SendLove <love@sendlove.us>',
    'replyto' => 'SendLove <love@sendlove.us>',
    ),
  'loveuser' => array (
    'from' => 'SendLove <love@sendlove.us>',
    'replyto' => 'SendLove <love@sendlove.us>',
    ),
  );
}



