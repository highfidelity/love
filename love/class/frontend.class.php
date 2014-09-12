<?php
$app_root_path = dirname(__FILE__);
$app_root_path = str_replace('class','',$app_root_path);

require_once ($app_root_path."config.php");
require_once ($app_root_path."class.session_handler.php");
require_once ($app_root_path."class/LoveUser.class.php");
require_once ($app_root_path."class/Login.class.php");
require_once ($app_root_path."class/Cloud.class.php");
require_once ($app_root_path."class/Company.class.php");
require_once ($app_root_path."class/Love.class.php");
require_once ($app_root_path."class/Utils.class.php");
require_once ($app_root_path."class/Compressor.class.php");
require_once ($app_root_path."class/CompressedFiles.class.php");
require_once ($app_root_path."class/Error.class.php");
require_once ($app_root_path."class/Periods.class.php");
require_once ($app_root_path."functions.php");
require_once ($app_root_path."helper/countrylist.php");
require_once ($app_root_path."send_email.php");
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
    public function isUserTryingToRegister(){
        if(isset($_REQUEST["signup"])){
            return true;
        }else{
            return false;
        }
    }
    
    public function tryToRegisterUser(){
        $username = isset($_REQUEST["username"]) ? trim($_REQUEST["username"]) : "";
        $password = isset($_REQUEST["password"]) ? $_REQUEST["password"] : "";
        $cpassword = isset($_REQUEST["confirmpassword"]) ? $_REQUEST["confirmpassword"] : "";
        
        if(empty($username)){
            $this->getError()->setError("Username cannot be empty.");
            
        }else if(empty($password)){
            $this->getError()->setError("Password cannot be empty.");
            
        }else if(strcmp($password, $cpassword) != 0){
            $this->getError()->setError("Passwords do not match.");
            
        }else{
            
            $params = array("username" => $username, "password" => $password, "action" => "signup", "confirm_string" => uniqid());
            if(isset($_REQUEST["nickname"])){
                $params["nickname"] = $_REQUEST["nickname"];
            }
            
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
                $cid = isset($_REQUEST["company"]) ? $_REQUEST["company"] : 0;
                $phone = isset($_REQUEST["phone"]) ? $_REQUEST["phone"] : NULL;
                $country = isset($_REQUEST["country"]) ? $_REQUEST["country"] : NULL;
                $provider = isset($_REQUEST["provider"]) ? $_REQUEST["provider"] : NULL;
                $this->getUser()->newUser($ret->id, $ret->username,$ret->nickname, $cid,$phone,$country,$provider);
                $confirmUrl = SECURE_SERVER_URL."confirmation.php?cs=".$ret->confirm_string."&str=".base64_encode($username);
                
                sendTemplateEmail($username, 'confirmation', array('url' => $confirmUrl));
                return false;
            }
        }
        return $this->getError()->getErrorFlag();
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
				if (($key = array_search('User is deactivated.', $ret->message)) !== false) {
					$ret->message[$key] = 'You need to be confirmed!<br /><a href="#" id="ping_admin">Ping the administrator ...</a>';
				}
                $this->getError()->setError($ret->message);
                return $this->getError()->getErrorFlag();
                
            }else{
                $id = $ret->userid;
                $username = $ret->username;
                $nickname = $ret->nickname;
                $admin    = $ret->admin;
                Utils::setUserSession($id, $username, $nickname, $admin);

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
        
        $this->initCloud();
        $this->initLove($entriesPerPage);
        $this->initStatistics();
        $this->setError(new Error())->setLogin(new Login());
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
        $sql = 'SELECT COUNT(*) AS `myLove` FROM `' . LOVE . '` ';
        $sql .= 'WHERE `at` >= (NOW() - INTERVAL 1 WEEK) ';
        $sql .= 'AND `giver` = "' . $this->getUser()->getUsername() . '" ';
        $sql .= 'UNION ';
        $sql .= 'SELECT COUNT(*) FROM `' . LOVE . '` ';
        $sql .= 'JOIN `' . USERS . '` ON `' . LOVE . '`.`giver` = `' . USERS . '`.`username` ';
        $sql .= 'WHERE `at` >= (NOW() - INTERVAL 1 WEEK) ';
        $sql .= 'UNION ';
        $sql .= 'SELECT COUNT(`id`) FROM `' . USERS . '`;';

        $result = mysql_query($sql);
        $tempVar = array();
        while($row = mysql_fetch_assoc($result)){
            $tempVar[] = intval($row['myLove']);
        }
        
        $this->setLoveSent($tempVar[0]);
        $this->setAverageCompanyLoveSent((empty($tempVar[2]))?0:ceil($tempVar[1] / $tempVar[2]));
    }
    
    // calls getMostLoved to get the info
    public function mostLoved(){
        return $this->getLove()->getMostLoved();
    }
    
    // This function return user-friendly text
    // consisting of total love sent and
    // average company love sent
    public function getLoveNotification(){
        return 'You sent ' . $this->getLoveSent() . ' love in the past seven days. <br>Company average is ' . $this->getAverageCompanyLoveSent() . '.';
        ;
    }

    public function totalLove() {
        return $this->getLove()->getTotalLove();
    }

    // This function return all usernames
    // except the current user.
    // It is used from autocompleter
    public function getToData(){
        $sql = "SELECT username, nickname " . "FROM " . USERS . " " . "WHERE id != " . $this->getUser()->getId();
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

        $username = isset($_REQUEST["username"]) && !empty($_REQUEST["username"])
            ? mysql_real_escape_string($_REQUEST["username"]) : $this->getUser()->getUsername();
        $page = (int) $_REQUEST["page"];
        $just_user = isset($_REQUEST["just_user"]) && $_REQUEST["just_user"] == 'true'
            ? true : false;
        $when = isset($_REQUEST["when"]) ? $_REQUEST["when"] : "true";
        
        $body = $this->getLove()->getLoveList($username, $page, $just_user, $when);
        $pager = $this->getLove()->getListPager($page);
        return array("body" => $body, "pager" => $pager);
    }
    
    // the following are for the toforAjax loads

    // load the reviewForm section
    public function reviewForm() {
        include_once("db_connect.php");
        include_once("autoload.php");

        // include_once("review.php");
        $user = new LoveUser();
        $periods = new Periods($user->getId());
        $review = new Review($user->getId(),$periods);
        
        global $front; 

        include('view/tofor/review/form.php');
        include("view/tofor/review/love-selector.php");
    }

	#public function deleteLove() {
	#	if ($this->getUser()->getCompany_admin()) {
	#		if ($this->getLove()->deleteLove($_REQUEST['love'])) {
	#			return array(
	#				'success' => true
	#			);
	#		}
	#	}
	#	return array(
	#		'success' => false
	#	);
	#}
}

