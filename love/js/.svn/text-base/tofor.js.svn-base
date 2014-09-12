// Brand the application
$.brand = function() {

    // Try to Initialize the branding module
    if (branding.init()) { // If succed, apply main branding and additional parts
        branding.brand();

        // css to inject into head tag
        var headAppend = '';
    
        // Apply header colors to the Custom tabs
        $('#pages li a').css('color', branding.header_color);
        $('#companyUserSwitch a').css('color', branding.header_color);
    
        // Apply custom colors to Most loved table
        $('#topLove li').css('color', branding.body_color);
        $('.lb').css('color', branding.body_color);
        
        // If we use fonts that are known to cause layout issues
        // then we adjust the font size so the text will fit.
        if ((branding.font).toLowerCase() == 'arial black' ||
            (branding.font).toLowerCase() == 'verdana'     ||
            (branding.font).toLowerCase() == 'courier new' ||
            (branding.font).toLowerCase() == 'andale mono') {
                $('.actionStats ul li').css('font-size', '12px');
                var headAppend = ' .actionStats ul li { font-size: 12px; } '
        }

        // Apply custom colors to charts
        $.user.review_done_color = branding.highlight1;
        $.user.review_not_done_color = branding.highlight2;
      
        // we need to append to head in order for 'live' updates - when new elements are created, they refer to the original css
        $('head').append('<style type="text/css">' +
                         '  .one a {color: ' + branding.body_color + ';}' + 
                         '  .two a {color: ' + branding.header_color + ';}' +
                         '  .three a {color: ' + branding.highlight1 + ';}' +
                         '  .four a {color: ' + branding.highlight2 + ';}' +
                         '  #topLove li { color: ' + branding.body_color + ';}' +
                         '  ' + headAppend + '  ' +
                         '</style>');
    }
};

$.help = {
    height: 315,
    width: 420,
    init: function() {
        var d = $('#helpDialog');
        d.dialog({
            autoOpen: false,
            height: 'auto',
            width: 'auto',
            modal: true
        });
        $('.helpTrigger').click(function() {
              d.dialog('open');
        })
    }
};

$.splashScreen = {
    height: 315,
    width: 420,
    init: function() {
    if (!$.user.isAdmin || !$.user.splashScreen) {
        return;
    }
    var d = $('#splashscreen');
    var cbox = $('<input type="checkbox" id="stopsplash" name="stopsplash" />');
    cbox.change(function(e) {
      if ($(this).attr('checked')) {
        $.splashScreen.deactivate();
      } else {
        $.splashScreen.activate();
      }
    }).insertBefore(d.find('label'));
    d.dialog({
      autoOpen: true,
      height: 'auto',
      width: 'auto',
      modal: true
    });
  },
  deactivate: function() {
    $.ajax({
      url: 'helper/splashscreen.php',
      type: 'POST',
      data: {
        action: 'deactivate',
        userid: $.user.user_id
      },
      dataType: 'json',
      success: function() {}
    });
  },
  activate: function() {
    $.ajax({
      url: 'helper/splashscreen.php',
      type: 'POST',
      data: {
        action: 'activate',
        userid: $.user.user_id
      },
      dataType: 'json',
      success: function() {}
    });
  }
};
/*var deleteLove = function() {
  $('.deleteButton input[type=button]').click(function() {
    $('.headDelete input[type=checkbox]:checked').each(function() {
      var id = $(this).val();
      $.ajax({
        url: 'toforAjax.php',
        type: 'POST',
        data: {
          tell: 'deleteLove',
          love: id,
          userid: $.user.user_id
        },
        dataType: 'json',
        success: function(json) {
          if (json.success) {
            $('.love_' + id).fadeOut('fast');
          }
        }
      });
    });
  });
};*/
$.loveTabInit = function(){
    // setup username autocomplete
    if (!jCache.isValid('userList')) {
        $.getJSON('helper/getemails.php', function(json) {
            $("#to").autocomplete({
                source: json,
                minLength: 0
            });

            // Store the data on cache
            jCache.set('userList', json, 1); // Set the email list to refresh every minute
            
        });
    } else {
        $("#to").autocomplete({
            source: jCache.get('userList'),
            minLength: 0
        });
    }
    // start the autocompleter on click to show full list of users
    $("#to").click(function() {
        $('#to').autocomplete("search");
    });

    // initialize validation
    initLiveValidation();
  
    // this function handles sending of love via ajax
    sendLove();
  
    // initialize graphs
    initGraphView();
      
    // creates a pager for list chart
    parseNavigation();
    
    //deleteLove(); // disabled
    // tooltips for mostloved
    loveTips();
    // handleMinimize();  The minification image has been removed, so this code is no longer required
}
$.loveTab = function(){
    //$.tabInits = false;
    $("#mainActionArea").height("250px");
    // Displays the latest love messages
    $.updateLove();
    $('.tabText').html('love');
    $.lowerTab();
};
var pagerDivRefresh =  function(username, page, just_user, lowerTab ) {
    var bWhen = (just_user) ? $.user.listUserLovesWhen : $.user.listCompanyLovesWhen;
    $.getJSON('toforAjax.php?tell=listLove&username='+username+'&just_user='+just_user+'&page='+page+'&when='+bWhen, function(json) {
        // Store the data on cache
        jCache.set('pagerDiv', json, $.cacheExpTime);
        
        $(lowerTab+' .table-history tbody:first').html(json.body);
        $(lowerTab+' .pagerDiv').html(json.pager);
        $.resizeLower(); 
    });
};
/**
 * This function is called when the page is loaded and from then on, everytime
 * a new page is requested, to reparse the new pager.
 * The code below handles click events on next and prev buttons, switches the
 * current page and display the new result which is received throught ajax request
 */
var parseNavigation = function() {

    $('.prev').live('click', function() {
        pagerDivRefresh( $.user.username, parseInt($($.currentLowerTab+' .page').text())-1 , $.currentLowerTab == '.userTab', $.currentLowerTab );
    });
    $('.next').live('click', function() {
        pagerDivRefresh( $.user.username, parseInt($($.currentLowerTab+' .page').text())+1 , $.currentLowerTab == '.userTab', $.currentLowerTab );
    });
    $('.otherPage').live('click', function() {
        pagerDivRefresh( $.user.username, parseInt($(this).text()) , $.currentLowerTab == '.userTab', $.currentLowerTab );
    });
    $('.listDiv .headWhen').live('click', function() {
        if ($.currentLowerTab == '.userTab') {
            $.user.listUserLovesWhen = ($.user.listUserLovesWhen) ? false : true;
        } else {
            $.user.listCompanyLovesWhen = ($.user.listCompanyLovesWhen) ? false : true;
        }
        pagerDivRefresh( $.user.username, parseInt($(this).parents(".listDiv").find(".pagerDiv .current").text()) , $.currentLowerTab == '.userTab', $.currentLowerTab );
    });
    $('.firstPage').live('click', function() {
        pagerDivRefresh( $.user.username, 1 , $.currentLowerTab == '.userTab', $.currentLowerTab );
    });
    $('.lastPage').live('click', function() {
        pagerDivRefresh( $.user.username, parseInt($(this).attr("lastPage")) , $.currentLowerTab == '.userTab', $.currentLowerTab );
    });
};
$.switchTabs = {
    init: function() {
        $('div.self_review').animate({opacity:0, height:'hide'}, 0);
        $('div.peer_review').animate({opacity:0, height:'hide'}, 0);
        $('#pages a').each(function(){
            $(this).click(function(e) {
                e.preventDefault();
                $li = $(this).parents('li');
                if (!$li.hasClass('current')) {
                    var currentTab = $('#pages > li.current > a').attr('class');
                    $('#pages > li.current').removeClass('current');
                    $li.addClass('current');
                    var newTab = $li.children(':first').attr('class');
                    $.switchTabs.tabChange(newTab, currentTab);
                }
            });
        });
        $.currentTab = 'love'
    },
    tabChange: function(tab, old) {
        $.currentTab = tab;
        $('div.'+old).animate({opacity:0, height:'hide'}, 300);
        $('div.'+tab).animate({opacity:1, height:'show'}, 300);
        if(tab == 'love') {
          $.loveTab();
        } else if (tab == 'self_review') {
          $.selfreviewTab();                
        } else {
          $.peerreviewTab();                
        }
    },
    tabChangeUsingClass: function(className) {
        $('#pages a.'+className).click();
    }
};

var initLiveValidation = function () {
    to_lv = new LiveValidation('to', { validMessage: "Valid email address.", onlyOnBlur: false })
        .add(SLEmail2)
        .add(Validate.Exclusion, { within: [ $.user.username ], caseSensitive: false, failureMessage: 'You cannot send love to yourself.' });

    to_lv2 = new LiveValidation('to', { validMessage: "Valid email address.", onlyOnSubmit: true })
        .add(SLEmail2)
        .add(Validate.Presence);

    for1_lv = new LiveValidation('for', { validMessage: "Valid message.", onlyOnSubmit: true })
        .add(Validate.Presence)
        .add(Validate.Length, { minimum: 4, maximum: 250 });

    $('#to').bind('keyup', function() {
        // check for a valid email first, before sending ajax request
        if (to_lv.validate()) {

            // process receiver to identify nickname and username
            var to = $('#to').val().replace(/^\s+|\s+$/g,"");
            var nickname = '';
            var username = '';
            if (to.match(/^[-\w\d+.~#:]+[ ]*[-\w\d+.~#:]+ \([-\w\d._%+]+@[\w\d.-]+\.[\w]{2,4}\)$/)) {
                var paren = to.indexOf('(');
                nickname = to.substr(0, paren-1);
                username = to.substr(paren+1, to.length - paren - 2);
            } else if (to.match(/^\b[\w\d._%-]+@[\w\d.-]+\.[\w]{2,4}\b$/)) {
                nickname = to;
                username = to;
            } else {
                return false;
            }        
        
            // validate the to field
            // fire when 500 milliseconds elapsed, reduce amount of ajax requests
            window.clearTimeout( timer );
            timer = window.setTimeout( function() {  
                $.get('helper/check_love_receiver.php',
                    { username: username }, 
                    function(data) {
                        if (data !== 'true') {
                            // add the nickname as a validate exclusion rule
                            to_lv.add(Validate.Exclusion, { within: [ username ], failureMessage: "This person is not in the system."  });
                            to_lv.validate();
                        }
                    }
                );  
            }, 500);
        }
    });
    $("#for").charCount({
        allowed: 250,
        warning: 20,
        counterText: ''
    });
};

var sendLove = function() {
    $('#sndLoveBtn').live('click', function(){
      $('#priv').val(0);
    });
    $('#sndLovePrvBtn').live('click', function(){
      $('#priv').val(1);
    });
    $('#sendloveForm').submit(function(e) {
        $("#infoFormArea *").hide();
        if((to_lv && !to_lv.validate())  || (to_lv2 && !to_lv2.validate())  || (for1_lv && !for1_lv.validate())) {
            $(".LV_valid").hide();
            $("#infoFormArea .error").html("Unknown user");
            $("#infoFormArea .error").show();
            return false;
        }
    
        var to = $('#to').val().replace(/^[\s]+|[\s]+$/g,"");
        var nickname = '';
        var username = '';
        if (to.match(/^[-\w\d+.~#:]+[ ]*[-\w\d+.~#:]+ \([-\w\d._%+]+@[\w\d.-]+\.[\w]{2,4}\)$/)) {
            var paren = to.indexOf('(');
            nickname = to.substr(0, paren-1);
            username = to.substr(paren+1, to.length - paren - 2);
        } else if (to.match(/^\b[-\w\d._%+]+@[\w\d.-]+\.[\w]{2,4}\b$/)) {
            nickname = to;
            username = to;
        } else {
            return false;
        }

        // reset update interval to 2 minutes
        clearInterval($.timers['all']);
        var interval = 2 * 60 * 1000;
        $.timers['all'] = setInterval(function() {
            refresh();
        }, interval);        

        $.blockUI({
            message: '<h1>Sending love...</h1>', 
            css: { border: '3px solid #a00' } 
        });
        var for1 = $('#for').val().replace(/&/, '%26');
        var data = '&to='+username;
        data += '&for1='+for1;
        
        /* parsing as parameter the private setting */
        data += '&priv='+$('#priv').val();
        
        $.ajax({
            type: "POST",
            url: "sendlove.php",
            data: data,
            dataType: 'json',
            success: function(json) {
                // clear the field and provide feedback to sender straight away
                $('#to').val('');
                $('#for').val('');
                // trigger change on message field to update counter
                $('#for').trigger('change');
                // hide validation
                $('.LV_valid').hide();
                $('#infoFormArea .success').show();
                $.unblockUI();

                RateLimit(true);
                // user tried to send love outside instance and this is not allowed by configuration

                if (json == 'outside') { 
                    // add to exclusion list for caching
                    to_lv.add(Validate.Exclusion, { within: [ username ], caseSensitive: false, failureMessage: 'This person is not in the system.'});
                    to_lv.validate();
                    $.unblockUI();
                    return false;
                }

                // if we receive love data back, refresh with it
                if (json.data) {
                    refresh(json.data);
                } else {
                    refresh();
                }

                if ($('#page').text() == 1) {
                    if (!jCache.isValid('listLove')) {
                        $.getJSON('toforAjax.php?tell=listLove&page=1', function(json){
                            // Store the data on cache
                            jCache.set('listLove', json, $.cacheExpTime);
                            
                            $('.table-history tbody:first').html(json.body);
                            $('#pagerDiv').html(json.pager);
                        });
                    } else {
                        var json = jCache.get('listLove');
                        
                        $('.table-history tbody:first').html(json.body);
                        $('#pagerDiv').html(json.pager);
                    }
                }
                pagerDivRefresh( $.user.username, parseInt($('.userTab .page').text()) , true, '.userTab' );
                pagerDivRefresh( $.user.username, parseInt($('.companyTab .page').text()) , false, '.companyTab' );
            },
            error: function(xhdr, status, err) {
                limitTime = 0;
                RateLimit();
                $(".LV_valid").hide();
                $("#infoFormArea .error").html("Error sending love");
                $("#infoFormArea .error").show();
                $.unblockUI();
            }
        });
        
        if ($('#priv').val() == 1) for1 += ' (love sent quietly)';
        return false;
  });
};
var RateLimit = function(check) {
  if(check) {
        $.ajax({
            type: "POST",
            url: "ratelimit.php",
            data: "c=love&id=<?php echo $_SESSION['userid'] ?>",
            dataType: "json",
            success: function(json) {
                limitTime = json;
                RateLimit();
            }, 
            error: function(xhdr, status, err) {
                limitTime = 0;
                RateLimit();
            }
        });
    } else {
        if (limitTime > 0) {
            $("#submitButtons").hide();
        setTimeout("RateLimit(true)", limitTime*1000);
            limitTime = 0;
        } else {
            $("#submitButtons").show();
        }
    }
};
var initGraphView = function() {
    $('.balToolbarul > li').each(function() {
        $(this).click(function() { 
            // If the item is the Fullscreen cloud do nothing
            if (! $(this).hasClass('loveCloudFull')) {
                if (! $(this).hasClass('loveCloud')) {
                    $('.loveCloudFull').fadeOut('fast');
                } else {
                    $('.loveCloudFull').fadeIn('fast');
                }
                if (! $(this).hasClass('active')) {
                    $($.currentLowerTab + ' li.active').removeClass('active');
                    oThis = $(this);
                    $($.currentLowerTab + ' .contents > div').animate({'opacity': '0',marginLeft: '-4321px'}, 10, function() {
                        $(this).removeClass('subCurrent');
                        var item = oThis.attr('class') + 'Div';
                        oThis.addClass('active');
                        $($.currentLowerTab + ' .'+item).animate({'opacity': '1',marginLeft: '0px'}, 10, function() {
                            $(this).addClass('subCurrent');
                            $(this).css({'opacity': 'none', 'filter': 'none'});
                            // this is needed to resize the lower area to the height of the current content
                            $.resizeLower();
                        });
                    });
                }
            }
        });
    });
    $('.love .companyTab .trendChart').click(setupCoGraph);
    $('.love .userTab .trendChart').click(setupUserGraph);
    $('.love .companyTab .loveCloud').click(function() { 
        // if the cloud is not already loaded, do it now
        if ( $('#companyTags').data('loaded') == false ) {
            $.cloud.load('companyTags');
        }
    });
    $('.love .userTab .loveCloud').click(function() { 
        // if the cloud is not already loaded, do it now
        if ( $('#userTags').data("loaded") == false ) {
            $.cloud.load('userTags',$.user.nickname);
        }
    });
    $('.love .companyTab .loveCloudFull').click(function() {
        // Open a new tab and show there the cloud fullscreen
        window.open('stage.php', '_blank');
    });
    
    $('.contents>div').not(':first').animate({'opacity': '0'});
};

var setupCoGraph = function() {
    var curDate = new Date();
    var pastDate = new Date(curDate.getFullYear(), curDate.getMonth() - 2, curDate.getDate());
    var from = (pastDate.getMonth()+1)+'/'+pastDate.getDate()+'/'+pastDate.getFullYear();
    var to = (curDate.getMonth()+1)+'/'+curDate.getDate()+'/'+curDate.getFullYear();
  
    var cographPanelId = 'company-timeline-chart';
    $('#'+cographPanelId).empty();
    CompanyLoveChart.initialize(cographPanelId, 930, 400, 30);
    CompanyLoveChart.setIsCompany(true);
    if (!jCache.isValid('co-graph')) {
        $.getJSON('lovechart-data.php', 
            {
                type: "userLoveCountsByDate", 
                from_date: from,
                to_date: to,
                username: ""
            },
            function(json) {
                jCache.set('co-graph', json, $.cacheExpTime);
                CompanyLoveChart.load(json);
            }
        );
    } else {
        CompanyLoveChart.load(jCache.get('co-graph'));
    }
}

var setupUserGraph = function() {
    var curDate = new Date();
    var pastDate = new Date(curDate.getFullYear(), curDate.getMonth() - 2, curDate.getDate());
    var from = (pastDate.getMonth()+1)+'/'+pastDate.getDate()+'/'+pastDate.getFullYear();
    var to = (curDate.getMonth()+1)+'/'+curDate.getDate()+'/'+curDate.getFullYear();

    var ugraphPanelId = 'user-timeline-chart';
    $('#'+ugraphPanelId).empty();
    UserLoveChart.initialize(ugraphPanelId, 930, 400, 30);

    // CompanyLoveChart.setIsCompany(false);
    if (!jCache.isValid('user-graph')) {
        $.getJSON('lovechart-data.php', 
            {
                type: "userLoveCountsByDate", 
                from_date: from,
                to_date: to,
                username: $.user.nickname // this is nickname not username left as is so nothing breaks
            },
            function(json){
                jCache.set('user-graph', json, $.cacheExpTime);
                UserLoveChart.load(json);
            }
        );
    } else {
        UserLoveChart.load(jCache.get('user-graph'));
    }
}

var updateLoveNotifications = function(data) {
    if (! data) {
        if (!jCache.isValid('loveNotification')) {
            $.getJSON('toforAjax.php?tell=getLoveNotification', function(json){
                // Store the data on cache
                jCache.set('loveNotifications', json, $.cacheExpTime);
                
                $('#loveNotification').html(json);
            });
        } else {
            var json = jCache.get('loveNotifications');
            $('#loveNotification').html(json);
        }
    } else {
        $('#loveNotification').html(data);
    }
};
var updateMostLoved = function(data) {
    if (! data) {
        if (!jCache.isValid('mostLoved')) {
            $.getJSON('toforAjax.php?tell=mostLoved', function(json) {
                // Store the data on cache
                jCache.set('mostLoved', json, $.cacheExpTime);
                
                $('#topLove').html(json);
                loveTips();
            });
        } else {
            var json = jCache.get('mostLoved');
            $('#topLove').html(json);
            loveTips();
        }
    } else {
        $('#topLove').html(data);
        loveTips();
    }
};
var updateTotalLove = function(data) {
    if (! data) {
        if (!jCache.isValid('totalLove')) {        
            $.getJSON('toforAjax.php?tell=totalLove', function(json) {
                // Store the data on cache
                jCache.set('totalLove', json, $.cacheExpTime);
                
                $('#totalLove').html(json);
            });
        } else {
            var json = jCache.get('totalLove');
            $('#totalLove').html(json);
        }
    } else {
        $('#totalLove').html(data);
    }
};
/**
 * Determine which trend chart is active, and update it
 */
var updateTrendChart = function() {
    if ($('#companyLink').hasClass('currentLink')) {
        // if the company tab is active, and the trend chart is in view, update it
        if ($('.love .companyTab .trendChart').hasClass('active')) {
            setupCoGraph();
        }
    } else if ($('.love .userTab .trendChart').hasClass('active')) {
        // otherwise, if the user trend chart is in view, update it. the user tab is active here
        setupUserGraph();
    }
}

$.lowerTab = function(bShowUser){
    var hideClass = ".userTab",
        showClass = ".companyTab",
        showLink = "#companyLink";
    if ( bShowUser !== undefined && bShowUser === true ) {
        hideClass = ".companyTab";
        showClass = ".userTab";
        showLink = "#userLink";
    }
    $('.'+$.currentTab + ' '+hideClass).animate({opacity: 0, marginLeft: '-4321px'},0);
    $.currentLowerTab = showClass;
    $('.'+$.currentTab + ' ' + showClass).animate({opacity: 1, marginLeft: '0px'}, 300);
    initializeTab($.currentLowerTab);
    $(showLink).click();
    $.resizeLower();
};
$.lowerTabInit = function() {
    $('#companyUserSwitch a').live('click', function(e) {
        e.preventDefault();
        if ($(this).hasClass('currentLink')) return;
        $('.currentLink').removeClass('currentLink').animate({top: 72}, 300);
        tabName = $(this).addClass('currentLink').animate({top: 2}, 300).attr('href');
        tabName = '.' + tabName.substr(1);
        //if ($.currentLowerTab == tabName) return;
        $.currentLowerTab = tabName;
        initializeTab(tabName);
        $tab = $('.'+$.currentTab + ' ' + tabName);
        $('.currentLowerTab').removeClass('currentLowerTab');
        $('.baloon:visible').animate({opacity: 0, marginLeft: '-4321px'}, 300);
        $tab.animate({marginLeft: '1200px'},0).animate({opacity: 1, marginLeft: '0px'}, 300);
        $.resizeLower();
    });
}

$.resizeLower = function() {
    if ($.rst) clearTimeout($.rst);
    $.rst = setTimeout(function() {
        // using 200 as a minimum height resize the lower area to fit the content
        var y = $('.'+$.currentTab + ' ' + $.currentLowerTab + ' .subCurrent').outerHeight(true);
        y = y < 200 ? 200 : y;
        $('#lowerInfoHolder').animate({height: y});  
    }, 350);
}

function initializeTab(id) {

    if ($.currentTab == 'self_review'){
        if (id == '.companyTab') {
            selfReviewCompanyTabAction();
        } else {
            selfReviewUserTabAction();
        }
    } else if ($.currentTab == 'peer_review'){
        if (id == '.companyTab') {
            peerReviewCompanyTabAction();
        } else {
            peerReviewUserTabAction();
        }
    } else {
        if (id == '.companyTab') {
            $.cloud.load('companyTags');
        } else {
            $.cloud.load('userTags',$.user.nickname);
            setTimeout('showLoveChart($.user.nickname);', 300);
        }
    }
    
    if(!$.tabInits) $.tabInits = [];
    $('.'+$.currentTab + ' ' + id).find('li.active').click();
    if($.tabInits[id]) return;
    $.tabInits[id] = true;
    $(id).find('li:first').click();
}

$.fn.rotator = function() {
    function supportsRotation() {
        if($.rotateSupport !== undefined) return $.rotateSupport;
        // thanks modernizr
        /**
         * Create our "modernizr" element that we do most feature tests on.
         */
        $.rotateSupport = false;
        var m = document.createElement( 'modernizr' );
        var m_style = m.style;
        var props = [ 'transformProperty', 'webkitTransform', 'MozTransform', 'mozTransform', 'oTransform', 'msTransform' ];
        
        for(var i in props) {
            if ( m_style[ props[i] ] !== undefined ) {
                $.rotateSupport = true;
                return $.rotateSupport;
            }
        }
        return $.rotateSupport;
    }
    function getSize(jqob, rotation) {
        if (rotation > 0) {
            return [jqob.height(), jqob.width()];
        } else {
            return [jqob.width(), jqob.height()];
        }
        
    }
    if (!supportsRotation()) { return($(this)); }
    return($(this).each(function() {
        var $this = $(this);
        var $children = $this.children();
        var $bounds = getSize($this);
        $.each($children, function(a,b) {
            var $child = $(b);
            var rotate = (Math.round(Math.random()));
            var s = getSize($child, rotate);
            rotation = 'rotate(' + (rotate * 90) + 'deg)';
            $child.find('a').css({WebkitTransform: rotation, MozTransform: rotation});
            $child.css({ width:s[0]+'px',height:s[1]+'px' });
        })
    }));
}

var loveTips = function() {
    $("#topLove li:not(.nodata)").tooltip({ 
        offset: [-10, 0], 
        position: "center left", 
        relative: true, 
        onBeforeShow: function() {
            $('.tooltip').hide();
        }
    });
};

var refresh = function(data) {
    // update graphs if they are visible. they are always updated on activation
    updateTrendChart();
    // update love connections chart
    showLoveChart($.user.nickname);
    // update live feed
    $.updateLove();
    // update love clouds
    $.cloud.load('companyTags');
    $.cloud.load('userTags', $.user.nickname);
    // use our data to update other page elements
    if (data) {
        updateLoveNotifications(data.loveNotifications);
        updateMostLoved(data.loveMost);
        updateTotalLove(data.loveTotal);
    } else {
        // otherwise request the data
        updateLoveNotifications();
        updateMostLoved();
        updateTotalLove();
    }
    
    // And update the love list
    if ($('#page').text() == 1) {
        $.getJSON('toforAjax.php?tell=listLove&page=1', function(json){
            // Store the data on cache
            jCache.set('listLove', json, $.cacheExpTime);
            
            $('.table-history tbody:first').html(json.body);
            $('#pagerDiv').html(json.pager);
        });
    }
    pagerDivRefresh( $.user.username, parseInt($('.userTab .page').text()) , true,'.userTab' );
    pagerDivRefresh( $.user.username, parseInt($('.companyTab .page').text()) , false,'.companyTab' );
};

