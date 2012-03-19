<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2012 AOE media GmbH <dev@aoemedia.de>
 * All rights reserved
 *
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
define ( 'PATH_tx_feuser_passwordexpiration', t3lib_extMgm::extPath ( $_EXTKEY ) );

/**
 * register scheduer-tasks
 */
if (TYPO3_MODE == 'BE') {
	require_once PATH_tx_feuser_passwordexpiration . 'Classes/Scheduler/DetectUsersWithExpiredPasswordsTask.php';
	require_once PATH_tx_feuser_passwordexpiration . 'Classes/Scheduler/DetectUsersWithExpiredPasswordsAdditionalFields.php';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_FeuserPasswordexpiration_Scheduler_DetectUsersWithExpiredPasswordsTask'] = array(
		'extension' => $_EXTKEY,
		'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:schedulerTask.detectUsersWithExpiredPasswords.name',
		'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:schedulerTask.detectUsersWithExpiredPasswords.description',
		'additionalFields' => 'tx_FeuserPasswordexpiration_Scheduler_DetectUsersWithExpiredPasswordsAdditionalFields',
	);

	require_once PATH_tx_feuser_passwordexpiration . 'Classes/Scheduler/DeleteUsersWithExpiredPasswordsTask.php';
	require_once PATH_tx_feuser_passwordexpiration . 'Classes/Scheduler/DeleteUsersWithExpiredPasswordsAdditionalFields.php';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_FeuserPasswordexpiration_Scheduler_DeleteUsersWithExpiredPasswordsTask'] = array(
		'extension' => $_EXTKEY,
		'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:schedulerTask.deactivateUsersWithExpiredPasswords.name',
		'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:schedulerTask.deactivateUsersWithExpiredPasswords.description',
		'additionalFields' => 'tx_FeuserPasswordexpiration_Scheduler_DeleteUsersWithExpiredPasswordsAdditionalFields',
	);
}

/**
 * register hooks to update lastPasswordChange field and/or remove expiration-UserGroup from FE-User
 */
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_feuserregister']['addObserver'][] = 'EXT:'.$_EXTKEY.'/Classes/Hooks/UpdateLastPasswordChangeHook.php:tx_FeuserPasswordexpiration_Hooks_UpdateLastPasswordChangeHook';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_eft']['resetPassword_sendRequest']['observers'][] = 'EXT:'.$_EXTKEY.'/Classes/Hooks/ResetPasswordSendRequest.php:tx_FeuserPasswordexpiration_Hooks_ResetPasswordSendRequest';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_eft']['resetPassword_setPassword']['observers'][] = 'EXT:'.$_EXTKEY.'/Classes/Hooks/ResetPasswordSetPassword.php:tx_FeuserPasswordexpiration_Hooks_ResetPasswordSetPassword';
?>