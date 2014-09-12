<?php
/**
 * Love User
 *
 * The Love User extends User
 * @category LoveMachine
 * @package  User
 * @author   LoveMachine Inc. <all@lovemachineinc.com>
 * @license  Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link     http://www.lovemachineinc.com
 */
class LoveUser extends User {
    
    public function __construct($db = null) {
        if(is_null($db)){
            global $dbConfig;
        } else {
            $dbConfig = $db;
        }
        parent::__construct ( new mysqli ( $dbConfig ['host'], $dbConfig ['username'], $dbConfig ['password'], $dbConfig ['dbname'] ) );
    }
    
    public function loadData($data) {
      foreach($data as $key=>$value){
          $set = "set".$key;
          $this->$set($value);
      }
    }
    public function loadUserFromSession(){
        if(isset($_SESSION["userid"]) && strlen(trim($_SESSION["userid"])) > 0){
          $this->setId($_SESSION["userid"]);
        }
        if(isset($_SESSION["username"]) && strlen(trim($_SESSION["username"])) > 0){
          $this->setUsername($_SESSION["username"]);
        }
        return $this->loadById($this->getId());
    }
    public function delete(){
        $dbAdapter = $this->getDbAdapter();
        $query =  'DELETE FROM '.self::TABLE_NAME.' '.
                  'WHERE id = '.$dbAdapter->real_escape_string($this->getId());
        $stmt = $this->dbAdapter->prepare($query);
        if (!$stmt->execute()) {
            throw new User_DbException($stmt->error, $stmt->errno);
            return false;
        }
        if ($stmt->affected_rows != 1) {
            throw new User_DbException('Unexpected number of affected rows: ' . $stmt->affected_rows);
            return false;
        }
        return true;
    }
    public function logout(){
        if(isset($_SESSION["userid"])){
            unset($_SESSION["userid"]);
        }
        if(isset($_SESSION["username"])){
            unset($_SESSION["username"]);
        }
        session_destroy();
    }
}
