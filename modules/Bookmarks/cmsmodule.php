<?php
#Bookmarks. A plugin for CMS - CMS Made Simple
#Copyright (c) 2004 by Rob Allen <rob@akrabat.com>
#
#CMS- CMS Made Simple is Copyright (c) Ted Kulp (wishy@users.sf.net)
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

// Set the module name -- should be the name of the class
$module_name = "Bookmarks";

// Defines
define('BOOKMARKS_MODULE_VERSION', '1.1');

define('BOOKMARKS_MODULE_MIN_COMMONCODE_VERSION', '1.1');

// Load functions
require_once(dirname(__FILE__).'/modulefunctions.php');

// Register module
cms_mapi_register_module($module_name, "Rob Allen <rob@akrabat.com>", BOOKMARKS_MODULE_VERSION);

// Register the dependency
cms_mapi_register_dependency($module_name, 'CommonCode', BOOKMARKS_MODULE_MIN_COMMONCODE_VERSION);

// Register module to work as a plugin (cms_module)
cms_mapi_register_plugin_module($module_name);

// Register callback functions
cms_mapi_register_install_function($module_name, 'bookmarks_module_install');
cms_mapi_register_upgrade_function($module_name, 'bookmarks_module_upgrade', true);
cms_mapi_register_uninstall_function($module_name, 'bookmarks_module_uninstall');
cms_mapi_register_execute_function($module_name, 'bookmarks_module_execute');
cms_mapi_register_executeuser_function($module_name, 'bookmarks_module_executeuser');
cms_mapi_register_executeadmin_function($module_name, 'bookmarks_module_executeadmin');

// Register callback help and about functions
cms_mapi_register_help_function($module_name, 'bookmarks_module_help');
cms_mapi_register_about_function($module_name, 'bookmarks_module_about');

function bookmarks_module_about()
{
	?>
	<p>Author: Rob Allen &lt;rob@akrabat.com&gt;</p>
	<dl>
		<dt>Version: 0.1</dt>
		<dd>Initial release. The code framework is based on the News module by Robert
			Campbell &lt;rob@techcom.dydnsns.org&gt;</dd>
		<dt>Version: 0.9</dt>
			<dd>First release of code to rest of world!</dd>
		<dt>Version: 0.9.1</dt>
			<dd>Bug fixes to help, column handling and the admin list.</dd>
		<dt>Version: 1.0</dt>
			<dd>Support email notifications from the submit bookmarks form. Addded delete option.
			Admin list now displays bookmarks that are not attached to any category.<br />
			Now required CommonCode 1.1<br />
			Tidied up code.<br />
			</dd>
		<dt>Version: 1.0.1</dt>
			<dd>Bug fixes that prevented display of the user facing submit form.</dd>
		<dt>Version: 1.0.2</dt>
			<dd>Uses CMS functions for CommonCode dependency.</dd>
		<dt>Version: 1.1</dt>
			<dd>Update to 0.8.</dd>
	</dl>
	<?php
}

# vim:ts=4 sw=4 noet
?>
