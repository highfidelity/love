<?php
//
//  Copyright (c) 2009-2010, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
//

require_once('authmail.php');
require_once('html2text.inc');

// email templates
require_once(dirname(__FILE__) . "/email/en.php");
/*  sl_send_email
 * 
 *  Check using Akismet if mail is probably spam, otherwise send an email 
 */
function sl_send_email($to, $subject, $html, $plain=null, $attachment=null) {
    if (empty($to)) return false;

    $hash = md5(date('r', time()));
    $headers['From']  = 'The LoveMachine <love@sendlove.us>';
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

function success_notify($name, $email, $company, $street, $city, $state, $zip, $country, $phone, $lm_description, $amt, $invoice, $txn_id) {

/*******************************************************************************************************
*	consolidated the messages per 11729
*	
*    $msg = '';
*    $msg .= '<p>Success! You are now LoveMachine enabled!</p>';
*    $msg .= '<p>A member of our team will be contacting you shortly to setup your service.</p>';
*    $msg .= '<p>Here is your purchase info:<br />';
*    $msg .= 'Name          : '. $name .'<br />';
*    $msg .= 'Company       : '. $company .'<br />';
*    $msg .= '# of Users    : '. $user_count .'<br />';
*    $msg .= 'Invoice #     : '. $invoice .'<br />';
*    $msg .= 'Purchase Price: $'. $amt .' for one month subscription</p>';
*    $msg .= '<p>You can also contact us at <a href="sales@lovemachineinc.com">sales@lovemachineinc.com</a></p>';
*
*    sl_send_email($email, 'You Are Now LoveMachine Enabled!', $msg);
*/   

    $msg = '';
    $msg .= '<p>Success! Your Recognition period has been funded !</p>';
    $msg .= '<p>Here is the purchase info:<br />';
    $msg .= 'Invoice #     : '. $invoice .'<br />';
    $msg .= 'PayPal TXN ID : '. $txn_id.'<br />';
    $msg .= 'Name          : '. $name .'<br />';
    $msg .= 'Company       : '. $company .'<br />';
    $msg .= 'Address       : '. $street .'<br />';
    $msg .= 'City          : '. $city .'<br />';
    $msg .= 'State         : '. $state .'<br />';
    $msg .= 'Country       : '. $country .'<br />';
    $msg .= 'Zip           : '. $zip .'<br />';
    $msg .= 'Email         : '. $email .'<br />';
    $msg .= 'Phone         : '. $phone .'<br />';
    $msg .= 'Recognition Period(s) : <br/>'. urldecode($lm_description) .'<br />';
    $msg .= 'Purchase Price: $'. $amt .'</p>';

    sl_send_email($email, 'Your Recognition period has been funded', $msg);
    
    $admin_msg = '<p>Success! We have a new Recognition period funded!</p>'. $msg;

    sl_send_email('sales@lovemachineinc.com', 'A New Recognition Period Has Been Funded', $admin_msg);
    //sl_send_email('support@lovemachineinc.com', $admin_msg);
    //sl_send_email('all@lovemachineinc.com', $admin_msg);
}

function fail_notify($name, $email, $company, $street, $city, $state, $zip, $country, $phone, $lm_description, $amt,   $error_msg) {

    $msg = '';
    $msg .= '<p>Error in payment !</p>';
    $msg .= '<p>Here is the error info:<br />';
    $msg .=  $error_msg .'<br />';
    $msg .= '<p>Here is the purchase info:<br />';
    $msg .= 'Name          : '. $name .'<br />';
    $msg .= 'Company       : '. $company .'<br />';
    $msg .= 'Address       : '. $street .'<br />';
    $msg .= 'City          : '. $city .'<br />';
    $msg .= 'State         : '. $state .'<br />';
    $msg .= 'Country       : '. $country .'<br />';
    $msg .= 'Zip           : '. $zip .'<br />';
    $msg .= 'Email         : '. $email .'<br />';
    $msg .= 'Phone         : '. $phone .'<br />';
    $msg .= 'Recognition Period(s) : <br/>'. urldecode($lm_description) .'<br />';
    $msg .= 'Purchase Price: $'. $amt .'</p>';

    $email = 'sales@lovemachineinc.com';
    sl_send_email($email, 'Payment error', $msg);
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
    $plain = isset($replacedTemplate['plain']) ?
                $replacedTemplate['plain'] :
                null;

    $result = null;
    foreach($recipients as $recipient){
        $result = sl_send_email($recipient, $subject, $html, $plain);
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
