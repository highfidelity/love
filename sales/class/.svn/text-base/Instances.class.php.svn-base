<?php
/*
* Class Instances - this class in responsible for instances 
    In the sales database, there is a table customers.
    Each record in this table is a customer / instance.
    Using this table we can get the list of instances.
    In order to get only the active instances, a filter should be used ( active = 1)
    The first use of this class is the redeemRequests page of Sales application.
*/

class Instances{
    public $link;

    public function __construct(){
        $this->link = mysql_connect(DB_SALES_SERVER, DB_SALES_USER, DB_SALES_PASSWORD) or die('Could not connect: ' . mysql_error());
        mysql_select_db(DB_NAME,$this->link);
        if($this->link === null){
            die('Could not connect: ' . mysql_error());
        }

    }
   
    
    public function changeInstanceStatus($domain,$new_instance_status){
        $new_instance_status = intval($new_instance_status);
        $sql = "UPDATE " . CUSTOMERS . " SET `active` = $new_instance_status WHERE " . 
                CUSTOMERS . ".`domain` = '" . mysql_real_escape_string($domain) . "'  ";
        $ret = mysql_unbuffered_query($sql,$this->link);
        if ($ret) {
            return  array('result' => "update new_instance_status: " ) ;
        } else {
            return  array('error' => 'error SQL in changeInstanceStatus' . mysql_error () . " * " .$sql ) ;
        }
    }
   
   /*
    * The format for the datbase name has and may change again.
    * IF the invoking process does not have access to the databaseName, use an api call to reach the instance
    */
    public function getInstanceNameFromDomain($domain) {
        return array('error'=>'Deprecated');
    }

    /*
    Return the list of instances that are active 
    
    */
    public function getActiveInstancesName(){
        $sqlFilterRequestSent = " WHERE ( active = 1)";
        $sql = "SELECT * FROM " . CUSTOMERS . $sqlFilterRequestSent ;
        if (! $result = mysql_query( $sql ,$this->link)) { error_log("Couldn t execute query.".mysql_error() . " SQL: ".$sql); die("Couldn t execute query"); } 
        $responce = array();
        while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
            $responce[]= $row['db_name']; 
        } 
        return $responce; 
    }

    /*
    Return the list of instances and for each instance some informations 
    
    */
    public function getInstancesList($page,$limit,$sidx,$sord,$filtreInstances){ 
        $sqlFilterRequestSent = " ";
        $filtreInstances = intval($filtreInstances);
        if ($filtreInstances != -1) {
            $sqlFilterRequestSent = " WHERE ( active = $filtreInstances)";
        } else {
            $sqlFilterRequestsSent = " WHERE (active != 0 ) ";
        }
        $sqlCount = "SELECT * FROM " . CUSTOMERS . $sqlFilterRequestSent ;
            
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
            $sql = "SELECT * FROM " . CUSTOMERS . $sqlFilterRequestSent ;
            $sql .= "  ORDER BY $sidx $sord LIMIT $start , $limit";
            $result = mysql_query( $sql ) or die("Couldn t execute query.".mysql_error() . " SQL: ".$sql); 
            $i=0; 
            while($row = mysql_fetch_array($result,MYSQL_ASSOC)) { 
                $responce->rows[$i]['id']=$row['id']; 
                $active = ($row['active'] == 1) ? "Active" : "Inactive";
                $actions = ($row['active'] == 1) ? 
                            "<span class='deactivateInstance' id_domain='".$row['domain'] ."' >Deactivate</span>" : 
                            "<span class='activateInstance' id_domain='".$row['domain'] ."' >Activate</span>";
                $responce->rows[$i]['cell']=array($row['domain'],$active,$row['created'],$row['contact_first_name'],$row['contact_email'],$actions ); 
                $i++; 
            } 
        }
        return $responce; 
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
