<?php
/**
 * Response object
 *
 * @category   LoveMachine
 * @package    Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link       http://www.lovemachineinc.com
 */
class Response {
    
    protected $body;
    private $users;
    
    // Create users request from Admin app
    public function pushAdminCreateUsers($calling_app, $vars){
         global $regApps;
         
         // Pass users to global memory
         $this->users = $vars;
         
         // Push the users creation to each registered application
        foreach ( $regApps as $app => $info ) {
            if(! $info || empty($info['endpoint']) || empty($info['key']) || empty($info['listenAddUser']) || $calling_app == $app)
                    continue;
            
            // Construct data for the request
            $addVars = array('action' => 'adminCreateUsers',
                             'api_key' => $info['key'],
                             'calling_app' => $calling_app,
                             'user_data' => $this->users
                             );
            
            // Send the request
            ob_start();
            CURLHandler::Post($info['endpoint'], $addVars);
            $result = ob_get_contents();
            
            // If the push user didn't succeded for all users
            if ($result['success'] == false) {
                // Set the valid users from the previous application
                // as the source for the next one.
                $this->updateLatestUsers($result['users']);                
            }
            ob_end_clean();
        }
        return $this->users;
    }
    
    public function updateLatestUsers($newUsers) {
        $this->users = $newUsers;
    }

    public function ldapAutoCreateUser($calling_app, $vars){
         global $regApps;
         foreach ( $regApps as $app => $info ) {
               if (! $info)
                    continue;
                    
               // additional vars for this application
               $addVars = array('action' => 'create','api_key' => $info['key'], 'calling_app' => $calling_app,'user_data'=>array());
               foreach($vars as $key=>$val){
                    if($key != "password"){
                         $addVars["user_data"][$key] = $val;
                    }
               }
               ob_start();
               // send the request
               CURLHandler::Post($info['endpoint'],$addVars);
               $result = ob_get_contents();
               ob_end_clean();
          }
        return false;
    }
    
    public function pushUser($calling_app, $id, $action, $vars = array()){
        global $regApps;
        
        // Create an array containing each user with it's data
        //foreach()
        
        $user = new LoveUser();
        if($user->loadById($id)){
            // push user to each registred application
            foreach($regApps as $app => $info) {
                if(!$info || empty($info['endpoint']) || empty($info['key']) || empty($info['listenAddUser']))
			        continue;
                    
                // TODO: Prepare an array containing the data for all users

                // Set the required info that will be given to the receiver application.
                $addVars = array(
                            'action' => $action, 
                            'id' => $id, 
                            'username' => $user->getUsername(), 
                            'nickname' => $user->getNickname(), 
                            'api_key' => $info['key'],
							'admin' => $user->getAdmin(),
                            'calling_app' => $calling_app,
                            );
                            
                // merge vars and additional vars
                $finVars = array_merge($addVars, $vars);
                ob_start();
                
                // Send the request
                CURLHandler::Post($info['endpoint'], $finVars);
                $result = ob_get_contents();
                ob_end_clean();
            }
            return true;
        }
        return false;
    }

    public function notifyOfLogin($calling_app, $user_id, $session_id){
        global $regApps;
        $user = new LoveUser();
        $user->loadById($user_id);

        // push notification of logged in user to each of registered apps
        // except of calling app
        foreach($regApps as $app => $info){
            if(is_array($info) && (!empty($info['endpoint'])) && (!empty($info['key']))  && ($calling_app != $app) && (!empty($info['listenLogin']))){

                // setting request variables
                $vars = array(
                            'action'     => 'login', 
                            'user_id'    => $user_id, 
                            'session_id' => $session_id,
                            'username'   => $user->getUsername(), 
                            'nickname'   => $user->getNickname(),
                            'admin'      => $user->getAdmin(),
                            'api_key'    => $info['key']);

                ob_start();
                // send the request
                CURLHandler::Post($info['endpoint'], $vars);
                $result = ob_get_contents();
                ob_end_clean();
            }
        }
        
        return true;
    }
    
    public function notifyOfUpdate($calling_app, $user_id, $user_data){
        global $regApps;

        // push notification of logged in user to each of registered apps
        // except of calling app
        foreach($regApps as $app => $info){
                if(! $info or empty($info['endpoint']) or empty($info['key']) or empty($info['listenUpdate']) or $calling_app == $app)
			continue; 

                // setting request variables
                $vars = array('action' => 'updateuser', 'user_id' => $user_id, 'api_key' => $info['key']);
                foreach($user_data as $key=>$value){
                    $vars["user_data"][$key] = $value;
                }
                ob_start();
                // send the request
                CURLHandler::Post($info['endpoint'], $vars);
                $result = ob_get_contents();
                ob_end_clean();
        }

        return true;
    }

    public function addParam($param){
        $this->body[] = $param;
    }
    public function addParams($params){
        foreach($params as $name => $value){
            $this->body[$name] = $value;
        }
    }
    public function sendResponse(){
        $output = array("error" => 0);
        foreach($this->body as $name => $value){
            $output[$name] = $value;
        }
        echo json_encode($output);
        exit(0);
    }
}
