<?php
/**
 * Controller
 *
 * @category   LoveMachine
 * @package    Login
 * @subpackage Controller
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link       http://www.lovemachineinc.com
 */
class Controller
{
    /**
     * Singleton instance
     *
     * @var Controller_Front
     */
    private static $_instance;

    /**
     * @var Controller_Request
     */
    protected $request;
    /**
     * @var Response
     */
    protected $response;

    /**
     * Constructor
     */
    protected function __construct()
    {
        // defaults
        $this->setRequest(new Request())
            ->setResponse(new Response());
    }

    /**
     * Singleton getter
     *
     * @return Controller_Front
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Set the request object
     *
     * @param Request $request The request
     *
     * @return Controller_Front
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Returns the current request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the response object
     *
     * @param Response $response The response
     *
     * @return Controller_Front
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Handle exceptions thrown during dispatching
     *
     * @param Exception $e Exception
     *
     * @return void
     */
    protected function handleException(Exception $e)
    {
        $msg  = 'An error occured while dispatching:' . "\n";
        $msg .= "\n" . ' ' . $e->getMessage();
        if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'development') {
            $msg .= "\n\nFile: " . $e->getFile();
            $msg .= "\nLine: "   . $e->getLine();
            $msg .= "\n" . $e->getTraceAsString();
        } else if (defined('APPLICATION_ENV') && APPLICATION_ENV == 'testing') {
            $msg .= "<br /><br />File: " . $e->getFile();
            $msg .= "<br />Line: "   . $e->getLine();
            $msg .= "<br />" . $e->getTraceAsString();
        }
        echo $msg;
        exit();
    }

    /**
     * Dispatch the current request
     *
     * @param Controller_Request $request An (optional) controller
     *                    request to dispatch
     *
     * @return void
     */
    public function dispatch()
    {
        require APPLICATION_DIR.'/config/routes.php';
        try {
            $request = new Request();
            $tmp = $request->getPathArray();
            $controller = null;
            if(isset($tmp[1]) && strlen(trim($tmp[1])) > 0){
                $controller = $tmp[1];
            }
            if(isset($controller)){
                if(array_key_exists($controller, $route)){
                    $c = new $route[$controller]($request, $this->response);
                    if($c->$controller()->isCallable()){
                        $c->$controller;
                    } else {
                        throw new Exception('Route was found but it is not callable.');
                    }
                } else {
                    throw new Exception('No matching route.');
                }
            } else {
                throw new Exception('Invalid call.');
            }
        } catch (Exception $e) {
            $this->handleException($e);
        }
        unset($route);
    }
}
