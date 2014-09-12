<?php
/**
 * Controller action
 *
 * @category   LoveMachine
 * @package    Login
 * @subpackage Controller
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link       http://www.lovemachineinc.com
 */
abstract class Controller_Action {
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Response
     */
    protected $response;
    /**
     * Error flag
     * @var bool
     */
    protected $error;
    /**
     * Error message
     * @var array
     */
    protected $errorMessage;
    
    /**
     * 
     * @var AppAuth
     */
    protected $AppAuth;
    
    /**
     * 
     * @var authHandler
     */
    protected $authHandler;
    /**
     * Constructor
     *
     * @param Request  $request  The request
     * @param Response $response The response
     */
    public function __construct(Request $request, Response $response){
        $this->setRequest($request);
        $this->setResponse($response);
        $this->AppAuth = new AppAuth($this);
        if((bool)LDAP_ENABLED === false) {
           $this->authHandler = new Db_Password_Authentication_Handler();
        }
        else {
           $this->authHandler = new Ldap_Authentication_Handler();
        }
    }
    
    public function setError($msg){
        $this->setErrorFlag(true);
        $this->setErrorMessage((empty($msg))?'':$msg);
        return $this;
    }
    public function setErrorMessage($msg){
        if(is_array($msg)){
            foreach($msg as $m){
                $this->errorMessage[] = $m;
            }
        }else{
            $this->errorMessage[] = $msg;
        }
        return $this;
    }
    public function setErrorFlag($f){
        $this->error = $f;
        return $this;
    }
    public function getErrorFlag(){
        return $this->error;
    }
    public function getErrorMessage(){
        return $this->errorMessage;
    }
    /**
     * Automatically called before calling the action method
     *
     * @return void
     */
    public function setUp(){
    }
    
    /**
     * Automatically called after the action method
     *
     * @return void
     */
    public function tearDown(){
    }
    
    /**
     * Sets the request
     *
     * @param Request $request The request
     *
     * @return Controller_Action
     */
    public function setRequest(Request $request){
        $this->request = $request;
        return $this;
    }
    
    /**
     * Returns the request
     *
     * @return Request
     */
    public function getRequest(){
        return $this->request;
    }
    
    /**
     * Sets the response
     *
     * @param Response $response The response
     *
     * @return Controller_Action
     */
    public function setResponse(Response $response){
        $this->response = $response;
        return $this;
    }
    
    /**
     * Returns the response
     * 
     * @return Response
     */
    public function getResponse(){
        return $this->response;
    }
}
