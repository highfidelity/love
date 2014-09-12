<?php
/***
 Class Periods - this class in responsible for periods part of Love application
 It's used to define and manage the different period records:
    - the review period (used in Self and Peer reviews)
    - the recognition periods
 This class makes the common actions, in case of the recognition period, there are many actions done in Campaign.class.php
 The field typeRC is used to determine if the period is a Review period or a Campaign period.
***/


class Periods{

    public function __construct($user_id){

        $this->user_id = $user_id;
    }


    public function getPeriodCount(){
        $sql = "SELECT COUNT(*) AS `count` FROM " . PERIODS ; //. "  WHERE ". PERIODS . ".`typeRC` = 'R' ";
        $ret = $this->doQuery($sql);
        return $ret[0]['count'];
    }

    // counts periods up to current one - does not include periods from the future
    public function getCurrentPeriodCount(){
        $sql = "SELECT COUNT(*) AS `count` FROM " . PERIODS . " WHERE NOW() >= `start_date` ";
        $ret = $this->doQuery($sql);
        return $ret[0]['count'];
    }

    // gets Period info based on it's position in database
    // used for pqagination (scrolling)
    public function getPeriodByPosition($position){

        // position 1 means offset is 0 in SQL
        $offset = $position > 1?$position - 1:0;
        $sql = "SELECT `id` FROM " . PERIODS . " ORDER BY `id` ASC LIMIT $offset,1";
        if(!count($ret = $this->doQuery($sql)) >0){
           $period = null;
           $period_id=null;
           return;
        }
        $period = $ret[0];
        $period_id = $period['id'];

        return $this->getPeriodInfo($period_id);
    }

    public function getCurrentPeriod(){

        $sql = "SELECT `id` FROM " . PERIODS . " WHERE NOW() >= `start_date` AND NOW() <= `end_date`";
        if(!count($ret = $this->doQuery($sql)) >0){
           $period = null;
           $period_id=null;
           return;
        }
        $period = $ret[0];

        return $period;
    }
    
    // get info for specific period 
    public function getPeriodInfo($period_id){
        if (empty($period_id)) { return null; }
        $sql = "SELECT *, DATE_FORMAT(`end_date`, '%M %D') AS `closing_date`,
                            TIMESTAMPDIFF(DAY,NOW(), `end_date`) AS `diff`,
                            IF(NOW() >= `start_date` AND NOW() <= `end_date`, 1, 0) 
                                AS `time_status`
                            FROM " . PERIODS . " WHERE `id`=$period_id";
        $ret = $this->doQuery($sql);
        return $ret[0];
    }

    public function deletePeriod($id,$campaign){
        $campaign->campaignChangeNotification("The period has been deleted.",$id);    
        $sql = "DELETE FROM " . PERIODS . "  WHERE `id` = $id";
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            return  array('result' => "delete query: " . $sql) ;
        } else {
            return  array('error' => 'SQL error in deletePeriod' . mysql_error () . " SQL: " . $sql) ;
        }
    }
  
   /*
   Copy the list of the team members in the new period
   */
    public function copyPeriodCampaignTeamMembers($new_period_id,$period_id){
        $sql = "INSERT INTO " . REVIEW_REWARDER . " (`giver_id`,`receiver_id` ,`period_id` ,`rewarder_points`)
                                SELECT {$this->user_id} ,`receiver_id` ,$new_period_id ,0
                                FROM " . REVIEW_REWARDER . 
                                " WHERE `period_id` = $period_id"; 
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            $res = array('result' => "insert in user_reviews") ;
        } else {
            $res =  array('error' => 'SQL error in copyPeriodCampaignTeamMembers' . mysql_error () . " SQL: " . $sql) ;
        }    
        return  $res;
    }
   /*
   Copy specific campaign information data
    -> the user-review record has to be created using the manager id
   */ 
    public function copyPeriodCampaignReview($period_id){
        $sql = "INSERT INTO " . USER_REVIEWS . " (`id` ,`user_id` ,`period_id` ,`status`)
                        VALUES (NULL , '{$this->user_id}', '$period_id', '0')"; 
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            $res = array('result' => "insert in user_reviews",
                            'id' => mysql_insert_id()) ;
        } else {
            $res =  array('error' => 'SQL error in copyPeriodCampaignReview' . mysql_error () . " SQL: " . $sql) ;
        }    
        return  $res;
    }

    public function copyPeriod($id,$campaign){
        $sql = "INSERT INTO " . PERIODS . " (`title` ,`start_date` ,`end_date` ,`status`,`typeRC`,`budget`)
                                SELECT `title` ,`start_date` ,`end_date` ,0,`typeRC`,`budget`
                                FROM " . PERIODS . 
                                " WHERE `id` = $id"; 
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            $new_period_id = mysql_insert_id();
            $res = array('result' => "insert",
                            'id' => $new_period_id) ;
            $retCampaign = $this->copyPeriodCampaignReview($new_period_id);
            if (!isset($retCampaign['error'])) {
                $retCampaign = $this->copyPeriodCampaignTeamMembers($new_period_id,$id);
            }
            $res['retCampaign'] = $retCampaign;
            $campaign->campaignChangeNotification("The recognition period has been created (by copy).",$new_period_id);    
            return  $res;
        } else {
            return  array('error' => 'SQL error in copyPeriod' . mysql_error () . " SQL: " . $sql) ;
        }    
    }
   
   /*
   Add specific campaign information data
    -> the user-review record has to be created using the manager id
   */
    public function addPeriodCampaignReview($period_id){
        $sql = "INSERT INTO " . USER_REVIEWS . " (`id` ,`user_id` ,`period_id` ,`status`)
                        VALUES (NULL , '{$this->user_id}', '$period_id', '0')"; 
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            $res = array('result' => "insert in user_reviews",
                            'id' => mysql_insert_id()) ;
        } else {
            $res =  array('error' => 'SQL error in addPeriodCampaignReview' . mysql_error () . " SQL: " . $sql) ;
        }    
        return  $res;
    }
    
    public function addPeriodCampaign($campaign){
        $typeRC = "C";
        $title = "";

        $sql = "INSERT INTO " . PERIODS . " (`id` ,`title` ,`start_date` ,`end_date` ,`status`,`typeRC`)
                                VALUES (NULL , '$title', DATE_FORMAT(NOW(),'%Y-%m-%e 00:00:00'), NULL, '0','C')"; 
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            $new_period_id = mysql_insert_id();
            $res = array('result' => "insert",
                            'id' => $new_period_id) ;
            $retCampaign = $this->addPeriodCampaignReview($new_period_id);
            $res['retCampaign'] = $retCampaign;
            $campaign->campaignChangeNotification("A new recognition period has been created.",$new_period_id);    
            return  $res;
        } else {
            return  array('error' => 'SQL error in setPeriod' . mysql_error () . " SQL: " . $sql) ;
        }    

    }

    public function addCampaign($options){
        $title = mysql_real_escape_string($options['title']);
        $end = date($options['end_date']);
        $start = date($options['start_date']);
        $options['end_date'] .= " 23:59:59";
        $options['start_date'] .= " 00:00:00";
        $start_date = $options['start_date'];
        $end_date = $options['end_date'];
        $budget = $options['budget'];
        if (strtotime($end) > strtotime($start)) {
           $sql = "INSERT INTO " . PERIODS . " (`id` ,`title` ,`start_date` ,`end_date` ,`status`,`typeRC`, `budget`)
                            VALUES (NULL , '$title', '$start_date', '$end_date', '0','C','$budget')"; 
            $ret = mysql_unbuffered_query($sql);
            if ($ret) {
                $res = array('result' => "A new recognition period has been created.",
                                'id' => mysql_insert_id()) ;
                $retCampaign = $this->addPeriodCampaignReview(mysql_insert_id());
                $res['retCampaign'] = $retCampaign;
                return  $res;
            } else {
                return  array('error' => 'SQL error in setPeriod' . mysql_error () . " SQL: " . $sql) ;
            }    
        } else {
             return  array('error' => 'End date (' . $end . '**' . $end_date . ') should be higher than start date (' . $start . '**' . $start_date .')') ;
        }
    }    
    /***
    $typeRC is the type of period:
        "C" -> Campaign periods, used in the campaign application, the period is visible only for the owner the period (manager)
        "R" -> Review periods, used by the Self Review and Peer Review application, those periods ae visible by all the users.
    ***/
    public function addPeriod($end_date,$grid_type){
        $typeRC = "C";
        if ( $grid_type == "review" ) {
            $typeRC = "R";
        }
        $title = "Click here to change the title.";
        $end = date($end_date);
        $end_date .= " 23:59:59";
        $start_date = $this->getLastPeriodClosureDate($typeRC,'%Y-%m-%e');
        if ($start_date != "") {
            $start = date($start_date);
            if (strtotime($end) > strtotime($start)) {
               $sql = "INSERT INTO " . PERIODS . " (`id` ,`title` ,`start_date` ,`end_date` ,`status`,`typeRC`)
                                VALUES (NULL , '$title', '$start_date', '$end_date', '1','" . $typeRC . "')"; 
                $ret = mysql_unbuffered_query($sql);
                if ($ret) {
                    $res = array('result' => "insert",
                                    'id' => mysql_insert_id()) ;
                    if ( $typeRC == "C" ) {
                        $retCampaign = $this->addPeriodCampaignReview(mysql_insert_id());
                        $res['retCampaign'] = $retCampaign;
                    }
                    return  $res;
                } else {
                    return  array('error' => 'SQL error in setPeriod' . mysql_error () . " SQL: " . $sql) ;
                }    
            } else {
                 return  array('error' => 'End date (' . $end . '**' . $end_date . ') should be higher than start date (' . $start . '**' . $start_date .')') ;
            }
        } else {
            return  array('error' => 'error in getLastPeriodClosureDate, empty date returned') ;
        }
    }
    
    public function changeStatus($id,$new_status,$campaign){
        $sql = "UPDATE " . PERIODS . " SET `status` = '$new_status' WHERE `id` = $id";
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            $campaign->campaignChangeNotification("The status of the period has been changed to $new_status .",$id);    
            return  array('result' => "update status: " . $sql) ;
        } else {
            return  array('error' => 'SQL error in setPeriod' . mysql_error () . " SQL: " . $sql) ;
        }
    }
    
    
    /***
    Campaign transaction status is the field budget_validated of table PERIODS
        N -> budget not validated
        C -> the manager put this budget in the cart in order to fund it
        R -> the Sales application starts the financial transaction to fund this budget
        Y -> the Sales application has accepted the payment from the manager, the campaign is funded
    For debug process, a state F is passed, it is used to reset the status to N. This functionality should not be used in the final product.
    ***/
    public function changeCampaignStatus($id,$new_validated_status){
        $filter="";
        $periodFilter=PERIODS . ".`id` = $id ";
        if ( $new_validated_status == 'C' ) {           // set to Cart if the previous status was No
            $filter = " AND budget_validated = 'N' ";
            if ( $id == -2 ) {
                // all the periods should be set for the current user
                $periodFilter=" user_id = " . $this->user_id ;
            }
        } else if ( $new_validated_status == 'N' ) {           
            $filter = " AND ( budget_validated = 'C' ";         // set to No  if the previous status  was Cart
            $filter .= " OR  budget_validated = 'R' ) ";         // set to No  if the previous status was Request (cancel)
            if ( $id == -2 ) {
                // all the periods should be set
                $periodFilter=" user_id = " . $this->user_id;
            }
        } else if ( $new_validated_status == 'R' ) {   // set to Request Running if the previous status was In Cart
            $filter = " AND budget_validated = 'C' ";
        } else if ( $new_validated_status == 'Y' ) {   // set to Yes if the previous status was Request (accepted)
            // $filter = " AND budget_validated = 'R' ";  // #16402 temporary removal
        } else if ( $new_validated_status == 'F' ) {  // force for debug
            $filter = "  ";
            $new_validated_status = "N";
        } else {
            $filter = " AND 1 = 0 ";
        }
        $sql = "UPDATE " . PERIODS . "," . USER_REVIEWS ." SET `budget_validated` = '$new_validated_status' WHERE " .
                    USER_REVIEWS .".period_id = " . PERIODS . ".id AND $periodFilter $filter";
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            if (mysql_affected_rows() != 1) {
                if ( $id != -2 ) {
                    return  array('error' => 'new status: '.$new_validated_status." is incompatible with current status: ". mysql_error ()."**".$sql ) ;
                } else {
                    return  array('result' => "update new_validated_status".mysql_affected_rows()."**".$sql ) ;
                }
            } else {
                return  array('result' => "update new_validated_status" ) ;
            }
        } else {
            return  array('error' => 'SQL error in changeCampaignStatus' . mysql_error () ) ;
        }
    }
    
    public function setTitle($id, $title){
        $title = mysql_real_escape_string($title);
        $sql = "UPDATE " . PERIODS . " SET `title` = '$title' WHERE `id` = $id";
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            return  array('result' => "update query: " . $sql) ;
        } else {
            return  array('error' => 'SQL error in setPeriod' . mysql_error () . " SQL: " . $sql) ;
        }
    }
    
    public function updateCampaignPeriod($id, $title, $budget, $start_date, $end_date, $status, $campaign) {
        $title = mysql_real_escape_string($title);
        $sql = "UPDATE " . PERIODS . " SET `title` = '$title',`budget` = '$budget',`start_date` = '$start_date',".
                "`end_date` = '$end_date' WHERE `id` = $id"; /*,`status` = '$status'*/
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            $campaign->campaignChangeNotification("The period details (title, dates, budget) have been changed.",$id);    
            return  array('result' => "update query: " . $sql) ;
        } else {
            return  array('error' => 'SQL error in setPeriod' . mysql_error () . " SQL: " . $sql) ;
        }
    }

    // gets id of period previous to $period_id
    public function getLastPeriodId($period_id){

        // getting number of periods up to now
        $sql = "SELECT COUNT(*) AS `count` FROM " . PERIODS . " WHERE `id` < $period_id";
        $total_count_result = $this->doQuery($sql);
        $period_id = 0;
        if(count($total_count_result) > 0){
            $previous_period = $this->getPeriodByPosition($total_count_result[0]['count']);
            $period_id = $previous_period['id'];
        }
        return $period_id;
    }

    // gets the last period end_date + 1 day
    // if the last period end date is 2010-09-14 23:59:59
    // it returns "09 15, 2010 23:59:59"
    public function getLastPeriodClosureDate($typeRC,$format_date){
        if (!isset($format_date)) {
            $format_date = '%m %e, %Y %T';
        }
        $date_element = " DATE_FORMAT(DATE_ADD(MAX(end_date), INTERVAL 1 DAY),'" . $format_date . "') AS `end_date` ";
        if ($typeRC == 'C') {
            $date_element = " DATE_FORMAT(NOW(),'" . $format_date . "') AS `end_date` ";
        }
        // getting number of periods up to now
        $sql = "SELECT " . $date_element . " FROM " . PERIODS .
                "  WHERE ". PERIODS . ".`typeRC` = '" . $typeRC ."' ";
        $total_count_result = $this->doQuery($sql);
        $end_date = "";
        if(count($total_count_result) > 0){
            $end_date = $total_count_result[0]['end_date'];
        } else {
            $end_date = "01 01, 2010 00:00:00";
        }
        return $end_date;
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
