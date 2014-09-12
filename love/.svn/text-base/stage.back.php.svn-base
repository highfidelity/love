<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

include_once("class/frontend.class.php");
$front = Frontend::getInstance();

include_once("db_connect.php");
include_once("autoload.php");
///include_once("review.php");

if(!$front->isUserLoggedIn()){
    $front->getUser()->askUserToAuthenticate();
}



require_once('class/LoveUser.class.php');
$user = new LoveUser();

$justUser = isset($_REQUEST['userdata']);
// If we are asked for the latest we give back 20
$love_limit = isset($_REQUEST['no']) ? intval($_REQUEST['no']) : 20;
echo GetLove($love_limit, $justUser);

/*	
 * Get last $limit love messages.
 * Return: json with the messages.
 */
function GetLove($limit, $justUser) {
	global $user;
    if (!defined('LIVE_FEED_ROTATION_DELAY')) {
        $rotationDelay = 5000;
    } else {
        $rotationDelay = LIVE_FEED_ROTATION_DELAY;
    }
	// Get user
	$userid = $user->getId();
	//if we have no user, just return empty array
	if (!$userid) return json_encode(array());

    if ($justUser === true) {
    	$username = $user->getUsername();
    	$query = "SELECT `giver`,`receiver`,`why`,TIMESTAMPDIFF(SECOND,at,NOW()) as delta
    		      FROM `".LOVE."`
	WHERE `company_id` = ".(array_key_exists('company_id',$_SESSION)?$_SESSION['company_id']:MAIN_COMPANY)."
    		      AND `private` = 0
    		      AND at > DATE_SUB(NOW(), INTERVAL 31 DAY)
    		      AND (`giver` = '{$username}' OR `receiver` = '{$username}')
    		      ORDER BY `at` DESC LIMIT $limit";
    } else {
    	$query = "SELECT `giver`,`receiver`,`why`,TIMESTAMPDIFF(SECOND,at,NOW()) as delta
    		      FROM `".LOVE."`
	WHERE `company_id` = ".(array_key_exists('company_id',$_SESSION)?$_SESSION['company_id']:MAIN_COMPANY)."
    		      AND `private` = 0
    		      AND at > DATE_SUB(NOW(), INTERVAL 31 DAY)
    		      ORDER BY `at` DESC LIMIT $limit";
    }

	$rt = mysql_query($query) or error_log("GetLove: ".mysql_error()."\n".$query);
	$result = array();
	
	while ($row = mysql_fetch_assoc($rt))	{
		$why = strip_tags(stripslashes($row['why']));
		
		$givername = getNickName($row['giver']);
		$receivername = getNickName($row['receiver']);
		$result[] = array($givername, $receivername, $why, $row['delta']);
	}
	return json_encode(array("rotationDelay" => $rotationDelay, "result" => $result));
}

?>
