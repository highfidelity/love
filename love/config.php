<?php
//
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
// 

if (defined("CONFIGLOADED")) {return; }
define ("CONFIGLOADED",true);

if (file_exists(dirname(dirname(__FILE__)) . '/autoconfig.php')) {
    require_once(dirname(dirname(__FILE__)) . '/autoconfig.php');
} else {
    // We don't want to run the app w/o autoconfig.php
    // Inform the user to set up configuration & exit
    header("HTTP/1.0 404 Not Found");
    die("Application configuration not found. If this is your sandbox, try:<br/><pre>" .
        "cd ".dirname(__FILE__)."/..\n" .
        "cp autoconfig.php.default autoconfig.php</pre><br/><br/>");
}

if (file_exists(dirname(__FILE__) . '/server.local.php')) {
    include_once(dirname(__FILE__) . '/server.local.php');
} else {
    // Similarly, we don't want to run the app w/o server.local.php
    // Inform the user to set up configuration & exit
    header("HTTP/1.0 404 Not Found");
    die("Application configuration not found. If this is your sandbox, try:<br/><pre>" .
        "cd ".dirname(__FILE__)."\n".
        "cp server.local.php.autodefault server.local.php</pre>" .
        "Once the file is in place, make sure to configure SANDBOX_URL_BASE" .
        " to point to your <strong>sendlove</strong> directory, ie:<br/><pre>" .
        "define('SANDBOX_URL_BASE', " . dirname(__FILE__));
}


if (!defined('SANDBOX_URL_BASE'))  define('SANDBOX_URL_BASE','');

if (!defined("APP_NAME"))       define("APP_NAME","SendLove.us");
if (!defined('APP_PATH'))     define('APP_PATH', realpath(dirname(__FILE__)));

if (!defined("APP_LOCATION")) {
  if(isset($_SERVER['SCRIPT_NAME'])) {
    define("APP_LOCATION",substr($_SERVER['SCRIPT_NAME'], 1, strrpos($_SERVER['SCRIPT_NAME'], "/")));
  } else {
    define("APP_LOCATION",basename(dirname(__FILE__)));
  }
}

if (!defined("APP_ROOT"))       define("APP_ROOT", substr(APP_LOCATION, 0, strrpos(APP_LOCATION, "/", -2)));
if (!defined("APP_JOURNAL"))    define("APP_JOURNAL", 'journal');
if (!defined("APP_WORKLIST"))   define("APP_WORKLIST", 'worklist');
if (!defined("APP_LOGIN"))      define("APP_LOGIN", '/login/index.php/');
if (!defined("REVIEW_APP_API"))      define("REVIEW_APP_API", 'https://' . $_SERVER['SERVER_NAME'] . SANDBOX_URL_BASE . '/review/api.php');

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

// Define the contrib libs repo
if(isset($_SERVER["HTTPS"])){
    if (!defined("CONTRIB_URL"))     define("CONTRIB_URL",'https://' . $_SERVER['SERVER_NAME'] . SANDBOX_URL_BASE . '/contrib/');
} else {
    if (!defined("CONTRIB_URL"))     define("CONTRIB_URL",'http://' . $_SERVER['SERVER_NAME'] . SANDBOX_URL_BASE . '/contrib/');
}

//SSL Not enabled on development
//define("SECURE_SERVER_URL",'https://'.SERVER_NAME.'/'.APP_LOCATION); //Secure domain defaults to standard; Include [:port] for secure https traffic if not :443
//So clone the standard URL
if (!defined("SECURE_SERVER_URL")) define("SECURE_SERVER_URL",SERVER_URL); //Secure domain defaults to standard; Include [:port] for secure https traffic if not :443

if (strpos(SECURE_SERVER_URL,'https')) { define ('SECURE_PROTOCOL','https://'); } else { define ('SECURE_PROTOCOL','http://'); }

if (!defined("LOGIN_APP_URL"))  define("LOGIN_APP_URL",'https://'.SERVER_NAME.APP_LOGIN);

if (!defined('UPLOAD_PATH'))  define('UPLOAD_PATH', realpath(APP_PATH . '/uploads'));

if (!defined('MAIN_COMPANY'))	define('MAIN_COMPANY', 1);

if (!defined('ALL_ASSETS'))	define('ALL_ASSETS', 'all_assets');


if (!defined("DB_SERVER"))      define("DB_SERVER", "mysql.dev.sendlove.us");
if (!defined("DB_USER"))        define("DB_USER", "project_stage");
if (!defined("DB_PASSWORD"))    define("DB_PASSWORD", "test30");
if (!defined("DB_NAME"))        define("DB_NAME", "love_dev");

if (!defined("LOVE"))           define("LOVE", "love");
if (!defined("USERS"))          define("USERS", "users");
if (!defined("COMPANY"))        define("COMPANY", "companies");
if (!defined("LIMITS"))         define("LIMITS", "limits");
if (!defined("EMAILUPDATES"))	define("EMAILUPDATES", "email_updates");
if (!defined('WS_SESSIONS'))    define('WS_SESSIONS', 'ws_sessions');
if (!defined('TOKENS'))    define('TOKENS', 'tokens');


if (!defined("REVIEW_DB_NAME")) define("REVIEW_DB_NAME", "review_dev");
if (!defined("PERIODS"))        define("PERIODS", "`" . REVIEW_DB_NAME . "`.`periods`");
if (!defined("USER_REVIEWS"))   define("USER_REVIEWS", "`" . REVIEW_DB_NAME . "`.`user_reviews`");
if (!defined("REVIEW_LOVES"))   define("REVIEW_LOVES", "`" . REVIEW_DB_NAME . "`.`review_loves`");
if (!defined("REVIEW_LOVE_LIMIT"))   define("REVIEW_LOVE_LIMIT", 7);
if (!defined("FAVORITE_LOVE_LIMIT"))   define("FAVORITE_LOVE_LIMIT", 7);

if (!defined("SALT"))           define("SALT", "SENDLOVE");
if (!defined("SESSION_EXPIRE")) define("SESSION_EXPIRE", 1440);

if (!defined("GUEST_USER"))     define("GUEST_USER", "guest@lovemachineinc.com");

if (!defined("JOURNAL_API_URL"))     define("JOURNAL_API_URL", "https://www.worklist.net/worklist/add.php");
if (!defined("JOURNAL_API_USER"))    define("JOURNAL_API_USER", "api_sendlove@dev.sendlove.us");
if (!defined("JOURNAL_API_PWD"))     define("JOURNAL_API_PWD", "journalpwd");
if (!defined("JOURNAL_API_COMPANY")) define("JOURNAL_API_COMPANY", "1");

if (!defined("REVIEW_URL"))  define("REVIEW_URL",SECURE_PROTOCOL.SERVER_NAME.SANDBOX_URL_BASE.'/review');

if (!defined("REVIEW_API_URL")) define("REVIEW_API_URL", REVIEW_URL . '/api.php');

// real api key is set up in server.local.php
if (!defined("REVIEW_API_KEY")) define("REVIEW_API_KEY", "");
    
if (!defined("ADMIN_URL")) define("ADMIN_URL", ".././admin/");

// Applicable for development server - to fetch company logo and background image from admin_dev database 
if (!defined("DB_NAME_ADMIN")) 	define("DB_NAME_ADMIN", "admin_dev");

// key to identificate api users
if (!defined("API_KEY"))    define("API_KEY", "uierbycur4yt73467t6trtycff3rt");

// Refresh interval for ajax updates of the history table (in seconds)
if (!defined("AJAX_REFRESH"))   define("AJAX_REFRESH", 30);
if (!defined("JCACHE_DELAY"))   define("JCACHE_DELAY", 10);

// Js compressor default date
if (!defined("LIB_DEFAULT_DATE")) define("LIB_DEFAULT_DATE", "false");

//pagination vars
if (!defined("QS_VAR"))         define("QS_VAR", "page");

if (!defined("STR_FWD"))        define("STR_FWD", "&nbsp;&nbsp;Next");
if (!defined("STR_BWD"))        define("STR_BWD", "Prev&nbsp;&nbsp;");
if (!defined("IMG_FWD"))        define("IMG_FWD", "images/left.png");
if (!defined("IMG_BWD"))        define("IMG_BWD", "images/right.png");

if (!defined("NUM_LINKS"))      define("NUM_LINKS", 5); // number of links to show
if (!defined("NUM_ROWS"))       define("NUM_ROWS", 10); // number of records per page

if (!defined("MAX_LOGO_WIDTH"))      define("MAX_LOGO_WIDTH", 200); // max height of the company logo
if (!defined("MAX_LOGO_HEIGHT"))     define("MAX_LOGO_HEIGHT", 200); // max width of company logo

if (!defined("MAX_NICKNAME_CHARS"))               define("MAX_NICKNAME_CHARS", 20);
if (!defined("MAX_DISPLAY_NICKNAME_CHARS"))       define("MAX_DISPLAY_NICKNAME_CHARS", 15);
if (!defined("MAX_DISPLAY_NICKNAME_REPLACE"))     define("MAX_DISPLAY_NICKNAME_REPLACE", "..");

if (!defined("COMPANY_NAME"))   define("COMPANY_NAME", "SendLove"); // Set to a value to force all users into one company

if (!defined("PROFILE_PICTURE_HEIGHT")) define("PROFILE_PICTURE_HEIGHT", 75);
if (!defined("PROFILE_PICTURE_WIDTH"))  define("PROFILE_PICTURE_WIDTH", 75);

//Spam Filtering
if (!defined("AKISMET_URL"))    define("AKISMET_URL", "http://dev.sendlove.us/~tcrowe/");
if (!defined("AKISMET_KEY"))    define("AKISMET_KEY", "dc7ec67bcf2c");

// closed system - love can go only to users
if (!defined("SEND_LOVE_OUTSIDE_INSTANCE"))       define('SEND_LOVE_OUTSIDE_INSTANCE', false);
//sending love via email defualt setting Y for yes and N for no
if (!defined("DEF_SENDLOVE_VIA_EMAIL"))    define("DEF_SENDLOVE_VIA_EMAIL", "Y");
// default emai address for sending love
if (!defined("LOVEMAIL"))    define("LOVEMAIL", "to@dev.sendlove.us");
// send email on settings update
if (!defined("LOVE_SETTINGS_UPDATE_EMAIL"))       define('LOVE_SETTINGS_UPDATE_EMAIL', false);

//Default cloud map (dies without one)
if (!defined("DEFAULT_CLOUD_MAP"))    define("DEFAULT_CLOUD_MAP", "clouds/lovecloud.map");

//Define email From username and display names for sent messages (for example, only registered emails are permitted to send through some gateways)
//This should degrade to the first array if the specific name does not exist. you could just pass a live array or set $headers['From'] before calling.
//In most cases this simply requires changing $header('Content-Type: ....') to $header['Content-Type'] and making sure the header builder creates the plaintext version correctly. (to degrade to php mail() if PEAR:Mail not available)
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
  'smsuser' => array (
    'from' =>  'SendLove SMSReply <sms@sendlove.us>',
    'replyto' => 'SendLove SMSReply <sms@sendlove.us>'
    )
  );
}


/*
 * Non-configuration values (CONSTANTS)
 */

// Premium Features
//
if (!defined('REQUIRELOGINAFTERCONFIRM')) define("REQUIRELOGINAFTERCONFIRM", true);

// User features: bits in the users.features column
if (!defined('FEATURE_SUPER_ADMIN')) define("FEATURE_SUPER_ADMIN", 0x0001);
if (!defined('FEATURE_USER_MASK')) define("FEATURE_USER_MASK", 0x0001);

// Company features: bits in the company.features column
if (!defined('FEATURE_BULK_INVITE')) define("FEATURE_BULK_INVITE", 0x0100);
if (!defined('FEATURE_REMOVE_USERS')) define("FEATURE_REMOVE_USERS",  0x0200);
if (!defined('FEATURE_COMPANY_MASK')) define("FEATURE_COMPANY_MASK",  0x0300);

if (!defined('SMS_ENABLED')) define('SMS_ENABLED', true);
if (!defined('SMS_CODE_LEXICON')) define("SMS_CODE_LEXICON",      "BCDFGHJKLMNPQRSTVWXZ");

if (!defined("INCLUDE_WORKLIST_URL"))define("INCLUDE_WORKLIST_URL", false);
if (!defined("INCLUDE_JOURNAL_URL"))define("INCLUDE_JOURNAL_URL", false);
if (!defined("INCLUDE_REWARDER_URL"))define("INCLUDE_REWARDER_URL", false);
if (!defined("INCLUDE_REVIEW_URL"))define("INCLUDE_REVIEW_URL", false);

function connect() {
//global $con;

  $con = mysql_connect(DB_SERVER,DB_USER,DB_PASSWORD);
  mysql_select_db(DB_NAME,$con);
  return $con;

}
