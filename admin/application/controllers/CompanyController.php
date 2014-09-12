<?php

class CompanyController extends Sendlove_SecureController
{

	protected $_companyId;
	
	public function setCompanyId($id)
	{
		$this->_companyId = (int)$id;
		return $this;
	}
	
	public function getCompanyId()
	{
		return $this->_companyId;
	}
	
    public function init()
    {
        parent::init();
        $this->_helper->layout->disableLayout();
        $this->setCompanyId($this->_helper->config('application')->companyid);
    }

    public function indexAction()
    {
        $companyMapper = new Admin_Model_CompanyMapper();
        $this->view->company = $companyMapper->find($this->getCompanyId());
        $this->view->fonts = array(
        	'Times New Roman',
        	'Georgia',
        	'Andale Mono',
        	'Arial',
        	'Arial Black',
        	'Century Gothic',
        	'Impact',
        	'Trebuchet MS',
        	'Verdana',
        	'Comic Sans MS',
        	'Courier New'
        );

		$this->view->weekdays = array(
			0 => 'Sunday',
			1 => 'Monday',
			2 => 'Tuesday',
			3 => 'Wednesday',
			4 => 'Thursday',
			5 => 'Friday',
			6 => 'Saturday'
		);
		$this->view->hours = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23);
		$this->view->minutes = array(0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55);
		
		$weeklyupdates = Zend_Json::decode($this->_helper->api(array(
			'action'   	=> 'getWeeklyUpdates',
			'api_key'   => $this->_helper->api->key('love')
		), $this->_helper->api->endpoint('love')));

		if ($weeklyupdates['success'] == true) {
			$this->view->weeklyupdates = $weeklyupdates['settings'];
		}

		$themesMapper = new Admin_Model_ThemesMapper();
		$this->view->themes = $themesMapper->fetchAll();
		$this->view->theme = $this->_helper->theme();
    }

	public function weeklyupdatesAction()
	{
    	$this->_helper->viewRenderer->setNoRender(true);
		$weeklyupdates = Zend_Json::decode($this->_helper->api(array(
			'action'   	=> 'setWeeklyUpdates',
			'api_key'   => $this->_helper->api->key('love'),
			'active'	=> $this->getRequest()->getParam('active'),
			'weekday'	=> $this->getRequest()->getParam('weekday'),
			'hour'		=> $this->getRequest()->getParam('hour'),
			'minute'	=> $this->getRequest()->getParam('minute'),
		), $this->_helper->api->endpoint('love')));

		if ($weeklyupdates['success'] === true) {
			echo(Zend_Json::encode(array(
				'success' => true,
				'message' => $weeklyupdates['message']
			)));
		} else {
			echo(Zend_Json::encode(array(
				'success' => false,
				'message' => 'Could not update the weekly updates settings.'
			)));
		}
	}

    public function updateappearanceAction()
    {
    	$this->_helper->viewRenderer->setNoRender(true);
    	$this->_helper->theme->changeTheme(array(
			'background_color'		=> $this->getRequest()->getParam('background_color'),
			'body_text_color'		=> $this->getRequest()->getParam('body_text_color'),
			'header_text_color'		=> $this->getRequest()->getParam('header_text_color'),
			'highlight1_color'		=> $this->getRequest()->getParam('highlight1_color'),
			'highlight2_color'		=> $this->getRequest()->getParam('highlight2_color'),
			'website_font'			=> $this->getRequest()->getParam('website_font')
		));

        echo(Zend_Json::encode(array(
          'success' => true,
          'message' => 'Appearance settings have been changed!'
        )));    
    }
    
    public function logouploadAction()
    {
    	$this->_helper->viewRenderer->setNoRender(true);
    	if ($this->getRequest()->isPost()) {
			
    		$upload = new Sendlove_File_Adapter_Database();
			$upload->setTable(new Zend_Db_Table('all_assets'))
				   ->addValidator('Count', false, array('min' =>1, 'max' => 1))
				   ->addValidator('Extension', false, 'jpg,jpeg,png,gif')
				   ->addValidator('ImageSize', false, array(
					   'maxwidth' => 200,
					   'maxheight' => 200
				   ));

			if ($this->_helper->theme->allowOverwrite('logo')) {
				$upload->setId($this->_helper->theme()->getLogo());
			}
			
			if ($upload->isValid()) {
	    		if ($upload->receive()) {
	    			
			    	$this->_helper->theme->changeTheme(array(
						'logo' => $upload->getId()
					));
			    	
	    			echo(Zend_Json::encode(array(
	    				'success' => true,
	    				'message' => 'File has been uploaded!',
	    				'id' 	  => $this->_helper->theme()->getLogo()
	    			)));
	    		}
			} else {
    			echo(Zend_Json::encode(array(
    				'success' => false,
    				'message' => 'File is not valid! Please check the filetype and size of the image.'
    			)));
			}
    	}
    }
    
    public function updatebackgroundAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
    	$this->_helper->theme->changeTheme(array(
			'background_tile'	=> (int)$this->getRequest()->getParam('background_tile'),
			'background_fix'	=> (int)$this->getRequest()->getParam('background_fix')
		));
        echo(Zend_Json::encode(array(
          'success' => true,
          'message' => 'Background settings have been changed!'
        )));        
    }
        

	public function backgroundimageuploadAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		if ($this->getRequest()->isPost()) {
    		$upload = new Sendlove_File_Adapter_Database();
			$upload->setTable(new Zend_Db_Table('all_assets'));
    		$upload->addValidator('Extension', false, 'jpg,jpeg,png,gif');

			if ($this->_helper->theme->allowOverwrite('background_image')) {
				$upload->setId($this->_helper->theme()->getBackground_image());
			}

			if ($upload->isValid()) {
	    		if ($upload->receive()) {
			    	$this->_helper->theme->changeTheme(array(
						'background_image' => $upload->getId()
					));
			    	
	    			echo(Zend_Json::encode(array(
	    				'success' => true,
	    				'message' => 'File has been uploaded!',
	    				'id' 	  => $this->_helper->theme()->getBackground_image()
	    			)));

				}
			} else {
    			echo(Zend_Json::encode(array(
    				'success' => false,
    				'message' => 'File is not valid! Please check the filetype and size of the image.'
    			)));
			}
		}
	}

	public function savethemeAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$name = $this->getRequest()->getParam('new_theme_name');
		$this->_helper->theme->saveTheme($name);

        echo(Zend_Json::encode(array(
          'success' => true,
          'message' => 'Theme has been saved!'
        )));
	}

	public function changethemeAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$name = $this->getRequest()->getParam('theme');
		$this->_helper->theme->changeSelectedTheme($name);

        echo(Zend_Json::encode(array(
          'success' => true,
          'message' => 'Changed to theme ' . $name . '!'
        )));
	}

	public function deletethemeAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$name = $this->getRequest()->getParam('theme');
		if ($this->_helper->theme->deleteTheme($name)) {
		    echo(Zend_Json::encode(array(
		      'success' => true,
		      'message' => 'Theme ' . $name . ' has been deleted!'
		    )));
		} else {
		    echo(Zend_Json::encode(array(
		      'success' => false,
		      'message' => 'Theme ' . $name . ' can not be deleted!'
		    )));
		}
	}

	public function renamethemeAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		if ($this->_helper->theme->renameTheme($this->getRequest()->getParam('old'), $this->getRequest()->getParam('new'))) {
		    echo(Zend_Json::encode(array(
		      'success' => true,
		      'message' => 'Theme has been renamed!'
		    )));
		} else {
		    echo(Zend_Json::encode(array(
		      'success' => false,
		      'message' => 'Theme could not be renamed!'
		    )));
		}
	}

}

