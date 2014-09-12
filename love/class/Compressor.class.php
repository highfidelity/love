<?php

class Compressor
{	
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
				throw new Exception('File ("' . $this->getPath() . '/' .$file . '") does not exist.');
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
		
		if (!$return) {
			echo('<script type="text/javascript">' . file_get_contents($this->getCombinedFilePath()) . '</script>' . "\n");
			return true;
		}
		
		return file_get_contents($this->getCombinedFilePath());
	}
	
	public function compile($return = false)
	{
		if ((null === $this->getFiles()) || (null === $this->getPath()) || (null === $this->getFilename())) {
			throw new Exception('Files, path or filename not set, please set all information before calling combine.');
		}
		
		if (!file_exists($this->getCompiledFilePath()) || $this->checkModifiedDate($this->getCompiledFilePath())) {
			$this->createCompiled();
		}
		
		if (!$return) {
			if (!file_exists($this->getCompiledFilePath())) {
				echo('<script type="text/javascript">' . file_get_contents($this->getCombinedFilePath()) . '</script>' . "\n");
				return true;
			} else {
				echo('<script type="text/javascript">' . file_get_contents($this->getCompiledFilePath()) . '</script>' . "\n");
				return true;
			}
		}
		
		if (!file_exists($this->getCompiledFilePath())) {
			return file_get_contents($this->getCombinedFilePath());
		}
		return file_get_contents($this->getCompiledFilePath());
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
		
		$compiler = realpath(APP_PATH . '/tools/closure') . '/compiler.jar';
		$level = 'SIMPLE_OPTIMIZATIONS';
		$output = $this->getCompiledFilePath();
		$input = $this->getCombinedFilePath();
		return shell_exec("java -jar $compiler --compilation_level $level --js $input --js_output_file $output");
	}
	
	public function getCombinedFilePath()
	{
		return APP_PATH . '/' . $this->getCompressorType() . '/' . $this->getFilename() . '.combined.' . $this->getCompressorType();
	}
	
	public function getCompiledFilePath()
	{
		return APP_PATH . '/' . $this->getCompressorType() . '/' . $this->getFilename() . '.compiled.' . $this->getCompressorType();
	}


    
    static function getFilesDir($nameInclude,$compressedFiles) {
        if ($compressedFiles->getFilesDir($nameInclude) != "" ) {
            return $compressedFiles->getFilesDir($nameInclude);
        }
        return '../contrib/js';
    }
    
    static function isUsingUncompiledVersion() {
        if ( defined('FORCE_COMPILE') && ( FORCE_COMPILE === true )) {
            return false;
        }
//        if (strstr(substr($_SERVER['REQUEST_URI'], 0, 3), '~') || (!defined('USE_COMPILED_LIBRARIES') || ( USE_COMPILED_LIBRARIES !== true ) ) ) {
        if ( defined('USE_COMPILED_LIBRARIES') && ( USE_COMPILED_LIBRARIES === true ) )  {
            return false;
        }
        return true;
    }
	
	static function echoInclude($nameInclude)
	{
        $compressedFiles = new CompressedFiles();
        if ( self::isUsingUncompiledVersion() ) {
            if ( isset($compressedFiles->files[$nameInclude]) ) {
                echo '<!-- include files for group : ' . $nameInclude . ' -->' . "\n";
                foreach ($compressedFiles->files[$nameInclude] as $fileName) {
                    echo '<script type="text/javascript" src="' .  self::getFilesDir($nameInclude,$compressedFiles)  . '/' . $fileName . '"></script>' . "\n";
                }
            } else {
                echo '<!-- include file name is missing in CompressedFiles class : ' . $nameInclude . ' -->' . "\n";
            }
        } else {
            if ( defined('FORCE_COMPILE') && ( FORCE_COMPILE === true )) {
                $compressor = new Compressor();
                $compressor->setCompressorType('js')
                           ->setPath(realpath(APP_PATH . '/' . self::getFilesDir($nameInclude,$compressedFiles) ))
                           ->setFiles($compressedFiles->files[$nameInclude])
                           ->setFilename($nameInclude);
                $compressor->compile();
            } else {
                echo '<script type="text/javascript" src="'  . 'js/' . $nameInclude . '.compiled.js"></script>' . "\n";
            }
        }
    }
	
}
