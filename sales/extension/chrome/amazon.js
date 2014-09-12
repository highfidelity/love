var addGift = function(redeemInfo) {
    var amount = Math.round( redeemInfo.amount * 100 ) / 100 ;
    $("#s-amount-Email").focus().val(amount);
    $("#s-email-Email").focus().val(redeemInfo.dest_email);
    $("#s-to-Email").focus().val(redeemInfo.toName);
    $("#s-from-Email").focus().val(redeemInfo.instance);
    $("#s-message-Email").focus().val(redeemInfo.comment);
    
    $("#s-amount-Email").focus();
    $("#add-to-cart-Email").click();
    var stop = false;
    while (stop == false) {
        debugAlert($("#gcOrderCartItems .gcOrderCard").length + redeemInfo.dest_email);
        stop = true;
    }
},
debugAlert = function(mes) {
    //alert(mes);
},
            
addGiftLoop = function(redeemInfoArray, current, alreadySent, fAfter) {
    debugAlert(current + "*"+ alreadySent);
        
    setTimeout(function() {
        var redeemInfoArrayLocal = redeemInfoArray,
            currentLocal = current,
            oRedeemInfo = redeemInfoArrayLocal[currentLocal];
        if (alreadySent == false) {
            addGift(oRedeemInfo);
        }
        if ( currentLocal < redeemInfoArrayLocal.length) {
                addGiftLoop(redeemInfoArrayLocal, currentLocal+1, false, fAfter);
        } else {
            if (fAfter) {
                fAfter();
            }
        }
    },1000);
    
};


chrome.extension.onRequest.addListener(function(request, sender, sendResponse) {
    var mes = sender.tab ?
                "from a content script:" + sender.tab.url :
                "from the extension";
    debugAlert("amazon.js  "+mes+"* action:"+request.action+"* aRedeemInfo:"+request.aRedeemInfo);
    if ( request.action== "toAmazon" && request.aRedeemInfo ) {
        if ($("#gcOrderCartItems .gcOrderCard").length > 0) {
            alert("Gift Card Order not empty, clear it and retry!");
            return; 
        }
        addGiftLoop(request.aRedeemInfo, 0, false, function() {
            sendResponse({
                action: "toLM",
                answer: "addGiftDone"
            });
        });
    } else {
      sendResponse({error: "aRedeemInfo empty in amazon.js"}); 
    }
});
          
var sendToBackgroundPage = function(aRedeemInfo) {
        try {
            chrome.extension.sendRequest({
                action: "toAmazon",
                aRedeemInfo: aRedeemInfo
            }, function(response) {
                    debugAlert("sendToBackgroundPage in amazon.js "+response.answer);
            });
        } catch(ee){
            alert(ee);
        }               
};     
        
$(function(){
    try {
            $(".amazonGiftPopup").attr("onclick","");
            $("#amazonGiftPopup2").click(function(){  
                var req = new Array();
                $(".rowRedeemRequests").each(function(){
                    debugAlert("oThis.amazonSendRedeemInfo();"+$("td",this).text());
                    req.push({
                        amount : $("td",this).eq(0).text(),
                        dest_email : $("td",this).eq(1).text(),
                        toName : $("td",this).eq(2).text(),
                        fromManager : $("td",this).eq(3).text(),
                        comment : $("td",this).eq(4).text(),
                        instance : $("td",this).eq(5).text()
                    });
                });
                sendToBackgroundPage(req);
            });
        

    } catch(ee) {
        alert(ee);
    }
});
