<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

// AJAX request from retrieving rate limiting information

include_once("config.php");
include_once("functions.php");
include_once("sendlove_func.php");
include_once("class/frontend.class.php");
require_once("helper/check_session.php");

// is love being sent from bookmarklet?
$fromMarklet = false;
$message = '';
if (array_key_exists('marklet', $_POST) && $_POST['marklet'] == 1) {
    $fromMarklet = true;
}

// if (!checkReferer()) die;
if (empty($_POST['to']) || empty($_POST['for1'])) {
    if ($fromMarklet) {
        $message = '<div class="LV_invalid">Error sending love - invalid request</div>';
    } else {
        error_log("sendlove.php invalid request: ".json_encode('params'));
        echo json_encode(array('error'=>1,'messages'=>'invalid request'));
        die;
    }
}

// Replace dropped +'s, urldecodes to space.
$to = str_replace(" ",'+',$_POST['to']);
if (!filter_var($to,FILTER_VALIDATE_EMAIL)) {
    if ($fromMarklet) {
        $message = '<div class="LV_invalid">Error sending love - invalid request</div>';
    } else {
        error_log("sendlove.php: email failed validation filter");
        echo json_encode(array('error'=>1,'messages'=>'invalid request'));
        die;
    }
}

// params are: $userid, $username, $isSuper, $nickname, $to, $for, $priv
$isSuper = isSuperAdmin();

$for_stripped = smart_strip_tags($_POST['for1']);

$for = mysql_real_escape_string($for_stripped);

if ($_SESSION['username']==$to) {
    if ($fromMarklet) {
        $message = '<div class="LV_invalid">You cannot send love to yourself.</div>';
    } else {
        die("Love sent: self");
    }
}
$rc = sendlove_toanother($_SESSION['userid'], $_SESSION['username'], $_SESSION['nickname'], $isSuper, $to, $for, ((int)$_POST["priv"]>0));

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    echo json_encode(array('response' => $rc));
    exit;

    // The following section is not executed due the preceeding speedup workaround. - GJ - Aug 12, 2011
    // return a json array containing updated Love counts for dynamic page update
    $front = new Frontend();
    $loveData = array(
        'response' => $rc,
        'data' => array(
            'loveNotifications' => $front->getLoveNotification(),
            'loveTotal'         => $front->totalLove(),
            'loveMost'          => $front->mostLoved())
    );

  echo json_encode($loveData);
} else {
    if ($fromMarklet) {
        if ($message == '') {
            if ($rc == 'outside') {
                $message = '<div class="LV_invalid">This user is not registered.</div>';
            } else {
                $message = '<div class="LV_valid">Love sent successfully</div>';
            }
        }
        $front = Frontend::getInstance();
        include("view/tofor/love/m_sendlove.php");
    } else {
        echo "Love sent: " . $rc;
    }
}
exit;
