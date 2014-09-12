<?php

// markletised login page


    require_once('class/frontend.class.php');
    $front = Frontend::getInstance();
    $redir = "m_tofor.php";
    if ( isset($_REQUEST['redir']) ) {
        $redir = $_REQUEST['redir'];
    }
    if($front->isUserLoggedIn()){
        header('location: ' . $redir);
        exit(0);
    } else if($front->isUserTryingToAuthenticate()) {
        if(!$front->tryToAuthenticateUser()){
            header('location: ' . $redir);
            exit(0);
        }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Welcome to LoveMachine</title>
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="all" />
<link href="css/m_login.css" rel="stylesheet" type="text/css" />

<?php
if (strstr(substr($_SERVER['REQUEST_URI'], 0, 3), '~') || (!defined('USE_COMPILED_LIBRARIES') || ( USE_COMPILED_LIBRARIES !== true ) ) ){
    echo '<script type="text/javascript" src="' . CONTRIB_URL . 'love_login.combined.js"></script>';
} else {
    echo '<script type="text/javascript" src="' . CONTRIB_URL . 'love_login.compiled.js"></script>';
}
?>

<script type="text/javascript">
  $('document').ready(function(){
<?php if(!defined('PASSTHROUGH_LOGIN_USERNAME')) { ?>
	  var username = new LiveValidation(
	    'username',
	    { validMessage: "Valid email address.", onlyOnBlur: false }
	  );
	  username.add(SLEmail);
	  
	  //var openid = new LiveValidation('googleLogin', {validMessage: 'Valid url.', onlyOnBlur: false});
	  //openid.add(Validate.Format, { pattern: /((http|https)(:\/\/))?([a-zA-Z0-9]+[.]{1}){2}[a-zA-z0-9]+(\/{1}[a-zA-Z0-9]+)*\/?/i, failureMessage: "Must be a valid url!" });
<?php } ?>
	});
</script>
<script type="text/javascript" src="js/login.js"></script>
</head>
<body>
  <!-- login header html -->
  <?php //require_once('view/login/m_header.php') ?>
  
  <!-- login content html -->
  <?php require_once('view/login/m_content.php') ?>
  
  <!-- login footer html -->
  <?php //require_once('view/login/m_footer.php') ?>
</body>
</html>
