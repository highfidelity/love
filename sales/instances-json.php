<?php
    require_once('config.php');
    require_once('db_connect.php');
    require_once('autoload.php');

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

    $instances = new Instances();

    switch($_REQUEST['action']){
    
    case 'get_instances_list':

//gj([X.X.X] - sanitize input, register patterns globally for readability and consistency. email, adword, instance, etc)^M
error_log("sidx: $sidx sord: $sord");
        $page = (int)$_REQUEST['page']; // get the requested page 
        $limit = (int)$_REQUEST['rows']; // get how many rows we want to have into the grid 
        $sidx = preg_replace("/[^a-zA-Z0-9\-\.\_\ \,]/","",$_REQUEST['sidx']); // get index row - i.e. user click to sort 
        $sord = preg_replace("/[^a-zA-Z0-9\-\.\_\ \,]/","",$_REQUEST['sord']); // get the direction 
        $filtreInstances = preg_replace("/[^a-zA-Z0-9\-\.\_]/","",$_REQUEST['filtreInstances']); 
        if(!$sidx) $sidx =1; // connect to the database 
        echo json_encode($instances -> getInstancesList($page,$limit,$sidx,$sord,$filtreInstances));
        break;
        
    case 'change_instance_status':

        $new_instance_status = preg_replace("/[^a-zA-Z0-9\-\.]/","",$_REQUEST['new_instance_status']); // get the requested page 
        $domain = preg_replace("/[^a-zA-Z0-9\-\.]/","",$_REQUEST['domain']); // get how many rows we want to have into the grid 
        echo json_encode($instances -> changeInstanceStatus($domain,$new_instance_status));
        break;
        
    }

