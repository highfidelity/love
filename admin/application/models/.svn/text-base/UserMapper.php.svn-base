<?php

class Admin_Model_UserMapper
{
	
	protected $_config;
	protected $_session;
	protected $_api;
	
	public function setSession()
	{
		$this->_session = new Zend_Session_Namespace();
		return $this;
	}
	
	public function getSession()
	{
		if (null === $this->_session) {
			$this->setSession();
		}
		return $this->_session;
	}

	public function setApiHelper()
	{
		$this->_api = Zend_Controller_Action_HelperBroker::getStaticHelper('api');
		return $this;
	}
	
	public function getApiHelper()
	{
		if (null === $this->_api) {
			$this->setApiHelper();
		}
		return $this->_api;
	}

	public function resetPassword(Admin_Model_User $user)
	{
		$login = Zend_Json::decode($this->getApiHelper()->direct(array(
    		'app' => $this->getApiHelper()->name('login'),
    		'key' => $this->getApiHelper()->key('login'),
			'user_id' => $user->getId(),
			'admin_id' => $this->getSession()->userid,
			'token' => uniqid()
		), $this->getApiHelper()->endpoint('login') . 'adminresettoken'));
		
		if ($login['error'] == 0) {
			// get email templates
			$vars = array(
				'url' => 'https://' . $_SERVER['HTTP_HOST'] . '/love/resetpass.php?un=' . base64_encode($login['username']) . '&token=' . $login['confirm_string']
			);
			$view = new Zend_View();
			$view->setScriptPath(APPLICATION_PATH . '/views/scripts/emails');
			$view->assign($vars);
		
			// send email
			$mail = new Sendlove_Mail();
			$mail->setBodyHtml($view->render('html_recovery.phtml'));
			$mail->setBodyText($view->render('text_recovery.phtml'));
			$mail->setFrom('love@sendlove.us', 'SendLove');
			$mail->addTo($login['username']);
			$mail->setSubject('SendLove Password Recovery');
			$mail->send();

			return true;
		}
		return false;
	}
	
	public function save(Admin_Model_User $user)
	{
		$user = $user->toArray();
		$login = Zend_Json::decode($this->getApiHelper()->direct(array(
			'admin_id' => $this->getSession()->userid,
    		'token' => uniqid(),
    		'app' => $this->getApiHelper()->name('login'),
    		'key' => $this->getApiHelper()->key('login'),
			'user_data' => array(
				'userid' => $user['id'],
				'username' => $user['username'],
				'nickname' => $user['nickname'],
				'confirmed' => $user['confirmed'],
				'active' => $user['active'],
				'admin' => $user['admin'],
				'date_added' => $user['date_added'],
				'date_modified' => $user['date_modified'],
				'removed' => $user['removed']				
			)
		), $this->getApiHelper()->endpoint('login') . 'setuserdata'));
		
		if (($login['error'] == 1) && ($login['message'][0] != 'Unexpected number of affected rows: 0')) {
			throw new Exception('1 - Failed to save user data.');
		}
		
		// save auditor to review
		$review = Zend_Json::decode($this->getApiHelper()->direct(array(
			'user_id' => $user['id'],
                        'api_key' => $this->getApiHelper()->key('review'),
			'action'  => 'saveUser',
			'user' => array(
				'auditor' => $user['auditor'],
				'is_giver' => $user['is_giver'],
				'is_receiver' => $user['is_receiver']
			)
		), $this->getApiHelper()->endpoint('review')));
		
		if ($review['success'] == false) {
			throw new Exception('2 - Failed to save user data.');
		}
		
		// save picture, team and skill from love app
		$love = Zend_Json::decode($this->getApiHelper()->direct(array(
			'action'   	=> 'saveUser',
			'api_key'       => $this->getApiHelper()->key('love'),
			'user_id'	=> $user['id'],
			'picture'	=> $user['picture'],
			'team'		=> $user['team'],
			'skill'		=> $user['skill'],
                        'active'        => $user['active'],
                        'removed'       => $user['removed']
		), $this->getApiHelper()->endpoint('love')));
		
		if ($love['success'] == false) {
			throw new Exception('3 - Failed to save user data.');
		}
		
		return true;
	}
	
	public function find($id)
	{
		// get a user from the login app
		$user = Zend_Json::decode($this->getApiHelper()->direct(array(
    		'admin_id' => $this->getSession()->userid,
			'user_id' => $id,
    		'token' => uniqid(),
    		'app' => $this->getApiHelper()->name('login'),
    		'key' => $this->getApiHelper()->key('login')
		), $this->getApiHelper()->endpoint('login') . 'getuserdata'));
		
		$user['id'] = !empty($user['userid'])?$user['userid']:0;
		
		$user = new Admin_Model_User($user);
		
		$review = Zend_Json::decode($this->getApiHelper()->direct(array(
			'user_id' => $user->getId(),
    		'api_key' => $this->getApiHelper()->key('review'),
			'action'  => 'getUser'
		), $this->getApiHelper()->endpoint('review')));
		
		if (isset($review['user'])) {
			$user->setAuditor($review['user']['is_auditor'])
				 ->setGiver($review['user']['is_giver'])
				 ->setReceiver($review['user']['is_receiver']);
		}
		
		// get picture, team and skill from love app
		$love = Zend_Json::decode($this->getApiHelper()->direct(array(
			'action'   	=> 'getUser',
			'api_key'   => $this->getApiHelper()->key('love'),
			'user_id'	=> $user->getId()
		), $this->getApiHelper()->endpoint('love')));
		
		if (isset($love['user'])) {
			$user->setPicture($love['user']['picture'])
				 ->setTeam($love['user']['team'])
				 ->setSkill($love['user']['skill']);
		}
		
		return $user;
	}
	
	public function fetchAll()
	{
        // get all users from the login app
		$userlist = Zend_Json::decode($this->getApiHelper()->direct(array(
    		'admin_id' => $this->getSession()->userid,
    		'token' => uniqid(),
    		'app' => $this->getApiHelper()->name('login'),
    		'key' => $this->getApiHelper()->key('login')
		), $this->getApiHelper()->endpoint('login') . 'getuserlist'));
		
		if ($userlist['error'] == 1) {
			throw new Exception('1 - Failed to load userlist.');
		}

		// get all auditors from the review app
		$review = Zend_Json::decode($this->getApiHelper()->direct(array(
                        'api_key' => $this->getApiHelper()->key('review'),
			'action'  => 'getUsers'
		), $this->getApiHelper()->endpoint('review')));

		if ($review['success'] == false) {
			throw new Exception('2 - Failed to load userlist.');
		}
		
		// get picture, team and skill from love app
		$love = Zend_Json::decode($this->getApiHelper()->direct(array(
			'action'   => 'getUserlist',
			'api_key'      => $this->getApiHelper()->key('love')
		), $this->getApiHelper()->endpoint('love')));
		
		if ($love['success'] == false) {
			throw new Exception('3 - Failed to load userlist.');
		}
		
		foreach ($userlist as $user) {
			if (is_array($user)) {
				$newUser = new Admin_Model_User($user);

				if (isset($review['userlist']) && isset($review['userlist'][$newUser->getId()])) {
					$newUser->setAuditor($review['userlist'][$newUser->getId()]['is_auditor'])
						 ->setGiver($review['userlist'][$newUser->getId()]['is_giver'])
						 ->setReceiver($review['userlist'][$newUser->getId()]['is_receiver']);
				}
				
				if (isset($love['userlist']) && isset($love['userlist'][$newUser->getId()])) {
					$newUser->setPicture($love['userlist'][$newUser->getId()]['picture'])
							->setTeam($love['userlist'][$newUser->getId()]['team'])
							->setSkill($love['userlist'][$newUser->getId()]['skill']);
				}
				
				$users[] = $newUser;
			}
		}
		
		return $users;
	}

}
