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
	 * Finds an object matching the given identifier.
	 *
	 * @param int $uid The identifier of the object to find
	 * @return object The matching object if found, otherwise NULL
	 * @api
	 */
	public function findByUid($uid) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectSysLanguage(FALSE);
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectEnableFields(FALSE);
		$query->matching( $query->equals('uid', $uid) );
		return $query->execute()->getFirst();
	}

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
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup>|null $expirationGroup
	 */
	public function findUsersWithExpiredPasswords($expirationDuration, $ignoreFeUsersWithPrefix, $expirationGroup = null) {
		$expirationDate = time() - $expirationDuration;

		$query = $this->createQuery ();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);

		$userHasNotChangedPasswordInExpirationDurationCondition = $query->lessThan ( 'tx_feuserpasswordexpiration_last_password_change', $expirationDate );
		$userHasIgnorePrefixCondition = $query->like('username', $ignoreFeUsersWithPrefix.'%');

		if($ignoreFeUsersWithPrefix === '') {
			$query->matching ($query->logicalAnd($userHasNotChangedPasswordInExpirationDurationCondition));
		} else {
			$query->matching (
				$query->logicalAnd(
					$userHasNotChangedPasswordInExpirationDurationCondition,
					$query->logicalNot($userHasIgnorePrefixCondition)
				)
			);
		}

		return $this->filterUsersByExpiredUsersGroup($query->execute(), $expirationGroup, FALSE);
	}
	/**
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup> $expirationGroup
	 * @param integer $pageUid
	 */
	public function findUsersWhichContainToExpirationGroup(Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup $expirationGroup, $pageUid) {
		$query = $this->createQuery ();
		$query->getQuerySettings()->setStoragePageIds ( array ($pageUid) );

		return $this->filterUsersByExpiredUsersGroup($query->execute(), $expirationGroup, TRUE);
	}

	/**
	 * return only users, which belong OR not belong to expired-usersGroup
	 * 
	 * @param Tx_Extbase_Persistence_QueryResultInterface $users
	 * @param Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup|null $expirationGroup
	 * @param boolean $userMustBelongToExpiredGroup
	 * @return array
	 */
	private function filterUsersByExpiredUsersGroup(Tx_Extbase_Persistence_QueryResultInterface $users, Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup $expirationGroup = null, $userMustBelongToExpiredGroup) {
		if ($expirationGroup === null) {
			return $users;
		}

		/* @var $user Tx_FeuserPasswordexpiration_Domain_Model_FrontendUser */
		$filteredUsers = array();
		foreach ($users as $user) {
			$userBelongToExpiredGroup = FALSE;
			foreach ($user->getUsergroup() as $group) {
				if ($group->getUid() === $expirationGroup->getUid()) {
					$userBelongToExpiredGroup = TRUE;
					break;
				}
			}
			if ($userBelongToExpiredGroup === $userMustBelongToExpiredGroup) {
				$filteredUsers[] = $user;
			}
		}
		return $filteredUsers;
	}
}