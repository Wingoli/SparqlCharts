<?php
/**
 * SparqlCharts_body.php for SparqlCharts extension
 * @author Oliver Lutzi
 */


class SparqlChartsOutputFactory {
	
	public static function getOutputForFormat($args) {	
		global $wgScriptPath;
		
		// parser is first element - store in $parser and remove it from args
		$parser = array_shift($args);		
		
		$listOfStylesPaths = array();
		$listOfScriptPaths = array();		

		/*
		 * in MediaWiki, the syntax of the parser function provided by this extension looks like this:
		 * {{#sparqlchart:
		 * 	   SPARQLQUERY
		 * 	   |parameter1=value1
		 *     |parameter2=value2
		 *     |...
		 * }}
		 * 
		 * $args looks like this: ["SPARQLQUERY","parameter1=value1","parameter2=value2",...]
		 * 
		 * in getParameters(.) $args gets parsed into an associative array, that looks like this:
		 * 	{"query":"SPARQLQUERY","parameter1":"value1","parameter2":"value2",...}
		 * 
		 */
		$parameters = SparqlChartsOutputFactory::getParameters($args);
		Log::log_this("PARAMETERS", $parameters);
		
		/*
		 *  Add all necessary scripts and styles in the head-element of the html-file
		 *  will look like this in the html-code:
		 *  <script src="/mediawiki/extensions/SparqlCharts/includes/scripts/d3.v3.js"></script>
		 *  AND
		 *  Create chart object
		 */
		switch ($parameters["format"]) {
			case "barchart": //D3
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/d3.v3.js');
				array_push($listOfStylesPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/styles/barChart.css');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/barChart.js');
				$output = new SparqlChartsBarChart($parameters);
				break;
			case "stackedareachart": //D3+NVD3		
				array_push($listOfStylesPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/styles/nv.d3.css');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/d3.v3.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/nv.d3.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/stackedAreaChart.js');
				$output = new SparqlChartsStackedAreaChart($parameters);
				break;
			case "stackedmultibarchart": //D3+NVD3
				array_push($listOfStylesPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/styles/nv.d3.css');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/d3.v3.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/nv.d3.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/stackedMultiBarChart.js');				
				$output = new SparqlChartsStackedMultiBarChart($parameters);
				break;
			case "linechart": //D3+NVD3
				array_push($listOfStylesPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/styles/nv.d3.css');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/d3.v3.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/nv.d3.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/lineChart.js');
				$output = new SparqlChartsLineChart($parameters);
				break;
			case "boxplot": //D3
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/d3.v3.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/boxPlot.js');
				array_push($listOfStylesPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/styles/boxPlot.css');
				$output = new SparqlChartsBoxPlot($parameters);
				break;
			case "kaplancurve": //D3+NVD3
				array_push($listOfStylesPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/styles/nv.d3.css');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/d3.v3.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/nv.d3.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/kaplan.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/kaplanCurve.js');
				$output = new SparqlChartsKaplanCurve($parameters);
				break;
			case "piechart": //D3+NVD3
				array_push($listOfStylesPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/styles/nv.d3.css');
				array_push($listOfStylesPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/styles/pieChart.css');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/d3.v3.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/nv.d3.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/pieChart.js');
				$output = new SparqlChartsPieChart($parameters);
				break;
			case "forcedirectedgraph": //D3
				array_push($listOfStylesPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/styles/forceDirectedGraph.css');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/d3.v3.js');
				array_push($listOfScriptPaths, $wgScriptPath.'/extensions/SparqlCharts/includes/scripts/forceDirectedGraph.js');
				$output = new SparqlChartsForceDirectedGraph($parameters);
				break;
			default:
				Log::log_this("Default", "default");
				$output = new SparqlChartsTable($parameters);
		}
			
		/*
		 * The second argument of addHeatItem serves as an identifier.
		 * The related script to this identifier gets only added once to the head of the html file.
		 */
		foreach($listOfStylesPaths as $stylesPath){
			$parser->getOutput()->addHeadItem(Html::linkedStyle($stylesPath),$stylesPath);
		}
		foreach($listOfScriptPaths as $scriptPath){
			$parser->getOutput()->addHeadItem(Html::linkedScript($scriptPath),$scriptPath);
		}
		
		//return result of getOutput() function from chart object
		return $output->getOutput();
	}
	
	public static function getParameters($args) {	
		Log::log_this("args before getParameters", $args);
		
		/*
		 * in MediaWiki, the syntax of the parser function provided by this extension looks like this:
		 * {{#sparqlchart:
		 * 	   SPARQLQUERY
		 * 	   |parameter1=value1
		 *     |parameter2=value2
		 *     |...
		 * }}
		 * 
		 * $args looks like this: ["SPARQLQUERY","parameter1=value1","parameter2=value2",...]
		 * 
		 * in getParameters(.) $args gets parsed into an associative array, that looks like this:
		 * 	{"query":"SPARQLQUERY","parameter1":"value1","parameter2":"value2",...}
		 * 
		 */
		
		 $query_string = array_shift($args);//array_shift deletes 1st element from array and returns it
		 $tmp = array();
		 
		 //prepend prefixes to query
		 /*
		  * TODO: PREFIX
		  * At the moment, prefixes dont get prepended to the SPARQL queries,
		  * but the function is already there (copied from the SparqlExtension)
		  * -> Would be nice, if prefixes could be edited on a special page!
		  */
		 //$query_string = SparqlChartsUtil::createPrefixes() . $query_string;
		 //Log::log_this("NACH createPrefixes():", json_encode($query_string));
		 	
		 //add query string to the parameters
		 $tmp["query"] = $query_string;
		 

		// get the rest: the parameters defined by the user		
		foreach ($args as $arg) {
		/*
		 * use regular expressions: if $arg is this: "format=barchart" 
		 * -> an entry is added to the $tmp-array: $tmp["format"] = "barchart"
		 */
		if (!is_object($arg)) {
				preg_match('/^(\\w+)\\s*=\\s*(.+)$/is', $arg, $match) ? $tmp[$match[1]] = $match[2] : $args[] = $arg;
			}
		}
		
		//convert "true" and "false"-Strings to boolean values
		foreach($tmp as $key=>$value) {
			if($value == "true") {
				$value = true;
			} else if ($value == "false") {
				$tmp[$key] = false;
			}
		}
				
		/*
		 *		                    hierarchical part
		 *		        ┌───────────────────┴─────────────────────┐
		 *		                    authority               path
		 *		        ┌───────────────┴───────────────┐┌───┴────┐
		 *		  abc://username:password@example.com:123/path/data?key=value#fragid1
		 *		  └┬┘   └───────┬───────┘ └────┬────┘ └┬┘           └───┬───┘ └──┬──┘
		 *		scheme  user information     host     port            query   fragment
		 * 
		 * need to extract authority and path from address to SPARQL endpoint for HTTP GET request:
		 * before: 	$tmp["endpoint"] = http://aifb-ls3-vm2.aifb.kit.edu:8890/sparql/
		 * after: 	$tmp["host"] = aifb-ls3-vm2.aifb.kit.edu:8890
		 * 			$tmp["localaddress"] = /sparql
		 *
		 */
		if(isset($tmp["endpoint"])) {
			/*
			 * pattern searches for at least two "words" (could be a actual word or numbers ->IP addresses)
			 * separated by a "." and optionally ":PORT" at the end, where PORT is a sequence of numbers
			 */
			preg_match('/((?:[a-z0-9\-]*\.){1,}[a-z0-9\-]*)(:[0-9]*)?/', $tmp["endpoint"], $host, PREG_OFFSET_CAPTURE );
			//	-> important: needs to be 'PATTERN' and not "PATTERN"!
		}
		if(!is_null($host[0][0])){
			//$host[0][0] = "aifb-ls3-vm2.aifb.kit.edu:8890" (=host+port)
			$tmp["host"]=$host[0][0];
			//$host[0][1] = start index of host (e.g. https://host/localaddress ->start index = 8)
			$localaddress = substr($tmp["endpoint"],$host[0][1]+strlen($host[0][0]));

			/*
			 * "/sparql/" --> "/sparql"
			 */
			if(substr($localaddress,-2) == "\/"){
				$localaddress = substr($localaddress, 0, -2);
			} else if (substr($localaddress,-1) == "/") {
				$localaddress = substr($localaddress, 0, -1);
			}
			$tmp["localaddress"]=$localaddress;
		} 
		/*
		 * check, if there has not been a match, since the host is "localhost"
		 * (if address to endpoint is given as: "|endpoint=http://localhost:3030/db/query"
		 *  -> there is no match, because there is no top level domain)
		 */
		else {
			preg_match('/localhost(:[0-9]*)/', $tmp["endpoint"], $host, PREG_OFFSET_CAPTURE );
			Log::log_this("Treffer im endpoint-String (localhost)", $host);
			if(!is_null($host[0][0])){
				//$host[0][0] = "localhost:3030" (=localhost+port)
				$tmp["host"]=$host[0][0];
				//$host[0][1] = startindex des hosts (z.B. bei https://host/localaddress ->startindex = 8)
				$localaddress = substr($tmp["endpoint"],$host[0][1]+strlen($host[0][0]));
			
				/*
				 * "/sparql/" --> "/sparql"
				 */
				if(substr($localaddress,-2) == "\/"){
					$localaddress = substr($localaddress, 0, -2);
				} else if (substr($localaddress,-1) == "/") {
					$localaddress = substr($localaddress, 0, -1);
				}
				$tmp["localaddress"]=$localaddress;
			}
			
			/*
			 * TODO: 
			 * Print error message if the endpoint was not defined
			 * 
			 * At the moment: use dbpedia SPARQL-endpoint as default
			 */
			if (!isset($tmp["endpoint"])) {
				$tmp["host"] = "dbpedia-live.openlinksw.com";
				$tmp["localaddress"] = "/sparql";
			}
		}
	
		if (!isset($tmp["format"])) {
			$tmp["format"] = SparqlChartsOutputFormat::$DEFAULT_FORMAT;
		}
		
// 		Log::log_this("tmp", $tmp);
		
		return $tmp;
	}
}

/**
 * Utility methods shared among the SparqlCharts classes (in parts reused from SparqlExtension)
 * @author Oliver Lutzi, (SparqlExtesion: A.Chmieliauskas@tudeflt.nl)
 * @package SparqlExtension
 */
class SparqlChartsUtil {
	

	/*
	 * this function prepares the parameters that are given to the function build_restful_url
	 * (build_restful_url constructs the request URL for the HTTP GET request)
	 */
	public function getEndpointRequestUrlForType($type, $args) {
		//store endpoint parameters
		$endpoint_parameters = array();
		$endpoint_parameters["query"] = $args["query"];//$this->args["query"];
		$endpoint_parameters["format"] = $type;
	
		//build endpoint request url
		$localaddress = $args["localaddress"];//$this->args["localaddress"];
	
		/*
		 * needs to be "default-graph-uri" and not "defaultgraphuri" in $endpoint_parameters,
		 * because this key is used unchanged for the request URL
		 */
		if(isset($args["defaultgraphuri"])) {		
			$endpoint_parameters["default-graph-uri"] = $args["defaultgraphuri"];
		} else {
			$endpoint_parameters["default-graph-uri"] = "";
		}
	
		$endpoint_request_url = SparqlChartsUtil::build_restful_url($localaddress, $endpoint_parameters);
		return $endpoint_request_url;
	}
	
	public static function get_data_mod($data, $args, $chartType) {
		
		//for an explanation of the $parameterNamesToCheck-array, check the extension page on mediawiki.org
		$data_mod = array();		
		$columnsToUse = array();
		$unUsedColumns = $data[0];
		$parameterNamesToCheck;
		if($chartType == "2-tuple") {
			$parameterNamesToCheck = array("xaxis","yaxis");
		} else if($chartType == "3-tuple") {
			$parameterNamesToCheck = array("xaxis","yaxis","zaxis");
		} else if($chartType == "boxplot") {
			$numberOfColumns = count($data[0]);
			for ($columnNumber = 0; $columnNumber < $numberOfColumns; $columnNumber++) {
				$parameterNamesToCheck[$columnNumber] = "boxplot".($columnNumber+1);
			}
		} else if($chartType == "forcedirectedgraph") {
			$parameterNamesToCheck = array("subject","object","group_subj","group_obj");
		}
		
		/*
		 * check all entries of $parameterNamesToCheck:
		 * -> if the user set one of the parameters in $parameterNamesToCheck, 
		 * 		check if the value of that parameter is one of the variable names from the SPARQL query
		 * 		->  if that is the case: add this variable name to the array $columnsToUse
		 * 			AND delete this variable name from ethe array $unUsedColumns
		 * 		->  else: add one of the entries of $unUsedColumns (here we take the first) to the array $columnsToUse
		 * 			AND delete this variable name from ethe array $unUsedColumns
		 */
		//Log::log_this("columnsToUse before", $columnsToUse);
		//Log::log_this("unUsedColumns before", $unUsedColumns);
		foreach($parameterNamesToCheck as $parameterName) {		
			$nameOfColumnToUse_candidate;
			//delete "?" at the beginning, so the user can use "xaxis=?a" or "xaxis=a"
			if(isset($args[$parameterName])) {
				$nameOfColumnToUse_candidate = $args[$parameterName];
				if($nameOfColumnToUse_candidate[0] == "?") {
					$nameOfColumnToUse_candidate = substr($nameOfColumnToUse_candidate, 1);
				}
			}
			if(isset($args[$parameterName]) && array_search($nameOfColumnToUse_candidate,$data[0]) !== false) {
				$nameOfColumnToUse = $nameOfColumnToUse_candidate;
				array_push($columnsToUse, $nameOfColumnToUse);
				$unUsedColumns = array_diff($unUsedColumns, array($nameOfColumnToUse));
				$unUsedColumns = array_values(array_filter($unUsedColumns));
			} else {
				$nameOfColumnToUse = $unUsedColumns[0];
				array_push($columnsToUse, $nameOfColumnToUse);
				$unUsedColumns = array_diff($unUsedColumns, array($nameOfColumnToUse));
				$unUsedColumns = array_values(array_filter($unUsedColumns));
			}
// 			Log::log_this("columnsToUse after update", $columnsToUse);
// 			Log::log_this("unUsedColumns after update", $unUsedColumns);
		}
		
		Log::log_this("columnsToUse final", $columnsToUse);
		
		
		/*
		 * Build the new array $data_mod, by adding all columns of the original $data array to it
		 * in the order definied by $columnsToUse
		 */
		$columnNumberOfDataMod = 0;
		foreach($columnsToUse as $nameOfColumnToUse){
// 			Log::log_this("data_mod before", $data_mod);
			$row_number = 0;
			/*
			 * e.g. chartType == 2-tuple:
			 * if xaxis value is "a", $data_mod looks like this after the first loop:
			 * 		data_mod[0][0] = data[1][0]["a"],
			 * 		data_mod[1][0] = data[1][1]["a"],
			 * 		data_mod[2][0] = data[1][2]["a"],...
			 */
			foreach($data[1] as $row) {
				$data_mod[$row_number][$columnNumberOfDataMod] = $row[$nameOfColumnToUse];
				$row_number++;
			}
			$columnNumberOfDataMod ++;
// 			Log::log_this("data_mod after", $data_mod);
		}
	
		/*
		 * extend input array with tooltips
		 * 
		 * if the user defined "|tooltip1=aaa" and "|tooltiplabel1=bbb"
		 * AND
		 * if aaa is one of the result variables in the result table of the SPARQL query
		 * --> the input array gets extended by a further column (the aaa-column)
		 * 
		 * TODO:
		 * The array with the tooltips should not be added to the array with the input data 
		 * but a separate one and it shouldnt be generate in this function, but in a separate
		 * one (would be "cleaner")
		 */
		$tooltipLabels = array();
		$more_tooltips = true;
		$tooltip_counter = 1;
		while ($more_tooltips) {
			
			$tooltip_parameter_name = "tooltipvar".$tooltip_counter; //e.g. "tooltipvar1" --> parameter in SMW: "|tooltipvar1 = time"
			$tooltip_label_parameter_name = "tooltiplabel".$tooltip_counter; //e.g. "tooltiplabel1" --> parameter in SMW: "|tooltiplabel1 = Zeitpunkt der Messung"
// 			Log::log_this("tooltip_parameter_name", $tooltip_parameter_name);
// 			Log::log_this("tooltip_label_parameter_name", $tooltip_label_parameter_name);
			
			//delete "?" at the beginning, so the user can use "tooltipvar1=?a" or "tooltipvar1=a"
			$tooltipVarName_candidate;
			if(isset($args[$tooltip_parameter_name])) {
				$tooltipVarName_candidate = $args[$tooltip_parameter_name];				
				if($tooltipVarName_candidate[0] == "?") {
					$tooltipVarName_candidate = substr($tooltipVarName_candidate, 1);
				}				
			}
			
			if(isset($args[$tooltip_parameter_name]) && array_search($tooltipVarName_candidate,$data[0]) !== false) {
				$tooltipVarName = $tooltipVarName_candidate;
// 				Log::log_this("tooltipVarName", $tooltipVarName);
				
				/*
				 * set the label, that gets displayed together with the tooltip.
				 * (e.g. "Count: 53" -> "Count" is tooltipLabel)
				 * if no label has been set: use the identifier of the variable from the SPARQL query
				 */
				if(isset($args[$tooltip_label_parameter_name])) {
					array_push($tooltipLabels, $args[$tooltip_label_parameter_name]); //$args["tooltiplabel1"] contains label fuer tooltipVar1
				} else {
					array_push($tooltipLabels, $tooltipVarName);
				}
					
				$row_number = 0;	
				/*
				 * e.g. chartType == 2-tuple:
				 * if tooltip1 value is "a", $data_mod looks like this after the first loop:
				 * 		data_mod[0][3] = data[1][0]["a"],
				 * 		data_mod[1][3] = data[1][1]["a"],
				 * 		data_mod[2][3] = data[1][2]["a"],...
				 */
				foreach($data[1] as $row) {
					$data_mod[$row_number][$tooltip_counter+1] = $row[$tooltipVarName]; // has to start with $data_mod[$row_number][2], therfore the "+1"
					$row_number++;
				}
				
				$tooltip_counter++;
			} else {
				/*
				 * if tooltipvar2 has not been set (or the value could not been found within the 
				 * set of result variables in the SPARQL query), we do not search after tooltipvar3
				 * and stop
				 */
				$more_tooltips = false;
			}
		}
		
		Log::log_this("data_mod", $data_mod);
		Log::log_this("tooltip_labels", $tooltipLabels);
		
		return array($data_mod,$columnsToUse,$tooltipLabels);
	}
	
	
	/*
	 * creates: <script>var tooltip_chart1 = ["Winner", "Top scorer", ];</script>
	 * (script tags are added later)
	 */
	public static function create_tooltip_label_var($tooltip_labels,$chart_id) {
		return 'var tooltip_'.$chart_id.' = '.json_encode($tooltip_labels);
	}
	
	public static function change_data_format_boxplot($data, $data_columnNames, $args) {		
		Log::log_this("data", $data);
		$data_mod;
		$boxplot_counter = 0;
		foreach($data_columnNames as $columnName) {
			if(isset($args["boxplot".($boxplot_counter+1)."_name"])) {
				$columnName = $args["boxplot".($boxplot_counter+1)."_name"];
			}
			$data_mod[$boxplot_counter][0] = $columnName;
			$data_mod[$boxplot_counter][1] = array();
			foreach($data as $row){
				if(isset($row[$boxplot_counter])) {
					array_push($data_mod[$boxplot_counter][1], floatval($row[$boxplot_counter]));
				}
			}
			$boxplot_counter++;
		}		
		return $data_mod;				
	}
	

	public static function change_data_format_stacked_3tuple_charts($data, $format) {
	
		$arrayWithOnly_xaxis_values = array();
		foreach($data as $row) {
			array_push($arrayWithOnly_xaxis_values,$row[0]);
		}
		$keys_x = array_unique($arrayWithOnly_xaxis_values);
	
		$arrayWithOnly_zaxis_values = array();
		foreach($data as $row) {
			array_push($arrayWithOnly_zaxis_values,$row[2]);
		}
		$keys_z = array_unique($arrayWithOnly_zaxis_values);// keys_z sieht so aus: {"0":"aaa", "1":"bbb",...}
	
		$data_mod;
		$key_z_counter = 0;
	
		foreach($keys_z as $key) {//key = "aaa", "bbb",..
			$data_mod[$key_z_counter]["key"]=$key; //>>[{"key":"aaa"}]<<
			/*
			* loop over data array:
			* 	for each row where 3rd entry matches $key
			* 	-> add new entry in values-array in array for this key
			* 		e.g. row = ["aaa","xxx",123]
			* 		$data_mod = [...,{"key":"aaa","values":[...,{"x":"xxx", "y":123}]}]
			*/
			$keys_x_checked = array();
			foreach($keys_x as $key_x) {				//creates: [["Montag",0],["Dienstag",0]...
				$keys_x_checked[$key_x] = array(0,0);
			}
	
			$rowcounter = 0;
			foreach($data as $row) {
				if($data[$rowcounter][2] == $key){
					$keys_x_checked[$data[$rowcounter][0]] = array(1,$data[$rowcounter][1]);//if x="Dienstag"--> update array: [["Montag",0],["Dienstag",1]...
				}
				$rowcounter++;
			}
	
			$keys_x_counter = 0;
			foreach($keys_x_checked as $key_x_checked=>$value) { //$key_x_checked = "Montag", $value[0] =0/1 -> value[0] = 0, if it is not in the result array yet. then: add it to the result array with y=0
				if($value[0] == 1) {
					if ($format == "stackedmultibarchart") {
						//[{"key":"aaa","values":[{"x":"Montag", "y":195}
						$data_mod[$key_z_counter]["values"][$keys_x_counter]["x"]=$key_x_checked;
						$data_mod[$key_z_counter]["values"][$keys_x_counter]["y"]=floatval($value[1]); //y-axis has to be numeric!
					} else if ($format == "stackedareachart") {
						//[{"key":"aaa","values":[{"x":"Montag", "y":195}
						$data_mod[$key_z_counter]["values"][$keys_x_counter][0]=floatval($key_x_checked);
						$data_mod[$key_z_counter]["values"][$keys_x_counter][1]=floatval($value[1]); //y-axis has to be numeric!
					}
				} 
				//here: add value pairs (x,y=0) for all x-values that has not been added so far 
				//		because they are missing in $data
				else if($value[0] == 0) {
					if ($format == "stackedmultibarchart") {
						//[{"key":"aaa","values":[{"x":"Montag", "y":0}
						$data_mod[$key_z_counter]["values"][$keys_x_counter]["x"]=$key_x_checked;
						$data_mod[$key_z_counter]["values"][$keys_x_counter]["y"]=0; //y-axis has to be numeric!
					} else if ($format == "stackedareachart") {
						//[{"key":"aaa","values":[{"x":"Montag", "y":0}
						$data_mod[$key_z_counter]["values"][$keys_x_counter][0]=floatval($key_x_checked);
						$data_mod[$key_z_counter]["values"][$keys_x_counter][1]=0; //y-axis has to be numeric!
					}
				}
				$keys_x_counter++;
			}
			$key_z_counter++;//********
		} //end foreach keys_z
		Log::log_this("data_mod multibar", $data_mod);
		return $data_mod;
	}
	
	
	public static function change_data_format_forceDirectedGraph($data) {
		Log::log_this("data", $data);

		//data-array has form: [["SUBJECT_NAME","OBJECT_NAME","GROUP_OF_SUBJECT_NAME","GROUP_OF_OBJECT_NAME",]]
		
		$nodes = array(); //[{"name": "0","group": 1}, {"name": "1","group": 3},...]
		$links = array(); //[{"source": 0,"target": 105,"value": 1},...] 
		
		foreach($data as $row) {
			
			$index_of_subject_in_nodes_array = array_search($row[0], array_column($nodes, 'name'));
			$index_of_object_in_nodes_array = array_search($row[1], array_column($nodes, 'name'));
			
			//add element for subject and/or object to nodes-array, if they are not yet contained
			if($index_of_subject_in_nodes_array == false){
				array_push($nodes, array("name"=>$row[0],"group"=>$row[2]));
				$index_of_subject_in_nodes_array = sizeof($nodes)-1;
			}
			if($index_of_object_in_nodes_array == false){
				array_push($nodes, array("name"=>$row[1],"group"=>$row[3]));
				$index_of_object_in_nodes_array = sizeof($nodes)-1;
			}
// 			Log::log_this("index_of_subject_in_nodes_array", $index_of_subject_in_nodes_array);
// 			Log::log_this("index_of_object_in_nodes_array", $index_of_object_in_nodes_array);
			
// 			//add link to links-array
			array_push($links, array(
				"source" => $index_of_subject_in_nodes_array,
				"target" => $index_of_object_in_nodes_array,
				"value" => 1, //value = weight of the link -> is always 1 at the moment, but could be set by an additional column
			));
		}
		
		$data_mod["nodes"] = $nodes;
		$data_mod["links"] = $links;
		
		Log::log_this("data_mod after change_data_format_forceDirectedGraph", $data_mod);
		
		return $data_mod;
	}
	
	
	public static function generate_createFunction_callString($nameOfCreateFunction,$defaultParametersArray,$chart_id,$tooltip_switch) {
		//create String for function call e.g.: createBarChart(width_chart1,height_chart1,color_chart1,input_chart1,"chart1")

		$deaultParameterNames = array_keys($defaultParametersArray);
		$createFunction_params_array = array();
		$createFunction_params = "";
		foreach($deaultParameterNames as $name){
			/*
			 * the position of $name in the list of parameters is stored at $deaultParameterNames[$name][0]
			 * -> the $defaultParametersArray looks like this:
			 * 		$defaultParametersArray=array("width"=>array(0,"600"),"height" => array(1,"350"),...);
			 */
			$createFunction_params_array[$deaultParameterNames[$name][0]] .= $name."_".$chart_id.",";
		}
		
		foreach($createFunction_params_array as $param) {
			$createFunction_params .= $param;
		}

		$createFunction_params .= "input_".$chart_id.",";
		if ($tooltip_switch) $createFunction_params .= "tooltip_".$chart_id.",";
		$createFunction_params .= '"'.$chart_id.'"';		
		$createFunction_callString = $nameOfCreateFunction.'('.$createFunction_params.');';
		
		return $createFunction_callString;
	}
	
	
	public static function create_js_input_var($data,$chart_id) {
		return 'var input_'.$chart_id.' = '.json_encode($data);
	}
	
	public static function create_js_param_vars($defaultParametersArray, $customizedParametersArray, $chart_id) {
	
		Log::log_this("PossibleParameters-Array before default values got replaced with values set by user:", $defaultParametersArray);
		foreach(array_keys($defaultParametersArray) as $param_key){
			//Log::log_this("Check if the following parameter has been set by the user:", $param_key);
			//Log::log_this("before: possibleParameters", $defaultParametersArray);
			if(isset($customizedParametersArray[$param_key])){
				//Log::log_this("The following parameter HAS been set by the user", $customizedParametersArray[$param_key]);
				$defaultParametersArray[$param_key][1] = $customizedParametersArray[$param_key];//*
			}
			//Log::log_this("after possibleParameters:", $defaultParametersArray);
		}
		//Log::log_this("PossibleParameters-Array after default values got replaced with values set by user:", $defaultParametersArray);
		$js_param_vars_string = "";
		$keys = array_keys($defaultParametersArray);
		//Log::log_this("keys of possibleParameters-Array", $keys);
		
		foreach($keys as $key){
			if(is_numeric($defaultParametersArray[$key][1])) {
				$js_param_vars_string .= 'var '.$key.'_'.$chart_id.'='.$defaultParametersArray[$key][1].'; ';
			} else {
				$js_param_vars_string .= 'var '.$key.'_'.$chart_id.'="'.$defaultParametersArray[$key][1].'";';
			}
		}
		return $js_param_vars_string;
	}
	
	
	public static function parse_csv($data_string, $options = null) {
		$delimiter = empty($options['delimiter']) ? "," : $options['delimiter'];
		$to_object = empty($options['to_object']) ? false : true;
		$expr="/$delimiter(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/";
		$str = $data_string;
		$lines = explode("\n", $str);
				
		//first line is blank - why?
		array_shift($lines);
				
		$field_names = explode($delimiter, trim(array_shift($lines)));
		$field_names = preg_replace("/^\"(.*)\"$/s","$1",$field_names); //added (@OL)
				
		foreach ($lines as $line) {
			// Skip the empty line
			if (empty($line)) continue;
			$fields = preg_split($expr,trim($line));
			$fields = preg_replace("/^\"(.*)\"$/s","$1",$fields);
			//$fields = explode($delimiter, $line);
			$_res = $to_object ? new stdClass : array();
			foreach ($field_names as $key => $f) {
				if ($to_object) {
					$_res->{$f} = $fields[$key];
				} else {
					$_res[$f] = $fields[$key];
				}
			}
			$res[] = $_res;
		}
		return array($field_names,$res);
	}
	
	
	public static function parse_json($data_string) {
		$json = json_decode($data_string, true);
		$fields = $json["head"]["vars"];
		$results = $json["results"]["bindings"];
		$res = array();
		$fields = is_array($fields)? $fields : array($fields);
		$results = is_array($results)? $results : array($results);
		foreach ($results as $result) {
			$row = array();
			foreach ($fields as $field) {
				$row[$field] = $result[$field]["value"];
			}
			$res[] = $row;
		}
		//result-array is array with result-array[0] = variable names of result-table from sparql query, e.g. ["entity", "time", "value"]
		//and result-array[1] = result-rows, e.g. [{"entity":"A", "time":"2015-11-01T12:15:00+01:00","value":"12"},{...},...]
		return array($fields,$res);
	}
	
	public static function build_restful_url($localaddress, $endpoint_parameters){		
		//$url = [dbpedia-live.openlinksw.com] + [/sparql] + [/?]
		//DBPedia works with: http://dbpedia.org/sparql?query=... AND with http://dbpedia.org/sparql/?query=...
		//-> fuseki endpoint needs: http://localhost:3030/db/query?query=
		//$url = $localaddress."/?";
		$url = $localaddress."?";
		
		
		foreach ($endpoint_parameters as $key=>$value){
			$url .=  "$key=".urlencode($value)."&";
		}
		
		if(substr($url,-1)=="&"){
			$url=substr($url,0,-1);
			$url.=" ";
		}
		
		return $url;
	}
	
	 /*
	  * TODO: PREFIX
	  * At the moment, prefixes dont get prepended to the SPARQL queries,
	  * but the function is already there (copied from the SparqlExtension)
	  * -> Would be nice, if prefixes could be edited on a special page!
	  */
	/*
	 * create prefixes for querying local
	 */
	public static function createPrefixes() {
// 		global $smwgNamespace;
// 		$pref  = "BASE <".$smwgNamespace.">\n";
// 		$pref .= "PREFIX article: <".$smwgNamespace.">\n";
// 		$pref .= "PREFIX a: <".$smwgNamespace.">\n";
// 		$pref .= "PREFIX property: <".$smwgNamespace."Property:>\n";
// 		$pref .= "PREFIX prop: <".$smwgNamespace."Property:>\n";
// 		$pref .= "PREFIX category: <".$smwgNamespace."Category:>\n";
// 		$pref .= "PREFIX cat: <".$smwgNamespace."Category:>\n";
		$pref .= "PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\n";
		$pref .= "PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>\n";
		$pref .= "PREFIX fn: <http://www.w3.org/2005/xpath-functions#>\n";
		$pref .= "PREFIX afn: <http://jena.hpl.hp.com/ARQ/function#>\n";
		return $pref;
	}
	

	public static function readQueryOutput($url, $host, $port, $type) {
		
		if (isset($host)) {			
			$fp = fsockopen($host, $port, $errno, $errstr);				
			$data = "";	
			
			if ($fp) {
				$out = "GET ".$url."HTTP/1.1\r\n";
				$out .= "Host: ".$host."\r\n";
				if ($type == "json") {
					$out .= "Accept: application/sparql-results+json \r\n";
				}
				$out .= "Connection: Close\r\n\r\n";
					
				Log::log_this("Generated HTTP GET request for sparql endpoint", $out);
				
				fputs($fp, $out);
				$output = "";
				$reading_headers = true;
				while (!feof ($fp)) {
					$curline = fgets($fp, 4096);
					if ($curline=="\r\n") {
						$reading_headers = false;
					}
					if (!$reading_headers) {
						$output .= $curline;
					}
				}
				fclose($fp);
				return $output;
			}
		} 
		
 		return false;
    	} //end readQueryOutput-function
 } //end class
 