<!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <script src="https://unpkg.com/d3@3.5.5/d3.min.js"></script>
  <style>
    body { margin:0;position:fixed;top:0;right:0;bottom:0;left:0; }
    
    .axis path, line {
      fill: none;
      stroke: black;
    }
    
    .brush .background {
      stroke: none;
      fill: none;
    }
    
    .brush .extent {
      stroke: #fff;
      fill-opacity: .125;
      shape-rendering: crispEdges;
    }
    
    .brush text {
      font-size: 0.75em;
    }
  </style>
</head>

<body>
  <script>
    // Feel free to change or delete any of the code you see in this editor!
    var svg = d3.select("body").append("svg")
      .attr("width", 960)
      .attr("height", 500)

    var yScale = d3.scale.linear().domain([0,1000]).range([50,250])
    
    var dimension = svg.append("g")
      .attr("transform", "translate(200,0)")
    
    dimension.append("g")
      .classed("axis axis-y", true)
      .call(d3.svg.axis().scale(yScale).orient("right"));
    
    var brush = d3.svg.brush()
      .y(yScale);
    
    var brushg = dimension.append("g")
      .classed("brush", true)
      .call(brush);
    
    brushg.selectAll("rect")
      .style("visibility", null)
      .attr("x", -10)
      .attr("width", 200);
    
    brushg.selectAll(".resize rect")
      .attr("height", 3);
  

  </script>
</body>