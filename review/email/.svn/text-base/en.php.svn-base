<?php
$emailTemplates = array(

/*
increase-balance
    email when user's rewarder balance is increased by api
    used in: api.php

replacement data:
    points - points awarded
    total-points - current rewarder balance
    reason - reason why points are given - for example worklist task completion
*/

'increase-balance' => array(

'subject' =>
"Your rewarder balance has been increased",

'body' =>
"<p>Your rewarder balance has been increased by {points} points!</p>
<p>Reason<br />
{reason}
</p>
<p>You currently have {total-points} points available to reward other LoveMachiners with.</p>
<p>Reward them now on the Rewarder page:<br />
<a href = \"" . SERVER_URL . "\">Rewarder</a>
</p>
<p>Thank you!</p>
<p>Love,
<br/>Philip and Ryan</p>",

'plain' =>
"Your rewarder balance has been increased by {points} points!\n\n
Reason\n
{reason}\n\n
You currently have {total-points} points available to reward other LoveMachiners with.\n\n
Reward them now on the Rewarder page:\n
" . SERVER_URL . "\n\n
Love,\n
Philip and Ryan",

),

/*
decrease-balance
    email when user's rewarder balance is decreased by api
    used in: api.php

replacement data:
    points - points substracted
    total-points - current rewarder balance
    reason - reason why points are given - for example worklist task completion
*/

'decrease-balance' => array(

'subject' =>
"Your rewarder balance has been decreased",

'body' =>
"<p>Your rewarder balance has been decreased by {points} points!</p>
<p>Reason<br />
{reason}
</p>
<p>You currently have {total-points} points available to reward other LoveMachiners with.</p>
<p>Reward them now on the Review page:<br />
<a href = \"" . SERVER_URL . "\">Review</a>
</p>
<p>Thank you!</p>
<p>Love,
<br/>Philip and Ryan</p>",

'plain' =>
"Your rewarder balance has been decreased by {points} points!\n\n
Reason\n
{reason}\n\n
You currently have {total-points} points available to reward other LoveMachiners with.\n\n
Reward them now on the Review page:\n
" . SERVER_URL . "\n\n
Love,\n
Philip and Ryan",

),

/*
end-period
    email when rewarder period is finished
    used in: functions.php (called from rewarder-json.php)

replacement data:
    date - formatted date when rewarder is ended
    points - number of received points
    people - number of people who gave points
    signature - review end period signature

*/

'end-period' => array(

'subject' =>
"Your Rewarder Earnings, {date}",

'body' =>
"<p>You received {points} rewarder points from {people} different people.</p>
<p>{signature}</p>",

'plain' =>
"You received {points} rewarder points from {people} different people.\n\n
{signature}",

),

/*
end-period-conversion
    email when rewarder period is finished
    same as end-pariod but with conversion points info
    used in: functions.php (called from rewarder-json.php)

replacement data:
    date - formatted date when rewarder is ended
    points - number of received points
    people - number of people who gave points
    signature - review end period signature
    worth
    total_earnings

*/

'end-period-conversion' => array(

'subject' =>
"Your Rewarder Earnings, {date}",

'body' =>
"<p>You received {points} rewarder points from {people} different people.</p>
<p>Those points are each worth \${worth}, for total earnings of \${total_earnings}</p>
<p>{signature}</p>",

'plain' =>
"You received {points} rewarder points from {people} different people.\n\n
Those points are each worth \${worth}, for total earnings of \${total_earnings}\n\n
{signature}",

),


);
