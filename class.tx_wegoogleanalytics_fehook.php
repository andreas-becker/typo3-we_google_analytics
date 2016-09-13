<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003 Boris Nicolai (boris.nicolai@andavida.com)
*  (c) 2010-2014 Modification by Andreas Becker <extensions@websedit.de>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
require_once (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('we_google_analytics') . 'class.tx_wegoogleanalytics.php');
/**
 * Hook for integrating Google Analytics into the website
 *
 * @author Andreas Becker - websedit AG <extensions@websedit.de>
 */
class tx_wegoogleanalytics_fehook {
	/**
	 * Modification for pages that doesn't get cached (COA_/USER_INT)
	 * @param array &$params
	 * @param object &$that
	 * @return void
	 */
	function intPages (&$params, &$that) {
		if (!$GLOBALS['TSFE']->isINTincScript()) {
			return;
		}
			// $tx_wegoogleanalytics = new tx_wegoogleanalytics();
		$tx_wegoogleanalytics = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_wegoogleanalytics');
		$tx_wegoogleanalytics->main ($params['pObj']->content, $GLOBALS['TSFE']->config['config']['tx_we_google_analytics.']);
	}
	/**
	 * Modification for pages on their way into the cache
	 * @param array &$params
	 * @param object &$that
	 * @return void
	 */
	function noIntPages (&$params, &$that) {
		if ($GLOBALS['TSFE']->isINTincScript()) {
			return;
		}
			// $tx_wegoogleanalytics = new tx_wegoogleanalytics();
		$tx_wegoogleanalytics = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_wegoogleanalytics');
		$tx_wegoogleanalytics->main ($params['pObj']->content, $GLOBALS['TSFE']->config['config']['tx_we_google_analytics.']);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/we_google_analytics/class.tx_wegoogleanalytics_fehook.php']) {
	require_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/we_google_analytics/class.tx_wegoogleanalytics_fehook.php']);
}
?>