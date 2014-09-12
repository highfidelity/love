
    var rewarder = {
        initialLoad: true,
        period_id: -1,
        period_closed: 0,
        user_reviews_peer_status: 0,
        userHeight: 0,
        rewarderList: [],

        isPeriodOpen: function() {
            if ( this.period_closed == 0) return true;
            return false;
        },
        canWeUpdateGraph: function() {
            if ( this.isPeriodOpen() && this.user_reviews_peer_status != 2 && this.user_reviews_peer_status != 3) {
                return true;
            }
            return false;
        },
        
        addNewUser: function(newUser, pos) {
            newUser.points = newUser.points | Math.floor(100/rewarder.totalLove*parseInt(newUser.lovecount)) | 0;
            $.pS.makePoint(newUser);
            rewarder.userHeight = 22;
        },


        /*
         * combinedList has the structure:
         *   combinedList[ user_id ] = [ enum:{0=remove,1=keep,2=new}, posDelta, user ]
         */
        combineUserLists: function(oldList, newList) {
            var user_id;
            var combinedList = new Array();

            /* Fill with old users */
            for (var i = 0; i < oldList.length; i++) {
                user_id = oldList[i].id;
                combinedList[user_id] = new Array(0, i);
            }

            for (var i = 0; i < newList.length; i++) {
                user_id = newList[i].id;
                /* Existing users */
                if (combinedList[user_id] != undefined) {
                    combinedList[user_id][0] = 1;
                    combinedList[user_id][1] = i;
                /* Removed users */
                } else {
                    combinedList[user_id] = new Array(2, i, newList[i]);
                }
            }

            return combinedList;
        },

        deleteRewarderUser: function(userid) {
            // Remove the user dropdown and get it again
            // directly from the DB
            $('#addr').html('<img src="images/loader.gif">');
            $.blockUI({
                message: '', 
                timeout: 20000, 
                overlayCSS: { opacity: 0.01 }
            });
            $('#user-list').fadeOut('slow',function() {
                $(this).remove();
            });
            $.ajax({
                url: reviewConfig.getReviewUrl() + '/rewarder-json.php?action=update-rewarder-user',
                data: 'id='+userid+'&delete=1&period_id='+rewarder.period_id,
                dataType: 'json',
                type: "POST",
                cache: false,
                success: function(json) {
                    if (json && json !== null) {
                        rewarder.addUsersBox();
                        rewarder.updateRewarderList(json[1],true);
                    } else {
                        alert("Error in deleteRewarderUser, json"+json);
                    }
                    $('#addr img').remove();
                    $.unblockUI();
                }
            });
        },
        
        /* 
         * Function addUsersBox
         * Does an Ajax call to get the current users, except
         * for those that are already rewarded.
         * 03/06/2010 <andres>
         */
        addUsersBox: function() {
            if ( rewarder.canWeUpdateGraph()  ) {
                $('#addr').html('<img src="images/loader.gif">');
                $.blockUI({
                    message: '', 
                    timeout: 20000, 
                    overlayCSS: { opacity: 0.01 }
                });
                $.ajax({
                    url: reviewConfig.getReviewUrl() + '/rewarder-json.php?action=get-user-list&period_id='+rewarder.period_id,
                    dataType: 'json',
                    type: "POST",
                    cache: false,
                    success: function(data) {
                        var userlist = $('<select id="user-list" name="userbox"><option value="0">Add Co-worker</option></select>');
                        for (var i = 0; i < data.length; i++) {
                            if(data[i].nickname) {
                                userlist.append($('<option value="'+data[i].id+'">'+data[i].nickname+'</option>'));
                            }
                        }

                        $('#addr').append(userlist);

                        // Else update dropdown
                        $('#user-list').change(function(){
                            var userid = $(this).val(),
                                oThis=$(this);
                            if (userid != 0) {
                                setTimeout(function() {
                                    oThis.find('option:selected').remove();
                                    rewarder.updateRewarderUser(userid, 0, 0);
                                }, 50);
                            }
                        });
                        $('#user-list').comboBox();
                        $('#addr img').remove();
                        $.unblockUI();
                   }
                });
            } 
        },

        displayRewarderUserDetail: function(userid) {
            // Show love sent to clicked user
            // 01-MAY-2010 <andres>
            var love_sent;
            $.ajax({
                async: false,
                url: reviewConfig.getReviewUrl() + '/helper/get-love.php',
                data: 'id='+userid,
                dataType: 'json',
                type: "POST",
                cache: false,
                success: function(json) {
                    love_sent = json;
                }
            });

            $.ajax({
                url: reviewConfig.getReviewUrl() + '/rewarder-json.php?action=get-rewarder-user-detail',
                data: 'id='+userid,
                dataType: 'json',
                type: "POST",
                cache: false,
                success: function(json) {
                    var name = json[0];
                    var detailHTML =
                        '<div id="detail" title="Review Detail for '+json[0]+'">' +
                        '<div id="audit" style="float:left;">' +
                        '  <h3>Distribution</h3>'+
                        '  <div style="overflow:auto; width=100%; height:400px;">'+
                        '  <table style="text-align: left">' +
                        '    <tr class="table-hdng"><th style="width: 120px">Name</th><th style="width: 100px">Given to</th><th style="width: 100px">Received from</th></tr>';

                    for (var i = 0; i < json[1].length; i++) {
                        var received = 0;
                        if (json[1][i].received_points != null) {
                            received = json[1][i].received_points;
                        }
                        detailHTML += '    <tr><td>'+json[1][i].nickname+'</td><td>'+json[1][i].points+' points</td><td>'+received+' points</td></tr>';
                    }

                    // Show love sent to clicked user
                    // 01-MAY-2010 <andres>
                    detailHTML += '  </table></div></div>' +
                        '  <div id="love" style="float:left; margin-left:15px;">' +
                        '  <h3>Love I sent ' +name+ '</h3>' +
                        '  <div style="overflow:auto; width=100%; max-height:300px;">'+
                        '  <table style="text-align: left; margin-bottom:10px;">' +
                        '    <tr class="table-hdng"><th>Why</th><th style="width: 80px;">When</th></tr>';

                    for (var i = 0; i < love_sent[0].love.length; i++) {
                        var json_when = love_sent[0].love[i].when;
                        var when = relativeTime(json_when);
                        detailHTML += '    <tr><td>'+love_sent[0].love[i].why+'</td><td>'+when+'</td></tr>';
                    }

                    if (love_sent[0].love.length == 0) {
                        // Add a no love sent message
                        detailHTML += '    <tr><td style="text-align:center;" colspan="2">No love sent to '+name+'</td></tr>';
                    }

                    detailHTML += '  </table>'+
                    '  </div>'+
                    '  <h3>Love everyone else sent to '+name+'</h3>'+
                    '  <div style="overflow:scroll; height: 300px;">' +
                    '  <table style="text-align: left">' +
                    '      <tr class="table-hdng"><th>Why</th><th style="width: 80px;">When</th></tr>';

                    for (var i = 0; i < love_sent[1].love.length; i++) {
                        var json_when = love_sent[1].love[i].when;
                        var when = relativeTime(json_when);
                        detailHTML += '    <tr><td>'+love_sent[1].love[i].why+'</td><td>'+when+'</td></tr>';
                    }
                
                    if (love_sent[1].love.length == 0) {
                        // Add a no love sent message
                        detailHTML += '    <tr><td style="text-align:center;" colspan="2">No love sent to '+name+'</td></tr>';
                    }
                
                    detailHTML += '    </table>'+
                                  '    </div>'+
                    '</div>';

                    detailHTML +=
                        '    </table></div></div>' +
                        '</div>';
                    var detail = $(detailHTML).dialog({ modal: true, width: 'auto', height: 'auto' });
                }
            });
        },

        loadAuditList: function(fAfter) {
            $.ajax({
                url: reviewConfig.getReviewUrl() + '/rewarder-json.php?action=get-audit-list',
                dataType: 'json',
                type: "POST",
                cache: false,
                success: function(json) {
                    rewarder.updateRewarderList(json,true,fAfter);
                }
            });
        },

        loadRewarderList: function(period_id,fAfter) {
            if (!period_id) {
                period_id=-1;
            }
            $.ajax({
                url: reviewConfig.getReviewUrl() + '/rewarder-json.php?action=get-rewarder-list&period_id='+period_id,
                dataType: 'json',
                type: "POST",
                cache: false,
                success: function(json) {
                    var fNewAfter = function() {
                        rewarder.updateStateButtons();
                        if (fAfter) fAfter();
                    };
                    if (!json || json === null || !json[2]) {
                        fNewAfter();
                        return;
                    }
                    if(json[2].start_date) {
                        rewarder.setHelpPeriod(json[2].sd, json[2].ed);
                        $.pS.setTitle(json[2].sd, json[2].ed);
                    }
                    // if period_id was = -1, we get the current period id
                    if(json[2].id) {
                        rewarder.period_id = json[2].id;
                    }
                    if (!json[1] || json[1] === null || !json[1].length) {
                        rewarder.user_reviews_peer_status=1;
                        if ( window.setPeerReviewStatus !== undefined ) {
                            setPeerReviewStatus(1,function(){
                                rewarder.populateRewarderList(period_id,false, fNewAfter);
                            });
                        } else {
                            rewarder.populateRewarderList(period_id,false, fNewAfter);
                        }
                    } else {
                        rewarder.updateRewarderList(json[1],true, fNewAfter);
                    } 
                }
            });
        },
        
        // user_reviews_peer_status = 
        //      0 -> no records in rewarder_distribution table 
        //      1 -> records and unpublished
        //      2 -> published
        //      3 -> paid
        updateStateButtons: function() {
            if ( !rewarder.isPeriodOpen() || rewarder.user_reviews_peer_status == 3) {
                $('#addr,#resetr,#updatr,#unpublishr,#publishr').remove();
            } else {
                if (  rewarder.user_reviews_peer_status ==  2  ) {
                // review is published, show only unpublish button
                    $('#resetr,#updatr').hide();
                    $('#addr').animate({opacity: 0})
                     $('#unpublishr').show();
                    $('#publishr').hide();
               } else {
                    // review is unpublished, all the buttons are available
                    $('#resetr,#updatr').show();
                    $('#addr').animate({opacity: 1})
                    $('#unpublishr').hide();
                    $('#publishr').show();
                }
            }         
            if (  rewarder.user_reviews_peer_status ==  2  ) {
                $('#peer_checkbox').attr('src', 'images/checked.png').css('background-color', checkboxBackground);
            } else {
                $('#peer_checkbox').attr('src', 'images/empty.png').css('background-color', checkboxBackground);
            }
            if ( rewarder.companyPeriod === true ) {
                $('#resetr,#updatr,#unpublishr,#publishr').remove();
            }
       },
        

        populateRewarderList: function(period_id,bNormalized,fAfter) {
            $.pS.init();
            $.ajax({
                url: reviewConfig.getReviewUrl() + '/rewarder-json.php?action=populate-rewarder-list&period_id='+period_id+'&normalized='+bNormalized,
                dataType: 'json',
                type: "POST",
                cache: false,
                success: function(json) {
                    if (json && json !== null) {
                        if ( json.error ) {
                            var sMess = "Error in populate process: "+json.error;
                                alert(sMess);
                            $("#raftest").prepend("<div class='errorInReview totalLove0'>"+sMess+"</div>");                        
                        } else {
                            rewarder.setHelpPeriod(json[0].sd, json[0].ed);
                            $.pS.setTitle(json[0].sd, json[0].ed);
                            rewarder.period_id = json[0].id;
                            rewarder.totalLove = parseInt(json.total) | 0;
                            rewarder.updateRewarderList(json[1],false,function(){
                                $.pS.savePoints(function() {
                                    $("#raftest .totalLove0").remove();
                                    if (rewarder.totalLove == 0) {
                                        $("#raftest").prepend("<div class='errorInReview totalLove0'>The list of Loves in this period is empty !</div>");
                                    }
                                   if ( fAfter ) fAfter();
                                });
                            });
                        }
                    } else {
                        if (fAfter) fAfter();
                    }
                }
            });
        },
        
        setHelpPeriod: function(start_date, end_date){
            $(".rewarderGraphHelp .startDate").text(start_date);
            $(".rewarderGraphHelp .endDate").text(end_date);
        },

        toggleRewarderAuditor: function(el){
            var userid = el.val();
            $.ajax({
                url: reviewConfig.getReviewUrl() + '/rewarder-json.php?action=update-rewarder-auditor',
                data: 'id='+userid,
                type: "POST",
                cache: false,
                success: function(data) {
                    var opt = el.find('option:selected');
                    if (opt.text()[0] == '*') {
                        opt.text(opt.text().substr(2));
                    } else {
                        opt.text('* '+opt.text());
                    }
                    opt.attr('selected','');
                }
            });
        },

        updateRewarderList: function(newRewarderList,bSave,fAfter) {
            if (newRewarderList.length > 40 ) {
                $.pS.width= 20 * newRewarderList.length;
            } else {
                $.pS.width= 800;
            }

            /* If this is a fresh load of the rewarder list, just fill all the users. */
            if (rewarder.rewarderList.length == 0 && newRewarderList.length > 0) {
                var pos = 0;
                var rt = 0;
                for (var i = 0; i < newRewarderList.length; i++){
                    newRewarderList[i].points = newRewarderList[i].points | Math.floor(100/rewarder.totalLove*parseInt(newRewarderList[i].lovecount)) | 0;
                    rt += newRewarderList[i].points;
                    this.addNewUser(newRewarderList[i], i);
                }
                $.pS.redoPoints(bSave,true);
            /* Real changes including add new users, remove out old users, reposition users. */
            } else {
                var combinedList = rewarder.combineUserLists(rewarder.rewarderList, newRewarderList);

                var animateFadeIn = function() {
                    var j = 0;
                    for (var i in combinedList) {
                        if (combinedList[i][0] == 2) {
                            rewarder.addNewUser(combinedList[i][2], combinedList[i][1]);
                        }
                    }
                };
                animateFadeIn();
            }
            if (rewarder.rewarderList.length == 0 && newRewarderList.length >= 0){
                if ($('#user-list').length == 0) {
                    rewarder.appendAddUser(newRewarderList.length);
                    $('#rewarder-chart').css('height',($('#rewarder-chart').children().length * rewarder.userHeight)+'px');
                }
            }

            this.rewarderList = newRewarderList;
            if (fAfter) fAfter();
        },

        updateRewarderUser: function(userid, points_val, points_perc){
            if ( this.canWeUpdateGraph() === false ) {
                return;
            }
            $('#addr *:visible').addClass("wasVisible").hide();
            $('#addr').append('<img src="images/loader.gif">');
            $.blockUI({
                message: '', 
                timeout: 20000, 
                overlayCSS: { opacity: 0.01 }
            });
            $.ajax({
                url: reviewConfig.getReviewUrl() + '/rewarder-json.php?action=update-rewarder-user',
                data: 'id='+userid+'&points_val='+points_val+'&points_perc='+points_perc+"&period_id="+rewarder.period_id,
                dataType: 'json',
                type: "POST",
                cache: false,
                success: function(json) {
                    rewarder.updateRewarderList(json[1],true);
                    $('#addr img').remove();
                    $('#addr *.wasVisible').show();
                    $.unblockUI();
                }
            });
        },

        updateRewarderUsers: function(userlist,fAfter){
            if ( this.canWeUpdateGraph() === false ) {
                return;
            }

            $.blockUI({
                message: '<div class="PeriodLoadergifContainer" ><img  src="images/loader.gif"> Saving ...</div>', 
                timeout: 20000, 
                css: { 
                    border: 'none' ,
                    width: '10%',
                    left: '45%'
                } ,
                overlayCSS: { opacity: 0.01 }
            });
            $.ajax({
                url: reviewConfig.getReviewUrl() + '/rewarder-json.php?action=update-rewarder-users',
                data: {list: userlist,
                    period_id: rewarder.period_id},
                dataType: 'json',
                type: "POST",
                cache: false,
                success: function(json) {
                    if (!json || json === null || json.error  ) {
                        $.unblockUI();
                        alert("Error: save not done.");
                        if (fAfter) fAfter();
                    } else {
                        rewarder.updateRewarderList(json[1],true,function() {
                            $.unblockUI();
                            if (fAfter) fAfter();
                        });
                    }
                },
                error: function(){
                    $.unblockUI();
                    alert("Error: save not done.");
                    if (fAfter) fAfter();
                }
            });
        },
        

        appendAddUser: function(pos) {
            // If this is the page load, we add the user dropdown.
            if (rewarder.initialLoad) {
                rewarder.initialLoad=false;
                rewarder.addUsersBox();
            }
        },

        // Show love sent to clicked user
        // 01-MAY-2010 <andres>
        showLove: function(userid, name) {
            $.ajax({
                url: reviewConfig.getReviewUrl() + '/helper/get-love.php',
                data: 'id='+userid+'&period_id='+ rewarder.period_id,
                dataType: 'json',
                type: "POST",
                cache: false,
                success: function(json) {
                    if (!json || json === null || !json[0] || !json[1] || !json[0].love || !json[1].love  ) {
                        return;
                    }
                    var detailHTML =
                        '<div id="detail" title="Love sent to '+name+'">' +
                        '  <h3>Love I sent ' +name+ '</h3>' +
                        '  <div style="overflow:auto; width=100%; max-height:300px;">'+
                        '  <table style="text-align: left; margin-bottom:10px;">' +
                        '    <tr class="table-hdng"><th>Why</th><th style="width: 80px;">When</th></tr>';

                    for (var i = 0; i < json[0].love.length; i++) {
                        var json_when = json[0].love[i].when;
                        var when = relativeTime(json_when);
                        detailHTML += '    <tr><td>'+json[0].love[i].why+'</td><td>'+when+'</td></tr>';
                    }

                    if (json[0].love.length == 0) {
                        // Add a no love sent message
                        detailHTML += '    <tr><td style="text-align:center;" colspan="2">No love sent to '+name+'</td></tr>';
                    }

                    detailHTML += '  </table>'+
                                  '  </div>'+
                                  '  <h3>Love everyone else sent to '+name+'</h3>'+
                                  '  <div style="overflow:scroll; height: 300px;">' +
                                  '  <table style="text-align: left">' +
                                  '      <tr class="table-hdng"><th>Why</th><th style="width: 80px;">When</th></tr>';

                    for (var i = 0; i < json[1].love.length; i++) {
                        var json_when = json[1].love[i].when;
                        var when = relativeTime(json_when);
                        detailHTML += '    <tr><td>'+json[1].love[i].why+'</td><td>'+when+'</td></tr>';
                    }

                    if (json[1].love.length == 0) {
                        // Add a no love sent message
                        detailHTML += '    <tr><td style="text-align:center;" colspan="2">No love sent to '+name+'</td></tr>';
                    }

                    detailHTML += '    </table>'+
                                  '    </div>'+
                                  '</div>';
                    var detail = $(detailHTML).dialog({ modal: true, width: 'auto', height: 'auto' });
                }
            });
        },

        /**
        * Get User review data, and populate popup
        * @url the URL from which the review will be loaded
        * nabbed and modded from love/js/review.js
        */
        showReview: function(user_id, nickname) {
            // if tooltip is not set - showing user popup
            // if we have tooltip
            var tooltip = false;
            var user_info;
            period_id = rewarder.period_id;
            $.ajax({
                async: false,
                url: love_url + '/review-json.php?action=user_info', 
                data: {user_id: user_id, period_id: period_id},
                dataType: 'json',
                success: function(json){
                    if (!json ||json === null) return;
                    $('#user-love-list').empty();
                    rewarder.setUserStatData(json,$('#user-popup'));
                    $('#user-popup').dialog('option','title',json.love_statistic.love_user_info.nickname+ 
                        " review <span class='username_popup_review' >("+json.love_statistic.love_user_info.username+")</span>");
                    if(json.user_status == 1){
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
        },
        setUserStatData: function(json,inElement) {
            if (json.love_statistic && json.love_statistic !== null) {
                $('.love_user_received .number',inElement).text(json.love_statistic.love_user_received);
                $('.love_user_unique_senders .number',inElement).text(json.love_statistic.love_user_unique_senders);
                $('.love_company_received',inElement).text(json.love_statistic.love_company_received);
                $('.love_company_unique_senders',inElement).text(json.love_statistic.love_company_unique_senders);
                $('.user_love_stat_info_user_img',inElement).html('<img src="thumb.php?t=sUSD&src='+
                    json.love_statistic.love_user_info.image +
                    '&w=100&h=100&zc=0" width="100" height="100" alt="profile" />');
            }
            $(".user_love_stat_period div",inElement).html($('#period-title').html());
        },
        
        getAmountInDollar: function() {
            return this.amountInDollar;
        },

        formatAsCurrency: function(amount)
        {
            var delimiter = ","; // replace comma if desired
            var a = amount.split('.',2);
            var d = '';
            if(a && a.length == 2) {
               d = a[1];
            }
            var i = parseInt(a[0]);
            if(isNaN(i)) { return ''; }
            var minus = '';
            if(i < 0) { minus = '-'; }
            i = Math.abs(i);
            var n = new String(i);
            var a = [];
            while(n.length > 3)
            {
                var nn = n.substr(n.length-3);
                a.unshift(nn);
                n = n.substr(0,n.length-3);
            }
            if(n.length > 0) { a.unshift(n); }
            n = a.join(delimiter);
            if(d.length < 1) { amount = n; }
            else { amount = n + '.' + d; }
            amount = minus + amount;
            return amount;
        },
        
        initRewarder: function(period_info,fAfter) {
            this.initialLoad= true;
            this.period_id = -1;
            this.period_closed = 0;
            this.user_reviews_peer_status = 0;
            this.userHeight= 0;
            this.amountInDollar=100;
            this.rewarderList= [];
            if ( period_info ) {
                this.period_id = period_info.id;
                this.period_closed = period_info.status;
                this.user_reviews_peer_status = period_info.peer_status;
                this.amountInDollar = period_info.amountInDollar;
            }

            $.pS.init(true);
            initializeTabs();

            rewarder.loadRewarderList(this.period_id,fAfter);

            $('#resetr').click(function(e) {
                var me=$(this);
                me.attr("disabled",true)
                .parent().parent().append('<img id="resetLoader" style="position:relative;top:-190px;left:300px;" src="images/loader.gif">');
                $.blockUI({
                    message: '', 
                    timeout: 20000, 
                    overlayCSS: { opacity: 0.01 }
                });
                e.preventDefault();
                rewarder.rewarderList = [];
                rewarder.populateRewarderList(rewarder.period_id,false,function(){
                    me.attr("disabled",false);
                    $("#resetLoader").remove();
                    $.unblockUI();
                });
            });

            $('#publishr').click(function(e) {
                e.preventDefault();
                setPeerReviewStatus(2,function(){
                    oReview.loadPeriod();
                });
            });
            $('#unpublishr').click(function(e) {
                e.preventDefault();
                setPeerReviewStatus(1,function() {
                    oReview.loadPeriod();
                });
            });
            
            $('#user-popup').dialog({autoOpen: false, width: 600});

            // Load tabs
            $('#review-tabs').tabs('select', currentTab);
            
            // The code below handles a click on the
            // History link. It switches the tab if
            // necessary, cleans the select element
            // and inserts reports dates to the select element
            // When a report date is selected a graph is show 
            // with the results for that period
            // 17-MAY-2010 <Yani>
            if($('#reportsHistory').length > 0) {
                $('#reportsHistory').click(function() {
                    if($( "#review-tabs" ).tabs( "option", "selected" ) != 1) {
                        $( "#review-tabs" ).tabs( "option", "selected", 1 );
                    }
                  
                    if($('#period-box').length > 0) {
                        $.getJSON(reviewConfig.getReviewUrl() + '/helper/get-rewarder-data.php?action=periods', function(json){
                            $('#period-box').empty();
                            $('#period-box').append('<option value="0">Select Rewarder Period</option>');
                            var c = 0;
                            $.each(json, function(key, value){
                                key = value;
                                if(c > 0){
                                    value = value.replace('rewarder_results_', '');
                                    value = value.split('_').join('/');
                                    $('#period-box').append($("<option></option>")
                                                    .attr("value",key)
                                                    .text(value));
                                }
                                c++;
                            });
                            handleRewardPeriod('period-box');
                        });
                    }
                });
            }
            
        }
        
    };

    // Prepare the graphic
    function setupGraph(p,period_id,fAfter) {
        var graphPanelId = 'timeline-graph',
            data;
        if ( !period_id ) { 
            data = { period: p };
        } else {
            data = { period_id: period_id }
        }
        $('#'+graphPanelId).empty();
        LoveChart.initialize(graphPanelId, 840, 320, 10, 100);
        $.ajax({
            type: "POST",
            url: reviewConfig.getReviewUrl() + '/helper/get-rewarder-data.php',
            data: data,
            dataType: 'json',
            success: function(data) {
                // If we retrieved our data, show it on the graph.
                if ( data && data.points && data.points.length == 0 ) {
                    $('#timeline-graph').html("<div class='errorInReview'>History not yet available.<br\></div>")
                } else {
                    LoveChart.load(data);
                }
                if (fAfter) fAfter();
            },
            error: function(xhdr, status, err) {
                if (fAfter) fAfter();
                $('#again').click(function(e){
                    $("#loader_img").css("display","none");
                    e.stopPropagation();
                    return false;
                });
            }
        });
    }

    function initializeTabs() {
        var getRewarderPeriod = function(){
            // Get rewarder periods from DB.
            $.ajax({
                type: 'POST',
                url: reviewConfig.getReviewUrl() + '/helper/get-rewarder-data.php',
                data: { action: 'periods' },
                dataType: 'json',
                success:function (json) {
                    if (!json  ||json === null) {
                        return;
                    }
                    $('#period-box').empty();
                    // Add default option
                    // 17-MAY-2010 <Yani>
                    $('#period-box').append('<option value="0">Select Rewarder Period</option>');
                    for (var i = 0; i < json.length; i++) {
                        // Remove the first 16 chars from the item
                        var option_text = json[i].formatted_date;
                        var option_value = json[i].paid_date;
                        // Construct the item and append it
                        var option = '<option value="' + option_value + '">' + option_text + '</option>';
                        $('#period-box').append(option);
                    }
                    // catches change events on
                    // select element. we assign that handler
                    // after the data has been received from the
                    // ajax request.
                    // 17-MAY-2010 <Yani>
                    handleRewardPeriod('period-box');
                }
            });
        };
        // review tabs is not always there
        if ($("#review-tabs").length == 0) {
            if ($('#period-box').length != 0) {
                getRewarderPeriod();
            }
        } else {
            $("#review-tabs").tabs({selected: 0,
                select: function(event, ui) {
                    if(ui.index == 0) {
                        currentTab = 0;
                    } else {
                        currentTab = 1;
                        getRewarderPeriod();
                    }
                }                                                                                                                                                       
            });
        }
    }

    function relativeTime(x) {
        var plural = '';

        var mins = 60, hour = mins * 60; day = hour * 24,
            week = day * 7, month = day * 30, year = day * 365;

        if (x >= year) { x = (x / year)|0; dformat="yr"; }
        else if (x >= month) { x = (x / month)|0; dformat="mnth"; }
        else if (x >= day) { x = (x / day)|0; dformat="day"; }
        else if (x >= hour) { x = (x / hour)|0; dformat="hr"; }
        else if (x >= mins) { x = (x / mins)|0; dformat="min"; }
        else { x |= 0; dformat="sec"; }
        if (x > 1) plural = 's';
        return x + ' ' + dformat + plural + ' ago';
    }

    // This function takes the id of the passed
    // parameter and assigns onchange event to it
    // When a valus is selected, the function checks
    // to see if the value is the default one (0) and if
    // it is not displays a graph.
    // 17-MAY-2010 <Yani>
    var handleRewardPeriod = function(id){
        $('#'+id).change(function(){
            if($('#'+id+' option:selected').val() != 0){
                setupGraph($('#'+id+' option:selected').val());
            }
        });
    };


