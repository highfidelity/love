$(function(){
    if ($('#ping_admin').length == 1) {
    
        $('#ping_admin').click(function(e) {
            e.preventDefault();
            if ($('#messageDialog').length == 0) {
                var d = $('<div id="messageDialog" title="Send a ping to the administrator" style="line-height: 30px; padding: 10px; "><form><label for="name">Name:</label><input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" value="" style="width:250px;float:right;" /><br style="clear:both;" /><label for="email">Email:</label><input type="text" name="email" id="email" class="text ui-widget-content ui-corner-all" value="" style="width:250px;float:right;" /><br style="clear:both;" /><label for="message">Message:</label><input type="text" name="message" id="message" class="text ui-widget-content ui-corner-all" value="Unable to log in!" style="width:250px;float:right;" /></form></div>');
            } else {
                var d = $('#messageDialog');
            }
      
            var email;   // keep this local variable here (required for closure)

            d.dialog({
                autoOpen: true,
                height: 'auto',
                width: 350,
                modal: true,
                buttons: {
                    Send: function() {
                        if (email.validate()) {
                            var d = $(this);
                            $.ajax({
                                url: 'helper/ping_admin.php',
                                data: {
                                    name: $('#messageDialog input[name=name]').val(),
                                    email: $('#messageDialog input[name=email]').val(),
                                    message: $('#message').val()
                                },
                                dataType: 'json',
                                type: 'POST',
                                success: function(j) {
                                    if (j.success == false) {
                                        alert(j.message);
                                    }
                                    d.dialog('close');
                                }  
                            });
                        }
                    },
                    Cancel: function() {
                        $(this).dialog('close');
                    }
                }
            });
      

            // add validation
            email = new LiveValidation('email', { validMessage: "Valid email address.", onlyOnBlur: false });
            email.add(SLEmail);      
        });
    }
});

$(function() {
    branding.init(); // If succeed, apply main branding and additional parts
    branding.brand();
});
