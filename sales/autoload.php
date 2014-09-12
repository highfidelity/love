<?php
//  Copyright (c) 2010, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

    // Autoloader
    function __autoload($class)
    {
        $file = realpath(dirname(__FILE__) . '/class') . "/$class.class.php";
        if (file_exists($file)) {
            require_once($file);
        }
    }