<?php
/**
 * SparqlCharts_helpFunctions.php for SparqlCharts extension
 * @author Oliver Lutzi
 *
 */

class Log { 
	public static function log_this( $heading, $logtext ) {
		global $IP;
		$output = "\n>> ".$heading." <<\n";
		$output .= "-----------------------------------------------------------------------------------------------\n";
		$output .= json_encode($logtext);
		$output .= "\n\n-----------------------------------------------------------------------------------------------\n";
		
		//If file is bigger than 1000000 Bytes (=1 MB), only keep last 200 lines of file and delete rest 
		$max_filesize = 1000000;
		$number_of_lines_to_keep = 200;
		$filesize = filesize($IP.'/extensions/SparqlCharts/log/console.log');
		
		if($filesize > $max_filesize) {
			$filearray = file($IP.'/extensions/SparqlCharts/log/console.log');
			$lastfifteenlines = array_slice($filearray,-$number_of_lines_to_keep);
				
			file_put_contents($IP.'/extensions/SparqlCharts/log/console.log', $lastfifteenlines);
		}
		
		wfErrorLog($output, $IP.'/extensions/SparqlCharts/log/console.log');
	}
}