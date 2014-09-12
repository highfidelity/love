//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

// Main global thing, factory function:
var DatePicker;

(function () {

var monthNames = {
    1:'JAN',
    2:'FEB',
    3:'MAR',
    4:'APR',
    5:'MAY',
    6:'JUN',
    7:'JUL',
    8:'AUG',
    9:'SEP',
    10:'OCT',
    11:'NOV',
    12:'DEC'
};

// This object controlls all kinds of flexible selection/deselection.
function Controller(y1, m1, y2, m2, selectedClass) {
    // First item:
    this.y1 = y1;
    this.m1 = m1;
    // Last item:
    this.y2 = y2;
    this.m2 = m2;
    // CSS class for items that are selected:
    this.selectedClass = selectedClass;
    // Cached map of items to save jQuery selects:
    this.items = {};
    this.selectionNotifyCallback = undefined;
}

// Executes callback for each item, passing in year and month.
Controller.prototype.forEachMonth = function (callback) {
    var y = this.y1, m = this.m1, ly;

    while (y < this.y2 || y == this.y2 && m <= this.m2) {
        var result = callback.call(this, y, m);

        if (typeof result !== 'undefined') {
            return result;
        }

        if (m == 12) {
            y++;
            m = 1;
        } else {
            m++;
        }
    }
};

// Remembers the item in the inner cache:
Controller.prototype.setItem = function (y, m, ref) {
    this.items[parseInt(y, 10) + '-' + parseInt(m, 10)] = ref;
};

// Returns the item from the inner cache:
Controller.prototype.getItem = function (y, m) {
    return this.items[parseInt(y, 10) + '-' + parseInt(m, 10)];
};

// Marks the item as selected:
Controller.prototype.select = function (y, m) {
    this.getItem(y, m).addClass(this.selectedClass);
};

// Marks the item as unselected:
Controller.prototype.deselect = function (y, m) {
    this.getItem(y, m).removeClass(this.selectedClass);
};

// Checks if the item is selected:
Controller.prototype.isSelected = function (y, m) {
    return this.getItem(y, m).hasClass(this.selectedClass);
};

// Toggles the item, i.e. switches it between selected/unselected states:
Controller.prototype.toggleItem = function (y, m) {
    if (this.isSelected(y, m)) {
        this.deselect(y, m);
    } else {
        this.select(y, m);
    }
};

// Changes state of all items according to predicate function
// (if predicate returns true for particular item, it will be
// selected, otherwise it will be unselected):
Controller.prototype.setSelection = function (predicate) {
    this.forEachMonth(function (y, m) {
        if (predicate.call(this, y, m)) {
            this.select(y, m);
        } else {
            this.deselect(y, m);
        }
    });
    if(this.selectionNotifyCallback != 'undefined') {
	this.selectionNotifyCallback();
    }
};

// Similar to setSelection, but only add selection, i.e. does not
// deselect any item, even if predicate returns false:
Controller.prototype.addToSelection = function (predicate) {
    this.forEachMonth(function (y, m) {
        if (predicate.call(this, y, m)) {
            this.select(y, m);
        }
    });
};
Controller.prototype.setSelectionNotifyCallback = function (callback) {
  this.selectionNotifyCallback = callback;
}
// Main factory function:
//     parent - container where selector should be put in
//     y1, m1 - first date
//     y2, m2 - last date
//     initialPredicate - predicate that will be passed to setSelection after initialization
//     callback - a function that will receive two Date object when range is changed
//     options - a bunch of options (see DefaultDatePicker below)
DatePicker = function (parent, y1, m1, y2, m2, initialPredicate, callback, options) {
    // Adding internal container:
    var container = $('<div></div>').attr('class', options.containerClass);

    // Instantinating the controller:
    this.controller= new Controller(y1, m1, y2, m2, options.selectedClass);

    // Function that actually fires the callback with proper arguments:
    function notify() {
        if (callback) {
            var months = [];

            controller.forEachMonth(function (y, m) {
                if (controller.isSelected(y, m)) {
                    months.push({ year:y, month:m });
                }
            });

            if (months.length == 0) {
                // Nothing selected...
                callback(undefined, undefined, undefined);
            } else if (months.length == 1) {
                // Single month selected...
                var d1 = new Date(months[0].year, months[0].month - 1, 1);
                var d2 = new Date(months[0].year, months[0].month, 0);
                callback(
                    d1,
                    d2,
                    monthNames[months[0].month] + ' ' + months[0].year
                );
            } else {
                // Several months selected...
                var d1 = new Date(months[0].year, months[0].month - 1, 1);
                var d2 = new Date(months[months.length - 1].year, months[months.length - 1].month, 0);
                callback(
                    d1,
                    d2,
                    months[0].year == months[months.length - 1].year
                    ?  monthNames[months[0].month] + ' - ' + monthNames[months[months.length - 1].month] + ' ' + months[0].year
                    :  monthNames[months[0].month] + ' ' + months[0].year + ' - ' + monthNames[months[months.length - 1].month] + ' ' + months[months.length - 1].year
                );
            }
        }

    }
    controller.setSelectionNotifyCallback(notify);

    // Some more hacking for selection prevention
    if ($.browser.mozilla) {
        $(container).css('MozUserSelect', 'none');
    } else if ($.browser.msie) {
        $(container).bind('selectstart', function () { return false; });
    }

    var tmp;

    // Adding quick links:
    tmp = $('<tr></tr>');

    for (var i = 0; i < options.quickLinks.length; i++) {
        (function (link) {
            tmp.append(
                $('<td></td>').html(link.caption).click(function () {
                    controller.setSelection(link.predicate);
//                     notify();
                })
            );
        }(options.quickLinks[i]));
    }

    $(container).append($('<table></table>').attr('class', options.quickLinksClass).append(tmp));

    // Creating the items:
    tmp = $('<tr></tr>');

    var ly;

    controller.forEachMonth(function (y, m) {
        var item = $('<td></td>').
            data('date', { year:y, month:m }).
            append($('<span class="month">' + monthNames[m] + '</span>'));

        // Adding the year number once for every year:
        if (ly !== y) {
            item.append('<span class="year">' + y + '</span>');
            ly = y;
        }

        // Some code to implement rollovers for browsers like IE6:
        item.mouseover(function (e) {
            controller.getItem(y, m).addClass(options.hoverClass);
        });

        item.mouseout(function (e) {
            controller.getItem(y, m).removeClass(options.hoverClass);
        });

        this.setItem(y, m, item);

        $(tmp).append(item);
    });

    $(container).append($('<table></table>').attr('class', 'months').append(tmp));

    // Inserting internal container into the one that was passed in:
    $(parent).append(container);

    // Helper function that determines which item was clicked by examining
    // properties of event object:
    function eToDate(e) {
        return controller.forEachMonth(function (y, m) {
            var i = this.getItem(y, m);
            var x1 = i.offset().left, x2 = x1 + i.width();
            if (e.pageX >= x1 && e.pageX <= x2) {
                return { year:y, month:m };
            }
        });
    }

    // Helper function that cancels event propagation:
    function cancel(e) {
        e.result = false;
        e.preventDefault();
        e.stopPropagation();
        return false;
    }

    // Last item clicked (for Shift-clicks):
    var lastY, lastM;

    // Complex event handler that captures the mouse:
    $(container).mousedown(function(e) {
        var data = eToDate(e);

        // If there was mousedown event within one of the items, start the fun:
        if (data) {
            var y = data.year, m = data.month, mode;

            /*if (e.ctrlKey) {
                mode = 'toggle';
            } else */if (e.shiftKey) {
                // Shift-clicking selects a range between currently clicked item and last clicked item:
                mode = 'range';
            } else {
                // Drag-to-select mode:
                mode = 'free';
            }

            // When user moves the mouse pointer...
            var moveHandler = function (e) {
                if (mode === 'free') {
                    var data = eToDate(e);
                    if (data) {
                        // In drag-to-select mode just select everything between two items:
                        controller.setSelection(DatePicker.PRange(y, m, data.year, data.month));
                    }
                }

                return cancel(e);
            };

            // When user releases the mouse button...
            var upHandler = function (e) {
                var data = eToDate(e);

                if (data) {
                    // Finalize selection:
                    switch (mode) {
                    /*case 'toggle':
                        controller.toggleItem(data.year, data.month);
                        break;*/

                    case 'range':
                        if (typeof lastY !== 'undefined') {
                            controller.addToSelection(DatePicker.PRange(lastY, lastM, data.year, data.month));
                        }
                        break;

                    case 'free':
                        controller.setSelection(DatePicker.PRange(y, m, data.year, data.month));
                        break;
                    }

                    // Remember current item as last-clicked:
                    lastY = data.year;
                    lastM = data.month;

                    // Fire the callback:
                    notify();
                }

                // Release the mouse capture:
                $(document).unbind("mousemove", moveHandler);
                $(document).unbind("mouseup", upHandler);

                return cancel(e);
            };

            // Setup the mouse capture:
            $(document).mousemove(moveHandler);
            $(document).mouseup(upHandler);

            return cancel(e);
        }
    });

    // Setup initial selection:
    controller.setSelection(initialPredicate);
//     notify();

    return controller;
};

// Predicate for a single month:
DatePicker.PMonth = function (y1, m1) {
    return function (y, m) {
        return y1 == y && m1 == m;
    };
};
DatePicker.prototype.setSelectionRange= function (predicate) {
      this.controller.setSelection(predicate);
    };

// Predicate for the current month:
DatePicker.PThisMonth = function () {
    var d = new Date();
    return DatePicker.PMonth(d.getFullYear(), d.getMonth() + 1);
};

// Predicate for the previous month:
DatePicker.PLastMonth = function () {
    var d = new Date();
    d.setMonth(d.getMonth() - 1);
    return DatePicker.PMonth(d.getFullYear(), d.getMonth() + 1);
};

// Predicate for a range (range will be swapped if dates are reversed):
DatePicker.PRange = function (y1, m1, y2, m2) {
    if (y1 > y2 || y1 == y2 && m1 > m2) {
        var t;

        t = y1;
        y1 = y2;
        y2 = t;

        t = m1;
        m1 = m2;
        m2 = t;
    }

    return function (y, m) {
        return (y > y1 || y == y1 && m >= m1) && (y < y2 || y == y2 && m <= m2);
    };
};

// Predicate for the current year:
DatePicker.PThisYear = function () {
    var d = new Date();
    return DatePicker.PRange(d.getFullYear(), 1, d.getFullYear(), 12);
};

// Predicate for the previous year:
DatePicker.PLastYear = function () {
    var d = new Date();
    return DatePicker.PRange(d.getFullYear() - 1, 1, d.getFullYear() - 1, 12);
};

// Predicate for empty selection:
DatePicker.PNone = function () {
    return function (y, m) {
        return false;
    };
};

}());

// Factory function with good defaults:
var DefaultDatePicker = function (parent, y1, m1, y2, m2, initialPredicate, callback) {
    return DatePicker(
        parent,
        y1, m1,
        y2, m2,
        initialPredicate,
        callback,
        {
            // CSS class for the internal container:
            containerClass:'datePicker',
            // CSS class for the list of quick links:
            quickLinksClass:'quickLinks',
            // CSS class for the selected items:
            selectedClass:'selected',
            // CSS class for items under mouse cursor:
            hoverClass:'hover',
            // Quick links:
            quickLinks:[
                /*{
                    caption:'None',
                    predicate:DatePicker.PNone()
                },*/
                {
                    // Caption of the link and...
                    caption:'This Month',
                    // ...predicate to apply when the link is clicked:
                    predicate:DatePicker.PThisMonth()
                },
                {
                    caption:'Last Month',
                    predicate:DatePicker.PLastMonth()
                },
                {
                    caption:'This Year',
                    predicate:DatePicker.PThisYear()
                },
                {
                    caption:'Last Year',
                    predicate:DatePicker.PLastYear()
                },
                {
                    caption:'All Time',
                    predicate:DatePicker.PRange(y1, m1, y2, m2)
                }
            ]
        }
    );
};
