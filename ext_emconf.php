<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "we_google_analytics".
 *
 * Auto generated 02-07-2014 15:23
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Google Analytics',
	'description' => 'This Plugin will add the Google Analytics Code to your site automagically. All types of Google Analytics supported (urchin.js, mobile, ga.js and analytics.js). Easy use of Googles Cookie Law (Googles Opt-Out function via cookie) implementation. Now also added Do Not Track support.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '2.2.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Andreas Becker - websedit AG',
	'author_email' => 'extensions@websedit.de',
	'author_company' => 'websedit AG',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.0-8.99.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:9:{s:9:"ChangeLog";s:4:"0f1e";s:30:"class.tx_wegoogleanalytics.php";s:4:"23df";s:37:"class.tx_wegoogleanalytics_fehook.php";s:4:"f849";s:12:"ext_icon.gif";s:4:"9181";s:17:"ext_localconf.php";s:4:"2cd2";s:6:"ga.php";s:4:"6fdf";s:10:"README.txt";s:4:"fc0f";s:14:"doc/manual.pdf";s:4:"fa62";s:14:"doc/manual.sxw";s:4:"168f";}',
	'suggests' => array(
	),
);

?>
