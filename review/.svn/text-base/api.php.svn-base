<?php
//  Copyright (c) 2010, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

$app_root_path = dirname(__FILE__)."/";

require_once ($app_root_path."config.php");
require_once ($app_root_path."functions.php");
require_once ($app_root_path."send_email.php");
require_once ($app_root_path."classes/Session.class.php");
require_once ($app_root_path."classes/Utils.class.php");

define ('SL_OK', 'ok');
define ('SL_ERROR', 'error');
define ('SL_NO_SSL', 'no ssl call');
define ('SL_WRONG_KEY', 'wrong api key');
define ('SL_DB_FAILURE', 'db failure');
define ('SL_BAD_CALL', 'bad call');
define ('SL_NO_ERROR', '');
define ('SL_NO_RESPONSE', 'no response');


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
if (empty($_SERVER['HTTPS'])) {
  $rsp['error'] = SL_NO_SSL;
  respond ($rsp);
}


if (!isset($_REQUEST['api_key']) ||
    (isset($_REQUEST['api_key']) && $_REQUEST['api_key'] != REVIEW_API_KEY)) {
  $rsp['error'] = SL_WRONG_KEY;
  respond ($rsp);
}


if(empty($_REQUEST['action'])){
    $rsp['error'] = SL_BAD_CALL;
    respond ($rsp);
}

// Connect to db
$db = @mysql_connect (DB_SERVER, DB_USER, DB_PASSWORD);
if (!$db || !@mysql_select_db (DB_NAME, $db)) {
    $rsp['error'] = SL_DB_FAILURE;
    $rsp['info'] = mysql_error();
    respond ($rsp);
}

$requiredArgs = array(
                    'change_balance' => array('user_id', 'points', 'reason'),
                    'reward_user' => array('giver_id', 'receiver_id', 'points'),
                    'get_points' => array('giver_id', 'receiver_id'),
                    'populate_team' => array('team'),
                    'populate_peers' => array('user_id', 'peers'),
                    'login' => array('user_id', 'session_id'),
                    );

if(array_key_exists($_REQUEST['action'], $requiredArgs)){

    foreach($requiredArgs[$_REQUEST['action']] as $arg){

        if(empty($_REQUEST[$arg])){
            echo 'error: args';
            return;
        }
    };
}



switch($_REQUEST['action']){

case 'change_balance':
    changeUserBalance();
    break;

case 'reward_user':
    rewardUser();
    break;

case 'get_points':
    getRewardedPoints();
    break;

case 'populate_team':
    populateTeam();
    break;

case 'populate_peers':
    populatePeers();
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

case 'getUsers':
	getUsers();
	break;

case 'getUser':
	getUser();
	break;

case 'saveUser':
	saveUser();
	break;
case 'adminCreateUsers':
     adminCreateUsers();
     break;
case 'getReviewPeriod':
     getReviewPeriod();
     break;
case 'getUserByReward':
     getUserByReward();
     break;
}

// Add the user passed by Login to the Db
function adminCreateUsers() {
    // Create SQL statements
    $sql = "INSERT INTO ".REVIEW_USERS." ";
    $columns = "(";
    $values = "VALUES(";
    
    // Take the first user and add the columns
    foreach ($_REQUEST["user_data"][0] as $key => $val) {
        if ($key == "username" ||
            $key == "nickname" ||
            $key == "confirmed" ||
            $key == "id" ||
            $key == "status") {

            $columns .= "`".$key."`,";                
        }
    }
    
    // For each user add the values to the query
    foreach ($_REQUEST["user_data"] as $user) {
        
        foreach($user as $name => $value){
            if ($name == "username" ||
                $name == "nickname" ||
                $name == "status") {
                
                $values .= "'" . $value . "', ";
            }
            if ($name == "confirmed" ||
                $name == "id") {
                
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
    mysql_query($sql) or error_log(mysql_error());
    
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

function getUsers() {
	$query = 'SELECT `id`, `is_auditor`, `is_giver`, `is_receiver` FROM `' . REVIEW_USERS . '`;';
	$result = mysql_query($query);
	$return = array();
	if ($result && (mysql_num_rows($result) > 0)) {
		while ($row = mysql_fetch_assoc($result)) {
			$return[$row['id']] = array(
				'is_auditor' => $row['is_auditor'],
				'is_giver' => $row['is_giver'],
				'is_receiver' => $row['is_receiver']
			);
		}
        respond(array(
            'success' => true,
            'userlist' => $return
        ));
	}
    respond(array(
        'success' => false
    ));
}

function getUser() {
	$user_id = (int)$_REQUEST['user_id'];
	$user = new User();
	$user->findUserById($user_id);

	if ($user->getId()) {
        respond(array(
            'success' => true,
            'user' => array(
				'is_auditor' => $user->getIs_auditor(),
				'is_giver' => $user->getIs_giver(),
				'is_receiver' => $user->getIs_receiver()
			)
        ));
	} else {
        respond(array(
            'success' => false,
            'message' => 'User not found'
        ));
	}
}

function saveUser() {
	$user_id = (int)$_REQUEST['user_id'];
	$user = new User();
	$user->findUserById($user_id);
	$user->setIs_auditor((int)$_REQUEST['user']['auditor']);
	$user->setIs_giver((int)$_REQUEST['user']['is_giver']);
	$user->setIs_receiver((int)$_REQUEST['user']['is_receiver']);

	if ($user->save()) {
        respond(array(
            'success' => true
        ));
	} else {
        respond(array(
            'success' => false
        ));
	}
}

function checkConfirmation(){
    $sql = 'SELECT `confirmed` FROM `' . REVIEW_USERS . '` WHERE `username` = "' . mysql_real_escape_string($_REQUEST['username']) . '"';
    $result = mysql_query($sql);
    if ($result && (mysql_num_rows($result) == 1)) {
        $row = mysql_fetch_object($result);
        if (($row->confirmed == 1)) {
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

function pushCreateUser()
{
    if($_REQUEST['calling_app'] != REVIEW_SERVICE_NAME){
        $is_admin = ($_REQUEST['admin'] ? 1 : 0);
        $is_auditor = ($_REQUEST['admin'] ? 1 : 0);
        $user_id = intval($_REQUEST['id']);
        $username = mysql_real_escape_string($_REQUEST['username']);
        $nickname = mysql_real_escape_string($_REQUEST['nickname']);

        $db = new Database();

        $sql = "INSERT INTO " . REVIEW_USERS . " 
                    (`id`, `rewarder_points`, `is_auditor`, `rewarder_limit_day`, `username`, `nickname`, `confirmed`, `is_active`, `is_giver`, `is_receiver`, `is_admin`) 
                    VALUES ('$user_id', '0', '$is_auditor', '60', '$username', '$nickname', '0', '1', '1', '1', '$is_admin')";
        if (!$db->query($sql)) {
          error_log("add user failed: " . $db->getError());
          respond(array(
            'success' => false,
            'message' => 'failed to create user'
          ));
        }
    }

    respond(array(
        'success' => true,
        'message' => 'User has been created!'
    ));
}

function pushVerifyUser()
{
    $user_id = intval($_REQUEST['id']);
    $sql = "UPDATE " . REVIEW_USERS . " SET `confirmed` =  '1', `is_active` = '1' WHERE `id` = $user_id";
    mysql_unbuffered_query($sql);

    respond(array(
        'success' => false,
        'message' => 'User has been confirmed!'
    ));
}

/*
*   changeUserBalance()
*   change balance of given user using request data
*/
function changeUserBalance(){

    $user = new User();
    $user->findUserById($_REQUEST['user_id']);

    $currentPoints = $user->getRewarder_points();
    $addPoints = $_REQUEST['points'];
    $newPoints = $currentPoints + intval($addPoints);
    $user->setRewarder_points($newPoints);
    $user->save();

    $reason = $_REQUEST['reason'];

    $worklist_id = isset($_REQUEST['worklist_id']) ? intval($_REQUEST['worklist_id']) : 0;
    $fee_id = isset($_REQUEST['fee_id']) ? intval($_REQUEST['fee_id']) : 0;

    mysql_unbuffered_query("INSERT INTO `".REVIEW_REWARDER_LOG."` (`user_id`, `worklist_id`, `fee_id`, `rewarder_points`) VALUES ('" . $user->getId() . "', '$worklist_id', '$fee_id', '" . intval($addPoints) . "')");

    if(intval($addPoints) > 0){
        sendTemplateEmail($user->getUsername(), 'increase-balance',
                                                array(
                                                    'points' => $addPoints,
                                                    'total-points' => $newPoints,
                                                    'reason' => $reason,
                                                ));
    }else{
        sendTemplateEmail($user->getUsername(), 'decrease-balance',
                                                array(
                                                    'points' => -$addPoints,
                                                    'total-points' => $newPoints,
                                                    'reason' => $reason,
                                                ));
    }

    $rsp['status'] = SL_OK;
    respond ($rsp);
}

/*
*   rewardUser()
*   reward one user from the bal;ance of another user
*/
/*Joanne added in code to calculate the percentage of giver's total points rewarded 29-May-2010 <joanne>*/


function rewardUser(){

    $giverId = intval($_REQUEST['giver_id']);
    $receiverId = intval($_REQUEST['receiver_id']);
    $points = isset($_REQUEST['points']) ? max(0, intval($_REQUEST['points'])) : 0;

    $giverUser = new User();
    $giverUser->findUserById($giverId);

    $rewarder = new Rewarder($giverUser->getId());

    // add new points to those already rewarded
    $newPoints = $rewarder->getGivenPoints($receiverId) + $points;
    $remainingPoints = $rewarder->setGivenPoints($receiverId, $newPoints);

    $giverUser->setRewarder_points($remainingPoints);
    $giverUser->save();

    $totalRewarded = intval($rewarder->getGivenPoints($receiverId));
    $availablePoints = $giverUser->getRewarder_points();
    $percentRewarded = round(($totalRewarded/$totalRewarded+$availablePoints)*100);


    $rsp['data'] = array('rewarded' => $totalRewarded, 'available' => $availablePoints,'percent' => $percentRewarded);
    $rsp['status'] = SL_OK;
    respond ($rsp);
}

/*
*   getRewardedPoints()
*   get points rewarded from one user to another
*/
/*Joanne added in code to calculate the percentage of giver's total points rewarded 29-May-2010 <joanne>*/
function getRewardedPoints(){

    $giverId = intval($_REQUEST['giver_id']);
    $receiverId = intval($_REQUEST['receiver_id']);

    $giverUser = new User();
    $giverUser->findUserById($giverId);

    $rewarder = new Rewarder($giverId);
    $totalRewarded = intval($rewarder->getGivenPoints($receiverId));
    $availablePoints = $giverUser->getRewarder_points();
    $percentRewarded = round(($totalRewarded/$totalRewarded+$availablePoints)*100);

    $rsp['data'] = array('rewarded' => $totalRewarded, 'available' => $availablePoints,'percent' => $percentRewarded );
    $rsp['status'] = SL_OK;
    respond ($rsp);
}

/*
*   populateTeam()
*   adds users from given array to rewarder list of each of them
*/
function populateTeam(){

    $team = $_REQUEST['team'];

    // iterate through coworkers and add the rest of teammates to each one
    foreach($team as $userId){

        $rewarder = new Rewarder($userId);

        $teamMates = $team;
        foreach($team as $teamMateId){
            // only add other users
            if($teamMateId != $userId){

                // if we already rewarded something to this user,
                // same value will be rerewarded
                // if not - user will be added with 0
                $totalRewarded = intval($rewarder->getGivenPoints($teamMateId));
                $rewarder->setGivenPoints($teamMateId, $totalRewarded);
            }
        }
    }

    $rsp['status'] = SL_OK;
    respond ($rsp);
}

/*
*   populateTeam()
*   adds users from given array to userlist of provided user
*/
function populatePeers(){

    $user_id = intval($_REQUEST['user_id']);
    $peers = $_REQUEST['peers'];
    $rewarder = new Rewarder($user_id);

    foreach($peers as $peer){
        $peer_id = intval($peer);

        // if user is not on our list - add him
        if($rewarder->getGivenPoints($peer_id) === null){
            $rewarder->setGivenPoints($peer_id, 0);
        }
    }

    $rsp['status'] = SL_OK;
    respond ($rsp);
}
/**
*
*/

function getReviewPeriod() {
	$sql = "SELECT *, if(NOW() >= `start_date` AND NOW() <= `end_date`,1, 0) as current FROM " . REVIEW_PERIODS;
	$result = mysql_query($sql);
	$return = array();
	if ($result && (mysql_num_rows($result) > 0)) {
		while ($row = mysql_fetch_assoc($result)) {
				$return[$row['id']] = $row;
		}
	}
	if(!empty($return)) {
		respond(array(
			'success' => true,
			'periodlist' => $return
		));
	}
	else {
		respond(array(
			'success' => false,
			'message' => 'No period'
		));
	}
}

function getUserByReward()  {

	$request = $_REQUEST;

	if(empty($request)) {
		return false;
	}

	$a = array();
	foreach($request as $key => $value) {
		$$key = mysql_real_escape_string($value);
		$a[$key]= mysql_real_escape_string($value);
	}

	$is_active = (empty($is_active) || is_null($is_active)) ? 0 : 1;
	$is_long_list = (empty($page)) ? true : false;

	if(!empty($start_date) && !empty($end_date)) {
		$from 	= strtotime(trim($start_date));
		$to 	= strtotime(trim($end_date));
		$period = "( `p`.`start_date` >= '".date("Y-m-d", $from)."' AND `p`.`start_date` <= '".date("Y-m-d", $to)."' ) OR ( `p`.`end_date` >= '".date("Y-m-d", $from)."' AND `p`.`end_date` <= '".date("Y-m-d", $to)."')"; 
	}
	
	
	$total_row 		= 0; 
	$page_limit		= 30;
	$limit = $is_long_list ? "" : " LIMIT " . ($page-1)*$page_limit . ",$page_limit";
	

	if(!$is_long_list) {
		$query =  " SELECT COUNT(1) AS `total` 
					FROM `".REVIEW_REWARDER."` AS `r` 
					INNER JOIN `".REVIEW_USERS."` AS `u` ON `u`.`id` = `r`.`receiver_id`
					INNER JOIN `".REVIEW_PERIODS."` AS `p` ON `p`.`id` = `r`.`period_id`	
					WHERE ($period) AND  `u`.`confirmed`= 1 AND `u`.`is_active` = $is_active AND `r`.`rewarder_points` > 0
					GROUP BY `u`.`username`";

		if ($result = mysql_query($query)) {
			$total_row = mysql_num_rows($result);
		}
		if($total_row == 0) {
			respond(array(
				'success' => true,
				'message' => "No reward found",
				'rewarderlist' => array(),
				'sql' => $query
			));
		}
	}
	

	$query =  " SELECT `u`.`username`, `u`.`nickname`, SUM(`r`.`rewarder_points`) AS `points` 
				FROM `".REVIEW_REWARDER."` AS `r` 
				INNER JOIN `".REVIEW_USERS."` AS `u` ON `u`.`id` = `r`.`receiver_id`
				INNER JOIN `".REVIEW_PERIODS."` AS `p` ON `p`.`id` = `r`.`period_id`
				WHERE ( $period ) AND  `u`.`confirmed`= 1 AND `u`.`is_active` = $is_active AND `r`.`rewarder_points` > 0
				GROUP BY `u`.`username`
				ORDER BY $sort $dir $limit";

	if (!($result = mysql_query($query))) {
		respond(array(
			'success' => false,
			'message' => SL_DB_FAILURE,
			'sql' 	  => $query
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
				'rewarderlist' 	=> $return,
				'pagination' 	=> $pagination,
				'sql' 	  => $query
			));
		}
		else {
			respond(array(
				'success' => false,
				'message' => 'No users',
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




