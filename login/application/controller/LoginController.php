<?php
/**
 * Login Controller
 *
 * The login controller extends controller acton. 
 * 
 * @category LoveMachine
 * @package  Login
 * @author   LoveMachine Inc. <all@lovemachineinc.com>
 * @license  Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version  SVN: $Id: LoginController.php 2010-05-26 2:22:22Z yani $
 * @link     http://www.lovemachineinc.com
 */

class LoginController extends Controller_Action {
    
    /**
     * Default controller function
     * Outputs content to the screen
     *
     * @return string
     */
    public function index() {
        echo "This is the login service. Please specify an action.";
    }
    
    public function isRequestValid($requester) {
        $ret = false;
        
        // Peform security checks
        
        // Make sure the request is made through https
        if ($this->request->getScheme() != 'https') {
            $this->setError($requester . ": Only secure connection is allowed to this service.");
            error_log($requester . ": Only secure connection is allowed to this service.");
        } // Only allow POST requests
        else if ($this->request->getMethod() != 'POST') {
            $this->setError($requester . ": Only posts requests are allowed.");
            error_log($requester . ": Only posts requests are allowed.");
        } else {
	    if ($this->AppAuth->valid()) {  
                if ($this->AppAuth->signed()) { // and signed.
                    
                    // Once everything is valid, set return value to true.
                    $ret = true;
                    
                } else {
                    $this->setError($requester . ": Your key is invalid.");
                    error_log($requester . ": Your key is invalid.");
                }
            
            } else {
                $this->setError($requester . ": The application is not registered for this service.");
                error_log($requester . ": The application is not registered for this service.");
            }
        }
        return $ret;
    }

    public function completeResponse() {
        if ($this->getErrorFlag() === true) {
            $output = array("error" => 1, "message" => array(), "token" => $_REQUEST["token"]);
            foreach ( $this->getErrorMessage() as $m ) {
                $output["message"][] = $m;
            }
            echo json_encode($output);
            exit(0);
        } else {
            $this->response->sendResponse();
        }  
    }
    
    /**
     * Admin reset token controller function
     * Allows admin users to reset the token of a specific user
     *
     * @return json|xml
     */
    public function adminresettoken() {
        if ($this->isRequestValid("adminresettoken")) {
        
            $user = $this->authHandler->adminresettoken($this,
                                                        $_REQUEST["user_id"],
                                                        $_REQUEST["admin_id"]
                                                        );
            if ($user) {
                $this->response->addParams(array("username" => $user->getUsername(),
                                                 "token" => $_REQUEST["token"],
                                                 "confirm_string" => $user->getToken()
                                                ));
            }
        }
        $this->completeResponse();
    }
    
    /**
     * Admin create users controller function
     * Allows admin users to create new users 
     *
     * @return json|xml
     */
    public function admincreateusers() {
        if ($this->isRequestValid("admincreateusers")) {
                                 
            // Array of users that would be returned to the caller app
            $returnUsers = array();
            
            // Add the users to Login and get the ids back
            $usersCreate = $this->authHandler->admincreateusers($_REQUEST["user_data"]);
            
            // Get the users that did actually make it to the Db
            $userids = $this->userCreationSucceded($usersCreate);
            if (count($userids) == 0) {
                $this->setError("No new users added.");
                $this->completeResponse();
            }
            
            try {
                // Prepare and store users to push
                $users = array();
                $resp_users = array(); // Array with user data to send as response, for the caller app

                foreach ($userids as $uid) {
                    if ($uid["uid"]) {
                        // Load the user data
                        $userArr = $uid["user_data"];
                        // Make sure the user data is associated with the ID on the Login DB
                        $userArr["id"] = $uid["uid"];

                        // Store the user data into our container array
                        $users[] = $userArr;
                        // Store data in the array that'll be returned back to the caller app
                        $resp_users[] = array($userArr['username'] => $uid["uid"]);
                    }
                }

                // Send the array with all users to all applications to be added
                $returnUsers[] = $this->getResponse()->pushAdminCreateUsers($this->AppAuth->getAppName(), $users);
                // Return same array being processed back to the calling app
                $this->response->addParams($returnUsers);
                
            } catch (Exception $e) {
                $controller->setError($e->getMessage());
            }
            // Add the token to the response
            $this->response->addParams(array("token" => $_REQUEST["token"]));
        }
        $this->completeResponse();
    }

    /**
     * Get users from DB and compare it with the supplied
     * array, return the users that are present in both.
     */
    public function userCreationSucceded($newUsers) {
        $user = new LoveUser();
        $currentUsers = $user->getUserList();
        $existingUsers = array();
        $found = false;
        
        foreach ($newUsers as $newUser) {
            foreach ($currentUsers as $currentUser) {
                // If we can find the entry's username on the Db take it as the user exists
                if ($this->searchMultiArray($currentUser, 'username', $newUser['user_data']['username'])) {
                    // Set the found flag as true
                    $found = true;
                }
            }
            // If the user has not been found we add it
            if (!$found) {
                // Add it to the existing users array
                $existingUsers[] = $newUser;
            }
        }
        
        return $existingUsers;
    }
    
    /**
     * Seach a multidimensional array for $key->value
     * Returns true if found false if not found.
     */
    public static function searchMultiArray($array, $key = '', $value = '') {
        // If @array is empty, return not found
        if (!is_array($array) || empty($array)) {
            return false;
        }

        foreach ($array as $subArray) {
            if ($subArray[$key] == $value) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * Set user data controller function
     * Sets the data of a specific user 
     *
     * @return json|xml
     */
    public function setuserdata() {
        if ($this->isRequestValid("setuserdata")) {
        
            $user = $this->authHandler->setuserdata($this,$_REQUEST["user_data"],$_REQUEST["admin_id"]);
            if ($user) {
                $this->getResponse()->notifyOfUpdate($this->AppAuth->getAppName(),$_REQUEST["user_data"]["userid"],$_REQUEST["user_data"]);
                $this->response->addParams(array("token" => $_REQUEST["token"]));
            } else {
                $this->setError("Update failed.");
            }
        }
        $this->completeResponse();
    }
    
    /**
     * Get user data controller function
     * Gets the data of a specific user 
     *
     * @return json|xml
     */
    public function getuserdata() {
        if ($this->isRequestValid("getuserdata")) {
        
            $user_id = isset($_REQUEST['user_id']) ? trim($_REQUEST['user_id']) : '';
            $admin_id = isset($_REQUEST['admin_id']) ? trim($_REQUEST['admin_id']) : '';
            $user = $this->authHandler->getuserdata($this, $user_id, $admin_id);
            if ($user) {
                $this->response->addParams(array("userid" => $user->getId(), "username" => $user->getUsername(), "nickname" => $user->getNickname(), "token" => $_REQUEST["token"], "confirm_string" => $user->getToken(), "confirmed" => $user->getConfirmed(), "active" => $user->getActive(), "date_added" => $user->getDateAdded(), "date_modified" => $user->getDateModified(), "admin" => $user->getAdmin(), "removed" => $user->getRemoved()));
            }    
        }
        $this->completeResponse();
    }
    
    /**
     * Get user list controller function
     * Get a list of all users
     *
     * @return json|xml
     */
    public function getuserlist() {
        if ($this->isRequestValid("getuserlist")) {
        
            // Send the user list as a response
            $this->response->addParams($this->authHandler->getuserlist($this,$_REQUEST["admin_id"]));
        }
        $this->completeResponse();
    }
    
    /**
     * Login controller function
     *
     * Authenticates a user with the passed credentials
     * If error occurs, error flag is set and error message(s)
     * are returned. If authentication is successful, session
     * contains userID, username, confirmed, active, token, date_added, 
     * date_modified, nickname
     *
     * @return json|xml
     */
    public function login() {                    
        if ($this->isRequestValid("login")) {

            $username = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
            $password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';
            $user = $this->authHandler->authenticate($this,$username,$password);
            if ($user) {
                $this->response->addParams(array("userid" => $user->getId(), "username" => $user->getUsername(), "nickname" => $user->getNickname(), "admin" => $user->getAdmin(), "token" => $_REQUEST["token"], "confirm_string" => $user->getToken()));
            }
        }
        $this->completeResponse();
    }
    
    /**
     * Notifies controller function
     * Notifies registered apps that user has authenticted
     *
     * @return json|xml True on success otherwise false
     */
    public function notify() {
        if ($this->isRequestValid("notify")) {
        
            $user_id = isset($_REQUEST['userid']) ? intval($_REQUEST['userid']) : 0;
            $session_id = isset($_REQUEST['sessionid']) ? $_REQUEST['sessionid'] : '';
            
            $this->getResponse()->notifyOfLogin($this->AppAuth->getAppName(),$user_id,$session_id);
            $this->response->addParams(array("token" => $_REQUEST["token"]));
        }
        $this->completeResponse();
    }
    
    /**
     * Create controller function
     *
     * This function creates a new user.
     * 
     * The function validates the passed data
     * and return error and description of error
     * if the data is invalid. When the data is valid
     * a new user is created. 
     *
     * @return json|xml true on success false on error
     */
    public function create() {
        if ($this->isRequestValid("create")) {
        
            // Create a new user object, and fill it with the given data.
            $user = new LoveUser();
            $username = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
            $password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';
            $nickname = isset($_REQUEST['nickname']) ? trim($_REQUEST['nickname']) : '';
            $token = isset($_REQUEST['confirm_string']) ? trim($_REQUEST['confirm_string']) : uniqid();
            
            // TODO: Disable nickname collision checks, and enable soft failing.
            if (!$user->loadByUsername($username)) {
                if (!$user->loadByNickname($nickname)) {
                    $data = array("Username" => $username, "Password" => $password, "Nickname" => $nickname, "Active" => 1, "Confirmed" => 1, "Removed" => 0, "Admin" => 0, "Token" => $token, "DateAdded" => 0, "DateModified" => 0);
                    $user->loadData($data);
                    $id = $user->save();
                    
                    // Push user created to the applications
                    if (! $this->getResponse()->pushUser($this->AppAuth->getAppName(),$id,'pushCreateUser')) {
                        $this->setError("User could not be pushed to the registred applications.");
                    }
                    $this->response->addParams(array("id" => $user->getId(), "username" => $user->getUsername(), "nickname" => $user->getNickname(), "confirm_string" => $token, "token" => $_REQUEST["token"]));
                } else {
                    $this->setError("Nickname already registered!");
                }
            } else {
                $this->setError("Username already registered!");
            }
        }
        $this->completeResponse();
    }
    
    /**
     * Push admin user controller function
     * Calls all registered apps and instructs to insert a new admin user
     *
     * @return json|xml True on success otherwise false
     */
    public function pushadminuser() {
        if ($this->isRequestValid("pushadminuser")) {
        
            $user = new LoveUser();
            // Check if the user can be loaded successfully.
            if ($user->loadById((int) $_REQUEST['id'])) {
                $response_result = $this->getResponse()->pushUser($this->AppAuth->getAppName(), $user->getId(), 'pushCreateUser'); 
                
                // Check if the user could be pushed successfully.
                if (!$response_result) {
                    // Set an error if it couldn't.
                    $this->setError("User could not be pushed to the registred applications.");
                }
                $this->response->addParams(array("token" => $_REQUEST["token"]));
            } else { // Fail if the user couldn't be loaded.
                $this->setError("This user does not exist!");
            }
        }
        $this->completeResponse();
    }
    
    /**
     * Update controller function
     *
     * Updates userid with the passed data.
     * Verifies that the user and validates the data
     *
     * @return json|xml True on success otherwise false
     */
    public function update() {
        if ($this->isRequestValid("update")) {
                  
            $user = $this->authHandler->update($this,$_REQUEST["user_data"]);
            if ($user) {
                $this->getResponse()->notifyOfUpdate($this->AppAuth->getAppName(),$_REQUEST["user_data"]["userid"],$_REQUEST["user_data"]);
                $this->response->addParams(array("token" => $_REQUEST["token"]));
            } else {
                $this->setError("Update failed.");
            }
        }
        $this->completeResponse();
    }
    
    /**
     * Confirm controller function
     * Sets confirm to 1 of user with userid
     *
     * @return json|xml True on success otherwise false
     */
    public function confirm() {
        try {
            $error_flag = false;
            $message = "";
            $user = new LoveUser();
            $username = $_REQUEST["username"];
            $token = $_REQUEST["token"];
            if ($user->loadByUsername($username) && $user->getConfirmed() == 0 && $user->getToken() == $token) {
                $user->setConfirmed(1)->setActive(1);
                $user->save();
                // push user confirmed to registred applications
                if (! $this->getResponse()->pushUser($this->AppAuth->getAppName(),$user->getId(),'pushVerifyUser')) {
                    throw Exception('User could not be pushed to the registred applications.');
                }
                
                echo json_encode(array("error" => 0, "message" => "User confirmed"));
                exit(0);
            } else {
                echo json_encode(array("error" => 1, "message" => "Unable to confirm the user"));
                exit(0);
            }
        } catch ( Exception $e ) {
            $msg = 'An error occured while updating:' . "\n";
            $msg .= "\n" . ' ' . $e->getMessage();
            if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'development') {
                $msg .= "\n\nFile: " . $e->getFile();
                $msg .= "\nLine: " . $e->getLine();
                $msg .= "\n" . $e->getTraceAsString();
            } else if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'testing') {
                $msg .= "<br /><br />File: " . $e->getFile();
                $msg .= "<br />Line: " . $e->getLine();
                $msg .= "<br />" . $e->getTraceAsString();
            }
            echo $msg;
            exit();
        }
    }
    /**
     * Delete controller function
     *
     * Deletes the user with userid.
     * Verifies that the user is authenticated.
     *
     * @return json|xml True on success otherwise false
     */
    public function delete() {
        try {
            $error_flag = false;
            $message = "";
            $user = new LoveUser();
            if (! $user->loadUserFromSession()) {
                $error_flag = true;
                $message[] = "Unable to located the user using the current session";
            } else {
                if (! $user->authenticate($_REQUEST["password"])) {
                    $error_flag = true;
                    $message[] = "Invalid password";
                } else {
                    if (! $user->delete()) {
                        $error_flag = true;
                        $message[] = "Delete failed";
                    } else {
                        $message[] = "Delete successful";
                        $user->logout();
                    }
                }
            }
            if ($error_flag) {
                echo json_encode(array("error" => 1, "message" => $message));
                exit(0);
            } else {
                $repost = $this->request->getRepostPage();
                if (isset($repost)) {
                    CURLHandler::doRequest("POST",$repost,$_REQUEST);
                }
                echo json_encode(array("error" => 1, "message" => $message));
                exit(0);
            }
        } catch ( Exception $e ) {
            $msg = 'An error occured while updating:' . "\n";
            $msg .= "\n" . ' ' . $e->getMessage();
            if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'development') {
                $msg .= "\n\nFile: " . $e->getFile();
                $msg .= "\nLine: " . $e->getLine();
                $msg .= "\n" . $e->getTraceAsString();
            } else if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'testing') {
                $msg .= "<br /><br />File: " . $e->getFile();
                $msg .= "<br />Line: " . $e->getLine();
                $msg .= "<br />" . $e->getTraceAsString();
            }
            echo $msg;
            exit();
        }
    }
    
    public function resettoken() {
        if (!$this->isRequestValid("resettoken"))  {
            echo (json_encode(array('success' => false, 'message' => 'Invalid Request')));
            exit(0);
        }
        try {
            $error_flag = false;
            $message = '';
            $token = md5(uniqid());
            $user = new LoveUser();
            if ($user->loadByUsername($_REQUEST['username'])) {
                $user->setToken($token);
                $user->save();
                echo (json_encode(array('success' => true, 'message' => 'Token created.', 'token' => $token)));
                exit(0);
            } else {
                echo (json_encode(array('success' => false, 'message' => 'User not found.')));
                exit(0);
            }
        } catch ( Exception $e ) {
            $msg = 'An error occured while updating:' . "\n";
            $msg .= "\n" . ' ' . $e->getMessage();
            if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'development') {
                $msg .= "\n\nFile: " . $e->getFile();
                $msg .= "\nLine: " . $e->getLine();
                $msg .= "\n" . $e->getTraceAsString();
            } else if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'testing') {
                $msg .= "<br /><br />File: " . $e->getFile();
                $msg .= "<br />Line: " . $e->getLine();
                $msg .= "<br />" . $e->getTraceAsString();
            }
            echo $msg;
            exit();
        }
    }
    /**
     * Change password controller function
     *
     * Changes the password
     *
     * @return json|xml True on success otherwise false
     */
    public function changepassword() {
        try {
            $error_flag = false;
            $message = '';
            $user = new LoveUser();
            if ($user->loadByUsername($_REQUEST['username'])) {
                if ($user->getToken() == $_REQUEST['token']) {
                    $user->setPassword($_REQUEST['password']);
                    $user->setToken(md5(uniqid()));
                    $user->save();
                    echo (json_encode(array('success' => true, 'message' => 'Password changed.')));
                    exit(0);
                }
                echo (json_encode(array('success' => false, 'message' => 'Token not correct.')));
                exit(0);
            } else {
                echo (json_encode(array('success' => false, 'message' => 'User not found.')));
                exit(0);
            }
        } catch ( Exception $e ) {
            $msg = 'An error occured while updating:' . "\n";
            $msg .= "\n" . ' ' . $e->getMessage();
            if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'development') {
                $msg .= "\n\nFile: " . $e->getFile();
                $msg .= "\nLine: " . $e->getLine();
                $msg .= "\n" . $e->getTraceAsString();
            } else if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'testing') {
                $msg .= "<br /><br />File: " . $e->getFile();
                $msg .= "<br />Line: " . $e->getLine();
                $msg .= "<br />" . $e->getTraceAsString();
            }
            echo $msg;
            exit();
        }
    }
}
