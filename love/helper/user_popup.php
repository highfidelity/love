<?php
//  Copyright (c) 2009, LoveMachine Inc.  
//  All Rights Reserved.  
//  http://www.lovemachineinc.com

include_once("../class/frontend.class.php");
$front = Frontend::getInstance();
include_once("../db_connect.php");

$this_user = $front->getUser()->getUsername();

$limit = 10;
$page = isset($_GET["page"]) ? $_GET["page"] : 1; //Get the page number to show 
if($page == "") 
{
	$page=1; //If no page number is set, the default page is 1 
}
/********************************************************************************************/
  
//$userLove = "userLove";
$user_nolove = $this_user;
$type="";
$nickname = "";
$nickname1 = "";
$nickname2 = "";
if(isset($_GET['type'])) {
	$type = addslashes($_GET['type']);
}
if(isset($_GET['u'])) {
	$nickname = addslashes($_GET['u']);
}
if(isset($_GET['u1'])) {
	$nickname1 = addslashes($_GET['u1']);
}
if(isset($_GET['u2'])) {
	$nickname2 = addslashes($_GET['u2']);
}
if(isset($_GET['from_date'])) {
	$from_date = addslashes($_GET['from_date']);
} else {
	$from_date = "";
}
if(isset($_GET['to_date'])) {
	$to_date = addslashes($_GET['to_date']);
} else {
	$to_date = "";
}

$user = mysql_real_escape_string($nickname);
$user1 = mysql_real_escape_string( $nickname1);
$user2 = mysql_real_escape_string( $nickname2);
$mysqlFromDate = mysql_real_escape_string(GetTimeStamp($from_date));
$mysqlToDate = mysql_real_escape_string(GetTimeStamp($to_date));


$sql = '';
if($user1 && $user2 ) {
	$sql = "SELECT usr.nickname,usr.username,usr.company_id FROM ".USERS." as usr WHERE usr.nickname IN ('$user1','$user2')";
}
if($user) {
	$sql = "SELECT usr.nickname,usr.username,usr.company_id FROM ".USERS." as usr WHERE usr.nickname ='$user'";
}
$user_assoc = array();
$queryResult = $sql ?  mysql_query( $sql) : false ;
$count = $queryResult ? mysql_num_rows($queryResult) : 0;
if($count > 0) {
	while($row = mysql_fetch_array($queryResult)) {
	  $user_assoc[$row['nickname']] = $row['username'];
	  $user_assoc['company_id'] = $row['company_id'];
	}
}
if ($type && $type == 'userLove' && $this_user == $user_assoc[$user]) {

    $sql = "SELECT lv.giver as giver,usr1.nickname as receiver,lv.why,lv.private 
	    FROM  ".LOVE." as lv
        INNER JOIN  ".USERS." as usr1 ON (lv.receiver = usr1.username)
	    WHERE lv.company_id = ".$user_assoc['company_id']." ".
			  "AND (   (lv.giver = '".$user_assoc[$user]."' ) OR (lv.receiver = '".$user_assoc[$user]."' ) )";
    $sql .= ($from_date && $to_date) ? " AND DATE(lv.at) BETWEEN '".$mysqlFromDate."' AND '".$mysqlToDate."'" : "";
    $sql .= " ORDER BY lv.at DESC";
    
    $link = "type=$type&u=$nickname";
    $link .= ($from_date && $to_date) ? "&from_date=$from_date&to_date=$to_date" : "";	

} elseif( $type && $type == 'userLove' &&  !empty($user_assoc) ) {
    
    $sql = "SELECT lv.giver as giver,usr1.nickname as receiver,lv.why,lv.private 
	    FROM  ".LOVE." as lv
        INNER JOIN  ".USERS." as usr1 ON (lv.receiver = usr1.username)
	    WHERE lv.company_id = ".$user_assoc['company_id']." ".
			  "AND (   (lv.giver = '".$user_assoc[$user]."' AND lv.private = 0) OR (lv.receiver = '".$user_assoc[$user]."' AND lv.private = 0) OR (lv.receiver = '". $user_assoc[$user] . "' AND lv.giver='".$this_user."') OR (lv.receiver = '" . $this_user . "' AND lv.giver = '" . $user_assoc[$user] . "') )";
    $sql .= ($from_date && $to_date) ? " AND DATE(lv.at) BETWEEN '".$mysqlFromDate."' AND '".$mysqlToDate."'" : "";
    $sql .= " ORDER BY lv.at DESC";
    $link = "type=$type&u=$nickname";
    $link .= ($from_date && $to_date) ? "&from_date=$from_date&to_date=$to_date" : "";	
}
elseif( $type && $type == 'userLoveExchange' &&  !empty($user_assoc)) {
	//Make private filter the default unless specifically allowed
        $privateDefaultOff = " and lv.private = 0 ";
  	if (($_SESSION['nickname'] == $user1) or ($_SESSION['nickname'] == $user2)) {
	   $privateDefaultOff = '';
 	}

    $sql="SELECT lv.giver as giver,usr1.nickname as receiver,lv.why,lv.private 
	  FROM  ".LOVE." as lv
      	INNER JOIN  ".USERS." as usr1 ON (lv.receiver = usr1.username)
	  WHERE (   (lv.giver = '".$user_assoc[$user1]."' AND lv.receiver = '".$user_assoc[$user2]."') 
	         OR	(lv.receiver = '".$user_assoc[$user1]."' AND lv.giver = '".$user_assoc[$user2]."'))
	  AND lv.company_id = ".$user_assoc['company_id'];
    $sql .= $privateDefaultOff;

    $sql .= ($from_date && $to_date) ? " AND DATE(lv.at) BETWEEN '".$mysqlFromDate."' AND '".$mysqlToDate."'" : "";
    $sql .= " ORDER BY lv.at DESC";
    
    $link = "type=$type&u=$nickname";
    $link = "type=$type&u1=$nickname1&u2=$nickname2";
    $link .= ($from_date && $to_date) ? "&from_date=$from_date&to_date=$to_date" : "";
    }else {
    echo "This page usually shows a social graph! It illustrates the connections between the people you've exchanged love with. Start sending love to see it at work.";
    die();
}

$queryResult = mysql_query( $sql);
$count = $queryResult ? mysql_num_rows($queryResult) : 0;

if($count > 0) { 
  $NumberOfPages = ceil($count/$limit); 
  $sql = $sql ." LIMIT " . ($page-1) * $limit . ",$limit";
  $queryResult = mysql_query($sql);

?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<link href="css/CMRstyles.css" rel="stylesheet" type="text/css">-->
<head>

	<table cellpadding="0" cellspacing="0" border="0" class="table-history" width="100%">
	  <tr>
	    <td colspan="3" valign="top">
		<table class="table-history" cellpadding="15" cellspacing="0" border="0" width="100%" height="100%" style="font-size:12px">
			<thead>
			<tr class="table-hdng">
			  <td class="love-from" align="left"><strong>From</strong></td>
			  <td class="love-to" align="left"><strong>To</strong></td>
			  <td class="love-for" align="left"><strong>For</strong></td>
			</tr>
			</thead>
			<tbody>
			    <? 
				$oddRow = 1;
			    while($row = mysql_fetch_array($queryResult))
			    {
			    	$why = stripslashes($row['why']);
					    if($oddRow == 1) {
						   $rowClass = 'row-history-live rowodd';
						   $oddRow = 0;
					    }else {
						    $rowClass = 'row-history-live roweven';
					    }
					    ?> 
					    <tr class="<?=$rowClass?>">
					    	<?php
					    		$nickname = getNickname($row['giver']);
					    		if ($nickname == '') $nickname = $row['giver'];
					    	?>
						    <td class="love-from" align="left"><?= $nickname ?></td>
						    <td class="love-to"  align="left"><?=$row['receiver']?></td>
						    <td class="love-for" align="left" style="height:auto; overflow: visible;">
								<?=htmlspecialchars($why) . ($row['private'] ? ' (love sent quietly)' : '')?>
							</td>
					    </tr>
			      <? } ?> 
			 <tr bgcolor="#FFFFFF"> 
			    <td colspan="3" style="text-align:center;">
					<?
					    $Nav=""; 
					    if($page > 1) { 
					    $Nav .= "<a class=\"page-number\" href=\"".$_SERVER['PHP_SELF']."?$link&page=" . ($page-1) ."\">Prev</a> &nbsp;"; 
					    } 
					    
					    for($i = 1 ; $i <= $NumberOfPages ; $i++) { 
						if($i == $page) { 
						$Nav .= "$i &nbsp;"; 
						}else{ 
						$Nav .= "<a class=\"page-number\" href=\"".$_SERVER['PHP_SELF']."?$link&page=" . $i . "\" >$i</a> &nbsp;"; 
						} 
					    }
					    
					    if($page < $NumberOfPages) { 
					    $Nav .= " &nbsp;<a class=\"page-number\" href=\"".$_SERVER['PHP_SELF']."?$link&page=" . ($page+1) . "\" >Next</a>"; 
					    } 
					    
					    echo   "Pages : &nbsp;".$Nav; 
					?>
			      </td>
			 </tr>
			</tbody>
		 </table>
	      </td>
	    </tr>
	</table>
<? }else {
      echo "This page usually shows a social graph! It illustrates the connections between the people you've exchanged love with. Start sending love to see it at work.";
}

function GetTimeStamp($MySqlDate, $i='')
{
	if (empty($MySqlDate)) {
		$MySqlDate = date("Y/m/d", time());
        }

        /*
                Take a date in yyyy-mm-dd format and return it to the user
                in a PHP timestamp
                Robin 06/10/1999
        */
        $date_array = explode("/",$MySqlDate); // split the array
        
        $var_year = $date_array[0];
        $var_month = $date_array[1];
        $var_day = $date_array[2];
		$var_timestamp=$date_array[2]."-".$date_array[0]."-".$date_array[1];
		//$var_timestamp=$var_month ."/".$var_day ."-".$var_year;
        return($var_timestamp); // return it to the user
}

?>
