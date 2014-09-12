<?php

//  Copyright (c) 2009-2010, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

// The sendlove() functionality split out into its own function so others can call it
// Enforce the rate limit and other restrictions, return JSON for results

include_once("config.php");
require_once("class.session_handler.php");
include("helper/check_session.php");
include("class/LoveUser.class.php");
$user = new LoveUser();
include_once("functions.php");
include_once("send_email.php");
// this version of the function is good if you only have the senders email address
function fromemail_sendllove_toanother($fromEmail, $toArg, $forArg, $priv)
{
  $res = mysql_query("select * from ".USERS." where username='".mysql_real_escape_string($fromEmail)."'");
  $user_row = (($res) ? mysql_fetch_assoc($res) : null);
  if (empty($user_row)) return;

  $userid = $user_row['id'];
  $username = $user_row['username'];
  $nickname = $user_row['nickname'];
  $features = intval($user_row['features']) & FEATURE_USER_MASK;
  $isSuper = isEnabled(FEATURE_SUPER_ADMIN);

  return sendlove_toanother($userid, $username, $nickname, $isSuper, $toArg, $forArg, $priv);
}

// this version of the function assumes you have all the args

function sendlove_toanother($userid, $username, $nickname, $isSuper, $toArg, $forArg, $priv)
{
    // UTF-8 Encode passed parameters to preserve non-latin characters.
    $username = setEncoding($username);
    $nickname = setEncoding($nickname);
    $toArg = setEncoding($toArg);
    $forArg = setEncoding($forArg);

  if (enforceRateLimit('love', $userid)) {
    error_log("User ".$userid." send love was rate limited.");
    return 'ratelimit';
  }
  
  
  // Only super admins can send love to the guest account
  $to = mysql_real_escape_string(strtolower(trim($toArg)));
  if ($to == GUEST_USER && !$isSuper) {
    return 'guest';
  }

  //Can't send love to self
  if ($to == $username) { 
    return 'self'; 
  }
  
  $sqlView="SELECT company_id, skill, team FROM ".USERS." WHERE id='".$userid."'";
  $resView=mysql_query($sqlView);
  $rowView=mysql_fetch_array($resView);
  $company_id = $rowView['company_id'];
  
  $skill = $rowView['skill'];
  $team = $rowView['team'];
  
  $sqlView="SELECT company_id FROM ".USERS." WHERE username = '".$to."'and removed = 0";
  $resView=mysql_query($sqlView);
  $rowView=mysql_fetch_array($resView);
  $to_company = $rowView['company_id'];
  $company = ($company_id == $rowView['company_id']) ? ", company_id='".$company_id."'" : "";
  $private = ($priv)?',private=1':'';
  
  //$allowed_tags = array();  // no tags are currently allowed in the 'forArg'
  $for = $forArg; //strip_tags($forArg);

  // this sends the actual email
  if (!sl_send_love($username, $nickname, $userid, $company_id, $to, $for, false, $priv)) {
    // false from sl_send_love means the user was outside the system
    return 'outside';
  }
  
  $rc = 'ok';
  $query = "insert into ".LOVE." set giver='".$username."', receiver='".addslashes($to)."', skill='$skill', team='$team', why='".addslashes($for)."', at=now()".$company.$private;
  $res = mysql_query($query);
  
  // See if the recipient is has a facebook id, if so we'll return a value so it can be handled.
  $resfb=mysql_query("select id, fb_id from ".USERS." where username = '".mysql_real_escape_string($to)."'");
  if(mysql_num_rows($resfb) > 0) {
    $rowfb = mysql_fetch_assoc($resfb);
    $fb_id = $rowfb['fb_id'];
    if (!empty($fb_id)) {
      $rc = array('facebook', $to, $for, $fb_id);
    }
  }
  if ($company_id == $to_company && $company_id == JOURNAL_API_COMPANY &&!$priv) {
    $toNickname = getNickName($to);
    if (empty($toNickname)) $toNickname = $to;
    
    $for = stripslashes($for);
    
    $data = array();
    $data['user'] = JOURNAL_API_USER;
    $data['pwd'] = sha1(JOURNAL_API_PWD);
    $data['message'] = $nickname . " to $toNickname: $for";
    $prc = postRequest(JOURNAL_API_URL, $data);
  }
  return $rc;
}
