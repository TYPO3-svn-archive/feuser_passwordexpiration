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
	const DEACTIVATION_TYPE_DELETE = 1;
	const DEACTIVATION_TYPE_HIDE = 2;

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
				$taskInfo['deactivationType'] = self::DEACTIVATION_TYPE_HIDE;
			} elseif($parentObject->CMD == 'edit') {
				$taskInfo['expirationDurationForDeletion'] = $task->expirationDurationForDeletion;
				$taskInfo['deactivationType'] = $task->deactivationType;
			} else {
				$taskInfo['expirationDurationForDeletion'] = '';
				$taskInfo['deactivationType'] = '';
			}
		}

		$additionalFields = array();

		// Write the code for the field 'expirationDurationForDeletion'
		$fieldID = 'task_expirationDurationForDeletion';
		$additionalFields[$fieldID] = array();
		$additionalFields[$fieldID]['code']  = $this->renderCodeForFieldExpirationDurationForDeactivation($fieldID, $taskInfo['expirationDurationForDeletion']);
		$additionalFields[$fieldID]['label'] = 'Expiration duration (seconds)';

		// Write the code for the field 'deactivationType'
		$fieldID = 'task_deactivationType';
		$additionalFields[$fieldID] = array();
		$additionalFields[$fieldID]['code']  = $this->renderCodeForFieldDeactivationType($fieldID, $taskInfo['deactivationType']);
		$additionalFields[$fieldID]['label'] = 'Deactivation Type';

		return $additionalFields;
	}

    /**
     * @param array $submittedData
     * @param tx_scheduler_Task $task
     */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		$task->expirationDurationForDeletion = (int) trim($submittedData['expirationDurationForDeletion']);
		$task->deactivationType = (int) trim($submittedData['deactivationType']);
	}

	/**
	 * @param array &$submittedData
	 * @param tx_scheduler_Module $parentObject
	 * @return boolean
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $parentObject) {
		$expirationDurationForDeletionIsValid = false;
		$deactivationTypeIsValid = false;

		// check field 'expirationDurationForDeletion'
		if ( t3lib_div::intval_positive( (int) trim($submittedData['expirationDurationForDeletion'])) > 0 ) {
			$expirationDurationForDeletionIsValid = true;
		} else {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:feuser_passwordexpiration/Resources/Private/Language/locallang_db.xml:schedulerTask.deactivateUsersWithExpiredPasswords.invalidExpirationDurationForDeletion'), t3lib_FlashMessage::ERROR);
		}

		// check field 'deactivationType'
		$deactivationType = (int) trim($submittedData['deactivationType']);
		if ( t3lib_div::intval_positive($deactivationType) > 0 && in_array($deactivationType, $this->getDeactivationTypes())) {
			$deactivationTypeIsValid = true;
		} else {
			$parentObject->addMessage($GLOBALS['LANG']->sL('LLL:EXT:feuser_passwordexpiration/Resources/Private/Language/locallang_db.xml:schedulerTask.deactivateUsersWithExpiredPasswords.invalidDeactivationType'), t3lib_FlashMessage::ERROR);
		}

		return ($expirationDurationForDeletionIsValid && $deactivationTypeIsValid);
	}

	/**
	 * @return array
	 */
	private function getDeactivationTypes() {
		return array( self::DEACTIVATION_TYPE_DELETE, self::DEACTIVATION_TYPE_HIDE );		
	}
	/**
	 * render HTML-code for field 'deactivationType'
	 * 
	 * @param string $fieldID
	 * @param string $fieldValue
	 * @return string
	 */
	private function renderCodeForFieldDeactivationType($fieldID, $fieldValue) {
		$fieldCode = '<select name="tx_scheduler[deactivationType]" id="' . $fieldID . '" size="1" >';
		foreach($this->getDeactivationTypes() as $deactivationType) {
			$label      = $GLOBALS['LANG']->sL('LLL:EXT:feuser_passwordexpiration/Resources/Private/Language/locallang_db.xml:schedulerTask.deactivateUsersWithExpiredPasswords.labelDeactivationType_'.$deactivationType);
			$selected   = $deactivationType === $fieldValue ? ' selected="selected"' : '';
			$fieldCode .= '<option value="'.$deactivationType.'"'.$selected.'>'.$label.'</option>';
		}
		$fieldCode .= '</select>';

		return $fieldCode;
	}
	/**
	 * render HTML-code for field 'expirationDurationForDeletion'
	 * 
	 * @param string $fieldID
	 * @param string $fieldValue
	 * @return string
	 */
	private function renderCodeForFieldExpirationDurationForDeactivation($fieldID, $fieldValue) {
		return '<input type="text" name="tx_scheduler[expirationDurationForDeletion]" id="' . $fieldID . '" value="' . $fieldValue . '" size="10" />';
	}
}