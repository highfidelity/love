<?php
//
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
// 

if (defined("CONFIGLOADED")) {return; }
define ("CONFIGLOADED",true);

if (file_exists(dirname(__FILE__) . '/server.local.php')) {
    include_once(dirname(__FILE__) . '/server.local.php');
}

if (!defined("APP_NAME"))       define("APP_NAME","SendLove.us");
if (!defined('APP_PATH'))     define('APP_PATH', realpath(dirname(__FILE__)));

if (!defined('API_KEY'))	define("API_KEY", "qxuakwyjhqp4zo7wt2ie");

if (!defined("APP_LOCATION")) {
  if(isset($_SERVER['SCRIPT_NAME'])) {
    define("APP_LOCATION",substr($_SERVER['SCRIPT_NAME'], 1, strrpos($_SERVER['SCRIPT_NAME'], "/")));
  } else {
    define("APP_LOCATION",basename(dirname(__FILE__)));
  }
}

if (!defined("APP_ROOT"))       define("APP_ROOT", substr(APP_LOCATION, 0, strrpos(APP_LOCATION, "/", -2)));
if (!defined("APP_LOGIN"))      define("APP_LOGIN", '/login/index.php/');

// this is the name of the app that will be used when
// authenticating with login service.
// change it per app.
if (!defined("SERVICE_NAME"))   define("SERVICE_NAME", 'lovemachine');

//http[s]://[[SECURE_]SERVER_NAME]/[LOCATION/]index.php   #Include a TRAILING / if LOCATION is defined
if (!defined("SERVER_NAME"))    define("SERVER_NAME","dev.sendlove.us");
if(isset($_SERVER["HTTPS"])){
    if (!defined("SERVER_URL"))     define("SERVER_URL",'https://'.SERVER_NAME.'/'.APP_LOCATION); //Include [:port] for standard http traffic if not :80
} else {
    if (!defined("SERVER_URL"))     define("SERVER_URL",'http://'.SERVER_NAME.'/'.APP_LOCATION); //Include [:port] for standard http traffic if not :80
}
//SSL Not enabled on development
//define("SECURE_SERVER_URL",'https://'.SERVER_NAME.'/'.APP_LOCATION); //Secure domain defaults to standard; Include [:port] for secure https traffic if not :443
//So clone the standard URL
if (!defined("SECURE_SERVER_URL")) define("SECURE_SERVER_URL",SERVER_URL); //Secure domain defaults to standard; Include [:port] for secure https traffic if not :443

if (strpos(SECURE_SERVER_URL,'https')) { define ('SECURE_PROTOCOL','https://'); } else { define ('SECURE_PROTOCOL','http://'); }
#login stuff
if (!defined("APP_LOGIN"))      define("APP_LOGIN", '/logon/index.php/');
if (!defined("LOGIN_APP_URL"))  define("LOGIN_APP_URL",'https://'.SERVER_NAME.APP_LOGIN);
if (!defined('WS_SESSIONS'))    define('WS_SESSIONS', 'ws_sessions');
if (!defined('TOKENS'))         define('TOKENS', 'tokens');
if (!defined('SALT'))           define('SALT', 'WORKLIST');
if (!defined('SESSION_EXPIRE')) define('SESSION_EXPIRE', 365*24*60*60);
if (!defined('REQUIRELOGINAFTERCONFIRM')) define('REQUIRELOGINAFTERCONFIRM', 1);


if (!defined('UPLOAD_PATH'))  define('UPLOAD_PATH', realpath(APP_PATH . '/uploads'));

if (!defined('MAIN_COMPANY'))	define('MAIN_COMPANY', 1);

if (!defined("DB_SERVER"))      define("DB_SERVER", "mysql.dev.sendlove.us");
if (!defined("DB_USER"))        define("DB_USER", "project_stage");
if (!defined("DB_PASSWORD"))    define("DB_PASSWORD", "test30");
if (!defined("DB_NAME"))        define("DB_NAME", "sales_dev");

if (!defined('WS_SESSIONS'))    define('WS_SESSIONS', 'ws_sessions');
if (!defined('LOVE_WS_SESSIONS'))    define('LOVE_WS_SESSIONS', 'love_ws_sessions');
if (!defined('TOKENS'))         define('TOKENS', 'tokens');

if (!defined("SALESDB"))	define("SALESDB",'sales_dev');
if (!defined("CUSTOMERS"))      define("CUSTOMERS", SALESDB.".customers");
if (!defined("BUYERS"))      define("BUYERS", SALESDB.".buyers");
if (!defined("PAYMENTS"))       define("PAYMENTS", SALESDB.".payments");
if (!defined('PERIODS'))	define('PERIODS', 'review_periods');
if (!defined('REVIEW_REWARDER'))	define('REVIEW_REWARDER', 'review_rewarder_distribution');
if (!defined('USER_REVIEWS'))	define('USER_REVIEWS', 'review_user_reviews');
if (!defined('USERS'))	define('USERS', 'love_users');
if (!defined('LOVE'))	define('LOVE', 'love_love');
if (!defined('REDEEM'))	define('REDEEM', 'redeem');


if (!defined("JOURNAL_API_URL"))     define("JOURNAL_API_URL", "https://dev.sendlove.us/journal/add.php");
if (!defined("JOURNAL_API_USER"))    define("JOURNAL_API_USER", "api_sales@dev.sendlove.us");
if (!defined("JOURNAL_API_PWD"))     define("JOURNAL_API_PWD", "journalpwd");
if (!defined("JOURNAL_API_COMPANY")) define("JOURNAL_API_COMPANY", "1");


function connect() {
//global $con;

  $con = mysql_connect(DB_SERVER,DB_USER,DB_PASSWORD);
  mysql_select_db(DB_NAME,$con);
  return $con;

}

?>
