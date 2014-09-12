<?php

class Javascript extends Controller
{

	const FILE_EXTENSION = 'js';
	const FOLDER = '/js';
	
	public $javascript = array(
		'tofor_redeem' => array(
						'jquery.min.js',
						'jquery-ui.min.js',
						'raphael-min.js'
		),		
		'tofor_redeem2' => array(
						'jquery.blockUI.js',
						'livevalidation.js',
						'jquery.charcount.js',
						'i18n/grid.locale-en.js',
						'jquery.jqGrid.min.js',
						'jquery.tools.tooltip.min.js',
						'jquery.tabSlideOut.v1.3.js',
						'jquery.masonry.mod.min.js',
						'jquery.tipsy.js',
						'jquery.inview.js',
						'branding.js',
						'feedback.js',    
						'jstorage.js',
						'jcache.js',    
						'jquery.listnav.min-2.1.js',
						'g.raphael-min.js',
						'g.pie-min.js',
						'jquery.tablePagination.0.2.min.js'
		),		
		'admin_login' => array(
		    'jquery.min.js',
			'jquery-ui.min.js',
            'jstorage.js',
            'jcache.js', 
			'branding.js'
		),
		'admin' => array(
			'jquery.min.js',
			'jquery-ui.min.js',
			'raphael-min.js',
			'g.raphael-min.js',
			'g.pie-min.js',
			'g.bar-min.js',
            'jstorage.js',
            'jcache.js', 
			'branding.js',
			'ajaxupload.js',
			'fileuploader.js',
			'colorpicker.js'
		),
		'love_login' => array(
			'jquery.min.js',
			'jquery-ui.min.js',
			'livevalidation.js',
			'jquery.tabSlideOut.v1.3.js',
            'jstorage.js',
            'jcache.js', 
			'feedback.js',
			'jquery.blockUI.js',
			'branding.js'
		),
		'love_tofor' => array(
    		'jquery.min.js',
    		'jquery-ui.min.js',
    		'jquery.blockUI.js',
    		'jquery.tools.tooltip.min.js',
    		'jquery.charcount.js',
    		'jquery.tabSlideOut.v1.3.js',
    		'jquery.masonry.mod.min.js',
			'jquery.tipsy.js', 
			'jquery.inview.js', 
			'jquery.listnav.min-2.1.js', 
			'jquery.tablePagination.0.2.min.js',
			'jquery.jqGrid.min.js',
			'i18n/grid.locale-en.js',
			'livevalidation.js', 
			'raphael-min.js', 
			'g.raphael-min.js', 
			'g.pie-min.js',
			'jstorage.js',
			'jcache.js', 
			'branding.js', 
			'feedback.js'
		),
		'love_settings' => array(
			'jquery.min.js', 
			'jquery-ui.min.js', 
			'jquery.blockUI.js',
            'jstorage.js',
            'jcache.js', 
			'livevalidation.js', 
			'ajaxupload.js'
		),
		'love_forgot' => array(
			'jquery.min.js', 
			'jquery-ui.min.js', 
			'livevalidation.js', 
			'jquery.tabSlideOut.v1.3.js', 
            'jstorage.js',
            'jcache.js',
			'feedback.js', 
			'jquery.blockUI.js', 
			'branding.js'
		),
		'love_campaign' => array(
			'jquery.min.js', 
			'jquery-ui.min.js', 
			'raphael-min.js', 
			'jquery.blockUI.js', 
			'jquery.tools.tooltip.min.js', 
			'livevalidation.js', 
			'i18n/grid.locale-en.js', 
			'jquery.jqGrid.min.js',     
			'jquery.scrollTo-min.js',     
			'class.js',
            'jquery.combobox.js'
    	),
		'love_campaign2' => array(
            'jstorage.js',
            'jcache.js'
    	),
/*		'love_campaign3' => array(
			'jquery.combobox.js'
    	),*/
    	'love_stage' => array(
            'jquery.min.js',
            'jquery-ui.min.js',
            'jquery.blockUI.js',
            'jquery.tools.min.js',
            'jquery.tabSlideOut.v1.3.js',
            'jquery.masonry.mod.min.js',
            'livevalidation.js',
            'raphael-min.js',
            'jstorage.js',
            'jcache.js',
            'branding.js'
        ),
    	'review_head' => array(
            'jquery.min.js',
            'jquery-ui.min.js'
        ),
    	'review_rewarder1' => array(
            'jquery.min.js',
            'jquery-ui.min.js',
            'livevalidation.js'
        ),
    	'review_rewarder2' => array(
            'raphael-min.js',
            'jquery.blockUI.js'
        ),
    	'review_module_scripts' => array(
            'jquery.scrollTo-min.js',
            'jquery.combobox.js'
        ),
		'sales',
		'trial'
	);

	public function compiled($filename, $options)
	{
		if (!isset($this->javascript[$filename])) {
			$this->bye();
		}
		
		$compressor = new Compressor();
		$compressor->setCompressorType('js')
				   ->setPath(CONTRIB_PATH . '/js')
				   ->setFiles($this->javascript[$filename])
				   ->setFilename($filename);

		$compressor->compile();
	}
	
	public function combined($filename, $options)
	{
		if (!isset($this->javascript[$filename])) {
			$this->bye();
		}
		
		$compressor = new Compressor();
		$compressor->setCompressorType('js')
				   ->setPath(CONTRIB_PATH . '/js')
				   ->setFiles($this->javascript[$filename])
				   ->setFilename($filename);

		$compressor->combine();
	}
	
	public function __call($name, $arguments)
	{
		$file = '';

		foreach ($arguments as $key => $argument) {
			if (empty($argument)) {
				unset($arguments[$key]);
				continue;
			}
			
			if (!is_array($argument)) continue;
			
			if (isset($argument['folder'])) {
				$file .= $argument['folder'];
				unset($arguments[$key]);
			}
		}
		
		if (!empty($arguments)) {
			$filename = array(implode('.', $arguments), $name, self::FILE_EXTENSION);
		} else {
			$filename = array($name, self::FILE_EXTENSION);
		}
		
		$file .= '/' . implode('.', $filename);
		
		if (file_exists(CONTRIB_PATH . self::FOLDER . $file)) {
			header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
			header('Etag: ' . md5(file_get_contents(CONTRIB_PATH . self::FOLDER . $file)));
			header('Vary: *');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime(CONTRIB_PATH . self::FOLDER . $file)) . ' GMT');
			header('Content-type: ' . Utilities::getMimeType(self::FILE_EXTENSION));
			echo(file_get_contents(CONTRIB_PATH . self::FOLDER . $file));
			exit();
		} else {
			$this->bye();
		}
	}
	
	public function bye()
	{
		header('HTTP/1.0 404 Not Found');
		exit();
	}

}
