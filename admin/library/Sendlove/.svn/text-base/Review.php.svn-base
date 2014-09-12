<?php

class Sendlove_Review
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
	* getReviewPeriod - get list of period 
	* @return boolean or array
	*/
	public function getReviewPeriod() {
		$period = Zend_Json::decode($this->getApiHelper()->direct(array(
				'action'   => 'getReviewPeriod',
				'api_key'	=> $this->getApiHelper()->key('review')
			), $this->getApiHelper()->endpoint('review')));

		if (!empty($period) && array_key_exists('success',$period) && $period['success'] === true) {
			return $period['periodlist'];
		}
		return false;
	}

	public function getUserByReward($request) {
       
		if(empty($request)) {
			return false;
		}
		$param = array('action' => 'getUserByReward','api_key' => $this->getApiHelper()->key('review'));
		$param = array_merge($param, $request);
		$rewarder = Zend_Json::decode($this->getApiHelper()->direct($param, $this->getApiHelper()->endpoint('review')));
		if (!empty($rewarder) && $rewarder['success'] === true) {		
			return $rewarder;
		} else {
			return false;
		}
	}
}
	

