<?php
//  vim:ts=4:et

//  Copyright (c) 2009-2010, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com
//
?>
<?php
/**
 * Sms_Numberlist
 */
require_once 'lib/Sms/Numberlist.php';
$country = $front->getUser()->getCountry(); 
$phone = $front->getUser()->getPhone(); 
$provider = $front->getUser()->getProvider();
$confirmation_code = $front->getUser()->getConfirm_phone();
?>
<!-- SMS include -->
            <div id="sms">
                <label for="phone">Mobile device number</label>
                <select id="country" name="country" style="">
                    <option value="">International Code</option>
                    <?php foreach (Sms_Numberlist::$codeList as $country_code => $phone_code) {
                        $country_name = $countrylist[$country_code];
                    ?>
                    <option value="<?php echo $country_code;?>"<?php echo ($country == $country_code) ? ' selected="selected"' : ''; ?>><?php echo $country_name . ' (+' . $phone_code . ')'; ?></option>
                    <?php } ?>
                </select>
                <input type="text" name="phone" id="phone" size="10" value="<?php echo $phone ?>" />
                <input name="phone_edit" type="hidden" id="phone_edit" value="0" />

                <div id="sms-provider" <?php echo ((empty($country) || $country == '--') ? 'style="display:none"' : '') ?>>
                    <label for="provider">Wireless provider</label>
                    <select id="provider" name="provider">
                        <?php if (empty($country) || $country == '--') { ?>
                        <option value="Select Country">Please select a Country</option>
                        <?php } else { ?>
                        <option value="Wireless Provider">(Other)</option>
                        <?php } ?>
                    </select>
                    <input name="stored-provider" type="hidden" id="stored-provider" value="<?php echo $provider; ?>" />
                </div>

                <div id="sms-other" <?php echo ((empty($provider) || $provider{0}!='+')?'style="display:none"':'') ?>>
                    <label>SMS Address</label>
                    <input type="text" id="smsaddr" name="smsaddr" size="35" value="<?php echo (!empty($smsaddr)?$smsaddr:((!empty($provider) && $provider{0} == '+')?substr($provider, 1):'')) ?>" />
                    <em id="sms_helper">Please enter the email address for sending text messages.</em>
                </div>

                <div id="sms-confirmation" <?php echo ((empty($confirmation_code)) ? 'style="display:none"' : '') ?>>
                    <label>Confirmation Code</label>
                    <input type="text" id="confirmation" name="confirmation" size="5" value="" />
                    <em id="sms_helper">Please enter confirmation code you received in text message</em>
                </div>

            </div>
