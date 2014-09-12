<?php
//  vim:ts=4:et
//
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
//
include ("../config.php");
require_once ("../send_email.php");

connect();

$result = sl_ping_company_admin(MAIN_COMPANY, strip_tags($_REQUEST['name']), strip_tags($_REQUEST['email']), strip_tags($_REQUEST['message']));

if ($result == true) {
    echo(json_encode(array(
        'success' => true,
        'message' => 'Ping has been sent.'
    )));
} else {
    echo(json_encode(array(
        'success' => false,
        'message' => 'Could not send ping.'
    )));
}
exit();
