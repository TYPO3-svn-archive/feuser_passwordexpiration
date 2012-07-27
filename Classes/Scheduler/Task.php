<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Max Beer <max.beer@aoemedia.de>, AOE media GmbH
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

require_once (t3lib_extMgm::extPath ( 'scheduler' ) . 'class.tx_scheduler_task.php');

/**
 * common scheduler-task
 */
abstract class Tx_FeuserPasswordexpiration_Scheduler_Task extends tx_scheduler_Task {
	/**
	 * @var Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository
	 */
	private $frontendUserRepository;
	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	private $objectManager;

	/**
	 * @return Tx_FeuserPasswordexpiration_Configuration_ExtensionManager
	 */
	protected function getExtensionManager() {
		return t3lib_div::makeInstance ( 'Tx_FeuserPasswordexpiration_Configuration_ExtensionManager' );
	}
	/**
	 * @return Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository
	 */
	protected function getFrontendUserRepository() {
		if($this->frontendUserRepository === NULL) {
			$this->frontendUserRepository = $this->getObjectManager()->get ( 'Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository' );
		}
		return $this->frontendUserRepository;
	}
	/**
	 * @return Tx_Extbase_Object_ObjectManager
	 */
	protected function getObjectManager() {
		if($this->objectManager === NULL) {
			$this->objectManager = t3lib_div::makeInstance ( 'Tx_Extbase_Object_ObjectManager' );
		}
		return $this->objectManager;
	}
	/**
	 * @return Tx_Extbase_Persistence_Manager
	 */
	protected function getPersistenceManager() {
		return $this->getObjectManager()->get('Tx_Extbase_Persistence_Manager');
	}
	
	/**
	 * @param string $message
	 * @param label $label
	 * @param int $severity
	 */
	protected function log($message, $label, $severity = t3lib_div::SYSLOG_SEVERITY_INFO) {
		t3lib_div::devLog($message, $label, $severity);
	}
}