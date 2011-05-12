<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2011 
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Repository for Tx_FeuserAdministration_Domain_Model_FrontendUser
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository extends Tx_Extbase_Domain_Repository_FrontendUserRepository {
	/**
	 * Updates the LastPasswordChange field of all feusers to the current timestamp if no timestamp is set
	 */
	public function updateLastPasswordChangeToCurrentTimestampIfNull() {
		$query = $this->createQuery ();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->matching ($query->equals ( 'tx_feuserpasswordexpiration_last_password_change', NULL ));
		
		foreach ($query->execute() as $user) {
			$user->setLastPasswordChange(time());
		}
	}
	/**
	 * Removes frontend users who didn't change their passwords since given timestamp
	 *
	 * @param integer $duration
	 * @param string $ignoreFeUsersWithPrefix
	 */
	public function findUsersWithExpiredPasswords($expirationDuration, $ignoreFeUsersWithPrefix) {
		$expirationDate = time() - $expirationDuration;

		$query = $this->createQuery ();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);

		$condition1 = $query->lessThan ( 'tx_feuserpasswordexpiration_last_password_change', $expirationDate );
		$condition2 = $query->like('username', $ignoreFeUsersWithPrefix.'%');
		if($ignoreFeUsersWithPrefix === '') {
			$query->matching ( $condition1 );
		} else {
			$query->matching ( $query->logicalAnd($condition1, $query->logicalNot($condition2)) );
		}

		return $query->execute();
	}
}