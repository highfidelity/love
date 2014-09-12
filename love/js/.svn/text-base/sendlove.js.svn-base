//  Copyright (c) 2009, LoveMachine Inc.  
//  All Rights Reserved.  
//  http://www.lovemachineinc.com
var smsCountry = '';
var smsProviderList = new Array();

function smsRefreshPhoneHelper() {
    var txt = '';
    if ($("#provider").val() != '--') {
        var prov = $("#provider").val();
        if (smsProviderList[prov] != undefined) {
            var phone = $("#phone").val().replace(/\D/g,'');
            txt = smsProviderList[prov].replace(/{n}/, phone);
        }
    } else if ($("#smsaddr").val()) {
        txt = $("#smsaddr").val();
    }
    if ($('#phone').val() != '' && txt != '') {
        $("#phone_helper").text("Your love will be sent to "+txt);
    } else {
        $("#phone_helper").text("Receive love as text messages on your phone.");
    }
}

function smsRefreshProvider(init) {
    if (!init) $("#phone_edit").val('1');
    if (smsCountry != $("#country").val()) {
        smsCountry = $("#country").val();
        if (smsCountry == '--') {
            $("#sms-provider").hide();
            $("#sms-other").hide();
        } else {  
  
            var country = $("#country option:selected").text();
            var len = country.length;

            var el = $("#provider");
            el.empty();
            $("#sms-provider").show();
            $.ajax({
                type: "POST",
                url: "helper/getsms.php",
                data: "c="+smsCountry,
                dataType: "json",
                success: function(json) {
                    smsProviderList = new Array();
                    for (var i = 0; i < json.length; i++) {
                        smsProviderList[json[i][0]] = json[i][1];
                        if (smsProvider && smsProvider == json[i][0]) {
                            el.append('<option value="'+json[i][0]+'" selected>'+json[i][0]+'</option>');
                        } else {
                            el.append('<option value="'+json[i][0]+'">'+json[i][0]+'</option>');
                        }
                    }
                    if (smsProvider && smsProvider[0] == '+') {
                        el.append('<option value="--" selected>(Other)</option>');
                        $("#sms-other").show();
                    } else {
                        el.append('<option value="--">(Other)</option>');
                    }
                }, 
                error: function(xhdr, status, err) {
                    smsProviderList = new Array();
                    el.append('<option value="--">(Other)</option>');
                }
            });
        }
    }

    if ($("#country").val() == '--' || $("#provider").val() == '--') {
        $("#sms-other").show();
    } else {
        $("#sms-other").hide();
    }
    smsRefreshPhoneHelper();
}

function smsUpdatePhone(filter) {
    $("#phone_edit").val('1');
    var phone = $("#phone").val();
    if (filter) {
        $("#phone").val(phone.replace(/\D/g,''));
    }
    smsRefreshPhoneHelper();
}

$(document).ready(function(){
    $("#revoke_linkedin").click(function() {
        $.post("settingsAjax.php", {action: "revoke"} );
        $("#content_linkedin").remove();
        $("#sync_linkedin").removeClass("hide");
        $("#revoke_linkedin").addClass("hide");
    });
    $("#phone").blur(function() { smsUpdatePhone(true); });
    $("#phone").keypress(function() { smsUpdatePhone(false); });
    $("#smsaddr").blur(function() { smsRefreshPhoneHelper(); });
    $("#country, #provider, #smsaddr").change(function() { smsRefreshProvider(); });
    smsRefreshProvider(true);
});
