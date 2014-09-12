<?php
$app_root_path = dirname(__FILE__);
$app_root_path = str_replace('Tests','',$app_root_path);

require_once ($app_root_path . 'bootstrap.php');

class CurlHandlerTest extends PHPUnit_Framework_TestCase {
    public function testGet() {
        $failureAffects = "Affects all get requests sent from Login.";
        ob_start();
        CURLHandler::Get("https://www.google.com/accounts/ClientLogin");
        $result = ob_get_contents();
        ob_end_clean();
        $this->assertContains('Error=BadAuthentication',$result,$failureAffects);
    }
    
    public function testPost() {
        $failureAffects = "Affects all post requests sent from Login. Login works but is unable to ".
                          "return any reply to the calling apps as well as to notify other apps(new user, user update, user delete)";
        ob_start();
        CURLHandler::Post('http://www.google.com',array('q' => 'belfabriek'));
        $result = ob_get_contents();
        ob_end_clean();
        $this->assertContains('The request method <code>POST</code>',$result,$failureAffects);
    }
}