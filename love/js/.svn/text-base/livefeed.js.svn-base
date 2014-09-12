// extract the delay of rotation
function getDelayFromJson(json) {
    var rotationDelay = 5000;
    if (json.rotationDelay) {
        rotationDelay = json.rotationDelay;
        json = json.result;
    }
    if (rotationDelay < 5000) {
        rotationDelay = 5000;
    }
    return {
        rotationDelay : rotationDelay,
        json : json
    };
}
//Slide new messages on top of our list
var updateCompanyLove = function()	{
	// Retrieve lastest messages from the DB
	$.ajax({
		type: 'GET',
		url: 'stage.back.php',
		data: {no: msg_amount},
		dataType: 'json',
		success: function(json)	{
            var oJson = getDelayFromJson(json), 
                rotationDelay = oJson.rotationDelay;
            json = oJson.json;
		    var tab = $('.'+$.currentTab + ' .companyTab');
	        tab.data('json', json);
	        clearInterval($.timers['crtimer']);
			$.timers['crtimer'] = setInterval(function() {
	            // Rotate the love message every 1.5ms
                if ( $.currentLowerTab != '.userTab' ) {
                    rotateMessage('.love .companyTab');
                }
	            },
	            rotationDelay
			);
		}, error: function( xhdr, status, err )	{ }
	});
}
var updateUserLove = function() {
	$.ajax({
		type: 'GET',
		url: 'stage.back.php',
		data: {no: msg_amount, userdata: true},
		dataType: 'json',
		success: function(json)	{
            var oJson = getDelayFromJson(json), 
                rotationDelay = oJson.rotationDelay;
            json = oJson.json;
		    var tab = $('.love .userTab');
	        tab.data('json', json);
	        clearInterval($.timers['urtimer']);
			$.timers['urtimer'] = setInterval(function() {
	            // Rotate the love message every 1.5ms
                if ( $.currentLowerTab == '.userTab' ) {
                    rotateMessage('.love .userTab');
                }
	            },
	            rotationDelay
			);
		}, error: function( xhdr, status, err )	{ }
	});
}

$.updateLove = function() {
	if (!live_feed) {
        if ($.timers['urtimer'] === undefined || $.timers['urtimer'] === null) {
        // probably first call so we get both lists
            updateUserLove();
            updateCompanyLove();
        } else {     
        // next calls we  can load only the visible one
            if ( $.currentLowerTab == '.userTab' ) {
                updateUserLove();
            } else {
                updateCompanyLove();
            }
        }
	} else {
		updateLoveLiveFeed();
	}
}

// Slide new messages on top of our list
function updateLoveLiveFeed()	{
	// Retrieve lastest messages from the DB
	$.ajax({
		type: 'POST',
		url: 'stage.back.php',
		data: 'no=' + msg_amount,
		dataType: 'json',
		success: function(json)	{
            var oJson = getDelayFromJson(json), 
                rotationDelay = oJson.rotationDelay;
            json = oJson.json;
            clearInterval(rTimer);
			rTimer = setInterval(function() {
                // Rotate the love message every 1.5ms
                rotateMessage(json);
                },
                rotationDelay
			);
		}, error: function( xhdr, status, err )	{ }
	});
}
//This function handles the animation and replacement of
//the love message displayed.
//json: Must contain all the entries to rotate from
function rotateMessage(which) {
	// Get next message
	if (!live_feed) {
		which = $(which);
		var next = fetchNext(which.data('json'));
		
		if(!next) return;
		
		// Check if the Only Feed view flag is set
		// If not
		if (!fView) {
		    // Fade out current item
		    $('.feed-div', which).fadeOut("slow", function() {
		        $('.love', which).remove();
		        // Fade in new element
		        $('.feed-div', which).append(next).fadeIn("slow");
		        adjustFonts();
		    });
		} else { // If the flag is set 
		    // If we have more than 15 items, we reset the list
		    // else we add the next item.
		    if ($('.feed-div', which).children().length < 25) {
		        // We add the next item under the current.
		        $(next).hide()
		               .appendTo('.feed-div', which)
		               .fadeIn("slow");
		        adjustFonts();
		    } else {
		        $('.love', which).fadeOut("slow");
		        $('.feed-div', which).empty();
		        rCounter = 0;
		        clearInterval(rTimer);
		        $.updateLove();
		    }
		}
	} else {
		var next = fetchNext(which);
		
		if(!next) return;
		
		// Check if the Only Feed view flag is set
		// If not
		if (!fView) {
		    // Fade out current item
		    $('.feed-div').fadeOut("slow", function() {
		        $('.love').remove();
		        // Fade in new element
		        $('.feed-div').append(next).fadeIn("slow");
		        adjustFonts();
		    });
		} else { // If the flag is set 
		    // If we have more than 15 items, we reset the list
		    // else we add the next item.
		    if ($('.feed-div').children().length < 25) {
		        // We add the next item under the current.
		        $(next).hide()
		               .appendTo('.feed-div', which)
		               .fadeIn("slow");
		        adjustFonts();
		    } else {
		        $('.love').fadeOut('slow', function() {
		            $('.feed-div').empty();
		        });
		        rCounter = 0;
		        clearInterval(rTimer);
		        $.updateLove();
		    }
		}
	}
}

//Get a new message from the json
function fetchNext(json) {
    // When we reach the end of the list update love and
    // start over again.
    if (!json || json === null) return false;
    if (rCounter >= json.length) {
        rCounter = 0;
        clearInterval(rTimer);
        $.updateLove();
        return;
    }
    if (! json[rCounter] ) return false;
    // Compose message
    var love = composeLove(json[rCounter]);
    rCounter++;
    return love;
}

function composeLove(json) {
	var date_div = '<span class="date">' + relativeTime( json[3] ) + '</span>';
	var love_div = '<div class="love">';
	var msg = [love_div, '<h5>', json[0], ' <img style="margin-bottom:5px;" src="images/arrow1.png" alt="arrow"/> ',
			  json[1], ': <span class="msg">', json[2], '</span>',
			  date_div, '</h5></div>'].join('');
	return msg;
}

//When the layout has changed and only the feed is
//visible, instead of rotate we add the items under
//each other.
function setJustFeedView(flag) {
	fView = flag;
	if (fView) {
	    // If true set the location to the top
	    $.feedWrapper.css('top', '0px');
	    $.feedWrapper.height(y +'px');
	    rCount = 0;
	} else {
	    // If not restore the orginal location at the bottom
	    // of the page
	    var y = $(window).height();
	    $.feedWrapper.css('top', (y -85) + 'px');
	    // Clean contents
	    $('.feed-div').empty();
	}
	$.updateLove();
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

function adjustFonts() {
	var y = $(window).height();
	var win_w = $(window).width();
	if (y > 1600 && win_w > 2000) {
	    // Change font sizes to bigger
	    if (!fView) {
	        $.feedWrapper.css('top', (y -110) + 'px');
	    }
	    $('h5').css('font-size', '72px');
	    $('#msg').css('font-size', '70px');
	} else if (y > 1300 && win_w > 1600) {
	    if (!fView) {
	        $.feedWrapper.css('top', (y -95) + 'px');
	    }
	    $('h5').css('font-size', '52px');
	    $('#msg').css('font-size', '50px');
	} else if (win_w < 1300) {
	    if (!fView) {
	        $.feedWrapper.css('top', (y -75) + 'px');
	    }
	    $('h5').css('font-size', '36px');
	    $('#msg').css('font-size', '34px');
	}
}

$.cloudInit = function() {
    var classes = ['one', 'two', 'three', 'four'],
        cloudIsVisible = function(cloud) {
    		if (!live_feed) {
                if ($("#redeemTabContent").length == 1) {
                // We are using jquery-ui tabs to display Love/Redeem tabs
                    if ( $("#loveTabContent.ui-tabs-hide").length == 1 ) {
                        // we cannot load the cloud now, the display won't be nice because the container is hidden
                        // it will be loaded later when required
                        cloud.data("loaded",false);
                        return false;
                    } else {
                        cloud.data("loaded",true);
                    }
                }else {
                // We are not in Love/Redeem application
                    if ($.currentTab != "love" || $.currentLowerTab == '.'+cloud[0].id) {
                        // we cannot load the cloud now, the display won't be nice because the container is hidden
                        // it will be loaded later when required
                        cloud.data("loaded",false);
                        return false;
                    } else {
                        cloud.data("loaded",true);
                    }
                }
    		}
            return true;
        };
    $.cloud = $('.tags');
    // opacity process in cloud tags is not well supported by IE (the cloud is not visible)
    // ticket #11598, the opacity is removed with IE 
    if (!$.browser.msie) {
        $.cloud.animate({opacity:0},0);
    } else {
        $.cloud.animate({marginLeft: '-4321px'},0);
    }
    if (!live_feed) {
        $.cloud.total = 200;
    } else {
        if ($(window).height() > 1000) {
            $.cloud.total = 400;
        } else {
            $.cloud.total = 300;
        }
    }
    $.cloud.intervalTime = 5 * 60 * 1000; // update every 5 minutes
    $.cloud.load = function(id,username) {
        var info,cloud = $('#'+id);
        var cloudCacheName = 'cloud';
        
        if ( cloudIsVisible(cloud) === false ) return;
        if(id == 'companyTags') {
            var info = {company_id: $.user.company_id, date: +new Date};
            cloudCacheName = cloudCacheName + '-co'
        } else {
            cloudCacheName = cloudCacheName + '-user'
            if (username && username != "") {
                var info = {company_id: $.user.company_id, userdata: true, date: +new Date, username: username};
            } else {
                var info = {company_id: $.user.company_id, userdata: true, date: +new Date};
            }
        }
        
        
        if (!jCache.isValid(cloudCacheName)) {
            $.getJSON('cloud.gen.php', info, function(data) {
                // Cache the cloud
                jCache.set(cloudCacheName, data, $.cacheExpTime);
                
                if ( !data || !data.tags ) return;
                // if the ajax call is long, the user may have change of tabs
                if ( cloudIsVisible(cloud) === false ) return;
                cloud.find('.tag').remove();
                if (!$.browser.msie) {
                    cloud.animate({opacity:0},0);
                } else {
                    cloud.css("width","100%");
                    cloud.animate({marginLeft: '-4321px'},0);
                }
                cloud.i = 0;
                var number=0;
                var frequency=[],aValues=[],frequencySize=[],maxFrequency;
                $.each(data.tags, function(index, value) {
                    if ( !maxFrequency ) maxFrequency=value;
                    number++;
                    cloud.i += 1+parseInt(9*value/maxFrequency) ;
                    if (!frequency[value]) {
                        frequency[value]=1;
                        aValues[aValues.length]=value;
                    }
                    if (cloud.i >= $.cloud.total) { 
                        delete data.tags[index];
                        return;
                    }
                });
                if (number < 10) {
                    cloud.html("<div class='msg tag'>Not enough words to display your personal word cloud.</div>");
                } else {
                    if (!live_feed) {
                        var maxSize=8,minSize=1;
                    } else {
                        var maxSize=10,minSize=2;
                    }
                    if (aValues.length < 6 && number<50) {
                        maxSize = 4;
                        minSize=2;
                        cloud.parents(".cloudWrapper").width("99%").css("margin-left","").css("margin-right","");
                    } else {
                        cloud.parents(".cloudWrapper").width("99%").css("margin-left","").css("margin-right","");
                    }
        
                    for (var k=0; k < aValues.length; k++) {
                        // using a max size of ten, let's work out what size to make things, where 1 is min              
                        frequencySize[aValues[k]]= (((aValues.length - 1 - k) / (aValues.length - 1)) * maxSize) + minSize;
                    }
                   $.each(data.tags, function(index, value) {
                        // as we know these are sorted in reverse order the first time through will give the max value
                        index = index.replace(/%u([a-fA-F0-9]{4})/g, '&#x$1;');
                        index = $.trim(index);
                        var tagLink = '"helper/gettorecords.php?tag=' + encodeURIComponent(index);
                        if ( username && username != "") {
                            tagLink += "&username=" + username;
                        }
                        tagLink += '"';
                        var item = $('<div class="tag">').html('<a class="cloudlink" href=' + tagLink + '>'+index+'</a>')
                                .attr('data', value)
                                .css({fontSize: frequencySize[value]+ "em"})
                                .addClass(classes[Math.floor(Math.random()*classes.length)]); 
                        cloud.append(item);        
                    });
                    cloud.reorder().masonry({columnWidth: 5,itemSelector: '.tag', resizeable: false}).removeData('masonry');
                    $('.cloudlink').click( function(e) {
                        e.preventDefault();
                        showDialog('user-love-popup', 'Tag Love', this.href, 610);
                    });
                }
                if (!$.browser.msie) {
                    cloud.animate({opacity:1},0);
                } else {
                    cloud.animate({marginLeft: '0px'},0);
                }
                $.resizeLower();
            });
        } else {
            var data = jCache.get(cloudCacheName);
            
            if ( !data || !data.tags ) return;
            // if the ajax call is long, the user may have change of tabs
            if ( cloudIsVisible(cloud) === false ) return;
            cloud.find('.tag').remove();
            if (!$.browser.msie) {
                cloud.animate({opacity:0},0);
            } else {
                cloud.css("width","100%");
                cloud.animate({marginLeft: '-4321px'},0);
            }
            cloud.i = 0;
            var number=0;
            var frequency=[],aValues=[],frequencySize=[],maxFrequency;
            $.each(data.tags, function(index, value) {
                if ( !maxFrequency ) maxFrequency=value;
                number++;
                cloud.i += 1+parseInt(9*value/maxFrequency) ;
                if (!frequency[value]) {
                    frequency[value]=1;
                    aValues[aValues.length]=value;
                }
                if (cloud.i >= $.cloud.total) { 
                    delete data.tags[index];
                    return;
                }
            });
            if (number < 10) {
                cloud.html("<div class='msg tag'>Not enough words to display your personal word cloud.</div>");
            } else {
                if (!live_feed) {
                    var maxSize=8,minSize=1;
                } else {
                    var maxSize=10,minSize=2;
                }
                if (aValues.length < 6 && number<50) {
                    maxSize = 4;
                    minSize=2;
                    cloud.parents(".cloudWrapper").width("99%").css("margin-left","").css("margin-right","");
                } else {
                    cloud.parents(".cloudWrapper").width("99%").css("margin-left","").css("margin-right","");
                }
    
                for (var k=0; k < aValues.length; k++) {
                    // using a max size of ten, let's work out what size to make things, where 1 is min              
                    frequencySize[aValues[k]]= (((aValues.length - 1 - k) / (aValues.length - 1)) * maxSize) + minSize;
                }
               $.each(data.tags, function(index, value) {
                    // as we know these are sorted in reverse order the first time through will give the max value
                    index = index.replace(/%u([a-fA-F0-9]{4})/g, '&#x$1;');
                    index = $.trim(index);
                    var tagLink = '"helper/gettorecords.php?tag=' + encodeURIComponent(index);
                    if ( username && username != "") {
                        tagLink += "&username=" + username;
                    }
                    tagLink += '"';
                    var item = $('<div class="tag">').html('<a class="cloudlink" href=' + tagLink + '>'+index+'</a>')
                            .attr('data', value)
                            .css({fontSize: frequencySize[value]+ "em"})
                            .addClass(classes[Math.floor(Math.random()*classes.length)]); 
                    cloud.append(item);        
                });
                cloud.reorder().masonry({columnWidth: 5,itemSelector: '.tag', resizeable: false}).removeData('masonry');
                $('.cloudlink').click( function(e) {
                    e.preventDefault();
                    showDialog('user-love-popup', 'Tag Love', this.href, 610);
                });
            }
            if (!$.browser.msie) {
                cloud.animate({opacity:1},0);
            } else {
                cloud.animate({marginLeft: '0px'},0);
            }
            $.resizeLower();
        }
    }
};

$.resizeLower = function() {
    if ($.rst) clearTimeout($.rst);
    $.rst = setTimeout(function() {
        $('#lowerInfoHolder').animate({height: $('.'+$.currentTab + ' ' + $.currentLowerTab + ' .subCurrent').outerHeight()});  
    }, 350);
}

/**
 * Randomly reorder child elements.
 * The optional *callback* is called with each child and its new (deep) clone, allowing you to
 * copy data over or anything else you may require.
 *
 * Example usage:
 * controls.songs.reorder(function(child, clone) {
 * clone.data('info', child.data('info'));
 * var id = child.data('info').id;
 * if (self.current != undefined && self.current.data('info').id == id) self.current = clone;
 * if (self.next != undefined && self.next.data('info').id == id) self.next = clone;
 * });
 *
 * @see http://blog.rebeccamurphey.com/2007/12/11/jquery-plugin-randomly-reorder-children-elements/
 */
$.fn.reorder = function(callback) {
    // random array sort @see http://javascript.about.com/library/blsort2.htm
    function randOrd() { return(Math.round(Math.random())-0.5); }
 
    return($(this).each(function() {
    	var $this = $(this);
    	var $children = $this.children();
    	var childCount = $children.length;

    	if (childCount > 1) {
    		$children.hide();
 
    		var indices = new Array();
    		for (i=0;i<childCount;i++) {
    			indices[indices.length] = i;
    		}
			indices = indices.sort(randOrd);
			$.each(indices,function(j,k) {
				var $child = $children.eq(k);
				var $clone = $child.clone(true);
				$clone.show().appendTo($this);
				if (callback != undefined) {
					callback($child, $clone);
				}
				$child.remove();
			});
    	}
    }));
}
