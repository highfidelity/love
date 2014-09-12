<?php 

//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

ob_start();
if(isset($_COOKIE['username']) and isset($_COOKIE['confirm_string']) and !empty($_COOKIE['username']) && !empty($_COOKIE['confirm_string'])) {
    $res=mysql_query("select id from ".USERS." where username = '".mysql_real_escape_string($_COOKIE['username'])."' and confirm_string = '".mysql_real_escape_string($_COOKIE['confirm_string'])."'");
    if(mysql_num_rows($res) == 0) {
        $row=mysql_fetch_array($res); 
        header("Location:login.php?redir=".urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
} else {
    header("Location:login.php");
    exit;
}
?>
