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

/**
 * Frontend users delete task for sheduler
 */
class Tx_FeuserPasswordexpiration_Scheduler_DeleteUsersWithExpiredPasswordsTask extends Tx_FeuserPasswordexpiration_Scheduler_Task {
	/**
	 * deletes all users who didn't change their passwords
	 */
	public function execute() {
		// Updates database field of new users
		$this->getFrontendUserRepository()->updateLastPasswordChangeToCurrentTimestampIfNull();

		foreach ($this->getFrontendUserRepository()->findUsersWithExpiredPasswords($this->getExpirationDuration(), $this->getExtensionManager()->getIgnoreFeUsersWithPrefix()) as $user) {
			$this->getFrontendUserRepository()->remove($user);
		}

		$this->getPersistenceManager()->persistAll();

		return TRUE;
	}

	/**
	 * get additional informations, which will be shown inside the scheduler-BE-modul
	 *
	 * @return string
	 */
	public function getAdditionalInformation() {
		return 'Expiration duration: '.$this->expirationDurationForDeletion;
	}

	/**
	 * @return integer
	 */
	protected function getExpirationDuration() {
		return $this->expirationDurationForDeletion;
	}
}