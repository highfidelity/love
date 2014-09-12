$(function(){
	$("#company").autocomplete('helper/getcompanies.php', { max: 8 } );
    $("#company").result(function(event, data, formatted) {
        if ($("#company").val()) {
            $("#company_helper").text(data ? "Your request to join will be sent to the "+data+" admin." : "You are the first to enter this company name!");
        }
    });
    $("#company").blur(function(){ $(this).search(); });
   
    var username = new LiveValidation('username', {validMessage: "Valid email address."});
    username.add( Validate.Email );

    var password = new LiveValidation('password',{ validMessage: "You have an OK password.", onlyOnBlur: true });
    password.add(Validate.Length, { minimum: 5, maximum: 12 } );
    
    var confirmpassword = new LiveValidation('confirmpassword', {validMessage: "Passwords Match."}); 
    confirmpassword.add(Validate.Confirmation, { match: 'password'} );
    
    var nickname = new LiveValidation('nickname', {validMessage: "You have an OK Nickname."});                  
    nickname.add(Validate.Format, {pattern: /[@]/, negate:true});
	$('#signupForm').submit(function(){
		if($('#username').val().length > 0 && 
		   $('#password').val().length > 0){
			return true;
		} else {
			return false;
		}
	});
    $('#country').change(function(){
		$('#divProvider').removeClass("hide");
		$('#divProvider').addClass("show");
		$('#divProvider').block({ 
            message: 'Loading...' 
        });
		$.ajax({
            type: "POST",
            url: "helper/getsms.php",
            data: "c="+$('#country option:selected').attr("value"),
            dataType: "json",
            success: function(json) {
				$('#provider').empty();
                for (var i = 0; i < json.length; i++) {
                	$('#provider').append('<option value="'+json[i][0]+'">'+json[i][0]+'</option>');
                }
                $('#provider').append('<option value="--">(Other)</option>');
                $('#divProvider').unblock();
            }, 
            error: function(xhdr, status, err) {
            	$('#provider').empty();
            	$('#provider').append('<option value="--">(Other)</option>');
            	$('#divProvider').unblock();
            }
        });
	});
});