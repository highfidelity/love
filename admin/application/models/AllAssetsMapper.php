<?php

class Admin_Model_AllAssetsMapper
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
            $this->setDbTable('Admin_Model_DbTable_AllAssets');
        }
        
        return $this->_dbTable;
    }
    
    public function save(Admin_Model_AllAssets $asset)
    {
        $data = array(
            'app'				=> $asset->getApp(),
            'content_type'		=> $asset->getContent_type(),
        	'content'			=> $asset->getContent(),
            'size'   			=> $asset->getSize(),
            'filename'			=> $asset->getFilename(),
        	'original_filename'	=> $asset->getOriginal_filename(),
        	'width'				=> $asset->getWidth(),
        	'height'			=> $asset->getHeight(),
        	'created'			=> $asset->getCreated(),
        	'updated'			=> $asset->getUpdated()
        );
 
        if (null === ($id = $asset->getId())) {
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
        $asset = new Admin_Model_AllAssets();
        $asset->setId($row->id)
        	->setApp($row->app)
        	->setContent_type($row->content_type)
        	->setContent($row->content)
            ->setSize($row->size)
            ->setFilename($row->filename)
        	->setOriginal_filename($row->original_filename)
        	->setWidth($row->width)
        	->setHeight($row->height)
        	->setCreated($row->created)
        	->setUpdated($row->updated);
        return $asset;
    }
	
}
