.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _Google Developers, _setSiteSpeedSampleRate(): https://goo.gl/TR3mN

Examples
========

Minimal configuration (asynchronous without _anonymizeIp())
-----------------------------------------------------------
| :typoscript:`config.tx_we_google_analytics.account = UA-123456-7`

AnonymizeIp configuration (asynchronous)
----------------------------------------
| :typoscript:`config.tx_we_google_analytics.account = UA-123456-7`
| :typoscript:`config.tx_we_google_analytics.anonymized = 1`

Synchronous configuration (without _anonymizeIp()) (deprecated)
---------------------------------------------------------------
The Traditional method shouldn't be used anymore.

Misc configuration with _xxx options used
-----------------------------------------
| :typoscript:`config.tx_we_google_analytics.account = UA-123456-7`
| :typoscript:`config.tx_we_google_analytics._cookiePathCopy = /newSubDirectory/`
| :typoscript:`config.tx_we_google_analytics._setAllowHash = true`
| :typoscript:`config.tx_we_google_analytics._setCustomVar = 1, 'Section', 'Life & Style', 3`

Track PageLoadTime (deprecated)
-------------------------------
| This method is deprecated because Site Speed reporting is enabled automatically by Google for all users.
| “By default, a fixed 1% sampling of your site visitors make up the data pool from which the Site Speed metrics are derived. If you have a relatively small number of daily visitors to your site, such as 100,000 or fewer, you might want to adjust the sampling to a larger rate. This will provide increased granularity for page load time and other Site Speed metrics.” See  `Google Developers, _setSiteSpeedSampleRate()`_ for more Information.
| To define a new sample set size via:
| :typoscript:`config.tx_we_google_analytics._setSiteSpeedSampleRate = 5`

Misc configuration with filetracking
------------------------------------
Links to files in fileadmin and uploads folder are going to be tracked in Google Analytics
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
| :typoscript:`config.tx_we_google_analytics.account = UA-123456-7`
| :typoscript:`config.tx_we_google_analytics.trackfiles = 1`
| :typoscript:`config.tx_we_google_analytics.trackfiles.path = fileadmin,uploads`

Track only pdf and zip files from fileadmin folder
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
| :typoscript:`config.tx_we_google_analytics.account = UA-123456-7`
| :typoscript:`config.tx_we_google_analytics.trackfiles = 1`
| :typoscript:`config.tx_we_google_analytics.trackfiles.path = fileadmin`
| :typoscript:`config.tx_we_google_analytics.trackfiles.types = pdf,zip`

Misc configuration with trackexternal
-------------------------------------
| :typoscript:`config.tx_we_google_analytics.account = UA-123456-7`
| :typoscript:`config.tx_we_google_analytics.trackexternal = 1`
| :typoscript:`config.tx_we_google_analytics.trackexternal.types = http, https`

Using the new universal tracking
--------------------------------
| :typoscript:`config.tx_we_google_analytics.account = UA-123456-7`
| :typoscript:`config.tx_we_google_analytics.type = universal`

Using both - asynchronous and universal tracking (dualtag!)
-----------------------------------------------------------
| :typoscript:`config.tx_we_google_analytics.account = UA-123456-7 # Account-Number for asynchronous tracking`
| :typoscript:`config.tx_we_google_analytics.type = universal`
| :typoscript:`config.tx_we_google_analytics.UAdualtag = 1`
| :typoscript:`config.tx_we_google_analytics.UAdualtag.account = UA-65432-1 # Account-Number universal tracking`

Add Opt-Out functionality
-----------------------------------------------------------
.. Note:: After activation, a link to the javascript function has to be set, e.g. <a href="javascript:gaOptout()">deactivate Google Analytics</a>
| :typoscript:`config.tx_we_google_analytics.optout = 1`