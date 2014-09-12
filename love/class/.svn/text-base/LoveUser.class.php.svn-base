<?php
$app_root_path = dirname(__FILE__);
$app_root_path = str_replace('class','',$app_root_path);
require($app_root_path.'class/DataObject.php');

class LoveUser extends DataObject
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
     * @var string Avatar name
     */
    protected $photo;
    /**
     * @var int Company id
     */
    protected $company_id;
    /**
     * @var string Company name
     */
    protected $company_name;
    /**
     * @var bool company_admin
     */
    protected $company_admin;
    /**
     * @var int Phone number
     */
    protected $phone;
    /**
     * @var string Country abbreviation
     */
    protected $country;
    /**
     * @var string Phone Provider
     */
    protected $provider;
    /**
     * @var string Confirm phone
     */
    protected $confirm_phone;
    /**
     * @var string Send love via email
     */
    protected $send_love_via_email;
    /**
     * @var int Splash Screen
     */
    protected $splash;
    
    protected $access_token;
    protected $access_token_secret;
    
    protected $request_token;
    protected $request_token_secret;
    
    protected $linkedin_share;
    
    /**
     * @var object MySQL link identifier
     */
    protected $link;
    public function __construct(){
        parent::__construct();
        $new_user = isset($_SESSION["new_user"]) ? (bool) $_SESSION["new_user"] : false;
        if(isset($_SESSION["username"]) && isset($_SESSION["userid"]) && !$new_user){
           $this->loadUserFromSession(); 
        }
    }
    public function getLink(){
        if(!$this->link){
            $this->setLink();
        }
        return $this->link;
    }
    public function setLink(){
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
         $this->loadUserById($this->getId());
    }
    private function loadUserById($id){
        $id = (int)$id;
        $sql = "SELECT ".USERS.".*, ".COMPANY.".name as company_name  ".
               "FROM ".USERS.", ".COMPANY." ".
               "WHERE ".USERS.".id = ".mysql_real_escape_string($id)." AND ".
               USERS.".company_id = ".COMPANY.".id";
               
        $row = $this->doQuery($sql);
        
        if(isset($row->picture) && $row->picture != "NULL"){
            $this->setPhoto('/uploads/'.$row->picture);
        } else {
            $this->setPhoto('/images/no_picture.png');
        }
                
        $this->setCompany_admin(false);
        if (isset($_SESSION)) {
            if (array_key_exists('admin',$_SESSION) && ($_SESSION['admin'] == 1)) {
                $this->setCompany_admin(true);
            }
            
            $_SESSION["userid"] = $row->id;
            $_SESSION["company_id"] = $row->company_id;
            $_SESSION["company_name"] = $row->company_name;
            $_SESSION["phone"] = $row->phone;
            $_SESSION["country"] = $row->country;
            $_SESSION["provider"] = $row->provider;
        }
        
        $this->setId($row->id);
        $this->setUsername($row->username);
        $this->setNickname($row->nickname);
        $this->setCompany_id($row->company_id);
        $this->setCompany_name($row->company_name);
        $this->setPhone($row->phone);
        $this->setCountry($row->country);
        $this->setProvider($row->provider);
        $this->setConfirm_phone($row->confirm_phone);
        $this->setSend_love_via_email($row->send_love_via_email);
		$this->setSplash($row->splash);
        $this->setAccess_token($row->access_token);
        $this->setAccess_token_secret($row->access_token_secret);
        $this->setRequest_token($row->request_token);
        $this->setRequest_token_secret($row->request_token_secret);
        $this->setLinkedin_share($row->linkedin_share);
    }
    
    public function loadById($id) {
        $id = (int)$id;
        $sql = "SELECT ".USERS.".*, ".COMPANY.".name ".
               "FROM ".USERS.", ".COMPANY." ".
               "WHERE ".USERS.".login_id = ".mysql_real_escape_string($id)." AND ".
               USERS.".company_id = ".COMPANY.".id";
        $row = $this->doQuery($sql);
        if(isset($row->picture) && $row->picture != "NULL"){
            $this->setPhoto('/uploads/'.$row->picture);
        } else {
            $this->setPhoto('/images/no_picture.png');
        }
        $this->setCompany_admin(false);
        if ($_SESSION['admin'] != 0) {
        	$this->setCompany_admin(true);
        }
        $this->setId($row->id);
        $this->setUsername($row->username);
        $this->setNickname($row->nickname);
        $this->setCompany_id($row->company_id);
        $this->setCompany_name($row->name);
        $this->setPhone($row->phone);
        $this->setCountry($row->country);
        $this->setProvider($row->provider);
        $this->setConfirm_phone($row->confirm_phone);
        $this->setSend_love_via_email($row->send_love_via_email);
		$this->setSplash($row->splash);
        $this->setAccess_token($row->access_token);
        $this->setAccess_token_secret($row->access_token_secret);
        $this->setRequest_token($row->request_token);
        $this->setRequest_token_secret($row->request_token_secret);
        $this->setLinkedin_share($row->linkedin_share);
    }
    
    public function updateUser($userid, $username, $nickname, $admin) {
        $this->loadUserById($userid);
        // Update user data
        $sql = "UPDATE ".USERS.
               " SET nickname='" . mysql_real_escape_string($nickname).
               "', username='" . mysql_real_escape_string($username).
               "' WHERE ".USERS.".id = {$userid}";

        if (mysql_query($sql, $this->getLink())) {

            $sql = "UPDATE " . LOVE . " SET giver='" . mysql_real_escape_string($username).
                   "' WHERE giver='" . $this->username . "'";

            if (mysql_query($sql, $this->getLink())) {
                $sql = "UPDATE " . LOVE. " SET receiver='" . mysql_real_escape_string($username).
                       "' WHERE receiver='" . $this->username . "'";

                if (mysql_query($sql, $this->getLink())) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }

        } else {
            return false;
        }
    }
    
    public function getCompanyName($company){
        $sql = "SELECT * FROM ".COMPANY." ".
               "WHERE name = '".mysql_real_escape_string($company)."'";
        $res = mysql_query($sql,$this->getLink());
        if(mysql_num_rows($res) > 0){
            return mysql_fetch_object($res);
        } else {
            return false;
        }
    }
    public function newUser($id, $username, $nickname, $company, $phone=NULL,$country=NULL,$provider=NULL){
        $company_id = 0;
        $company_admin = 0;
        if($company != 0){
          $row = $this->getCompanyName($company);
          if($row !== false){
              $company_id = $row->id;
          }
        }
        $sql = "INSERT INTO ".USERS." ".
               "(`id`, `username`, `nickname`, `company_id`, `company_admin`, `company_confirm`, `phone`, `country`, `provider`) ".
               "VALUES(".$id.",'".$username."', '$nickname',".$company_id.",".$company_admin.",'0','".$phone."','".$country."','".$provider."')";
        mysql_query($sql,$this->getLink());
    }
    public function nicknameThenusername(){
        $nickname = $this->getNickname();
        return isset($nickname) ? $nickname : $this->getUsername();
    }
    private function doQuery($sql){
        $result=mysql_query($sql, $this->getLink());
        $ret=mysql_fetch_object($result);
        return $ret;
    }
    public function is_logged(){
        return isset($_SESSION['userid']);
    }
    public function logout(){
        session_destroy();
        header("location:login.php");
        exit;
    }
    
    // support redirect for marklet
    public function askUserToAuthenticate($marklet = false) {
        session_destroy();
        
        if ($marklet) {
            header("location: m_login.php?redir=".urlencode($_SERVER['REQUEST_URI']));
        } else {
            header("location: login.php?redir=".urlencode($_SERVER['REQUEST_URI']));
        }
        exit;
    }
	public function getSplash()
	{
		if ($this->splash) {
			return true;
		}
		return false;
	}
	
    public function getUserList()
    {
        $users = array();
        $link = $this->getLink();
        $query = 'SELECT `id`, `username`, `nickname`, `confirmed`, `active`, `date_added`, `removed`, `status` FROM `'.USERS.'`;';
        $res = mysql_query($query, $link) or die(mysql_error());
        
        while ($row = mysql_fetch_assoc($res)) {
            $users[] = array(
                'id' => $row['id'],
                'username' => $row['username'],
                'nickname' => $row['nickname'],
                'confirmed' => $row['confirmed'],
                'active' => $row['active'],
                'date_added'  => $row['date_added'],
                'removed' => $row['removed'],
                'status' => $row['status']
            );
        }
        return $users;
    }
    
    public function setLinkedinShare($switch){
        $sql = "UPDATE ".USERS." SET linkedin_share=" . (int)$switch . " WHERE ".USERS.".id = {$this->getId()}";
        mysql_query($sql);
    }
    
    public function updateRequestTokens($request, $request_secret) {
        $sql = "UPDATE ".USERS." SET request_token='" . mysql_real_escape_string($request)."',
                request_token_secret='" . mysql_real_escape_string($request_secret)."' WHERE ".USERS.".id = {$this->getId()}";
        mysql_query($sql);
    }
    
    public function updateAccessTokens($access, $access_secret) {
        $sql = "UPDATE ".USERS." SET access_token='" . mysql_real_escape_string($access)."',
                access_token_secret='" . mysql_real_escape_string($access_secret)."' WHERE ".USERS.".id = {$this->getId()}";
        mysql_query($sql);
    }
    
    public function deleteTokens(){
        $sql = "UPDATE ".USERS." SET access_token=NULL, request_token=NULL,
                request_token_secret=NULL, access_token_secret=NULL WHERE ".USERS.".id = {$this->getId()}";
        mysql_query($sql);
    }
}
