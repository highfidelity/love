/**
    oReview object is used to manage review
    the target is to move all the review variables and functions to the following object
    in order to get cleaner jscript, avoid globals in window object, and avoid $. functions that could corrupt jquery library
    The jquery style should be used
**/
var oReview = function($)
{
    // Define here all the private variables
    var periodUpdate = function(json) {
            // attaching info about period to title div
            var period_title = $('.period-title');
            period_title.html(json.info.title);
            period_title.data('period_info', json.info);
            oReview.period_position = json.position;

            $('.scroll').removeClass('disabled');
            if(json.position == 1){
                $('.prev-period').addClass('disabled');
            }
            if(json.position == json.count){
                $('.next-period').addClass('disabled');
            }

            // determine which color to put on closing date text
            // if period is done - put soon class - red
            // else - play with colors
            var closing_date = $('.closing-date');
            closing_date.removeClass('far middle soon');
            if(json.info.status == 0){
                if(json.time_percentage <= 60){
                    closing_date.addClass('far');
                }else if(json.time_percentage <= 90){
                    closing_date.addClass('middle');
                }else{
                    closing_date.addClass('soon');
                }
            }else{
                closing_date.addClass('soon');
            }

            if(json.info.status == 1){
                closing_text = 'Closed on: ' + json.info.closing_date;
            }else{
                closing_text = 'Closing on: ' + json.info.closing_date
                + ' - ' + json.info.diff + ' day' + getSuff(json.info.diff) + ' left';
            }

            closing_date.html(closing_text);
        },
        loadPeriod = function(fAfter){
            var oThis=this,
                fNewAfter = function() {
                    if (fAfter !== undefined) {
                        fAfter();
                    }                    
                $('#lowerInfoHolder > img').remove();
                $.resizeLower();       
                $.unblockUI();

                
                };
            this.bNeedReload=false;
            $('#lowerInfoHolder').prepend('<img style="margin:auto;display:table;" src="images/loader.gif">');
            $.blockUI({
                message: '', 
                timeout: 20000, 
                overlayCSS: { opacity: 0.01 }
            });
            $("#mainActionArea .selector-holder,.peer_review .mainAction").after('<div class="PeriodLoadergifContainer" ><img  src="images/loader.gif"></div>');

            $('#mainActionArea .selector-holder > div').animate({opacity: 0}, 300);
            $users = $('#users').animate({opacity: 0}, 300);
            $chart = $('#self_pie_chart').animate({opacity: 0}, 300);
            $chart = $('#peer_pie_chart').animate({opacity: 0}, 300);
            $('#love-selector').remove();

            $('#rewarder-div').animate({opacity: 0}, 300);
            $('#timeline-graph').animate({opacity: 0}, 300);


            $.getJSON('review-json.php?action=get_period', 
                {position: oReview.period_position},
                function(json){
                    if ( !json || json === null || json.info === null) {
                        $(".PeriodLoadergifContainer").remove();
                        $(".reviewPeriodTitle").html("No review period has been defined.")
                        $(".reviewPeriodMessage").html("Contact the system administrator.");		    
                        fNewAfter();
                        return;
                    }                    
                    periodUpdate(json);
                    if ($.currentTab == 'self_review'){
                        drawPie({
                            done: json.stats.self_stats.done_percentage,
                            not_done: json.stats.self_stats.not_done_percentage,
                            review_type: "self",
                            chart_id: "self_pie_chart"
                        });
                        getUserList(json.info.id);
                        loadSelfReview(json,function() {
                            $('#mainActionArea .selector-holder > div').animate({opacity: 1}, 300);
                            $users = $('#users').animate({opacity: 1}, 300);
                            $chart = $('#self_pie_chart').animate({opacity: 1}, 300);
                            $(".PeriodLoadergifContainer").remove();
                            fNewAfter();
                        });
                    } else {
                        drawPie({
                            done: json.stats.peer_stats.done_percentage,
                            not_done: json.stats.peer_stats.not_done_percentage,
                            review_type: "peer",
                            chart_id: "peer_pie_chart"
                        });
                        loadPeerReview(json,function() {
                            $('#mainActionArea .selector-holder > div').animate({opacity: 1}, 300);
                            $('#rewarder-div').animate({opacity: 1}, 300);
                            $('#timeline-graph').animate({opacity: 1}, 300);
                            $chart = $('#peer_pie_chart').animate({opacity: 1}, 300);
                            $(".PeriodLoadergifContainer").remove();
                            fNewAfter();
                        });
                    }                       
                }
            );
        }
        ;
        
    return {
        bNeedReload: false,     // used when the period need to be reloaded due to some updates
        period_position: 0,
        initLetter: 'a',
        loadPeriod: loadPeriod
    };
}(jQuery); // end of object review


$(function() {
   $.reviewTabInit(); 
});

$.reviewTabInit = function() {
    userAutoComplete();
    $('#user-popup').dialog({autoOpen: false, width: 600});
    $('#user-review-popup').dialog({
        autoOpen: false, 
        width: 500, 
        modal:true
    });  
    $('.prev-period').click(function(e){
        e.preventDefault();
        if(!$(this).hasClass('disabled')){
            oReview.period_position--;
            oReview.loadPeriod();
            return false;
        }
    });
    
    $('.next-period').click(function(e){
        e.preventDefault();
        if(!$(this).hasClass('disabled')){
            oReview.period_position++;
            oReview.loadPeriod();
            return false;
        }
        
    });
    
    $('li.done').unbind('click').live('click', function(){
        var period_info = $('.period-title').data('period_info');
        showUserReview($(this).data('id'), period_info.id);
    });
    
    $('li.started').unbind('click').live('click', function(){
        var period_info = $('.period-title').data('period_info');
        showUserReview($(this).data('id'), period_info.id);
    });

}
$.selfreviewTab = function(){
    $('#form-content form ').html(""); 
    $('.tabText').html('self review');
    $.lowerTab(true);
    oReview.loadPeriod(function(){
    });
    
};
$.peerreviewTab = function(){
    $('#rewarder-div').html(""); 
    $('.tabText').html('peer review');
    $.lowerTab(true);
    oReview.loadPeriod(function(){
        showSliders();

    });
};



function drawPie(options){
    $chart = $('#'+options.chart_id).empty();
    if ( $chart.length == 0 ) return;
    var r = Raphael(options.chart_id);
    if (!r.g) return;
    r.g.txtattr.font="12px 'Fontin Sans', Fontin-Sans, sans-serif";
    r.g.text(100, 20, "Review's Completed").attr({"font-size": 20 });
    if ( options.done == 0) {
        options.done = 0.001;
    }
    var pie = r.g.piechart(100, 100, 60, 
                [options.done, options.not_done], 
                {legend: ["%% - Done", "%% - Not Done"], legendpos: "south", href:['#','#'],
                colors:[$.user.review_not_done_color, $.user.review_done_color]
                });

    //mouseover text on the pie chart
    var frame = r.rect( 40,60, 150, 15, 5).attr({fill: "#fff", stroke: "#474747", "stroke-width": 1}).hide(); 
    var hoverText= r.text(113,67,'').attr({font: '10px helvetica', fill: "#000"}).hide();
    hoverText.attr({text: "Click slice to see list of names"});
    
    pie.hover(function () {
        this.sector.stop();
        this.sector.scale(1.1, 1.1, this.cx, this.cy);
        frame.show().animate({x:(this.mx)-73, y: (this.my)-7}, 200, true);  // show mouseover text on the pie chart
        hoverText.show().animateWith(frame, {x: this.mx, y: this.my}, 200 , true); // show mouseover text on the pie chart
        if (this.label) {
            this.label[0].stop();
            this.label[0].scale(1.5);
            this.label[1].attr({"font-weight": 800});
            }
    }, function () {
	    frame.hide(); // hide mouseover text on the pie chart
	    hoverText.hide(); // hide mouseover text on the pie chart
        this.sector.animate({scale: [1, 1, this.cx, this.cy]}, 500, "bounce");
        if (this.label) {
            this.label[0].animate({scale: 1}, 500, "bounce");
            this.label[1].attr({"font-weight": 400});
        }
    });

    // click from pie chart sector
    pie.click(function () {
        if (this.value) {
            var review_status = this.value.order == 0 ? 1 : 0;
            if(review_status !== null) {
                var url = 'review-json.php?action=get_users_by_review&position='+ oReview.period_position + '&review_status=' + review_status
                            + '&review_type=' + options.review_type;
                showUserReviewPopup({
                    url:url,
                    review_status : review_status,
                    review_type: options.review_type
                });
            }
        }
    });
                
}

/**
* Get User review data, and populate popup
* @url the URL from which the review will be loaded
*/

function showUserReviewPopup(options) {
    $.getJSON(options.url,'',function(json) {
        if(json && json['user_list'] !== null && json['user_list'].length > 0) {
            loadUserReview(json['user_list'],json['pagination'], 'user-review-table > tbody',options);
        }
        else {
            $('#user-review-table > tbody').html('<tr><td class="center-text" colspan="2" >No user found</td></tr>');
        }

        $('#user-review-popup').dialog('open');

        var dialogTitle = options.review_status == 0 ? 'Reviews Not Done' : 'Reviews Done';
        $('#user-review-popup').dialog('option', 'title', dialogTitle);

        $('.get-user-review-info').click(function() {
            var period_info = $('.period-title').data('period_info');
            showUserReview($(this).attr('rel'), period_info.id);
        });
        $('.page-number').click(function(e) {
            e.preventDefault();
            showUserReviewPopup({
                url: $(this).attr('href'),
                review_status: options.review_status,
                review_type: options.review_type
            });
        });
    });
}

/**
* Populate user review data to the popup dialog box
* @user_list list of user
* @pagination pagination link
* @panel_id id of the target element
*/

function loadUserReview(user_list,pagination, panel_id,options) {
    var bLink = ((options.review_status == 0) || (options.review_type == "peer"));
    $('#'+ panel_id + '> tr').remove();
    $.each(user_list, function(i, jsonrow) {
        var tr = '';
        tr += '<tr id="user-review-'+ jsonrow.id + '"' + ((i%2) == 0 ? 'class="roweven"' :  'class="rowodd"')  + '>';
        tr += bLink ? '<td>' : '<td><a class="get-user-review-info" rel="'+ jsonrow.id +'" href="#" alt="Get review info" title="Get review info">';
        tr += jsonrow.nickname;
        tr += bLink ? '</td>' : '</a></td>';
        tr += bLink ? '<td>' : '<td><a class="get-user-review-info" rel="'+ jsonrow.id +'" href="#" alt="Get review info" title="Get review info">';
        tr += jsonrow.username;
        tr += bLink ? '</td>' : '</a></td>';
        tr += '</tr>';
        $('#'+ panel_id).append(tr);
    });
    var pagination_tr = '<tr class="pagination"><td colspan="2">'+ pagination +'</td></tr>';
    $('#' + panel_id).append(pagination_tr);
}

function getUserList(period_id){
    $('#users').animate({opacity: 0}, 30,function(){
    
        $.getJSON('review-json.php?action=get_userlist', {period_id: period_id}, function(json){
            $('#users li').remove();
            if ( json && json !== null) {
                $.each(json, function(i, jsonrow){
                    var li = $('<li>');
                    if(jsonrow.status == -1){
                        li.addClass('not-done').css('color',$.user.review_not_done_color);
                        li.attr('title', "Review not yet completed for "+jsonrow.username);      
                    } else if(jsonrow.status == 0){
                        li.addClass('started').css('color',$.user.review_started);
                        li.attr('title', "Review started but not yet completed for "+jsonrow.username);                   
                    }else{
                        li.addClass('done').css('color',$.user.review_done_color);
                        li.attr('title', "Click to see review of "+jsonrow.username);
                    }
                    li.append(jsonrow.nickname);
                    li.data('id', jsonrow.id);
                    $('#users').append(li);
                });
            }
            $('#users-nav').empty();
            $('#users').listnav({includeAll: false, initLetter: oReview.initLetter, showCounts: false, 
                onClick: function(letter){ 
                    oReview.initLetter = letter; 
                    $.resizeLower();
                }
            });
            $('#users').animate({opacity: 1}, 30,function(){
                $.resizeLower();
            });
        });

    });
}

function loadSelfReview(period,fAfter) {
        $.ajax({
            url: 'toforAjax.php', 
            data: { 
                period_id: period.info.id,
                view: 'reviewForm'
            },
            dataType: 'html',
            success: function(html){
                $('#reviewForm').html(html);
                selfReviewForm(period,fAfter);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("Error in toforAjax.php ajax call: "+textStatus);
            }
        });
}

function loadPeerReview(period,fAfter) {
    peerReviewForm(period,function() {
        peerCompanyReviewForm(period,function(){
            peerReviewFormDisplay(function() {
                if (fAfter) fAfter();
            });
        });
    });
 }

// if tooltip is not set - showing user popup
// if we have tooltip
function showUserReview(user_id, period_id, tooltip){

    var user_info;
    $.ajax({
        async: false,
        url: 'review-json.php?action=user_info', 
        data: {user_id: user_id, period_id: period_id},
        dataType: 'json',
        success: function(json){
            if (!json ||json === null) return;
            $('#user-love-list').empty();
            setUserStatData(json,$('#user-popup'));
            $('#user-popup').dialog('option','title',json.love_statistic.love_user_info.nickname+ 
                " review <span class='username_popup_review' >("+json.love_statistic.love_user_info.username+")</span>");
            if( json.user_status == 1 || json.user_status == 0 ){
                $.each(json.review_love, function(i, jsonrow){
                    
                    var star = jsonrow.favorite == 'yes' ? 'star_on.gif' : 'star_off.gif';
                    var visible = jsonrow.favorite == 'yes' ? '' : 'hidden';
                    var favorite_why = jsonrow.favorite_why == null ? '' : jsonrow.favorite_why;
                    var love_div = $('<div class="love-holder">');
                    var love_line = '<img class="star" src="images/' + star + '" />'
                    + '<span class="love-why">' + jsonrow.why + '</span> '
                    + '<span class="love-from"> &mdash; love from ' + jsonrow.nickname + '</span> '
                    + '<br /><div class="favorite-why-popup ' + visible + '" >' + favorite_why + '</div>';
                    love_div.data('love_id', jsonrow.love_id);
                    love_div.data('favorite_status', jsonrow.favorite);
                    love_div.append(love_line);
                    
                    $('#user-love-list').append(love_div);

                });
            }else{
                user_info = $('#user-love-list').html('<div class="no-review">User has not submitted review yet</div>');        
            }
                
            if(tooltip){
                user_info = $('#user-popup').html();
            }else{
                $('#user-popup').dialog('open');
            }
        }
    });
    return user_info;
}

function setUserStatData(json,inElement) {
    if (json && json !== null && json.love_statistic && json.love_statistic !== null) {
        $('.love_user_received .number',inElement).text(json.love_statistic.love_user_received);
        $('.love_user_unique_senders .number',inElement).text(json.love_statistic.love_user_unique_senders);
        $('.love_company_received',inElement).text(json.love_statistic.love_company_received);
        $('.love_company_unique_senders',inElement).text(json.love_statistic.love_company_unique_senders);
        $('.user_love_stat_info_user_img',inElement).html('<img src="thumb.php?t=sUSDr&src='+
            json.love_statistic.love_user_info.image +
            '&w=100&h=100&zc=0" width="100" height="100" alt="profile" />');
    }
    $(".user_love_stat_period div",inElement).html($('.period-title').html());
}
// get suffix for value
function getSuff(val){
    return (val==1) ? '' : 's';
}

function userAutoComplete() {
    $("#reviewforuser").autocomplete({
        source:'helper/getemails.php',
        minLength : 1,
        select: function(event, ui) {
            if ( ui.item ) {
                period_info = $('.period-title').data('period_info');
                showUserReview(ui.item.id, period_info.id);
            }
        }
    });

	// helper text process : 
	$("#reviewforuser").focus(function() {
		if ($(this).hasClass("showhint")) {
			$(this).data("helper",$(this).val()).val("").removeClass("showhint");
		}
	});
	$("#reviewforuser").blur(function() {
		if ($(this).val().length == 0) {
			$(this).addClass("showhint").val($(this).data("helper"));
		}
	});

	// reset button to clear the search field
	$("#reviewforuser_search_reset").click(function(event) {
		if ($("#reviewforuser").hasClass("showhint")) {
			$("#reviewforuser").data("helper",$("#reviewforuser").val()).removeClass("showhint");
		}
		$("#reviewforuser").val("").focus();
		event.preventDefault();
	});
}

// this function is executed when user has clicked on company tab in review
function peerReviewCompanyTabAction(){
//    $("#timeline-graph").height("320px")
    peerReviewFormDisplay();
}

// this function is executed when user has clicked on user tab in peer review
function peerReviewUserTabAction(){
        peerReviewFormDisplay();
}
// this function is executed when user has clicked on user tab in review
function selfReviewUserTabAction(){

    if(period_info){
        wizard.showHide();

        // hiding statistics for review that is in the future
        if(period_info.status == 0 && period_info.diff > 0 && period_info.time_status == 0){
            showNoData();
        }else{
            hideNoData();
        }
    }
}

// this function is executed when user has clicked on company tab in review
function selfReviewCompanyTabAction(){
    wizard.hide();
    $('#self_review_progress_holder').hide();
    $('#no-data-holder').hide();
    if ( oReview.bNeedReload ) {
        oReview.loadPeriod();
    }
}
