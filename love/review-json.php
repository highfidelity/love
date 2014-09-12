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
                        'get_userlist' => array('period_id'),
                        'user_info' => array('user_id', 'period_id'),
                        'user_love' => array('period_id'),
                        'mark_favorite' => array('love_id', 'status'),
                        'favorite_why' => array('love_id', 'why'),
                        'update_love' => array('love_id', 'love_status', 'period_id'),
                        'update_wizard' =>  array('user_review_id', 'wizard_step'),
                        'peer_review_status' =>  array('user_review_id','user_review_peer_status'),
                        'get_periods_list' =>  array('page','rows'),
                        'review_completed' =>  array('user_review_id'),
                        'review_started' =>  array('user_review_id'),
                        'reset_user_review' =>  array('user_id'),
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
    $review = new Review($user->getId(),$periods);
    $review->setUserEmail($user->getUsername());

    switch($_REQUEST['action']){

    // gets info about requested period with period stats (done/not done)
    case 'get_period':
        $totalCount = $periods->getPeriodCount();
        $position = !empty($_REQUEST['position']) ? intval($_REQUEST['position']) : $periods->getCurrentPeriodCount();

        $period_info = $periods->getPeriodByPosition($position);
        if ($period_info !== null) {
            $period_info = $review->getPeriodById($period_info['id']);
        }
        $period_stats = $review->getPeriodStats($period_info['id']);

        $time_percentage = 0;
        if($period_info['status'] == 0){
            $time_percentage = getTimePercentage($period_info['start_date'], $period_info['end_date']);
        }

        echo json_encode(array(
                            'info' => $period_info, 
                            'stats' => $period_stats, 
                            'count' => $totalCount, 
                            'position' => $position,
                            'time_percentage' => $time_percentage,
                            ));
        break;
/*        
    case 'add_period':
        $end_date = $_REQUEST['end_date']; 
        echo json_encode($periods -> addPeriod($end_date));
        break;
        
    case 'delete_period':
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : -1;
        echo json_encode($periods -> deletePeriod($id));
        break;
        
    case 'change_status_period':
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : -1;
        $new_status = $_REQUEST['new_status']; 
        echo json_encode($periods -> changeStatus($id,$new_status));
        break;
        
        
    // gets info about requested period with period stats (done/not done)
    case 'set_period':
    
        $oper = $_REQUEST['oper']; 
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : -1;
        if ($oper == "edit") {
            // currently, only the edit of period title is available
            $title = $_REQUEST['title']; 
            echo json_encode($periods -> setTitle($id,$title));
        }
        break;
*/
    case 'get_periods_list':

        $page = $_REQUEST['page']; // get the requested page 
        $limit = $_REQUEST['rows']; // get how many rows we want to have into the grid 
        $sidx = $_REQUEST['sidx']; // get index row - i.e. user click to sort 
        $sord = $_REQUEST['sord']; // get the direction 
        if(!$sidx) $sidx =1; // connect to the database 
        echo json_encode($review -> getPeriodList($page,$limit,$sidx,$sord));
        break;
/*
    case 'getLastPeriodClosureDate':
        echo json_encode(array( 'max_end_date' => $periods -> getLastPeriodClosureDate()));
        break;
*/
    case 'get_userlist':
        $period_id = intval($_REQUEST['period_id']);
        echo json_encode($review -> getUserList($period_id));
        break;

    // gets info to fill user popup (published review information)
    case 'user_info':
        $user_id = intval($_REQUEST['user_id']);
        $period_id = intval($_REQUEST['period_id']);
        $popup_review = new Review($user_id);
        $review_love = $popup_review->getReviewLoves($period_id);
        $user_status = $popup_review->getUserStatus($period_id);
        $love_statistic = $popup_review->getLoveReceivedAndUniqueSenders($period_id);

        echo json_encode(array(
                            'review_love' => $review_love,
                            'user_status' => $user_status,
                            'love_statistic' => $love_statistic,
                            ));
        break;

    // list of love received by user with review id if present
    case 'user_love':
        $period_id = intval($_REQUEST['period_id']);

        $review_love = $review->getReviewLoves($period_id);
        $love_statistic = $review->getLoveReceivedAndUniqueSenders($period_id);

        echo json_encode(array(
                            'review_love' => $review_love,
                            'love_statistic' => $love_statistic,
                            ));

        break;

    // updates favorite status of given love
    case 'mark_favorite':
        $love_id = intval($_REQUEST['love_id']);
        $status = mysql_real_escape_string($_REQUEST['status']);
        $sql = "UPDATE " . LOVE . " SET `favorite` = '$status' WHERE `id` = $love_id";
        mysql_unbuffered_query($sql);
        break;

    // changes description of why love is favorite
    case 'favorite_why':
        $love_id = intval($_REQUEST['love_id']);
        $why = strip_tags(mysql_real_escape_string($_REQUEST['why']));
        $sql = "UPDATE " . LOVE . " SET `favorite_why` = '$why' WHERE `id` = $love_id";
        mysql_unbuffered_query($sql);
        break;

    // adds/removes love from current review
    case 'update_love':
        $love_id = intval($_REQUEST['love_id']);
        $love_status = intval($_REQUEST['love_status']);
        $period_id = intval($_REQUEST['period_id']);

        if($love_status){
            $review->addLove($love_id, $period_id);
        }else{
            $review->removeLove($love_id, $period_id);
        }
        break;

    // list of user based on review status
    case 'get_users_by_review':   
        $result = array('user_list' => '', 'pagination' => '');
        $per_page = 10;
        $position = !empty($_REQUEST['position']) ? intval($_REQUEST['position']) : $periods->getCurrentPeriodCount();
        $review_status = $_REQUEST['review_status'];
        $review_type = $_REQUEST['review_type'];
        $current_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $period_info = $periods->getPeriodByPosition($position);
        $count = $review->getUserCountByReview($period_info['id'], $review_status);  
        $user_count = !empty($count) ? $count[0]['count'] :  0;
        if($user_count > 0) {
            $limit = " LIMIT " . ( $current_page - 1 ) * $per_page . ", $per_page";
            $link_to = $_SERVER['PHP_SELF']."?action=get_users_by_review&position=$position&review_status=$review_status&review_type=$review_type&page="; 
            $result['user_list'] = $review->getUserListByReview($period_info['id'], $review_status, $limit,$review_type);
            $result['pagination'] = getPagination($user_count, $per_page, $current_page, $link_to);
        }
        echo json_encode($result);
	break;

    // updates wizard step for user_review_id
    case 'update_wizard':
        $user_review_id = intval($_REQUEST['user_review_id']);
        $wizard_step = intval($_REQUEST['wizard_step']);
        $review->updateWizardStep($user_review_id, $wizard_step);
        break;

    // set the peer review status
    case 'peer_review_status':
        $user_review_id = intval($_REQUEST['user_review_id']);
        $user_review_peer_status = intval($_REQUEST['user_review_peer_status']);
        $review->setPeerReviewStatus($user_review_id,$user_review_peer_status);
        break;

    // marks review as completed
    case 'review_completed':
        $user_review_id = intval($_REQUEST['user_review_id']);
        $review->setReviewCompleted($user_review_id);
        break;

    // marks review as started
    case 'review_started':
        $user_review_id = intval($_REQUEST['user_review_id']);
        $review->setReviewStarted($user_review_id);
        break;

    // reset current review of other user
    case 'reset_user_review':
        $user_id = intval($_REQUEST['user_id']);
        $reviewTo = new Review($user_id);
        $result = $reviewTo->resetCurrentReview();
        echo json_encode($result);
        break;
    }

// defining position of current time between 
// $startTime and $endTime to set the right color
// for closing time caption
function getTimePercentage($startTime, $endTime){

        $start_time = strtotime($startTime);
        $end_time = strtotime($endTime);
        $current_time = time();
        $time_diff = $end_time - $start_time;
        $time_position = $current_time - $start_time;

        $time_percentage = 0;

        // it will be negative if we are looking into future period
        if($time_position > 0){
            $time_percentage = ceil(($time_position/$time_diff)*100);
        }
        return $time_percentage;
}

/**
* Generate pagination link
* @param $total_rows total number of rows from database
* @param $entries_per_page number of rows to be dispaly in a page
* @param $current_page current page
* @param $link_to target link 
* @return string pagination link
*/

function getPagination($total_rows, $entries_per_page, $current_page, $link_to) {
	if(!$total_rows OR !$total_rows OR !$link_to) {
	  return "";
	}
	$total_page = ceil($total_rows / $entries_per_page);
	$str_page	= (strpos($link_to, "?") === false) ? "?page" : "&amp;page";
	if ($current_page == 1) {
	    $paginate_links = " &laquo; Prev ";
	} else {
	    $paginate_links = " <a class='page-number' href=\"$link_to$str_page=" . ($current_page - 1) . "\">&laquo; Prev</a> ";
	}
	for ($i = 1; $i <= $total_page; $i++) {
	    if ($i == $current_page) {
	      $paginate_links .= "<b>$i</b> ";
	    } else {
	      $paginate_links .= " <a class='page-number' href=\"$link_to$str_page=$i\">$i</a> ";
	    }
	}
	if ($current_page == $total_page) {
	    $paginate_links .= " Next &raquo; ";
	} else {
	    $paginate_links .= " <a class='page-number' href=\"$link_to$str_page=" . ($current_page + 1) . 	"\">Next &raquo;</a> ";
	}
	return $paginate_links;
}
