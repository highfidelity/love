<?php

class Admin_Model_User
{
	protected $_id;
	protected $_username;
	protected $_confirmed;
	protected $_active;
	protected $_date_added;
	protected $_date_modified;
	protected $_nickname;
	protected $_admin;
	protected $_removed;
	protected $_auditor;
	protected $_picture;
	protected $_team;
	protected $_skill;
	protected $_giver;
	protected $_receiver;
		
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
            throw new Exception('Invalid user property');
        }
        $this->$method($value);
	}
	public function __get($name)
	{
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property');
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
    		'id' => $this->getId(),
    		'username' => $this->getUsername(),
    		'confirmed' => $this->getConfirmed(),
    		'active' => $this->getActive(),
    		'date_added' => $this->getDate_added(),
    		'date_modified' => $this->getDate_modified(),
    		'nickname' => $this->getNickname(),
    		'admin' => $this->getAdmin(),
    		'removed' => $this->getRemoved(),
    		'auditor' => $this->getAuditor(),
    		'picture' => $this->getPicture(),
    		'team' => $this->getTeam(),
    		'skill' => $this->getSkill(),
    		'is_giver' => $this->getGiver(),
    		'is_receiver' => $this->getReceiver()
    	);
    }
    
	public function getId()
	{
		return $this->_id;
	}

	public function setId($_id)
	{
		$this->_id = (int)$_id;
		return $this;
	}
    
	public function getUsername()
	{
		return $this->_username;
	}

	public function setUsername($_username)
	{
		$this->_username = (string)$_username;
		return $this;
	}

	public function getConfirmed()
	{
		return $this->_confirmed;
	}

	public function setConfirmed($_confirmed)
	{
		$this->_confirmed = (int)$_confirmed;
	}

	public function getActive()
	{
		return $this->_active;
	}

	public function setActive($_active)
	{
		$this->_active = (int)$_active;
		return $this;
	}

	public function getDate_added()
	{
		return $this->_date_added;
	}

	public function setDate_added($_date_added)
	{
		$this->_date_added = $_date_added;
		return $this;
	}

	public function getDate_modified()
	{
		return $this->_date_modified;
	}

	public function setDate_modified($_date_modified)
	{
		$this->_date_modified = $_date_modified;
		return $this;
	}

	public function getNickname()
	{
		return $this->_nickname;
	}

	public function setNickname($_nickname)
	{
		$this->_nickname = (string)$_nickname;
		return $this;
	}

	public function getAdmin()
	{
		return $this->_admin;
	}

	public function setAdmin($_admin)
	{
		$this->_admin = (int)$_admin;
		return $this;
	}

	public function getRemoved()
	{
		return $this->_removed;
	}

	public function setRemoved($_removed)
	{
		$this->_removed = (int)$_removed;
		return $this;
	}

	public function getAuditor()
	{
		return $this->_auditor;
	}

	public function setAuditor($_auditor)
	{
		$this->_auditor = (int)$_auditor;
		return $this;
	}
	
	public function getPicture()
	{
		return $this->_picture;
	}
	
	public function setPicture($picture)
	{
		$this->_picture = (string)$picture;
		return $this;
	}
	
	public function getTeam()
	{
		return $this->_team;
	}
	
	public function setTeam($team)
	{
		$this->_team = (string)$team;
		return $this;
	}
	
	public function getSkill()
	{
		return $this->_skill;
	}
	
	public function setSkill($skill)
	{
		$this->_skill = (string)$skill;
		return $this;
	}
	public function getGiver() {
		return $this->_giver;
	}

	public function setGiver($_isgiver) {
		$this->_giver = (int)$_isgiver;
		return $this;
	}

	public function getReceiver() {
		return $this->_receiver;
	}

	public function setReceiver($_isreceiver) {
		$this->_receiver = (int)$_isreceiver;
		return $this;
	}


}