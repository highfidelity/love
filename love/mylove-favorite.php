<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com

ob_start();
include("config.php");
require_once("class.session_handler.php");
include("helper/check_session.php");
include_once("functions.php");
include_once("chart-functions.php");

$id = mysql_real_escape_string($_GET['id']);
$why = mysql_real_escape_string($_GET['reason']);

mysql_query("UPDATE love SET favorite = 'yes', favorite_why = '$why' WHERE id = $id AND receiver = '".$_SESSION['username']."'");

mysql_close();

echo "true";
?>
