function createPieChart(
			w, h,  
			showlabels,	labeltype, valueformat, labelthreshold, labelsoutside, sunbeamlayout,
			donut, donuttitle, spacingbetweenslices,
			input, chart_id)	{

	var shares = [];
	for (var j = 0; j < input.length; j++)	{
		shares.push([input[j][0], input[j][1]]);
	}

	function getValues()	{
		var erg = [];
		for (var i = 0; i < shares.length; i++)	{	
		erg.push(
		      {
		    	"label": shares[i][0],
		      	"value": parseInt(shares[i][1])
		      }
		);
		
		}
		return erg;
	}

	var data = getValues();		

	d3.select('#mw-content-text')
		.append('div')
		.attr("id",chart_id)
		;

	var svg = d3.select('#'+chart_id)
		.append('svg')
		.attr("id",chart_id + ' svg')
		.classed("pie", true)
		;

	nv.addGraph(function() {
		
		  document.getElementById(chart_id + ' svg').style.width = w + "px";
		  document.getElementById(chart_id + ' svg').style.height = h + "px";	
		
	  var chart = nv.models.pieChart()

	      .x(function(d) { return d.label })
	      .y(function(d) { return d.value })

	      //.width(w).height(h)
	      .showLabels(showlabels)
	      .labelType(labeltype) //"key", "value", "percent"
	       .valueFormat(d3.format(valueformat)) //",.0f" -> only relevant when labeltype==value
          .labelThreshold(labelthreshold)
	      .pieLabelsOutside(labelsoutside)
	      .labelSunbeamLayout(sunbeamlayout)

	      .donut(donut)
	      .title(donuttitle)
	      .padAngle(spacingbetweenslices) //for donut charts only
	      ;
	  	  
	    d3.select('#' + chart_id +' svg')
	    	.attr()
	        .datum(data)
	        .transition().duration(2200)
	        .call(chart)
	        ;
	    nv.utils.windowResize(chart.update);

	  return chart;
	});

}
