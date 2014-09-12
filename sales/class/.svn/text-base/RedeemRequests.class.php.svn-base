<?php
/****
 Class RedeemRequests - this class in responsible for the redeem requests sent to LM
 When a recognition period is funded and published, the team members receive a part of the fund.
 Then they can redeem this gain. 
 The redeem request page is used by LM to display all the redeem requests that need to be processed.
 The redeem request is transformed in an Amazon gift card, and when the amazon process is completed the
 status of the redeem request is set to paid.
 The redeem requests page is a cross instances process. 
 All the redeem requests of all the active instances are displayed in the same page. 
****/

class RedeemRequests{
    public $link;

    public function __construct(){
        $this->link = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
        mysql_select_db(DB_NAME,$this->link);
        if($this->link === null){
            die('Could not connect: ' . mysql_error());
        }
    }

    /***
    Redeem status is the field "paid" of REVIEW_REWARDER
        -> 0 : default value at record creation time 
        -> 5 : the campaign has been published, the redeem is available
        -> 2 : the team member put the redeem in the cart
        -> 3 : the team member sent the request to LM in order to get gift code
        -> 4 : the request has been received by LM, but not yet approuved
        -> 1 : the request has been accepted and paid to the team member by LM
    
    ***/
    
    public function changeRedeemStatus($idPeriod,$new_paid_status,$owner_id,$instance_id){
        $filter="";
        $dateUpdate = "";
        $errMessage="";
        $periodFilter=" AND `period_id` = $idPeriod ";
        if ( $new_paid_status == 5 ) {           
            $filter = " AND  paid = 4  ";         // set to No (5)  if the previous status was paypal running (cancel)
            $errMessage="Paypal transaction can be canceled only if Paypal transaction is running .";
        } else if ( $new_paid_status == 3 ) {    // set to Redeem Request Sent if the previous status was In Sales Cart
            $filter = " AND  paid = 6  ";         
            $errMessage="Redeem request is not in Sales application cart .";
//        } else if ( $new_paid_status == 4 ) {    
//            $filter = " AND paid = 6 ";             // set to Request Running if the previous status was in Sales Cart
//            $errMessage="Redeem request is not in Sales application cart .";
        } else if ( $new_paid_status == 6 ) {    // set to Request in Sales cart if the previous status was Sent
            $filter = " AND paid = 3 ";
            $errMessage="The status of the Redeem is not 'Sent'.";
        } else if ( $new_paid_status == 1 ) {    // set to Yes if the previous status was in Sales Cart
            $filter = " AND paid = 6 ";
            $dateUpdate = " , paid_date =  NOW() ";
            $errMessage="Paypal transaction can be validated only if Paypal transaction is running .";
        } else if ( $new_paid_status == "F" ) {    // force for debug
            $filter = "  ";
            $new_paid_status = 5;
        } else {
            $filter = " AND 1 = 0 ";
        }
        $sql = "UPDATE $instance_id." . REVIEW_REWARDER . " SET `paid` = '$new_paid_status' $dateUpdate WHERE " . 
                REVIEW_REWARDER . ".`receiver_id` = $owner_id $periodFilter $filter";
        $ret = mysql_unbuffered_query($sql,$this->link);
        if ($ret) {
        // the following test works only if the update is for 1 redeem, need to count before the number of redeem requested and compare with this number
            if (mysql_affected_rows() == 0) { 
                return  array('error' => 'new status: ' . $new_paid_status." is incompatible with current status." .
                                            $errMessage ) ;
            } else {
                return  array('result' => "update new_paid_status: " . $sql) ;
            }
        } else {
            return  array('error' => 'error SQL in changeRedeemStatus' . mysql_error () . " SQL: " . $sql) ;
        }
    }
    
    /***
    Change the redeem status of the list of redeem requests saved in the current session
    ***/
    public function batchChangeRedeemStatus(){
        $redeem_to_process = $_SESSION['redeem_to_process'];
        $list_debug = "";
        $retError = array();
        $jj = 0;
        while ($jj < count($redeem_to_process)) {
            $redeem = $redeem_to_process[$jj];
            $list_debug .= $redeem_to_process[$jj]['idperiod'] . "*" . $redeem_to_process[$jj]['idowner'] . "*" . $redeem_to_process[$jj]['instance'] . "*\r\n";
            $ret = $this->changeRedeemStatus($redeem['idperiod'],1,$redeem['idowner'],$redeem['instance']);
            if ( isset($ret['error']) ) {
                $retError[] = array( 'idperiod' => $redeem['idperiod'],
                                'idowner' => $redeem['idowner'],
                                'instance' => $redeem['instance'],
                                'error' => $ret['error']);
            }
            $jj++;
        }
        if ( count($retError) > 0 ) {
            return  array('error' => 'error in batchChangeRedeemStatus' . $list_debug,
                            'aError' => $retError) ;
        } else {
            return  array('result' => 'batch redeem update done!' . $list_debug) ;
        }
    }
    
     /*
    return an array of the tables pre-fixed by the instance name
    */
    public function getTablesForInstance($instance){
        $tb = array(
            PERIODS => $instance.".".PERIODS,
            REVIEW_REWARDER => $instance.".".REVIEW_REWARDER,
            USER_REVIEWS => $instance.".".USER_REVIEWS,
            USERS => $instance.".".USERS,
            LOVE => $instance.".".LOVE,
            REDEEM => $instance.".".REDEEM
        );
        return $tb;
    }
    /*
    verify that the redeem view is available in the instance
    */
    public function checkRedeemViewForInstance($instance){
        $tb = $this->getTablesForInstance($instance);
        $sqlFields = "SELECT ". PERIODS . ".`id`,title, start_date, end_date, budget,budget_validated,paid_amount,sent_by_receiver, ". PERIODS . ".`status` AS periodStatus,
                um.nickname AS manager, um.username AS managerUsername,
                uo.nickname AS owner,uo.username AS ownerUsername,uo.id AS idowner, ". 
                REVIEW_REWARDER . ".`rewarded_percentage` AS rewarded_percentage, ". 
                REVIEW_REWARDER . ".`paid` AS paid ";
        $table_filter = " FROM " . $tb[PERIODS] . 
            "    LEFT JOIN " . $tb[USER_REVIEWS] . " ON ". USER_REVIEWS . ".`period_id` = ". PERIODS . ".`id` ".
            "    LEFT JOIN " . $tb[REVIEW_REWARDER] . " ON ". REVIEW_REWARDER . ".`period_id` = ". PERIODS . ".`id` ".
            "    INNER JOIN " . $tb[USERS] . " um ON " . REVIEW_REWARDER . ".giver_id = um.id ".
            "    INNER JOIN " . $tb[USERS] . " uo ON " . REVIEW_REWARDER . ".receiver_id = uo.id ".
            "  WHERE ". PERIODS . ".`typeRC` = 'C' " 
            ;
        $sqlCreateView = "CREATE OR REPLACE VIEW " . $tb[REDEEM] . "  AS ".$sqlFields.$table_filter;
        
        $ret = mysql_unbuffered_query($sqlCreateView,$this->link);
        if ($ret) {
             return  array('msg' => "redeem created in instance: " . $instance . " ** " .$sqlCreateView) ;
      } else {
            return  array('error' => "error SQL in changeRedeemStatus" . mysql_error () . " SQL: " . $sqlCreateView) ;
        }
    }
    
    public function verifyActiveInstances() {
        $instances = new Instances();
        $actives = $instances->getActiveInstancesName();
        $activesWithError = array();
        $activesAvailable = array();
        $jj=0;
        if (count($actives) == 0){
            die("getActiveInstancesName return no active instance");
        }
        while ($jj < count($actives)) {
            $ret = $this->checkRedeemViewForInstance($actives[$jj]);
            if (isset($ret['error'])) {
                $activesWithError[] = array("error" => $ret['error']);
            } else {
                $activesAvailable[] = $actives[$jj];
            }
            $jj++;
        }
        if (count($activesAvailable) == 0){
            die("We found ".count($actives) . " active instances, but the redeem view creation return error in all those instances".var_dump($activesWithError));
        }
        $_SESSION["activesInstancesWithError"] = $activesWithError;
        return $activesAvailable;
    }
    
    public function getSelectUnionForInstances($activesInstancesAvailable,$sqlFilterRequestSent) {
        $jj=0;
        $sqlCount="";
        while ($jj < count($activesInstancesAvailable)) {
            if ($jj > 0) {
                $sqlCount .= " UNION ";
            }
            $sqlCount .= "SELECT '" . $activesInstancesAvailable[$jj] . "' AS instance, ". REDEEM .".* FROM ". 
                        $activesInstancesAvailable[$jj] . "." . REDEEM . " " . $sqlFilterRequestSent ;
            $jj++;
        }
        return $sqlCount;
    }
    
    /*
    Return the list of periods and for each period some informations 
    about the number of members linked to the periods
    */
    public function getRedeemPeriodsList($page,$limit,$sidx,$sord){

        $sqlFilterRequestSent = " WHERE ( paid = 3 OR paid = 4 OR paid = 6 OR paid = 7)";
        $activesInstancesAvailable = $this->verifyActiveInstances();
        $sqlCount = $this->getSelectUnionForInstances($activesInstancesAvailable,$sqlFilterRequestSent);
        if ( $sqlCount == "" ){
            die("No active instance found"); 
        }        
        $result = mysql_query($sqlCount,$this->link) or die("Couldn t execute query.".mysql_error());  
        $count = mysql_num_rows($result); 
        if( $count >0 ) { 
            $total_pages = ceil($count/$limit); 
        } else { 
            $total_pages = 0; 
        } 
        if ($page > $total_pages) {
            $page=$total_pages;
        }    
        $responce->page = $page; 
        $responce->total = $total_pages; 
        $responce->records = $count; 
        if ($count >0 ) { 
            $start = $limit*$page - $limit; // do not put $limit*($page - 1)
            $sql = $sqlCount .
                "  ORDER BY $sidx $sord LIMIT $start , $limit";
            $result = mysql_query( $sql,$this->link ) or die("Couldn t execute query.".mysql_error() . " SQL: ".$sql); 
            $i=0; 
            while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
                $responce->rows[$i]['id']=$row['id']; 
                $actionAvailable = "";
                $redeemed="No";
                $idsAttribute = "idperiod='" . $row['id'] .  "'  idowner='" . $row['idowner'] .  "'  idinstance='". $row['instance'] .  "' ";
                // period published
                if (  $row['periodStatus'] == 2 ) {
                    if ($row['budget_validated'] == 'Y' ) {
                        if ( $row['paid'] == 5 ) {
                            $redeemed = "no, available";
                        } else if ( $row['paid'] == 1 ) {
                            $redeemed = "yes";
                        } else if ( $row['paid'] == 2 ) {
                            $redeemed = "in redeem cart";
                        } else if ( $row['paid'] == 3 ) {
                            $actionAvailable = " <input type='checkbox' class='redeem_in_cart_of_sales' ".$idsAttribute." />";
                            $redeemed = "redeem request sent";
                        } else if ( $row['paid'] == 4 ) {
                            $redeemed = "redeem request running";
                        } else if ( $row['paid'] == 6 ) {
                            $actionAvailable = " <input type='checkbox' class='redeem_out_cart_of_sales' ".$idsAttribute." checked='checked' />";
                            $redeemed = "redeem request running, in sales cart";
                        } else {
                            $redeemed = "redeem paid status invalid";
                        }
                    } else {
                        $redeemed = "nop, not funded";
                    }
                } else {
                    $redeemed = "nop, not published";
                }
                if ( $row['periodStatus'] == 1 ) {
                    $status = "open";
                } else if ( $row['periodStatus'] == 2 ) {
                    $status = "published";
                } else {
                    $status = "closed";
                }
                $actionsDebug = "<span class='api_for_debug' >(API:  ".
                                "<a href='javascript:;' class='simulate_API_redeem_transaction_started' ".$idsAttribute." >Ru</a> ".
                                "<a href='javascript:;' class='simulate_API_redeem_transaction_validated' ".$idsAttribute." > Va</a> ".
                                "<a href='javascript:;' class='simulate_API_redeem_transaction_canceled' ".$idsAttribute." > Ca</a> )</span>"
                                ;
                $responce->rows[$i]['cell']=array($actionAvailable."(".$row['paid'].")",$row['instance'],$row['title'],$row['start_date'],$row['end_date'],
                                                $row['manager'],$row['managerUsername'],$row['owner'],$row['ownerUsername'],
                                                $status,
                                                $row['rewarded_percentage'],$row['paid_amount'],$redeemed,$row['sent_by_receiver'],$actionsDebug,$sql ); 
                $i++; 
            } 
        }
        return $responce; 
    }
     
    
    public function getReport() {
        $sep=",";
        $ex_report = array();
        $sqlFilterRequestSent = " WHERE  paid = 6 ";
        $activesInstancesAvailable = $this->verifyActiveInstances();
        $sqlCount = $this->getSelectUnionForInstances($activesInstancesAvailable,$sqlFilterRequestSent);
        if ( $sqlCount == "" ){
             die("No active instance found"); 
        }
        
        $sql = $sqlCount . "  ORDER BY ownerUsername";
        $result = mysql_query( $sql,$this->link ) or die("Couldn t execute query.".mysql_error() . " SQL: ".$sql); 
        while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
            $title=$row['title'];
            $ex_report[] ="'".$row['instance'].$sep.$row['ownerUsername'].
                                $sep.$row['owner'].$sep.$row['paid_amount'].
                                $sep.$title.$sep.$row['start_date'].$sep.$row['end_date'].
                                $sep.$row['manager'].$sep.$row['managerUsername'].$sep.$row['numberLovesReceived']."'" ; 
        } 
        return $ex_report;
    }
     
    
    public function exportRedeemRequests() {
        $report = $this->getReport();
        
        // Create with headers
        $csv = "Instance,ownerUsername,owner,Amount,title,start_date,end_date,manager,managerUsername,numberLovesReceived\n";
        foreach ($report as $item) {
                $csv .=  $item."\n";
        }
        
        // Output headers to force download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="Redeem Requests Report.csv"');
        echo $csv;
        
    }
    
    public function getRedeemRequests() {
        $ex_report = array();
        $redeem_to_process = array();
        $sqlFilterRequestSent = " WHERE  paid = 6 ";
        $activesInstancesAvailable = $this->verifyActiveInstances();
        $sqlCount = $this->getSelectUnionForInstances($activesInstancesAvailable,$sqlFilterRequestSent);
        if ( $sqlCount == "" ){
             die("No active instance found"); 
        }
        $sql = $sqlCount  .
            "  ORDER BY ownerUsername";
        $result = mysql_query( $sql,$this->link ) or die("Couldn t execute query.".mysql_error() . " SQL: ".$sql); 
        while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
            $ex_report[] =array(
                'instance' => $row['instance'],
                'ownerUsername' => $row['ownerUsername'],
                'owner' => $row['owner'],
                'paid_amount' => $row['paid_amount'],
                'manager' => $row['manager'],
                'managerUsername' => $row['managerUsername'],
                'comment' => $row['title'] . " from " . $row['start_date'] . " to " .$row['end_date']  
                ) ;
            $redeem_to_process[] = array(
                'idowner' => $row['idowner'],
                'idperiod' => $row['id'],
                'instance' => $row['instance']
            );
        } 
        $_SESSION['redeem_to_process'] = $redeem_to_process;
        return $ex_report;       
    }
    
    private function doQuery($sql){
        $result = mysql_query($sql,$this->link) or error_log("Review.doQuery:".mysql_error()."\n".$sql);;
        $ret = array();

        while($obj = mysql_fetch_assoc($result)){
            $ret[] = $obj;
        }
        return $ret;
    }

}
