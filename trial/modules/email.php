<?php
//
//  Copyright (c) 2012, Below92 LLC.
//  All Rights Reserved. 
//  http://www.sendlove.us
//
require_once('modules/authmail.php');

/*  send_email
 *  Send an email
 */
function send_email($to, $subject, $html) {
    if (empty($to)) {
        return false;
    }

    $headers['To']=$to;
    $headers['Content-Type']="text/html; charset=UTF-8";

    send_authmail(array('sender'=>'authuser','server'=>'gmail-ssl'),$to,$subject,$html,$headers);
    return true;
}

?> 
