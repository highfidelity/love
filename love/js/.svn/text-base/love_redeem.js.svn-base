$(function(){
    if ($("#loveReviewTabs").length > 0 ) {
        $("#loveReviewTabs").tabs({
            show: function(event, ui) {
                if ( ui.index == 0 ){
                    if ( $('#companyTags').data("loaded") == false ) {
                        $.cloud.load('companyTags');
                    }
                    if ( $('#userTags').data("loaded") == false ) {
                        $.cloud.load('userTags',$.user.nickname); 
                    }
                    $.resizeLower();
                } else if ( ui.index == 1 ){
                    window.onShowRedeem();
                }
            }
        });
    } else {
        window.onShowRedeem();
    }
});
var onShowRedeem = function() {
    if ( window.LM_periods ) {
        if (! window.review_periods_redeem) {
            window.review_periods_redeem = LM_periods( {
                containerID : "periods_redeem",
                gridType: "redeem"
            });
            try {
            window.review_periods_redeem.fInit();
            } catch (exc) {
                alert(exc);
            }
        }
    }
};