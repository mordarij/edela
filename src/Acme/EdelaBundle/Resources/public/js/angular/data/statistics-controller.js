edelaControllers.controller('StatisticsListController', ['$scope', '$http', '$rootScope', '$filter', function ($scope, $http, $rootScope, $filter) {
    $http.get('api/statistics/progress').success(function (data) {
        $scope.progress = data;
    });

    $http.get('api/statistics/actions').success(function(data){
        for (var i in data){
            if (!data.hasOwnProperty(i)) continue;
            data[i].start_date = (new Date(data[i].start_at)).format('dd.mm.yyyy');
            $scope.actions = data;
        }
    });

    $scope.efficiencyRenderer = function (el, origData) {
        if (!origData) return;
        var len = 0;
        var data = angular.copy(origData);
        var sumEfficiency = 0;
        if (data.length < 1) {
            data.unshift({calculated_at: (new Date()).format('Y-m-d'), efficiency: 0});
        }
        for (var i in data) {
            if (!data.hasOwnProperty(i)) continue;
            data[i].date = new Date(data[i].calculated_at);
            sumEfficiency += Number(data[i].efficiency);
            data[i].price = sumEfficiency;
        }
        len = data.length;

        var margin = {top: 10, right: 60, bottom: 100, left: 0},
            width = 995 - margin.left - margin.right,
            height = 590 - margin.top - margin.bottom,


            x = d3.time.scale().range([0, width]),
            y = d3.scale.linear().range([height, 0]),

            xAxis = d3.svg.axis().scale(x).orient("bottom"),
            yAxis = d3.svg.axis().scale(y).orient("right");

        var area = d3.svg.area()
            .interpolate("monotone")
            .x(function (d) {
                return x(d.date);
            })
            .y0(height)
            .y1(function (d) {
                return y(d.price);
            });
        var svg = el.append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom);

        svg.append("defs").append("clipPath")
            .attr("id", "clip")
            .append("rect")
            .attr("width", width)
            .attr("height", height);

        var focus = svg.append("g")
            .attr("class", "focus")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        x.domain(d3.extent(data.map(function (d) {
            return d.date;
        })));
        y.domain([0, d3.max(data.map(function (d) {
            return d.price;
        }))]);

        focus.append("g")
            .attr("class", "y axis grid")
            .attr("transform", "translate(" + width + ",0)")
            .call(yAxis.tickSize(-width).tickPadding(10));

        var line = d3.svg.line()
            .x(function (d) {
                return x(d.date);
            })
            .y(function (d) {
                return y(d.price);
            });

        focus.append("path")
            .datum(data)
            .attr("class", "area")
            .attr("d", line);

        var graphArea = focus.append('g').attr('clip-path', 'url(#clip)');
        var div = d3.select("body").append("div")
            .attr("class", "d3-tip")
            .style("opacity", 0);

        var elem = focus.selectAll('g.circle')
            .data(data);
        var elemEnter = elem.enter().append('g').attr('pointer-events', 'all')
                .on('mouseover', function () {
                    d3.select(this).select('text').style('visibility', 'visible')
                })
                .on('mouseout', function () {
                    d3.select(this).select('text').style('visibility', 'hidden')
                })
            ;
        var circle = elemEnter.append('circle')
                .attr('cx', function (d) {
                    return x(d.date);
                })
                .attr('cy', function (d) {
                    return y(d.price);
                })
                .attr('r', 5)
                .attr('fill', '#055FBC')
            ;
        elemEnter.append('text')
            .attr('x', function (d) {
                return x(d.date) - 15;
            })
            .attr('y', function (d) {
                return y(d.price) - 10;
            })
            .style('visibility', 'hidden')
            .style('font-size', '1.2em')
            .html(function (d) {
                return d.price
            });

        focus.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + height + ")")
            .call(xAxis.ticks(5).tickFormat(d3.time.format('%e %B')));
        function type(d) {
            if (typeof d.date === 'string' || typeof d.date === 'undefined') {
                d.date = new Date(d.calculated_at);
            }
            d.price = +d.efficiency;
            return d;
        }
    }


    $scope.progressRenderer = function (el, origData) {
        if (!origData || origData.length < 1) return;

        el.html('');
        var len = 0;
        var data = angular.copy(origData);
        data = data.map(type);
        len = data.length;

        var margin = {top: 10, right: 60, bottom: 100, left: 0},
            margin2 = {top: 410, right: 60, bottom: 20, left: 0},
            width = 995 - margin.left - margin.right,
            height = 500 - margin.top - margin.bottom,
            height2 = 480 - margin2.top - margin2.bottom;


        var x = d3.time.scale().range([0, width]),
            x2 = d3.time.scale().range([0, width]),
            y = d3.scale.linear().range([height, 0]),
            y2 = d3.scale.linear().range([height2, 0]);

        var xAxis = d3.svg.axis().scale(x).orient("bottom"),
            xAxis2 = d3.svg.axis().scale(x2).orient("bottom"),
            yAxis = d3.svg.axis().scale(y).ticks(4).orient("right");

        var brush = d3.svg.brush()
            .x(x2)
            .on("brush", brushed);

        var area = d3.svg.area()
            .interpolate("monotone")
            .x(function (d) {
                return x(d.date);
            })
            .y0(height)
            .y1(function (d) {
                return y(d.price);
            });

        var area2 = d3.svg.area()
            .interpolate("monotone")
            .x(function (d) {
                return x2(d.date);
            })
            .y0(height2)
            .y1(function (d) {
                return y2(d.price);
            });


        var svg = el.append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom);

        svg.append("defs").append("clipPath")
            .attr("id", "clip")
            .append("rect")
            .attr("width", width)
            .attr("height", height);

        var focus = svg.append("g")
            .attr("class", "focus")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        var context = svg.append("g")
            .attr("class", "context")
            .attr("transform", "translate(" + margin2.left + "," + margin2.top + ")");


        x.domain(d3.extent(data.map(function (d) {
            return d.date;
        })));
        y.domain([0, d3.max(data.map(function (d) {
            return d.price;
        }))]);
        x2.domain(x.domain());
        y2.domain(y.domain());

        focus.append("g")
            .attr("class", "y axis grid")
            .attr("transform", "translate(" + width + ",0)")
            .call(yAxis.tickSize(-width).tickPadding(10).tickFormat(d3.format("%")));
        var graphArea = focus.append('g').attr('clip-path', 'url(#clip)');
        var div = d3.select("body").append("div")
            .attr("class", "d3-tip")
            .style("opacity", 0);
        var focusPos = focus.node().getBoundingClientRect();

        graphArea.selectAll("rect")
            .data(data)
            .attr("d", area)
            .enter().append("rect").attr('class', 'bar')
            .attr('y', function (d) {
                return y(d.price);
            })
            .attr("width", function (d) {
                return (width / len) - 5;
            })
            .attr("x", function (d) {
                return x(d.date) - ((width / len) - 5) / 2;
            })
            .attr('height', function (d) {
                return height - y(d.price)
            })
            .on("mouseover", function (d) {

                div.transition()
                    .duration(200)
                    .style("opacity", .9);
                div.html(Math.floor(100 * d.progressed_actions / d.total_actions) + '%')
                    .style("left", focusPos.left + x(d.date) + "px")
                    .style("top", (focusPos.top - 20) + "px");
            })
            .on("mouseout", function (d) {
                div.transition()
                    .duration(500)
                    .style("opacity", 0);
            });

        if (data.length < 2) {
            return;
        }

        var line2 = d3.svg.line()
            .x(function (d) {
                return x2(d.date);
            })
            .y(function (d) {
                return y2(d.price);
            });

        context.append("path")
            .datum(data)
            .attr("class", "area")
            .attr("d", line2);

        var brushg = context.append("g")
            .attr("class", "x brush");


        brushg.append("rect").attr("class", 'background').attr('x', 0).attr('y', 0).attr('width', width).attr('height', height2);
        brushg.call(brush);
        brushg.selectAll("rect")
            .attr("y", -6)
            .attr("height", height2 + 7);

        brushg.selectAll(".resize").append("rect").attr('x', -4).attr('y', -2).attr('width', 8).attr('height', height2).attr('rx', 2).attr('ry', 2);

        if (data.length > 2) {
            brushg.call(brush.extent([data[Math.floor(data.length / 2)].date, data[data.length - 1].date]));
        } else {
            brushg.call(brush.extent([data[0].date, data[data.length - 1].date]));
        }
        brushg.call(brush.event);

        function brushed() {
            x.domain(brush.empty() ? x2.domain() : brush.extent());
            focus.select(".area").attr("d", area);
            var ratio = (brush.extent()[1] - brush.extent()[0]) / (data[data.length - 1].date - data[0].date)
            if (isNaN(ratio)) {
                ratio = 1;
            }
            console.log(ratio);
            focus.selectAll("rect")
                .attr("width", function (d) {
                    var newwidth = (width / ratio / len) - 5/ ratio;
                    return newwidth < 3 ? 3 : newwidth;
                })
                .attr("x", function (d) {
                    return x(d.date) - ((width / ratio / len)) / 2 + 5 / ratio;
                });
            focus.select(".x.axis").call(xAxis);

        }

        function type(d) {
            if (typeof d.date === 'string' || typeof d.date === 'undefined') {
                d.date = new Date(d.calculated_at);
            }
            d.price = d.progressed_actions / d.total_actions;
            return d;
        }
    }

}]);