<?php
require_once ('classes/frontend.class.php');
require_once ('functions.php');

  $front = Frontend::getInstance();
  if($front->isUserLoggedIn()){
      header('location: rewarder.php');
      exit(0);
  } else if($front->isUserTryingToAuthenticate()) {
      if(!$front->tryToAuthenticateUser()){
          header('location: rewarder.php');
          exit(0);
      }
  }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Welcome to Review</title>
<link href="css/login.css" rel="stylesheet" type="text/css" />
<link href="css/feedback.css" rel="stylesheet" type="text/css" />
<link href="css/smoothness/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
<!-- jquery file is for LiveValidation -->
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.min.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>livevalidation.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tabSlideOut.v1.3.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>feedback.js"></script>
<script type="text/javascript" src="js/ping_admin.js"></script>
<script type="text/javascript">
  $('document').ready(function(){
	  var username = new LiveValidation(
	    'username',
	    { validMessage: "Valid email address.", onlyOnBlur: false }
	  );
	  username.add(SLEmail);
	  
	  //var openid = new LiveValidation('googleLogin', {validMessage: 'Valid url.', onlyOnBlur: false});
	  //openid.add(Validate.Format, { pattern: /((http|https)(:\/\/))?([a-zA-Z0-9]+[.]{1}){2}[a-zA-z0-9]+(\/{1}[a-zA-Z0-9]+)*\/?/i, failureMessage: "Must be a valid url!" });
	});
</script>
</head>
<body>
<!-- login header html -->
  <?php
require_once ('view/login/header.php')?>
  
  <!-- login content html -->
  <?php
require_once ('view/login/content.php')?>
  
  <!-- login footer html -->
  <?php
require_once ('view/login/footer.php')?>
  
  <!-- Feedback tab html -->
  <?php
require_once ('feedback.inc')?>
</body>
</html>
