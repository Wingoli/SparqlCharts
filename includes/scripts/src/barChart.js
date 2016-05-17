var w=window["width_"+chart_id];						
var h=window["height_"+chart_id];
var barPadding=5;
var chartPadding = 40;	
var color = window["color_"+chart_id];
var transition = "bounce";
var duration = 1100;
var delay = 50;

/* Setting the biggest Value of input as upper limit for scales */
//var no = input.length;
var no = window["input_"+chart_id].length;

var biggest = 0;
for (var i = 0; i < no; i++)	{
	//if (parseInt(input[i][2]) > biggest)	{
	if (parseInt(window["input_"+chart_id][i][2]) > biggest)	{
		//biggest = parseInt(input[i][2]);
		biggest = parseInt(window["input_"+chart_id][i][2]);
	}
}

/* adding space for large y-axis numbers */
for (var i = 2; i < 5; i++)	{
	var hundred = 10^(i);
	if (biggest >= h)	{
		chartPadding = chartPadding + 7;
	}
}

/* Defining Scales */
var xScale = d3.scale.linear()
						.domain([0, no])
						.range([chartPadding, w - chartPadding]);
		
var yScale = d3.scale.linear()
				.domain([0, biggest])
				.range([chartPadding/2, h - chartPadding/2]);

var yAxisScale = d3.scale.linear()
				.domain([0, biggest])
				.range([h-chartPadding/2, chartPadding/2]);

//'<div id="chart5"></div>'
d3.select('#mw-content-text')
.append('div')
.attr("id",chart_id)
;

//'<div id="tooltip_chart5" class="hidden"></div>'
d3.select('#'+chart_id)
	.append('div')
	.attr("id","bartooltip")
	.attr("class","hidden")
	;

//<p>Anzahl: <strong id="value"></strong></p>
d3.select("#bartooltip")
	.append("p")
	.text("Anzahl: ")
	.append("strong")
	.attr("id","value_"+chart_id)
	;

//<p><span id="date"></span></p>
d3.select("#bartooltip")
	.append("p")
	.append("span")
	.attr("id","date_"+chart_id)
	;

var svg = d3.select('#'+chart_id)
.append('svg')
.attr("width", w)
.attr("height", h);
;

/* x-axis labels */
var text = svg.selectAll("text")
				//.data(input)
				.data(window["input_"+chart_id])
				.enter()
				.append("text");
				
var xAxis = text.attr("x", function(d, i)	{
						//return ((w-2*chartPadding)/input.length - barPadding)/2 + chartPadding + (i*(w - 2*chartPadding)/input.length);
						return ((w-2*chartPadding)/window["input_"+chart_id].length - barPadding)/2 + chartPadding + (i*(w - 2*chartPadding)/window["input_"+chart_id].length);
					})
					.attr("y", h - chartPadding/5)
					.text(function(d)	{
						return d[1];
					})
					.attr("font-family", "sans-serif")
					.attr("font-size", "12px")
					.attr("text-anchor", "middle")
					;


/* Append Bars and set Attributes */
svg.selectAll("rect")
	//.data(input)
	.data(window["input_"+chart_id])
	.enter()
	.append("rect")
	/* Position on x-Axis */
	.attr("x",function(d, i)	{
		//return chartPadding + (i*(w - 2*chartPadding)/input.length);
		return chartPadding + (i*(w - 2*chartPadding)/window["input_"+chart_id].length);
	})
	/* Position on y-Axis */
	.attr("y", function(d)	{
		return (h - chartPadding/2);
	})
	/* Width of the Bars */
	//.attr("width", (w-2*chartPadding)/input.length - barPadding)
	.attr("width", (w-2*chartPadding)/window["input_"+chart_id].length - barPadding)
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
		var xPos = parseInt(d3.select(this).attr("x")) + ((w - 2*chartPadding)/no) - 10;
		var yPos = parseInt(d3.select(this).attr("y"))*(3/4);
		d3.select("#bartooltip")
			.style("left", xPos + "px")
			.style("top", yPos + "px")
			.select("#date_"+chart_id)
			.text(d[1]);

			d3.select("#value_"+chart_id)
			.text(d[2]);
		d3.select("#bartooltip").classed("hidden", false);
	})
	.on("mouseout", function(d){
		d3.select("#bartooltip").classed("hidden", true);
	})
	/* Transition */
	.transition()
	.duration(duration)
	.delay(delay)
	.ease(transition)
	.attr("y", function(d)	{
		return h - yScale(d[2]);
	})
	.attr("height", function(d)	{
		return yScale(d[2]) - chartPadding/2;
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
	.attr("transform", "translate(" + (w - chartPadding/(3/2)) + ",0)")
	.call(yAxis2);
	

//alert(d3.select("#mw-content-text").html());