// Add Loading overlay
$(function(){	
	$(window).bind('ajaxSuccess', function(e, xhr, options) {
		try {
			var j = $.parseJSON(xhr.responseText);
			if ((j.success === false) && (j.code === 1001)) {
				$('#content').empty();
				$('<h2>Session Expired</h2>').appendTo($('#content'));
				location.reload();
			}
		} catch(err) {}
	});
	$('#loading').bind('ajaxSend', function() {
		$(this).fadeIn('fast');
	}).bind('ajaxComplete', function() {
		$(this).fadeOut('fast');
	});
});

// Initialize tabs
$(function(){
	$('#tabs').tabs({
		ajaxOptions: {
			cache: false
		},
	    load: function(event, ui) {
			$('#tabs').show();
			try {
			    switch (ui.index) {
			    	case 0:
			    		company.init();
			    		branding.init();
			    		branding.brand();
			    		break;
			    	case 1:
						$('#loading').unbind('ajaxComplete');
			    	    user.init();
			    	    branding.init();
			    		branding.brand();
			    	    break;
					case 3:
						$(function(){
							$('#sub-tabs').tabs({
									ajaxOptions: {
									cache: false
								},
								load: function(event, ui) {
									try {
										switch (ui.index) {
											case 0:
												$('#loading').unbind('ajaxComplete');
												reportsLove.init();
												branding.init();
                        						branding.brand();
												break;
											/*case 1:
												$('#loading').unbind('ajaxComplete');
												reportsRewarder.init();
												branding.init();
				                        			    		branding.brand();
												break;*/
											case 1:
												$('#loading').unbind('ajaxComplete');
												reportGraph.init();
												branding.init();
                        						branding.brand();
												break;
										}
									} catch(err) {
                                        if (typeof(console) == 'object') {
											console.error(err);
										}
									}
								}
							});
							$('#sub-tabs').find('ul').addClass('custom-ui-widget-header');
						});
						reports.init();
						break;
				}
			} catch(err) {
                if (typeof(console) == 'object') {
					console.error(err);
				}
			}
	    }
	});
	$('#tabs').find('ul').addClass('custom-ui-widget-header');
});

// Plugin for disable text selection
$(function(){
    $.extend($.fn.disableTextSelect = function() {
        return this.each(function(){
            if($.browser.mozilla){//Firefox
                $(this).css('MozUserSelect','none');
            }else if($.browser.msie){//IE
                $(this).bind('selectstart',function(){return false;});
            }else{//Opera, etc.
                $(this).mousedown(function(){return false;});
            }
        });
    });
    $('.noSelect').disableTextSelect();//No text selection on elements with a class of 'noSelect'
});
