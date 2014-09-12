<?php
    require_once('config.php');
    require_once('db_connect.php');
    require_once('autoload.php');
    
    session_start();

    if(empty($_REQUEST['action'])){
        die(json_encode(array('error' => 'wrong action')));
    }
    
    // array of required arguments for each action (when needed)
    $requiredArgs = array(
                        'get_periods_list' =>  array('page','rows'),
                        );

    if(array_key_exists($_REQUEST['action'], $requiredArgs)){

        foreach($requiredArgs[$_REQUEST['action']] as $arg){

            if(!isset($_REQUEST[$arg])){
                echo json_encode(array('error' => 'args'));
                return;
            }
        };
    }

    $redeemRequests = new RedeemRequests();

    switch($_REQUEST['action']){
        
    case 'batch_change_redeem_status':

        echo json_encode($redeemRequests -> batchChangeRedeemStatus());
        break;
    case 'get_periods_list':

//gj([X.X.X] - sanitize input, register patterns globally for readability and consistency. email, adword, instance, etc)^M
        $page = (int)$_REQUEST['page']; // get the requested page 
        $limit = (int)$_REQUEST['rows']; // get how many rows we want to have into the grid 
        $sidx = preg_replace("/[^a-zA-Z0-9\-\.\_\ \,]/","",$_REQUEST['sidx']); // get index row - i.e. user click to sort 
        $sord = preg_replace("/[^a-zA-Z0-9\-\.\_]\ \,/","",$_REQUEST['sord']); // get the direction 
        if(!$sidx) $sidx =1; // connect to the database 
        echo json_encode($redeemRequests -> getPeriodsList($page,$limit,$sidx,$sord));
        break;
        
    case 'get_redeem_periods_list':

//gj([X.X.X] - sanitize input, register patterns globally for readability and consistency. email, adword, instance, etc)^M
        $page = (int)$_REQUEST['page']; // get the requested page 
        $limit = (int)$_REQUEST['rows']; // get how many rows we want to have into the grid 
        $sidx = preg_replace("/[^a-zA-Z0-9\-\.\_\ \,]/","",$_REQUEST['sidx']); // get index row - i.e. user click to sort 
        $sord = preg_replace("/[^a-zA-Z0-9\-\.\_\ \,]/","",$_REQUEST['sord']); // get the direction 
        if(!$sidx) $sidx =1; // connect to the database 
        echo json_encode($redeemRequests -> getRedeemPeriodsList($page,$limit,$sidx,$sord));
        break;
        
    case 'get_redeem_requests':

        echo json_encode($redeemRequests -> getRedeemRequests());
        break;
        
    case 'export_redeem_requests':

        $redeemRequests -> exportRedeemRequests();
        break;
        
    case 'change_validated_status_period':
//gj([X.X.X] - sanitize input, register patterns globally for readability and consistency. email, adword, instance, etc)^M
        $id = isset($_REQUEST['id']) ? preg_replace("/[^a-zA-Z0-9\-\.\_]/","",$_REQUEST['id']) : -1;
        $new_validated_status = preg_replace("/[^a-zA-Z0-9\-\.\_]/","",$_REQUEST['new_validated_status']);
        if ( $new_validated_status == 'F' ) { // reset for debug
            $ret = $redeemRequests -> unPublishCampaign($id);
        }
        if (isset($ret['error'])) {
            echo json_encode($ret);
        } else {
            echo json_encode($redeemRequests -> changeCampaignStatus($id,$new_validated_status));
        }
        break;
        
    case 'change_redeem_status':
//gj([X.X.X] - sanitize input, register patterns globally for readability and consistency. email, adword, instance, etc)^M
        $period_id = isset($_REQUEST['period_id']) ? preg_replace("/[^a-zA-Z0-9\-\.\_]/","",$_REQUEST['period_id']) : -1;
        $owner_id = isset($_REQUEST['owner_id']) ? preg_replace("/[^a-zA-Z0-9\-\.\_]/","",$_REQUEST['owner_id']) : -1;
        $instance_id = isset($_REQUEST['instance_id']) ? preg_replace("/[^a-zA-Z0-9\-\.\_]/","",$_REQUEST['instance_id']) : -1;
        $new_paid_status = preg_replace("/[^a-zA-Z0-9\-\.\_]/","",$_REQUEST['new_paid_status']);
        echo json_encode($redeemRequests -> changeRedeemStatus($period_id,$new_paid_status,$owner_id,$instance_id));
        break;
    }

