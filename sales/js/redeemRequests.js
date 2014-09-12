/*
Define the object LM_periods
This object is used to display redeemed periods records in a grid.
This object is using the grid plugin like in recognition period or redeem.
Two main actions for this object:
    - changeRedeemStatus, used to put the redeem request in card 
    - amazonSendRedeemInfo, used to open the modal dialog box to communicate with the Amazon gift card
    - batchChangeRedeemStatus, used after the Amazon gift card process to set the redeem status to paid
*/
(function(win){
	var lastsel,
        block = function(bBlock){
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
        changeRedeemStatus = function(newStatus,idPeriod,idOwner,idInstance,fAfter) {
            var oThis=this;
            $.getJSON('redeemRequests-json.php?action=change_redeem_status&period_id='+idPeriod+
                    '&owner_id='+idOwner+'&instance_id='+idInstance+'&new_paid_status='+newStatus,
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
        },
        lockUnlockScreen = function(bLock) {
            var oThis=this,
                buttonsGrid = ".redeem_out_cart_of_sales",
                buttonsOutsideGrid = ".giftPopup,.amazonGiftPopup ",
                areaLock = "#"+oThis.options.containerID + " div.topArea";
            oThis.block(bLock);
            if (bLock === true) {
                $(areaLock).block({ message: null, 
                    overlayCSS: { opacity: 0.10 } });
                $(buttonsGrid, "#"+oThis.options.gridID).attr("disabled","disabled");
                $(buttonsOutsideGrid).attr("disabled","disabled");
            } else {
                $(areaLock).unblock();
                $(buttonsGrid, "#"+oThis.options.gridID).attr("disabled","");
                $(buttonsOutsideGrid).attr("disabled","");
            }
        },
        
        batchChangeRedeemStatus = function(fAfter){
            var oThis=this;
            $.getJSON('redeemRequests-json.php?action=batch_change_redeem_status',
                {},
                function(json){
                    if ( !json || json === null ) {
                        alert("json null in batchChangeRedeemStatus");
                        if (fAfter) fAfter();
                        return;
                    }
                    if ( json.error ) {
                        var sError = json.error;
                        if ( json.aError ) {
                            for (var jj=0; jj < json.aError.length; jj++) {
                                sError += "\r\n" + json.aError[jj].error;
                            }
                        }
                        alert(sError);
                        if (fAfter) fAfter();
                    } else {
                        alert("Update done without error");
                       if (fAfter) fAfter();
                        
                    }
                }
            );
        },
        
        amazonSendRedeemInfo = function(fAfter){
            var oThis=this;
            $.ajax({
                url: 'redeemRequests-json.php?action=get_redeem_requests',
                data :{},
                dataType: 'json',
                error:function(XMLHttpRequest,textStatus, errorThrown) {
                    alert("server return invalid json data"+textStatus+" * "+ errorThrown);
                },
                success: function(json){
                    if ( !json || json === null ) {
                        alert("json null in amazonSendRedeemInfo");
                        if (fAfter) fAfter();
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                        if (fAfter) fAfter();
                    } else {
                        $("#tableRedeemRequests").remove();
                        table_html = "<table id='tableRedeemRequests'>";
                        var total_amount=0;
                        for ( var jj=0; jj < json.length ; jj++ ) {
                            total_amount += parseFloat(json[jj].paid_amount);
                            if (json[jj].instance.substr(0,3) == "LM_") {
                                json[jj].instance = json[jj].instance.substr(3);
                            }
                            table_html += "<tr class='rowRedeemRequests'><td>"+json[jj].paid_amount+"</td><td>"+
                                        json[jj].ownerUsername+"</td><td>"+
                                        json[jj].owner+"</td><td>"+
                                        json[jj].manager+"</td><td>"+
                                        json[jj].comment+"\r\nFrom LoveMachine</td><td>"+
                                        json[jj].instance+"</td></tr>";
                        }
                        $(table_html+"</table>").prependTo("#dialogForAmazon");
                        $("#dialogForAmazon .total_amount_verif").text(total_amount);
                        $("#dialogForAmazon").dialog({
                            modal: false,
                            width: "90%",
                            height: 300,
                            position: ['center','top'],
                            open: function(){
                                var oDialog = $(this);
                                $(".ui-dialog-titlebar-close",oDialog.parent()).remove();
                                $(".cancelAmazon",oDialog).unbind().click(function(){
                                    ret = window.confirm("Are you really sure that you didn't send the Gift Cards ? (Amazon form will be removed) ");
                                    if ( ret === true ) {
                                        $("#amazonIFrame").attr("src","https://www.amazon.com/gp/gc/order-email?ie=UTF8&ref_=corpgc_chart_bulkemail");
                                        oDialog.dialog("close");
                                        $("#refresh_"+oThis.options.gridID+" div " ).click();
                                    }
                                });
                                $(".validAmazon",oDialog).unbind().click(function(){
                                    ret = window.confirm("Are you really sure that you sent the Gift Cards ?(Amazon form will be removed) ");
                                    if ( ret === true ) {
                                        oThis.batchChangeRedeemStatus();
                                        $("#amazonIFrame").attr("src","https://www.amazon.com/gp/gc/order-email?ie=UTF8&ref_=corpgc_chart_bulkemail");
                                        oDialog.dialog("close");
                                        $("#refresh_"+oThis.options.gridID+" div " ).click();
                                    }
                                });
                            },
                            close: function() {
                            }
                        });
                        $("#amazonGiftPopup2").click();
                        if (fAfter) fAfter();
                    }
                }
            });
            
        },
 
        initRedeemPeriod = function() {
            var oThis=this,
                containerID = this.options.containerID,
                gridType = this.options.gridType;
            this.options.gridID = containerID+"_listPeriods";
            $("#" + containerID + " .listPeriods").attr('id',containerID+"_listPeriods");
            $("#amazonIFrame").attr("src","https://www.amazon.com/gp/gc/order-email?ie=UTF8&ref_=corpgc_chart_bulkemail");
            oThis.lockUnlockScreen(true);
            oThis.afterLoadComplete = function() {
                oThis.lockUnlockScreen(false);
            };

            $("#"+this.options.gridID).jqGrid({ 
                url:'redeemRequests-json.php?action=get_redeem_periods_list&grid_type='+gridType, 
                datatype: "json", 
                colNames:['<input type="checkbox" />','Instances','Name of Span','Open Date', 'Closure Date', 'Manager', 'Manager Email',
                            'Owner','Owner Email', 'Status',  'Perc.', 'Paid', 'Redeemed','Sent','actions'], 
                colModel:[{name:'actions',index:'actions', width:40, editable:false}, 
                            {name:'instance',index:'instance', width:120, editable:false}, 
                            {name:'title',index:'title', width:160, editable:false}, 
                            {name:'start_date',index:'start_date', width:70, editable:false}, 
                            {name:'end_date',index:'end_date', width:70, editable:false}, 
                            {name:'manager',index:'manager', width:60, align:"right", editable:false}, 
                            {name:'managerUsername',index:'managerUsername', width:100, align:"right", editable:false}, 
                            {name:'owner',index:'owner', width:50, align:"right", editable:false}, 
                            {name:'ownerUsername',index:'ownerUsername', width:100, align:"right", editable:false}, 
                            {name:'status',index:'status', width:60,  editable:false, sortable:false}, 
                            {name:'rewarded_percentage',index:'rewarded_percentage', width:40, align:"right",  editable:false ,
                                formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: "", decimalPlaces: 2, suffix: "%",defaultValue: ''}}, 
                            {name:'paid_amount',index:'paid_amount', width:40, align:"right",  editable:false,
                                formatter:'currency', formatoptions:{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2, prefix: "$"}}, 
                            {name:'redeemed',index:'redeemed', width:140,  editable:false, sortable:false}, 
                            {name:'sent_by_receiver',index:'sent_by_receiver', width:70,  editable:false},                      
                            {name:'actions',index:'actions', width:100,  editable:false, sortable:false}], 
                height:200,
                loadui:"block",
                rowNum:30, 
                rowList:[10,30,50,100], 
                pager: '#pagerPeriods', 
                sortname: 'start_date', 
                viewrecords: true, 
                sortorder: "asc", 
                caption:"Pending redeem requests of active instances",
                onSelectRow: function(id){               
                    if (id && id!==lastsel){ 
                        lastsel=id; 
                    } 
                },
                loadComplete : function(request) {
                    oThis.loadComplete(request);
                }
            });
            $("#" + containerID + " .listPeriods").jqGrid('navGrid','#pagerPeriods',{
                edit:false,add:false,del:false,search:false,refresh:true
            });  
               // Generate a CSV file with all the data in the report
            $(".giftPopup").click(function(){            
                window.open('redeemRequests-json.php?action=export_redeem_requests', '_blank');
            });
            $(".amazonGiftPopup").click(function(){
                if ($(this).attr("onclick") == null) {
                    oThis.lockUnlockScreen(true);
                    oThis.amazonSendRedeemInfo(function(){
                        oThis.lockUnlockScreen(false);
                    });
                }
            });
             
        },
        loadComplete = function (request) {
            var oThis = this;
            if (oThis.afterLoadComplete) {
                oThis.afterLoadComplete();
                oThis.afterLoadComplete=undefined;                        
            }
            $(".redeem_in_cart_of_sales").attr("title","click me to put this redeem in the Sales application cart.").click(function(e){
                oThis.changeRedeemStatus(6,$(this).attr("idperiod"),$(this).attr("idowner"),$(this).attr("idinstance"));
                return true;
            });
            $(".redeem_out_cart_of_sales").attr("title","click me to remove this redeem request from the Sales application cart.").click(function(e){
                oThis.changeRedeemStatus(3,$(this).attr("idperiod"),$(this).attr("idowner"),$(this).attr("idinstance"));
                return true;
            });
            $(".simulate_API_redeem_transaction_started")
                .attr("title","Simulate the start of the paypal transaction, new status is Running(4)").click(function(e){
                e.preventDefault();
                oThis.changeRedeemStatus(4,$(this).attr("idperiod"),$(this).attr("idowner"),$(this).attr("idinstance"));
                return false;
            });
            $(".simulate_API_redeem_transaction_validated")
                .attr("title","Simulate the success of paypal transaction, the new status is Paid (1)").click(function(e){
                e.preventDefault();
                oThis.changeRedeemStatus(1,$(this).attr("idperiod"),$(this).attr("idowner"),$(this).attr("idinstance"));
                return false;
            });
            $(".simulate_API_redeem_transaction_canceled")
                .attr("title","Simulate the cancel of the paypal transaction., the new status is 5").click(function(e){
                e.preventDefault();
                oThis.changeRedeemStatus(5,$(this).attr("idperiod"),$(this).attr("idowner"),$(this).attr("idinstance"));
                return false;
            });
            $(".reset_redeem_transaction")
                .attr("title","Reset the redeem transaction status to 5.").click(function(e){
                e.preventDefault();
                oThis.changeRedeemStatus("F",$(this).attr("idperiod"),$(this).attr("idowner"),$(this).attr("idinstance"));
                return false;
            });
        },
        init = function() {
                this.initRedeemPeriod();
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
        initRedeemPeriod:initRedeemPeriod,
        changeRedeemStatus: changeRedeemStatus,
        amazonSendRedeemInfo: amazonSendRedeemInfo,
        batchChangeRedeemStatus: batchChangeRedeemStatus,
        lockUnlockScreen: lockUnlockScreen,
        loadComplete: loadComplete,
        block: block
    };
    LM_periods.fn.mainObject.prototype=LM_periods.fn;
}(window));

/*
Define the object LM_instances
This object is used to display the instances records in a grid.
The current class is only used to make the display, the next version will probably implement the
changeInstanceStatus function in order to be able to set an instance active or inactive.
The lockUnlockScreen should also be implemented in order to lock the grid while the update is done.
(see previous object LM_periods, for a sample of the 2 methods)
*/
(function(win){
	var lastsel,
        filtreInstances = 1,
        block = function(bBlock){
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

        changeInstanceStatus = function(newStatus,domain,fAfter) {
            var oThis=this;
            $.getJSON('instances-json.php?action=change_instance_status&domain='+domain+'&new_instance_status='+newStatus+'&nocache=' + Math.floor(Math.random()*111111111111),
                {},
                function(json){
                    if ( !json || json === null ) {
                        alert("json null in changeInstanceStatus");
                        return;
                    }
                    if ( json.error ) {
                        alert(json.error);
                    } else {
                        $("#info_table_update").html("Instance status changed !");
                    }
                    $("#"+oThis.options.gridID).trigger("reloadGrid");
                    if (fAfter) fAfter();
                }
            );
        },
        lockUnlockScreen = function(bLock) {
        },
 
        initInstancesGrid = function() {
            var oThis=this,
                containerID = this.options.containerID,
                gridType = this.options.gridType;
            oThis.filtreInstances=1;
            this.options.gridID = containerID+"_listInstances";
            $("#" + containerID + " .listInstances").attr('id',containerID+"_listInstances");
            $("#"+this.options.gridID).jqGrid({ 
                url:'instances-json.php?action=get_instances_list&grid_type='+gridType+"&filtreInstances="+oThis.filtreInstances, 
                datatype: "json", 
                colNames:['Domain','State', 'Created', 'Owner', 'Owner Email','Actions'], 
                colModel:[{name:'domain',index:'domain', width:240, editable:false}, 
                            {name:'active',index:'active', width:50, editable:false}, 
                            {name:'created',index:'created', width:110, editable:false}, 
                            {name:'contact_first_name',index:'contact_first_name', width:150, editable:false}, 
                            {name:'contact_email',index:'contact_email', width:200,  editable:false}, 
                            {name:'actions',index:'actions', width:80,  editable:false}
                            ], 
                height:150,
                loadui:"block",
                hiddengrid:true,
                rowNum:30, 
                rowList:[10,30,50,100], 
                pager: '#pagerInstances', 
                sortname: 'active desc,domain', 
                viewrecords: true, 
                sortorder: "asc", 
                caption:"Instances : <input type='radio' class='activeInstanceLink' name='instanceSelector' checked='checked'/>Active  <input type='radio' class='allInstanceLink'  name='instanceSelector'/>All",
                onSelectRow: function(id){               
                    if (id && id!==lastsel){ 
                        lastsel=id; 
                    } 
                },
                loadComplete : function (request) {
                    if (oThis.afterLoadComplete) {
                        oThis.afterLoadComplete();
                        oThis.afterLoadComplete=undefined;                        
                    }
                    $(".activateInstance").attr("title","click me to activate this instance.").click(function(e){
                        oThis.block(true);
                        oThis.changeInstanceStatus(1,$(this).attr("id_domain"),function(){
                            window.review_periods_redeem.afterLoadComplete = function() {
                                oThis.block(false);
                            };
                            $("#"+window.review_periods_redeem.options.gridID).trigger("reloadGrid");
                        });
                        return true;
                    });
                    $(".deactivateInstance").attr("title","click me to deactivate this instance.").click(function(e){
                        oThis.block(true);
                        oThis.changeInstanceStatus(0,$(this).attr("id_domain"),function(){
                            window.review_periods_redeem.afterLoadComplete = function() {
                                oThis.block(false);
                            };
                            $("#"+window.review_periods_redeem.options.gridID).trigger("reloadGrid");
                        });
                        return true;
                    });
                }
            });
            $("#" + containerID + " .listInstances").jqGrid('navGrid','#pagerInstances',{
                edit:false,add:false,del:false,search:false,refresh:true
            });  
            $(".activeInstanceLink").attr("title","Click me to display the active instances.").click(function(e){
                oThis.block(true);
                oThis.filtreInstances=1;
                oThis.afterLoadComplete = function() {
                    oThis.block(false);
                };
                $("#"+oThis.options.gridID).setGridParam({
                    url:'instances-json.php?action=get_instances_list&grid_type='+gridType+"&filtreInstances="+oThis.filtreInstances
                    });
                $("a.ui-jqgrid-titlebar-close.HeaderButton  .ui-icon-circle-triangle-s").click();
                $("#"+oThis.options.gridID).trigger("reloadGrid");
                return true;
            });
            $(".allInstanceLink").attr("title","Click me to display all instances.").click(function(e){
                oThis.block(true);
                oThis.filtreInstances=-1;
                oThis.afterLoadComplete = function() {
                    oThis.block(false);
                };
                $("#"+oThis.options.gridID).setGridParam({
                    url:'instances-json.php?action=get_instances_list&grid_type='+gridType+"&filtreInstances="+oThis.filtreInstances
                });
                $("a.ui-jqgrid-titlebar-close.HeaderButton  .ui-icon-circle-triangle-s").click();
                $("#"+oThis.options.gridID).trigger("reloadGrid");
                return true;
            });
             
        },
        init = function() {
                this.initInstancesGrid();
        };

        win.LM_instances=function(options){        
            return new LM_instances.fn.mainObject(options);
        };
    LM_instances.fn=LM_instances.prototype = {
        options : {
            containerID: "periodArea",
            gridType: "review"
        },
        mainObject:function(options){
            this.options = $.extend(true, {},this.options,options);
            return this;
        },
        fInit: init,
        initInstancesGrid:initInstancesGrid,
        changeInstanceStatus: changeInstanceStatus,
        lockUnlockScreen: lockUnlockScreen,
        block: block
    };
    LM_instances.fn.mainObject.prototype=LM_instances.fn;
}(window));

/*
        
*/

$(function(){
    if ( window.LM_periods ) {
        if (window.review_periods_redeem) {
            delete window.review_periods_redeem;
        }
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
    if ( window.LM_instances ) {
        if (window.instances_grid) {
            delete window.instances_grid;
        }
        window.instances_grid = LM_instances( {
            containerID : "periods_redeem",
            gridType: "instancesActive"
        });
        try {
        window.instances_grid.fInit();
       } catch (exc) {
            alert(exc);
        }
    }
});

/*
  Only for debug, we can have some API  links to simulate paypal process   
  This part should be removed from the final version  
*/
$(function(){
    $('html').keydown(function(event) {
        if (event.altKey) {
            if (event.keyCode == 87) { // 'W'
                $(".api_for_debug").toggleClass("api_for_debug_show");
                event.cancelBubble = true;
                event.returnValue = false;
                return false;

            }
        }
    });
});


