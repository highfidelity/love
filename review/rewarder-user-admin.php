<?php
//  vim:ts=4:et

//  Copyright (c) 2010, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com


/**
*    Page: rewarder-user-admin.php
*    Features:  List of users that in the company.  Allow
*        Admins to grant rewarder points to each, with  
*        option to grant points to all.  Include option 
*        to send email notification to each user.  Finally,
*        record amounts distributed in the rewarder_log 
*        table with admin_id and date.  
*        Addendum: Also allow admin to mark users eligible 
*        ineligible to receive and/or distribute rewarder
*        points.  
*    Author: Jason (jkofoed@gmail.com)
*    Date: 2010-05-04 
*/

ini_set('display_errors', 1);
error_reporting(E_ALL);

include("config.php");
include("class.session_handler.php");
include_once("functions.php");
include_once("send_email.php");

//send non-admins back to main.
if (empty($_SESSION['is_admin'])) {
//    header("Location:rewarder.php");
}

$userId = getSessionUserId();

//open db connection
$db = @mysql_connect (DB_SERVER, DB_USER, DB_PASSWORD) or die ('I cannot connect to the database because: ' . mysql_error());
$db = @mysql_select_db(DB_NAME);

$rowclass = 'rowodd';

//If $action is set, it should be 'grantpoints'
if (isset($_POST["action"]) && ($_POST["action"] == 'grant-points')) {
    foreach($_POST["points"] as $user => $rewarder_points) {
        if ($rewarder_points != '') {
            $user_points_sql = 'SELECT id, rewarder_points, username FROM '.REVIEW_USERS.' WHERE id = '.$user;
            $user_points_query = mysql_query($user_points_sql);
            $user_data = mysql_fetch_array($user_points_query);
    
            $updated_rewarder_points = $user_data["rewarder_points"] + $rewarder_points;
            if ($updated_rewarder_points < 0) {
                $rewarder_points = $user_data["rewarder_points"];
                $updated_rewarder_points = 0;
            }
            $update_points_sql = 'UPDATE '. REVIEW_USERS .' SET rewarder_points = \''.$updated_rewarder_points.'\' where id = '.$user;
            $update_points_results = mysql_query($update_points_sql);
    
            if ($update_points_results) {
                $log_sql = "INSERT INTO ".REVIEW_REWARDER_LOG." (user_id, rewarder_points, awarded_by, date_awarded) VALUES ('".$user."', '".$rewarder_points."', '".$userId."', '".date("Y-m-d H:i:s")."')";
                $log_result = mysql_query($log_sql);
                if (isset($_POST["notify-users"]) && ($_POST["notify-users"] == '1')) {      
                    $subject = "Rewarder points received";
                    $body  = "You have received ".$rewarder_points." rewarder points for distribution.  You now have ".$updated_rewarder_points." points available.<br />";
                    $body .= "To distribute your points, <a href=\"".SERVER_BASE."rewarder/rewarder.php\">click here</a><br/><br/>";
                    $body_plain  = "You have received ".$rewarder_points." rewarder points for distribution.  You now have ".$updated_rewarder_points." points available.<br />";
                    $body_plain .= "To distribute your points, Visit: ".SERVER_BASE."rewarder/rewarder.php<br/><br/>";
                    sl_send_email($user_data['username'], $subject, $body, $body_plain);
                }
            }  
        }
    }
}
//pull list of users from db
    $employees_sql = "SELECT * FROM ".REVIEW_USERS." ORDER BY nickname ASC"; 
    $employees_query = mysql_query($employees_sql);


/*********************************** HTML layout begins here  *************************************/

include("head.html"); ?>

<!-- Add page-specific scripts and styles here, see head.html for`global scripts and styles  -->
<link href="css/rewarder.css" rel="stylesheet" type="text/css">
<link href="css/ui.toaster.css" rel="stylesheet" type="text/css">
<link type="text/css" href="css/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>livevalidation.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.autocomplete.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tablednd_0_5.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.template.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.jeditable.min.js"></script>
<script type="text/javascript" src="js/worklist.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>timepicker.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.tabSlideOut.v1.3.js"></script>
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>ui.toaster.js"></script>
<script type="text/javascript" src="js/rewarder.useradmin.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('.eligibilitytoggleg').click(function(e) {
        e.preventDefault();
        toggleEligible('giver', this);
    });

    $('.eligibilitytoggler').click(function(e) {
        e.preventDefault();
        toggleEligible('receiver', this);
    });
});
</script>
</head>
<body>

<?php include("format.php"); ?>

<h1>Grant Points</h1>

<div id="select-actions">
    <input type="submit" id="go-back" value="Go Back" style="float:left;margin-right:25px;" onclick="window.location='rewarder.php'"/>
    <input type="text" id="grant-all-text" name="grant-all-text" value="" /><input type="submit" name="grant-all-btn" id="grant-all-btn" value="Grant To All" onclick="grantAll();" />
    &nbsp;&nbsp;<label for="notify-users">Notify By Email: </label><input type="checkbox" name="notify-users" value="1" />
    &nbsp;&nbsp;<input type="submit" id="commit-btn" name="commit" value="Grant" />
</div>
<form action="rewarder-user-admin.php?<?php echo isset($_GET["order"])?'order='.$_GET["order"]:''; ?>" method="POST">
<input type="hidden" name="action" value="grant-points" />
<table id="users-table-headers">
    <thead><tr class="table-hdng">
        <th width="25%">User</th>
        <th width="20%">Current Points</th>
        <th width="25%">Give Points</th>
        <th width="15%">Giver-Eligible</th>
        <th width="15%">Receive-Eligible</th>
    </tr></thead>
</table>
<div id="user-table-container">
<table id="users-table">
<tbody>
<?php 

while ($user = mysql_fetch_array($employees_query)) {
    $isGiver = 0;
    $isReceiver = 0;
    echo "\r\n"; //added \r\n to make output code modestly presentable
    echo '<tr>';
    echo '<td width="25%" class="name-field">'.$user["nickname"].'</td>';
    echo '<td width="20%" align="center">'.$user["rewarder_points"].'</td>';
    echo '<td width="25%"><input type="text" name="points['.$user["id"].']" class="points" id="points['.$user["id"].']" value="" /></td>';
    echo '<td width="15%" align="center"><a class="eligibilitytoggleg" href="toggle-eligibility.php?user_id='.$user["id"].'&type=giver" rel="'.$user["id"].'" onclick="">';
    if ($user["is_giver"] == '1') {
        echo '<img id="giver-'.$user["id"].'" src="images/yes.png" />';
        $isGiver = 1;
    } else {
        echo '<img id="giver-'.$user["id"].'" src="images/no.png" />';
    }
    echo '</a>';
    echo '</td>';    
    echo '<td width="15%" align="center"><a class="eligibilitytoggler" href="toggle-eligibility.php?user_id='.$user["id"].'&type=receiver" id="receiver-'.$user["id"].'" rel="'.$user["id"].'" onclick="">';
    if ($user["is_receiver"] == '1') {
        echo '<img id="receiver-'.$user["id"].'" src="images/yes.png" />';
        $isReceiver = 1;
    } else {
        echo '<img id="receiver-'.$user["id"].'" src="images/no.png" />';
    }
    echo '</a>';
    echo '</td>';
    echo '</tr>'; 
    echo "\r\n"; //added \r\n to make output code modestly presentable
    
}
?>
</tbody>
</table>
</div>
</form>

<?php include("footer.php"); ?>
</body>
</html>
