.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


Installation
============

The extension needs to be installed like any other extension of TYPO3 CMS:

1) Create a Google Analytics account at: http://www.google.com/analytics/
2) To enable Google Analytics tracking in your website, you need to know your profile ID, otherwise referred to as “analytics account number”. This is a combination that looks like “UA-xxxx-y” or for mobile websites it's “MO-xxxx-y”where x and y are digits. The x's represent your account number and the y's represent the profile numbers within your account. You can look up your analytics account number by clicking “Check Status” link in your Analytics Settings once you are logged in.
3) Login to yout TYPO3 Backend
4) Switch to the module “Extension Manager”
5) Get the extension
	* Get it from the Extension Manager: Press the “Retrieve/Update” button and search for the extension key we_google_analytics and import the extension from the repository.
	* Get it from typo3.org: You can always get current version from http://typo3.org/extensions/repository/view/we_google_analytics/current/ by downloading either the t3x or zip version. Upload the file afterwards in the Extension Manager.
6) Add the configuration options to your template

:typoscript:`config.tx_we_google_analytics.account = UA-xxxx-y`

That's it!
If you would like to change the way of tracking, please see “Configuration” section for description of the template
parameters.