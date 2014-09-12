<?php
/**
 * User
 *
 * The user extends the DataObject. Here you can find an example on how to implement
 * data objects.
 * You can override the default set/get methods, to match your expected behaviour.
 *
 * Note, that there is no validation of input. It's a simple interface. Except for
 * the id, as it is required there. Please use strings for IDs to avoid integer
 * overflow issues (if ever).
 *
 * @category LoveMachine
 * @package  User
 * @author   LoveMachine Inc. <all@lovemachineinc.com>
 * @license  Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link     http://www.lovemachineinc.com
 */
class User extends DataObject
{
    /**
     * Table name
     */
    const TABLE_NAME = LOGIN_USER_TABLE;
    /**
     * User status: not confirmed
     */
    const USER_NOT_CONFIRMED = 0;
    /**
     * User status: confirmed
     */
    const USER_CONFIRMED     = 1;
    /**
     * User status: not active
     */
    const USER_NOT_ACTIVE	 = 0;
    /**
     * User status: active
     */
    const USER_ACTIVE		 = 1;
    
    /**
     * User removed: true
     */
    const USER_REMOVED = 1;
    
    /**
     * User removed: false
     */
    const USER_NOT_REMOVED = 0;
    
    const USER_ADMIN = 1;
    const USER_NOT_ADMIN = 0;

    /**
     * MySQLi db adapter
     *
     * @var mysqli
     */
    protected $dbAdapter;
    /**
     * Columns
     *
     * @var array User table columns
     */
    protected static $columns;
    /**
     * @var string User id
     */
    protected $id;
    /**
     * @var string username
     */
    protected $username;
    /**
     * @var string password
     */
    protected $password;
    /**
     * @var string nickname
     */
    protected $nickname;
    /**
     * @var int confirmed field
     */
    protected $confirmed;
    /**
     * @var int active field
     */
    protected $active;
    /**
     * @var string General purpose token
     */
    protected $token;
    /**
     * @var string Added
     */
    protected $dateAdded;
    /**
     * @var string Last modified
     */
    protected $dateModified;
    /**
     * @var int removed flag
     */
    protected $removed;
    /**
     * @var int admin flag
     */
    protected $admin;
    /**
     * @var string status flag
     */
    protected $status;
    
    /**
     * Constructor
     *
     * @param mysqli $dbAdapter The MySQLi db adapter
     * @param array  $config    Config array
     */
    public function __construct(mysqli $dbAdapter = null, array $config = array())
    {
        if ($dbAdapter != null) {
            $this->setDbAdapter($dbAdapter);
        }
        $this->registerIgnoreProps(array('dbAdapter', 'columns'));
        parent::__construct();
    }

    /**
     * Returns an array with all table columns
     *
     * It uses the static User::$columns.
     *
     * The columns can be used for SQL statements.
     *
     * @return array
     */
    public function getColumns()
    {
        if (self::$columns === null) {
            $props = $this->retrieveProperties();
            $cols = array();
            foreach ($props as $prop) {
                $cols[] = DataObject::decamelize($prop);
            }
            self::$columns = $cols;
        }
        return self::$columns;
    }

    /**
     * Sets the db adapter
     *
     * @param mysqli $dbAdapter MySQLi instance
     *
     * @return User
     */
    public function setDbAdapter(mysqli $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    /**
     * Returns the db adapter
     *
     * @param boolean $require Whether the db adapter is required (default: yes)
     *
     * @throws User_Exception
     * @return mysqli
     */
    public function getDbAdapter($require = true)
    {
        if ($require && $this->dbAdapter === null) {
            throw new User_Exception('Required db adapter not set.');
        }
        return $this->dbAdapter;
    }

    /**
     * Sets the id
     *
     * Using string to prevent integer overflow
     *
     * @param string $id Id
     *
     * @throws User_Exception
     * @return User
     */
    public function setId($id)
    {
        $id = (string) $id;
        // ANSI SQL-style hex literals (e.g. x'[\dA-F]+')
        // are not supported here, because these are string
        // literals, not numeric literals.
        $pattern = '/^(
                    (?:
                    0[Xx][\da-fA-F]+     # ODBC-style hexadecimal
                    |\d+                 # decimal or octal, or MySQL ZEROFILL decimal
                    (?:[eE][+-]?\d+)?    # optional exponent on decimals or octals
                    )
                    )/x';
        if (!preg_match($pattern, $id)) {
            throw new User_Exception('Invalid id: ' . var_export($id, true));
        }
        $this->id = $id;
        return $this;
    }

    /**
     * Sets the password and automatically encrypts it
     *
     * @param string  $password The cleartext password
     * @param boolean $encrypt  Whether to encrypt
     *
     * @return User
     */
    public function setPassword($password, $encrypt = true)
    {
        if ($encrypt) {
            $this->password = '{crypt}' . Functions::encryptPassword($password);
        } else {
            $this->password = $password;
        }
        return $this;
    }

    /**
     * Sets the confirmed value
     *
     * Must be either one of the constants:
     * User::USER_CONFIRMED
     * User::USER_NOT_CONFIRMED
     *
     * @param int $confirmed Confirmed value
     *
     * @throws User_Exception
     * @return User
     */
    public function setConfirmed($confirmed)
    {
        $confirmed = (int) $confirmed;
        if ($confirmed != self::USER_CONFIRMED && $confirmed != self::USER_NOT_CONFIRMED) {
            throw new User_Exception('Invalid value: ' . $confirmed);
        }
        $this->confirmed = $confirmed;
        return $this;
    }

    /**
     * Sets the active value
     *
     * Must be either one of the constants:
     * User::USER_ACTIVE
     * User::USER_NOT_ACTIVE
     *
     * @param int $active Active value
     *
     * @throws User_Exception
     * @return User
     */
    public function setActive($active)
    {
        $active = (int)$active;
        if ($active != self::USER_ACTIVE && $active != self::USER_NOT_ACTIVE) {
            throw new User_Exception('Invalid value: ' . $active);
        }
        $this->active = $active;
        return $this;
    }
    
    /**
     * Sets an user removed flag
     * 
     * Must be either one of the constants:
     * User::USER_REMOVED
     * User::USER_NOT_REMOVED
     * 
     * @param int $removed
     * 
     * @throws User_Exception
     * @return User
     */
    public function setRemoved($removed) {
        $removed = (int)$removed;
        if ($removed != self::USER_REMOVED && $removed != self::USER_NOT_REMOVED) {
            throw new User_Exception('Invalid value: ' . $removed);
        }
        $this->removed = $removed;
        return $this;
    }
     
    /**
     * Authenticates against given password
     *
     * @param string $password Cleartext password
     *
     * @throws User_Exception
     * @return boolean
     */
    public function authenticate($password)
    {
        if (!$this->hasPassword()) {
            throw new User_Exception('The user has no password set.');
        }
        if (substr($this->getPassword(), 0, 7) == '{crypt}') {
            $encrypted = substr($this->getPassword(), 7);
            return ($encrypted == crypt($password, $encrypted));
        } else {
            return (sha1($password) == $this->getPassword());
        }
    }
    
    /**
     * Returns true if the user is active
     * 
     * @return boolean
     */
    public function isActive()
    {
    	if ($this->active == self::USER_ACTIVE) {
    		return true;
    	}
    	return false;
    }
    
    public function isNotRemoved()
    {
        if($this->removed == self::USER_REMOVED){
            return false;
        } else {
            return true;
        }
    }
    public function isAdmin()
    {
        if($this->admin == self::USER_ADMIN){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Loads user by id
     *
     * @param string $id User id
     *
     * @throws User_Exception
     * @return boolean Successful or not
     */
    public function loadById($id)
    {
        $dbAdapter = $this->getDbAdapter();
        $query =  'SELECT ';
        $query .= implode(', ', $this->getColumns());
        $query .= ' FROM ' . self::TABLE_NAME;
        $query .= ' WHERE `id` = ' . $dbAdapter->escape_string($id);

        /**
         * @var mysqli_result $res
         */
        if (! $res = $dbAdapter->query($query)) {
            error_log("loadById: ".$dbAdapter->error);
            error_log("loadById - Query: ".$query);
            return false;
        }

        if ($res->num_rows == 0) {
            return false;
        }
        if ($res->num_rows != 1) {
            throw new User_Exception('Invalid result count.');
        }
        $ret = $this->load($res->fetch_object());
        $res->free_result();
        return $ret;
    }

    /**
     * Loads user by username
     *
     * @param string $username Username
     *
     * @throws User_Exception
     * @return boolean Whether loading was successful
     */
    public function loadByUsername($username)
    {
        $dbAdapter = $this->getDbAdapter();
        $query =  'SELECT ';
        $query .= implode(', ', $this->getColumns());
        $query .= ' FROM ' . self::TABLE_NAME;
        $query .= ' WHERE `username` = "' . $dbAdapter->escape_string($username) . '"';
        $res = $dbAdapter->query($query) or error_log("Login.loadByUsername: {$query}\n".$dbAdapter->error);
        if ($res->num_rows == 0) {
            return false;
        }
        if ($res->num_rows != 1) {
            throw new User_Exception('Invalid result count.');
        }
        $ret = $this->load($res->fetch_object());
        $res->free_result();
        return $ret;
    }

	public function getUserList()
	{
		$dbAdapter = $this->getDbAdapter();
		$query = 'SELECT * FROM `' . self::TABLE_NAME . '`;';
		$res = $dbAdapter->query($query);
		while ($row = $res->fetch_array()) {
			$users[] = array(
				'id' => $row['id'],
                'username' => $row['username'],
                'nickname' => $row['nickname'],
                'confirmed' => $row['confirmed'],
                'active' => $row['active'],
                'date_added'  => $row['date_added'],
                'date_modified' => $row['date_modified'],
                'admin' => $row['admin'],
                'removed' => $row['removed']
			);
		}
		return $users;
	}
    
    /**
     * Loads user by nickname
     *
     * @param string $nickname Nickname
     *
     * @throws User_Exception
     * @return boolean Whether loading was successful
     */
    public function loadByNickname($nickname)
    {
        $dbAdapter = $this->getDbAdapter();
        $query =  'SELECT ';
        $query .= implode(', ', $this->getColumns());
        $query .= ' FROM ' . self::TABLE_NAME;
        $query .= ' WHERE `nickname` = "' . $dbAdapter->escape_string($nickname) . '"';
        $res = $dbAdapter->query($query);
        if ($res->num_rows == 0) {
            return false;
        }
        if ($res->num_rows != 1) {
            throw new User_Exception('Invalid result count.');
        }
        $ret = $this->load($res->fetch_object());
        $res->free_result();
        return $ret;
    }
    
    public function insertUsers($users) {
        $dbAdapter = $this->getDbAdapter();
        
         // Create SQL statements
        $sql = "INSERT INTO ". self::TABLE_NAME ." ";
        $columns = "(";
        $values = "VALUES(";
        
        // Take the first user
        foreach ($users[0] as $key => $val) {
            $columns .= "`".$key."`,";
        }
        
        // For each user
        foreach ($users as $user) {
            // As key => val
            foreach ($user as $key => $val) {
                // If the key is "date_added" set the value to the current date
                // else just add the value.
                if($key == 'date_added'){
                    $values .= "'NOW()',";
                    continue;
                } else if ($key == 'password') {
                    $values .= "'" . $dbAdapter->escape_string('{crypt}' . Functions::encryptPassword($val)) . "',";
                    continue;
                } else {
                    // Add the values to our query
                    $values .= "'".$val."',";
                }
            }
            $values = substr_replace($values, "", -1);
            $values .= "), (";
        }

        $columns = substr_replace($columns, "", -1);
        $columns .= ") ";
        $values = substr_replace($values, "", -3);
        
        // Merge SQL statements
        $sql = $sql.$columns.$values;
        
        $stmt = $this->dbAdapter->prepare($sql) or error_log("prepare: ".mysqli_error($this->dbAdapter)."\n".$sql);
        if (!$stmt->execute()) {
	    error_log("execute: ".mysqli_error($this->dbAdapter));
            throw new User_DbException($stmt->error, $stmt->errno);
        }
        
        $stmt = null;
        
        // Get the new user ids
        $ids = array();
        
        foreach ($users as $user) {
            // Localy store the username
            $username = $user['username'];
            
            // Create query to fetch the 'id' for the given user
            $sql_id = "SELECT `id`, `username`, `nickname`, `confirmed`, `Active`, `status` FROM ". self::TABLE_NAME ." WHERE `username`='{$username}'";
            // Prepare the query or log a error result
            $stmt = $this->dbAdapter->prepare($sql_id) or error_log("prepare: ".mysqli_error($this->dbAdapter));
            
            // Execute our query or throw exception
            if (!$stmt->execute()) {
                throw new User_DbException($stmt->error, $stmt->errno);
            }
            
            // Bind the query column to our $id variable
            $stmt->bind_result($id, $username, $nickname, $confirmed, $active, $status);
            
            // For each row fetch the id and username, and compose a secondary array containing
            // this data.
            while ($stmt->fetch()) {
                $user_data = array("username" => $username,
                                   "nickname" => $nickname,
                                   "confirmed" => $confirmed,
                                   "Active" => $active,
                                   "status" => "new"
                                   );

                $ids[] = array("uid" => $id, "user_data" => $user_data);
            }
        }
        
        // Return our ids array to the controller
        return $ids;
    }

    /**
     * Loads data into instance
     *
     * @param mixed $data Either an instance of stdClass or an associative array
     *
     * @throws Exception
     * @throws User_Exception
     * @return boolean
     */
    protected function load($data)
    {
        if (is_object($data) && $data instanceof stdClass) {
            foreach ($this->getColumns() as $col) {
                if ($col == 'password') {
                    // directly set password value
                    $this->password = $data->$col;
                    continue;
                }
                $method = 'set' . DataObject::camelize($col);
                $this->$method($data->$col);
            }
            return true;
        } else {
            throw new User_Exception('Invalid parameter.');
        }
    }
    /**
     * Saves the user
     *
     * @return string User id
     */
    public function save()
    {
        $dbAdapter = $this->getDbAdapter();
        $cols      = $this->getColumns();
        if($this->hasId()){
            $query =  'UPDATE ' . self::TABLE_NAME;
            $query .= ' SET ';
            $updates = array();
            foreach ($cols as $col) {
                if ($col == 'id') {
                    continue;
                }
                if($col == 'date_modified'){
                    $values[] = 'NOW()';
                    continue;
                }
                if ($this->$col === null) {
                    $updates[] = $col . ' = NULL';
                } else {
                    $updates[] = $col . ' = \'' . $dbAdapter->real_escape_string($this->$col) . '\'';
                }
            }
            $query .= implode(', ', $updates);
            $query .= ' WHERE id = ' . $dbAdapter->real_escape_string($this->getId());
            $stmt = $this->dbAdapter->prepare($query);
            if (!$stmt->execute()) {
                throw new User_DbException($stmt->error, $stmt->errno);
            }
            if ($stmt->affected_rows != 1) {
                throw new User_DbException('Unexpected number of affected rows: ' . $stmt->affected_rows);
            }
            return $this->getId();
        } else {
            $query =  'INSERT INTO ' . self::TABLE_NAME;
            $query .= ' (' . implode(', ', $cols) . ')';
            $query .= ' VALUES (';
            $values = array();
            foreach ($cols as $col) {
                if ($col == 'id' || $this->$col === null) {
                    $values[] = 'NULL';
                    continue;
                }
                if($col == 'date_added'){
                    $values[] = 'NOW()';
                    continue;
                }
                $values[] = '\'' . $dbAdapter->real_escape_string($this->$col) . '\'';
            }
            $query .= implode(', ', $values) . ')';
            $stmt = $this->dbAdapter->prepare($query) or error_log("prepare: $sql ".mysqli_error());
            if (!$stmt->execute()) {
                throw new User_DbException($stmt->error, $stmt->errno);
            }
            $id = (string) $stmt->insert_id;
            $this->setId($id);
            return $id;
        }
    }
}
