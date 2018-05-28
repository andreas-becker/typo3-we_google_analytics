<?php
/**************************************************************
*  Copyright notice
*
*  (c) 2010-2014 Andreas Becker - websedit AG <extensions@websedit.de>
*
*  All rights reserved
*
*  This script is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; version 2 of the License.
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
 * Based on the extension 'm1_google_analytics'
 * m1_google_analytics written by:
 * Dimitri Tarassenko (mitka@mitka.us),
 * Bjoern Kraus (kraus@phoenixwebsolutions.de)
 */

//if (!class_exists('tslib_pibase')) require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('frontend') . 'Classes/ContentObject/ContentObjectRenderer.php';


/**
 * This extension is used to insert the Google Analytics
 * Tracking code into your website
 *
 * @author Andreas Becker - websedit AG <extensions@websedit.de>
 *
 */
class tx_wegoogleanalytics {

	public $prefixId = 'tx_wegoogleanalytics';
	public $scriptRelPath = 'tx_wegoogleanalytics.php';
	public $extKey = 'we_google_analytics';

	/**
	 * Insert Google Analytics Code before the output of the page
	 *
	 * @param str &$content Pagecontent
	 * @param str $conf TyposcriptConfig
	 * @return Void
	 */
	public function main ( &$content, $conf ) {
		$this->content = &$content;
		$this->conf = $conf;

		if (!$this->conf['account']) {
			return;
		}

		$content = $this->process($this->content);
	}

	/**
	 * Processes the configuration given by TypoScript
	 *
	 * @param str $con Pagecontent
	 * @return str Pagecontent
	 *
	 */
	protected function process( $con ) {
			// validate given account number
		if (preg_match('#^\b(UA|MO)-\d{4,10}-\d{1,4}\b$#i', $this->conf['account'])) {
			$accountCheckPassed = 1;
		} else {
			$accountCheckPassed = 0;
		}

		$content = $con;
		if ($accountCheckPassed) {
			switch($this->conf['type']){
				case 'mobile':
					$content = $this->insertMobileGaCode($con);
					break;
				case 'sync':
					$content = $this->insertSyncGaCode($con);
					break;
				case 'universal':
					if ($this->conf['UAdualtag'] == 1) {
						/**
						 * The analytics.js snippet is part of Universal Analytics,
						 * which is currently in public beta. New users should use analytics.js.
						 * Existing ga.js users should create a new web property for analytics.js
						 * and dual tag their site. It is perfectly safe to include both ga.js and
						 * analytics.js snippets on the same page.
						 */
						$con2 = $this->insertUniversalGaCode($con);
						$content = $this->insertAsyncGaCode($con2);
					} else {
						$content = $this->insertUniversalGaCode($con);
					}
					break;
				case 'async':
				default:
					// Async is default
				$content = $this->insertAsyncGaCode($con);
			}
		} else {
			$errorMessage = '<!--' . chr(10);
			$errorMessage .= '     Ooops: Syntaxcheck of Google Analytics Account Number failed!' . chr(10);
			$errorMessage .= '     Maybe misspelled entry in config.tx_we_google_analytics.account.' . chr(10);
			$errorMessage .= '     You used ' . htmlspecialchars($this->conf['account']) . chr(10);
			$errorMessage .= '     Please use the following format UA-xxxx-y ,' . chr(10);
			$errorMessage .= '     or for mobile tracking, use MO-xxxx-y.' . chr(10);
			$errorMessage .= '-->' . chr(10);
			$content = $this->insertTrackerCode($con, $errorMessage, 'headEnd');
		}
		return $content;
	}

	/**
	 * Google Analytics Mobile Tracking Code (extended)
	 *
	 * @return str TrackerUrl
	 *
	 */
	protected function googleAnalyticsGetImageUrl() {
			// Copyright 2009 Google Inc. All Rights Reserved.
		$url = '';
		$url .= \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($this->extKey) . 'ga.php?';
		$url .= 'utmac=' . str_replace('UA', 'MO', $this->conf['account']);
		$url .= '&utmn=' . rand(0, 0x7fffffff);
		$referer = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('HTTP_REFERER');
		$query = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('QUERY_STRING');
		$path = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('REQUEST_URI');
		if (empty($referer)) {
			$referer = '-';
		}
		$url .= '&utmr=' . urlencode($referer);
		if (!empty($path)) {
			$url .= '&utmp=' . urlencode($path);
		}
		$url .= '&guid=ON';
		return($url);
	}

	/**
	 * Google Analytics Mobile Tracking Code
	 *
	 * @param str $con Pagecontent
	 * @return str Pagecontent
	 *
	 */
	protected function insertMobileGaCode($con) {
		$gaMobileAfterContent = '<!-- Google Analytics Mobile Tracking Code by we_google_analytics -->' . chr(10) .
			'<img src="' . $this->googleAnalyticsGetImageUrl() . '" alt="" />';
		$content = $this->insertTrackerCode($con, $gaMobileAfterContent, 'bodyEnd');
		return $content;
	}

	/**
	 * Google Analytics sync (urchin.js)
	 *
	 * @param str $con Pagecontent
	 * @return str Pagecontent
	 *
	 */
	protected function insertSyncGaCode($con) {

		$anonymizeIp = '';
		if ($this->conf['anonymized'] == 1) {
			$anonymizeIp = '_gat._anonymizeIp();';
		}

		$trackpageload = '';
		if ($this->conf['trackpageload'] == 1) {
			$trackpageload = 'pageTracker._trackPageLoadTime();';
		}
		/**
		 * Remove _gaq. and _gat. if set
		 * also remove configs without underscore (e.g. account)
		 *
		 */
		$gaConf = array();
		foreach ($this->conf as $param => $val) {
			$param = htmlspecialchars($param);
			if (is_array($val)) {
				foreach ($val as $paramTwo => $valTwo) {
					$paramTwo = htmlspecialchars($paramTwo);
					$valTwo = htmlspecialchars($valTwo);
					if (substr($paramTwo, 0, 1) == '_') {
						$gaConf[$paramTwo] = $valTwo;
					}
				}
			} else {
				$val = htmlspecialchars($val);
				if (substr($param, 0, 1) == '_') {
					$gaConf[$param] = $val;
				}
			}
		}

		$options = '';
		foreach ($gaConf as $param => $val) {
			if ($val != 'true' && $val != 'false' && $val != '1' && $val != '0' && strpos($val, ',') === FALSE) {
				$val = '"' . $val . '"';
			}
			$val = str_replace('&amp;', '&', $val);
			$options .= 'pageTracker.' . $param . '(' . $val . ');' . chr(10);
		}

		$gaSyncBeforeContent = chr(10) . '<script type="text/javascript">
	/* <![CDATA[ */
	 var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	 document.write("\<script src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'>\<\/script>" );
	/* ]]> */
	</script>
	<script type="text/javascript">
	/* <![CDATA[ */
	 try{
		var pageTracker = _gat._getTracker("' . str_replace('MO', 'UA', $this->conf['account']) . '");
		' . $anonymizeIp . '
		' . $options . '
		pageTracker._initData();
	 } catch(err) {}
	/* ]]> */
	</script>';

		$gaSyncAfterContent = '<script type="text/javascript">
	/* <![CDATA[ */
	 try{
		pageTracker._trackPageview();
		' . $trackpageload . '
	 } catch(err) {}
	/* ]]> */
	</script>';

		$con = $this->insertTrackerCode($con, $gaSyncBeforeContent, 'bodyBegin');
		$content = $this->insertTrackerCode($con, $gaSyncAfterContent, 'bodyEnd');
		return $content;
	}

	/**
	 * Google Analytics async (ga.js)
	 *
	 * @param str $con Pagecontent
	 * @return str Pagecontent
	 *
	 */
	protected function insertAsyncGaCode($con) {

		$anonymizeIp = '';
		if ($this->conf['anonymized'] == 1) {
			$anonymizeIp = " ['_gat._anonymizeIp'],";
		}

		$trackfiles = '';
		$trackfiletypes = '';
		/* If filetracking is enabled, clean the userinput */
		if ($this->conf['trackfiles'] == 1) {
			if (!empty($this->conf['trackfiles.']['path'])) {
				$trackfiles = str_replace(' ', '', trim($this->conf['trackfiles.']['path']));
				$trackfiles = str_replace(',', '|', addslashes(strip_tags($trackfiles)));
				$trackfiles = str_replace('/', '\/', addslashes($trackfiles));
			} else {
				$trackfiles = 'fileadmin|uploads|typo3temp';
			}
			/* Track only given filetypes */
			if (!empty($this->conf['trackfiles.']['types'])) {
				$trackfiletypes = str_replace(' ', '', trim($this->conf['trackfiles.']['types']));
				$trackfiletypes = str_replace(',', '|', addslashes(strip_tags($trackfiletypes)));
				$trackfiletypes = str_replace('/', '\/', addslashes($trackfiletypes));
			} else {
				$trackfiletypes = '\w{1,3}';
			}
		}
		/**
		 * Add Outbound Links
		 * http://support.google.com/analytics/bin/answer.py?hl=en&answer=1136920
		 *
		 */
		$trackexternaltypes = 'http|https|mailto|ftp';
		$trackexternaljs = FALSE;
		if ($this->conf['trackexternal'] == 1) {
			$trackexternaljs = TRUE;
			if (!empty($this->conf['trackexternal.']['types'])) {
					// Remove Spaces
				$trackexternaltypes = str_replace(' ', '', trim($this->conf['trackexternal.']['types']));
					// Convert comma to pipe
				$trackexternaltypes = str_replace(',', '|', addslashes(strip_tags($trackexternaltypes)));
				/* Remove :// if type is f.e. given as http:// */
				$trackexternaltypes = str_replace('://', '', $trackexternaltypes);
					// Mask Slashes for use in preg_replace
				$trackexternaltypes = str_replace('/', '\/', $trackexternaltypes);
			}
		}
		/**
		 * Add Site Speed reports (deprecated) (will be deleted in a future release)
		 * Site Speed reporting is enabled automatically by Google for all users
		 * https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiBasicConfiguration#_gat.GA_Tracker_._trackPageLoadTime
		 *
		 */
		$trackpageload = '';
		if ($this->conf['trackpageload'] == 1) {
			$trackpageload = ", ['_trackPageLoadTime']";
		}
		/**
		 * Add Enhanced Link Attribution
		 * http://support.google.com/analytics/bin/answer.py?hl=en&answer=2558867
		 *
		 */
		$inpagelinkid = '';
		if ($this->conf['inpagelinkid'] == 1) {
			$inpagelinkid = "['_require', 'inpage_linkid', pluginUrl],";
		}
		/**
		 * Add Adjusted Bounce Rate
		 * http://analytics.blogspot.de/2012/07/tracking-adjusted-bounce-rate-in-google.html
		 *
		 */
		$bouncecategory = '15_seconds';
		$bounceaction = 'read';
		$bouncetimeout = '15000';
		if ($this->conf['bouncerate'] == 1) {
			if (!empty($this->conf['bouncerate.']['category'])) {
				$bouncecategory = htmlspecialchars($this->conf['bouncerate.']['category']);
			}
			if (!empty($this->conf['bouncerate.']['action'])) {
				$bounceaction = htmlspecialchars($this->conf['bouncerate.']['action']);
			}
			if (preg_match('/^[0-9]+$/', $this->conf['bouncerate.']['timeout'])) {
					// Only numbers allowed
				$bouncetimeout = $this->conf['bouncerate.']['timeout'];
			}
		}
		/**
		 * Add Code to Support Display Advertising
		 * http://support.google.com/analytics/bin/answer.py?hl=en&answer=2444872
		 *
		 */
		$gaSrc = "g.src = ('https:' == d.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';";
		if ($this->conf['doubleclick'] == 1) {
			$gaSrc = "g.src = ('https:' == d.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';";
		}
		/**
		 * Disable Tracking by cookie
		 * https://developers.google.com/analytics/devguides/collection/gajs/?hl=de#disable
		 *
		 */
		$gaOptout = '';
		if ($this->conf['UAdualtag'] == 1) {
			// Include optout only once
		} else {
			if ($this->conf['optout'] == 0) {
				// Disable tracking if the opt-out cookie exists.
				$gaOptout .= "var disableStr = 'ga-disable-" . str_replace('MO', 'UA', $this->conf['account']) . "';
if (document.cookie.indexOf(disableStr + '=true') > -1) {
	window[disableStr] = true;
}" . chr(10);
				if ($this->conf['optout.']['disablefunction'] == 0) {
					// Opt-out function
					$gaOptout .= "function gaOptout() {
	document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
	window[disableStr] = true;
}" . chr(10);
				}
			}
		}
		/**
		 * Disable Tracking if Do Not Track is set
		 * https://www.eff.org/issues/do-not-track
		 *
		 */
		if ($this->conf['dnt'] == 1) {
			// Disable tracking if set in the configurations of the used browser
			$gaOptout .= "if(navigator.doNotTrack == 1) {
	document.cookie = 'ga-disable-" . str_replace('MO', 'UA', $this->conf['account']) . "' + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
	window['ga-disable-" . str_replace('MO', 'UA', $this->conf['account']) . "'] = true;
}" . chr(10);
		}
		/**
		 * Tracking Multiple Domains
		 * https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingSite
		 *
		 */
		$setdomainname = '';
		if (!empty($this->conf['_setDomainName'])) {
			$setdomainname = ",['_setDomainName', '" . htmlspecialchars($this->conf['_setDomainName']) . "']";
		}
		/**
		 * Remove _gaq. and _gat. if set
		 * also remove configs without underscore (e.g. account)
		 *
		 */
		$gaConf = array();
		foreach ($this->conf as $param => $val) {
			$param = htmlspecialchars($param);
			if (is_array($val)) {
				foreach ($val as $paramTwo => $valTwo) {
					$paramTwo = htmlspecialchars($paramTwo);
					$valTwo = htmlspecialchars($valTwo);
					if (substr($paramTwo, 0, 1) == '_') {
						$gaConf[$paramTwo] = $valTwo;
					}
				}
			} else {
				$val = htmlspecialchars($val);
				if (substr($param, 0, 1) == '_') {
					$gaConf[$param] = $val;
				}
			}
		}

		$options = '';
		foreach ($gaConf as $param => $val) {
			if ($val != 'true' && $val != 'false' && $val != '1' && $val != '0' && strpos($val, ',') === FALSE) {
				$val = "'" . $val . "'";
			}
			$val = str_replace('&amp;', '&', $val);
			$options .= " ['" . $param . "', " . $val . '],';
		}

		$gaAsync .= '<script type="text/javascript">' . chr(10);
		$gaAsync .= '/* <![CDATA[ */' . chr(10);
		$gaAsync .= $gaOptout;

		if ($trackexternaljs === TRUE) {
			$gaAsync .= "function recordOutboundLink(link, category, action) {
	try {
		var myTracker = _gat._getTrackerByName();
		_gaq.push(['_trackEvent', category ,  action ]);
	} catch(err) {}
	if(link.target == '_blank') {
		window.open(link.href);
	} else {
		setTimeout('document.location = \"' + link.href + '\"', 100);
	}
};" . chr(10);
		}

		if ($this->conf['inpagelinkid'] == 1) {
			$gaAsync .= "var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';" . chr(10);
		}
		$gaAsync .= 'var _gaq = [' . $inpagelinkid . "['_setAccount', '" . str_replace('MO', 'UA', $this->conf['account']) . "']," . $anonymizeIp . $options . " ['_trackPageview']" . $trackpageload . $setdomainname . '];' . chr(10);

		if ($this->conf['bouncerate'] == 1) {
			$gaAsync .= " setTimeout(\"_gaq.push(['_trackEvent', '" . $bouncecategory . "', '" . $bounceaction . "'])\"," . $bouncetimeout . ');' . chr(10);
		}
		$gaAsync .= ' (function(d, t) {
	var g = d.createElement(t); g.async = true;
	' . $gaSrc . "
	var s = d.getElementsByTagName(t)[0]; s.parentNode.insertBefore(g, s);
})(document, 'script');" . chr(10);

		$gaAsync .= '/* ]]> */
	</script>';

			// Add filetracking code to the links in the document
		if ($trackfiles) {
			$con = preg_replace('/(<a\s*.*?href\s*=\s*[\"\\\'](\/?(' . $trackfiles . ')(.*?))(' . $trackfiletypes . ')[\"\\\']([^\>]*?))/i', "$1 onclick=\"_gaq.push(['_trackEvent', 'Downloads', '$5', '$3$4$5']);\"", $con);
		}

			// Add filetracking code to the external links in the document
		if ($trackexternaljs === TRUE) {
			$con = preg_replace('/((\<a\s([^\>]*?)href\=[\"\\\'](' . $trackexternaltypes . ')\:\/\/(.*?)[\"\\\']([^\>]*?))\>)/i', "$2 onclick=\"recordOutboundLink(this, 'External Links', '$4://$5');return false;\">", $con);
		}

		$content = $this->insertTrackerCode($con, $gaAsync, 'headEnd');
		return $content;
	}

	/**
	 * Google Universal Analytics (analytics.js)
	 *
	 * @param str $con Pagecontent
	 * @return str Pagecontent
	 *
	 */
	protected function insertUniversalGaCode($con) {

		$trackfiles = '';
		$trackfiletypes = '';
		/* If filetracking is enabled, clean the userinput */
		if ($this->conf['trackfiles'] == 1) {
			if (!empty($this->conf['trackfiles.']['path'])) {
				$trackfiles = str_replace(' ', '', trim($this->conf['trackfiles.']['path']));
				$trackfiles = str_replace(',', '|', addslashes(strip_tags($trackfiles)));
				$trackfiles = str_replace('/', '\/', addslashes($trackfiles));
			} else {
				$trackfiles = 'fileadmin|uploads|typo3temp';
			}
			/* Track only given filetypes */
			if (!empty($this->conf['trackfiles.']['types'])) {
				$trackfiletypes = str_replace(' ', '', trim($this->conf['trackfiles.']['types']));
				$trackfiletypes = str_replace(',', '|', addslashes(strip_tags($trackfiletypes)));
				$trackfiletypes = str_replace('/', '\/', addslashes($trackfiletypes));
			} else {
				$trackfiletypes = '\w{1,3}';
			}
		}
		/**
		 * Add Outbound Links
		 * http://support.google.com/analytics/bin/answer.py?hl=en&answer=1136920
		 *
		 */
		$trackexternaltypes = 'http|https|mailto|ftp';
		$trackexternaljs = FALSE;
		if ($this->conf['trackexternal'] == 1) {
			$trackexternaljs = TRUE;
			if (!empty($this->conf['trackexternal.']['types'])) {
					// Remove Spaces
				$trackexternaltypes = str_replace(' ', '', trim($this->conf['trackexternal.']['types']));
					// Convert comma to pipe
				$trackexternaltypes = str_replace(',', '|', addslashes(strip_tags($trackexternaltypes)));
				/* Remove :// if type is f.e. given as http:// */
				$trackexternaltypes = str_replace('://', '', $trackexternaltypes);
					// Mask Slashes for use in preg_replace
				$trackexternaltypes = str_replace('/', '\/', $trackexternaltypes);
			}
		}
		/**
		 * Tracking Multiple Domains
		 * https://developers.google.com/analytics/devguides/collection/gajs/gaTrackingSite
		 *
		 */
		$setdomainname = '';
		if ($this->conf['_setDomainName']) {
			$setdomainname = "'cookieDomain': '" . $this->conf['_setDomainName'] . "'";
			if ($this->conf['legacyCookieDomain']) {
				$setdomainname .= ",'legacyCookieDomain': '" . $this->conf['legacyCookieDomain'] . "'";
			}
		} elseif($this->conf['legacyCookieDomain']) {
			$setdomainname = "'legacyCookieDomain': '" . $this->conf['legacyCookieDomain'] . "'";
		}
		/**
		 * Disable Tracking by cookie
		 * https://developers.google.com/analytics/devguides/collection/gajs/?hl=de#disable
		 *
		 */
		$gaOptout = '';
		if ($this->conf['optout'] == 0) {
			// Disable tracking if the opt-out cookie exists.
			$gaOptout .= "var disableStr = 'ga-disable-" . str_replace('MO', 'UA', $this->conf['account']) . "';
if (document.cookie.indexOf(disableStr + '=true') > -1) {
	window[disableStr] = true;
}" . chr(10);
			if ($this->conf['optout.']['disablefunction'] == 0) {
				// Opt-out function
				$gaOptout .= "function gaOptout() {
	document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
	window[disableStr] = true;
}" . chr(10);
			}
		}
		/**
		 * Disable Tracking if Do Not Track is set
		 * https://www.eff.org/issues/do-not-track
		 *
		 */
		if ($this->conf['dnt'] == 1) {
			// Disable tracking if set in the configurations of the used browser
			$gaOptout .= "if(navigator.doNotTrack == 1) {
	document.cookie = 'ga-disable-" . str_replace('MO', 'UA', $this->conf['account']) . "' + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
	window['ga-disable-" . str_replace('MO', 'UA', $this->conf['account']) . "'] = true;
}" . chr(10);
		}
		// Create initial options object
		$gaUniversalOptions = '';
		if($setdomainname != '') {
			// Set options only, if object is not empty (add additional init options)
			$gaUniversalOptions = ', {';
			$gaUniversalOptions .= $setdomainname; // add _setDomainname
			$gaUniversalOptions .= '}';
		}
		// Build Goolge Analytics script tag
		$gaUniversal = '<script type="text/javascript">' . chr(10);
		$gaUniversal .= $gaOptout;
		$gaUniversal .= "
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','__gaTracker');" . chr(10);

			// If website is dualtagged and a different analytics.js-Account is given, use the given one instead of the conf['account']
		if ($this->conf['UAdualtag'] == 1) {
			if (preg_match('#^\b(UA|MO)-\d{4,10}-\d{1,4}\b$#i', $this->conf['UAdualtag.']['account'])) {
				$gaUniversal .= "__gaTracker('create', '" . $this->conf['UAdualtag.']['account'] . "'" . $gaUniversalOptions . ");" . chr(10);
			} else {
				$gaUniversal .=  chr(10) . chr(10) . '<!-- oops: Syntaxcheck of Google Universal Analytics Account Number failed!' . chr(10);
				$gaUniversal .= 'Please check tx_we_google_analytics.UAdualtagAccount ' . chr(10);
				$gaUniversal .= 'Please use the following format UA-xxxx-y -->' . chr(10) . chr(10);
			}
		} else {
			$gaUniversal .= "__gaTracker('create', '" . $this->conf['account'] . "'" . $gaUniversalOptions . ");" . chr(10);
		}

			// Send anonymized IP to Google Analytics
		if ($this->conf['anonymized'] == 1) {
			$gaUniversal .= "__gaTracker('set', 'anonymizeIp', true);" . chr(10);
		}
		/**
		 * Enable Demographics and Interests reports
		 *
		 */
		if ($this->conf['demographics'] == 1) {
			$gaUniversal .= "__gaTracker('require', 'displayfeatures');" . chr(10);
		}

		$gaUniversal .= "__gaTracker('send', 'pageview');" . chr(10);
		$gaUniversal .= '</script>';

			// Add filetracking code to the links in the document
			// If the website is dualtagged, do not use filetracking two times (ga.js and analytics.js)
		if ($trackfiles && $this->conf['UAdualtag'] != 1) {
			$con = preg_replace('/(<a\s*.*?href\s*=\s*[\"\\\'](\/?(' . $trackfiles . ')(.*?))(' . $trackfiletypes . ')[\"\\\']([^\>]*?))/i', "$1 onclick=\"__gaTracker('send', 'event', 'Downloads', '$5', '$3$4$5');\"", $con);
		}

			// Add filetracking code to the external links in the document
			// If the website is dualtagged, do not use filetracking two times (ga.js and analytics.js)
		if ($trackexternaljs === TRUE && $this->conf['UAdualtag'] != 1) {
			$con = preg_replace('/((\<a\s([^\>]*?)href\=[\"\\\'](' . $trackexternaltypes . ')\:\/\/(.*?)[\"\\\']([^\>]*?))\>)/i', "$2 onclick=\"__gaTracker('send', 'event', 'External Links', 'click', '$4://$5');\">", $con);
		}

		$content = $this->insertTrackerCode($con, $gaUniversal, 'headEnd');
		return $content;
	}

	/**
	 * Inserts the Tracker Code on the given position (headEnd, bodyBegin, bodyEnd)
	 *
	 * @param str $con      Pagecontent
	 * @param str $gaCode   Google Analytics Tracker Code
	 * @param str $position Position of the Tracker Code in the html document
	 * @return str          Pagecontent
	 *
	 */
	protected function insertTrackerCode($con, $gaCode, $position) {
		switch($position) {
			case 'headEnd':
				$content = str_replace('</head>', $gaCode . chr(10) . '</head>', $con);
				break;
			case 'bodyBegin':
				$content = preg_replace('/(<body)([^>]*)>/', '\\1\\2>' . $gaCode . chr(10), $con);
				break;
			case 'bodyEnd':
				$content = str_replace('</body>', $gaCode . chr(10) . '</body>', $con);
				break;
				// none
			default:
		}
		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/we_google_analytics/class.tx_wegoogleanalytics.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/we_google_analytics/class.tx_wegoogleanalytics.php']);
}

?>
