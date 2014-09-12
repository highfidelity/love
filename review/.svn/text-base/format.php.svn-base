<?php
//  Copyright (c) 2010, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com
?>

<!-- This page will only get loaded on standalone mode -->

<div id="outside">
<!-- Welcome, login/out -->

	<div id="welcome">
		<?php if ( isset($_SESSION['username'])) {
			if (empty($_SESSION['nickname'])){ ?>
				Welcome, <?php echo $_SESSION['username']; ?> | <a href="logout.php">Logout</a>
			<?php }else{ ?>
				Welcome, <?php echo $_SESSION['nickname']; ?> | <a href="logout.php">Logout</a>
			<?php } ?>
			<?php }else{?>
				<a href="login.php">Login</a>
			<?php } ?>
		<div id="tagline">Reward your coworkers.</div>
	</div>

	<div id="container">
		<div id="left"></div>

	    <!-- MAIN BODY -->
		<div id="center">

	    <!-- LOGO -->
		<div id="stats">
			<span id='stats-text'></span>
		</div>

	    <!-- Navigation placeholder -->
		<div id="nav">
			<?php if (isset($_SESSION['username'])) { ?>
	
			<a href="<?php echo SERVER_BASE ?>/love/" class="iToolTip menuLove" target="_blank">Love</a> |
			<a href="<?php echo SERVER_BASE ?>/review/" class="iToolTip menuRewarder">Review</a>
			<?php } ?>
		</div>
        <!-- END Navigation placeholder -->

