<?php 
$domain = $_SERVER['HTTP_HOST'];
$tenant = substr($domain, 0, strpos($domain, '.'));
require_once('config.php');
?>

<title>Your Trial Has Expired</title>

<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.min.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>livevalidation.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.blockUI.js"></script> 
<script type="text/javascript">var thisTenant = "<?php echo $tenant; ?>";</script>
<script type="text/javascript" src="js/disabled.js"></script>

<link href="css/jquery-ui.css" rel="stylesheet" type="text/css">
<link href="css/disabled.css" rel="stylesheet" type="text/css">

<div class="content">
    <h1><img id="smallLogo" src="images/SendLoveLogoSmall.png" alt="SendLove" /></h1>
        <div class="disabledTitleDiv">
            <h2>Your Trial Has Expired</h2>
        </div>
    </h1>
    <body>
        <div class="disabledText">
            <p>Your trial has expired. We hope you would like to continue.</p>
            <p> If you would like to re-activate SendLove, click here to <a href="javascript" id="ping_contact">contact us</a></p>
        </div>
    </body>
</div>
