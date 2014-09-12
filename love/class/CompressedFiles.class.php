<?php
class CompressedFiles
{	
	public $files = array(
/*		'tofor_redeem' => array(
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
		),*/
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
    	),
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
        ),*/
    	'campaign' => array(
            'campaign-chart.js',
            'periods.js',
            'campaign.js'
        )/*,
    	'campaignjs3' => array(
			'class.js'
        ),
    	'campaignjs2' => array(
            'rewarder.js',
            'rewarder-chart.js',
			'rewarder.chartchoose.js'
        )*/
	);
	public $filesDir = array();
    function __construct() {
        $this->filesDir["campaign"] = 'js';
    }
    public function getFilesDir($key) {
        if (isset($this->filesDir[$key])) {
            return $this->filesDir[$key];
        }
        return "";
    }
    
}