<?php
// include paypal and email functions
require_once('config.php');
include("paypal-functions.php");
include("cc_form.php");
include("class.session_handler.php");


$info_campaigns = urldecode($_SESSION['checkoutCampaign']['infoCampaigns']);
$ref_ids = $_SESSION['checkoutCampaign']['listCampaigns'];
$user_id = $_SESSION['checkoutCampaign']['user_id'];
$sub_amt = $_SESSION['checkoutCampaign']['totalBudgets'];   
$managerEmails = $_SESSION['checkoutCampaign']['managerEmails'];   

// Reset Sales information
if (isset($_SESSION['sales'])) {
    unset($_SESSION['sales']);
} 
$_SESSION['sales'] = array();


//init vars if in a request (if POST'd or GET'd)
$fname 	= (isset($_REQUEST['fname']) ? $_REQUEST['fname'] : ''); 
$lname = (isset($_REQUEST['lname']) ? $_REQUEST['lname'] : ''); 
$email = (isset($_REQUEST['email']) ? $_REQUEST['email'] : '');
$phone = (isset($_REQUEST['phone']) ? $_REQUEST['phone'] : '');
$company = (isset($_REQUEST['company']) ? $_REQUEST['company'] : ''); 
$street = (isset($_REQUEST['street']) ? $_REQUEST['street'] : '');
$city = (isset($_REQUEST['city']) ? $_REQUEST['city'] : '');
$state = (isset($_REQUEST['state']) ? $_REQUEST['state'] : '');
$zip = (isset($_REQUEST['zip']) ? $_REQUEST['zip'] : '');
$country = (isset($_REQUEST['country']) ? $_REQUEST['country'] : '');
$card_type = (isset($_REQUEST['card_type']) ? $_REQUEST['card_type'] : '');
$acct = (isset($_REQUEST['acct']) ? $_REQUEST['acct'] : '');
$cvv2 = (isset($_REQUEST['cvv2']) ? $_REQUEST['cvv2'] : '');
$exp_date = (isset($_REQUEST['exp_date']) ? $_REQUEST['exp_date'] : '');
$can_contact = (isset($_REQUEST['can_contact']) ? '1' : '');



if (substr($ref_ids,strlen($ref_ids-1),1) == ',') {
    $ref_ids = substr($ref_ids,0,strlen($ref_ids)-1);
}
// Add % of LM cut
$LMFee = 0.10; //5% for now... lets move to a constant instead
$cut = $sub_amt * $LMFee; 
$total = $sub_amt + $cut;

$_SESSION['sales']["sub_amt"] = $sub_amt;
$_SESSION['sales']["total"] = $total;
$_SESSION['sales']["ip"] = $_SERVER['REMOTE_ADDR'];
$_SESSION['sales']["domain"] = $domain;
$_SESSION['sales']["databaseName"] = $databaseName;
$_SESSION['sales']["ref_ids"] = $ref_ids;
$_SESSION['sales']["lm_description"] = urlencode($info_campaigns);
$_SESSION['sales']["lm_user_id"] = $user_id;
$_SESSION['sales']["lm_type"] = "C";

function isValidEmail($email){
    return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}


?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US"> 
 
<head profile="http://gmpg.org/xfn/11"> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
 
<title>lovemachine - the crowd–sourced review and bonus system</title> 
<link rel="shortcut icon" href="http://www.lovemachineinc.com/images/favicon.png" />
<style> 
<!-- <link rel="stylesheet" href="http://www.lovemachineinc.com/wp-content/themes/solutions/style.css" type="text/css" media="screen" />  -->
</style> 
<link rel="stylesheet" type="text/css" media="screen" href="css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="css/smoothness/lm.ui.css"/>
<link rel="stylesheet" href="css/sales.css" type="text/css" media="all" />
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.4.min.js"></script>
<script type="text/javascript" src="js/livevalidation_standalone.compressed.js"></script>
<script type="text/javascript" src="js/jquery.blockUI.js"></script>
<script type="text/javascript" src="js/sales.js"></script>


</head> 
<body>

<div style="display: none; position: fixed; top: 0px; left: 0px; width: 100%; height: 100%; text-align: center; line-height: 100%; background: white; opacity: 0.7; filter: alpha(opacity =   70); z-index: 9998"
     id="loader_img"><div id="loader_img_title"><img src="images/loading_big.gif"
     style="z-index: 9999"></div></div> 

    
        <div id="content" class="content">

            <div class="lovemachineInstanceArea">
                <div class='title'>Fund my Recognition Period!</div>
                <p><label for="domain">Domain: </label><?php echo $domain; ?></p>
                <p><label for="company">Campaign(s): </label><?php echo $info_campaigns; ?></p
                ><p><label for="amt">SubTotal: </label> $<?php echo $sub_amt; ?></p>
                <p><label for="amt">LM Fee: </label> $<?php echo $cut; ?>&nbsp;&nbsp;<a class="whatsthisTrigger" style=" font-size:smaller;color: rgb(201, 12, 12);">what's this?</a></p>
                <p><label for="amt">Total: </label> $<?php echo $total; ?><input type="hidden" name="amt" id="amt" value="<?php echo $total; ?>" /></p>
                <p style='display:none'><input type="checkbox" name="can_contact" value="1" />I am interested in other LoveMachine products.</p>
                <div class='postInformation'></div>
            </div>
            <div class='paypalFormArea'>
                <form id="buylove" method="post">
                        <?php cc_form($fname, $lname, $email, $phone, $street, $city, $state, $zip, $acct, $exp_date, $cvv2,$domain,$databaseName); ?>

                </form>
            </div>
             <?php include('dialogs/whatsthis-lmfee.inc'); ?>
       </div>
</body>
</html>
