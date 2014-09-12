<?php
//  Copyright (c) 2010, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

  /**
   add.php -- Send love via incoming HTTP call.
   POST parameters:
     caller (required) -- who is calling, must be on an appoved list
     from (required) -- nickname of the user sending love
     to (required) -- nickname of the user receiving love
     why (required) -- reason for sending love
   Returns JSON.
  */
#ini_set('display_errors', 1); error_reporting(-1);var_dump('Test');
require 'config.php';
require 'functions.php';
require 'send_email.php';
require('class/Session.class.php');
require_once('class/LoveUser.class.php');
require_once('class/Utils.class.php');
require_once('class/CURLHandler.php');
require_once('class/Database.class.php');
require_once('oauth/linkedin.class.php');

// Constants: Keep in sync with .../journal/love.bot.class.php
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

/**
 * respond -- Send an array, encoded as JSON, back to the caller and exit.
 * @param $val The array to send.
 * @return It doesn't. It exits.
 */
function respond ($val) {
    exit (json_encode ($val));
}

$rsp = array ('status' => SL_ERROR,       // SL_OK, SL_WARNING or SL_ERROR
	          'error'  => SL_NO_RESPONSE  // error type
	         );

// Check that we have a secure line
if (empty($_SERVER['HTTPS']) && ($_REQUEST['action'] != 'uploadProfilePicture') && ($_REQUEST['action'] != 'checkConfirmation')) {
  $rsp['error'] = SL_NO_SSL;
  respond ($rsp);
}

if (!isset($_REQUEST['api_key']) ||
    (isset($_REQUEST['api_key']) && ( $_REQUEST['api_key'] != API_KEY  && $_REQUEST['api_key'] != SALES_API_KEY ) && $_REQUEST['action'] != 'uploadProfilePicture')) {
  $rsp['error'] = SL_WRONG_KEY;
  respond ($rsp);
}

if(!empty($_REQUEST['action'])){

    // Connect to db
    $db = @mysql_connect (DB_SERVER, DB_USER, DB_PASSWORD);
    if (!$db || !@mysql_select_db (DB_NAME, $db)) {
	    $rsp['error'] = SL_DB_FAILURE;
	    $rsp['info'] = mysql_error();
	    respond ($rsp);
    }

    switch($_REQUEST['action']){
        
    case 'sendlove':
        sendFromJournal();
        break;
        
    case 'sendlovemsg':
        sendlove();
        break;

    case 'getReportCount':
        // Get 7 and 14 day total, unique Senders and Givers
	// Respond in json
        getLoveReportCount();
        break;
		
    case 'getuniquecount':
        getUniqueLoveCount();
        break;

    case 'getAllLove':
        getAllLove();
        break;

    case 'getlove':
        // If $_REQUEST['giver'] or $_REQUEST['receiver'] is set,
        // it will filter the results accordingly.
        // $_REQUEST['pagination'] == 0 -> Disables paginated output
        getUserLove();
        break;
    
    case 'uploadProfilePicture':
    	uploadProfilePicture();
    	break;
    	
    case 'getProfilePicture':
    	getProfilePicture();
    	break;
    	
	case 'pushCreateUser':
		pushCreateUser();
		break;

    case 'pushVerifyUser':
    	pushVerifyUser();
    	break;
    
	case 'checkConfirmation':
		checkConfirmation();
		break;

    case 'login':
        loginUserIntoSession();
        break;

	case 'getUserlist':
		getUserlist();
		break;

	case 'getUser':
		getUser();
		break;

    case 'getNicknameByUsername':
        getNicknameByUsername();
        break;

	case 'saveUser':
		saveUser();
		break;

	case 'getInvitationUrl':
		getInvitationUrl();
		break;

	case 'sendloveToAll':
		sendloveToAll();
		break;
    
    case 'setUserRemoved':
        setUserRemoved();
        break;

	case 'adminCreateUsers':
	    adminCreateUsers();
	    break;

	case 'getLoveReceivedAmount':
		getLoveAmount();
		break;
	case 'getLoveSentAmount':
		getLoveAmount(true);
		break;
	case 'getLoveReceivedMessages':
		getLoveMessages();
		break;
	case 'getLoveSentMessages':
		getLoveMessages(true);
		break;
	case 'getUserLoveCount':
		getUserLoveCount();
		break;
	case 'getWeeklyUpdates':
		getWeeklyUpdates();
		break;
	case 'setWeeklyUpdates':
		setWeeklyUpdates();
		break;
	case 'sendWeeklyUpdates':
		sendWeeklyUpdates();
		break;
	case 'updateuser':
	    $user = new LoveUser();
        $userdata = $_REQUEST['user_data'];
	    if ($user->updateUser($userdata['userid'], $userdata['username'],
	                       $userdata['nickname'], $userdata['admin'])) {

	        respond(array(
	            'success' => true,
	            'message' => 'User updated'
	        ));
        } else {
	        respond(array(
	            'success' => false,
	            'message' => 'Failed to update the user'
	        ));
        }
	    break;
	case 'newSignupsReportData':
		newSignupsReportData();
		break;
        case 'updateLinkedInStatus':
		updateLinkedInStatus();
		break;
	case 'getActiveUsersCount':
		getActiveUsersCount();
		break;
	}

    // Respond positively
    $rsp['status'] = SL_OK;
    $rsp['error'] = SL_NO_ERROR;
    respond ($rsp);

}else{
	$rsp['error'] = SL_BAD_CALL;
	respond ($rsp);
}


/*
* Setting session variables for the user so he is logged in
*
*/
function loginUserIntoSession(){
    $user_id = intval($_REQUEST['user_id']);
    $username = $_REQUEST['username'];
    $nickname = $_REQUEST['nickname'];
    $admin = $_REQUEST['admin'];

    $session_id = $_REQUEST['session_id'];
    session_id($session_id);
    session::init();
    Utils::setUserSession($user_id, $username, $nickname, $admin);
}

function updateLinkedInStatus() {
    $API_CONFIG = array(
        'appKey'       => LINKEDIN_API_KEY_PUBLIC,
        'appSecret'    => LINKEDIN_API_KEY_PRIVATE,
        'callbackUrl'  => NULL 
    );

    $sql = "select `username`, `access_token`, `access_token_secret` FROM `love_users` where `linkedin_share` = 1;";
    $result = mysql_query($sql);

    while ($row = mysql_fetch_assoc($result)) { 
        $sql = "select  count(*) as loves ,count(distinct giver) as givers from love_love where receiver = '" . $row['username'] . "' and at > DATE_SUB(CURDATE(),INTERVAL 7 DAY);";
        $result2 = mysql_query($sql);
        $row2 = mysql_fetch_assoc($result2);
       
        if((int)$row2['givers'] > 0 && (int)$row2['loves'] > 0) {
            $OBJ_linkedin = new LinkedIn($API_CONFIG);
            $OBJ_linkedin->setTokenAccess(array('oauth_token' => $row['access_token'], 'oauth_token_secret' => $row['access_token_secret']));
            
            $response = $OBJ_linkedin->share('new', array('comment' => "This week's love stats: " . $row2['loves'] . " love from " . $row2['givers'] . " people."), FALSE);
            
            //DEBUG OAUTH TOKENS IF NEEDED
            /*
            if($response['success'] === TRUE) {
                error_log("DATA SENT TO LINKEDIN!");
            } else {
                error_log("Error revoking user's token:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>");
            }
            */
        }
    }
}

function getWeeklyUpdates() {
	$sql = 'SELECT `weekday`, `hour`, `minute` FROM `' . EMAILUPDATES . '` WHERE `company_id` = "' . (int)MAIN_COMPANY . '";';
	$result = mysql_query($sql);
	if ($result && (mysql_num_rows($result) == 1)) {
		$row = mysql_fetch_assoc($result);
		respond(array(
			'success' => true,
			'settings' => $row
		));
	}
	respond(array(
		'success' => false,
		'message' => 'Weekly updates are not activated.'
	));
}

function setWeeklyUpdates() {
	$sql = 'SELECT `weekday`, `hour`, `minute` FROM `' . EMAILUPDATES . '` WHERE `company_id` = "' . (int)MAIN_COMPANY . '";';
	$result = mysql_query($sql);
	if ($result && (mysql_num_rows($result) == 1)) {
		if ($_REQUEST['active'] == 'true') {
			$sql = 'UPDATE `' . EMAILUPDATES . '` SET `weekday` = ' . (int)$_REQUEST['weekday'] . ', `hour` = ' . (int)$_REQUEST['hour'] . ', `minute` = ' . (int)$_REQUEST['minute'] . ' WHERE `company_id` = ' . MAIN_COMPANY . ';';
			mysql_unbuffered_query($sql);
			$sql = 'UPDATE `' . COMPANY . '` SET `weekly_updates` = 1 WHERE `id` = ' . MAIN_COMPANY . ';';
			mysql_unbuffered_query($sql);
			respond(array(
				'success' => true,
				'message' => 'Weekly updates have been updated.'
			));
		} else {
			$sql = 'DELETE FROM `' . EMAILUPDATES . '` WHERE `company_id` = ' . MAIN_COMPANY . ';';
			mysql_unbuffered_query($sql);
                        $sql = 'UPDATE `' . COMPANY . '` SET `weekly_updates` =	0 WHERE	`id` = ' . MAIN_COMPANY	. ';';
                        mysql_unbuffered_query($sql);
			respond(array(
				'success' => true,
				'message' => 'Weekly updates have been deactivated.'
			));
		}
	} else {
		$sql = 'INSERT INTO `' . EMAILUPDATES . '` VALUES (' . MAIN_COMPANY . ', ' . (int)$_REQUEST['weekday'] . ', ' . (int)$_REQUEST['hour'] . ', ' . (int)$_REQUEST['minute'] . ');';
		mysql_unbuffered_query($sql);
                $sql = 'UPDATE `' . COMPANY . '` SET `weekly_updates` =	1 WHERE	`id` = ' . MAIN_COMPANY	. ';';
                mysql_unbuffered_query($sql);

		respond(array(
			'success' => true,
			'message' => 'Weekly updates have been activated.'
		));
	}
}

function sendWeeklyUpdates() {
	$time = time() - 28800; // removing 8 hours for PDT
	$date = getdate($time);

	$sql = 'SELECT `id` FROM `' . COMPANY . '` WHERE `weekly_updates` = 1;';
	$result = mysql_query($sql);

	if ($result && (mysql_num_rows($result) > 0)) {
		while ($row = mysql_fetch_array($result)) {
			$sql = 'SELECT `weekday`, `hour`, `minute` FROM `' . EMAILUPDATES . '` WHERE `company_id` = "' . (int)$row['id'] . '";';
			$resDate = mysql_query($sql);
			$rowDate = mysql_fetch_assoc($resDate);		

			mysql_free_result($resDate);

			// continue if we don't send today
			if ((int)$rowDate['weekday'] != $date['wday']) {
				continue;
			}
		
			// continue if we don't send in this hour
			if ((int)$rowDate['hour'] != $date['hours']) {
				continue;
			}
		
			// continue if minute is in the future
			if (((int)$rowDate['minute'] > $date['minutes']) || (($date['minutes'] - (int)$rowDate['minute']) >= 15)) {
				continue;
			}
		
			$lastWeek = date('Y-m-d H:i:s', strtotime('- 1 week', $time));
			$sql = 'SELECT `giver`, `receiver`, `why` FROM `' . LOVE . '` WHERE `company_id` = "' . (int)$row['id'] . '" AND `private` = 0 AND `at` > "' . $lastWeek . '";';
			$resLove = mysql_query($sql);
		
			$tblStyle = 'font-family: Lucida Sans Unicode, Lucida Grande, Sans-Serif;font-size: 12px;width: 700px;text-align: left;border-collapse: collapse;margin: 20px;';
			$tblHStyle = 'font-size: 14px;font-weight: normal;color: #039;padding: 10px 8px;';
			$tblCStyle = 'color: #669;padding: 8px;';
			$tblOStyle = 'background: #e8edff;';
			$counter = 0;
		
			$tbl  =	'<table border="0" style="' . $tblStyle . '">' . "\n";
			$tbl .=		'<thead>' . "\n";
			$tbl .=			'<tr>' . "\n";
			$tbl .=				'<th style="' . $tblHStyle . '">Giver</th>' . "\n";
			$tbl .=				'<th style="' . $tblHStyle . '">Receiver</th>' . "\n";
			$tbl .=				'<th style="' . $tblHStyle . '">Message</th>' . "\n";
			$tbl .=			'</tr>' . "\n";
			$tbl .=		'</thead>' . "\n";
		
			$tbl .=		'<tbody>' . "\n";
			while ($rowLove = mysql_fetch_array($resLove)) {
				$counter++;
				$tbl .=		'<tr>' . "\n";
				$tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($rowLove['giver']) . '</td>' . "\n";
				$tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($rowLove['receiver']) . '</td>' . "\n";
				$tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($rowLove['why']) . '</td>' . "\n";
				$tbl .=		'</tr>' . "\n";
			}
			$tbl .= 	'</tbody>' . "\n";
		
			$tbl .=	'</table>' . "\n";
		
			mysql_free_result($resLove);
		
			$receivers = array();
			$sql = 'SELECT `username` FROM `' . USERS . '` WHERE `company_id` = "' . (int)$row['id'] . '";';
			$resReceivers = mysql_query($sql);
			while ($rowReceivers = mysql_fetch_array($resReceivers)) {
				$receivers[] = $rowReceivers['username'];
			}
			mysql_free_result($resReceivers);

			sendTemplateEmail($receivers, 'weeklyupdates', array(
				'app_name' => APP_NAME,
				'table' => $tbl
			));	
		}
	
		mysql_free_result($result);
	}
}

/**
 * Check if a user exists
 */
function userExists($id) {
    $sql = "SELECT * FROM ".USERS." WHERE `id`={$id}";
    $query = mysql_query($sql) or error_log(mysql_error());
    
    if (mysql_num_rows($query) != 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Set a user removed or not
 */
function setUserRemoved($id, $removed) {
    $sql = "UPDATE ".USERS." SET `removed`={$removed}, `active`=1 WHERE `id`={$id}";
    return mysql_query($sql);
}

// Add the user passed by Login to the Db
function adminCreateUsers() {
    $skip_query = false;
    // Create SQL statements
    $sql = "INSERT INTO ".USERS." ";
    $columns = "(";
    $values = "VALUES(";
    
    // Take the first user and add the columns
    foreach ($_REQUEST["user_data"][0] as $key => $val) {
        if ($key == "username" ||
            $key == "nickname" ||
            $key == "confirmed" ||
            $key == "Active" ||
            $key == "removed" ||
            $key == "id" ||
            $key == "status") {

            $columns .= "`".$key."`,";
        }
    }
    
    // For each user add the values to the query
    foreach ($_REQUEST["user_data"] as $user) {
        if (userExists($user['id'])) {
            if (!setUserRemoved($user['id'], $user['removed'])) {
                error_log(mysql_error());
            }
            if (count($_REQUEST['user_data']) > 1) {
                continue;
            } else {
                $skip_query = true;
            }
        }
        
        foreach($user as $name => $value){            
            if ($name == "username" ||
                $name == "nickname" ||
                $name == "removed" ||
                $name == "status") {
                
                $values .= "'" . $value . "', ";
            }
            if ($name == "confirmed" ||
                $name == "Active" ||
                $name == "id") {
                    
                // Store the user id
                if ($name == "id") {
                    $id = $value;
                }
                    
                $values .= $value . ", ";
            }
        }
        $values = substr_replace($values, "", -2);
        $values .= "), (";
    }
    
    $columns = substr_replace($columns, "", -1);
    $columns .= ") ";
    $values = substr_replace($values, "", -3);
    
    // Merge SQL statements
    $sql = $sql.$columns.$values;
    
    // Execute query
    if (!$skip_query) {
        mysql_query($sql) or error_log(mysql_error());
    }

    // Get the users that actually did make it to the Db
    $validUsers = userCreationSucceded($_REQUEST["user_data"]);
    
    // If all the users were added pass a success = true message
    if (count($validUsers) == count($_REQUEST["user_data"])) {
        // Send the response back to the caller app
        respond(array(
            'success' => true,
            'users' => $validUsers
        ));
    } else { // If not pass a success = false
        // Send the response back to the caller app
        respond(array(
            'success' => false,
            'users' => $validUsers
        ));
    }
}

function getNicknameByUsername() {
    require_once('class/UserInfo.php');
    if(isset($_REQUEST['username'])){
        $user = urldecode($_REQUEST['username']);
        $userInfo = new UserInfo();
            $userInfo->loadUserByUsername($user, 1);

        if($userInfo->getId()){
            respond(array(
                'success' => true,
                'nickname' => $userInfo->getNickname()
            ));
        }
    }
    respond(array(
        'success' => false,
        'params' => ''
    ));
}
/**
 * Get users from DB and compare it with the supplied
 * array, return the users that are present in both.
 */
function userCreationSucceded($newUsers = array()) {
    $user = new LoveUser();
    $currentUsers = $user->getUserList();
    $existingUsers = array();
    $found = false;
    
    foreach ($newUsers as $newUser) {
        foreach ($currentUsers as $currentUser) {
            // If we can find the entry's username on the Db take it as the user exists
            if (searchMultiArray($currentUser, 'username', $newUser['username'])) {
                // Set the found flag as true
                $found = true;
            }
        }
        // If the user has not been found we add it
        if (!$found) {
            // Add it to the existing users array
            $existingUsers[] = $newUser;
        }
    }
    
    return $existingUsers;
}

/**
 * Seach a multidimensional array for $key->value
 * Returns true if found false if not found.
 */
function searchMultiArray($array, $key = '', $value = '') {
    // If @array is empty, return not found
    if (!is_array($array) || empty($array)) {
        return false;
    }

    foreach ($array as $subArray) {
        if ($subArray[$key] == $value) {
            return true;
        }
    }

    return false;
}

function pushCreateUser() {
    $user_id = intval($_REQUEST['id']);
    $username = mysql_real_escape_string($_REQUEST['username']);
    $nickname = mysql_real_escape_string($_REQUEST['nickname']);

	// getting default company_id
	$company_id = MAIN_COMPANY;
    $company_admin = ($_REQUEST['admin'] ? 1 : 0);
    
    $db = new Database();

	$sql = " INSERT INTO " . USERS . " 
	            (`id`, `username`, `nickname`, `send_love_via_email`, `phone`, `country`, `provider`, `skill`, `team`, `company_id`, `company_admin`, `company_confirm`, `confirmed`, `removed`)
	            VALUES ($user_id, '$username', '$nickname', '', '', '', '', null, null, $company_id, $company_admin, 1, 1, 0)";
    if (!$db->query($sql)) {
      error_log("add user failed: " . $db->getError());
      respond(array(
        'success' => false,
        'message' => 'failed to create user'
      ));
    }

    respond(array(
        'success' => true,
        'message' => 'User has been created!'
    ));
}

// Check if the user with @username has the confirmed flag set
function checkConfirmation() {
	$sql = 'SELECT `company_id`, `confirmed`, `company_confirm` FROM `' . USERS . '` WHERE `username` = "' . mysql_real_escape_string($_REQUEST['username']) . '";';
	$result = mysql_query($sql);
	if ($result && (mysql_num_rows($result) == 1)) {
		$row = mysql_fetch_assoc($result);
		if (($row['company_id'] == MAIN_COMPANY) && ($row['confirmed'] == 1) && ($row['company_confirm'] != 0)) {
			respond(array(
				'success' => true,
				'message' => 'Access granted.'
			));
		} else {
			respond(array(
				'success' => false,
				'message' => 'Your membership with the company has not been confirmed yet.'
			));
		}
	}
	
	respond(array(
		'success' => false,
		'message' => 'Your user can not be found.'
	));
	
}

// Sets user as confirmed
function pushVerifyUser() {
    $user_id = intval($_REQUEST['id']);
    $sql = "UPDATE " . USERS . " SET `confirmed` =  '1' WHERE `id` = $user_id";
    mysql_unbuffered_query($sql);

	respond(array(
		'success' => false,
		'message' => 'User has been confirmed!'
	));
}

function uploadProfilePicture() {
    // check if we have a file
    if (empty($_FILES)) {
        respond(array(
              'success' => false,
              'message' => 'No file uploaded!'
        ));
     }
    
    if (empty($_REQUEST['userid'])) {
        respond(array(
            'success' => false,
            'message' => 'No user ID set!'
        ));
    }

    $ext = end(explode(".", $_FILES['profile']['name']));
    $tempFile = $_FILES['profile']['tmp_name'];
    $imgName = strtolower($_REQUEST['userid'] . '.' . $ext);
    $query = 'UPDATE `'.USERS.'` SET `picture` = "' . mysql_real_escape_string($imgName) . '" WHERE `id` = ' . (int)$_REQUEST['userid'] . ' LIMIT 1;';
    
//  no need to move file, put it straight in database
//  $path = dirname(__FILE__);
//  $path = $path . UPLOAD_PATH. '/' . $_REQUEST['userid'] . '.' . $ext;
//  if (move_uploaded_file($tempFile, $path)) { 

    if (! mysql_query($query)) {
        respond(array(
            'success' => false,
            'message' => SL_DB_FAILURE
        ));
    } else {
        $file = $tempFile;
        $rc = null;
        $type = null;
        if ($ext == "JPG" || $ext == "jpg" || $ext == "JPEG" || $ext == "jpeg") {
            $rc = imagecreatefromjpeg($file);
            $type = "image/jpeg";
        } else if ($ext == "GIF" || $ext == "gif") {
            $rc = imagecreatefromgif($file);
            $type = "image/gif";
        } else if ($ext == "PNG" || $ext == "png") {
            $rc = imagecreatefrompng($file);
            $type = "image/png";
        }
               
        // Get original width and height
        $width = imagesx($rc);
        $height = imagesy($rc);
        $cont = addslashes(fread(fopen($file,"r"),filesize($file)));
        $size = filesize($file);
        $sql = "INSERT INTO " . ALL_ASSETS . " 
            (`app`, `content_type`, `content`, `size`, `filename`,`created`, `width`, `height`)
            VALUES('".LOVE."','" . $type . "','" . $cont . "','" . $size . "','" . $imgName . "',NOW()," . $width . "," . $height . ") 
            ON DUPLICATE KEY UPDATE content_type = '".$type."', content = '".$cont."', size = '".$size."', updated = NOW(), width = ".$width.", height = ".$height;
               
            $db = new Database();
            if (file_exists($tempFile)) { unlink($tempFile); } 

            if (! $db->query($sql)) {
                respond(array(
                    'success' => false, 
                    'message' => "Error with: " . $file . " Error message: " . $db->getError())
                );
            } else {
                respond(array(
                    'success' => true, 
                    'picture' => $imgName
                ));
            }
     }
}

function getUserlist() {
	$query = 'SELECT `id`, `skill`, `team`, `picture` FROM `' . USERS . '`';
	$userlist = array();
	if (!($result = mysql_query($query))) {
		respond(array(
			'success' => false,
			'message' => SL_DB_FAILURE
		));
	} else {
		while ($row = mysql_fetch_assoc($result)) {
			if (empty($row['picture'])) {
				$picture = SERVER_URL . 'thumb.php?t=gUle&src=/images/no_picture.png&h=' . PROFILE_PICTURE_HEIGHT . '&w=' . PROFILE_PICTURE_WIDTH . '&zc=0';
			} else {
				$picture = SERVER_URL . 'thumb.php?t=gUl&src=/uploads/' . $row['picture'] . '&h=' . PROFILE_PICTURE_HEIGHT . '&w=' . PROFILE_PICTURE_WIDTH . '&zc=0';
			}
			$userlist[$row['id']] = array(
				'skill' => $row['skill'],
				'team' => $row['team'],
				'picture' => $picture
			);
		}
	}

	if (!empty($userlist)) {
		respond(array(
			'success' => true,
			'userlist' => $userlist
		));
	} else {
		respond(array(
			'success' => false,
			'message' => 'No users.'
		));
	}
}

function getUser() {
	$query = 'SELECT `id`, `skill`, `team`, `picture` FROM `' . USERS . '` WHERE `id` = ' . (int)$_REQUEST['user_id'] . ';';
	if (!($result = mysql_query($query))) {
		respond(array(
			'success' => false,
			'message' => SL_DB_FAILURE
		));
	} else {
		$row = mysql_fetch_assoc($result);
		if (empty($row['picture'])) {
			$picture = SERVER_URL . 'thumb.php?t=gUe&src=/images/no_picture.png&h=' . PROFILE_PICTURE_HEIGHT . '&w=' . PROFILE_PICTURE_WIDTH . '&zc=0';
		} else {
			$picture = SERVER_URL . 'thumb.php?t=gU&src=/uploads/' . $row['picture'] . '&h=' . PROFILE_PICTURE_HEIGHT . '&w=' . PROFILE_PICTURE_WIDTH . '&zc=0';
		}
		$user = array(
			'skill' => $row['skill'],
			'team' => $row['team'],
			'picture' => $picture
		);
	}

	if (!empty($user)) {
		respond(array(
			'success' => true,
			'user' => $user
		));
	} else {
		respond(array(
			'success' => false,
			'message' => 'No user.'
		));
	}
}

function saveUser() {
	$query = "UPDATE `" . USERS . "`
                    SET `team` = '" . mysql_real_escape_string($_REQUEST['team']) . "',
                        `skill` = '" . mysql_real_escape_string($_REQUEST['skill']) . "',
                        `active` = " . intval($_REQUEST['active']) . ",
                        `removed` = " . intval($_REQUEST['removed']) . " WHERE `id` = " . intval($_REQUEST['user_id']);

	if (!mysql_query($query)) {
		respond(array(
			'success' => false,
			'message' => SL_DB_FAILURE
		));
	} else {
		respond(array(
			'success' => true,
			'message' => 'User saved'
		));
	}
}

function getProfilePicture() {
	if (empty($_REQUEST['username'])) {
		respond(array(
			'success' => false,
			'message' => 'No user set!'
		));
	}
	
	$height = (!empty($_REQUEST['height']) ? (int)$_REQUEST['height'] : PROFILE_PICTURE_HEIGHT);
	$width = (!empty($_REQUEST['width']) ? (int)$_REQUEST['width'] : PROFILE_PICTURE_WIDTH);
	
	$query = 'SELECT `picture` FROM `'.USERS.'` WHERE `username` = "' . mysql_real_escape_string($_REQUEST['username']) . '" LIMIT 1;';
	if (!($result = mysql_query($query))) {
		respond(array(
			'success' => false,
			'message' => SL_DB_FAILURE
		));
	} else {
		if (mysql_num_rows($result) == 0) {
			respond(array(
				'success' => true,
				'picture' => SERVER_URL . 'thumb.php?t=gPPn&src=/images/no_picture.png&h=' . $height . '&w=' . $width . '&zc=0'
			));
		} else {
			$row = mysql_fetch_assoc($result);
			if (!empty($row['picture'])) {
				respond(array(
					'success' => true,
					'picture' => SERVER_URL . 'thumb.php?t=gPPp&src=/uploads/' . $row['picture'] . '&h=' . $height . '&w=' . $width . '&zc=0'
				));
			} else {
				respond(array(
					'success' => true,
					'picture' => SERVER_URL . 'thumb.php?t=gPPs&src=/images/no_picture.png&h=' . $height . '&w=' . $width . '&zc=0'
				));
			}
		}
	}
}

function sendlove() {
    // Check that all required parameters exist
    if (empty($_POST['caller']) || empty($_POST['from']) ||
        empty($_POST['to']) || empty($_POST['why'])) {
        $rsp['error'] = SL_BAD_CALL;
        respond ($rsp);
    }
    // Prepare received data
    $to = mysql_real_escape_string (trim (setEncoding($_POST['to'])));
    $from = mysql_real_escape_string (trim (setEncoding($_POST['from'])));
    $why = smart_strip_tags(mysql_real_escape_string (trim (setEncoding($_POST['why']))));
    $private = isset($_POST['priv']) && (int)$_POST['priv'] > 0;

    // Can't send love to self
    if (strtolower($to) == strtolower($from)) {
        $rsp['error'] = SL_NOT_COWORKER;
        respond ($rsp);
    }

    // If the love it's from Sendlove this means it's internal (automated) love
    // and therefore it doesn't neeed more checks (it would not pass them).
    if (! ($from == 'Sendlove')) {
        // Check that to and from nicknames exist and find their data
        foreach (array('from', 'to') as $v) {
            $query = ("select id, fb_id, username, nickname, company_id, skill, team " .
                    "from ".USERS." where nickname='".$$v."' and removed = 0");
            $res = mysql_query ($query);
            $line = mysql_fetch_array ($res, MYSQL_ASSOC);
            if ($res && $line) {
                $$v = $line;
            } else {
                $rsp['error'] = SL_UNKNOWN_USER;
                respond ($rsp);
            }
        }

        // Check rate limit
        if (enforceRateLimit('love', $from['id'])) {
            error_log("User ".$from['id']." send love was rate limited.");
            $rsp['error'] = SL_RATE_LIMIT;
            respond($rsp);
        }
        
        // Send love
	// MailToLove means the user should have received the love directly, do not resend
	if ($_POST['caller']!='mailtolove') {
	$lovestatus='new';
        $result = sl_send_love ($from['username'], $from['nickname'], $from['id'],
                  $from['company_id'], $to['username'], $why, false, $private);

        if ($result !== true) {
            $rsp['error'] = SL_SEND_FAILED;
            $rsp['result'] = $result;
            respond ($rsp);
        } 
	$lovestatus='sent';
	} else {
	$lovestatus="mail2love";
	}

        // Record love in database
        $company = $to['company_id'] == $from['company_id'] ? ", company_id={$to['company_id']}" : "";

        $priv_str = $private ? ', private=1' : '';
        $query = ("insert into ".LOVE." set giver='{$from['username']}', receiver='{$to['username']}', " .
                "skill='{$from['skill']}', team='{$from['team']}', why='$why', at=now(), status='$lovestatus'".$company.$priv_str);
                
        $rsp['status'] = SL_OK;
        $rsp['error'] = SL_NO_ERROR;
        $rsp['info'] = $query;

        if (!mysql_query($query)) {
            error_log("Add Love.err:".mysql_error());
            $rsp['error'] = SL_DB_FAILURE;
            respond ($rsp);
        }  else {
            respond($rsp);
        }
    } else {
        // Send love
        if (!sl_send_love ($from['username'], $from['nickname'], $from['id'],
                $from['company_id'], $to['username'], $why, false, $private, true)) {
            $rsp['error'] = SL_SEND_FAILED;
            respond ($rsp);
        }

        // Record love in database
        $company = ", company_id=1";
        
        $priv_str = $private ? ', private=1' : '';
        $query = ("insert into ".LOVE." set giver='{$from}', receiver='{$to}', " .
            "skill='', team='', why='$why', at=now()".$company.$priv_str);

        $rsp['status'] = SL_OK;
        $rsp['error'] = SL_NO_ERROR;
        $rsp['info'] = $query;
        if (!mysql_query($query)) {
            error_log("Add Love.err:".mysql_error());
            $rsp['error'] = SL_DB_FAILURE;
            respond ($rsp);
        }
    }
}

function sendloveToAll() {
	if (empty($_REQUEST['from']) || empty($_REQUEST['why'])) {
		respond(array(
			'success' => false,
			'message' => SL_BAD_CALL
		));
	}

	$query = 'SELECT `nickname` FROM `' . USERS . '` WHERE `nickname` != "' . mysql_real_escape_string($_REQUEST['from']) . '" AND `company_confirm` != 0;';
	if (!($result = mysql_query($query))) {
		respond(array(
			'success' => false,
			'message' => SL_DB_FAILURE
		));
	} else {
		while ($row = mysql_fetch_assoc($result)) {
			$array = array(
				'action'	=> 'sendlovemsg',
				'api_key'	=> API_KEY,
				'caller'	=> 'admin',
				'from'		=> (string)$_REQUEST['from'],
				'to'		=> (string)$row['nickname'],
				'why'		=> (string)smart_strip_tags($_REQUEST['why'])
			);
			CURLHandler::Post(SERVER_URL . 'api.php', $array, false, true);
		}
		respond(array(
			'success' => true,
			'message' => 'Love has been sent!'
		));
	}
	respond(array(
		'success' => false,
		'message' => 'An error occured'
	));
}

function sendFromJournal() {
    // Check that all required parameters exist
    if (empty($_POST['caller']) || empty($_POST['from']) ||
        empty($_POST['to']) || empty($_POST['why'])) {
        
	    $rsp['error'] = SL_BAD_CALL;
	    respond ($rsp);
    }

    // Prepare received data
    $to = mysql_real_escape_string (trim (setEncoding($_POST['to'])));
    $from = mysql_real_escape_string (trim (setEncoding($_POST['from'])));
    $why = smart_strip_tags(mysql_real_escape_string (trim (setEncoding($_POST['why']))));
    $private = isset($_POST['priv']) && (int)$_POST['priv'] > 0;

    // Can't send love to self
    if (strtolower($to) == strtolower($from)) {
        $rsp['error'] = SL_NOT_COWORKER;
        respond ($rsp);
    }

    // Check that to and from nicknames exist and find their data
    foreach (array('from', 'to') as $v) {
	    $query = ("select id, fb_id, username, nickname, company_id, skill, team " .
	            "from ".USERS." where nickname='".$$v."' and removed = 0");
	    $res = mysql_query ($query);
	    $line = mysql_fetch_array ($res, MYSQL_ASSOC);
	    if ($res && $line) {
	        $$v = $line;
	    } else {
	        $rsp['error'] = SL_UNKNOWN_USER;
	        respond ($rsp);
	    }
    }

    // Check rate limit
    if (enforceRateLimit('love', $from['id'])) {
        error_log("User ".$from['id']." send love was rate limited.");
        $rsp['error'] = SL_RATE_LIMIT;
        respond($rsp);
    }

    // Send love
    if (!sl_send_love ($from['username'], $from['nickname'], $from['id'],
            $from['company_id'], $to['username'], $why, false, $private)) {
	    $rsp['error'] = SL_SEND_FAILED;
	    respond ($rsp);
    }

    // Record love in database
    $company = $to['company_id'] == $from['company_id'] ? ", company_id={$to['company_id']}" : "";
    $priv_str = $private ? ', private=1' : '';
    $query = ("insert into ".LOVE." set giver='{$from['username']}', receiver='{$to['username']}', " .
        "skill='{$from['skill']}', team='{$from['team']}', why='$why', at=now()".$company.$priv_str);
    $rsp['status'] = SL_OK;
    $rsp['error'] = SL_NO_ERROR;
    $rsp['info'] = $query;
    if (!mysql_query($query)) {
	error_log("Add Love.err:".mysql_error());
        $rsp['error'] = SL_DB_FAILURE;
        respond ($rsp);
    }

    // See if the recipient is has a facebook id, if so we'll return a value so it can be handled.
    // if (!empty($to['fb_id'])) {
    //   $rc = array('facebook', $to['username'], $why, $to['fb_id']);
    // }

    // Make love notice in journal
    if ($to['company_id'] == JOURNAL_API_COMPANY && !$private) {
	    $data = array ('user' => JOURNAL_API_USER,
	            'pwd' => sha1(JOURNAL_API_PWD),
	            'message' => "{$from['nickname']} to {$to['nickname']}: $why");
	    $journal_rsp = postRequest (JOURNAL_API_URL, $data);
	    $journal_rsp = trim($journal_rsp);
	    if ($journal_rsp != 'ok') {
	        $rsp['status'] = SL_WARNING;
	        $rsp['error'] = SL_JOURNAL_FAILED;
	        $rsp['info'] = $journal_rsp;
	        respond ($rsp);
	    }
    }
}

function getLoveReportCount(){
    // Get 7 and 14 day total, unique Senders and Givers
    // Respond in json

    //Run expected query to produce report output
    if(($data = getDbLoveReportCount()) !== false){
        $rsp['status'] = SL_OK;
        $rsp['error'] = SL_NO_ERROR;
        $rsp['data'] = $data;
        respond ($rsp);
    }else{
        $rsp['error'] = SL_DB_FAILURE;
        respond ($rsp);
    }
}

function getUniqueLoveCount(){

    // Check that all required parameters exist
    if (empty($_REQUEST['username'])) {
        $rsp['error'] = SL_BAD_CALL;
        respond ($rsp);
    }

    $email = mysql_real_escape_string($_REQUEST['username']);

    if(($count = getUniqueDbLoveCount($email)) !== false){
        $rsp['status'] = SL_OK;
        $rsp['error'] = SL_NO_ERROR;
        $rsp['data'] = array('count' => $count);
        respond ($rsp);
    }else{
        $rsp['error'] = SL_DB_FAILURE;
        respond ($rsp);
    }
}

function getAllLove() {
    $_REQUEST['giver'] = $_REQUEST['receiver'] = NULL;
    getUserLove(false);
}

function getUserLove($check_user = true) {
    
    if ($check_user && !isset($_REQUEST['giver']) && !isset($_REQUEST['receiver'])) {
        $rsp['error'] = SL_BAD_CALL;
        respond ($rsp);
    }
    
    $pagination = isset($_REQUEST['pagination']) ? $_REQUEST['pagination'] : 1;
    $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    $perPage = isset($_REQUEST['perpage']) ? $_REQUEST['perpage'] : 20;
    
    $selectQuery = array("`private` = 0");
   
    if (!empty($_REQUEST['giver'])) {
        $giver = mysql_real_escape_string($_REQUEST['giver']);
        $selectQuery[] = "`giver` = '$giver'";
    } else {
        $giver = FALSE;
    }
    
    if (!empty($_REQUEST['receiver'])) {
        $receiver = mysql_real_escape_string($_REQUEST['receiver']);
        $selectQuery[] = "`receiver` = '$receiver'";
    } else {
        $receiver = FALSE;
    }
    
    // Check if we should use pagination 
    if ($pagination) {
        $limit = "LIMIT " . ($page - 1) * $perpage . ", $perPage";
    }
    
    $startDate = trim($_REQUEST['startdate']);
    $endDate   = trim($_REQUEST['enddate']);
    
    if (!empty($startDate) && !empty($endDate)) {
        $selectQuery[] = "`lv`.`at` BETWEEN '$startDate' AND '$endDate'";
    }
    
    $where = join(' AND ', $selectQuery);

    $contentSql = "SELECT `giver`, `receiver`, `why`, `at`, DATE_FORMAT(`at`, '%m/%d/%Y') AS `at_format`,
            TIMESTAMPDIFF(SECOND,`at`,NOW()) AS `when` FROM `" . LOVE . "` as lv WHERE
            $where ORDER BY `at` DESC $limit";
    
    $countSql = "SELECT COUNT(1) as total FROM `" . LOVE . "` as lv WHERE $where";
    $countResult = mysql_query($countSql);
    $countSet = mysql_fetch_assoc($countResult);
    $count = $countSet['total'];
    
    $loveArray = array();
    $contentResult = mysql_query($contentSql);

    if ($contentResult && $countResult) {
        while ($row = mysql_fetch_assoc($contentResult)) {
            $loveArray[] = $row;
        }
        $rsp['status'] = SL_OK;
        $rsp['error'] = SL_NO_ERROR;
        $rsp['data'] = array(
            'total' => $count,
            'pages' => ceil($count/$perPage),
            'page' => $page, 
            'love' => $loveArray
        );
        respond($rsp);
    } else {
        $rsp['error'] = SL_DB_FAILURE;
        respond($rsp);
    }
}

function getDbLoveReportCount(){
    //This request is for the daily status report.
    //Tenants data should only be requested through protected api

    //Return three columns, loveCount, uniqueSenders, uniqueReceivers for 0-7,8-14 days
    //Report tool performs additional calculataions
    //api needs mysqli conversion.
    //SQL extracted from instanceStatusCronJob.php
    $sql = "SELECT * FROM (SELECT '7' AS days, COUNT('id') AS loveCount, COUNT(DISTINCT giver) AS uniqueSenders, COUNT(DISTINCT receiver) AS uniqueReceivers FROM ".LOVE." WHERE at >= (NOW() - INTERVAL 7 day) ORDER BY loveCount DESC) AS day7 UNION SELECT * FROM(SELECT '14' AS days, COUNT(id) AS oldLoveCount, COUNT(DISTINCT giver) AS oldUniqueSenders, COUNT(DISTINCT receiver) AS oldUniqueReceivers FROM ".LOVE." WHERE at <= (NOW() - INTERVAL 8 day) AND at >= (NOW() - INTERVAL 14 day)) AS day14;";

    //Define array
    $reportArray=array();

    //always text for mysql failure
    if ($res = mysql_query($sql)) {
	
        while($row = mysql_fetch_assoc($res)) {
            //$reportArray['days'.$row['days']] = $row;
            $reportArray[] = $row;
        }
        return $reportArray;
    }else{
	return false;
    }
}

function getUniqueDbLoveCount($email){
    $sql = "SELECT * FROM `".LOVE."`"
            . "WHERE `receiver` = '$email' AND `private` = 0 AND `company_id` = 1 GROUP BY `love`.`giver`";
    $res = mysql_query($sql);
	$num_rows = mysql_num_rows($res);
    if($res && $num_rows){
        return $num_rows;
    }
    return 0;
}

function getUniqueDbLoveCountSentBy($giver_email, $email){
    $sql = "SELECT * FROM `".LOVE."`"
            . "WHERE `receiver` = '$email' AND `love`.`giver`='$giver_email' AND `private` = 0 "
            . "AND `company_id` = 1 GROUP BY `love`.`giver`";
    $res = mysql_query($sql);
    $num_rows = mysql_num_rows($res);
    if($res && $num_rows){
        return $num_rows;
    }
    return 0;
}

function getInvitationUrl()
{
	$username = (string)$_REQUEST['username'];
	$company_id = (int)$_REQUEST['companyid'];
	$invitor_id = (int)$_REQUEST['invitorid'];
	$as_admin = (int)$_REQUEST['asadmin'];
	$page = 'signup.php';
	$token = urlencode(sha1(SALT . "$company_id/$invitor_id/$asAdmin"));

	$query = 'SELECT `company_id` FROM `' . USERS . '` WHERE `username` = "' . mysql_real_escape_string($username) . '";';
	$result = mysql_query($query);
	
	if (mysql_num_rows($result) > 0) {
		respond(array(
			'success' => false,
			'message' => 'User already affiliated with the company.',
			'code' => '1_1'
		));
	} else {
		respond(array(
			'success' => true,
			'url' => SECURE_SERVER_URL . 'signup.php?invite=1&cid=' . $company_id . '&iid=' . $invitor_id . '&admin=' . $as_admin . '&token=' . $token
		));
	}
}

function getLoveAmount($flag = false) {
	if (empty($_REQUEST['id']) || empty($_REQUEST['days'])) {
		respond(array(
			'success' => false,
			'message' => SL_BAD_CALL
		));
	}

	$column = 'receiver';
	if ($flag === true) {
		$column = 'giver';
	}

	$query = 'SELECT COUNT(`id`) AS `sum_love` FROM `' . LOVE . '` WHERE `' . $column . '` = (SELECT `username` FROM `' . USERS . '` WHERE `id` = ' . (int)$_REQUEST['id'] . ') AND `at` > DATE_SUB(NOW(), INTERVAL ' . (int)$_REQUEST['days'] . ' DAY);';
	if (!($result = mysql_query($query))) {
		respond(array(
			'success' => false,
			'message' => SL_DB_FAILURE
		));
	} else {
		$row = mysql_fetch_assoc($result);
		respond(array(
			'success' => true,
			'user'	  => array(
				'id'  => (int)$_REQUEST['id'],
				'days'=> (int)$_REQUEST['days'],
				'love'=> (int)$row['sum_love']
			)
		));
	}
}

function getLoveMessages($flag = false) {
	if (empty($_REQUEST['id']) || empty($_REQUEST['days'])) {
		respond(array(
			'success' => false,
			'message' => SL_BAD_CALL
		));
	}

	$column = 'receiver';
	$column2 = 'giver';
	if ($flag === true) {
		$column = 'giver';
		$column2 = 'receiver';
	}

	$query = 'SELECT `why`, `at`, `' . $column2 . '` FROM `' . LOVE . '` WHERE `' . $column . '` = (SELECT `username` FROM `' . USERS . '` WHERE `id` = ' . (int)$_REQUEST['id'] . ') AND `at` > DATE_SUB(NOW(), INTERVAL ' . (int)$_REQUEST['days'] . ' DAY);';
	if (!($result = mysql_query($query))) {
		respond(array(
			'success' => false,
			'message' => SL_DB_FAILURE
		));
	} else {
		while ($row = mysql_fetch_assoc($result)) {
			$messages[] = array(
				'message'	=> $row['why'],
				$column2	=> $row[$column2],
				'timestamp' => strtotime($row['at'])
			);
		}
		respond(array(
			'success' => true,
			'user'	  => array(
				'id'  		=> (int)$_REQUEST['id'],
				'days'		=> (int)$_REQUEST['days'],
				'messages'	=> $messages
			)
		));
	}
}

function getUserLoveCount()  {
	$request = $_REQUEST;
	if(empty($request)) {
		respond(array(
			'success' => false,
			'message' => SL_BAD_CALL
		));
	}

	foreach($request as $key => $value) {
		$$key = mysql_real_escape_string($value);
	}
	
	$is_long_list = (!isset($page)) ? true : false;
	if(isset($start_date) && isset($end_date)) {
		$from 	= strtotime(trim($start_date));
		$to 	= strtotime(trim($end_date));
		$period = "date(`at`) >= '".date("Y-m-d", $from)."' AND date(`at`) <= '".date("Y-m-d", $to)."'";
	} 
	$total_row 		= 0; 
	$page_limit		= 30;
	$total_amount   = 0;
	$limit = $is_long_list ? "" : " LIMIT " . ($page-1)*$page_limit . ",$page_limit";
	
	if(!$is_long_list) {
		$query = "SELECT COUNT(*) as `total`
				FROM `".LOVE."`
				WHERE `company_id` = 1 AND $period 
				GROUP BY `receiver` ";

		if ($result = mysql_query($query)) {
			$total_row = mysql_num_rows($result);
			while ($row = mysql_fetch_assoc($result)) {
				$total_amount = $total_amount + (int) $row['total']; 
			}
			$total_amount = number_format($total_amount * $weightage, 2);
		}

		if( $total_row == 0 ) {
			respond(array(
				'success' => true,
				'message' => "No user found",
				'rewarderlist' => array()
			));
		}
	}

	$query = "SELECT  `u`.`id` AS `user_id`, `nickname`, `username`,
              SUM( IF( `username`=`giver`, 1, 0 )) AS `sent`,
              SUM( IF( `username`=`receiver`, 1, 0 )) AS `received`
			  FROM `".USERS."` u
			  INNER JOIN `".LOVE."` l
			  ON (`username` = `receiver` or `username` = `giver`)
			  AND `u`.`company_id` = `l`.`company_id` AND $period
			  WHERE `u`.`company_id` = ".$company_id." AND u.confirmed = 1
			  GROUP BY `username` 
              ORDER BY  $sort $dir $limit";
   
	if (!($result = mysql_query($query))) {
		respond(array(
			'success' => false,
			'message' => SL_DB_FAILURE
		));
	} else {
		$return = array();
		if (mysql_num_rows($result) > 0) {
			while ($row = mysql_fetch_assoc($result)) {
					$return[] = $row;
			}
		}
		if(!empty($return)) {
			$pagination = "";
			if(!$is_long_list) {
				$total_row = ceil($total_row/$page_limit);
				$pagination = getPagination($page, $total_row);
			}
			respond(array(
				'success' 		=> true,
				'userlist' 		=> $return,
				'pagination'	=> $pagination,
				'total_amount' 	=> $total_amount
			));
		}
		else {
			respond(array(
				'success' => false,
				'message' => 'No users',
				'sql' => $query
			));
		}
	}
}

function getPagination($page, $totalPage) {
	$output = '<ul class="pager">';
	$iPage = 0;
		$iPage = max(0, $page - 5 );
	if ($iPage + 5 > $totalPage) {
		$iPage = max(0, $totalPage - 5 );
	}
	$iPageStart = $iPage;
	if($page != 1){
		$output .= '<li class="firstPage">&lt;&lt;</li>';
		$output .= '<li class="prev">&lt;</li>';
	}
	while ($iPage < $totalPage) {
		if ($iPage > $iPageStart + 9 && $iPage+1 != $totalPage) {
			$output .= '<li class="morePage">...</li>';
			break;
		}
		if($page == $iPage+1){
			$output .= '<li class="page current">' . ($iPage+1) . '</li>';
		} else {
			$output .= '<li class="otherPage">' . ($iPage+1) . '</li>';
		}
		$iPage++;
	}
	if($page != $totalPage){
		$output .= '<li class="next">&gt;</li>';
		$output .= '<li class="lastPage" lastPage="'.$totalPage.'">&gt;&gt;</li>';
	}
	$output .= "</ul>";
	return $output;
}

function newSignupsReportData() {
	$query= " SELECT COUNT(*) FROM ". USERS.";";
	if (!($result = mysql_query($query))) {
		respond(array(
			'success' => false,
			'message' => SL_DB_FAILURE.$query
		));
	}	
	$totalUsers=mysql_result($result,0);

	$query= " SELECT COUNT(*) FROM ". LOVE ." WHERE at > NOW() - INTERVAL 7 DAY;";
	if (!($result = mysql_query($query))) {
		respond(array(
			'success' => false,
			'message' => SL_DB_FAILURE.$query
		));
	}	
	$totalLoveSent=mysql_result($result,0);

	respond(array('status' => 'ok','totalUsers' => $totalUsers, 'totalLoveSent' => $totalLoveSent));

}

function getActiveUsersCount(){
	$query= " SELECT COUNT(*) FROM " . USERS . " WHERE active = 1 AND removed = 0;";
	if (!($result = mysql_query($query))) {
		respond(array(
			'success' => false,
			'message' => SL_DB_FAILURE . $query
		));
	}	
	$totalUsers = mysql_result($result, 0);
	
	respond(array('status' => 'ok', 'totalActiveUsers' => $totalUsers));
}
?>
