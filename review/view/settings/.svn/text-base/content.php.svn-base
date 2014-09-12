<div id="content">
    <div id="leftBorder"></div>
    <div id="contentBody">
      <div id="nav">
        <a href="tofor.php">Send Love</a>
      </div>
      <div id="contentBodyLeft">
        <h1>Edit Account Settings</h1>
        <div id="contentBodyFormLeft">
          <div id="errorHolder"></div>
          <form id="settingsForm" method="post" action="<?php echo LOGIN_APP_URL; ?>update">
            <fieldset id="settings" class="fieldset">
              
              <label class="label" for="nickname">Nickname</label>
              <input class="input" type="text" id="nickname" name="nickname" value="<?php $front->getUser()->outNickname();?>" />
              
              <label class="label" for="company">Company</label>
              <input class="input" type="text" id="company" name="company" value="<?php $front->getUser()->outCompany_name();?>" />
              
              <label class="label" for="phone">Cell Phone Number</label>
              <input class="input" type="text" id="phone" name="phone" value="<?php $front->getUser()->outPhone();?>" />
              
              <label class="label" for="country">Country</label>
              <select name="country" id="country">
              <?php
              echo Utils::getCountryList($front->getUser()->getCountry()); 
              ?>
              </select>
              <div class="<?php echo strlen($front->getUser()->getProvider()) > 1 ? "show" : "hide" ?>">
                  <label class="label" for="provider">Wireless Provider</label>
                  <select name="provider" id="provider">
                  <?php
                  echo Utils::getProviderList($front->getUser()->getCountry(),
                                              $front->getUser()->getProvider()); 
                  ?>
                  </select>
              </div>
              
              <label class="label" for="password">Current Password</label>
              <input class="input" type="password" id="password" name="password" value="" />
              
              <label class="label" for="newpassword">New Password</label>
              <input class="input" type="password" id="newpassword" name="newpassword" value="" />
              
              <label class="label" for="newpasswordconfirm">Re-enter New Password</label>
              <input class="input" type="password" id="newpasswordconfirm" name="newpasswordconfirm" value="" />
              
              <label class="label" for="allow_email_love">Allow sending love via email</label>
              <input class="input" type="checkbox" id="allow_email_love" name="allow_email_love" />
              
              <label class="label" for="skill">Skill</label>
              <input class="input" type="text" id="skill" name="skill" value="" />
              
              <label class="label" for="team">Team</label>
              <input class="input" type="text" id="team" name="team" value="" />
              
              <input type="submit" class="button" name="update" value="Update" id="update" />
              <input type="submit" class="button" name="delete" value="Delete My Account" id="delete" />
              
              <h1 id="shareLoveH1">Share the Love</h1>
              
              <label class="label" for="invite">Invite a Co-worker</label>
              <input class="input" type="text" name="invite" id="invite" />
              
              <input type="submit" class="button" name="inviteButton" value="Invite" id="inviteButton" /> 
            </fieldset>
          </form>
        </div>
        <div id="contentBodyFormRight">
        </div>
        <div style="clear:both;"></div>
      </div>
      <div id="contentBodyRight">
        <!-- LOGO -->
        <div id="logo" <?php echo(($front->getCompany()->getLogo() != '') ? 'style = "background:url(' . $front->getCompany()->getLogo() . ') right top no-repeat;"' : ''); ?> >
          <a href="index.php"><img src="images/transparent.gif" alt="SendLove" width="173px" height="91px"/></a>
        </div>
        <label class="label" for="picture">
          Photo
        </label>
        <img id="picture" src="thumb.php?t=rsc&src=<?php $front->getUser()->outPhoto();?>&w=160&h=150&zc=0" />
        <span class="LV_validation_message LV_invalid upload" style="display: none;"></span>        
      </div>
      <div style="clear:both;"></div>
    </div>
    <div id="rightBorder"></div>
    <div style="clear:both;"></div>
</div>
