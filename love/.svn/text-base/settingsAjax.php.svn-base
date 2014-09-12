<?php
require_once("class/frontend.class.php");
$front = Frontend::getInstance();

include_once("db_connect.php");
include_once("autoload.php");
require_once('oauth/linkedin.class.php');

if ( !$front->isUserLoggedIn() ) {
    $front->getUser()->askUserToAuthenticate();
}

$messages = array();
$redirect = false;

if (isset($_POST['action'])) {
    if($_POST['action'] == "revoke"){
        $API_CONFIG = array(
            'appKey'       => LINKEDIN_API_KEY_PUBLIC,
            'appSecret'    => LINKEDIN_API_KEY_PRIVATE,
            'callbackUrl'  => NULL 
        );
        
        $OBJ_linkedin = new LinkedIn($API_CONFIG);
        $OBJ_linkedin->setTokenAccess(array('oauth_token' => $front->getUser()->getAccess_token(), 'oauth_token_secret' => $front->getUser()->getAccess_token_secret()));
        $OBJ_linkedin->revoke();
        
        $front->getUser()->deleteTokens();
    }
}

if (isset($_POST['nickname'])) {

    $nickname = trim($_POST['nickname']);
    $updateNickname = false;
    $updatePassword = false;
    
    if ($nickname != $_SESSION['nickname']) {
        if (strpos($nickname, ' ') !== false) {
            $front->getError()->setError("Your nickname cannot contain any spaces. Please try again.");
        } elseif (strlen($nickname) > MAX_NICKNAME_CHARS) {
            $front->getError()->setError("Your nickname must be less than " . MAX_NICKNAME_CHARS . " long.");
        } elseif (preg_match("/<(.|\n)*?>/", $nickname)) {
            $front->getError()->setError("Your nickname contained illegal characters. Please try again.");
        } else {
           $updateNickname = true;
        }
    }

    if (Utils::checkVar($_POST['newpassword'])) {
        if (Utils::checkVar($_POST['confirmpassword'])) {
            if (Utils::checkVar($_POST['oldpassword'])) {
                if (Utils::matchStrings($_POST['newpassword'], $_POST['confirmpassword'])) {
                    $updatePassword = true;
                } else {
                    $front->getError()->setError('Your passwords do not match.');
                }
            } else {
                // current password not entered
                $front->getError()->setError('You need to provide your current password.');
            }
        } else {
            // confirm password not entered
            $front->getError()->setError('Your confirm password is missing.');
        }
    }


    if ($updateNickname || $updatePassword) {
        $params = array('action' => 'update', 'user_data' => array('userid' => $_SESSION['userid']));
        if ($updateNickname) {
            $params['user_data']['nickname'] = $_REQUEST['nickname'];
        }
        if ($updatePassword) {
            $params['user_data']['newpassword'] = $_REQUEST['newpassword'];
            $params['user_data']['oldpassword'] = $_REQUEST['oldpassword'];
            $messages[] = "Your password has been updated.";
        }
        $params['sid'] = session_id();

        ob_start();
        // send the request
        CURLHandler::Post(SERVER_URL . 'loginApi.php', $params, false, true);
        $result = ob_get_contents();
        ob_end_clean();
        $result = json_decode($result);
        if ($result->error == false) {
            // only update nickname if necessary
            if ($updateNickname) {
                $sql = "UPDATE " . USERS . " SET nickname='" . mysql_real_escape_string($nickname) . "' WHERE id ='" . $_SESSION['userid'] . "'";
                mysql_query($sql);
                $_SESSION['nickname'] = $nickname;
                $messages[] = "Your nickname is now '$nickname'.";
            }
        } else {
            die(json_encode($result));
        }
    }
}


$linkedin_share = isset($_POST['linkedin_status']) ? 1 : 0;

$query = "SELECT * FROM " . USERS . " WHERE id='" . $_SESSION['userid'] . "'";
$result = mysql_query($query);
$user_row = mysql_fetch_array($result);
if ($user_row['linkedin_share'] != $linkedin_share) {
    $messages[] = "Your linkedin share preference has been updated!";
    $sql_linkedin_share = "UPDATE " . USERS . " SET linkedin_share='$linkedin_share' WHERE id ='" . $_SESSION['userid'] . "'";
    mysql_unbuffered_query($sql_linkedin_share);
}



$smsConfirmed = false;
if (SMS_ENABLED) {


    $qry = "SELECT * FROM " . USERS . " WHERE id='" . $_SESSION['userid'] . "'";
    $rs = mysql_query($qry);
    $user_row = mysql_fetch_array($rs);

    // user has provided confirmation code from phone - confirm user phone number
    if(!empty($_POST['confirmation'])){

            if ($user_row['confirm_phone'] == strtoupper(trim($_POST['confirmation']))) {
                mysql_query("update ".USERS." SET confirm_phone='' where id='".$user_row['id']."'");
                $messages[] = "Your phone ({$user_row['phone']}) has been confirmed.  Thank you!";
                $smsConfirmed = true;
            }else{
                $front->getError()->setError('Wrong phone confirmation code.');
            }
    // check if user has made changes to phone info - if so - update data and issue new confirmation code
    }else{

        $allow_email_sendlove = isset($_POST['allow_email_sendlove']) ? 'Y' : 'N';

        if ($user_row['send_love_via_email'] != $allow_email_sendlove) {
            $messages[] = "Your email preference has been updated!";
            $sqlallow_email_sendlove = "UPDATE " . USERS . " SET send_love_via_email='$allow_email_sendlove' WHERE id ='" . $_SESSION['userid'] . "'";
            mysql_unbuffered_query($sqlallow_email_sendlove);
        }

        $phone = (isset($_POST['phone']) ? mysql_real_escape_string($_POST['phone']) : '');
        $country = (isset($_POST['country']) ? mysql_real_escape_string($_POST['country']) : '');
        $provider = (isset($_POST['provider']) ? mysql_real_escape_string($_POST['provider']) : '');
        if ($provider == '--' && isset($_POST['smsaddr'])) {
            $provider = "+" . mysql_real_escape_string($_POST['smsaddr']);
        }
        $smsset = ", country='$country', provider='$provider' ";
        if ($phone != $user_row['phone'] || $country != $user_row['country'] || $provider != $user_row['provider']){
            $lexicon = SMS_CODE_LEXICON;
            $confirm_code = $lexicon[rand(0, 19)] . $lexicon[rand(0, 19)] . $lexicon[rand(0, 19)] . $lexicon[rand(0, 19)];
            if( ! empty($phone)){
                $messages[] = "Love will now be sent to your phone ($phone).  Please enter confirmation text when you receive it.";
            } else {
                $messages[] = "Love will no longer be sent to your phone.";
            }

            $sql = "UPDATE " . USERS . " " . "SET phone='$phone', confirm_phone='$confirm_code' " . $smsset . "WHERE id ='" . $_SESSION['userid'] . "'";
            mysql_query($sql);
        }

        if  (! empty($_POST['phone_edit']) && ! empty($phone) && ! empty($country) && ! empty($provider) && ! empty($confirm_code)){
            sl_send_phone_confirm_sms($_SESSION['userid'], $phone, $country, $provider, $confirm_code);

        // check if user has confirmed his number before even if we update something else
        // fixes appearing of sms field on updating other info
        }elseif(empty($user_row['confirm_phone'])){
            $smsConfirmed = true;
        }
    }

}

if ($front->getError()->getErrorFlag() == 1) {
    foreach ($front->getError()->getErrorMessage() as $message) {
        $messages[] = $message;
    }
}

$changes = array();
if (! empty($messages)) {
    
    $to = $_SESSION['username'];
    $changes = '';
    foreach($messages as $msg){
        $changes .= "&nbsp;&nbsp;$msg<br/>";
    }

    if (LOVE_SETTINGS_UPDATE_EMAIL) {
        if (! $front->getError()->getErrorFlag()) {
            sendTemplateEmail($to, 'changed_settings', array('app_name' => APP_NAME, 'changes' => $changes));
        }
    }
}

if (! $front->getError()->getErrorFlag()) {
    echo json_encode(array(
        'error' => 0, 
        'message' => $changes,
        'redirect' => false,
        'smsConfirmed' => $smsConfirmed,
    ));
} else {
    echo json_encode(array(
        'error' => 1, 
        'message' => $front->getError()->getErrorMessage(),
        'redirect' => false
    ));
}
