function createStackedMultiBarChart(
			w,h,
			xaxislabel,yaxislabel,
			rotatex,xaxislabeldistance,yaxislabeldistance,
			margin_top,margin_right,margin_bottom,margin_left,
			group_spacing,
			input,chart_id
		) {

//Input data looks like this:
//	var data = [{
//	  "values" : [
//	              {"y" : 195,"x" : "Montag"}, 
//	              {"y" : 187,"x" : "Dienstag"},
//	              {"y" : 179,"x" : "Mittwoch"},
//	              {"y" : 172,"x" : "Donnerstag"},
//	              ],
//	  "key" : "OP 11"
//	  }, {
//	  "values" : [
//				  {"y" : 195,"x" : "Montag"}, 
//				  {"y" : 187,"x" : "Dienstag"},
//				  {"y" : 179,"x" : "Mittwoch"},
//				  {"y" : 172,"x" : "Donnerstag"},
//	             ],
//	  "key" : "OP 14"
//	  }];

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
	            var chart = nv.models.multiBarChart()
	            //.transitionDuration(350)
	            .reduceXTicks(false)   //If 'false', every single x-axis tick label will be rendered.
	            .rotateLabels(rotatex)      //Angle to rotate x-axis labels.
	            .showControls(true)   //Allow user to switch between 'Grouped' and 'Stacked' mode.
	            .groupSpacing(group_spacing)    //Distance between each group of bars.
	            .margin({top: margin_top, right: margin_right, bottom: margin_bottom, left: margin_left})
	            .clipEdge(true)
	            .stacked(true)
	            ;
	            
//	            chart.xAxis
//	            .tickFormat(d3.format(',f'));
	            	            
	            chart.xAxis//Oli
	            .axisLabel(xaxislabel)
	            .axisLabelDistance(xaxislabeldistance);
	            ;
	            
	            
	            chart.yAxis
	            .tickFormat(d3.format(',.1f'))
	            .axisLabel(yaxislabel)//Oli
	            .axisLabelDistance(yaxislabeldistance);
	            ;
	            	            
	    		d3.select('#' + chart_id + ' ' +'svg')
	    		.datum(input)
	            .call(chart);
	            
	            nv.utils.windowResize(chart.update);
	            
	            return chart;
	            });
}
