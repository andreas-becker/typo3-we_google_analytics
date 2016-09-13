.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _websedit AG, Internetagentur Ravensburg: http://www.websedit.de/
.. _Andreas Becker: http://andreasbecker.de

.. introduction-manual:

Introduction
============

What does it do?
----------------

| This extension implements Google Analytics tracking in TYPO3-based websites. It is developed by `Andreas Becker`_ at `websedit AG, Internetagentur Ravensburg`_

What's new?
----------------
* **2.1.0** Support for TYPO3 CMS 8. Added :typoscript:`_setDomainName` and :typoscript:`legacyCookieDomain` option.
* **2.0.1** Fixed Support for TYPO3 CMS 7 LTS.
* **2.0.0** Support for TYPO3 CMS 7 LTS. Not tested under TYPO3 6.1 and lower. Use **v1.8.0** instead!
* **1.8.0** Integrated :typoscript:`optout` and :typoscript:`optout.disablefunction` params. Now it is possible to disable the tracking with a cookie.
* **1.6.0** Universal Analytics is now integrated (:typoscript:`type = universal`). With the :typoscript:`UAdualtag` param you can use both tracking methods.
* **1.5.0**	If you use display advertising, the new param :typoscript:`doubleclick` helps you a lot. Also the :typoscript:`bouncerate` param helps you to adjust the bounce rate.
* **1.4.0** For enhanced link attribution, :typoscript:`inpagelinkid` is your param.
* **1.3.0** Track external links? Check the :typoscript:`trackexternal` param.
* **1.2.0** File- (:typoscript:`trackfiles`) and pageload tracking (:typoscript:`“trackpageload”`)[deprecated] is now available.
* **1.1.0** Mobile tracking code added (:typoscript:`type = mobile`) and ga.js set as default.