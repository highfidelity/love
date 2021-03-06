<?php
/**
 * Session
 *
 * @category LoveMachine
 * @package  Core
 * @author   LoveMachine Inc. <all@lovemachineinc.com>
 * @license  Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version  SVN: $Id: Session_Exception.php 20 2010-05-08 15:33:11Z seong $
 * @link     http://www.lovemachineinc.com
 */
/**
 * Session exception
 *
 * @category LoveMachine
 * @package  Core
 * @author   LoveMachine Inc. <all@lovemachineinc.com>
 * @license  Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link     http://www.lovemachineinc.com
 */
class Session_Exception extends Exception
{
    /**
     * Session error info
     *
     * Used by the session error handler.
     *
     * @var string
     */
    public static $sessionError;

    /**
     * Interface for set_error_handler()
     *
     * @param int    $errno      No
     * @param string $errstr     Message
     * @param string $errfile    File
     * @param int    $errline    Line
     * @param array  $errcontext Context
     *
     * @see set_error_handler()
     * @return void
     */
    static public function handleSessionError($errno, $errstr, $errfile, $errline, $errcontext)
    {
        self::$sessionError = $errfile . '(Line:' . $errline . '): Error #' . $errno . ' ' . $errstr . ' ' . $errcontext;
    }

}
