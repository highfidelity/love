var nickname, currentpassword, newpassword, confirmpassword;

var settings = {
    init: function() {
        settings.initBranding();
        settings.initPictureUpload();
        settings.initSubmit();
        settings.initValidation();
        if (smsEnabled) {
            settings.initSMS();
        }
    },
    initBranding: function() {
        // try to initialize branding module
        if (branding.init()) {
            // on success, apply main branding and additional parts
            branding.brand();
        }
    },
    initSubmit: function() {
        $('#settings form').submit(function() {
        	if ($('#oldpassword').val() === '' && $('#newpassword').val() === '' && $('#confirmpassword').val() === '') {
        		var massValidation = LiveValidation.massValidate( [ nickname ]);
                currentpassword.disable();
                confirmpassword.disable();
        	} else {
        		var massValidation = LiveValidation.massValidate( [ nickname, currentpassword, newpassword, confirmpassword ]);
        	}
        
            if (massValidation) {
                  $.blockUI({
                      message: '<h1>Updating settings...</h1>', 
                      css: { border: '3px solid #a00' } 
                  });
                  
                  // clear any previous message
                  $('#ajaxresponse').html('');
                  
                  // send the settings
                  $.ajax({
                      url: 'settingsAjax.php',
                      dataType: 'json',
                      type: 'POST',
                      data: $('#settings form').serialize(), 
                      success:
                          function(json) {
                              if ( json.error > 0 ) {
                                  for(var i = 0; i < json.message.length; i++){
                                      $('#ajaxresponse').append(json.message[i] + "<br/>");
                                  }
                                  $('#ajaxresponse').show();
                                  
                                  if (json.message[0] == 'Invalid password.') {
                                      $('#oldpassword').after('<span class="LV_validation_message LV_invalid">You did not enter the correct password</span>');
                                  }
                              } else {
                                  if (json.redirect) {
                                      $('div.blockMsg').text('Redirecting...');
                                      window.location.href = json.redirect;
                                  }
                                  if (json.message && json.message.length > 0) {
                                      $('#ajaxresponse').html("Update was successful." + "<br/>" + json.message).show();
                                      // update the user object
                                      $.user.nickname = $('#nickname').val(); 
                                      $('.nicknameAjax').text($.user.nickname);
                                  }

                                if (smsEnabled) {
                                    settings.updateSmsConfirmation(json.smsConfirmed);
                                }
                              }  

                              // clear validation and password fields
                              $('.LV_valid').hide();
                              //currentpassword.disable();
                              //confirmpassword.disable();
                              $('#settings input[type=password]').val('');
                              $.unblockUI();
                          }
                  });
            }
        
          return false;
        });
    },
    initPictureUpload: function() {
        var validateUpload = function(file, extension) { 
            if (! (extension && /^(jpg|jpeg|gif|png)$/i.test(extension))) {
                // extension is not allowed
                $('span.LV_validation_message.upload').css('display', 'none').empty();
                var html = 'This filetype is not allowed!';
                $('span.LV_validation_message.upload').css('display', 'inline').append(html);
                // cancel upload
                return false;
            }
            $('#profilepicture').addClass('loading');
            $('#profilepicture img').remove();
        }

        var completeUpload = function(file, data) {
            $('span.LV_validation_message.upload').css('display', 'none').empty();
            if ( !data.success ) {
                $('span.LV_validation_message.upload').css('display', 'inline').append(data.message);
                $('#profilepicture').removeClass('loading');
                $('#profilepicture img').css('opacity', 1);
           } else {
                var src = 'thumb.php?t=lScU&src=/uploads/' + data.picture + '&w=120&h=110&zc=z&_nocache=' + Math.floor(Math.random()*111111111111);
                var headerSrc = 'thumb.php?t=lScUh&src=/uploads/' + data.picture + '&w=50&h=50&zc=z&_nocache=' + Math.floor(Math.random()*111111111111);
                // load new image in the background
                var img = new Image();
                $(img).load(function() {
                    $(this).hide();
                    $('#profilepicture').removeClass('loading').append(this);
                    $(this).fadeIn();
                    $(this).attr('id', 'picture');

                    var pictureUpload = new AjaxUpload('picture', {
                        action: 'api.php',
                        name: 'profile',
                        data: { action: 'uploadProfilePicture', api_key: $.user.love_key, userid: $.user.user_id },
                        autoSubmit: true,
                        hoverClass: 'imageHover',
                        responseType: 'json',
                        onSubmit: validateUpload,
                        onComplete: completeUpload
                    });
                }).attr('src', src);
                $('#userImage img').attr('src', headerSrc);
            }
        }
        var pictureUpload = new AjaxUpload('picture', {
            action: 'api.php',
            name: 'profile',
            data: { action: 'uploadProfilePicture', api_key: $.user.love_key, userid: $.user.user_id },
            autoSubmit: true,
            hoverClass: 'imageHover',
            responseType: 'json',
            onSubmit: validateUpload,
            onComplete: completeUpload
        });
        var pictureUploadText = new AjaxUpload($('.uploadTrigger'), {
            action: 'api.php',
            name: 'profile',
            data: { action: 'uploadProfilePicture', api_key: $.user.love_key, userid: $.user.user_id },
            autoSubmit: true,
            hoverClass: 'imageHover',
            responseType: 'json',
            onSubmit: validateUpload,
            onComplete: completeUpload
        });	
    },
    initValidation: function() {

        var timer;

        nickname = new LiveValidation('nickname', {
            validMessage: "You have an OK Nickname", 
            onValid: function() {
                if ($('#nickname').val() != $.user.nickname) {
                    this.insertMessage( this.createMessageSpan() ); 
                    this.addFieldClass();
                }
            }
        });
        // fail on spaces, html, character range, and 'Guest'. more blocked usernames to be added
        nickname.add(Validate.Format, {pattern: /<(.|\n)*?>/, negate:true, failureMessage: 'Your username contains invalid characters.'});
        nickname.add(Validate.Format, {pattern: /[\s\"\'\<\>\\\/]/, negate:true, failureMessage: 'Your username contains invalid characters.'})
        nickname.add(Validate.Length, { minimum: 2, maximum: $('#nickname').attr('maxlength') } );
        nickname.add(Validate.Exclusion, { within: [ 'Guest' ], caseSensitive: false, failureMessage: 'Nickname in use, please try again.' });

        $('#nickname').bind('keyup', function() {
            // make sure nickname is actually different
            if ($('#nickname').val() !== $.user.nickname) {
                // fire when 500 milliseconds elapsed, reduce amount of ajax requests
                window.clearTimeout( timer );
                timer = window.setTimeout( function() {  
                    $.get('helper/check_duplicate_user.php', { nickname: $('#nickname').val()}, function(data) {
                        if (data !== 'true') {
                            // add the nickname as a validate exclusion rule
                            nickname.add(Validate.Exclusion, { within: [ data ], failureMessage: "Nickname in use, please try again."  });	  
                            nickname.validate();
                        }
                    });  
                }, 500);
            } else {
                window.clearTimeout( timer );
            }
        });

        currentpassword = new LiveValidation('oldpassword', { onValid: function(){}, onlyOnBlur: true });
        //currentpassword.disable();

        newpassword = new LiveValidation('newpassword',{ validMessage: "You have an OK password.", onlyOnBlur: true });
        newpassword.add(Validate.Length, { minimum: 5, maximum: 12 } );
            $('#newpassword').bind('keyup', function() {
                if ($(this).val().length > 0) {
                    currentpassword.enable().add(Validate.Presence).validate();
                    confirmpassword.enable().add(Validate.Presence).validate();
                } else {
                    //currentpassword.disable();
                    //confirmpassword.disable();
                }
        });

        confirmpassword = new LiveValidation('confirmpassword', {validMessage: "Passwords Match." });
        confirmpassword.add(Validate.Custom, { against: function(v,a) { return (($('#newpassword').val().length > 0) && ($('#newpassword').val() != v)) ? false : true; }, failureMessage: 'Passwords do not match.' } );
        //confirmpassword.disable();
    },
    initSMS: function() {

        $("#send-test").click(function() {

            var int_code = $('#int_code').val();
            var phone = $('#phone').val();
            if (int_code != '' && phone != '') {
                $.ajax({
                    type: "POST",
                    url: 'jsonserver.php',
                    data: {
                        action: 'sendTestSMS',
                        phone: int_code + phone
                    },
                    dataType: 'json'
                });
                alert('Test SMS Sent');
            } else {
                alert('Please enter a valid telephone number.');
            }
            return false;

        });
    },
    updateSmsConfirmation: function(smsConfirmed){
        
        $('#sms-confirmation #confirmation').empty();
        if(smsConfirmed){
            $('#sms-confirmation').hide();
        }else{
            $('#sms-confirmation').show();
        }
    }
};

$(function() {
  settings.init();
});
