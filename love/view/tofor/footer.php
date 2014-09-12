<div id="footer">
	<span class="loves"><span id="totalLove"><?php echo $front->getLove()->getTotalLove(); ?></span> love sent</span>
	<p>&copy; <? echo date("Y"); ?> Below92, LLC &nbsp;| &nbsp;<a href="privacy.php" target="_blank">Privacy Policy</a> &nbsp;

	<?php if($_SERVER['SERVER_NAME'] == 'dev.sendlove.us' || $_SERVER['SERVER_NAME'] == 'www.sendlove.us' || $_SERVER['SERVER_NAME'] == 'sendlove.us') { ?>
  | 	&nbsp;<a href="http://svn.sendlove.us/" target="_blank">View the source code</a></p>
  	<?php } ?>
</div>