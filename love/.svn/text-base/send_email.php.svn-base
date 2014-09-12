<?php
//
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
//
if ($_SERVER['PHP_SELF']==__FILE__) die("Invalid request"); 
require_once("Akismet.class.php");
require_once('html2text.inc');
require_once('helper/authmail.php');

require_once 'lib/Sms/Numberlist.php';
$provider_list = Sms_Numberlist::$providerList;

// email templates
require_once(dirname(__FILE__) . "/email/en.php");

/* sl_block_user
 *
 * Checks for spammy users and users of anonymous email services.  Such folks are not welcome.
 */
function sl_block_user($email)
{
    /* Block known spam */
    if (sl_is_spam($email)) return true;

    /* Block invalid domains */
    $parts = split("@", $email);
    $domain = $parts[1];
    if (empty($domain)) return true;

    /* Block anything in the anonymous domain list */
    $anon = file_get_contents('anonlist.txt');
    return strpos($anon, $domain) !== false;
}

/* sl_confirm_company_email
 *
 * Validates a company approval link and if so confirms the user in the company.
 *
 * ASSUMPTIONS: DB has been initialized, user is authorized
 */
function sl_confirm_company_email($admin, $userid, $token)
{
    /* Get the user record for the admin
     */
    $res = mysql_query("select id, company_id, company_admin from ".USERS." where username='".mysql_real_escape_string($admin)."'");
    $admin_row = mysql_fetch_assoc($res);

    /* Fail if the 'admin' isn't a valid user or isn't actually an admin.
     */
    if (!$admin_row || !$admin_row['company_admin']) return 'NOT_ADMIN';

    /* Get the user record for the user
     */
    $res = mysql_query("select username,company_id,company_confirm from ".USERS." where id='".mysql_real_escape_string($userid)."'");
    $user_row = mysql_fetch_assoc($res);
    if (!$user_row) return 'NOT_USER';
    
    /* Fail if the user has no company or if their company is different than the admin's.
     */
    if ($user_row['company_id'] != $admin_row['company_id'] || $user_row['company_confirm'] != 0) return 'NOT_COMPANY';

    /* Verify that the confirmation link wasn't forged.
     */
    if ($token != urlencode(sha1(SALT.$user_row['username']))) return 'INVALID_TOKEN';
    
    /* Confirm the user in the company.
     */
    mysql_query("UPDATE ".USERS." SET company_confirm='".$admin_row['id']."' WHERE id=".mysql_real_escape_string($userid));
    
    // Send an email to the user to confirm his email address
    $to = $user_row['username'];
    $confirmUrl = SECURE_SERVER_URL . "confirmation.php?cs=".base64_encode($token)."&str=" . base64_encode($to);
    sendTemplateEmail($to, 'confirmation', array('url' => $confirmUrl));

    return 'OK';
}

function sl_ping_company_admin($company_id, $name = '', $email = '', $message = '')
{
	// Get company name
	$query = 'SELECT `name` FROM `' . COMPANY . '` WHERE `id` = ' . (int)$company_id . ';';
	$result = mysql_query($query) or error_log("slPingCom-1: $query ".mysql_error());
	$company = mysql_fetch_assoc($result) or error_log("slPingCom-2: $query ".mysql_error());
	// Get administrators
	$query = 'SELECT `username` FROM `' . USERS . '` WHERE `company_admin` = 1 AND `company_id` = ' . (int)$company_id . ';';
	$result = mysql_query($query) or error_log("slPingCom-3: $query ".mysql_error());
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)) {
			sendTemplateEmail($row['username'], 'ping_admin', array('url' => 'https://' . SERVER_NAME . '/admin/login', 'user_name' => $name, 'user_email' => $email, 'company_name' => $company['name'], 'message' => $message)); 
		}
	}
	return true;
}

function sl_ping_contact($tenant = '', $name = '', $email = '', $message = '') {
    sendTemplateEmail('contact@lovemachineinc.com', 'ping_contact', array('user_name' => $name, 'user_email' => $email, 'company_name' => $tenant, 'message' => $message)); 
    return true;
}

/* sl_confirm_company_invitation
 *
 * Validates a company invitation link and if so confirms the user in the company.
 *
 * ASSUMPTIONS: DB has been initialized, user is authorized
 */
function sl_confirm_company_invitation($username, $company_id, $invitor_id, $asAdmin, $token)
{
    /* Verify that the invitation link wasn't forged.
     */
    if ($token != urlencode(sha1(SALT."$company_id/$invitor_id/$asAdmin"))) return false;

    /* Update the user's record with the new company, admin status, and invitor.
     */
    $qry="update ".USERS." SET confirmed='1', company_id='".$company_id."', company_admin='".$asAdmin."', company_confirm='".$invitor_id."' where username='".mysql_real_escape_string($username)."'";
    mysql_query($qry);

    return true;
}

/*  sl_send_email
 * 
 *  Check using Akismet if mail is probably spam, otherwise send an email 
 */
function sl_send_email($to, $subject, $html, $plain=null, $attachment=null) {
    if (empty($to)) return false;

    $hash = md5(date('r', time()));
    $headers['From']  = 'SendLove <love@sendlove.us>';
    $headers['To']    = $to;
    if (!empty($html)) {
        if (empty($plain)) {
            $h2t =new html2text($html, 75);
            $plain = $h2t->convert();
        }

        $headers["Content-Type"]="multipart/alternative; boundary=\"PHP-alt-$hash\"";
        $body = "
--PHP-alt-$hash
Content-Type: text/plain; charset=\"utf-8\"
Content-Transfer-Encoding: 7bit

".$plain."

--PHP-alt-$hash
Content-Type: text/html; charset=\"utf-8\"
Content-Transfer-Encoding: 7bit

".$html."

--PHP-alt-$hash--";
        if ($attachment != null && !(empty($attachment['name'])) && !(empty($attachment['content'])) ) {
            $headers["Content-Type"]="multipart/mixed; boundary=\"PHP-mixed-$hash\"";
            //encode it with MIME base64,
            //and split it into smaller chunks
            $attachmentContent = chunk_split(base64_encode($attachment['content']));
            $body = "
--PHP-mixed-$hash
Content-Type: multipart/alternative; boundary=\"PHP-alt-{$hash}\"

" . $body .
"
--PHP-mixed-{$hash}  
Content-Type: {$attachment['type']}; name=\"{$attachment['name']}\"  
Content-Transfer-Encoding: base64  
Content-Disposition: attachment  

{$attachmentContent}
--PHP-mixed-{$hash}-- 
";
         }
    } else {
        $body = $plain;
    }

    send_authmail(array('sender'=>'authuser','server'=>'gmail-ssl'),$to,$subject,$body,$headers);

    return true;
}

/*  sendTemplateEmail - send email using email template
 *  $template - name of the template to use, for example 'confirmation'
 *  $data - array of key-value replacements for template
 */ 

function sendTemplateEmail($to, $template, $data){

    $recipients = is_array($to) ? $to : array($to);
    global $emailTemplates;

    $replacedTemplate = !empty($data) ?
                        templateReplace($emailTemplates[$template], $data) :
                        $emailTemplates[$template];

    $subject = $replacedTemplate['subject'];
    $html = $replacedTemplate['body'];
    $attachment = isset($replacedTemplate['attachment']) ? $replacedTemplate['attachment'] : null;
    $plain = isset($replacedTemplate['plain']) ? $replacedTemplate['plain'] : null;

    $result = null;
    foreach($recipients as $recipient){
        $result = sl_send_email($recipient, $subject, $html, $plain, $attachment );
    }

    return $result;
}

/* templateReplace - function to replace all occurencies of 
 * {key} with value from $replacements array
 * for example: if $replacements is array('nickname' => 'John')
 * function will replace {nickname} in $templateData array with 'John'
 */

function templateReplace($templateData, $replacements){

    foreach($templateData as &$templateIndice){
        foreach($replacements as $find => $replacement){

            $pattern = array(
                        '/\{' . preg_quote($find) . '\}/',
                        '/\{' . preg_quote(strtoupper($find)) . '\}/',
                            );
            $templateIndice = preg_replace($pattern, $replacement, $templateIndice);
        }
    }

    return $templateData;
}


/*  sl_send_feedback
 */
function sl_send_feedback($sender, $subject, $message) {

    sendTemplateEmail(FEEDBACK_EMAIL, 'feedback', array(
                                                        'app_name' => APP_NAME,
                                                        'instance' => COMPANY_NAME.".".APP_NAME,
                                                        'sender' => $sender,
                                                        'message' => $message,
                                                        ));
    return true;
}

/*  sl_send_love
 * 
 *  Check using Akismet if mail is probably spam, otherwise send an email 
 */
function sl_send_love($from_username, $from_nickname, $from_id, $company_id, $to, $for, $check_spam=true, $private=false, $firstlove = false) {

    $from = !empty($from_nickname) ? $from_nickname: $from_username;
    if ($check_spam && sl_is_spam($from_username, $for)) return false;

    $newUser = false;
    $query = "SELECT id, nickname, phone, confirm_phone, country, provider, send_love_via_email FROM ".USERS." WHERE username = '".mysql_real_escape_string($to)."'";
    $res = mysql_query($query);
    if (!$res || mysql_num_rows($res) == 0) { 
        // user could not be found, check if we allow to send love outside instance
        if ((SEND_LOVE_OUTSIDE_INSTANCE === false) && ($firstlove === false)) {
            return false;
        }

        $newUser = true;
    } else {
        $row = mysql_fetch_assoc($res);
        $nickname = !empty($row['nickname'])?$row['nickname']:$to;
        $loveinbox = $row['send_love_via_email'];
        $phone = $row['phone'];
        $confirmsms = $row['confirm_phone'];
        $country = $row['country'];
        $provider = $row['provider'];
    }


    // Send a normal email message
    $token = urlencode(sha1(SALT."$company_id/$from_id/0"));

    // available templates:
    // love_email_old - for registered users
    // love_template_new - for new users
    // love_email_old_private, love_email_new_private - private versions

    $privateString = '';
    if ($private == true){
        $privateString = '_private';
    }

    $status = 'old';

    $summary = getLastMonthLoveSummary($to);
    $loveReceived = 0;
    if($summary) {
        $loveReceived  = $summary['love_received'];
    }

    $statsHtml = '';
    $statsPlain = '';

    if ($loveReceived > 1) {
        // This person has received love in the past. Show the details
        $message = "Your last few loves: ";
        $statsHtml .= "<br/>" . $message;
        $statsPlain .= "\n" . $message;

        $query = "SELECT TIMESTAMPDIFF(SECOND,NOW(),at) as delta, CASE when usr.nickname is null then lv.giver else usr.nickname END as sender, lv.why ".
                    " FROM " . LOVE . " lv " .
                    " LEFT OUTER JOIN " . USERS . " as usr ON (lv.giver = usr.username) " .
                    " WHERE lv.receiver = '" . addslashes($to). "' ORDER BY lv.at desc limit 3";

        $result = mysql_query($query);
        $detail = array();
        $count = $result ? mysql_num_rows($result) : 0;
        if($count > 0) {
            while($row = mysql_fetch_array($result)) {
            $time = relativeTime($row['delta'], 1);
            $message = $row['sender'] .", " . $time . " : " . $row['why'];
            $statsHtml .= "<br/>&nbsp;&nbsp;&nbsp;&nbsp;" . $message;
            $statsPlain .= "\n    " . $message;
            }
            $statsHtml .= "<br/>";
            $statsPlain .= "\n";
        }

        $message = "In the last 30 days, " . $summary['senders'] . " people have sent you love " . $loveReceived . " times.";
        $statsHtml .= "<br/><br.>" . $message;
        $statsPlain .= "\n\n". $message;
    }

    if ($newUser) {
        $url = "http://www.lovemachineinc.com/trial/";
        $status = 'new';
    } else {
        $url = SECURE_SERVER_URL . "tofor.php";
    }

    //Allow disabling of inbound email love in case of spamming (can later be expanded to allow signed/tokened messages or private addresses)
    if ($loveinbox!=='N') {
    //$statsHtml .= "<br/>Now! Send Love via Email: ".LOVEMAIL."<br/>Subject: $nickname {$row['why']}<br/> Subject: ".$nickname." ".$for."<br/>";
    //$statsPlain .= "\nNow! Send Love via Email: ".LOVEMAIL."\n Subject: $nickname {$row['why']}\n Subject: ".$nickname." ".$for."\n";
    }

    sendTemplateEmail($to, "love_email_$status" . $privateString,
                                                            array(
                                                                'sender_nickname' => $from,
                                                                'for' => $for,
                                                                'url' => $url,
                                                                'stats_html' => $statsHtml,
                                                                'stats_plain' => $statsPlain,
                                                            ));


    // check if we are set up to send sms
    if(!empty($phone) && empty($confirmsms) && !empty($country) && !empty($provider)){
        global $provider_list;

        // Send an SMS message
        if (!empty($provider) && !empty($provider_list[$country][$provider])) {
            if ($provider{0} != '+') {
                $smsaddr = str_replace('{n}', $phone, $provider_list[$country][$provider]);
            } else {
                $smsaddr = substr($provider, 1);
            }
            
            $sms = "Love from $from: $for";
            if ($private == true) {
                $sms .= ' (sent quietly)';
            }
            send_authmail(array('sender'=>'smsuser','server'=>'gmail-ssl'),$smsaddr,'',$sms,'');
        }
    }

    return true;
}

/* sl_send_company_confirm_email
 *
 * Checks to see if user (id) needs to have company membership confirmed.
 * If so, sends an email to the company admin with an approval link.
 *
 * Returns an array containing;
 *   - 'OK', and on of 'NO_COMPANY', 'IS_ADMIN', 'CONFIRMED', 'SENT', or
 *   - 'ERRROR", and a text message describing the error.
 *
 * ASSUMPTIONS: DB has been initialized, user is authorized
 */
function sl_send_company_confirm_email($id) {
    $company = '';

    $res = mysql_query("select username, company_id, company_admin, company_confirm  from ".USERS." where id='".$id."'");
    if ($row = mysql_fetch_assoc($res)) {
        /* Confirmation is not necessary if:
         *  The user has already been confirmed.
         */
        if (!empty($row['company_confirm'])) {
            return array('OK','CONFIRMED');
        }
        $user = $row['username'];
        $company_id = $row['company_id'];
        $is_admin = $row['company_admin'];

        /* Get the name of the company so we can include it in the email.
         */
        $res = mysql_query("select name from ".COMPANY." where id='".$company_id."'");
        if ($row = mysql_fetch_assoc($res)) {
            $company = $row['name'];
        } else {
            return array('ERROR', 'Could not load company record.');
        }

        /*  The user is the company admin, we don't need to send an confirmation (but we do want the company name).
         */
        if ($is_admin) {
            return array('OK','IS_ADMIN', $company);
        }

        /* Get the company admin user record.
         */
        $res = mysql_query("select username from ".USERS." where company_id='".$company_id."' and company_admin>='1'");
        if ($row = mysql_fetch_assoc($res)) {

            $confirmUrl = SECURE_SERVER_URL . "confirmation.php?company=1&id=$id&token=" . urlencode(sha1(SALT . $user));

            /* Send an approval email to each admin.
             */
            $admins = array();
            do {
                $admins[] = $row['username'];
            } while ($row = mysql_fetch_assoc($res));

            sendTemplateEmail($admins, 'join_request', array(
                                                        'sender_nickname' => $user,
                                                        'company_name' => $company,
                                                        'url' => $confirmUrl,
                                                        ));

        } else {
            return array('ERROR', 'Could not load company admin user record.');
        }
    } else {
        return array('ERROR', 'Could not load user record.');
    }

    return array('OK', 'SENT', $company);
}

/* sl_send_invitation
 *
 * Sends an invitation email to a user.  Handles cases where the user is already
 * a member of a company, is a member of another company, and will become either
 * a regular user or admin.
 *
 * Returns true if an invitation was sent.
 *
 * ASSUMPTIONS: DB has been initialized, user is authorized
 */
function sl_send_invitation($invitor, $invitor_nn, $invitor_id, $invitee, $company_name, $company_id, $asAdmin) {
    $ctxt = "";
    if ($company_id && !empty($company_name)) {
        $ctxt = " $company_name on";
    }

    $subject = "SendLove: Company join request from ".$invitee;

    $asAdmin = ($asAdmin)?"1":"0";
    $page = "confirmation.php";
    $token = urlencode(sha1(SALT."$company_id/$invitor_id/$asAdmin"));

    $sqlView="SELECT company_id, company_admin FROM ".USERS." WHERE username = '".mysql_real_escape_string($invitee)."'";
    $resView=mysql_query($sqlView);
    $invite_row = mysql_fetch_array($resView);


    // available templates
    // invite_admin - invite user to be administrator
    // invite_user - invite regular user
    // invite_switch - invite user to switch companies

    $inviteType = '';
    if (   !$invite_row || $invite_row['company_id'] == 0
        || ($invite_row['company_id'] == $company_id && !$invite_row['company_admin'] && $asAdmin)) {

        /* The user is: 
         *  a) not a LoveMachine user, or
         *  b) is not affiliated with another company, or
         *  c) affiliated with the same company but is being invited as an admin.
         */
        $invite = 1;
        if (!$invite_row) {
            $page = "signup.php";
        }
        if ($asAdmin) {
            $inviteType = 'admin';
        } else {
            $inviteType = 'user';
        }
    } else if ($invite_row['company_id'] != $company_id) {
        /* The user is affiliated with another company */
        $invite = 2;
        $inviteType = 'switch';
    } else {
        /* The user is already affiliated with the company */
        return false;
    }

    $joinUrl = SECURE_SERVER_URL . "$page?invite=$invite&cid=$company_id&iid=$invitor_id&admin=$asAdmin&token=$token";

    return sendTemplateEmail($invitee, 'invite_' . $inviteType,
                                                        array(
                                                            'invitor_nickname' => $invitor_nn,
                                                            'invitor_email' => $invitor,
                                                            'company_name' => $company_name,
                                                            'url' => $joinUrl,
                                                            ));
}

/* sl_send_phone_confirm_sms
 *
 * Sends a confirmation SMS email to a user when they change their phone number.
 */
function sl_send_phone_confirm_sms($id, $phone, $country, $provider, $code)
{
    global $provider_list;

    if ($country != '--' && $provider{0} != '+') {
        $smsaddr = str_replace('{n}', $phone, $provider_list[$country][$provider]);
    } else {
        $smsaddr = substr($provider, 1);
    }
    $sms = 'This is your SendLove confirmation code: '.$code;

    send_authmail(array('sender'=>'smsuser','server'=>'gmail-ssl'),$smsaddr,'',$sms,'');
}

/* sl_is_spam
 *
 * Uses the PHP5 Akismet library Alex Potsides to check for spam content. The 'optional' third
 * argument is an array of optional values to pass to Akismet.  Some of these values are merely
 * optional others can be used to override values the Akismet library fills in automatically.
 *
 * Usage:
 *    $isSpam = sl_is_spam('user@example.com', 'Sample text', array('nickname'=>'bozospammer',
 *                 'permalink'=>'url-for-some-page-on-send-love.com');
 */
function sl_is_spam($email, $content=null, $optional=array())
{
    $optargs = array(
        'ip'=>'setUserIP', 'nickname'=>'setCommentAuthor', 'permalink'=>'setPermalink',
         'referrer'=>'setReferrer', 'type'=>'setCommentType');

    $akismet = new Akismet(AKISMET_URL, AKISMET_KEY);
    $akismet->setCommentAuthorEmail($email);
    if (!empty($content)) {
        $akismet->setCommentContent($content);
    }
    // User-agent, IP, and referrer are automatically set by the Akismet class, but can be overriden

    foreach ($optional as $optarg=>$val) {
        if (in_array($optarg, $optargs)) {
            $akismet->{$optargs[$optarg]}($val);
        }
    }

    try {
        $isSpam = $akismet->isCommentSpam();
    } catch (Exception $e) {
        $isSpam = false;
            error_log('Akismet exception: ',  $e->getMessage(), "\n");
    }
    return $isSpam;
}

/**
* Get Last month Love Summary for a given receiver. Used while sending love email.
*
* @receiver Receiver's username (email)
* @loveCount Count of love to retrieve
*/
function getLastMonthLoveSummary($receiver) {
    $query = "SELECT count(distinct giver) senders, COUNT(*) as loveCount FROM ".LOVE." WHERE at >= NOW() - INTERVAL 1 MONTH AND receiver = '" . addslashes($receiver). "'";  
    $result = mysql_query($query);
    
    $row = mysql_fetch_array($result);
    
    $summary['love_received'] =  $row['loveCount'];
    $summary['senders'] =  $row['senders'];
    return $summary;
}

