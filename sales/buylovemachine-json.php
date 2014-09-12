<?php

$headers = apache_request_headers(); 
$real_client_ip = $headers["X-Forwarded-For"];

    include("config.php");
    require_once("db_connect.php");
    require_once('autoload.php');
    include("paypal-functions.php");

    include("class.session_handler.php");

    if (!isset($_SESSION['sales'])) {
        echo json_encode(array("error" => "Not authorized!!"));
        return;
    }
    
    
    if(empty($_REQUEST['action'])){
        die(json_encode(array('error' => 'wrong action')));
    }

    // array of required arguments for each action (when needed)
    $requiredArgs = array(
                        'get_periods_list' =>  array('page','rows'),
                        );

    if(array_key_exists($_REQUEST['action'], $requiredArgs)){

        foreach($requiredArgs[$_REQUEST['action']] as $arg){

            if(!isset($_REQUEST[$arg])){
                echo json_encode(array('error' => 'args'));
                return;
            }
        };
    }

    switch($_REQUEST['action']){
    
    case 'send_payment_info':

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
        $pm = (isset($_REQUEST['pm']) ? $_REQUEST['pm'] : '');
        $can_contact = (isset($_REQUEST['can_contact']) ? '1' : '');
        
        // info coming from the session    
        $domain = $_SESSION['sales']["domain"];
        $databaseName = $_SESSION['sales']["databaseName"];
        $ref_ids = $_SESSION['sales']["ref_ids"];
        $total = $_SESSION['sales']["total"];
        $sub_amt = $_SESSION['sales']["sub_amt"];
        $lm_description = $_SESSION['sales']["lm_description"];
        $lm_user_id = $_SESSION['sales']["lm_user_id"];
        $lm_type = $_SESSION['sales']["lm_type"];
        $ip = $_SERVER['REMOTE_ADDR'];
        
		// send CC payment, then end if this is a jq/ajax call
	    $ccPayment = sendCCPayment($domain, $databaseName, $fname, $lname, $company, $ref_ids, $email, $phone, $street, $city, $state, $zip, $country, 
                        $card_type, $acct, $cvv2, $exp_date, $total, $sub_amt,$can_contact, $ip,$lm_type, $lm_description, $lm_user_id); 
	//        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') die($ccPayment);
        echo json_encode($ccPayment);
        break;
        
        
    }

