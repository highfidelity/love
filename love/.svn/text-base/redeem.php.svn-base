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

    <link rel="Stylesheet" type="text/css" href="css/tofor.css" />
    <link rel="Stylesheet" type="text/css" href="css/periods.css" />
    
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery-ui.min.js"></script>
    
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.blockUI.js"></script>

    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>i18n/grid.locale-en.js"></script>
    <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.jqGrid.min.js"></script>
    
    <?php
    	$files = array(
    		'periods.js',
    		'campaign.js',
    		'love_redeem.js'
    	);
    	
    	$compressor = new Compressor();
    	$compressor->setCompressorType('js')
    			   ->setPath(APP_PATH . '/js')
    			   ->setFiles($files)
    			   ->setFilename('redeem');
    	$combinedJs = $compressor->compile();
    ?>
    <!--<script type="text/javascript" src="js/rowedex3.js"> </script> 
    <script type="text/javascript" src="js/periods.js"> </script> 
    
 
    <script type="text/javascript" src="js/campaign.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/love_redeem.js" charset="utf-8"></script>-->
    
    <script type="text/javascript">
    //Color for checkboxes
    var checkboxBackground = '<?php echo($front->getCompany()->getReview_done_color()); ?>';
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
    var campaignConfig = function($)
    {
        // Define here all the private variables
<?php 
    if ( isset($concierge_transaction_id) ) {
        echo 'var with_concierge_transaction_id = true;' ; 
    } else {
        echo 'var with_concierge_transaction_id = false;' ; 
    }
?>
        return {
            getWithConciergeTransactionId : function() { return with_concierge_transaction_id;}
        };
    }(jQuery); // end of object campaignConfig
    
    </script>
    
    <script type="text/javascript">
        var uservoiceOptions = {
          /* required */
          key: 'lovemachine',
          host: 'lovemachine.uservoice.com', 
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
    <div id="wrapper">
        <!-- Include header -->
        <?php include("view/campaign/header.php"); ?>
      
        <div id="content" style="clear:both;">
            <!-- Include tabs -->
            <?php include("view/redeem/tabContents.php"); ?>
        </div>
            <!-- Include footer -->
            <?php include("view/campaign/footer.php"); ?>
        
        <p class="lm"><a href="http://www.lovemachineinc.com" target="_blank"><img src="images/LMLogo3.png"  border="0"/></a></p>
    	<?php include('dialogs/splashscreen.inc'); ?>
    	
        <div id="user-love-popup"></div>
        <div id="tooltip" class="tooltip">
            <img src="img/tooltip/spinner.gif" border="0" alt="Loadin" />
        </div>
    </div>
</body>
</html>
