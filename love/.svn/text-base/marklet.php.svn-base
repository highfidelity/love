<?php

include("class/frontend.class.php");
include_once("helper/check_new_user.php"); 
$front = Frontend::getInstance();

include_once("db_connect.php");
include_once("autoload.php");

if(!$front->isUserLoggedIn()){
    $front->getUser()->askUserToAuthenticate();
}

$company_id = $front->getCompany()->getid();
$user_id = $front->getUser()->getid();

define('LOVE_TABS_DISABLED', true);

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="copyright" content="Copyright (c) 2010, SendLove Inc.  All Rights Reserved. http://www.lovemachineinc.com ">

    <title>SendLove</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
    
    <link rel="stylesheet" type="text/css" href="css/settings.css" />    
    <link rel="stylesheet" type="text/css" href="css/tofor.css" />    
    <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="all" />
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css" media="all" />
    <link rel="stylesheet" type="text/css" href="css/smoothness/lm.ui.css"/>

</head>

<body style="padding-left:25px;">
    <div id="wrapper">
        <!-- Include header -->
        <?php include("view/settings/header.php"); ?>
        <div id="content">
			<div id="settings">	
				<!-- Include marklet content -->
                <?php include("view/static/bookmarklet.php"); ?>
			</div>	
            <!-- Include footer -->
        </div>
        <?php include("view/tofor/footer.php"); ?>
        <p class="lm"><a href="http://www.sendlove.us" target="_blank"><img class="logo_footer" src="images/SendLove_logo_sm.png" /></a></p>
    </div>
</body>
</html>