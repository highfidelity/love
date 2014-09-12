var reports = {
	weightage:null,
	reviewPeriod:null,
	startDate:null,
	endDate:null,
	activeUser:null,
	errorMsg: "Please select a period",
	init : function() {		
		reports.datepicker();
		reports.initDate();
		
		reports.changePeriodInput();
		reports.generateReport();
		//$('#report-start-date').val($('#review-period :selected').attr('start-date'));
		//$('#report-end-date').val($('#review-period :selected').attr('end-date'));
	}, // end of the init
	datepicker : function() {
		$('.text-field-sm').datepicker({
			changeMonth: true,
			changeYear: true,
			maxDate: 0,
			showOn: 'button',
			dateFormat: 'mm/dd/yy',
			buttonImage: 'images/Calendar.gif',
			buttonImageOnly: true
		});
        $('#start-date').datepicker('option', {
            maxDate:'0'
        });
        $('#end-date').datepicker('option', {
            minDate:'0',
            maxDate:'0'
        });
    },//end of the datepicker
    
    // Sets default date values on the period fields,
    // and then sets the data we use to generate reports
    // and then it generates the first report with this values.
    initDate: function() {
		// Set period date pickers to default value
		var now = new Date();
		var month = now.getMonth();
		var fromMonth,toMonth = month;
		if (month < 10) {
			var fromMonth = '0' + month;
			var toMonth = '0' + (month + 1);
		}
		now_val = fromMonth + '/' + now.getDate() + '/' + now.getFullYear();
		$('#start-date').datepicker('setDate', now_val);
		
		var next = toMonth + '/' + now.getDate() + '/' + now.getFullYear();
		$('#end-date').datepicker('setDate', next);
		// When changing the start date make sure that the end date can't be
		// less than the date selected for the period start.
	    $('#start-date').change(function() {
	    	var fromDate = $('#start-date').datepicker('getDate');
	    	$('#end-date').datepicker('option', 'minDate', fromDate);
	    });
	    
	    // Store the default date  values in the right places
		$('#review-period').val(0);
		$('#report-start-date').val($('#start-date').val());
		$('#report-end-date').val($('#end-date').val());
        $('#ui-datepicker-div').hide();
    },
	
	generateReport : function () {
		$('#generate-report').click(function() {
			reports.captureInput();
			reportGraph.setStartDate = null;
			reportGraph.setEndDate = null;
			if	(reports.startDate !== null && reports.endDate !== null )  {
				var action = $(this).attr('rel');
				if(action == 'love') {
					reportsLove.getUserLoveCount();
				}
				else if(action == 'rewarder'){
					//reportsRewarder.getUserByReward();
				}
				else if(action == 'graph'){
					reportGraph.loadLoveData();
					//reportGraph.loadRewarderData();
					reportGraph.loadLoveDistributionData();
				}
			}
			else {
				$('#error-period').text(reports.errorMsg); 
			}
		});
	},
		
	captureInput : function() {
		reports.weightage = $('#weightage').val();
		reports.reviewPeriod = $('#review-period').val();
		reports.startDate = $('#report-start-date').val();
		reports.endDate = $('#report-end-date').val();
		reports.activeUser = ($('#active-users').is(':checked')) ? 1 : 0;
	},
	
	changePeriodInput : function() {
		$('.text-field-sm').change(function() {
			$('#review-period').val(0);
			$('#report-start-date').val($('#start-date').val());
			$('#report-end-date').val($('#end-date').val());
		});
		$('#review-period').change(function() {
			$('.text-field-sm').val('');
			$('#report-start-date').val($('#review-period :selected').attr('start-date'));
			$('#report-end-date').val($('#review-period :selected').attr('end-date'));
		});
	}
	
	
};//end of the reports

var reportsLove = {
	url: admin.baseUrl + '/reports/lovedata', 
	userList:null,
	totalAmount:null,
	currentPage:1,
	sortFlag:'DESC',
	sortDirection:'ASC',
	tableHeading:'username',
	pagination:null,
	init : function() {
		$('#generate-report').attr('rel', 'love');
		reports.captureInput();
		reportsLove.getUserLoveCount();
		reportsLove.sortUserLoveTable();
		reportsLove.pageNavigation();
		reportsLove.exportReport();
		
	},

	populateUserLoveCount : function() {
		var tr = '';
		var totalAmount = 0;
		if(reportsLove.userList !== null && reportsLove.userList.length > 0) {
			$.each(reportsLove.userList, function(key, user) {
				var amount = isNaN(user.received) ? 0 : parseInt(user.received);
				amount = parseFloat(amount * reports.weightage).toFixed(2);
				totalAmount += parseFloat(amount);
				tr += (key%2 == 0) ? '<tr class="roweven">' : '<tr class="rowodd">';
				tr += '<td class="col1">' + user.username + '</td>';
				tr += '<td class="col2">' + user.nickname + '</td>';
				tr += '<td class="col3">' + user.sent + '</td>';
				tr += '<td class="col4">' + user.received + '</td>';
				tr += '<td class="col5"> $' + amount + '</td>';
				tr += '</tr>';
			});
			totalAmount = parseFloat(totalAmount).toFixed(2);
			tr += (reportsLove.pagination !== null) ?  '<tr><td colspan="5"><div class="pagerDiv">'+ reportsLove.pagination +'</div></td></tr>' : '';
			tr += '<tr><td colspan="4" class="colTotal">Page Total</td><td class="col5"> $'+ totalAmount +'</td></tr>';
			tr += '<tr><td colspan="4" class="colTotal">Grand Total</td><td class="col5"> $'+ reportsLove.totalAmount +'</td></tr>';
			
		}
		else {
			tr += '<tr><td colspan="5">No records found</td></tr>';
		}
		$('#report-love-tbody tr').remove();
		$('#report-love-tbody').append(tr);
	},

	getUserLoveCount : function() {
		$.ajax({
			url: reportsLove.url,
			type: 'POST',
			data: {weightage: reports.weightage, review_period : reports.reviewPeriod, start_date : reports.startDate, end_date:reports.endDate, 
					sort : reportsLove.tableHeading, dir : reportsLove.sortDirection, page : reportsLove.currentPage,is_active:reports.activeUser},
			dataType: 'json',
			success: function(json){
				// clear the userList
				reportsLove.userList = '';
				if(json !== null) {
					$.each(json,function(key,value){
						if(key == 'userlist') {
							reportsLove.userList = value;
						}
						if(key == 'pagination') {
							reportsLove.pagination = value;
						}
						if(key == 'total_amount') {
							reportsLove.totalAmount = value;
						}
					});
					reportsLove.populateUserLoveCount();
				} else { // failed
				}
				// Hide loading overlay and reattach the overlay events
				$('#loading').bind('ajaxComplete', function() {
                    $(this).fadeOut('fast');
                });
			}
		});
	},
	sortUserLoveTable : function() {
		$('#report-love-table-heading td').hover(function() {
			$(this).css('cursor', 'pointer');
		},function() {
			$(this).css('cursor', 'default');
		});
		$('#report-love-table-heading td').click(function() {
			reportsLove.currentPage = 1;
			reportsLove.tableHeading = $(this).attr('rel');
			if(reportsLove.sortFlag ===  null) {
					reportsLove.sortDirection = 'DESC';
					reportsLove.sortFlag = 'ASC';
			}
			else {
				reportsLove.sortDirection = reportsLove.sortFlag;
				reportsLove.sortFlag = reportsLove.sortFlag == 'ASC' ? 'DESC' : 'ASC';
			}
			reportsLove.getUserLoveCount();
		});
	},

	pageNavigation : function() {
		$('#report-love-tbody .prev').live('click', function(){
			reportsLove.currentPage = parseInt($('#report-love-tbody .page').text())-1;
			reportsLove.getUserLoveCount();
		});
		$('#report-love-tbody .next').live('click', function(){
			reportsLove.currentPage =  parseInt($('#report-love-tbody .page').text())+1;
			reportsLove.getUserLoveCount();
		});
		$('#report-love-tbody .otherPage').live('click', function(){
			reportsLove.currentPage =  parseInt($(this).text());
			reportsLove.getUserLoveCount();
		});
		$('#report-love-tbody .firstPage').live('click', function(){
			reportsLove.currentPage =  1 ;
			reportsLove.getUserLoveCount();
		});
		$('#report-love-tbody .lastPage').live('click', function(){
			reportsLove.currentPage =  parseInt($(this).attr("lastPage"));
			reportsLove.getUserLoveCount();
		});
	},
	exportReport : function() {
		$('#export-report-love').click(function() {
			window.open('reports/exportlove?start_date=' + reports.startDate +'&end_date='+ reports.endDate + '&is_active=' + reports.activeUser + '&weightage=' + reports.weightage, '_blank');
		});
	}

};

/*var reportsRewarder = {                                                                             
        url: 'reports/rewarderdata',      
		rewarderList:null,
		currentPage:1,
		sortFlag:'DESC',
		sortDirection:'ASC',
		tableHeading:'username',
		pagination:null,
        init : function(tab) { 
			$('#generate-report').attr('rel', 'rewarder');
			reports.captureInput();                                                             
			reportsRewarder.getUserByReward();      
			reportsRewarder.sortUserLoveTable();
			reportsRewarder.pageNavigation();
			reportsRewarder.exportReport();
        },                                                                                          
                                                                                                    
        populateUserRewards : function(rewarderlist) {
			var tr = '';                                                                        
			if(reportsRewarder.rewarderList !== null && reportsRewarder.rewarderList.length > 0) {
				$.each(reportsRewarder.rewarderList, function(key, user) {
						tr += (key%2 == 0) ? '<tr class="roweven">' : '<tr class="rowodd">';
						tr += '<td>' + user.username + '</td>';                             
						tr += '<td>' + user.nickname + '</td>';                             
						tr += '<td>' + user.points + '</td>';                                
						tr += '</tr>';                                                      
				});
				tr += (reportsRewarder.pagination !== null) ?  '<tr><td colspan="4"><div class="pagerDiv">'+ reportsRewarder.pagination +'</div></td></tr>' : '';
			}
			else {
				tr += '<tr><td colspan="4">No Rewards</td></tr>';
			}
			$('#report-rewarder-body tr').remove();
			$('#report-rewarder-body').append(tr);
		},                                                                                          
                                                                                                    
        getUserByReward : function() {                                                               
			$.ajax({                                                                            
				url: reportsRewarder.url,                                                   
				type: 'POST',                                                               
				data: {review_period : reports.reviewPeriod, start_date : reports.startDate, end_date:reports.endDate, 
						sort : reportsRewarder.tableHeading, dir : reportsRewarder.sortDirection, page : reportsRewarder.currentPage, is_active:reports.activeUser},                                                                         
				dataType: 'json',                                                           
				success: function(json){                                                    
					if(json !== null) {                                                 
						$.each(json,function(key,value){                            
							if(key == 'rewarderlist') {                             
								reportsRewarder.rewarderList = value; 
							}
							if(key == 'pagination') {
								reportsRewarder.pagination = value;
							}
						});                                                         
						reportsRewarder.populateUserRewards();
						// Hide loading overlay and reattach the overlay events
						$('#loading').bind('ajaxComplete', function() {
							$(this).fadeOut('fast');
						});
					}                                                                   
					else {                                                              
					// failed                                                   
					}                                   
					
					
				}                                                                           
			});                                                                                 
        },  
		
        sortUserLoveTable : function() {
			$('#report-reward-table-heading td').hover(function() {
				$(this).css('cursor', 'pointer');
			},function() {
				$(this).css('cursor', 'default');
			});
			$('#report-reward-table-heading td').click(function() {
				reportsRewarder.currentPage = 1;
				reportsRewarder.tableHeading = $(this).attr('rel');
				if(reportsRewarder.sortFlag ===  null) {
					reportsRewarder.sortDirection = 'DESC';
					reportsRewarder.sortFlag = 'ASC';
				}
				else {
					reportsRewarder.sortDirection = reportsRewarder.sortFlag;
					reportsRewarder.sortFlag = reportsRewarder.sortFlag == 'ASC' ? 'DESC' : 'ASC';
				}
				reportsRewarder.getUserByReward();
			});
		},

		pageNavigation : function() {
			$('#report-rewarder-body .prev').live('click', function(){
				reportsRewarder.currentPage = parseInt($('#report-rewarder-body .page').text())-1;
				reportsRewarder.getUserByReward();
			});
			$('#report-rewarder-body .next').live('click', function(){
				reportsRewarder.currentPage =  parseInt($('#report-rewarder-body .page').text())+1;
				reportsRewarder.getUserByReward();
			});
			$('#report-rewarder-body .otherPage').live('click', function(){
				reportsRewarder.currentPage =  parseInt($(this).text());
				reportsRewarder.getUserByReward();
			});
			$('#report-rewarder-body .firstPage').live('click', function(){
				reportsRewarder.currentPage =  1 ;
				reportsRewarder.getUserByReward();
			});
			$('#report-rewarder-body .lastPage').live('click', function(){
				reportsRewarder.currentPage =  parseInt($(this).attr("lastPage"));
				reportsRewarder.getUserByReward();
			});
		},

		exportReport : function() {
			$('#export-report-rewarder').click(function() {
				window.open('reports/exportrewarder?start_date=' + reports.startDate +'&end_date='+ reports.endDate + '&is_active=' + reports.activeUser, '_blank');
			});
		}
		

};*/
 
var reportGraph = {
	marginPieX : 150,
	marginPieY : 160,
	widthPie : 100,
	barX : 80,
	barY : 50,
	barwidth : 300,
	extraHeight : 50,
	extraSpace : 38,
	barSize : 20,
	setStartDate: null,
	setEndDate: null,
	loveDistributionYear : null,
	notificationMsg : "No record found between this period", 
	init: function() {
	$('#generate-report').attr('rel', 'graph');
		reports.captureInput();   
		reportGraph.reloadData();
		reportGraph.setStartDate = null;
		reportGraph.setEndDate = null;
		reportGraph.loadLoveData();
		//reportGraph.loadRewarderData();
		reportGraph.loadLoveDistributionData();
		
	},

	loadLoveData : function() {
	var userName = [];
	var userReceived = [];
		$.ajax({
			url: admin.baseUrl + '/reports/graphlove',
			type: 'POST', 
			data:  ( reportGraph.setStartDate !== null && reportGraph.setEndDate !== null) ?  {is_active:reports.activeUser, weightage: reports.weightage, review_period : reports.reviewPeriod, start_date : reportGraph.setStartDate, end_date:reportGraph.setEndDate, sort: 'received', dir : 'DESC'}
					: {is_active:reports.activeUser, weightage: reports.weightage, review_period : reports.reviewPeriod, start_date : reports.startDate, end_date:reports.endDate, sort: 'received', dir : 'DESC'},
			dataType: 'json',
			success: function(json){
				if(json !== false) {
					$.each(json,function(key,value) {
						if(key == 'userlist') {
							if(value !== null && value.length > 0) {
								$.each(value, function(key, user) {
									var received = isNaN(user.received) ? 0 : parseInt(user.received);
									userName.push(user.nickname + " (" + received + ")");		
									userReceived.push(received);
								});
								reportGraph.drawPieChart(userName,userReceived);
								// Hide loading overlay and reattach the overlay events
								$('#loading').bind('ajaxComplete', function() {
                                    $(this).fadeOut('fast');
                                });
							}
							
						}
					});
				}
				else {
					$('#love-count-chart').empty();
					$('#love-count-chart').text(reportGraph.notificationMsg).css("color","red");
				}
				$('#loading').bind('ajaxComplete', function() {
                    $(this).fadeOut('fast');
                });
			}
		}); 
	},

	drawPieChart : function(userName,userReceived) {           
		with(reportGraph) {
			$('#love-count-chart').empty();
			var loveCountChart = Raphael("love-count-chart");
			fin = function () {
			this.sector.stop();
			this.sector.scale(1.1, 1.1, this.cx, this.cy);
			if (this.label) {
				this.label[0].stop();
				this.label[0].scale(1.5);
				this.label[1].attr({"font-weight": 800});
			}
			}; 
			fout = function () {
				this.sector.animate({scale: [1, 1, this.cx, this.cy]}, 500, "bounce");
				if (this.label) {
					this.label[0].animate({scale: 1}, 500, "bounce");
					this.label[1].attr({"font-weight": 400});
			}
			};
			if(userName.length > 0 && userReceived.length > 0 ) {
				loveCountChart.g.txtattr.font = "12px 'Fontin Sans', Fontin-Sans, sans-serif";
				loveCountChart.g.piechart(marginPieX, marginPieY, widthPie, userReceived, {legend: userName }).hover(fin, fout);	
			}
		}
	},

/*	loadRewarderData : function() {
	var rewarderName = [];
	var rewarderPoint = [];
		$.ajax({
			url: 'reports/graphrewarder',
			type: 'POST',  
			data: ( reportGraph.setStartDate !== null && reportGraph.setEndDate !== null)? { is_active:reports.activeUser, sort: 'points', dir : 'DESC', start_date : reportGraph.setStartDate, end_date: reportGraph.setEndDate  } :
			{ is_active:reports.activeUser, start_date : reports.startDate, end_date:reports.endDate, sort: 'points', dir : 'DESC' } ,
			dataType: 'json',
			success: function(json){
					
				if(json !== false) {
					$.each(json,function(key,value){
						if(key == 'rewarderlist') {
							if(value !== null && value.length > 0) {
								$.each(value, function(key, user) {
									rewarderName.push(user.nickname);		
									rewarderPoint.push(user.points);
								}); 
								reportGraph.drawRewarderChart(rewarderName,rewarderPoint);
								// Hide loading overlay and reattach the overlay events
								$('#loading').bind('ajaxComplete', function() {
									$(this).fadeOut('fast');
								});
							}
						}	
					});
				}
				else {
					$('#rewarder-chart').empty();
					$('#rewarder-chart').text(reportGraph.notificationMsg).css("color","red");
				}
			}
		});		

	},

	drawRewarderChart: function(rewarderName,rewarderPoint) {
		with(reportGraph) { 
			$('#rewarder-chart').empty();
			var rewarderChart = Raphael("rewarder-chart");
			var axisX =  [];
			var nameX = [];	
			var nameSpace = 19;
			var valY = ["0"];
			var x =  barX;
			var y = 60;
			var hLines = 8;
			var lineSet = rewarderChart.set();
			if (rewarderName.length > 0 && rewarderPoint.length > 0 ) {
				// Trim the name
				for (var i = rewarderName.length; i--;) {
					if (rewarderName[i].length > 13) {
						rewarderName[i] = (rewarderName[i].substr(0, 9)) + '...';
					}
				}
				var rewarderMaxCount = Math.round(rewarderPoint[0]);
				var range = Math.round(rewarderMaxCount / hLines);
				var rangeSet = range;
				var barheigth = barSize * rewarderPoint.length;
				rewarderChart.g.txtattr.font = "12px 'Fontin Sans', Fontin-Sans, sans-serif";
				fin = function () {
					this.flag = rewarderChart.g.popup(this.bar.x, this.bar.y, this.bar.value || "0").insertBefore(this);
				};
				fout = function () {
					this.flag.animate({opacity: 0}, 300, function () {this.remove();});
				};
				rewarderChart.g.hbarchart(barX, barY, barwidth, barheigth, [rewarderPoint]).hover(fin, fout);
				targetHeigth = barheigth + extraHeight;  
				for(i = 0; i <= hLines; i++)	{
					valY.push(rangeSet);
					rangeSet += range;
				}				
				for(i = 0; i <= hLines ;i++) {
					axisX[i] =	rewarderChart.path("M"+ x + ","+ barY + "L" + x + "," + targetHeigth ).attr({stroke:'#D8D8DB',"stroke-width":1});   
					rewarderChart.g.text(x, (targetHeigth + nameSpace), valY[i]).attr({rotation:315});
					x = x + extraSpace;
					lineSet.push(axisX[i]);
				}    
				lineSet.push(rewarderChart.path("M"+ barX + ","+ barY + "H" + barwidth + "," + (barwidth + barX + 3)).attr({stroke:'#D8D8DB',"stroke-width":1}));
				lineSet.push(rewarderChart.path("M"+ barX + ","+ targetHeigth + "H" + barX + "," + (barwidth + barX + 3)).attr({stroke:'#D8D8DB',"stroke-width":1})); 
				for(i=0; i< rewarderName.length; i++) {
					var val = barheigth / rewarderName.length; 
					nameX[i] =	rewarderChart.g.text((barX - extraSpace), y, rewarderName[i]).attr({"font-size": 10}); 
					y =  y + nameSpace;

				}
				lineSet.toBack();
			}
			
		}
	},*/
	loadLoveDistributionData: function() {
		var loveDistributionName = [];
		var loveReceived = [];
		$.ajax({
		url: admin.baseUrl + '/reports/graphannuallove',
			type: 'POST',
			data: ( reportGraph.setStartDate !== null && reportGraph.setEndDate !== null) ? {is_active:reports.activeUser, sort: 'received', dir : 'DESC', start_date : reportGraph.setStartDate, end_date:reportGraph.setEndDate } 
				: {is_active:reports.activeUser, sort: 'received', dir : 'DESC', start_date : reports.startDate, end_date:reports.endDate },
			dataType: 'json',
			success: function(json){
				if(json !== false) {
					$.each(json,function(key,value){
						if(key == 'userlist') {
							if(value !== null && value.length > 0) {
								$.each(value, function(key, user) {
									var received = isNaN(user.received) ? 0 : parseInt(user.received);
									loveDistributionName.push(user.nickname);		
									loveReceived.push(received);
								});         
								reportGraph.drawLoveDistributionChart(loveDistributionName,loveReceived);
							}
						}
					});
				}
				else {
					$('#love-distribution-chart').empty();
					$('#love-distribution-chart').text(reportGraph.notificationMsg).css("color","red");
				}
				// Hide loading overlay and reattach the overlay events
				$('#loading').bind('ajaxComplete', function() {
                    $(this).fadeOut('fast');
                });
			}
		}); 
	},

	drawLoveDistributionChart: function(loveDistributionName,loveReceived) {
		
		with(reportGraph) {
 
			$('#love-distribution-chart').empty();
			var loveDistributionChart = Raphael("love-distribution-chart");
			var axisX =  [];
			var nameX = [];
			var nameSpace = 19;
			var valY = ["0"];
			var x =  barX;
			var y = 60;
			var hLines = 8;
			var totalHeight = 0;
			var lines = loveDistributionChart.set();
			if (loveDistributionName.length > 0 && loveReceived.length) {
				// Trim the name
				for (var i = loveDistributionName.length; i--;) {
					if (loveDistributionName[i].length > 13) {
						loveDistributionName[i] = (loveDistributionName[i].substr(0, 9)) + '...';
					}
				}
				var loveDistributionMaxCount = loveReceived[0];
				var range = loveDistributionMaxCount / hLines;
				var rangeSet = range;	
				var barheigth = barSize * loveReceived.length;
				fin = function () {
					this.flag = loveDistributionChart.g.popup(this.bar.x, this.bar.y, this.bar.value || "0").insertBefore(this);
				};
				fout = function () {
					this.flag.animate({opacity: 0}, 300, function () {this.remove();});
				};
				loveDistributionChart.g.txtattr.font = "12px 'Fontin Sans', Fontin-Sans, sans-serif";
				loveDistributionChart.g.hbarchart(barX, barY, barwidth, barheigth, [loveReceived]).hover(fin, fout);
				targetHeigth = barheigth + extraHeight;  
				for(i = 0; i <= hLines; i++)	{
					valY.push(rangeSet);
					rangeSet += range;
				}				
				for(i = 0; i <= hLines ;i++) {
					axisX[i] =	loveDistributionChart.path("M"+ x + ","+ barY + "L" + x + "," + targetHeigth ).attr({stroke:'#D8D8DB',"stroke-width":1});  
					loveDistributionChart.g.text(x, (targetHeigth + nameSpace), valY[i]).attr({rotation:315});
					x = x + extraSpace;
					lines.push(axisX[i]);
				}
				lines.push(loveDistributionChart.path("M"+ barX + ","+ barY + "H" + barwidth + "," + (barwidth + barX + 3)).attr({stroke:'#D8D8DB',"stroke-width":1}));
				lines.push(loveDistributionChart.path("M"+ barX + ","+ targetHeigth + "H" + barX + "," + (barwidth + barX + 3)).attr({stroke:'#D8D8DB',"stroke-width":1})); 
				for(i=0; i< loveDistributionName.length; i++) {
					var val = barheigth / loveDistributionName.length; 
					nameX[i] =	loveDistributionChart.g.text((barX - extraSpace), y, loveDistributionName[i]).attr({"font-size": 10}); 
					y =  y + nameSpace;
				}
				lines.toBack();
			}	
		}
		$('#love-distribution-chart').animate({height : parseInt(y) + 100, width : 500});
                $('#love-distribution-chart svg').animate({height : parseInt(y) + 100, width : 500});

	},
	reloadData: function() {
	
		$('#update-most-loved').click(function() {
			var lovePeriodYear = $('#love-period-year').val();
			var lovePeriodMonth = $('#love-period-month').val();
			var lovePeriodDate = reportGraph.loadDate(lovePeriodMonth, lovePeriodYear);
			if(lovePeriodDate !== false)
			{
				reportGraph.loadLoveData();	
				$('#error-love').empty();
			}
			else {
				$('#error-love').text(reports.errorMsg);
			}
		});
	/*	$('#update-rewarder').click(function() {
			var rewarderPeriodYear = $('#rewarder-period-year').val();
			var rewarderPeriodMonth = $('#rewarder-period-month').val();
			var rewarderPeriodDate = reportGraph.loadDate(rewarderPeriodMonth, rewarderPeriodYear);
			if (rewarderPeriodDate !== false) {
				reportGraph.loadRewarderData();
				$('#error-rewarder').empty();
			}
			else {
				$('#error-rewarder').text(reports.errorMsg);
			}
			
		});*/
		$('#update-love-distribution').click(function() {
			var loveDistributionYear = $('#love-distribution-period-year').val();
			var loveDistributionMonth = $('#love-distribution-period-month').val();
			var loveDistributionDate = reportGraph.loadDate(loveDistributionMonth, loveDistributionYear);
			if (loveDistributionDate !== false) {
				reportGraph.loadLoveDistributionData();
				$('#error-love-distribution').empty();
			}
			else {
				$('#error-love-distribution').text(reports.errorMsg);
			}
			
		});
	},
	loadDate: function(month, year) {
		if (month == 0 && year > 0) {
			reportGraph.setStartDate = year + '-01' + '-01'; 
			reportGraph.setEndDate = 	year + '-12' + '-31';
		}
		else if (month > 0 && year > 0) {
			reportGraph.setStartDate = year + '-' + month  + '-01'; 
			reportGraph.setEndDate = 	year + '-' + month + '-31';
		}
		else {
			return false;
		}
	}

};
