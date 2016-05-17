<?php
/**
 * Hooks for SparqlCharts extension
 * @author Oliver Lutzi
 */

class SparqlChartsHooks {

	// Register any render callbacks with the parser
	function onParserSetup( &$parser ) {
		global $IP;
		
		// Create a function hook associating the "sparqlcharts" magic word with SparqlCharts_ParserFunctionRender()
		$parser->setFunctionHook( 'SPARQLCHARTS', 'SparqlChartsHooks::SparqlCharts_ParserFunctionRender' );		
	}
	
	
	static function SparqlCharts_ParserFunctionRender(&$parser) {

		// get the function arguments
		$argv = func_get_args(); //first argument is parser -> is needed in getOutputForFormat
		
		$output = SparqlChartsOutputFactory::getOutputForFormat($argv);
		Log::log_this("The final result, that gets returned to the parser", $output);
		
		// The input parameters are wikitext with templates expanded.
		// The output should be wikitext too.
		if ($output) {
			// if array - output it in the "parser" format (ie with flags) (used for table format only)
			// if not an array output it w/o processing - raw (used in drawing charts)
			if (is_array($output)) {
				return $output;
			} else {
				return $parser->insertStripItem( $output, $parser->mStripState );
				
			}
		} else {
			return wfMsg('no_data');
		}
	}
}


