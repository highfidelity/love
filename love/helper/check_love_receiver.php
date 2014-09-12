<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

include("../config.php");
require_once("../class.session_handler.php");
include("../functions.php");

if (! SEND_LOVE_OUTSIDE_INSTANCE) {
    //if (!checkReferer()) die();
    if (empty($_GET["username"])) die("[]");
    $q = strtolower($_GET["username"]);
    $original = $_GET['username'];
    
    // Database Connection Establishment String
    $con=mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
    // Database Selection String
    mysql_select_db(DB_NAME,$con);

    $query= "select username FROM ".USERS." 
        where lower(username) = '".mysql_real_escape_string($q)."' AND `active`=1 AND `removed`=0";
    $result = mysql_query($query,$con) or error_log("check user error: ".mysql_error());

    if (mysql_num_rows($result) > 0) {
        echo 'true';
    } else {
        echo 'false';
    }
} else {
    // return true - allowed to send to any email
    echo 'true';
}
