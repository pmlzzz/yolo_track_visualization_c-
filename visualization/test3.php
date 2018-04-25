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
  <meta charset="utf-8"/>
  <title>Crowd Trajectory demo</title>
  <link href="./src/dragit.css" rel="stylesheet"/>
  <script src="https://d3js.org/d3.v3.js"></script>
  <script src="./src/dragit.js"></script>
</head>
<style>

body {
  font: 10px sans-serif;
}

.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.x.axis path {
  display: none;
}

.area.above {
  fill: rgb(252,141,89);
}

.area.below {
  fill: rgb(145,207,96);
}

.line {
  fill: none;
  stroke: #000;
  stroke-width: 1.5px;
}
.drag-line {
    cursor: col-resize;
  }
</style>
<body>
<div id="viz"></div>
<p style="clear:both"></p>
<div id="velocity"></div>
<div id="slider"></div>

<label><input type="checkbox" name="mode" value="trajectory" onclick="dragit.trajectory.toggleAll('selected');"> Show all trajectories</label>

<label><input type="radio" name="drag-scope" value="low" onchange="dragit.mouse.scope = 'focus'" checked>drag focus</label>
<label><input type="radio" name="drag-scope" value="low" onchange="dragit.mouse.scope = 'selected'">drag all</label><br>

<label><input type="radio" name="drag-direction" value="low" onchange="dragit.mouse.dragging = 'jump'" checked>jump drag</label>
<label><input type="radio" name="drag-direction" value="low" onchange="dragit.mouse.dragging = 'horizontal'">horizontal drag</label>
<label><input type="radio" name="drag-direction" value="low" onchange="dragit.mouse.dragging = 'free'">free drag</label>
<br>

current state: <span id="current-state"></span><br>
current focus: <span id="current-focus"></span><br>
closest line: <span id="closest-line"></span><br>
closest point: <span id="closest-point"></span><br>
closest time: <span id="closest-time"></span><br>
<!-- extract tracking data-->
<script>
var point = {
  blobs:[],
  points:[],
  frames:[],
  x:[],
  y:[],
  velocity:[],
  orientationx:[],
  orientationy:[]
  };
var framestate = [];
var perframe=[];
var i=0;
</script>
  <?php
$sql = "SELECT blobs,points,frames,x,y,velocity,orientationx,orientationy from testvideo order by frames ";
$result = $link->query($sql);if ($result->num_rows <= 0) {
       echo "0 results";
     }   
while($rows = mysqli_fetch_assoc($result))
  {
    ?>
  <script>
    point.blobs.push(<?php echo($rows["blobs"]); ?>);
    point.points.push(<?php echo($rows["points"]); ?>);
    point.frames.push(<?php echo($rows["frames"]); ?>);
    point.x.push(<?php echo($rows["x"]); ?>);
    point.y.push(<?php echo($rows["y"]); ?>);
    point.velocity.push(<?php echo($rows["velocity"]); ?>);
    point.orientationx.push(<?php echo($rows["orientationx"]); ?>);
    point.orientationy.push(<?php echo($rows["orientationy"]); ?>);
    
    </script>
  <?php
 }
         ?>
<script>
var j=0;
p0={blobs:0,
  points:0,
  frames:0,
  x:0,
  y:0,
  velocity:0,
  orientationx:0,
  orientationy:0};

//console.log(point);
for (var i = 0; i < point.frames.length; i++) {//4725
  var p = {
  blobs:point.blobs[i],
  points:point.points[i],
  frames:point.frames[i],
  x:point.x[i],
  y:point.y[i],
  velocity:point.velocity[i],
  orientationx:point.orientationx[i],
  orientationy:point.orientationy[i]
  };
  
  
  while(point.frames[i]!=j)
  {
    j++;
    if(perframe.length==0)
    {
       perframe.push(p0);
    }
   
    framestate.push(perframe);
    
    var perframe=[];
    
  }
  perframe.push(p);
  //console.log("asdas")
  //console.log(p);
};
//console.log(framestate);
var margin = {top: 20, right: 20, bottom: 30, left: 50},
    width = 960 - margin.left - margin.right,angha
    height = 500 - margin.top - margin.bottom;



var x = d3.scale.linear()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height, 0]);

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

var line = d3.svg.area()
    .interpolate("basis")
    .x(function(d,i) { return x(i); })
    .y(function(d) { return y(d); });

var area = d3.svg.area()
    .interpolate("basis")
    .x(function(d,i) { return x(i); })
    .y1(function(d) { return y(d); });

var svg = d3.select("#velocity").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
var median=7.2
var framedata = d3.range(794).map(function(d, i) {
  var sum=0;
  for (var j = 0; j < framestate[j].length; j++) {
    if(framestate[i][j].velocity>100)
    {
      sum+=median;
    }
    else{sum+=framestate[i][j].velocity;}
     
  };
  return sum/(j+1);
})
console.log(framedata);


var points = svg.selectAll(".points")
                .data(framedata)
                .enter()
                .append("circle")
                .attr("class", "points");


  x.domain([0,framedata.length-1]);

  y.domain([
    d3.min(framedata),
    d3.max(framedata)
  ]);
 
  svg.datum(framedata);
  

  svg.append("clipPath")
      .attr("id", "clip-below")
    .append("path")
      .attr("d", area.y0(height));

  svg.append("clipPath")
      .attr("id", "clip-above")
    .append("path")
      .attr("d", area.y0(0)); 

  svg.append("path")
      .attr("class", "area above")
      .attr("clip-path", "url(#clip-above)")
      .attr("d", area.y0(function(d) { return y(median); }));//分界线
console.log("over");
  svg.append("path")
      .attr("class", "area below")
      .attr("clip-path", "url(#clip-below)")
      .attr("d", area);
console.log("over");
  svg.append("path")
      .attr("class", "line")
      .attr("d", line);

  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);

  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("speed");
var offsetX=400;
  dragLine = svg.append('path')
  .attr('class', 'drag-line')
  .attr('d', `M${0},${y(0)}L${0},0`)
  .attr('stroke', 'lightgrey')
  .attr('stroke-width', 3);

  dragLine.call(
    d3.behavior.drag()  
      .on("dragstart", function(){ return d3.select(this).each(function(){this.parentNode.appendChild(this);})})
      .on("drag", function(){
        let dx = d3.event.sourceEvent.clientX
        let _x = dx - margin.left
        let _base = 0
        let _width = width
        let _offset = _x < _base ? _base : _x > _width ? _width : _x
        
        d3.select(this)
          .attr('transform', () => {
            return `translate(${_offset})`
          })
      })
  )
  dragLine
  .transition()  
  .duration(1000)
  .attr('transform', `translate(${x(offsetX)})`)
// initial parameters
var track = {
  blobs : [],
  points : [],
  frames : [],
  x : [],
  y: [],
  velocity : [],
  orientationx : [],
  orientationy: []
  };
</script>
<!-- extract tracking data-->
  <?php
$sql = "SELECT blobs,points,frames,x,y,velocity,orientationx,orientationy from testvideo ";
$result = $link->query($sql);if ($result->num_rows <= 0) {
       echo "0 results";
     }   
while($rows = mysqli_fetch_assoc($result))
  {
    ?>
  <script>
    track.blobs.push(<?php echo($rows["blobs"]); ?>);
    track.points.push(<?php echo($rows["points"]); ?>);
    track.frames.push(<?php echo($rows["frames"]); ?>);
    track.x.push(<?php echo($rows["x"]); ?>);
    track.y.push(<?php echo($rows["y"]); ?>);
    track.velocity.push(<?php echo($rows["velocity"]); ?>);
    track.orientationx.push(<?php echo($rows["orientationx"]); ?>);
    track.orientationy.push(<?php echo($rows["orientationy"]); ?>);
    </script>
  <?php
 }
         ?>
<script>
var a=137
var b=794
var coordinate={x:[],y:[]};
var point =[];
    for (var i = 0; i <= a; i++) {
      point[i]=[];
      for (var j = 0; j <= b; j++) {
        point[i][j]={x:1,y:1,v:0};
      };
    };

//console.log(point[a][b]);
for (var i = track.blobs.length-1; i >0; i--) {

  a=track.blobs[i];
  b=track.frames[i];
  
  point[a][b].x=track.x[i];
  point[a][b].y=track.y[i];
  point[a][b].v=track.velocity[i];
  //console.log(point[a][b].x,point[a][b].y,point[a][b].v);
};
function checkpoint(a,b){
  if(b<794&&point[a][b].x==1 && point[a][b].y==1 ){
    return checkpoint(a,b+1)
  }
  else{
    return point[a][b]
  }
}

var points=[];
for (var a = 0; a <= 137; a++) {
  points[a]=[];
  for (var b = 0; b <= 794; b++) {
    //point[a][b].x=checkpoint(a,b).x;
    //point[a][b].y=checkpoint(a,b).y;
    points[a][b]={x:checkpoint(a,b).x,y:checkpoint(a,b).y,v:point[a][b].v};
  };
};
function checkpoint2(a,b){
  if(point[a][b].x==1 && point[a][b].y==1 && b>0){
    return checkpoint2(a,b-1)
  }
  else{
    return points[a][b]
  }
}
var color=[];
for (var i = 0; i <=137 ; i++) {
  color[i]="rgb("+Math.floor(Math.random()*255)+", "+Math.floor(Math.random()*255)+", "+Math.floor(Math.random()*255)+")"
  //console.log(color[i]);
};
var pointss=[];
for (var a = 0; a <= 137; a++) {
  pointss[a]=[];
  for (var b = 0; b <= 794; b++) {
    //point[a][b].x=checkpoint(a,b).x;
    //point[a][b].y=checkpoint(a,b).y;
    pointss[a][b]={x:checkpoint2(a,b).x,y:checkpoint2(a,b).y,v:point[a][b].v,color:color[a]};
  };
};
//console.log(pointss);
var margin = {top: 20, right: 20, bottom: 20, left: 50},
    width = 900 - margin.right - margin.left,
    height = 500 - margin.top - margin.bottom;
var time_steps = 794, nb_points = 138, current_time = Math.floor(Math.random()*time_steps);
var timecube = d3.range(nb_points).map(function(d, i) {
  return d3.range(time_steps).map(function(e, j) { 
    return {x: pointss[i][j].x, y: pointss[i][j].y, t: j, v: pointss[i][j].v,color:pointss[i][j].color};
  });
})
var xScale = d3.scale.linear().domain([0, time_steps]).range([margin.left, width]);
var yScale = d3.scale.linear()
                     .domain([0, d3.max(timecube, function(d) { 
                                                    return d3.max(d, function(e) { 
                                                      return e.y; 
                                                    });
                                                  })
                     ])
                     .range([margin.top, height]);
var svg = d3.select("#viz").append("svg")
                           .attr({width: width+100, height:height+100})
var rect=svg
            .append("rect")
            .attr("x",0)
            .attr("y",0)
            .attr("width", 900)
            .attr("height", 500)
            .attr("fill", "none")
            .attr("stroke", "blue");
var points = svg.selectAll(".points")
                .data(timecube)
                .enter()
                .append("circle")
                .attr("class", "points");
// All other focus points
//console.log(timecube);
points.attr({cx: function(d) { return xScale(d[current_time].x)}, 
             cy: function(d) { return yScale(d[current_time].y)}, 
             r:function(d) { if(d[current_time].v<30) return d[current_time].v},  
             fill:function(d) { return d[current_time].color},
             opacity:"0.3"
            })
      .on("mouseenter", dragit.trajectory.display)
      .on("mouseleave", dragit.trajectory.remove)
      .call(dragit.object.activate)
// Time update callback function

var points2 = svg.selectAll(".points2")
                .data(timecube)
                .enter()
                .append("circle")
                .attr("class", "points2");
// All other focus points
//console.log(track);
points2.attr({cx: function(d) { return xScale(d[current_time].x)}, 
             cy: function(d) { return yScale(d[current_time].y)}, 
             r:function(d) { if(d[current_time].v<30) return d[current_time].v}, 
             fill:function(d) { return d[current_time].color},
             opacity:"0.3"
            })
      .on("mouseenter", dragit.trajectory.display)
      .on("mouseleave", dragit.trajectory.remove)
      .call(dragit.object.activate)


var points3 = svg.selectAll(".points3")
                .data(timecube)
                .enter()
                .append("circle")
                .attr("class", "points3");
// All other focus points
//console.log(track);
points3.attr({cx: function(d) { return xScale(d[current_time].x)}, 
             cy: function(d) { return yScale(d[current_time].y)}, 
             r:function(d) { if(d[current_time].v<30) return d[current_time].v}, 
             fill:function(d) { return d[current_time].color},
             opacity:"0.3"
            })
      .on("mouseenter", dragit.trajectory.display)
      .on("mouseleave", dragit.trajectory.remove)
      .call(dragit.object.activate)

var points4 = svg.selectAll(".points4")
                .data(timecube)
                .enter()
                .append("circle")
                .attr("class", "points4");
// All other focus points
//console.log(track);
points4.attr({cx: function(d) { return xScale(d[current_time].x)}, 
             cy: function(d) { return yScale(d[current_time].y)}, 
             r:function(d) { if(d[current_time].v<30) return d[current_time].v},  
             fill:function(d) { return d[current_time].color},
             opacity:"0.3"
            })
      .on("mouseenter", dragit.trajectory.display)
      .on("mouseleave", dragit.trajectory.remove)
      .call(dragit.object.activate)

var points5 = svg.selectAll(".points5")
                .data(timecube)
                .enter()
                .append("circle")
                .attr("class", "points5");
// All other focus points
//console.log(track);
points5.attr({cx: function(d) { return xScale(d[current_time].x)}, 
             cy: function(d) { return yScale(d[current_time].y)}, 
             r:function(d) { if(d[current_time].v<30) return d[current_time].v},  
             fill:function(d) { return d[current_time].color},
             opacity:"0.3"
            })
      .on("mouseenter", dragit.trajectory.display)
      .on("mouseleave", dragit.trajectory.remove)
      .call(dragit.object.activate)

// Time update callback function

function update(v, t) {
  dragit.time.current = v || dragit.time.current;
  //console.log(dragit.time.current);
  //console.log(dragit.time.current);
  points.transition().duration(100)
        .attr({cx: function(d) { return xScale(d[dragit.time.current].x)}, 
               cy: function(d) { return yScale(d[dragit.time.current].y)},
               r: function(d) { if(d[dragit.time.current].v<30) return d[dragit.time.current].v}
              })

  points2.transition().duration(100)
        .attr({cx: function(d) { return xScale(d[dragit.time.current+1].x)}, 
               cy: function(d) { return yScale(d[dragit.time.current+1].y)},
               r: function(d) { if(d[dragit.time.current+1].v<30) return d[dragit.time.current+1].v}
              }) 

  points3.transition().duration(100)
        .attr({cx: function(d) { return xScale(d[dragit.time.current+2].x)}, 
               cy: function(d) { return yScale(d[dragit.time.current+2].y)},
               r: function(d) { if(d[dragit.time.current+2].v<30) return d[dragit.time.current+2].v}
              })     

  points4.transition().duration(100)
        .attr({cx: function(d) { return xScale(d[dragit.time.current+3].x)}, 
               cy: function(d) { return yScale(d[dragit.time.current+3].y)},
               r: function(d) { if(d[dragit.time.current+3].v<30) return d[dragit.time.current+3].v}
              })     

  points5.transition().duration(100)
        .attr({cx: function(d) { return xScale(d[dragit.time.current+4].x)}, 
               cy: function(d) { return yScale(d[dragit.time.current+4].y)},
               r: function(d) { if(d[dragit.time.current+4].v<30) return d[dragit.time.current+4].v}
              })     

}
function init() {
  dragit.init("svg");
  dragit.data = timecube.map(function(d, i) { 
    return d.map(function(e, j) { 
      return [xScale(e.x), yScale(e.y), i]; 
    }) 
  });
  
  dragit.time = {min:0, max:time_steps, step:1, current: current_time}
  
  dragit.evt.register("update", update);
  dragit.evt.register("new_state", function() {
    d3.select("#current-state").text(dragit.statemachine.current_state);
  });
  dragit.evt.register("update", function() { 
    d3.select("#closest-time").text(dragit.time.current);
    d3.select("#current-focus").text(dragit.statemachine.current_id);
  });
  dragit.utils.slider("#slider", true)
}
init();
</script>
</body>
</html>
