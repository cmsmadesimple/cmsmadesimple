CMS Made Simple http://www.cmsmadesimple.org

For information on installation, see INSTALL.txt.
For information on upgrading your installation from a previous version, see UPGRADE.txt.

Official documentation website: https://docs.cmsmadesimple.org

Web server notes
---------------------------------
Apache-style .htaccess reference: doc/htaccess.txt
OpenLiteSpeed and LiteSpeed Enterprise (CyberPanel-friendly): doc/openlitespeed-litespeed-web.txt

System requirements (summary)
---------------------------------
PHP minimum and recommended values used by the installer checks are defined in
lib/functions/test.functions.php (function getTestValues). As of this tree,
PHP minimum is 7.4.4 and recommended is 8.1.0 unless you change that array.

Notes
---------------------------------
The version of ADODB_lite used is modified for CMS Made Simple's needs.
Copying over it with the latest version will result in some things not working correctly and it probably won't even install on all databases.

Credits
---------------------------------
CMSMS uses the Smarty template engine in various places.
Their license and various documentation files are located in the Smarty subdirectory of this distribution.
Their website is located at: http://smarty.php.net

CMSMS uses the adodb_lite database abstraction library.
It is released under the LGPGL license and is located at: http://adodblite.sourceforge.net/index.php
