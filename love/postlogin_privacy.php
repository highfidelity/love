<?php
require_once('config.php');
include_once("db_connect.php");
include_once("autoload.php");
if (!defined('LOVE_TABS_DISABLED')) define('LOVE_TABS_DISABLED', false);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>SendLove | Privacy Statement</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="css/settings.css" />    
    <link rel="stylesheet" type="text/css" href="css/tofor.css" />    
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css" media="all" />
    <link rel="stylesheet" type="text/css" href="css/smoothness/lm.ui.css"/>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery-ui.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.blockUI.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>livevalidation.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>ajaxupload.js" charset="utf-8"></script>
    <script type="text/javascript">
/* <![CDATA[ */
    // set up user object
    $.user = {
        love_key: '<?php echo API_KEY; ?>',
        user_id: <?php echo isset($_SESSION['userid']) ? $_SESSION['userid'] : 0; ?>,
        username: '<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>',
        nickname: '<?php echo isset($_SESSION['nickname']) ? $_SESSION['nickname'] : ''; ?>',
        isAdmin: <?php echo (isset($_SESSION['admin']) && $_SESSION['admin'] === 1) ? 'true' : 'false'; ?>
    };
    
    var smsEnabled = <?php echo SMS_ENABLED === true ? 'true' : 'false'; ?>;
    var smsProvider = '<?php echo $front->getUser()->getProvider(); ?>';
/* ]]> */
    </script>
<?php if (SMS_ENABLED): ?>    <script type="text/javascript" src="js/sendlove.js" charset="utf-8"></script><?php endif; ?>
	<!--<script type="text/javascript" src="js/settings.js" charset="utf-8"></script>-->
	<?php
		$files = array(
			'settings.js'
		);
	
		$compressor = new Compressor();
		$compressor->setCompressorType('js')
				   ->setPath(APP_PATH . '/js')
				   ->setFiles($files)
				   ->setFilename('postlogin_privacy');
		$combinedJs = $compressor->compile();
	?>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>branding.js" charset="utf-8"></script>    

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
<body style="padding-left:25px;">
    <div id="wrapper">
        <!-- Include header -->
        <?php include("view/settings/header.php"); ?>
        <div id="content">
			<div id="settings">	
				<!-- Include privacy content -->
				<?php include("view/static/privacy.php"); ?>
			</div>	
            <!-- Include footer -->
        </div>
        <?php include("view/tofor/footer.php"); ?>
        <p class="lm"><a href="http://www.sendlove.us" target="_blank"><img class="logo_footer" src="images/SendLove_logo_sm.png"/></a></p>
    </div>
</body>
</html>
