<?php
if ($_SERVER['PHP_SELF']==__FILE__) die("Invalid request"); 
// this file is simply connects to db
// since every our page needs bd connection
    require_once("config.php");
    $link = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD) or die('Connection failed: ' . mysql_error());
    mysql_select_db(DB_NAME,$link);

