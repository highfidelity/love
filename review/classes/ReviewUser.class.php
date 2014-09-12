<?php
require 'classes/DataObject.php';
class ReviewUser extends DataObject
{
    /**
     * @var string User id
     */
    protected $id;
    /**
     * @var string username
     */
    protected $username;
    /**
     * @var string password
     */
    protected $password;
    /**
     * @var string nickname
     */
    protected $nickname;
    /**
     * @var int confirmed field
     */
    protected $confirmed;
    /**
     * @var int active field
     */
    protected $active;
    /**
     * @var string General purpose token
     */
    protected $token;
    /**
     * @var int User is giver
     */
    protected $is_giver;
    /**
     * @var int User is receiver
     */
    protected $is_receiver;
    /**
     * @var int User is auditor
     */
    protected $is_auditor;
    /**
     * @var int User is admin
     */
    protected $is_admin;
    /**
     * @var int Rewarder points balance
     */
    protected $rewarder_points;
    /**
     * @var int Rewarder limit day
     */
    protected $rewarder_limit_day; 
    /**
     * @var object MySQL link identifier
     */
    protected $link;
    public function __construct(){
        parent::__construct();
        $this->dbConnect();
        if(isset($_SESSION["username"]) && isset($_SESSION["userid"])){
           $this->loadUserFromSession(); 
        }
    }
    private function dbConnect(){
        if(!$this->link){
            $this->link = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
            if(!$this->link){
                die('Not connected : ' . mysql_error());
            }
            mysql_select_db(DB_NAME,$this->link);
        }
    }
    private function loadUserFromSession(){
        $this->setId($_SESSION["userid"]);
        #$this->setUsername($_SESSION["username"]); (will be set in loadUserById)
        #$this->setNickname($_SESSION["nickname"]); (will be set in loadUserById)
        #$this->setConfirmed($_SESSION["confirmed"]); (index not defined)
        #$this->setToken($_SESSION["token"]); (index not defined)
        $this->loadUserById($this->getId());
    }
    private function loadUserById($id){
        $id = (int)$id;
        $sql = "SELECT ".REVIEW_USERS.".* ". 
               "FROM ".REVIEW_USERS." ".
               "WHERE ".REVIEW_USERS.".id = ".mysql_real_escape_string($id); echo $sql;
        $row = $this->doQuery($sql);
        $this->setUsername($row->username);
        $this->setNickname($row->nickname);
        $this->setIs_giver($row->is_giver);
        $this->setIs_giver($row->is_receiver);
        $this->setIs_auditor($row->is_auditor);
        $this->setIs_admin($row->is_admin);
        $this->setRewarder_points($row->rewarder_points);
        $this->setRewarder_limit_day($row->rewarder_limit_day);
    }
    public function nicknameThenusername(){
        $nickname = $this->getNickname();
        return isset($nickname) ? $nickname : $this->getUsername();
    }
    private function doQuery($sql){
        $result=mysql_query($sql, $this->link);
        $ret=mysql_fetch_object($result);
        return $ret;
    }
    public function is_logged(){
        $id = $this->getId();
        return isset($id);
    }
    public function logout(){
        session_destroy();
        header("location:login.php");
        exit;
    }
    public function askUserToAuthenticate(){
        session_destroy();
        header("location:login.php?redir=".urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}
