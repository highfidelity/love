<?php
/**
 * Application Authorization class
 * 
 * Provides handy methods to authorize that
 * a request is made from known, registered application
 *
 * @category   LoveMachine
 * @package    Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link       http://www.lovemachineinc.com
 */
class AppAuth {
    
    /**
     * @var Controller_Action
     */
    protected $controller;
    protected $appKey;
    protected $appName;
    
    public function __construct(Controller_Action $c = null) {
        if (! is_null($c)) {
            $this->setController($c);
        }
    }
    
    public function setController($c) {
        $this->controller = $c;
    }
    
    public function valid($regApps = null) {
        if (is_null($regApps)) {
            global $regApps;
        }
        if (isset($_REQUEST["app"])) {
            $appFound = false;
            foreach ( $regApps as $app => $data ) {
                if ($app == $_REQUEST["app"]) {
                    $appFound = true;
                    break;
                }
            }
            if ($appFound) {
                $this->setAppName($_REQUEST["app"])->setAppKey($regApps[$_REQUEST["app"]]["key"]);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function setAppName($name) {
        $this->appName = $name;
        return $this;
    }
    public function setAppKey($key) {
        $this->appKey = $key;
        return $this;
    }
    public function getAppKey() {
        return $this->appKey;
    }
    public function getAppName() {
        return $this->appName;
    }
    public function signed() {
        if (isset($_REQUEST["key"])) {
            if ($_REQUEST["key"] == $this->getAppKey()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
