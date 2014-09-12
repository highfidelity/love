<?php

class Admin_Model_CompanyMapper
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
            $this->setDbTable('Admin_Model_DbTable_Company');
        }
        
        return $this->_dbTable;
    }
    
    public function save(Admin_Model_Company $company)
    {
        $data = array(
            'company_name'		=> $company->getCompany_name(),
			'selected_theme'	=> $company->getSelected_theme()
        );
 
        if (null === ($id = $company->getId())) {
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
        $company = new Admin_Model_Company();
        $company->setId($row->id)
        		->setCompany_name($row->company_name)
				->setSelected_theme($row->selected_theme);
        return $company;
    }
	
}
