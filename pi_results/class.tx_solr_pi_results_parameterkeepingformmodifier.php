<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Michel Tremblay <mictre@gmail.com>
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
 * A form modifier to carry over GET parameters from one request to another if
 * the option plugin.tx_solr.search.keepExistingParametersForNewSearches is
 * enabled.
 *
 * @author Michel Tremblay <mictre@gmail.com>
 * @author Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage solr
 */
class tx_solr_pi_results_ParameterKeepingFormModifier implements tx_solr_FormModifier, tx_solr_CommandPluginAware {

	/**
	 * Configuration
	 *
	 * @var array
	 */
	protected $configuration;

	/**
	 * The currently active plugin
	 *
	 * @var tx_solr_pluginbase_CommandPluginBase
	 */
	protected $parentPlugin;

	/**
	 * Constructor for class tx_solr_pi_results_ParameterKeepingFormModifier
	 *
	 */
	public function __construct() {
		$this->configuration = tx_solr_Util::getSolrConfiguration();
	}

	/**
	 * Sets the currently active parent plugin.
	 *
	 * @param tx_solr_pluginbase_CommandPluginBase $parentPlugin Currently active parent plugin
	 */
	public function setParentPlugin(tx_solr_pluginbase_CommandPluginBase $parentPlugin) {
		$this->parentPlugin = $parentPlugin;
	}

	/**
	 * Modifies the search form by providing hidden form fields to transfer
	 * parameters to a news search.
	 *
	 * @param array $markers An array of existing form markers
	 * @param tx_solr_Template|tx_solr_FluidTemplate $template An instance of the template engine
	 * @return array Array with additional markers for suggestions
	 */
	public function modifyForm(array $markers, $template) {
		$hiddenFields = array();

		if ($this->parentPlugin instanceof tx_solr_pi_results && $this->configuration['search.']['keepExistingParametersForNewSearches']) {
			foreach ($this->parentPlugin->piVars as $key => $value) {
				$name = $this->parentPlugin->prefixId . '[' . $key . ']';
				if (is_array($value)) {
					foreach ($value as $k => $v) {
						$hiddenFields[] = '<input type="hidden" name="' . $name . '[' . $k . ']" value="' . $v . '" />';
					}
				} else {
					$hiddenFields[] = '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
				}
			}
		}

		$markers['hidden_parameter_fields'] = implode("\n", $hiddenFields);

		return $markers;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/pi_results/class.tx_solr_pi_results_parameterkeepingformmodifier.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/pi_results/class.tx_solr_pi_results_parameterkeepingformmodifier.php']);
}

?>