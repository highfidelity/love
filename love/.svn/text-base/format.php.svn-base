<?php 
//  Copyright (c) 2010, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
?>


<div id="outside">

<!-- Welcome, login/out -->
       
        	<div id="welcome">
            <?php if ( isset($_SESSION['username'])) {

					if (empty($_SESSION['nickname'])){ ?>
                        Welcome, <? $_SESSION['username']?> | <a href="logout.php">Logout</a>
					<?php }else{ ?>
                        Welcome, <?php echo $_SESSION['nickname']; ?> | <a href="logout.php">Logout</a>
                        
                        <?php $love_count = weeklyLoveCount();
                        echo "<span id=\"weeklyLove\">You sent " . $love_count['love_sent'] . " love this week";
                                                
                        if(isset($_SESSION['company_id'])){
                        	echo ", company average " . $love_count['company_average'];	
						}
						
						echo "<span>";
						?>
                        
                        
					<?php }
				}else{?>
						<a href="login.php">Login</a>
                <?php } ?>

                <div id="tagline">Make people happy.  Find out what's happening at work.</div>
            </div>			
			    
    <div id="container">
    
    	<div id="left"></div>
        
<!-- MAIN BODY -->
        
        <div id="center">
        
<!-- LOGO -->
    <?php if(isset($front)){ ?>
    <div id="logo" <?php echo(($front->getCompany()->getLogo() != '') ? 'style = "background:url(' . $front->getCompany()->getLogo() . ') right top no-repeat;"' : ''); ?> >
    <?php } else {?>
    <div id="logo" style = "background:url(images/customLogo.png) right top no-repeat;" >
    <?php }?>
		<a href="index.php"><img src="images/transparent.gif" alt="SendLove" width="173px" height="91px"/></a>
    </div>
		
<!-- Navigation placeholder -->
            <div id="nav">    				
			<?php if ( isset($_SESSION['username'])) { ?>
           
              <a href="tofor.php"><?php if(INCLUDE_REVIEW_URL===true) { echo('Love & Review'); } else { echo('Love'); } ?></a> |
              <a href="settings.php">Settings</a> 
                
        <?php if (!empty($_SESSION['company_admin'])) { ?>
              | <a href="admin.php">Administration</a>
                <?php } ?>
            <?php } 
            if (isset($_SESSION['username'])) { 
            ?>
				<?php if(INCLUDE_WORKLIST_URL===true){?>
				| <a href="../worklist/worklist.php">Worklist</a>
				<?php }?>
				<?php if(INCLUDE_JOURNAL_URL===true){?>
				|<a href="../journal/">Journal</a>
				<?php }?>
			<?php }?>
            </div>
<!-- END Navigation placeholder -->
