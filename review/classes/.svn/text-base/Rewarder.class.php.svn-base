<?php


class Rewarder{

    protected $userId;
    protected $auditMode;
    protected $rewarder_points;
    protected $is_auditor;

    public function __construct($userId=-1){
        $this->setUserId($userId);
        if(isset($_REQUEST['over'])) $this->setUserId($_REQUEST['over']);
    }

    public function setUserId($userId){
        $this->userId = $userId;
    }

    public function setAuditMode($auditMode){
        $this->auditMode = $auditMode;
    }

    public function getAuditMode(){
        return $this->auditMode;
    }

    public function getPeriod($period_id){
        if ( $period_id == -1) {
            $oPeriod = $this->getCurrentPeriod();
        } else {
            $oPeriod = Array();
            $sql = "SELECT *, DATE_FORMAT(start_date, '%e %M') as sd, DATE_FORMAT(end_date, '%e %M') as ed FROM `".REVIEW_PERIODS.
                    "` WHERE `id` = $period_id ";
            $res = mysql_query($sql);

            if(mysql_num_rows($res) > 0) {
                $oPeriod = mysql_fetch_assoc($res);
            } else {
                $oPeriod["error"] = "Period not found: " . $sql;
            }
        }
        return $oPeriod;
    }

    public function getCurrentPeriod(){
        if (!isset($this->currentPeriod)) {
            // first get the id of current open review period
            $period_id = 0;
            $sql = "SELECT *, DATE_FORMAT(start_date, '%e %M') as sd, DATE_FORMAT(end_date, '%e %M') as ed FROM `".REVIEW_PERIODS."` WHERE `status` = 0 AND NOW() >=`start_date` AND NOW() <= `end_date`";
            $res = mysql_query($sql);

            if(mysql_num_rows($res) > 0) $this->currentPeriod = mysql_fetch_assoc($res);
        }
        return $this->currentPeriod;
    }

    public function populateRewarderUserList($period,$normalized) {
        // From user
        if(!isset($period['id'])) {
            $periodError="";
            if ( isset($period['error']) ) {
                $periodError = $period['error'];
            }
            return (array('error'=>'period not found' . $periodError));
        }
        $fromUser = new User();
        $fromUser->findUserById($this->userId);
        $fromUsername = mysql_real_escape_string($fromUser->getUsername());
        
        if( isset($period['typeRC']) && $period['typeRC'] == "R") {
            $ret = $this->populateRewarderUserListForReview($period,$fromUsername);
        } else {
            if ($normalized == "true") {
                $ret = $this->populateRewarderUserListForCampaignNormalized($period,$fromUsername);
            } else {
                $ret = $this->populateRewarderUserListForCampaign($period,$fromUsername);
            }
        }
        
        return $ret;
    }
    
    public function query($sql) {
        $res = mysql_query($sql) or die("Couldn't execute query.".mysql_error() . " ** " . $sql ); 
        return $res;
    }

    public function populateRewarderUserListForCampaignNormalized($period,$fromUsername) {

        // get the users list of the team members 
        $this->query("CREATE TEMPORARY TABLE teamLoves
            SELECT DISTINCT " . LOVE_USERS . ".username, " . LOVE_USERS . ".nickname AS nickname, " . LOVE_USERS . ".id AS id 
            FROM " . LOVE_USERS . " 
            INNER JOIN " . REVIEW_REWARDER . " ON " . LOVE_USERS . ".id =" . REVIEW_REWARDER . ".receiver_id  
            WHERE  " . LOVE_USERS . ".username IS NOT NULL  
            AND " . REVIEW_REWARDER . ".giver_id={$this->userId}  
             AND " . REVIEW_REWARDER . ".period_id = {$period['id']};
        "); 
        // create a second list, we need both because temporary tables can only be used once by query
        $this->query("CREATE TEMPORARY TABLE teamLoves2
            SELECT * FROM teamLoves ;
        "); 
        // create the table statistic : number of Loves by giver/receiver (only for the team members)
        $this->query("CREATE TEMPORARY TABLE teamLovesStat
            SELECT COUNT(*) totalGiverReceiver, giver,receiver FROM " . LOVE_LOVE . " 
            WHERE " . LOVE_LOVE . ".at > '{$period['start_date']}'  AND " . LOVE_LOVE . ".at < '{$period['end_date']}' 
            AND giver IN ( 
            SELECT teamLoves.username
            FROM teamLoves)
            AND receiver IN ( 
            SELECT teamLoves2.username
            FROM teamLoves2)
            GROUP BY giver,receiver
            ORDER BY giver,receiver
        ;");
        // Calculate for each team member the number of loves sent
        $this->query("CREATE TEMPORARY TABLE lovesGiver
            SELECT SUM(totalGiverReceiver) AS loveSentByGiverInPeriod, giver
            FROM teamLovesStat
            GROUP BY giver
        ;");
        // Calculate the number of team members that sent at least one love
        $this->query("
            CREATE TEMPORARY TABLE totalMembersSenders
            SELECT COUNT(*) AS totalMembersGivers
            FROM lovesGiver 
        ;");
        /* Return the number of team members that sent at least one Love
        $this->query("
            SELECT totalMembersGivers
             FROM totalMembersSenders
        ;"); */
        // Calculate the price of the Loves for each givers : lovesPriceForGiver
        $this->query("
            CREATE TEMPORARY TABLE priceForGiver
            SELECT 1 / totalMembersGivers / loveSentByGiverInPeriod AS lovesPriceForGiver , loveSentByGiverInPeriod,giver
            FROM totalMembersSenders,lovesGiver
        ;");
        /* Display the price of the Loves for each receiver - giver
        $this->query("
            SELECT teamLovesStat.receiver, teamLovesStat.giver, teamLovesStat.totalGiverReceiver * priceForGiver.lovesPriceForGiver AS priceOfLovesReceived
            FROM teamLovesStat
            INNER JOIN priceForGiver ON priceForGiver.giver = teamLovesStat.giver
        ;"); */
        // Calculate the price of all the Loves received by receiver (priceOfLovesReceived)
        $this->query("
            CREATE TEMPORARY TABLE priceForReceiver
            SELECT teamLovesStat.receiver,SUM( teamLovesStat.totalGiverReceiver),SUM( teamLovesStat.totalGiverReceiver * priceForGiver.lovesPriceForGiver) AS priceOfLovesReceived
            FROM teamLovesStat
            INNER JOIN priceForGiver ON priceForGiver.giver = teamLovesStat.giver
            GROUP BY teamLovesStat.receiver
        ;");
        $res = $this->query("
            SELECT " . LOVE_USERS . ".username ,  IFNULL(100*priceForReceiver.priceOfLovesReceived,0)  AS lovecount
            , " . LOVE_USERS . ".nickname AS nickname, " . LOVE_USERS . ".id AS id FROM " . LOVE_USERS . " 
            INNER JOIN " . REVIEW_REWARDER . " ON " . LOVE_USERS . ".id =" . REVIEW_REWARDER . ".receiver_id  
            LEFT JOIN priceForReceiver ON " . LOVE_USERS . ".username =priceForReceiver.receiver
            WHERE  " . LOVE_USERS . ".username IS NOT NULL  AND " . REVIEW_REWARDER . ".giver_id={$this->userId}  
            AND " . REVIEW_REWARDER . ".period_id = {$period['id']} 
            GROUP BY nickname ORDER BY lovecount DESC
        ;"); 
        $rewarderList = Array();
        $total = 0;
        if(mysql_num_rows($res) > 0){
            while($row = mysql_fetch_assoc($res)) {
                $total += $row['lovecount'];
                $rewarderList[] = $row;
            }
            
        }
        return(array($period, $rewarderList, 'total'=> $total));
    }


    public function populateRewarderUserListForCampaign($period,$fromUsername) {
        $total_reward_pts = 1;
        $sql = "UPDATE ".REVIEW_REWARDER." SET rewarder_points = 50, rewarded_percentage = 50 ".
                " WHERE `giver_id`={$this->userId}  AND `period_id` = {$period['id']}";
        $res = mysql_query($sql);
        
        $dateFilter = " AND ".REVIEW_REWARDER.".`period_id` = " . $period['id'] . " ";

        $sql = "SELECT  IFNULL(COUNT(DISTINCT ".LOVE_LOVE.".giver),0) AS total_reward_points ".
               " FROM ".LOVE_USERS.
               " INNER JOIN ".REVIEW_REWARDER." ON ".LOVE_USERS.".id =".REVIEW_REWARDER.".receiver_id ".
               " LEFT JOIN ".LOVE_LOVE." ON username=".LOVE_LOVE.".receiver ".
               " AND ".LOVE_LOVE.".at > '{$period['start_date']}' ".
               " AND ".LOVE_LOVE.".at < '{$period['end_date']}' ". 
               " WHERE  ".LOVE_USERS.".username IS NOT NULL " .
               " AND ".REVIEW_REWARDER.".period_id = " . $period['id']
               ;
        $rt1 = mysql_query($sql) or die("Couldn't execute query.".mysql_error() . " ** " . $sql ); 
        
        if ($row=mysql_fetch_assoc($rt1)) {
	        $total_reward_pts = $row['total_reward_points'];
        }   
        $date_filter = "  `period_id` = '" . $period['id'] . "' ";

        $subQuery = "SELECT ".REVIEW_REWARDER.".receiver_id AS receiver_id, IFNULL(COUNT(DISTINCT ".LOVE_LOVE.".giver),0) AS givers ".
               " FROM ".LOVE_USERS.
               " INNER JOIN ".REVIEW_REWARDER." ON ".LOVE_USERS.".id =".REVIEW_REWARDER.".receiver_id ".
               " LEFT JOIN ".LOVE_LOVE." ON username=".LOVE_LOVE.".receiver ".
               " AND ".LOVE_LOVE.".at > '{$period['start_date']}' ".
               " AND ".LOVE_LOVE.".at < '{$period['end_date']}' ". 
               " WHERE  ".LOVE_USERS.".username IS NOT NULL " .
               " AND ".REVIEW_REWARDER.".period_id = " . $period['id'] . 
               " GROUP BY receiver_id ";

        $sql = "SELECT ".LOVE_USERS.".username ,  IFNULL(COUNT(".LOVE_LOVE.".receiver),0)  AS lovecount, ".LOVE_USERS.".nickname AS nickname, ".LOVE_USERS.".id AS id ".
               "FROM ".LOVE_USERS.
               " INNER JOIN ".REVIEW_REWARDER." ON ".LOVE_USERS.".id =".REVIEW_REWARDER.".receiver_id ".
               " LEFT JOIN ".LOVE_LOVE." ON username=".LOVE_LOVE.".receiver ".
                              " AND ".LOVE_LOVE.".at > '{$period['start_date']}' ".
               " AND ".LOVE_LOVE.".at < '{$period['end_date']}' ". 

               "     LEFT JOIN ( " . $subQuery ." ) 
                        AS `givers_table` ON `givers_table`.`receiver_id` = `" . REVIEW_REWARDER . "`.`receiver_id`  " .
               " WHERE  ".LOVE_USERS.".username IS NOT NULL " .
               " AND ".REVIEW_REWARDER.".giver_id={$this->userId}  AND ".REVIEW_REWARDER.".period_id = {$period['id']} ".
               "GROUP BY nickname ORDER BY lovecount DESC";

        $res = mysql_query($sql) or die("Couldn't execute query.".mysql_error() . " ** " . $sql ); 
        $rewarderList = Array();
        $total = 0;
        if(mysql_num_rows($res) > 0){
            while($row = mysql_fetch_assoc($res)) {
                $total += $row['lovecount'];
                $rewarderList[] = $row;
            }
            
        }
        return(array($period, $rewarderList, 'total'=> $total, 'sql' => $sql));
    }

    public function populateRewarderUserListForReview($period,$fromUsername) {
        $sql = "DELETE FROM ".REVIEW_REWARDER." WHERE `giver_id`='{$this->userId}'  AND `period_id` = {$period['id']}";
        $res = mysql_query($sql);
        
        $sql = "SELECT receiver, COUNT(*) AS lovecount, ".LOVE_USERS.".nickname AS nickname, ".LOVE_USERS.".id AS id ".
               "FROM ".LOVE_LOVE." LEFT JOIN ".LOVE_USERS." ON receiver=".LOVE_USERS.".username ".
               "WHERE ".LOVE_LOVE.".giver='{$fromUsername}' ".
               "AND ".LOVE_LOVE.".receiver!='{$fromUsername}' ".
               "AND ".LOVE_LOVE.".at > '{$period['start_date']}' ".
               "AND ".LOVE_LOVE.".at < '{$period['end_date']}' ".
               "AND ".LOVE_USERS.".username IS NOT NULL " .

               "GROUP BY receiver ORDER BY lovecount DESC";

        $res = mysql_query($sql);
        if(mysql_num_rows($res) > 0){
            $rewarderList = Array();
            $total = 0;
            while($row = mysql_fetch_assoc($res)) {
                $total += $row['lovecount'];
                $rewarderList[] = $row;
            }
            
            return(array($period, $rewarderList, 'total'=> $total));
        }
    }
    /*  Function: getRewarderUserList
    *
    *  Purpose: return the list of rewarder users for a given user.
    *
    */
    
    /* Joanne adding in rewarded percentage 2010-May-26 starting at line 44 to pull the total rewarded points 
     *  and use to calculate pctAlloc on line 72 and added to rewarderList on line 73<joanne>
     */
    function getRewarderUserList($period_id = -1) {

        // From user
        $fromUser = new User();
        $fromUser->findUserById($this->userId);
        $fromUsername = mysql_real_escape_string($fromUser->getUsername());

        if ( $period_id == -1 ) {
            $period = $this->getCurrentPeriod();
            $period_id = $period['id'];
        } else {
            $period = $this->getPeriod($_REQUEST['period_id']);
        }
        
        if( isset($period['typeRC']) && $period['typeRC'] == "R") {
            $ret = $this->getRewarderUserListForReview($period_id,$period,$fromUsername);
        } else {
            $ret = $this->getRewarderUserListForCampaign($period_id,$period,$fromUsername);
        }
        return $ret;
    }
        
    function getRewarderUserListForCampaign($period_id ,$period,$fromUsername) {
        $dateFilter = " AND ".REVIEW_REWARDER.".`period_id` = " . $period_id . " ";

        $sql = "SELECT  IFNULL(COUNT(DISTINCT ".LOVE_LOVE.".giver),0) AS total_reward_points ".
               " FROM ".LOVE_USERS.
               " INNER JOIN ".REVIEW_REWARDER." ON ".LOVE_USERS.".id =".REVIEW_REWARDER.".receiver_id ".
               " LEFT JOIN ".LOVE_LOVE." ON username=".LOVE_LOVE.".receiver ".
               " AND ".LOVE_LOVE.".at > '{$period['start_date']}' ".
               " AND ".LOVE_LOVE.".at < '{$period['end_date']}' ". 
               " WHERE  ".LOVE_USERS.".username IS NOT NULL " .
               " AND ".REVIEW_REWARDER.".period_id = " . $period_id
               ;
        $rt1 = mysql_query($sql);

        while($row=mysql_fetch_assoc($rt1)){
	
	        $total_reward_pts = $row['total_reward_points'];
	        $sql = "SELECT `receiver_id`, ".LOVE_USERS.".`nickname` as `receiver_nickname`, `".REVIEW_REWARDER."`.`paid`, `".REVIEW_REWARDER."`.`rewarder_points`,".
	               "`".REVIEW_REWARDER."`.`rewarded_percentage` FROM `".REVIEW_REWARDER."` ".
	               "LEFT JOIN ".LOVE_USERS." ON `receiver_id` = ".LOVE_USERS.".`id`  AND ".LOVE_USERS.".`confirm`=1  " .
	               "WHERE `giver_id`='{$this->userId}' ".
	               
	               $dateFilter .
	               "ORDER BY `rewarder_points` DESC, `receiver_nickname` ASC";
                   
	        $rt = mysql_query($sql);
	
	        $rewarderList = array();
    //debug stuff DO NOT REMOVE IT        $rewarderList[] = array('error'=>$sql . "**" .mysql_num_rows($rt));
	        while ($rt && ($row = mysql_fetch_assoc($rt))) {
	            // Get the rewardee username
	            $r_user = new User();
	            $r_user->findUserById($row['receiver_id']);
	            $r_username = mysql_real_escape_string($r_user->getUsername());
	            $pctalloc = 0;

	            $lovecount = 1;
	            $totallove = 1;
	            if ($row['rewarder_points'] > 0 && $total_reward_pts > 0) {
    	            $pctalloc = round(($row['rewarder_points'])*100);
    	        }
                
                $rewarderList[] = array('id'=>$row['receiver_id'], 'nickname'=>$row['receiver_nickname'],
                                        'points'=>$row['rewarder_points'],'pctalloc' => $row['rewarded_percentage'],
                                        'loveFrom' => $lovecount['lovecount'], 'totalLove' => $totallove, 'paid' => $row['paid']);
            }
	
	        return $rewarderList;
	    }
    }
        
    function getRewarderUserListForReview($period_id ,$period,$fromUsername) {
        $dateFilter = " AND ".REVIEW_REWARDER.".`period_id` = " . $period_id . " ";

        $sql = "SELECT SUM(`".REVIEW_REWARDER."`.`rewarder_points`) AS `total_reward_points` FROM `".REVIEW_REWARDER."` ".
            "WHERE `giver_id`='{$this->userId}'  " . $dateFilter .
            "ORDER BY `giver_id` ASC";
        $rt1 = mysql_query($sql);

        while($row=mysql_fetch_assoc($rt1)){
	
	        $total_reward_pts = $row['total_reward_points'];
	
	        $sql = "SELECT `receiver_id`, ".REVIEW_USERS.".`nickname` as `receiver_nickname`, `".REVIEW_REWARDER."`.`paid`, `".REVIEW_REWARDER."`.`rewarder_points`,".
	               "`".REVIEW_REWARDER."`.`rewarded_percentage` FROM `".REVIEW_REWARDER."` ".
	               "LEFT JOIN ".REVIEW_USERS." ON `receiver_id` = ".REVIEW_USERS.".`id` ".
	               "WHERE `giver_id`='{$this->userId}' ".
	               "AND ".REVIEW_USERS.".`confirmed`=1 AND `is_active`= 1 " .
	               $dateFilter .
	               "ORDER BY `rewarder_points` DESC, `receiver_nickname` ASC";
                   
	        $rt = mysql_query($sql);
	
	        $rewarderList = array();
        //    $rewarderList[] = array('error'=>$sql . "**" .mysql_num_rows($rt));
	        while ($rt && ($row = mysql_fetch_assoc($rt))) {
	            // Get the rewardee username
	            $r_user = new User();
	            $r_user->findUserById($row['receiver_id']);
	            $r_username = mysql_real_escape_string($r_user->getUsername());
	            $pctalloc = 0;

	            $lovecount = $this->getLoveCount($fromUsername,$r_username,$period_id);
	            $totallove = countLove($r_username);
	            if ($row['rewarder_points'] > 0) {
    	            $pctalloc = round(($row['rewarder_points']/$total_reward_pts)*100);
    	        }
                
                $rewarderList[] = array('id'=>$row['receiver_id'], 'nickname'=>$row['receiver_nickname'],
                                        'points'=>$row['rewarder_points'],'pctalloc' => $row['rewarded_percentage'],
                                        'loveFrom' => $lovecount['lovecount'], 'totalLove' => $totallove, 'paid' => $row['paid']);
	       
            }
            // Sort the users by the love the user has given them
            foreach($rewarderList as $key => $key_row) {
                if ( $key_row['loveFrom'] != null ) {
                    $sortby_idx[$key] = $key_row['loveFrom'];
                }
            }
            array_multisort($sortby_idx, SORT_DESC, SORT_NUMERIC, $rewarderList);
       //     error_log(json_encode($rewarderList));
	
	        return $rewarderList;
	    }
    }

    /*  Function: getRewarderAuditList
    *
    *  Purpose: return the complete list of rewardered users and their rewards - EXCEPT the user specified
    *
    */
    public function getRewarderAuditList() {
        $sql = "SELECT `receiver_id`, ".REVIEW_USERS.".`nickname` as `receiver_nickname`, SUM(`".REVIEW_REWARDER."`.`rewarder_points`) AS `total_points` 
            FROM `".REVIEW_REWARDER."` ".
            "LEFT JOIN ".REVIEW_USERS." ON `receiver_id` = ".REVIEW_USERS.".`id` ".
            "WHERE `receiver_id`!='{$this->userId}' AND `".REVIEW_REWARDER."`.`rewarder_points` > 0 AND `paid` = 0 GROUP BY `receiver_id` ORDER BY `receiver_nickname` ASC";
        $rt = mysql_query($sql);

        $rewarderList = array();
        while ($rt && ($row = mysql_fetch_assoc($rt))) {
            $rewarderList[] = array('id'=>$row['receiver_id'], 'nickname'=>$row['receiver_nickname'],'points'=>$row['total_points']);
        }

        return $rewarderList;
    }


    /*  Function: getRewarderUserDetail
    *
    *  Purpose: return list of users (and points) user in question has given points to and received from
    *
    */
    public function getRewarderUserDetail($userId) {     
        $user_id = $_SESSION['userid'];

        $query = "SELECT `nickname` as `receiver_nickname`, `".REVIEW_REWARDER."`.`rewarder_points`, `receiver_id` as `tmp_id`
                  FROM `".REVIEW_REWARDER."` LEFT JOIN ".REVIEW_USERS." ON ".REVIEW_USERS.".`id`=`".REVIEW_REWARDER."`.`receiver_id`
                  WHERE `giver_id`='$userId' AND `receiver_id`!='$user_id' AND `paid` = 0 ORDER BY `receiver_nickname`";

        $rt = mysql_query($query);
        $rewarderList = array();

        while ($rt && ($row = mysql_fetch_assoc($rt))){
            $tmp_id = $row['tmp_id'];
            $query2 = "SELECT `".REVIEW_REWARDER."`.`rewarder_points` as `received_points` FROM `".REVIEW_REWARDER."` LEFT JOIN ".REVIEW_USERS." ON ".REVIEW_USERS.".`id`=`".REVIEW_REWARDER."`.`giver_id`
                      WHERE `giver_id`='$tmp_id' AND `receiver_id`='$userId' AND `".REVIEW_REWARDER."`.`rewarder_points` > 0 AND `paid` = 0";

            $received = mysql_fetch_assoc(mysql_query($query2));
            if ($row['receiver_nickname'] != null) {
                if ($row['rewarder_points'] > 0 || $received['received_points'] > 0) {
                    $rewarderList[] = array('nickname'=>$row['receiver_nickname']." _ ".$row['rewarder_points']." _ ".$received['received_points'],
                    'points'=>$row['rewarder_points'],'received_points' =>$received['received_points']);
                }
            }
        }

        return $rewarderList;
    }

    /*  Function: getGivenPoints
    *
    *  Purpose: get info for single rewarder user
    *
    */
    public function getGivenPoints($userId, $period_id){

        $query = "SELECT `rewarder_points` FROM `".REVIEW_REWARDER."` 
                    WHERE `giver_id`='{$this->userId}' 
                    AND `receiver_id`='$userId' AND `paid` = 0  AND `period_id`='{$period_id}'";

        $rt = mysql_query($query);

        $points = null;
        if ($rt && ($row = mysql_fetch_assoc($rt))) {
                $points = $row['rewarder_points'];
        }

        return $points;
    }

    /*  Function: setGivenPoints
    *
    *  Purpose: set number of points to give to user
    *
    * returns available points for giver
    */

    // Joanne added code to calculate percentage and add to db 31-May-2010 <joanne>

 //    public function setGivenPoints($userId, $points, $period_id){

    public function setGivenPoints($userId, $points, $percent = 0, $period_id){

        $user = new User();
        $user->findUserById($this->userId);

        $givenPoints = $this->getGivenPoints($userId, $period_id);
        // this is new user
        if($givenPoints === null){

            // still alowing inserting even if review period is not set (equals 0)
            // example: using rewarder apart from Love application

            $query = "INSERT INTO `".REVIEW_REWARDER."`
                      (`giver_id`,`receiver_id`,`rewarder_points`,`rewarded_percentage`, `period_id`)
                      VALUES ('{$this->userId}','$userId','$points','$percent', '$period_id')";
        }else{
            $query = "UPDATE `".REVIEW_REWARDER."` 
                    SET `rewarder_points`='$points', `rewarded_percentage`='$percent'
                    WHERE `giver_id`='{$this->userId}' AND `receiver_id`='$userId' and `period_id` = '$period_id'";
        }

        mysql_query($query);
    }

    public function removeGivenPoints($period_id){
        $sError = "";
        $query = "DELETE FROM `".REVIEW_REWARDER."` 
                    WHERE `giver_id`='{$this->userId}' AND `period_id`='{$period_id}'";
        if ( !mysql_unbuffered_query($query) ) {
            $sError =  "error SQL in removeGivenPoints, " . mysql_error ();
        }
        return $sError;
    }
    /*  Function: removeUser
    *
    *  Purpose: remove user from list of rewarded users
    *
    */
    public function removeUser($userId, $period_id){

        $query = "DELETE FROM `".REVIEW_REWARDER."` WHERE `giver_id`='{$this->userId}' AND `receiver_id`='$userId' AND `period_id`='{$period_id}'";
        return mysql_query($query);
    }

    /*
    *  Purpose: get sum of total points distributed in review process
    *
    */
    public static function getStatsGranted(){
        $query = "SELECT SUM(`rewarder_points`) FROM `" . REVIEW_REWARDER . "` 
                    WHERE `paid` = 0";
        $rt = mysql_query($query);
        $row = mysql_fetch_array($rt);
        return intval($row[0]);
    }

    /*
    *  Purpose: get sum of total points distributed in review process
    *  limited to current user
    */
    public function getUserStatsGranted($period_id){
        $query = "SELECT SUM(`rewarder_points`) FROM `" . REVIEW_REWARDER . "` 
                    WHERE `giver_id`='{$this->userId}' AND `paid` = 0  AND `period_id`='{$period_id}'";
        $rt = mysql_query($query);
        $row = mysql_fetch_array($rt);
        return intval($row[0]);
    }

    /*
    *  Purpose: get number of givers in current distribution
    *
    */
    public static function getStatsGivers(){
        $query = "SELECT COUNT(DISTINCT `giver_id`) FROM `".REVIEW_REWARDER."` 
                    WHERE `paid` = 0 AND `rewarder_points` > 0";
        $rt = mysql_query($query);
        $row = mysql_fetch_array($rt);
        return intval($row[0]);
    }

    /*
    *  Purpose: get number of receivers in current distribution
    *
    */
    public static function getStatsReceivers(){
        $query = "SELECT COUNT(DISTINCT `receiver_id`) FROM `".REVIEW_REWARDER."` 
                    WHERE `paid` = 0 AND `rewarder_points` > 0";
        $rt = mysql_query($query);
        $row = mysql_fetch_array($rt);
        return intval($row[0]);
    }

    /*
    *  Purpose: get number of distibutions distribution
    *
    */
    public static function getStatsDistributions(){
        $query = "SELECT COUNT(*) FROM `".REVIEW_REWARDER."` WHERE `paid` = 0 AND `rewarder_points` > 0";
        $rt = mysql_query($query);
        $row = mysql_fetch_array($rt);
        return intval($row[0]);
    }

    /*
    *  Purpose: get id's of receivers in current distribution
    *  
    */
    public static function getCurrentReceivers(){

        $query = "SELECT `" . REVIEW_REWARDER . "`.`receiver_id`, 
                    SUM(`" . REVIEW_REWARDER . "`.`rewarder_points`) AS `received_points`, 
                    `givers`, `username` 
                  FROM `" . REVIEW_REWARDER . "` 
                    LEFT JOIN " . REVIEW_USERS . " ON " . REVIEW_USERS . ".`id` = `" . REVIEW_REWARDER . "`.`receiver_id` 
                    LEFT JOIN 
                        (SELECT `receiver_id`, COUNT(`giver_id`) AS `givers` 
                            FROM `" . REVIEW_REWARDER . "` WHERE `rewarder_points` > 0 AND `paid` = 0 GROUP BY `receiver_id`) 
                        AS `givers_table` ON `givers_table`.`receiver_id` = `" . REVIEW_REWARDER . "`.`receiver_id` 
                  WHERE `paid` = 0 AND `" . REVIEW_REWARDER . "`.`rewarder_points` > 0 GROUP BY `receiver_id`";
        $rt = mysql_query($query);

        $receivers = array();
        while($row = mysql_fetch_assoc($rt)){
            $receivers[] = $row;
        }

        return $receivers;
    }

    /*
    *  Purpose: marks all unpaid distributions as paid thus finishing rewarder period
    *  use with care!
    */
    public static function markPaidAll(){

        $query = "UPDATE `" . REVIEW_REWARDER . "` SET `paid` = 1, `paid_date` = NOW() WHERE `paid` = 0";
        return mysql_unbuffered_query($query);
    }

    /*
    *  Purpose: gets list of already finished periods
    *  
    */
    public static function getFinishedPeriods(){

        $query = "SELECT DISTINCT(`paid_date`), DATE_FORMAT(`paid_date`, '%m/%d/%Y') AS `formatted_date` 
                    FROM `" . REVIEW_REWARDER . "` WHERE `paid` = 1";
        $rt = mysql_query($query);
        $periods = array();
        while($row = mysql_fetch_assoc($rt)){
            $periods[] = $row;
        }
        return $periods;
    }

    public static function getResultsForPeriod($paid_date,$period_id = -1) {


        if ( $period_id == -1 ) {
            $ret = Rewarder::getResultsForPeriodForReview($paid_date,$period_id);
        } else {
             $ret = Rewarder::getResultsForPeriodForCampaign($paid_date,$period_id);
        }
        return $ret;
    }
    /*
    *  Purpose: data for specific rewarder about people who received the points
    *  
    */
    public static function getResultsForPeriodForReview($paid_date,$period_id = -1){

        $date_filter = "  `paid_date` = '$paid_date' AND `paid` = 1 AND `" . REVIEW_REWARDER . "`.`rewarder_points` > 0   ";
        $query = "SELECT `" . REVIEW_REWARDER . "`.`receiver_id`, 
                    SUM(`" . REVIEW_REWARDER . "`.`rewarder_points`) AS `received_points`, 
                    `givers`, `username`, `nickname` 
                  FROM `" . REVIEW_REWARDER . "` 
                    LEFT JOIN " . REVIEW_USERS . " ON " . REVIEW_USERS . ".`id` = `" . REVIEW_REWARDER . "`.`receiver_id` 
                    LEFT JOIN 
                        (SELECT `receiver_id`, COUNT(`giver_id`) AS `givers` 
                            FROM `" . REVIEW_REWARDER . "` WHERE " . $date_filter .
                          " GROUP BY `receiver_id`) 
                        AS `givers_table` ON `givers_table`.`receiver_id` = `" . REVIEW_REWARDER . "`.`receiver_id` 
                  WHERE " . $date_filter . " GROUP BY `receiver_id` ORDER BY `received_points` DESC";
        $rt = mysql_query($query) or die("Couldn't execute query.".mysql_error()); 

        $receivers = array();
        while($row = mysql_fetch_assoc($rt)){
            $receivers[] = $row;
        }

        return $receivers;
    }
                   
               
 
               
    public static function getResultsForPeriodForCampaign($paid_date,$period_id = -1){
        $period = Rewarder::getPeriod($period_id);
        $date_filter = "  `period_id` = '$period_id' ";
        // get the users list of the team members 
        $query = "CREATE TEMPORARY TABLE teamLoves
            SELECT DISTINCT " . LOVE_USERS . ".username, " . LOVE_USERS . ".nickname AS nickname, " . LOVE_USERS . ".id AS id 
            FROM " . LOVE_USERS . " 
            INNER JOIN " . REVIEW_REWARDER . " ON " . LOVE_USERS . ".id =" . REVIEW_REWARDER . ".receiver_id  
            WHERE  " . LOVE_USERS . ".username IS NOT NULL  
             AND " . REVIEW_REWARDER . ".period_id = {$period['id']};
        "; 
        $rt = mysql_query($query) or die("Couldn't execute query.".mysql_error()); 

        $subQuery = "SELECT ".REVIEW_REWARDER.".receiver_id AS receiver_id, IFNULL(COUNT(DISTINCT ".LOVE_LOVE.".giver),0) AS givers ".
               " FROM ".LOVE_USERS.
               " INNER JOIN ".REVIEW_REWARDER." ON ".LOVE_USERS.".id =".REVIEW_REWARDER.".receiver_id ".
               " LEFT JOIN ".LOVE_LOVE." ON username=".LOVE_LOVE.".receiver ".
               " AND ".LOVE_LOVE.".at > '{$period['start_date']}' ".
               " AND ".LOVE_LOVE.".at < '{$period['end_date']}' ". 
               " WHERE  ".LOVE_USERS.".username IS NOT NULL " .
               " AND ".REVIEW_REWARDER.".period_id = " . $period_id . 
               " GROUP BY receiver_id ";

        $teamSubQuery = "SELECT ".REVIEW_REWARDER.".receiver_id AS team_receiver_id, IFNULL(COUNT(DISTINCT ".LOVE_LOVE.".giver),0) AS team_givers ".
               " FROM ".LOVE_USERS.
               " INNER JOIN ".REVIEW_REWARDER." ON ".LOVE_USERS.".id =".REVIEW_REWARDER.".receiver_id ".
               " LEFT JOIN ".LOVE_LOVE." ON username=".LOVE_LOVE.".receiver ".
               " AND ".LOVE_LOVE.".at > '{$period['start_date']}' ".
               " AND ".LOVE_LOVE.".at < '{$period['end_date']}' ". 
               " AND ".LOVE_LOVE.".giver IN (SELECT username FROM teamLoves) " . 
               " WHERE  ".LOVE_USERS.".username IS NOT NULL " .
               " AND ".REVIEW_REWARDER.".period_id = " . $period_id . 
               " GROUP BY receiver_id ";
               
        $query = "SELECT `" . REVIEW_REWARDER . "`.`receiver_id`, 
                    SUM(`" . REVIEW_REWARDER . "`.`rewarder_points`) AS `received_points`, 
                    SUM(`" . REVIEW_REWARDER . "`.`rewarded_percentage`) AS `received_percentage`, 
                    `givers`, `team_givers`, `username`, `nickname` 
                  FROM `" . REVIEW_REWARDER . "` 
                    LEFT JOIN " . REVIEW_USERS . " ON " . REVIEW_USERS . ".`id` = `" . REVIEW_REWARDER . "`.`receiver_id` 
                    LEFT JOIN ( " . $subQuery ." ) 
                        AS `givers_table` ON `givers_table`.`receiver_id` = `" . REVIEW_REWARDER . "`.`receiver_id` 
                    LEFT JOIN ( " . $teamSubQuery ." ) 
                        AS `team_givers_table` ON `team_givers_table`.`team_receiver_id` = `" . REVIEW_REWARDER . "`.`receiver_id` 
                  WHERE " . $date_filter . " GROUP BY `receiver_id` ORDER BY `received_points` DESC, givers DESC, team_givers DESC";
        $rt = mysql_query($query) or die("Couldn't execute query.".mysql_error()); 

        $receivers = array();
        while($row = mysql_fetch_assoc($rt)){
            $receivers[] = $row;
        }

        return $receivers;
    }

    /**
     * @return the $rewarderPoints
     */
    public function getRewarder_points() {
            return $this->rewarder_points;
    }

    /**
     * @param $points: the number rewarder points to set
     */
    public function setRewarder_points($points) {
            $this->rewarder_points = $points;
            return $this;
    }

    /**
     * @return the $is_auditor
     */
    public function getIs_auditor() {
            return $this->is_auditor;
    }

    /**
     * @param $is_auditor the $is_auditor to set
     */
    public function setIs_auditor($is_auditor) {
            $this->is_auditor = $is_auditor;
            return $this;
    }

    /**
     * @return the $is_active
     */
    public function getIs_active() {
            return $this->is_active;
    }

    /**
     * @param $is_active the $is_active to set
     */
    public function setIs_active($is_active) {
            $this->is_active = $is_active;
            return $this;
    }

    /**
     * Purpose: get all users in review DB
     */
    public function getUsersAndPerms () {
        $sql = "SELECT `id`,`nickname`,`is_auditor`,`is_admin`,`is_active`,`is_giver`,`is_receiver` FROM ".REVIEW_USERS." ORDER BY `nickname` ASC";
        $query = mysql_query($sql);
        if(!$query) return;
        
        $users = array();
        while ($row = mysql_fetch_assoc($query)) {
            $users[] = array('id'=>$row['id'], 'nickname'=>$row['nickname'], 'is_auditor'=>$row['is_auditor'],
                             'is_admin'=>$row['is_admin'], 'is_active'=>$row['is_active'],
                             'is_giver'=>$row['is_giver'],'is_receiver'=>$row['is_receiver']);
        }
        return json_encode($users);
    }
    
    /**
     * Purpose: set all users and permissions into review DB
     */
    public function setUsersAndPerms ($user_list) {
        foreach ($user_list as $user) {
            $sql = "UPDATE ".REVIEW_USERS." SET `is_admin`='{$user['is_admin']}', `is_auditor`='{$user['is_auditor']}',".
                   "`is_active`='{$user['is_active']}', `is_giver`='{$user['is_giver']}', `is_receiver`='{$user['is_receiver']}'".
                   "WHERE ".REVIEW_USERS.".`id`='{$user['id']}'";

            mysql_query($sql);
        }
    }
    
    /**
    * Retrieve the current period review
    *   Get the review for current period
    *   Remove the loves of the review
    *   Remove the review
    * Return the review id 
    */

    public function resetCurrentReview($review_id,$period_id){
        $result = array('error' => '');
        $error = $this->removeGivenPoints();
        if ($error != "") {
            $result['error'] = "Error in remove points process. Details: " . $error;
        } 
        return $result;
    }
    
    /**
     * Get the love count from @fromUsername to @r_username
     * during the current period.
     * 
     * Returns a plain var.
     */
    public function getLoveCount($fromUsername,$r_username,$period_id) {
        $period = $this->getPeriod($period_id);
    
        $sql = "SELECT IFNULL(COUNT(*),0) as lovecount ".
               "FROM ".LOVE_LOVE." ".
               "LEFT JOIN ".REVIEW_USERS." ON `receiver` = ".REVIEW_USERS.".`username` ".
               "WHERE ".LOVE_LOVE.".giver = '{$fromUsername}' ".
               "AND ".LOVE_LOVE.".receiver = '{$r_username}' ".
               "AND ".LOVE_LOVE.".at > '{$period['start_date']}' ".
               "AND ".LOVE_LOVE.".at < '{$period['end_date']}' ".
               "AND ".REVIEW_USERS.".`username` IS NOT NULL " .
        
               "GROUP BY receiver ORDER BY lovecount DESC";
               
        $sql_q = mysql_query($sql) or die(mysql_error());
        $arr = mysql_fetch_array($sql_q);
        
        return $arr['lovecount']; 
    }
}

?>
