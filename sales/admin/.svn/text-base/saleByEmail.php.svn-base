<?php
// include paypal and email functions
include_once('../server.local.php');
require_once('../config.php');
include_once("../db_connect.php");

// Caller script should pass a GET parameter with the databaseName name
//gj([X.X.X] - sanitize input, register patterns globally for readability and consistency. email, adword, instance, etc)^M
$databaseName= preg_replace("/[^a-zA-Z0-9\_]/","",$_REQUEST['databaseName']);
$campaignId = (int) $_REQUEST['campaignId'];

if ($databaseName == null || $databaseName == "") {
    die("Invalid parameter databaseName!");
}
if ($campaignId == null || $campaignId == "") {
    die("Invalid parameter campaignId!");
}


 /***
Campaign transaction status is the field budget_validated of table PERIODS
    N -> budget not validated
    Y -> the Sales application has accepted the payment from the manager, the campaign is funded

***/
function changeCampaignStatus($campaign_id,$new_validated_status,$instance){
    $filter="";
    $periodFilter=PERIODS . ".`id` = $campaign_id ";
    $filter = " AND NOT budget_validated = 'Y' ";

    $sql = "UPDATE $instance." . PERIODS . ",$instance." . USER_REVIEWS ." SET `budget_validated` = '$new_validated_status' WHERE " .
                USER_REVIEWS .".period_id = " . PERIODS . ".id AND $periodFilter $filter";

    $ret = mysql_unbuffered_query($sql);
    if ($ret) {
        if (mysql_affected_rows() == 1) {
            return  "Payment accepted." ;
        } else {
            return  "Period was already funded." ;
        }
    } else {
        return  'error SQL in changeCampaignStatus' . mysql_error ()  ;
    }
}

$ret = changeCampaignStatus($campaignId,"Y",$databaseName);
echo $ret;