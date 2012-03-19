<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Juergen Kussmann <juergen.kussmann@aoemedia.de>, AOE media GmbH
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

require_once(t3lib_extMgm::extPath('eft') . 'domain/model/observer/interface.tx_eft_domain_model_observer_observer.php');
require_once PATH_tx_feuser_passwordexpiration . 'Classes/Hooks/AbstractHook.php';

/**
 * Hook to remove FE-user from 'password-expired'-userGroup
 * NOTE: Use for this class 'tx' instead of 'Tx' in class name
 */
class tx_FeuserPasswordexpiration_Hooks_ResetPasswordSetPassword extends Tx_FeuserPasswordexpiration_Hooks_AbstractHook implements tx_eft_domain_model_observer_observer {
	/**
	 * update fe-user
	 * @param tx_eft_domain_model_observer_subject $subject
	 */
	public function update(tx_eft_domain_model_observer_subject $subject) {
		$params = $subject->getParams();
		$this->setFrontendUser( $this->createFrontendUser( $params['feUserId'] ) );
		$this->removeFrontendUserFromExpirationUsergroup();
		$this->updateLastPasswordChangeOfFrontendUser();
		$this->persistAll();
	}
}