<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Ingo Renner <ingo@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * No Results found view command
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage solr
 */
class tx_solr_pi_results_NoResultsCommand implements tx_solr_Command {

	protected $parentPlugin;

	/**
	 * constructor for class tx_solr_pi_results_ResultsCommand
	 */
	public function __construct(tslib_pibase $parentPlugin) {
		$this->parentPlugin = $parentPlugin;
	}

	public function execute() {
		$searchWord = t3lib_div::removeXSS(trim($this->parentPlugin->piVars['q']));

		$nothingFound = strtr(
			$this->parentPlugin->pi_getLL('no_results_nothing_found'),
			array(
				'@searchWord' => htmlspecialchars($searchWord)
			)
		);

		$searchedFor = strtr(
			$this->parentPlugin->pi_getLL('results_searched_for'),
			array(
				'@searchWord' => htmlspecialchars($searchWord)
			)
		);

		return array(
			'nothing_found' => $nothingFound,
			'searched_for'  => $searchedFor,
		);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/pi_results/class.tx_solr_pi_results_noresultscommand.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/pi_results/class.tx_solr_pi_results_noresultscommand.php']);
}

?>