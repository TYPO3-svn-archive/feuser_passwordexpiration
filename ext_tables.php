<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Frontend User Password Expiration');


// configure BE-module (as web-function) to reset expired frontend-user
if (TYPO3_MODE == 'BE') {
	require_once PATH_tx_feuser_passwordexpiration . 'Classes/Mods/ResetExpiredFrontendUser.php';
	t3lib_extMgm::insertModuleFunction(
		'web_func',        
		'Tx_FeuserPasswordexpiration_Mods_ResetExpiredFrontendUser',
		PATH_tx_feuser_passwordexpiration.'Classes/Mods/ResetExpiredFrontendUser.php',
		'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xml:moduleFunction.ResetExpiredFrontendUser'
	);
}
?>