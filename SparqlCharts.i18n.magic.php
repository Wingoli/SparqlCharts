<?php  
/**
 * SparqlChartsi18n.magic.php for SparqlCharts extension
 * @author Oliver Lutzi
 * 
 * The variable $magicWords is used to associate each magic word ID 
 * with a language-dependent array that describes all the text strings (1)
 * that are mapped to the magic word ID
 * 
 * (1) These text strings can be used as wikitext, e.g. {{#sparqlcharts:...}}
 *
 */

$magicWords = array();  

//SPARQLCHARTS is the magic word ID 
//and sparqlcharts is the text string that can be used in wikitext
//in this way: {{#sparqlcharts:...}}
$magicWords['en'] = array( 'SPARQLCHARTS' => array( 0, 'sparqlcharts' ), );  
$magicWords['de'] = array( 'SPARQLCHARTS' => array( 0, 'sparqlcharts' ),);
