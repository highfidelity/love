<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

include("../config.php");
require_once("../class.session_handler.php");
//include("helper/check_session.php");
include("../functions.php");

if (!checkReferer()) die;

if (empty($_GET["term"])) die("[]");
$q = strtolower($_GET["term"]);

//the $_GET on limit here is safe because max will return '0' for anything that does not evaluate to a number)
$limit = !empty($_GET["limit"]) ? $_GET["limit"] : 8;
$limit = max($limit, 8);

// Database Connection Establishment String
$con=mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
// Database Selection String
mysql_select_db(DB_NAME,$con);

$query= "select id,name from ".COMPANY." 
	   where lower(name) like '".mysql_real_escape_string($q)."%' order by name limit ".$limit;
$result = mysql_query($query,$con);
$companies = array();
$ret = '[';
while ($row=mysql_fetch_assoc($result))
{
    if ( $ret != '[' ) {
        $ret .= ",";
    }
    $ret .= '{"id":"' . $row['id'] . '","label":"' . $row['name'] .'"}';
}
echo $ret."]";
?>
