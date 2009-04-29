<?php
if (!defined('TYPO3_MODE')) {
	die('No TYPO3_MODE!');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['connectToDB'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_filerealurl_fehook.php:tx_filerealurl_fehook->resolveFilePath';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_filerealurl_fehook.php:&tx_filerealurl_fehook->convertFilePaths';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-cached'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_filerealurl_fehook.php:&tx_filerealurl_fehook->convertFilePaths';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/hooks/class.tx_filerealurl_fehook.php:&tx_filerealurl_fehook->convertFilePaths';

?>