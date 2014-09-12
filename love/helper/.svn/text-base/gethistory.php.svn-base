<?php
//  Copyright (c) 2009, LoveMachine Inc.  
//  All Rights Reserved.  
//  http://www.lovemachineinc.com

// AJAX request from ourselves to retrieve history

include("config.php");
require_once("class.session_handler.php");
include("functions.php");

if (!checkReferer()) die;

if (!isset($_SESSION['company_id'])) {
    return;
}

$guest = !empty($_POST['guest']) ? true : false;
if (empty($_SESSION['username']) && !$guest) {
    echo json_encode('expired');
    die;
}

$limit = 30;
$page = isset($_REQUEST["page"])?$_REQUEST["page"]:1; //Get the page number to show, set default to 1
if (!$guest) {
    $username = mysql_real_escape_string($_SESSION['username']);
} else {
    $username = GUEST_USER;
}

$query = "select company_id, company_admin, company_confirm from ".USERS." where username='$username'";
$rt = mysql_query($query);
$row = mysql_fetch_assoc($rt);
$where = ($row['company_id'] != 0 && (($row['company_admin'] >= '1') || $row['company_confirm'])) ? (" or (".LOVE.".company_id = '".$row['company_id']."' AND ".LOVE.".private=0)") : '';

$rt = mysql_query("select count(*) from ".LOVE);
$row = mysql_fetch_row($rt);
$loves = $row[0];

$query= "select count(*) from ".LOVE. 
        " where ".LOVE.".receiver = '$username' or ".LOVE.".giver = '$username' ". 
        $where . " order by id desc";
$rt=mysql_query($query);
$row = mysql_fetch_row($rt);
$count = $row[0];
$cPages = ceil($count/$limit); 

$query= "select id,giver,receiver,why,private,TIMESTAMPDIFF(SECOND,at,NOW()) as delta from ".LOVE. 
        " where ".LOVE.".receiver = '$username' or ".LOVE.".giver = '$username' ". 
        $where . " order by id desc";
$query .= " LIMIT " . ($page-1)*$limit . ",$limit";
$rt=mysql_query($query);

// Construct json for history
$history = array(array($page, $cPages, number_format($loves)));
for ($i = 1; $row=mysql_fetch_assoc($rt); $i++)
{
    $givernickname = getNickName($row['giver']);    
    $givernickname = (!empty($givernickname))?($givernickname):($row['giver']);
            
    $receivernickname = getNickName($row['receiver']);    
    $receivernickname = (!empty($receivernickname))?($receivernickname):($row['receiver']);
    
    $giverPicture = getPicture($row['giver']);
    $receiverPicture = getPicture($row['receiver']);

    $why = $row['why'];
    if ($row['private']) $why .= " (love sent quietly)";

    $history[] = array($row['id'], $row['giver'], $givernickname, $row['receiver'], $receivernickname, $why, $row['delta'], $giverPicture, $receiverPicture);
}
                      
$json = json_encode($history);
echo $json;     
