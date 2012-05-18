<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

/**
 * Static file cache extension
 *
 * @package eft
 * @subpackage mods
 * @author	Michiel Roos <extensions@netcreators.com>
 */
class Tx_FeuserPasswordexpiration_Mods_ResetExpiredFrontendUser extends t3lib_extobjbase {
	/**
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup>
	 */
	private $exiprationUsergroup;
	/**
	 * @var Tx_FeuserPasswordexpiration_Configuration_ExtensionManager
	 */
	private $extensionManager;
	/**
	 * @var string
	 */
	private $infoMessage;
	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	private $objectManager;

	/**
	 * MAIN function for static publishing information
	 *
	 * @return	string		Output HTML for the module.
	 */
	function main()	{
		// Handle actions:
		$this->handleActions();

		// show buttons
		$content = '';
		$content .= $this->renderActionButton('resetExpiredFrontendUser', $this->getLabel('resetExpiredFrontendUser'));

		// show info-message
		$content .= '<br /><br /><strong>'.$this->getInfoMessage().'</strong>';

		return $content;
	}

	/**
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup>
	 */
	private function getExiprationUsergroup() {
		if($this->exiprationUsergroup === NULL) {
			if($this->getExtensionManager()->getExpirationUsergroup() === 0) {
				throw new RuntimeException( 'expirationUsergroup must be defined in extension-configuration!' );
			}
			$frontendUserGroupRepository = $this->getObjectManager()->get ( 'Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserGroupRepository' );
			$this->exiprationUsergroup = $frontendUserGroupRepository->findByUid( $this->getExtensionManager()->getExpirationUsergroup() );
		}
		return $this->exiprationUsergroup;		
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
	 * @return Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository
	 */
	private function getFrontendUserRepository() {
		return $this->getObjectManager()->get ( 'Tx_FeuserPasswordexpiration_Domain_Repository_FrontendUserRepository' );
	}
	/**
	 * @return string
	 */
	private function getInfoMessage() {
		return $this->infoMessage;
	}
	/**
	 * @param string $key
	 * @return string
	 */
	private function getLabel($key) {
		global $LANG;
		return $LANG->sL('LLL:EXT:feuser_passwordexpiration/Resources/Private/Language/locallang_db.xml:moduleFunction.ResetExpiredFrontendUser.'.$key, 1);
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
	/**
	 * @return integer
	 * @throws RuntimeException
	 */
	private function getPageId() {
		$pageId = (integer) t3lib_div::_GP('id');
		if($pageId > 0) {
			return $pageId;
		}
		throw new RuntimeException('no page selected!');
	}
	/**
	 * @return Tx_Extbase_Persistence_Manager
	 */
	private function getPersistenceManager() {
		return $this->getObjectManager()->get('Tx_Extbase_Persistence_Manager');
	}

	/**
	 * Handles incoming actions (e.g. removing all expired pages).
	 *
	 * @return	void
	 */
	private function handleActions() {
		$action = t3lib_div::_GP('ACTION');

		if (isset($action['resetExpiredFrontendUser'])) {
			$expiredUsers = $this->getFrontendUserRepository()->findUsersWhichContainToExpirationGroup( $this->getExiprationUsergroup(), $this->getPageId() );
			if(count($expiredUsers) > 0) {
				foreach ($expiredUsers as $expiredUser) {
					$expiredUser->removeUsergroup( $this->getExiprationUsergroup() );
					$expiredUser->setLastPasswordChange( time() );
				}
				$this->getPersistenceManager()->persistAll();
				$infoMessage = sprintf($this->getLabel('expiredFrontendUserReset'), count($expiredUsers));
			} else {
				$infoMessage = $this->getLabel('noExpiredFrontendUserFound');
			}
			$this->setInfoMessage( $infoMessage );
		}
	}

	/**
	 * Renders a single action button,
	 *
	 * @param	string $elementName Name attribute of the element
	 * @param	string $elementLabel Label of the action button
	 * @param	string $confirmationText (optional) Confirmation text - will not be used if empty
	 * @return	string The HTML representation of an action button
	 */
	private function renderActionButton($elementName, $elementLabel) {
		return '<input type="submit" name="ACTION[' . htmlspecialchars($elementName) . ']" value="' . $elementLabel . '" style="margin-right: 10px;" />';
	}

	/**
	 * @param string $infoMessage
	 */
	private function setInfoMessage($infoMessage) {
		$this->infoMessage = $infoMessage;
	}
}