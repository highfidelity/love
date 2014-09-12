<?php

// markletised tofor page

require_once("class/frontend.class.php");
require_once("helper/check_new_user.php"); 
$front = Frontend::getInstance();

require_once("db_connect.php");
require_once("autoload.php");

if(! $front->isUserLoggedIn()) {
    // redirect to the marklet login page
    $front->getUser()->askUserToAuthenticate(true);
}

$company_id1 = $front->getCompany()->getid();
$user_id1 = $front->getUser()->getid();

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
require_once("view/tofor/love/m_sendlove.php");
