<?php

require_once dirname(__FILE__) . '/../BaseTestCase.php';

class Tx_FeuserPasswordexpiration_Scheduler_DetectUsersWithExpiredPasswordsTaskTest
	extends Tx_FeuserPasswordexpiration_Tests_BaseTestCase {

	/**
	 * @var array
	 */
	protected $globalExtensionConfigurationBackup;

	/**
	 * @var Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup
	 */
	protected $expirationUserGroup;

	/**
	 * @var Tx_FeuserPasswordexpiration_Scheduler_DetectUsersWithExpiredPasswordsTask
	 */
	protected $task;

	/**
	 * @var Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository
	 */
	protected $userRepository;

	protected function setUp() {
		$this->globalExtensionConfigurationBackup = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'];
		$this->unsetGloballyConfiguredObservers();

		$this->userRepository = $this->getMock('Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository');
		$this->expirationUserGroup = new Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup('a group object for expired users');

		// @todo: fix spelling errors in method names
		$this->task = $this->getMock(
			'Tx_FeuserPasswordexpiration_Scheduler_DetectUsersWithExpiredPasswordsTask',
			array('getFrontendUserRepository', 'getExiprationUsergroup')
		);
		$this->task->expects($this->any())
			->method('getFrontendUserRepository')
			->will($this->returnValue($this->userRepository));
		$this->task->expects($this->any())
			->method('getExiprationUsergroup')
			->will($this->returnValue($this->expirationUserGroup));
		$this->task->expects($this->any())
			->method('getPersistenceManager')
			->will($this->returnValue($this->getMock('Tx_Extbase_Persistence_Manager')));
	}

	/**
	 * @test
	 */
	public function noSideEffectsThroughGlobalConfiguration() {
		$this->assertFalse($this->task->doGloballyConfiguredObserversExist());
	}

	/**
	 * @test
	 */
	public function taskNotifiesObservers() {
		$john = $this->getMock('Tx_FeuserPasswordexpiration_Domain_Model_FrontendUser');
		$jane = $this->getMock('Tx_FeuserPasswordexpiration_Domain_Model_FrontendUser');

		$this->userRepository->expects($this->any())
			->method('findUsersWithExpiredPasswordsNotInExpiredUsersGroup')
			->will($this->returnValue(array($john, $jane)));

		$this->task->attach($this->getMockObserver());
		$this->task->execute();
	}

	/**
	 * @test
	 */
	public function addExpiredUsersToGroup() {
		$john = $this->getMock('Tx_FeuserPasswordexpiration_Domain_Model_FrontendUser');
		$john->expects($this->once())->method('addUsergroup')->with(clone $this->expirationUserGroup);
		$jane = $this->getMock('Tx_FeuserPasswordexpiration_Domain_Model_FrontendUser');
		$jane->expects($this->once())->method('addUsergroup')->with(clone $this->expirationUserGroup);

		$this->userRepository->expects($this->any())
			->method('findUsersWithExpiredPasswordsNotInExpiredUsersGroup')
			->will($this->returnValue(array($john, $jane)));

		$this->task->attach($this->getMockObserver());
		$this->task->execute();
	}

	/**
	 * @test
	 */
	public function testExecutionLogsNumberOfUsers() {
		$frontendUserRepository = $this->getMock(
			'Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository',
			array(
				'updateLastPasswordChangeToCurrentTimestampIfNull',
				'findUsersWithExpiredPasswordsNotInExpiredUsersGroup'
			)
		);
		$frontendUserRepository
			->expects($this->any())
			->method('updateLastPasswordChangeToCurrentTimestampIfNull')
			->will($this->returnValue(void));
		$frontendUserRepository
			->expects($this->any())
			->method('findUsersWithExpiredPasswordsNotInExpiredUsersGroup')
			->will($this->returnValue(array(new Tx_FeuserPasswordexpiration_Domain_Model_FrontendUser())));

		$extensionManager = $this->getMock(
			'Tx_FeuserPasswordexpiration_Configuration_ExtensionManager',
			array('getIgnoreFeUsersWithPrefix')
		);
		$extensionManager
			->expects($this->any())
			->method('getIgnoreFeUsersWithPrefix')
			->will($this->returnValue(''));

		$task = $this->getMock(
			'Tx_FeuserPasswordexpiration_Scheduler_DetectUsersWithExpiredPasswordsTask',
			array('log', 'getFrontendUserRepository', 'getExtensionManager', 'getExiprationUsergroup')
		);
		$task
			->expects($this->any())
			->method('getFrontendUserRepository')
			->will($this->returnValue($frontendUserRepository));
		$task
			->expects($this->any())
			->method('getExtensionManager')
			->will($this->returnValue($extensionManager));
		$task
			->expects($this->any())
			->method('getExiprationUsergroup')
			->will($this->returnValue(new Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup('test')));

		$task->expects($this->once())->method('log')->with($this->equalTo('Found 1 users with expired password.'));

		$task->execute();
	}

	/**
	 * @test
	 */
	public function logsErrors() {
		$frontendUserRepository = $this->getMock(
			'Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository',
			array(
				'updateLastPasswordChangeToCurrentTimestampIfNull',
				'findUsersWithExpiredPasswordsNotInExpiredUsersGroup'
			)
		);
		$frontendUserRepository
			->expects($this->any())
			->method('updateLastPasswordChangeToCurrentTimestampIfNull')
			->will($this->returnValue(void));
		$frontendUserRepository
			->expects($this->any())
			->method('findUsersWithExpiredPasswordsNotInExpiredUsersGroup')
			->will($this->returnValue(array(new Tx_FeuserPasswordexpiration_Domain_Model_FrontendUser())));

		$extensionManager = $this->getMock(
			'Tx_FeuserPasswordexpiration_Configuration_ExtensionManager',
			array('getIgnoreFeUsersWithPrefix')
		);
		$extensionManager
			->expects($this->any())
			->method('getIgnoreFeUsersWithPrefix')
			->will($this->returnValue(''));

		$persistenceManager = $this->getMock(
			'Tx_Extbase_Persistence_Manager',
			array('persistAll')
		);
		$persistenceManager
			->expects($this->once())
			->method('persistAll')
			->will($this->throwException(new Exception('FooMsg')));

		$task = $this->getMock(
			'Tx_FeuserPasswordexpiration_Scheduler_DetectUsersWithExpiredPasswordsTask',
			array('log', 'getFrontendUserRepository', 'getExtensionManager', 'getExiprationUsergroup', 'getPersistenceManager')
		);
		$task
			->expects($this->any())
			->method('getFrontendUserRepository')
			->will($this->returnValue($frontendUserRepository));
		$task
			->expects($this->any())
			->method('getExtensionManager')
			->will($this->returnValue($extensionManager));
		$task
			->expects($this->any())
			->method('getExiprationUsergroup')
			->will($this->returnValue(new Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup('test')));

		$task
			->expects($this->any())
			->method('getPersistenceManager')
			->will($this->returnValue($persistenceManager));

		$task->expects($this->exactly(2))->method('log');

		$task->execute();
	}

	protected function getMockObserver() {
		$observer = $this->getMock('SplObserver', array('update'));
		$observer->expects($this->once())
			->method('update')
			->with($this->isInstanceOf('Tx_FeuserPasswordexpiration_Scheduler_DetectUsersWithExpiredPasswordsTask'));

		return $observer;
	}

	protected function unsetGloballyConfiguredObservers() {
		unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_feuserpasswordexpiration']['DetectUsersWithExpiredPasswordsTask']['observers']);
	}

	protected function tearDown() {
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'] = $this->globalExtensionConfigurationBackup;
	}
}