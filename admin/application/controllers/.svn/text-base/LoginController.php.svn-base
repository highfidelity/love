<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('login');
    }

    public function indexAction()
    {
        if ($this->getRequest()->isPost()) {
			$username = $this->getRequest()->getParam('username');
			$password = $this->getRequest()->getParam('password');

			if (!empty($username) && !empty($password)) {
				$token = uniqid();
				$login = Zend_Json::decode($this->_helper->api(array(
					'app'	   => $this->_helper->api->name('login'),
					'key'	   => $this->_helper->api->key('login'),
					'username' => $username,
					'password' => $password,
					'token'	   => $token
				), $this->_helper->api->endpoint('login') . 'login'));
				
				if ($login['error'] == 0) {
					if ($login['token'] == $token) {
						if ($login['admin'] == 1) {
							$dn = new Zend_Session_Namespace();
							$dn->userid = $login['userid'];
							$dn->username = $login['username'];
							$dn->nickname = $login['nickname'];
							$dn->setExpirationSeconds(300, 'logged_in');
							$dn->logged_in = true;

							$this->_helper->getHelper('Redirector')->gotoSimple('index', 'index');
						} else {
							$this->view->error = true;
							$this->view->message = 'You are not allowed to log into the control panel.';
						}
					} else {
						$this->view->error = true;
						$this->view->message = 'Invalid token, try it again later.';
					}
				} else {
					$this->view->error = true;
					$this->view->message = $login['message'][0];
				}
			} else {
				$this->view->error = true;
				$this->view->message = 'Username and password must not be empty!';
			}
		}
    }

	public function logoutAction()
	{
		$dn = new Zend_Session_Namespace();
		$dn->userid = '';
		$dn->username = '';
		$dn->nickname = '';
		$dn->logged_in = false;
		$this->_helper->getHelper('Redirector')->gotoSimple('index', 'login');
	}

}

