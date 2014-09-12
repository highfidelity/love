<?php
require_once('config.php');
require_once('autoload.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SendLove | Privacy Statement</title>
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="all" />
<link href="css/login.css" rel="stylesheet" type="text/css" />
<!-- jquery file is for LiveValidation -->
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.min.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>livevalidation.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tabSlideOut.v1.3.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>feedback.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.blockUI.js"></script>
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
			   ->setFilename('prelogin_privacy');
	$combinedJs = $compressor->compile();
?>
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
  
	<div id="content">  
	  <!-- privacy content html -->
	  <?php require_once('view/static/privacy.php') ?>
  	</div>
  <!-- login footer html -->
  <?php require_once('view/login/footer.php') ?>
</body>
</html>
