<?php
//  Copyright (c) 2010, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com

require_once(dirname(dirname(__FILE__)).'/config.php');
require_once(dirname(dirname(__FILE__)).'/send_email.php');

$db = @mysql_connect (DB_SERVER, DB_USER, DB_PASSWORD);

if (!$db || !@mysql_select_db (DB_NAME, $db)) {
    print mysql_error();
    exit(1);
}


function send_email_to_customer($customer, $template='reminder'){
    #TODO create the data array
    $data = array('domain' => $customer->domain);
    #print "sending mail to " . $customer->contact_email; 
    return sendTemplateEmail($customer->contact_email, $template, $data);
}

function process_customers($customers, $reminder_tpl = 'reminder1') {
    foreach($customers as $cus) {
        send_email_to_customer($cus, $reminder_tpl);
    }
}

function get_expired_in($days = 7){
    $ret = array();
    $sql = "SELECT * FROM ". CUSTOMERS ." WHERE DATEDIFF(recur_date, NOW()) = $days";
    if(!($result = mysql_query($sql))) {
        print "No results for customers expiring in $days days";
    } else {
        while($row = mysql_fetch_object($result)) {
            $ret[] = $row;
        }
    }
    return $ret;
}

#send 7 days
#$seven = get_expired_in(7);
#process_customers($seven);

#send 3 days
#$three = get_expired_in(3);
#process_customers($three);

#send 24hrs
$one = get_expired_in(1);
process_customers($one, 'reminder1');


exit(0);
