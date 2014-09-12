<?php $admin_link = ADMIN_URL; ?>
 
<div id="header">
    <div id="user">
        <div id="userImage">
            <img src="<?php echo SERVER_URL; ?>thumb.php?t=lCH&src=<?php $front->getUser()->outPhoto(); ?>&w=50&h=50&zc=0" width="50" height="50" alt="profile" />
        </div>
        <div id="userInfo">
            <div class="hTitle">LoveMachine</div><br/>
            <div style="clear:both"></div>
            <span id="welcome-msg">Welcome <?php echo $front->getUser()->nicknameThenusername();?>!&nbsp;&nbsp;|&nbsp;&nbsp;<a href="settings.php">Settings</a>&nbsp;&nbsp;|&nbsp;&nbsp;<?php if ($front->getUser()->getCompany_admin()) : ?><a href="<?php echo $admin_link; ?>">Admin</a>&nbsp;&nbsp;|&nbsp;&nbsp;<?php endif; ?><a href="logout.php">Logout</a></span>
        </div>
      
    </div>
   
    <div id="logo">
      <img id="logo_img" src="images/transparent.gif" alt="Company Logo" />
    </div>
</div>
