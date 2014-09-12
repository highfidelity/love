<?php

class Admin_Model_AllAssets
{

	protected $_id;
	protected $_app;
	protected $_content_type;
	protected $_content;
	protected $_size;
	protected $_filename;
	protected $_original_filename;
	protected $_width;
	protected $_height;
	protected $_created;
	protected $_updated;
	
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
	
	public function __set($name, $value)
	{
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid all assets property');
        }
        $this->$method($value);
	}
	public function __get($name)
	{
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid all assets property');
        }
        return $this->$method();
	}
	
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setApp($app)
	{
		$this->_app = $app;
		return $this;
	}

	public function getApp()
	{
		return $this->_app;
	}

	public function setContent_type($content_type)
	{
		$this->_content_type = $content_type;
		return $this;
	}

	public function getContent_type()
	{
		return $this->_content_type;
	}

	public function setContent($content)
	{
		$this->_content = $content;
		return $this;
	}

	public function getContent()
	{
		return $this->_content;
	}

	public function setSize($size)
	{
		$this->_size = $size;
		return $this;
	}

	public function getSize()
	{
		return $this->_size;
	}

	public function setFilename($filename)
	{
		$this->_filename = $filename;
		return $this;
	}

	public function getFilename()
	{
		return $this->_filename;
	}

	public function setOriginal_filename($original_filename)
	{
		$this->_original_filename = $original_filename;
		return $this;
	}

	public function getOriginal_filename()
	{
		return $this->_original_filename;
	}

	public function setWidth($width)
	{
		$this->_width = $width;
		return $this;
	}

	public function getWidth()
	{
		return $this->_width;
	}

	public function setHeight($height)
	{
		$this->_height = $height;
		return $this;
	}

	public function getHeight()
	{
		return $this->_height;
	}

	public function setCreated($created)
	{
		$this->_created = $created;
		return $this;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function setUpdated($updated)
	{
		$this->_updated = $updated;
		return $this;
	}

	public function getUpdated()
	{
		return $this->_updated;
	}

}
