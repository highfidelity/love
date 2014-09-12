<?php 
//  Copyright (c) 2009, LoveMachine Inc.  
//  All Rights Reserved.  
//  http://www.lovemachineinc.com
include("../config.php");
require_once("../class.session_handler.php");
include("helper/check_session.php");

// Database Connection Establishment String
$con=mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
// Database Selection String
mysql_select_db(DB_NAME,$con);
include_once("../functions.php");

$page = $_GET['page']; // get the requested page 
$limit = $_GET['rows']; // get how many rows we want to have into the grid 
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort 
$sord = $_GET['sord']; // get the direction 
if(!$sidx) $sidx =1; // connect to the database 
//$db = mysql_connect($dbhost, $dbuser, $dbpassword) or die("Connection Error: " . mysql_error()); 
//mysql_select_db($database) or die("Error conecting to db."); 
$result = mysql_query("SELECT COUNT(*) AS count FROM " . PERIODS . ""); 
$row = mysql_fetch_array($result,MYSQL_ASSOC); 
$count = $row['count']; 
if( $count >0 ) { 
    $total_pages = ceil($count/$limit); 
} else { 
    $total_pages = 0; 
} 
if ($page > $total_pages) {
    $page=$total_pages;
}    
$start = $limit*$page - $limit; // do not put $limit*($page - 1)
$SQL = "SELECT id,title, start_date, end_date, status FROM " . PERIODS . "  ORDER BY $sidx $sord LIMIT $start , $limit";
$result = mysql_query( $SQL ) or die("Couldn t execute query.".mysql_error()); 
$responce->page = $page; 
$responce->total = $total_pages; 
$responce->records = $count; 
$i=0; 
while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
    $responce->rows[$i]['id']=$row[id]; 
    $responce->rows[$i]['cell']=array($row[title],$row[start_date],$row[end_date],$row[status]); 
    $i++; 
} 
echo json_encode($responce); 
?>
