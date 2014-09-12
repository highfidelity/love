<?php
/**
 * This file is used to verify that the session parameters are correct
 * In case of invalid Session, the script is stopped
 */
require_once('class/Session.class.php');

// Caller script should pass a GET parameter with the domain name
//gj([X.X.X] - sanitize input, register patterns globally for readability and consistency. email, adword, instance, etc)^M
$domain = preg_replace("/[^a-zA-Z0-9\-\.]/","",$_REQUEST['domain']);
$databaseName= preg_replace("/[^a-zA-Z0-9\_]/","",$_REQUEST['databaseName']);

if ($domain == null || $domain == "") {
    echo json_encode(array('error' => "parameter",
                    'errorMsg' => "Invalid parameter domain!"));
    die();
}
// Get the instance name from the domain
// Open the session of the instance database
//$instance = getInstanceNameFromDomain($domain);
$instance = $databaseName;
session::initByInstance($instance);
session::check();

//Verify that the instance session has the checkout information
// and the same domain
if (!isset($_SESSION['checkoutCampaign'])) {
    echo json_encode(array('error' => "Session",
                    'errorMsg' => "Missing Session Information! Instance name:" . $instance . " , domain name: ".$domain));
    die();
} else {
    $domainFromSession = $_SESSION['checkoutCampaign']['instanceHost'];
    if ( $domain != $domainFromSession) {
        echo json_encode(array('error' => "Session",
                            'errorMsg' => "Invalid domain information!"));
        die();
    }
}
