<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{    
	protected function _initSession()
	{
		Zend_Session::start();
	}
	
	protected function _initBaseUrl()
	{
		$this->bootstrap('frontController');
		$front = $this->getResource('frontController');
		if ((APPLICATION_ENV == 'production') || (APPLICATION_ENV == 'staging')) {
			$front->setBaseUrl('/admin/');
		} else {
			$array = preg_split('/index\.php/', $_SERVER['PHP_SELF']);
			$front->setBaseUrl($array[0]);
		}
	}
	
	protected function _initAutoloader()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace(array(
			'Sendlove_'
		));

		Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/controllers/helpers');
	}
	
	protected function _initDoctype()
	{
	    $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
	}
	
	protected function _initTitle()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->headTitle('Control Panel');
	}
	
	protected function _initCss()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
        $view->headLink()->headLink(array('rel' => 'shortcut icon', 'href' => $view->baseUrl() . '/images/favicon.ico', 'type'=>"image/x-icon"), 'PREPEND')
						 ->appendStylesheet($view->baseUrl() . '/css/smoothness/jquery-ui.css', 'screen')
						 ->appendStylesheet($view->baseUrl() . '/css/global.css', 'screen')
						 ->appendStylesheet($view->baseUrl() . '/css/login.css', 'screen');
		$dn = new Zend_Session_Namespace();
		if ($dn->logged_in) {
			$view->headLink()->appendStylesheet($view->baseUrl() . '/css/company.css', 'screen')
							 ->appendStylesheet($view->baseUrl() . '/css/user.css', 'screen')
							 //->appendStylesheet($view->baseUrl() . '/css/love.css', 'screen')
							 ->appendStylesheet($view->baseUrl() . '/css/reports.css', 'screen')
							 ->appendStylesheet($view->baseUrl() . '/css/colorpicker.css', 'screen');
		} else {
			$view->headLink()->appendStylesheet($view->baseUrl() . '/css/login.css', 'screen');
		}
	}
	
	protected function _initJs()
	{
	    $contribUrl = "https://" . SERVER_NAME . SANDBOX_URL_BASE . "/contrib";
        
		$this->bootstrap('view');
		$view = $this->getResource('view');
		
		$front = $this->getResource('frontController');
		$jsVariables = "
			var admin = {
				baseUrl: '" . rtrim($front->getBaseUrl(),"/") . "',
			};
		";
		
		$dn = new Zend_Session_Namespace();
				
		if ($dn->logged_in) {
		    $jsVariables .= "
		            var currentUser = {
		                    id: " . $dn->userid . ",
		                    username: '" . $dn->username . "',
		                    nickname: '" . $dn->nickname . "'
		            };
		    ";
		    $view->adminHeadScript()->appendScript($jsVariables)
		    						->appendFile($contribUrl . '/admin.combined.js', 'text/javascript')
                                    ->appendFile($view->baseUrl() . '/js/global.js', 'text/javascript')
									->appendFile($view->baseUrl() . '/js/company.js', 'text/javascript')
									->appendFile($view->baseUrl() . '/js/love.js', 'text/javascript')
									->appendFile($view->baseUrl() . '/js/user.js', 'text/javascript')
									->appendFile($view->baseUrl() . '/js/reports.js', 'text/javascript');
		} else {
			$view->adminHeadScript()->appendFile($contribUrl . '/admin_login.combined.js', 'text/javascript')
			                        ->appendFile($view->baseUrl() . '/js/login.js', 'text/javascript');
		}
	}

}

