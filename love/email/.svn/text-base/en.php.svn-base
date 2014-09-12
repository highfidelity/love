<?php

$emailTemplates = array(

/*
confirmationi
    email address confirmation
    used in: resend.php, signup.php

replacement data:
    url - link to email confirmation
*/

'confirmation' => array(

'subject' =>
"SendLove Registration Confirmation",

'body' =>
"<p>Thank you for trying SendLove.</p>
<p>The application is free to use for the next 30 days. At the end of that period, you will receive a usage invoice at the end of each month. The invoice is calculated based upon $5 per seat, i.e., each user in your application.</p>
<p>Before you can continue, please confirm your account by clicking the link below.</p>
<br/>
<p><a href=\"{url}\">Activate Account</a></p>
<br/>
<br/>
<p>Love,
<br/>SendLove</p>",

'plain' =>
"Thank you for trying SendLove.\n
The application is free to use for the next 30 days. At the end of that period, you will receive a usage invoice at the end of each month. The invoice is calculated based upon $5 per seat, i.e., each user in your application.\n
Before you can continue, please confirm your account by clicking the link below.\n\n
{url}\n\n\n
Love,\n
SendLove",

),

'ping_admin' => array(

'subject' =>
"Add request from {user_name}",

'body' =>
"<p>Hello there!</br>
<p>The user {user_name} would like to join the {company_name} SendLove.<br />
<a href=\"{url}\">Login to your admin area to add this user.</a></p>
<p>{user_name}s message:<br />
{message}</p>
<p>{user_name}s email: {user_email}</p>
<p>Love,
<br />SendLove</p>",

'plain' =>
"Hello there!\n\n
The user {user_name} would like to join the {company_name} SendLove.\n
To add this user please click the link below and login to your admin area:\n
{url}\n\n
{user_name}s message:\n
{message}\n\n
{user_name}s email: {user_email}\n\n
Love,\n
SendLove"

),

'ping_contact' => array(

'subject' =>
"Reactivation request from {user_name}",

'body' =>
"<p>Hello there!</br>
<p>The user {user_name} would like to reactivate the instance for {company_name}.<br />
<p>{user_name}s message:<br />
{message}</p>
<p>{user_name}s email: {user_email}</p>
<p>Love,
<br />SendLove</p>",

'plain' =>
"Hello there!\n\n
The user {user_name} would like to reactivate the instance for {company_name}.\n
{user_name}s message:\n
{message}\n\n
{user_name}s email: {user_email}\n\n
Love,\n
SendLove"

),

/*
changed_settings
    notification of changed settings
    used in: settings.php

replacement data:
    app_name - human-reqadable application name
    changes - list of settings changes
*/

'changed_settings' => array(

'subject' =>
"SendLove Account Edit Successful.",

'body' =>
"<p>Congratulations!</p>
<p>You have successfully updated your settings with {app_name} on the {company_name} SendLove.<p/>
<p>{changes}</p>
<p>Love,
<br/>SendLove</p>",
),

/*
changed_pass
    notification of changed password
    used in: resetpass.php

replacement data:
    app_name - human-reqadable application name
*/

'changed_pass' => array(

'subject' =>
"SendLove Password Changed",

'body' =>
"<p>Change is good!</p>
<p>You have successfully changed your password with {app_name} on the {company_name} SendLove.</p>
<p>Love,
<br/>SendLove</p>",
),

/*
recovery
    email for password recovery
    used in: forgot.php

replacement data:
    url - link to reset password
*/

'recovery' => array(

'subject' =>
"SendLove Password Recovery",

'body' =>
"<p>Hi,</p>
<p>Please click on the link below or copy and paste the url in browser to reset your password.<br/>
{url}</p>
<p>Love,
<br/>SendLove</p>",
),

/*
love_email_new
    notification about received love for unregistered users
    used in: send_email.php

replacement data:
    sender_nickname
    for - love reason
    url - url to signup with SendLove
*/

'love_email_new' => array(

'subject' =>
"Love from {sender_nickname}.",

'body' =>
"<p>{for}</p>
{stats_html}
"/*"/<br/><br/><p><a href=\"{url}\">Send your own love!</a></p>"*/,

'plain' =>
"{for}\n"/*\n\n\n
Send your own love!
{stats_plain}
Click the link below to join {sender_nickname} on SendLove and claim your love!:\n
{url}"*/,

'mail_auth'=>'report-low',
'mail_queue'=>'standard'
),

/*
love_email_old
    notification about received love for registered users
    used in: send_email.php

replacement data:
    sender_nickname
    for - love reason
    url - url to tofor page
*/

'love_email_old' => array(

'subject' =>
"Love from {sender_nickname}.",

'body' =>
"<p>{for}</p>
{stats_html}
<br/><br/><p><a href=\"{url}\">View your love!</a></p>",

'plain' =>
"{for}\n\n\n\n
{stats_plain}
Send your own love!

View your love:\n
{url}",

),

/*
love_email_new_private
    notification about received private love for unregistered users
    used in: send_email.php

replacement data:
    sender_nickname
    for - love reason
    url - url to signup with SendLove
*/

'love_email_new_private' => array(

'subject' =>
"Love from {sender_nickname} (love sent quietly).",

'body' =>
"<p>{for}</p>
{stats_html}
"/*<br/><br/><p><a href=\"{url}\">Send your own love!</a></p>"*/,

'plain' =>
"{for}\n\n\n\n
{stats_plain}
"/*Send your own love!
Click the link below to join {sender_nickname} on SendLove:\n
{url}"*/,

'mail_auth'=>'report-low',
'mail_queue'=>'standard'
),

/*
love_email_old_private
    notification about received private love for registered users
    used in: send_email.php

replacement data:
    sender_nickname
    for - love reason
    url - url to tofor page
*/

'love_email_old_private' => array(

'subject' =>
"Love from {sender_nickname} (love sent quietly).",

'body' =>
"<p>{for}</p>
{stats_html}
<br/><br/><p><a href=\"{url}\">View your love!</a></p>",

'plain' =>
"{for}\n\n\n\n
{stats_plain}
Send your own love!

View your love:\n
{url}",

),


/*
invite_user
    invitation to join the company on SendLove
    used in: send_email.php

replacement data:
    invitor_email
    invitor nickname
    company_name
    url - url to join the company
*/

'invite_user' => array(

'subject' =>
"You are invited to join the {company_name} SendLove!",

'body' =>
"<p>{invitor_nickname} ({invitor_email}) has invited you to join the {company_name} SendLove.</p>
<p><a href=\"{url}\">Accept this invitation.</a></p>
<p>Love,
<br/>SendLove</p>",

'plain' =>
"{invitor_nickname} ({invitor_email}) has invited you to join the {company_name} SendLove.\n
To accept this invitation click the link below:\n
    {url}\n\n
Love,\n
SendLove\n",

),

/*
invite_switch
    invitation to join another company on SendLove
    used in: send_email.php

replacement data:
    invitor_email
    invitor nickname
    company_name
    url - url to join the company
*/

'invite_switch' => array(

'subject' =>
"You are invited to join {company_name} on SendLove!",

'body' =>
"<p>{invitor_nickname} ({invitor_email}) has invited you to join {company_name} on SendLove.<br />
You are already a member of another company. If you accept this invitation you will leave your current company and switch to {company_name}.<p/>
<p><a href=\"{url}\">Accept this invitation.</a></p>
<p>Love,
<br/>SendLove</p>",

'plain' =>
"{invitor_nickname} ({invitor_email}) has invited you to join {company_name} on SendLove.\n
You are already a member of another company. If you accept this invitation you will leave your current company and switch to {company_name}.\n\n
To accept this invitation click the link below:\n
    {url}\n\n
Love,\n
SendLove\n",

),

/*
invite_admin
    invitation to become admin of the company on SendLove
    used in: send_email.php

replacement data:
    invitor_email
    invitor nickname
    company_name
    url - url to join the company
*/

'invite_admin' => array(

'subject' =>
"You are invited to join {company_name} on SendLove!",

'body' =>
"<p>{invitor_nickname} ({invitor_email}) has invited you to become an administrator for {company_name} on SendLove.</p>
<p><a href=\"{url}\">Accept this invitation.</a></p>
<p>Love,
<br/>SendLove</p>",

'plain' =>
"{invitor_nickname} ({invitor_email}) has invited you to become an administrator for {company_name} on SendLove.\n
To accept this invitation click the link below:\n
   {url}\n\n
Love,\n
SendLove\n",

),

/*
join_request
    sent to company admins with user request to join the company
    used in: send_email.php

replacement data:
    sender_nickname
    company_name
    url - url to join the company
*/

'join_request' => array(

'subject' =>
"Company join request from {sender_nickname}.",

'body' =>
"<p>The user {sender_nickname} would like to join the {company_name} SendLove<br />
<a href=\"{url}\">Approve this request.</a></p>
<p>Love,
<br/>SendLove</p>",

'plain' =>
"The user {sender_nickname} would like to join the {company_name} SendLove.\n
To approve this request please click the link below:\n
{url}\n\n
Love,\n
SendLove",

),


/*
love_value
    changing value of love notification
    used in: admin.php

replacement data:
    company_name
    old_multiplier
    new_multiplier
*/

'love_value' => array(

'subject' =>
"Love value changed.",

'body' =>
"<p>Change is good! :)</p>
<p>The value of love messages within '{company_name}'
has been changed from {old_multiplier} to {new_multiplier}.
</p><p>Love,<br/>SendLove</p>",

'plain' =>
"Change is good! :)\n
The value of love messages within '{company_name}'
has been changed from {old_multiplier} to {new_multiplier}.\n\n
Love,\n
SendLove",

),


/*
feedback
    feedback received from feedback slidout
    used in: helper/feedback.php

replacement data:
    app_name - human-reqadable application name
    message - message from user
*/

'feedback' => array(

'subject' =>
"Feedback for {app_name}.",

'body' =>
"<p>You received a message from {sender} via the contact us form on the SendLove at {instance}!</p>
<p>{message}<p/>
<p>Love,
<br/>SendLove</p>",
),

/*
Weekly Updates
	used in: weeklyupdates.php

replacement data:
	app_name - human-readable application name
	table - the table with the love sent
*/
'weeklyupdates' => array(
	'subject' => 'Weekly Updates for {app_name}',
	'body' => '<p>This is the love you and your colleagues shared this week:</p>
	{table}
	<p>Love,
	<br />SendLove</p>',
'mail_auth'=>'report-low',
'mail_queue'=>'standard'
),

/*
Published Campaign
	used in: Campaign.class.php

replacement data:
	export_data - the cvs data with the paid amount for each team members
*/
'publishedCampaign' => array(
	'subject' => 'Recognition period cvs export',
	'body' => '<p>You published a recognition period. 
    The details by team member are available in the attached file.</p>
        <p>Love,
	<br />SendLove</p>',
    'attachment' => array(
        'name' => 'period_details.csv',
        'type' => 'text/plain',
        'content' => '{export_data}',
    )
),

/*
Change In Campaign
	used in: Campaign.class.php

replacement data:
    periodInfo - list of the recognition periods that have been changed
	changeInfo - details about the change
*/
'changeInCampaign' => array(
	'subject' => 'Recognition period(s) updated/created',
	'body' => '<p>Here, you have the list of the recognition periods updated or created :</p>
    {periodInfo}
    <p>The detail of the action done on the previous list of periods is available here:</p>
    <div style="color: #669;padding: 28px;"; >
        {changeInfo}
    </div>
        <p>Love,
	<br />SendLove</p>',
),
            ); 
