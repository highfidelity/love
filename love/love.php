<?php

include("class/frontend.class.php");
include_once("helper/check_new_user.php"); 
$front = Frontend::getInstance();

include_once("db_connect.php");
include_once("autoload.php");
include("review.php");

if(!$front->isUserLoggedIn()){
    $front->getUser()->askUserToAuthenticate();
}

$tab = isset($_REQUEST['tab']) ? intval($_REQUEST['tab']) : 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>LoveMachine</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
    
    <link type="text/css" href="css/tofor.css" rel="Stylesheet" />
    <link type="text/css" href="css/review.css" rel="Stylesheet" />
    <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="all" />
    <link rel="stylesheet" type="text/css" href="css/smoothness/lm.ui.css"/>
    
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery-ui.min.js"></script>
    
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>raphael-min.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.blockUI.js"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tools.tooltip.min.js"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>livevalidation.js"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jstorage.js"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jcache.js"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>branding.js"></script>

    <!--<script type="text/javascript" src="js/tofor-chart.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/periods.js"></script>-->
	<?php
		$files = array(
			'tofor-chart.js',
			'periods.js'
		);
	
		$compressor = new Compressor();
		$compressor->setCompressorType('js')
				   ->setPath(APP_PATH . '/js')
				   ->setFiles($files)
				   ->setFilename('love');
		$combinedJs = $compressor->compile();
	?>
    <script type="text/javascript">
      /////////////////////////////
     /// Configuration strings ///
    /////////////////////////////
    
    // this is the refresh rate aka heart beat
    // the value is used as timeout value before
    // requesting update from the server.
    var hBeat = 60000; 
    
    //Amount of messages to fetch
    //(can be changed through settings tab)
    var msg_amount = 30;
    //Interval for updating from DB
    var timer = 0;
    //Message rotation interval
    var rTimer = 0;
    //Rotation counter
    var rCounter = 0;
    //Just Feed View flag
    var fView = false;
    //Color for checkboxes
    var checkboxBackground = '<?php echo($front->getCompany()->getReview_done_color()); ?>';
    
    var to_lv;
    var for1_lv;
    var _fromDate, _toDate;
    var fromDate = '';
    var toDate = '';
    var datePickerControl; // Month/Year date picker.
    var dateChangedUsingField = false; // True  if the date was changed using date field rather than picker.
    
    // set up user object
    $.user = {
    	love_key: '<?php echo API_KEY; ?>',
        company_id: <?php $front->getCompany()->outid(); ?>,
        user_id: <?php $front->getUser()->outid(); ?>,
        username: '<?php $front->getUser()->outusername(); ?>',
        nickname: '<?php $front->getUser()->outnickname(); ?>',
        review_done_color: '<?php $front->getCompany()->outreview_done_color(); ?>',
        review_not_done_color: '<?php $front->getCompany()->outreview_not_done_color(); ?>',
        review_started: 'orange', // should be replaced by an admin color setting ...
    	isAdmin: <?php echo(($front->getUser()->getCompany_admin() === true) ? 'true' : 'false'); ?>,
        splashScreen: <?php echo(($front->getUser()->getSplash() === true) ? 'true' : 'false'); ?>,
        jcacheDelay: <?php echo JCACHE_DELAY; ?>
    };
    // reviewConfig object used to get all the review configuration data
    // in general those variables are statics
    var reviewConfig = function($)
    {
        // Define here all the private variables
        // limit of loves to select for period
        var love_limit = <?php echo REVIEW_LOVE_LIMIT; ?>,
        // number of loves user can make favorite
            favorite_limit = <?php echo FAVORITE_LOVE_LIMIT; ?>,
            review_url = '<?php echo REVIEW_URL; ?>';
        return {
            getLoveLimit : function() { return love_limit;},
            getFavoriteLimit : function() { return favorite_limit;},
            getReviewUrl : function() { return review_url;}
        };
    }(jQuery); // end of object reviewConfig
    
    // Make sure the cloud code knows this is not the live feed
    var live_feed = false;
    </script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tabSlideOut.v1.3.js"></script>
    <!-- <script type="text/javascript" src="js/stage.settings.js"></script> -->
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.masonry.mod.min.js"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tipsy.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.inview.js" charset="utf-8"></script>
    <!-- <script type="text/javascript" src="js/livefeed.js"></script> -->
    <!-- <script type="text/javascript" src="js/tofor.js"></script> -->
    <!-- review js -->
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.listnav.min-2.1.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>g.raphael-min.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>g.pie-min.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tablePagination.0.2.min.js" charset="utf-8"></script>
	<?php
		$files = array(
			'stage.settings.js',
			'livefeed.js',
			'tofor.js',
			'review.js',
			'review-form.js'
		);
	
		$compressor = new Compressor();
		$compressor->setCompressorType('js')
				   ->setPath(APP_PATH . '/js')
				   ->setFiles($files)
				   ->setFilename('love2');
		$combinedJs = $compressor->compile();
	?>
    <!--
    <script type="text/javascript" src="js/review.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/review-form.js" charset="utf-8"></script>-->
</head>

<body>
    <div id="wrapper">
        <!-- Include header -->
        <?php include("view/love/header.php"); ?>
      
        <div id="content" style="clear:both;">
            <!-- Include tabs -->
            <?php include("view/love/tabContents.php"); ?>
            <!-- Include footer -->
            <?php include("view/tofor/footer.php"); ?>
        </div>
        
        <p class="lm"><a href="http://www.lovemachineinc.com" target="_blank"><img src="images/LMLogo3.png"  border="0"/></a></p>
    	<?php include('dialogs/splashscreen.inc'); ?>
    	
        <div id="user-love-popup"></div>
        <div id="tooltip" class="tooltip">
            <img src="img/tooltip/spinner.gif" border="0" alt="Loadin" />
        </div>
    </div>
</body>
</html>
