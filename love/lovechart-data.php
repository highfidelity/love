<?php
//  Copyright (c) 2009, LoveMachine Inc.                                                                                                                          
//  All Rights Reserved.                                                                                                                                          
//  http://www.lovemachineinc.com

include("class/frontend.class.php");
$front = Frontend::getInstance();

include_once("db_connect.php");
include_once("autoload.php");
include("review.php");

if(!$front->isUserLoggedIn()){
    $front->getUser()->askUserToAuthenticate();
}
include_once("functions.php");
include_once("chart-functions.php");

$_SESSION['company_id'] = 1;

if (!isset($_SESSION['company_id'])) {
    return;
}

if(isset($_GET['from_date'])) {
	$from_date = $_GET['from_date'];
}
if(isset($_GET['to_date'])) {
	$to_date = $_GET['to_date'];
}
if(isset($_GET['type'])) {
	$type = $_GET['type'];
}
if(isset($_GET['username'])) {
	$username = urldecode($_GET['username']);
}
if(isset($_GET['centerNickname'])) {
	$centerNickname = $_GET['centerNickname'];
}

if(!isset($centerNickname) || empty($centerNickname)) {
	$centerNickname = $_SESSION['nickname'];
} 

$mysqlFromDate = mysql_real_escape_string(GetTimeStamp($from_date));
$mysqlToDate = mysql_real_escape_string(GetTimeStamp($to_date));
$centerNickname = mysql_real_escape_string($centerNickname);

$res = mysql_query("SELECT usr.company_id,c.name FROM ".USERS." as usr LEFT JOIN ".COMPANY." as c on c.id=usr.company_id WHERE usr.username ='".$_SESSION['username']."'");
if($res && mysql_num_rows($res) > 0) {
    $row = mysql_fetch_row($res);
    $company_id = $row[0];
    $company_name = $row[1];
}

$dateRangeFilter = ($from_date && $to_date) ? " AND DATE(lv.at) BETWEEN '".$mysqlFromDate."' AND '".$mysqlToDate."'" : "";
$username = !empty($username) ? mysql_real_escape_string($username) : '';

if($type && $type == 'userLoveCountsByDate') {
    // Get nickname for the user who's being viewed
    $viewPointNickname = $centerNickname;
	$viewPointUsername = get_username_for_nick($viewPointNickname, $company_id);
    $filters = ''; // No filters at the moment.
    
    // Get list of users that the $viewPointNickname has had interactions with.
    $usernameList = get_love_exchange_user_list($viewPointUsername, $company_id, $filters);

    if(isset($from_date)) {
      $fromDate = getMySQLDate($from_date);
    }
    if(isset($to_date)) {
      $toDate = getMySQLDate($to_date);
    }
    $fromDateTime = mktime(0,0,0,substr($fromDate,5,2),  substr($fromDate,8,2), substr($fromDate,0,4));
    $toDateTime = mktime(0,0,0,substr($toDate,5,2),  substr($toDate,8,2), substr($toDate,0,4));

    $daysInRange = round( abs($toDateTime-$fromDateTime) / 86400, 0 );
    $rollupColumn = getRollupColumn('lv.at', $daysInRange);
    $dateRangeType = $rollupColumn['rollupRangeType'];
	
    $userLoveCountQuery = "SELECT count(lv.id) as loveCount, count(distinct lv.giver) as uniqueSenders, " . $rollupColumn['rollupQuery'] . " as loveDate
        FROM ".USERS." as usr
        LEFT OUTER JOIN ".LOVE." as lv ON usr.username = lv.receiver
        WHERE lv.company_id=$company_id ";
        
    if ($username) {
        $userLoveCountQuery .= " AND usr.nickname = '$username' ";
        if ($username != $_SESSION['username']) {
        	$userLoveCountQuery;
        }
    }
    $userLoveCountQuery .= $dateRangeFilter ;
    $userLoveCountQuery .= " GROUP BY loveDate ORDER BY lv.at ASC";
    $messages = array();
    $senders = array();
    $res = mysql_query($userLoveCountQuery);
    if($res && mysql_num_rows($res) > 0) {
        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
            $senders[$row['loveDate']] = $row['uniqueSenders'];
            $messages[$row['loveDate']] = $row['loveCount'];
        }
    }

    $json_data = array('messages' => fillAndRollupSeries($fromDate, $toDate, $messages, false, $dateRangeType, true), 'senders' => fillAndRollupSeries($fromDate, $toDate, $senders, false, $dateRangeType, true), 'labels' => fillAndRollupSeries($fromDate, $toDate, null, true, $dateRangeType, false)); 

    $json = json_encode($json_data);
    echo $json;

} elseif($type && $type == 'userLoveCount') {

    // Get all nicknames of the company for the Scrollable list
    $nicknameListQuery = "SELECT usr.nickname FROM ".USERS." as usr WHERE usr.company_id=$company_id ORDER BY usr.nickname ASC";
    $res = mysql_query($nicknameListQuery);
     if($res && mysql_num_rows($res) > 0) {
        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
            $nicknameList[] = $row["nickname"];
        }
    }
    // Get nickname for the user who's being viewed
    $viewPointNickname = $centerNickname;
    $viewPointUsername = get_username_for_nick($viewPointNickname, $company_id);
    $filters = $dateRangeFilter;
    
    // Get list of users that the $viewPointNickname has had interactions with.
    $usernameList = get_love_exchange_user_list($viewPointUsername, $company_id, $filters);
    $usernameFilter = format_array_as_infilter($usernameList);
        
    // Get Love counts for the identified users
    $userLoveCountQuery = "SELECT count(lv.id) as loveCount,usr.nickname 
        FROM ".USERS." as usr
        LEFT OUTER JOIN ".LOVE." as lv ON usr.username=lv.giver OR usr.username = lv.receiver
        WHERE usr.company_id=$company_id ";
        
    if($usernameFilter) {
	    $userLoveCountQuery .= " AND usr.username in " . $usernameFilter;
    }
    $userLoveCountQuery .=$dateRangeFilter;
    $userLoveCountQuery .= " GROUP BY usr.id ORDER BY usr.nickname ASC";

    $totalLoveCount = 0;
    $userLoveCount = array();
    $res = mysql_query($userLoveCountQuery);
    
    if($res && mysql_num_rows($res) > 0) {
        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
            if ($row["loveCount"]>0) {
              $userLoveCount[] = $row;
              $loveCount = $row["loveCount"];
              $totalLoveCount += $loveCount;
            }
        }
    }
    
    $json_data = array();
    $json_data["companyName"] = $company_name;
    $json_data["userLoveCount"] = $userLoveCount;
    $json_data["currentUser"] = $_SESSION['nickname'];
    $json_data["totalLoveCount"] = $totalLoveCount;
    $json_data["nicknameList"] = $nicknameList;
    $json = json_encode($json_data);
    echo $json;

 } elseif($type && $type == 'userLoveExchange') {
    // Get nickname for the user who's being viewed
    $viewPointNickname = $centerNickname;
	$viewPointUsername = get_username_for_nick($viewPointNickname, $company_id);
    $filters = ''; // No filters at the moment.
    
    // Get list of users that the $viewPointNickname has had interactions with.
    $usernameList = get_love_exchange_user_list($viewPointUsername, $company_id, $filters);
    
    $usernameFilter = format_array_as_infilter($usernameList);
    
 	$userLoveExchangeQuery = "SELECT usr.nickname as giver,usr1.nickname as receiver,count(lv.id) as count,lv.private
        FROM ".LOVE." as lv
        INNER JOIN  ".USERS." as usr ON (lv.giver = usr.username)
        INNER JOIN  ".USERS." as usr1 ON (lv.receiver = usr1.username)
        WHERE lv.company_id=$company_id ";
        
    if($usernameFilter) {
	    $userLoveExchangeQuery .= " AND (usr.username in " . $usernameFilter . " AND usr1.username in " . $usernameFilter . ") ";
    }
    
 	$userLoveExchangeQuery .= $dateRangeFilter ;
    $userLoveExchangeQuery .= " GROUP BY lv.giver,lv.receiver order by lv.giver, lv.receiver";
    $res = mysql_query($userLoveExchangeQuery);

        $loveDataArray = array();
        $userLoveExchangeTotal = 0;  

    if($res && mysql_num_rows($res) > 0) {
        while ($loves = mysql_fetch_array($res, MYSQL_ASSOC)) {
            if ($loves['private'] != 0 && $loves['giver'] != $_SESSION['nickname'] && $loves['receiver'] != $_SESSION['nickname']) continue;
            $giverKey = $loves['giver'] . "|" . $loves['receiver'];
            $receiverKey = $loves['receiver'] . "|" . $loves['giver'];
            if(array_key_exists($giverKey, $loveDataArray)) {
                $loveDataArray[$giverKey] = $loveDataArray[$giverKey] + $loves['count'];
            } elseif(array_key_exists($receiverKey, $loveDataArray)) {
                $loveDataArray[$receiverKey] = $loveDataArray[$receiverKey] + $loves['count'];
            } else {
                $loveDataArray[$giverKey] = $loves['count'];
            }
            $userLoveExchangeTotal = $userLoveExchangeTotal + $loves['count'];
        }
    }
      
    $json_data = array();
    $json_data["userLoveExchangeData"] = !empty($loveDataArray)?$loveDataArray:array();
    $json_data["totalLoveCount"] = !empty($userLoveExchangeTotal)?$userLoveExchangeTotal:0;
    $json = json_encode($json_data);
    echo $json;

}else {
    $json_data = array();
    $json = json_encode($json_data);
    echo $json;    
}
die();


