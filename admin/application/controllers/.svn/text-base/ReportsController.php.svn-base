<?php

class ReportsController extends Sendlove_SecureController
{
	protected $_companyId;
	protected $_reviewPeriodList;
	protected $_reportRequest;
	
	public function init()
	{
		parent::init();
		$this->_helper->layout->disableLayout();	
		//get company_id
		$company_id = $this->_helper->config('application')->companyid;
		// set company_id
		$this->setCompanyId($company_id);
		//get period list
		$reviewPeriodList = $this->getReviewPeriod();
		//set period list
		$this->setReviewPeriodList($reviewPeriodList);
		// pass the period list to the view
		$this->view->periods = $this->getReviewPeriodList();
		$this->_setRequest();
	}

	public function indexAction()
	{

	}

	public function loveAction() 
	{


	}

	public function lovedataAction() 
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getReportRequest();
		if(empty($request) && empty($request['start_date']) && empty($request['end_date'])) {
			echo(Zend_Json::encode(array(
				'success' => false,
				'message' => 'Please check the period.'
			)));
			return;
		}
		$userList = $this->getUserLoveCount($request);
		echo(Zend_Json::encode($userList));
	}

	public function rewarderAction() 
	{

	}

	public function rewarderdataAction() 
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getReportRequest();
		if(empty($request) && empty($request['start_date']) && empty($request['end_date'])) {
			echo(Zend_Json::encode(array(
				'success' => false,
				'message' => 'Please check the period.'
			)));
			return;
		}
		$rewarderList = $this->getUserByReward($request);
		echo(Zend_Json::encode($rewarderList));
	}

	public function graphAction() {
		$this->view->months = $this->getMonth();
		$this->view->year = $this->getYear();
	}

	public function graphloveAction() {
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getReportRequest();
		if(empty($request) && empty($request['start_date']) && empty($request['end_date'])) {
			echo(Zend_Json::encode(array(
				'success' => false,
				'message' => 'Please check the period.'
			)));
			return;
		}
		$userList = $this->getUserLoveCount($request);
		echo(Zend_Json::encode($userList));

	}

	public function graphrewarderAction() {
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getReportRequest();
		if(empty($request)) {
			echo(Zend_Json::encode(array(
				'success' => false,
				'message' => 'Please check the period.'
			)));
			return;
		}
		$rewarderList = $this->getUserByReward($request);
		echo(Zend_Json::encode($rewarderList));
	}

	public function graphannualloveAction() {
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getReportRequest();
		if(empty($request) && empty($request['period_year'])) {
			echo(Zend_Json::encode(array(
				'success' => false,
				'message' => 'Please check the period.'
			)));
			return;
		}
		$userList = $this->getUserLoveCount($request);
		echo(Zend_Json::encode($userList));
	}

	public function exportloveAction() 
	{   
		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getReportRequest();
		if(empty($request) && empty($request['start_date']) && empty($request['end_date'])) {
			echo(Zend_Json::encode(array(
				'success' => false,
				'message' => 'Unable to export. Please check the period.'
			)));
			return;
		}
		$love = $this->getUserLoveCount($request);
		if(!empty($love['userlist'])) {
			$data = array();
			$weightage = empty($request['weightage']) ? 1 : $request['weightage'];
			foreach($love['userlist'] as $row) {
				$amount = $row['received'] * $weightage;
				$data[] = array($row['username'],$row['nickname'], $row['sent'], $row['received'], $amount);
			}
			$from = strtotime($request['start_date']);
			$to = strtotime($request['end_date']);

			$filename = 'love-'.date('d.M.Y', $from) ."-". date('d.M.Y', $to);
			$cvs_header = "Username,Nickname,Love Sent,Total Love,Total Value\n";
			$utilities = new Sendlove_Utilities();
			$utilities->exportCSV($filename, $data, $cvs_header); 
			exit;
		}
	}


	public function exportrewarderAction() 
	{   

		$this->_helper->viewRenderer->setNoRender(true);
		$request = $this->getReportRequest();
		if(empty($request) && empty($request['start_date']) && empty($request['end_date'])) {
			echo(Zend_Json::encode(array(
				'success' => false,
				'message' => 'Unable to export. Please check the period.'
			)));
			return;
		}
		$rewarder = $this->getUserByReward($request);
		if(!empty($rewarder['rewarderlist'])) {
			$data = array();
			foreach($rewarder['rewarderlist'] as $row) {
				$data[] = array($row['username'],$row['nickname'], $row['points']);
			}
			$from = strtotime($request['start_date']);
			$to = strtotime($request['end_date']);

			$filename = 'rewarder-'.date('d.M.Y', $from) ."-". date('d.M.Y', $to);
			$cvs_header = "Username,Nickname,Points\n";
			$utilities = new Sendlove_Utilities();
			$utilities->exportCSV($filename, $data, $cvs_header); 
			exit;
		}
	}

	/**
	* getReviewPeriod function - get review periods
	* @return array
	*/
	public function getReviewPeriod() 
	{
		$review = new Sendlove_Review();
		$periods = $review->getReviewPeriod();
		return $periods;
	}

	public function getUserLoveCount($request) 
	{
		$love = new Sendlove_Love();
		return $love->getUserLoveCount($request);
	}

	public function getUserByReward($request) 
	{
		$review = new Sendlove_Review();
		return $review->getUserByReward($request);
	}

	function _setRequest() {
		if($this->_request->isGet() || $this->_request->isPost()) {
			$request = $this->_request->getParams();
			$reportRequest = array();
			$reportRequest['company_id'] = $this->getCompanyId();
			$reportRequest['start_date'] = !empty($request['start_date']) ? $request['start_date'] : "";
			$reportRequest['end_date'] = !empty($request['end_date']) ? $request['end_date'] : "";
			$reportRequest['weightage'] = !empty($request['weightage']) ? $request['weightage'] : 1;
			$reportRequest['is_active'] = !empty($request['is_active']) ? $request['is_active'] : 0;
			$reportRequest['sort'] = !empty($request['sort']) ? $request['sort'] : "username";
			$reportRequest['dir'] = !empty($request['dir']) ? $request['dir'] : "ASC";
			$reportRequest['page'] = !empty($request['page']) ? $request['page'] : 1;

			switch($request['action']) {
				case 'index':
					break;
				case 'love':
					break;
				case 'lovedata':
					$this->setReportRequest($reportRequest);
					break;
				case 'rewarder':
					break;
				case 'rewarderdata':
					$this->setReportRequest($reportRequest);
					break;
				case 'graph':
				
					break;
				case 'graphlove':
					unset($reportRequest['page']);
					$reportRequest['sort'] = "received";
					$reportRequest['dir']  = "DESC";
					$this->setReportRequest($reportRequest);
					break;
				case 'graphrewarder':
					unset($reportRequest['page']);
					$reportRequest['sort'] = "points";
					$reportRequest['dir']  = "DESC";
					$reportRequest['debug']  = 1;
					$this->setReportRequest($reportRequest);
					break;
				case 'graphannuallove':
					unset($reportRequest['page']);
					$reportRequest['sort'] = "received";
					$reportRequest['dir']  = "DESC";
					$this->setReportRequest($reportRequest);
					break;
				case 'exportlove':
					unset($reportRequest['page']);
					$reportRequest['sort'] = "username";
					$reportRequest['dir']  = "ASC";
					$this->setReportRequest($reportRequest);
					break;
				case 'exportrewarder':
					unset($reportRequest['page']);
					$reportRequest['sort'] = "username";
					$reportRequest['dir']  = "ASC";
					$this->setReportRequest($reportRequest);
					break;

			}
		}
	}

	//Setter

	function setCompanyId($data)
	{
		$this->_companyId = $data;
	}
	function setReviewPeriodList($data)
	{
		$this->_reviewPeriodList = $data;
	}
	function setReportRequest($data)
	{
		$this->_reportRequest = $data;
	}

	// Getter

	function getCompanyId()
	{
		return $this->_companyId;
	}
	function getReviewPeriodList()
	{
		return $this->_reviewPeriodList;	
	}
	function getReportRequest()
	{
		return $this->_reportRequest;
	}

	public	function getMonth() 
	{
		$month  = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" );
		return $month;
	}
	public function getYear() 
	{
		$year = array();
		$beginyear = 2009;
		$limit = 6;
		$endyear = $beginyear +  $limit;
			for ($i = $beginyear; $i <= $endyear; $i++ )  {
				 array_push($year, $i);
			}
		return $year;
	}

}

