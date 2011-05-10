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

require_once(t3lib_extMgm::extPath('scheduler').'interfaces/interface.tx_scheduler_additionalfieldprovider.php');

/**
 * class to define the additional field 'expirationDurationForDeletion'
 */
class tx_FeuserPasswordexpiration_Scheduler_DeleteUsersWithExpiredPasswordsAdditionalFields implements tx_scheduler_AdditionalFieldProvider {
	
	/**
	 * @param array &$taskInfo
	 * @param unknown_type $task
	 * @param tx_scheduler_Module $parentObject
	 * @return unknown
	 */
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $parentObject) {

		if (empty($taskInfo['expirationDurationForDeletion'])) {
			if ($parentObject->CMD == 'add') {
				$taskInfo['expirationDurationForDeletion'] = 15552000;
			} elseif($parentObject->CMD == 'edit') {
				$taskInfo['expirationDurationForDeletion'] = $task->expirationDurationForDeletion;
			} else {
				$taskInfo['expirationDurationForDeletion'] = '';
			}
		}
		
		$additionalFields = array();
		
		// Write the code for the field
		$fieldID = 'task_expirationDurationForDeletion';
		$fieldCode = '<input type="text" name="tx_scheduler[expirationDurationForDeletion]" id="' . $fieldID . '" value="' . $taskInfo['expirationDurationForDeletion'] . '" size="10" />';
		$additionalFields[$fieldID] = array(
			'code'     => $fieldCode,
			'label'    => 'Expiration duration (seconds)'
		);

		return $additionalFields;
	}
    
    /**
     * @param array $submittedData
     * @param tx_scheduler_Task $task
     */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		$task->expirationDurationForDeletion = (int) $submittedData['expirationDurationForDeletion'];
	}
	
	/**
	 * @param array &$submittedData
	 * @param tx_scheduler_Module $parentObject
	 * @return boolean
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {
		$expirationDurationForDeletionIsValid = false;

		if ( t3lib_div::intval_positive( (int) trim($submittedData['expirationDurationForDeletion'])) > 0 ) {
			$expirationDurationForDeletionIsValid = true;
		} else {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:feuser_passwordexpiration/Resources/Private/Language/locallang_db.xml:schedulerTask.deleteUsersWithExpiredPasswords.invalidExpirationDurationForDeletion'), t3lib_FlashMessage::ERROR);
		}

		return $expirationDurationForDeletionIsValid;
	}

}