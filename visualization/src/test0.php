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
  <title>dragit example with a single circle</title>
  <link href="./src/dragit.css" rel="stylesheet"/>
  <script src="./src/d3.v3.js"></script>
  <script src="./src/dragit.js"></script>
</head>
<body>
<div id="viz"></div>
<p style="clear:both"></p>
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
<script>
// initial parameters
var track = {
  blobs : [],
  points : [],
  x : [],
  y: [],
  velocity : [],
  orientationx : [],
  orientationy: []
  };
</script>
<!-- extract tracking data-->
  <?php
$sql = "SELECT blobs,points,x,y,velocity,orientationx,orientationy from trackingdata";
$result = $link->query($sql);if ($result->num_rows <= 0) {
       echo "0 results";
     }   
while($rows = mysqli_fetch_assoc($result))
  {
    ?>
  <script>

    track.blobs.push(<?php echo($rows["blobs"]); ?>);
    track.points.push(<?php echo($rows["points"]); ?>);
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
var margin = {top: 20, right: 20, bottom: 20, left: 20},
    width = 900 - margin.right - margin.left,
    height = 500 - margin.top - margin.bottom;
var time_steps = 20, nb_points = 2856, current_time = Math.floor(Math.random()*time_steps);
var timecube = d3.range(nb_points).map(function(d, i) {
  return d3.range(time_steps).map(function(e, j) { 
    return {x: j, y: Math.random(), t: j};
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
                           .attr({width: width, height:height})
var points = svg.selectAll(".points")
                .data(timecube)
                .enter()
                .append("circle")
                .attr("class", "points");
// All other focus points
console.log(track.x);
points.attr({cx: function(d,i) { return track.x[i]}, 
             cy: function(d,i) { return track.y[i]}, 
             r:10, 
             fill:"red"
            })
      .on("mouseenter", dragit.trajectory.display)
      .on("mouseleave", dragit.trajectory.remove)
      .call(dragit.object.activate)
// Time update callback function
function update(v, t) {
  dragit.time.current = v || dragit.time.current;
  points.transition().duration(100)
        .attr({cx: function(d) { return xScale(d[dragit.time.current].x)}, 
               cy: function(d) { return yScale(d[dragit.time.current].y)}
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