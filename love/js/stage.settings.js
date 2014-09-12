//  vim:ts=4:et
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

// Brand the application
$(function() {

    // Try to Initialize the branding module
    if (branding.init()) { // If succed, apply main branding and additional parts
        branding.brand();
    
        // Apply header colors to the Custom tabs
        $('#pages li a').css('color', branding.header_color);
        $('#companyUserSwitch a').css('color', branding.header_color);
    
        // Apply custom colors to Most loved table
        $('#topLove li').css('color', branding.body_color);
        $('.lb').css('color', branding.body_color);
    
        // Apply custom colors to charts
        $.user.review_done_color = branding.highlight1;
        $.user.review_not_done_color = branding.highlight2;
      
        // we need to append to head in order for 'live' updates - when new elements are created, they refer to the original css
        $('head').append('<style type="text/css">' +
                         '  .one a {color: ' + branding.body_color + ';}' + 
                         '  .two a {color: ' + branding.header_color + ';}' +
                         '  .three a {color: ' + branding.highlight1 + ';}' +
                         '  .four a {color: ' + branding.highlight2 + ';}' +
                         '</style>');
    }
});  

$(function(){
    // domsearch caching
    $.cloudWrapper = $('#cloudWrapper');
    $.feedWrapper = $('#feedWrapper');
    $.cloudDiv = $('.cloud-div');
    $.feedDiv = $('.feed-div');
	$('.stage-slide-out-div').show(); // to avoid div poping up on page load before Jquery is running
    $('.stage-slide-out-div').tabSlideOut({
        tabHandle: '.stage-handle',               //class of the element that will become your tab
        pathToTabImage: 'images/Corner-button.png',//path to the image for the tab //Optionally can be set using css
        imageHeight: '24px',                     //height of tab image           //Optionally can be set using css
        imageWidth: '26px',                      //width of tab image            //Optionally can be set using css
        tabLocation: 'left',                       //side of screen where tab lives, top, right, bottom, or left
        speed: 300,                               //speed of animation
        action: 'click',                          //options: 'click' or 'hover', action to trigger animation
        topPos: '-2px',                            //position from the top/ use if tabLocation is left or right
        leftPos: '0px',                          //position from left/ use if tabLocation is bottom or top
        fixedPosition: false                      //options: true makes it stick(fixed position) on scroll
    });
	
	// Since slide-out won't let text into the tab, we'll force it	
	$('.stage-handle').css({
		'text-align':'center',
		'text-indent':'0px',
		'line-height':'22px',
		'font-family':'Arial, Helvetica, Sans-serif',
		'font-size':'13px',
		'font-weight':'bold',
		'cursor':'pointer',
		'color':'#444'
	});
	// ---  

	$('#settings').submit(function(e){
		var view_choosen = '';
		
		if ($('#view-both:checked').val())	{
			view_choosen = $('#view-both:checked').val();
		}
		if( $('#view-feed:checked').val() )	{
			view_choosen = $('#view-feed:checked').val();
		}
		if ($('#view-cloud:checked').val())	{
			view_choosen = $('#view-cloud:checked').val();
		}

		if (view_choosen != ''){
			// If selected both we remove the old view and fade in
			// the cloud and the feed.
			if (view_choosen == 'both')	{
			    // Unset the only feed view flag
                setJustFeedView(false);
				$('.cloudWrapper').fadeIn(400);
				$('.feedWrapper').fadeIn(400);
				
				// Adjust the cloud size to make room for the feed
                var win_h = $(window).height();
                var feed_h = $('.feedWrapper')[0].offsetHeight;
                $('.cloud-div').animate({height:(win_h - ((feed_h*1.5) + 10)) +'px'});
			}
			// If selected the cloud we fade out the feed and
			// fade in the cloud.
			if (view_choosen == 'cloud')	{
                // Unset the only feed view flag
                setJustFeedView(false);
				$('.cloudWrapper').fadeIn(400);
				$('.feedWrapper').fadeOut(400);
				$('.feedWrapper').hide();
				
				// Adjust the cloud height to fill the whole container
				$('.cloud-div').height($('.cloudWrapper').height());
			}
			// if selected the feed we fade out the cloud and
			// fade in the feed.
			if (view_choosen == 'feed')	{
                // Set the only feed view flag
                setJustFeedView(true);
                // Show the feed
				$('.feedWrapper').fadeIn(400);
				$('.cloudWrapper').fadeOut(400);
			}			
			$('.stage-handle').click();
		}
		e.preventDefault();
		return false;
	});
});