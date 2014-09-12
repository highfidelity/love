<?php

class ApiController extends Zend_Controller_Action
{
	const DEBUG = false;
	
	protected $_app;
	protected $_key;
	protected $_config;
	protected $_token;
	protected $_rsp;
	
	public function getApp()
	{
		return $this->_app;
	}

	public function setApp($_app)
	{
		$this->_app = $_app;
		return $this;
	}

	public function getKey()
	{
		return $this->_key;
	}

	public function setKey($_key)
	{
		$this->_key = $_key;
		return $this;
	}

	public function getToken()
	{
		return $this->_token;
	}

	public function setToken($_token)
	{
		$this->_token = $_token;
		return $this;
	}

	public function getRsp()
	{
		return $this->_rsp;
	}

	public function setRsp(array $_rsp)
	{
		$this->_rsp = $_rsp;
		$this->postDispatch();
	}
	
	public function init()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if (!$this->getRequest()->isPost()) {
			if (self::DEBUG === true) {
				echo('No post request!<br />');
			} else {
				$this->setRsp(array(
					'success' => false,
					'message' => 'No post request.'
				));
			}
		}
		
		$this->setApp((string)$this->getRequest()->getParam('app'))
			 ->setKey((string)$this->getRequest()->getParam('key'))
			 ->setToken((string)$this->getRequest()->getParam('token'));

        $this->securityCheck();
	}

	public function postDispatch()
	{
		echo(Zend_Json::encode($this->getRsp()));
		exit();
	}
	
	public function securityCheck()
	{
		if (($this->getApp() == '') || ($this->getKey() == '')) {
			if (self::DEBUG === true) {
				echo('Either app name or app key are not set!<br />');
			} else {
				$this->setRsp(array(
					'success' => false,
					'message' => 'You are not allowed to use this API!'
				));
			}
		}
		
		if ($this->getKey() !== $this->_helper->api->key($this->getApp())) {
			if (self::DEBUG === true) {
				echo('You have either the wrong app name or the wrong app key!<br />');
			} else {
				$this->setRsp(array(
					'success' => false,
					'message' => 'Wrong API app or key!'
				));
			}
		}
	}
	
	public function indexAction()
	{
		
	}
	
	public function getcompanysettingsAction()
	{
		$settings=$this->_helper->theme()->toArray();
		unset($settings["theme_name"]);
		$this->setRsp(array(
			'success'	=> true,
			'message'	=> 'companysettings',
			'token'		=> $this->getToken(),
			'settings'	=> $settings
		));
	}
  
  public function getactiveusersAction()
  {
    $userMapper = new Admin_Model_UserMapper();
    $users = $userMapper->find($this->_helper->config('application')->companyid);
    
    $this->setRsp(array(
      'success' => true,
      'message' => 'activeusers',
      'token'   => $this->getToken(),
      'count'   => count($users->toArray())
    ));
  }
  
}
