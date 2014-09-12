<?php
//
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
//

$app_root_path = dirname(__FILE__)."/";

require_once($app_root_path.'html2text.inc');
require_once($app_root_path.'helper/authmail.php');

// email templates
require_once($app_root_path."email/en.php");



/*  sl_send_email
 *
 */
function sl_send_email($to, $subject, $html, $plain=null, $mailq='gmail-ssl') {
    if (empty($to)) return false;

    $hash = md5(date('r', time()));

    if (!empty($html)) {
        if (empty($plain)) {
            $h2t =new html2text($html, 75);
            $plain = $h2t->convert();
        }

        $headers["Content-Type"]="multipart/alternative; boundary=\"PHP-alt-$hash\"";
        $body = "
--PHP-alt-$hash
Content-Type: text/plain; charset=\"iso-8859-1\"
Content-Transfer-Encoding: 7bit

".$plain."

--PHP-alt-$hash
Content-Type: text/html; charset=\"iso-8859-1\"
Content-Transfer-Encoding: 7bit

".$html."

--PHP-alt-$hash--";
    } else {
        $body = $plain;
    }

    send_authmail(array('sender'=>'loveuser','server'=>$mailq),$to,$subject,$body,$headers);

    return true;
}

/*  sendTemplateEmail - send email using email template
 *  $template - name of the template to use, for example 'confirmation'
 *  $data - array of key-value replacements for template
 */ 

function sendTemplateEmail($to, $template, $data){
    $mailq='';
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

    $resutl = null;
    foreach($recipients as $recipient){

    if(!empty($emailTemplates[$template]['mailq'])) { $mailq=$emailTemplates[$template]['mailq']; };
        $result = sl_send_email($recipient, $subject, $html, $plain,$mailq);
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
            // escape dollar signs first
            $replacement = preg_replace("!" . '\x24' . "!" , '\\\$' , $replacement);
            $templateIndice = preg_replace($pattern, $replacement, $templateIndice);
        }
    }

    return $templateData;
}

