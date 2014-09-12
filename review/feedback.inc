<?php
  if(isset($front)){
      $feedback_email = $front->getUser()->getUsername();
  } else {
      $feedback_email = $user->getUsername();
  }
?>
<div class="slide-out-div">
    <a class="handle">feedback</a>
    <h3>Leave Feedback</h3>
    <form method="post" action="">
      <p>
	<label for = "feedback-email">Email:</label><br />
	<input class="input" type = "text" id = "feedback-email" name = "email" value = "<?php echo $feedback_email; ?>" size = "30" />
      </p>
            <script type="text/javascript">
	      var username = new LiveValidation('feedback-email', {validMessage: "Valid email address."});
	      username.add(Validate.Email);
	    </script>
      <p>
	<label for = "feedback-message">Message:</label><br />
	<textarea id = "feedback-message" name = "message" cols = "30" rows = "8"></textarea>
      </p>
      <p class = "last">
	<input class="button" type = "submit" name = "feedback-submit" id = "feedback-submit" value = "Submit" />
      </p>
    </form>
</div>
 
