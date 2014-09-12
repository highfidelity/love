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
$company_id1 = $front->getCompany()->getid();
$user_id1 = $front->getUser()->getid();

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="copyright" content="Copyright (c) 2010, SendLove Inc.  All Rights Reserved. http://www.lovemachineinc.com ">

    <title>SendLove</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
    
    <link type="text/css" href="css/tofor.css" rel="Stylesheet" />
    <link type="text/css" href="css/review.css" rel="Stylesheet" />
    <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="all" />
    <link rel="stylesheet" type="text/css" href="css/smoothness/lm.ui.css"/>

    <link rel="stylesheet" type="text/css" media="screen" href="css/ui.jqgrid.css" />
    <link rel="Stylesheet" type="text/css" href="css/periods.css" /> 
    <link rel="stylesheet" type="text/css" href="css/loveTabs.css"/>

    
    <?php
    if (strstr(substr($_SERVER['REQUEST_URI'], 0, 3), '~')|| !defined('USE_COMPILED_LIBRARIES') || ( USE_COMPILED_LIBRARIES !== true )  ) {
        // Disabled by Marco's request
        // echo '<script type="text/javascript" src="' . CONTRIB_URL . 'tofor_redeem.combined.js"></script>';
        ?>
        
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery-ui.min.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>raphael-min.js"></script>
        
        <script type="text/javascript" src="js/tooltip.js"></script>
        <script type="text/javascript" src="js/periods.js"></script>
        <script type="text/javascript" src="js/campaign.js"></script>
        <script type="text/javascript" src="js/love_redeem.js"></script>
        <script type="text/javascript" src="js/stage.settings.js"></script>
        <script type="text/javascript" src="js/livefeed.js"></script>
        <script type="text/javascript" src="js/review.js"></script>
        <script type="text/javascript" src="js/review-form.js"></script>
        
        <?php
    } else {
        echo '<script type="text/javascript" src="' . CONTRIB_URL . 'tofor_redeem.compiled.js"></script>';
        
        
        // Include compressed files
        $files = array(
            'tooltip.js',
            'periods.js', 
            'campaign.js',
            'love_redeem.js',
            'stage.settings.js',
            'livefeed.js',
            'review.js',
            'review-form.js'
        );
        
        $compressor = new Compressor();
        $compressor->setCompressorType('js')
                   ->setPath(APP_PATH . '/js')
                   ->setFiles($files)
                   ->setFilename('tofor1');
        if (strstr(substr($_SERVER['REQUEST_URI'], 0, 3), '~')|| !defined('USE_COMPILED_LIBRARIES') || ( USE_COMPILED_LIBRARIES !== true )  ) {
            $compressor->combine();
        } else {
            $compressor->compile();
        }
    }
    ?>
    
    <?php
    if (strstr(substr($_SERVER['REQUEST_URI'], 0, 3), '~')|| !defined('USE_COMPILED_LIBRARIES') || ( USE_COMPILED_LIBRARIES !== true ) ) {
        // Disabled by Marco's request
        //echo '<script type="text/javascript" src="' . CONTRIB_URL . 'tofor_redeem2.combined.js"></script>';
        ?>
        
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.blockUI.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>livevalidation.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.charcount.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>i18n/grid.locale-en.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.jqGrid.min.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tools.tooltip.min.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tabSlideOut.v1.3.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.masonry.mod.min.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tipsy.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.inview.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>branding.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>feedback.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jstorage.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jcache.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.listnav.min-2.1.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>g.raphael-min.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>g.pie-min.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tablePagination.0.2.min.js"></script>
        
        <script type="text/javascript" src="js/tofor-chart.js"></script>
        <script type="text/javascript" src="js/tofor.js"></script>
        
        <?php
    } else {
        echo '<script type="text/javascript" src="' . CONTRIB_URL . 'tofor_redeem2.compiled.js"></script>';
        
        
        // Include compressed files
        $files2 = array(
            'tofor-chart.js',
            'tofor.js'
        );
        
        $compressor2 = new Compressor();
        $compressor2->setCompressorType('js')
                   ->setPath(APP_PATH . '/js')
                   ->setFiles($files2)
                   ->setFilename('tofor2');
        if (strstr(substr($_SERVER['REQUEST_URI'], 0, 3), '~')|| !defined('USE_COMPILED_LIBRARIES') || ( USE_COMPILED_LIBRARIES !== true )  ) {
            $compressor2->combine();
        } else {
            $compressor2->compile();
        }
    }
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
        company_id: <?php echo (( isset($company_id1) && $company_id1 != "") ? $company_id1 : -1 ); ?>,
        user_id: <?php echo( ( isset($user_id1 ) && $user_id1 != "") ? $user_id1 : -1 ) ; ?>,
        username: '<?php $front->getUser()->outusername(); ?>',
        nickname: '<?php $front->getUser()->outnickname(); ?>',
        review_done_color: '<?php $front->getCompany()->outreview_done_color(); ?>',
        review_not_done_color: '<?php $front->getCompany()->outreview_not_done_color(); ?>',
        review_started: 'orange', // should be replaced by an admin color setting ...
        isAdmin: <?php echo(($front->getUser()->getCompany_admin() === true) ? 'true' : 'false'); ?>,
        splashScreen: <?php echo(($front->getUser()->getSplash() === true) ? 'true' : 'false'); ?>,
        jcacheDelay: <?php echo JCACHE_DELAY; ?>,
        listUserLovesWhen: true,
        listCompanyLovesWhen: true
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

    // Initialize the application
    $(function(){
        // Set cache expirtation time
        $.cacheExpTime = $.user.jcacheDelay;
        $.brand();

        $.timers = [];
        // This initilizes the tabs
        $.switchTabs.init();
        $.cloudInit();
        $.loveTabInit();
        $.loveTab();
        $.lowerTabInit();
        $.splashScreen.init();
        $.help.init();
        $('a').live('click', $.resizeLower);
        $('.contents a').css("color","");  // remove the branding color (highlight1) for the links in the list of Loves
    });

    // setup a timer to update all dynamic love elements every 2 minutes
    (function() {
        var interval = 2 * 60 * 1000;
        $.timers['all'] = setInterval(function() {
            refresh();
        }, interval);
    })();
    </script>
    
    <script type="text/javascript" src="js/uservoice.js"></script>
</head>

<body>
    <div id="wrapper">
        <!-- Include header -->
        <?php include("view/love/header.php"); ?>
      
        <div id="content" style="clear:both;">
            <!-- Include tabs -->
            <?php include("view/tabs/love_redeem.php"); ?>
        </div>
        
        <p class="lm"><a href="http://www.sendlove.us" target="_blank"><img class="logo_footer" src="images/SendLove_logo_sm.png"/></a></p>
    	<?php include('dialogs/splashscreen.inc'); ?>
    	<?php include('dialogs/help-maillove.inc'); ?>
        <div id="user-love-popup"></div>
        <div id="tooltip" class="tooltip">
            <img src="img/tooltip/spinner.gif" border="0" alt="Loading" />
        </div>
    </div>
</body>
</html>
