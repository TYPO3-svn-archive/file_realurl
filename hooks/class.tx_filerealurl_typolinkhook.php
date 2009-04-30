<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Dmitry Dulepov <dmitry@typo3.org>
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

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * $Id$
 */


/**
 * This class contains a hook to typolink to strip HTML suffix from generated URLs.
 *
 * @author	Dmitry Dulepov <dmitry@typo3.org>
 * @package	TYPO3
 * @subpackage	tx_filerealurl
 */
class tx_filerealurl_typolinkhook {

	/**
	 * Content object (set by a calling class
	 *
	 * @var	tslib_cObj
	 */
	public	$cObj;

	/**
	 * Typolink user function to strip HTML suffix from the page URL
	 *
	 * @param	array	$finalTagParts	Typolink data
	 * @return	string	Tag
	 * @see	tslib_cObj::typolink()
	 */
	function removeHtmlSuffix(array &$finalTagParts) {
		$realurl = t3lib_div::getUserObj('EXT:realurl/class.tx_realurl.php:&tx_realurl');
		/* @var $realurl tx_realurl */
		$realurl->setConfig();
		if ($realurl->extConf['fileName']['defaultToHTMLsuffixOnPrev']) {
			$suffix = (t3lib_div::testInt($realurl->extConf['fileName']['defaultToHTMLsuffixOnPrev']) ?
				'.html' : $realurl->extConf['fileName']['defaultToHTMLsuffixOnPrev']);
			if (substr($finalTagParts['url'], -strlen($suffix)) == $suffix) {
				$this->cObj->lastTypoLinkUrl = $finalTagParts['url'] = substr($finalTagParts['url'], 0, -strlen($suffix)) . '/';
				$finalTagParts['TAG'] = '<a href="' . $finalTagParts['url'] .'" ' .
					$finalTagParts['targetParams'] . ' ' .
					$finalTagParts['aTagParams'] . '>';
			}
		}
		return $finalTagParts['TAG'];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/file_realurl/hooks/class.tx_filerealurl_typolinkhook.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/file_realurl/hooks/class.tx_filerealurl_typolinkhook.php']);
}

?>