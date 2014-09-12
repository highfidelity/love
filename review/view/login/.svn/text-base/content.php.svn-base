<div id="content">
    <div id="leftBorder"></div>
    <div id="contentBody">
      <div id="contentBodyLeft">
        <h1>Login to LoveMachine</h1>
        <div id="contentBodyFormLeft">
            <?php if($front->getError()->getErrorFlag()): ?>
            <div id="errorHolder">
                <ul>
                  <?php foreach($front->getError()->getErrorMessage() as $message):?>
                    <li><?php echo $message;?></li>
                  <?php endforeach;?>
                </ul>
            </div>
            <?php endif;?>
          <form id="loginForm" method="post" action="login.php">
            <fieldset class="fieldset">
              
              <label class="label" for="username">Username</label>
              <input class="input" type="text" id="username" name="username" value="" />
              
              <label class="label" for="password">Password</label>
              <input class="input" type="password" id="password" name="password" value="" />
              
              <input type="submit" class="button" name="login" value="Login" id="login" />
            </fieldset>
          </form>
        </div>
        <div id="contentBodyFormRight">
		  <!-- Google Login Deactivated
          <div id="errorHolderRight"></div>
          <form id="GoogleLoginForm" method="post" action="login.php">
              <fieldset>
                <label for="googleLogin">Google Login</label>
                <input type="text" id="googleLogin" class="input google" name="googleLogin" value="" />
                
                <input type="submit" class="button" name="gLogin" id="gLogin" value="Google Login" />
              </fieldset>
          </form>
		  -->
        </div>
        <div style="clear:both;"></div>
      </div>
      <div id="contentBodyRight">
        
        <h3>Need a login?</h3>
        <a href="#" id="ping_admin">Send a message to the admin</a>
        
        <h3>Forgot your password?</h3>
        <a href="<?php echo LOVE_LOCATION; ?>/forgot.php">Recover it here</a>
			
		<h3>Want a LoveMachine?</h3>
		<a href="http://www.lovemachineinc.com/trial/">Get your own LoveMachine</a>
        
      </div>
      <div style="clear:both;"></div>
    </div>
    <div id="rightBorder"></div>
    <div style="clear:both;"></div>
</div>
