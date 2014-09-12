var PieChart;

(function () {

PieChart = function (raphael, cx, cy, r, legendContainerId, width, height) {
    this.r = raphael;
    this.cx = cx;
    this.cy = cy;
    this.radius = r;
    this.legendContainerId = legendContainerId;
    this.width = width;
    this.height = height;
    this.base = this.r.rect(0, 0, width, height).attr({ fill:'#FFF', stroke:'none' });
    this.animationLength = 1000;
    this.sectors = [];
    this.shadows = [];
    this.lastValues = [];
    this.to = undefined;
};

PieChart.colors = [
    '#8A2BE2',
    '#5F9EA0',
    '#FF7F50',
    '#DC143C',
    '#008B8B',
    '#006400',
    '#556B2F',
    '#8B0000',
    '#483D8B',
    '#9400D3',
    '#FF1493',
    '#2F4F4F',
    '#1E90FF'
];

PieChart.prototype.render = function (labels, values) {
    var cx = this.cx, cy = this.cy, r = this.radius, stroke = '#EEE';
    var rad = Math.PI / 180, chart = this.r.set(), angle = 0, total = 0;

    for (var i = 0; i < values.length; i++) {
        total += values[i];
    }

    for (var i = 0; i < Math.max(this.sectors.length, values.length); i++) { (function (i) {
        var value = values[i] || 0,
            angleplus = 360 * value / total,
            popangle = angle + (angleplus / 2),
            color = PieChart.colors[i % PieChart.colors.length],
            bcolor = color,
            delta = r * -0.4;

        var startAngle = angle, endAngle = angle + angleplus;
        var x1 = cx + r * Math.cos(-startAngle * rad),
            x2 = cx + r * Math.cos(-endAngle * rad),
            y1 = cy + r * Math.sin(-startAngle * rad),
            y2 = cy + r * Math.sin(-endAngle * rad);

        var shadowAttrs = { fill:'#888', stroke:'#888', 'stroke-width':3 };
        var sectorAttrs = { gradient: '90-' + bcolor + '-' + color, stroke:stroke, 'stroke-width':3 };
        var path = function (x1, y1, x2, y2, cx, cy) {
            return endAngle - startAngle > 359 ?
                [ 'M', cx, cy, 'L', x1, y1, 'M', cx + r, cy, 'A', r, r, 0, +1, 0, cx - r, cy, 'A', r, r, 0, +1, 0, cx + r, cy, 'z' ] :
                [ 'M', cx, cy, 'L', x1, y1, 'A', r, r, 0, +(endAngle - startAngle > 180 ? 1 : 0), 0, x2, y2, 'z' ];
        };
        var so = 10;
        var sectorPath = path(x1, y1, x2, y2, cx, cy);
        var shadowPath = path(x1 + so, y1 + so, x2 + so, y2 + so, cx + so, cy + so);
        var self = this;

        var s = this.shadows[i];
        var p = this.sectors[i];

        if (!p) {
            s = this.shadows[i] = this.r.path(shadowPath).attr(shadowAttrs);
            p = this.sectors[i] = this.r.path(sectorPath).attr(sectorAttrs);

            p.mouseover(function () {
                if (!self.to) {
                    p.animate({scale: [1.1, 1.1, cx, cy]}, self.animationLength, "elastic");
                    s.animate({scale: [1.1, 1.1, cx, cy]}, self.animationLength, "elastic");
                    p.txt.animate({opacity: 1}, self.animationLength, "elastic");
                    p.txtBg.animate({opacity: 0.7}, self.animationLength, "elastic");
                }
            }).mouseout(function () {
                if (!self.to) {
                    p.animate({scale: [1, 1, cx, cy]}, self.animationLength, "elastic");
                    s.animate({scale: [1, 1, cx, cy]}, self.animationLength, "elastic");
                    p.txt.animate({opacity: 0}, self.animationLength, "elastic");
                    p.txtBg.animate({opacity: 0}, self.animationLength, "elastic");
                }
            });
        }

        if (!p.txtBg) {
            p.txtBg = this.r.rect(0, 0, 0, 0, 5).attr({ fill:'#FFF', stroke:'#888', 'stroke-width':1, opacity:0 });
        }

        if (!p.txt) {
            p.txt = this.r.text(0, 0, '').attr({
                fill:color,
                stroke:'none',
                opacity:0,
                'font-weight':'bold',
                'font-family':'Fontin-Sans, Arial',
                'font-size':'10pt'
            });
        }

        s.attr({ path:shadowPath, opacity:value ? 1 : 0 });
        p.attr({ path:sectorPath, opacity:value ? 1 : 0 });

        p.txt.attr({
            x:Math.round(cx + (r + delta) * Math.cos(-popangle * rad)),
            y:Math.round(cy + (r + delta) * Math.sin(-popangle * rad)),
            text:labels[i],
            'text-anchor':popangle > 90 && popangle < 270 ? 'end' : 'start'
        });

        var tpx = 10, tpy = 4;
        var tw = p.txt.getBBox().width, th = p.txt.getBBox().height, ty = p.txt.attr('y') - th / 2;
        var tx = p.txt.attr('x') + (popangle > 90 && popangle < 270 ? -tw : 0);

        p.txtBg.attr({
            x:Math.round(tx - tpx),
            y:Math.round(ty - tpy),
            width:Math.round(tw + tpx * 2),
            height:Math.round(th + tpy * 2)
        });

        angle += angleplus;
    }.call(this, i)); }

    for (var i = 0; i < Math.max(this.sectors.length, values.length); i++) { (function (i) {
        var p = this.sectors[i];

        if (p) {
            p.toFront();
            p.txtBg.toFront();
            p.txt.toFront();
        }
    }.call(this, i)); }
};

var to = undefined;

PieChart.prototype.setData = function (labels, values) {
    var steps = 25, step = 0, delay = 20, self = this;

    if (this.to) {
        clearTimeout(this.to);
        this.to = undefined;
    }

    if (this.legendContainerId) {
        var total = 0;

        $.each(values, function (i, v) { total += v; });

        var html = '<ul>';
        $.each(labels, function (index, label) {
            html +=
                '<li>' +
                '<span class="sample" style="background-color: ' + PieChart.colors[index % PieChart.colors.length] + ';">&nbsp;&nbsp;&nbsp;</span>' +
                ' <span class="percentage">' + Math.round(values[index] / total * 100)  + '%</span>' +
                ' <span class="count">' + values[index] + '</span>' +
                ' <span class="label">' + label + '</span>' +
                '</li>';
        });
        html += '</ul>'
        $('#' + this.legendContainerId).html(html);
    }

    if (!self.lastValues.length) {
        self.render(labels, values);
        self.lastValues = values;
    } else {
        (function () {
            step++;

            var v = [], k = step / steps;
            for (var i = 0; i < Math.max(values.length, self.lastValues.length); i++) {
                v[i] = (self.lastValues[i] || 0) * (1 - k) + (values[i] || 0) * k;
            }

            self.render(labels, v);
            self.lastValues = v;

            self.to = step < steps ? setTimeout(arguments.callee, delay) : undefined;
        }());
    }
};

}());
