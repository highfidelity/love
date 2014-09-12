<?php 
//  Copyright (c) 2009, LoveMachine Inc.                                                                                                                          
//  All Rights Reserved.                                                                                                                                          
//  http://www.lovemachineinc.com
require_once("class/frontend.class.php");
require_once("send_email.php");

$front = Frontend::getInstance();

if(!empty($_POST['username'])) {
	ob_start();
    // send the request
    CURLHandler::Post(LOGIN_APP_URL . 'resettoken', array('username' => $_POST['username'], 'app' => 'lovemachine', 'key' => API_KEY));
    $result = ob_get_contents();
    ob_end_clean();
    
    $result = json_decode($result);
    if ($result->success == true) {
    	$resetUrl = SECURE_SERVER_URL . 'resetpass.php?un=' . base64_encode($_POST['username']) . '&token=' . $result->token;
    	$resetUrl = '<a href="' . $resetUrl . '" title="Password Recovery">' . $resetUrl . '</a>';
    	sendTemplateEmail($_POST['username'], 'recovery', array('url' => $resetUrl));
    	$msg= '<p class="LV_valid">Login information will be sent if the email address ' . $_POST['username'] . ' is registered.</p>';
    } else {
    	$msg = '<p class="LV_invalid">Sorry, unable to send password reset information. Try again or contact an administrator.</p>';
    }
}

/*********************************** HTML layout begins here  *************************************/
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SendLove | Reset Password</title>
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="all" />
<link href="css/login.css" rel="stylesheet" type="text/css" />

<?php
if (strstr(substr($_SERVER['REQUEST_URI'], 0, 3), '~') || (!defined('USE_COMPILED_LIBRARIES') || ( USE_COMPILED_LIBRARIES !== true ) ) ){
    echo '<script type="text/javascript" src="' . CONTRIB_URL . 'love_forgot.combined.js"></script>';
} else {
    echo '<script type="text/javascript" src="' . CONTRIB_URL . 'love_forgot.compiled.js"></script>';
}
?>

<?php
	$files = array(
		'login.js'
	);
	
	$compressor = new Compressor();
	$compressor->setCompressorType('js')
			   ->setPath(APP_PATH . '/js')
			   ->setFiles($files)
			   ->setFilename('forgot');
	$combinedJs = $compressor->compile();
?>
<script type="text/javascript">
  $('document').ready(function(){
      var username = new LiveValidation(
        'username',
        { validMessage: "Valid email address.", onlyOnBlur: false }
      );
      username.add(SLEmail);

    });
</script>
<script type="text/javascript">
    var uservoiceOptions = {
      /* required */
      key: 'sendlove',
      host: 'sendlove.uservoice.com', 
      forum: '75971',
      showTab: true,  
      /* optional */
      alignment: 'right',
      background_color:'#f00', 
      text_color: 'white',
      hover_color: '#000',
      lang: 'en'
    };

    function _loadUserVoice() {
      var s = document.createElement('script');
      s.setAttribute('type', 'text/javascript');
      s.setAttribute('src', ("https:" == document.location.protocol ? "https://" : "http://") + "cdn.uservoice.com/javascripts/widgets/tab.js");
      document.getElementsByTagName('head')[0].appendChild(s);
    }
    _loadSuper = window.onload;
    window.onload = (typeof window.onload != 'function') ? _loadUserVoice : function() { _loadSuper(); _loadUserVoice(); };
</script>
</head>
<body>
  <!-- login header html -->
  <?php require_once('view/login/header.php') ?>
  
  <!-- forgot content html -->
  <?php require_once('view/forgot/content.php') ?>
  
  <!-- login footer html -->
  <?php require_once('view/login/footer.php') ?>
  
</body>
</html>
