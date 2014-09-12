<?php

abstract class Sendlove_SecureController extends Zend_Controller_Action
{
	
	protected $_session;
	
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
	
	public function init()
	{
	    // session check
        if ($this->getSession()->logged_in !== true) {
			if ($this->getRequest()->isXmlHttpRequest()) {
				echo(Zend_Json::encode(array(
					'success' => false,
					'code'	  => 1001,
					'message' => 'Your session has expired. Please refresh this page to login again.'
				)));
				exit();
			}
        	$this->_helper->getHelper('Redirector')->gotoSimple('index', 'login');
        }
        $this->getSession()->setExpirationSeconds(300, 'logged_in');
        $this->getSession()->logged_in = true;
	}
	
}
