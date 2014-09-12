<?php

//  vim:ts=4:et
//
//  Copyright (c) 2010, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com
//
//session_start();

    

$app_root_path = dirname(__FILE__)."/";
include($app_root_path."config.php");

//ob_start();
$load_module = false;
if (isset($_REQUEST['load'])) {
    if ($_REQUEST['load'] == 'module') {
        $load_module = true;
    }
}
require('classes/Session.class.php');
require_once('classes/Compressor.class.php');
require_once('classes/CompressedFiles.class.php');

session::init();
if ( $load_module == false ) {
    session::check();
    //include($app_root_path."class.session_handler.php");
}
include($app_root_path."helper/check_session.php");
include($app_root_path."functions.php");

/* If the parameter "load=module" is passed to the page:
 * we load the view without the header and footer, just
 * the barebones View/History.
 * 28/05/2010 <andres>
 */
$loadGraphOnly = false;
if (isset($_REQUEST['loadGraphOnly'])) {
    if ($_REQUEST['loadGraphOnly'] == 'true') {
        $loadGraphOnly = true;
    }
} 

$loadFirstTime = false;
if (isset($_REQUEST['loadFirstTime'])) {
    if ($_REQUEST['loadFirstTime'] == 'true') {
        $loadFirstTime = true;
    }
} else {
    $loadFirstTime = true;
}
/* End of modular initialization */

$showTab = 0;
if (!empty($_REQUEST['view'])) {
    if ($_REQUEST['view'] == 'history') {
        $showTab = 1;
    }
}

$user = new User();
$user->findUserById($_SESSION['userid']);
$rewarder = new Rewarder($user->getId());

$audit_mode = ($user->getIs_auditor() && !empty($_REQUEST['audit'])) ? 1 : 0;

// THIS IS TEMPORARY UNTIL LOGIN MODULE IS IN PLACE!!!
$is_admin = ($user->getIs_admin() ) ? 1 : 0;
/***
if ($audit_mode) {
    $userList = GetUserList($_SESSION['userid'], $_SESSION['nickname'], true, array('is_auditor'));
} else {
    $userList = GetUserList($_SESSION['userid'], $_SESSION['nickname'], true);
	
    // Strip users already in the rewarderList 
    $rewarderList = $rewarder->getRewarderUserList($_SESSION['userid']);
    foreach ($rewarderList as $info) {
	    unset($userList[$info['id']]);
    }
}
// Get the users for the user selection
$user_box = '<select id="user-list" name="userbox"><option value="0">Add Co-worker</option>';
foreach ($userList as $userid=>$nickname) {
    $user_box .= '<option value="'.$userid.'">'.$nickname['nickname'].'</option>';
}
$user_box .= '</select>';

//-- Overall balance variables ---
if (isset($_SESSION['is_auditor']) && $_SESSION['is_auditor'] && $audit_mode) {
    // get global values for auditor
    $userPoints = mysql_fetch_row(mysql_query('select sum(`rewarder_points`) from ' . REVIEW_REWARDER));    
    $totalGrant = Rewarder::getStatsGranted();
    $totalAlloc = $userPoints[0] + $totalGrant;
}else{
    // restrict values for usual user
    $userPoints = $user->getRewarder_points();
    $totalGrant = $rewarder->getUserStatsGranted();
    $totalAlloc = $userPoints + $totalGrant;
}
$percentGranted = $totalAlloc ? ceil(($totalGrant/$totalAlloc)*100) : 0;
***/
//-----

/*********************************** HTML layout begins here  *************************************/
if (!$load_module) {
    include("head.php");
}


// Add page-specific scripts and styles here, see head.html for global scripts and styles  -->
// Additional scripts should be included at the end of the page, after the main content. -->

if (!$load_module) {
    echo '<link href="css/CMRstyles.css" rel="stylesheet" type="text/css">';
	echo '<link href="css/LVstyles.css" rel="stylesheet" type="text/css">';
	echo '<link href="css/jquery.autocomplete.css" rel="stylesheet" type="text/css">';
    echo '<link rel="stylesheet" href="css/lm.ui.css" type="text/css" media="all" />';
} 
?>
	<link type="text/css" href="<?php echo REVIEW_URL; ?>/css/rewarder.css" rel="stylesheet" />
<?php 
if (!$load_module){ ?>	
	<title>Review</title>
</head>
<body>
<?php include("format.php"); ?>

<!-- ---------------------- BEGIN MAIN CONTENT HERE ---------------------- -->

    <h1>Review</h1>
<?php } ?>

<div style="clear: both"></div>
<?php
if ($loadGraphOnly) {
    include("rewarder_graph.php");
} else {
?>
<div id="review-tabs" style="margin-top:8px;">
    <ul>
        <li><a href="#tab-reward">Reward</a></li>
        <li><a href="#tab-history">History</a></li>
    </ul>
    
    <div id="tab-reward">
        <?php 
            include("rewarder_graph.php");
        ?>
    </div>
    <div id="tab-history">
        <div id="period-filter">
            <select id="period-box"></select>
        </div>
        <div id="timeline-graph"></div>
    </div>
</div>
<?php 
}
?>
<div style="clear: both"></div>

<!-- ---------------------- end MAIN CONTENT HERE ---------------------- -->
    <link href="<?php echo REVIEW_URL;?>/css/jquery.combobox.css" rel="stylesheet" type="text/css">

<?php
include("dialogs/finish-period.inc");

/* In standalone mode, we include jquery and livevalidation
 * any other script not required to be added in module mode
 * should be loaded here as well.
 */
if (!$load_module) {
    Compressor::echoInclude("review_rewarder1");
    Compressor::echoInclude("rewarder1");
    Compressor::echoInclude("review_rewarder2");
} else { 
    if ($loadFirstTime == true) {
        Compressor::echoInclude("rewarder2");
    }
}
if ($loadFirstTime == true) {
    include("module_scripts.php");  
}
?>
<script type="text/javascript"  charset="utf-8">
$(function(){
    window.currentTab = <?php echo $showTab; ?>; // 0 for rewarder and 1 for chart
    window.review_url = '<?php echo REVIEW_URL;?>';
    window.love_url = '<?php echo LOVE_URL;?>';

    window.rewarder.initialLoad = true;
    
    <?php 
    if (!$load_module) { ?>
        rewarder.initRewarder();
    <?php  } ?>
});
</script>
<?php
if (!$load_module) {
    include("google_analytics.inc");
    include("footer.php"); 
}
?>
