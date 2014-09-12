<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

include("../config.php");
require_once("../class.session_handler.php");

include("../functions.php");
if (!checkReferer()) die();
if (empty($_GET["nickname"])) die("[]");
$q = strtolower($_GET["nickname"]);
$original = $_GET['nickname'];


// Database Connection Establishment String
$con=mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
// Database Selection String
mysql_select_db(DB_NAME,$con);

$query= "select nickname FROM ".USERS." 
	   where lower(nickname) = '".mysql_real_escape_string($q)."'";
$result = mysql_query($query,$con);

if (mysql_num_rows($result) > 0) {
  echo $original;
} else {
  echo 'true';
}
