function createBarChart(width, height, color, xaxislabel, yaxislabel, input, tooltip, chart_id)	{
		
var barPadding=5;
var chartPadding = 50;	
var transition = "bounce";
var duration = 1100;
var delay = 50;

/* Setting the biggest Value of input as upper limit for scales */
var no = input.length;

var biggest = 0;
for (var i = 0; i < no; i++)	{
	if (parseInt(input[i][1]) > biggest)	{
		biggest = parseInt(input[i][1]);
	}
}

/* adding space for large y-axis numbers */
for (var i = 2; i < 5; i++)	{
	var hundred = 10^(i);
	if (biggest >= height)	{
		chartPadding = chartPadding + 7;
	}
}

/* Defining Scales */
var xScale = d3.scale.linear()
						.domain([0, no])
						.range([chartPadding, width - chartPadding]);
		
var yScale = d3.scale.linear()
				.domain([0, biggest])
				.range([chartPadding/2, height - chartPadding/2]);

var yAxisScale = d3.scale.linear()
				.domain([0, biggest])
				.range([height-chartPadding/2, chartPadding/2]);

//'<div id="chart5"></div>'
d3.select('#mw-content-text')
.append('div')
.attr("id",chart_id)
;

//'<div id="tooltip_chart5" class="hidden"></div>'
d3.select('#'+chart_id)
	.append('div')
	.attr("id","bartooltip_" + chart_id)
	.attr("class","hidden")
	.attr("class", "bartooltip")
	.classed("hidden", true)
	;

//if(tooltip != null)	{
if(tooltip.length > 0)	{
for (var i = 0; i < tooltip.length; i++){
	//<p><span id="date"></span></p>
	d3.select("#bartooltip_" + chart_id)
		.append("p")
		.text(tooltip[i] + ": ")
		.append("strong")
		.attr("id","tooltip_"+i+"_"+chart_id)
		;
}
}

var svg = d3.select('#'+chart_id)
.append('svg')
.attr("width", width)
.attr("height", height);
;

/* x-axis labels */
var text = svg.selectAll("text")
				.data(input)
				.enter()
				.append("text");
					
var xAxis = text.attr("x", function(d, i)	{
						return ((width-2*chartPadding)/input.length - barPadding)/2 + chartPadding + (i*(width - 2*chartPadding)/input.length);
					})
					.attr("y", height - chartPadding/3)
					.text(function(d)	{
						return d[0];
					})
					.attr("font-family", "sans-serif")
					.attr("font-size", "12px")
					.attr("text-anchor", "middle")
					;

/* calculate offsets for tooltip position */
function getPosition(element) {
    var xPosition = 0;
    var yPosition = 0;
  
    while(element) {
        xPosition += (element.offsetLeft - element.scrollLeft + element.clientLeft);
        yPosition += (element.offsetTop - element.scrollTop + element.clientTop);
        element = element.offsetParent;
    }
    return { x: xPosition, y: yPosition };
}

/* Append Bars and set Attributes */
svg.selectAll("rect")
	.data(input)
	.enter()
	.append("rect")
	/* Position on x-Axis */
	.attr("x",function(d, i)	{
		return chartPadding + (i*(width - 2*chartPadding)/input.length);
	})
	/* Position on y-Axis */
	.attr("y", function(d)	{
		return (height - chartPadding/2);
	})
	/* Width of the Bars */
	.attr("width", (width-2*chartPadding)/input.length - barPadding)
	/* Color of the Bars */
	.attr("fill", function(d)	{
		return color;
	})
	/* Heigth of Bars before transition - should be 0 */
	.attr("height", function(d)	{
		return 0;
	})

	/* Tooltip on mouseover */
	.on("mouseover", function(d){
		
		var offsetTop = getPosition(document.getElementById(chart_id)).y;
		var offsetLeft = getPosition(document.getElementById(chart_id)).x;
		offsetTop -= getPosition(document.getElementById("mw-content-text")).y;
		offsetLeft -= getPosition(document.getElementById("mw-content-text")).x;
		
		var yPos = offsetTop + parseInt(d3.select(this).attr("y"))*(3/4);
		var xPos = offsetLeft + parseInt(d3.select(this).attr("x")) + ((width - 2*chartPadding)/no) - 10;

		d3.select("#bartooltip_" + chart_id)
			.style("left", xPos + "px")
			.style("top", yPos + "px")
			.select("#date_"+chart_id)
			.text(d[0]);

//			d3.select("#value_"+chart_id)
//			.text(d[1]);
			
			//if (tooltip != null)	{
			if (tooltip.length > 0)	{
			for (var i = 0; i < tooltip.length; i++){
			d3.select("#tooltip_"+i+"_"+chart_id)
			.text(d[2+i]);
			}
			}
			
			d3.select("#bartooltip_" + chart_id).classed("hidden", false);
	})
	.on("mouseout", function(d){
		d3.select("#bartooltip_" + chart_id).classed("hidden", true);
	})
	/* Transition */
	.transition()
	.duration(duration)
	.delay(delay)
	.ease(transition)
	.attr("y", function(d)	{
		return height - yScale(d[1]);
	})
	.attr("height", function(d)	{
		return yScale(d[1]) - chartPadding/2;
	});

/* Append y-Axis on both sides */
var yAxis1 = d3.svg.axis()
				.scale(yAxisScale)
				.orient("left")
				.ticks(5);

svg.append("g")
	.attr("class", "axis")
	.attr("transform", "translate(" + chartPadding/(3/2) + ",0)")
	.call(yAxis1);

var yAxis2 = d3.svg.axis()
				.scale(yAxisScale)
				.orient("right")
				.ticks(5)
				;
						
svg.append("g")
	.attr("class", "axis")
	.attr("transform", "translate(" + (width - chartPadding/(3/2)) + ",0)")
	.call(yAxis2);
    

/*add axis labels*/
svg.append("text")
.attr("x", (width-chartPadding/2)/2)
.attr("y", height-(chartPadding/6))
.style("font-size", "10px")
.text(xaxislabel);

svg.append("text")
.attr("x", chartPadding/8)
.attr("y", height/2)
.attr("transform", "rotate(270 " + (chartPadding/8) + "," + height/2 + ")")
.style("font-size", "10px")
.text(yaxislabel);

//d3.select('#mw-content-text').append('div').text(d3.select("#mw-content-text").html());
//alert(d3.select("#mw-content-text").html());

}