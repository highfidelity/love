<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

// AJAX request from retrieving rate limiting information

include("config.php");
require_once("class.session_handler.php");
include("functions.php");

if (!checkReferer()) die;

if (empty($_SESSION['username']) || empty($_POST['c']) || empty($_POST['id'])) {
    echo json_encode(0);
    die;
}

$class = mysql_real_escape_string($_POST['c']);
$id = mysql_real_escape_string($_POST['id']);

echo json_encode(enforceRateLimit($class, $id, true));
