<?php
//  vim:ts=4:et
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

include("class/frontend.class.php");
include_once("helper/check_new_user.php"); 
$front = Frontend::getInstance();

include_once("db_connect.php");
include_once("autoload.php");

if(!$front->isUserLoggedIn()){
    $front->getUser()->askUserToAuthenticate();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="copyright" content="&copy; 2012, Below92, LLc.  All Rights Reserved. http://www.below92.com ">
		<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico"/>
		<link href="css/tofor.css" rel="stylesheet" type="text/css"/>
		<link href="css/stage.settings.css" rel="stylesheet" type="text/css"/>
		<link href="css/StageStyle.css" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="all" />
		<link rel="stylesheet" type="text/css" href="css/smoothness/lm.ui.css"/>

		<title>SendLove | Live Feed</title>
	</head>
	<body>
        <div id="contentWrapper">
            <div class="content">
                <?php include('view/tofor/loveCloudDiv.php'); ?>
            </div>
        </div>
		
		<!-- Settings tab html -->
		<?php require_once('dialogs/stage-settings.inc'); ?>
	</body>


<?php
if (strstr(substr($_SERVER['REQUEST_URI'], 0, 3), '~') || (!defined('USE_COMPILED_LIBRARIES') || ( USE_COMPILED_LIBRARIES !== true ) ) ){
    echo '<script type="text/javascript" src="' . CONTRIB_URL . 'love_stage.combined.js"></script>';

} else {
    echo '<script type="text/javascript" src="' . CONTRIB_URL . 'love_stage.compiled.js"></script>';
}
?>

<?php
	$files = array(
		'tofor-chart.js',
		'stage.settings.js'
	);
	
	$compressor = new Compressor();
	$compressor->setCompressorType('js')
			   ->setPath(APP_PATH . '/js')
			   ->setFiles($files)
			   ->setFilename('stage');
	$combinedJs = $compressor->compile();
?>
<script type="text/javascript">
var live_feed = true;
</script>
<?php
	$files = array(
		'livefeed.js'
	);
	
	$compressor = new Compressor();
	$compressor->setCompressorType('js')
			   ->setPath(APP_PATH . '/js')
			   ->setFiles($files)
			   ->setFilename('stage2');
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
    company_id: <?php $front->getCompany()->outid(); ?>,
    user_id: <?php $front->getUser()->outid(); ?>,
    username: '<?php $front->getUser()->outusername(); ?>',
    nickname: '<?php $front->getUser()->outnickname(); ?>',
    review_done_color: '<?php $front->getCompany()->outreview_done_color(); ?>',
    review_not_done_color: '<?php $front->getCompany()->outreview_not_done_color(); ?>'
};



$.currentTab = 'love';

$(window).resize(function() {
    var win_h = $(window).height();
    var feed_h = $('.feedWrapper')[0].offsetHeight;
    $('.cloud-div').animate({height:(win_h - ((feed_h*1.5) + 10)) +'px'});

    // Set Cloud size
    //$('#companyTags').css('height',(win_h - (feed_h*2)) +'px');
    // Get the window size, if big resolution
    // Change the font size for the cloud element

    // Set fonts
    setCloudFonts();
});

function setCloudFonts() {
    // Get current font size
    var size_s = $('#companyTags').css('font-size');
    size_s = size_s.substring(0, size_s.length-2);
    var size = parseInt(size_s);
    var w_height = $(window).height();
    
    if (w_height > 600 && w_height < 900) {
        $('#companyTags').css('font-size',14+'px');
    }
    if (w_height > 1000) {
        $('#companyTags').css('font-size',20+'px');
    }
    if (w_height > 1250) {
        $('#companyTags').css('font-size',35+'px');
    }
    if (w_height > 1500) {
        $('#companyTags').css('font-size',44+'px');
    }
    clearInterval($.timers['cloud']);
    // Reload the cloud
    $.cloudInit();
    $.cloud.load('companyTags');
}

$(document).ready(function() {
    // Reset certain styles so it fills up the whole view
    $('#contentWrapper').css('position','inherit');

    var win_h = $(window).height();
    var feed_h = $('.feedWrapper')[0].offsetHeight;
    $('.cloud-div').animate({height:(win_h - (feed_h*1.5)) +'px'});

    // Set Cloud size
    $('#companyTags').css('height',(win_h - (feed_h*2)) +'px');
    // Set fonts
    setCloudFonts();
    
	$.cloudInit();
	$.cloud.load('companyTags');
	$.updateLove();
});
</script>
</html>

