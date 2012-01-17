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
class Tx_FeuserPasswordexpiration_Scheduler_DetectUsersWithExpiredPasswordsTask
	extends Tx_FeuserPasswordexpiration_Scheduler_Task
	implements SplSubject {
		
	protected $users;
	
	/**
	 * @var Tx_Extbase_Persistence_ObjectStorage
	 */
	protected $observers;
	
	/**
	 * Tasks are stored serialized in database, constructor is not called.
	 * 
	 * @see __wakeup()
	 */
	public function __construct() {
		parent::__construct();
		$this->initializeObject();
	}
	
	/**
	 * Tasks are stored serialized in database, constructor is not called.
	 */
	public function __wakeup() {
		$this->initializeObject();
	}
	
	/**
	 * detect all users who didn't change their passwords and add them to a defined userGroup
	 */
	public function execute() {
		$this->getFrontendUserRepository()->updateLastPasswordChangeToCurrentTimestampIfNull();
		$this->users = $this->getFrontendUserRepository()->findUsersWithExpiredPasswords(
			$this->getExpirationDuration(),
			$this->getExtensionManager()->getIgnoreFeUsersWithPrefix(),
			$this->getExiprationUsergroup()
		);
		
		t3lib_div::devLog(sprintf('Found %d users with expired password.', count($this->users)), 'feuser_passwordexpiration', t3lib_div::SYSLOG_SEVERITY_INFO);
		
		if (count($this->users) > 0) {
			foreach ($this->users as $user) {
				$user->addUsergroup( $this->getExiprationUsergroup() );
			}
	
			$this->getPersistenceManager()->persistAll();
			$this->notify();
		}

		return TRUE;
	}
	
	/**
	 * Provide users with expired password to observers.
	 */
	public function getUsers() {
		return $this->users;
	}
	
	/**
	 * Implement SplSubject
	 * 
	 * @param SplObserver $observer
	 * @see SplSubject::attach()
	 */
	public function attach(SplObserver $observer) {
		$this->observers->attach($observer);
	}
	
	/**
	 * Implement SplSubject
	 * 
	 * @param SplObserver $observer
	 * @see SplSubject::detach()
	 */
	public function detach(SplObserver $observer) {
		$this->observers->detach($observer);
	}
	
	/**
	 * Implement SplSubject
	 * 
	 * @see SplSubject::notify()
	 */
	public function notify() {
		foreach ($this->observers as $observer) {
			$observer->update($this);
		}
	}
	
	/**
	 * get additional informations, which will be shown inside the scheduler-BE-modul
	 *
	 * @return string
	 */
	public function getAdditionalInformation() {
		return 'Expiration duration: '.$this->expirationDurationForDetection;
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
	 * @return integer
	 */
	protected function getExpirationDuration() {
		return $this->expirationDurationForDetection;
	}
	
	/**
	 * Do extensions register observers in their config files (ext_localconf.php)?
	 */
	public function doGloballyConfiguredObserversExist() {
		return is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_feuserpasswordexpiration']['DetectUsersWithExpiredPasswordsTask']['observers'])
			&& count($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_feuserpasswordexpiration']['DetectUsersWithExpiredPasswordsTask']['observers']) > 0;
	}
	
	/**
	 * Attach observers configured through config files (ext_localconf.php).
	 */
	protected function attachGloballyConfiguredObservers() {
		if ($this->doGloballyConfiguredObserversExist()) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_feuserpasswordexpiration']['DetectUsersWithExpiredPasswordsTask']['observers'] as $classDefinition) {
				$this->attach(t3lib_div::getUserObj($classDefinition));
			}
		}
	}
	
	/**
	 * Initialize object after construction and wakeup from serialization.
	 */
	protected function initializeObject() {
		$this->observers = new Tx_Extbase_Persistence_ObjectStorage();
		$this->attachGloballyConfiguredObservers();
	}
}