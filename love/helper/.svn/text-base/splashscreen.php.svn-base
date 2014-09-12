<?php ini_set('display_errors', 1); error_reporting(-1);
if (empty($_REQUEST['action']) || empty($_REQUEST['userid'])) {
	die('not allowed!');
}
require_once('../config.php');

if ($_REQUEST['action'] == 'deactivate') {
	$val = 0;
} else {
	$val = 1;
}

connect();
$query = 'UPDATE `' . USERS . '` SET `splash` = ' . $val . ' WHERE `id` = ' . (int)$_REQUEST['userid'] . ';';
mysql_query($query);
