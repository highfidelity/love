<?php 

//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com


ob_start();

include("config.php");
require_once("class.session_handler.php");
if (!empty($_SESSION['username'])) {
     header("Location:tofor.php");
     exit;
} else {
     /* Doing away with the page below for now. */
     header("Location:login.php");
     exit;
}
