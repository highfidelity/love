<?php
    require_once('class/frontend.class.php');
    $front = Frontend::getInstance();
    $redir = "tofor.php";
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
<title>Welcome to SendLove</title>
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="all" />
<link href="css/login.css" rel="stylesheet" type="text/css" />

<?php
if (strstr(substr($_SERVER['REQUEST_URI'], 0, 3), '~') || (!defined('USE_COMPILED_LIBRARIES') || ( USE_COMPILED_LIBRARIES !== true ) ) ){
    echo '<script type="text/javascript" src="' . CONTRIB_URL . 'love_login.combined.js"></script>';
} else {
    echo '<script type="text/javascript" src="' . CONTRIB_URL . 'love_login.compiled.js"></script>';
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
			   ->setFilename('login');
	$combinedJs = $compressor->compile();
?>
<script type="text/javascript">
  $('document').ready(function(){
<?php if(!defined('PASSTHROUGH_LOGIN_USERNAME')) { ?>
	  /*var username = new LiveValidation(
	    'username',
	    { validMessage: "Valid email address.", onlyOnBlur: false }
	  );
	  username.add(SLEmail);*/
	  
	  //var openid = new LiveValidation('googleLogin', {validMessage: 'Valid url.', onlyOnBlur: false});
	  //openid.add(Validate.Format, { pattern: /((http|https)(:\/\/))?([a-zA-Z0-9]+[.]{1}){2}[a-zA-z0-9]+(\/{1}[a-zA-Z0-9]+)*\/?/i, failureMessage: "Must be a valid url!" });
<?php } ?>
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
  
  <!-- login content html -->
  <?php require_once('view/login/content.php') ?>
  
  <!-- login footer html -->
  <?php require_once('view/login/footer.php') ?>
</body>
</html>
