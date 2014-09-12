<?php
// Retreive the session from Zend, we should be in admin application to display this script
session_start();
//var_dump($_SESSION);
if (isset($_SESSION['Default']) && isset($_SESSION['Default']['userid']) ) {
    $userid_from_zend = $_SESSION['Default']['userid'];
} else {
    $userid_from_zend = -2;
}
session_write_close();

function doQuery($sql){
        $result=mysql_query($sql);
        $ret=mysql_fetch_object($result);
        return $ret;
    }
    
function clearSession() {
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    session_destroy();
}
function isAdmin($user_id) {
    $_REQUEST["user_id"] = $user_id;
    $response = new Response();
    $login = new Login();
    $login->setResponse($response);
    $login->getUserData();
    $result = $response->getResponse();
    if($result['error'] == 1){
        return false;
    }
    if ( $result['admin'] == "1" ) {
        return true;
    }
    return false;
}

   /* 
function loginUserIntoSession($user_id,$username,$nickname,$admin,$session_id){

    session_id($session_id);
    session::init();
    Utils::setUserSession($user_id, $username, $nickname, $admin);
}
    */

//$_SERVER['REQUEST_URI']
//       $params["sid"] = session_id();
// Process when the userid is coming from admin
function checkLoginFromAdmin($userid_from_zend) {
    $front = Frontend::getInstance();
    if ( isset($userid_from_zend ) &&  $userid_from_zend != "" &&  $userid_from_zend != -2) {
    //echo "0*".$userid_from_zend."*";
        $user_id = (int) $userid_from_zend; 
        if ($user_id == 0) {
            die("Admin session expired");
        }
       if ( $front->isUserLoggedIn() && isset($_SESSION["userid"]) && $_SESSION["userid"] != 0 && $_SESSION["userid"] == $user_id ){
           // already logged nothing to do
        } else if( $front->isUserLoggedIn() && isset($_SESSION["userid"])&& $_SESSION["userid"] != 0 && $_SESSION["userid"] != $user_id ){
            die("You are logged in Love application with another userid in this session. Please, logout from Love application!" . $_SESSION["userid"] . "**" . $user_id);
        } else {   
            $sql = "SELECT ".USERS.".*, ".COMPANY.".name as company_name  ".
                   "FROM ".USERS.", ".COMPANY." ".
                   "WHERE ".USERS.".id = ".mysql_real_escape_string($user_id)." AND ".
                   USERS.".company_id = ".COMPANY.".id";
                   
            $row = doQuery($sql);
            $username = $row->username;
            $nickname = $row->nickname;
 //           $admin = $row->admin;
            $_SESSION["userid"] = $user_id;
            $_SESSION["username"] = $username;
            $_SESSION["nickname"] = $nickname;
   //         $_SESSION["admin"] = $admin;
            $_SESSION['running'] = "true"; 
            
            if(!$front->isUserLoggedIn()){
                $front = new Frontend();
                if(!$front->isUserLoggedIn()){
                    clearSession();
                    die("You are still not logged! Click on another tab, and come back back here it could work");
                }
            }
            if ( ! isAdmin($user_id) ) {
                clearSession();
                die("You should have admin right to get access to this page.".$admin. "**" .USERS);
            }
        }      
    }
    if(!$front->isUserLoggedIn()){
        clearSession();
        $front->getUser()->askUserToAuthenticate();
    }
    if ( !isAdmin($_SESSION["userid"] )) {
        clearSession();
        die("You should have admin right to get access to this page.");
    }
}