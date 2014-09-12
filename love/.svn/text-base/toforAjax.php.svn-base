<?php 
if(isset($_REQUEST["tell"])){
    include("class/frontend.class.php");
    $front = Frontend::getInstance();
    
    include_once("db_connect.php");
    include_once("autoload.php");
include("helper/check_session.php");

    $method = trim($_REQUEST["tell"]);
    echo json_encode($front->$method());
}
if(isset($_REQUEST["view"])){
    include("class/frontend.class.php");
    $front = Frontend::getInstance();

    include_once("db_connect.php");
    include_once("autoload.php");
include("helper/check_session.php");
    $method = trim($_REQUEST["view"]);
    $front->$method();
}
?>
