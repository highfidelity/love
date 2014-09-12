<?php
$app_root_path = dirname(__FILE__);
$app_root_path = str_replace('Tests','',$app_root_path);

require_once ($app_root_path . 'bootstrap.php');

class AppAuthTest extends PHPUnit_Framework_TestCase {
    protected $appAuth;
    protected $regApps;
    
    public function setUp() {
        try {
            $this->appAuth = new AppAuth();
            $this->regApps['lovemachine'] = array('endpoint' => 'https://dev.sendlove.us/love/api.php', 'key' => '12345');
        
        } catch ( Exception $e ) {
            throw $e;
        }
    }
    public function testValid() {
        $failureAffects = "Login is not able to validate apps";
        
        $this->assertEquals(false,$this->appAuth->valid($this->regApps),$failureAffects);
        
        // setting an invalid app name
        $_REQUEST["app"] = "InvalidAppName";
        $this->assertEquals(false,$this->appAuth->valid($this->regApps),$failureAffects);
        
        // setting a valid app name
        $_REQUEST["app"] = "lovemachine";
        $_REQUEST["key"] = "12345";
        $this->assertEquals(true,$this->appAuth->valid($this->regApps),$failureAffects);
        
        // if the app is found the app name and the app key are set
        $this->assertEquals("lovemachine",$this->appAuth->getAppName(),$failureAffects);
        $this->assertEquals("12345",$this->appAuth->getAppKey(),$failureAffects);
    }
    
    public function testSetGetAppName(){
        $failureAffects = "Login is not able to validate apps";
        
        $appName = "testAppName";
        
        $this->assertEquals(false, ($this->appAuth->getAppName() == $appName),$failureAffects);
        $this->appAuth->setAppName($appName);
        $this->assertEquals($appName, $this->appAuth->getAppName(),$failureAffects);
    }
    
    public function testSetGetAppKey(){
        $failureAffects = "Login is not able to validate apps";
        
        $appKey = "MyTestKey";
        
        $this->assertEquals(false, ($this->appAuth->getAppKey() == $appKey),$failureAffects);
        $this->appAuth->setAppKey("MyTestKey");
        $this->assertEquals($appKey, $this->appAuth->getAppKey(),$failureAffects);
    }
    
    public function testSignApp(){
        $failureAffects = "Login is not able to validate apps";
        
        $_REQUEST["key"] = "blablabla";
        $this->assertEquals(false, $this->appAuth->signed(),$failureAffects);
        
        $_REQUEST["key"] = "MyTestKey";
        $this->assertEquals(false, $this->appAuth->signed(),$failureAffects);
        
        $this->appAuth->setAppKey("MyTestKey");
        $this->assertEquals(true, $this->appAuth->signed(),$failureAffects);
    }
}
