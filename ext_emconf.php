<?php

########################################################################
# Extension Manager/Repository config file for ext: "file_realurl"
#
# Auto generated 29-04-2009 10:40
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'File RealURL',
	'description' => 'Replaces uploads/ or fileadmin/ in file paths with a path to the current page. Requires RealURL extension.',
	'category' => 'fe',
	'author' => 'Dmitry Dulepov',
	'author_email' => 'dmitry.dulepov@gmail.com',
	'shy' => '',
	'dependencies' => 'realurl',
	'conflicts' => 'fl_realurl_image',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'realurl' => '',
		),
		'conflicts' => array(
			'fl_realurl_image' => '',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:11:{s:9:"ChangeLog";s:4:"7f48";s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"7913";s:14:"ext_tables.sql";s:4:"f4d0";s:29:"icon_tx_filerealurl_cache.gif";s:4:"475a";s:16:"locallang_db.xml";s:4:"752e";s:7:"tca.php";s:4:"1324";s:19:"doc/wizard_form.dat";s:4:"34a9";s:20:"doc/wizard_form.html";s:4:"00bf";s:33:"static/file_realurl/constants.txt";s:4:"d41d";s:29:"static/file_realurl/setup.txt";s:4:"668c";}',
);

?>