<?php
//  vim:ts=4:et

//  Copyright (c) 2010, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com


/*******************************************************
    Page: paypal-functions.php
    Features:  Paypal Functions.  
        PPHttpPost: NVP post function for masspay.
    Author: Jason (jkofoed@gmail.com)
    Date: 2010-04-01 [Happy April Fool's!]

********************************************************/

/**
This file should be redesigned and moved into the class directory: a payment class could be created
The class methods will be called by buylovemachine-json.php script
**/

    include("send_email.php");
    require_once('class/CURLHandler.php');

    function PPHttpPost($methodName_, $nvpStr_) {
        $environment = 'sandbox'; // 'sandbox' or 'beta-sandbox' or 'live'
	if (get_cfg_var('paypal_environment') && get_cfg_var('paypal_environment')=='live') { $environment='live'; }
         
        $version = urlencode('51.0');

        //use sandbox credentials if sandbox
        if("sandbox" === $environment || "beta-sandbox" === $environment) {
            $pp_user = 'canad1_1284827859_biz_api1.gmail.com';
            $pp_pass = '1284827872';
            $pp_signature = 'AFcWxV21C7fd0v3bYYYRCpSSRl31ApuZpqe0mo8MA-47K-o94W3pMyWC';
            $API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
        } else {
            $paypal_api = parse_ini_file(get_cfg_var("paypal_conf"), true);
            if ($paypal_api == null) {
                return array('ACK' => "API",
                            'errorMsg' => "Paypal api configuration issue, conf file is missing (paypal_conf: *".get_cfg_var("paypal_conf")."*)!");
            }
            if ( !isset($paypal_api["paypal"]) ) {
                return array('ACK' => "API",
                            'errorMsg' => "Paypal api configuration is missing (paypal array)!");
            }
            if ( !isset($paypal_api["paypal"]["API_Username"]) ) {
                return array('ACK' => "API",
                            'errorMsg' => "Paypal api configuration is missing (username)!");
            }
            if ( !isset($paypal_api["paypal"]["API_Password"]) ) {
                return array('ACK' => "API",
                            'errorMsg' => "Paypal api configuration is missing (password)!");
            }
            if ( !isset($paypal_api["paypal"]["API_Signature"]) ) {
                return array('ACK' => "API",
                            'errorMsg' => "Paypal api configuration is missing (API signature)!");
            }
            $pp_user = urlencode($paypal_api["paypal"]["API_Username"]);
            $pp_pass = urlencode($paypal_api["paypal"]["API_Password"]);
            $pp_signature = urlencode($paypal_api["paypal"]["API_Signature"]);  
            $API_Endpoint = "https://api-3t.paypal.com/nvp";
        }
        
        error_log('PP User: '.$pp_user);        

        // Set the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        // Set the API operation, version, and API signature in the request.
        $nvpreq = 'METHOD='.$methodName_.'&VERSION='.$version.'&PWD='.$pp_pass.'&USER='.$pp_user.'&SIGNATURE='.$pp_signature.''.$nvpStr_;

        // Set the request as a POST FIELD for curl.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        // Get response from the server.
        $httpResponse = curl_exec($ch);
error_log("paypa;2: ".json_encode($httpResponse));

        if(!$httpResponse) {
            exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
        }

        // Extract the response details.
        $httpResponseAr = explode("&", $httpResponse);
        $httpParsedResponseAr = array();
        foreach ($httpResponseAr as $i => $value) {
            $tmpAr = explode("=", $value);
            if(sizeof($tmpAr) > 1) {
                $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
            }
        }
        $httpParsedResponseAr["nvpEndpoint"] = $API_Endpoint;
        $httpParsedResponseAr["nvpString"] = $nvpreq;
        if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
            exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
        }

        return $httpParsedResponseAr;
    }
    
    function sendCCPayment($domain, $databaseName, $fname, $lname, $company, $ref_ids, $email, $phone, $street, $city, $state, $zip, $country, 
                                   $card_type, $card_number, $cvv, $exp_date, $total,$sub_amt, $can_contact, $ip, $lm_type, $lm_description, $lm_user_id) {
        //collect confirmed payees and run paypal transaction
        // Set request-specific fields.
        $currency = 'USD';       // or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')

        //build nvp string
        
        $nvp  = '';
        $nvp .= '&PAYMENTACTION=Sale';
        $nvp .= '&IPADDRESS='.urlencode($ip);
        $nvp .= '&AMT='.urlencode($total);
        $nvp .= '&CURRENCYCODE='.urlencode($currency);
        $nvp .= '&CREDITCARDTYPE='.urlencode($card_type);
        $nvp .= '&ACCT='.urlencode($card_number);
        $nvp .= '&EXPDATE='.urlencode($exp_date);
        $nvp .= '&CVV2='.urlencode($cvv);
        $nvp .= '&FIRSTNAME='.urlencode($fname);
        $nvp .= '&LASTNAME='.urlencode($lname);
        $nvp .= '&STREET='.urlencode($street);
        $nvp .= '&CITY='.urlencode($city);
        $nvp .= '&STATE='.urlencode($state);
        $nvp .= '&ZIP='.urlencode($zip);
        $nvp .= '&COUNTRYCODE='.urlencode($country);

        // store customer data if required, return customer_id for payment

        $cust = storeCustomerData($domain, $fname, $lname, $company, $ref_ids, $email, $phone, $street, $city, $state, $zip, $country, $can_contact);
        if ( isset($cust["error"]) ) {
            return array ('error' => "Buyer",
                        'errorMsg' => "Error in Buyer Creation: ".$cust["error"]);
        } else {
            $cust = $cust['customer_id'];
        }
        $buyer = storeBuyerData($cust, $fname, $lname, $company, $email, $phone, $street, $city, $state, $zip, $country);
        if ( isset($ret["error"]) ) {
            return array ('error' => "Buyer",
                        'errorMsg' => "Error in Buyer Creation: ".$ret["error"]);
        }
        $buyer_id = $buyer["buyer_id"];

        //  $instance = getInstanceNameFromDomain($domain);
        $instance = $databaseName;
        if ($instance == "") {
            return array ('error' => "Invalid domain",
                        'errorMsg' => "Invalid domain: ".$domain);
        }
        $ret = changeCampaignStatus($ref_ids,"R",$instance);
        if ( isset($ret["error"]) ) {
            return array ('error' => "Recognition",
                        'errorMsg' => "Error in Recognition Period Update (R): ".$ret["error"]);
        }
         // Execute the API operation; see the PPHttpPost function in the paypal-functions.php file.
        $PPResponseAr = PPHttpPost('DoDirectPayment', $nvp);
        $warningMsg="";
        if ( isset($PPResponseAr["ACK"]) ) {
            $transactionID = "";
            if (isset($PPResponseAr["TRANSACTIONID"]) ) {
                $transactionID = $PPResponseAr["TRANSACTIONID"];
            }
            $longMessage = "";
            if (isset($PPResponseAr["L_LONGMESSAGE0"]) ) {
                $longMessage = urldecode($PPResponseAr["L_LONGMESSAGE0"]);
            }
            $save = storePaymentData($cust, $card_type, $PPResponseAr["ACK"], $total, $sub_amt, $transactionID, $ip, 
                        $lm_type,$domain,$ref_ids, $lm_description, $lm_user_id,$buyer_id,$longMessage);
            if ( isset($save['error']) ) {
                $payment = -1;
                $warningMsg .= '<p>Warning: ' . $save['error'] . "</p>";
            } else {
                $payment = $save['payment_id'];
            }
        }
        
        if( isset($PPResponseAr["ACK"]) && 
            ("SUCCESS" == strtoupper($PPResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($PPResponseAr["ACK"]))
            ) {
            $ret = changeCampaignStatus($ref_ids,"Y",$instance);
            if ( isset($ret["error"]) ) {
                $warningMsg .= "<p>Warning: Error in Recognition Period Update (Y), ".$ret["error"] . "</p>";
            }
   
            $invoice = 1000000 + intval($payment);
            $invoice = $cust.''.$invoice; 

        //payment posted
        /**
         *   $PPResponseAr["TIMESTAMP"] 
         *   $PPResponseAr["CORRELATIONID"]
         *   $PPResponseAr["ACK"]
         *   $PPResponseAr["VERSION"] 
         *   $PPResponseAr["BUILD"]
         *   $PPResponseAr["AMT"]
         *   $PPResponseAr["CURRENCYCODE"] 
         *   $PPResponseAr["AVSCODE"] //X 
         *   $PPResponseAr["CVV2MATCH"]  //M
         *   $PPResponseAr["TRANSACTIONID"]
         */

            $name = $fname.' '.$lname;
                        
            success_notify($name, $email, $company, $street, $city, $state, $zip, $country, $phone, $lm_description, $total, $invoice, $PPResponseAr["TRANSACTIONID"]); 
            
            $messJournal = "A payment has been made for a Recognition Period!";
            if ( "SUCCESSWITHWARNING" == strtoupper($PPResponseAr["ACK"]) ) {
                $messJournal .= " Warning in Paypal transaction.";
            }
            // Make a notice in journal
            $data = array ('user' => JOURNAL_API_USER,
                    'pwd' => sha1(JOURNAL_API_PWD),
                    'message' => $messJournal);
            ob_start();
            $res = CURLHandler::Post(JOURNAL_API_URL, $data);
            ob_end_clean();
            $ret = array ('success' => strtoupper($PPResponseAr["ACK"]),
                            'transactionID' => $PPResponseAr["TRANSACTIONID"],
                            'warning' => $warningMsg
            );

        } else  {            
        // We are not in an asynchronous process so put back the campaign in card
            $ret = changeCampaignStatus($ref_ids,"C",$instance);
            if ( isset($ret["error"]) ) {
                $warningMsg .= "<p>Warning: Error in Recognition Period Update (N), ".$ret["error"] . "</p>";
            }
            if ( isset($PPResponseAr["L_SEVERITYCODE0"]) ) {
                $errorMsg = $PPResponseAr["ACK"] . ", " . $PPResponseAr["L_SEVERITYCODE0"] . ': ' . urldecode($PPResponseAr["L_SHORTMESSAGE0"]) .
                                        ' (' . $PPResponseAr["L_ERRORCODE0"] . ') - ' . urldecode($PPResponseAr["L_LONGMESSAGE0"]) . $warningMsg;
            } else {
                $errorMsg =  $PPResponseAr["ACK"] . ", " .$PPResponseAr["errorMsg"] . $warningMsg;
            }
 
            fail_notify($fname.' '.$lname, $email, $company, $street, $city, $state, $zip, $country, $phone, $lm_description, $total, $errorMsg); 

            if ( isset($PPResponseAr["L_SEVERITYCODE0"]) ) {
                $ret = array ('error' => $PPResponseAr["L_ERRORCODE0"],
                            'errorMsg' => $errorMsg
                );
            } else {
                $ret = array ('error' => $PPResponseAr["ACK"],
                            'errorMsg' => $errorMsg);
            }      
        }

        return $ret;
    } 
    
    function getCustomerData($field, $term) {
        
    }

    /**
    The design of the following function is not good.
    1- When the function is called, the domain is always already in the table.
       So the information are never saved in the table.
    2- The function is called when there is a transaction, but at this time the information sent are not the customer information
       but it's the buyer information. Buyer and Customer can be different. The customer is the owner of the instance.
       The buyers are the managers of the Customer company. In small company, Buyer = Customer.
    3- there is no test after SQL process in case of error
    **/
    function storeCustomerData($domain, $fname, $lname, $company, $employee_count, $email, $phone, $street, $city, $state, $zip, $country, $can_contact) {
        
        $customeridSQL = "SELECT id FROM " . CUSTOMERS . " WHERE domain = '".$domain."'";
        $customer_id_query = mysql_query($customeridSQL) or error_log("sCD: $customeridSQL ".mysql_error());

        if (mysql_num_rows($customer_id_query) == 0) {
            $custSQL = "INSERT INTO " . CUSTOMERS . " (domain, contact_first_name, contact_last_name, company_name, employee_count, contact_email, contact_phone, address_street, address_city, address_state, address_zip, address_country, can_contact, created) VALUES (";
            $custSQL .= "'".mysql_real_escape_string($domain)."',";
            $custSQL .= "'".mysql_real_escape_string($fname)."',";
            $custSQL .= "'".mysql_real_escape_string($lname)."',";
            $custSQL .= "'".mysql_real_escape_string($company)."',";
            $custSQL .= "'".mysql_real_escape_string($employee_count)."',";
            $custSQL .= "'".mysql_real_escape_string($email)."',";
            $custSQL .= "'".mysql_real_escape_string($phone)."',";
            $custSQL .= "'".mysql_real_escape_string($street)."',";
            $custSQL .= "'".mysql_real_escape_string($city)."',";
            $custSQL .= "'".mysql_real_escape_string($state)."',";
            $custSQL .= "'".mysql_real_escape_string($zip)."',";
            $custSQL .= "'".mysql_real_escape_string($country)."',";
            $custSQL .= "'".mysql_real_escape_string($can_contact)."',";
            $custSQL .= "'".date('Y-m-d H:i:s')."')";
            $customer_query = mysql_query($custSQL) or error_log("sCD2: $custSQL ".mysql_error());


            if ($customer_query) {
                return  array('result' => "new customer created",
                                "customer_id" => mysql_insert_id()) ;
            } else {
                return  array('error' => 'error SQL in storeCustomerData' . mysql_error () ) ;
            }
            //$customer_id = mysql_insert_id();
        } else {
            $customer_result = mysql_fetch_array($customer_id_query);
            if ($customer_result) {
                return  array('result' => "customer found",
                                "customer_id" => $customer_result[0]) ;
            } else {
                return  array('error' => 'error SQL in storeCustomerData' . mysql_error () ) ;
            }
           // $customer_id = $customer_result[0];
        }

        return $customer_id;
    }

    function storeBuyerData($customer_id, $fname, $lname, $company, $email, $phone, $street, $city, $state, $zip, $country) {
        
            $custSQL = "INSERT INTO " . BUYERS . 
                " ( customer_id,contact_first_name, contact_last_name, company_name,  contact_email, contact_phone, ".
                "address_street, address_city, address_state, address_zip, address_country) VALUES (";
            $custSQL .= "'".mysql_real_escape_string($customer_id)."',";
            $custSQL .= "'".mysql_real_escape_string($fname)."',";
            $custSQL .= "'".mysql_real_escape_string($lname)."',";
            $custSQL .= "'".mysql_real_escape_string($company)."',";
            $custSQL .= "'".mysql_real_escape_string($email)."',";
            $custSQL .= "'".mysql_real_escape_string($phone)."',";
            $custSQL .= "'".mysql_real_escape_string($street)."',";
            $custSQL .= "'".mysql_real_escape_string($city)."',";
            $custSQL .= "'".mysql_real_escape_string($state)."',";
            $custSQL .= "'".mysql_real_escape_string($zip)."',";
            $custSQL .= "'".mysql_real_escape_string($country)."'";
            $custSQL .= ")";
            $ret = mysql_unbuffered_query($custSQL);
            if ($ret) {
                return  array("buyer_id" => mysql_insert_id(),"sql" => $custSQL) ;
            } else {
                return  array('error' => 'error SQL in storeBuyerData' . mysql_error () ."*". $custSQL) ;
            }
 
        return $customer_id;
    }
    
    function storePaymentData($customer_id, $card_type, $status, $total, $sub_amt, $txn_id, $ip,$lm_type,$domain,$ref_ids, $lm_description, $lm_user_id,$buyer_id,$comment) {

        $payment_sql = "INSERT INTO " . PAYMENTS . 
                        " (customer_id, payment_method, payment_status, payment_amount, payment_amount_no_fee, payment_date, paypal_transaction_id,  ip_address,".
                        " lm_type, lm_user_id, buyer_id, lm_linked_ids, lm_description, payment_comment, lm_domain".
                        ") VALUES (";
        $payment_sql .= "'".mysql_real_escape_string($customer_id)."',";
        $payment_sql .= "'".mysql_real_escape_string($card_type)."',";
        $payment_sql .= "'".mysql_real_escape_string($status)."',";
        $payment_sql .= "'".mysql_real_escape_string($total)."',";
        $payment_sql .= "'".mysql_real_escape_string($sub_amt)."',";
        $payment_sql .= "'".date('Y-m-d H:i:s')."',";
        $payment_sql .= "'".mysql_real_escape_string($txn_id)."',";
        $payment_sql .= "'".mysql_real_escape_string($ip)."',";
        $payment_sql .= "'".mysql_real_escape_string($lm_type)."',";
        $payment_sql .= "".mysql_real_escape_string($lm_user_id).",";
        $payment_sql .= "".mysql_real_escape_string($buyer_id).",";
        $payment_sql .= "'".mysql_real_escape_string($ref_ids)."',";
        $payment_sql .= "'".mysql_real_escape_string($lm_description)."',";
        $payment_sql .= "'".mysql_real_escape_string($comment)."',";
        $payment_sql .= "'".mysql_real_escape_string($domain)
        ."')";

        $ret = mysql_unbuffered_query($payment_sql);
        if ($ret) {
            $ret = array ('payment_id' => mysql_insert_id()); 
        } else {
            $ret = array ('error' => 'error SQL in storePaymentData ' . mysql_error () . " SQL: " . $payment_sql); 
        }

        return $ret;
    }

    function updatePaymentStatus($payment_id, $status, $status_reason) {

        $payment_sql = "UPDATE " . PAYMENTS . " SET payment_status='".$status."', payment_status_reason = '".$status_reason."' WHERE id='".$payment_id."'";
        $payment_update = mysql_query($payment_sql);
    }
    
    function updatePaymentAttribute($payment_id, $attribute, $value) {

        $payment_sql = "UPDATE " . PAYMENTS . " SET ".$attribute."='".$value."' WHERE id='".$payment_id."'";
        $payment_update = mysql_query($payment_sql);
    }
        
    function updateCustomerAttribute($customer_id, $attribute, $value) {

        $cust_sql = "UPDATE " . CUSTOMERS. " SET ".$attribute."='".$value."' WHERE id='".$customer_id."'";
        $cust_update = mysql_query($cust_sql);
    }
    
    function getInstanceNameFromDomain($domain) {
	//Garth - database names have size restrictions that makes domain name an unreliable indicator of the name, use DB_NAME if defined
	if (defined('DB_NAME')) {return DB_NAME;}
        $instance = "";
        $pos = strpos($domain,".");
        if ( $pos !== false ) {
            $instance = "LM_" .substr($domain,0,$pos);
        }
        return $instance;
    }
    
     /***
    Campaign transaction status is the field budget_validated of table PERIODS
        N -> budget not validated
        C -> the manager put this budget in the cart in order to fund it
        R -> the Sales application starts the financial transaction to fund this budget
        Y -> the Sales application has accepted the payment from the manager, the campaign is funded
    
    ***/
    function changeCampaignStatus($idsList,$new_validated_status,$instance){
        $filter="";
        $periodFilter=PERIODS . ".`id` in ($idsList) ";
        if ( $new_validated_status == 'C' ) {  
            $infoForMail = "Campaigns are in the paypal card";
            $filter = " AND  budget_validated = 'R'  ";         // set to No  if the previous status was Request (cancel)
        } else if ( $new_validated_status == 'R' ) {   // set to Request Running if the previous status was In Cart
            $infoForMail = "Paypal payment request has been sent, waiting for acknowledgement from Paypal.";
            $filter = " AND budget_validated = 'C' ";
        } else if ( $new_validated_status == 'Y' ) {   // set to Yes if the previous status was Request (accepted)
            $infoForMail = "Paypal payment accepted.";
            $filter = " AND budget_validated = 'R' ";
        } else {
            $filter = " AND 1 = 0 ";
        }

        $sql = "UPDATE $instance." . PERIODS . ",$instance." . USER_REVIEWS ." SET `budget_validated` = '$new_validated_status' WHERE " .
                    USER_REVIEWS .".period_id = " . PERIODS . ".id AND $periodFilter $filter";

        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            sendTemplateEmail($_SESSION['checkoutCampaign']['managerEmails'], 'changeInCampaign', array(
                'changeInfo' => "New paypal status is : ".$new_validated_status ." , " . $infoForMail,
                'periodInfo' => urldecode($_SESSION['checkoutCampaign']['infoCampaigns'])
            ));	
            return  array('result' => "update new_validated_status",
                            "count" => mysql_affected_rows()) ;
        } else {
            return  array('error' => 'error SQL in changeCampaignStatus' . mysql_error () ) ;
        }
    }
 /*      
    if(isset($_GET['action'])) {
        if ($_GET['action'] == 'initPPInfo') {
            $cID = storeCustomerData($_POST['domain'], $_POST['fname'], $_POST['lname'], $_POST['company'], $_POST['user_count'], $_POST['email'], $_POST['phone'], $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['country'], $_POST['can_contact']);
            $pID = storePaymentData($cID,'paypal', 'Pending', $_POST['amt'], '', $_POST['ip']);
            echo $pID;
        }

        if ($_GET['action'] == 'compPPTxn') {
            $status_reason = $_POST['status']=='Pending' ? $_POST['pending_reason'] : '';
            updatePaymentStatus(parseInt($_POST['invoice']), $_POST['status'], $status_reason);
            if ($_POST['payment_status']=='Completed') {
                $cust_sql = "SELECT * FROM ".PAYMENTS." WHERE id = '".parseInt($_POST['invoice'])."'";
                $cust = mysql_fetch_array(mysql_query($cust_sql));
                $update_cust_sql = "UPDATE ".CUSTOMERS." SET employee_count = '".parseInt($_POST['quantity'])."', months_purchased = months_purchased + 1 WHERE id = '".$cust['id']."'";
                $update_cust = mysql_query($update_cust_sql);
                $update_payments_sql = "UPDATE " . PAYMENTS . " SET payment_amount = '".$_POST['payment_gross']."', paypal_transaction_id = '".$_POST['txn_id']."', payment_date = '".$_POST['payment_date']."', payment_method = '".$_POST['payment_type']."' where id = '".parseInt($_POST['invoice'])."'";   
                $update_payments = mysql_query($update_payments_sql);
                
                success_notify($name, $email, $_POST['company_name'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'], $_POST['contact_phone'], $_POST['quantity'], $_POST['payment_gross'], $_POST['invoice']); 
            } else {
                $pp_admin_message = "Error: PayPal Failed<br />";
                $pp_admin_message .= print_r(POST);
                fail_notify($pp_admin_message);
            }
                             
        }
        
    }*/
   
    
    
?>
