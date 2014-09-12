<?php

class Admin_Model_Company
{
	
	protected $_id;
	protected $_company_name;
	protected $_selected_theme;
	
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
            throw new Exception('Invalid company property');
        }
        $this->$method($value);
	}
	public function __get($name)
	{
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid company property');
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
    
    public function toArray()
    {
    	return array(
    		'id'				=> $this->getId(),
    		'company_name'		=> $this->getCompany_name(),
			'selected_theme'	=> $this->getSelected_theme()
    	);
    }
	
	public function setId($id)
	{
		$this->_id = (int)$id;
		return $this;
	}
	public function getId()
	{
		return $this->_id;
	}
	
	public function setCompany_name($name)
	{
		$this->_company_name = (string)$name;
		return $this;
	}
	public function getCompany_name()
	{
		return $this->_company_name;
	}
	
	public function setSelected_theme($theme)
	{
		$this->_selected_theme = (string)$theme;
		return $this;
	}
	public function getSelected_theme()
	{
		return $this->_selected_theme;
	}
}

