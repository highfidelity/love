
$(function(){
    if ($('#ping_contact').length === 1) {
    
        $('#ping_contact').click(function(e) {
            e.preventDefault();
            if ($('#messageDialog').length === 0) {
                var d = $('<div id="messageDialog" title="Contact SendLove" style="position:relative; line-height: 30px; padding: 10px; "><form>'+
                          '<label for="name">Name:</label><input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" value="" style="width:245px;float:right;" /> ' +
                          '<br style="clear:both;" /><label for="email">Email:</label><input type="text" name="email" id="email" class="text ui-widget-content ui-corner-all" value="" style="width:245px;float:right;" /> ' +
                          '<br style="clear:both;" /><label for="message">Message:</label><textarea name="message" id="message" rows="4" size="100" class="text ui-widget-content ui-corner-all" value="" style="width:245px;float:right;" /> ' +
                          '</textarea></form></div>');
            } else {
                var d = $('#messageDialog');
            }
      
            var email;   // keep this local variable here (required for closure)
        
            d.dialog({
                autoOpen: true,
                resizable: false,
                height: 'auto',
                width: 350,
                modal: false,
                buttons: {
                    Send: function() {
                        if (email.validate()) {
                            var d = $(this);
                            $.ajax({
                                url: 'helper/ping_contact.php',
                                data: {
                                    tenant: thisTenant,
                                    name: $('#messageDialog input[name=name]').val(),
                                    email: $('#messageDialog input[name=email]').val(),
                                    message: $('#messageDialog textarea[name=message]').val()
                                },
                                dataType: 'json',
                                type: 'POST',
                                success: function(j){
                                    if (j.success !== false) {
                                        alert("Message Sent!");
                                        d.dialog('close');
                                    } else {
                                        alert("Message Not Sent. Please Try Again.");
                                        d.dialog('close');
                                    }  
                                }
                            });
                        }
                    },
                        
                }
            });
             
            $(window).resize(function() {
                $("#ping_contact").dialog("option", "position", "center");
            });
      

            // add validation
            email = new LiveValidation('email', { validMessage: "Valid email address.", onlyOnBlur: false });
            email.add(SLEmail);      
        });
    }
});


