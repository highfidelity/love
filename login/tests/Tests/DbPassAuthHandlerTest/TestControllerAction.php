<?php

class TestControllerAction extends Controller_Action {
    public function __construct() {

    }
    public function getError() {
        return $this->error;
    }
}