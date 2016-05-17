<?php
/**
 * SparqlChartsOutputFormat.php for SparqlCharts extension
 * @author Oliver Lutzi
 *
 */
		
class SparqlChartsOutputFormat {
	protected $chart_id;
	protected $args;
	protected $data;
	protected $chartType;
	protected $defaultParametersArray;
	protected $nameOfJavaScriptFunction;
	protected $tooltip_switch;	
	
	public static $DEFAULT_FORMAT = "table"; // default format
	public static $chart_id_counter = 0;

	//constructor
	function __construct($args, $defaultParametersArray, $chartType, $nameOfJavaScriptFunction, $tooltip_switch) {		
		//create unique ID for every chart on page
		self::$chart_id_counter++; //self gives access to properties of class (not instance)
		$this->chart_id = "chart".strval(self::$chart_id_counter);
		$this->args = $args;
		$this->data = $this->getData();
		$this->chartType = $chartType;
		$this->defaultParametersArray = $defaultParametersArray;
		$this->nameOfJavaScriptFunction = $nameOfJavaScriptFunction;
		$this->tooltip_switch = $tooltip_switch;
		
	}//end __construct
	
	
	/*
	* ***********************************************************************************************
	* 
	* 		getData()
	* 
	* ***********************************************************************************************
	*/
	//get data into an array from csv or json endpoint response
	public function getData() {		
		global $wgScriptPath;
		global $IP;
		$data = false;
		$type = false;
		
		$type = "json";
// 		$type ="csv";
			
		$url = SparqlChartsUtil::getEndpointRequestUrlForType($type, $this->args);
		
		Log::log_this("host sent to endpoint", $this->args["host"]);
		Log::log_this("request url sent to endpoint", $url);
		
 	$query_output = SparqlChartsUtil::readQueryOutput($url, $this->args["host"], 80, $type);			
 	Log::log_this("query output", $query_output);
 			
		switch ($type) {
			case "json":
				$data = SparqlChartsUtil::parse_json($query_output);
				break;
			default:
				$data = SparqlChartsUtil::parse_csv($query_output);
		}
				
		Log::log_this("DATA array", $data);
		return $data;
	}

	
	/*
	 * ***********************************************************************************************
	 *
	 * 		getOutput()
	 *
	 * ***********************************************************************************************
	 */
	public function getOutput() {

		//creates: <script>var width_chart1=400; var height_chart1=300; var color_chart1="red";</script>
		$output .= Html::inlineScript(SparqlChartsUtil::create_js_param_vars($this->defaultParametersArray,$this->args,$this->chart_id));

		$data_mod;
		$temp;
		$temp = SparqlChartsUtil::get_data_mod($this->data, $this->args, $this->chartType);	//$temp[0] = manipulated result-array (so that x-axis = column1 and y-axis = column2)
		$data_mod = $temp[0];																//AND: tooltip-variable1 = column3, tooltip-variable2 = column4 and so on...
		$data_mod_columnNames = $temp[1];
		
		if($this->args["format"] == "boxplot") {
		//if($this->chartType == "boxplot") {
			$data_mod = SparqlChartsUtil::change_data_format_boxplot($data_mod, $data_mod_columnNames,$this->args);
		} else if($this->args["format"] == "stackedmultibarchart" || $this->args["format"] == "stackedareachart") {
			$data_mod = SparqlChartsUtil::change_data_format_stacked_3tuple_charts($data_mod, $this->args["format"]);
		} else if($this->args["format"] == "forcedirectedgraph") {
			$data_mod = SparqlChartsUtil::change_data_format_forceDirectedGraph($data_mod);
		}

		//tooltip_switch: on = true, off = false (if tooltips implemented in this format, then needs to be true)
		if($this->tooltip_switch) {
			$tooltip_labels = $temp[2];									//$temp[1] = labels for the tooltips. label_of_tooltip-variable1 = $tooltip_labels[0]
			//creates: <script>var tooltip_chart1 = [["2015-11-01T12:15:00+01:00", "2", ],...];</script>
			$output .= Html::inlineScript(SparqlChartsUtil::create_tooltip_label_var($tooltip_labels,$this->chart_id));
		}

		//creates: <script>var input_chart1 = [["2015-11-01T12:15:00+01:00", "2", ],...];</script>
// 		$output .= Html::inlineScript(call_user_func("SparqlChartsUtil::".$this->nameOfFunctionThatsGeneratesInputVar,$data_mod,$this->chart_id));		
		$output .= Html::inlineScript(SparqlChartsUtil::create_js_input_var($data_mod,$this->chart_id));
		
		//creates: <script>createBarChart(width_chart1,height_chart1,color_chart1,input_chart1,"chart1");</script>
		$output .= Html::inlineScript(SparqlChartsUtil::generate_createFunction_callString($this->nameOfJavaScriptFunction,$this->defaultParametersArray,$this->chart_id,$this->tooltip_switch));//erzeugt im html: <script>createBarChart(...)</script>
		
		Log::log_this("HTML-OUTPUT", $output);
		
		return $output;
	}	
}


/**
 * Simple Bar Chart format - (js-Library: d3.js)
 * | format=barchart
 * @author Oliver Lutzi
 * @package SparqlCharts
 */
class SparqlChartsBarChart extends SparqlChartsOutputFormat {
	
	public function __construct($args) {
		//"parameter-name" => array( [position of parameter in create-function], [default value for parameter] )
		$defaultParametersArray = array(
				"width" 		=> array(0,"600"),
				"height" 		=> array(1,"350"),
				"color" 		=> array(2,"steelblue"),
				"xaxislabel"	=> array(3,"x-axis"),
				"yaxislabel" 	=> array(4,"y-axis"),
		);
		parent::__construct(
				$args,						//$args contains the QUERY and all PARAMETERS
				$defaultParametersArray,
				"2-tuple",					//type of the chart: e.g. "2-tuple" (bar chart, line chart,...), "3-tuple" (multi-bar chart), "boxplot" (boxplot)
				"createBarChart",			//name of the JavaScript-function, that produces the diagram
				true						//tooltip-switch: if tooltips are implemented for this format -> true, else: false
				);
	}
}

/**
 * Simple Line Chart format (js-Library: NVD3)
 * | format=linechart
 * @author Oliver Lutzi
 * @package SparqlCharts
 */
class SparqlChartsLineChart extends SparqlChartsOutputFormat {

	public function __construct($args) {
		//"parameter-name" => array( [position of parameter in create-function], [default value for parameter] )
		$defaultParametersArray = array(
				"width" 		=> array(0,"600"),
				"height" 		=> array(1,"350"),
				"color" 		=> array(2,"steelblue"),
				"xaxislabel" 	=> array(3,"x-axis"),
				"yaxislabel" 	=> array(4,"y-axis"),
				"margintop" 	=> array(5,"10"),
				"marginright" 	=> array(6,"20"),
				"marginbottom" 	=> array(7,"80"),
				"marginleft" 	=> array(8,"80"),
		);
		parent::__construct(
				$args,						//$args contains the QUERY and all PARAMETERS
				$defaultParametersArray,
				"2-tuple",					//type of the chart: e.g. "2-tuple" (bar chart, line chart,...), "3-tuple" (multi-bar chart), "boxplot" (boxplot)
				"createLineChart",			//name of the JavaScript-function, that produces the diagram
				false						//tooltip-switch: if tooltips are implemented for this format -> true, else: false
				);
	}
}


/**
 * Pie/Donut Chart format (js-Library: NVD3)
 * | format=piechart
 * @author Oliver Lutzi
 * @package SparqlCharts
 */
class SparqlChartsPieChart extends SparqlChartsOutputFormat {
	
	public function __construct($args) {
		//"parameter-name" => array( [position of parameter in create-function], [default value for parameter] )
		$defaultParametersArray = array(
				"width" 				=> array(0,"500"),
				"height" 				=> array(1,"500"),
				"showlabels" 			=> array(2,true),
				"labeltype" 			=> array(3,"percent"),
				"valueformat"			=> array(4,",.0f"),
				"labelthreshold" 		=> array(5,0.1),
				"labelsoutside"	 		=> array(6,false),
				"sunbeamlayout"			=> array(7,false),
				"donut" 				=> array(8,false),
				"donuttitle"			=> array(9,""),
				"spacingbetweenslices" 	=> array(10,0),
		);
		parent::__construct(
				$args,						//$args contains the QUERY and all PARAMETERS
				$defaultParametersArray,
				"2-tuple",					//type of the chart: e.g. "2-tuple" (bar chart, line chart,...), "3-tuple" (multi-bar chart), "boxplot" (boxplot)
				"createPieChart",			//name of the JavaScript-function, that produces the diagram
				false						//tooltip-switch: if tooltips are implemented for this format -> true, else: false
				);
	}
}


/**
 * Stacked/Streamed/Expanded Area Chart format (js-Library: NVD3)
 * | format=stackedareachart
 * @author Oliver Lutzi
 * @package SparqlCharts
 */
class SparqlChartsStackedAreaChart extends SparqlChartsOutputFormat {

	public function __construct($args) {
		//"parameter-name" => array( [position of parameter in create-function], [default value for parameter] )
		$defaultParametersArray = array(
				"width" 				=> array(0,"600"),
				"height" 				=> array(1,"350"),
				"margintop" 			=> array(2,"10"),
				"marginright" 			=> array(3,"80"),
				"marginbottom" 			=> array(4,"80"),
				"marginleft" 			=> array(5,"30"),
		);
		parent::__construct(
				$args,						//$args contains the QUERY and all PARAMETERS
				$defaultParametersArray,
				"3-tuple",					//type of the chart: e.g. "2-tuple" (bar chart, line chart,...), "3-tuple" (multi-bar chart), "boxplot" (boxplot)
				"createStackedAreaChart",	//name of the JavaScript-function, that produces the diagram
				false						//tooltip-switch: if tooltips are implemented for this format -> true, else: false
				);
	}
}


/**
 * Stacked/Grouped Multi-Bar Chart format (js-Library: NVD3)
 * | format=stackedmultibarchart
 * @author Oliver Lutzi
 * @package SparqlCharts
 */
class SparqlChartsStackedMultiBarChart extends SparqlChartsOutputFormat {

	public function __construct($args) {
		//"parameter-name" => array( [position of parameter in create-function], [default value for parameter] )
		$defaultParametersArray = array(
			"width" 				=> array(0,"500"),
			"height" 				=> array(1,"300"),
			"xaxislabel" 			=> array(2,"x-axis"),
			"yaxislabel" 			=> array(3,"y-axis"),
			"rotatex" 				=> array(4,"0"),
			"xaxislabeldistance" 	=> array(5,"0"),
			"yaxislabeldistance" 	=> array(6,"0"),
			"margintop" 			=> array(7,"10"),
			"marginright" 			=> array(8,"10"),
			"marginbottom" 			=> array(9,"80"),
			"marginleft" 			=> array(10,"80"),
			"groupspacing"			=> array(11,"0.2"),
				
		);
		parent::__construct(
				$args,							//$args contains the QUERY and all PARAMETERS
				$defaultParametersArray,
				"3-tuple",						//type of the chart: e.g. "2-tuple" (bar chart, line chart,...), "3-tuple" (multi-bar chart), "boxplot" (boxplot)
				"createStackedMultiBarChart",	//name of the JavaScript-function, that produces the diagram
				false							//tooltip-switch: if tooltips are implemented for this format -> true, else: false
				);
	}
}



/**
 * Box plot format (js-Library: d3.js)
 * | format=boxplot
 * @author Oliver Lutzi
 * @package SparqlCharts
 */
class SparqlChartsBoxPlot extends SparqlChartsOutputFormat {
	
	public function __construct($args) {
		//"parameter-name" => array( [position of parameter in create-function], [default value for parameter] )
		$defaultParametersArray = array(
				"width" 		=> array(0,"800"),
				"height" 		=> array(1,"400"),
				"outlier" 		=> array(2,0), //0 = outlier not displayed, 1 = outlier displayed
				"xaxislabel" 	=> array(3,"x-axis"),
				"yaxislabel" 	=> array(4,"y-axis"),
				"caption" 		=> array(5,"Box Plot"),
		);
		parent::__construct(
				$args,							//$args contains the QUERY and all PARAMETERS
				$defaultParametersArray,
				"boxplot",						//type of the chart: e.g. "2-tuple" (bar chart, line chart,...), "3-tuple" (multi-bar chart), "boxplot" (boxplot)
				"createBoxPlot",				//name of the JavaScript-function, that produces the diagram
				false							//tooltip-switch: if tooltips are implemented for this format -> true, else: false
				);
	}
}


/**
 * Force Directed Graph format - (js-Library: d3.js)
 * | format=forcedirectedgraph
 * @author Oliver Lutzi
 * @package SparqlCharts
 */
class SparqlChartsForceDirectedGraph extends SparqlChartsOutputFormat {

	public function __construct($args) {
		//"parameter-name" => array( [position of parameter in create-function], [default value for parameter] )
		$defaultParametersArray = array(
			"width" => array(0,"600"),
			"height" => array(1,"400"),
		);
		parent::__construct(
				$args,						//$args contains the QUERY and all PARAMETERS
				$defaultParametersArray,
				"forcedirectedgraph",		//type of the chart: e.g. "2-tuple" (bar chart, line chart,...), "3-tuple" (multi-bar chart), "boxplot" (boxplot)
				"createForceDirectedGraph",	//name of the JavaScript-function, that produces the diagram
				false						//tooltip-switch: if tooltips are implemented for this format -> true, else: false
				);
	}
}


/**
 * Kaplan Curve format (js-Library: NVD3)
 * | format=kaplancurve
 * @author Oliver Lutzi
 * @package SparqlCharts
 */
class SparqlChartsKaplanCurve extends SparqlChartsOutputFormat {

	public function __construct($args) {
		//"parameter-name" => array( [position of parameter in create-function], [default value for parameter] )
		$defaultParametersArray = array(
				"width" => array(0,"600"),
				"height" => array(1,"350"),
				"color" => array(3,"steelblue"),
		);
		parent::__construct(
				$args,						//$args contains the QUERY and all PARAMETERS
				$defaultParametersArray,
				"2-tuple",					//type of the chart: e.g. "2-tuple" (bar chart, line chart,...), "3-tuple" (multi-bar chart), "boxplot" (boxplot)
				"createKaplanCurve",		//name of the JavaScript-function, that produces the diagram
				false						//tooltip-switch: if tooltips are implemented for this format -> true, else: false
				);
	}
}


//Copied and modified from SparqlExtension
/**
 * default output format - a wiki table with a number of formating options
 * | format=template
 * |  tablestyle=border-width:1px; border-spacing:0px; border-style:outset; border-color:black; border-collapse:collapse;
 * | rowstyle=padding:2px;
 * | oddrowstyle=background-color:Lavender
 * | evenrowstyle=background-color:white
 * | headerstyle=background-color:CornflowerBlue; color: white
 * @author Oliver Lutzi (SparqlExtension: A.Chmieliauskas@tudeflt.nl)
 * @package SparqlCharts
 */
class SparqlChartsTable extends SparqlChartsOutputFormat {

	public function __construct($args) {
		//"parameter-name" => array( [position of parameter in create-function], [default value for parameter] )
		$defaultParametersArray = array(
				"width" => array(0,"600"),
				"height" => array(1,"400"),
		);
		parent::__construct(
				$args,						//$args contains the QUERY and all PARAMETERS
				$defaultParametersArray,
				"table",					//type of the chart: e.g. "2-tuple" (bar chart, line chart,...), "3-tuple" (multi-bar chart), "boxplot" (boxplot)
				"createForceDirectedGraph",	//name of the JavaScript-function, that produces the diagram
				false						//tooltip-switch: if tooltips are implemented for this format -> true, else: false
				);
	}
	
	//Overrides the getOutput()-function from parent class SparqlChartsOutputFormat
	public function getOutput() {
		$args = $this->args;
		$data = $this->data;
		
		// get settings
		$link = (isset($args["link"]) && $args["link"] == "none") ? false : true;
		
		$tablestyle = isset($args["tablestyle"]) ? "style=\"".$args["tablestyle"]."\"" : "border=\"1\"";
		$rowstyle = isset($args["rowstyle"]) ? $args["rowstyle"] : "";
		$oddrowstyle = isset($args["oddrowstyle"]) ? $args["oddrowstyle"] : "";
		$evenrowstyle = isset($args["evenrowstyle"]) ? $args["evenrowstyle"] : "";
		$headerstyle = isset($args["headerstyle"]) ? $args["headerstyle"] : "";
		$replaceWhat = isset($args["replacewhat"]) ? $args["replacewhat"] : "";
		$replaceWith = isset($args["replacewith"]) ? $args["replacewith"] : "";
		$decimals = isset($args["decimals"]) ? $args["decimals"] : 2;
		$header = "{| class=\"wikitable sortable\" ".$tablestyle;
		$new_line = "\n|-";
		$headerstyle = ($headerstyle == "") ? "" : "style=\"".$headerstyle."\"";
		$header .= $new_line." ".$headerstyle." \n!";
				
		if (is_array($data[1][0])) {
			foreach(array_keys($data[1][0]) as $key) {
				$header .= $key . "!!";
			}
		}
		$header = trim($header, "!");
		
		
		$is_odd = true;
		foreach($data[1] as $row) {
			$style = ($rowstyle == "" || $this->endsWith(trim($rowstyle), ";")) ? $rowstyle : $rowstyle.";";
			$style .= ($is_odd) ? $oddrowstyle : $evenrowstyle;
			$style = ($style == "") ? "" : "style=\"".$style."\"";
			$header .= $new_line." ".$style." \n";
			$header .= "| ";
		
			//wfDebugLog('SPARQL_LOG', "#=== row ===\n".print_r($row,TRUE)."\n");
		
			foreach($row as $cell) {
				if ($replaceWhat != "") {
					$cell = str_replace($replaceWhat, $replaceWith, $cell);
				}
				$value = $cell;				
				//This is a (slight) hack to prevent commas from appearing
				//in year values.  What we should probably do eventually is look at the
				//xsd type specified for the literal and then format it appropriately
				if (is_numeric($value) && strlen($value) > 4) {
					$value = number_format($value, $decimals, ".", ",");
				}
				$header .= " " . $value . " ||";
			}
			$header = trim($header, "|");
			$is_odd = !$is_odd;
		}
		$header .= "\n|}";		
		
		return array($header, 'noparse' => false, 'isHtml' => false);
	}
	
	
	function endsWith( $str, $sub ) {
		return ( substr( $str, strlen( $str ) - strlen( $sub ) ) === $sub );
	}
}


