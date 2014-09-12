$(function(){
	if ($('#ping_admin').length == 1) {
		$('#ping_admin').click(function(e) {
			e.preventDefault();
			if ($('#messageDialog').length == 0) {
				var d = $('<div id="messageDialog" title="Send a ping to the administrator" style="line-height:25px;"><form><label for="name">Name:</label><input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" value="" style="width:250px;float:right;" /><br style="clear:both;" /><label for="email">Email:</label><input type="text" name="email" id="email" class="text ui-widget-content ui-corner-all" value="" style="width:250px;float:right;" /><br style="clear:both;" /><label for="message">Message:</label><input type="text" name="message" id="message" class="text ui-widget-content ui-corner-all" value="Unable to log in!" style="width:250px;float:right;" /></form></div>');
			} else {
				var d = $('#messageDialog');
			}
			d.dialog({
				autoOpen: true,
				height: 200,
				width: 350,
				modal: true,
				buttons: {
					Send: function() {
						var d = $(this);
						$.ajax({
							url: '/love/helper/ping_admin.php',
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
					},
					Cancel: function() {
						$(this).dialog('close');
					}
				}
			});
		});
	}
});

$(function() {
	branding.init();
	branding.brand();
});