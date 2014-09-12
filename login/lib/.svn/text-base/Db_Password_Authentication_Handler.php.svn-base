<?php
/**
 * Db_Password_Authentication_Handler
 * 
 * Handles the regular database-backed authentication. This checks the uesr's email and password in database user table.
 *
 * @category   LoveMachine
 * @package    Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link       http://www.lovemachineinc.com
 */

class Db_Password_Authentication_Handler extends Authentication_Handler {
    
    /**
     * Authenticate the user
     *
     * @var Controller_Action
     * @var Username
     * @var Password
     * @return if success user's object otherwise NULL.
     */
    
    public function authenticate(Controller_Action $controller, $username, $password) {
        $result = NULL;
        if ($this->user->loadByUsername($username)) {
            if ($this->user->isNotRemoved()) {
                if ($this->user->isActive()) {
                    if ($this->user->authenticate($password)) {
                        $result = $this->user;
                    } else {
                        $controller->setError("Invalid password.");
                    }
                } else {
                    $controller->setError("User is deactivated.");
                }
            } else {
                $controller->setError("User is removed.");
            }
        } else {
            $controller->setError("Username doesn't exist.");
        }
        
        return $result;
    }
    
    
    public function admincreateusers($user_data) {
        // Check that the user is not already on the system
        $users = new LoveUser();
        $userlist = $users->getUserList();
        $newUsers = array();
        $returnUsers = array();
        foreach ($user_data as $user) {
            if (!$this->searchMultiArray($userlist, 'username', $user['username'])) {
                $counter = 1;
                $user_nick = $user['nickname'];
                
                while ($this->searchMultiArray($userlist, 'nickname', $user['nickname'])) {
                    $user['nickname'] = $user_nick . "+" . (string)$counter;
                    $counter++;
                }
                $newUsers[] = $user;
            } else {
                // Check if the user was removed, and if so, readd him
                foreach ($userlist as $singleUser) {
                    if ($singleUser['username'] == $user['username']) {
                        if ($singleUser['removed'] == 1) {
                            // Unset removed flag
                            $user['removed'] = 0;
                            $user_obj = new LoveUser();
                            $user_obj->loadByUsername($user['username']);
                            $user_obj->setPassword($user['password']);
                            $user_obj->setRemoved(0);
                            $user_obj->save();
                            
                            // And add it to the create list
                            $returnUsers[] = array('uid' => $user_obj->id,
                                                   'user_data' => $user);
                        }
                    }
                }
            }
        }
        
        if (count($newUsers) > 0) {
            return array_merge($returnUsers, $users->insertUsers($newUsers));
        } else {
            return $returnUsers;
        }
    }
    
    public function setuserdata(Controller_Action $controller, $user_data, $admin_id) {
        $result = NULL;
        if ($this->user->loadById($admin_id)) {
            if ($this->user->isActive()) {
                if ($this->user->isNotRemoved()) {
                    if ($this->user->isAdmin()) {
                        if ($this->user->loadById($user_data["userid"])) {
                            foreach ( $user_data as $key => $value ) {
                                if ($key != "userid") {
                                    $method = 'set' . DataObject::camelize($key);
                                    $this->user->$method($value);
                                }
                            }
                            try {
                                if ($this->user->save()) {
                                    $result = true;
                                } else {
                                    $controller->setError("Unable to save new user data.");
                                }
                            } catch ( Exception $e ) {
                                $controller->setError($e->getMessage());
                            }
                        } else {
                            $controller->setError("User doesn't exist.");
                        }
                    } else {
                        $controller->setError("You are not admin user.");
                    }
                } else {
                    $controller->setError("Admin user is removed.");
                }
            } else {
                $controller->setError("Admin user is deactivated.");
            }
        } else {
            $controller->setError("Admin user doesn't exist.");
        }
        return $result;
    }
    public function update(Controller_Action $controller, $user_data) {
        $result = NULL;
        if ($this->user->loadById($user_data["userid"])) {
            if (isset($user_data["oldpassword"])) {
                if (! $this->user->authenticate($user_data["oldpassword"])) {
                    $controller->setError("Invalid password.");
                }
            }
            if (! $controller->getErrorFlag()) {
                foreach ( $user_data as $key => $value ) {
                    if ($key != "userid" && $key != "oldpassword" && $key != "newpassword") {
                        $method = 'set' . DataObject::camelize($key);
                        $this->user->$method($value);
                    }
                    if ($key == "newpassword") {
                        $this->user->setPassword($value);
                    }
                }
                try {
                    if ($this->user->save()) {
                        $result = true;
                    } else {
                        $controller->setError("Unable to save new user data.");
                    }
                } catch ( Exception $e ) {
                    $controller->setError($e->getMessage());
                }
            }
        } else {
            $controller->setError("User doesn't exist.");
        }
        return $result;
    }
    
    public function getuserdata(Controller_Action $controller, $user_id, $admin_id) {
        $result = NULL;
        if ($this->user->loadById($admin_id)) {
            if ($this->user->isActive()) {
                if ($this->user->isNotRemoved()) {
                    if ($this->user->isAdmin()) {
                        if ($this->user->loadById($user_id)) {
                            $result = $this->user;
                        } else {
                            $controller->setError("User doesn't exist.");
                        }
                    } else {
                        $controller->setError("You are not admin user.");
                    }
                } else {
                    $controller->setError("Admin user is removed.");
                }
            } else {
                $controller->setError("Admin user is deactivated.");
            }
        } else {
            $controller->setError("Admin user doesn't exist.");
        }
        return $result;
    }
    
    public function getuserlist(Controller_Action $controller, $admin_id) {
        $result = NULL;
        if ($this->user->loadById($admin_id)) {
            if ($this->user->isActive()) {
                if ($this->user->isNotRemoved()) {
                    if ($this->user->isAdmin()) {
                        
                        $result = $this->user->getUserList();
                        
                    } else {
                        $controller->setError("You are not admin user.");
                    }
                } else {
                    $controller->setError("Admin user is removed.");
                }
            } else {
                $controller->setError("Admin user is deactivated.");
            }
        } else {
            $controller->setError("Admin user doesn't exist.");
        }
        return $result;
    }
    
    public function adminresettoken(Controller_Action $controller, $user_id, $admin_id) {
        $result = NULL;
        if ($this->user->loadById($admin_id)) {
            if ($this->user->isActive()) {
                if ($this->user->isNotRemoved()) {
                    if ($this->user->isAdmin()) {
                        if ($this->user->loadById($user_id)) {
                            $this->user->setToken(uniqid());
                            try {
                                if ($this->user->save()) {
                                    $result = $this->user;
                                } else {
                                    $controller->setError("Unable to save new user data.");
                                }
                            } catch ( Exception $e ) {
                                $controller->setError($e->getMessage());
                            }
                            $result = $this->user;
                        } else {
                            $controller->setError("User doesn't exist.");
                        }
                    } else {
                        $controller->setError("You are not admin user.");
                    }
                } else {
                    $controller->setError("Admin user is removed.");
                }
            } else {
                $controller->setError("Admin user is deactivated.");
            }
        } else {
            $controller->setError("Admin user doesn't exist.");
        }
        return $result;
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
}

?>
