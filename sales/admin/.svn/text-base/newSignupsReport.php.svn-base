<?php

include_once('../server.local.php');
require_once('../config.php');
require_once('../class/CURLHandler.php');

// log errors to the file below
ini_set('display_errors', true);
// Connect to the db
$mysqli = new mysqli(DB_SALES_SERVER, DB_SALES_USER, DB_SALES_PASSWORD);
// Check connection
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

//$mysqli->select_db($cupid_config['db_name']);
if (! $mysqli->select_db(DB_SALES)) {error_log("failed to connect to db: ".DB_SALES.' : '.$mysqli->error); }

$query="SELECT created,company_name,source,keywords,domain,instance_api_key FROM ".CUSTOMERS." ORDER BY created DESC ;";

// prepare array of domains
//$query = 'SELECT DISTINCT `domain`,`data` FROM ' . $cupid_config['db_conf'] . ' WHERE `config_key` = "DB_NAME";';

if (! $result = $mysqli->prepare($query)) { error_log("failed prepare".$mysqli->error); }
$result->execute();
$result->store_result();
if ($result->num_rows == 0) {
    die('No customers found');
}

// build array of domains
$instances = array();
$result->bind_result($_created,$_company_name,$_source,$_keywords,$_domain, $_api2);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>New Signup Report</title>
<link type="text/javascript" href="../js/jquery-1.4.2.min.js" />
</head>

<body>


<table border="1" >
<thead>
<th style="width:160px">Created</th>
<th style="width:250px">Company</th>
<th style="width:100px">Source</th>
<th style="width:150px">Keywords</th>
<th style="width:150px"># Users</th>

<?php if($debug){
		echo "<th style=\"width:150px\">CURL Response</th>";
	}
 ?>
<th style="width:150px"># Love</th>
</thead>
<tbody>
<?php

$cupid_config['db_name'] = get_cfg_var('cupid_db') ? get_cfg_var('cupid_db') : '';
$cupid_config['db_conf'] = get_cfg_var('cupid_conf') ? get_cfg_var('cupid_conf') : '';

if( !strlen($cupid_config['db_name']) && !strlen($cupid_config['db_conf'])) {
	die("Failed to load cupid values!");
}

$mysqli->select_db($cupid_config['db_name']);
while ($result->fetch()) {
    $split_instance = explode('.', $_domain, 2);

	$query=" SELECT data FROM ".$cupid_config['db_name'].".".$cupid_config['db_conf']." WHERE domain='{$_domain}' AND config_key='API_KEY' ;";
	$result2 = $mysqli->prepare($query) or error_log("unable to select db: $query\n".$mysqli->error);
	$result2->execute();
	$result2->store_result();
	if ($result2->num_rows == 0) {
		continue;
	}
	
	// build array of domains
	$result2->bind_result($_api);	
	$result2->fetch();
    $vars = array(
        'action' => 'newSignupsReportData',
		'api_key' => $_api
    );
    $url= 'https://'.$split_instance[0].'.sendlove.us/love/api.php';
	ob_start();
    CURLHandler::Post($url , $vars);
	$CURLresult=json_decode(ob_get_contents());
	

	ob_end_clean();
	
?>
<tr id="inst_<?php echo $_domain; ?>">
<td class="created"><?php echo $_created; ?></td>
<td class="company"><?php echo $split_instance[0]; ?></td>
<td class="source"><?php echo $_source; ?></td>
<td class="keywords"><?php echo $_keywords; ?></td>
<td class="users"><?php echo isset($CURLresult->totalUsers)?$CURLresult->totalUsers:"N/A"; ?>&nbsp;</td>
<td class="love"><?php echo isset($CURLresult->totalLoveSent)?$CURLresult->totalLoveSent:"N/A"; ?>&nbsp;</td>
<?php 	
	if($debug){
		echo "<td>";
		print_r($CURLresult);
		echo "</td>"; 
	}
?>
</tr>
<?php
}
?>
</tbody>
</table>
<script type="text/javascript" language="javascript">
// Run through all scripts
$(function () {
	
}
);
</script>
</body>
</html>
