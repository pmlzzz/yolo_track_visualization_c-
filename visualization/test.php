<?php global $link; 
error_reporting(E_ERROR | E_PARSE );
ini_set('display_errors', '1');
ini_set('memory_limit', '2048M');
function mysqlconnect(){
  global $link;
  $link = new mysqli('127.0.0.1', 'crowdtracking', 'crowdzhao719', 'crowdtracking');//https://va.tech.purdue.edu/phpMyAdminVA401/

}
mysqlconnect();
function mysqlclose(){ 
  global $link;
  mysql_close($link);
} 
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
  <meta property="og:image" content="http://romsson.github.io/dragit/img/nations.png"/>
  <meta name="twitter:card" content="photo">
  <meta name="twitter:site" content="@romsson">
  <meta name="twitter:creator" content="@romsson">
  <meta name="twitter:title" content="A Re-Recreation of Gapminder’s Wealth & Health of Nations - Romain Vuillemot">
  <meta name="twitter:image:src" content="http://romsson.github.io/dragit/img/nations.png"/>
  <meta name="twitter:domain" content="http://romsson.github.io/dragit/">
  <title>A Re-Recreation of Gapminder’s Wealth & Health of Nations - Romain Vuillemot</title>
  <meta name="description" content="Drag & Drop World Countries (if you can)">
  <link href="./src/dragit.css" rel="stylesheet"/>
  <script src="./src/d3.v3.js"></script>
  <script src="./src/dragit.js"></script>
</head>
<title>A Re-Recreation of Gapminder’s Wealth & Health of Nations - Romain Vuillemot</title>
<style>
body {
  font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
  margin-left:40px; 
  font-weight: 200;
  font-size: 14px;
}
html,body {
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
text {
  cursor: default;
}
h1 {
  font-weight: 400;
}
#chart {
  height: 506px;
}
text {
  font-size: 10px;
}
.dot {
  stroke: #000;
}
.axis path, .axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}
.label {
  fill: #777;
}
.year.label {
  font: 500 196px "Helvetica Neue";
  fill: #ddd;
}
.country.label  {
  font: 500 96px "Helvetica Neue";
  fill: #ddd;
}
.year.label.active {
  fill: #aaa;
}
circle.pointTrajectory {
   pointer-events: none;
   stroke: lightgray;
   fill: black;
   opacity: 0;
}
path.lineTrajectory {
  stroke-width: 2;
  stroke-opacity: .5;
  stroke: black;
  fill: none;
  pointer-events: none;
}
.selected {
  stroke-width: 4;
}
</style>

<h1>A Re-Recreation of Gapminder’s Wealth & Health of Nations</h1>

<i>Use your mouse to click and drag countries (represented as circles) to explore +200 years of life expectancy and income indicators.</i></p>

<div id="chart" style="margin:0px"></div>
<span id="min-time">1800</span> 
<input type="range" name="points" min="0" max="208" step="1" value="0" id="slider-time" style="width:900px">
<span id="max-time">2008</span>
<br>
<p>
Original creation by <a href="http://www.gapminder.org/world/">Gapminder</a> using <a href="https://github.com/RandomEtc/mind-gapper-js">Tom Carden</a> JavaScript version.<br>
Recreation by <a href="http://bost.ocks.org/mike/nations/">Mike Bostock</a> with <a href="http://d3js.org/">D3.js</a>.<br>
Re-Recreation using <a href="http://romsson.github.io/dragit/">dragit.js</a> by <a href="http://romain.vuillemot.net/">Romain Vuillemot</a> (view <a href="https://github.com/romsson/dragit/blob/master/example/nations.html">source</a> on GitHub).

<div class="twitter-share-container">
  <a href="https://twitter.com/share" class="twitter-share-button" data-via="romsson" data-url="http://romsson.github.io/dragit/example/nations.html">Tweet</a>
  <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
</div></p>

<!-- Place this tag in your head or just before your close body tag. -->
<script type="text/javascript" src="https://apis.google.com/js/platform.js"></script>

<!-- Place this tag where you want the share button to render. -->
<div class="g-plus" data-action="share" data-annotation="bubble" data-href="http://romsson.github.io/dragit/example/nations.html"></div>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=251461691555585&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<br>

<!-- Social media buttons -->
<div class="fb-share-button" data-href="http://romsson.github.io/dragit/example/nations.html" data-type="button_count"></div>

<script>
// Various accessors that specify the four dimensions of data to visualize.
function x(d) { return d.income; }
function y(d) { return d.lifeExpectancy; }
function radius(d) { return d.population; }
function color(d) { return d.region; }
function key(d) { return d.name; }
// Chart dimensions.
var margin = {top: 19.5, right: 19.5, bottom: 19.5, left: 39.5},
    width = 960 - margin.right,
    height = 500 - margin.top - margin.bottom;
// Various scales. These domains make assumptions of data, naturally.
var xScale = d3.scale.log().domain([300, 1e5]).range([0, width]),
    yScale = d3.scale.linear().domain([10, 85]).range([height, 0]),
    radiusScale = d3.scale.sqrt().domain([0, 5e8]).range([0, 40]),
    colorScale = d3.scale.category10();
// The x & y axes.
var xAxis = d3.svg.axis().orient("bottom").scale(xScale).ticks(12, d3.format(",d")),
    yAxis = d3.svg.axis().scale(yScale).orient("left");
// Create the SVG container and set the origin.
var svg = d3.select("#chart").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")")
    .attr("class", "gRoot")
// Add the x-axis.
svg.append("g")
    .attr("class", "x axis")
    .attr("transform", "translate(0," + height + ")")
    .call(xAxis);
// Add the y-axis.
svg.append("g")
    .attr("class", "y axis")
    .call(yAxis);
// Add an x-axis label.
svg.append("text")
    .attr("class", "x label")
    .attr("text-anchor", "end")
    .attr("x", width)
    .attr("y", height - 6)
    .text("income per capita, inflation-adjusted (dollars)");
// Add a y-axis label.
svg.append("text")
    .attr("class", "y label")
    .attr("text-anchor", "end")
    .attr("y", 6)
    .attr("dy", ".75em")
    .attr("transform", "rotate(-90)")
    .text("life expectancy (years)");
// Add the year label; the value is set on transition.
//var label = svg.append("text")
//    .attr("class", "year label")
//    .attr("text-anchor", "end")
//    .attr("y", height - 24)
//    .attr("x", width)
//   .text(1800);
// Add the country label; the value is set on transition.
var countrylabel = svg.append("text")
    .attr("class", "country label")
    .attr("text-anchor", "start")
    .attr("y", 80)
    .attr("x", 20)
    .text(" ");
var first_time = true;
// Load the data.
d3.json("./nations.json", function(nations) {
  // A bisector since many nation's data is sparsely-defined.
  var bisect = d3.bisector(function(d) { return d[0]; });
  // Add a dot per nation. Initialize the data at 1800, and set the colors.
  var dot = svg.append("g")
      .attr("class", "dots")
    .selectAll(".dot")
      .data(interpolateData(1800))
    .enter().append("circle")
      .attr("class", "dot")
      .style("fill", function(d) { return colorScale(color(d)); })
      .call(position)
      .on("mousedow", function(d, i) {
      })
      .on("mouseup", function(d, i) {
        dot.classed("selected", false);
        d3.select(this).classed("selected", !d3.select(this).classed("selected"));
        dragit.trajectory.display(d, i, "selected");
        //TODO: test if has been dragged
        // Look at the state machine history and find a drag event in it?
      })
      .on("mouseenter", function(d, i) {
        clear_demo();
        if(dragit.statemachine.current_state == "idle") {
          dragit.trajectory.display(d, i)
          dragit.utils.animateTrajectory(dragit.trajectory.display(d, i), dragit.time.current, 1000)
          countrylabel.text(d.name);
          dot.style("opacity", .4)
          d3.select(this).style("opacity", 1)
          d3.selectAll(".selected").style("opacity", 1)
        }
      })
      .on("mouseleave", function(d, i) {
        if(dragit.statemachine.current_state == "idle") {
          countrylabel.text("");
          dot.style("opacity", 0.4);
        }
  
        dragit.trajectory.remove(d, i);
      })
      .call(dragit.object.activate)
  // Add a title.
  dot.append("title")
      .text(function(d) { return d.name; });
  // Start a transition that interpolates the data based on year.
  svg.transition()
      .duration(30000)
      .ease("linear")
  // Positions the dots based on data.
  function position(dot) {
    dot.attr("cx", function(d) { return xScale(x(d)); })
       .attr("cy", function(d) { return yScale(y(d)); })
       .attr("r", function(d) { return radiusScale(radius(d)); });
  }//r: radiusScale(radius(d))
  // Defines a sort order so that the smallest dots are drawn on top.
  function order(a, b) {
    return radius(b) - radius(a);
  }
  // Updates the display to show the specified year.
  function displayYear(year) {
    dot.data(interpolateData(year+dragit.time.min), key).call(position).sort(order);
    label.text(dragit.time.min + Math.round(year));
  }
  // Interpolates the dataset for the given (fractional) year.
  function interpolateData(year) {
    return nations.map(function(d) {
      return {
        name: d.name,
        region: d.region,
        income: interpolateValues(d.income, year),
        population: interpolateValues(d.population, year),
        lifeExpectancy: interpolateValues(d.lifeExpectancy, year)
      };
    });
  }
  // Finds (and possibly interpolates) the value for the specified year.
  function interpolateValues(values, year) {
    var i = bisect.left(values, year, 0, values.length - 1),
        a = values[i];
    if (i > 0) {
      var b = values[i - 1],
          t = (year - a[0]) / (b[0] - a[0]);
      return a[1] * (1 - t) + b[1] * t;
    }
    return a[1];
  }
  
  init();
  function update(v, duration) {
    dragit.time.current = v || dragit.time.current;
    displayYear(dragit.time.current)
    d3.select("#slider-time").property("value", dragit.time.current);
  }
  function init() {
    dragit.init(".gRoot");
    dragit.time = {min:1800, max:2009, step:1, current:1800}
    dragit.data = d3.range(nations.length).map(function() { return Array(); })
    for(var yy = 1800; yy<2009; yy++) {
      interpolateData(yy).filter(function(d, i) { 
        dragit.data[i][yy-dragit.time.min] = [xScale(x(d)), yScale(y(d))];
      })
    }
    dragit.evt.register("update", update);
    //d3.select("#slider-time").property("value", dragit.time.current);
    d3.select("#slider-time")
      .on("mousemove", function() { 
        update(parseInt(this.value), 500);
        clear_demo();
      })
    var end_effect = function() {
      countrylabel.text("");
      dot.style("opacity", 1)
    }
    dragit.evt.register("dragend", end_effect)
  }
function clear_demo() {
  if(first_time) {
     svg.transition().duration(0);
    first_time = false;
    window.clearInterval(demo_interval);
    countrylabel.text("");
    dragit.trajectory.removeAll();
    d3.selectAll(".dot").style("opacity", 1)
  }
}
function play_demo() {
  var ex_nations = ["China", "India", "Indonesia", "Italy", "France", "Spain", "Germany", "United States"]
  var index_random_nation = null;
  var random_index = Math.floor(Math.random() * ex_nations.length);
  var random_nation = nations.filter(function(d, i) { 
    if(d.name == ex_nations[random_index]) {
      index_random_nation = i;
      return true;
    }
  })[0];
  var random_nation = nations[index_random_nation];
  dragit.trajectory.removeAll();
  dragit.trajectory.display(random_nation, index_random_nation);
  countrylabel.text(random_nation.name);
  dragit.utils.animateTrajectory(dragit.lineTrajectory, dragit.time.min, 2000)
  d3.selectAll(".dot").style("opacity", .4)
  d3.selectAll(".dot").filter(function(d) {
    return d.name == random_nation.name;
  }).style("opacity", 1)
}
var demo_interval = null;
setTimeout(function() {
  if(first_time) {
    //play_demo()
    demo_interval = setInterval(play_demo, 3000)
  }
}, 1000);
});
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-57485706-1', 'auto');
  ga('send', 'pageview');
</script>
</body>
</html>