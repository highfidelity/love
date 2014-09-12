//
//  Copyright (c) 2012, Below92 LLC.
//  All Rights Reserved. 
//  http://www.sendlove.us
//

$('document').ready(function() {

    // Set Trial end date
    var currentTime = new Date();
    currentTime.setMonth(currentTime.getMonth()+1);
    
    var date = dateFormat(currentTime, 'mmmm dS, yyyy');
    // 
    // $('#trial-finish').text(date);
	
	  // Clean signup form
  	cleanSignup();
	
  	// Enable Live Validation
  	addLV();
	
  	// Domain checking
  	checkDomain();
	
    // overall animation speed
    $.animSpeed = 300;

     // image viewer functionality
         $.imageViewer = {
             // cache the necessary elements
             viewer: $('#imageViewer').show(0).fadeOut(0),
             container: $('#imageContainer'),
             image: $('#imageViewed'),
             closer: $('#imageClose'),
             zoom: $('#imageZoom').show(0).animate({opacity: 0},0),
             close: function(e) {
                 // close image with animation
                 e.preventDefault();
                 $.imageViewer.viewer.animate({opacity: 0, width: 400, marginLeft: -200}, $.animSpeed);
                 $.imageViewer.container.animate({height: 0}, $.animSpeed);
                 $.imageViewer.image.fadeOut($.animSpeed);
             },
             showImage: function(e) {
                 // click function handler use $(elem).click() to fire from elsewhere
                 e.preventDefault();
                 var im = $(this);
                 //load this image's src into the viewer's image source
                 $.imageViewer.image.attr('src', $(this).attr('src'));
                 // the rest is just to make it look nice
                 $.imageViewer.viewer.animate({opacity: 1, width: im.width() + 20, marginLeft: (0 - im.width() - 20) / 2}, $.animSpeed);
                 $.imageViewer.container.animate({height: im.height() + 20}, $.animSpeed);
                 $.imageViewer.viewer.fadeIn($.animSpeed);
                 $.imageViewer.image.fadeIn($.animSpeed);
             },
             // show and hide the zoom overlay
             showZoom: function(e) {
                 $.imageViewer.zoom.stop().animate({opacity: 1}, $.animSpeed);
             },
             hideZoom: function(e) {
                 $.imageViewer.zoom.stop().animate({opacity: 0}, $.animSpeed);
             }
         }
    
    // commenting this out until the images are added -birarda
    // set up hover for list elements
     $('.content li').hover(function() {
        var x = $(this);
        if (!x.hasClass('current')) {
             // to ensure we can animate this nicely there's a fair bit of jiggery pockery here
             // fade the current one and it's image and once that's complete remove the class and fade it back in
             $('li.current', this.parentNode).animate({opacity: 0.5}, 'fast').find('img').fadeOut('fast', function() {
                 $(this).parent().removeClass('current').animate({opacity: 1}, 'fast');
             });
             
             // then sort out the new list item and associated image
             var img = x.addClass('current').find('img').fadeIn('fast');
             
             // if it's zoomable show the tip, otherwise make sure we don't
             if (x.attr('id') === 'getStarted') {
                 $('#imageZoom').hide();
             }
             if (img.hasClass('zoom')) {
                 $.imageViewer.showZoom();
             } else {
                 $.imageViewer.hideZoom();
             }
         }
     });
    
    // add fixed class to footer
    if ($('.fix-footer').length > 0) {
        $('#footer').addClass('fixed');
    }    
    
    // set up content switching
    $('.headerSwitch').click(function(){
        if ($('.signup').length == 0) {
            window.location.href = "index.php?box=signup"
        } else {
            newTab = $(this);
            oldTab = $('.headerSwitch.active');
            oldTab.removeClass('active');
            newTab.addClass('active');
            if (newTab.attr('data-contentClass') == 'signup') {
              $('#footer').removeClass('fixed');
              $('#signupSideBox').show();
              $('#getStarted').hide();
            } else {
              $('#footer').addClass('fixed');
              $('#signupSideBox').hide();
              $('#getStarted').show();
            }
            $('.' + oldTab.attr('data-contentClass')).hide();
            if (newTab.attr('data-contentClass') == 'how' || newTab.attr('data-contentClass') == 'why') {
                $('.' + newTab.attr('data-contentClass') + ' :first').trigger('mouseover');
                $('.' + newTab.attr('data-contentClass') + ' :first ').trigger('mouseout');
            }
            $('.' + newTab.attr('data-contentClass')).show();
            setFirstArrowSelected();
        }        
    });   
    
    // triangle on list items in each section
    $('.content li').hover(function() {
        $('.content li').children('.arrow-right').remove();
        $('.content li').css('background', 'inherit');
        
        $('.height40 li').children('.arrow-right').remove();
        $('.height40 li').css('background', 'inherit');
        
        $(this).css('background', '#E6E6E6');
        arrow = $('<div class="arrow-right" />');
        height = $(this).outerHeight();
        arrow.css('border-top', height / 2 + 'px solid transparent');
        arrow.css('border-bottom', height / 2 + 'px solid transparent');
        arrow.css('border-left', height / 2 + 'px solid rgb(230,230,230)');
        arrow.css('left', $(this).width());
        arrow.css('top', -1 * $(this).height() - (11 - Math.floor(height / 20)));
        $(this).append(arrow);
    });
    
    // set up the viewer click/hover behaviour
    $.imageViewer.closer.click($.imageViewer.close);
    $.imageViewer.zoom.click(function(){ 
        $('li.current img').click();
    });
    
    //initialise images 
     $('.box img').fadeOut(0);
     $('.box img.zoom').click($.imageViewer.showImage);
 
    // ajax form
    $('#lovesignup').submit(function(e) {
    	e.preventDefault();
        // Check that all data is right
        if (isInstanceValid() && isEmailValid() && isPasswordLengthOK() && isPasswordValid()) {
	        var instance = $('#instance-name').val();
	        
	        // Attach Name field validation msg
	        var valid = false;
                if ($('#name').val() !== '' && ($('#name').val().match("[a-z0-9A-Z]+")==$('#name').val())) {
                    valid = true;
                }
			
	        addValidationMsg({fieldId: 'name', isValid: valid, validMsg: 'Name is valid', invalidMsg: 'User name is invalid'});
	        
	        var name = $('#name').val();
	        // If name is empty exit.
	        if (name == '') {
	        	return false;
	        }
	        var email = $('#email').val();
	        var password = $('#password').val();
	        // If a logo is selected attach that
	        var logo_url = $('#logo').val();
	        
	        var strSource = $('#source').val();
	        var strAdword = $('#adword').val();
	        if (logo_url != '') {
	        	var data = {action:'req_confirm', instance: instance, name: name, email: email, password: password, source:strSource, adword:strAdword, logo:logo_url};
	        } else {
	        	var data = {action:'req_confirm', instance: instance, name: name, email: email, password: password, source:strSource, adword:strAdword};
	        }
	        
	        $('.handle').click();
	        $.post('confirm.php',data);
	        $('#signup-sent').fadeIn('slow');
        } else {
        	alert('You must fill all the fields, and make sure the information is valid.');
        }
    });
    
    // switch to the right box on page load
    $('body').find("[data-contentClass='" + $.box + "']").click();
    $('.how .first').trigger('mouseenter');
    $('.how .first').trigger('mouseout');

    setFirstArrowSelected();
});

function setFirstArrowSelected() {
    $('.content li').children('.arrow-right').remove();
    $('.content li').css('background', 'inherit');
    
    $('.height40 li').children('.arrow-right').remove();
    $('.height40 li').css('background', 'inherit');
    
    $('.selected').each(function(i) {
        var obj = $('.selected')[i];
        
        arrow = $('<div class="arrow-right" />');
        height = $(obj).outerHeight();
        arrow.css('border-top', height / 2 + 'px solid transparent');
        arrow.css('border-bottom', height / 2 + 'px solid transparent');
        arrow.css('border-left', height / 2 + 'px solid rgb(230,230,230)');
        arrow.css('left', $(obj).width());
        arrow.css('top', -1 * $(obj).height() - (11 - Math.floor(height / 20)));
        $(obj).append(arrow);
        
        $(obj).css('background', '#E6E6E6');
        
        var x = $(obj);
        if (!x.hasClass('current')) {
             // to ensure we can animate this nicely there's a fair bit of jiggery pockery here
             // fade the current one and it's image and once that's complete remove the class and fade it back in
             $('li.current', this.parentNode).animate({opacity: 0.5}, 'fast').find('img').fadeOut('fast', function() {
                 $(this).parent().removeClass('current').animate({opacity: 1}, 'fast');
             });
             
             // then sort out the new list item and associated image
             var img = x.addClass('current').find('img').fadeIn('fast');
             
             // if it's zoomable show the tip, otherwise make sure we don't
             if (x.attr('id') === 'getStarted') {
                 $('#imageZoom').hide();
             }
             if (img.hasClass('zoom')) {
                 $.imageViewer.showZoom();
             } else {
                 $.imageViewer.hideZoom();
             }
         }
    });
}

function cleanSignup() {
	$('#instance-name').val('');
	$('#name').val('');
	$('#email').val('');
	$('#password').val('');
	$('#password-repeat').val('');
	$('#logo-sel').val('');
	$('#signup-sent').css('display','none');
}


/* Live validation stuff */
function addLV() {
	// Check Instance name availability
	$('#instance-name').change(function() {
		var isValid = isInstanceValid();
		addInstanceValidation(isValid);
	});
	
	$('#name').change(function() {
		var isValid = false;
		if ($('#name').val() !== ''  && ($('#name').val().match("[a-z0-9A-Z]+")==$('#name').val())) {
			isValid = true;
		}
		addValidationMsg({fieldId: 'name', isValid: isValid, validMsg: 'Name is valid', invalidMsg: 'User name is invalid'});
	});
	
	$('#name').keydown(function(event) {
		if (event.keyCode == 9) {
			var isValid = false;
			if ($('#name').val() !== '' && ($('#name').val().match("[a-z0-9A-Z]+")==$('#name').val())) {
				isValid = true;
			}
			addValidationMsg({fieldId: 'name', isValid: isValid, validMsg: 'Name is valid', invalidMsg: 'User name is invalid'});
		}
	});
	
	// Check email address
	$('#email').change(function() {
		var isValid = isEmailValid();
        addValidationMsg({fieldId: 'email', isValid: isValid, validMsg: 'Email is valid', invalidMsg: 'Invalid email address'});
	});
	$('#email').keydown(function(event) {
		if (event.keyCode == 9) {
			var isValid = isEmailValid();
	        addValidationMsg({fieldId: 'email', isValid: isValid, validMsg: 'Email is valid', invalidMsg: 'Invalid email address'});
	    }
	});

    // Check password length status
    $('#password').change(function() {
        var isValid = isPasswordLengthOK();
        addValidationMsg({fieldId: 'password', isValid: isValid, validMsg: 'Password length is OK', invalidMsg: 'Password must be between 5 and 12 chars'});
    });
    $('#password').keydown(function(event) {
    	if (event.keyCode == 9) {
	        var isValid = isPasswordLengthOK();
    	    addValidationMsg({fieldId: 'password', isValid: isValid, validMsg: 'Password length is OK', invalidMsg: 'Password must be between 5 and 12 chars'});
    	}
    });
    
	// Check password
	$('#password-repeat').keyup(function() {
		// Validate
		var pass = $('#password').val();
		var pass_r = $('#password-repeat').val();

		// If there is already a Validation message,
		// remove it.
		if ($('#ps-lv-msg').length > 0) {
			$('#ps-lv-msg').remove();
		}
		if (pass_r == pass) {
			// Add validation msg
			var msg = $('<span id="ps-lv-msg" style="display:inline;" class="LV_validation_message LV_valid"></span>');
			msg.text('Password is valid');
			$(this).parent().append(msg);
		} else {
			// Add invalid msg
			var msg = $('<span id="ps-lv-msg" style="display:inline;" class="LV_validation_message LV_invalid"></span>');
			msg.text('Passwords don\'t match');
			$(this).parent().append(msg);
		}

		// If password empty remove validation msg
		if (pass == '' && pass_r == '') {
			$('#ps-lv-msg').remove();
		}
	});
	
	$('#password').keyup(function() {
		// Validate
		var pass = $('#password').val();
		var pass_r = $('#password-repeat').val();

		// If there is already a Validation message,
		// remove it.
		if ($('#ps-lv-msg').length > 0) {
			$('#ps-lv-msg').remove();
		}
		if (pass_r == pass) {
			// Add validation msg
			var msg = $('<span id="ps-lv-msg" style="display:inline;" class="LV_validation_message LV_valid"></span>');
			msg.text('Password is valid');
			$('#password-repeat').parent().append(msg);
		} else {
			// Add invalid msg
			var msg = $('<span id="ps-lv-msg" style="display:inline;" class="LV_validation_message LV_invalid"></span>');
			msg.text('Passwords don\'t match');
			$('#password-repeat').parent().append(msg);
		}
		
		// If password empty remove validation msg
		if (pass == '' && pass_r == '') {
			$('#ps-lv-msg').remove();
		}
	});
}

// Check if requesting a new instance
// if so, set the instance field on signup form
// and validate it.
function checkDomain() {
	var current = document.domain;
	var parts = current.split('.');
	var domain = parts[0];
	
	// If domain begins with "www." remove it and only show
	// a complete word without periods
	if (domain == 'www' && parts.length >= 4) {
	    domain = parts[1];
	}
	
	// If domain is the main website or a sandbox skip it this
	if (domain == 'www' || domain == 'dev') {
		return;
	} else {
		$('#instance-name').val(domain);
	
		// Validate the instance
		var isValid = isInstanceValid();
		addInstanceValidation(isValid);
	}
}

function isPasswordValid() {
	var pass = $('#password').val();
	var pass_r = $('#password-repeat').val();
	
	if (pass_r == pass && pass_r != '') return true;
	else return false;
}

// password should be between 5 and 12 characters
function isPasswordLengthOK(){
    var pass = $('#password').val();
    if(pass.length >= 5 && pass.length <= 12){
        return true;
    }else{
        return false;
    }
}

function isEmailValid() {
	var email = $('#email').val();
	var ret = false;
	$.ajax({
		async:false,
		type:'POST',
		url:'validate.php',
		dataType: 'json',
		data: { action:'email',data:email },
		success:function(data) {
			if (data.valid == true) {
				ret = true;
			} else {
				ret = false;
			}
		}
	});
	return ret;
}

function addValidationMsg(options){

    var holderId = options.fieldId + '-lv-msg';

    // remove existing validation
    if ($('#' + holderId).length > 0) {
        $('#' + holderId).remove();
    }

    if (options.isValid == true) {
        // Add validation msg
        var msg = $('<span id="' + holderId + '" style="display:inline;" class="LV_validation_message LV_valid"></span>');
        msg.text(options.validMsg);
        $('#' + options.fieldId).parent().append(msg);
    } else {
        // Add invalid msg
        var msg = $('<span id="' + holderId + '" style="display:inline;" class="LV_validation_message LV_invalid"></span>');
        msg.text(options.invalidMsg);
        $('#' + options.fieldId).parent().append(msg);
    }
}

function isInstanceValid() {
	var instance = $('#instance-name').val();
	// If the instance name contains spaces, auto remove them
	instance = instance.split(' ').join('');
	$('#instance-name').val(instance);
	
	var parts = instance.split('.');
    var domain = parts[0];
    
    // If domain is has more than 2 components check
    // if the first is "www" switch to the second
    if (parts.length > 1 && parts[1] != '') {
        if (domain == 'www') {
            domain = parts[1];
            // Update the filtered name to the instance field
            $('#instance-name').val(domain);
        }
    }
    
	var ret = false;
	$.ajax({
		async:false,
		type:'POST',
		url:'validate.php',
		dataType: 'json',
		data: { action:'instance',data:domain },
		success:function(data) {
			if (data && data.exists == true) {
				ret = true;
			} else {
				ret = false;
			}
		}
	});
	return ret;
}

function addInstanceValidation(isValid) {
	// If there is already a Validation message,
	// remove it.
	if ($('#in-lv-msg').length > 0) {
		$('#in-lv-msg').remove();
	}
	
	if (isValid == true) {
		// Add validation msg
		var msg = $('<span id="in-lv-msg" class="LV_validation_message LV_valid"></span>');
		msg.text('Selected name is available');
		$('#inst-name-block').append(msg);
	} else {
		// Add invalid msg
		var msg = $('<span id="in-lv-msg" class="LV_validation_message LV_invalid"></span>');
		msg.text('Selected name is not available');
		$('#inst-name-block').append(msg);
	}
	
	// If the field is empty remove any validation
	if ($('#instance-name').val() == '') {
		$('#in-lv-msg').remove();
		$('#instance-name').css('margin-bottom','10px');
	} else {
		$('#instance-name').css('margin-bottom','2px');
	}
}
