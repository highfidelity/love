<?php

class Sendlove_Love
{
	
	protected $_api;

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

	/**
	 * Sendlove function 
	 * @param string $from Users nickname
	 * @param string $to Users nickname
	 * @param string $why
	 * @return boolean
	 */
	public function sendLove($from = null, $to = null, $why = null)
	{
		if ((null === $from) || (null === $to) || (null === $why)) {
			throw new Exception('No or too less arguments given.');
		}
		
		$love = Zend_Json::decode($this->getApiHelper()->direct(array(
			'action'	=> 'sendlovemsg',
			'api_key'	=> $this->getApiHelper()->key('love'),
			'caller'	=> 'admin',
			'from'		=> (string)$from,
			'to'		=> (string)$to,
			'why'		=> (string)$why
		), $this->getApiHelper()->endpoint('love')));
		
		if ($love['status'] == 'ok') {
			return true;
		}
		return false;
	}
	
	/**
	 * Send love to all users
	 * @param string $from Users nickname
	 * @param string $why
	 * @return boolean
	 */
	public function sendLoveToAll($from = null, $why = null)
	{
		if ((null === $from) || (null === $why)) {
			throw new Exception('No or too less arguments given.');
		}
		
		$love = Zend_Json::decode($this->getApiHelper()->direct(array(
			'action'	=> 'sendloveToAll',
			'api_key'	=> $this->getApiHelper()->key('love'),
			'caller'	=> 'admin',
			'from'		=> (string)$from,
			'why'		=> (string)$why
		), $this->getApiHelper()->endpoint('love')));
		if ($love['success']) {
			return true;
		}
		return false;
	}
	
	/**
	 * send love to selected users
	 * @param string $from Users nickname
	 * @param string $why
	 * @param array $ids Ids of the receiving users
	 * @return boolean
	 */
	public function sendLoveToSelected($from = null, $why = null, array $ids = array())
	{
		if ((null === $from) || (null === $why) || empty($ids)) {
			throw new Exception('No or too less arguments given.');
		}
		
		$love = Zend_Json::decode($this->getApiHelper()->direct(array(
			'action'	=> 'sendLoveToSelected',
			'api_key'	=> $this->getApiHelper()->key('love'),
			'caller'	=> 'admin',
			'from'		=> (string)$from,
			'why'		=> (string)$why,
			'receivers' => $ids
		), $this->getApiHelper()->endpoint('love')));
		if ($love['success'] === true) {
			return true;
		}
		return false;
	}
	
	public function getLoveReceivedAmount($id = null, $days = 0)
	{
		if ((null === $id) || (0 === $days)) {
			throw new Exception('No or too less arguments given.');
		}
		
		$love = Zend_Json::decode($this->getApiHelper()->direct(array(
			'action'	=> 'getLoveReceivedAmount',
			'api_key'	=> $this->getApiHelper()->key('love'),
			'caller'	=> 'admin',
			'id'		=> (int)$id,
			'days'		=> (int)$days
		), $this->getApiHelper()->endpoint('love')));
		
		if ($love['success']) {
			return $love['user'];
		}
		return false;
	}

	public function getLoveSentAmount($id = null, $days = 0)
	{
		if ((null === $id) || (0 === $days)) {
			throw new Exception('No or too less arguments given.');
		}
		
		$love = Zend_Json::decode($this->getApiHelper()->direct(array(
			'action'	=> 'getLoveSentAmount',
			'api_key'	=> $this->getApiHelper()->key('love'),
			'caller'	=> 'admin',
			'id'		=> (int)$id,
			'days'		=> (int)$days
		), $this->getApiHelper()->endpoint('love')));
		
		if ($love['success']) {
			return $love['user'];
		}
		return false;
	}
	
	public function getLoveReceivedMessages($id = null, $days = 0)
	{
		if ((null === $id) || (0 === $days)) {
			throw new Exception('No or too less arguments given.');
		}
		
		$love = Zend_Json::decode($this->getApiHelper()->direct(array(
			'action'	=> 'getLoveReceivedMessages',
			'api_key'	=> $this->getApiHelper()->key('love'),
			'caller'	=> 'admin',
			'id'		=> (int)$id,
			'days'		=> (int)$days
		), $this->getApiHelper()->endpoint('love')));
		
		if ($love['success']) {
			return $love['user'];
		}
		return false;
	}
	
	public function getLoveSentMessages($id = null, $days = 0)
	{
		if ((null === $id) || (0 === $days)) {
			throw new Exception('No or too less arguments given.');
		}
		
		$love = Zend_Json::decode($this->getApiHelper()->direct(array(
			'action'	=> 'getLoveSentMessages',
			'api_key'	=> $this->getApiHelper()->key('love'),
			'caller'	=> 'admin',
			'id'		=> (int)$id,
			'days'		=> (int)$days
		), $this->getApiHelper()->endpoint('love')));
		
		if ($love['success']) {
			return $love['user'];
		}
		return false;
	}
	
	public function getInvitationUrl($username = null, $companyid = null, $invitorid = null, $asadmin = 0)
	{
		if ((null === $username) || (null === $companyid) || (null === $invitorid)) {
			throw new Exception('No or too less arguments given.');
		}
		
		$love = Zend_Json::decode($this->getApiHelper()->direct(array(
			'action'	=> 'getInvitationUrl',
			'api_key'	=> $this->getApiHelper()->key('love'),
			'username'	=> (string)$username,
			'companyid'	=> (int)$companyid,
			'invitorid'	=> (int)$invitorid,
			'asadmin'	=> (int)$asadmin
		), $this->getApiHelper()->endpoint('love')));
		
		if (!empty($love) && $love['success'] === true) {		
			return $love['url'];
		} else {
			return false;
		}
	}

	
	public function getUserLoveCount($request) {
		if(empty($request)) {
			return false;
		}
		$param = array('action' => 'getUserLoveCount','api_key' => $this->getApiHelper()->key('love'));
		$param = array_merge($param, $request);
		$love = Zend_Json::decode($this->getApiHelper()->direct($param, $this->getApiHelper()->endpoint('love')));
		if (!empty($love) && $love['success'] === true) {		
			return $love;
		} else {
			return false;
		}
	}

	public function getCampaignView() {
	    $param = array('action' => 'getCampaignView','api_key' => $this->getApiHelper()->key('love'));
	    $html = Zend_Json::decode($this->getApiHelper()->direct($param, $this->getApiHelper()->endpoint('love')));
	    if (!empty($html) && $html['success'] === true) {
	        return $html;
	    } else {
	        return false;
	    }
	}
}
