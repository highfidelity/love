<?php

class Application
{

	protected $_fileExt;
	protected $_fileMode;
	protected $_fileName;
	protected $_folderPath;
	protected $_routes;
	protected $_bootstrap;
	protected $_config;
	protected $_baseUrl;
	
	public function __construct($config = null)
	{
		$this->setConfig($config);
		$this->bootstrap();
		$this->setRequest();
	}
	
	public function addRoute($pattern, $class, $function)
	{
		$this->_routes[] = array($pattern, ucfirst($class), $function);
	}
	
	public function run()
	{
		foreach ($this->_routes as $rule => $conf) {
			if (preg_match($conf[0], Utilities::getMimeType($this->_fileExt), $matches)) {
				require_once CONTRIB_PATH . '/controller/' . $conf[1] . '.php';
				$class = new $conf[1]();
				$method = $this->_fileMode;
				$class->$method($this->_fileName, array('folder' => $this->_folderPath));
			}
		}
	}
	
	protected function bootstrap()
	{
		$bootstrap = new Bootstrap();
		$bootstrap->application = $this;
		
		$bsFunction = get_class_methods($bootstrap);
		foreach ($bsFunction as $function) {
			$bootstrap->$function();
		}
	}
	
	protected function setRequest()
	{
		$array = preg_split('/\/index\.php/', $_SERVER['PHP_SELF']);
		$this->_baseUrl = $array[0];
		
		if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
			$file = explode('?', $_SERVER['REQUEST_URI']);
			$file = basename($file[0]);
		} else {
			$file = basename($_SERVER['REQUEST_URI']);
		}
		
		$array = explode('.', $file);
		
		preg_match('/' . str_replace('/', '\/', $this->_baseUrl) . '(.*)\/' . str_replace('.', '\.', $file) . '/i', $_SERVER['REQUEST_URI'], $matches);
		
		$this->_fileExt = array_pop($array);
		$this->_fileMode = array_pop($array);
		$this->_fileName = implode('.', $array);
		$this->_folderPath = $matches[1];
		
		return $this;
	}
	
	protected function getRequestFile()
	{
		return $this->_requestFile;
	}
	
	protected function setConfig($config)
	{
		if (null !== $config) {
			require 'Zend/Config.php';
			if (file_exists($config)) {
				$filename = basename($config);
				if ('INI' == strtoupper($this->_fileExt)) {
					$this->_config = new Zend_Config_Ini($config);
				} else if ('XML' == strtoupper($ext)) {
					$this->_config = new Zend_Config_Xml($config);
				} else {
					$this->_config = new Zend_Config(require $config);
				}
			}
		
			if (is_array($config)) {
				$this->_config = new Zend_Config($config);
			}
		}
	}

}
