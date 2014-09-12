<?php

abstract class Sendlove_DbTable extends Zend_Db_Table_Abstract
{

	protected $_configHelper;

	public function setConfigHelper()
	{
		$this->_configHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('config');
		return $this;
	}

	public function getConfigHelper()
	{
		if (null === $this->_configHelper) {
			$this->setConfigHelper();
		}
		return $this->_configHelper;
	}

	public function __construct()
	{
		$name = $this->_name;
		$this->_name = $this->getConfigHelper()->direct('application')->dbTable->$name;
		parent::__construct();
	}

}
