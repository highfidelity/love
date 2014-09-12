<?php
//  vim:ts=4:et

//  Copyright (c) 2009-2010, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com
$app_root_path = dirname(__FILE__)."/";

require_once($app_root_path.'send_email.php');

// Autoloader
function __autoload($class)
{
    $file = realpath(dirname(__FILE__) . '/classes') . "/$class.class.php";
    if (file_exists($file)) {
        require_once($file);
    }
}


function checkReferer() {
    $len = strlen(SERVER_NAME);
    if (   empty($_SERVER['HTTP_REFERER'])
    || (   substr($_SERVER['HTTP_REFERER'], 0, $len + 7) != 'http://'.SERVER_NAME
    && substr($_SERVER['HTTP_REFERER'], 0, $len + 8) != 'https://'.SERVER_NAME)) {
        return false;
    } else {
        return true;
    }
}

// Get the userId from the session, or set it to 0 for Guests.
function getSessionUserId() {
	return isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : 0;
}


/* initSessionData
 *
 * Initializes the session data for a user.  Takes as input either a username or a an array containing
 * data from a row in the users table.
 *
 * NOTE: keep this function in sync with the same function in journal!!!
 */
function initSessionData($user) {
    if (!is_array($user)) {
        $res = mysql_query("select * from ".REVIEW_USERS." where username='".mysql_real_escape_string($user)."'");
        $user_row = (($res) ? mysql_fetch_assoc($res) : null);
        if (empty($user_row)) return;
    } else {
        $user_row = $user;
    }

    $_SESSION['username']           = $user_row['username'];
    $_SESSION['userid']             = $user_row['id'];
    $_SESSION['confirm_string']     = $user_row['confirm_string'];
    $_SESSION['nickname']           = $user_row['nickname'];
    $_SESSION['timezone']           = $user_row['timezone'];
    $_SESSION['is_runner']          = intval($user_row['is_runner']);
    $_SESSION['is_payer']           = intval($user_row['is_payer']);
    $_SESSION['is_auditor']         = intval($user_row['is_auditor']);
    $_SESSION['is_admin']         = intval($user_row['is_admin']);
    $_SESSION['rewarder_points']         = intval($user_row['rewarder_points']);
    $_SESSION['rewarder_limit_day']         = intval($user_row['rewarder_limit_day']);
}

function isEnabled($features) {
    if (empty($_SESSION['features']) || ($_SESSION['features'] & $features) != $features) {
        return false;
    } else {
        return true;
    }
}

function isSuperAdmin() {
    if (empty($_SESSION['features']) || ($_SESSION['features'] & FEATURE_SUPER_ADMIN) != FEATURE_SUPER_ADMIN) {
        return false;
    } else {
        return true;
    }
}



/*  Function: countLoveToUser
 * 
 *  Purpose: Gets the count of love sent to a user.
 *  
 *  Parameters: username - The username of the desired user.
 *              fromUser - If set will get the love sent by this user. 
 */
function countLove($username, $fromUsername="") {
    defineSendLoveAPI();

    if($fromUsername != "") {
        $params = array (
                'action' => 'getcount',
                'api_key' => LOVE_API_KEY,
                'username' => $username,
                'fromUsername' => $fromUsername);
    } else {
        $params = array (
                'action' => 'getcount',
                'api_key' => LOVE_API_KEY,
                'username' => $username);
    }
    $referer = (empty($_SERVER['HTTPS'])?'http://':'https://').$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
    $retval = json_decode(postRequest (LOVE_API_URL, $params, array(CURLOPT_REFERER, $referer)), true);

    if ($retval['status'] == "ok") {
        return $retval['data']['count'];
    } else {
        return -1;
    }
}

/*  Function: getUserLove
 * 
 *  Purpose: Get Love sent to the user
 *  
 *  Parameters: username - The username of the user to get love from.
 *              fromUsername - If set it will filter to the love sent by this username.
 *		startDate    - review period start date 
 *		endDate     - review period end date 
 */
function getUserLove($username, $fromUsername = "", $startDate = "", $endDate = "") {
    defineSendLoveAPI();
    $params = array (
		'action' => 'getlove',
		'api_key' => LOVE_API_KEY,
		'username' => $username,
		'startDate' => $startDate, 
		'endDate' => $endDate,
		'pagination' => 0 );
	
    if($fromUsername != "") {
	$params['fromUsername'] = $fromUsername;
    }

    $referer = (empty($_SERVER['HTTPS'])?'http://':'https://').$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
    $retval = json_decode(postRequest (LOVE_API_URL, $params, array(CURLOPT_REFERER, $referer)), true);
    
    if (isset($retval['status']) && $retval['status'] == "ok") {
        return $retval['data'];
    } else {
        return -1;
    }
}

function defineSendLoveAPI() {
    // Sendlove API status and error codes. Keep in sync with .../sendlove/api.php
    // only define constants once
    if (!defined('SL_OK')){
        define ('SL_OK', 'ok');
        define ('SL_ERROR', 'error');
        define ('SL_WARNING', 'warning');
        define ('SL_NO_ERROR', '');
        define ('SL_NO_RESPONSE', 'no response');
        define ('SL_BAD_CALL', 'bad call');
        define ('SL_DB_FAILURE', 'db failure');
        define ('SL_UNKNOWN_USER', 'unknown user');
        define ('SL_NOT_COWORKER', 'receiver not co-worker');
        define ('SL_RATE_LIMIT', 'rate limit');
        define ('SL_SEND_FAILED', 'send failed');
        define ('SL_JOURNAL_FAILED', 'journal failed');
        define ('SL_NO_SSL', 'no ssl call');
        define ('SL_WRONG_KEY', 'wrong api key');
    }
}



/*  Function: GetUserList
 *
 *  Purpose: This function return a list of confirmed users.
 *
 *  Parameters: userid - The userid of the user signed in.
 *              nickname - The nickname of the user signed in.
 *              skipUser - If true, don't include the row for the user passed in.
 *              attrs - list of additional attributes to return
 */
function GetUserList($userid, $nickname, $skipUser=false, $attrs=array()) {
    if (!empty($attrs)) {
        $extra = ", `" . implode("`,`", $attrs) . "`"; 
    } else {
        $extra = "";
    }

    $rt = mysql_query("SELECT `id`, `nickname` $extra  FROM ".REVIEW_USERS." WHERE `id`!='{$userid}' AND `confirmed`='1' AND `is_active` = 1 ORDER BY `nickname`");

    $userList = array();
    if (!$skipUser && !empty($userid) && !empty($nickname)) {
        $skipUser = true;
        $userList[] = array('id'=>$userid,'nickname'=>$nickname);
    }

    while ($rt && $row = mysql_fetch_assoc($rt)) {
        if (!$skipUser || $userid != $row['id']) {
            if (empty($attrs)) {
                $userList[] = array('id'=>$row['id'], 'nickname'=>$row['nickname']);
            } else {
                $userList[] = array('id'=>$row['id'],'nickname'=>$row['nickname'], 'attr'=>$attrs);
            }
        }
    }

    return $userList;
}

/* postRequest 
 *
 * Function for performing a CURL request given an url and post data.
 * Returns the results.
 */
function postRequest($url, $post_data) {
    if (!function_exists('curl_init')) {
        error_log('Curl is not enabled.');
        return 'error: curl is not enabled.';
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

// ends review period and sends emails to participants
function endReviewPeriod($reset, $conversion_rate, $signature){

    $email_template = $conversion_rate ? 'end-period-conversion' : 'end-period';
    $template_data = array(
                        'date' => date("F j, Y"),
                        'signature' => $signature,
                            );

    foreach(Rewarder::getCurrentReceivers() as $receiver){

        $user_template_data = $template_data;
        $user_template_data['points'] = $receiver['received_points'];
        $user_template_data['people'] = $receiver['givers'];

        if($conversion_rate){
            $user_template_data['worth'] = $conversion_rate;
            $user_template_data['total_earnings'] = $receiver['received_points'] * $conversion_rate;
        }

        sendTemplateEmail($receiver['username'], $email_template , $user_template_data);
    }

    // makes changes to the database finishing the rewarder
    Rewarder::markPaidAll();

    // reseting all rewarder balances to 0
    if($reset){
        $sql = "UPDATE " . REVIEW_USERS . " SET `rewarder_points` = 0";
        mysql_unbuffered_query($sql);
    }
}


