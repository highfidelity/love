<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
//

//ini_set('display_errors', 0);
//Include PEAR:Mail
require_once "Mail.php";

//send_authmail(array('server'=>'localhost','sender'=>'authuser'),$to,$subject,$body,'');


function send_authmail($auth,$to,$subject,$body,$headers) {
 if (!is_array($auth) || (!empty($headers) && !is_array($headers)) || empty($to) || empty($body)) {
  error_log("failing send_auth: ".json_encode(array('auth'=>$auth,'to'=>$to,'subject'=>$subject,'headers'=>$headers))."\n".$body);
  return false;
 }

global $mail_auth;
global $mail_user;

//set local variables for auth[server] and auth[sender] and test [0] if no match
//fail if [0] no match

if (!empty($mail_auth)) {reset($mail_auth);}
if (!empty($mail_user)) {reset($mail_user);}

if (!isset($headers['From'])) {
  if (!empty($mail_user[$auth['sender']]['from'])) {
    $headers['From'] = $mail_user[$auth['sender']]['from'];
  } else if (!empty($mail_user[key($mail_user)]['from'])) {
    $headers['From'] = $mail_user[key($mail_user)]['from'];
  } else {
    error_log("From not defined, exiting");
    exit;
  }
}
$oldheaders="From: ".$headers['From']."\n";;

if (!isset($headers['Reply-To'])) {
  if (!empty($mail_user[$auth['sender']]['replyto'])) {
    $headers['Reply-To'] = $mail_user[$auth['sender']]['replyto'];
    $oldheaders.="Reply-To: ".$headers['Reply-To']."\n";
  } else if (!empty($mail_user[key($mail_user)]['replyto'])) {
    $headers['Reply-To'] = $mail_user[key($mail_user)]['replyto'];
    $oldheaders.="Reply-To: ".$headers['Reply-To']."\n";;
  } else if (!empty($headers['From'])) {
    $headers['Reply-To'] = $headers['From'];
  }
} else {
  $oldheaders.="Reply-To: ".$headers['Reply-To']."\n";;
}
if (!isset($headers['Subject'])) {
  if (!empty($subject)) {
    $headers['Subject'] = $subject;
  } else if (!empty($mail_user[$auth['sender']]['subject'])) {
    $headers['Subject'] = $mail_user[$auth['sender']]['subject'];
    $oldheaders.="Subject: ".$headers['Subject']."\n";;
  } else if (!empty($mail_user[key($mail_user)]['subject'])) {
    $headers['Subject'] = $mail_user[key($mail_user)]['subject'];
    $oldheaders.="Subject: ".$headers['Subject']."\n";;
  }
} else {
  $oldheaders.="Subject: ".$headers['Subject']."\n";;
}
if (!isset($headers['Content-Type'])) {
  if (!empty($mail_user[$auth['sender']]['Content-Type'])) {
    $headers['Content-Type'] = $mail_user[$auth['sender']]['Content-Type'];
    $oldheaders.="Content-Type: ".$headers['Content-Type']."\n";;
  } else if (!empty($mail_user[key($mail_user)]['Content-Type'])) {
    $headers['Content-Type'] = $mail_user[$auth[key($auth)]]['Content-Type'];
    $oldheaders.="Content-Type: ".$headers['Content-Type']."\n";;
  }
} else {
  $oldheaders.="Content-Type: ".$headers['Content-Type']."\n";;
}
  

if (isset($auth['server']) && isset($mail_auth[$auth['server']])) {
  $smtpauth=$mail_auth[$auth['server']];
} else if (isset($mail_auth['localhost'])) {
  $smtpauth=$mail_auth['localhost'];
} else {
  $smtpauth = array ('host'=>'localhost','auth'=>false);
  @mail($to,$subject,$body,$oldheaders);
  return;
}

if (isset($smtpauth['host']) and preg_match('/[A-Za-z]/',$smtpauth['host'])) {
   $smtpauth['host']=gethostbyname($smtpauth['host']);
}

    if (class_exists('Mail')) {
        $smtp = Mail::factory('smtp',$smtpauth);
        $mail = $smtp->send($to, $headers, $body);
        //@$smtp->send($to, $headers, $body);

        // This code is bogus, $mail isn't set any more
        if (PEAR::isError($mail)) {
            error_log("smtpauth: host ".$smtpauth['host']);
            error_log(PEAR::isError($mail));
            error_log($mail->getMessage());
         }
    }

}

