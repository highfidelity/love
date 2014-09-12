<?php
    include("class/frontend.class.php");
    $front = new Frontend();
    require_once('db_connect.php');
    require_once('autoload.php');

    $user = new LoveUser();

    if(!$user->getId()){
        die(json_encode(array('error' => 'unauthorized')));
    }

    if(empty($_REQUEST['action'])){
        die(json_encode(array('error' => 'wrong action')));
    }

    // array of required arguments for each action (when needed)
    $requiredArgs = array(
                        'get_periods_list' =>  array('page','rows')
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

    case 'add_period':
        $grid_type = $_REQUEST['grid_type']; 
        if ( $grid_type == "review" ) {
            $end_date = $_REQUEST['end_date']; 
            echo json_encode($periods -> addPeriod($end_date,$grid_type));
        } else {
            echo json_encode($periods -> addPeriodCampaign($campaign));
        }
        break;
        
    case 'add_campaign':
        if((isset($_REQUEST['title']) && isset($_REQUEST['budget']) && isset($_REQUEST['startDate']) && isset($_REQUEST['endDate']))
            && (!empty($_REQUEST['title']) && !empty($_REQUEST['startDate']) && !empty($_REQUEST['endDate'])) ) {
                $options = Array(
                "title" => $_REQUEST['title'],
                "start_date" => $_REQUEST['startDate'],
                "end_date" => $_REQUEST['endDate'],
                "budget" => $_REQUEST['budget']
                );
//            echo json_encode($periods->addCampaign($options));       // #16402 temporary removal
            $json_ret = $periods->addCampaign($options);
            $periods -> changeCampaignStatus($json_ret['id'],'Y');
            echo json_encode($json_ret);
        } else {
            echo json_encode(array('error' => 'All fields are mandatory.'));
        }
        break; 
               
    case 'delete_period':
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : -1;
        echo json_encode($periods -> deletePeriod($id,$campaign));
        break;
        
    case 'copy_period':
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : -1;
        echo json_encode($periods -> copyPeriod($id,$campaign));
        break;
        
    case 'change_status_period':
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : -1;
        $new_status = $_REQUEST['new_status']; 
        echo json_encode($periods -> changeStatus($id,$new_status,$campaign));
        break;
        
        
    // gets info about requested period with period stats (done/not done)
    case 'set_period':
    
        $oper = $_REQUEST['oper']; 
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : -1;
        if ($oper == "edit") {
            if (isset($_REQUEST['budget']) ) {
                // Campaign periods update
                $title = $_REQUEST['title']; 
                $budget = $_REQUEST['budget']; 
                $start_date = $_REQUEST['start_date']; 
                $end_date = $_REQUEST['end_date']; 
                $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : 0; 
                echo json_encode($periods -> updateCampaignPeriod($id,$title,$budget,$start_date,$end_date,$status,$campaign));
            } else {
                // Review period update, currently, only the edit of period title is available
                $title = $_REQUEST['title']; 
                echo json_encode($periods -> setTitle($id,$title));
            }
        }
        break;

    case 'getLastPeriodClosureDate':
        $grid_type = $_REQUEST['grid_type']; 
        $typeRC = "C";
        if ( $grid_type == "review" ) {
            $typeRC = "R";
        }
        echo json_encode(array( 'max_end_date' => $periods -> getLastPeriodClosureDate($typeRC)));
        break;


    }
?>