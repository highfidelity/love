$(function() {
    $.cacheExpTime = $.user.jcacheDelay;
    $.brand();

    $.timers = [];

    $.currentTab = 'info';
    $.currentLowerTab = '.userTab';
    $('.listDiv a').css("color","");  // remove the branding color (highlight1) for the links in the list of Loves
    initGraphView();

    parseNavigation();
    pagerDivRefresh($.user.username, parseInt($('.userTab .page').text()) , true, '.userTab' );


    // for now only love list is working so setting it as default
    $('.balToolbarul > li.list').click();
});

// udate lower info every 2 minues
(function() {
    var interval = 2 * 60 * 1000;
    $.timers['all'] = setInterval(function() {
        pagerDivRefresh($.user.username, parseInt($('.userTab .page').text()) , true, '.userTab' );
    }, interval);
})();
 
