<?php

class Admin_Model_Themes
{
	
	protected $_id;
	protected $_theme_name;
	protected $_logo;
	protected $_background_image;
	protected $_background_tile;
	protected $_background_fix;  
	protected $_background_color;
	protected $_body_text_color;
	protected $_header_text_color;
	protected $_highlight1_color;
	protected $_highlight2_color;
	protected $_website_font;
	protected $_protected;
	protected $_all_assets_mapper;
	
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
        $this->_all_assets_mapper = new Admin_Model_AllAssetsMapper();
    }
	
	public function __set($name, $value)
	{
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid company property');
        }
        $this->$method($value);
	}
	public function __get($name)
	{
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid company property');
        }
        return $this->$method();
	}
	
    public function setOptions(array $options)
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
    
    public function toArray()
    {
    	return array(
    		'id'				        => $this->getId(),
    		'theme_name'		        => $this->getTheme_name(),
    		'logo'				        => $this->getLogo(),
    	    'logo_updated'			    => $this->getLogo_updated(),
    		'background_image'	        => $this->getBackground_image(),
    	    'background_image_updated'	=> $this->getBackground_image_updated(),
			'background_tile'           => $this->getBackground_tile(),
			'background_fix'            => $this->getBackground_fix(),
    		'background_color'	        => $this->getBackground_color(),
    		'body_text_color'	        => $this->getBody_text_color(),
    		'header_text_color'	        => $this->getHeader_text_color(),
    		'highlight1_color'	        => $this->getHighlight1_color(),
    		'highlight2_color'	        => $this->getHighlight2_color(),
    		'website_font'		        => $this->getWebsite_font()
    	);
    }
	
	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}
	public function getId()
	{
		return $this->_id;
	}
	
	public function setTheme_name($name)
	{
		$this->_theme_name = (string)$name;
		return $this;
	}
	public function getTheme_name()
	{
		return $this->_theme_name;
	}
	
	public function setLogo($logo)
	{
		$this->_logo = $logo;
		return $this;
	}
	public function getLogo()
	{
		return $this->_logo;
	}
	public function getLogo_updated()
	{
	    return strtotime($this->_all_assets_mapper->find($this->getLogo())->getUpdated());
	}
	public function getLogoSrc()
	{
		return  SANDBOX_URL_BASE . '/love/thumb.php?t=gLS&app=admin&imageid=' . $this->getLogo() . '&h=100&w=100';
	}
	
	public function setBackground_image($image)
	{
		$this->_background_image = $image;
		return $this;
	}
	public function getBackground_image()
	{
		return $this->_background_image;
	}
	public function getBackground_image_updated()
	{
	    return strtotime($this->_all_assets_mapper->find($this->getBackground_image())->getUpdated());
	}
	public function getBackgroundImageSrc()
	{
		return  SANDBOX_URL_BASE . '/love/thumb.php?t=gBI&app=admin&imageid=' . $this->getBackground_image() . '&h=20&w=120';
	}
	
	public function setBackground_tile($tile) {
		$this->_background_tile = (int)$tile;
		return $this;
	}
	public function getBackground_tile()
	{
		return $this->_background_tile;
	}

	public function setBackground_fix($fix) {
		$this->_background_fix = (int)$fix;
		return $this;
	}
	public function getBackground_fix()
	{
		return $this->_background_fix;
	}

	public function setBackground_color($color)
	{
		$this->_background_color = (string)$color;
		return $this;
	}
	public function getBackground_color()
	{
		return $this->_background_color;
	}
	
	public function setBody_text_color($color)
	{
		$this->_body_text_color = (string)$color;
		return $this;
	}
	public function getBody_text_color()
	{
		return $this->_body_text_color;
	}
	
	public function setHeader_text_color($color)
	{
		$this->_header_text_color = (string)$color;
		return $this;
	}
	public function getHeader_text_color()
	{
		return $this->_header_text_color;
	}
	
	public function setHighlight1_color($color)
	{
		$this->_highlight1_color = (string)$color;
		return $this;
	}
	public function getHighlight1_color()
	{
		return $this->_highlight1_color;
	}
	
	public function setHighlight2_color($color)
	{
		$this->_highlight2_color = (string)$color;
		return $this;
	}
	public function getHighlight2_color()
	{
		return $this->_highlight2_color;
	}
	
	public function setWebsite_font($font)
	{
		$this->_website_font = (string)$font;
		return $this;
	}
	public function getWebsite_font()
	{
		return $this->_website_font;
	}

	public function setProtected($protected)
	{
		$this->_protected = (int)$protected;
		return $this;
	}
	public function getProtected()
	{
		return $this->_protected;
	}

}

