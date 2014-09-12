//
//  Copyright (c) 2009, LoveMachine, LLC.
//  All Rights Reserved.
//  http://www.lovemachineinc.com
//

// Important!!! This file must be kept in sync accross all apps, if you do modify it
// remember to update the other repos.

// Brand the application
var branding = {

    settings: null,
    bkg_color: null,
    bkg_image: null,
    bkg_tile: null,
    bkg_fix: null,
    bkg_pos: null,
    logo: null,
    font: null,
    body_color: null,
    header_color: null,
    highlight1: null,
    highlight2: null,
    loaded: false,

    // Load and prepare branding settings
    init: function() {
        if (! branding.loaded) {
            // Load the settings
            return branding.reload(360);
        }  else {
            return true;
        }
    },
    
    // Brand application
    brand: function() {
        // Remove to disable the beta tag
//        branding.addBetaTag();
        branding.applyCompanyLogo();
        branding.applyBkgColor();
        branding.applyBkgImage();
        branding.applyBodyColor();
        branding.applyFont();
        branding.applyHighlight1Color();
        branding.applyHighlight2Color();
        branding.applyHeaderColor();
    },
    
    // Reload branding settings
    reload: function(expiration, force) {
        branding.loaded = false;
        //force = (force == true) ? true : false;
	force = true;
        
        // If we have cached results use them if not, request them
        if (!jCache.isValid('companySettings') || (force == true)) {
            $.ajax({
                async: false,
                type: 'POST',
                url: '/../admin/api/getcompanysettings/cacheValid',
                data: { app: 'admin', key: '0', token: 'tofor' },
                dataType: 'json',
                success:function(json) {                   
                    // Make sure that we got the settings from Admin
                    if (json.success === true) {
                        // Store our data in the cache
                        jCache.set('companySettings', json, expiration);
                        
                        // Locally store the settings
                        branding.settings = json.settings;
                        branding.bkg_color = '#' + branding.settings.background_color;
                        branding.bkg_image = '/../love/thumb.php?t=con1&app=admin&imageid=' + branding.settings.background_image +  '&time=' + branding.settings.background_image_updated;
                        branding.bkg_tile = branding.settings.background_tile == '1' ? 'repeat' : 'no-repeat';
                        branding.bkg_fix = branding.settings.background_fix == '1' ? 'scroll' : 'fixed';
                        branding.bkg_pos = '50% 450px';
                        branding.logo = '/../love/thumb.php?t=con2&app=admin&imageid=' + branding.settings.logo +  '&time=' + branding.settings.logo_updated;
                        branding.font = branding.settings.website_font;
                        branding.body_color = '#' + branding.settings.body_text_color;
                        branding.header_color = '#' + branding.settings.header_text_color;
                        branding.highlight1 = '#' + branding.settings.highlight1_color;
                        branding.highlight2 = '#' + branding.settings.highlight2_color;
                        branding.loaded = true;
                    }
                }
            });
        } else {
            var json = jCache.get('companySettings');

            // Locally store the settings
            branding.settings = json.settings;
            branding.bkg_color = '#' + branding.settings.background_color;
            branding.bkg_image = '/../love/thumb.php?t=con3&app=admin&imageid=' + branding.settings.background_image +  '&time=' + branding.settings.background_image_updated;
            branding.bkg_tile = branding.settings.background_tile == '1' ? 'repeat' : 'no-repeat';
            branding.bkg_fix = branding.settings.background_fix == '1' ? 'scroll' : 'fixed';
            branding.bkg_pos = '50% 450px';
            branding.logo = '/../love/thumb.php?t=con4&app=admin&imageid=' + branding.settings.logo +  '&time=' + branding.settings.logo_updated;
            branding.font = branding.settings.website_font;
            branding.body_color = '#' + branding.settings.body_text_color;
            branding.header_color = '#' + branding.settings.header_text_color;
            branding.highlight1 = '#' + branding.settings.highlight1_color;
            branding.highlight2 = '#' + branding.settings.highlight2_color;
            branding.loaded = true;
        }
        return branding.loaded;
    },
    
    forceReload: function() {
    	branding.reload(120, true);
    },
    
    // Apply company logo
    applyCompanyLogo: function() {
        $('#logo_img').attr('src', branding.logo);
		$('#company_branding_logo').attr('src', branding.logo);
    },
    
    // Apply background color
    applyBkgColor: function() {
        $('body').css('background', branding.bkg_color + ' url(' + branding.bkg_image + ') ' + branding.bkg_tile + ' ' + branding.bkg_fix + ' ' + branding.bkg_pos);
    },
    
    // Apply background image
    applyBkgImage: function() {
        $('body').css('background', branding.bkg_color + ' url(' + branding.bkg_image + ') ' + branding.bkg_tile + ' ' + branding.bkg_fix + ' ' + branding.bkg_pos);
    },
    
    // Apply body text color
    applyBodyColor: function() {
        $('body').css('color', branding.body_color);
        $('h1').css('color', branding.body_color);
        $('h2').css('color', branding.body_color);
        $('h3').css('color', branding.body_color);
        $('h4').css('color', branding.body_color);
        $('p').css('color', branding.body_color);
        $('.ui-widget-content').css('color', branding.body_color);
        $('.ui-widget').css('color', branding.body_color);
    },
    
    // Apply app font
    applyFont: function() {
        $('body').css('font-family', branding.font);
        $('.ui-widget').css('font-family', branding.font + ' !important');
        $('#tabs').css('font-family', branding.font);
        $('.ui-widget-content').css('font-family', branding.font);
    },
    
    // Apply header text color
    applyHeaderColor: function() {
        $('.hTitle').css('color', branding.header_color);
        $('h1').css('color', branding.header_color);
        $('li a').css('color', branding.header_color);
        $('#companyUserSwitch a').css('color', branding.header_color);
        $('.ui-widget').css('color', branding.header_color);
        $('.ui-tabs-nav a').css('color', branding.header_color);
    },
    
    // Apply highlight 1 color
    applyHighlight1Color: function() {
        $('a').css('color', branding.highlight1);
        $('.me').css('color', branding.highlight1);
    },
    
    // Apply highlight 2 color
    applyHighlight2Color: function() {
        $('table td').css('color', '');
        $('.msg').css('color', branding.highlight2);
    },

    addBetaTag: function() {
    	// If the tag is already there skip the adition
    	if ($('#beta-tag').length !== 0) {
    		return;
    	}
    	
    	// Create the tag element
    	var tag = $('<span id="beta-tag">BETA</span>');
    	// Get the title element
    	var title = $('#userInfo .hTitle');
    	
    	// Set the elment style
    	tag.css('float', 'right')
    	   .css('padding-top', '7px')
    	   .css('font-family', 'Verdana')
    	   .css('font-size', '8px')
    	   .css('font-weight', 'bold')
    	   .css('letter-spacing', '1px');
    	
    	// If we are on Admin we need to add a bit of margin to
    	// compensate for the tall last letter.
    	if (title.text() === 'Control Panel') {
    		tag.css('margin-left', '2px');
    	}
    	
    	// Append the element
    	title.append(tag);
    }
};

function getCurrentTime() {
	return  new Date().getTime();
}

