<?php
require dirname(__FILE__) . '/DataObject.php';
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
        $sql = "SELECT ".LOVE_USERS.".*, ".LOVE_COMPANIES.".name ".
               "FROM ".LOVE_USERS.", ".LOVE_COMPANIES." ".
               "WHERE ".LOVE_USERS.".id = ".mysql_real_escape_string($id)." AND ".
               LOVE_USERS.".company_id = ".LOVE_COMPANIES.".id";
        $row = $this->doQuery($sql);
        if(isset($row->picture) && $row->picture != "NULL"){
            $this->setPhoto('/uploads/'.$row->picture);
        } else {
            $this->setPhoto('/images/no_picture.png');
        }
        $this->setCompany_admin(false);
        if ($row->company_admin != 0) {
        	$this->setCompany_admin(true);
        }
        $this->setUsername($row->username);
        $this->setNickname($row->nickname);
        $this->setCompany_id($row->company_id);
        $this->setCompany_name($row->name);
        $this->setPhone($row->phone);
        $this->setCountry($row->country);
        $this->setProvider($row->provider);
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
}
