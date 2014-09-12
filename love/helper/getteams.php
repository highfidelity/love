<?php 
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

include("../config.php");
require_once("../class.session_handler.php");
include("../helper/check_session.php");
include("../functions.php");

if (!checkReferer()) die;

if (empty($_GET["term"])) die("[]");
$q = strtolower($_GET["term"]);

//the $_GET on limit here is safe because max will return '0' for anything that does not evaluate to a number)
$limit = !empty($_GET["limit"]) ? $_GET["limit"] : 8;
$limit = max($limit, 8);

// getting default company_id
$company_id = 0;
$sql = "select id from " . COMPANY . " where lower(name)='" . COMPANY_NAME . "'";

$res = mysql_query($sql);
if($res && $row = mysql_fetch_assoc($res)){
    $company_id = $row['id'];
}

$query= "select distinct(team) from ".USERS." 
       where lower(team) like '".mysql_real_escape_string($q)."%' and company_id='$company_id' and id <> '".$_SESSION['userid']."' order by team limit ".$limit;

       $result = mysql_query($query);
$team = array();
$ret = '[';
while ($row=mysql_fetch_assoc($result))
{
    if ( $ret != '[' ) {
        $ret .= ",";
    }
    $ret .= '{"label":"' . $row['team'] .'"}';
}
echo $ret."]";

?>
