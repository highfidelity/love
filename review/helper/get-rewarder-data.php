<?php
//  vim:ts=4:et

//  Copyright (c) 2009-2010, LoveMachine Inc.  
//  All Rights Reserved.  
//  http://www.lovemachineinc.com

// AJAX request to get rewarder results data

include("../config.php");
include("../class.session_handler.php");
include("check_session.php");
include("../functions.php");

$con=mysql_connect(DB_SERVER,DB_USER,DB_PASSWORD);
mysql_select_db(DB_NAME,$con);


// If selected action = periods we return the available rewarder periods
if (isset($_REQUEST['action']) && $_REQUEST['action'] == "periods") {

    // Get back with an array containing rewarder periods
    $periods = Rewarder::getFinishedPeriods();
    echo json_encode($periods);
    return;
} else {
    $period = "";
	// Check that a specified period is passed
	if (empty($_REQUEST['period'])) {
        if (empty($_REQUEST['period_id'])) {
            echo 'error: args';
            return;
        } else {
            $period_id = mysql_real_escape_string($_REQUEST['period_id']);
        }
	} else {
        $period = mysql_real_escape_string($_REQUEST['period']);
    }
	
	

    $results = Rewarder::getResultsForPeriod($period,$period_id);

    // Create an array to hold the point values
    $points = array();
    $percentage = array();
    // Create an array to hold the users
    $users = array ();
    // Create an array to hold the givers
    $givers = array();
    $team_givers = array();
    // Relation array
    $relation = array();
	
	foreach ($results as $result) {
        $points[] = $result['received_points'];
        $percentage[] = $result['received_percentage'];
        $users[] = $result['nickname'];
        $givers[] = $result['givers'];
        $team_givers[] = $result['team_givers'];
        $relation[] = array($result['nickname'], $result['received_points'], $result['givers'],$result['received_percentage'], $result['team_givers']);
	}
	$data = array('users' => $users,
	              'points' => $points,
	              'percentage' => $percentage,
	              'givers' => $givers,
	              'team_givers' => $team_givers,
	              'relation' => $relation);
	echo json_encode($data);
}

?>
