
$.pS = {
	width: 800,
	height: 220,
	margin: 55,
	marginx: 55,
	extraHeight:35,
	sliderHeight:30,
	sliderWidth:15,
	highLimit: 100/3*2,
	lowLimit: 100/3,
	lowLevelColor: "#A1AEBF",
	middleLevelColor: "#D7E8FF",
	highLevelColor: "#8598B2",
    counter: 1,
	dragger: function (e) {
		// raphael uses this function to calc the offset
		// we have to target the rect
		// when we're moving with the lines
		// on the slider
	    var target = this;
		if(target.type != 'rect') {
    	    target = target.point;
	    }
		target.ox = target.attr("x");
		target.oy = target.attr("y");
	},
    move: function (dx, dy) {
		// this is the function that actually moves the object
		// we have to target the rect
		// when we're moving with the lines
		// on the slider
	    var target = this;
		if(target.type != 'rect') {
    	    target = target.point;
	    }
        // get the adjusted y value taking into consideration the margins, slider height and min/max values
		var ty = target.oy + dy  - $.pS.margin + $.pS.sliderHeight/2;
		ty = ty > 0 ? ty < $.pS.height ? ty : $.pS.height : 0;
        
        // update the targets val attribute
		target.val = $.pS.getValFromY(ty);
        // move the point (1 for now)
		$.pS.setPoint(target, 1);
        // set the hover target
		$.pS.r.current = target;
        // hover activation
		$.pS.hover();
        // while we're moving we can't show the percentage value
		$('#YPerc').html('--');
		$('#YAmount').html('--');
        // this cleans up for safari
		$.pS.r.safari();
    },
	up: function () {
		// and this one is called once we've finished the drag
        // once we're finished let's get the unhover timeout going
		$.pS.unhover();
        // and recalculate everyone's percentages
        $.pS.recalcPercs();
        // and save the new positions
        $.pS.savePoints();
        // and update the hoverbox's % indicator
		$('#YPerc').html((parseInt(100 * $.pS.current.perc)/100) + '%');
		$('#YAmount').html("$" + rewarder.formatAsCurrency( parseInt(rewarder.getAmountInDollar() * $.pS.current.perc )/100+'')   );
    },
    doHover: function() {
        // get the correct target
        var target = this;
        if(target.type != 'rect') {
            target = target.point;
        }
        // if there's no y there's no point
        if(target.attr('y') != undefined) {
            $.pS.current = target;
            $.pS.hover();
        }
    },
    setPopupEvent: function() {
        // add event handling to the menuPop menu options
        $('#menuPopup .clicklove').unbind('click').click(function(e) {
            e.preventDefault();
            if ( $.pS.current && $.pS.current.data) {
                rewarder.showLove($.pS.current.data.id, $.pS.current.data.nickname);
            }
        });
        $('#menuPopup .clickreview').unbind('click').click(function(e) {
            e.preventDefault();
            if ( $.pS.current && $.pS.current.data) {
                rewarder.showReview($.pS.current.data.id, $.pS.current.data.nickname);
            }
        });
        if ( rewarder.canWeUpdateGraph()  ) {
            $('#menuPopup .removeUser').click(function(e) {
                e.preventDefault();
                if ( $.pS.current && $.pS.current.data) {
                    var id = $.pS.current.data.id;
                    $.pS.removePoint($.pS.current);
                    rewarder.deleteRewarderUser(id);
                }
            });
        } else {
            $('#menuPopup .removeUser').remove();
        }
    },
    hover: function() {
        // if we don't know what to hover for, let's not do anything
		if (!$.pS.current) return;
		if(!$.pS.mPop) {
            // menupop needs adding to the dom if it's not already happened
		    // add this in here as we need it to pop out of any container
		    // so we need it dynamically appended to the body
            html = $('#menuPopupRef').html();
            $('#menuPopupRef').remove();
            $.pS.mPop = $('<div id="menuPopup" style="opacity: 0;"></div>');
            $.pS.mPop.html(html);
            $('#raftest').append($.pS.mPop);
            $.pS.setPopupEvent();
            $.pS.off = $('#canvas').offset();
            $('#menuPopup').animate({opacity: 0}).hover($.pS.hover, $.pS.unhover);
		} else {
            $('#menuPopupRef').remove();
        }
        if ( $.pS.current && $.pS.current.data) {
            // set the username on the hover box
            $('#rname').html($.pS.current.data.nickname);
        }
        // set the percent info on the hover box
		$('#YPerc').html((parseInt(100 * $.pS.current.perc) / 100) + '%');
		$('#YAmount').html("$" + rewarder.formatAsCurrency( parseInt(rewarder.getAmountInDollar() * $.pS.current.perc )/100+'')   );
        
        // get the adjusted values for x and y of the menupop
		var x = $.pS.current.attr('x') /*+ $.pS.off['left'] */- ($.pS.mPop.outerWidth()/2 - $.pS.current.attr('width')/2);
		var y = $.pS.current.attr('y') /*+ $.pS.off['top']*/ - ($.pS.mPop.outerHeight());
        // stop any unhover behaviour
		clearTimeout($.pS.mPop.data('tm'));
        // change y now and animate visibility and x value
		$.pS.mPop.css({top: y, display: 'block'}).stop().animate({left: x, opacity: 1}, 100);
		// On moving change the color of silder
		var newColor = $.pS.changeColor($.pS.current.val);
		$.pS.current.attr({fill:newColor, stroke:"#000"});
    },
    unhover: function() {
        // reset the unhover timeout to hide the pop
		clearTimeout($.pS.mPop.data('tm'));
		$.pS.mPop.data('tm', setTimeout(function() {
			$.pS.mPop.stop().animate({opacity: 0}, 300, function() { $(this).css('display', 'none');});
		}, 500));
    },
	changeColor: function(val) {
        //
		if (val > $.pS.highLimit) {
			return $.pS.highLevelColor;
		}
		else if((val >= $.pS.lowLimit) && (val <= $.pS.highLimit)) {
			return  $.pS.middleLevelColor;
		}
		else if(val < $.pS.lowLimit) {
			return $.pS.lowLevelColor;
		}		
	},
    savePoints: function(fAfter) {
        // save all the points values and percentages
        // this needs to update in one go as this has too much lag
        // with large data sets
        var list = {};
        for (var i = 0; i < $.pS.shapes.length; i++) {
            // get the val and percs for the id into a nice object we can send
            if ($.pS.shapes[i].data.id !== undefined) {
                list[$.pS.shapes[i].data.id] = {val: $.pS.shapes[i].val, perc: $.pS.shapes[i].perc};
            }
        }
        // this is a call to rewarder.js
        rewarder.updateRewarderUsers(list,fAfter);
    },
	setPoint: function(item, now) {
        // get the actual Y position based on the item's current val
		var y = $.pS.getYFromVal(item.val) -  $.pS.sliderHeight/2;
		if(isNaN(y)) {
			return;
		}
        // calculate the amount it's going to move (for the hlines)
		var dY = y - item.attr("y");
		if(!now) {
			item.animate({y: y, fill:$.pS.changeColor(item.val), stroke:"#000"},100);           
		}
		else {
			item.attr({y: y});
		}
		//Set the horizontal lines on the sliders
		if ( item.linePack && item.linePack.length ) {
			item.linePack.translate(0,dY);
		}
    },
    setPerc: function(item, now) {
        // update the percentage bar
        var yh = $.pS.getYFromPerc(item.perc);
        if(item.real) {
    		if(!now) {
    		    item.real.animate({y: $.pS.margin + $.pS.height - yh, height: yh}, 100);
    		}
    		else {
    		    item.real.attr({y: $.pS.margin + $.pS.height - yh, height: yh})
    		}
		}
		
    },
    // the following are helper functions which should be used to calculate Y positions of elements
    getYFromVal: function(val) {
        //this includes margin for simplicities sake
        return (100-val)*($.pS.height/100) + $.pS.margin;
    },
    getYFromPerc: function(val) {
        // you can get a height based on a percentage
        return (val)*($.pS.height/100);
    },
    getValFromY: function(y) {
        // this calculates the actual value based on a given Y pos 
        // you need to remove margins of the passed in value
        return(Math.floor(($.pS.height-y)/(($.pS.height)/100)));
    },
    makePoint: function(data) {
		//Drawing the slider
        if ( !$.pS || !$.pS.r ) {
            return;
        }
        
		var point = $.pS.r.rect(0,$.pS.margin,$.pS.sliderWidth,$.pS.sliderHeight,4);
		point.val = data.points|0;
		point.data = data;
        // add the point to the points array
		$.pS.addPoint(point);
    },
    addPoint: function(point) {
        //does what it says
        $.pS.shapes.push(point);
		$.pS.updatePoints();
		$.pS.recalcPercs();
    },
    removePoint: function(point) {
        //does what it says
		var ref = point.ref;
		point.t.remove();
		point.l.remove();
        if (point.real) {
            point.real.remove();
        }
		point.linePack.remove();
		point.remove();
		$.pS.shapes.remove(ref);
		$.pS.redoPoints(true,false);
	//	$.pS.recalcPercs();
    },
    drawSlider: function(point){
        var dotLine = $.pS.r.set(),
            thumbSet = $.pS.r.set(),
        // Drawing three horizontal line on slider
            path1 =  "M 0 217 L 5 0",
            path2 =  "M 0 220 L 5 0",	
            path3 =  "M 0 223 L 5 0"; 	
        
        point.drawDone=true;
        dotLine.push($.pS.r.path(path1));
        dotLine.push($.pS.r.path(path2));
        dotLine.push($.pS.r.path(path3));
        
        // we'll add a reference to the point to each
        // line so we can track them for the slider
        dotLine[0].point = point;
        dotLine[1].point = point;
        dotLine[2].point = point;
        
        point.linePack = dotLine;
        thumbSet.push(point,dotLine); 
        
////		point.val = data.points|0;
        //percentage bar
        if ($.pS.width > 900) {
            $.pS.sliderWidth = 5;
        } else {
            $.pS.sliderWidth = 15;
        }
        point.real = $.pS.r.rect($.pS.marginx,$.pS.margin + $.pS.height - 1,$.pS.sliderWidth/3,1);
        point.real.attr({stroke: 'none', fill: "#E0E0E0"});
        
////		point.data = data;

        point.attr({"stroke-width": 1});
        if ( rewarder.canWeUpdateGraph() ) {
        // only allow movement when the period is open
            thumbSet.attr({cursor: "move"}).drag($.pS.move, $.pS.dragger, $.pS.up);
        }
        thumbSet.mouseover($.pS.doHover);
        point.mouseout($.pS.unhover);
        
        // set the text label and vertical line for this point
// To debug insert the value in the nickname		point.t = $.pS.r.text(0, $.pS.height+($.pS.margin*1.5), point.val+" - "+point.data.nickname);
        point.t = $.pS.r.text(0, $.pS.height+($.pS.margin*1.5), point.data.nickname);
        point.t.attr({font: '10px Helvetica, Arial', 'font-weight':'bold',rotation: -45});
        point.l = $.pS.r.path('M0 0L1 1');
        point.l.attr({stroke: '#C8C8C8', fill: "none"});
        dotLine.attr({stroke:"#00f"}); 
        point.real.toBack();
        point.l.toBack();
        dotLine.toFront();
    },
    updateSlider: function(point,space) {
        var i = point.ref;
        if (!point.drawDone) {
            $.pS.drawSlider(point);
        }
        //get this points x pos
        var ix = i*space+$.pS.marginx;
        
        //vertical line path def
        path  =  'M' +  (ix) + ' ' + parseInt($.pS.margin) + 'L' + parseInt((ix)) + ' ' + parseInt($.pS.margin + $.pS.height);
        
        
        // slider
        point.attr({x: (ix-8)});
        // perc bar
        if (point.real) {
            point.real.attr({x: (ix+2)});
        }
        // text label
        point.t.attr({x: ix}); 
        // vert line
        point.l.attr({path: path});

        // those pesky horizontal lines on each slider need the following
        var origY =  point.attr("y");
        var targetY = origY + 12;
        var path1 =  "M "+ (ix-3) + " " + targetY +" l 5 0"; 	
        var path2 =  "M "+ (ix-3) + " " + (targetY + 3) +" l 5 0"; 	
        var path3 =  "M "+ (ix-3) + " " + (targetY + 6 )+" l 5 0"; 	
        point.linePack[0].attr({path: path1});
        point.linePack[1].attr({path: path2});
        point.linePack[2].attr({path: path3});
        
// keep this line to debug            point.t.attr("text",point.val+" - "+point.data.nickname);
        point.t.attr("text",point.data.nickname);
        
        $.pS.setPoint(point);
    },
    updatePoints: function() {
        var point,
            t = 0,
            i;

         // calculate the correct horizontal offset for all points
        $.pS.space = $.pS.shapes.length-1 ? Math.floor(($.pS.width - (2*$.pS.marginx)) / ($.pS.shapes.length-1)) : 0;

		// move the sliders and vertical lines on the x axis
		for ( i = 0, ii = $.pS.shapes.length; i < ii; i++) {
            point = $.pS.shapes[i];
			point.ref = i;
            if ( $('#canvas:visible').length != 0 ) {
                setTimeout(function(){
                    $.pS.updateSlider(point,$.pS.space);
                }, i*500);
            }
			t += point.val; 	  
		}
        $.pS.updateTotalOfPoints(t);
        return t;
    },
    getTotalPoints: function() {
        var t = 0,
            i;

		for ( i = 0, ii = $.pS.shapes.length; i < ii; i++) {
			t += $.pS.shapes[i].perc; 	  
		}
        $.pS.updateTotalOfPoints(t);
        return t;
    },
    setMinimumAmount: function(minimumAmount,bUpdateAndSave, fAfter) {
        var i;
		$.pS.recalcPercs();
        for ( i = 0, ii = $.pS.shapes.length; i < ii; i++) {
            if ( (rewarder.getAmountInDollar() * $.pS.shapes[i].perc / 100) < minimumAmount) {
                $.pS.shapes[i].val = 0;
            } 	  
        }
        if (bUpdateAndSave) {
            $.pS.updatePoints();
            $.pS.recalcPercs();
            $.pS.savePoints(fAfter);
        } else {
            if (fAfter) fAfter();
        }
    },
    setEqually: function(minimumAmount,fAfter) {
        var t = $.pS.getTotalPoints(),
            i,
            average= 0;
        
        if ( $.pS.shapes.length ) {
            average= t / $.pS.shapes.length;
        }
		for ( i = 0, ii = $.pS.shapes.length; i < ii; i++) {
			$.pS.shapes[i].val = average; 	  
		}
        if (minimumAmount > 0) {
            $.pS.setMinimumAmount(minimumAmount);
        }
		$.pS.updatePoints();
		$.pS.recalcPercs();
        $.pS.savePoints(fAfter);
    },
    setTopPerc: function(limitPerc, minimumAmount, fAfter) {
        var i,
            max= 0,
            limit;
        
		for ( i = 0, ii = $.pS.shapes.length; i < ii; i++) {
			if ( max < $.pS.shapes[i].val) {
                max = $.pS.shapes[i].val;
            }            
		}
        limit = max - (max * limitPerc / 100);
		for ( i = 0, ii = $.pS.shapes.length; i < ii; i++) {
			if ( limit > $.pS.shapes[i].val) {
                $.pS.shapes[i].val = 0;
            }            
		}
        if (minimumAmount > 0) {
            $.pS.setMinimumAmount(minimumAmount);
        }
		$.pS.updatePoints();
		$.pS.recalcPercs();
        $.pS.savePoints(function() {
            fAfter({
                max: max,
                limit: limit
            });
        });
    },
    setTopNum: function(limitNum, minimumAmount, fAfter) {
        var i,
            prevVal=0;
        
		for ( i = 0, ii = $.pS.shapes.length; i < ii; i++) {
			if ((i > limitNum - 1) && (prevVal != $.pS.shapes[i].val)) {
                $.pS.shapes[i].val=0;
            }     
            prevVal = $.pS.shapes[i].val;
		}
        if (minimumAmount > 0) {
            $.pS.setMinimumAmount(minimumAmount);
        }
		$.pS.updatePoints();
		$.pS.recalcPercs();
        $.pS.savePoints(fAfter);
    },

    updateTotalOfPoints : function(t) {
        return; // keep this function to debug
        if ($("#totalOfPoints").length == 0) {
            $("#raftest").prepend("<div id='totalOfPoints'>Total of points in the graph:<span id='totalOfPointsVal'></span></div>");
        }
        $("#totalOfPointsVal").html(t);
    },
    
    recalcPercs: function() {
        // recalculate percentages based on the slider positions
		var total = 0;
		var totalperc = 0;
		for (var i = 0, ii = $.pS.shapes.length; i < ii; i++) {
			total += $.pS.shapes[i].val;	
		}
		for (var i = 0, ii = $.pS.shapes.length; i < ii; i++) {
			$.pS.shapes[i].perc = $.pS.shapes[i].val * 100/total;
			$.pS.setPerc($.pS.shapes[i]);
			totalperc += $.pS.shapes[i].perc;
		}
//		if($.pS.loading == false) $.pS.savePoints();        
    },
    redoPoints: function(update,sort) {
        // will resort and save if you want
        if (sort && sort === true) {
            $.pS.shapes.sort(pointSort);
        }
        var rt = $.pS.updatePoints();
        $.pS.recalcPercs();
		if ( update ) {
		    $.pS.savePoints();
		}
    },
    setTitle: function(start_date, end_date) {
        $.pS.tx.attr('text',"Added value between " + start_date + " and " + end_date);
    },
    init: function(bReset) {
        // initiate the canvas and set up graph chrome
        $.pS.shapes = [];
        $.pS.space = 0;
        //set/reset the canvas
        if( !bReset && $.pS.r) {
            $.pS.r.clear();
            Raphael.getColor.reset(); 
        } else {
            if (bReset) {
                if ($.pS.r) {
                    $.pS.r.clear();
                    delete $.pS.r;
                }
            }
            if ($.pS.mPop) {
                $.pS.mPop.remove();
                delete $.pS.mPop;
            }
            if ($('#canvas').length == 0) {
                return;
            }
            $.pS.r = Raphael("canvas", $.pS.width, $.pS.height + ($.pS.margin * 2));
        }
        // drawing left axis rectangle range 
        // use getYFromVal to calculate accurate Y based on range of 100
		x = $.pS.marginx/4;
		$.pS.r.rect(2*x, $.pS.getYFromVal(100), x, $.pS.getYFromPerc(100/3)).attr({fill: $.pS.highLevelColor, stroke:'none'}); // high level rectangle
		$.pS.r.rect(2*x, $.pS.getYFromVal(100/3*2), x, $.pS.getYFromPerc(100/3)).attr({fill: $.pS.middleLevelColor, stroke:'none'}); // middle level rectangle
		$.pS.r.rect(2*x, $.pS.getYFromVal(100/3), x,  $.pS.getYFromPerc(100/3)).attr({fill: $.pS.lowLevelColor, stroke:'none'}); // low level rectangle
        // drawing rightaxis rectangle range 
		$.pS.r.rect($.pS.width - x, $.pS.margin, x, $.pS.height).attr({fill: '#E0E0E0', stroke:'none'}); // percentage bar
        
		yt = $.pS.r.set();
		yt.push(
            // labels for left axis
			$.pS.r.text(x, $.pS.getYFromVal(0), '-'),
			$.pS.r.text(x, $.pS.getYFromVal(100), '+'),
			$.pS.r.text(x, $.pS.getYFromVal(50), 'Added value').attr({rotation: -90}),
            // labels for right axis
			$.pS.r.text($.pS.width-2*x, $.pS.getYFromVal(50), '%age of total').attr({rotation: -90})
			);
		yt.attr({font: '16px Helvetica, Arial', 'font-weight':'bold'});

		// Drawing horizontal grid lines
		var y_axis = $.pS.r.set();
		var y_axis_path  = [] ;
		var y_limit = $.pS.margin; 
		for(i= 0; i< 7; i++) {  
			var alternateColor = ( (i % 2) == 0 ) ? '#D8D8D8': '#DFDFDF' ;
			y_axis_path[i] =  $.pS.r.path("M0 0L0 1"); 
			y_axis.push(y_axis_path[i].attr({path:"M"+ $.pS.marginx +","+ y_limit + "H106,"+ ($.pS.width - $.pS.marginx),stroke:alternateColor}));
			y_limit = y_limit + $.pS.getYFromPerc(100/6);
		}
		y_axis.toBack();
        // button bindings
        $('#updatr').unbind('click').click(function(e) {
            e.preventDefault();
            $.pS.redoPoints(true, true);
        });
        $('#publish').click(function(e) {
            e.preventDefault();
            $.pS.recalcPercs();
        });
        $('#pointsUpdated').hide();
        $.pS.tx = $.pS.r.text($.pS.width/2, $.pS.height/4 + $.pS.margin, "");
        $.pS.tx.attr({
            font: '30px Helvetica, Arial',
            fill: "#E0E0E0",
            'fill-opacity': '1'
        });
        $.pS.tx.toBack();
    }
};

//helper functions
function pointSort(m,n){
    try{

        var a = parseInt(m.val);
        var b = parseInt(n.val);
        if(a== b) return 0;
        else return (a<b)? 1: -1;
    }
    catch(er){
        return 0;
    }
}

// Array Remove - By John Resig (MIT Licensed)
Array.prototype.remove = function(from, to) {
  var rest = this.slice((to || from) + 1 || this.length);
  this.length = from < 0 ? this.length + from : from;
  return this.push.apply(this, rest);
};

$.in_array = function(needle, haystack) {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
        return false;
    };
    
