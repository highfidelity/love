<?php
//  vim:ts=4:et

//  Copyright (c) 2009-2010, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com

// this file handles AJAX requests to rewarder

include("config.php");
include("class.session_handler.php");
include("functions.php");

$con=mysql_connect(DB_SERVER,DB_USER,DB_PASSWORD);
mysql_select_db(DB_NAME,$con);

if (!isset($_SESSION['userid'])) {
    echo json_encode(array('error' => 'unauthorized'));
    return;
}

$user = new User();
$user->findUserById(getSessionUserId());

// array of actions with audit permissions
$auditActions = array('get-audit-list', 'update-rewarder-auditor');

if(in_array($_REQUEST['action'], $auditActions)){
    if(!$user->getIs_auditor()){
        echo json_encode(array('error' => 'unauthorized'));
        return;
    }
}

// array of actions with admin permissions
$adminActions = array('end-period','reset_user_review');

if(in_array($_REQUEST['action'], $adminActions)){

    // THIS IS TEMPORARY UNTIL LOGIN MODULE IS IN PLACE
    if(!$user->getIs_admin()){
        echo json_encode(array('error' => 'unauthorized - not admin'));
        return;
    }
}


// array of required arguments for each action (when needed)
$requiredArgs = array(
                    'get-rewarder-user-detail' => array('id'),
                    'get-rewarder-user' => array('id'),
                    'update-rewarder-user' => array('period_id'),
                    'update-rewarder-users' => array('period_id'),
                    'get-rewarder-list' => array('period_id'),
                    'populate-rewarder-list' => array('period_id'),
                    'update-rewarder-auditor' => array('id'),
                    'end-period' => array('reset', 'conversion_rate', 'signature'),
                    'reset_user_review' => array('user_id', 'review_id','period_id'),
                    );

if(array_key_exists($_REQUEST['action'], $requiredArgs)){

    foreach($requiredArgs[$_REQUEST['action']] as $arg){

        if(!isset($_REQUEST[$arg])){
            echo json_encode(array('error' => 'args'));
            return;
        }
    };
}

$rewarder = new Rewarder($user->getId());


switch($_REQUEST['action']){

    case 'get-user-list':
        $userList = GetUserList($_SESSION['userid'], $_SESSION['nickname'], true);
        $currentUsers = $rewarder->getRewarderUserList($_REQUEST['period_id']);
        $ids = array();
        foreach ($currentUsers as $user) {
            if ( $user['id'] != null ) {
                $ids[] = $user['id'];
            }
        }
        
        $users = array();
        foreach ($userList as $user) {
            if (!in_array($user['id'], $ids)) {
                $users[] = array('id'=>$user['id'], 'nickname'=>$user['nickname']);
            }
        }
        echo json_encode($users);
    break;

    case 'get-rewarder-list':

        $period = $rewarder->getPeriod($_REQUEST['period_id']);
        $rewarderList = $rewarder->getRewarderUserList($period['id']);
        $json = json_encode(array(/*$user->getRewarder_points()*/0, $rewarderList, $period));
        echo $json;
    break;

    case 'get-audit-list':

        $rewarderList = $rewarder->getRewarderAuditList();
        $json = json_encode($rewarderList);
        echo $json;
    break;

    case 'get-rewarder-user-detail':

        $detailUser = new User();
        $detailUser->findUserById($_REQUEST['id']);
        $rewarderList = $rewarder->getRewarderUserDetail($_REQUEST['id']);
        $json = json_encode(array($detailUser->getNickname(), $rewarderList));
        echo $json;
    break;

    case 'update-rewarder-auditor':

        $auditorUser = new User();
        $auditorUser->findUserById($_REQUEST['id']);

        $toggledAuditor = $auditorUser->getIs_auditor() ? 0 : 1;
        $auditorUser->getIs_auditor($toggledAuditor);
        $auditorUser->save();

    break;

    case 'update-rewarder-user':

        $period_id = $_REQUEST['period_id'];
        $rewardeeId = intval($_REQUEST["id"]);
        $points_val = isset($_REQUEST["points_val"]) ? max(0, intval($_REQUEST["points_val"])) : 0;
        $points_perc = isset($_REQUEST["points_perc"]) ? max(0, floatval($_REQUEST["points_perc"])) : 0;
        $delete = isset($_REQUEST["delete"]) ? intval($_REQUEST["delete"]) : 0;
        if ($rewardeeId != 0 ) {

            if($delete){
                $rewarder->removeUser($rewardeeId, $period_id);
            }else{
                $remainingPoints = $rewarder->setGivenPoints($rewardeeId, $points_val,$points_perc, $period_id);
            }

            $rewarderList = $rewarder->getRewarderUserList($period_id);
            $json = json_encode(array(0, $rewarderList ));
        } else {
            $json = json_encode(array( ));
        }
        echo $json;

    break;

    case 'update-rewarder-users':

        $period_id = $_REQUEST['period_id'];
        foreach($_REQUEST["list"] as $userid => $points) {
            $rewarder->setGivenPoints($userid, $points['val'], $points['perc'],$period_id);
        }

        $rewarderList = $rewarder->getRewarderUserList($period_id);
        $json = json_encode(array(0, $rewarderList  ));
        echo $json;

    break;
    
    case 'end-period':

        $reset = (bool) $_REQUEST['reset'];
        $conversion_rate = floatval($_REQUEST['conversion_rate']);
        $signature = $_REQUEST['signature'];
        endReviewPeriod($reset, $conversion_rate, $signature);

    break;
    
    case 'populate-rewarder-list':
        $period = $rewarder->getPeriod($_REQUEST['period_id']);
        $normalized = $_REQUEST['normalized'];
        echo json_encode($rewarder->populateRewarderUserList($period,$normalized));
    break;

    case 'update-user-admin':
    
        $user_list = $_REQUEST['users'];
        // Update database with the new users
        $rewarder->setUsersAndPerms($user_list);
    
    break;

    case 'reset_user_review':
    
        $user_id = $_REQUEST['user_id'];
        $review_id = $_REQUEST['review_id'];
        $period_id = $_REQUEST['period_id'];
        $rewarderTo = new Rewarder($user_id);
        $result = $rewarderTo->resetCurrentReview($review_id,$period_id);
        echo json_encode($result);
    
    break;

    default:
        echo json_encode(array('error' => 'wrong action'));
    break;

}
