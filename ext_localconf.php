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
	require_once t3lib_extMgm::extPath($_EXTKEY) . 'Classes/System/DeleteUsersWithExpiredPasswordsTask.php';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_FeuserPasswordexpiration_System_DeleteUsersWithExpiredPasswordsTask'] = array(
		'extension' => $_EXTKEY,
		'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:schedulerTask.deleteUsersWithExpiredPasswords.name',
		'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:schedulerTask.deleteUsersWithExpiredPasswords.description',
	);
}

?>