<?php
//
//  Copyright (c) 2012, Below92 LLC.
//  All Rights Reserved. 
//  http://www.sendlove.us
//

require_once("interfaces/cupid.php");

// If no instance name is provided, redirect to Trial
if (!isset($_REQUEST['inst'])) {
    // Do the redirect thing
    header('Location: http://trial.sendlove.us/trial/');
}

function getInstanceName() {
    $inst_domain = '';
    if (!empty($_REQUEST['inst'])) {
        $inst_domain = $_REQUEST['inst'];
    } else {
        $fulldomain = explode('.', $_SERVER['HTTP_HOST']);
        $inst_domain = $fulldomain[0];
    }
    return $inst_domain;
}

function getServer() {
    return 'https://'.getInstanceName().'.sendlove.us/';
}

function getAppLocation() {
    return 'trial/';
}
?> 
<!-- Header -->
<?php include("views/head.php"); ?>
<input  type=hidden id="instance-name">
<link rel="stylesheet" href="css/welcome.css" type="text/css" media="screen" />

	<div id="welcome-content">   
		<h1>Welcome to SendLove</h1>
		
		<h3>An instance of your company is being set up in our systems.</h3>
	    <div id="status-text">
	        <h3>This may take a couple of minutes.</h3>
	    </div>
	    <div id="status-update">
	        <span id="waitIndicator">Configuring...</span>
	    </div>
		
		<div style="clear:both;"></div>
		<br/>
		<h3>Once your instance is ready you will be asked to login with the password that you provided.</h3><br/>
		<h3>Getting co-workers to send praise messages to each other is what the SendLove is all about so the first<br/> thing you will want to do when you login is start adding co-workers to the system.</h3>
		<br/>		
		<h3>This is done by clicking the "Admin" link at the top. Once in the Admin control panel you can add co-workers<br/> individually or upload a bunch of them using a CSV file.<br/></h3>
		<br/>
		<p><img src="images/user_settings.jpg" width=90% height=30%></p><br/><br/>
		<h3>You can also upload your company logo and tweak the colors of your SendLove so it matches your company's<br/> aesthetic. </h3><br/>         
		<h3><img src="images/company_settings.jpg" width=100% height=50%></h3>
	</div>

<!-- Footer -->
<?php include("views/footer.php"); ?>
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1010065022;
var google_conversion_language = "ar";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "yMzzCMqY8wEQ_rzR4QM";
var google_conversion_value = 0;
if (0) {
  google_conversion_value = 0;
}
/* ]]> */
</script>
<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="https://www.googleadservices.com/pagead/conversion/1010065022/?value=0&amp;label=yMzzCMqY8wEQ_rzR4QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

</body>
<!-- Include JavaScript -->
<script type='text/javascript'>
    $('#imageZoom').remove();
    $('#imageViewer').remove();

    $('#logo-link').attr('href','index.php');
    var interval;
    
    $(document).ready(function() {
        // Every 4s check if the instance is ready
        interval = setInterval('isInstanceReady()', 4000);
    });
    
    // Checks that the instance passed as argument to the
    // page is ready
    function isInstanceReady() {
        var server_name = '<?php echo getServer(); ?>';
        var app_location = '<?php echo getAppLocation(); ?>';
        var inst = '<?php echo getInstanceName(); ?>';
        
        $.getJSON('validate.php?action=isready&data='+inst, function(json) {
            if (json.ready == true) {
                // Show Redirect to instance link
                var link = $('<a href="' + server_name + '" id="readyIndicator">Go to your Instance</a>');

                $('#status-update').empty().append(link);
                clearInterval(interval);
            } else {
                if (json.ready == false && status == 'down') {
	                // Show Redirect to maintenance link
	                var link = $('<a href="'+server_name+'/love/maintenance.html" id="errorIndicator">Something wen\'t wrong, go to maintenance page</a>');
	                $('#status-update').empty().append(link);
	                clearInterval(interval);
                }
            }
        });
    }
</script>
</html>
