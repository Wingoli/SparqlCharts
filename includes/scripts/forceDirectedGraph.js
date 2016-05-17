function createForceDirectedGraph(width, height, input, chart_id)	{
	
	var charge = -400;
	var margin = 20;
	var pad = margin/2;

	var color = d3.scale.category20();
	
	//'<div id="chart5"></div>'
	d3.select('#mw-content-text')
	.append('div')
	.attr("id",chart_id)
	;
	
	drawGraph(input);
	
	// Generates a tooltip for a SVG circle element based on its ID
	function addTooltip(circle) {
	    var x = parseFloat(circle.attr("cx"));
	    var y = parseFloat(circle.attr("cy"));
	    var r = parseFloat(circle.attr("r"));
	    var text = circle.attr("id");

	    var tooltip = d3.select("#plot")
	        .append("text")
	        .text(text)
	        .attr("x", x)
	        .attr("y", y)
	        .attr("dy", -r * 2)
	        .attr("id", "graphtooltip");

	    var offset = tooltip.node().getBBox().width / 2;

	    if ((x - offset) < 0) {
	        tooltip.attr("text-anchor", "start");
	        tooltip.attr("dx", -r);
	    }
	    else if ((x + offset) > (width - margin)) {
	        tooltip.attr("text-anchor", "end");
	        tooltip.attr("dx", r);
	    }
	    else {
	        tooltip.attr("text-anchor", "middle");
	        tooltip.attr("dx", 0);
	    }
	}

	function drawGraph(graph) {

	    var svg = d3.select("#"+chart_id).append("svg")
	        .attr("width", width)
	        .attr("height", height)
	        ;

	    // draw plot background
	    svg.append("rect")
	        .attr("width", width)
	        .attr("height", height)
	        .style("fill", "white");

	    // create an area within svg for plotting graph
	    var plot = svg.append("g")
	        .attr("id", "plot")
	        .attr("transform", "translate(" + pad + ", " + pad + ")");

	    // https://github.com/mbostock/d3/wiki/Force-Layout#wiki-force
	    var layout = d3.layout.force()
	        .size([width - margin, height - margin])
	        .charge(charge)
	        .theta(0,8)
	        .linkDistance(function(d, i) {
	            return (d.source.group == d.target.group) ? 50 : 100;
	        })
	        .nodes(graph.nodes)
	        .links(graph.links)
	        .start();

	    drawLinks(graph.links);
	    drawNodes(graph.nodes);

	    // add ability to drag and update layout
	    // https://github.com/mbostock/d3/wiki/Force-Layout#wiki-drag
	    d3.selectAll(".node").call(layout.drag);

	    // https://github.com/mbostock/d3/wiki/Force-Layout#wiki-on
	    layout.on("tick", function() {
	        d3.selectAll(".link")
	            .attr("x1", function(d) { return d.source.x; })
	            .attr("y1", function(d) { return d.source.y; })
	            .attr("x2", function(d) { return d.target.x; })
	            .attr("y2", function(d) { return d.target.y; });

	        d3.selectAll(".node")
	            .attr("cx", function(d) { return d.x; })
	            .attr("cy", function(d) { return d.y; });
	        
	        //Oliver Lutzi
	        d3.selectAll(".text")
	        	.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; })
            	;
	    });
	}

	// Draws nodes on plot
	function drawNodes(nodes) {
	    // used to assign nodes color by group
	    var color = d3.scale.category10();

	    // https://github.com/mbostock/d3/wiki/Force-Layout#wiki-nodes
	    d3.select("#plot").selectAll(".node")
	        .data(nodes)
	        .enter()
	        .append("circle")
	        .attr("class", "node")
	        .attr("id", function(d, i) { return d.name; })
	        .attr("cx", function(d, i) { return d.x; })
	        .attr("cy", function(d, i) { return d.y; })
	        .attr("r",  function(d, i) { return 15; })
	        .style("fill",   function(d, i) { return color(d.group); })
	        .on("mouseover", function(d, i) { addTooltip(d3.select(this)); })
	        .on("mouseout",  function(d, i) { d3.select("#graphtooltip").remove(); });
	     	    
	    //Oliver Lutzi
	    var text = d3.select("#plot").selectAll(".text")
	    	.data(nodes)
	        .enter()
	        .append("text")
	        .attr("class", "text")
	        .attr("id", function(d, i) { return d.name; })
	        .style("font-size", 20 + "px")
	        .text(function(d) { return d.name.substring(0,3); })
//	    	.style("text-anchor", "middle")
//	        .style("opacity", 0.5)
	        .style("fill", "black")
	        .style("font-weight", "bold")
	        .style("stroke", "white")
	        .style("stroke-width", 1)
	    	;
	    

	}

	// Draws edges between nodes
	function drawLinks(links) {
	    var scale = d3.scale.linear()
	        .domain(d3.extent(links, function(d, i) {
	           return d.value;
	        }))
	        .range([1, 6]);

	    // https://github.com/mbostock/d3/wiki/Force-Layout#wiki-links
	    d3.select("#plot").selectAll(".link")
	        .data(links)
	        .enter()
	        .append("line")
	        .attr("class", "link")
	        .attr("x1", function(d) { return d.source.x; })
	        .attr("y1", function(d) { return d.source.y; })
	        .attr("x2", function(d) { return d.target.x; })
	        .attr("y2", function(d) { return d.target.y; })
	        .style("stroke-width", function(d, i) {
	            return scale(d.value) + "px";
	        })
	        .style("stroke-dasharray", function(d, i) {
	            return (d.value <= 1) ? "2, 2" : "none";
	        });

	}

}