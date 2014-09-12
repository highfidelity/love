<div id="content">
    <div id="leftBorder"></div>
    <div id="contentBody">
      <div id="contentBodyLeft">
        <div class="hTitle hTitle2">Login to SendLove</div><br/>
        <div style="clear:both;"></div>
        <div id="contentBodyFormLeft">
            <?php if($front->getError()->getErrorFlag()): ?>
			<div class="ui-widget" style="width: 300px;">
				<div style="padding: 0pt 0.7em;" class="ui-state-error ui-corner-all"> 
					<p style="line-height:100%;color:#CD0A0A;margin:0;"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span> 
					<strong>The following errors occured:</strong></p>
		            <ul>
		              <?php foreach($front->getError()->getErrorMessage() as $message):?>
		                <li><?php echo $message;?></li>
		              <?php endforeach;?>
		            </ul>
				</div>
			</div>
            <?php endif;?>
          <form id="loginForm" method="post" action="login.php">
            <fieldset class="fieldset">
              <div style="width: 250px; float: left">
              
              <label class="label" for="username">Username</label><br/>
              <input type="hidden" value="<?php echo $redir;?>" id='redir' name='redir'/>
              <input class="input" type="text" id="username" name="username" value="" />
              
              <label class="label" for="password">Password</label><br/>
              <input class="input" type="password" id="password" name="password" value="" />
              </div>
              <input type="submit" class="button" name="login" value="Login" id="login" />
            </fieldset>
          </form>
        </div>
        <div id="contentBodyFormRight">
		  <!-- Google Login deactivated
          <div id="errorHolderRight"></div>
          <form id="GoogleLoginForm" method="post" action="login.php">
                <fieldset class="fieldset">
                    <label for="googleLogin">Google Login</label><br/>
                    <input type="text" id="googleLogin" class="input google" name="googleLogin" value="" />

                    <input type="submit" class="button" name="gLogin" id="gLogin" value="Google Login" />
                </fieldset>
          </form>
		  -->
        </div>
        <div style="clear:both;"></div>
      </div>
      <div id="contentBodyRight">   
        <div id="forgot-block">
			<div>
		        <h3>Forgot your password?</h3>
		        <a href="forgot.php">Reset your password</a>
			</div>
			
			<div>
				<h3>Need a login?</h3>
				<a href="#" id="ping_admin">Send a message to the admin</a>
			</div>
			<div>
				<h3>Want a SendLove?</h3>
				<a href="http://trial.sendlove.us/trial/">Get your own SendLove</a>
			</div>
        </div> 
      </div>
      <div style="clear:both;"></div>
    </div>
    <div id="rightBorder"></div>
    <div style="clear:both;"></div>
</div><!-- end of "content" div -->

