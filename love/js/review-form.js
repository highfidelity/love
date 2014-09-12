var period_info;
var pick_message_start  = '<span class = "love-chosen"></span>/<span class = "love-limit"></span> choose <span class = "to-pick"></span> more.';
var pick_message = '<span class = "love-chosen"></span>/<span class = "love-limit"></span> choose <span class = "to-pick"></span> more. Click <a href="#" id="pick_done">Done</a> to finish.';

$(function(){

    $('#hide-floater').live('click', function(){
        wizard.hideFloater();
        if(wizard.current_step == 0){
            $('#love-selector').dialog('open');
        }
        return false;
    });
    
    $('#pick_done').live('click', function(){
        if(wizard.current_step == 0){
            wizard.updateStep(1);
        }
        $('#love-selector').dialog('close');
        return false;
    });
    
    $('.add-comment').live('click', function(){
        
        $lh = $(this).parents('.love-holder');
        $lh.find('.favorite-why-holder').animate({opacity:1, height:'show'}, 300);

        $(this).fadeOut(300);
        $lh.find('.remove-comment').fadeIn(300, function() {
            $lh.find('.favorite-why').focus();
        });

        return false;
    });
        
    $('.remove-comment').live('click', function(){
        
        $lh = $(this).parents('.love-holder');
        $lh.find('.favorite-why-holder').animate({opacity:0, height:'hide'}, 300);
        $lh.find('img').attr('src', 'images/star_off.gif');
        $(this).fadeOut(300);
        $lh.find('.add-comment').fadeIn(300);
        markFavorite($lh.data('love_id'), 'no');
        
        // if we are under the limit - show add comment links
        if( $('#reviewForm img[src="images/star_on.gif"]').size() < reviewConfig.getFavoriteLimit() ){
            $('#reviewForm img[src="images/star_off.gif"]').each(function(i, off_image){
                $('.add-comment', $(this).parent()).show(300);
            });
        }
        updateCommentLinks(true);
        return false;
    });
    
    $('.favorite-why').live('blur', function(){
        var $lh = $(this).parents('.love-holder'),
            currentImg = $lh.find('img').attr('src');
        if ($.trim($(this).val()) !== "") {
            updateFavoriteWhy($lh.data('love_id'), $(this).val());
            if ( currentImg != 'images/star_on.gif' ) {
                $lh.find('img').attr('src', 'images/star_on.gif');
                markFavorite($lh.data('love_id'), 'yes');
                updateCommentLinks(true);
            }
        } else {
            $lh.find('.favorite-why-holder').animate({opacity:0, height:'hide'}, 300);
            $lh.find('.remove-comment').fadeOut(300,function(){
                $lh.find('.add-comment').fadeIn(300);
            });           
            updateFavoriteWhy($lh.data('love_id'), "");
            if ( currentImg != 'images/star_off.gif' ) {
                $lh.find('img').attr('src', 'images/star_off.gif');
                markFavorite($lh.data('love_id'), 'no');
                updateCommentLinks(true);
            }
        }
    });
    
    $('.love-check').live('click', function(){
        var love_picked = $('#love-selector input:checked').size();
        if( love_picked > $('#reviewForm').data("love_limit") ){
            $(this).attr('checked', false);
        }else{
            var love_id = $('.love_id', $(this).parent()).val();
            var love_status = $(this).attr('checked') ? 1 : 0;
            updateReviewLove(love_id, love_status);
            updateToPick();
            if ( ( !$(this).attr('checked') ) && 
                ( love_picked == $('#reviewForm').data("love_limit") - 1 )) {
                updateReviewStatus( false , false );
            }
        }
    });
    
    $('.add-love').live('click', function(){
        $('#love-selector').dialog('open');
        return false;
    });
    
    // user pressed "Next" in wizard, so updating the step
    $('#next').live('click', function(){
        wizard.updateStep(3);
        return false;
    });
    
    // user pressed "Back" in wizard - getting him back to Love screen
    $('#back').live('click', function(){
        wizard.updateStep(2);
        hideSliders();
        return false;
    });
    
});

var loadModuleFirstTime=true;
var selfReviewForm = function(period,fAfter){
    var total_user_love;
    
    period_info = period.info;
    $('#form-content form').show();
    $('.review-message').hide();
    if($.currentLowerTab == '.userTab'){
        hideNoData();
    }else{
        wizard.hide();
    }


    // initializing it here because we need wizard info for past periods
    wizard.init(period.info.user_review_id, period.info.wizard_step - 0);
    wizard.updateDisplay();

    total_user_love = $('#love-selector table tbody tr').size();
    if( total_user_love < reviewConfig.getLoveLimit() ){
        $('#reviewForm').data("love_limit", total_user_love);
    } else {
        $('#reviewForm').data("love_limit", reviewConfig.getLoveLimit());
    }
    // check if period is not closed and we are inside the period dates
    // or this is not closed but already passed it's date
    if (( period_info.status == 0 && period_info.time_status == 1 ) ||
        ( period_info.status == 0 && period_info.diff < 0 )) {      
        $('#love-selector').dialog({autoOpen: false, width: 700, show: 'fade', hide: 'fade'});      
        $('#love-selector').tablePagination({rowsPerPage: 10});      
        fillLove();
        updateToPick();
        if (fAfter) fAfter();      
    // this is closed 
    }else if(period_info.status == 1 ){
        $('#reviewForm').data("love_limit",$('#love-selector input:checked').size());
        wizard.hide();
        if(period_info.user_status == 1){
            fillDoneStats();
        }
        if ($.currentLowerTab == '.userTab'){
            fillLove();
           // updateToPick();
        }
        
        $('#review-status-message').show();
        hideActionLink();
        if (fAfter) fAfter();

    // at this point we know that status == 0 and diff is positive = review from the future
    }else{
        //show "Not started yet"
        wizard.hide();
        if($.currentLowerTab == '.userTab'){
            showNoData();       
        }
        
        $('#form-content form').hide();
        $('#not-started-text').show();
        if (fAfter) fAfter();

    }
};

var peerReviewForm = function(period,fAfter){
    period_info = period.info;
    wizard.hide();
    // check if period is not closed and we are inside the period dates
        
        $('#rewarder-div').html(""); 
        $('#rewarder-div').css('position', 'absolute');
        $('#rewarder-div').css('left', -10000);
        $.get( reviewConfig.getReviewUrl() + '/rewarder.php?loadGraphOnly=true&load=module&loadFirstTime='+loadModuleFirstTime, function(rewarder_div){
            var fNewAfter = function() {
                $('#rewarder-div').css('position', 'relative');
                $('#rewarder-div').css('left', 0);
                if (fAfter) fAfter();
            };
            $('#rewarder-div').html(rewarder_div);
            if ($("#raftest .rewarderGraphHelp").length > 0) {
                $("#mainActionArea .rewarderGraphHelp").html($("#raftest .rewarderGraphHelp").hide().html());
            } else {
                $("#mainActionArea .rewarderGraphHelp").html("");
            }
            $("#period-filter").hide();
            if (!window.rewarder) {
                $('#rewarder-div').prepend("<div>Error in review.<br\></div>").wrapInner("<div class='errorInReview'></div>");
                if ($('#period-box').next('.errorInReview').length == 0) {
                    $('#period-box').after( $('#rewarder-div').html());
                }
                fNewAfter();
            } else {
            var sMess = "";
            if ( period_info.status == 1  ) {
                sMess = "The current period is closed.";
            }
            if ( period_info.peer_status == 2  ) {
                sMess += "The current review is published.";
            }
            if (sMess != "") {
                sMess +=  "Update forbidden.";
                $("#mainActionArea .rewarderGraphHelp").html(sMess);
            }
                rewarder.initRewarder(period_info,fNewAfter);
            }
        });
        loadModuleFirstTime=false;
};

var peerCompanyReviewForm = function(period,fAfter){
    period_info = period.info;
    wizard.hide();
    // check if period is not closed 
    if ( period_info.status == 0 ){
        $('#timeline-graph').html("<div class='errorInReview'>History of not closed peer review is not available.<br\></div>")
        if (fAfter) fAfter();     
    // this is closed 
    } else if ( period_info.status == 1 ){
        setupGraph( period_info.end_date,period_info.id,fAfter );
    }
};

var peerReviewFormDisplay = function(fAfter) {
    if($.currentLowerTab == '.userTab'){
        $("#mainActionArea .rewarderGraphHelp").show();
        $("#peer_review_progress_holder").show();
    } else {
        $("#mainActionArea .rewarderGraphHelp").hide();
        $("#peer_review_progress_holder").hide();
    }
    if (fAfter) fAfter();
}

function fillLove(){
    $.getJSON('review-json.php?action=user_love',
        {period_id: period_info.id},
        function(json){
            if (!json ||json === null) return;
            $('#love-list').empty();
            setUserStatData(json,$('#loves'));
            if ( json.review_love &&json.review_love.length > 0 ){
                $.each(json.review_love, function(i, jsonrow){
                    var star = jsonrow.favorite == 'yes' ? 'star_on.gif' : 'star_off.gif';
                    var show_comment = jsonrow.favorite == 'yes' ? '' : 'hidden';
                    var show_add = jsonrow.favorite == 'no' ? '' : 'hidden';
                    var favorite_why = jsonrow.favorite_why == null ? '' : jsonrow.favorite_why;
                    var love_div = $('<li class="love-holder">');
                    var love_line = '<img class="star" src="images/' + star + '" />'
                                + '<span class="love-why">' + jsonrow.why + '</span> '
                                + '<span class="love-from"> &mdash;&nbsp;love from&nbsp;' + jsonrow.nickname + '</span>  &crarr; '
                                + '<a href="#" class="add-comment ' + show_add + '">Add comment</a> '
                                + '<a href="#" class="remove-comment '+ show_comment +'">Remove comment</a> '
                                + '<div class="favorite-why-holder '+ show_comment +'">'
                                +'<span class="love-comment"> my comment </span> <textarea class="favorite-why">' + favorite_why + '</textarea>'
                                + '</div>';
                    love_div.data('love_id', jsonrow.love_id);
                    love_div.data('favorite_status', jsonrow.favorite);
                    love_div.append(love_line);
                    
                    $('.favorite-why').each(function() {
                        if ($(this).val().length > 231) {
                            pH = ($(this).height() *1.7) + 'px';
                            $(this).css('height',pH);
                        }
                        
                        $(this).keydown(function() {
                            if ($(this).val().length < 231) {
                                if ($(this).height() > 62) {
                                    $(this).css('height',62+'px');
                                }
                            }
                            if ($(this).val().length > 231 && $(this).val().length < 255) {
                                if ($(this).height() <= 62) { 
                                    pH = ($(this).height() *1.7) + 'px';
                                    $(this).css('height',pH);
                                }
                            }
                            if ($(this).val().length > 255) {
                                $(this).val($(this).val().substring(0, 255));
                            }
                        });
                    });
                    
                    $('#love-list').append(love_div);
                });
            }else{  
                var add_love_message="";
                if ( period_info.status == 0 ) {
                    // no loves and period not closed
                    add_love_message = '<div id="add-love-message">'
                                            + '<a href="#" class="add-love">Choose</a> ' 
                                            + $('#reviewForm').data("love_limit") + ' love from'
                                            +' the last period that you '
                                            +'think best represents your contributions</div>';
                } else {
                    // no loves and update of review is impossible
                    add_love_message = '<div id="add-love-message">No review for this period.</div>';
                    $('#review-status-message').html("");
                }
                $('#love-list').append(add_love_message);
            }
//            $.resizeLower();
            updateCommentLinks();
            // hide action link only if the period is closed
            // action link is available if the end period date is passed
            if ( period_info.status == 1 ){ 
                hideActionLink();
            }
        }
    );
}

function updateReviewLove(love_id, love_status){
    $.get('review-json.php?action=update_love',
              {love_id: love_id,  love_status: love_status, period_id: period_info.id},
              function(){
                  fillLove();
              });
}

function markFavorite(love_id, status){
    $.getJSON('review-json.php?action=mark_favorite',
              {love_id: love_id,  status: status},
              function(json){});
}

function updateFavoriteWhy(love_id, why){
    $.getJSON('review-json.php?action=favorite_why',
              {love_id: love_id,  why: why},
              function(json){});
              
}

// updates "pick () more" message
function updateToPick(){

    var love_picked = $('#love-selector input:checked').size();
    if(love_picked == 0){
        // update messages to initial ones
        $('#love-left').html(pick_message_start);
        
        // it means user is in commenting step but has deselected all love
        // going back to step 0
        if(wizard.current_step == 1 || wizard.current_step == 2){
            // at the beginning of the period, the list of loves is empty
            if ($('#love-selector input').length > 0) {
                wizard.updateStep(0);
            }
        }
    }else{
        if(wizard.current_step == 0){
            $('#love-left').html(pick_message); 
        }
    }
    
    if(love_picked == 0){
        
        // update messages to initial ones
        $('#love-left').html(pick_message_start);
        
        // it means user is in commenting step but has deselected all love
        // going back to step 0
        if(wizard.current_step == 1 || wizard.current_step == 2){
            if ($('#love-selector input').length > 0) {
                wizard.updateStep(0);
            }
        }
    }else{
        if(wizard.current_step == 0){
            $('#love-left').html(pick_message); 
        }
    }
    
    var to_pick = $('#reviewForm').data("love_limit") - love_picked;
    if(to_pick == 0){
        $('#love-left').hide();

        // closing selector
        $('#love-selector').dialog('close');
        $('#love-checkbox').attr('src', 'images/checked.png').css('background-color', checkboxBackground);
        
        // user has picked all love - going to next step in wizard
        // update wizard only if this is a start
        if(wizard.current_step == 0){
            wizard.updateStep(1);
        }
    }else{
        $('#love-left').show();
        $('.to-pick').text(to_pick);
        $('#love-checkbox').attr('src', 'images/empty.png').css('background-color', 'inherit');
    }
    $('.love-chosen').text(love_picked);
    $('.love-limit').text( $('#reviewForm').data("love_limit") );
}

function updateCommentLinks(bUpdateReviewStatus){

    // if limit is exceeded - hide all other "add comment links"
    var comments_count = $('#reviewForm img[src="images/star_on.gif"]').size();
    var love_picked = $('#love-selector input:checked').size();
    var to_comment = love_picked - comments_count;
    $('.commented').text(comments_count);
    $('.to-comment').text(to_comment);
    
    if(comments_count == love_picked && love_picked > 0){
        if(wizard.current_step == 1){
            wizard.updateStep(2);
        }
        $('.add-comment').hide();
        $('#comment-checkbox').attr('src', 'images/checked.png').css('background-color', checkboxBackground);
        
    // go to previous step in the wizard
    }else if(comments_count < love_picked){
        if(wizard.current_step == 2){
            $('.next').hide();
            wizard.updateStep(1);
        }
        $('#comment-checkbox').attr('src', 'images/empty.png').css('background-color', 'inherit');
    }
    if ( bUpdateReviewStatus !== undefined && bUpdateReviewStatus === true ) {
        updateReviewStatus( (comments_count == love_picked) , ($('#reviewForm').data("love_limit") == love_picked) );
    }
}

var wizard = {
    user_review_id: 0,
    current_step: 0,
    prev_step: 0,
    visible: false,
    
    init: function(user_review_id, current_step){
        wizard.user_review_id = user_review_id;
        wizard.current_step = current_step - 0;
        wizard.prev_step = 0;
    },
    
    // looking at the steps to show or hide wizard
    showHide: function() {
        if($.currentLowerTab == '.userTab'){
            // if we pressed "Back" button - don't show floater again
            if ( wizard.prev_step != 3 || wizard.current_step == 4 ){
                // End of self review but not just done, do not display the floater
                if (wizard.prev_step != 2 && wizard.current_step == 3) {
                    wizard.hide();
                } else {
                    wizard.show();
                }
            }
        } else {
            wizard.hide();
        }
    },
    // updates wizard text and percentage and calls stepAction
    // to perform additional hide/show actions
    updateDisplay: function(){
       $('#wizard-text').html(wizard_strings[wizard.current_step]);
        wizard.showHide();
//      wizard.stepAction();   :with empty review recursive loop
    },
    
    updateStep: function(wizard_step){
       wizard.prev_step = wizard.current_step;
        wizard.current_step = wizard_step;
        wizard.updateDisplay();
        $.get('review-json.php?action=update_wizard',
                  {user_review_id: wizard.user_review_id,  wizard_step: wizard_step});  
    },
    
    // performs additional actions based on current wizard step
    // for example when we finished commenting - show "Next" button
    stepAction: function(){
        switch(wizard.current_step){

        // adding pick more 
        case 0:
            updateToPick();
            updateCommentLinks();
            $('.next').hide();
            break;
        
        case 1:
            $('.love-chosen').text($('#love-selector input:checked').size());
            updateToPick();
            updateCommentLinks();
            break;

        // user has finished commenting - show next button to go to sliders
        case 2:
            updateToPick();
            updateCommentLinks();
            $('.next').show();
            
            // we are replacing it with "Next"
            $('#hide-floater').hide();
            break;
        
        // user pressed "Next" button - hide loves div and show sliders
        case 3:
            if(period_info.user_status == 1){
                fillDoneStats();
            }
            $.resizeLower();
            if (wizard.prev_step == 2) {
                wizard.prev_step=0;
                $.switchTabs.tabChangeUsingClass( "peer_review" );
                wizard.showFloater();
            } else {
                wizard.hideFloater();
            }
            break;
            
        case 4: 
            if(period_info.user_status == 1){
                fillDoneStats();
            }
            $('#review-status-message').show();
            hideActionLink();
            $.resizeLower();
            break;
        }
        
    },
    
    show: function(){
        wizard.visible = true;
        wizard.showFloater();
    },
    
    hide: function(){
        wizard.visible = false;
        wizard.hideFloater();
    },
    
    showFloater: function(){
        if(wizard.visible && period_info.user_status != 1 && period_info.time_status == 1){
            $('#wizard-floater').show().fadeIn(300);
            $('#wizard-floater').center();
        }

    },
    
    hideFloater: function(){

        // if you use jquery to fadeout an element it is
        // still there but not visible so effects like hover on underlaying
        // elements wouldn't work - you have to completely hide them
        $('#wizard-floater').fadeOut(300,function() {
                $(this).hide();
            });
    }
    
};

function showSliders(){
    $('#rewarder-div').css('position', 'relative');
    $('#rewarder-div').css('left', 0);
    $('#loves').hide();
    $('.next').hide();
    $('#rewarder-div').show();
    $('#back').show();
    $('#publish').show();
    $.resizeLower();
}

function hideSliders(){
    $('#loves').show();
    $('#rewarder-div').hide();
    $('#back').hide();
    $('#publish').hide();
    $.resizeLower();
}

function setPeerReviewStatus(peer_status,fAfter){
    $.get('review-json.php?action=peer_review_status',
        {user_review_id: period_info.user_review_id,
            user_review_peer_status: peer_status},function(){
                if (fAfter) fAfter();
            }); 
}

function setReviewCompleted(){
    $.get('review-json.php?action=review_completed',
        {user_review_id: period_info.user_review_id}); 
}

function setReviewStarted(){
    $.get('review-json.php?action=review_started',
        {user_review_id: period_info.user_review_id}); 
}

function updateReviewStatus( bAllCommented, bAllLoves ) {
    if ( bAllCommented && bAllLoves ) {
        setReviewCompleted();
    } else {
        setReviewStarted();
    }
    oReview.bNeedReload = true;
}

// fills love limit, love selected, love commented for done review 
function fillDoneStats(){
    $('#love-checkbox').attr('src', 'images/empty.png');
    $('#comment-checkbox').attr('src', 'images/empty.png');
    var review_stats = period_info.user_stats;
    $('.love-limit').text(review_stats.love_limit);
    $('.love-chosen').text(review_stats.love_picked);
    $('.commented').text(review_stats.love_commented);
    if(review_stats.love_limit == review_stats.love_picked){
        $('#love-checkbox').attr('src', 'images/checked.png').css('background-color', checkboxBackground);
    }
    if(review_stats.love_picked == review_stats.love_commented 
        && review_stats.love_picked != 0){
        $('#comment-checkbox').attr('src', 'images/checked.png').css('background-color', checkboxBackground);
    }

}

function showNoData(){
    $('#no-data-holder').show();
    $('#self_review_progress_holder').hide();
}
function hideNoData(){
    $('#self_review_progress_holder').show();
    $('#no-data-holder').hide();
}

function hideActionLink() {
    $('#add-love').hide();
    $('.add-comment').hide();
    $('.remove-comment').hide();
    $('.favorite-why').replaceWith(function(){
        return '<div class="love-comment-text">' + $(this).val() + '</div>';
    });
}

(function($){
    $.fn.extend({
        center: function () {
            return this.each(function() {
                var top = ($(window).height() - $(this).outerHeight()) / 2;
                var left = ($(window).width() - $(this).outerWidth()) / 2;
                $(this).css({margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
            });
        }
    }); 
})(jQuery);
