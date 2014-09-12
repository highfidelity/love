<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

/**
 * File:            $Id$
 *
 * @lastrevision    $Date$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $LastChangedDate$
 */

// AJAX request from ourselves to retrieve history

include_once("../class/frontend.class.php");
$front = Frontend::getInstance();
include_once("../db_connect.php");
include_once("../autoload.php");
if (! $front->isUserLoggedIn()) {
    die("User not logged !");
}

require_once '../lib/Sms/Numberlist.php';
$provider_list = Sms_Numberlist::$providerList;

if (empty($_POST['c'])) {
    die;
}

$provlist = array();
if (isset($provider_list[$_POST['c']])) {
    foreach ($provider_list[$_POST['c']] as $prov=>$fmt) {
        $provlist[] = array($prov, $fmt);
    }
}

$json = json_encode($provlist);
echo $json;
