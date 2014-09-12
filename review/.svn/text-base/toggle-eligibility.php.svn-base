<?php

include("config.php");

//open db connection
$db = @mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD) or die ('I cannot connect to the database because: ' . mysql_error());
$db = @mysql_select_db(DB_NAME);


$user_id = $_GET["user_id"];
$type = $_GET["type"];

$user_flag_sql = 'SELECT is_'.$type.' FROM '.REVIEW_USERS.' WHERE id = '.$user_id;
$user_flag_query = mysql_query($user_flag_sql);
while ($row = mysql_fetch_array($user_flag_query)) {
    $current_flag = $row[0];
}

$flag = $current_flag == '0' ? '1' : '0';

$user_update_sql = 'UPDATE '.REVIEW_USERS.' SET is_'.$type.' = \''.$flag.'\' WHERE id = '.$user_id;
$user_update = mysql_query($user_update_sql);

if ($user_update) { 
    echo $flag;
} else { 
    echo 'Update Failed: '.mysql_error(); 
}

if(empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header("location:rewarder-user-admin.php");
}
?>
