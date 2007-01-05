<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

require_once('../fileloc.php');
require_once('../lib/global.functions.php');
require_once('lib/class.cms_install_operations.php');

$smarty = CmsSmarty::get_instance(false);
$smarty->force_compile = true;
$smarty->template_dir = cms_join_path(dirname(dirname(__FILE__)),'install','templates'.DIRECTORY_SEPARATOR);
$smarty->plugins_dir = array(cms_join_path(dirname(dirname(__FILE__)),'lib','smarty','plugins'.DIRECTORY_SEPARATOR), cms_join_path(dirname(__FILE__),'plugins'.DIRECTORY_SEPARATOR));

switch (CmsInstallOperations::get_action())
{
	case "intro":
	
		$smarty->assign('languages', CmsInstallOperations::get_language_list());
		$smarty->assign('selected_language', CmsInstallOperations::get_language_cookie());

		$smarty->assign('include_file', 'intro.tpl');
		$smarty->display('body.tpl');
		break;
		
	case "check":
		
		$required_checks = CmsInstallOperations::required_checks();

		$smarty->assign('php_version', CmsInstallOperations::required_setting_output($required_checks['php_version']));
		$smarty->assign('has_database', CmsInstallOperations::required_setting_output($required_checks['has_database']));
		$smarty->assign('which_database', $required_checks['which_database']);

		$smarty->assign('has_xml', CmsInstallOperations::required_setting_output($required_checks['has_xml']));
		$smarty->assign('has_simplexml', CmsInstallOperations::required_setting_output($required_checks['has_simplexml']));
		
		$smarty->assign('templates_path', cms_join_path(dirname(dirname(__FILE__)),'tmp','template_c'));
		$smarty->assign('canwrite_templates', CmsInstallOperations::required_setting_output($required_checks['canwrite_templates']));
		$smarty->assign('cache_path', cms_join_path(dirname(dirname(__FILE__)),'tmp','cache'));
		$smarty->assign('canwrite_cache', CmsInstallOperations::required_setting_output($required_checks['canwrite_cache']));
		
		$smarty->assign('failure', $required_checks['failure']);

		$smarty->assign('include_file', 'check.tpl');
		$smarty->display('body.tpl');
		break;
}

# vim:ts=4 sw=4 noet
?>
