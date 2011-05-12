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

require_once(t3lib_extMgm::extPath('feuserregister') . 'interfaces/interface.tx_feuserregister_interface_observer.php');

/**
 * Hook to update lastPasswordChange field in fe_users table on password update
 * NOTE: Use for this class 'tx' instead of 'Tx' in class name
 */
class tx_FeuserPasswordexpiration_Hooks_UpdateLastPasswordChangeHook implements tx_feuserregister_interface_Observer {
	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	private $objectManager;

	/**
	 * update lastPasswordChange of given user
	 * 
	 * @param string $event
	 * @param array $params
	 * @param tx_feuserregister_interface_Observable $observable
	 * @return mixed Whether to cancel further processings
	 */
	public function update($event, array $params, tx_feuserregister_interface_Observable $observable) {
		if ($event === 'onEditAfterSave') {
			$frontendUser = $params['feuser'];
			$frontendUserRepository = $this->getObjectManager()->get ( 'Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository' );
			$user = $frontendUserRepository->findByUid($frontendUser->get('uid'));
			$user->setLastPasswordChange( time() );
			$user->removeUsergroup( $this->getExiprationUsergroup() );

			$persistenceManager = $this->getObjectManager()->get('Tx_Extbase_Persistence_Manager');
			$persistenceManager->persistAll();
		}
	}

	/**
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup>
	 */
	protected function getExiprationUsergroup() {
		if($this->getExtensionManager()->getExpirationUsergroup() === 0) {
			throw new RuntimeException( 'expirationUsergroup must be defined in extension-configuration!' );
		}
		$frontendUserGroupRepository = $this->getObjectManager()->get ( 'Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserGroupRepository' );
		return $frontendUserGroupRepository->findByUid( $this->getExtensionManager()->getExpirationUsergroup() );
	}
	/**
	 * @return Tx_FeuserPasswordexpiration_Configuration_ExtensionManager
	 */
	protected function getExtensionManager() {
		return t3lib_div::makeInstance ( 'Tx_FeuserPasswordexpiration_Configuration_ExtensionManager' );
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
}