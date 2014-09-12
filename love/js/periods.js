
/***
Define the object LM_periods
This object is used to display periods records in a grid.
Several actions are available to change the status of the period, add new period, remove a period, ...
The object manage 2 types of periods : Review periods and Campaign periods

The file is also used to manage redeem grid and team members grid.
Those jscript objects could be moved in different files with only one class in each file.
    - Review period grig (object LM_periods with type = review),
    - Campaign period grid (object LM_periods with type = campaign),
    - Redeem grid  (object LM_periods with type = redeem)
    - Team members grid (object LM_members)
***/

(function(win){
	var block = function(bBlock){
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
        formatAsCurrency = function(amount)
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
        roundAndFormat = function(amount) {
            var newAmount = parseInt(amount * 100) / 100;
            newAmount = this.formatAsCurrency(newAmount+' ');
            return newAmount;
        },
        getRedeemTotal = function(fAfter) {
            var oThis=this;
            $.getJSON('campaign-json.php?action=get_redeem_total',
                {},
                function(json){
                    var oT = oThis;
                    if ( !json || json === null ) {
                        alert("json null in getRedeemTotal");
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $(".lifetimeTotalRedeemed").html(oT.roundAndFormat(json.total_redeemed));
                        $(".pendingTotalRedeemed").html(oT.roundAndFormat(json.total_redeem_pending));
                        $(".incartTotalRedeemed").html(oT.roundAndFormat(json.total_redeem_in_cart));
                        $(".totalAvailableRedeemed").html(oT.roundAndFormat(json.total_redeem_available));
                        if (json.total_redeem_in_cart == "0") {
                            $(".redeemButtonArea input").attr("disabled",true);
                        } else {
                            $(".redeemButtonArea input").removeAttr("disabled");
                        }
                    }
                    if (fAfter) fAfter();
                }
            );
        },
    
        exportCampaign = function(idPeriod,fAfter) {
            window.open('campaign-json.php?action=export_redeem_by_campaign&period_id='+idPeriod, '_blank');
        },
    
        publishCampaign = function(idPeriod,fAfter) {
            if ($(".simulation:checked").length == 0){
                alert("Please select a type of chart using radio buttons.");
                return;
            }
            var oThis=this;
            $.getJSON('campaign-json.php?action=publish_campaign&period_id='+idPeriod,
                {},
                function(json){
                    if ( !json || json === null ) {
                        alert("json null in publishCampaign");
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $("#info_table_update").html("Campaign published !");
                        // This function will called just after the reload of the grid
                        var currentSel = oThis.lastsel;
                        oThis.afterServerAddRow = function(){
                            oThis.lastsel=-1;
                            $("#"+oThis.options.gridID).setSelection(currentSel,true);
                        };
                        $("#"+oThis.options.gridID).trigger("reloadGrid");
                    }
                    if (fAfter) fAfter();
                }
            );
        },
        changeRedeemStatus = function(newStatus,idPeriod,fAfter) {
            /*** Keep this comment, the functionality could come back later (#16400)
        
            var oThis=this;
            $.getJSON('campaign-json.php?action=change_redeem_status&period_id='+idPeriod+'&new_paid_status='+newStatus,
                {},
                function(json){
                    if ( !json || json === null ) {
                        alert("json null in changeRedeemStatus");
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $("#info_table_update").html("Redeem Status changed !");
                    }
                    $("#refresh_"+oThis.options.gridID+" div " ).click();
                    if (fAfter) fAfter();
                }
            );
            ***/
        },
        checkoutSend = function(options,fAfter) {
            var oThis=this;
            $.getJSON('campaign-json.php',
                options,
                function(json){
                    if ( !json || json === null ) {
                        alert("json null in checkoutSend");
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $("#info_table_update").html("Email sent !");
                    }
                    if (fAfter) fAfter();
                }
            );
        },
        changePeriodValidated = function(newStatus,idPeriod,fAfter) {
            var oThis=this;
            $.getJSON('campaign-json.php?action=change_validated_status_period&id='+idPeriod+'&new_validated_status='+newStatus,
                {},
                function(json){
                    if ( !json || json === null ) {
                        alert("json null in changePeriodValidated");
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $("#info_table_update").html("Validated Status changed !");
                    }
                    $("#refresh_"+oThis.options.gridID+" div " ).click();
                    if (fAfter) fAfter();
                }
            );
        },
        changePeriodStatus = function(newStatus,idPeriod,fAfter) {
            var oThis=this;
            $.getJSON('periods-json.php?action=change_status_period&id='+idPeriod+'&new_status='+newStatus,
                {},
                function(json){
                    if ( !json || json === null ) {
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $("#info_table_update").html("Status changed !");
                    }
                    $("#refresh_"+oThis.options.gridID+" div " ).click();
                    if (fAfter) fAfter();
                }
            );
        },
        addMembersToPeriod = function(idPeriod,fAfter) {
            var oThis=this;
         //   alert("User popup list"+idPeriod+"**"+$("#dialogMembersOfPeriod"+idPeriod).length);
             if ($("#dialogMembersOfPeriod"+idPeriod).length == 0) {
                $("<div id='dialogMembersOfPeriod" + idPeriod + "' class='dialogMembersOfPeriod' style='display:none;' ></div>").appendTo("body");
                
                $("#dialogMembersOfPeriod"+idPeriod).dialog({
                    width: 700,
                    height: 500,
                    open: function() {
                        var members_periods;
                        $("#dialogMembersOfPeriod"+idPeriod).html("<div id='membersArea"+idPeriod+"' style='width:90%;height:90%;'>"+
                            "<form class='membersFilterForm'><div class='membersFilter' >Filter: <input type='text' class='membersFilterValue' value='' />"+
                            "<input type='submit' value='Search' class='membersFilterButton periodButton'/>" +
                            "<input type='submit' value='Add All' class='membersAddAllButton periodButton'/>" +
                            "<input type='submit' value='Remove All' class='membersRemoveAllButton periodButton'/>" +
                            "</div></form>"+
                            "<table class='listMembers'></table>"+
                            "<div class='pagerMembers'></div></div>");
                        members_periods = LM_members( {
                                containerID : "membersArea"+idPeriod,
                                period_id: idPeriod
                            });
                        members_periods.fInit();
                    },
                    close: function() {
                        $("#membersArea"+idPeriod).remove();
                        $("#dialogMembersOfPeriod"+idPeriod).html("");
                        $("#refresh_"+oThis.options.gridID+" div " ).click();
                        if (fAfter) fAfter();
                    }
                });
            } else {
                $("#dialogMembersOfPeriod"+idPeriod).dialog("open");
            }
            
        },
        refreshGraphInfo = function(graph,bShow) {
            $(".display_graph_period,.display_only_graph_period").css("color","black");
            graph.css("color","red");
            if ( bShow ) {
                $("input[name=simulation]").parent().show();
                $(".set_published").show();
                $("#periods_campaign  .hiddenOptions, #periods_campaign  .hideOptions").hide();
                
            } else {
                $("input[name=simulation]").parent().hide();
                $(".set_published").hide();
            }
            $("input[name=simulation]").attr("checked","");
            $("#repartition_graph").html("");
            
        },

        setupGraph = function(period_id,graph_id,fAfter) {
            var oThis=this, budget,
                graphPanelId = graph_id, 
                data;
            data = { 
                action: "get_campaign_data",
                period_id: period_id 
            };
            $('#'+graphPanelId).empty();
            budget = $("#"+oThis.options.gridID).getCell(oThis.lastsel,"budget");
            $.ajax({
                type: "POST",
                url:  'campaign-json.php',
                data: data,
                dataType: 'json',
                success: function(data) {
                    // return true if the data is empty and there is at least one giver with a Love sent
                    var additionalWidth=0,
                        isEmpty = function(localData){                      
                        for (var oo=0; oo < localData.percentage.length; oo++) {
                            if (localData.percentage[oo] != "0") return false;
                        }
                        for (var oo=0; oo < localData.givers.length; oo++) {
                            if (localData.givers[oo] != "0") return true;
                        }
                        return false;
                    };
                    // If we retrieved our data, show it on the graph.
                    if ( data && data.points && data.points.length == 0 ) {
                        $('#timeline-graph').html("<div class='errorInReview'>History not yet available.<br\></div>")
                        if (fAfter) fAfter();
                    } else {                       
                        if (data.givers.length > 30) {
                            additionalWidth = 20 * data.givers.length;
                        }
                        LoveChart.initialize(graphPanelId, $('#'+graphPanelId).width()+additionalWidth, 320, 10 , budget);
                        LoveChart.load(data, function() {
                            if (isEmpty(data)) {
                                if (fAfter) fAfter();
                                if ($('.simulationLovesReceived:visible').length > 0) {
                                    if ($('.simulationLovesReceived:checked').length == 0) {
                                        $("#info_table_update").html("<span style='color:red;'>Graph empty with last setting, default view 'prorata' is used.</span>");
                                        $('.simulationLovesReceived').attr("checked", "checked");
                                        $('.simulationLovesReceived').click();
                                    }
                                }
                            } else {
					
                                if (fAfter) fAfter();
                            }
                        });
                    }
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
        },
        populateRewarderList = function(period_id, bNormalized, minAmount, fAfter) {
            var bReturnStat = this.bReturnStat;
            $.ajax({
                url: 'campaign-json.php?action=populate_campaign_prorata&period_id='+period_id+
                        '&normalized='+bNormalized+
                        '&return_stat='+bReturnStat+
                        '&min_amount='+minAmount,
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
                            if (json.debug_sql) {

                                sNormalized = bNormalized ?  "Normalized" : "" ;
                                sNormalized = "<div>Prorata "+sNormalized+" (min amount="+minAmount+"% of the budget)</div>";
                                var details_periods;
                                $("#detailsWindow").html(sNormalized+"<div id='detailsWindowArea' style='width:90%;height:90%;'>"+
                                    "<table class='listDetailsRecognition'></table>"+
                                    "<div class='pagerDetailsRecognition'></div>"+
									"<div style='position:relative;left:600px;width:100px;'>Total:<span class='totalPerc'></span> %</div></div>");
                                details_periods = LM_detailsRecognition( {
                                        containerID : "detailsWindowArea"
                                });
                                details_periods.fInit();

                                sRes='';
								iTotalPerc = 0;
                                for (var oo=0; oo < json.stat.length; oo++) {
                                    if (json.stat[oo]['receiver'] ) {
                                        //json.stat[oo]['numberOfLoves'] = json.stat[oo]['numberOfLoves']+" ";
                                        sRes += ""+(1+oo)+","+json.stat[oo]['receiver']
                                                +","+json.stat[oo]['giver']
                                                +","+json.stat[oo]['numberOfLoves']
                                                +","+json.stat[oo]['lovesPriceForGiver']
                                                +"\r\n";
										json.stat[oo]['lovesPriceForGiver'] = json.stat[oo]['lovesPriceForGiver'] * 100;
										json.stat[oo]['totalForLoves'] = json.stat[oo]['numberOfLoves'] * json.stat[oo]['lovesPriceForGiver'];
										iTotalPerc += json.stat[oo]['totalForLoves'];
                                        $("#detailsWindowArea_listDetailsRecognition").addRowData(oo, json.stat[oo]);
                                    }
                                }
								iTotalPerc = parseInt(100 * iTotalPerc ) / 100;
								$("#detailsWindow .totalPerc").html(iTotalPerc);
                                $("#debugWindow").prepend(sNormalized+
                                    "<div>receiver , giver , number of Loves , Price of 1 love (giver price)</div><textarea style='width:95%;height:45%'>"+
                                        sRes+"</textarea>" + 
                                    "<textarea style='width:95%;height:45%'>"+json.debug_sql+"</textarea>");
                                        
                                        
                            }
                            if ( fAfter ) fAfter();
                        }
                    } else {
                        if (fAfter) fAfter();
                    }
                 }
            });
        },


        deletePeriod = function(idPeriod,fAfter) {
            var oThis=this;
            $.getJSON('periods-json.php?action=delete_period&id='+idPeriod,
                {},
                function(json){
                     if ( !json || json === null ) {
                        alert("error delete return undefined json");
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $("#info_table_update").html("Period deleted !");
                    }
                    $("#refresh_"+oThis.options.gridID+" div " ).click();
                    if (fAfter) fAfter();
                }
            );
        },

        copyPeriod = function(idPeriod,fAfter) {
            var oThis=this;
            $.getJSON('periods-json.php?action=copy_period&id='+idPeriod,
                {},
                function(json){
                     if ( !json || json === null ) {
                        alert("error copy return undefined json");
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $("#info_table_update").html("Period copied !");
                    }
                    rowID=json.id;
                    oThis.afterServerAddRow = function(){
                        if (fAfter) fAfter(rowID);
                    };
//                     $("#pg_pagerCampaignPeriods .ui-pg-input").val("1");
                    $("#"+oThis.options.gridID).jqGrid("setGridParam",{page:1,sortname:'id',sortorder: "desc",postData:{"displayAllMy":$("#"+oThis.options.gridID).data("displayAllMy")}}).trigger("reloadGrid");
//                    $("#refresh_"+oThis.options.gridID+" div " ).click();
                }
            );
        },
        addNewPeriod = function(newClosureDate,gridType,fAfter) {
            var oThis=this;
            $.getJSON('periods-json.php?action=add_period&end_date='+newClosureDate+'&grid_type='+gridType,
                {},
                function(json){
                    if ( !json || json === null ) {
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $("#info_table_update").html("Period added !");
                    }
                    $("#refresh_"+oThis.options.gridID+" div " ).click();
                    if (fAfter) fAfter();
                }
            );
        },

        addNewCampaign = function(options, fAfter) {
            var oThis=this;
            $.getJSON('periods-json.php?action=add_campaign&title='+options.title+'&budget='+options.budget+'&startDate='+options.startDate+'&endDate='+options.endDate,
                {},
                function(json){
                    var rowID;
                    if ( !json || json === null ) {
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } 
                    rowID=json.id;
                    oThis.afterServerAddRow = function(){
                        if (fAfter) fAfter(rowID);
                    };                    
                    $("#"+oThis.options.gridID).jqGrid("setGridParam",{page:1,sortname:'id',sortorder: "desc",postData:{"displayAllMy":$("#"+oThis.options.gridID).data("displayAllMy")}}).trigger("reloadGrid");
                }
            );
        },
        
        addNewRow = function(gridType,fAfter) {
            var oThis=this;
            $.getJSON('periods-json.php?action=add_period&grid_type='+gridType,
                {},
                function(json){
                    var rowID;
                    if ( !json || json === null ) {
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $("#info_table_update").html("Add the info in the fields, and hit 'Enter' key to save or 'ESC' to cancel !");
                    }
                    rowID=json.id;
                    oThis.afterServerAddRow = function(){
                        if (fAfter) fAfter(rowID);
                    };
                    $("#"+oThis.options.gridID).jqGrid("setGridParam",{page:1,sortname:'id',sortorder: "desc",postData:{"displayAllMy":$("#"+oThis.options.gridID).data("displayAllMy")}}).trigger("reloadGrid");
                   // $("#refresh_"+oThis.options.gridID+" div " ).click();
                }
            );
        },
        
        sendRedeemRequest = function(fAfter) {
            /*** Keep this comment, the functionality could come back later (#16400)
            $("#dialogRedeemConfirm").dialog("open");
            ***/
        },
        /*
        KEEP the following function, the code is no more used but it will be used again with a new merchant.
        */
        checkoutCampaignUsingPaypal = function() {
            var oThis=this;
            $.getJSON('campaign-json.php?action=checkoutCampaign'+"&t="+(new Date()).getTime(), 
                {},
                function(json){
                    if ( !json || json === null ) {
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        if (json.totalBudgets !== null && json.totalBudgets != 0) {
                            var openModal = function(){
                                var d = (new Date()).valueOf();
                                 var paramURL='?d='+d+'&domain='+json.instanceHost+"&databaseName="+json.instanceDatabaseName+"&t="+(new Date()).getTime();
                                        
                                $("#buylovemachine iframe").attr("src",json.url_buylovemachine +paramURL);
                            };
                            if ($("#buylovemachine").length != 0) {
                                $("#buylovemachine").remove();
                                $("#buylovemachine").remove();              
                            }
                            $("<div id='buylovemachine' ><iframe style='border:0 none;width:98%;height:98%;' src=''></iframe></div>").appendTo("body");
                            $("#buylovemachine").dialog({
                                modal:true,
                                autoOpen:false,
                                width:880,
                                height:650,
                                position: ['top'],
                                open: function() {
                                    openModal();
                                },
                                close: function() {
                                    $("#refresh_"+oThis.options.gridID+" div " ).click();
                                }
                            });
                            
                            $("#buylovemachine").dialog("open");
                        } else {
                            $("#info_table_update").html("Cart is empty !");
                        }
                    }
                 }
            );
        },
        /*
        Temporary checkout process : send by email the campaigns and buyer data
        */
        checkoutCampaign = function() {
            var oThis=this;
            $.getJSON('campaign-json.php?action=checkoutCampaign'+"&t="+(new Date()).getTime(), 
                {},
                function(json){
                    if ( !json || json === null ) {
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        if (json.totalBudgets !== null && json.totalBudgets != 0) {
                            $("#buylovemachine").data("checkoutAdminEmailAddress",json.managerEmails);
                            $("#buylovemachine").data("checkoutTenant",json.instanceHost);
                            $("#buylovemachine").data("checkoutDatabase",json.instanceDatabaseName);
                            $("#buylovemachine").data("checkoutPeriods",json.infoCampaigns);                           
                            $("#buylovemachine").data("totalBudgets",json.totalBudgets);                           
                            $("#buylovemachine").data("fee",json.fee);                           
                            $("#buylovemachine").data("totalBudgetsFee",json.totalBudgetsFee);                           
                            $("#buylovemachine").dialog("open");
                        } else {
                            $("#info_table_update").html("Cart is empty !");
                        }
                    }
                 }
            );
        },
        
        setMaxEndDate = function(gridType) {
            $.getJSON('periods-json.php?action=getLastPeriodClosureDate&grid_type='+gridType, 
                {},
                function(json){
                    if ( !json || json === null ) {
                        return;
                    }
                    if ( json.max_end_date ) {
                        $( "#nextPeriodClosureDate" ).datepicker( "option", "minDate", new Date(json.max_end_date) );
                    }
                }
            );
        },
       
        initReviewPeriod = function() {
            var oThis=this,
                containerID = this.options.containerID,
                gridType = this.options.gridType;
            oThis.lastsel=-1;
            this.options.gridID = containerID+"_listPeriods";
            $("#" + containerID + " .listPeriods").attr('id',containerID+"_listPeriods");
            $("#"+this.options.gridID).jqGrid({ 
                url:'review-json.php?action=get_periods_list&grid_type='+gridType, 
                datatype: "json", 
                colNames:['Title','Open Date', 'Closure Date', 'Status', '# Reviews'], 
                colModel: [{name:'title',index:'title', width:150, editable:true}, 
                            {name:'start_date',index:'start_date asc', width:150, editable:false}, 
                            {name:'end_date',index:'end_date', width:150, editable:false}, 
                            {name:'status',index:'status', width:80, align:"right", editable:false}, 
                            {name:'totalUserReviews',index:'totalUserReviews', width:80,  editable:false}], 
                rowNum:10, 
                rowList:[10,30,50,100], 
                pager: '#pagerPeriods', 
                sortname: 'start_date', 
                viewrecords: true, 
                sortorder: "desc", 
                caption:"Review Periods",
                onSelectRow: function(id){               
                    if (id && id!==oThis.lastsel){ 
                        $("#"+oThis.options.gridID).jqGrid('restoreRow',oThis.lastsel); 
                        $("#"+oThis.options.gridID).jqGrid('editRow',id,true); 
                        oThis.lastsel=id; 
                    } 
                },
                editurl: 'periods-json.php?action=set_period', 
                loadComplete : function (request) {             
                    $(".status_action_close").parent().attr("title","click me to open the period.").click(function(e){
                        e.preventDefault();
                        oThis.changePeriodStatus("0",$("a",this).attr("idperiod"));
                        return false;
                    });
                    $(".status_action_open").parent().attr("title","click me to close the period.").click(function(e){
                        e.preventDefault();
                        oThis.changePeriodStatus("1",$("a",this).attr("idperiod"));
                        return false;
                    });
                    $(".delete_period").attr("title","click me to delete the period.").click(function(e){
                        e.preventDefault();
                        oThis.deletePeriod($(this).attr("idperiod"),function(){
                            setMaxEndDate(gridType);
                        });
                        return false;
                    });
                    
                }
            });
            $("#" + containerID + " .listPeriods").jqGrid('navGrid','#pagerPeriods',{edit:false,add:false,del:false,search:false,refresh:true});  
            $('#nextPeriodClosureDate').datepicker({
                changeMonth: true,
                changeYear: true,
                minDate: 0,
                showOn: 'both', 
                dateFormat: 'mm/dd/yy',
                buttonImage: 'images/Calendar.gif',
                buttonImageOnly: true 
            });
            $("#addNewPeriod").click(function(){
                if ($("#nextPeriodClosureDate").val() != "" ){
                    var dateText = $('#nextPeriodClosureDate').datepicker("getDate");
                    if (dateText != "") {
                        newDateObj = new Date(dateText);
                        newdate = newDateObj.getFullYear()+"-"+(1+newDateObj.getMonth())+"-"+newDateObj.getDate();
                        oThis.addNewPeriod(newdate,gridType,function(){
                            setMaxEndDate(gridType);
                        });
                        $('#nextPeriodClosureDate').datepicker("setDate","")
                    }
                } else {
                    $("#info_table_update").html("Date empty !");
                }
            });
            setMaxEndDate(gridType);
        },
        
        initCampaignPeriod = function() {
            var oThis=this,
                containerID = this.options.containerID,
                gridType = this.options.gridType;
                pickdates = function(id,gridID){
                    $("#"+id+"_start_date","#"+gridID).datepicker({
                        showOn: 'button',
                        buttonImage: 'images/Calendar.gif',
                        dateFormat:"yy-mm-dd 00:00:00",
                        buttonImageOnly: true,
                        beforeShow: function(input,inst) {
                            var maxDate="";
                            setInterval(function() {
                                $("#ui-datepicker-div").css("z-index","2000");
                            },500);
                            if ($.trim($("#"+id+"_end_date","#"+gridID).val()) != "" && $("#"+id+"_end_date","#"+gridID).datepicker("getDate") != "") {
                                maxDate = $("#"+id+"_end_date","#"+gridID).datepicker("getDate")
                            }
                            return {
                                    maxDate: maxDate
                                };
                        },
                        onClose:function(){
                            $(this).focus();
                        },
                        onSelect:function(){
                            $(this).focus();
                        }
                    }).css("width","65px");
                    $("#"+id+"_end_date","#"+gridID).datepicker({
                        showOn: 'button',
                        buttonImage: 'images/Calendar.gif',
                        dateFormat:"yy-mm-dd 23:59:59",
                        buttonImageOnly: true,
                        beforeShow: function(input,inst) {
                            setInterval(function() {
                                $("#ui-datepicker-div").css("z-index","2000");
                            },500);
                            return {
                                    minDate: jQuery("#"+id+"_start_date","#"+gridID).datepicker("getDate")
                                };
                        },
                        onClose:function(){
                            $(this).focus();
                        },
                        onSelect:function(){
                            $(this).focus();
                        }
                    }).css("width","65px");
                },
                lockUnlockGridLinks = function(bLock,bGridAll){
                    var buttonsGrid = ".buy_budget_validated_running,.buy_budget_validated_no,.change_budget_validated_running,.change_budget_validated_no",
                        buttonsOutsideGrid = "#addNewRow,#checkoutCampaign,.checkAllCampaigns,.set_published,input.simulation ",
                        areaLock = "#raftest,#pg_pagerCampaignPeriods";
                    if (bGridAll) {
                        areaLock += ",#"+oThis.options.gridID;
                    }
                    if (bLock === true) {
                        $(areaLock).block({ message: null, 
                            overlayCSS: { opacity: 0.10 } });
                        $(buttonsGrid, "#"+oThis.options.gridID).attr("disabled",true);
                        $(buttonsOutsideGrid).attr("disabled",true);
                        if ($(".grid_actions_disabled","#"+oThis.options.gridID).length == 0) {    // not already disabled            
                            $(".grid_actions","#"+oThis.options.gridID).after(function(){
                                return "<span class='grid_actions_disabled'>" +
                                    $(this).hide().text()+
                                    "</span>";
                            });
                        }
                    } else {
                        $(areaLock).unblock();
                        $(buttonsGrid, "#"+oThis.options.gridID).removeAttr("disabled");
                        $(buttonsOutsideGrid).removeAttr("disabled");
                        $(".grid_actions_disabled","#"+oThis.options.gridID).remove();
                        $(".grid_actions","#"+oThis.options.gridID).show();
                        $("#info_table_update").html("");
                    }
                },
                isRowEditable = function (id) {
                    // implement your criteria here 
                    if ($("#"+oThis.options.gridID).getCell(id,"statusVal") == "2" ) {
                        return false;
                    }
                    return true;
                };
    
            oThis.lastsel=-1;
            $("input[name=simulation]").parent().hide();
            this.options.gridID = containerID+"_listPeriods";
            $("#" + containerID + " .listPeriods").attr('id',containerID+"_listPeriods");
//                           {name:'status',index:'status', width:60,  editable:true,edittype:'select', editoptions:{value:{1:'Closed',0:'Open'}}}, 
            $("#"+this.options.gridID).data("displayAllMy","my");
            $("#"+this.options.gridID).jqGrid({ 
                url:'campaign-json.php?action=get_periods_list&grid_type='+gridType, 
                datatype: "json", 
                postData: {
                    displayAllMy:$("#"+this.options.gridID).data("displayAllMy")
                },
                colNames:['Title', 'Owner', 'Open Date', 'Closure Date', 'Status', 'Projected  Budget', 
                            /*  #16402 temporary removal'Funded', '<input type="checkbox" class="checkAllCampaigns"/>Order', */
                            '# Team members', 'Actions', 'statusVal', 'idPeriod'], 
                colModel:[{name: 'title',index: 'title', width: 180, editable: true, editrules: {edithidden: true, required: true},
                                editoptions: {size: "30", maxlength: "150"}}, 
                            {name:'owner', index:'owner', width:110},
                            {name:'start_date',index:'start_date', width:90, editable:true,editrules:{edithidden:true, required:true},
                                formatter:'date', formatoptions:{srcformat: 'Y-m-d H:i:s',newformat: 'Y/m/d'}
                                }, 
                            {name:'end_date',index:'end_date', width:90, editable:true,editrules:{edithidden:true, required:true},
                                formatter:'date', formatoptions:{srcformat: 'Y-m-d H:i:s',newformat: 'Y/m/d'}}, 
                            {name:'status',index:'status', width:65,  editable:false}, 
                            {name:'budget',index:'budget', width:115, align:"right", editable:true,
                                editrules:{edithidden:true, number:true},
                                formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0, prefix: "$ "} }, 
                        /*  #16402 temporary removal  
                            {name:'budget_validated', index:'budget_validated', width:50, align:"center", editable:false}, 
                            {name:'order', index:'order', width:60,  editable:false, sortable:false},  
                        */
                            {name:'numberTeamMembers',index:'numberTeamMembers', width:110,  editable:false}, 
                            {name:'actions',index:'actions', width:100,  editable:false}, 
                            {name:'statusVal',index:'statusVal', width:100,  hidden:true}, 
                            {name:'idPeriod',index:'idPeriod', width:100,  hidden:true}], 
                loadui:"block",
                hidegrid:false,
                rowNum:50, 
                rowList:[10,30,50,100], 
                pager: '#pagerCampaignPeriods', 
                sortname: 'id', 
                viewrecords: true, 
                sortorder: "desc", 
                caption:"Recognition Periods",
                ondblClickRow: function(id, ri, ci) {
                    if ($("#"+oThis.options.gridID).data("gridAddMode") === true) {
                        return;
                    }
                    $("#info_table_update").html("");
                    if (isRowEditable(id)) {
                        // edit the row and save it on press "enter" key
                        lockUnlockGridLinks(true);
                        $("#info_table_update").html("Change the info in the fields, and hit 'Enter' key to save or 'ESC' to cancel !");
                        $("#"+oThis.options.gridID).data("gridEditMode",true);
                        $("#"+oThis.options.gridID).jqGrid('editRow',id,true,function(id) {
                            $("#"+oThis.options.gridID).data("endDateBeforeEdit",$("#"+id+"_end_date","#"+oThis.options.gridID).val());
                            pickdates(id,oThis.options.gridID);
                            $("#"+id+"_budget","#"+oThis.options.gridID).blur(function(){
                                $("#"+id+"_title","#"+oThis.options.gridID).focus();
                            });
                        },false, false, false, function(id){
                            $("#"+oThis.options.gridID).data("gridEditMode",false);
                            // if the end date didn't change, just unlock the grid, 
                            // else we need to reload the grid in order to get the new status value
                            if ($("#"+oThis.options.gridID).data("endDateBeforeEdit") == $("#"+oThis.options.gridID).getCell(id,"end_date")) {
                                lockUnlockGridLinks(false);
                            } else {
                                $("#"+oThis.options.gridID).trigger("reloadGrid");
                                lockUnlockGridLinks(false);
                            }
                        },false, function(){
                            $("#"+oThis.options.gridID).data("gridEditMode",false);
                            lockUnlockGridLinks(false);
                        }); 
                    } else {
                        $("#info_table_update").html("Campaign already published, you cannot change it !");
                    }
                },
                onSelectRow: function(id){  
                    if ($("#"+oThis.options.gridID).data("gridAddMode") === true) {
                        return;
                    }
                    $("#info_table_update").html("");
                    if (id && id!==oThis.lastsel){ 
                        $("#info_table_update").append('<img style="position:relative;top:0px;left:200px" class="loader" src="images/loader.gif">');                         
                        if ( $("#"+oThis.options.gridID).data("gridAddMode") === true ) {
                            return;
                        }
                        if ( $("#"+oThis.options.gridID).data("gridEditMode") === true ) {
                            resp = confirm("you are editing another row, do you want to cancel your changes ?");
                            if ( resp !== true ) {
                                return;
                            }
                            $("#"+oThis.options.gridID).data("gridEditMode",false);
                        }
                        lockUnlockGridLinks(true,true);
                        $("#"+oThis.options.gridID).jqGrid('restoreRow',oThis.lastsel); 
                        oThis.lastsel=id; 
                        var statusVal = $("#"+oThis.options.gridID).getCell(id,"statusVal");
                        if (statusVal == "2" || statusVal == "1") {
                            var idPeriod = $("#"+oThis.options.gridID).getCell(id,"idPeriod")
                            $('.set_published').unbind('click').attr("disabled",true);
                            $('.periodExport').unbind('click').attr("disabled",true);
                            if ( statusVal == "2" ) {
                                $(".periodExport").show();
                            } else {
                                $(".periodExport").hide();
                            }
                            oThis.refreshGraphInfo($(this),(statusVal == "1") ? true: false);
                            $(".periodExport").attr("title","Click me to export the campaign data.").click(function(e){
                                e.preventDefault();
                                oThis.exportCampaign(idPeriod,function(){
                                });
                                return false;
                            }).removeAttr("disabled");
                            $(".set_published").attr("title","Click me to publish the campaign.").click(function(e){
                                e.preventDefault();
                                oThis.publishCampaign(idPeriod,function(){
                                });
                                return false;
                            }).removeAttr("disabled");
                            oThis.setupGraph( idPeriod,"timeline-graph" ,function(){
                                lockUnlockGridLinks(false,true);
                                $("#info_table_update .loader").remove();
                            });
                        } else {
                            $(".periodExport").hide();
                            lockUnlockGridLinks(false,true);
                            oThis.refreshGraphInfo($(this),(statusVal == "1") ? true: false);
                            $("#repartition_graph").html("Fund, add team members, and start your campaign in order to get your reward graph!");
                            $("#timeline-graph").html("");
                            $("#info_table_update .loader").remove();
                       }
                    }
                },
                editurl: 'periods-json.php?action=set_period', 
                gridComplete: function () {
                    $("td",this).removeAttr("title");
                    $("td .fundedNo").parent().css("background-color","orange").attr("title","Please fund your campaign!");
                    $("td .noTeamMumber").parent().css("background-color","orange").attr("title","Please add some team members to your campaign!");
                },
                loadComplete : function (request) { 
                    if (oThis.afterServerAddRow) {
                        oThis.afterServerAddRow();
                        oThis.afterServerAddRow=undefined;                        
                    }
                    /*  #16402 temporary removal 
                    $(".change_budget_validated_running").attr("title","click me to fund this campaign, the new status is in Cart.").click(function(e){
                        oThis.block(true);
                        oThis.changePeriodValidated("C",$(this).attr("idperiod"),function(){
                            oThis.block(false);
                        });
                        return true;
                    });
                    $(".change_budget_validated_no").attr("title","click me to unfund this campaign, the new status is No.").click(function(e){
                        oThis.block(true);
                        oThis.changePeriodValidated("N",$(this).attr("idperiod"),function(){
                            oThis.block(false);
                        });
                        return true;
                    });
                    $(".buy_budget_validated_running").attr("title","click me to add this campaign in Cart and checkout.").click(function(e){
                        oThis.block(true);
                        oThis.changePeriodValidated("C",$(this).attr("idperiod"),function(){
                            oThis.block(false);
                            oThis.checkoutCampaign();
                        });
                        return true;
                    });
                    $(".buy_budget_validated_no").attr("title","click me to checkout your cart.").click(function(e){
                        oThis.checkoutCampaign();
                        return true;
                    });
                    */
                    $(".simulate_API_transaction_started")
                        .attr("title","Simulate the start of the paypal transaction, new status is Running").click(function(e){
                        e.preventDefault();
                        oThis.changePeriodValidated("R",$(this).attr("idperiod"));
                        return false;
                    });
                    $(".simulate_API_transaction_validated")
                        .attr("title","Simulate the success of the paypal transaction, the new status is Yes").click(function(e){
                        e.preventDefault();
                        oThis.changePeriodValidated("Y",$(this).attr("idperiod"));
                        return false;
                    });
                    $(".simulate_API_transaction_canceled")
                        .attr("title","Simulate the cancel of the paypal transaction., the new status is No").click(function(e){
                        e.preventDefault();
                        oThis.changePeriodValidated("N",$(this).attr("idperiod"));
                        return false;
                    });
                    $(".reset_transaction")
                        .attr("title","Reset the transaction status to N.").click(function(e){
                        e.preventDefault();
                        oThis.changePeriodValidated("F",$(this).attr("idperiod"));
                        return false;
                    });
                    
                    
                    $(".delete_period").attr("title","click me to delete the period.").click(function(e){
                        e.preventDefault();
                        oThis.deletePeriod($(this).attr("idperiod"),function(){
                        });
                        return false;
                    });
                    $(".copy_period").attr("title","Click me to copy the period.").click(function(e){
                        e.preventDefault();
                        oThis.copyPeriod($(this).attr("idperiod"),function(rowID){
                            oThis.block(false);
                            $("#"+oThis.options.gridID).data("gridAddMode",true);
                            $("#" + containerID + " .listPeriods").editRow(rowID,true, function(id) {
                                    $("#"+id+"_budget","#"+oThis.options.gridID).blur(function(){
                                        $("#"+id+"_title","#"+oThis.options.gridID).focus();
                                    });
                                    pickdates(rowID,oThis.options.gridID);
                                    lockUnlockGridLinks(true);
                                },false, false, false, function() {
                                    $("#"+oThis.options.gridID).data("gridAddMode",false);
                                    oThis.addMembersToPeriod(rowID,function(){
                                        lockUnlockGridLinks(false);
                                    });
                                },false, function(){
                       // The previous json request is not completed put this one in a timer         
                                    $("#"+oThis.options.gridID).data("gridAddMode",false);
                                    oThis.block(true);
                                    setTimeout(function(){
                                        oThis.deletePeriod(rowID,function(){
                                            oThis.block(false);
                                            lockUnlockGridLinks(false);
                                        });
                                    },50);
                                }
                            );
                        });
                        return false;
                    });
                    $(".add_members_in_campaign").attr("title","click me to add members to the period.").click(function(e){
                        e.preventDefault();
                        oThis.addMembersToPeriod($(this).attr("idperiod"));
                        return false;
                    });

                    $(".set_start").attr("title","click me to start the campaign.").click(function(e){
                        e.preventDefault();
                        oThis.changePeriodStatus("1",$(this).attr("idperiod"));
                        return false;
                    });
                }
            });
            $(".checkAllCampaigns")
                .css("vertical-align","text-top")
                .parent()
                .css("left","-4px")
                .parents("th").eq(0)
                .css("text-align","left");
            $(".checkAllCampaigns").click(function(e){
                var newStatus = "C";
                e.stopPropagation();
                oThis.block(true);              
                if ($(".checkAllCampaigns:checked").length == 0) {
                    newStatus = "N";
                }
                oThis.changePeriodValidated(newStatus,-2,function(){
                    oThis.block(false);
                });
                return true;                
            });
                    
            $("#" + containerID + " .listPeriods").jqGrid('navGrid','#pagerCampaignPeriods',{
                edit:false,add:false,del:false,search:false,refresh:true
            });  
            $('#nextPeriodClosureDate').datepicker({
                changeMonth: true,
                changeYear: true,
                minDate: 0,
                showOn: 'both', 
                dateFormat: 'mm/dd/yy',
                buttonImage: 'images/Calendar.gif',
                buttonImageOnly: true 
            });
            $("#addNewRow").click(function(){
                if ($("#addCampaignData").length == 0) {
                    $("<div id='addCampaignData' class='addCampaignData' style='display:none;' ></div>").appendTo("body");
                    $("#addCampaignData").dialog({
                        title: "Add Campaign",
                        width: 400,
                        height: 260,
                        position: ['center','top'],
                        resizable: false,
                        buttons: [
                            {
                                text: "ok",
                                click: function() { 
                                    var startDate = $('#startDate').datepicker('getDate');                               
                                    var newStartDateObj = new Date(startDate);
                                    var sDate = newStartDateObj.getFullYear()+"-"+(1+newStartDateObj.getMonth())+"-"+newStartDateObj.getDate();
                                    var endDate = $('#endDate').datepicker('getDate');  
                                    var newEndDateObj = new Date(endDate);
                                    var eDate = newEndDateObj.getFullYear()+"-"+(1+newEndDateObj.getMonth())+"-"+newEndDateObj.getDate();
                                    var options = {
                                        title: $('#title').val(),
                                        budget: $('#budget').val(),
                                        startDate: sDate,
                                        endDate: eDate 
                                    };                                    
                                    oThis.addNewCampaign(options, function(rowID) { 
                                        if(rowID) {
                                            $("#addCampaignData").dialog("close"); 
                                            oThis.addMembersToPeriod(rowID);    
                                        }
                                    });                               
                                }
                            },  
                            {
                                text: "cancel",
                                click: function() { $(this).dialog("close"); }
                            }                            
                        ],                        
                        open: function() {
                            $("#addCampaignData").html("<div id='campaignData' style='width:90%;height:90%;'>"+
                                "<table>" +
                                    "<tr>" +
                                        "<th>Title</th>" +  
                                        "<td><input class='input' type='text' name='title' id='title'/></td>"+
                                    "</tr>" +
                                    "<tr>" +   
                                        "<th>Start Date</th>" +                                      
                                        "<td><input class='input' id='startDate' type='text' name='startDate'/></td>" +
                                    "</tr>" +
                                    "<tr>" +
                                        "<th>End Date</th>" + 
                                        "<td><input class='input' id='endDate' type='text' name='endDate'/></td>" +
                                    "</tr>" +   
                                    "<tr>" +
                                        "<th>Projected  Budget </th>" +                                       
                                        "<td><input class='input-field' type='text' id='budget' name='budget'/> <a id='showBudgetHelp' href='javascript:showBudgetHelp();'>what's this?</a></td>" +
                                    "</tr>" + 
                                "</table>" +
                                "</div>");
                                $('#startDate').datepicker({
                                    changeMonth: true,
                                    changeYear: true,
                                    maxDate: 0,
                                    showOn: 'button',
                                    dateFormat: 'mm/dd/yy',
                                    buttonImage: 'images/Calendar.gif',
                                    buttonImageOnly: true
                                });
                                $('#endDate').datepicker({
                                    changeMonth: true,
                                    changeYear: true,
                                    minDate: new Date(),
                                    maxDate: null,
                                    showOn: 'button',
                                    dateFormat: 'mm/dd/yy',
                                    buttonImage: 'images/Calendar.gif',
                                    buttonImageOnly: true
                                });
                        },
                        close: function() {
                            $("#campaignData").remove();
                            $("#addCampaignData").html("");
                        }
                    });
                } else {
                    $("#addCampaignData").dialog("open");
                }
            });
            $("#displayAllCampaigns").click(function(){
                $("#"+oThis.options.gridID).data("displayAllMy","all");
                $(this).hide();
                $("#displayMyCampaigns").show();
                $("#"+oThis.options.gridID).jqGrid("setGridParam",{postData:{"displayAllMy":"all"}}).trigger("reloadGrid");
            });
            $("#displayMyCampaigns").click(function(){
                $("#"+oThis.options.gridID).data("displayAllMy","my");
                $(this).hide();
                $("#displayAllCampaigns").show();
                $("#"+oThis.options.gridID).jqGrid("setGridParam",{postData:{"displayAllMy":"my"}}).trigger("reloadGrid");
            });
            /* #16402 temporary removal
            $("#checkoutCampaign").click(function(){
                oThis.checkoutCampaign();
            });
            */
            $('.simulationCustom').click(function(e) {
                $("#dialogCustom").dialog("open");
            });
            $('.moreOptionsLink').click(function(e) {
                $('#periods_campaign .hiddenOptions,#periods_campaign .options').toggle();
            });
            $('.detailsProrataNormalized').click(function(e) {
                oThis.bReturnStat = true;
                $('.simulationLovesReceivedNormalized').click();
                $("#detailsWindow").dialog("open");
                $('.simulationLovesReceivedNormalized').attr('checked','true');
            });
            $('.detailsProrata').click(function(e) {
                oThis.bReturnStat = true;
                $('.simulationLovesReceived').click();
                $("#detailsWindow").dialog("open");
                $('.simulationLovesReceived').attr('checked','true');
            });
            $('#simulationFloorInput').blur(function(){
                if (isNaN(parseInt($('#simulationFloorInput').val())) ||  parseInt($('#simulationFloorInput').val())< 0) {
                    $('#simulationFloorInput').val(0);
                    $("#info_table_update").html("<span style='color:red;'>Minimum amount must be a positive integer value.</span>");
                }
            });
            $('.simulationLovesReceivedNormalized').click(function(e) {
                var me=$(this),
                    idPeriod = $("#"+oThis.options.gridID).getCell(oThis.lastsel,"idPeriod"),
                    budget = $("#"+oThis.options.gridID).getCell(oThis.lastsel,"budget");
                lockUnlockGridLinks(true,true);
                oThis.populateRewarderList(idPeriod,true,100 * $('#simulationFloorInput').val() / budget,function(){
                    oThis.setupGraph( idPeriod,"timeline-graph" ,function(){
                        lockUnlockGridLinks(false,true);
                    });
                });
            });
            $('.simulationLovesReceived').click(function(e) {
                var me=$(this),
                    idPeriod = $("#"+oThis.options.gridID).getCell(oThis.lastsel,"idPeriod"),
                    budget = $("#"+oThis.options.gridID).getCell(oThis.lastsel,"budget");
                lockUnlockGridLinks(true,true);
                oThis.populateRewarderList(idPeriod,false,100 * $('#simulationFloorInput').val()/budget,function(){
                    oThis.setupGraph( idPeriod,"timeline-graph" ,function(){
                        lockUnlockGridLinks(false,true);
                    });
                });
            });
            /** KEEP THOSE LINES, THE OPTIONS SHOULD COME BACK ONE DAY
            $('.simulationEqually').click(function(e) {
                lockUnlockGridLinks(true,true);
                $.pS.setEqually($('#simulationFloorInput').val(),function(){
                    oThis.setupGraph( rewarder.period_id,"timeline-graph" ,function(){
                        lockUnlockGridLinks(false,true);
                    });
                });
            });
            $('.simulationTopPerc').click(function(e) {
                lockUnlockGridLinks(true,true);
                rewarder.rewarderList = [];
                rewarder.populateRewarderList(rewarder.period_id,false,function(){
                    $.pS.setTopPerc($('#simulationTopPercInput').val(),$('#simulationFloorInput').val(),function(info){
                        $("#info_table_update").html("Max:"+info.max+", Limit:"+info.limit);
                        oThis.setupGraph( rewarder.period_id,"timeline-graph" ,function(){
                            lockUnlockGridLinks(false,true);
                        });
                    });
                });
            });
            $('.simulationTopNum').click(function(e) {
                lockUnlockGridLinks(true,true);
                rewarder.rewarderList = [];
                rewarder.populateRewarderList(rewarder.period_id,false,function(){
                    $.pS.setTopNum($('#simulationTopNumInput').val(),$('#simulationFloorInput').val(),function(){
                        oThis.setupGraph( rewarder.period_id,"timeline-graph" ,function(){
                            lockUnlockGridLinks(false,true);
                        });
                    });
                });
            });
            $('.simulationWinner').click(function(e) {
                lockUnlockGridLinks(true,true);
                rewarder.rewarderList = [];
                rewarder.populateRewarderList(rewarder.period_id,false,function(){
                    $.pS.setTopNum(1,$('#simulationFloorInput').val(),function(){
                        oThis.setupGraph( rewarder.period_id,"timeline-graph" ,function(){
                            lockUnlockGridLinks(false,true);
                        });
                    });
                });
            });
            **/
            $("#buylovemachine").dialog({
                modal:true,
                autoOpen:false,
                width:550,
                height:550,
                position: ['top'],
                open: function() {
                    $("#checkoutAdminEmailAddress").val($("#buylovemachine").data("checkoutAdminEmailAddress"));
                    $("#checkoutTenant").text($("#buylovemachine").data("checkoutTenant"));
                    $("#checkoutDatabase").text($("#buylovemachine").data("checkoutDatabase"));
                    $("#checkoutPeriods").html($("#buylovemachine").data("checkoutPeriods"));
                    $("#totalBudgets").text("$ "+$("#buylovemachine").data("totalBudgets"));
                    $("#fee").text("$ "+$("#buylovemachine").data("fee"));
                    $("#totalBudgetsFee").text("$ "+$("#buylovemachine").data("totalBudgetsFee"));
                    
                },
                close: function() {
                    $("#refresh_"+oThis.options.gridID+" div " ).click();
                }
            });
            $("#checkoutSend").click(function(){
                var checkoutPeriods = $("#checkoutPeriods").html();
                checkoutPeriods = $("<div>"+checkoutPeriods+"</div>");
                var scriptSaleByEmail = "/sales/admin/saleByEmail.php";
                $(".actionInEmail",checkoutPeriods).each(function(){
                    href = $(this).attr("href");
                    href = href.replace("displayCampaign.php?","https://"+$("#checkoutTenant").text()+scriptSaleByEmail+"?databaseName="+$("#checkoutDatabase").text()+"&");
                    $(this).attr("href",href)
                    $(this).text("Set funded");
                    $(this).attr("style","")
                });
                oThis.checkoutSend({
                    action: "checkout_send",
                    checkoutAdminEmailAddress:$("#checkoutAdminEmailAddress").val(),
                    checkoutTenant: $("#checkoutTenant").text(),
                    checkoutDatabase: $("#checkoutDatabase").text(),
                    checkoutPeriods: checkoutPeriods.html(),
                    checkoutContactPhone: $("#checkoutContactPhone").val(),
                    checkoutComment: $("#checkoutComment").val(),
                    totalBudgets: $("#totalBudgets").text(),
                    fee: $("#fee").text(),
                    totalBudgetsFee: $("#totalBudgetsFee").text()
                },function(){
                    $("#buylovemachine").dialog("close");
                });
            });
            $("body").append("<div id='debugWindow' style='scroll:auto'>Click on Prorata or Prorata Normalized radio buttons to get details here.</div>");
            $("#debugWindow").dialog({
                title:"Debug window",
                modal:false,
                autoOpen:false,
                width:800,
                height:650,
                position: ['left','top'],
                open: function() {
                },
                close: function() {
                }
            });
            $("body").append("<div id='detailsWindow' style='scroll:auto'></div>");
            $("#detailsWindow").dialog({
                title:"Details on repartition graph",
                modal:false,
                autoOpen:false,
                width:740,
                height:550,
                position: ['left','top'],
                    open: function() {
                    },
                    close: function() {
                        $("#detailsWindowArea").remove();
                        $("#detailsWindow").html("");
                    }
            });
            $("body").append("<div id='budgetHelp' style='scroll:auto'>" + 
            "<p>Enter an amount here to have your projected budget allocation automaticlly calculated based upon SendLove's received per user.</p>" +
            "<p>Otherwise, leave the amount as $0 and the graph will just show you the total number of SendLove's received per team member for the period.</p></div>");
            $("#budgetHelp").dialog({
                title: 'Projected Budget',
                modal: false,
                autoOpen: false,
                position: ['center','top'],
                width: 400
            });
            showBudgetHelp = function() {
                $("#budgetHelp").dialog("open");
            }
         },
                
        initRedeemPeriod = function() {
            var oThis=this,
                containerID = this.options.containerID,
                gridType = this.options.gridType;
            $("#dialogRedeemConfirm").dialog({
                modal:true,
                autoOpen:false,
                width:300,
                height:160
            });
            $(".redeemNow").click(function(){
                $("#dialogRedeemConfirm").dialog("close");
                oThis.block(true);
                oThis.changeRedeemStatus(3,-1,function(){
                    oThis.block(false);
                    $(".redeemSuccessfullySent").show();
                    setTimeout(function(){
                            $(".redeemSuccessfullySent").hide();
                        },10000);
                });
            });
            $(".closeRedeemDialog").click(function(){
                $("#dialogRedeemConfirm").dialog("close");
            });
    
            oThis.lastsel=-1;
            this.options.gridID = containerID+"_listPeriods";
            this.options.pagerID = containerID+"_pagerRedeem";
            $("#" + containerID + " .listPeriods").attr('id',containerID+"_listPeriods");
            $("#" + containerID + " .pagerRedeem").attr('id',containerID+"_pagerRedeem");
			
		
            $("#"+this.options.gridID).jqGrid({ 
                url:'campaign-json.php?action=get_redeem_periods_list&grid_type='+gridType, 
                datatype: "json", 
                /*** Keep the following comments, the functionality could come back later (#16400) 
                ***/
                colNames:[/*'<input type="checkbox" class="checkAllRedeem"/>',*/'Recognition Period','Open Date', 'Closure Date', 'Manager', 'Status', 'Love', 
                            /*'Value', 'Redeemed',*/'actions'], 
                colModel:[/*{name:'actions',index:'actions', width:20, editable:false, sortable: false}, */
                            {name:'title',index:'title', width:440, editable:false}, 
                            {name:'start_date',index:'start_date'	,formatter : 'date', formatoptions:{srcformat:'Y-m-d H:i:s',newformat:'Y-m-d'}, width:92, align:"right", editable:false}, 
                            {name:'end_date',index:'end_date'		,formatter : 'date', formatoptions:{srcformat:'Y-m-d H:i:s',newformat:'Y-m-d'}, width:107, align:"right", editable:false}, 
                            {name:'manager',index:'manager', width:85, align:"right", editable:false}, 
                            {name:'periodStatus',index:'periodStatus', width:65, align:"right",  editable:false}, 
                            {name:'numberLovesReceived',index:'numberLovesReceived', align:"right", width:55,  editable:false}, 
                            /*{name:'paid_amount',index:'paid_amount', width:65, align:"right",  editable:false,
                                formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}}, 
                            {name:'redeemed',index:'redeemed', width:150, align:"right",  editable:false}, */
                            {name:'actions',index:'actions', width:60,  editable:false,  hidden:true}], 
                height:300,
                loadui:"block",
                rowNum:30, 
                rowList:[10,30,50,100], 
                pager: '#'+this.options.pagerID,  
                sortname: 'start_date', 
                viewrecords: true, 
                sortorder: "desc", 
                caption:"Your Recognition Periods",
                onSelectRow: function(id){               
                    if (id && id!==oThis.lastsel){ 
                        oThis.lastsel=id; 
                    } 
                },
                loadComplete : function (request) {             
                    $(".change_redeem_paid_status_cart").attr("title","click me to put this redeem in cart.").click(function(e){
                        oThis.block(true);
                        oThis.changeRedeemStatus(2,$(this).attr("idperiod"),function(){
                            oThis.getRedeemTotal();
                            oThis.block(false);
                        });
                        return true;
                    });
                    $(".change_redeem_paid_status_not_cart").attr("title","click me to remove this redeem request from the cart.").click(function(e){
                        oThis.block(true);
                        oThis.changeRedeemStatus(5,$(this).attr("idperiod"),function(){
                            oThis.getRedeemTotal();
                            oThis.block(false);
                        });
                        return true;
                    });
                    $(".simulate_API_redeem_transaction_started")
                        .attr("title","Simulate the low level API call to start the redeem transaction, new status is Running(4)").click(function(e){
                        e.preventDefault();
                        oThis.changeRedeemStatus(4,$(this).attr("idperiod"));
                        return false;
                    });
                    $(".simulate_API_redeem_transaction_validated")
                        .attr("title","Simulate the low level API call to validate the redeeem transaction, the new status is Paid (1)").click(function(e){
                        e.preventDefault();
                        oThis.changeRedeemStatus(1,$(this).attr("idperiod"));
                        return false;
                    });
                    $(".simulate_API_redeem_transaction_canceled")
                        .attr("title","Simulate the low level API call to cancel the redeem transaction., the new status is 5").click(function(e){
                        e.preventDefault();
                        oThis.changeRedeemStatus(5,$(this).attr("idperiod"));
                        return false;
                    });
                    $(".reset_redeem_transaction")
                        .attr("title","Reset the redeem transaction status to 5.").click(function(e){
                        e.preventDefault();
                        oThis.changeRedeemStatus("F",$(this).attr("idperiod"));
                        return false;
                    });
                    oThis.getRedeemTotal();
                }
            });

	
            $("#" + containerID + " .listPeriods").jqGrid('navGrid',"#"+this.options.pagerID,{
                edit:false,add:false,del:false,search:false,refresh:true
            });  
            $(".redeemPopup").click(function(){
                oThis.sendRedeemRequest(function(){
                });
            });
            $(".checkAllRedeem").click(function(e){
                var newStatus = 2;
                e.stopPropagation();
                oThis.block(true);              
                if ($(".checkAllRedeem:checked").length == 0) {
                    newStatus = 5;
                }
                oThis.changeRedeemStatus(newStatus,-2,function(){
                    oThis.getRedeemTotal();
                    oThis.block(false);
                });
                return true;                
            });
        },
        init = function() {
            if (this.options.gridType == "review" ) {
                this.initReviewPeriod();
            } else if (this.options.gridType == "campaign" ) {
                this.initCampaignPeriod();
            } else if (this.options.gridType == "redeem" ) {
                this.initRedeemPeriod();
            }
        };

        win.LM_periods=function(options){        
            return new LM_periods.fn.mainObject(options);
        };
    LM_periods.fn=LM_periods.prototype = {
        options : {
            containerID: "periodArea",
            gridType: "review"
        },
        mainObject:function(options){
            this.options = $.extend(true, {},this.options,options);
            return this;
        },
        fInit: init,
        block: block,
        initReviewPeriod:initReviewPeriod,
        initCampaignPeriod:initCampaignPeriod,
        initRedeemPeriod:initRedeemPeriod,
        checkoutCampaign: checkoutCampaign,
        addNewRow: addNewRow,
        addNewCampaign: addNewCampaign,
        deletePeriod: deletePeriod,
        copyPeriod: copyPeriod,
        addMembersToPeriod: addMembersToPeriod,
        changePeriodStatus: changePeriodStatus,
        changePeriodValidated: changePeriodValidated,
        refreshGraphInfo: refreshGraphInfo,
        sendRedeemRequest: sendRedeemRequest,
        changeRedeemStatus: changeRedeemStatus,
        exportCampaign: exportCampaign,
        publishCampaign: publishCampaign,
        formatAsCurrency: formatAsCurrency,
        roundAndFormat: roundAndFormat,
        getRedeemTotal: getRedeemTotal,
        setupGraph: setupGraph,
        populateRewarderList: populateRewarderList,
        checkoutSend: checkoutSend
    };
    LM_periods.fn.mainObject.prototype=LM_periods.fn;
}(window));

/*
Define the object LM_members
This object is used to display members records in a grid.
For each member, the user can select or unselect to link the user to the period
*/
(function(win){
	var deleteLink = function(idPeriod,idUser,filter,fAfter) {
            var oThis=this,
                sFilter="";
            if (filter) {
                sFilter=filter;
            }
            $.getJSON('campaign-json.php',
                {
                    action:'delete_user',
                    period_id:idPeriod,
                    user_id:idUser,
                    filter:sFilter
                },
                function(json){
                    if ( !json || json === null ) {
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $("#info_table_update").html("Team member removed from the campaign list !");
                    }
                    $("#refresh_"+oThis.options.gridID+" div " ).click();
                    if (fAfter) fAfter();
                }
            );
        },
        
        addLink = function(idPeriod,idUser,filter,fAfter) {
            var oThis=this,
                sFilter="";
            if (filter) {
                sFilter=filter;
            }
            $.getJSON('campaign-json.php',
                {
                    action:'add_user',
                    period_id:idPeriod,
                    user_id:idUser,
                    filter:sFilter
                },
                function(json){
                    if ( !json || json === null ) {
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $("#info_table_update").html("Team member added to the campaign!");
                    }
                    $("#refresh_"+oThis.options.gridID+" div " ).click();
                    if (fAfter) fAfter();
                }
            );
        },
        
        searchWithFilter = function(period_id) {
            var searchFilter = $("#membersArea"+period_id+" .membersFilterValue").val();
            $("#"+this.options.gridID).jqGrid("setGridParam",{postData:{"searchFilter":searchFilter}}).trigger("reloadGrid");
        },
       
        init = function() {
            var oThis=this,
                containerID = this.options.containerID,
                period_id = this.options.period_id,
                searchFilter;
            oThis.lastsel=-1;
            this.options.gridID = containerID+"_listMembers";
            this.options.pagerID = containerID+"_pagerMembers";
            $("#" + containerID + " .listMembers").attr('id',containerID+"_listMembers");
            $("#" + containerID + " .pagerMembers").attr('id',containerID+"_pagerMembers");
            
            $("#"+this.options.gridID).jqGrid({ 
                url:'campaign-json.php?action=get_users_list&period_id='+period_id, 
                datatype: "json", 
                postData: {
                    searchFilter:$("#membersArea"+period_id+" .membersFilterValue").val()
                },
                colNames:['Username','Nickname', 'Member'], 
                colModel: [{name:'username',index:'username', width:250, editable:false}, 
                            {name:'nickname',index:'nickname', width:250, editable:false}, 
                            {name:'points',index:'points', width:150, editable:false}], 
                rowNum:30, 
                height: 320,
                rowList:[10,30,50,100], 
                pager: '#'+this.options.pagerID, 
                sortname: 'points', 
                viewrecords: true, 
                sortorder: "desc", 
                caption:"Members of the campaign",
                onSelectRow: function(id){               
                    if (id && id!==oThis.lastsel){ 
                        $("#"+oThis.options.gridID).jqGrid('restoreRow',oThis.lastsel); 
                        $("#"+oThis.options.gridID).jqGrid('editRow',id,true); 
                        oThis.lastsel=id; 
                    } 
                },
                editurl: 'periodsssss-json.php?action=set_period', 
                loadComplete : function (request) {             
                    $(".add_user_in_campaign").attr("title","click me to add the user to the period.").click(function(e){
                        e.preventDefault();
                        oThis.addLink($(this).attr("idperiod"),$(this).attr("iduser"));
                        return false;
                    });
                    $(".remove_user_from_campaign").attr("title","click me to remove the member from the period.").click(function(e){
                        e.preventDefault();
                        oThis.deleteLink($(this).attr("idperiod"),$(this).attr("iduser"));
                        return false;
                    });                    
                }
            });
            $(".membersFilterButton").click(function(){
                oThis.searchWithFilter(period_id);                
            });
            $(".membersAddAllButton").click(function(){
                oThis.addLink(period_id,-2,$("#membersArea"+period_id+" .membersFilterValue").val());
            });
            $(".membersRemoveAllButton").click(function(){
                oThis.deleteLink(period_id,-2,$("#membersArea"+period_id+" .membersFilterValue").val());
            });
            $(".membersFilterForm").submit(function(){
                oThis.searchWithFilter(period_id);  
                return false;
            });

            $("#"+this.options.gridID).jqGrid('navGrid',"#"+this.options.pagerID,{
                edit:false,add:false,del:false,search:false,refresh:true
            });  
        };

        win.LM_members=function(options){        
            return new LM_members.fn.mainObject(options);
        };
    LM_members.fn=LM_members.prototype = {
        options : {
            containerID: "members",
            period_id: -1
        },
        mainObject:function(options){
            this.options = $.extend(true, {},this.options,options);
            return this;
        },
        fInit: init,
        addLink: addLink,
        deleteLink: deleteLink,
        searchWithFilter: searchWithFilter
    };
    LM_members.fn.mainObject.prototype=LM_members.fn;
}(window));

/*
Define the object LM_detailsRecognition
This object is used to display the recognition details in a grid.
*/
(function(win){
	var init = function() {
            var oThis=this,
                containerID = this.options.containerID,
                period_id = this.options.period_id,
                searchFilter;
            oThis.lastsel=-1;
            this.options.gridID = containerID+"_listDetailsRecognition";
            this.options.pagerID = containerID+"_pagerDetailsRecognition";
            $("#" + containerID + " .listDetailsRecognition").attr('id',containerID+"_listDetailsRecognition");
            $("#" + containerID + " .pagerDetailsRecognition").attr('id',containerID+"_pagerDetailsRecognition");
 
            $("#"+this.options.gridID).jqGrid({ 
                datatype: "clientSide", 
                colNames:['receiver','giver', 'number of loves', 'price of 1 love (%)', 'total for loves (%)'], 
                colModel: [{name:'receiver',index:'receiver', width:200, editable:false}, 
                            {name:'giver',index:'giver', width:200, editable:false}, 
                            {name:'numberOfLoves',index:'numberOfLoves', width:100, editable:false,sorttype:'int'}, 
                            {name:'lovesPriceForGiver',index:'lovesPriceForGiver', width:110, editable:false,sorttype:'float' ,
                                formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: "", decimalPlaces: 2, suffix: "%",defaultValue: ''}},
                            {name:'totalForLoves',index:'totalForLoves', width:110, editable:false,sorttype:'float' ,
                                formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: "", decimalPlaces: 2, suffix: "%",defaultValue: ''}}
							
							], 
                rowNum:30, 
                height: 320,
                rowList:[10,30,50,100], 
                pager: '#'+this.options.pagerID, 
                sortname: 'points', 
                viewrecords: true, 
                sortorder: "desc", 
                caption:"Details of the recognition period",
                onSelectRow: function(id){               
                },
                loadComplete : function (request) {             
                }
            });

            $("#"+this.options.gridID).jqGrid('navGrid',"#"+this.options.pagerID,{
                edit:false,add:false,del:false,search:false,refresh:true
            });  
        };

        win.LM_detailsRecognition=function(options){        
            return new LM_detailsRecognition.fn.mainObject(options);
        };
    LM_detailsRecognition.fn=LM_detailsRecognition.prototype = {
        options : {
            containerID: "detailsRecognition",
            period_id: -1
        },
        mainObject:function(options){
            this.options = $.extend(true, {},this.options,options);
            return this;
        },
        fInit: init
    };
    LM_detailsRecognition.fn.mainObject.prototype=LM_detailsRecognition.fn;
}(window));

/*
  Only for debug, we can have some API  links to simulate paypal process   
  This part should be removed from the final version  
*/
$(function(){
    $('html').keydown(function(event) {
        if (event.altKey) {
            if (event.keyCode == 87) { // 'W'
                $(".api_for_debug").toggleClass("api_for_debug_show");
                if ( $(".api_for_debug_show").length > 0 ) {
                    $("#periods_redeem_listPeriods").showCol("actions");
                } else {
                    $("#periods_redeem_listPeriods").hideCol("actions");
                }
                $("#debugWindow").dialog("open");
                window.review_periods_campaign.bReturnStat = true;
                event.cancelBubble = true;
                event.returnValue = false;
                return false;

            }
        }
    });
});
