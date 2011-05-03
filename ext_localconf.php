<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi1',
	array(
		'ExpiredPassword' => 'index'
	),
	array(
		'ExpiredPassword' => 'index'
	)
);

if (TYPO3_MODE == 'BE') {

	require_once t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Scheduler/DetectUsersWithExpiredPasswordsTask.php';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_FeuserPasswordexpiration_Scheduler_DetectUsersWithExpiredPasswordsTask'] = array(
		'extension' => $_EXTKEY,
		'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:schedulerTask.detectUsersWithExpiredPasswords.name',
		'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:schedulerTask.detectUsersWithExpiredPasswords.description',
	);
	
	require_once t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Scheduler/DeleteUsersWithExpiredPasswordsTask.php';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_FeuserPasswordexpiration_Scheduler_DeleteUsersWithExpiredPasswordsTask'] = array(
		'extension' => $_EXTKEY,
		'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:schedulerTask.deleteUsersWithExpiredPasswords.name',
		'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:schedulerTask.deleteUsersWithExpiredPasswords.description',
	);

}

// register hook to update lastPasswordChange field
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_feuserregister']['addObserver'][] = 'EXT:feuser_passwordexpiration/Classes/Hooks/UpdateLastPasswordChangeHook.php:UpdateLastPasswordChangeHook->update';

?>