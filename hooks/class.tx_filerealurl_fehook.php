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
 * This class implements a hook to create RealURLed paths for files.
 *
 * @author	Dmitry Dulepov <dmitry@typo3.org>
 * @package	TYPO3
 * @subpackage	tx_filerealurl
 */
class tx_filerealurl_fehook {

	/**
	 * Indicates if the extension was executed already
	 *
	 * @var	boolean
	 */
	protected	$executed = false;

	/**
	 * Content object for path creation
	 *
	 * @var tslib_cObj
	 */
	protected	$cObj;

	/**
	 * Hooks to TSFE and converts speaking paths to real paths. Outputs the file
	 * if found.
	 *
	 * @return	void
	 */
	public function resolveFilePath() {
		$path = $this->getPathOnly(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));

		// Check if it is in the cache
		list($row) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('file_path',
			'tx_filerealurl_cache', 'realurl_path_hash=' . crc32($path) .
			' AND realurl_path=' .
			$GLOBALS['TYPO3_DB']->fullQuoteStr($path, 'tx_filerealurl_cache'),
			'', '', 1);
		if (is_array($row)) {
			// Send it
			$filePath = PATH_site . rawurldecode($row['file_path']);
			if (!file_exists($filePath)) {
				header('HTTP/1.1 404 Not found');
			}
			else {
				$mtime = filemtime($filePath);
				$size = filesize($filePath);
				$eTag = sprintf('%x-%x', $mtime, $size);
				$lastModified = date('r', $mtime);
				$sendFile = (!isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ||
					$mtime > strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) ||
					(isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
						$_SERVER['HTTP_IF_NONE_MATCH'] != $eTag));
				if (!$sendFile) {
					header('HTTP/1.1 304 Not Modified');
					header('Last-modified: ' . $lastModified);
					header('E-tag: "' . $eTag . '"');
					header('Content-type: ' . $this->getContentType($filePath));
				}
				else {
					header('Last-modified: ' . $lastModified);
					header('E-tag: "' . $eTag . '"');
					header('Content-length: ' . $size);
					header('Content-type: ' . $this->getContentType($filePath));
					readfile($filePath);
				}
			}
			exit;
		}
	}

	/**
	 * Hooks to the TSFE to replace paths to speaking paths.
	 *
	 * @param	array	$params	Parameters
	 * @param	tslib_fe	$pObj	Calling object
	 * @return	void
	 */
	public function convertFilePaths(array &$params, tslib_fe &$pObj) {
		if (($pObj->no_cache || !$this->executed) &&
				is_array($pObj->tmpl->setup['tx_filerealurl.']) &&
				$pObj->tmpl->setup['tx_filerealurl.']['enable']) {
			$this->doConvertFilePaths($pObj);
			$this->executed = !$pObj->isINTincScript();
		}
	}

	/**
	 * Searches the content for matches and calls processMatches() to generate
	 * and replace paths.
	 *
	 * @param	tslib_fe	$pObj	Calling object
	 * @return	void
	 */
	protected function doConvertFilePaths(tslib_fe &$pObj) {
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
		// Walk each definition replacing paths
		foreach ($pObj->tmpl->setup['tx_filerealurl.']['files.'] as $fileDef) {
			$matches = array();
			if (preg_match_all($fileDef['match'], $pObj->content, $matches, PREG_OFFSET_CAPTURE)) {
				$pObj->content = $this->processMatches($pObj->content, $matches, $fileDef);
			}
		}
		$this->cObj = null;
	}

	/**
	 * Creates a new path for each match from $matches.
	 *
	 * @param	string	$content	Page content
	 * @param	array	$matches	Matches from preg_match
	 * @param	array	$config		Configuration
	 * @return	string	Updated content
	 */
	protected function processMatches($content, array $matches, array $config) {
		$group = $config['matchGroup'];
		// Go in backward order to keep offsets working
		for ($i = count($matches[$group]) - 1; $i >= 0; $i--) {
			$currentPath = $matches[$group][$i][0];
			if (!$config['exclude'] || !preg_match($config['exclude'], $currentPath)) {

				// Create a path
				$newPath = $this->createNewPath($currentPath, $config);

				// Add to cache if necessary
				$newPathCopy = $this->getPathOnly($newPath);
				$newPathHash = crc32($newPathCopy);
				list($info) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('COUNT(*) AS t',
					'tx_filerealurl_cache', 'realurl_path_hash=' . $newPathHash .
					' AND realurl_path=' .
					$GLOBALS['TYPO3_DB']->fullQuoteStr($newPathCopy, 'tx_filerealurl_cache'));
				if ($info['t'] == 0) {
					$fields = array(
						'file_path' => $this->getPathOnly($currentPath),
						'realurl_path_hash' => $newPathHash,
						'realurl_path' => $newPathCopy
					);
					$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_filerealurl_cache', $fields);
				}

				// Update the content
				$content = substr($content, 0, $matches[$group][$i][1]) .
					t3lib_div::locationHeaderUrl($newPath) .
					substr($content, $matches[$group][$i][1] + strlen($currentPath));
			}
		}

		return $content;
	}

	/**
	 * Creates a new path from the old path.
	 *
	 * @param	string	$currentPath	Current path
	 * @param	array	$config	Configuration
	 * @return	string	A new path
	 */
	protected function createNewPath($currentPath, array $config) {
		$fileParts = pathinfo($this->getPathOnly($currentPath));
		if (strpos($currentPath, 'typo3temp/') !== false &&
				preg_match('/(?:_|-)[a-f01-9]{6,10}$/i', $fileParts['filename'])) {
			// This is a file from typo3temp/ and it has a hash already
			$this->cObj->setCurrentVal(sprintf('%s.%s', $fileParts['filename'],
				$fileParts['extension']));
		}
		else {
			// Need to hash this one
			$replacementChar = ($this->config['config']['simulateStaticDocuments_replacementChar'] ?
				$this->config['config']['simulateStaticDocuments_replacementChar'] : '_');
			$this->cObj->setCurrentVal(sprintf('%s%s%x.%s', $fileParts['filename'],
				$replacementChar, crc32($currentPath), $fileParts['extension']));
		}
		$newPath = $this->cObj->cObjGetSingle($config['path'], $config['path.']);
		return $newPath;
	}

	/**
	 * Parses URL and returns only the path from it
	 *
	 * @param	string	$url	URL to parse
	 * @return	string	Path
	 */
	protected function getPathOnly($url) {
		$parts = @parse_url($url);
		return (is_array($parts) && $parts['path'] ? substr($parts['path'], 1) : $url);
	}

	/**
	 * Determines content type of the file
	 *
	 * @param	string	$filePath	File path
	 * @return	string	Content type
	 */
	protected function getContentType($filePath) {
		$result = '';
		if (is_callable('finfo_file')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$result = finfo_file($finfo, basename($filePath));
			finfo_close($finfo);
		}
		elseif (is_callable('mime_content_type')) {
			$result = mime_content_type(basename($filePath));
		}
		elseif (($im = @getimagesize($filePath))) {
			$result = $im['mime'];
		}
		else {
			$fileParts = pathinfo($filePath);
			$result = 'application/' . $fileParts['extension'];
		}
		return $result;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/file_realurl/hooks/class.tx_filerealurl_fehook.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/file_realurl/hooks/class.tx_filerealurl_fehook.php']);
}

?>