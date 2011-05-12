<?php

########################################################################
# Extension Manager/Repository config file for ext "feuser_passwordexpiration".
#
# Auto generated 29-04-2011 15:45
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Frontend User Password Expiration',
	'description' => 'Feuser passwords expire after defined time since last password change.',
	'category' => 'plugin',
	'author' => 'Max Beer',
	'author_email' => 'max.beer@aoemedia.de',
	'author_company' => 'AOE media GmbH',
	'shy' => '',
	'dependencies' => 'extbase,fluid,feuserregister',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.1.0',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0',
			'typo3' => '4.3.6',
			'extbase' => '1.3.0',
			'fluid' => '1.3.0',
			'feuserregister' => '0.2.1',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:9:{s:12:"ext_icon.gif";s:4:"e922";s:17:"ext_localconf.php";s:4:"22c6";s:14:"ext_tables.php";s:4:"514b";s:14:"ext_tables.sql";s:4:"d41d";s:16:"kickstarter.json";s:4:"c1a6";s:34:"Configuration/TypoScript/setup.txt";s:4:"8333";s:40:"Resources/Private/Language/locallang.xml";s:4:"f83b";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"4e59";s:35:"Resources/Public/Icons/relation.gif";s:4:"e615";}',
);

?>