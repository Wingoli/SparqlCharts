<?php
/**
 * SparqlCharts.php for SparqlCharts extension
 * @author Oliver Lutzi
 *
 */

if ( function_exists( 'wfLoadExtension' ) ) {
  wfLoadExtension( 'SparqlCharts' );
  // Keep i18n globals so mergeMessageFileList.php doesn't break
  $wgMessagesDirs['SparqlCharts'] = __DIR__ . '/i18n';
  $wgExtensionMessagesFiles['SparqlChartsAlias'] = __DIR__ . '/SparqlCharts.i18n.alias.php';
  wfWarn(
    'Deprecated PHP entry point used for SparqlCharts extension. Please use wfLoadExtension ' .
    'instead, see https://www.mediawiki.org/wiki/Extension_registration for more details.'
  );
  return true;
} else {
  die( 'This version of the SparqlCharts extension requires MediaWiki 1.25+' );
}