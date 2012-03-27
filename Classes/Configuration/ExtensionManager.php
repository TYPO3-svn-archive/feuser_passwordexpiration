<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * @package feuser_passwordexpiration
 */
class Tx_FeuserPasswordexpiration_Configuration_ExtensionManager implements t3lib_Singleton {
	/**
	 * @var array
	 */
	private $configuration=array();

	/**
	 * constructor - loading the current localconf configuration for the extension
	 *
	 */
	public function __construct() {
		$this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['feuser_passwordexpiration']);
	}

	/**
	 * @return integer
	 */
	public function getExpirationUsergroup() {
		return ( integer ) $this->get ( 'expirationUsergroup' );
	}
	/**
	 * @return string
	 */
	public function getIgnoreFeUsersWithPrefix() {
		return $this->get ( 'ignoreFeUsersWithPrefix' );
	}

	/**
	 * returns configurationvalue for the given key
	 *
	 * @param string $key
	 * @return string/boolean	depending on configuration key
	 */
	private function get($key) {
		return $this->configuration[$key];
	}
}