<?php

class Zend_Controller_Action_Helper_Theme extends Zend_Controller_Action_Helper_Abstract
{

	protected $_mapper;
	protected $_theme;

	// sets the current theme on construct
	public function init()
	{
		$this->_mapper = new Admin_Model_ThemesMapper();
		$this->_theme = $this->getCurrentTheme();
	}

	public function direct($themeName = '')
	{
		if (!empty($themeName)) {
			return $this->getThemeByName($themeName);
		}
		return $this->getCurrentTheme();
	}

	public function saveTheme($newThemeName = '', $themeName = 'Custom')
	{
		if ('' === $newThemeName) {
			return false;
		}		
		$this->copyTheme($newThemeName, $themeName);
		return true;
	}

	public function changeTheme($options = array())
	{
		// create Custom if it doesn't exist
		if (!($this->_theme = $this->getThemeByName('Custom'))) {
			$this->copyTheme('Custom', 'LoveMachine');
		}

		// get the current theme 
		$theme = $this->getCurrentTheme();
		$newOptions = array_merge($this->cleanTheme($theme->toArray()), $options);
		$this->_theme->setOptions($newOptions);
		$this->_mapper->save($this->_theme);
		$this->changeSelectedTheme($this->_theme->getTheme_name());
	}

	public function deleteTheme($themeName = '')
	{
		$this->_theme = $this->getThemeByName($themeName);
		if (!$this->_theme->getProtected()) {
			$this->_mapper->delete($this->_theme);
			return true;
		}
		return false;
	}

	public function renameTheme($oldThemeName = '', $newThemeName = '')
	{
		if (('' === $oldThemeName) || ('' === $newThemeName)) {
			return false;
		}

		$theme = $this->getThemeByName($oldThemeName);
		$theme->setTheme_name($newThemeName);
		$this->_mapper->save($theme);
		return true;
	}

	public function changeSelectedTheme($newThemeName)
	{
		$companyMapper = new Admin_Model_CompanyMapper();
		$company = $companyMapper->find(Zend_Controller_Action_HelperBroker::getStaticHelper('config')->direct('application')->companyid);
		$company->setSelected_theme($newThemeName);
		$companyMapper->save($company);
	}

	public function allowOverwrite($file = 'logo')
	{
		if ($this->getCurrentTheme()->getTheme_name() == 'Custom') {
			// build getter
			$method = 'get' . ucfirst($file);
			if (!method_exists($this->getCurrentTheme(), $method)) {
				throw new Exception('The method "' . $method . '" does not exist for themes.');
			}
			
			if (!$this->isFileProtected($method, $this->getCurrentTheme()->$method())) {
				return true;
			}
		}	
		return false;
	}

	protected function getCurrentTheme()
	{
		$companyMapper = new Admin_Model_CompanyMapper();
		$company = $companyMapper->find(Zend_Controller_Action_HelperBroker::getStaticHelper('config')->direct('application')->companyid);
		if (!$this->_mapper->findByName($company->getSelected_theme())) {
			$this->changeSelectedTheme('LoveMachine');
		}
		return $this->_mapper->findByName($company->getSelected_theme());
	}

	protected function getThemeByName($themeName = 'LoveMachine')
	{
		return $this->_mapper->findByName($themeName);
	}

	protected function copyTheme($newThemeName = '', $themeName = 'Custom')
	{
		$default = $this->getThemeByName($themeName);
		$default->setId(null)
				->setTheme_name($newThemeName);
		$this->_mapper->save($default);
		$this->_theme = $this->getThemeByName($newThemeName);
		$this->changeSelectedTheme($newThemeName);
	}

	protected function cleanTheme($theme = array())
	{
		if (isset($theme['id'])) {
			unset($theme['id']);
		}

		if (isset($theme['theme_name'])) {
			unset($theme['theme_name']);
		}
	
		return $theme;
	}

	protected function isFileProtected($method, $id)
	{
		$themes = $this->_mapper->fetchAll();
		foreach ($themes as $theme) {
			if (!$theme->getProtected()) {
				continue;
			}
			
			if ($id === $theme->$method()) {
				return true;
			}
		}
		return false;
	}

}
