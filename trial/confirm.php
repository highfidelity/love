<?php
//
//  Copyright (c) 2012, Below92 LLC.
//  All Rights Reserved. 
//  http://www.sendlove.us
//



require_once("interfaces/cupid.php");
require_once("interfaces/Functions.php");
require_once("modules/email.php");

// Check if we've got an action
if (!isset($_REQUEST['action'])) {
    die("No action set.");
}



switch($_REQUEST['action']) {
    case 'req_confirm':
		// Check all the necessary parameters are received
		if (!isset($_REQUEST['instance']) || !isset($_REQUEST['name']) || !isset($_REQUEST['email']) || !isset($_REQUEST['password']) || !isset($_REQUEST['source']) || !isset($_REQUEST['adword'])) {
		    die("Not all parameters passed");
		}
		if (isset($_REQUEST['name'])){
			preg_match("/[^a-z0-9A-Z]+/", $_REQUEST['name'], $check_matches, PREG_OFFSET_CAPTURE);
			if (count($check_matches)>=1){
			die("Invalid name");
			}
		}
        sendConfirmationEmail();
        break;

    case 'confirm':
        // Check all the necessary parameters are received
        if (!isset($_REQUEST['is']) || !isset($_REQUEST['n']) || !isset($_REQUEST['e']) || !isset($_REQUEST['w'])) {
            die("Invalid request");
        }
        // Create the Instance
        if (create()) {
            // Redirect to the welcome page
            header('Location: '.getServer().getAppLocation().'welcome.php?inst='.base64_decode($_REQUEST['is']));
        }
        break;
}


function sendConfirmationEmail() {
error_log("sendConfirmationEmail");
    // Store params
    $instance = base64_encode($_REQUEST['instance']);
    $name = base64_encode($_REQUEST['name']);
    $email = base64_encode($_REQUEST['email']);
    $password = base64_encode('{crypt}' . Functions::encryptPassword($_REQUEST['password']));
	  $source=base64_encode($_REQUEST['source']);
	  $adword=base64_encode($_REQUEST['adword']);
    
    $to = $_REQUEST['email'];
    $subject = 'Confirm account creation on SendLove';
    
    // Create a hard verification
    $salt = "aB1cD2eF3G_&$^%+";
    $proof = md5($email.$instance.$salt);
    
    $link = getServer().getAppLocation().'confirm.php?action=confirm&is='.$instance.'&n='.$name.'&e='.$email.'&w='.$password.'&p='.$proof.'&s='.$source."&a=".$adword;
    $msg = '<html><body>'.
           '<p>Thank you for trying SendLove</p>'.
           '<p>Before you can continue, please confirm your account by clicking the link below.<p>'.
           '<p><a href="'.$link.'" >Activate Account</a></p>'.
           '</body></html>';
    
    send_email($to, $subject, $msg);
}

function create() {
    $ret = false;

	// Check if it passes the encryption proof
	$is = $_REQUEST['is'];
	$e = $_REQUEST['e'];
	$p = $_REQUEST['p'];
	$salt = "aB1cD2eF3G_&$^%+";
	$proof = md5($e.$is.$salt);
	if ($proof != $p) {
	   die("Error: Illegal attempt.");
	}
	
    // Store params
	$instance = base64_decode($_REQUEST['is']);
	$name = base64_decode($_REQUEST['n']);
	$email = base64_decode($_REQUEST['e']);
	$password = ($_REQUEST['w']);
	$source = base64_decode($_REQUEST['s']);
	$adword = base64_decode($_REQUEST['a']);
	
	// Initialize cupid
	$cupid = new Cupid();
	$cupid->init();
	
	// Create Instance
	if ($cupid->createInstance($instance, $email, $name, $password,$source,$adword)) {
	    $ret = true;
	} else {
	    // There was an error
	    echo "Error: Instance creation unsuccessful";
	}
	
	// Close connection
	$cupid->disconnect();
	return $ret;
}

function getServer() {
    if (isset($_REQUEST['instance'])) {
        return $domain = 'https://'.$_REQUEST['instance'].'.sendlove.us/';
    } else {
        return $domain = 'https://'.base64_decode($_REQUEST['is']).'.sendlove.us/';
    }
}

function getAppLocation() {
    return 'trial/';
}

?>

