<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2013 Christoph Moeller <support@network-publishing.de>
*  (c) 2012-2013 Ingo Renner <ingo@typo3.org>
*
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
 * Scheduler task to empty the indexes of a site and re-initialize the
 * Solr Index Queue thus making the indexer re-index the site.
 *
 * @author Christoph Moeller <support@network-publishing.de>
 * @package TYPO3
 * @subpackage solr
 */
class tx_solr_scheduler_ReIndexTask extends tx_scheduler_Task {

	/**
	 * The site this task is supposed to initialize the index queue for.
	 *
	 * @var tx_solr_Site
	 */
	protected $site;

	/**
	 * Indexing configurations to re-initialize.
	 *
	 * @var array
	 */
	protected $indexingConfigurationsToReIndex = array();

	/**
	 * Whether to also empty the index when re-indexing.
	 *
	 * @var bool
	 */
	protected $emptyIndex = FALSE;


	/**
	 * Purges/commits all Solr indexes, initializes the Index Queue
	 * and returns TRUE if the execution was successful
	 *
	 * @return boolean Returns TRUE on success, FALSE on failure.
	 */
	public function execute() {
		$result = FALSE;

		if ($this->emptyIndex) {
			$solrServers = t3lib_div::makeInstance('tx_solr_ConnectionManager')->getConnectionsBySite($this->site);

			foreach($solrServers as $solrServer) {
					// make sure not-yet committed documents are removed, too
				$solrServer->commit();

				$solrServer->deleteByQuery('*:*');
				$response = $solrServer->commit(FALSE, FALSE, FALSE);
				if ($response->getHttpStatus() == 200) {
					$result = TRUE;
				}
			}
		} else {
			$result = TRUE;
		}

		$itemIndexQueue = t3lib_div::makeInstance('tx_solr_indexqueue_Queue');
		foreach ($this->indexingConfigurationsToReIndex as $indexingConfigurationName) {
			$itemIndexQueue->initialize($this->site, $indexingConfigurationName);
		}

			// TODO implement better error handling - can be done as soon as instantiated classes do return, see comment:
			// "return success / failed depending on sql error, affected rows"
			// in classes/indexqueue/class.tx_solr_indexqueue_queue.php::initialize()

		return $result;
	}

	/**
	 * Gets the site / the site's root page uid this task is running on.
	 *
	 * @return tx_solr_Site The site's root page uid this task is optimizinh
	 */
	public function getSite() {
		return $this->site;
	}

	/**
	 * Sets the task's site.
	 *
	 * @param tx_solr_Site $site The site to be handled by this task
	 */
	public function setSite(tx_solr_Site $site) {
		$this->site = $site;
	}

	/**
	 * Gets the indexing configurations to re-index.
	 *
	 * @return array
	 */
	public function getIndexingConfigurationsToReIndex() {
		return $this->indexingConfigurationsToReIndex;
	}

	/**
	 * Sets the indexing configurations to re-index.
	 *
	 * @param array $indexingConfigurationsToReIndex
	 */
	public function setIndexingConfigurationsToReIndex(array $indexingConfigurationsToReIndex) {
		$this->indexingConfigurationsToReIndex = $indexingConfigurationsToReIndex;
	}

	/**
	 * Sets whether to empty the index when re-indexing
	 *
	 * @param boolean $emptyIndex
	 */
	public function setEmptyIndex($emptyIndex) {
		$this->emptyIndex = (boolean) $emptyIndex;
	}

	/**
	 * Get whether the index is emptied when re-indexing
	 *
	 * @return boolean
	 */
	public function getEmptyIndex() {
		return $this->emptyIndex;
	}

	/**
	 * This method is designed to return some additional information about the task,
	 * that may help to set it apart from other tasks from the same class
	 * This additional information is used - for example - in the Scheduler's BE module
	 * This method should be implemented in most task classes
	 *
	 * @return string Information to display
	 */
	public function getAdditionalInformation() {
		$information = '';

		if($this->site) {
			$information = 'Site: ' . $this->site->getLabel();
		}

		if (!empty($this->indexingConfigurationsToReIndex)) {
			$information .= ', Indexing Configurations: ' . implode(', ', $this->indexingConfigurationsToReIndex);
		}

		return $information;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/scheduler/class.tx_solr_scheduler_reindextask.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/scheduler/class.tx_solr_scheduler_reindextask.php']);
}

?>