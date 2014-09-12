<div id="content">
    <div id="leftBorder"></div>
    <div id="contentBody">
        <div id="contentBodyLeft">
            <div class="hTitle hTitle2">Reset Password</div><br/>
            <div style="clear:both;"></div>
            <div id="contentBodyFormLeft" style="margin-bottom: 20px;">
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
                <form action="resetpass.php" method="post" name="frmlogin" onSubmit="return validate();">
                <?php if (!empty($msg)) { echo $msg; } ?>
                    <fieldset class="fieldset">
                        <div style="width: 250px; float: left">
                            <label class="label" for="username">Email</label><br/>
                            <input type="text" id="username" name="username" class="input" readonly="" value="<?php if (array_key_exists('un',$_REQUEST)) { echo(base64_decode($_REQUEST['un'])); } ?>" />
                            <label class="label" for="password">New Password</label><br/>
                            <input type="password" id="password" name="password" autocomplete="off" class="input" value="" />
                            <script type="text/javascript">
                                var password = new LiveValidation('password',{ validMessage: "You have an OK password.", onlyOnBlur: true });
                                password.add(Validate.Length, { minimum: 5, maximum: 12 } ); 
                            </script>
                            <label class="label" for="confirmpassword">Confirm Password</label><br/>
                            <input type="password" id="confirmpassword" autocomplete="off" name="confirmpassword" class="input" value="" />
                            <script type="text/javascript">
                                 var confirmpassword = new LiveValidation('confirmpassword', {validMessage: "Passwords Match."});
                                  //confirmpassword.add(Validate.Length, { minimum: 5, maximum: 12 } ); 
                                 confirmpassword.add(Validate.Confirmation, { match: 'password'} );
                            </script>
                        </div>
                        <input type="hidden" name="token" value="<?php echo($_REQUEST['token']); ?>" />
                        <input type="submit" class="button lgbutton" value="Reset Password" alt="Reset Password" name="submit" style="float: right; margin: 15px 20px 0 0;" />
                    </fieldset>
                </form> 
            </div>
        </div>
        <div style="clear:both;"></div>
    </div>
    <div id="rightBorder"></div>
    <div style="clear:both;"></div>
</div><!-- end of "content" div -->
