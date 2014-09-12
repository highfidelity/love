<?php
include("class/frontend.class.php");
include_once("helper/check_new_user.php"); 
$front = Frontend::getInstance();
if(!$front->isUserLoggedIn()){
	define('LOGIN_LINK_ENABLE', true);	
  	require_once('prelogin_privacy.php');
}else{
	define('SETTINGS_LINK_ENABLE', true);	
	require_once('postlogin_privacy.php');	
}
?>