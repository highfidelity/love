<?php

class Admin_Model_ThemesMapper
{
	protected $_dbTable;
	
	public function setDbTable($dbTable)
	{
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        
        $this->_dbTable = $dbTable;
        return $this;
	}
	
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Admin_Model_DbTable_Themes');
        }
        
        return $this->_dbTable;
    }
    
    public function save(Admin_Model_Themes $theme)
    {
        $data = array(
            'theme_name'		=> $theme->getTheme_name(),
            'logo'				=> $theme->getLogo(),
        	'background_image'	=> $theme->getBackground_image(),
            'background_tile'   => $theme->getBackground_tile(),
            'background_fix'    => $theme->getBackground_fix(),
        	'background_color'	=> $theme->getBackground_color(),
        	'body_text_color'	=> $theme->getBody_text_color(),
        	'header_text_color'	=> $theme->getHeader_text_color(),
        	'highlight1_color'	=> $theme->getHighlight1_color(),
        	'highlight2_color'	=> $theme->getHighlight2_color(),
        	'website_font'		=> $theme->getWebsite_font()
        );
 
        if (null === ($id = $theme->getId())) {
            unset($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    
    public function find($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $theme = new Admin_Model_Themes();
        $theme->setId($row->id)
        		->setTheme_name($row->theme_name)
        		->setLogo($row->logo)
        		->setBackground_image($row->background_image)
            	->setBackground_tile($row->background_tile)
            	->setBackground_fix($row->background_fix)
        		->setBackground_color($row->background_color)
        		->setBody_text_color($row->body_text_color)
        		->setHeader_text_color($row->header_text_color)
        		->setHighlight1_color($row->highlight1_color)
        		->setHighlight2_color($row->highlight2_color)
        		->setWebsite_font($row->website_font)
				->setProtected($row->protected);
        return $theme;
    }

	public function findByName($name)
	{
		$row = $this->getDbTable()->fetchRow(
			$this->getDbTable()->select()->where('theme_name = ?', $name)
		);
		
		if (null === $row) {
			return false;
		}

		$theme = new Admin_Model_Themes();
        $theme->setId($row->id)
        		->setTheme_name($row->theme_name)
        		->setLogo($row->logo)
        		->setBackground_image($row->background_image)
            	->setBackground_tile($row->background_tile)
            	->setBackground_fix($row->background_fix)
        		->setBackground_color($row->background_color)
        		->setBody_text_color($row->body_text_color)
        		->setHeader_text_color($row->header_text_color)
        		->setHighlight1_color($row->highlight1_color)
        		->setHighlight2_color($row->highlight2_color)
        		->setWebsite_font($row->website_font)
				->setProtected($row->protected);
        return $theme;
	}
	
	public function fetchAll()
	{
		$resultSet = $this->getDbTable()->fetchAll();
		$themes = array();
		foreach ($resultSet as $row) {
			$theme = new Admin_Model_Themes();
			$theme->setId($row->id)
        		->setTheme_name($row->theme_name)
        		->setLogo($row->logo)
        		->setBackground_image($row->background_image)
            	->setBackground_tile($row->background_tile)
            	->setBackground_fix($row->background_fix)
        		->setBackground_color($row->background_color)
        		->setBody_text_color($row->body_text_color)
        		->setHeader_text_color($row->header_text_color)
        		->setHighlight1_color($row->highlight1_color)
        		->setHighlight2_color($row->highlight2_color)
        		->setWebsite_font($row->website_font)
				->setProtected($row->protected);
			$themes[] = $theme;
		}
		return $themes;
	}

	public function delete(Admin_Model_Themes $theme)
	{
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $theme->getId());
		return $this->getDbTable()->delete($where);
	}
}
