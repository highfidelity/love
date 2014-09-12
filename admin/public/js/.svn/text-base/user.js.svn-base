var user = {
    my_id:currentUser.id,
    dialog_user:null,
    user_settings:null,
	status_timer:null,
	cacheInterval:30,  // Set the cache to be valid for 30mins
    
    // Called when the User tab loads
    // Fetches the user list, fills the view
    // and attaches the event handlers.
    init: function() {
		// Check if we have the users already loaded, if not
		// do a request to get them.
		
		if (!jCache.isValid('users')) {
	        // Ajax request to get the users
	        $.ajax({
	            type: 'POST',
	            url: admin.baseUrl + '/user/userlist?show=all',
	            dataType: 'json',
	            success: function(json){
	            	// First thing sort our user list
                    // TODO : firefox says json.sort is not a function
	            	//json = json.sort(sortUsers);
	            
					// Save the user list for later
	        		jCache.set('users', json, user.cacheInterval);
					
					// Once we get all the users add them to the appropiate
					// lists, and then attach the events to them.
					user.addUsers();
					user.attachEvents();
					
					// Hide loading overlay and reattach the overlay events
					$('#loading').bind('ajaxComplete', function() {
				        $(this).fadeOut('fast');
				    });
				}
	        });
	        
	    } else {
	        // Retrieve our cached data
	        var json = jCache.get('users');
	        
            // Once we get all the users add them to the appropiate
            // lists, and then attach the events to them.
            user.addUsers();
			user.attachEvents();
			
			// Hide loading overlay and reattach the overlay events
			$('#loading').bind('ajaxComplete', function() {
		        $(this).fadeOut('fast');
		    });
		}
		
		// Invite user
		$('#invite-btn').click(function() {
			var email = $('#invite_coworker').val();
			// If the field is blank exit
			if (email === '') {
				return;
			}
			
			// Check if the user exists but is removed, and if so
			// just toggle the removed flag on the user
			var users = jCache.get('users');
			
			// Add request
			$.ajax({
	            type:'POST',
	            url: admin.baseUrl + '/user/add',
	            data: {email:email},
				dataType:'json',
				success: function(json) {
					if (json.success === true) {
					    // Clear the add user field
					    $('#invite_coworker').val('');
					    
						user.reloadUsers();
						
						// Hide loading overlay and reattach the overlay events
						$('#loading').bind('ajaxComplete', function() {
					        $(this).fadeOut('fast');
					    });
						user.showStatus(json.message, true);
					} else {
						user.showStatus(json.message, false);
					}
				}
	        });
		});
		
		// Initialize CSV File upload
		user.initCSVUpload();
        
        // Initialize the advanced settings dialog
        $('#advanced-settings').dialog({ autoOpen: false,
                                         height: 325,
                                         width: 670,
                                         resizable: false,
                                         show:'drop',
                                         hide:'drop',
                                         closeOnEscape:true,
            close:function() {
                user.cleanUp();
            }
        });
		
        // Attach search event with the users list
        // so on key pressed filters users with that name.
        $('#search-box').keyup(function() {
            user.doSearch();
        });
        $('#clean-search').click(function() {
            user.cleanSearch();
        });
    },
    bgStatus: function(msg, status) {
        if (status == null) {
            status = 0;
        } else {
        }

        $('#background-status').html(msg);
        switch (status) {
            case false:
                $('#background-status').addClass('background-error');
                // reenable uploads
                $('#upload-csv-btn').removeClass('disabled').removeAttr('disabled');
                $('#background-status').removeClass('background-working').css('cursor', 'pointer');
                $('#background-status').click(function() {
                    $(this).html('').css('cursor', 'default');
                });
                break;
            case 0:
                break;
            case 1:
                $('#background-status').addClass('background-working');
                $('#background-status').removeClass('background-success');
                $('#background-status').removeClass('background-error');
                // disable the upload button
                // $('#upload-csv-btn').addClass('disabled').attr('disabled', 'disabled');
                break;
            case 2:
                break;
            case 3:
                $('#background-status').addClass('background-success');
                // reenable uploads
                $('#upload-csv-btn').removeClass('disabled').removeAttr('disabled');
                $('#background-status').removeClass('background-working').css('cursor', 'pointer');
                $('#background-status').click(function() {
                    $(this).html('').css('cursor', 'default');
                });
            default:
                break;
        }
    },
    initCSVUpload: function() {
        new AjaxUpload('upload-csv-btn', {
            action: 'user/bulkadd',
            name: 'users',
            autoSubmit: true,
            responseType: 'json',
            onSubmit: function(file, ext) {
                if (ext != 'csv') {
                    $('#loading').fadeOut('fast');
                    user.showStatus('No valid CSV file uploaded.', false);
                    return false;
                }

                user.bgStatus('Uploading data...', 1);
            },
            onComplete: function(file, response) {
                if (response.success === true) {
                    user.bgStatus(response.message);
                    // start the job
                    $.ajax({
                        type: 'POST',
                        url: 'user/bulkprocess',
                        // timeout after 2 seconds
                        timeout: 2000,
                        // don't show loading overlay
                        global: false,
                        dataType: 'json',
                        // script timed out, it's taking a while... this is the expected function
                        error: function(response) {
                            return false;
                        },
                        success: function(response) {
                            return true;
                        }
                    });
    
                    // poll the bulk upload
                    user.background_timer = setInterval(function () {
                        $.ajax({
                            type: 'POST',
                            url: 'user/bulkstatus', 
                            dataType: 'json',
                            // no overlay
                            global: false,
                            success: function(response) {
                                if (response.success === true) {
                                    // finished processing
                                    if (response.message === 0) {
                                        // stop the polling
                                        clearInterval(user.background_timer);
                                        // update userlist cache
                                        var userlist = jCache.get('users');
                                        jCache.remove('users');
                                        for (var i = response.users.length; i--;) {
                                            userlist.push(response.users[i]);
                                        }
                                        jCache.set('users', userlist, user.cacheInterval);
                                        user.addUsers(userlist);
                                        user.bgStatus('Completed. Added ' + response.users.length + ' users', 3);
                                        
                                    // job is continuing
                                    } else {
                                        user.bgStatus(response.message, 2);
                                    }
                                } else {
                                    // job has errored - stop the poll
                                    clearInterval(user.background_timer);
                                    // show an error message
                                    user.bgStatus(response.message, false);
                                }
                            }
                        });
                    }, 10000);
                } else {
                    // If not, quit with the error message.
                    user.bgStatus(response.message, false);
                }
            }
        });
    },
    
    addUsers: function(users) {
        // Clean user viewports
        $('#all-users').empty();
        $('#user-admins').empty();
        $('#user-auditors').empty();
        
		var users = jCache.get('users');
		
        for (var i = users.length; i--;) {
        	if (users[i].removed) {
        		continue;
        	}
        	
            var nickname;
            // If the user doesn't have a nickname tooltip is ID
            if (users[i].nickname == '' || !users[i].nickname) {
                nickname = 'Id: ' + users[i].id;
            } else {
                nickname = users[i].nickname;
            }
            // Get the user
            var user_h = user.createUser(users[i].id, nickname,
                users[i].auditor, users[i].admin, null);
            user_h.addClass('user');
            // If the user is not active, add the "deactivated" class
            if (!users[i].active) {
            	user_h.addClass('deactivated');
            }
            
            // Append the user to the All Users list
            $('#all-users').prepend(user_h);
            
            // Append the user to the other categories if member
            // and add the data as well
            if (users[i].admin > 0) {
                var admin = user.createUser(users[i].id, nickname,
                    users[i].auditor, users[i].admin, '-admin');
                admin.addClass('admin');
            
                // Add icons
                admin.prepend(user.getRemoveIcon());
                // Append the user to the All Users list
                $('#user-admins').prepend(admin);
            }
            if (users[i].auditor > 0) {
                var audit = user.createUser(users[i].id, nickname,
                    users[i].auditor, users[i].admin, '-audit');
                audit.addClass('audit');
            
                // Add icons
                audit.prepend(user.getRemoveIcon());
                // Append the user to the All Users list
                $('#user-auditors').prepend(audit);
            }
        }
    },
	
	// Clears the view, and reloads the users
	reloadUsers: function() {		
	    // Ajax request to get the users
        $.ajax({
            type: 'POST',
            url: admin.baseUrl + '/user/userlist?show=all',
            dataType: 'json',
            async: false,
            success: function(json){
		        $('#all-users').empty();
		        $('#user-admins').empty();
		        $('#user-auditors').empty();
		        
		        // Sort the new users array
                // TODO : firefox says json.sort is not a function
		        //json = json.sort(sortUsers);
		        
		        jCache.remove('users');
		        
                // Save the user list for later
                jCache.set('users', json, user.cacheInterval);
                
                // Once we get all the users add them to the appropiate
                // lists, and then attach the events to them.
                user.addUsers();
                user.attachEvents();
            }
        });
	},
	
	// Update a single user
	updateUser: function(id) {
		// Get the new user data
		$.ajax({
			type:'POST',
			url: admin.baseUrl + '/user/getuserdata?user_id='+id,
			dataType:'json',
			success: function(json) {
				if (json.id !== id) {
					user.showStatus('Couldn\'t update user info', false);
					return;
				}
				
				var userlist = jCache.get('users');
				
				// Update the local copy
				// Replace the user on the local store
				for (var i = userlist.length; i--;) {
					if (userlist[i].id == id) {
						userlist.splice(i, 1);
						userlist.push(json);
					}
				}
				
				jCache.remove('users');
				jCache.set('users', userlist, user.cacheInterval);
				
				// Update the view info
		        $('#all-users').empty();
		        $('#user-admins').empty();
		        $('#user-auditors').empty();
		        
                // Once we get all the users add them to the appropiate
                // lists, and then attach the events to them.
                user.addUsers();
                user.attachEvents();
			}
		});
	},
    
    // Creates a new user item
    createUser: function(id, nickname, auditor, admin, id_ext) {
        var user_h;
        if (id_ext !== null) {
            user_h = $('<div id="' + id + id_ext + '">');
        } else {
            user_h = $('<div id="' + id + '">');
        }
        user_h.addClass('notSelected');
        if (id_ext !== null) {
            user_h.css('border','none');
        }
        
        // Add the data to the user
        user_h.data('id', parseInt(id));
        user_h.data('nickname', nickname);
        user_h.data('auditor', auditor);
        user_h.data('admin', admin);
        
        // Add the text to the item
        user_h.text(nickname);
        
        // Add icons
        user_h.append(user.getInfoIcon());
        
        // Give back the jQuery object user
        return user_h;
    },
    
    // Set @user_h as selected user
    select: function(user_h) {
        // Check if the user has been selected
        if (user_h.data('selected') != 1) {
            user_h.data('selected', 1);
            
            // Give selected user a .selected class which sets the selection colors.
            user_h.addClass('selected');
            // Remove the notSelected class
            user_h.removeClass('notSelected');
        } else {
            user_h.data('selected', 0);
            
            // Remove .selected class from the user
            user_h.removeClass('selected');
            // Add the notSelected class
            user_h.addClass('notSelected');
        }
    },
    
    // Returns if the user is selected
    isSelected: function(user_h) {
        return user_h.hasClass('selected');
    },
    
    doSearch: function() {
        var filter = $('#search-box').val().toLowerCase();
        var count = filter.length;
        
        var children = $('#all-users').children();
        var len = children.length;
        
        for (var i = len; i--;) {
            var nickname = $(children[i]).data('nickname');
            if (!nickname) {
                return;
            }
            /* Check if the nickname of the user starts with the selected filter.
             * If so we show the user, if not we hide it.
             * We make the text lowercase so the search is accurate.
             */
            if (nickname.substr(0, count).toLowerCase() != filter) {
            	$(children[i]).hide();
            } else {
            	$(children[i]).show();
            }
        }
    },
    
    cleanSearch: function() {
        $('#search-box').val('');
        user.doSearch();
    },
    
    getInfoIcon: function() {
        return $('<div class="sprite sprite-gear_14x14 info-icon" onclick="user.openAdvancedDialog($(this))"></div>');
    },
    
    getRemoveIcon: function() {
        return $('<div class="sprite sprite-cross_14x14 remove-icon" onclick="user.removeItem($(this))"></div>');
    },
    
    // Remove a user and save the change
    removeItem: function(item) {
        // Show Loading overlay
        $('#loading').show();
		
        var user_h = item.parent();
        // Store the current state
        var admin = user_h.data('admin');
        var auditor = user_h.data('auditor');
        
        // Check if parent are admins or auditors then remove the flag
        if (user_h.parent().attr('id') == 'user-admins') {
            user_h.data('admin', 0);
            admin = 0;
        }
        if (user_h.parent().attr('id') == 'user-auditors') {
            user_h.data('auditor', 0);
            auditor = 0;
        }
        
        // Save the user
        user.removeAdminAuditor(user_h, admin, auditor);
    },
    
    saveAdminAuditor: function(item, admin_f, auditor) {
        var userid = item.data('id');
        $.ajax({
            type: 'POST',
            url: admin.baseUrl + '/user/saveadminauditor',
            data: { id:userid, admin:admin_f, auditor:auditor },
            dataType: 'json',
            success: function(json) {
                if (json.success === true) {
                	// If admin add to the list
                	if (json.user.admin === 1) {
                		var admins = $('#user-admins').children();
                		var admins_l = admins.length;
                		var already_in = false;
                		
                		for (var i = admins_l; i--;) {
                			if ($(admins[i]).data('id') === userid) {
                				already_in = true;
                			}
                		}
                		
                		// Check if is already in the list
                		if (!already_in) {
                			// Add the user
                			$(item).data('admin', 1);
                			$('#user-admins').append(item);
                			
                			// Update the parent
                			$('#' + userid).data('admin', 1);
                		}
                	}
                	if (json.user.auditor === 1) {
                		var auditors = $('#user-auditors').children();
                		var auditors_l = auditors.length;
                		var already_in = false;
                		
                		for (var i = auditors_l; i--;) {
                			if ($(auditors[i]).data('id') === userid) {
                				already_in = true;
                			}
                		}
                		
                		// Check if is already in the list
                		if (!already_in) {
                			// Add the user
                			$(item).data('admin', 1);
                			$('#user-auditors').append(item);
                			
                			// Update the parent
                			$('#' + userid).data('auditor', 1);
                		}
                	}
                }
            }
        });
    },
    
    removeAdminAuditor: function(item, admin_f, auditor) {
        var userid = item.data('id');
        $.ajax({
            type: 'POST',
            url: admin.baseUrl + '/user/saveadminauditor',
            data: { id:userid, admin:admin_f, auditor:auditor },
            dataType: 'json',
            success: function(json) {
                if (json.success === true) {
                	// If admin add to the list
                	if (json.user.admin === 0) {
                		item.fadeOut('fast').remove();
                		$('#' + userid).data('auditor', 0);
                	}
                	if (json.user.auditor === 0) {
                		item.fadeOut('fast').remove();
                		$('#' + userid).data('auditor', 0);
                	}
                }
            }
        });
    },
    
    // Displays a status message in either green or red depending
    // on the value of @valid
    showStatus: function(msg, valid) {
    	// Set what kind of message we want to show
    	if (valid) {
    		$('#status-msg').addClass('validation-g');
    	} else {
    		$('#status-msg').addClass('validation-r');
    	}
    	
    	// Set the message text
    	$('#status-msg').html(msg);
    	// Show it
    	$('#status-box').fadeIn('fast');
    	
    	// Set clear message interval
    	user.status_timer = setTimeout(function () {
    		// Hide it
    		$('#status-box').fadeOut('slow');
    		// Clear the message
    		$('#status-msg').html('');
    		$('#status-msg').removeClass('validation-g');
    		$('#status-msg').removeClass('validation-r');
    	}, 3500);
    },
    
    openAdvancedDialog: function(item) {
        // Load user data
        var user_h = item.parent();
        var userid = user_h.data('id');
        var nickname_h = user_h.data('nickname');
        var userlist = jCache.get('users');
        
        // Get all the user data from the previously stored var
        var count = userlist.length; 
        for (var i = 0; i < count; i++) {
            // When we find a record that matches the id
            if ((userlist[i].id != null) && (userlist[i].id != undefined)) {
                if (userlist[i].id == userid) {
                    user.user_settings = {
                        id:userid,
                        nickname:nickname_h,
                        email:userlist[i].username,
                        team:userlist[i].team,
                        skill:userlist[i].skill,
                        picture:userlist[i].picture,
                        active:userlist[i].active,
                        removed:userlist[i].removed
                    }
                }
            }
        }
        
        // Load the user data into the UI
        $('#au-nickname').text(user.user_settings.nickname);
        $('#au-nickname-field').val(user.user_settings.nickname);
        $('#au-email').text(user.user_settings.email);
        $('#au-email-field').val(user.user_settings.email);
        if (user.user_settings.picture != '' && user.user_settings.picture !== null) {
            $('#picture').attr('src', user.user_settings.picture);
			$('#no-picture').hide();
			$('#picture').fadeIn('fast');
        }
        if (user.user_settings.team) {
            $('#au-team-field').val(user.user_settings.team);
        }
        if (user.user_settings.skill) {
            $('#au-skill-field').val(user.user_settings.skill);
        }
        
        // Set buttons if the user is either Deactivated or Removed
        if (!user.user_settings.active) {
            $('#au-deactivate-user').val('Activate');
        }
        if (user.user_settings.removed) {
            $('#au-remove-user').val('Undo Remove');
        }
        
        // Attach the events for the dialog
        user.reattachAdvancedSettingsEvents();
        
        // Open dialog and load info
        $('#advanced-settings').dialog('open');
        branding.brand();
    },
    
    // Clean all the settings on the advanced settings dialog
    cleanUp: function() {
    	// Reset the temporal store for settings
		user.user_settings = null;
		
		// Revert values
        $('#picture').attr('src', 'images/blank.png');
		$('#picture').hide();
		$('#no-picture').show();
        $('#au-nickname').val('').show();
        $('#au-nickname-field').val('').hide();
        $('#au-email').val('').show();
        $('#au-email-field').val('').hide();
        $('#au-team-field').val('');
        $('#au-skill-field').val('');
        $('#au-deactivate-user').val('Deactivate');
        $('#au-remove-user').val('Remove');
        
        // Clean Event handlers
		$('#advanced-settings').unbind('click');
		$('#au-nickname').unbind('click');
		$('#au-email').unbind('click');
		$('#au-reset-pass-btn').unbind('click');
		$('#au-deactivate-user').unbind('click');
		$('#au-remove-user').unbind('click');
		$('#au-submit-btn').unbind('click');
		$('#au-cancel-btn').unbind('click');
    },
	
	fixFields: function() {
        // If any of the changeable fields have been activated
        // save their status, and set them back to fixed label.
        if (!$('#au-nickname').is(':visible')) {
			if ($('#au-nickname-field').val() !== '') {
                // Check if the user nickname is duplicated, if so abort
                if (user.isNicknameInUse($('#au-nickname-field').val())) {
                    alert("Nickname didn't save because it's already in use.");
                } else {
				    $('#au-nickname').text($('#au-nickname-field').val());
			    }
            }
            $('#au-nickname-field').fadeOut('fast', function() {
                $('#au-nickname').fadeIn('fast');
            });
            $('#au-nickname-field').text('');
								
        }
        
        if (!$('#au-email').is(':visible')) {
			if ($('#au-email-field').val() !== '') {
				$('#au-email').text($('#au-email-field').val());
			}
            $('#au-email-field').fadeOut('fast', function() {
                $('#au-email').fadeIn('fast');
            });
            $('#au-email-field').text('');
        }
	},
    
    attachEvents: function() {
		var all_children = $('#all-users').children();
		var all_len = all_children.length;
		
        for (var i = all_len; i--;) {
            // Clear current events
            $(all_children[i]).unbind('click');
            
            // Give each user a click event
            $(all_children[i]).click(function() {
                // When clicking a user make it selected
                user.select($(this));
            });
        }
        
        $('#make-user-admin').click(function() {
    		var all_children = $('#all-users').children();
    		var all_len = all_children.length;
    		
            for (var i = all_len; i--;) {
                // If the user is selected
                if (user.isSelected($(all_children[i]))) {
                    user_h = $(all_children[i]).clone({event:true, data:true});
  
                    // Check if the user already is on the list, if so just exit.
                    var id = '#' + $(all_children[i]).data('id') + '-admin';
                    if ($('#user-admins').find(id).length > 0) {
                        return;
                    }
					
                    // Save to main user item
                    $(all_children[i]).data('admin', 1);
                    
                    // Copy data
                    user_h.data('id',$(all_children[i]).data('id'));
                    user_h.data('nickname', $(all_children[i]).data('nickname'));
                    user_h.data('admin', $(all_children[i]).data('admin'));
                    user_h.data('auditor',$(all_children[i]).data('auditor'));
                    
                    // Remove borders
                    user_h.css('border', 'none');
                    
                    user_h.prepend(user.getRemoveIcon());
                    
                    // Convert the item to admin
                    var current_id = user_h.data('id');
                    user_h.attr('id', current_id + '-admin');
                    user_h.removeClass('user')
                    	  .removeClass('selected')
                          .addClass('admin');
                                        
                    // Add it to the list
                    user.saveAdminAuditor(user_h, 1, $(all_children[i]).data('auditor'));
                }
            }
        });
        
        $('#make-user-auditor').click(function() {
    		var all_children = $('#all-users').children();
    		var all_len = all_children.length;
    		
            for (var i = all_len; i--;) {
                // If the user is selected
                if (user.isSelected($(all_children[i]))) {
                    user_h = $(all_children[i]).clone({event:true, data:true});
  
                    // Check if the user already is on the list, if so just exit.
                    var id = '#' + $(all_children[i]).data('id') + '-audit';
                    if ($('#user-auditors').find(id).length > 0) {
                        return;
                    }
                    
                    // Save to main user item
                    $(all_children[i]).data('auditor', 1);
                    
                    // Copy data
                    user_h.data('id',$(all_children[i]).data('id'));
                    user_h.data('nickname',$(all_children[i]).data('nickname'));
                    user_h.data('admin',$(all_children[i]).data('admin'));
                    user_h.data('auditor', $(all_children[i]).data('auditor'));
                    
                    // Remove borders
                    user_h.css('border', 'none');
                    
                    user_h.prepend(user.getRemoveIcon());
                    
                    // Convert the item to admin
                    var current_id = user_h.data('id');
                    user_h.attr('id', current_id + '-audit');
                    user_h.removeClass('user')
                    	  .removeClass('selected')
                    	  .addClass('audit');
                                        
                    // Add it to the list
                    user.saveAdminAuditor(user_h, $(all_children[i]).data('admin'), $(all_children[i]).data('auditor'));
                }
            }
        });
        
        // Unselect all users
        $('#unselect-all').click(function() {
    		var all_children = $('#all-users').children();
    		var all_len = all_children.length;
    		
            for (var i = all_len; i--;) {
                if (user.isSelected($(all_children[i]))) {
                    user.select($(all_children[i]));
                }
            }
        });
	},
        
    reattachAdvancedSettingsEvents: function() {
        // --- Advanced Settings handlers
        $('#advanced-settings').click(function() {
            user.fixFields();
        });
        
		// Transition between label->field
        $('#au-nickname').click(function() {
            $(this).fadeOut('fast', function() {
                $('#au-nickname-field').fadeIn('fast');
                $('#au-nickname-field').focus();
            });
        });
        
        // Prevent the field to fix if the click is inside
        $('#au-nickname-field').click(function(event) {
            event.stopPropagation();
        });
        
		// Transition between label->field
        $('#au-email').click(function() {
            $(this).fadeOut('fast', function() {
                $('#au-email-field').fadeIn('fast');
				$('#au-email-field').focus();
            });
        });
        
        // Prevent the field to fix if the click is inside
        $('#au-email-field').click(function(event) {
            event.stopPropagation();
        });
		
		// Reset the password for the current user
		$('#au-reset-pass-btn').click(function() {
            // Show Loading overlay
            $('#loading').show();

			$.ajax({
				type:'POST',
				url: admin.baseUrl + '/user/resetpassword',
				data: { userid: user.user_settings.id },
				dataType:'json',
				success:function(json) {
					// Show return message
					if (json.success === true) {
						user.showStatus(json.message, true);
					} else {
						user.showStatus(json.message, false);
					}
				}
			});
		});
		
		// Deactivate user
		$('#au-deactivate-user').click(function() {
            if ($(this).val() === 'Deactivate') {
                $(this).val('Activate');
            } else {
                $(this).val('Deactivate');
            }
		});
		
		// Remove user
		$('#au-remove-user').click(function() {
			if ($(this).val() === 'Remove') {
				$(this).val('Undo Remove');
			} else {
				$(this).val('Remove');
			}
		});
		
		// Save settings
		$('#au-submit-btn').click(function() {
			// Save info into settings array
			// If the click-to-change fields are edited save them
			user.fixFields();
			var id = user.user_settings.id;
			user.user_settings.nickname = $('#au-nickname').text();
			user.user_settings.email = $('#au-email').text();
			user.user_settings.team = $('#au-team-field').val();
			user.user_settings.skill = $('#au-skill-field').val();
            
            // Show Loading overlay
            $('#loading').show();
			
			if ($('#au-deactivate-user').val() === 'Deactivate') {
				var active = 1;
			} else {
				var active = 0;
			}
			
			if ($('#au-remove-user').val() === 'Remove') {
                var removed = 0;
            } else {
                var removed = 1;
            }
			
			// Close the dialog
			$('#advanced-settings').dialog('close');
			
			// Do save request
            $.ajax({
                type:'POST',
                url: admin.baseUrl + '/user/save',
                data: {
					userid: user.user_settings.id,
					nickname: user.user_settings.nickname,
					username: user.user_settings.email,
					team: user.user_settings.team,
					skill: user.user_settings.skill,
					active: active,
					removed: removed
				},
                dataType:'json',
                success:function(json) {
                    // Show return message
                    if (json.success === false) {
                        user.showStatus(json.message, false);
                    } else {
                    	user.showStatus(json.message, true);
                    }
                    
                    // Reload view with updated user
                    user.updateUser(id);
                }
            });
		});
		
		$('#au-cancel-btn').click(function() {
			$('#advanced-settings').dialog('close');
		});
    },
    
    isNicknameInUse: function(nickname) {
        var ret = false;
        $('#all-users .user').each(function(ind) {
            if ($(this).text() == nickname) {
                ret = true;
            }
        });
        return ret;
    }
};

