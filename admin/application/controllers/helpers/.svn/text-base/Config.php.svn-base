<?php

class Zend_Controller_Action_Helper_Config extends Zend_Controller_Action_Helper_Abstract
{
	protected $_config;
	
	public function getConfig()
	{
		return $this->_config;
	}
	
	public function setConfig(Zend_Config $config)
	{
		$this->_config = $config;
		return $this;
	}
	
	public function direct($config = null, $type = 0)
	{
		if (null === $config) {
			throw new Exception('No config item set!');
		}
		
		if (0 === $type) {
			$this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . '/configs/' . $config . '.ini', APPLICATION_ENV));
		} else {
			$this->setConfig(new Zend_Config_Ini(APPLICATION_PATH . '/configs/' . $config . '.xml', APPLICATION_ENV));
		}
		
		return $this->getConfig();
	}
	
}