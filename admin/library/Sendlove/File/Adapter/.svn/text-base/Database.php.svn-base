<?php

class Sendlove_File_Adapter_Database extends Zend_File_Transfer_Adapter_Http
{

	protected $_dbTable;
	protected $_app;
	
    /**
     * Constructor for Database File Transfers
     *
     * @param array $options OPTIONAL Options to set
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
    }
    
    public function setTable(Zend_Db_Table_Abstract $table)
    {
    	$this->_dbTable = $table;
    	return $this;
    }
    
    public function getTable()
    {
    	return $this->_dbTable;
    }
    
    public function setApp($app = 'admin')
    {
    	$this->_app = (string)$app;
    	return $this;
    }
    
    public function getApp()
    {
    	if (null === $this->_app) {
    		$this->setApp('admin');
    	}
    	return $this->_app;
    }
	
    /**
     * Receive the file from the client (Upload)
     *
     * @param  string|array $files (Optional) Files to receive
     * @return bool
     */
	public function receive($files = null)
	{
	    if (!$this->isValid($files)) {
            return false;
        }
        
        $check = $this->_getFiles($files);
        foreach ($check as $file => $content) {
        	if (!$content['received']) {
        		$table = $this->getTable();
        		if ($table === null) {
        			throw new Exception('No table to save set.');
        		}
        		
        		$filename = $content['name'];
        		$rename   = $this->getFilter('Rename');
        		if ($rename !== null) {
                    $tmp = $rename->getNewName($content['tmp_name']);
                    if ($tmp != $content['tmp_name']) {
                        $filename = $tmp;
                    }

                    $key = array_search(get_class($rename), $this->_files[$file]['filters']);
                    unset($this->_files[$file]['filters'][$key]);
                }
                
                $filecontent = file_get_contents($content['tmp_name']);
                $filesize = filesize($content['tmp_name']);
                $filemime = Sendlove_Utilities::getMimeType($content['name']);
                
                $filewidth = $fileheight = 0;
                if (strpos($filemime, 'image') !== false) {
                	list($filewidth, $fileheight) = getimagesize($content['tmp_name']);
                }
				
				$options = array(
                	'app'				=> $this->getApp(),
                	'content'			=> $filecontent,
                	'content_type'		=> $filemime,
					'filename'			=> Sendlove_Utilities::createUniqueFilename($filename),
                	'original_filename'	=> $filename,
                	'width'				=> $filewidth,
                	'height'			=> $fileheight,
                	'size'				=> $filesize,
                	'created'			=> new Zend_Db_Expr('NOW()'),
					'updated'			=> new Zend_Db_Expr('NOW()')
                );

				if (isset($content['id'])) {
					$options['id'] = $content['id'];
				}

                if (!($fileid = $this->writeToDatabase($options))) {
	                if ($content['options']['ignoreNoFile']) {
						$this->_files[$file]['received'] = true;
						$this->_files[$file]['filtered'] = true;
						continue;
					}
					
					$this->_files[$file]['received'] = false;
					return false;
                }
                
                $this->_files[$file]['id'] = (isset($content['id']) ? $content['id'] : $fileid);
        	}

            if (!$content['filtered']) {
                if (!$this->_filter($file)) {
                    $this->_files[$file]['filtered'] = false;
                    return false;
                }

                $this->_files[$file]['filtered'] = true;
            }
        }
        
        return true;
	}

	public function writeToDatabase(array $options = array())
	{
		if (empty($options)) {
			throw new Exception('Options can not be empty!');
		}

		if (isset($options['id'])) {
			$where = $this->getTable()->getAdapter()->quoteInto('id = ?', $options['id']);
			unset($options['id']);
			return $this->getTable()->update($options, $where);
		} else {
			return $this->getTable()->insert($options);
		}

		return false;
	}
	
    /**
     * Returns the files id in the database
     *
     * @param  string|array $files (Optional) Files to check
     * @return bool|int
     */
    public function getId($files = null)
    {
        $files = $this->_getFiles($files, false, true);
        if (empty($files)) {
            return false;
        }

        foreach ($files as $content) {
            if (!empty($content['id'])) {
                return (int)$content['id'];
            }
        }

        return false;
    }

    /**
     * Sets the files id so it will be updated instead of inserted
     *
     * @param  string|array $file (Optional) File to check
     * @return bool|int
     */
	public function setId($id, $file = null)
	{
		$files = $this->_getFiles($file, false, true);
		if (empty($files)) {
			return false;
		}

		foreach ($files as $key => $value) {
			$this->_files[$key]['id'] = $id;
		}

		return true;
	}

}
