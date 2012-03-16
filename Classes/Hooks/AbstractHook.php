<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Juergen Kussmann <juergen.kussmann@aoemedia.de>, AOE media GmbH
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
 * abstract hook
 */
abstract class Tx_FeuserPasswordexpiration_Hooks_AbstractHook {
	/**
	 * @var Tx_FeuserPasswordexpiration_Configuration_ExtensionManager
	 */
	private $extensionManager;
	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	private $objectManager;

	/**
	 * @param Tx_FeuserPasswordexpiration_Configuration_ExtensionManager $extensionManager
	 */
	public function injectExtensionManager(Tx_FeuserPasswordexpiration_Configuration_ExtensionManager $extensionManager) {
		$this->extensionManager = $extensionManager;
	}
	/**
	 * @param Tx_Extbase_Object_ObjectManager $container
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * persist all changes
	 */
	protected function persistAll() {
		$this->getObjectManager()->get('Tx_Extbase_Persistence_Manager')->persistAll();
	}
	/**
	 * remove FE-user from expiration-usergroup
	 * 
	 * @param integer $feUserUid
	 */
	protected function removeFrontendUserFromExpirationUsergroup($feUserUid) {
		$this->getFrontendUser($feUserUid)->removeUsergroup( $this->getExpirationUsergroup() );
	}
	/**
	 * update field, which defines the timestamp of last password-change
	 * 
	 * @param integer $feUserUid
	 */
	protected function updateLastPasswordChangeOfFrontendUser($feUserUid) {
		$this->getFrontendUser($feUserUid)->setLastPasswordChange( time() );
	}

	/**
	 * @return Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup
	 */
	private function getExpirationUsergroup() {
		$userGroupUid = $this->getExtensionManager()->getExpirationUsergroup();

		if($userGroupUid === 0) {
			throw new RuntimeException( 'expirationUsergroup must be defined in extension-configuration!' );
		}

		$repository = $this->getObjectManager()->get ( 'Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserGroupRepository' );
		return $repository->findByUid( $userGroupUid );
	}
	/**
	 * @return Tx_FeuserPasswordexpiration_Configuration_ExtensionManager
	 */
	private function getExtensionManager() {
		if($this->extensionManager === NULL) {
			$this->extensionManager = t3lib_div::makeInstance ( 'Tx_FeuserPasswordexpiration_Configuration_ExtensionManager' );
		}
		return $this->extensionManager;
	}
	/**
	 * @param integer $feUserUid
	 * @return Tx_FeuserPasswordexpiration_Domain_Model_FrontendUser
	 */
	private function getFrontendUser($feUserUid) {
		$repository = $this->getObjectManager()->get ( 'Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository' );
		return $repository->findByUid( $feUserUid );
	}
	/**
	 * @return Tx_Extbase_Object_ObjectManager
	 */
	private function getObjectManager() {
		if($this->objectManager === NULL) {
			$this->objectManager = t3lib_div::makeInstance ( 'Tx_Extbase_Object_ObjectManager' );
		}
		return $this->objectManager;
	}
}