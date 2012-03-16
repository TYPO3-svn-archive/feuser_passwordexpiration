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
 * Extends Default Frontend User
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_FeuserPasswordexpiration_Domain_Model_FrontendUser extends Tx_Extbase_Domain_Model_FrontendUser {
	/**
	 * @var integer
	 */
	protected $deleted;
	/**
	 * @var integer
	 */
	protected $disable;
	/**
	 * @var integer
	 */
	protected $lastPasswordChange;	
	/**
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_FeuserPasswordexpiration_Domain_Model_FrontendUserGroup>
	 */
	protected $usergroup;

	/**
	 * activate user
	 */
	public function activate() {
		$this->setDisable( 0 );
	}
	/**
	 * delete user
	 */
	public function delete() {
		$this->setDeleted( 1 );
	}
	/**
	 * disable user
	 */
	public function disable() {
		$this->setDisable( 1 );
	}

	/**
	 * @return integer
	 */
	public function getDeleted() {
		return $this->deleted;
	}
	/**
	 * @return integer
	 */
	public function getDisable() {
		return $this->disable;
	}
	/**
	 * @return integer
	 */
	public function getLastPasswordChange() {
		return $this->lastPasswordChange;
	}

	/**
	 * @param integer $deleted
	 */
	public function setDeleted($deleted) {
		$this->deleted = $deleted;
	}
	/**
	 * @param integer $disable
	 */
	public function setDisable($disable) {
		$this->disable = $disable;
	}
	/**
	 * @param integer
	 * @return void
	 */
	public function setLastPasswordChange($lastPasswordChange) {
		$this->lastPasswordChange = $lastPasswordChange;
	}
}
?>