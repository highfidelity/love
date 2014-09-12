<?php

$emailTemplates = array(

'reminder1' => array(

'subject' =>
"Your Love Instance is about to expire",

'body' =>
"<p>You are only one click away from completing your registration with SendLove!</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"{domain}\">Click here to verify your email address and activate your account.</a></p>
<p>Love,
<br/>The LoveMachine</p>",

'plain' =>
"You are only one click away from completing your registration with SendLove!\n\n
Click the link below or copy into your browser's window to verify your email address and activate your account.\n
    {domain}\n\n
Love,\n
The LoveMachine",

),
'reminder3' => array(

'subject' =>
"Your Love Instance is about to expire",

'body' =>
"<p>You are only one click away from completing your registration with SendLove!</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"{domain}\">Click here to verify your email address and activate your account.</a></p>
<p>Love,
<br/>The LoveMachine</p>",

'plain' =>
"You are only one click away from completing your registration with SendLove!\n\n
Click the link below or copy into your browser's window to verify your email address and activate your account.\n
    {domain}\n\n
Love,\n
The LoveMachine",

),
'reminder7' => array(

'subject' =>
"Your Love Instance is about to expire",

'body' =>
"<p>You are only one click away from completing your registration with SendLove!</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"{domain}\">Click here to verify your email address and activate your account.</a></p>
<p>Love,
<br/>The LoveMachine</p>",

'plain' =>
"You are only one click away from completing your registration with SendLove!\n\n
Click the link below or copy into your browser's window to verify your email address and activate your account.\n
    {domain}\n\n
Love,\n
The LoveMachine",

),
/*
Change In Campaign
	used in: Campaign.class.php

replacement data:
    periodInfo - list of the recognition periods that have been changed
	changeInfo - details about the change
*/
'changeInCampaign' => array(
	'subject' => 'Recognition period(s) payment status change',
	'body' => '<p>Here, you have the list of the recognition periods updated :</p>
    {periodInfo}
    <p>The new payment status on the previous list of periods is available here:</p>
    <div style="color: #669;padding: 28px;"; >
        {changeInfo}
    </div>
        <p>Love,
	<br />The LoveMachine</p>',
),
);