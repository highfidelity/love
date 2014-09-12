<?php
if (!defined('APP_PATH'))     define('APP_PATH', realpath(dirname(__FILE__)));
include("loginFromAdmin.php");    // Must be inserted before any session call 
include("class/frontend.class.php");
require_once('class/Database.class.php');

checkLoginFromAdmin($userid_from_zend);
$front = Frontend::getInstance();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>LoveMachine</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
    
    <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="all" />
    <link rel="stylesheet" type="text/css" href="css/smoothness/lm.ui.css"/>

    <link rel="stylesheet" type="text/css" media="screen" href="css/ui.jqgrid.css" />
    <link type="text/css" href="css/tofor.css" rel="Stylesheet" />
    <link type="text/css" href="css/review.css" rel="Stylesheet" />
    <link type="text/css" href="css/periods.css" rel="Stylesheet" />
    <link type="text/css" href="css/checkoutDialog.css" rel="Stylesheet" />
    <?php
    Compressor::echoInclude("love_campaign");
    Compressor::echoInclude("campaign");
    
    ?>
    <script type="text/javascript">
    //Color for checkboxes
    var checkboxBackground = '<?php echo($front->getCompany()->getReview_done_color()); ?>';

    </script>
</head> 

<body style="padding:0px;">
    <div id="wrapper">
        <div id="content" style="width: 100%;">
            <!-- Include tabs -->
            <?php include("view/campaign/tabContents.php"); ?>
        </div>
        
    </div>
    <?php include("view/campaign/checkoutDialog.php"); ?>
</body>
</html>