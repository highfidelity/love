<?php

class Compressor
{
	const COMPILED_PATH = '/compiled';
	const COMBINED_PATH = '/combined';
	
	protected $_path;
	protected $_files;
	protected $_filename;
	protected $_compressorType;
	
	public function __construct()
	{}
	
	public function setCompressorType($type)
	{
		if (($type == 'js') || ($type == 'css')) {
			$this->_compressorType = $type;
			return $this;
		}
		
		throw new Exception('Wrong compressor type ("' . $type . '") set.');
	}
	
	public function getCompressorType()
	{
		return $this->_compressorType;
	}
	
	public function setPath($path)
	{
		if (!is_dir($path)) {
			throw new Exception('Path ("' . $path . '") is not a valid directory.');
		}
		
		$this->_path = $path;
		return $this;
	}
	
	public function getPath()
	{
		return $this->_path;
	}
	
	public function setFiles($files = array())
	{
		if (empty($files)) {
			throw new Exception('Files can not be empty.');
		}
		
		$cleanFiles = array();
		
		foreach ($files as $file) {
			if ((defined('LIB_DEFAULT_DATE') && (LIB_DEFAULT_DATE === "false")) && !file_exists($this->getPath() . '/' . $file)) {
				throw new Exception('File ("' . $file . '") does not exist.');
			}
			
			$modified = (defined('LIB_DEFAULT_DATE') && (LIB_DEFAULT_DATE !== "false")) ? (int)LIB_DEFAULT_DATE : filemtime($this->getPath() . '/' . $file);
			$fullpath = $this->getPath() . '/' . $file;
			$filename = basename($file);
			$path = preg_split("/$filename/", $fullpath);
			
			$cleanFiles[] = array(
				'modified' => $modified,
				'path'	   => realpath($path[0]),
				'fullpath' => $fullpath,
				'filename' => $filename
			);
		}
		
		$this->_files = $cleanFiles;
		return $this;
	}
	
	public function getFiles()
	{
		return $this->_files;
	}
	
	public function setFilename($filename)
	{
		$this->_filename = basename($filename);
		return $this;
	}
	
	public function getFilename()
	{
		return $this->_filename;
	}
	
	public function combine($return = false)
	{
		if ((null === $this->getFiles()) || (null === $this->getPath()) || (null === $this->getFilename())) {
			throw new Exception('Files, path or filename not set, please set all information before calling combine.');
		}
		
		if (!file_exists($this->getCombinedFilePath()) || $this->checkModifiedDate($this->getCombinedFilePath())) {
			$this->createCombined();
		}
		
		$content = file_get_contents($this->getCombinedFilePath());
		
		if (!$return) {
			$this->outputContent($content, filemtime($this->getCombinedFilePath()));
		}
		
		return $content;
	}
	
	public function compile($return = false)
	{
		if ((null === $this->getFiles()) || (null === $this->getPath()) || (null === $this->getFilename())) {
			throw new Exception('Files, path or filename not set, please set all information before calling combine.');
		}
		
		if (!file_exists($this->getCompiledFilePath()) || $this->checkModifiedDate($this->getCompiledFilePath())) {
			$this->createCompiled();
		}
		
		if (file_exists($this->getCompiledFilePath())) {
			$content = file_get_contents($this->getCompiledFilePath());
			$modified = filemtime($this->getCompiledFilePath());
		} else {
			$content = file_get_contents($this->getCombinedFilePath());	
			$modified = filemtime($this->getCombinedFilePath());
		}
		
		if (!$return) {
			$this->outputContent($content, $modified);
		}
	
		return $content;
	}
	
	protected function checkModifiedDate($file)
	{		
		if (defined('LIB_DEFAULT_DATE') && (LIB_DEFAULT_DATE === "false")) {
			$created = filemtime($file);
			foreach ($this->getFiles() as $item) {
				if ($created < $item['modified']) {
					return true;
				}
			}
		}
		return false;
	}
	
	protected function createCombined()
	{
		$fh = fopen($this->getCombinedFilePath(), 'w');
		
		foreach ($this->_files as $item) {
			$header = "\n\n
			///////////////////////////////////////////////////////////////////////\n
			/////     Filename: " . $item['filename'] . "\n
			/////     Path: " . $item['path'] . "\n
			///////////////////////////////////////////////////////////////////////\n\n";
			
			$body = $header . file_get_contents($item['fullpath']);
			fwrite($fh, $body);
		}
		
		fclose($fh);
	}
	
	protected function createCompiled()
	{
		if (!file_exists($this->getCombinedFilePath()) || $this->checkModifiedDate($this->getCombinedFilePath())) {
			$this->createCombined();
		}
		
		$compiler = realpath(CONTRIB_PATH . '/tools/closure' . '/compiler.jar');
		$level = 'SIMPLE_OPTIMIZATIONS';
		$output = $this->getCompiledFilePath();
		$input = $this->getCombinedFilePath();
		return shell_exec("java -jar $compiler --compilation_level $level --js $input --js_output_file $output");
	}
	
	protected function getCombinedFilePath()
	{
		return CONTRIB_PATH . self::COMBINED_PATH . '/' . $this->getFilename() . '.combined.' . $this->getCompressorType();
	}
	
	protected function getCompiledFilePath()
	{
		return CONTRIB_PATH . self::COMPILED_PATH . '/' . $this->getFilename() . '.compiled.' . $this->getCompressorType();
	}
	
	protected function outputContent($content, $lastmodified)
	{
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header('Etag: ' . md5($content));
		header('Vary: *');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
		header('Content-type: ' . Utilities::getMimeType($this->getCompressorType()));
		echo($content);
		exit();
	}
	
}
