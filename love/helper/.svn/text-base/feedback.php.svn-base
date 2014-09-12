<?php
    include("../config.php");
    include("../send_email.php");

    if(isset($_POST['message'])){
        //sending feedback email
        $subject = "Feedback for ".APP_NAME;
        $email = isValidEmail(trim($_POST['email'])) ? trim($_POST['email']) : FEEDBACK_EMAIL; 
        $message = strip_tags($_POST['message']);

        sl_send_feedback($email, $subject, $message);
    }

    function isValidEmail($email){
        return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
    }
?> 
