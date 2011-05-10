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
	 * update lastPasswordChange of given user
	 * 
	 * @param string $event
	 * @param array $params
	 * @param tx_feuserregister_interface_Observable $observable
	 * @return mixed Whether to cancel further processings
	 */
	public function update($event, array $params, tx_feuserregister_interface_Observable $observable) {
		if ($event === 'onEditAfterSave') {
			$objectManager = t3lib_div::makeInstance ( 'Tx_Extbase_Object_ObjectManager' );
			$frontendUserRepository = $objectManager->get ( 'Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository' );
			$frontendUserGroupRepository = $objectManager->get ( 'Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserGroupRepository' );
			
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['feuser_passwordexpiration']);
			$exiprationUsergroup = $frontendUserGroupRepository->findByUid($extConf['expirationUsergroup']);
			
			$frontendUser = $params['feuser'];
			$user = $frontendUserRepository->findByUid($frontendUser->get('uid'));
			$user->setLastPasswordChange(time());
			$user->removeUsergroup($exiprationUsergroup);
			
			$persistenceManager = $objectManager->get('Tx_Extbase_Persistence_Manager');
			$persistenceManager->persistAll();
		}
	}

}