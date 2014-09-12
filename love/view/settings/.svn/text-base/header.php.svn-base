<?php $admin_link = ADMIN_URL; ?>

<div id="header">
    <div id="user">
        <div id="userImage">
            <img src="<?php echo Utils::getImageFromThumbs($front->getUser()->getPhoto(), 50, 50, 0); ?>" width="50" height="50" alt="profile" />
        </div>
        <div id="userInfo">
            <div class="hTitle">SendLove</div><br/>
            <div style="clear:both"></div>
            <span id="welcome-msg">
			Welcome <span class="nicknameAjax"><?php echo $front->getUser()->nicknameThenusername();?></span>
			!&nbsp;&nbsp;|&nbsp;&nbsp;<a href="tofor.php">Back to SendLove</a>
			<?php if (defined('SETTINGS_LINK_ENABLE')) { ?>
				&nbsp;&nbsp;|&nbsp;&nbsp;<a href="settings.php">Settings</a>			
			<?php } ?>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<?php if ($front->getUser()->getCompany_admin()) : ?>
				<a href="<?php echo $admin_link; ?>">Admin</a>&nbsp;&nbsp;|&nbsp;&nbsp;
			<?php endif; ?>
			<a href="logout.php">Logout</a>
			&nbsp;&nbsp;|&nbsp;&nbsp;Try our new 
            <?php include('view/static/marklet.php'); ?>
			</span>
        </div>      
    </div>
    <div id="tabs">
<?php   //Allow to turn off tabs if other applications are not enabled
        if (!defined('LOVE_TABS_DISABLED')) { ?>
        <ul id="pages">
          <li <?php echo $tab == 0 ? 'class="current"' : ''; ?> ><a href="?tab=0" class="love">Love</a></li>
        </ul>
<?php } ?>
    </div>
    <div id="logo">
      <img id="logo_img" src="images/transparent.gif" alt="Company Logo"/>
    </div>
</div>