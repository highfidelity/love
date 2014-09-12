<?php

class Zend_View_Helper_AdminHeadScript extends Zend_View_Helper_HeadScript
{
	
	protected $_state = 'login';
	protected $_path;
	protected $_files;
	protected $_env;
	
	public function __construct()
	{
		parent::__construct();
		$this->_path = realpath(APPLICATION_PATH . '/../public/js');
		$this->_env = APPLICATION_ENV;
		$dn = new Zend_Session_Namespace();
		if ($dn->logged_in) {
			$this->_state = 'admin';
		}
	}
	
	public function adminHeadScript($mode = Zend_View_Helper_HeadScript::FILE, $spec = null, $placement = 'APPEND', array $attrs = array(), $type = 'text/javascript')
	{
		return parent::headScript($mode, $spec, $placement, $attrs, $type);
	}
	
	public function toString($indent = null)
	{
		$this->_files = array();
		
		// turn off errors for this code block, we are mucking with the loop structing in real time
		$errdis = ini_get('display_errors');
		$errlog = ini_get('error_reporting');
		ini_set('display_errors', 0);
		ini_set('error_reporting', 0);
		foreach ($this as $key => $item) {
			if (!empty($this[$key]->attributes['src'])) {
				$this->_files[] = array(
					'modified' => $this->getFiletime($this[$key]->attributes['src']),
					'fullpath' => (Zend_Uri::check($this[$key]->attributes['src']) ? $this[$key]->attributes['src'] : $this->_path . '/' . basename($this[$key]->attributes['src'])),
					'filename' => basename($this[$key]->attributes['src'])
				);
				$this->offsetUnset($key);
			}
		}
		// turn errors back on
		ini_set('display_errors', $errdis);
		ini_set('error_reporting', $errlog);
		
		if ((!defined('USE_COMPILED_LIBRARIES') || USE_COMPILED_LIBRARIES !== 'true')) {
			if (!file_exists($this->_path . '/' . $this->_state . '.combined.js') || $this->_checkModifiedDate($this->_path . '/' . $this->_state . '.combined.js')) {
				$this->_createCombined();
			}
			$this->appendScript(file_get_contents($this->_path . '/' . $this->_state . '.combined.js'));
		} else {
			if (!file_exists($this->_path . '/' . $this->_state . '.compiled.js') || $this->_checkModifiedDate($this->_path . '/' . $this->_state . '.compiled.js')) {
				if (!file_exists($this->_path . '/' . $this->_state . '.combined.js') || $this->_checkModifiedDate($this->_path . '/' . $this->_state . '.combined.js')) {
					$this->_createCombined();
				}
				$this->_createCompiled();
			}
			if (file_exists($this->_path . '/' . $this->_state . '.compiled.js')) {
				$this->appendScript(file_get_contents($this->_path . '/' . $this->_state . '.compiled.js'));
			} else {
				$this->appendScript(file_get_contents($this->_path . '/' . $this->_state . '.combined.js'));
			}
		}
		
		return parent::toString();
	}
	
	protected function getFiletime($uri)
	{
		if (Zend_Uri::check($uri)) {
			$config = array(
				'adapter'   => 'Zend_Http_Client_Adapter_Curl',
				'curloptions' => array(
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_NOBODY => true
				)
			);
			$client = new Zend_Http_Client($uri, $config);
			return strtotime($client->request('GET')->getHeader('last-modified'));
		}
		return filemtime($this->_path . '/' . basename($uri));
	}
	
	protected function _checkModifiedDate($file)
	{
		$created = filemtime($file);
		foreach ($this->_files as $item) {
			if ($created < $item['modified']) {
				return true;
			}
		}
		return false;
	}
	
	protected function _createCombined()
	{
               if (($fh = fopen($this->_path . '/' . $this->_state . '.combined.js', 'w'))===FALSE) {
               error_log("unable to open file ".$this->_path.":".$this->_state.":".error_get_last());
               };
		
		foreach ($this->_files as $item) {
			$header = "\n\n
			///////////////////////////////////////////////////////////////////////\n
			/////     " . $item['filename'] . "\n
			///////////////////////////////////////////////////////////////////////\n\n";
			
			$body = $header . file_get_contents($item['fullpath']);
			fwrite($fh, $body);
		}
		
		fclose($fh);
	}
	
	protected function _createCompiled()
	{
		$compiler = realpath(APPLICATION_PATH . '/../tools/closure') . '/compiler.jar';
		$level = 'SIMPLE_OPTIMIZATIONS';
		$output = $this->_path . '/' . $this->_state . '.compiled.js';
		$input = $this->_path . '/' . $this->_state . '.combined.js';
		return shell_exec("java -jar $compiler --compilation_level $level --js $input --js_output_file $output");
	}
	
}
