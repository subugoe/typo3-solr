<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Ingo Pfennigstorf <pfennigstorf@sub-goettingen.de>
 *      Goettingen State Library
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * ************************************************************* */

/**
 * Fluid Template for Solr rendering
 */
class tx_solr_FluidTemplate {

	protected $subPart;

	/**
	 * @var tx_solr_FluidTemplate
	 */
	protected $template;

	/**
	 * @var array
	 */
	protected $variables = array();

	/**
	 * @var string
	 */
	protected $workOnSubpart;

	/**
	 * @var string
	 */
	protected $templateRootPath;

	/**
	 * Constructor with defaults
	 */
	public function __construct($contentObject, $templateFile, $subPart) {

		/** @var Tx_Extbase_Object_ObjectManager $objectManager  */
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');

		/** @var Tx_Fluid_View_StandaloneView $template  */
		$template = $this->objectManager->create('Tx_Fluid_View_StandaloneView');

		$this->cObj = $contentObject;

		$this->templateFile = $templateFile;
		$this->loadHtmlFile($templateFile);
		$this->workOnSubpart($subPart);

		$this->templateRootPath = t3lib_div::getFileAbsFileName(t3lib_extMgm::extPath('solr') . 'Resources/Private/Templates/');
		$template->setPartialRootPath(t3lib_div::getFileAbsFileName(t3lib_extMgm::extPath('solr') . 'Resources/Private/Partials/'));
		$template->setLayoutRootPath(t3lib_div::getFileAbsFileName(t3lib_extMgm::extPath('solr') . 'Resources/Private/Layouts/'));
		$template->setFormat('html');
		$this->template = $template;
	}

	/**
	 * Just a wrapper for EXT;solr templating to fluid syntax variable assignment
	 *
	 * @param String $identifier
	 * @param mixed $value
	 */
	public function addVariable($identifier, $value) {
		$assignment = array($identifier => $value);
		$this->variables = t3lib_div::array_merge_recursive_overrule($assignment, $this->variables);
	}

	public function addSubpart($subPart, $commandName) {
	}

	/**
	 * Wraps subParts to Templates
	 *
	 * @param String$subPart
	 */
	public function setTemplate($subPart) {
		$this->template->setTemplatePathAndFilename($this->templateRootPath . t3lib_div::underscoredToUpperCamelCase($subPart) . '.html');

	}

	public function addViewHelperObject($a, $b) {
	}

	public function workOnSubpart($content) {
	}

	/**
	 * Sets the content for the template we're working on
	 *
	 * @param string the template's content - usually HTML
	 * @return unknown_type
	 */
	public function setWorkingTemplateContent($templateContent) {
		$this->workOnSubpart = $templateContent;
	}

	public function getSubpart($subPart) {
		$this->subPart = $subPart;
	}


	public function setTemplateContent($templateContent) {
		return $this->template = $templateContent;
	}

	public function getWorkOnSubpart() {
		return $this->workOnSubpart;
	}

	public function loadHtmlFile($htmlFile) {
	}

	public function addLoop($loop, $second, $third) {
		$this->variables[$second] = array();
		foreach($third as $document) {
			array_push($this->variables[$second], $document);
		}
	}

	public function render() {
//		Tx_Extbase_Utility_Debugger::var_dump($this->template);
		$this->template->assignMultiple(array_merge_recursive($this->variables));
		return $this->template->render();
	}

}