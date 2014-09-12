<?php
//  vim:ts=4:et

//  Copyright (c) 2009-2010, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com

// AJAX request to get love sent to an user

include("../config.php");
include("../class.session_handler.php");
include("helper/check_session.php");
include("../functions.php");


$con=mysql_connect(DB_SERVER,DB_USER,DB_PASSWORD);
mysql_select_db(DB_NAME,$con);

if (empty($_REQUEST['id'])) {
    echo 'error: args';
    return;
}

if (empty($_REQUEST['period_id'])) {
    echo 'error: args';
    return;
}

// From user
$fromUser = new User();
$fromUser->findUserById($_SESSION['userid']);
$fromUsername = mysql_real_escape_string($fromUser->getUsername());

// Sent to user
$user = new User();
$user->findUserById($_REQUEST['id']);
$username = mysql_real_escape_string($user->getUsername());

//Get review period start date and end date
$rewarder = new Rewarder();
$period = $rewarder->getPeriod($_REQUEST['period_id']);
$start_date = $period['start_date'];
$end_date = $period['end_date'];

$love = getUserLove($username, $fromUsername, $start_date, $end_date);
$total_love = getUserLove($username,"", $start_date, $end_date);

echo json_encode(array($love, $total_love));

?>
