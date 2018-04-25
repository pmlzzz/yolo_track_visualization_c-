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
  stroke: #111;
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
div.tooltip { 
    position: absolute;     
    text-align: center;     
    width: 60px;          
    height: 28px;         
    padding: 2px;       
    font: 12px sans-serif;    
    background: lightsteelblue; 
    border: 0px;    
    border-radius: 8px;     
    pointer-events: none;     
}
.slider {
  position: relative;
  top: 40px;
  left: 40px;
}

.slider-tray {
  position: absolute;
  width: 100%;
  height: 6px;
  border: solid 1px #ccc;
  border-top-color: #aaa;
  border-radius: 4px;
  background-color: #f0f0f0;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.08);
}

.slider-handle {
  position: absolute;
  top: 3px;
}

.slider-handle-icon {
  width: 14px;
  height: 14px;
  border: solid 1px #aaa;
  position: absolute;
  border-radius: 10px;
  background-color: #fff;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
  top: -7px;
  left: -7px;
}
.framework{
  fill: none;
  stroke: #404040;
  stroke-width: 1.5px;
}
.brush .background {
      stroke: none;
      fill: none;
    }
    
.brush .extent {
  stroke: #404040;
  fill-opacity: .125;
  shape-rendering: crispEdges;
}

.brush text {
  font-size: 0.75em;
}
#button {
    
    position: absolute;
    top: 540px;
    left: 1100px;
}
#velocity {
    
    position: relative;
    top: 100px;
    left: 0px;
}
#videodiv {
    
    position: absolute;
    top: 30px;
    left: 60px;
}

</style>
<body bgcolor="#272822">
<div id="videodiv">
<video id="video" width="900" opacity="0.5" src="./test.webm" >
your browser does not support the video tag
</video>
</div>
<div id="viz"></div>
<p style="clear:both"></p>
<div id="velocity"></div>
<div id="shortline"></div>

<div id="button">

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
</div>

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
var allpoints=[];

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
var i=0;
</script>
<!-- extract tracking data-->
  <?php
$sql = "SELECT blobs,points,frames,x,y,velocity,orientationx,orientationy from newtrack768x576 ";
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
  <?php
$sql = "SELECT blobs,points,frames,x,y,velocity,orientationx,orientationy from newtrack768x576 order by frames ";
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
var blobsnum=0;
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
  
  if(point.blobs[i]>blobsnum) blobsnum=point.blobs[i];
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
  allpoints.push(p);
  
};

var pointnum=allpoints.length;
var framenum=framestate.length;
var linexScale = d3.scale.linear().domain([0, allpoints[allpoints.length-1].frames]).range([0, allpoints[allpoints.length-1].frames*30]);

var margin = {top: 20, right: 20, bottom: 30, left: 50},
    width = linexScale(50);
    height = 300 - margin.top - margin.bottom;



var x = d3.scale.linear()
    .range([0, linexScale(50)]);

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
    .x(function(d,i) { return x(i);})
    .y1(function(d) { return y(d);});

var svg = d3.select("#velocity").append("svg")
    .attr("width", (width + margin.left + margin.right)*2)
    .attr("height", height + margin.top + margin.bottom+100)
    .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
var innerSvg = svg.append('foreignObject').append('svg').attr('width', 0)
  .attr('height', height);
var median=7.2
var framedata = d3.range(framenum).map(function(d, i) {
  var sum=0;
  for (var j = 0; j < framestate[j].length; j++) {
    if(framestate[i][j].velocity>50)
    {
      sum+=median;
    }
    else{sum+=framestate[i][j].velocity;}
     
  };
  return sum/(j+1);
})


var div = d3.select("body").append("div") 
    .attr("class", "tooltip")       
    .style("opacity", 0);



  x.domain([0,framedata.length]);

  y.domain([
    d3.min(framedata),
    d3.max(framedata)
  ]);



 var brush = d3.svg.brush()
      .x(x)
      .extent([0,50])
      .on("brush", brushmove);
 var brushg = svg.append("g")
      .classed("brush", true)
      .call(brush);
    
    brushg.selectAll("rect")   .style("visibility", null)   .attr("y", 0)
    .attr("height", height);
    
    brushg.selectAll(".resize rect")
      .attr("width", 3);
  function brushmove() {

      lines.attr("transform", "translate(" + (-linexScale(brush.extent()[0])) + "," + 0 + ")");
      document.getElementById("video").currentTime =(brush.extent()[0]/framenum)*79;
      dragit.time.current=parseInt(brush.extent()[0]);
      dragit.evt.call("update", parseInt(brush.extent()[0]), 0);
      console.log("current",dragit.time.current)
    }




  svg.datum(framedata);
  innerSvg.datum(framedata);
  var rect=svg
            .append("rect").attr("class", "framework") 
            .attr("x",0)
            .attr("y",0)
            .attr("width", linexScale(50))
            .attr("height", height)
            .attr("fill", "none")
            .attr("stroke", "blue");
  innerSvg.append("clipPath")
      .attr("id", "clip-below")
    .append("path")
      .attr("d", area.y0(height));

  innerSvg.append("clipPath")
      .attr("id", "clip-above")
    .append("path")
      .attr("d", area.y0(0)); 

  innerSvg.append("path")
      .attr("class", "area above")
      .attr("clip-path", "url(#clip-above)")
      .attr("d", area.y0(function(d) { return y(median); }));//分界线

  innerSvg.append("path")
      .attr("class", "area below")
      .attr("clip-path", "url(#clip-below)")
      .attr("d", area);

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
      .attr("transform", "translate(170,0)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .attr("font-size", "20")
      .style("text-anchor", "end")
      .text("moving speed");
var offsetX=400;
  dragLine = svg.append('path')
  .attr('class', 'drag-line')
  .attr('d', `M${0},${y(0)}L${0},0`)
  .attr('stroke', 'lightgrey')
  .attr('stroke-width', 3)
  .on("mouseover", function(d,i) {    
            div.transition()    
                .duration(200)    
                .style("opacity", .9);
            var X= parseInt((d3.event.pageX-57)/890*framenum)
            div .html(X + "<br/>"  + framedata[X])  
                .style("left", (d3.event.pageX) + "px")   
                .style("top", (d3.event.pageY - 28) + "px");  
            })          
        .on("mouseout", function(d) {   
            div.transition()    
                .duration(500)    
                .style("opacity", 0); 
        });

  dragLine.call(
    d3.behavior.drag()  
      .on("dragstart", function(){ return d3.select(this).each(function(){this.parentNode.appendChild(this);})})
      .on("drag", function(){
        let dx = d3.event.sourceEvent.clientX
        let _x = dx - margin.left
        let _base = 0
        let _width = linexScale(50)
        let _offset = _x < _base ? _base : _x > _width ? _width : _x
        innerSvg.attr('width', _offset)
        //direction.attr('opacity',function(d){if (d.frames>_offset) {return 0;}else {return 0.3;};})
       
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
  innerSvg
  .transition()  
  .duration(1000)
  .attr('width', x(offsetX))
// initial parameters

</script>

<script>
var a=blobsnum
var b=framenum;
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
  if(b<framenum&&point[a][b].x==1 && point[a][b].y==1 ){
    return checkpoint(a,b+1)
  }
  else{
    return point[a][b]
  }
}

var points=[];
for (var a = 0; a <= blobsnum; a++) {
  points[a]=[];
  for (var b = 0; b <= framenum; b++) {
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
for (var i = 0; i <=blobsnum ; i++) {
  color[i]="rgb("+Math.floor(Math.random()*255)+", "+Math.floor(Math.random()*255)+", "+Math.floor(Math.random()*255)+")"
  //console.log(color[i]);
};
var pointss=[];
for (var a = 0; a <= blobsnum; a++) {
  pointss[a]=[];
  for (var b = 0; b <= framenum; b++) {
    //point[a][b].x=checkpoint(a,b).x;
    //point[a][b].y=checkpoint(a,b).y;
    pointss[a][b]={x:checkpoint2(a,b).x,y:checkpoint2(a,b).y,v:point[a][b].v,color:color[a]};
  };
};
//console.log(pointss);
var margin = {top: 20, right: 20, bottom: 20, left: 50},
    width = 900 - margin.right - margin.left,
    height = 675;
var time_steps = framenum, nb_points = blobsnum, current_time = Math.floor(Math.random()*time_steps);
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
var mainsvg = d3.select("#viz").append("svg")
                           .attr({width: (width+100)*2, height:height+50})
                           .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
var videodiv = d3.select("#viz").append("div") 
    .attr("class", "tooltip")       
    .style("opacity", 0);
var videocover=mainsvg
            .append("rect")
            .attr("x",2)
            .attr("y",2)
            .attr("width", 900)
            .attr("height", 675)
            .attr("fill", "white")
            .attr("stroke", "none")
            .attr("opacity","0.5");
var rect=mainsvg
            .append("rect").attr("class", "framework") 
            .attr("x",2)
            .attr("y",2)
            .attr("width", 900)
            .attr("height", 675)
            .attr("fill", "none")
            .attr("stroke", "blue");
var points = mainsvg.selectAll(".points")
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

var points2 = mainsvg.selectAll(".points2")
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


var points3 = mainsvg.selectAll(".points3")
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

var points4 = mainsvg.selectAll(".points4")
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

var points5 = mainsvg.selectAll(".points5")
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


//////////////////
var angleleft=width + margin.left + margin.right+ margin.left;
var center= {x: angleleft+250, y: 250}
var axisR=[ 50, 100, 150, 200 ];
var circleaxis = mainsvg.selectAll(".circleaxis")
                .data(axisR)
                .enter()
                .append("circle")
                .attr("class", "circleaxis");

    circleaxis.attr({cx: center.x, 
                 cy: center.y, 
                 r:function(d){return d},  
                 fill:"white",
                 stroke:"black",
                 opacity:"0.3"
                });
mainsvg.append("text")
      .attr("transform", "translate(170,0)")
      .attr("x", angleleft)
      .attr("y", 6)
      .attr("dy", ".71em")
      .attr("font-size", "20")
      .style("text-anchor", "end")
      .text("moving angle")


var  R= d3.scale.linear()
    .range([0, 200]);
R.domain([0,800]);
function degree(x,y)
{
  var tmp=Math.atan2(y,x) / (Math.PI/180);
  return (tmp>0)?tmp:(tmp+360);
}
function RGB(x,y)
{
  var R,G,B,V,a1,b1,j,f;
  var H=degree(x,y);
  H/=60;
  j=parseInt(H);
  f=H-j;
  V=255;

  a1=V*(1-f);
  b1=V*(1-(1-f));
  switch(j)
  {
    case 0: R = V; G = b1; B = 0;break;      
    case 1: R = a1; G = V; B = 0;break;      
    case 2: R = 0; G = V; B = b1;break;      
    case 3: R = 0; G = a1; B = V;break;      
    case 4: R = b1; G = 0; B = V;break;      
    case 5: R = V; G = 0; B = a1;break;
  }
  R=parseInt(R);
  G=parseInt(G);
  B=parseInt(B);
  //console.log(a1,b1,a2,b2)
  //console.log(H,j,R,G,B)

  return "rgb("+ parseInt(R) +","+ parseInt(G) +","+ parseInt(B) +")"
}
var direction = mainsvg.selectAll(".direction")
                .data(allpoints)
                .enter()
                .append("circle")
                .attr("class", "direction");

    direction.attr({cx: function(d){ return d.orientationx*R(d.frames*0.5+400)+center.x;}, 
                 cy: function(d){return -d.orientationy*R(d.frames*0.5+400)+center.y;}, 
                 r:function(d){return 3}, 
                 opacity: 0.2, 
                 fill:function(d){
                  return RGB(d.orientationx,d.orientationy);
                }
                });
var rect2=mainsvg
            .append("rect").attr("class", "framework") 
            .attr("x", angleleft)
            .attr("y",2)
            .attr("width", 500)
            .attr("height", 500)
            .attr("fill", "none")
            .attr("stroke", "blue");

////////////////////////////////////////////////

var linediv = d3.select("#shortline").append("div") 
    .attr("class", "tooltip")       
    .style("opacity", 0);
var linesvg = d3.select("#shortline").append("svg")
    .attr("width", margin.left +2+linexScale(50))
    .attr("height", (height + margin.top + margin.bottom)*2+100)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var rect=linesvg
            .append("rect").attr("class", "framework") 
            .attr("x",0)
            .attr("y",0)
            .attr("width", linexScale(50))
            .attr("height", 1000)
            .attr("fill", "none");
//console.log(allpoints)
var lastframe=[];
var currentframe=[];
var sortallpoints=[];
//resort allpoints: 让出现过的点按之前出现的顺序排在前面
for (var i = 0; i < allpoints.length; i++) {
  if (currentframe.length==0||currentframe[currentframe.length-1].frames==allpoints[i].frames) {
    
    currentframe.push(allpoints[i]);
    //console.log(i,currentframe,lastframe)
  } 
  else{
    var sameblobs=[];
    var sortcurrentframe=[];
    //console.log(lastframe,currentframe);
    if (lastframe.length!=0) {
      for (var w = 0; w < currentframe.length; w++) {
        
        for (var j = 0; j < lastframe.length; j++) {

          if(lastframe[j].blobs==currentframe[w].blobs)
          {
            sortcurrentframe.push(currentframe[w]);
            sortallpoints.push(currentframe[w]);
            sameblobs.push(currentframe[w]);
            break;
          }
        };
          
        
      };
      
      for (var j = 0; j < currentframe.length; j++) {
        var samebool=0;
        for (var w = 0; w < sameblobs.length; w++) {
          if(sameblobs[w].blobs==currentframe[j].blobs) samebool=1;
        };
        if(samebool==0)
        {
          sortcurrentframe.push(currentframe[j]);
          sortallpoints.push(currentframe[j]);
        }
      };
    }else sortcurrentframe=currentframe;
    lastframe=sortcurrentframe;
    currentframe=[];
    currentframe.push(allpoints[i]);
    //console.log(i,currentframe,lastframe)
  };
  

};
//console.log(sortallpoints);
var lastf=[];
var currentf=[];
var lines=linesvg.selectAll(".lines")
.data(sortallpoints).enter().append('line').attr("class","lines")

var lock=0;
lines
  .attr({
    x1: function(d){return linexScale(d.frames)}, 
    y1: function(d){

      
      if(currentf.length==0)
      {
          d.yy=135;
          //console.log(d.yy);
          currentf.push(d);
          //console.log(currentf)
      }
      else if(currentf[currentf.length-1].frames==d.frames)
      {
          
          lock=0;
          for (var i = 0; i < lastf.length; i++) {
            if(lastf[i].blobs==d.blobs) 
            {
              lock=1;//上一针有相同目标
              d.yy=lastf[i].yy;
            }
            //another check here
          };
          if(lock==0)
          {
              var destination=135;
              var tmp=[];
              for (var i = 0; i < currentf.length; i++) {
                  tmp.push(currentf[i].yy);
                  //console.log(currentf[i].yy,tmp[i]);
              };
              for (var i = 0; i < lastf.length; i++) {
                  tmp.push(lastf[i].yy);
                  //console.log(currentf[i].yy,tmp[i]);
              };
              //console.log("-----------------------------");
              while(tmp.indexOf(destination)!=-1)
              {//console.log(destination);
                destination+=70;
              }
              d.yy=destination;
          }
          var tmp=[];
              for (var i = 0; i < currentf.length; i++) {
                  tmp.push(currentf[i].yy);
                  //console.log(currentf[i].yy,tmp[i]);
              };
          //console.log("~~~",d.yy);
          currentf.push(d);
      }
      else
      {
          
          lastf=currentf;
          currentf=[];
          d.yy=135;
          //console.log(d.yy);
          currentf.push(d);
      };
      //console.log(lastf)
      return d.yy

    },
    x2: function(d){return linexScale(d.frames+1)},
    y2: function(d){

       

      return d.yy


    }
  }) 
  .attr("stroke-width",function(d){
    if(d.velocity<50) return d.velocity;
    else return 50;
  })
  .attr("opacity","1") 
  .attr("stroke",function(d){return RGB(d.orientationx,d.orientationy);})
  .on("mouseover", function(d,i) {    
            linediv.transition()    
                .duration(200)    
                .style("opacity", .9);
            var X= parseInt((d3.event.pageX-57)/890*framenum)
            linediv.html(d.frames + "<br/>" +d.velocity+ "<br/>" +d.blobs)  
                .style("left", (d3.event.pageX) + "px")   
                .style("top", (d3.event.pageY - 28) + "px");  
            videodiv.transition()    
                .duration(200)    
                .style("opacity", .9);
            videodiv.html("id" + "<br/>" +d.blobs)  
                .style("left", (pointss[d.blobs][d.frames].x+50) + "px")   
                .style("top", (pointss[d.blobs][d.frames].y) + "px"); 
            });
 var coverrect=linesvg
            .append("rect")
            .attr("x",0)
            .attr("y",0)
            .attr("width", margin.left-1)
            .attr("height", 1000)
            .attr("fill", "#272822")
            .attr("stroke", "none")
            .attr("transform", "translate(" + (-margin.left) + "," + 0 + ")");; 

</script>


</body>
</html>
