<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

#ini_set('display_errors', 1);error_reporting(-1);var_dump('Test');
require 'config.php';
#require 'functions.php';
#require 'send_email.php';
#require('class/Session.class.php');
#require_once('class/Utils.class.php');
require_once('class/CURLHandler.php');

// Constants: Keep in sync with .../journal/love.bot.class.php
define ('SL_OK', 'ok');
define ('SL_ERROR', 'error');
define ('SL_WARNING', 'warning');
define ('SL_NO_ERROR', '');
define ('SL_NO_RESPONSE', 'no response');
define ('SL_BAD_CALL', 'bad call');
define ('SL_DB_FAILURE', 'db failure');
define ('SL_UNKNOWN_USER', 'unknown user');
define ('SL_NOT_COWORKER', 'receiver not co-worker');
define ('SL_RATE_LIMIT', 'rate limit');
define ('SL_SEND_FAILED', 'send failed');
define ('SL_JOURNAL_FAILED', 'journal failed');
define ('SL_NO_SSL', 'no ssl call');
define ('SL_WRONG_KEY', 'wrong api key');

/**
 * respond -- Send an array, encoded as JSON, back to the caller and exit.
 * @param $val The array to send.
 * @return It doesn't. It exits.
 */
function respond ($val) {
    exit (json_encode ($val));
}

$rsp = array ('status' => SL_ERROR,       // SL_OK, SL_WARNING or SL_ERROR
	          'error'  => SL_NO_RESPONSE  // error type
	         );

// Check that we have a secure line
if (empty($_SERVER['HTTPS'])) {
  $rsp['error'] = SL_NO_SSL;
  respond ($rsp);
}

error_log("saleApi: ".$_REQUEST['api_key']." ".API_KEY);
if ( (!isset($_REQUEST['uuid']) && !isset($_REQUEST['api_key']) ) ||
    (isset($_REQUEST['api_key']) && $_REQUEST['api_key'] != API_KEY) ) {
    $rsp['error'] = SL_WRONG_KEY;
    respond($rsp);
}

if(!empty($_REQUEST['action'])){

    // Connect to db
    $db = @mysql_connect (DB_SERVER, DB_USER, DB_PASSWORD);
    if (!$db || !@mysql_select_db (DB_NAME, $db)) {
	    $rsp['error'] = SL_DB_FAILURE;
	    $rsp['info'] = mysql_error();
	    respond ($rsp);
    }
error_log("salesApi: action ".$_REQUEST['action']);
$_silent = isset($_REQUEST['silent']) && $_REQUEST['silent'] == 'true' ? true : false;

    switch($_REQUEST['action']){
        case 'getCustomerList':
            return getCustomerList();
        case 'getPaymentList':
            return getPaymentList();
        case 'getCustomerNames':
            return getCustomerNames();
        case 'getCustomer':
            return getCustomer();
        case 'getPaymentDetails':
            return getPaymentDetails();
        case 'getPaymentHistory':
            return getPaymentHistory();
        case 'getLastPayment':
            return getLastPayment();
        case 'newInstance':
            return newInstance($_silent);
        case 'updateInstanceData':
            return updateInstanceData();
        default:
            break;
	}

    // Respond positively
    $rsp['status'] = SL_OK;
    $rsp['error'] = SL_NO_ERROR;
    respond ($rsp);

}else{
	$rsp['error'] = SL_BAD_CALL;
	respond ($rsp);
}


/*
* Setting session variables for the user so he is logged in
*
*/
function loginUserIntoSession(){
//gj([X.X.X] - sanitize input, register patterns globally for readability and consistency. email, adword, instance, etc)
    $user_id = intval($_REQUEST['user_id']);
    $username = preg_replace("/[^a-zA-Z0-9\+\.\_\@\-]/","",$_REQUEST['username']);
    $nickname = preg_replace("/[^a-zA-Z0-9\.\_\-\ ]/","",$_REQUEST['nickname']);
    $admin = preg_replace("/[^a-zA-Z0-9\-\_]/","",$_REQUEST['admin']);

    $session_id = preg_replace("/[^a-zA-Z0-9\-\,]/","",$_REQUEST['session_id']);
    session_id($session_id);
    session::init();
    Utils::setUserSession($user_id, $username, $nickname, $admin);
}

function getCustomerList()
{
//gj([X.X.X] - sanitize input, register patterns globally for readability and consistency. email, adword, instance, etc)
    $page = intval($_REQUEST['page']);
    $ordering = preg_replace("/[^a-zA-Z0-9\-\_]/","",$_REQUEST['ordering']);
    $sort = preg_replace("/[^a-zA-Z0-9\-\_]/","",$_REQUEST['sort']);
    $customer = intval($_REQUEST['customer']);
    $date_from = empty($_REQUEST['date_from']) ? '' : strftime("%Y-%m-%d" ,strtotime($_REQUEST['date_from']));
    $date_to = empty($_REQUEST['date_to']) ? '' : strftime("%Y-%m-%d" ,strtotime($_REQUEST['date_to']));
    $status = preg_replace("/[^a-zA-Z0-9\-\_]/","",$_REQUEST['status']);

    if(empty($page)) $page = 1;
    if($page == 1) {
            $offset=0;
            $limit=20;
    } else {
            $offset = ($page-1) * 20;
            $limit = 20;
    }
    $ordering = empty($ordering) ? 'created' : $ordering;
    
    $filter = '';
    #process filter
    if(!empty($customer) || !empty($date_from) || !empty($status)) {
        if(!empty($customer)) {
            $filter .= ' AND cus.id = '.$customer;
        }
        if(!empty($date_from)) {
            if(empty($date_to))
                $date_to = strftime("%Y-%m-%d", 'today');
            $filter .= " AND p.payment_date >= '$date_from' AND p.payment_date <= '$date_to'";
        }
        if(!empty($status) && $status != 'ALL') {
            $filter .= " AND p.payment_status = '$status' ";
        }
    }
    
    $sql = "SELECT cus.id as cid, cus.*, p.* , (SELECT SUM(payment_amount) FROM ". PAYMENTS ." WHERE cus.id=p.customer_id AND payment_status='Completed') as total_amount
    FROM " .CUSTOMERS. " AS cus LEFT JOIN ". PAYMENTS ." as p ON cus.id=p.customer_id 
    WHERE (payment_date IS NULL 
    OR payment_date = (SELECT MAX(payment_date) FROM ". PAYMENTS ." WHERE cus.id=p.customer_id )) $filter
    ORDER BY $ordering $sort";
    #echo $sql;
    if($page != 0)
        $sql_limited = $sql." LIMIT $offset, $limit";
    else
        $sql_limited = $sql;
    if(!($result = mysql_query($sql_limited))) {
        respond(array(
                    'success' => false,
                    'message' => SL_DB_FAILURE
                ));
    } else {
        while($row = mysql_fetch_object($result)){
            #temporary hack until the mode column is available in the database
            if($row->months_purchased == 0) {
                $row->mode = 'active';
            } else {
                $row->mode = 'subscription';
            }
            $ret[] = $row;
        }
        $pqry = mysql_query($sql);
        $totalpages = ceil(mysql_num_rows($pqry) / $limit);
        
        if(!empty($ret)) {
            respond(array(
                        'success' => true,
                        'result' => array('customers' => $ret, 'totalpages' => $totalpages)
                    ));
        } else {
            respond(array(
                        'success' => false,
                        'message' => 'No customers.'
                    ));
        }
    }
}

function getPaymentList()
{
//gj([X.X.X] - sanitize input, register patterns globally for readability and consistency. email, adword, instance, etc)
    $page = intval($_REQUEST['page']);
    $ordering = preg_replace("/[^a-zA-Z0-9\-\_]/","",$_REQUEST['ordering']);
    $sort = preg_replace("/[^a-zA-Z0-9\-\_]/","",$_REQUEST['sort']);
    $customer = intval($_REQUEST['customer']);
    $date_from = empty($_REQUEST['date_from']) ? '' : strftime("%Y-%m-%d" ,strtotime($_REQUEST['date_from']));
    $date_to = empty($_REQUEST['date_to']) ? '' : strftime("%Y-%m-%d" ,strtotime($_REQUEST['date_to']));
    $status = preg_replace("/[^a-zA-Z0-9\-\_]/","",$_REQUEST['status']);


    if(empty($page)) $page = 1;
    if($page == 1) {
            $offset=0;
            $limit=20;
    } else {
            $offset = ($page-1) * 20;
            $limit = 20;
    }
    $ordering = empty($ordering) ? 'payment_date' : $ordering;
    
    $filter = '';
    #process filter
    if(!empty($customer) || !empty($date_from) || !empty($status)) {
        if(!empty($customer)) {
            $filter .= ' AND p.customer_id = '.$customer;
        }
        if(!empty($date_from)) {
            if(empty($date_to))
                $date_to = strftime("%Y-%m-%d", 'today');
            $filter .= " AND p.payment_date >= '$date_from' AND p.payment_date <= '$date_to'";
        }
        if(!empty($status) && $status != 'ALL') {
            $filter .= " AND p.payment_status = '$status' ";
        }
    }
    
    $sql = "SELECT p.*, cus.company_name, cus.domain FROM ". PAYMENTS ." as p LEFT JOIN ". CUSTOMERS ." AS cus ON p.customer_id = cus.id 
    WHERE 1=1 $filter
    ORDER BY $ordering $sort";
    #var_dump( $sql);
    if($page != 0)
        $sql_limited = $sql." LIMIT $offset, $limit";
    else
        $sql_limited = $sql;
    if(!($result = mysql_query($sql_limited))) {
        respond(array(
                    'success' => false,
                    'message' => SL_DB_FAILURE
                ));
    } else {
        while($row = mysql_fetch_object($result)){
            $ret[] = $row;
        }
        $pqry = mysql_query($sql);
        $totalpages = ceil(mysql_num_rows($pqry) / $limit);
        
        if(!empty($ret)) {
            respond(array(
                        'success' => true,
                        'result' => $ret
                    ));
        } else {
            respond(array(
                        'success' => false,
                        'message' => 'No customers.'
                    ));
        }
    }
}

function getCustomerNames()
{
    $sql = "SELECT id, contact_first_name, contact_last_name, company_name FROM " .CUSTOMERS;
    if(!($result = mysql_query($sql))) {
        respond(array(
                    'success' => false,
                    'message' => SL_DB_FAILURE
                ));
    } else {
        while($row = mysql_fetch_object($result)) {
            $ret[] = $row;
        }
        if(!empty($ret)) {
            respond(array(
                        'success' => true,
                        'result' => $ret
                    ));
        } else {
            respond(array(
                        'success' => false,
                        'message' => 'No customers.'
                    ));
        }
    }
}

function getCustomer()
{
    if(empty($_REQUEST['customer_id'])) {
        respond(array(
                    'success' => false,
                    'message' => 'customer_id is required'
                ));
    } else {
        $cid = intval($_REQUEST['customer_id']);
    }
    
    $sql = "SELECT * FROM ". CUSTOMERS ." WHERE id=$cid";
    if(!($result = mysql_query($sql))) {
        respond(array(
                    'success' => false,
                    'message' => SL_DB_FAILURE
                ));
    } else {
        while($row = mysql_fetch_object($result)) {
            $ret[] = $row;
        }
        if(!empty($ret)) {
            respond(array(
                        'success' => true,
                        'result' => $ret
                    ));
        } else {
            respond(array(
                        'success' => false,
                        'message' => 'No customer found.'
                    ));
        }
    }
}

function getPaymentDetails()
{
    if(empty($_REQUEST['payment_id'])) {
        respond(array(
                    'success' => false,
                    'message' => 'payment_id is required'
                ));
    } else {
        $pid = intval($_REQUEST['payment_id']);
    }
    
    $sql = "SELECT * FROM ". PAYMENTS ." WHERE id= $pid";
    
    if(!($result = mysql_query($sql))) {
        respond(array(
                    'success' => false,
                    'message' => SL_DB_FAILURE
                ));
    } else {
        while($row = mysql_fetch_object($result)) {
            $ret[] = $row;
        }
        if(!empty($ret)) {
            respond(array(
                        'success' => true,
                        'result' => $ret
                    ));
        } else {
            respond(array(
                        'success' => false,
                        'message' => 'No payment records found.'
                    ));
        }
    }
}

function getPaymentHistory() 
{
    if(empty($_REQUEST['customer_id'])) {
        respond(array(
                    'success' => false,
                    'message' => 'customer_id is required'
                ));
    } else {
        $cid = intval($_REQUEST['customer_id']);
    }
    
    $sql = "SELECT * FROM ". PAYMENTS ." WHERE customer_id = $cid";
    if(!($result = mysql_query($sql))) {
        respond(array(
                    'success' => false,
                    'message' => SL_DB_FAILURE
                ));
    } else {
        while($row = mysql_fetch_object($result)) {
            $ret[] = $row;
        }
        if(!empty($ret)) {
            respond(array(
                        'success' => true,
                        'result' => $ret
                    ));
        } else {
            respond(array(
                        'success' => false,
                        'message' => 'No payment records found.'
                    ));
        }
    }
}
function getLastPayment() 
{
    if(empty($_REQUEST['customer_id'])) {
        respond(array(
                    'success' => false,
                    'message' => 'customer_id is required'
                ));
    } else {
        $cid = intval($_REQUEST['customer_id']);
    }
    
    $sql = "SELECT * FROM ". PAYMENTS ." WHERE customer_id = $cid LIMIT 1";
    if(!($result = mysql_query($sql))) {
        respond(array(
                    'success' => false,
                    'message' => SL_DB_FAILURE
                ));
    } else {
        while($row = mysql_fetch_object($result)) {
            $ret[] = $row;
        }
        if(!empty($ret)) {
            respond(array(
                        'success' => true,
                        'result' => $ret
                    ));
        } else {
            respond(array(
                        'success' => false,
                        'message' => 'No payment records found.'
                    ));
        }
    }
}

# Creates a new customer entry in the database
# Method gets called by tewari when a new instance is created
function newInstance($silent=false)
{
//gj([X.X.X] - sanitize input, register patterns globally for readability and consistency. email, adword, instance, etc)
    if(empty($_REQUEST['domain'])) {
        respond(array(
                    'success' => false,
                    'message' => 'domain is required'
                ));
    } else {
        $domain = preg_replace("/[^a-zA-Z0-9\-\.]/","",$_REQUEST['domain']);

    }
    if(empty($_REQUEST['email'])) {
        respond(array(
                    'success' => false,
                    'message' => 'email is required'
                ));
    } else {
        $email = $_REQUEST['email'];
        $email = preg_replace("/[^a-zA-Z0-9\@\-\.]/","",$_REQUEST['email']);
    }
    if(empty($_REQUEST['first_name'])) {
        respond(array(
                    'success' => false,
                    'message' => 'first_name is required'
                ));
    } else {
        $first_name = $_REQUEST['first_name'];
        $first_name = preg_replace("/[^a-zA-Z0-9]/","",$_REQUEST['first_name']);
    }

    if(empty($_REQUEST['uuid'])) {
        respond(array(
                    'success' => false,
                    'message' => 'uuid is required'
                ));
    } else {
        $uuid = $_REQUEST['uuid'];
        $uuid = preg_replace("/[^a-fA-F0-9\-\.]/","",$_REQUEST['uuid']);
    }

    if(empty($_REQUEST['db_name'])) {
        respond(array(
                    'success' => false,
                    'message' => 'db_name is required'
                ));
    } else {
        $db_name = $_REQUEST['db_name'];
        $db_name = preg_replace("/[^a-zA-Z0-9\_]/","",$_REQUEST['db_name']);
    }

    if(empty($_REQUEST['instance_api_key'])) {
        respond(array(
                    'success' => false,
                    'message' => 'instance_api_key is required'
                ));
    } else {
        $instance_api_key = preg_replace("/[^a-fA-F0-9\-]/","",$_REQUEST['instance_api_key']);
    }

    if(empty($_REQUEST['source'])) {
        $source = "Other";
    } else {
        $source = preg_replace("/[^a-zA-Z0-9\-\_\ \.]/","",$_REQUEST['source']);
    }

    if(empty($_REQUEST['adwords'])) {
        $adwords = 'null';
    } else {
        $adwords = preg_replace("/[^a-zA-Z0-9\-\_\ \.]/","",$_REQUEST['adwords']);
    }

    $sql = "INSERT INTO ". CUSTOMERS ." SET uuid='$uuid', instance_api_key='$instance_api_key', created=NOW(), domain='$domain', contact_email='$email', contact_first_name='$first_name', employee_count=1, recur_date = DATE_ADD(now(), INTERVAL +1 MONTH), source='$source', keywords='$adwords', db_name='$db_name'";
    #echo $sql;
error_log("salesNewInstance  sql: ".$sql);
    $result = mysql_query($sql) or error_log('salesNI.error: '.mysql_error());;
    if(mysql_affected_rows() == 0) {
        respond(array(
                    'success' => false,
                    'message' => SL_DB_FAILURE
                ));
    } else {
	if ($silent !== true) {
if ($silent != true) {
		error_log("salesNewInstance  announce: ".JOURNAL_API_URL);
        	// Make notice in journal
	        $data = array ('user' => JOURNAL_API_USER,
        	        'pwd' => sha1(JOURNAL_API_PWD),
                	'message' => "A new LoveMachine ".$silent." tenant has just moved in!");
	        $journal_rsp = CURLHandler::Post(JOURNAL_API_URL, $data);
}
		error_log("salesNewInstance  respond: ".json_encode(array('journal_rsp'=>$journal_rsp,'data'=>$data)));
	        respond(array(
        	            'success' => true,
                	    'message' => 'entry added'
	                ));
	}
    }
    
}

# updates the customers current user count and email address.
function updateInstanceData()
{
//gj([X.X.X] - sanitize input, register patterns globally for readability and consistency. email, adword, instance, etc)
    if(empty($_REQUEST['uuid'])) {
        respond(array(
                    'success' => false,
                    'message' => 'uuid is required'
                ));
    } else {
        $uuid = preg_replace("/[^a-fA-F0-9\-\.]/","",$_REQUEST['uuid']);
    }
    if(empty($_REQUEST['usercount'])) {
        respond(array(
                    'success' => false,
                    'message' => 'usercount is required'
                ));
    } else {
        $usercount = intval($_REQUEST['usercount']);
    }
    if(empty($_REQUEST['email'])) {
        respond(array(
                    'success' => false,
                    'message' => 'email is required'
                ));
    } else {
        $email = preg_replace("/[^a-zA-Z0-9\@\-\.]/","",$_REQUEST['email']);
    }
    
    $sql = "UPDATE ". CUSTOMERS ." SET contact_email='$email', employee_count='$usercount' WHERE uuid = '$uuid' ";
    
    $result = mysql_query($sql);
    if(mysql_affected_rows() == 0) {
        respond(array(
                    'success' => false,
                    'message' => SL_DB_FAILURE
                ));
    } else {
        respond(array(
                    'success' => true,
                    'message' => 'entry updated'
                ));
    }
}

?>
