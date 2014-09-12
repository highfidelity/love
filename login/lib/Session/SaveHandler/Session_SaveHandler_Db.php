<?php
/**
 * Session
 *
 * @category LoveMachine
 * @package  Core
 * @author   LoveMachine Inc. <all@lovemachineinc.com>
 * @license  Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version  SVN: $Id: Session_SaveHandler_Db.php 29 2010-05-24 21:30:35Z yani $
 * @link     http://www.lovemachineinc.com
 */
/**
 * Session save handler: database
 *
 * @category LoveMachine
 * @package  Core
 * @author   LoveMachine Inc. <all@lovemachineinc.com>
 * @license  Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link     http://www.lovemachineinc.com
 */
class Session_SaveHandler_Db implements Session_SaveHandler_Interface
{
    /**
     * @var mysqli
     */
    protected $dbAdapter;
    /**
     * @var string The session table name
     */
    protected $tableName;
    /**
     * @var string Field for session id
     */
    protected $fieldSessionId;
    /**
     * @var string Field for session data
     */
    protected $fieldSessionData;
    /**
     * @var string Field for session expiry
     */
    protected $fieldSessionExpires;

    /**
     * Constructor
     *
     * @param mysqli $dbAdapter           The db adapter
     * @param string $tableName           The table name to use
     * @param string $fieldSessionId      Field name for session id
     * @param string $fieldSessionData    Field name for session data
     * @param string $fieldSessionExpires Field name for session expiry
     */
    public function __construct(
        $fieldSessionId = 'session_id',
        $fieldSessionData = 'session_data',
        $fieldSessionExpires = 'session_expires'
    ) {
        global $dbConfig;
        $this->dbAdapter           = new mysqli ( $dbConfig ['host'], $dbConfig ['username'], $dbConfig ['password'], $dbConfig ['dbname'] );
        $this->tableName           = "ws_sessions";
        $this->fieldSessionId      = $fieldSessionId;
        $this->fieldSessionData    = $fieldSessionData;
        $this->fieldSessionExpires = $fieldSessionExpires;
    }

    /**
     * Open session
     *
     * @param string $save_path Session save path (ignored)
     * @param string $name      Session name (ignored)
     *
     * @return boolean
     */
    public function open($save_path, $name)
    {
        return true;
    }

    /**
     * Closes the session
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id Session id
     *
     * @return string Session data or empty string
     */
    public function read($id)
    {
        $query =  'SELECT `' . $this->fieldSessionData . '`';
        $query .= ' FROM `' . $this->tableName . '`';
        $query .= ' WHERE `' . $this->fieldSessionId. '`';
        $query .= ' = \'' . $this->dbAdapter->real_escape_string($id) . '\'';
        $res = $this->dbAdapter->query($query);
        if ($res->num_rows == 0) {
            return '';
        }
        return $res->fetch_object()->{$this->fieldSessionData};
    }

    /**
     * Whether an id exists
     *
     * @param string $id
     *
     * @return boolean
     */
    public function sessionIdExists($id)
    {
        $query =  'SELECT `' . $this->fieldSessionId . '`';
        $query .= ' FROM `' . $this->tableName . '`';
        $query .= ' WHERE `' . $this->fieldSessionId . '`';
        $query .= ' = \'' . $this->dbAdapter->real_escape_string($id) . '\'';
        $res = $this->dbAdapter->query($query);
        return (boolean) $res->num_rows;
    }

    /**
     * Write session data
     *
     * @param string $id   Session id
     * @param string $data Session data
     *
     * @return boolean
     */
    public function write($id, $data)
    {
        $newExpiry = time() . ini_get('session.gc_maxlifetime');
        if ($this->sessionIdExists($id)) {
            $query =  'UPDATE `' . $this->tableName . '`';
            $query .= ' SET `' . $this->fieldSessionData . '`';
            $query .= ' = \'' . $this->dbAdapter->real_escape_string($data) . '\',';
            $query .= ' `' . $this->fieldSessionExpires . '`';
            $query .= ' = ' . $newExpiry;
        } else {
            $query =  'INSERT INTO `' . $this->tableName . '`';
            $query .= ' (`' . $this->fieldSessionId . '`,';
            $query .= ' `' . $this->fieldSessionData . '`,';
            $query .= ' `' . $this->fieldSessionExpires . '`)';
            $query .= ' VALUES (';
            $query .= '\'' . $this->dbAdapter->real_escape_string($id) . '\',';
            $query .= '\'' . $this->dbAdapter->real_escape_string($data) . '\',';
            $query .= $newExpiry;
            $query .= ')';
        }
        return (boolean) $this->dbAdapter->query($query);
    }

    /**
     * Deletes data for given session id
     *
     * @param string $id session id
     *
     * @return boolean
     */
    public function destroy($id)
    {
        $query =  'DELETE FROM `' . $this->tableName . '`';
        $query .= ' WHERE `' . $this->fieldSessionId . '`';
        $query .= ' = \'' . $this->dbAdapter->real_escape_string($id) . '\'';
        return (boolean) $this->dbAdapter->query($query);
    }

    /**
     * Garbage collection
     *
     * @param int $maxlifetime Session lifetime in seconds
     *
     * @return boolean
     */
    public function gc($maxlifetime)
    {
        $query =  'DELETE FROM `' . $this->tableName . '`';
        $query .= ' WHERE `' . $this->fieldSessionExpires . '`';
        $query .= ' < ' . time();
        return (boolean) $this->dbAdapter->query($query);
    }
}