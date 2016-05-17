function createStackedAreaChart(
			w, h, 
			margin_top,margin_right,margin_bottom,margin_left,
			input, chart_id)	{	

	var data = input;
	
	function getValues()	{
		var val = [];
		for (var i = 0; i < input.length; i++)	{
			val.push({x: i, y: input[i][2]});
		}
		return [
		      {
		    	values: val,
		      	key: "Anzahl der Blabla",
		      	color: color
		      }  
		];
	}

	/* Create SVG element */
	d3.select('#mw-content-text')
	.append('div')
	.attr("id", chart_id)
	;
	
	d3.select('#' + chart_id)
	.append('svg')
	.attr("width", w)
	.attr("height", h);
	;
		
	nv.addGraph(function() {
		var chart = nv.models.stackedAreaChart()
	            	  .margin({top: margin_top, right: margin_right, bottom: margin_bottom, left: margin_left}) // Oli
	                  .x(function(d) { return d[0] })   //We can modify the data accessor functions...
		              .y(function(d) { return d[1] })   //...in case your data is formatted differently.
		              .useInteractiveGuideline(true)    //Tooltips which show all data points. Very nice!
		              .rightAlignYAxis(true)      //Let's move the y-axis to the right side.
		              
//		              .transitionDuration(1000) // --> if this is not commented out, nothings works..?
		              
		              .showControls(true)       //Allow user to choose 'Stacked', 'Stream', 'Expanded' mode.
		              .clipEdge(true)
		              //.width(w)
					  //.height(h)
					  ;
	
		//Format x-axis labels with custom function.
//		chart.xAxis
//		.tickFormat(function(d) { 
//		return d3.time.format('%x')(new Date(d)) 
//		});
		
		
		chart.yAxis
		.tickFormat(d3.format(',.2f'))
		;

		d3.select('#' + chart_id + ' ' +'svg')
//		.attr("width", w)
//		.attr("height", h)
		.datum(data)
		.call(chart)
		;
		
		nv.utils.windowResize(chart.update);
		
		return chart;
	});
}
