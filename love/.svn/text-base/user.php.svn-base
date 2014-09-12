<?php
    //  Copyright (c) 2010, LoveMachine Inc.
    //  All Rights Reserved.
    //  http://www.lovemachineinc.com
    require_once("class/frontend.class.php");
    require_once("class/UserInfo.php");
    $front = Frontend::getInstance();

    if(!$front->isUserLoggedIn()){
        $front->getUser()->askUserToAuthenticate();
    }

    if(isset($_GET['user'])){
        $user = urldecode($_GET['user']);
        $userInfo = new UserInfo();
        if(strpos($user, '@')){
            $userInfo->loadUserByUsername($user, $front->getCompany()->getid());
        }else{
            $userInfo->loadUserByNickname($user, $front->getCompany()->getid());
        }

        if(!$userInfo->getId()){
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
            die("Object Not Found. Error 404");
        }
    }else{
        die('No user data provided');
    }

/*********************************** HTML layout begins here  *************************************/
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" xml:lang="en-US" >
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>SendLove | User info</title>
        <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />
        <link href="css/tofor.css" rel="stylesheet" type="text/css" />
        <link href="css/user.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jstorage.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jcache.js"></script>
        <script type="text/javascript" src="<?php echo CONTRIB_URL; ?>branding.js"></script>
        <script type="text/javascript" src="js/tofor.js"></script>
        <script type="text/javascript" src="js/user.js"></script>
        <script type="text/javascript" src="js/uservoice.js"></script>
        <script type="text/javascript">

            // setting up user settings
            $.user = {
                jcacheDelay: <?php echo JCACHE_DELAY; ?>,
                username: '<?php echo $userInfo->getUsername(); ?>',
                listUserLovesWhen: true
            };
        </script>
    </head>
    <body>
        <div id="wrapper">
            <!-- Include header -->
            <?php
                define('LOVE_TABS_DISABLED', true);
                include("view/settings/header.php");
            ?>
 
            <!-- Include content -->
            <?php include("view/user/content.php"); ?>

            <?php include("view/tofor/footer.php"); ?>
            <p class="lm"><a href="http://www.sendlove.us" target="_blank"><img class="logo_footer" src="images/SendLove_logo_sm.png"/></a></p>
        </div><!-- end of "wrapper" -->
    </body>
</html>
