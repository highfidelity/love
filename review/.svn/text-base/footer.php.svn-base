<?php
//  Copyright (c) 2009-2010, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
?>

<!-- This page will only be included on standalone mode -->

    <!-- break contained floats -->        
	<div style="float:none; clear:both;"></div>

    <!-- END MAIN BODY - Close DIV center -->
	</div>
	<div id="right"></div>

    <!-- break 3-col float -->        
	<div style="float:none; clear:both;"></div>

    <!-- Close DIV container -->
	</div>
	<div id="footer">
		<?php
			$res = preg_split('%/%', $_SERVER['SCRIPT_NAME']);
			$filename = array_pop($res);
			$repname = array_pop($res);
			$viewSourceLink = "http://svn.sendlove.us/";
		?>
		<div class="copyText">&copy;&nbsp;<? echo date("Y"); ?> <a href="http://www.lovemachineinc.com" target="_blank">LoveMachine, Inc.</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="privacy.php" target="_blank">Privacy Policy</a>&nbsp;&nbsp;
		<?php if($_SERVER['SERVER_NAME'] == 'dev.sendlove.us' || $_SERVER['SERVER_NAME'] == 'www.sendlove.us' || $_SERVER['SERVER_NAME'] == 'sendlove.us'):?>
		|&nbsp;&nbsp;<a href="<?php echo $viewSourceLink;?>" target="_blank">View the source code</a></div>
		<?php endif;?>
		<div class="loves"><img src="images/LMLogo3.png"/></div>
	</div>

	<!-- Close DIV outside -->
	</div id="outside">
</body>
</html>
