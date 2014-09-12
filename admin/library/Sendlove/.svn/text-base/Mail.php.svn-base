<?php

class Sendlove_Mail extends Zend_Mail
{
	protected $_config;
	
	public function setConfig()
	{
		$this->_config = Zend_Controller_Action_HelperBroker::getStaticHelper('config')->direct('mail');
		return $this;
	}
	
	public function getConfig()
	{
		if (null === $this->_config) {
			$this->setConfig();
		}
		return $this->_config;
	}
	
	public function __construct($charset = 'iso-8859-1')
	{
		if (!file_exists(APPLICATION_PATH . '/configs/mail.ini')) {
			throw new Exception('Mail configuration does not exist.');
		}
		parent::__construct($charset = 'iso-8859-1');
	}
	
	public function send()
	{
		$transport = null;
		if (!empty($this->getConfig()->auth)) {
			$config = array(
				'auth' => $this->getConfig()->auth,
				'username' => $this->getConfig()->username,
				'password' => $this->getConfig()->password,
				'ssl' => $this->getConfig()->ssl,
				'port' => $this->getConfig()->port
			);
			$transport = new Zend_Mail_Transport_Smtp($this->getConfig()->host, $config);
		} else {
                         error_log("send-ConfigAuth: ".$this->getConfig()->auth);
		}
		try {
			return parent::send($transport);
		} catch (Zend_Mail_Protocol_Exception $e) {
			error_log("ALERT: SendTransport: $e");
			return false;
		}
		return true;
	}
	
}
