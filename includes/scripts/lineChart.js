function createLineChart(
			w, h, color, 
			xaxislabel, yaxislabel, 
			margin_top,margin_right,margin_bottom,margin_left,
			input, chart_id)	{	
		
		function getValues()	{
			var val = [];
			for (var i = 0; i < input.length; i++)	{
				val.push({x: parseInt(input[i][0]), y: parseInt(input[i][1])});
			}
			return [
			      {
			    	values: val,
			      	key: yaxislabel,
			      	color: color
			      }  
			];
		}
		
		//'<div id="chart5"></div>'
		d3.select('#mw-content-text')
		.append('div')
		.attr("id",chart_id)
		;
		
		var svg = d3.select('#'+chart_id)
		.append('svg')
		.attr("id",chart_id + ' svg')
		;
		
		nv.addGraph(function() {

			var chart = nv.models.lineChart()
						.useInteractiveGuideline(true)  //We want nice looking tooltips and a guideline!
						.width(w).height(h)
						.margin({top: margin_top, right: margin_right, bottom: margin_bottom, left: margin_left}) // Oli
						;
			
			chart.xAxis
			.axisLabel(xaxislabel)
//			.tickFormat(d3.format(",r"))
			;
			
			chart.yAxis
			.axisLabel(yaxislabel)
			.tickFormat(d3.format(".02f"))
			;
			
			d3.select('#'+chart_id+' svg')
			.attr()
			.datum(getValues())
			.transition().duration(500)
			.call(chart)
			;
			
			document.getElementById(chart_id + ' svg').style.width = w + "px";
			document.getElementById(chart_id + ' svg').style.height = h + "px";
			
			nv.utils.windowResize(chart.update);
			
			return chart;
		});
	}