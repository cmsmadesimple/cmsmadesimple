<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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
#
#$Id$

//Set the module name -- should be the name of the class
$module_name = "News";

//Load functions
require_once(dirname(__FILE__)."/modulefunctions.php");
require_once(dirname(__FILE__)."/contentfunctions.php");

//Register Module
cms_mapi_register_module($module_name, "Robert Campbell <rob@techcom.dyndns.org", "1.5");

//Register module to work as a content type
cms_mapi_register_content_module($module_name);
cms_mapi_register_content_module_set_properties_function($module_name, 'news_module_content_set_properties');
cms_mapi_register_content_module_edit_function($module_name, 'news_module_content_edit');
cms_mapi_register_content_module_fill_params_function($module_name, 'news_module_content_fill_params');
cms_mapi_register_content_module_get_url_function($module_name, 'news_module_content_get_url');
cms_mapi_register_content_module_show_function($module_name, 'news_module_content_show');

//Register module to work as a plugin (cms_module)
cms_mapi_register_plugin_module($module_name);

//Register callback functions
cms_mapi_register_install_function($module_name, 'news_module_install');
cms_mapi_register_upgrade_function($module_name, 'news_module_upgrade', true);
cms_mapi_register_uninstall_function($module_name, 'news_module_uninstall');
cms_mapi_register_execute_function($module_name, 'news_module_execute');
cms_mapi_register_executeuser_function($module_name, 'news_module_executeuser');
cms_mapi_register_executeadmin_function($module_name, 'news_module_executeadmin');

//Register help function
cms_mapi_register_help_function($module_name, 'news_module_help');
cms_mapi_register_about_function($module_name, 'news_module_about');

//Enable the wysiwyg for editing
cms_mapi_enable_wysiwyg($module_name);

# vim:ts=4 sw=4 noet
?>
