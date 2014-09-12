<?php $admin_link = ADMIN_URL; ?>

<div id="header">
    <div id="user">
        <div id="userImage">
            <img src="<?php echo Utils::getImageFromThumbs($front->getUser()->getPhoto(), 50, 50, 0); ?>" width="50" height="50" alt="profile" />
        </div>
        <div id="userInfo">
            <div class="hTitle">LoveMachine</div><br/>
            <div style="clear:both"></div>
            <span id="welcome-msg">Welcome <?php echo $front->getUser()->nicknameThenusername();?>!&nbsp;&nbsp;|&nbsp;&nbsp;<a href="settings.php">Settings</a>&nbsp;&nbsp;|&nbsp;&nbsp;<?php if ($front->getUser()->getCompany_admin()) : ?><a href="<?php echo $admin_link; ?>">Admin</a>&nbsp;&nbsp;|&nbsp;&nbsp;<?php endif; ?><a href="logout.php">Logout</a></span>
        </div>
      
    </div>
    <div id="tabs">
<?php   //Allow to turn off tabs if other applications are not enabled
        if (!defined('LOVE_TABS_DISABLED')) { ?>
        <ul id="pages">
            <li <?php echo $tab == 0 ? 'class="current"' : ''; ?> ><a href="?tab=0" class="love">Love</a></li>
            <li <?php echo $tab == 1 ? 'class="current"' : ''; ?> ><a href="?tab=1" class="self_review">Self Review</a></li>
            <li <?php echo $tab == 2 ? 'class="current"' : ''; ?> ><a href="?tab=2" class="peer_review">Peer Review</a></li>
        </ul>
<?php } ?>
    </div>
    <div id="logo">
      <img id="logo_img" src="images/transparent.gif" alt="Company Logo"/>
    </div>
</div>
