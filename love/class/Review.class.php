<?php
/*
* Class Review - this class in responsible for review part of Love application
* It's responsible for setting goals, love (for purposes of review) and working with
* review periods
* It has nothing to do with Rewarder part of the application
*/
//if(!defined('TESTPERIODS')) define("TESTPERIODS", "`LM_lovemachine`.`review_periods_campaign`");


class Review{

    protected $user_id;
    protected $user_email;
    protected $periods;

    public function __construct($user_id,$periods){

        $this->user_id = $user_id;
        $this->periods = $periods;
    }

    // sets user_email (needed to get user love)
    public function setUserEmail($user_email){
        $this->user_email = $user_email;
    }

    // gets list of users with statuses of their reviews
    public function getUserList($period_id){
        global $front;
        $sql = "SELECT " . USERS . ".`id`, " . USERS . ".`username`, " . USERS . ".`nickname`, 
                IFNULL(" . USER_REVIEWS . ".`status`,-1) AS `status`," . USER_REVIEWS . ".`peer_status` FROM " . USERS . " 
                    LEFT JOIN " . USER_REVIEWS . " ON " . USERS . ".`id` = " . USER_REVIEWS . ".`user_id` 
                        AND " . USER_REVIEWS . ".`period_id` = $period_id ".
                "WHERE " . USERS . ".company_id = '".$front->getCompany()->getId()."' ";
        return $this->doQuery($sql);
    }

    // 
    public function getLoveReceivedAndUniqueSenders($period_id) {
        $love_user_received = 0;
        $love_company_received = 0;
        $love_user_unique_senders = 0;
        $love_company_unique_senders = 0;
        $love_user_img = "";
        $period = $this->periods->getPeriodInfo($period_id);
        if($period !== null){
            $user_count = $this->getUserCount();
            $love_user_received = $this->getNumberLoveUserReceived($period['start_date'], $period['end_date']);
            $love_user_unique_senders = $this->getNumberLoveUserReceived($period['start_date'], $period['end_date'], "DISTINCT(giver)");
            if ($user_count != 0) {
                $love_company_received = round($this->getNumberLoveCompanyReceived($period['start_date'], $period['end_date']) / $user_count);
                $love_company_unique_senders = round($this->getNumberLoveCompanyReceived($period['start_date'], $period['end_date'], "DISTINCT(giver)") / $user_count);  
            }
            $love_user_info = Utils::getUserInfoByUserId($this->user_id);
        }

        return array(
                    'love_user_received' => $love_user_received,
                    'love_company_received' => $love_company_received,
                    'love_user_unique_senders' => $love_user_unique_senders,
                    'love_company_unique_senders' => $love_company_unique_senders,
                    'love_user_info' => $love_user_info,
                    );
    }   
    
    // get info for specific period with user status for this period (done or not done)
    public function getPeriodById($period_id){
        $period = $this->periods->getPeriodInfo($period_id);
        $user_review = $this->getUserReview($period_id);

        // user review is not created yet
        // if review is open and we are inside dates - creating it
        if($user_review === null
            && $period['status'] == 0
            && $period['time_status'] == 1){
            $this->createUserReview($period_id);
            $user_review = $this->getUserReview($period_id);
            $this->populatePeers();
        }
        $period['user_review_id'] = $user_review['id'];
        $period['user_status'] = $user_review['status'];
        $period['wizard_step'] = $user_review['wizard_step'];
        $period['peer_status'] = $user_review['peer_status'];
        $period['user_stats'] = null;

        // if user has done this review - fill the stats for "Your Review" part
        if($user_review['status'] == 1){

            $period['user_stats'] = $this->getUserPeriodStats($period_id, $user_review['id'], $period['start_date'], $period['end_date']);
        }
        return $period;
    }


    // gets status of user review for particular period
    // 0 means - user is not done, 1 - review is published
    public function getUserStatus($period_id){

        $user_status = null;
        $user_review = $this->getUserReview($period_id);
        if($user_review !== null){
            $user_status = $user_review['status'];
        }

        return $user_status;
    }

    // gets statistics on given period - done/not done percentages
    public function getPeerPeriodStats($period_id,$user_count){
        global $front;
        if(empty($period_id)) { return; }
        $filter = $this->buildFilter(1, "peer");
        $done_count = 0;
        $sql = "SELECT COUNT( *  ) AS `count`
                    FROM " . USER_REVIEWS . " ur
                    INNER JOIN " . USERS . " u ON ur.user_id = u.id
                    WHERE ur.`period_id` = $period_id $filter AND u.company_id = " . $front->getUser()->getCompany_id();

        if(count($done_res = $this->doQuery($sql)) >0){
            $done_count = $done_res[0]['count'];
        }
        $not_done_count = $user_count - $done_count;

        // user_count can't be 0 so dividing by 0 is not a problem
        $done_percentage = ceil(($done_count/$user_count)*100); 

        return array(
                    'total' => $user_count,
                    'done' => $done_count,
                    'not_done' => $not_done_count,
                    'done_percentage' => $done_percentage,
                    'not_done_percentage' => 100 - $done_percentage
                    );
    }


    
    public function getPeriodList($page,$limit,$sidx,$sord){
        $result = mysql_query("SELECT COUNT(*) AS count FROM " . PERIODS . " WHERE ". PERIODS . ".`typeRC` = 'R' "); 
        $row = mysql_fetch_array($result,MYSQL_ASSOC); 
        $count = $row['count']; 
        if( $count >0 ) { 
            $total_pages = ceil($count/$limit); 
        } else { 
            $total_pages = 0; 
        } 
        if ($page > $total_pages) {
            $page=$total_pages;
        }    
        $start = $limit*$page - $limit; // do not put $limit*($page - 1)
        $sql = "SELECT ". PERIODS . ".`id`,title, start_date, end_date, ". PERIODS . ".`status`,COUNT(". 
                USER_REVIEWS . ".`period_id`) AS totalUserReviews FROM " . PERIODS . 
            "    LEFT JOIN " . USER_REVIEWS . " ON ". USER_REVIEWS . ".`period_id` = ". PERIODS . ".`id` ".
            "  WHERE ". PERIODS . ".`typeRC` = 'R' " .
            "  GROUP BY id,title,start_date,end_date".
            "  ORDER BY $sidx $sord LIMIT $start , $limit";
        $result = mysql_query( $sql ) or die("Couldn t execute query.".mysql_error()); 
        $responce->page = $page; 
        $responce->total = $total_pages; 
        $responce->records = $count; 
        $i=0; 
        while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
            $responce->rows[$i]['id']=$row['id']; 
            if ( $row['totalUserReviews'] == 0 && $row['status'] == 1 ) {
                $totalUserReviews = $row['totalUserReviews'] . " (<a href='javascript:;' class='delete_period' idperiod='" . $row['id'] . "'>Delete</a>) ";
            } else {
                $totalUserReviews = $row['totalUserReviews'] ;
            }
            if ( $row['status'] == 0 ) {
                $status = "open";
            } else {
                $status = "close";
            }
            $statusURL = "<a href='javascript:;' class='status_action_" . $status . "' idperiod='" . $row['id'] . "'>" . $status . "</a>";
            $responce->rows[$i]['cell']=array($row['title'],$row['start_date'],$row['end_date'],$statusURL,$totalUserReviews ); 
            $i++; 
        } 
        return $responce; 
    }
    

    // gets statistics on given period - done/not done percentages
    public function getSelfPeriodStats($period_id,$user_count){
        global $front;
        if(empty($period_id)) { return; }
        $filter = $this->buildFilter(1, "self");
        $done_count = 0;
        $sql = "SELECT COUNT( *  ) AS `count`
                    FROM " . USER_REVIEWS . " ur
                    INNER JOIN " . USERS . " u ON ur.user_id = u.id
                    WHERE ur.`period_id` = $period_id $filter AND u.company_id = " . $front->getUser()->getCompany_id();

        if(count($done_res = $this->doQuery($sql)) >0){
            $done_count = $done_res[0]['count'];
        }
        $not_done_count = $user_count - $done_count;

        // user_count can't be 0 so dividing by 0 is not a problem
        $done_percentage = ceil(($done_count/$user_count)*100); 

        return array(
                    'total' => $user_count,
                    'done' => $done_count,
                    'not_done' => $not_done_count,
                    'done_percentage' => $done_percentage,
                    'not_done_percentage' => 100 - $done_percentage
                    );
    }

    // gets statistics on given period - done/not done percentages
    public function getPeriodStats($period_id){
        $user_count = $this->getUserCount();
        $self_stats = $this->getSelfPeriodStats($period_id,$user_count);
        $peer_stats = $this->getPeerPeriodStats($period_id,$user_count);
        return array(
                    'self_stats' => $self_stats,
                    'peer_stats' => $peer_stats
                    );
    }


    public function getUserCount(){
        global $front;
        $sql = "SELECT COUNT(*) AS `count` FROM " . USERS . " WHERE company_id = " . $front->getUser()->getCompany_id();
        $ret = $this->doQuery($sql);
        return $ret[0]['count'];
    }

    
    // adds love to review
    // $love_id - love id from love database
    public function addLove($love_id, $period_id){
        $user_review_id = $this->getUserReviewId($period_id,true);
        $love_id = intval($love_id);

        // first make it not favorite, so we won't go over the limit
        $sql = "UPDATE " . LOVE . " SET `favorite` = 'no' WHERE `id` = $love_id";
        mysql_unbuffered_query($sql);

        $sql = "INSERT INTO " . REVIEW_LOVES . " (`id`, `love_id`, `user_reviews_id`) 
                                    VALUES (NULL, '$love_id', '$user_review_id')";
        return mysql_unbuffered_query($sql);
    }

    // clear favorite and favorite_why in loves of the review
    // unlink love from review
    // for all loves of the review
    public function unlinkAllLoveFromReview($user_review_id){
        $sError="";
        $sql = "CREATE TEMPORARY TABLE tmp_unlinkloves SELECT id FROM " . LOVE . "  WHERE id IN (
                    SELECT `love_id`
                        FROM " . REVIEW_LOVES . "
                            LEFT JOIN " . LOVE . " ON " . LOVE . ".`id` = `love_id`
                            LEFT JOIN " . USERS . " ON " . USERS . ".`username` = `giver` 
                        WHERE `user_reviews_id` = $user_review_id)";
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            $sql = "UPDATE " . LOVE . " SET `favorite` = 'no' WHERE `id` IN (SELECT id FROM tmp_unlinkloves)";
            $ret = mysql_unbuffered_query($sql);
            if ($ret) {
                $sql = "DELETE FROM " . REVIEW_LOVES . " 
                        WHERE `user_reviews_id` = $user_review_id";
                $ret = mysql_unbuffered_query($sql);
                if ( !$ret ) {
                    $sError = "error SQL in unlinkAllLoveFromReview step3, " . mysql_error ();
                }
            } else {
                $sError = "error SQL in unlinkAllLoveFromReview step2, " . mysql_error ();
            }
        } else {
            $sError =  "error SQL in unlinkAllLoveFromReview step1, " . mysql_error ();
        }
        return $sError;
    }

    // removes love from review
    // $love_id - love id from love database
    public function removeLove($love_id, $period_id){
        $user_review_id = $this->getUserReviewId($period_id);
        $love_id = intval($love_id);

        $sql = "DELETE FROM " . REVIEW_LOVES . " 
                    WHERE `love_id` = $love_id AND `user_reviews_id` = $user_review_id";
        return mysql_unbuffered_query($sql);
    }

    // get love user received during given period
    public function getPeriodLoves($period_id){
        $period_info = $this->getPeriodById($period_id);
        $start_date = $period_info['start_date'];
        $end_date = $period_info['end_date'];
        $user_review_id = $this->getUserReviewId($period_id);

        $sql = "SELECT DISTINCT(" . LOVE . ".`id`), `nickname`, `giver`, `why`, `at`, `favorite`, `favorite_why`, 
            " . REVIEW_LOVES . ".`id` AS `review_love_id`, DATE_FORMAT(`at`, '%m/%d/%Y') AS `when`, 
                    TIMESTAMPDIFF(SECOND,at,NOW()) as delta 
                    FROM `" . LOVE . "` 
                    LEFT JOIN " . USERS . " ON " . USERS . ".`username` = `giver` 
                    LEFT JOIN " . REVIEW_LOVES . " ON " . REVIEW_LOVES . ".`love_id` = `love`.`id` 
                    AND " . REVIEW_LOVES . ".`user_reviews_id` = $user_review_id
                WHERE `receiver` = '{$this->user_email}' 
                AND `at` >= '$start_date' AND `at` <= '$end_date' ORDER BY `at` DESC";
        return $this->doQuery($sql);
    }

    // get loves user attached to his review
    public function getReviewLoves($period_id){
        $user_review_id = $this->getUserReviewId($period_id);
        if ($user_review_id != 0) {
            $sql = "SELECT DISTINCT(" . REVIEW_LOVES . ".`id`), `love_id`, `nickname`, `giver`, `why`, `favorite`, `favorite_why` 
                        FROM " . REVIEW_LOVES . "
                            LEFT JOIN " . LOVE . " ON " . LOVE . ".`id` = `love_id`
                            LEFT JOIN " . USERS . " ON " . USERS . ".`username` = `giver` 
                        WHERE `user_reviews_id` = $user_review_id ORDER BY `id` ASC";
            $ret = $this->doQuery($sql);
        } else {
            $ret = array();
        }
        return $ret;
    }

    // gets user review entry based on period_id
    public function getUserReview($period_id){
        $user_review = null;
        $sql = "SELECT * FROM " . USER_REVIEWS . " WHERE `period_id` = $period_id AND `user_id` = {$this->user_id}";
        if(count($review_result = $this->doQuery($sql)) > 0){
            $user_review = $review_result[0];
        }
        return $user_review;
    }

    // change the status of peer review
    public function setPeerReviewStatus($user_review_id,$peer_status){
        $sql = "UPDATE " . USER_REVIEWS . " SET `peer_status` = {$peer_status}
            WHERE `id` = $user_review_id AND `user_id` = {$this->user_id}";
        return mysql_unbuffered_query($sql);
    }

    // publish user review - marks it as done
    public function setReviewCompleted($user_review_id){
        $sql = "UPDATE " . USER_REVIEWS . " SET `status` = 1 
            WHERE `id` = $user_review_id AND `user_id` = {$this->user_id}";
        return mysql_unbuffered_query($sql);
    }

    // publish user review - marks it as done
    public function setReviewStarted($user_review_id){
        $sql = "UPDATE " . USER_REVIEWS . " SET `status` = 0 
            WHERE `id` = $user_review_id AND `user_id` = {$this->user_id}";
        return mysql_unbuffered_query($sql);
    }

    // publish user review - marks it as done
    public function deleteReview($user_review_id){
        $sError="";
        $sql = "DELETE FROM " . USER_REVIEWS . 
            " WHERE `id` = $user_review_id AND `user_id` = {$this->user_id}";
        if ( ! mysql_unbuffered_query($sql) ) {
            $sError =  "error SQL in deleteReview, " . mysql_error ();
        }
        return $sError;  
    }

    // creates user review for user id and period
    public function createUserReview($period_id){

        $sql = "INSERT INTO " . USER_REVIEWS . " (`id` ,`user_id` ,`period_id` ,`status`)
                        VALUES (NULL , '{$this->user_id}', '$period_id', '0')"; 
        mysql_unbuffered_query($sql);
        return mysql_insert_id();
    }


    // wrapper for getUserReview to just get an id
    public function getUserReviewId($period_id,$bCreateOne=false){
        $user_review = $this->getUserReview($period_id);
        if ( ! $user_review && $bCreateOne == true ) {
            // user review is not created yet
            $this->createUserReview($period_id);
            $user_review = $this->getUserReview($period_id);
            $this->populatePeers();
        }
        return $user_review ? $user_review['id'] : 0;
    }

    // get list of users current user have sent to or received love from
    public function getUserPeersIds(){
        $sql = "SELECT " . USERS . ".`id` FROM
                (SELECT DISTINCT `giver` AS `peer` FROM " . LOVE . "
                    WHERE `receiver` = '{$this->user_email}' AND `at` > DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                UNION
                SELECT DISTINCT `receiver` AS `peer` FROM " . LOVE . "
                    WHERE `giver` = '{$this->user_email}' AND `at` > DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY))
                 AS `peers`, users WHERE `peers`.`peer` = " . USERS . ".`username` AND `peer` != '{$this->user_email}' GROUP BY id";

        return $this->doQuery($sql); 
    }

    public function populatePeers(){
        $peers = $this->getUserPeersIds();
        $data = array(
                'api_key' => REVIEW_API_KEY,
                'action'  => 'populate_peers',
                'user_id' => $this->user_id,
        );
        if(count($peers) > 0){
            $i = 0;
            foreach($peers as $peer){

                $data["peers[$i]"] = $peer['id'];
                $i++;
            }
        }


        postRequest(REVIEW_API_URL, $data);
    }

    // update current user step in wizard for given user_review_id
    public function updateWizardStep($user_review_id, $wizard_step){
        $sql = "UPDATE " . USER_REVIEWS . " SET `wizard_step` = $wizard_step 
            WHERE `id` = $user_review_id AND `user_id` = {$this->user_id}";
        return mysql_unbuffered_query($sql);
    }

    // get number love user has received during the review period
    public function getNumberLoveUserReceived($start_date, $end_date, $what = "*"){

        // get number love user has received during the review period
        if ($this->user_email !== null) {
            $sql = "SELECT COUNT(" . $what . ") AS `count` FROM " . LOVE . " WHERE `receiver` = '{$this->user_email}' 
                AND `at` >= '$start_date' AND `at` <= '$end_date'";
        } else {
            $sql = "SELECT COUNT(" . $what . ") AS `count`
                    FROM " . LOVE . " lv
                    INNER JOIN " . USERS . " u ON lv.receiver = u.username 
                    WHERE lv.at >= '$start_date' AND lv.at <= '$end_date' AND u.id = " . $this->user_id;
        }
        $res = mysql_query($sql);
        $love_received = 0;
        if(count($done_res = $this->doQuery($sql)) >0){
            $love_received = $done_res[0]['count'];
        }
        return $love_received;
    }
    // gets statistics on given period - done/not done percentages
    public function getNumberLoveCompanyReceived($start_date, $end_date, $what = "*"){
        global $front;
        $done_count = 0;
        $sql = "SELECT COUNT(" . $what . ") AS `count`
                    FROM " . LOVE . " lv
                    INNER JOIN " . USERS . " u ON lv.receiver = u.username 
                    WHERE lv.at >= '$start_date' AND lv.at <= '$end_date' AND u.company_id = " . $front->getUser()->getCompany_id();

        if(count($done_res = $this->doQuery($sql)) >0){
            $done_count = $done_res[0]['count'];
        }
        return $done_count;
    }

    // gets statisticts for completed user review
    // returns array of stats
    public function getUserPeriodStats($period_id, $user_review_id, $start_date, $end_date){
	if(empty($period_id)) { return; }

        // get number love user has received during the review period
        $love_received = $this->getNumberLoveUserReceived($start_date, $end_date);

        $love_limit = REVIEW_LOVE_LIMIT < $love_received ? REVIEW_LOVE_LIMIT : $love_received;
        $love_picked = count($this->getReviewLoves($period_id));

        
        // get number of love user has commented on
        $sql = "SELECT COUNT(*) AS `count` FROM " . LOVE . " WHERE favorite = 'yes' AND id IN 
                    (SELECT `love_id` FROM " . REVIEW_LOVES . " WHERE `user_reviews_id` = $user_review_id)";

        $res = mysql_query($sql);
        $love_commented = 0;
        if(mysql_num_rows($res) > 0){
            $row = mysql_fetch_assoc($res);
            $love_commented = $row['count'];
        }

        return array(
                    'love_limit' => $love_limit,
                    'love_picked' => $love_picked,
                    'love_commented' => $love_commented,
                    );
    }

   /**
    * Get count of user based on review status
    * @param $period_id period
    * @param $status review status
    * @return array which has count of user
    */
    public function getUserCountByReview($period_id, $status){
	if(empty($period_id)) { return; }
        global $front;
        $sql = "SELECT COUNT(u.id) AS count
                    FROM " . USER_REVIEWS . " ur
                    INNER JOIN " . USERS . " u ON ur.user_id = u.id
                    WHERE ur.`period_id` = $period_id AND ur.`status` = $status AND u.company_id = " . $front->getUser()->getCompany_id();
	$done_res = $this->doQuery($sql);
        if(count($done_res) > 0){
            return $done_res;
        }
    }

    /**
    * Build the where filter based on review status and review type
    * @param $status review status : 0 (not done), 1 (done)
    * @param $review_type : "self" or "peer" 
    * @return string
    */

    public function buildFilter($status, $review_type){
        $filter="";
        if ( $review_type == "self" ) {
            $filter = " AND ur.`status` = $status ";
        } else {
            if ( $status == 1 ) {
                $filter = " AND ( ur.`peer_status` = 2 OR ur.`peer_status` = 3) ";
            } else {
                $filter = " AND ( ur.`peer_status` = 0 OR ur.`peer_status` = 1) ";
            }
        }
        return $filter;
    }
    
    /**
    * Get list of user based on review status
    * @param $period_id period
    * @param $status review status
    * @param $limit query limitation string 
    * @return array user list
    */

    public function getUserListByReview($period_id, $status, $limit,$review_type){
        if(empty($period_id)) { 
            return; 
        }
        global $front;
        $filter = $this->buildFilter($status,$review_type);
        $sql = "SELECT u.id, u.username, u.nickname
                    FROM " . USER_REVIEWS . " ur
                    INNER JOIN " . USERS . " u ON ur.user_id = u.id
                    WHERE ur.`period_id` = $period_id $filter AND u.company_id = " . $front->getUser()->getCompany_id();
        $sql .= " $limit"; 
        $done_res = $this->doQuery($sql);
        if(count($done_res) > 0){
            return $done_res;
        }
    }
    
    
    /**
    * Retrieve the current period review
    *   Get the review for current period
    *   Unlink the loves from the review, clear the comments
    *   Remove the review
    * Return the review id and the period id
    */

    public function resetCurrentReview(){
        $result = array('error' => '','review_id' =>'');
        $review_id=null;
        $period = $this->periods->getCurrentPeriod();
        if ($period) {
            $period_id = $period['id'];
            $review = $this->getUserReview($period_id);
            if($review !== null){
                $review_id = $review['id'];
                if (($error = $this->unlinkAllLoveFromReview($review_id)) == "") {
                    if ( ($error = $this->deleteReview($review_id)) == "" ) {
                        $result['review_id'] = $review_id;
                        $result['period_id'] = $period_id;
                    } else {
                        $result['error'] = "Error deleting review". $error;
                    }
                } else {
                    $result['error'] = "Error in Loves comment reset. Details:" . $error;
                }
            } else {
                $result['error'] = "User review not found.";
            }
        } else {
            $result['error'] = "Current period not found.";
        }
        return $result;
    }

    private function doQuery($sql){
        $result = mysql_query($sql) or error_log("Review.doQuery:".mysql_error()."\n".$sql);;
        $ret = array();

        while($obj = mysql_fetch_assoc($result)){
            $ret[] = $obj;
        }
        return $ret;
    }

}
