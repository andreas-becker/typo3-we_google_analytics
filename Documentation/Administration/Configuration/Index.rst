.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


Configuration
=============

All configuration parameters for this extension should be in your TypoScript template, inside the
:typoscript:`config.tx_we_google_analytics` container. Please see the option descriptions below.

.. Important:: Not all parameters are supported by the different types of the Google Analytics trackers (traditional, ga.js, mobile and analytics.js). Take into account that the used parmeters are compatible with your selected type of tracker.

.. _Google Developers, The _gat Global Object: https://goo.gl/sfHwt
.. _Google Support, Additional kinds of tracking: https://goo.gl/Eb7Au
.. _Google Support, Track outbound links: https://goo.gl/BqiHR
.. _Google Support, Enhanced Link Attribution: https://goo.gl/HwpUw
.. _Google Analytics Blog, Tracking Adjusted Bounce Rate In Google Analytics: https://goo.gl/SuP3g
.. _Google Support, Enable Remarketing and Advertising Reporting Features in Google Analytics: http://goo.gl/bDsVC
.. _Google Developers, Disabling Tracking: https://goo.gl/cnAEzg


.. t3-field-list-table::
 :header-rows: 1

 - :Property:
         Property

   :Data type:
         Data type

   :Description:
         Description

   :Default:
         Default

   :Trackingtype:
   		 Trackingtype

 - :Property:
 		 :typoscript:`account`

   :Data type:
 		 String

   :Description:
 		 Defines your Google Analytics account number. This parameter needs to be set for the extension to work. If not set, you will see an error-message as html-comment in your pages html source.

   :Default:


   :Trackingtype:
   		 all

 - :Property:
 		 :typoscript:`anonymized`

   :Data type:
 		 Boolean

   :Description:
 		 Set 1 if you want to use the “_anonymizeIp”-function. More information can be found here: `Google Developers, The _gat Global Object`_.

   :Default:
 		 0

   :Trackingtype:
   		 all

 - :Property:
 		 :typoscript:`type`

   :Data type:
 		 String

   :Description:
 		 Choose whether the asynchronous Tracker is used (type = async), the synchronous one (type = sync) or the mobile (type = mobile) one for your mobile-Website. Basic implementation of Universal Analytics is also supported (type = universal)

   :Default:
 		 async

   :Trackingtype:
   		 all

 - :Property:
 		 :typoscript:`trackfiles`

   :Data type:
 		 Boolean

   :Description:
 		 Set 1 if links to files should be tracked as well. Check `Google Support, Additional kinds of tracking`_ for more information.

   :Default:
 		 0

   :Trackingtype:
   		 ga.js, analytics.js

 - :Property:
 		 :typoscript:`trackfiles.path`

   :Data type:
 		 String

   :Description:
 		 Comma separated list of top-level directories from your website

   :Default:
 		 fileadmin, uploads, typo3temp

   :Trackingtype:
   		 ga.js, analytics.js

 - :Property:
 		 :typoscript:`trackfiles.types`

   :Data type:
 		 String

   :Description:
 		 Comma separated list of file extension which will be tracked. If this parameter isn't set, all files within the “trackfiles.path”-directory that have a file extension between one and three charakters will be tracked

   :Default:


   :Trackingtype:
   		 ga.js, analytics.js

 - :Property:
 		 :typoscript:`_xxx`

   :Data type:
 		 String

   :Description:
 		 You can add most of the options starting with an underscore like _setDomainName.

   :Default:


   :Trackingtype:
   		 ga.js

 - :Property:
 		 :typoscript:`trackexternal`

   :Data type:
 		 Boolean

   :Description:
 		 Set 1 if links to external Websites should be tracked. More on `Google Support, Track outbound links`_

   :Default:
 		 0

   :Trackingtype:
   		 ga.js, analytics.js

 - :Property:
 		 :typoscript:`trackexternal.types`

   :Data type:
 		 String

   :Description:
 		 Comma separated list of protocols which should be tracked

   :Default:
 		 http, https, mailto, ftp

   :Trackingtype:
   		 ga.js, analytics.js

 - :Property:
 		 :typoscript:`inpagelinkid`

   :Data type:
 		 Boolean

   :Description:
 		 Set 1 to implement an enhanced link-tracking functionality (only supported in async mode). Documented on `Google Support, Enhanced Link Attribution`_
 		 **You have to enable the enhanced link attribution in your Google Analytics reports!**

   :Default:
 		 0

   :Trackingtype:
   		 ga.js

 - :Property:
 		 :typoscript:`bouncerate`

   :Data type:
 		 Boolean

   :Description:
 		 Set 1 to adjust the bounce rate (only supported in async mode). See `Google Analytics Blog, Tracking Adjusted Bounce Rate In Google Analytics`_ for more information

   :Default:
 		 0

   :Trackingtype:
   		 ga.js

 - :Property:
 		 :typoscript:`bouncerate.category`

   :Data type:
 		 String

   :Description:
 		 Set the general event category

   :Default:
 		 15_seconds

   :Trackingtype:
   		 ga.js

 - :Property:
 		 :typoscript:`bouncerate.action`

   :Data type:
 		 String

   :Description:
 		 Set the action for the event

   :Default:
 		 Read

   :Trackingtype:
   		 ga.js

 - :Property:
 		 :typoscript:`bouncerate.timeout`

   :Data type:
 		 Number

   :Description:
 		 Set the timeout after the event is fired

   :Default:
 		 15000

   :Trackingtype:
   		 ga.js

 - :Property:
 		 :typoscript:`doubleclick`

   :Data type:
 		 Boolean

   :Description:
 		 Set 1 to support display advertising (only supported in async mode). Check `Google Support, Enable Remarketing and Advertising Reporting Features in Google Analytics`_ for more information about remarking lists

   :Default:
 		 0

   :Trackingtype:
   		 ga.js

 - :Property:
 		 :typoscript:`UAdualtag`

   :Data type:
 		 Boolean

   :Description:
 		 Set 1 to use the “Universal Analytics” and the Google Analytics asynchronous Tracking snippets on the same page

   :Default:
 		 0

   :Trackingtype:
   		 analytics.js

 - :Property:
 		 :typoscript:`UAdualtag.account`

   :Data type:
 		 String

   :Description:
 		 Add your Universal Analytics Account Number, if you want to tag your website with a different number for “Universal Analytics”. Only used if “UAdualtag” is 1 and “type” = universal

   :Default:


   :Trackingtype:
   		 analytics.js

 - :Property:
 		 :typoscript:`optout`

   :Data type:
 		 Boolean

   :Description:
 		 Set 1 to remove disabling of tracking by cookie (CookieLaw). Check `Google Developers, Disabling Tracking`_ for more information.

   :Default:
 		 0

   :Trackingtype:
   		 ga.js, analytics.js

 - :Property:
       :typoscript:`optout.disablefunction`

   :Data type:
       Boolean

   :Description:
       Set 1 to remove the default gaOptout-function. Note: You have to integrate your own js-function to create the opt-out cookie.

   :Default:
       0

   :Trackingtype:
          ga.js, analytics.js

 - :Property:
       :typoscript:`_setDomainName`

   :Data type:
       String

   :Description:
       Sets the domain name for the GATC cookies. Google Analytics provides three modes to this method: ("auto" | "none" | [domain]).

   :Default:


   :Trackingtype:
          ga,js, analytics.js

 - :Property:
       :typoscript:`legacyCookieDomain`

   :Data type:
       String

   :Description:
       This field is used to configure how analytics.js searches for cookies generated by earlier Google Analytics tracking scripts such as ga.js and urchin.js.
       Typically it is a (sub-)domain like 'alpha.websedit.de' (without the quotes).

   :Default:


   :Trackingtype:
          analytics.js

 - :Property:
       :typoscript:`dnt`

   :Data type:
       Boolean

   :Description:
       Set 1 to respect the Do Not Track configuration of the browser.

   :Default:
       0

   :Trackingtype:
          ga,js, analytics.js
