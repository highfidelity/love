<?php 

class Cloud
{
	
	protected $cloudFile;
	protected $cloudMap;
	
	public function getCloudFile()
	{
		return $this->cloudFile;
	}
	
	public function getCloudMap()
	{
		return $this->cloudMap;
	}
	
	public function setCloudFile($file)
	{
	    $dir = str_replace("class", "", dirname(__FILE__));
	    $file = $dir.$file;

		if (!file_exists($file)) {
                    if (!file_exists($dir.DEFAULT_CLOUD_MAP)) {
			throw new Exception('Cloud file '.$file.'does not exist.');
		    } else { $file=$dir.DEFAULT_CLOUD_MAP; }
		}
		$this->cloudFile = $file;
		
		return $this->setCloudMap(file_get_contents($file));
	}
	
	protected function setCloudMap($content)
	{
		$this->cloudMap = $content;
		return $this;
	}
	
    /**
     * With this constructor set a cloud by passing an array.
     *
     * @param array $options
     * @return User $this
     */
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
        return $this;
    }
	
	/**
     * Automatically sets the options array
     * Array: Name => Value
     *
     * @param array $options
     * @return User $this
     */
	private function setOptions(array $options)
	{
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
	}
	
}
