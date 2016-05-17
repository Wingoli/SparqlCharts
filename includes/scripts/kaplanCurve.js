function createKaplanCurve (w, h, color, input, chart_id)	{	
	
input = [[3,1],[4,0],[7,1],[6,1],[9,1],[10,1],[10,0],[15,1],[7,1],[21,1],[21,1],[24,0],[40,1]];

var censored = [];
input = kaplan(input);

if (input == null){
	document.write("No Data Error");
}
	
function getValues()	{
	var val = [];
	for (var i = 0; i < input.length; i++)	{
		if (input[i] != 0)	{
			val.push({x: i, y: input[i]});
			if ((input[i+1] < input[i])&&(input[i+1] != 0))	{
				val.push({x: i, y: input[i+1]});
			} else if ((input[i+1] < input[i])&&(input[i+1] == 0))	{
				val.push({x: i, y: input[i+1]});
			}
		}	
	}
	
	return [
	      {
	    	values: val,
	      	key: "Kaplan-Meier Kurve",
	      	color: color
//	      	, area: true			//Zum Faerben Kommentar entfernen
	      }  
	];
}

var data = getValues();

//'<div id="chart5"></div>'
d3.select('#mw-content-text')
.append('div')
.attr("id",chart_id)
;

var svg = d3.select('#'+chart_id)
.append('svg')
.attr("width", w)
.attr("height", h);
;

nv.addGraph(function() {
	
		  var chart = nv.models.lineChart()
            .useInteractiveGuideline(true)
            .width(w)
            .height(h)
		    ;
		  
		  chart.xAxis
		    .axisLabel("Lebensdauer")
		    .ticks(5)
		    .tickFormat(d3.format(",r"))
		    ;

		  chart.yAxis
		    .axisLabel("Anteil der Population")
		    .tickFormat(d3.format(".2f"))
		    ;

		  d3.select("#"+chart_id+" svg")
		  	.datum(data)
		    .call(chart)
		    .attr('width', w).attr('height', h)
		    ;

		  nv.utils.windowResize(chart.update);
		 
		  return chart;
});

}
