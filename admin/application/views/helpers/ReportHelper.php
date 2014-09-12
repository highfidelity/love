<?php

class Zend_View_Helper_ReportHelper extends Zend_View_Helper_Abstract 
{
	public function  reportHelper($option, $data = array() ) {
	if (!is_array($data) || empty($data)) return $data;
        
		switch($option) {
			case 'month':
			return $this->getMonth($data);
			break;
			case 'period':
			return $this->getPeriod($data);
			break;
			case 'year':
			return $this->getYear($data);
			break;
		}
	}

	public function  getMonth(array $months = array()) 
	{
		$options = "";
		if(empty($months)) {
			return $options;
		}
		foreach($months as $key => $month)  {
			$options .=  "<option value= '".($key + 1)."'>". $month . "</option>";
		}
		return 	$options;
	}
	public function getYear(array $years = array()) 
	{
		$options = "";
		if(empty($years)) { 
			return $options;
		}
		foreach($years as $year) {
			$options .=  "<option value= '$year'>". $year . "</option>";
		}
		return $options;
	}
	public function getPeriod(array $periods = array())
	{
		$options = "";
		if(empty($periods)) {
			return $options;
		}
		foreach($periods as $period) {			
			$selected = '';
			if($period['current'] == 1) {
				$selected = 'selected';
			}				
			$options .= "<option start-date='".$period['start_date']."' end-date='". $period['end_date']."' value='". $period['id']."'". $selected .">" .$period['title']. " </option>";
		}
		return $options;
	}	

}
