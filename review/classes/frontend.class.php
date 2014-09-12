<?php
include ("config.php");
include ("class.session_handler.php");
require_once ("classes/LoveUser.class.php");
require_once ("classes/Login.class.php");
require_once ("classes/Cloud.class.php");
require_once ("classes/Company.class.php");
require_once ("classes/Love.class.php");
require_once ("classes/Utils.class.php");
require_once ("classes/Error.class.php");
require_once ("classes/Response.class.php");
require_once ("classes/Database.class.php");
require_once ("functions.php");
////////////////////////////
// VARIABLES
////////////////////////////


// number of rows in the list view
$entriesPerPage = 15;

class Frontend {
    protected $userLoggedIn = false;
    protected $user;
    protected $love;
    protected $company;
    protected $cloud;
    protected $error;
    protected $login;
    
    protected $dbConnection;
    
    protected $averageCompanyLoveSent;
    protected $loveSent;
    
    private static $instance = null;
    
    #var $cloudMap;
    #var $limit;
    #var $pages;
    #var $loveSent;
    #var $averageCompanyLoveSent;
    #var $user;
    #var $dbConnection;
    

    public function setUserLoggedIn($flag){
        $this->userLoggedIn = (bool) $flag;
        return $this;
    }
    
    public function setUser(LoveUser $user){
        $this->user = $user;
        return $this;
    }
    
    public function setDbConnection($con){
        $this->dbConnection = $con;
        return $this;
    }
    
    public function setCompany(){
        if($this->isUserLoggedIn()){
            $id = $this->getUser()->getCompany_id();
        }else{
            $id = MAIN_COMPANY;
        }
        $this->company = new Company($id);
        return $this;
    }
    
    public function setLove(){
        $this->love = new Love();
        return $this;
    }
    
    public function setCloud(Cloud $cloud){
        $this->cloud = $cloud;
        return $this;
    }
    
    public function setAverageCompanyLoveSent($love){
        $this->averageCompanyLoveSent = (int) $love;
        return $this;
    }
    
    public function setLoveSent($love){
        $this->loveSent = (int) $love;
        return $this;
    }
    
    public function getUserLoggedIn(){
        return $this->userLoggedIn;
    }
    
    public function getUser(){
        return $this->user;
    }
    
    public function getDbConnection(){
        return $this->dbConnection;
    }
    
    public function getCompany(){
        if(null === $this->company){
            $this->setCompany();
        }
        return $this->company;
    }
    
    public function getCloud(){
        return $this->cloud;
    }
    
    public function getLove(){
        if(null === $this->love){
            $this->setLove();
        }
        return $this->love;
    }
    
    public function getAverageCompanyLoveSent(){
        return $this->averageCompanyLoveSent;
    }
    
    public function getLoveSent(){
        return $this->loveSent;
    }
    
    public function isUserTryingToAuthenticate(){
        if(isset($_REQUEST["login"])){
            return true;
        }else{
            return false;
        }
    }
    public function tryToAuthenticateUser(){
        $username = isset($_REQUEST["username"]) ? trim($_REQUEST["username"]) : "";
        $password = isset($_REQUEST["password"]) ? $_REQUEST["password"] : "";
        if(empty($username)){
            $this->getError()->setError("Username cannot be empty.");
        }else if(empty($password)){
            $this->getError()->setError("Password cannot be empty.");
        }else{
            $params = array("username" => $username, "password" => $password, "action" => "login");
            ob_start();
            // send the request
            CURLHandler::Post(SERVER_URL . 'loginApi.php', $params, false, true);
            $result = ob_get_contents();
            ob_end_clean();
            $ret = json_decode($result);
            if($ret->error == 1){
                $this->getError()->setError($ret->message);
                return $this->getError()->getErrorFlag();
            }else{
                $id = $ret->userid;
                $username = $ret->username;
                $nickname = $ret->nickname;
                $_SESSION["userid"] = $id;
                $_SESSION["username"] = $username;
                $_SESSION["nickname"] = $nickname;

                // notifying other applications
                $response = new Response();
                $login = new Login();
                $login->setResponse($response);
                $login->notify($id, session_id());

                return false;
            }
        }
        return $this->getError()->getErrorFlag();
    }
    
    public static function getInstance(){
        if(null === self::$instance){
            self::$instance = new Frontend();
        }
        return self::$instance;
    }
    
    // Get user info from the database
    // and populate the local variables with it
    // Initializes loveSent and calculates 
    // averageCompanyLoveSent
    function __construct(){
        global $entriesPerPage;
        
        $this->setUser(new LoveUser());
        if($this->getUser()->is_logged()){
            $this->setUserLoggedIn(true);
        }
        
        $this->setDbConnection($this->getUser()->getLink());
        if(! $this->dbConnection){
            die("No mysql connection");
        }
        $this->setError(new Error())
             ->setLogin(new Login());
    }
    
    public function setLogin($l){
        $this->login = $l;
        return $this;
    }
    
    public function getLogin(){
        return $this->login;
    }
    
    public function setError($err){
        $this->error = $err;
        return $this;
    }
    public function getError(){
        return $this->error;
    }
    
    public function isUserLoggedIn(){
        return $this->getUserLoggedIn();
    }
    
    protected function initCloud(){
        $file = 'clouds/lovecloud-' . $this->getCompany()->getId() . '.map';
        $this->setCloud(new Cloud(array('cloudFile' => $file)));
    }
    
    protected function initLove($limit){
        $this->getLove()->setLimit($limit);
    }
    
    protected function initStatistics(){
        $sql = 'SELECT COUNT(*) AS `myLove` FROM `' . LOVE_LOVE . '` ';
        $sql .= 'WHERE `at` >= (NOW() - INTERVAL 1 WEEK) ';
        $sql .= 'AND `giver` = "' . $this->getUser()->getUsername() . '" ';
        $sql .= 'UNION ';
        $sql .= 'SELECT COUNT(*) FROM `' . LOVE_LOVE . '` ';
        $sql .= 'JOIN `' . LOVE_USERS . '` ON `' . LOVE_LOVE . '`.`giver` = `' . LOVE_USERS . '`.`username` ';
        $sql .= 'WHERE `at` >= (NOW() - INTERVAL 1 WEEK) ';
        $sql .= 'UNION ';
        $sql .= 'SELECT COUNT(`id`) FROM `' . LOVE_USERS . '`;';
        
        $result = mysql_query($sql);
        $tempVar = array();
        while($row = mysql_fetch_assoc($result)){
            $tempVar[] = $row['myLove'];
        }
        
        $this->setLoveSent($tempVar[0]);
        $this->setAverageCompanyLoveSent(ceil($tempVar[1] / $tempVar[2]));
    }
    
    // calls getMostLoved to get the info
    public function mostLoved(){
        return $this->getLove()->getMostLoved();
    }
    
    // This function return user-friendly text
    // consisting of total love sent and
    // average company love sent
    public function getLoveNotification(){
        return 'You sent ' . $this->getLoveSent() . ' love this week, company average ' . $this->getAverageCompanyLoveSent() . '.';
        ;
    }
    
    // This function return all usernames
    // except the current user.
    // It is used from autocompleter
    public function getToData(){
        $sql = "SELECT username, nickname " . "FROM " . REVIEW_USERS . " " . "WHERE id != " . $this->getUser()->getId();
        $res = mysql_query($sql);
        $output = "";
        while($row = mysql_fetch_assoc($res)){
            if(isset($row["nickname"]) && strlen(trim($row["nickname"])) > 0){
                $output .= $row["nickname"] . " ";
            }
            $output .= $row["username"] . " ";
        }
        $output = substr($output, 0, strlen($output) - 1);
        return $output;
    }
    
    // returns the list love entries for a particular
    // page
    public function listLove(){
        $page = (int) $_REQUEST["page"];
        $pager = $this->getLove()->getListPager($page);
        $body = $this->getLove()->getLoveList($page);
        return array("body" => $body, "pager" => $pager);
    }
    
// get list of members sorted and grouped by first letter
//    public function sortedMemberList()
//    {
//    	$sql = 'SELECT `username`, `nickname` FROM `' . REVIEW_USERS . '` WHERE `company_id` = "' . $this->company_id . '" ORDER BY `nickname` ASC;';
//    	$result = mysql_query($sql);
//    	$prevLetter = ''; $memberList = array();
//    	while ($row = mysql_fetch_assoc($result)) {
//    		// first letter of nickname transformed to uppercase
//    		$letter = strtoupper(substr($row['nickname'],0,1));
//    		$memberList[$letter][] = $row;
//    		$prevLetter = $letter;
//    	}
//    	return $memberList;
//    }


}

?>
