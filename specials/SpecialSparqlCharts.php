<?php
/**
 * SparqlCharts SpecialPage for SparqlCharts extension
 *
 * @file
 * @ingroup Extensions
 */

class SpecialSparqlCharts extends SpecialPage {
  public function __construct() {
    parent::__construct( 'SparqlCharts' );
  }

  /**
   * Show the page to the user
   *
   * @param string $sub The subpage string argument (if any).
   *  [[Special:HelloWorld/subpage]].
   */
  public function execute( $sub ) {
    $out = $this->getOutput();

    $out->setPageTitle( $this->msg( 'sparqlcharts-specialpage-heading' ) );

    $out->addHelpLink( 'How to become a MediaWiki hacker' );

    $out->addWikiMsg( 'sparqlcharts-specialpage-intro' );
  }

  /**
   * @author Oliver Lutzi
   * Overrides the getGroupName() method inherited from SpecialPage to return "smw_group"
   * so that Special:SparqlCharts is listed in the group "Semantic MediaWiki"
   * at Special:SpecialPages
   * 
   * @return the key for the group of the special page Special:SparqlCharts
   *
   */
  protected function getGroupName() {
    return 'smw_group';
  }
}