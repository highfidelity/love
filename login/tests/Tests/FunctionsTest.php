<?php
$app_root_path = dirname(__FILE__);
$app_root_path = str_replace('Tests','',$app_root_path);

require_once ($app_root_path . 'bootstrap.php');

class FunctionsTest extends PHPUnit_Framework_TestCase {
    public function testRandomString() {
        $failureAffects = "Password generation and password authentication are failing.";
        for($i = 5; $i < 20; $i ++) {
            $str1 = Functions::randomString($i);
            $str2 = Functions::randomString($i);
            $this->assertFalse($str1 == $str2,$failureAffects);
        }
    }
}