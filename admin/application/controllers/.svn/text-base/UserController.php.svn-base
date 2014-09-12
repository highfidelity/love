<?php

class UserController extends Sendlove_SecureController
{
	
	protected $_uploadPath;
	protected $_loveemail;
	protected $_return;

	public function setUploadPath($path)
	{
		$this->_uploadPath = (string)$path;
		return $this;
	}
	
	public function getUploadPath()
	{
		return $this->_uploadPath;
	}

	public function setLoveEmail($email)
	{
		$this->_loveemail = (string)$email;
		return $this;
	}

	public function getLoveEmail()
	{
		return $this->_loveemail;
	}

    public function init()
    {
        parent::init();
        $this->_helper->layout->disableLayout();
        $this->setUploadPath(realpath($this->_helper->config('application')->uploadPath))
			 ->setLoveEmail($this->_helper->config('application')->loveEmail);
    }

	public function postDispatch()
	{
		if (!empty($this->_return)) {
			echo(Zend_Json::encode($this->_return));
		}
	}

    public function indexAction()
    {
        echo $this->view->render('user/advanced-settings.phtml');
    }

    public function bulkaddAction()  {
        $this->_helper->viewRenderer->setNoRender(true);
        
        if ($this->getRequest()->isPost()) {
            $upload = new Zend_File_Transfer_Adapter_Http();
            $upload->setDestination($this->getUploadPath());
            $upload->addValidator('Extension', false, 'csv');
            
            if ($upload->isValid()) {
                if ($upload->receive()) {
                    $userlist = array();
                    // collect some stats
                    $userstats = array('lines' => 0, 'count' => 0, 'blank' => 0, 'username_dupe' => 0, 'email_invalid' => 0);
                    $handle = fopen($upload->getFileName(), 'r');      
                          
                    while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                        // count total lines
                        $userstats['lines']++;
                        if ($data[0] == 'emails') {
                            // skip first line if contains 'emails' as per instructions 
                            continue;
                        } elseif ($data[0] == '') {
                            // count blank lines
                            $userstats['blank']++;
                        } else {
                            $validator = new Zend_Validate_EmailAddress();
                            // validate email address
                            if ($validator->isValid($data[0])) {
                                $splitEmail = explode('@', $data[0]);
                                // check if the username is duplicated in file
                                if (!Sendlove_Utilities::searchMultiArray($userlist, 'username', $data[0])) {
                                    // Check if the Nickname is duplicated
                                    $nick = $splitEmail[0];
                                    if (Sendlove_Utilities::searchMultiArray($userlist, 'nickname', $nick)) {
                                        $i = 1;
                                        // append number until we get a free nickname
                                        $temp_nick = $nick.(string)$i;
                                        while (Sendlove_Utilities::searchMultiArray($userlist, 'nickname', $temp_nick)) {
                                            $i++;
                                        }
                                        $nick .= (string)$i;
                                    }
                                                  
                                    $userlist[] = array(
                                        'username'  => $data[0],
                                        'password'  => Sendlove_Utilities::createPassword(),
                                        'nickname'  => $nick,
                                        'Active'    => 1,
                                        'Confirmed' => 1,
                                        'status'    => 'new'
                                    );
                                } else {
                                    // count duplicate emails
                                    $userstats['username_dupe']++;
                                }
                            } else {
                                // count invalid emails
                                $userstats['email_invalid']++;
                            }
                        }
                    }
                    // total users to process
                    $userstats['count'] = count($userlist);
                    echo(Zend_Json::encode(array(
                        'success' => true,
                        'message' => 'Processing ' . $userstats['count'] . ' users',
                        'stats'   => $userstats,
                        'file'    => basename($upload->getFileName())
                    )));
                    // write the user into the session for further processing. is this a problem?
                    $_SESSION['bulkusers'] = $userlist;
                    session_write_close();
                }
            } else {
                session_write_close();
                // upload failed
                echo(Zend_Json::encode(array(
                    'success' => false,
                    'message' => 'Could not add the users. Make sure you uploaded a CSV file in the format specified'
                )));                
            }
        }
    }
    
    public function bulkstatusAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        // is a bulk job in action?
        if (isset($_SESSION['bulkstatus'])) {
            if ($_SESSION['bulkstatus'] === false) {
                // yes, but it failed
                echo (Zend_Json::encode(array(
                    'success' => false,
                    'message' => $_SESSION['bulkerror']
                )));
                unset($_SESSION['bulkstatus']);
                unset($_SESSION['bulkusers']);
            } elseif ($_SESSION['bulkstatus'] === 0) {
                // yes, but it has finished
                echo(Zend_Json::encode(array(
                    'success' => true,
                    'message' => $_SESSION['bulkstatus'],
                    'users' => $_SESSION['bulkusers']
                )));
                unset($_SESSION['bulkstatus']);
                unset($_SESSION['bulkusers']);
            } else {
                // job in progress
                echo(Zend_Json::encode(array(
                    'success' => true,
                    'message' => $_SESSION['bulkstatus'],
                )));
            }
        } else {
            echo(Zend_Json::encode(array(
                'success' => false,
                'message' => 'No bulk job in progress'
            )));
        }
    }

    public function bulkprocessAction() {
        $this->_helper->viewRenderer->setNoRender(true);

        if ($_SESSION['bulkusers']) {
            $userlist = $_SESSION['bulkusers'];
            $_SESSION['bulkstatus'] = "Processing " . count($userlist) . " users";
            session_write_close();

            $token = uniqid();
            $login = Zend_Json::decode($this->_helper->api(array(
                'app'      => $this->_helper->api->name('login'),
                'key'      => $this->_helper->api->key('login'),
                'token'    => $token,
                'admin_id' => $this->getSession()->userid,
                'user_data'=> $userlist
            ), $this->_helper->api->endpoint('login') . 'admincreateusers'));
            
            if ($login['error'] == 0) {
                if ($login['token'] == $token) {
                    $companyMapper = new Admin_Model_CompanyMapper();
                    $company = $companyMapper->find($this->_helper->config('application')->companyid);
                    $love = new Sendlove_Love();
                    session_start();
                    $_SESSION['bulkstatus'] = 'Sending emails...';
                    session_write_close();

                    foreach ($userlist as $key => $user) {
                        $this->sendCreateMail(array(
                            'creator_nickname' => $this->getSession()->nickname,
                            'creator_email' => $this->getSession()->username,
                            'company_name' => $company->getCompany_name(),
                            'url' => $this->_helper->config('application')->loveLoginUrl,
                            'user_username' => $user['username'],
                            'user_password' => $user['password']
                        ));
                        
                        if (!empty($login[$user['username']])) {
                            $userlist[$key]['userid'] = $login[$user['username']];
                        }
                        unset($userlist[$key]['password']);
                        
                        if ($this->getRequest()->getParam('sendlove')) {
                            $love->sendLove($this->getLoveEmail(), $user['username'], 'Welcome to SendLove');
                        }
                    }
                    session_start();
                    $_SESSION['bulkstatus'] = 0;
                    // send back the userlist
                    $_SESSION['bulkusers'] = $userlist;
                    session_write_close();
                    echo(Zend_Json::encode(array(
                        'success' => true,
                        'message' => 'Users have been added.',
                        'users'   => $userlist
                    )));
                } else {
                    session_start();
                    $_SESSION['bulkstatus'] = false;
                    $_SESSION['bulkerror'] = 'Connection broke, try it again later.';
                    session_write_close();                       
                    echo(Zend_Json::encode(array(
                        'success' => false,
                        'message' => 'Connection broke, try it again later.'
                    )));
                }
            } else {
                session_start();
                $_SESSION['bulkstatus'] = false;
                $_SESSION['bulkerror'] = 'Could not add the users.';
                session_write_close();                        
                echo(Zend_Json::encode(array(
                    'success' => false,
                    'message' => 'Could not add the users.'
                )));
            }
        } else {
            session_start();
            $_SESSION['bulkstatus'] = false;
            $_SESSION['bulkerror'] = 'Did not find any users to add.';
            session_write_close();                        
            echo(Zend_Json::encode(array(
                'success' => false,
                'message' => 'Did not find any users to add.'
            )));
        }
    }
    
	public function addAction($email = null)
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$validator = new Zend_Validate_EmailAddress();
		if ($validator->isValid($this->getRequest()->getParam('email'))) {

			$splitEmail = explode('@', $this->getRequest()->getParam('email'));
			$password = Sendlove_Utilities::createPassword();
			$token = uniqid();
			$login = Zend_Json::decode($this->_helper->api(array(
				'app'	   => $this->_helper->api->name('login'),
				'key'	   => $this->_helper->api->key('login'),
				'token'	   => $token,
				'admin_id' => $this->getSession()->userid,
				'user_data'=> array(array(
					'username'	=> $this->getRequest()->getParam('email'),
					'password'	=> $password,
					'nickname'	=> $splitEmail[0],
					'Active'	=> 1,
					'Confirmed'	=> 1,
					'status'    => 'new'
				    ))
			), $this->_helper->api->endpoint('login') . 'admincreateusers'));
			
			if ($login['error'] == 0) {
				if ($login['token'] == $token) {
					if ($this->getRequest()->getParam('sendlove')) {
						$love = new Sendlove_Love();
						$love->sendLove($this->getLoveEmail(), $this->getSession()->username, 'Welcome to SendLove');
					}
					
					$companyMapper = new Admin_Model_CompanyMapper();
					$company = $companyMapper->find($this->_helper->config('application')->companyid);
					
					if (!$this->sendCreateMail(array(
						'creator_nickname' => $this->getSession()->nickname,
						'creator_email' => $this->getSession()->username,
						'company_name' => $company->getCompany_name(),
						'url' => $this->_helper->config('application')->loveLoginUrl,
						'user_username' => $this->getRequest()->getParam('email'),
						'user_password' => $password
					))) {
						throw new Exception('Email could not be sent!');
					}
				    
                    // Get the user data 
                    $user = $login['0'][0];
                    
                    $newUsers[] = array(
							'id'    	=> $user['id'],
							'username'	=> $user['username'],
							'nickname'	=> $user['nickname'],
							'active'	=> 1,
							'confirmed' => 1
                            );
                    
                    echo(Zend_Json::encode(array(
                        'success' => true,
                        'message' => 'User successfully added',
                        'users'   => $newUsers
                    )));
				} else {
					echo(Zend_Json::encode(array(
						'success' => false,
						'message' => $login['message']
					)));
				}
			} else {
				echo(Zend_Json::encode(array(
					'success' => false,
					'message' => 'Could not add the user.'
				)));
			}

		} else {
			echo(Zend_Json::encode(array(
				'success' => false,
				'message' => 'No valid email address.'
			)));
		}
	}
	
	public function resetpasswordAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$userMapper = new Admin_Model_UserMapper();
		$user = $userMapper->find((int)$this->getRequest()->getParam('userid'));
		if ($userMapper->resetPassword($user)) {
			echo(Zend_Json::encode(array(
				'success' => true,
				'message' => 'Password reset email has been sent.'
			)));
		} else {
			echo(Zend_Json::encode(array(
				'success' => false,
				'message' => 'Could not reset the password.'
			)));
		}
	}
	
	public function getuserdataAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$userMapper = new Admin_Model_UserMapper();
		$user = $userMapper->find((int)$this->getRequest()->getParam('user_id'));
		echo(Zend_Json::encode($user->toArray()));
	}
	
	public function saveadminauditorAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$id = (int)$this->getRequest()->getParam('id');
		$admin = (int)$this->getRequest()->getParam('admin');
		$auditor = (int)$this->getRequest()->getParam('auditor');
		
		$userMapper = new Admin_Model_UserMapper();
		$user = $userMapper->find($id);
		$user->setAdmin($admin);
		$user->setAuditor($auditor);
		
		if ($userMapper->save($user)) {
			echo(Zend_Json::encode(array(
				'success' => true,
				'message' => 'User has been saved!',
				'user'	  => $user->toArray()
			)));
		} else {
			echo(Zend_Json::encode(array(
				'success' => false,
				'message' => 'Could not save the user'
			)));
		}
	}

	public function saveAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		
		$params = $this->getRequest()->getParams();
		
		$userMapper = new Admin_Model_UserMapper();
		$user = $userMapper->find((int)$params['userid']);
		
		if ($user->getId()) {
			$user->setOptions($params);
			try {
				$userMapper->save($user);
				if ($params['removed'] == 1) {
                    echo(Zend_Json::encode(array(
                        'success' => true,
                        'message' => 'User has been removed',
                        'user'    => $user->toArray()
                    )));
				} else {
					echo(Zend_Json::encode(array(
						'success' => true,
						'message' => 'User has been saved!',
						'user'	  => $user->toArray()
					)));
				}
			} catch(Exception $e) {
				echo(Zend_Json::decode(array(
					'success' => false,
					'message' => $e->getMessage()
				)));
			}
		} else {
			echo(Zend_Json::encode(array(
				'success' => false,
				'message' => 'Could not save the user'
			)));
		}
	}
	
	public function testAction()
	{
//		$this->_helper->viewRenderer->setNoRender(true);
//		$userMapper = new Admin_Model_UserMapper();
//		$user = $userMapper->find(1129);
//		$user->setUsername('thomas@stachl.me');
//		$user->setConfirmed(1);
//		$user->setActive(1);
//		$user->setNickname('Tom');
//		$user->setAdmin(1);
//		$user->setAuditor(1);
//		$user->setPicture('https://dev.sendlove.us/~tom/love/thumb.php?src=/uploads/1129.jpg&h=75&w=75&zc=0');
//		$user->setTeam('');
//		$user->setSkill('PHP');
//		
//		$userMapper->save($user);
//		$this->_helper->viewRenderer->setNoRender(true);
//		$userMapper = new Admin_Model_UserMapper();
//		$user = $userMapper->find(1129);
//		$user->setAuditor(1)
//			 ->setGiver(1)
//			 ->setReceiver(1);
//		$userMapper->save($user);
	}
	
	public function userlistAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$userMapper = new Admin_Model_UserMapper();
		$result = array();
		try {
			$users = $userMapper->fetchAll();
			switch ($this->getRequest()->getParam('show')) {
				case 'admins':
					foreach ($users as $user) {
						if ($user->getAdmin()) {
							$result[] = $user->toArray();
						}
					}
					break;
				case 'auditors':
					foreach ($users as $user) {
						if ($user->getAuditor()) {
							$result[] = $user->toArray();
						}
					}
					break;
				case 'all':
				default:
					foreach ($users as $user) {
						$result[] = $user->toArray();
					}
					break;
			}
		} catch (Exception $e) {
			$result = array(
				'success' => false,
				'message' => $e->getMessage()
			);
		}

		
		echo(Zend_Json::encode($result));
	}
	
	protected function sendCreateMail(array $vars = array())
	{
		if (empty($vars)) {
			throw new Exception('You must set the email variables');
		}
		
		// get email templates
		$view = new Zend_View();
		$view->setScriptPath(APPLICATION_PATH . '/views/scripts/emails');
		$view->assign($vars);
	
		// send email
		$mail = new Sendlove_Mail();
		$mail->setBodyHtml($view->render('html_create_user.phtml'));
		$mail->setBodyText($view->render('text_create_user.phtml'));
		$mail->setFrom('love@sendlove.us', 'SendLove');
		$mail->addTo($vars['user_username']);
		$mail->setSubject('SendLove User Account Created');
		if ($mail->send()) {
			return true;
		}
		return false;
	}
}

