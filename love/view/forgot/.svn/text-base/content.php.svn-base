<div id="content">
    <div id="leftBorder"></div>
    <div id="contentBody">
        <div id="contentBodyLeft">
            <div class="hTitle hTitle2">Reset Your Password</div><br/>
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
                <h3>Forgot your password? It happens to the best of us.</h3>
                <form id="forgotForm" action="#" method="post">
                <?php if (!empty($msg)) { echo $msg; } ?>
                    <fieldset class="fieldset">
                        <div style="width: 250px; float: left">
                            <label class="label" for="username">Email</label><br/>
                            <input type="text" id="username" name="username" class="input" value="" />
                        </div>
                        <input type="submit" class="button" id="send" value="Send Mail" name="send" style="float: right; margin: 15px 20px 0 0;" /><br/>
                    </fieldset>
                </form>
                  </div>
		        <div style="clear:both;"></div>
                      <div id="contentBodyRight">
                      <div id="forgot-block">
		            <div>
                              <h3>Remember your password?</h3>
			         <a href="logout.php" title="Return to the login page" >Return to the login page</a>
		            </div>
		                <div>
                                  <h3>Need a login?</h3>
			             <a href="#" id="ping_admin" title="Send a message to the admin" >Send a message to the admin</a>
		                </div>
		                    <div>
			                 <h3>Want a SendLove?</h3>
			                 <a href="http://trial.sendlove.us/trial/" title="Get your own LoveMachine" >Get your own SendLove</a>
	                           </div>  
                    </div>
             </div>
        </div>
        <div style="clear:both;"></div>
    </div>
    <div id="rightBorder"></div>
    <div style="clear:both;"></div>
</div><!-- end of "content" div -->