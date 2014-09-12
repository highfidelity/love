<?php
require_once ("classes/CURLHandler.php");
require_once ("classes/Response.class.php");
require_once ("classes/Database.class.php");

class Login {
    /**
     * @var array
     */
    private $params;
    /** 
     * @var Response
     */
    protected $response;
    /**
     * @var Database
     */
    protected $database;
    
    public function __construct(){
        $this->params = array("app" => REVIEW_SERVICE_NAME, "key" => REVIEW_API_KEY);
    }
    public function setDatabase($db){
        $this->database = $db;
        return $this;
    }
    public function getDatabase(){
        if(! isset($this->database)){
            $this->setDatabase(new Database());
        }
        return $this->database;
    }
    public function setResponse($response){
        $this->response = $response;
        return $this;
    }
    public function getResponse(){
        if(! isset($this->response)){
            $this->setResponse(new Response());
        }
        return $this->response;
    }
    public function saveToken($token){
        $this->getDatabase()->insert(REVIEW_TOKENS, array('token' => $token, 'completed' => 0), array('%s', '%d'));
    }
    public function updateToken($token){
        $this->getDatabase()->update(REVIEW_TOKENS, array('completed' => 1), array('token' => $token), array('%d'), array('%s'));
    }
    public function checkToken($token){
        $res = $this->getDatabase()->query("SELECT completed FROM ".REVIEW_TOKENS." WHERE token = '" . sprintf('%s', $token) . "'");
        $ret = mysql_fetch_object($res);
        $found = mysql_num_rows($res);
        if($found > 0 && $ret->completed == 0){
            return true;
        }else{
            return false;
        }
    }
    public function signup(){
        if(! isset($_REQUEST["username"])){
            $this->getResponse()->getError()->setError("Username field is missing.");
        }else if(! isset($_REQUEST["password"])){
            $this->getResponse()->getError()->setError("Password field is missing.");
        }else if(! isset($_REQUEST["confirm_string"])){
            $this->getResponse()->getError()->setError("Confirm string is missing.");
        }else{
            $token = uniqid();
            $this->saveToken($token);
            $this->params["username"] = $_REQUEST["username"];
            $this->params["password"] = $_REQUEST["password"];
            if(isset($_REQUEST["nickname"])){
                $this->params["nickname"] = $_REQUEST["nickname"];
            }
            $this->params["token"] = $token;
            $this->params["confirm_string"] = $_REQUEST["confirm_string"];
            ob_start();
            // send the request
            CURLHandler::Post(LOGIN_APP_URL . 'create', $this->params, false, true);
            $result = ob_get_contents();
            ob_end_clean();
            $result = json_decode($result);
            if($result->error == 1){
                $this->getResponse()->getError()->setError($result->message);
            }else{
                if($this->checkToken($result->token) && $token == $result->token){
                    $this->updateToken($result->token);
                    $this->getResponse()->addParams($result);
                }else{
                    $this->getResponse()->getError()->setError("Invalid Token aka Malicious attempt.");
                }
            }
        }
    }
    public function login(){
        if(! isset($_REQUEST["username"])){
            $this->getResponse()->getError()->setError("Username field is missing.");
        }else if(! isset($_REQUEST["password"])){
            $this->getResponse()->getError()->setError("Password field is missing.");
        }else{
            $token = uniqid();
            $this->saveToken($token);
            $this->params["username"] = $_REQUEST["username"];
            $this->params["password"] = $_REQUEST["password"];
            $this->params["token"] = $token;
            ob_start();
            // send the request
            CURLHandler::Post(LOGIN_APP_URL . 'login', $this->params, false, true);
            $result = ob_get_contents();
            ob_end_clean();
            $result = json_decode($result);
            if($result->error == 1){
                $this->getResponse()->getError()->setError($result->message);
            }else{
                if($this->checkToken($result->token) && $token == $result->token){
                    $this->updateToken($result->token);
                    $this->getResponse()->addParams($result);
                }else{
                    $this->getResponse()->getError()->setError("Invalid Token aka Malicious attempt.");
                }
            }
        }
    }

    public function notify($user_id, $session_id){
        $token = uniqid();
        $this->saveToken($token);
        $this->params["userid"] = $user_id;
        $this->params["sessionid"] = $session_id;
        $this->params["token"] = $token;
        ob_start();
        // send the request
        CURLHandler::Post(LOGIN_APP_URL . 'notify', $this->params, false, true);
        $result = ob_get_contents();
        ob_end_clean();

        $result = json_decode($result);
        if($result->error == 1){
            $this->getResponse()->getError()->setError($result->message);
        }else{
            if($this->checkToken($result->token) && $token == $result->token){
                $this->updateToken($result->token);
                $this->getResponse()->addParams($result);
            }else{
                $this->getResponse()->getError()->setError("Invalid Token aka Malicious attempt.");
            }
        }
    }
}
