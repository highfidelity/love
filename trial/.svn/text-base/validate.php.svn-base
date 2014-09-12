<?php
//
//  Copyright (c) 2012, Below92 LLC.
//  All Rights Reserved. 
//  http://www.sendlove.us
//



require_once("interfaces/cupid.php");

require('Zend/Validate/Hostname.php');


if (!isset($_REQUEST['action'])) {
    die('Init: Error -> No action');
}


$action = $_REQUEST['action'];

switch ($action) {
    case 'instance':
        // If no data passed to validate just die
        if (!isset($_REQUEST['data'])) {
            die('Instance: Error -> No data');
        }
        $data = $_REQUEST['data'];
        validateInstance($data);
        break;
    
    case 'getstatus':
        // If no data passed to validate just die
        if (!isset($_REQUEST['data'])) {
            die('Instance: Error -> No data');
        }
        $data = $_REQUEST['data'];
        getStatus($data);
        break;

	case 'email':
	    // If no data passed to validate just die
	    if (!isset($_REQUEST['data'])) {
	        die('Email: Error -> No data');
	    }
	    $data = $_REQUEST['data'];
	    validateMail($data);
	    break;
        
    case 'isready':
        // If no data passed to validate just die
        if (!isset($_REQUEST['data'])) {
            die('Instance: Error -> No data');
        }
        $data = $_REQUEST['data'];
	//header('Content-type: application/json');
	header('Cache-Control: no-cache');
	header('Expires: Thu, 1, Apr 2010 00:00:00 GMT');
        echo isInstanceReady($data);
        break;
	    
	case 'debug':
	   debug();
	   break;
}

// Check that the instance is available
function validateInstance($instance) {
    if ($instance == 'www' || $instance == 'www.' || $instance == 'dev') {
        $ret = false;
        echo json_encode(array('exists'=>$ret));
        die();
    }
    
    // Check that the selected instance name is a valid standard host name
    $validate = new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_ALL);
    if (!$validate->isValid($instance)) {
        $ret = false;
        echo json_encode(array('exists'=>$ret));
        die();
    }
    
    $cupid = new Cupid();
    $cupid->init();
    $exists = $cupid->instanceExists($instance);
    
    if (!$exists) {
        $ret = true;
        echo json_encode(array('exists'=>$ret));
    } else {
        $ret = false;
        echo json_encode(array('exists'=>$ret));
    }
    
    $cupid->disconnect();
}

// Get Instance status
function getStatus($instance) {
    $cupid = new Cupid();
    $cupid->init();
    
    echo $cupid->getInstanceStatus($instance);
    
    $cupid->disconnect();
}

// Check that the email is available
function validateMail($email) {
    if (isValidEmail($email)) {
        $ret = true;
        echo json_encode(array('valid'=>$ret));
    } else {
        $ret = false;
        echo json_encode(array('valid'=>$ret));
    }
}

function isValidEmail($email){
    return eregi("^[_a-z0-9\+\-]+(\.[_a-z0-9\+\-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}

function isInstanceReady($instance) {
    // Get Status
    $cupid = new Cupid();
    
    $cupid->init();    
    $inst_status = $cupid->getInstanceStatus($instance);
    
    $cupid->disconnect();
    
    // If the instance is Ready
    //   -> Go to the Instance domain and begin
    // If the instance is not created
    //   -> Go to Trial
    // If the instance is being set up
    //   -> Continue with welcome
    $ret = '';
    switch ($inst_status) {
        case 'INSTANCE_LIVE':
            // Do the redirect thing
            $ret = json_encode(array('ready'=>true));
            break;
            
        case 'INSTANCE_NEW':
            $ret = json_encode(array('ready'=>false, 'status'=>'pending'));
            break;
            
        case 'INSTANCE_MAINTENANCE':
            $ret = json_encode(array('ready'=>false, 'status'=>'down'));
            break;
        
        case 'NULL':
            // Do the redirect thing
            $ret = json_encode(array('ready'=>true));
            break;
    }
    
    return $ret;
}

function debug() {
    $cupid = new Cupid();
    $cupid->debug();
}

?>
