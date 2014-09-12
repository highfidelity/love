<?php
/****
Class Campaign - this class in responsible for campaign part of Love application
    A campaign is a recognition period (the name has been changed).
    This period has the following information :
        - dates (start/end)
        - title
        - a budget 
        - the financial status (budget_validated: Yes, No, Card, Running), 
        - the status of the period (periodStatus: open, published, closed)
    For each period, there is a list of team members.At the end of the process, those persons will receive a part of the budget.
    When the recognition period is funded by the manager, then the manager can make the rewarder graph to allow some points ($) to the team members.
    When the manager has completed the repartition, he can publish the period.
    At this point, there is no change possible in the period, the team member can ask to redeem their gains.
****/


class Campaign{

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

    public function redeem_notify($redeemDetails) {
        $instanceDB = "";
        if (defined('DB_NAME')) {
            $instanceDB =  DB_NAME;
        }
        $msg = '';
        $msg .= '<p>Here is the redeem info:<br />';
        $msg .= 'Email         : '. $this->user_email .'<br />';
        $msg .= 'User ID       : '. $this->user_id.'<br />';
        $msg .= 'Database      : '. $instanceDB .'<br />';
        $msg .= 'Redeem Info      : '. $redeemDetails .'<br />';
        

        sl_send_email('sales@lovemachineinc.com', 'Redeem request has been received for ' . $instanceDB, $msg);
        $msg = '';
        $msg .= '<p>This is the last redeem request(s) you sent to LoveMachine:<br />';
        $msg .=  $redeemDetails .'<br />';
        

        sl_send_email($this->user_email, 'Your last redeem requests', $msg);
    }
    

    public function checkoutSend($checkoutAdminEmailAddress,$checkoutDatabase,$checkoutTenant,$checkoutPeriods,$checkoutContactPhone,$checkoutComment,$totalBudgets,$fee,$totalBudgetsFee) {
        $msg = '';
        $msg .= '<p>Here is the checkout request info:<br />';
        $msg .= 'Admin emails       : '. $checkoutAdminEmailAddress .'<br />';
        $msg .= 'Database           : '. $checkoutDatabase.'<br />';
        $msg .= 'Tenant             : '. $checkoutTenant .'<br />';
        $msg .= 'Periods            : '. $checkoutPeriods .'<br />';
        $msg .= 'Total              : '. $totalBudgets .'<br />';
        $msg .= 'Fee                : '. $fee .'<br />';
        $msg .= 'Total included fee : '. $totalBudgetsFee .'<br />';
        $msg .= 'Contact phone      : '. $checkoutContactPhone .'<br />';
        $msg .= 'Comment            : '. $checkoutComment .'<br />';
        

        $ret = sl_send_email('sales@lovemachineinc.com', 'Checkout request for ' . $checkoutTenant , $msg);
        return  array('ret' => $ret ) ;
    }
    
    public function addCurrentAdminToListOfOwners($listOfOwners) {
        $dest = $listOfOwners;
        if (strpos($dest,$this->user_email.",") === false) {
            $dest .= $this->user_email;
        } 
        return $dest;
    }

    public function campaignChangeNotification($changeInfo,$period_id) {
        $campaignInfo = $this->getCampaignDetails(" ". PERIODS . ".`id`  = $period_id AND ". PERIODS . ".status > 0 ");
        if (isset($campaignInfo) && isset($campaignInfo['listOfOwners']) && $campaignInfo['listOfOwners'] != "") {
            $dest = $this->addCurrentAdminToListOfOwners($campaignInfo['listOfOwners']);

            sendTemplateEmail($dest, 'changeInCampaign', array(
                'changeInfo' => $changeInfo,
                'periodInfo' => $campaignInfo['table']
            ));	
        }
    }

    /***
    Redeem status is the field "paid" of REVIEW_REWARDER
        -> 0 : default value at record creation time 
        -> 5 : the campaign has been published, the redeem is available
        -> 2 : the team member put the redeem in the cart
        -> 3 : the team member sent the request to LM in order to get gift code
        -> 4 : the request has been received by LM, the paypal transaction is running but not yet validated/canceled
        -> 6 : the request has been received by LM, and the request is in Sales cart
        -> 1 : the request has been accepted and paid to the team member by LM
    
    **/
    
    public function changeRedeemStatus($idPeriod,$new_paid_status){
        $filter="";
        $dateUpdate = "";
        $changeInfo="";
        $periodFilter=" AND `period_id` = $idPeriod ";
        if ( $new_paid_status == 2 ) {             // set to Cart (2) if the previous status was No (5)
            $filter = " AND paid = 5 ";
            if ( $idPeriod == -2 ) {
                // all the periods should be set
                $periodFilter="";
            }
        } else if ( $new_paid_status == 5 ) {           
            $filter = " AND paid = 2 ";          // set to No (5)  if the previous status  was in cart (2)
            if ( $idPeriod == -2 ) {
                // all the periods should be set
                $periodFilter="";
            }
//            $filter .= " OR  paid = 7 ) ";         // set to No (5)  if the previous status was paypal running (cancel)
        } else if ( $new_paid_status == 3 ) {    // set to Redeem Request Sent if the previous status was In Cart
            $filter = " AND paid = 2 ";
            $dateUpdate = " , sent_by_receiver =  NOW() ";
            $periodFilter="";                   // all the in cart will be updated and set to sent (3)
            $redeemRequestDetails = $this->getRedeemRequestDetails($filter);
//        } else if ( $new_paid_status == 4 ) {    
//            $filter = " AND (paid = 3 ";             // set to Request Running if the previous status was Sent
  //          $filter .= " OR  paid = 6 ) ";         // set to Request Running  if the previous status was in Sales cart
//        } else if ( $new_paid_status == 6 ) {    // set to Request in Sales cart if the previous status was Sent
  //          $filter = " AND paid = 4 ";
//        } else if ( $new_paid_status == 7 ) {    // set to Paypal transaction running if the previous status was in Sales Cart
  //          $filter = " AND paid = 6 ";
//        } else if ( $new_paid_status == 1 ) {    // set to Yes if the previous status was Paypal running
  //          $filter = " AND paid = 7 ";
        } else if ( $new_paid_status == "F" ) {    // force for debug
            $filter = "  ";
            $new_paid_status = 5;
        } else {
            $filter = " AND 1 = 0 ";
        }
        $sql = "UPDATE " . REVIEW_REWARDER . " SET `paid` = '$new_paid_status' $dateUpdate WHERE " . 
                REVIEW_REWARDER . ".`receiver_id` = {$this->user_id} $periodFilter $filter";
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            if ($dateUpdate != "") {
                $this->redeem_notify($redeemRequestDetails);
            }
        // the following test works only if the update is for 1 redeem, need to count before the number of redeem requested and compare with this number
            if (mysql_affected_rows() == 0) { 
                if ( $idPeriod != -2 ) {
                    return  array('error' => 'new status: '.$new_paid_status." is incompatible with current status: ") ;
                } else {
                    return  array('result' => "update new_paid_status: " ) ;
                }
            } else {
                return  array('result' => "update new_paid_status: " ) ;
            }
        } else {
            return  array('error' => 'error SQL in changeRedeemStatus' . mysql_error () ) ;
        }
    }
    
    public function getCampaignDetails($filter) {
        $table_filter = " FROM " . PERIODS . 
            "    LEFT JOIN " . USER_REVIEWS . " ON ". USER_REVIEWS . ".`period_id` = ". PERIODS . ".`id` ".
            "    INNER JOIN " . USERS . " um ON " . USER_REVIEWS . ".user_id = um.id ".
            "  WHERE ". PERIODS . ".`typeRC` = 'C' 
               AND $filter ";

        $sql = "SELECT ". PERIODS . ".`id`,title, start_date, end_date, um.nickname AS manager, um.username AS ownerEmail, 
            budget,budget_validated, ". PERIODS . ".`status` AS periodStatus " . $table_filter  .
            " ORDER BY manager "
            ;
        $result = mysql_query( $sql ) or die("Couldn t execute query.".mysql_error() . " ** " . $sql); 
        $tblStyle = 'font-family: Lucida Sans Unicode, Lucida Grande, Sans-Serif;font-size: 12px;width: 700px;text-align: left;border-collapse: collapse;margin: 20px;';
        $tblHStyle = 'font-size: 14px;font-weight: normal;color: #039;padding: 10px 8px;';
        $tblCStyle = 'color: #669;padding: 8px;';
        $tblOStyle = 'background: #e8edff;';

        $tbl  =	'<table border="0" style="' . $tblStyle . '">' . "\n";
        $tbl .=		'<thead>' . "\n";
        $tbl .=			'<tr>' . "\n";
        $tbl .=				'<th style="' . $tblHStyle . '">Title</th>' . "\n";
        $tbl .=				'<th style="' . $tblHStyle . '">Start Date</th>' . "\n";
        $tbl .=				'<th style="' . $tblHStyle . '">End Date</th>' . "\n";
        $tbl .=				'<th style="' . $tblHStyle . '">Manager</th>' . "\n";
        $tbl .=				'<th style="' . $tblHStyle . '">Budget</th>' . "\n";
        $tbl .=			'</tr>' . "\n";
        $tbl .=		'</thead>' . "\n";
    
        $tbl .=		'<tbody>' . "\n";
        $owners = '';
        $counter = 0;
        while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
            $counter++;
            $tbl .=		'<tr>' . "\n";
            $tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($row['title']) . '</td>' . "\n";
            $tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($row['start_date']) . '</td>' . "\n";
            $tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($row['end_date']) . '</td>' . "\n";
            $tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($row['manager']) . '</td>' . "\n";
            $tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($row['budget']) . '</td>' . "\n";
            $tbl .=		'</tr>' . "\n";
            if (strpos($owners , $row['ownerEmail'] .",") === false) {
                $owners .= $row['ownerEmail'] .",";
            } 
        }
        $tbl .= 	'</tbody>' . "\n";
    
        $tbl .=	'</table>' . "\n";

        return array('table' => $tbl, 'listOfOwners' => $owners);
    }
    
    public function getRedeemRequestDetails($filter) {
        $table_filter = $this->getTableForRedeemQuery();
        $sql = "SELECT ". PERIODS . ".`id`,title, start_date, end_date,  
            budget,budget_validated,paid_amount,sent_by_receiver,paid_date, ". PERIODS . ".`status` AS periodStatus,
            (rewarded_percentage * budget) / 100 AS value_of_loves,
        paid as redeemed,       
        COUNT(lo.id) AS numberLovesReceived, um.nickname AS manager, ". 
                REVIEW_REWARDER . ".`rewarded_percentage` AS rewarded_percentage, ". 
                REVIEW_REWARDER . ".`paid` AS paid " . $table_filter . $filter .
            "  GROUP BY id,title,start_date,end_date,budget,budget_validated,periodStatus,manager,rewarded_percentage";
        $result = mysql_query( $sql ) or die("Couldn t execute query.".mysql_error()); 
        $tblStyle = 'font-family: Lucida Sans Unicode, Lucida Grande, Sans-Serif;font-size: 12px;width: 700px;text-align: left;border-collapse: collapse;margin: 20px;';
        $tblHStyle = 'font-size: 14px;font-weight: normal;color: #039;padding: 10px 8px;';
        $tblCStyle = 'color: #669;padding: 8px;';
        $tblOStyle = 'background: #e8edff;';

        $tbl  =	'<table border="0" style="' . $tblStyle . '">' . "\n";
        $tbl .=		'<thead>' . "\n";
        $tbl .=			'<tr>' . "\n";
        $tbl .=				'<th style="' . $tblHStyle . '">Title</th>' . "\n";
        $tbl .=				'<th style="' . $tblHStyle . '">Start Date</th>' . "\n";
        $tbl .=				'<th style="' . $tblHStyle . '">End Date</th>' . "\n";
        $tbl .=				'<th style="' . $tblHStyle . '">Manager</th>' . "\n";
        $tbl .=				'<th style="' . $tblHStyle . '"># Loves</th>' . "\n";
        $tbl .=				'<th style="' . $tblHStyle . '">Paid Amount</th>' . "\n";
        $tbl .=			'</tr>' . "\n";
        $tbl .=		'</thead>' . "\n";
    
        $tbl .=		'<tbody>' . "\n";
        $counter = 0;
        $total=0;
        while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
            $counter++;
            $tbl .=		'<tr>' . "\n";
            $tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($row['title']) . '</td>' . "\n";
            $tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($row['start_date']) . '</td>' . "\n";
            $tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($row['end_date']) . '</td>' . "\n";
            $tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($row['manager']) . '</td>' . "\n";
            $tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">' . htmlentities($row['numberLovesReceived']) . '</td>' . "\n";
            $tbl .= 		'<td style="' . $tblCStyle . (($counter % 2) ? $tblOStyle : '') . '">$' . htmlentities(round($row['paid_amount'], 2)) . '</td>' . "\n";
            $tbl .=		'</tr>' . "\n";
            $total += round($row['paid_amount'], 2);
        }
        $tbl .= 	'</tbody>' . "\n";
    
        $tbl .=	'</table>' . "\n";
        $tbl .=	'Total requested: $' . $total . "\n";
        return $tbl;
    }
    
    public function getTableForRedeemQuery() {
        return " FROM " . PERIODS . 
            "    LEFT JOIN " . USER_REVIEWS . " ON ". USER_REVIEWS . ".`period_id` = ". PERIODS . ".`id` ".
            "    LEFT JOIN " . REVIEW_REWARDER . " ON ". REVIEW_REWARDER . ".`period_id` = ". PERIODS . ".`id` ".
            "    INNER JOIN " . USERS . " um ON " . REVIEW_REWARDER . ".giver_id = um.id ".
            "    LEFT JOIN " . LOVE . " lo ON " . PERIODS . ".start_date <= lo.at AND " . 
            PERIODS . ".end_date >= lo.at AND lo.receiver = '{$this->user_email}' ".
            "  WHERE ". PERIODS . ".`typeRC` = 'C' " .
            " AND " . REVIEW_REWARDER . ".`receiver_id` = {$this->user_id} ";
    }
    /*
    Return the list of periods and for each period some informations 
    about the number of members linked to the periods
    */
    public function getRedeemPeriodsList($page,$limit,$sidx,$sord){
        $table_filter = $this->getTableForRedeemQuery();
        $sqlCount = "SELECT ". PERIODS . ".`id` AS id,COUNT( *) AS count " . $table_filter 
                    . "  GROUP BY id"
                    ;        
        $result = mysql_query($sqlCount) or die("Couldn t execute query.".mysql_error());  
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
            if ($sidx == "periodStatus" && $sord == "asc") {
                $sidx = "closed," . $sidx;
            } else if ($sidx == "periodStatus" && $sord == "desc") {
                $sidx = "closed desc," . $sidx;
            }
            $sql = "SELECT ". PERIODS . ".`id`,title, start_date, end_date, IF (end_date > NOW(), 0 , 1) AS closed, IF (start_date > NOW(), 1 , 0) AS notstarted, 
                budget,budget_validated,paid_amount,sent_by_receiver,paid_date, ". PERIODS . ".`status` AS periodStatus,
            IF (".PERIODS.".status = 2, (rewarded_percentage * budget) / 100, 0) AS value_of_loves,
            paid as redeemed,       
            COUNT(lo.id) AS numberLovesReceived, um.nickname AS manager, ". 
                    REVIEW_REWARDER . ".`rewarded_percentage` AS rewarded_percentage, ". 
                    REVIEW_REWARDER . ".`paid` AS paid " . $table_filter .
                "  GROUP BY id,title,start_date,end_date,budget,budget_validated,periodStatus,manager,rewarded_percentage".
                "  ORDER BY  $sidx $sord LIMIT $start , $limit";
            $result = mysql_query( $sql ) or die("Couldn t execute query.".mysql_error()); 
            $i=0; 
            while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
                $responce->rows[$i]['id']=$row['id']; 
                $actionAvailable = "";
                $redeemed="No";
                // period published
                if (  $row['periodStatus'] == 2 ) {
                    if ($row['budget_validated'] == 'Y' ) {
                        if ( $row['paid'] == 5 ) {
                            $actionAvailable = " <input type='checkbox' class='change_redeem_paid_status_cart' idperiod='" . 
                                        $row['id'] . "' />";
                            $redeemed = "no, available";
                        } else if ( $row['paid'] == 1 ) {
                            $redeemed = "<span title='Paid:" . $row['paid_date'] . ", sent:" . $row['sent_by_receiver'] . "'>yes</span>";
                        } else if ( $row['paid'] == 2 ) {
                            $actionAvailable = " <input type='checkbox' class='change_redeem_paid_status_not_cart' idperiod='" . 
                                        $row['id'] .  "'  checked='checked'  />";
                            $redeemed = "in redeem cart";
                        } else if ( $row['paid'] == 3 ) {
                            $redeemed = "<span title='Sent: " . $row['sent_by_receiver'] . "'>redeem request sent</span>" ;
                        } else if ( $row['paid'] == 4  ) {
                            $redeemed = "redeem request running";
                        } else if (  $row['paid'] == 6  ) {
                            $redeemed = "redeem request running (SC)";
                        } else if (  $row['paid'] == 7 ) {
                            $redeemed = "redeem request running (SR)";
                        } else {
                            $redeemed = "redeem paid status invalid";
                        }
                    } else {
                        $redeemed = "nop, not funded";
                    }
                } else {
                    $redeemed = "nop, not published";
                }
                // if the end date of the period is past, the status displayed is "closed"
                if ( $row['notstarted'] == 1 ) {
                    $status = "Not started";
                } else if ( $row['closed'] == 1 ) {
                    $status = "closed";
                } else {
                    if ( $row['periodStatus'] == 1 ) {
                        $status = "open";
                    } else if ( $row['periodStatus'] == 2 ) {
                        $status = "published";
                    } else {
                        $status = "closed";
                    }
                }
				
                $actionsDebug = "<span class='api_for_debug' >(API:  ".
                                "<a href='javascript:;' class='reset_redeem_transaction' idperiod='" . 
                                $row['id'] . "' > Reset</a>) </span>";
                if ( $row['periodStatus'] != 2 ) {
					$row['paid_amount']="";
				}
                /*** Keep the following comments, the functionality could come back later (#16400) 
                $responce->rows[$i]['cell']=array($actionAvailable,$row['title'],$row['start_date'],$row['end_date'],$row['manager'],$status,
                                                $row['numberLovesReceived'],$row['paid_amount'],$redeemed,$actionsDebug,$sql ); 
                ***/                                                
                $responce->rows[$i]['cell']=array($row['title'],$row['start_date'],$row['end_date'],$row['manager'],$status,
                                                $row['numberLovesReceived'],$actionsDebug,$sql ); 
                $i++; 
            } 
        }
        return $responce; 
    }
    /*
    Return the total of paid amount that have been already redeemed for the current user
    and the total of paid amount that are in cart
    */
    public function getRedeemTotal(){
            // Calculate the total of redeemed 
            $total_redeemed = 0;
            $sqlCommon = "SELECT SUM(paid_amount) AS total FROM " . REVIEW_REWARDER . 
            "    INNER JOIN " . PERIODS . " ON ". REVIEW_REWARDER . ".`period_id` = ". PERIODS . ".`id` ".
            "  AND ". PERIODS . ".`typeRC` = 'C' " .
            "  WHERE  " . REVIEW_REWARDER . ".`receiver_id` = {$this->user_id} " ;
            $sql = $sqlCommon . " AND paid = 1 ";
            $result = mysql_query( $sql ) or die( "Couldn t execute query.".mysql_error()); 
            if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
                $total_redeemed = isset( $row['total'] ) ? $row['total']  : 0;
            }
            // Calculate the total of redeem in cart
            $total_redeem_in_cart = 0;
            $sql = $sqlCommon . " AND paid = 2 ";
            $result = mysql_query( $sql ) or die( "Couldn t execute query.".mysql_error()); 
            if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
                $total_redeem_in_cart = isset( $row['total'] ) ? $row['total']  : 0;
            }
            // Calculate the total of redeem pending
            $total_redeem_pending = 0;
            $sql = $sqlCommon . " AND ( paid = 3 OR paid = 4)  ";
            $result = mysql_query( $sql ) or die( "Couldn t execute query.".mysql_error()); 
            if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
                $total_redeem_pending = isset( $row['total'] ) ? $row['total']  : 0;
            }
            // Calculate the total of redeem available
            $total_redeem_available = 0;
            $sql = $sqlCommon . " AND  paid = 5  ";
            $result = mysql_query( $sql ) or die( "Couldn t execute query.".mysql_error()); 
            if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
                $total_redeem_available = isset($row['total']) ? $row['total'] : 0 ;
            }
        return  array(
            'total_redeemed' => $total_redeemed ,
            'total_redeem_in_cart' => $total_redeem_in_cart,
            'total_redeem_pending' => $total_redeem_pending,
            'total_redeem_available' => $total_redeem_available
        ) ;
    }
    
    
    /***
    Calculate the redeem amount and set the redeem status to 5 
    At this point, the team member can ask to redeem this amount
    ***/
    public function payCampaign($period_id){
        // Calculate the total of redeemed 
        $sql = "UPDATE " . REVIEW_REWARDER . "," . PERIODS .
            " SET paid_amount = (rewarded_percentage * budget / 100), paid=5 WHERE " . 
            REVIEW_REWARDER . ".`period_id` = ". PERIODS . ".`id` AND ".
            REVIEW_REWARDER . ".`period_id` = $period_id " 
            ;
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            sendTemplateEmail($this->user_email, 'publishedCampaign', array(
                'export_data' => $this->exportRedeemByCampaignContent($period_id,"\n")
            ));	
            $this->campaignChangeNotification("The recognition period has been published.",$period_id);
            return  array('result' => "Campaign paid! " ) ;
        } else {
            return  array('error' => 'error SQL in payCampaign' . mysql_error () . " SQL: " . $sql) ;
        }
    }
    
    
    public function publishCampaign($period_id){
    // First verify that there is at least 1 person with at least 1 Love in the period
        $sql = "SELECT SUM(rewarder_points) AS total FROM " . REVIEW_REWARDER . 
        "    INNER JOIN " . PERIODS . " ON ". REVIEW_REWARDER . ".`period_id` = ". PERIODS . ".`id` ".
        "  AND ". PERIODS . ".`typeRC` = 'C' " .
        "  WHERE  ". REVIEW_REWARDER . ".`period_id` = $period_id " ;
        $result = mysql_query( $sql ) or die( "Couldn t execute query.".mysql_error()); 
        if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
            $total_paid = isset( $row['total'] ) ? $row['total']  : 0;
        }
    // Change the status in the periods table
       $sql = "UPDATE " . PERIODS . " SET `status` = 2 WHERE `id` = $period_id AND status = 1 AND budget_validated = 'Y' ";
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            if (mysql_affected_rows() != 1) {
                return  array('error' => 'new status: published is incompatible with current status: ') ;
            } else {
                return  $this->payCampaign($period_id);
            }
        } else {
            return  array('error' => 'error SQL in publishCampaign' . mysql_error () . " SQL: " . $sql) ;
        }
    }
    
    
    /***
    The 2 following functions are used only for debugging purpose.
    The application should not allow to unPay some campaigns
    ***/
    public function unPayCampaign($period_id){
        // Calculate the total of redeemed 
        $sql = "DELETE " . REVIEW_REWARDER . " FROM " . REVIEW_REWARDER . "," . PERIODS .
            "  WHERE " . 
            REVIEW_REWARDER . ".`period_id` = ". PERIODS . ".`id` AND ".
            REVIEW_REWARDER . ".`period_id` = $period_id " 
            ;
        $ret = mysql_unbuffered_query($sql);
                if ($ret) {
                    return  array('result' => "Campaign paid! " ) ;
                } else {
                    return  array('error' => 'error SQL in payCampaign' . mysql_error () . " SQL: " . $sql) ;
                }
    }
    
    
    public function unPublishCampaign($period_id){
        $sql = "UPDATE " . PERIODS . " SET `status` = 0 WHERE `id` = $period_id  ";
        $ret = mysql_unbuffered_query($sql);
        if ($ret) {
            return  $this->unPayCampaign($period_id);
        } else {
            return  array('error' => 'error SQL in unPublishCampaign' . mysql_error () . " SQL: " . $sql) ;
        }
    }
    /*
    Return the list of periods and for each period some informations 
    about the number of members linked to the periods
    The result is used in the campaign.php screen
    */

    public function getPeriodsList($page,$limit,$sidx,$sord,$displayAllMy = "my"){
        $campaignFilter = " ";
        if ( $displayAllMy == "my") {
            $campaignFilter = "  AND " . USER_REVIEWS . ".`user_id` = {$this->user_id} ";
        } 
        $table_filter = " FROM " . PERIODS . 
            "    LEFT JOIN " . USER_REVIEWS . " ON ". USER_REVIEWS . ".`period_id` = ". PERIODS . ".`id` ".
            "    LEFT JOIN " . REVIEW_REWARDER . " ON ". REVIEW_REWARDER . ".`period_id` = ". PERIODS . ".`id` ".
            "    INNER JOIN " . USERS . " oT ON ". USER_REVIEWS . ".`user_id` = oT.id ".
             "  WHERE ". PERIODS . ".`typeRC` = 'C' " . $campaignFilter .
            " ";
 //            " AND (" . REVIEW_REWARDER . ".`giver_id` = {$this->user_id}  OR (" . 
  //                  REVIEW_REWARDER . ".`giver_id` IS NULL AND " . USER_REVIEWS . ".`user_id` = {$this->user_id} ))";
 //           " AND " . USER_REVIEWS . ".`user_id` = {$this->user_id} ";
        $sqlCount = "SELECT ". PERIODS . ".`id` AS id,COUNT( *) AS count " . $table_filter. "  GROUP BY id";        
        $result = mysql_query($sqlCount) or die("Couldn t execute query.".mysql_error());  
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
            $SQL = "SELECT ". PERIODS . ".`id`,title, start_date, end_date, " .
                " oT.`nickname` AS owner, oT.`username` AS ownerEmail, " .
                " IF (end_date > NOW(), 0 , 1) AS closed, budget,budget_validated, ". PERIODS . ".`status`,COUNT(". 
                    REVIEW_REWARDER . ".`period_id`) AS numberMembers " . $table_filter .
                "  GROUP BY id,title,start_date,end_date".
                "  ORDER BY $sidx $sord LIMIT $start , $limit";

            $result = mysql_query( $SQL ) or die("Couldn t execute query.".mysql_error()); 
            $i=0; 
            while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
                $responce->rows[$i]['id']=$row['id']; 
                $teamMemberNumber =  "<span class='yesTeamMumber'>". $row['numberMembers']. "</span>";
                $teamMemberAddLibel = "Add - Remove";
                if ($row['numberMembers'] == 0) {
                    $teamMemberNumber = "<span class='noTeamMumber'>". $row['numberMembers'] . "</span>";
                    $teamMemberAddLibel = "Add";
                }
                $actionAvailable ="";
                if ( $row['numberMembers'] == 0 && $row['status'] == 1 ) {
                    $addMembers = $teamMemberNumber . " (<a href='javascript:;' class='grid_actions add_members_in_campaign' idperiod='" . 
                                $row['id'] . "'>$teamMemberAddLibel</a>)";
                    if ($row['budget_validated'] == 'N' ) {
                        $actionAvailable = " <a href='javascript:;' class='grid_actions delete_period' idperiod='" . 
                                $row['id'] . "'>Delete</a> ";
                    }
                } else {
                    $addMembers = $teamMemberNumber . " (<a href='javascript:;' class='grid_actions add_members_in_campaign' idperiod='" . 
                                $row['id'] . "'>$teamMemberAddLibel</a>) ";
                    $actionAvailable ="";
                    if ($row['budget_validated'] == 'Y' && $row['status'] == 1 ) {
                    } else if ( $row['status'] == 0 ) {
                        $actionAvailable .= "<a href='javascript:;' class='grid_actions set_start' idperiod='" . 
                                $row['id'] . "'>Start</a> ";
                        if ($row['numberMembers'] == 0 && $row['budget_validated'] == 'N' ) {
                            $actionAvailable .= "<a href='javascript:;' class='grid_actions delete_period' idperiod='" . 
                                $row['id'] . "'>Delete</a> ";
                        }
                    } 
                }
                $actionAvailable .= "<a href='javascript:;' class='grid_actions copy_period' idperiod='" . 
                        $row['id'] . "'>Copy</a>";
                $actionAvailable .= "<span class='api_for_debug' >(API: <a href='javascript:;' class='grid_actions simulate_API_transaction_started' idperiod='" . 
                                $row['id'] . "' >Ru</a> ".
                                "<a href='javascript:;' class='grid_actions simulate_API_transaction_validated' idperiod='" . 
                                $row['id'] . "' > Va</a> ".
                                "<a href='javascript:;' class='grid_actions simulate_API_transaction_canceled' idperiod='" . 
                                $row['id'] . "' > Ca</a> ".
                                "<a href='javascript:;' class='grid_actions reset_transaction' idperiod='" . 
                                $row['id'] . "' > Reset</a>) </span>";
                // if the end date of the period is past, the status displayed is "closed"
                if ( $row['closed'] == 1 ) {
                    $status = "Closed";
                } else {
                    /***
                    Campaign status is the field 'status' of table PERIODS
                        0 -> the campaign has not been started, it's the default value at creation time
                        1 -> the campaign is open
                        2 -> the campaign is published, at this point update of the campaign record should be forbidden
                        3 -> the campaign is closed, probably means cancelled or not yet published
                    ***/
                    if ( $row['status'] == 0 ) {
                        $status = "Not started";
                    } else if ( $row['status'] == 1 ) {
                        $status = "Open";
                    } else if ( $row['status'] == 2 ) {
                        $status = "Published";
                        $addMembers = $teamMemberNumber;
                    } else if ( $row['status'] == 3 ) {
                        $status = "Closed";
                    } else {
                        $status = "Invalid";
                    }
                }
                $budgetValidatedTitle = $row['budget_validated'];
                $budgetTransactionIsRunning = " ";
                $checkbox = "";
                if ($row['budget_validated'] == 'N') {
                    $budgetValidatedTitle = "<span class='fundedNo'>No</span>";
                    $checkbox = "<input type='checkbox' class='change_budget_validated_running' idperiod='" . $row['id'] . "'  />".
                                "<input type='button' value='Buy' class='buy_budget_validated_running' idperiod='" . $row['id'] . "'  /> "
                                    ;
                }  else if ($row['budget_validated'] == 'C') {
                    $budgetValidatedTitle = "No, in cart";
                    $checkbox = "<input type='checkbox' class='change_budget_validated_no' idperiod='" . $row['id'] . "'  checked='checked' />".
                                    "<input type='button' value='Buy' class='buy_budget_validated_no' idperiod='" . $row['id'] . "'  /> "
                    ;
                }  else if ($row['budget_validated'] == 'Y') {
                    $budgetValidatedTitle = "<span class='fundedYes'>Yes</span>";
                }  else if ($row['budget_validated'] == 'R') {
                    $budgetValidatedTitle = "Pending";
                }
                
                    $order =  $checkbox ;
                    $budget_validated = $budgetValidatedTitle;
                $statusURL = "<a href='javascript:;' class='status_action_" . $status . "' idperiod='" . $row['id'] . "'>" . $status . "</a>";
                $responce->rows[$i]['cell']=array($row['title'],$row['owner'],$row['start_date'],$row['end_date'],$status,
                                                $row['budget'],/*  #16402 temporary removal $budgetValidatedTitle,$order , */ 
                                                $addMembers,$actionAvailable,$row['status'],$row['id'] ); 
                $i++; 
            } 
        }
        return $responce; 
    }
    /*
    Return the list of users of the company and for each user the information
    if the user is linked to the period or not
    */
    public function getUsersList($page,$limit,$sidx,$sord,$period_id,$searchFilter){
        global $front;
        $filter = "";
        if ( isset($searchFilter) && $searchFilter != "") {
            $filter = " AND username LIKE '%" . mysql_escape_string($searchFilter) . "%' ";
        }
        $result = mysql_query("SELECT COUNT(*) AS count FROM " . USERS . 
                        " WHERE " . USERS . ".company_id = '".$front->getCompany()->getId()."' " . $filter
                ); 
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
        $sql = "SELECT " . USERS . ".`id`, " . USERS . ".`username`, " . USERS . ".`nickname`, 
                IFNULL(" . REVIEW_REWARDER . ".`rewarder_points`,-1) AS `points` FROM " . USERS . " 
                    LEFT JOIN " . REVIEW_REWARDER . " ON " . USERS . ".`id` = " . REVIEW_REWARDER . ".`receiver_id` 
                        AND " . REVIEW_REWARDER . ".`period_id` = $period_id ".
                " WHERE " . USERS . ".company_id = '".$front->getCompany()->getId()."' ". $filter .
                "  GROUP BY id,username,nickname,points".
                "  ORDER BY $sidx $sord LIMIT $start , $limit";
            
        $result = mysql_query( $sql ) or die("Couldn t execute query.".mysql_error() . $sql); 
        $responce->page = $page; 
        $responce->total = $total_pages; 
        $responce->records = $count; 
        $i=0; 
        while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
            $responce->rows[$i]['id']=$row['id']; 
            if ( $row['points'] == -1 ) {
                $addMembers = " (<a href='javascript:;' class='add_user_in_campaign' idperiod='" . 
                            $period_id . "' iduser='" . 
                            $row['id'] . "'>Add</a>) ";
            } else {
                $addMembers = " (<a href='javascript:;' class='remove_user_from_campaign' idperiod='" . 
                            $period_id . "' iduser='" . 
                            $row['id'] . "'>Remove</a>) ";
            }
            $responce->rows[$i]['cell']=array($row['username'],$row['nickname'],
                                            $addMembers,$row['id'] ); 
            $i++; 
        } 
        return $responce; 
    }
    /*
    Add a new user in the team or add all the users in the team
    */
    public function addUser($period_id,$user_id,$searchFilter=""){
        global $front;
        if ($user_id == -2) {
            $filter = "";
            if ( isset($searchFilter) && $searchFilter != "") {
                $filter = " AND username LIKE '%" . mysql_escape_string($searchFilter) . "%' ";
            }
/*            $sql = "INSERT INTO " . REVIEW_REWARDER . " (`giver_id`,`receiver_id` ,`period_id` ,`rewarder_points`)
                        SELECT  {$this->user_id}, id, '$period_id', 0
                            FROM ". USERS .
                            " WHERE " . USERS . ".company_id = ".$front->getCompany()->getId()." ". $filter .
                            " AND NOT id IN (SELECT receiver_id FROM " . REVIEW_REWARDER . " WHERE period_id = $period_id )"; */
            $sql = "INSERT INTO " . REVIEW_REWARDER . " (`giver_id`,`receiver_id` ,`period_id` ,`rewarder_points`)
                        SELECT  " . USER_REVIEWS . ".user_id , " . USERS . ".id, '$period_id', 0
                            FROM ". USERS . "," . USER_REVIEWS .
                            " WHERE " . USERS . ".company_id = ".$front->getCompany()->getId()." ". $filter .
                            " AND NOT " . USERS . ".id IN (SELECT receiver_id FROM " . REVIEW_REWARDER . " WHERE period_id = $period_id )
                              AND " . USER_REVIEWS . ".period_id = $period_id "; 
        } else {
/*            $sql = "INSERT INTO " . REVIEW_REWARDER . " (`giver_id`,`receiver_id` ,`period_id` ,`rewarder_points`)
                        VALUES ( {$this->user_id}, '$user_id', '$period_id', 0)"; */
            $sql = "INSERT INTO " . REVIEW_REWARDER . " (`giver_id`,`receiver_id` ,`period_id` ,`rewarder_points`)
                        SELECT  user_id, '$user_id', '$period_id', 0
                            FROM ". USER_REVIEWS .
                            " WHERE period_id = $period_id "; 
        }
        $res = mysql_unbuffered_query($sql) or die("Couldn t execute query.".mysql_error() . $sql); 
        $this->campaignChangeNotification("New member(s) added to the team of the recognition period.",$period_id);
        return array($res);
    }
    /*
    Remove a user from the team or remove all the users from the team
    */
    public function deleteUser($period_id,$user_id,$searchFilter=""){
        global $front;
        if ($user_id == -2) {
            $filter = "";
            if ( isset($searchFilter) && $searchFilter != "") {
                $filter = " AND username LIKE '%" . mysql_escape_string($searchFilter) . "%' ";
            }
            $sql = "DELETE " . REVIEW_REWARDER . " FROM " . REVIEW_REWARDER . 
                    " INNER JOIN ". USERS . " ON receiver_id = " . USERS . ".id " .
                    " AND " . USERS . ".company_id = ".$front->getCompany()->getId()." ". $filter .
                    " WHERE period_id = $period_id "; 
        } else {
            $sql = "DELETE FROM " . REVIEW_REWARDER . 
                    " WHERE receiver_id = '$user_id'
                         AND period_id = '$period_id' "; 
        }
        $res = mysql_unbuffered_query($sql) or die("Couldn t execute query.".mysql_error() . $sql); 
        $this->campaignChangeNotification("Team member(s) removed from the recognition period.",$period_id);
        return array($res);
    }

    /*
    Return the information required by the payment page
        - url
        - instance
        - total
        - Campaigns summary
        - list of campaign ids
    */
        
    public function checkout(){
        $table_filter = " FROM " . PERIODS . 
            "    LEFT JOIN " . USER_REVIEWS . " ON ". USER_REVIEWS . ".`period_id` = ". PERIODS . ".`id` ".
            "    INNER JOIN " . USERS . " um ON " . USER_REVIEWS . ".user_id = um.id " .
            "  WHERE ". PERIODS . ".`typeRC` = 'C' " 
            ; 
            /*.
            " AND " . USER_REVIEWS . ".`user_id` = {$this->user_id} ";*/
        $sqlCount = "SELECT SUM( budget) AS total_budget " . $table_filter . " AND budget_validated = 'C'";        
        $result = mysql_query( $sqlCount ) or die("Couldn t execute query.".mysql_error() . $sqlCount); 
        $row = mysql_fetch_array($result,MYSQL_ASSOC); 
        $totalBudgets = $row['total_budget']; 

        $sqlIds = "SELECT " . PERIODS . ".id,title,budget,um.username AS ownerEmail,".
                   " DATE_FORMAT(start_date,' %Y, %M %D ') AS start_date, ".
                   " DATE_FORMAT(end_date,'%Y, %M %D') AS end_date  " . $table_filter . " AND budget_validated = 'C'";        
        $result = mysql_query( $sqlIds ) or die("Couldn t execute query.".mysql_error() . $sqlIds); 
        $listCampaigns="";
        $infoCampaigns="";
        $owners = "";
        while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
            $listCampaigns .= $row['id'] . ","; 
            $infoCampaigns .= "<div class='campaignRecord'>" .
                $row['start_date'].
                "-" .$row['end_date'] . 
                " - " .$row['title'].
                " ($" .$row['budget']. ")".
                " <a style='display:none' href='displayCampaign.php?campaignId=" . $row['id'] . "' class='actionInEmail'></a></div>"; 
            if (strpos($owners , $row['ownerEmail'] .",") === false) {
                $owners .= $row['ownerEmail'] .",";
            } 
        }      
        $managerEmails = $this->addCurrentAdminToListOfOwners($owners);
       
        $this->saveInfoForSales($listCampaigns,$infoCampaigns,$totalBudgets,$managerEmails);
        $instanceDatabaseName = (defined('DB_NAME')) ? DB_NAME : "";
        $LMFee = 0.10; //5% for now... lets move to a constant instead
        $fee = $totalBudgets * $LMFee; 
        $totalBudgetsFee = $totalBudgets + $fee;
        return  array(  'result' => "checkout" ,
                        'url_buylovemachine' => '../sales/buylovemachine.php',                      
                        'instanceHost'  => $_SERVER['SERVER_NAME'],                      
                        'instanceDatabaseName'  => $instanceDatabaseName,
                        'managerEmails' => $managerEmails,
                        'listCampaigns' => $listCampaigns,                        
                        'infoCampaigns' => $infoCampaigns,
                        'totalBudgets' => $totalBudgets,
                        'fee' => $fee,
                        'totalBudgetsFee' => $totalBudgetsFee
                        ) ;
    }
    
    public function saveInfoForSales($listCampaigns,$infoCampaigns,$totalBudgets,$managerEmails) {
        $instanceDatabaseName = (defined('DB_NAME')) ? DB_NAME : "";
        $_SESSION['checkoutCampaign'] = array(
                        'user_id'   => $this->user_id,
                        'listCampaigns' => $listCampaigns,
                        'infoCampaigns' => urlencode($infoCampaigns),
                        'totalBudgets' => $totalBudgets,
                        'instanceHost'  => $_SERVER['SERVER_NAME'],                      
                        'instanceDatabaseName'  => $instanceDatabaseName,
                        'managerEmails' => $managerEmails 
                        ) ;
    }

    // Make a query and display sql error if any
    public function query($sql) {
        $res = mysql_query($sql) or die("Couldn't execute query.".mysql_error() . " ** " . $sql ); 
        return $res;
    }

    // Make a query and return the result in an array
    public function getStat($sql) {
        $rt = mysql_query($sql) or die("Couldn't execute query.".mysql_error() . " ** " . $sql); 

        $results = array();
        while($row = mysql_fetch_assoc($rt)){
            $results[] = $row;
        }
        return $results;
    }
 
    // Remove the temporary tables used by the populateCampaignProrataNormalized process
    public function populateCampaignProrataNormalizedDrop(&$debug_sql) {
        $sql = "DROP TEMPORARY TABLE IF EXISTS lovesGiver;";
        $this->query($sql); 
        $debug_sql .= $sql;
        $sql = "DROP TEMPORARY TABLE IF EXISTS totalMembersSenders;";
        $this->query($sql); 
        $debug_sql .= $sql;
        $sql = "DROP TEMPORARY TABLE IF EXISTS priceForGiver;";
        $this->query($sql); 
        $debug_sql .= $sql;
        $sql = "DROP TEMPORARY TABLE IF EXISTS priceForReceiver;";
        $this->query($sql); 
        $debug_sql .= $sql;
        $sql = "DROP TEMPORARY TABLE IF EXISTS populated;";
        $this->query($sql); 
        $debug_sql .= $sql;
    } 
        
    // Make the calculation of the repartition for the Prorata Normalized option
    public function populateCampaignProrataNormalizedCalculation($period_id,$return_stat,&$debug_sql) {
        // Calculate for each team member the number of loves sent
        $sql = "CREATE TEMPORARY TABLE lovesGiver
            SELECT SUM(totalGiverReceiver) AS loveSentByGiverInPeriod, giver
            FROM teamLovesStat
            GROUP BY giver
        ;";
        $this->query($sql); 
        $debug_sql .= $sql;

        // Calculate the number of team members that sent at least one love
        $sql = "
            CREATE TEMPORARY TABLE totalMembersSenders
            SELECT COUNT(*) AS totalMembersGivers
            FROM lovesGiver 
        ;";
        $this->query($sql); 
        $debug_sql .= $sql;

        /* Return the number of team members that sent at least one Love
        $sql = "
            SELECT totalMembersGivers
             FROM totalMembersSenders
        ;"); */
        // Calculate the price of the Loves for each givers : lovesPriceForGiver
        $sql = "
            CREATE TEMPORARY TABLE priceForGiver
            SELECT 1.0 / totalMembersGivers / loveSentByGiverInPeriod AS lovesPriceForGiver , loveSentByGiverInPeriod,giver
            FROM totalMembersSenders,lovesGiver
        ;";
        $this->query($sql); 
        $debug_sql .= $sql;

        /* Display the price of the Loves for each receiver - giver
        $sql = "
            SELECT teamLovesStat.receiver, teamLovesStat.giver, teamLovesStat.totalGiverReceiver * priceForGiver.lovesPriceForGiver AS priceOfLovesReceived
            FROM teamLovesStat
            INNER JOIN priceForGiver ON priceForGiver.giver = teamLovesStat.giver
        ;"); */
        // Calculate the price of all the Loves received by receiver (priceOfLovesReceived)
        $sql = "
            CREATE TEMPORARY TABLE priceForReceiver
            SELECT teamLovesStat.receiver,SUM( teamLovesStat.totalGiverReceiver) AS numberOfLoves,SUM( teamLovesStat.totalGiverReceiver * priceForGiver.lovesPriceForGiver) AS priceOfLovesReceived
            FROM teamLovesStat
            INNER JOIN priceForGiver ON priceForGiver.giver = teamLovesStat.giver
            GROUP BY teamLovesStat.receiver
        ;";
        $this->query($sql); 
        $debug_sql .= $sql;

        $sql = "
            SELECT " . USERS . ".username ,  IFNULL(100*priceForReceiver.priceOfLovesReceived,0)  AS priceOfLoves,  IFNULL(priceForReceiver.numberOfLoves,0)  AS lovecount
            , " . USERS . ".nickname AS nickname, " . USERS . ".id AS id FROM " . USERS . " 
            INNER JOIN " . REVIEW_REWARDER . " ON " . USERS . ".id =" . REVIEW_REWARDER . ".receiver_id  
            LEFT JOIN priceForReceiver ON " . USERS . ".username =priceForReceiver.receiver
            WHERE  " . USERS . ".username IS NOT NULL  AND " . /*REVIEW_REWARDER . ".giver_id={$this->user_id}  
            AND " .*/ REVIEW_REWARDER . ".period_id = $period_id 
            GROUP BY nickname ORDER BY lovecount DESC
        ;";
        $this->query($sql); 
        $debug_sql .= $sql;
 
        $sql = "CREATE TEMPORARY TABLE populated " . $sql;
        $this->query($sql);
        $debug_sql .= $sql;
    }    

    // The prorata normalized process main function (see 12666 for details)
    public function populateCampaignProrataNormalized($period_id, $return_stat, $min_amount=5) {
        $debug_sql="";
        // get the users list of the team members 
        $sql = "CREATE TEMPORARY TABLE teamLoves
            SELECT DISTINCT " . USERS . ".username, " . USERS . ".nickname AS nickname, " . USERS . ".id AS id 
            FROM " . USERS . " 
            INNER JOIN " . REVIEW_REWARDER . " ON " . USERS . ".id =" . REVIEW_REWARDER . ".receiver_id  
            WHERE  " . USERS . ".username IS NOT NULL  
            AND " . /*REVIEW_REWARDER . ".giver_id={$this->user_id}  
             AND " .*/ REVIEW_REWARDER . ".period_id = $period_id;
        ";
        $this->query($sql); 
        $debug_sql .= $sql;
        // create a second list, we need both because temporary tables can only be used once by query
        $sql = "CREATE TEMPORARY TABLE teamLoves2
            SELECT * FROM teamLoves ;
        ";
        $this->query($sql); 
        $debug_sql .= $sql; 
 
        // create the table statistic : number of Loves by giver/receiver (only for the team members)
        $sql = "CREATE TEMPORARY TABLE teamLovesStat
            SELECT COUNT(*) AS totalGiverReceiver, giver,receiver FROM " . LOVE_LOVE . ", " .  PERIODS . "
            WHERE " . LOVE_LOVE . ".at > " . PERIODS . ".start_date  AND " . LOVE_LOVE . ".at < " . PERIODS . ".end_date 
            AND " . PERIODS . ".`typeRC` = 'C' 
            AND " . PERIODS . ".`id` = $period_id 
            AND giver IN ( 
            SELECT teamLoves.username
            FROM teamLoves)
            AND receiver IN ( 
            SELECT teamLoves2.username
            FROM teamLoves2)
            GROUP BY giver,receiver
            ORDER BY giver,receiver
        ;";
        $this->query($sql); 
        $debug_sql .= $sql;

        $this->populateCampaignProrataNormalizedCalculation($period_id,$return_stat,$debug_sql);
        if ( $min_amount > 0 ) {
            $sql = "DELETE FROM teamLovesStat WHERE receiver IN (SELECT username  FROM populated WHERE priceOfLoves < " . $min_amount .");";
            $this->query($sql); 
            $debug_sql .= $sql;
            // populateCampaignProrataNormalizedCalculation is called again (see comment in 12809)
            $this->populateCampaignProrataNormalizedDrop($debug_sql);
            $this->populateCampaignProrataNormalizedCalculation($period_id,$return_stat,$debug_sql);
        }
        
        $sql = "UPDATE `".REVIEW_REWARDER."` , populated 
                    SET `rewarder_points`=populated.lovecount, `rewarded_percentage`=populated.priceOfLoves
                    WHERE  `receiver_id`=populated.id and `period_id` = $period_id ";
//                    WHERE `giver_id`={$this->user_id} AND `receiver_id`=populated.id and `period_id` = $period_id ";
        $this->query($sql);
        $debug_sql .= $sql;
        
        $stat="";
        if ($return_stat == "true") {
            $sql = "SELECT teamLovesStat.receiver,teamLovesStat.giver,
                    teamLovesStat.totalGiverReceiver AS numberOfLoves,
                    priceForGiver.lovesPriceForGiver
                    FROM teamLovesStat
                    INNER JOIN priceForGiver ON priceForGiver.giver = teamLovesStat.giver
                    ORDER BY teamLovesStat.receiver,teamLovesStat.giver
                    ";
            $stat = $this->getStat($sql);
        }
        return (array('debug_sql'=>$debug_sql,
                        'stat' => $stat));
    }
 
    // delete the temporary tables used in the calculation process of populateCampaignProrata
    public function populateCampaignProrataDrop(&$debug_sql) {
        $sql = "DROP TEMPORARY TABLE IF EXISTS lovesReceived;";
        $this->query($sql); 
        $debug_sql .= $sql;
        $sql = "DROP TEMPORARY TABLE IF EXISTS totalReceived;";
        $this->query($sql); 
        $debug_sql .= $sql;
        $sql = "DROP TEMPORARY TABLE IF EXISTS priceForReceiver;";
        $this->query($sql); 
        $debug_sql .= $sql;
        $sql = "DROP TEMPORARY TABLE IF EXISTS populated;";
        $this->query($sql); 
        $debug_sql .= $sql;
    } 
    
    // Make the calculation of the repartition for the Prorata option
    public function populateCampaignProrataCalculation($period_id,$return_stat,&$debug_sql) {
        // Calculate for each team member the number of loves received
        $sql = "CREATE TEMPORARY TABLE lovesReceived
            SELECT SUM(totalGiverReceiver) AS loveReceivedInPeriod, receiver
            FROM teamLovesStat
            GROUP BY receiver
        ;";
        $this->query($sql); 
        $debug_sql .= $sql;

        // Calculate the total of Loves received in the period
        $sql = "
            CREATE TEMPORARY TABLE totalReceived
            SELECT SUM(loveReceivedInPeriod) AS total
            FROM lovesReceived 
        ;";
        $this->query($sql); 
        $debug_sql .= $sql;

        // Calculate the price of all the Loves received by receiver (priceOfLovesReceived)
        $sql = "
            CREATE TEMPORARY TABLE priceForReceiver
            SELECT teamLovesStat.receiver,SUM( teamLovesStat.totalGiverReceiver) AS numberOfLoves,(loveReceivedInPeriod/total) AS priceOfLovesReceived
            FROM totalReceived,teamLovesStat
            INNER JOIN lovesReceived ON lovesReceived.receiver = teamLovesStat.receiver
            GROUP BY teamLovesStat.receiver
        ;";
        $this->query($sql); 
        $debug_sql .= $sql;

        $sql = "
            SELECT " . USERS . ".username ,  IFNULL(100*priceForReceiver.priceOfLovesReceived,0)  AS priceOfLoves,  IFNULL(priceForReceiver.numberOfLoves,0)  AS lovecount
            , " . USERS . ".nickname AS nickname, " . USERS . ".id AS id FROM " . USERS . " 
            INNER JOIN " . REVIEW_REWARDER . " ON " . USERS . ".id =" . REVIEW_REWARDER . ".receiver_id  
            LEFT JOIN priceForReceiver ON " . USERS . ".username =priceForReceiver.receiver
            WHERE  " . USERS . ".username IS NOT NULL  AND " . /*REVIEW_REWARDER . ".giver_id={$this->user_id}  
            AND " .*/ REVIEW_REWARDER . ".period_id = $period_id 
            GROUP BY nickname ORDER BY lovecount DESC
        ;";
        $this->query($sql); 
        $debug_sql .= $sql;
 
        $sql = "CREATE TEMPORARY TABLE populated " . $sql;
        $this->query($sql);
        $debug_sql .= $sql;
    }
 
    // main function for the Prorata option (% is simply based on the number of Loves received)
    public function populateCampaignProrata($period_id, $return_stat, $min_amount) {
        $debug_sql="";
        // get the users list of the team members 
        $sql = "CREATE TEMPORARY TABLE teamLoves
            SELECT DISTINCT " . USERS . ".username, " . USERS . ".nickname AS nickname, " . USERS . ".id AS id 
            FROM " . USERS . " 
            INNER JOIN " . REVIEW_REWARDER . " ON " . USERS . ".id =" . REVIEW_REWARDER . ".receiver_id  
            WHERE  " . USERS . ".username IS NOT NULL  
            AND " . /*REVIEW_REWARDER . ".giver_id={$this->user_id}  
             AND " .*/ REVIEW_REWARDER . ".period_id = $period_id;
        ";
        $this->query($sql); 
        $debug_sql .= $sql;
        // create a second list, we need both because temporary tables can only be used once by query
        $sql = "CREATE TEMPORARY TABLE teamLoves2
            SELECT * FROM teamLoves ;
        ";
        $this->query($sql); 
        $debug_sql .= $sql; 
 
        // create the table statistic : number of Loves by giver/receiver 
        $sql = "CREATE TEMPORARY TABLE teamLovesStat
            SELECT COUNT(*) totalGiverReceiver, giver,receiver FROM " . LOVE_LOVE . ", " .  PERIODS . "
            WHERE " . LOVE_LOVE . ".at > " . PERIODS . ".start_date  AND " . LOVE_LOVE . ".at < " . PERIODS . ".end_date 
            AND " . PERIODS . ".`typeRC` = 'C' 
            AND " . PERIODS . ".`id` = $period_id 
            AND receiver IN ( 
            SELECT teamLoves2.username
            FROM teamLoves2)
            GROUP BY giver,receiver
            ORDER BY giver,receiver
        ;";
        $this->query($sql); 
        $debug_sql .= $sql;

        $this->populateCampaignProrataCalculation($period_id,$return_stat,$debug_sql);

        if ( $min_amount > 0 ) {
            $sql = "DELETE FROM teamLovesStat WHERE receiver IN (SELECT username  FROM populated WHERE priceOfLoves < " . $min_amount .");";
            $this->query($sql); 
            $debug_sql .= $sql;
            // populateCampaignProrataCalculation is called again (see comment in 12809)
            $this->populateCampaignProrataDrop($debug_sql);
            $this->populateCampaignProrataCalculation($period_id,$return_stat,$debug_sql);
        }
        $sql = "UPDATE `".REVIEW_REWARDER."` , populated 
                    SET `rewarder_points`=populated.lovecount, `rewarded_percentage`=populated.priceOfLoves
                    WHERE `receiver_id`=populated.id and `period_id` = $period_id ; ";
//                    WHERE `giver_id`={$this->user_id} AND `receiver_id`=populated.id and `period_id` = $period_id ; ";
        $this->query($sql);
        $debug_sql .= $sql;
        $stat="";
        if ($return_stat == "true") {
            $sql = "SELECT teamLovesStat.receiver,teamLovesStat.giver,
                    teamLovesStat.totalGiverReceiver AS numberOfLoves,
                    1.00/totalReceived.total AS lovesPriceForGiver ,
					totalReceived.total AS total
                    FROM teamLovesStat,totalReceived
                    ORDER BY teamLovesStat.receiver,teamLovesStat.giver ;
                    ";
            $stat = $this->getStat($sql);
        }
        return (array('debug_sql'=>$debug_sql,
                        'stat' => $stat));
              
    }    
    
    // Return the campaign repartition data to display the graph
    public static function getResultsForPeriodForCampaign($period_id ){
        $date_filter = "  `period_id` = '$period_id' ";
        // get the users list of the team members 
        $query = "CREATE TEMPORARY TABLE teamLoves
            SELECT DISTINCT " . USERS . ".username, " . USERS . ".nickname AS nickname, " . USERS . ".id AS id 
            FROM " . USERS . " 
            INNER JOIN " . REVIEW_REWARDER . " ON " . USERS . ".id =" . REVIEW_REWARDER . ".receiver_id  
            WHERE  " . USERS . ".username IS NOT NULL  
             AND " . REVIEW_REWARDER . ".period_id = $period_id;
        "; 
        $rt = mysql_query($query) or die("Couldn't execute query.".mysql_error() . " ** " . $query ); 

        $subQuery = "SELECT ".REVIEW_REWARDER.".receiver_id AS receiver_id, IFNULL(COUNT(DISTINCT ".LOVE_LOVE.".giver),0) AS givers ".
               " FROM " . USERS  . " 
                 INNER JOIN ".REVIEW_REWARDER." ON ".USERS.".id =".REVIEW_REWARDER.".receiver_id 
                 INNER JOIN ".PERIODS." ON 
                  " . PERIODS . ".`typeRC` = 'C' 
                 AND " . PERIODS . ".`id` = $period_id 
                 LEFT JOIN " .LOVE_LOVE. " ON username=" .LOVE_LOVE. ".receiver 
                 AND " .LOVE_LOVE. ".at > " . PERIODS . ".start_date 
                 AND " .LOVE_LOVE. ".at < " . PERIODS . ".end_date 
                 WHERE  ".USERS.".username IS NOT NULL 
                 AND ".REVIEW_REWARDER.".period_id = " . $period_id . "
                GROUP BY receiver_id ";

        $teamSubQuery = "SELECT ".REVIEW_REWARDER.".receiver_id AS team_receiver_id, IFNULL(COUNT(DISTINCT ".LOVE_LOVE.".giver),0) AS team_givers ".
               " FROM  " .  USERS . " 
                 INNER JOIN ".REVIEW_REWARDER." ON ".USERS.".id =".REVIEW_REWARDER.".receiver_id 
                 INNER JOIN ".PERIODS." ON 
                  " . PERIODS . ".`typeRC` = 'C' 
                 AND " . PERIODS . ".`id` = $period_id 
                 LEFT JOIN ".LOVE_LOVE." ON username=".LOVE_LOVE.".receiver ".
               " AND ".LOVE_LOVE.".at > " . PERIODS . ".start_date
                 AND ".LOVE_LOVE.".at < " . PERIODS . ".end_date 
                 AND ".LOVE_LOVE.".giver IN (SELECT username FROM teamLoves) " . 
               " WHERE  ".USERS.".username IS NOT NULL " .
               " AND ".REVIEW_REWARDER.".period_id = " . $period_id . 
               " AND " . PERIODS . ".`typeRC` = 'C' 
                 AND " . PERIODS . ".`id` = $period_id 
                GROUP BY receiver_id ";
               
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
                  WHERE " . $date_filter . " GROUP BY `receiver_id` ORDER BY `received_percentage` DESC, givers DESC, team_givers DESC ;";
        $rt = mysql_query($query) or die("Couldn't execute query.".mysql_error() . " ** " . $query); 

        $results = array();
        while($row = mysql_fetch_assoc($rt)){
            $results[] = $row;
        }

        // Create an array to hold the point values
        $points = array();
        $percentage = array();
        // Create an array to hold the users
        $users = array ();
        // Create an array to hold the givers
        $givers = array();
        $team_givers = array();
        // Relation array
        $relation = array();
        
        foreach ($results as $result) {
            $points[] = $result['received_points'];
            $percentage[] = $result['received_percentage'];
            $users[] = $result['nickname'];
            $givers[] = $result['givers'];
            $team_givers[] = $result['team_givers'];
            $relation[] = array($result['nickname'], $result['received_points'], $result['givers'],$result['received_percentage'], $result['team_givers']);
        }
        $data = array('users' => $users,
                      'points' => $points,
                      'percentage' => $percentage,
                      'givers' => $givers,
                      'team_givers' => $team_givers,
                      'relation' => $relation);
        return $data;
    }
    
    
    public function getReport($period_id) {
        $sep=",";
        $ex_report = array();
        
        $sqlFields = "SELECT ". PERIODS . ".`id`,title, start_date, end_date, budget,budget_validated,paid_amount,sent_by_receiver, ". PERIODS . ".`status` AS periodStatus,
                IFNULL(COUNT(lo.id),0) AS numberLovesReceived, um.nickname AS manager, um.username AS managerUsername,
                uo.nickname AS owner,uo.username AS ownerUsername,uo.id AS idowner, ". 
                REVIEW_REWARDER . ".`rewarded_percentage` AS rewarded_percentage, ". 
                REVIEW_REWARDER . ".`paid` AS paid ";
        $sqlGroupBy = "  GROUP BY id,title,start_date,end_date,budget,budget_validated,periodStatus,manager,owner,idowner,rewarded_percentage";
        $table_filter = " FROM " . PERIODS . 
            "    LEFT JOIN " . USER_REVIEWS . " ON ". USER_REVIEWS . ".`period_id` = ". PERIODS . ".`id` ".
            "    LEFT JOIN " . REVIEW_REWARDER . " ON ". REVIEW_REWARDER . ".`period_id` = ". PERIODS . ".`id` ".
            "    INNER JOIN " . USERS . " um ON " . REVIEW_REWARDER . ".giver_id = um.id ".
            "    INNER JOIN " . USERS . " uo ON " . REVIEW_REWARDER . ".receiver_id = uo.id ".
            "    LEFT JOIN " . LOVE . " lo ON " . PERIODS . ".start_date <= lo.at AND " . 
            PERIODS . ".end_date >= lo.at AND lo.receiver = uo.username " . 
            "  WHERE ". PERIODS . ".`typeRC` = 'C' 
               AND ". PERIODS . ".`id` = $period_id " 
            ;
        
        $sql = $sqlFields . $table_filter . $sqlGroupBy . "  ORDER BY ownerUsername";
        $result = mysql_query( $sql ) or die("Couldn t execute query.".mysql_error() . " SQL: ".$sql); 
        while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
            $title = '"' . $row['title'] . '"';
            $ex_report[] ="".$row['ownerUsername'].
                                $sep.$row['owner'].$sep.$row['paid_amount'].
                                $sep.$title.$sep.$row['start_date'].$sep.$row['end_date'].
                                $sep.$row['manager'].$sep.$row['managerUsername'].$sep.$row['numberLovesReceived']."" ; 
        } 
        return $ex_report;
    }
     
    
    public function exportRedeemByCampaignContent($period_id,$endOfLine) {
        $report = $this->getReport($period_id);
        
        // Create with headers
        $csv = "ownerEmail,owner,Amount,title,start_date,end_date,manager,managerEmail,numberLovesReceived".$endOfLine;
        foreach ($report as $item) {
                $csv .=  $item.$endOfLine;
        }
        return $csv;
    }
     
    
    public function exportRedeemByCampaign($period_id) {
        $csv = $this->exportRedeemByCampaignContent($period_id,"\n");
        // Output headers to force download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="Redeem Report.csv"');
        echo $csv;
        
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
