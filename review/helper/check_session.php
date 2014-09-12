<?php 
//  Copyright (c) 2009-2010, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
if (empty($_SESSION['username']) || empty($_SESSION['userid'])) {
 	unset($_SESSION);
    if (isset($load_module) && $load_module == true) {
        echo "Error: session not initialized in review application.";
        exit;
    } else
    {
        session_destroy();
        header("location:login.php?expired=1&redir=".urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}
?>
