var TimelineChart;
var LoveChart;

(function () {


function interpolate(values, x) {
    var i = (values.length - 1) * x;
    var i1 = Math.floor(i), i2 = Math.ceil(i), k = i - i1;
    return values[i1] + (values[i2] - values[i1]) * k;
};

function Series(chart, attrs) {
    return {
        attrs:attrs,
        chart:chart,
        stroke:chart.r.path('M0,0').attr({ fill:'none' }).attr(attrs),
        fill:chart.r.path('M0,0').attr({ stroke:'none' }).attr(attrs),
        hasData:false,
        values:undefined,
        interpolated:undefined,
        verticals:[],
        dots:[],
        dotSize:5,
        bigDotSize:7,
        maxSamples:30,

        y:function (v) {
            return this.chart.chartTop + Math.round((1 - v) * this.chart.chartHeight);
        },

        yFromX:function (x) {
            return this.y(interpolate(this.interpolated, (x - this.chart.chartLeft) / this.chart.chartWidth));
        },

        findNearestPoint:function (x) {
            var result = undefined, dx = undefined;

            if(this.interpolated) {
          for (var i = 0; i < this.interpolated.length; i++) {
          if (typeof this.interpolated[i] !== 'undefined') {
              var z = this.chart.chartLeft + this.chart.chartWidth * i / (this.interpolated.length - 1);
              var t = Math.abs(x - z);
              if (typeof dx === 'undefined' || t < dx) {
              dx = t;
              result = { x:z, y:this.y(this.interpolated[i]) };
              }
          }
          }
        }

            return result;
        },

        generatePath:function () {
            var dx = this.chart.chartWidth / (this.interpolated.length - 1), px = -dx, py = 0;
            var path = '';


            for (var i = 0; i < this.interpolated.length; i++) {
                var x = this.chart.chartLeft + this.chart.chartWidth * i / (this.interpolated.length - 1);

                if (typeof this.interpolated[i] !== 'undefined') {
                    var y = this.y(this.interpolated[i]);
                    path += i ? 'C' + [ x - (x - px) / 2, py, x - (x - px) / 2, y, x, y ] : 'M' + [ x, y ];
                    py = y;
                    px = x;
                }
            }

            /*if (i > this.maxSamples) {
                this.maxSamples = i;
            } else {
                for (; i < this.maxSamples; i++) {
                    path += 'L' + [ x, y ];
                }
            }*/

            return path;
        },

        drawStroke:function () {
            var path = this.generatePath();
            var stroke = { path:path };

            if (!this.hasData) {
                this.stroke.attr(stroke);
                this.hasData = true;
            } else {
                this.stroke.animate(stroke, this.chart.animationLength, '<>');
            }
        },

        drawFill:function () {
            var path = this.generatePath();

            var fill = { path:path + 'L' + (this.chart.chartLeft + this.chart.chartWidth) + ',' + (this.chart.chartTop + this.chart.chartHeight) + ' ' + this.chart.chartLeft + ',' + (this.chart.chartTop + this.chart.chartHeight) + 'z' };

            if (!this.hasData) {
                this.fill.attr(fill);
                this.hasData = true;
            } else {
                var self = this;
                this.fill.animate({ opacity:0 }, this.chart.animationLength / 2, '<>', function () {
                    self.fill.attr(fill);
                    self.fill.animate({ opacity:attrs.opacity || 1 }, self.chart.animationLength / 2, '<>');
                });
            }
        },

        drawDots:function () {
            var dx = this.chart.chartWidth / (this.interpolated.length - 1);

            for (var i = 0, o = 0; i < this.interpolated.length; i++) {
                if (typeof this.interpolated[i] === 'undefined') {
                    continue;
                }

                var x = this.chart.chartLeft + this.chart.chartWidth * i / (this.interpolated.length - 1), y = this.y(this.interpolated[i]);
                if (!this.dots[o]) {
                    this.dots[o] = this.chart.r.circle(this.chart.width + this.dotSize, y, this.dotSize).attr(this.attrs);
                }
                
                this.dots[o].show().animate({ cx:x, cy:y }, this.chart.animationLength, '<>');
                
                o++;
            }

            if (o > this.maxSamples) {
                this.maxSamples = o;
            }

            for (; o < this.dots.length; o++) {
                if (this.dots[o]) {
                    this.dots[o].show().animate({ cx:this.chart.width + this.dotSize, cy:y }, this.chart.animationLength, '<>');
                }
            }
        },

        drawVerticals:function () {
            var dx = this.chart.chartWidth / (this.interpolated.length - 1);

            for (var i = 0, o = 0; i < this.interpolated.length; i++) {
                if (typeof this.interpolated[i] === 'undefined') {
                    continue;
                }

                var x = this.chart.chartLeft + this.chart.chartWidth * i / (this.interpolated.length - 1), y = this.y(this.interpolated[i]);
                if (!this.verticals[o]) {
                    this.verticals[o] = this.chart.r.path('M' + this.chart.width + ',' + this.chart.chartTop + ' L' + this.chart.width + ',' + (this.chart.chartTop + this.chart.chartHeight)).attr(this.attrs);
                }
                this.verticals[o].animate({ path:'M' + x + ',' + this.chart.chartTop + ' L' + x + ',' + (this.chart.chartTop + this.chart.chartHeight) }, this.chart.animationLength, '<>');

                o++;
            }

            if (o > this.maxSamples) {
                this.maxSamples = o;
            }

            for (; o < this.verticals.length; o++) {
                if (this.verticals[o]) {
                    this.verticals[o].animate({ path:'M' + this.chart.width + ',' + this.chart.chartTop + ' L' + this.chart.width + ',' + (this.chart.chartTop + this.chart.chartHeight) });
                }
            }
        },

        draw:undefined,

        setData:function (values, dontInterpolate) {
            this.values = values;
            this.interpolated = !dontInterpolate && values.length > this.maxSamples ? this.chart.interpolate(values, this.maxSamples) : values;
            this.draw();
        },

        setRandomData:function () {
            var values = [], o = Math.random() * 100;

            for (var i = 0; i < 30; i++) {
                values.push(Math.random() * 0.2 + 0.8 * (Math.sin(o + i / 3) / 2 + 0.5));
            }

            this.setData(values);
        }
    };
}

TimelineChart = function (containerId, width, height, samples) {
    this.r = Raphael(containerId, width, height);
    this.base = this.r.rect(0, 0, width, height).attr({ fill:'#FFF', stroke:'none' });
    this.tmpObjects = [];
    this.width = width;
    this.height = height;
    this.samples = samples;
    this.series = [];
    this.scale = 1.0;
    this.animationLength = 2000;
    this.popupTextCallback = undefined;
    this.dotMouseoverCallback = undefined;
    this.dotMouseoutCallback = undefined;
    this.frameVisible = false;
    this.frame = undefined;
    this.labels = [];
    this.axisLabelsAreaHeight = undefined;
    this.padding = 10;
    this.chartLeft = this.padding + 30;
    this.chartTop = this.padding;
    this.chartRight = this.padding + 30;
    this.chartWidth = width - this.padding - this.chartLeft - this.chartRight;
    this.chartHeight = undefined;
    this.leftLabels = [];
    this.rightLabels = [];
    this.bottomLabels = [];
    this.grid = [];
    this.gridRows = 1;
    this.gridCols = 1;
    this.gridColor = '#E0E0E0';

    this.vGridLine = function (x) {
        return this.r.path('M' + x + ',' + this.chartTop + ' L' + x + ',' + (this.chartTop + this.chartHeight)).attr({ stroke:this.gridColor }).toBack();
    };

    this.hGridLine = function (y) {
        return this.r.path('M' + this.chartLeft + ',' + y + ' L' + (this.chartLeft + this.chartWidth) + ',' + y).attr({ stroke:this.gridColor }).toBack();
    };

    this.setAxisLabelsAreaHeight = function (v) {
        this.axisLabelsAreaHeight = v;
        this.chartHeight = this.height - this.chartTop - v;

        for (var i = 0; i < this.grid.length; i++) {
            this.grid[i].remove();
        }

        this.grid = [];

        for (var i = 0; i <= this.gridRows; i++) {
            this.grid.push(this.hGridLine(this.chartTop + this.chartHeight / this.gridRows * i));
        }

        for (var i = 0; i <= this.gridCols; i++) {
            this.grid.push(this.vGridLine(this.chartLeft + this.chartWidth / this.gridCols * i));
        }
    };

    this.setAxisLabelsAreaHeight(0);
};

TimelineChart.prototype.interpolate = function (v, samples) {
    if (!samples) {
        samples = this.samples;
    }

    var r = [ samples ];

    for (var i = 0; i < samples; i++) {
        r[i] = interpolate(v, i / (samples - 1));
    }

    return r;
};

TimelineChart.prototype.addVerticalsSeries = function (attrs) {
    var result = new Series(this, attrs);
    result.draw = result.drawVerticals;
    this.series.push(result);
    return result;
};

TimelineChart.prototype.addDotsSeries = function (attrs) {
    var result = new Series(this, attrs);
    result.draw = result.drawDots;
    this.series.push(result);
    return result;
};

TimelineChart.prototype.addStrokeSeries = function (attrs) {
    var result = new Series(this, attrs);
    result.draw = result.drawStroke;
    this.series.push(result);
    return result;
};

TimelineChart.prototype.addFillSeries = function (attrs) {
    var result = new Series(this, attrs);
    result.draw = result.drawFill;
    this.series.push(result);
    return result;
};

TimelineChart.prototype.forAllSeries = function (callback) {
    for (var i = 0; i < this.series.length; i++) {
        callback.call(this, this.series[i], i);
    }
};

TimelineChart.prototype.showFrameAt = function (frameX, frameY, lines) {
    if (this.frameVisible) {
        this.hideFrame();
    }

    var hPadding = 20, vPadding = 10, vOffset = 10, hOffset = 10;

    if (!this.frame) {
        this.frame = this.r.rect(0, 0, 0, 0, 5).attr({ fill:'#FFF', stroke:'#000', 'stroke-width':2 } );
    }

    this.tmpObjects.push(this.r.circle(frameX, frameY, 7).attr(this.framePointAttr || {}).toFront());

    var maxWidth = 0, totalHeight = 0;

    for (var i = 0; i < lines.length; i++) {
        var l = this.r.text(0, 0, '').attr(lines[i]).attr({ 'text-anchor':'start' });
        this.labels.push(l);
        maxWidth = Math.max(maxWidth, l.getBBox().width);
        totalHeight += l.getBBox().height;
    }

    var frameWidth = hPadding * 2 + maxWidth, frameHeight = vPadding * 2 + totalHeight;

    frameX += hOffset;

    if (frameX + frameWidth > this.width - 1) {
        frameX -= hOffset * 2 + frameWidth;
    }

    frameY += vOffset;

    if (frameY + frameHeight > this.chartHeight - 1) {
        frameY -= vOffset * 2 + frameHeight;
    }

    this.frame.attr({ x:frameX, y:frameY, width:frameWidth, height:frameHeight }).show().toFront();
    var y = frameY + vPadding;
    var xPadIE=0;
    if ($.browser.msie) {
        xPadIE = hPadding/2;
    }
    for (var i = 0; i < this.labels.length; i++) {
        this.labels[i].attr({
            x:frameX - xPadIE + frameWidth / 2 - this.labels[i].getBBox().width / 2,
            y:y + this.labels[i].getBBox().height / 2
        }).show().toFront();

        y += this.labels[i].getBBox().height;
    }

    this.frameVisible = true;
};

TimelineChart.prototype.hideFrame = function () {
    if (this.frameVisible) {
        for (var i = 0; i < this.tmpObjects.length; i++) {
            this.tmpObjects[i].remove();
        }

        this.frame.hide();

        for (var i = 0; i < this.labels.length; i++) {
            this.labels[i].remove();
        }

        this.labels = [];

        this.frameVisible = false;
    }
};

TimelineChart.prototype.showSample = function (x, y) {
    var xs = [];

    this.forAllSeries(function (series, i) {
        xs[i] = Math.round((series.values.length - 1) * (x - this.chartLeft) / (this.chartWidth - 1));
    });

    if (this.popupTextCallback) {
        this.showFrameAt(x, y, this.popupTextCallback(xs));
    }
};

TimelineChart.prototype.hideSample = function () {
    this.hideFrame();
};

TimelineChart.prototype.getSeriesMax = function (seriesData, initialMax) {
    var max = initialMax;
    for (var i = 0; i < seriesData.length; i++) {
        var dataValue = seriesData[i], intValue = 0;
        if(dataValue == undefined) {
          continue;
        }
        intValue = parseInt(dataValue,10);
        max = Math.max(intValue, max);
    }
    return max;
};
    
TimelineChart.prototype.normalizeSeries = function (seriesData, maxValue) {
    var normalizedData = [];
    for (var i = 0; i < seriesData.length; i++) {
        var dataValue = seriesData[i], intValue = 0;
        if(dataValue == undefined) {
          normalizedData[i] = undefined;
        } else {
          intValue = parseInt(dataValue,10);
          normalizedData[i] = intValue / maxValue;
        }
    }
    return normalizedData;
};

TimelineChart.prototype.updateAxisMax = function (seriesData, targetSeriesData) {
    var normalizedData = [];
    for (var i = 0; i < seriesData.length; i++) {
        var dataValue = seriesData[i];
        if(dataValue == undefined || dataValue <= targetSeriesData[i]) {
          normalizedData[i] = targetSeriesData[i];
        } else { 
        normalizedData[i] = dataValue;
        }
    }
    return normalizedData;
};


TimelineChart.prototype.setBottomLabels = function (attrs, labels) {
	this.setAxisLabelsAreaHeight(labels.length * 10 + 10);

	for (var i = 0; i < this.bottomLabels.length; i++) {
        this.bottomLabels[i].remove();
    }

    this.bottomLabels = [];

    for (var i = 0; i < labels.length; i++) {
        var y = this.height - this.axisLabelsAreaHeight + (i + 1) * 10;
        var row = labels[i];

        var minX = -1000, spacing = 3;

        if (row.length > 1) {
            for (var j = 0; j < row.length; j++) {
                if (typeof row[j] !== 'undefined' && row[j] != null) {
                    var x = this.chartLeft + j / (row.length - 1) * this.chartWidth;
                    var t = this.r.text(x, y, row[j]).attr({ 'text-anchor':'middle' });
                    if (x - t.getBBox().width / 2 < minX) {
                        t.remove();
                    } else {
                        minX = x + t.getBBox().width / 2 + spacing;
                        this.bottomLabels.push(t);
                    }
                }
            }
        }
    }
};

TimelineChart.prototype.setLeftLabels = function (attrs, max) {
    var steps = [ 5, 10, 20, 50, 100 ], minSpacing = 20;
    var step = steps[0], i = 0;
    while (this.chartHeight * step / max < minSpacing) {
        step = steps[i] ? steps[i] : step * 2;
        i++;
    }

    for (var i = 0; i < this.leftLabels.length; i++) {
        this.leftLabels[i].remove();
    }

    this.leftLabels = [];

    var y = 0;

    while (y <= max) {
        var vy = this.chartTop + this.chartHeight * (1 - y / max);
        this.leftLabels.push(this.r.text(35, vy, '' + y).attr(attrs));
        this.leftLabels.push(this.hGridLine(vy));
        y += step;
    }
};

TimelineChart.prototype.setRightLabels = function (attrs, max) {
    var steps = [ 5, 10, 20, 50, 100 ], minSpacing = 20;
    var step = steps[0], i = 0;
    while (this.chartHeight * step / max < minSpacing) {
        step = steps[i] ? steps[i] : step * 2;
        i++;
    }

    for (var i = 0; i < this.rightLabels.length; i++) {
        this.rightLabels[i].remove();
    }

    this.rightLabels = [];

    var y = 0;

    while (y <= max) {
        var vy = this.chartTop + this.chartHeight * (1 - y / max);
        this.rightLabels.push(this.r.text(this.chartWidth + 45, vy, '' + y).attr(attrs));
        this.rightLabels.push(this.hGridLine(vy));
        y += step;
    }
};

LoveChart = {
    cache:{},
    chart:null,
    messagesFill:null,
    messagesStroke:null,
    messagesDots:null,
    sendersFill:null,

    initialize:function (containerId, width, height, samples) {
        this.chart = new TimelineChart(containerId, width, height, samples);

        this.chart.framePointAttr = { fill:'#CE0808', stroke:'#FFF' };
        this.userType = 'sender';

        // This is used to determine the label for the chart 'Total Love Sent vs. Total Love Received'
        this.isCompany = false;
        this.forceWeekly = false;
        this.enableCache = false;
        
        this.strokeColor = '#000';
        this.dotColor = '#888'
        this.mainFill = '#C0C0C0';
        this.subFill = '#888'

        this.messagesVerticals = this.chart.addVerticalsSeries({ stroke:'#000', opacity:0.1 });
        this.messagesFill = this.chart.addFillSeries({ fill:this.mainFill, opacity:0.2 });
        this.messagesStroke = this.chart.addStrokeSeries({ stroke:this.strokeColor, 'stroke-width':2 });
        var dots = this.messagesDots = this.chart.addDotsSeries({ fill:this.dotColor, stroke:'#FFF' });

        this.sendersFill = this.chart.addFillSeries({ fill:this.subFill, opacity:0.2 });

        var to;
        $LoveChart = this;
        $(this.chart.r.canvas).mousemove( function (e) { 
            if (to) {
                clearTimeout(to);
                to = undefined;
            }

            var coords = dots.findNearestPoint(e.pageX - $('#' + containerId).offset().left);
            if (typeof coords !== 'undefined') {
                $LoveChart.chart.showSample(coords.x, coords.y);
            }
        });

        $(this.chart.r.canvas).mouseout(function (e) {
            if (to) {
                clearTimeout(to);
                to = undefined;
            }

            to = setTimeout(function () {
                $LoveChart.chart.hideSample();
            }, 500);
        });
    },


    load:function (data) {
        $LoveChart = this;
        var leftMax = $LoveChart.chart.getSeriesMax(data.messages,5);
        leftMax = $LoveChart.chart.getSeriesMax(data.senders,leftMax);
        var senders = [], messages = [];

        messages = $LoveChart.chart.normalizeSeries(data.messages, leftMax);
        senders = $LoveChart.chart.normalizeSeries(data.senders, leftMax);

        var labels = [];
        var fullLabels = data.labels.xFull;
        var month, year;
        var nonEmptyDateLabels = 0, nonEmptyWeekLabels = 0, nonEmptyMonthLabels = 0, nonEmptyYearLabels = 0;
        labels.push(data.labels.x1);
        labels.push(data.labels.x2);

        $LoveChart.chart.setBottomLabels({ fill:'#000000', font:'9px Arial, sans-serif' }, labels);
        $LoveChart.chart.setLeftLabels({ fill:'#000', font:'11px Arial, sans-serif', 'text-anchor':'end' }, leftMax, 100);

        var messagesFillData = [].concat(messages);
        messagesFillData[0] = messages[0] || 0;
        messagesFillData[messagesFillData.length - 1] = messages[messages.length - 1] || 0;

        var sendersFillData = [].concat(senders);
        sendersFillData[0] = senders[0] || 0;
        sendersFillData[sendersFillData.length - 1] = senders[senders.length - 1] || 0;

        var range = 'Day';

        var yAxisDots = [].concat(messages);
        yAxisDots = $LoveChart.chart.updateAxisMax(sendersFillData,yAxisDots);

        messages[0] = messages[0] || 0;
        messages[messages.length - 1] = messages[messages.length - 1] || 0;

        $LoveChart.messagesVerticals.setData(messages, true);
        $LoveChart.messagesFill.setData(messagesFillData, true);
        $LoveChart.messagesStroke.setData(messages, true);
        $LoveChart.sendersFill.setData(sendersFillData, true);
        $LoveChart.messagesDots.setData(messages, true);

        $LoveChart.chart.popupTextCallback = function (xs) {
	        var o = xs[0];
	
	        var selectedMessageValue = messages[o];
	        if(selectedMessageValue == undefined) {
	            selectedMessageValue = messages[o - 1] || 0;
	        }
	        var selectedSendersValue = senders[o];
	        if(selectedSendersValue == undefined) {
	            selectedSendersValue = senders[o - 1]  || 0;
	        }

	        var dateRangeText = fullLabels[o];

                // 10-AUG-2010 <steffan> Added distinction between Company / Personal for label purposes 
                var loveDirection = ($LoveChart.isCompany == true)?'sent: ':'received: '
	
	        return [
                { fill:'#CE0808', font:'bold 11px Arial, sans-serif', text:' Total love ' + loveDirection + Math.round(selectedMessageValue * leftMax) + ' ' },
				{ fill:'#888888', font:'bold 11px Arial, sans-serif', text:' Unique '+$LoveChart.userType + (Math.round(selectedSendersValue * leftMax) == 1 ? '' : 's') + ': '+ Math.round(selectedSendersValue * leftMax) + ' '  },
				{ fill:'#C0C0C0', font:'bold 11px Arial, sans-serif', text: dateRangeText }
	        ];
        };
    },
    
    fetchData:undefined,
    
    forceWeeklyLabels: function(forceWeekly) {
        this.forceWeekly = forceWeekly;
    },

    getData:function (from, to, username, callback) {
	  this.fetchData(from, to, username, function (messages, senders, labels) {
	      var result = {
	      from:from,
	      to:to,
	      messages:messages,
	      senders:senders,
	      labels:labels
	      };

	      callback(result);
	    });
	   
	},
    setIsCompany:function (isCompany) {
        this.isCompany = isCompany;
    }, 
 	/**
	 * Set the User type ( as Sender or Receiver )
	 */
	setUserType:function (type) {
	    this.userType = type;
	}
};
UserLoveChart = LoveChart;
CompanyLoveChart = LoveChart;

}());

function formatAsCurrency(amount)
{
    var delimiter = ","; // replace comma if desired
    var a = amount.split('.',2)
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
}


//  Copyright (c) 2009, LoveMachine Inc.  
//  All Rights Reserved.  
//  http://www.lovemachineinc.com

var _fromDate, _toDate;


var curDate = new Date();
var pastDate = new Date(curDate.getFullYear(), curDate.getMonth() - 2, curDate.getDate()); // Fixed for #12731 - change pastDate to a month earlier
$.lD = {};
$.lD.fromDate = (pastDate.getMonth()+1)+'/'+pastDate.getDate()+'/'+pastDate.getFullYear();
$.lD.toDate = (curDate.getMonth()+1)+'/'+curDate.getDate()+'/'+curDate.getFullYear();

var textColor = "#000";    // Color of the names on the chart circles
var canvasWidth = 900;     // (was 862) Width (in pixels) of the chart canvas - centerpoint will be calculated automatically.
var canvasHeight = 490;    // Height (in pixels) of the chart canvas - centerpoint will be calculated automatically.
var listWidth = '120px';   // Width (specify units) for list of people in the company
var listHeight = 24;       // Height (in number of option rows) for list of people in the company
var titleWidth = '120px';  // Width (specify units) of the div that contains the name of the company
var titleHeight = '1.5em'; // Height (specify units) of the div that contains the name of the company

var idLoveChart = 'holder';       // The id of the placeholder div in which the lovechart is to be shown
var centralNickname = '';         // The default is the current user

// List of LoveDot objects
var loveDots = [];
// Dot indices
var dotIndices = {};
var r ;

function fmtDate(d) {
    return '' + (d.getMonth()+1) + '/' + d.getDate() + '/' + d.getFullYear();
}

function fmtDate2(d) {
    return d.getFullYear() + '-' + String(d.getMonth() + 101).slice(-2) + '-' + String(d.getDate() + 101).slice(-2);
}


/**
Rotate point x,y with reference to the center point cx, cy at an angle <angle>
*/
$.rotate_point = function (x, y, cx, cy, angle) {
	var new_coords = {};
	var rad = (angle * Math.PI / 180);

	// Matrix rotation
	new_coords.x = Math.cos(rad) * (x - cx) - Math.sin(rad) * (y - cy) + cx;
	new_coords.y = Math.sin(rad) * (x - cx) + Math.cos(rad) * (y - cy) + cy;
	return new_coords;
};

Raphael.fn.loveDot = function(dx, dy, R, nickname, max, angle, hubFlag) {
    var color = Raphael.getColor();
    var rad = (angle * Math.PI / 180);

    rx = dx;
    ry = dy;

    var dt = this.circle(rx, ry, R).attr( {
        stroke : color,
        fill : color,
        opacity : 0.75
    });

    var lbl = this.text(rx, ry, nickname).attr( {
        "font" : '14px Helvetica, Arial',
        fill : textColor,
        'text-anchor' : 'middle'
    });
  
    // add box to assist with clicking in IE, which required you clicked on a pixel with text underneath it
    var blanket = this.rect().attr(lbl.getBBox()).attr({
        fill: "#000",
        opacity: 0,
        cursor: "pointer"
    }).click(function() { 
        $.userLovePopup(dt.nickname);
        return false;
    }).mouseover(function() {
        var clr = color;
        clr.b = .5;
        dt.attr("opacity", 0.5);
    }).mouseout(function() {
        dt.attr("opacity", 0.75);
    });

    dt.node.style.cursor = "pointer";
    lbl.node.style.cursor = "pointer";
    dt.dotLabel = lbl;
    dt.nickname = nickname;
    dt.rad = rad;
    dt.angle = angle;
    dt.isHub = hubFlag;

    lbl.node.onmouseover = function() {
        var clr = color;
        clr.b = .5;
        dt.attr("opacity", 0.5);
    };
    lbl.node.onmouseout = function() {
        dt.attr("opacity", 0.75);
    };
    dt.node.onmouseover = function() {
        var clr = color;
        clr.b = .5;
        dt.attr("opacity", 0.5);
    };
    dt.node.onmouseout = function() {
        dt.attr("opacity", 0.75);
    };

  lbl.click(function() {
      $.userLovePopup(dt.nickname);
      return false;
  });

  dt.node.onclick = function() {
      $.userLovePopup(dt.nickname);
      return false
  };
  return {
      dot : dt,
      label : lbl,
      angleFromCenter: angle 
  };
};

Raphael.fn.connection = function(obj1, obj2, line, bg, type) {
	if (obj1.line && obj1.from && obj1.to) {
		line = obj1;
		obj1 = line.from;
		obj2 = line.to;
		type = line.type;
	}
	var bb1 = obj1.getBBox();
	var bb2 = obj2.getBBox();
	var p = [ {
		x : bb1.x + bb1.width / 2,
		y : bb1.y - 1
	}, {
		x : bb1.x + bb1.width / 2,
		y : bb1.y + bb1.height + 1
	}, {
		x : bb1.x - 1,
		y : bb1.y + bb1.height / 2
	}, {
		x : bb1.x + bb1.width + 1,
		y : bb1.y + bb1.height / 2
	}, {
		x : bb2.x + bb2.width / 2,
		y : bb2.y - 1
	}, {
		x : bb2.x + bb2.width / 2,
		y : bb2.y + bb2.height + 1
	}, {
		x : bb2.x - 1,
		y : bb2.y + bb2.height / 2
	}, {
		x : bb2.x + bb2.width + 1,
		y : bb2.y + bb2.height / 2
	} ];
	var d = {}, dis = [];
	for ( var i = 0; i < 4; i++) {
		for ( var j = 4; j < 8; j++) {
			var dx = Math.abs(p[i].x - p[j].x), dy = Math.abs(p[i].y - p[j].y);
			if ((i == j - 4)
					|| (((i != 3 && j != 6) || p[i].x < p[j].x)
							&& ((i != 2 && j != 7) || p[i].x > p[j].x)
							&& ((i != 0 && j != 5) || p[i].y > p[j].y) && ((i != 1 && j != 4) || p[i].y < p[j].y))) {
				dis.push(dx + dy);
				d[dis[dis.length - 1]] = [ i, j ];
			}
		}
	}
	if (dis.length == 0) {
		var res = [ 0, 4 ];
	} else {
		var res = d[Math.min.apply(Math, dis)];
	}
	var x1 = p[res[0]].x, y1 = p[res[0]].y, x4 = p[res[1]].x, y4 = p[res[1]].y, dx = Math
			.max(Math.abs(x1 - x4) / 2, 10), dy = Math.max(
			Math.abs(y1 - y4) / 2, 10), x2 = [ x1, x1, x1 - dx, x1 + dx ][res[0]]
			.toFixed(3), y2 = [ y1 - dy, y1 + dy, y1, y1 ][res[0]].toFixed(3), x3 = [
			0, 0, 0, 0, x4, x4, x4 - dx, x4 + dx ][res[1]].toFixed(3), y3 = [
			0, 0, 0, 0, y1 + dy, y1 - dy, y4, y4 ][res[1]].toFixed(3);

    if (isNaN(x1)) return;
	if (type == "line") {
		var fromObj, toObj;
		if(obj1.isHub) {
			fromObj = obj1;
			toObj = obj2;
		}
		else 
		{
			fromObj = obj2;
			toObj = obj1;
		}
		var cx1 = fromObj.attr("cx"), cy1 = fromObj.attr("cy");
		var cx2 = toObj.attr("cx"), cy2 = toObj.attr("cy");
		if(toObj.angle >  1) {
		}

		var rotatedPoint1 = { x : cx1 , y : cy1 };
		var rotatedPoint2 = { x : cx2 , y : cy2 };
		var path = [ "M", rotatedPoint1.x, rotatedPoint1.y , "L", rotatedPoint2.x,
				rotatedPoint2.y ].join(",");
	} else if (type == "curve") {
		var path = [ "M", x1.toFixed(3), y1.toFixed(3), "C", x2, y2, x3, y3,
				x4.toFixed(3), y4.toFixed(3) ].join(",");
	}
	if (line && line.line) {
		line.bg && line.bg.attr( {
			path : path
		});
		line.line.attr( {
			path : path
		});
		line.bg.toBack();
		line.line.toBack();
	} else {
		var color = typeof line == "string" ? line : "#000";
		rbg = bg && bg.split && this.path(path).attr( {
			stroke : bg.split("|")[0],
			fill : "none",
			opacity : bg.split("|")[2],
			"stroke-width" : bg.split("|")[1] || 3
		});
		rline = this.path(path).attr( {
			stroke : color,
			opacity : bg.split("|")[2],
			fill : "none"
		});

		rbg.node.onclick = function() {
			$.userLoveExchangePopup(obj1.nickname, obj2.nickname);
            return false
		};

		rline.node.onclick = function() {
			$.userLoveExchangePopup(obj1.nickname, obj2.nickname);
            return false
		};
		rbg.toBack();
		rline.toBack();
		rbg.node.style.cursor = "pointer";
		rline.node.style.cursor = "pointer";

		return {
			bg : rbg,
			line : rline,
			from : obj1,
			to : obj2
		};
	}
};

$.userLovePopup = function(nickname) {
	var dialogTitle = "Love for " + nickname;
	var url = "helper/user_popup.php?type=userLove&u=" + escape(nickname) + "&from_date="+ $.lD.fromDate + "&to_date=" + $.lD.toDate + "&page=1";
	showDialog('user-love-popup', dialogTitle, url, 610);
};

function showDialog(dialogId, dialogTitle, dialogUrl, dialogWidth, reuseDialog)
{
    var dialog = $('#'+dialogId);
    if(!reuseDialog)
    {
    	dialog.dialog('close');
    }
	var navigationHandler = function(e) {
        e.preventDefault();
        showDialog(dialogId, dialogTitle, $(this).attr("href"), dialogWidth, true );
    };
    
    dialog.load(
    		dialogUrl, 
            {},
            function (responseText, textStatus, XMLHttpRequest) {
                dialog.dialog({modal : true, width: dialogWidth, autoOpen: true, resizable: false });
                dialog.dialog('option', 'title', dialogTitle);
                dialog.dialog('option', 'width', dialogWidth);
            	dialog.dialog('open');
            	dialog.dialog('enable');
            	dialog.dialog('moveToTop');
            	$(".page-number").click(navigationHandler);
            }
    );
	
}
$.userLoveExchangePopup = function(nickname1, nickname2) {
	var dialogTitle = "Love exchanged between " + nickname1 + " and " + nickname2;
	var url = "helper/user_popup.php?type=userLoveExchange&u1=" + escape(nickname1) + "&u2="
			+ escape(nickname2) + "&from_date=" + $.lD.fromDate + "&to_date=" + $.lD.toDate
			+ "&page=1";
	showDialog('user-love-popup', dialogTitle, url, 610);
};

//
// showLoveChart() displays a new chart in the div with id=idLoveChart
// - The central nickname is according to global centralNickname, or the current user if that is an empty string
// - The date range is according to global variables dateFrom and dateTo or all time if they are empty strings
//
function showLoveChart(centerNickname)
{
    $.createDots = function(loveCount, currentUserData, totalLoveCount) {

        // Remove previous canvas if one exists
        var h = document.getElementById(idLoveChart);
        if (h.childNodes.length > 0)
            h.removeChild(h.firstChild);

        // Draw
        r = Raphael(idLoveChart, canvasWidth, canvasHeight);
        var leftgutter = 0, bottomgutter = 0;
        var X = (canvasWidth - leftgutter) / loveCount.length;
        var Y = (canvasHeight - bottomgutter)/ loveCount.length;
        var centerX = canvasWidth / 2, centerY = canvasHeight / 2;

        max = Math.round(X / 2) - 1;
        centerMax = Math.round(centerX / 2) - 1;
        var radiusRatio = currentUserData[1] / totalLoveCount * 100;
        var userRadius = 20;
        var currentUserNick = currentUserData[0];

        // Need to reset the color sequence to get consistent colors when redrawing the chart
        Raphael.getColor.reset();

        // Create the User's Dot. It's in the center of the canvas
        currentUserLoveDot = r.loveDot(centerX, centerY, userRadius,
                //currentUserNick, centerMax, -1, true);
            $.user.nickname, centerMax, -1, true);  

        loveDots.push(currentUserLoveDot);
        dotIndices[currentUserData[0]] = loveDots.length - 1;
        
        var dotsPerLayer = loveCount.length;
        var angle = 0, angleDelta = 360 / Math.min(dotsPerLayer,loveCount.length);
        var dx = centerX;
        var dy = (2 * centerY) - 30;
        var layerCount = Math.floor(loveCount.length / dotsPerLayer), currentLayer = 1;

        // Now create all other dots
        for ( var i = 0, ii = loveCount.length; i < ii; i++) {
            radiusRatio = loveCount[i][1] / totalLoveCount * 100;
            var layerIndex = Math.max(1,Math.floor(i / dotsPerLayer));
            var R = loveCount[i][1]
                    && Math.min(Math.round(Math.sqrt(radiusRatio / Math.PI) * 8), 20);
            R = Math.max(R, 8);
            if (R) {
                if(layerIndex > currentLayer) {
                    currenLayer = layerIndex;
                    if(currentLayer > 1) {
                      var dx = 320;
                      var dy = 330;
                      var layerStartCoordinates = $.rotate_point(dx,dy,centerX,centerY,5*layerIndex);
                      dx = layerStartCoordinates.x;
                      dy = layerStartCoordinates.y;
                  }
                }
                if(loveCount.length > dotsPerLayer) {
                    dy = 250 +Math.min((layerIndex * (150 / layerCount)),120);
                }
                var rotatedCoordinates = $.rotate_point(dx,dy,centerX,centerY,angle);
                var userLoveDot = r.loveDot(rotatedCoordinates.x, rotatedCoordinates.y, R, loveCount[i][0], max,
                        angle, false);


                angle = angle + angleDelta;

                loveDots.push(userLoveDot);
                dotIndices[loveCount[i][0]] = loveDots.length - 1;
            }
        }

    }
    
	$.connectDots = function(currentUserNick, loveExchangeCount,
			totalLoveCount) {
		// It's time to connect the dots
		var connections = [];
		for (userKey in loveExchangeCount) {
			var fromUser = userKey.split("|")[0];
			var toUser = userKey.split("|")[1];
			var userLoveExchangeCount = loveExchangeCount[userKey];
			var fromUserDot = loveDots[dotIndices[fromUser]];
			var toUserDot = loveDots[dotIndices[toUser]];
			var thickness = (userLoveExchangeCount / totalLoveCount) * 80;
			thickness = Math.min(thickness, 10);
			thickness = Math.max(thickness, 1);
			var lineType = "curve";
			var lineColor = "#A9A5B6";
			var bgColor = "#A9A5B6";
			var opacity = 0.5;
			if (toUser == currentUserNick || fromUser == currentUserNick) {
				lineType = "line";
				opacity = 0.7;
				bgColor = "#B61D22";
				lineColor = "#B61D22";
			}
			if(fromUserDot == null || toUserDot == null) {
			  // This is likely to be a data issue. Skip connection.			  
			  continue;				   
			}
			var connector = r.connection(fromUserDot.dot, toUserDot.dot,
					lineColor, bgColor+"|" + thickness+"|"+opacity, lineType);
			connections.push(connector);
		}
	}

    if (!jCache.isValid('userLoveCount')) {
        $.getJSON("lovechart-data.php?type=userLoveCount&centerNickname="+ centerNickname + "&from_date=" + $.lD.fromDate + "&to_date=" + $.lD.toDate, function(data) {
            // Store the data on cache
            jCache.set('userLoveCount', data, $.cacheExpTime);
            
            var currentUser = centerNickname ? centerNickname : data.currentUser;
            var loveCount = [];
            var loveExchangeCount = [];
    
            var currentUserData = false;
            
            $.each(data.userLoveCount, function(i, userLoveData) {
                var loveData = [ userLoveData.nickname, userLoveData.loveCount ];
                if (currentUser != userLoveData.nickname) {
                    loveCount.push(loveData);
                } else {
                    currentUserData = loveData;
                }
            });
            if(currentUserData)
            {
                var currentuserData = [ currentUser, 0];
            }
    
            if (idLoveChart == '') return;
    
            // I don't think the following is necessary
            //$('#' + idLoveChart).empty();
            $.createDots(loveCount, currentUserData, data.totalLoveCount);
    
            if (!jCache.isValid('userLoveExchange')) {
                // Dots are created. Now get connection data, and connect the dots.
                $.getJSON("lovechart-data.php?type=userLoveExchange&centerNickname="+ currentUser + "&from_date=" + $.lD.fromDate + "&to_date=" + $.lD.toDate, function(lxData) {
                    // Store the data on cache
                    jCache.set('userLoveExchange', lxData, $.cacheExpTime);
                    
                    if (lxData.userLoveExchangeData)
                        $.connectDots(currentUserData[0], lxData.userLoveExchangeData, lxData.totalLoveCount);
                });
            } else {
                var lxData = jCache.get('userLoveExchange');
                
                if (lxData.userLoveExchangeData)
                        $.connectDots(currentUserData[0], lxData.userLoveExchangeData, lxData.totalLoveCount);
            }
        });
    } else {
        var data = jCache.get('userLoveCount');
        
        var currentUser = centerNickname ? centerNickname : data.currentUser;
        var loveCount = [];
        var loveExchangeCount = [];

        var currentUserData = false;
        
        $.each(data.userLoveCount, function(i, userLoveData) {
            var loveData = [ userLoveData.nickname, userLoveData.loveCount ];
            if (currentUser != userLoveData.nickname) {
                loveCount.push(loveData);
            } else {
                currentUserData = loveData;
            }
        });
        if(currentUserData)
        {
            var currentuserData = [ currentUser, 0];
        }

        if (idLoveChart == '') return;

        // I don't think the following is necessary
        //$('#' + idLoveChart).empty();
        $.createDots(loveCount, currentUserData, data.totalLoveCount);

        if (!jCache.isValid('userLoveExchange')) {
            // Dots are created. Now get connection data, and connect the dots.
            $.getJSON("lovechart-data.php?type=userLoveExchange&centerNickname="+ currentUser + "&from_date=" + $.lD.fromDate + "&to_date=" + $.lD.toDate, function(lxData) {
                // Store the data on cache
                jCache.set('userLoveExchange', lxData, $.cacheExpTime);
                
                if (lxData.userLoveExchangeData)
                    $.connectDots(currentUserData[0], lxData.userLoveExchangeData, lxData.totalLoveCount);
            });
        } else {
            var lxData = jCache.get('userLoveExchange');
            
            if (lxData.userLoveExchangeData)
                    $.connectDots(currentUserData[0], lxData.userLoveExchangeData, lxData.totalLoveCount);
        }
    }
}
