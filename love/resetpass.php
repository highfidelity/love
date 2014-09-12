<?php
//  Copyright (c) 2010, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
require_once('class/frontend.class.php');

$front = Frontend::getInstance();
$msg = '';

if (! empty($_POST['submit'])) {
    if (!empty($_POST['password'])) {
        $vars = array(
            'username' => $_POST['username'],
            'token' => $_POST['token'], 
            'password' => $_POST['password']
        );

        // send the request
        ob_start();
        CURLHandler::Post(LOGIN_APP_URL . 'changepassword', $vars);
        $result = json_decode(ob_get_contents());
        ob_end_clean();
      
        if ($result->success == true) {
            sendTemplateEmail($_POST['username'], 'changed_pass', array('app_name' => APP_NAME));
            header('Location: login.php');
        } else {
            $msg = 'The link to reset your password has expired or is invalid. <a href="forgot.php">Please try again.</a>';
        }
    } else {
        $msg = "Please enter a password!";
    }
}

if (empty($_REQUEST['token'])) {
    // no required information specified, redirect user
    header('Location: login.php');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>LoveMachine | Reset Password</title>
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="all" />
<link href="css/login.css" rel="stylesheet" type="text/css" />
<!-- jquery file is for LiveValidation -->
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.min.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>livevalidation.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tabSlideOut.v1.3.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>feedback.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.blockUI.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jstorage.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jcache.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>branding.js"></script>
<!--<script type="text/javascript" src="js/login.js"></script>-->
<?php
	$files = array(
		'login.js'
	);
	
	$compressor = new Compressor();
	$compressor->setCompressorType('js')
			   ->setPath(APP_PATH . '/js')
			   ->setFiles($files)
			   ->setFilename('resetpass');
	$combinedJs = $compressor->compile();
?>
<script type="text/javascript">
 function validate() {
 
  if(document.frmlogin.username.value=="") {
  alert("Please enter your email");
  document.frmlogin.username.focus();
  return false;
  } 
  else if (!(/^\w+([+\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.frmlogin.username.value))){
  alert("Invalid email address! please re-enter");
  document.frmlogin.username.focus();
  return false;
  }
  else if(document.frmlogin.password.value=="") {
  alert("Please enter your password");
  document.frmlogin.password.focus();
  return false;
  } 
  else if(document.frmlogin.password.value!=document.frmlogin.confirmpassword.value)
  {
    alert("Your passwords don't match");
    document.frmlogin.confirmpassword.focus();
    return false;
  }
  else 
  return true;
 }
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
<body>
  <!-- login header html -->
  <?php require_once('view/login/header.php') ?>
  
  <!-- reset content html -->
  <?php require_once('view/reset/content.php') ?>
  
  <!-- login footer html -->
  <?php require_once('view/login/footer.php') ?>
</body>
</html>
