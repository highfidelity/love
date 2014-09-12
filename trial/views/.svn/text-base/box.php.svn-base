<?php
//
//  Copyright (c) 2012, Below92 LLC.
//  All Rights Reserved. 
//  http://www.sendlove.us
//

?>

<div class="box fix-footer">
	<div class="header">
	    <ul>
	        <li data-contentClass="how" class="active headerSwitch"><span>How does it work?</span></li>
	        <li data-contentClass="why" class="headerSwitch"><span>Why does it work?</span></li>
	        <li data-contentClass="much" class="headerSwitch"><span>What does it cost?</span></li>
	    </ul>
	</div>
	<div class="content how">
		<ul>
            <li class="height40 first selected">
                You pick a date range for your Recognition Period
                <img class="productshot" src="images/how1.png" />
            <li class="height60">
                Team members send short messages recognizing each other, at any time.
                The team can see all the messages being sent 
                <img class="productshot" src="images/how2.png" />
            </li>
            <li class="height80">
                At the end of the period, you review the results and
                use the data to inform your company's existing bonus
                program (ie gift cards, dinners, cash, vacation days, etc)
                <img class="productshot" src="images/how3.png" />
            </li>
		</ul>
	</div>
	<div class="content why">
		<ul>
			<li class="height80 first selected">
                Employees recognize one another every day
                ("Thanks Bob, Just what I needed!"). A 'love'
                message can be sent even more easily than an 
                an email, often in under five seconds
                <img class="productshot" src="images/why1.png" />
            </li>
			<li class="height60">
                Public recognition from peers is the highest form of
                praise, and SendLove enables you to measure it
                <img class="productshot" src="images/why2.png" />
            </li>
			<li class="height60">
                Empowering team members to reward each other 
                helps organizations work better and stay informed
                <img class="productshot" src="images/why3.png" />
            </li>
		</ul>
    </div>
    <div class="much">
		<ul>
	        <li>
                $5.00 per person / month
            </li>
    	    <li>
                Month to Month, no obligation
            </li>
    	    <li>
                Free 30 day trial
            </li>
	    </ul>
    </div>
    <div class="content signup">
		  <form id="lovesignup" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">			                
            <div id="form-contents">
  			<label>Your company name</label>
  			    <div id="inst-name-block" style="float:left;">
  			        <input type="text" id="instance-name"/><div style="display:inline;">.sendlove.us/<br/></div>
  			    </div>
                       <div style="clear:both;"></div>
                     <div style="text-align:center"><span id="trial-text2">&nbsp;(Please enter only alphanumeric characters i.e., symbols such as &nbsp; #$%_ &nbsp;are invalid)</span></div>
                       <div style="clear:both;"></div><br/>
  			<div>
  				<label>Your name</label>
  	            <input type="text" id="name" />
              </div>
  			<div>
                  <label>Your email</label>
                  <input type="text" id="email" />
              </div>
              <div>
                  <label>Your password</label>
                  <input type="password" id="password" />
              </div>
  			<div>
                  <label>Repeat password</label>
                  <input type="password" id="password-repeat" style="margin-bottom:8px;"/>
              </div>
  			<div style="margin-bottom:20px;text-align:center"><span id="signup-sent" class="LV_validation LV_valid" style="display:none;">
  			A confirmation email has been sent, signup will finish once you confirm your address.</span></div>
              
                <?php
  				// take the referer
  				// Check for value before using it to avoid warnings in logs
  				$thereferer = strtolower(array_key_exists('HTTP_REFERER',$_SERVER)?$_SERVER['HTTP_REFERER']:'');
  				// see if it comes from google
  				if (strpos($thereferer,"google")) {
  					// delete all before q=
  					$a = substr($thereferer, strpos($thereferer,"q="));		
  					// delete q=
  					$a = substr($a,2);
  					// delete all FROM the next & onwards
  					if (strpos($a,"&")) {
  						$a = substr($a, 0,strpos($a,"&"));
  					}	
  					// we have the results.
  					$source="google";
  					$adword = urldecode($a);
					
  				} else {
  					$source="Other";
  					$adword="null";
  				}				
			
  			?>
                <input type="hidden" id="source" value="<?php echo $source; ?>" name="source" />
                <input type="hidden" id="adword" value="<?php echo $adword; ?>" name="adword" />
  			<input type="submit" name="submit" id="submit" value="" />
  			<div id="disclaimer" style="text-align:center"><span id="trial-text3">By selecting the <strong>Signup</strong> button, I agree to the <a href="termsandconditions.php" target="_blank">Terms and Conditions</a></span></div>
  		</div>
    	</form>
	</div>
</div>