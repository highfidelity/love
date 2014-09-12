/***
 The sales jscript object is used to manage the paymentForm:
    - create the validation
    - submit the form using ajax
***/
var sales = {

    init: function() {
        sales.initPaymentForm();
        sales.formSubmit();
    },

    initPaymentForm: function() {
    
        sales.fname = new LiveValidation('fname', { onlyOnBlur: true });
        sales.lname = new LiveValidation('lname', { onlyOnBlur: true });
        sales.email = new LiveValidation('email', { onlyOnBlur: true });
        sales.phone = new LiveValidation('phone', { onlyOnBlur: true });
        sales.street = new LiveValidation('street', { onlyOnBlur: true });
        sales.city = new LiveValidation('city', { onlyOnBlur: true });
        sales.state = new LiveValidation('state', { onlyOnBlur: true });
        sales.zip = new LiveValidation('zip', { onlyOnBlur: true });
        sales.country = new LiveValidation('country', { onlyOnBlur: true });
        sales.acct = new LiveValidation('acct', { onlyOnBlur: true });
        sales.cvv2 = new LiveValidation('cvv2', { onlyOnBlur: true });
        
        sales.fname.add(Validate.Presence);
        sales.fname.add(Validate.Presence);
        sales.email.add(Validate.Presence);
        sales.email.add(Validate.Email);
        sales.phone.add(Validate.Presence);
        sales.street.add(Validate.Presence);
        sales.city.add(Validate.Presence);
        sales.state.add(Validate.Presence);
        sales.zip.add(Validate.Presence);
        sales.country.add(Validate.Presence);
        sales.acct.add(Validate.Presence);
        sales.cvv2.add(Validate.Presence);

    },
    
    sendPaymentInfo: function(fAfter) {
        
        $.getJSON('buylovemachine-json.php?action=send_payment_info',
            {
                domain: $('#domain').val(), 
                databaseName: $('#databaseName').val(), 
                fname: $('#fname').val(), 
                lname: $('#lname').val(),
                email: $('#email').val(),
                phone: $('#phone').val(),
                street: $('#street').val(),
                city: $('#city').val(),
                state: $('#state').val(),
                zip: $('#zip').val(), 
                country: $('#country').val(), 
                card_type: $('#card_type').val(), 
                acct: $('#acct').val(), 
                cvv2: $('#cvv2').val(), 
                exp_date: $('#exp_month').val() +''+ $('#exp_year').val(), 
                confirm: 1,
                xmlhttp: 1
            },
            function(json){
                if ( !json || json === null ) {
                    alert("json null in sendPaymentInfo");
                    if (fAfter) fAfter();
                    return;
                }
                if ( json.error ) {
                    $(".postInformation").html(json.errorMsg).removeClass('postInformationSuccess').addClass('postInformationError').show();
                    if (fAfter) fAfter();
                } else {
                    var msg = "<div>Transaction done !</div><div>Please save the following information:</div>" +
                                "<div>Date: "+ (new Date())  +"</div><div>Transaction ID:"+json.transactionID +"</div><div>"+json.warning +"</div>" ;
                    $('input[type=text]').val('');
                    $('.paypalFormArea').html(msg).addClass('postInformationSuccess');
                    if (fAfter) fAfter();
                }
            }
        );
        
    },

    block : function(bBlock){
        if (bBlock) {
            $.blockUI({
                message: '', 
                timeout: 20000, 
                overlayCSS: { opacity: 0.30 }
            });
        } else {
            $.unblockUI();
        }
    }, 
    
    formSubmit: function() {
    // ajax form for CC
        $('#buylove').submit(function(e) {
            $(".postInformation").html("").removeClass('postInformationError').removeClass('postInformationSuccess').hide()
            sales.block(true);
            e.preventDefault();
            clearTimeout($.emailTimer);
            if (LiveValidation.massValidate( [ sales.fname, sales.lname, sales.email, sales.phone, sales.street, 
                                                sales.city, sales.state, sales.zip, sales.country, sales.cvv2 ] )) {
                sales.sendPaymentInfo(function(){
                    sales.block(false);
                });
            } else {
                sales.block(false);
            }
        });
    }
};

$.whatsthis = {
    height: 150,
    width: 220,
    init: function() {
        var d = $('#whatsthisDialog');
        d.dialog({
            autoOpen: false,
            height: 'auto',
            width: 'auto',
            modal: true
        });
        $('.whatsthisTrigger').click(function() {
              d.dialog('open');
        })
    }
};

$(function() {
    sales.init();
	$.whatsthis.init();
	
});    