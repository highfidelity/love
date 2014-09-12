<?php
    include("class/frontend.class.php");
    $front = new Frontend();
    require_once('db_connect.php');
    require_once('autoload.php');
    require_once('class/Session.class.php');

    $user = new LoveUser();

    if(!$user->getId()){
        die(json_encode(array('error' => 'unauthorized')));
    }

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

    $periods = new Periods($user->getId());
    $campaign = new Campaign($user->getId(),$periods);
    $campaign->setUserEmail($user->getUsername());

    switch($_REQUEST['action']){
    case 'get_periods_list':

        $page = $_REQUEST['page']; // get the requested page 
        $limit = $_REQUEST['rows']; // get how many rows we want to have into the grid 
        $sidx = $_REQUEST['sidx']; // get index row - i.e. user click to sort 
        $sord = $_REQUEST['sord']; // get the direction 
        $displayAllMy = $_REQUEST['displayAllMy'];  
        if(!$sidx) $sidx =1; // connect to the database 
        echo json_encode($campaign -> getPeriodsList($page,$limit,$sidx,$sord,$displayAllMy));
        break;
        
    case 'get_redeem_periods_list':

        $page = $_REQUEST['page']; // get the requested page 
        $limit = $_REQUEST['rows']; // get how many rows we want to have into the grid 
        $sidx = $_REQUEST['sidx']; // get index row - i.e. user click to sort 
        $sord = $_REQUEST['sord']; // get the direction 
        if(!$sidx) $sidx =1; // connect to the database 
        echo json_encode($campaign -> getRedeemPeriodsList($page,$limit,$sidx,$sord));
        break;
        
    case 'get_redeem_total':

        echo json_encode($campaign -> getRedeemTotal());
        break;
        
    case 'change_validated_status_period':
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : -1;
        $new_validated_status = $_REQUEST['new_validated_status']; 
        if ( $new_validated_status == 'F' ) { // reset for debug
            $ret = $campaign -> unPublishCampaign($id);
        }
        if (isset($ret['error'])) {
            echo json_encode($ret);
        } else {
            echo json_encode($periods -> changeCampaignStatus($id,$new_validated_status));
        }
        break;
        
    case 'get_users_list':

        $page = $_REQUEST['page']; // get the requested page 
        $limit = $_REQUEST['rows']; // get how many rows we want to have into the grid 
        $sidx = $_REQUEST['sidx']; // get index row - i.e. user click to sort 
        $sord = $_REQUEST['sord']; // get the direction 
        $period_id = $_REQUEST['period_id']; // get the period 
        $searchFilter = $_REQUEST['searchFilter']; // get the period 
        if(!$sidx) $sidx =1; // connect to the database 
        echo json_encode($campaign -> getUsersList($page,$limit,$sidx,$sord,$period_id,$searchFilter));
        break;
        
    case 'change_redeem_status':
        /*** Keep this comment, the functionality could come back later (#16400)
        $period_id = isset($_REQUEST['period_id']) ? $_REQUEST['period_id'] : -1;
        $new_paid_status = $_REQUEST['new_paid_status']; 
        echo json_encode($campaign -> changeRedeemStatus($period_id,$new_paid_status));
        ***/
        break;
        
    case 'checkout_send':
        $checkoutAdminEmailAddress = isset($_REQUEST['checkoutAdminEmailAddress']) ? $_REQUEST['checkoutAdminEmailAddress'] : "";
        $checkoutDatabase = isset($_REQUEST['checkoutDatabase']) ? $_REQUEST['checkoutDatabase'] : "";
        $checkoutTenant = isset($_REQUEST['checkoutTenant']) ? $_REQUEST['checkoutTenant'] : "";
        $checkoutPeriods = isset($_REQUEST['checkoutPeriods']) ? $_REQUEST['checkoutPeriods'] : "";
        $checkoutContactPhone = isset($_REQUEST['checkoutContactPhone']) ? $_REQUEST['checkoutContactPhone'] : "";
        $checkoutComment = isset($_REQUEST['checkoutComment']) ? $_REQUEST['checkoutComment'] : "";
        $totalBudgets = isset($_REQUEST['totalBudgets']) ? $_REQUEST['totalBudgets'] : "";
        $fee = isset($_REQUEST['fee']) ? $_REQUEST['fee'] : "";
        $totalBudgetsFee = isset($_REQUEST['totalBudgetsFee']) ? $_REQUEST['totalBudgetsFee'] : "";
        echo json_encode($campaign -> checkoutSend($checkoutAdminEmailAddress,$checkoutDatabase,$checkoutTenant,$checkoutPeriods,$checkoutContactPhone,$checkoutComment,$totalBudgets,$fee,$totalBudgetsFee));
        break;
        
    case 'publish_campaign':
        $period_id = isset($_REQUEST['period_id']) ? $_REQUEST['period_id'] : -1;
        echo json_encode($campaign -> publishCampaign($period_id));
        break;
        
    case 'populate_campaign_prorata':
        $period_id = isset($_REQUEST['period_id']) ? $_REQUEST['period_id'] : -1;
        $return_stat = isset($_REQUEST['return_stat']) ? $_REQUEST['return_stat'] : "false";
        $normalized = isset($_REQUEST['normalized']) ? $_REQUEST['normalized'] : "false";
        $min_amount = (isset($_REQUEST['min_amount'])) ? $_REQUEST['min_amount'] : 5;
        if ($normalized == "true") { 
            echo json_encode($campaign -> populateCampaignProrataNormalized($period_id, $return_stat, $min_amount));
        } else {
            echo json_encode($campaign -> populateCampaignProrata($period_id, $return_stat, $min_amount));
        }
        break;
        
    case 'get_campaign_data':
        $period_id = isset($_REQUEST['period_id']) ? $_REQUEST['period_id'] : -1;
        echo json_encode($campaign -> getResultsForPeriodForCampaign($period_id));
        break;
        
    case 'export_redeem_by_campaign':
        $period_id = isset($_REQUEST['period_id']) ? $_REQUEST['period_id'] : -1;
        $campaign -> exportRedeemByCampaign($period_id);
        break;
        
        
    case 'checkoutCampaign':
        //$user_id = $_REQUEST['user_id'];
        /* #16402 temporary removal 
        echo json_encode($campaign -> checkout());
        */
        break;
        
    case 'add_user':
        $period_id = $_REQUEST['period_id'];
        $user_id = $_REQUEST['user_id'];
        $filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        echo json_encode($campaign -> addUser($period_id,$user_id,$filter));
        break;
        
    case 'delete_user':
        $period_id = $_REQUEST['period_id'];
        $user_id = $_REQUEST['user_id'];
        $filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "";
        echo json_encode($campaign -> deleteUser($period_id,$user_id,$filter));
        break;
        
    }

?>