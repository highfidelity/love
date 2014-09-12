var rate;
$(function(){
    try() {
        $('#finish-period-popup').dialog({autoOpen: false, width: 400});
        
        $('#finish-period').click(function(){
        
            $('#finish-period-popup').dialog('open');
        });
        
        $('#finish-period-popup input[name="end-period"]').click(function(){
            
            if(checkFormValues()){
                confirmDialog("End review period confirm", 
                            "<span class = \"emp\">You are about to end this review period.<br />"
                            + "This action is irreversible.<br />"
                            + "Are you ready to proceed?</span>", endPeriod);
            }
        });
        
        if ($("#conversion-rate").length == 1) {
            rate = new LiveValidation('conversion-rate');
            rate.add( Validate.Numericality );
        }
        // to make IE happy
        // it only works if we apply throbber on visible element
        $('#throbber-holder').show();
        $("#throbber").throbber({bgopacity: 0});
        $('#throbber-holder').hide();
        
    } catch(exc){
        alert(exc);
    }
}); 

// checks if values in form are valid
function checkFormValues(){
    if (rate) {
        return LiveValidation.massValidate([rate]);
    }
    return true;
}

// calls php file to perform actual rewarder period ending
function endPeriod(){
    $('#finish-period-popup').dialog('close');
    $('#throbber-holder').show();
    $('#throbber').throbber('enable');
    var reset = $('input[name="reset-balances"]').attr('checked') ? 1 : 0;
    var conversion_rate = $('#conversion-rate').val();
    var signature = $('textarea[name="signature"]').val();

    $.post(
        'rewarder-json.php?action=end-period', 
         {reset: reset, conversion_rate: conversion_rate, signature: signature},
         function(json){
             $('#throbber-holder').hide();
             $('#throbber').throbber('disable');
             window.location.reload();
         });
}


function confirmDialog(titleString, message, callback){
    
    $('body').append(
    '<div id="message_container">' +
    '</div>');
    $('#message_container').attr('title', titleString);
    $('#message_container').html(message);
    
    $('#message_container').dialog({
        
        modal: true,
        buttons: {
            Ok: function() {
                if(callback) callback(true);
                $(this).dialog('destroy');
            },
        Cancel: function() {
            $(this).dialog('destroy');
        }
        
        },
        close: function(){
            
            $(this).dialog('destroy');
            $('#message_container').remove(); 
        }
    });
    
}