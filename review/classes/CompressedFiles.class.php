<?php

class CompressedFiles
{	
	public $files = array(
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
    	'rewarder1' => array(
            'throbber.js'
        ),
    	'rewarder2' => array(
            'review.end-period.js'
        ),
    	'modulescripts' => array(
            'class.js',
            'rewarder.js',
            'rewarder-chart.js',
            'rewarder.chartchoose.js'
        )
	);
    

	public $filesDir = array();
    function __construct() {
        $this->filesDir["rewarder1"] = 'js';
        $this->filesDir["rewarder2"] = 'js';
        $this->filesDir["modulescripts"] = 'js';
    }
    public function getFilesDir($key) {
        if (isset($this->filesDir[$key])) {
            return $this->filesDir[$key];
        }
        return "";
    }
    
}