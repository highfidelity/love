    <div id="settings">
        <div id="leftBorder"></div>
        <div id="contentBody">
            <div id="contentBodyLeft">
                <div class="hTitle hTitle2">Edit Account Settings</div>
                <div id="ajaxresponse"></div>
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

                    <form action="settings.php" id="test" method="POST">
                        <fieldset class="fieldset">
                            <div class="column">
                                <h2>Account Settings</h2>
                                <label for="nickname">Nickname</label>
                                <input name="nickname" type="text" id="nickname" autocomplete="off" value="<?php $front->getUser()->outNickname();?>" size="35" maxlength="<?php echo MAX_NICKNAME_CHARS; ?>" />
<?php if (!defined('LDAP_ENABLED') ||  LDAP_ENABLED!==1 ) { ?>
                                <label for="oldpassword">Current Password</label>
                                <input type="password" name="oldpassword" id="oldpassword" autocomplete="off" value="" size="35" maxlength="12" />
                                <label for="newpassword">New Password</label>
                                <input type="password" name="newpassword" id="newpassword" autocomplete="off" value="" size="35" maxlength="12" />
                                <label for="confirmpassword">Re-enter New Password</label>
                                <input type="password" name="confirmpassword" id="confirmpassword" size="35" maxlength="12" />
<?php } ?>
                                <br>
                                <?php if (defined('LINKEDIN_API_KEY_PRIVATE')) { require_once('oauth-inc.php'); } ?>
                            </div>
<?php if (SMS_ENABLED): ?>
                            <div class="column">
                                <h2>Notification Settings</h2>
                                <?php require_once('sms-inc.php'); ?>
                                <div style="clear: both;"></div>
                                <!-- <input type="checkbox" style="margin-top: 12px;" <?php echo $front->getUser()->getSend_love_via_email() == 'Y' ? 'checked="checked"' : ''; ?> id="allow_email_sendlove" name="allow_email_sendlove" value="Y" style="float: left; width: 30px;" />
                                <label style="float: left; clear: none; display: inline;">Allow sending love via email</label> -->
                            </div>
<?php endif; ?>                            
                            <div class="column" id="avatar">
                                <h2>Profile Picture</h2>
                                <div id="profilepicture">
                                    <img id="picture" src="<?php echo Utils::getImageFromThumbs($front->getUser()->getPhoto(), 120, 110, 0); ?>" width="120" height="110" />
                                </div>
                                <span class="picture_info uploadTrigger">Click here to change it</span>
                                <span style="display: none;" class="LV_validation_message LV_invalid upload"></span>
                                <fieldset style="margin-top: 30px; border: 0 !important;">
                                    <input id="update" name="update" type="submit" value="Update settings" />
                                    <input id="delete" name="delete" type="submit" value="Delete my account" />
                                </fieldset>
                            </div>
                        </fieldset>
                     </form>
                </div>
                <div style="clear:both;"></div>
            </div>
            <div id="contentBodyRight">
            </div>
            <div style="clear:both;"></div>
        </div>
        <div id="rightBorder"></div>
        <div style="clear:both;"></div>
    </div><!-- end of "content" div -->    
