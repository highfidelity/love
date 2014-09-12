$(function(){
	$("#loginForm").submit(function(){
		$.blockUI({ message: login_message });
		var username = $('#username').val();
		var password = $('#password').val();
		var url = $('#loginForm').attr("action");
		if(username.length > 0 && password.length > 0){
			$.post(url, { "username": username, "password": password}, function(json){
				if(json.error == 1){
					$.unblockUI();
					$.blockUI({ message: error_message });
					$('#errorHolder').html("");
					$('#errorHolder').css({"display": "block"});
					var content = "<ul>";
					$.each(json.message, function(index, value){
						var row = "<li>"+value+"</li>";
						content += row;
					});
					content += "</ul>";
					$('#errorHolder').html(content);
					$.unblockUI();
				} else {
					$.unblockUI();
					$.blockUI({ message: success_message });
					window.location = love_location+"rewarder.php";
				}
			},"json");
		}
		return false;
		$.post(
				"", 
				  { "username": username, "password": password}, 
				  function(json){
					  if(json.error == 1){
						  $('#errorHolder').html("");
						  $('#errorHolder').css({"display": "block"});
						  var content = "<ul>";
						  $.each(json.message, function(index, value){
							  var row = "<li>"+value+"</li>";
							  content += row;
							});
							content += "</ul>";
							$('#errorHolder').html(content);
					  } else {
						  window.location = "http://dev.sendlove.us/~yani/11433/sendlove/rewarder.php";
					  }
					},
					"json"
					)
		  return false;
	});
});
