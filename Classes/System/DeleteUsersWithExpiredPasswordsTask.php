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
 * Defines the duration after which frontend users who didn't change their passwords shold be deleted
 */
define('EXPIRATION_DURATION', 60 * 60 * 24 * 180);
/**
 * Frontend users delete task for sheduler
 * @package feuser_passwordexpiration
 */
class Tx_FeuserPasswordexpiration_System_DeleteUsersWithExpiredPasswordsTask extends tx_scheduler_Task {
	/**
	 * deletes all users who didn't change their passwords
	 */
	public function execute() {
		$objectManager = t3lib_div::makeInstance ( 'Tx_Extbase_Object_ObjectManager' );
		$frontendUserRepository = $objectManager->get ( 'Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository' );
		
		$usersWithExpiredPasswords = $frontendUserRepository->findUsersWithExpiredPasswords(EXPIRATION_DURATION);
		
		foreach ($usersWithExpiredPasswords as $user) {
			$frontendUserRepository->remove($user);
		}
		
		$persistenceManager = $objectManager->get('Tx_Extbase_Persistence_Manager');
		$persistenceManager->persistAll();
		
		return TRUE;
	}

}