<?php
//
//  Copyright (c) 2012, Below92 LLC.
//  All Rights Reserved. 
//  http://www.sendlove.us
//
//define('DB_USER','project_cupid');
//define('DEBUG_DUMP',true);
//define('DEBUG_CONSOLE',true);
if(file_exists("../autoconfig.php"))
	require_once("../autoconfig.php");
else
	require_once("../../autoconfig.php");

class Cupid {
    private $link;

    public function init() {
        $this->link = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
        if (!$this->link) {
            die("Error on: Init -> ".mysql_error());
        }
        mysql_select_db(CUPID_DB, $this->link);
    }
    
    public function disconnect() {
        mysql_close($this->link);
    }

    /**
     * Check if @instance exists as an instance on Cupid DB
     */
    public function instanceExists($instance) {
        $inst = mysql_real_escape_string($instance);
        
        $sql = "SELECT domain FROM ".CUPID_CONF." WHERE domain='{$inst}.sendlove.us' limit 1;";
        $query = mysql_query($sql,$this->link) or error_log("instanceExists: ".mysql_error($this->link)."\n".get_resource_type($this->link));
        if (!$ret = mysql_fetch_row($query)) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Check if instance is configured or pending
     */
    public function getInstanceStatus($instance) {
        if (!$this->instanceExists($instance)) {
            return "NULL";
        } else {
	        $inst = mysql_real_escape_string($instance);
	        
		    //Trial does not have permission to view data column. Pass flag status as key name.
            // INSTANCE_NEW = still being configured, INSTANCE_LIVE=ready, INSTANCE_MAINTENANCE=instance is being worked on and not available right now
	        $sql = "SELECT `config_key` FROM ".CUPID_CONF." WHERE `config_key` like 'INSTANCE_%' AND  `domain`='{$inst}.sendlove.us' limit 1";
	        $sql_q = mysql_query($sql) or die(mysql_error());
	        
	        $result = mysql_fetch_array($sql_q);
	        return $result['config_key'];
        }
    }
    
    /**
     * Creates a new instance on Cupid with:
     * @instance
     * @username
     * @nickname
     * @password
     */
    public function createInstance($instance, $username, $nickname, $password,$source,$adword) {
        if ($this->instanceExists($instance)) {

            die('Error: The instance already exists in the system.');
        }
    
        // Escape params
        $inst = mysql_real_escape_string(strtolower($instance));
        $email = mysql_real_escape_string($username);
        $name = mysql_real_escape_string($nickname);
        
        $sql = "INSERT INTO ".CUPID_CONF." (`config_key`,`data`,`domain`,`type`,`priority`,`created`,`modified`) VALUES ".
                   "('MARKETING_SOURCE', '{$source}','{$inst}.sendlove.us','DEFSTRING',1000,now(),now()),".
                   "('MARKETING_ADWORD', '{$adword}','{$inst}.sendlove.us','DEFSTRING',1000,now(),now()),".
                   "('ADMIN_NAME', '{$name}','{$inst}.sendlove.us','DEFSTRING',1000,now(),now()),".
                   "('ADMIN_EMAIL', '{$email}','{$inst}.sendlove.us','DEFSTRING',1000,now(),now()),".
                   "('ADMIN_PASS', '{$password}','{$inst}.sendlove.us','DEFSTRING',1000,now(),now()),".
                   "('INSTANCE_NEW', 'PENDING','{$inst}.sendlove.us','DEFSTRING',1000,now(),now());";
        
        // If the values could be inserted return true, else false
        if (mysql_query($sql)) {
            return true;
        } else {
            die(mysql_error());
            return false;
        }
    }
    
    /**
     * Create all DB tables
     */
    public function configureInstance() {

        //First try to create the databse
        $sql .= "CREATE DATABASE ".NEW_DB_PREFIX."{$inst};";
        if (!mysql_query($sql)) {
            return false;
        }

	    // Can we GET to the database we just made?
        if (! mysql_select_db(NEW_DB_PREFIX."{$inst}")) {
            return false;
        }
        
        
        if (mysql_query(CREATE_LOVE_DB)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Debug Output
     */
    public function debug() {
        if (defined(DEBUG_CONSOLE)) {
	        $diag = get_defined_constants(true);
	        echo CREATE_LOVE_DB;
	        die(var_dump($diag['user']));
        }
    }
}

?>
