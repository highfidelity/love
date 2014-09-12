<?php ob_start();
//
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
//

// markletised logout page

include("config.php");
require_once("class.session_handler.php");
?>

<script type="text/javascript">
function update_user_fb() {
	var d = new Date();
	var v = "";
	document.cookie = "username=" + v + ";expires=" + d.toGMTString() + ";" + ";";
	FB.Connect.logoutAndRedirect('./login.php');
}
function update_user_lcls() {
	 var d = new Date();
	var v = "";
	document.cookie = "username=" + v + ";expires=" + d.toGMTString() + ";" + ";";
	window.location = "./login.php";
}
</script>
<script type="text/javascript" src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" mce_src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php"> </script>
<script type="text/javascript">
var api_key = "08d9b0ee7abba4710fe83f380b1d67d4";
var channel_path = "xd_receiver.htm";
FB.init(api_key, channel_path, {"ifUserConnected": update_user_fb, "ifUserNotConnected": update_user_lcls});

</script>

<?php
unset($_SESSION['username']);
unset($_SESSION['userid']);
unset($_SESSION['confirm_string']);
unset($_SESSION['nickname']);
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}
session_destroy();
header("location:m_login.php");
exit;
?>
