$(function(){
    if ( window.LM_periods ) {
        window.review_periods_campaign = LM_periods( {
            containerID : "periods_campaign",
            gridType: "campaign"
        });
        try {
        window.review_periods_campaign.fInit();
        } catch (exc) {
            alert(exc);
        }
    }
 
});
