var company = {
	weeklyUpdates: false,
	init: function() {
		company.initColorPicker();
		company.initLogoUpload();
		company.initBackgroundImageUpload();
		company.initBindEvents();
		company.initPaymentButton();
		company.initWeeklyUpdates();
	},
	initColorPicker: function() {
		$('#apperance_background_color').ColorPicker({
			color: $('input[name=background_color]').val(),
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				company.updateColors();
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#apperance_background_color').css('backgroundColor', '#' + hex);
				$('input[name=background_color]').val(hex);
			}
		});
		$('#apperance_body_text_color').ColorPicker({
			color: $('input[name=body_text_color]').val(),
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				company.updateColors();
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#apperance_body_text_color').css('backgroundColor', '#' + hex);
				$('input[name=body_text_color]').val(hex);
			}
		});
		$('#apperance_header_text_color').ColorPicker({
			color: $('input[name=header_text_color]').val(),
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				company.updateColors();
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#apperance_header_text_color').css('backgroundColor', '#' + hex);
				$('input[name=header_text_color]').val(hex);
			}
		});
		$('#apperance_highlight1_color').ColorPicker({
			color: $('input[name=highlight1_color]').val(),
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				company.updateColors();
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#apperance_highlight1_color').css('backgroundColor', '#' + hex);
				$('input[name=highlight1_color]').val(hex);
			}
		});
		$('#apperance_highlight2_color').ColorPicker({
			color: $('input[name=highlight2_color]').val(),
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				company.updateColors();
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#apperance_highlight2_color').css('backgroundColor', '#' + hex);
				$('input[name=highlight2_color]').val(hex);
			}
		});
	},
	
	initBindEvents: function() {
		$('select[name=website_font]').change(function() {company.updateColors();});
		
		// * Theme management events
		
		// When changing the value of the combobox, apply selected branding
		$('#theme_dropdown').change(function() {
			var selected_theme = $('#theme_dropdown :selected').text();
		
			$.ajax({
				type: 'POST',
				url: admin.baseUrl + '/company/changetheme',
				data: { theme: selected_theme },
				dataType: 'json',
				success: function(json) {					
					// Brand the application with the choosen theme
					branding.forceReload();
					branding.brand();
					
					// Apply the theme options to the interface controls
					company.updateThemeSettings();
				}
			});
		});
		
		// Save customized theme as a new, with the given name
		$('#save_theme_btn').click(function() {
			// If no name is selected use the current selected theme
			// unless that one is the a default theme
			if ($('#new_theme_name').val() === '') {
				if ($('#theme_dropdown :selected').text() !== 'LoveMachine') {
					var new_theme = $('#theme_dropdown :selected').text();
				} else { // Do nothing
					return;
				}
			} else {
				var new_theme = $('#new_theme_name').val();
			}
		
			$.ajax({
				type: 'POST',
				url: admin.baseUrl + '/company/savetheme',
				data: { new_theme_name: new_theme },
				dataType: 'json',
				success: function(json) {
					// Unselect current selected theme
					$('#theme_dropdown :selected').removeAttr('selected');
				
					// Add the new theme to the themes list
					var option = $('<option value="' + new_theme + '" selected="selected">' + new_theme + '</option>');
					$('#theme_dropdown').append(option);
					
				
					// Clean the save field
					$('#new_theme_name').val('');
				}
			});
		});
		
		// Remove selected theme and revert to the default
		$('#remove_theme_btn').click(function() {
			var selected_theme = $('#theme_dropdown :selected').text();
		
			$.ajax({
				type: 'POST',
				url: admin.baseUrl + '/company/deletetheme',
				data: { theme: selected_theme },
				dataType: 'json',
				success: function(json) {
					// Remove from the list
					$('#theme_dropdown :selected').remove();
					
					// Revert to the default theme
					branding.forceReload();
					branding.brand();
					
					// Apply the theme options to the interface controls
					company.updateThemeSettings();
				}
			});
		});
	},
	
	changeToCustom: function() {
		// If the Custom entry doesn't exist yet on the combobox we add it now
		if ($('option[value=Custom]').length === 0) {
			$('#theme_dropdown').append($('<option value="Custom">Custom</option>'));
		}
	
		$('#theme_dropdown :selected').removeAttr('selected');
		$('option[value=Custom]').attr('selected', 'selected');
	},
	
	// Update the interface controls to reflect changes on the selected theme
	updateThemeSettings: function() {
		// Get and update colors
		$('#apperance_background_color').css('background', branding.bkg_color);
		$('#apperance_background_color').ColorPickerSetColor(branding.bkg_color);

		$('#apperance_body_text_color').css('background', branding.body_color);
		$('#apperance_body_text_color').ColorPickerSetColor(branding.body_color);
		
		$('#apperance_header_text_color').css('background', branding.header_color);
		$('#apperance_header_text_color').ColorPickerSetColor(branding.header_color);
		
		$('#apperance_highlight1_color').css('background', branding.highlight1);
		$('#apperance_highlight1_color').ColorPickerSetColor(branding.highlight1);
		
		$('#apperance_highlight2_color').css('background', branding.highlight2);
		$('#apperance_highlight2_color').ColorPickerSetColor(branding.highlight2);
		
		// Set selected font
		$('#website_font :selected').removeAttr('selected');
		$('option[value=' + branding.font + ']').attr('selected', 'selected');
		
		// Get and update bkg options
		$('#background_tile').removeAttr('checked');
		if (branding.settings.background_tile) {
			$('#background_tile').attr('checked', 'checked');
		}
		$('#background_fix').removeAttr('checked');
		if (branding.settings.background_fix) {
			$('#background_fix').attr('checked', 'checked');
		}
		
		// Get and update logo and bkg images
		$('#background_image').css('background-image', 'url("' + branding.bkg_image + '&h=100&w=180")');
		$('#company_branding_logo').attr('src', branding.logo);
	},
	
	initLogoUpload: function() {
		new AjaxUpload('company_branding_logo', {
			action: 'company/logoupload',
			name: 'companylogo',
			autoSubmit: true,
			responseType: 'json',
			onSubmit: function(file, extension) {},
			onComplete: function(file, response) {
				if (response.success == true) {
					var src =  '/love/thumb.php?t=iLU&app=admin&imageid=' + response.id +  '&time=' + getCurrentTime();
					$('#company_branding_logo').attr('src', src);
					$('#logo_img').attr('src', src);
					branding.forceReload();
					branding.brand();
					
					company.changeToCustom();
				}
				else {
					$('#error_upload').text(response.message);
				}
			}
		});
	},
	
	initBackgroundImageUpload: function() {
		new AjaxUpload('background_image', {
			action: 'company/backgroundimageupload',
			name: 'backgroundImage',
			autoSubmit: true,
			responseType: 'json',
			onSubmit: function(file, extension) {},
			onComplete: function(file, response) {
				if (response.success == true) {
					var src =  '/love/thumb.php?t=iBI&app=admin&imageid=' + response.id +  '&h=100&w=180&time=' + getCurrentTime();
					$('#background_image').css('background-image', 'url("' + src + '")');
					branding.forceReload();
					branding.brand();
					
					company.changeToCustom();
				}
				
			}
		});
		new AjaxUpload('bgUploadTrigger', {
			action: 'company/backgroundimageupload',
			name: 'backgroundImage',
			autoSubmit: true,
			responseType: 'json',
			onSubmit: function(file, extension) {},
			onComplete: function(file, response) {
				if (response.success == true) {
					var src =  '/love/thumb.php?t=AU&app=admin&imageid=' + response.id +  '&h=100&w=180&time=' + getCurrentTime();
					$('#background_image').css('background-image', 'url("' + src + '")');
					branding.forceReload();
					branding.brand();
					
					company.changeToCustom();
				}
			}
		});

		$('#background_tile').change(function() {
			company.updateBGSettings();
    		});    

    		$('#background_fix').change(function() {
        		company.updateBGSettings();
    		});
    },
    
	updateBGSettings: function() {
		$.ajax({
			url: $('#form_background').attr('action'),
			type: 'post',
			dataType: 'json',
			data: {
				background_tile: $('input[name=background_tile]').attr('checked') ? 1 : 0,
				background_fix: $('input[name=background_fix]').attr('checked') ? 1 : 0
			},
			success: function(j) {
			    branding.forceReload();
			    branding.brand();
			    
			    company.changeToCustom();
			}
		});  
	},  

	initPaymentButton: function() {
		$('#buy-btn').click(function() {
			$('#company_subscription form').submit();
		});
	
	},

	updateColors: function() {
		$.ajax({
			url: $('#form_appearance').attr('action'),
			type: 'post',
			dataType: 'json',
			data: {
				background_color: $('input[name=background_color]').val(),
				body_text_color: $('input[name=body_text_color]').val(),
				header_text_color: $('input[name=header_text_color]').val(),
				highlight1_color: $('input[name=highlight1_color]').val(),
				highlight2_color: $('input[name=highlight2_color]').val(),
				website_font: $('select[name=website_font]').val()
			},
			success: function(j) {
			    branding.forceReload();
			    branding.brand();
			    
			    company.changeToCustom();
			}
		});
	},

	initWeeklyUpdates: function() {
		if ($('#weeklyupdate').val() == 'on') {
			$('.weekly_detail_settings').fadeIn('fast');
			company.setWeekly(true);
		}
		$('#weeklyupdate').click(function(e) {
			if (e.target.checked) {
				$('.weekly_detail_settings').fadeIn('fast');
				company.setWeekly(true);
			} else {
				$('.weekly_detail_settings').fadeOut('fast');
				company.setWeekly(false);
			}
			company.weeklyUpdateSend();
		});
		$('select[name=updateweekday]').change(company.weeklyUpdateSend);
		$('select[name=updatehour]').change(company.weeklyUpdateSend);
		$('select[name=updateminute]').change(company.weeklyUpdateSend);
	},

	weeklyUpdateSend: function() {
		$.ajax({
			url: $('form.weekly_detail_settings').attr('action'),
			type: 'post',
			dataType: 'json',
			data: {
				active: company.getWeekly(),
				weekday: $('select[name=updateweekday]').val(),
				hour: $('select[name=updatehour]').val(),
				minute: $('select[name=updateminute]').val()
			},
			success: function() {}
		});
	},

	setWeekly: function(flag) {
		if (flag) {
			company.weeklyUpdates = true;
		} else {
			company.weeklyUpdates = false;
		}
	},

	getWeekly: function() {
		return company.weeklyUpdates;
	}
};


