<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

ob_start();
include("config.php");
require_once("class.session_handler.php");
include("helper/check_session.php");

if (!isset($_SESSION['company_id'])) {
    return;
}

// Database Connection Establishment String
mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
// Database Selection String
mysql_select_db(DB_NAME);
echo $_SESSION['username'];

  $res = mysql_query("SELECT usr.company_id FROM ".USERS." as usr WHERE usr.username ='".$_SESSION['username']."'");
    if($res && mysql_num_rows($res) > 0) {
	$row=mysql_fetch_row($res);
	$company_id = $row[0];
    }

  $res = mysql_query("SELECT count(lv.id) as loveCount,usr.nickname 
	   FROM ".USERS." as usr
	   INNER JOIN ".LOVE." as lv ON usr.username=lv.giver 
	   WHERE usr.company_id=$company_id
	   GROUP BY lv.giver");
	  if($res && mysql_num_rows($res) > 0) {
	      while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
		  $userLoveCount[] = $row;
	      }
	  }

  $res = mysql_query("SELECT count(lv.id) as count,usr.nickname as giver,usr1.nickname as receiver
	   FROM ".LOVE." as lv
	   INNER JOIN  ".USERS." as usr ON lv.giver = usr.username
	   INNER JOIN  ".USERS." as usr1 ON lv.receiver = usr1.username 
	   WHERE lv.company_id=$company_id
	   GROUP BY lv.giver,lv.receiver");
	  if($res && mysql_num_rows($res) > 0) {
	      $result = array();
	      while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
		  $result[] = $row;
	      }
	  }
	  $userLoveExchangeData = array();
	  foreach($result as $loves) {
	      $inner['nickname'] = $loves['receiver'];
	      $inner['lovecount'] = $loves['count'];
	      $userLoveExchangeData[$loves['giver']][] = $inner;
	}
   

$json = array();
$json["userLoveCount"] = $userLoveCount;
$json["currentUser"] = $_SESSION['nickname'];
$json["userLoveExchangeData"] = $userLoveExchangeData;

echo "<pre>";
echo json_encode($json);
//print_r($json);
echo "</pre>";


?>
