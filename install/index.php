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

require_once('../lib/cmsms.api.php');
require_once('lib/class.cms_install_operations.php');

$smarty = CmsSmarty::get_instance(false);
$smarty->force_compile = true;
$smarty->template_dir = cms_join_path(dirname(dirname(__FILE__)),'install','templates'.DS);
$smarty->plugins_dir = array(cms_join_path(dirname(dirname(__FILE__)),'lib','smarty','plugins'.DS), cms_join_path(dirname(__FILE__),'plugins'.DS));

require_once(cms_join_path(dirname(dirname(__FILE__)), 'lib', 'xajax', 'xajax.inc.php'));
$xajax = new xajax();
$xajax->registerFunction('test_connection');
$xajax->processRequests();
$smarty->assign('xajax_header', $xajax->getJavascript('../lib/xajax'));

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
		
		$recommended_checks = CmsInstallOperations::recommended_checks();
		
		$smarty->assign('file_uploads', CmsInstallOperations::recommended_setting_output($recommended_checks['file_uploads']));
		$smarty->assign('safe_mode', CmsInstallOperations::recommended_setting_output($recommended_checks['safe_mode'], true));
		$smarty->assign('magic_quotes_runtime', CmsInstallOperations::recommended_setting_output($recommended_checks['magic_quotes_runtime'], true));
		$smarty->assign('register_globals', CmsInstallOperations::recommended_setting_output($recommended_checks['register_globals'], true));
		$smarty->assign('output_buffering', CmsInstallOperations::recommended_setting_output($recommended_checks['output_buffering'], true));

		$smarty->assign('uploads_path', cms_join_path(dirname(dirname(__FILE__)),'uploads'));
		$smarty->assign('canwrite_uploads', CmsInstallOperations::recommended_setting_output($recommended_checks['canwrite_uploads']));
		$smarty->assign('modules_path', cms_join_path(dirname(dirname(__FILE__)),'modules'));
		$smarty->assign('canwrite_modules', CmsInstallOperations::recommended_setting_output($recommended_checks['canwrite_modules']));
		
		$smarty->assign('failure2', $recommended_checks['failure']);

		$smarty->assign('include_file', 'check.tpl');
		$smarty->display('body.tpl');
		break;
		
	case "database":
	
		$smarty->assign('databases', CmsInstallOperations::get_loaded_database_modules());
	
		$smarty->assign('include_file', 'database.tpl');
		$smarty->display('body.tpl');
		break;
		
}

function test_connection($params)
{
	global $smarty; //Too lazy to set it all up again

	$objResponse = new xajaxResponse();
	
	$result = CmsInstallOperations::test_database_connection($params['connection']['driver'], $params['connection']['hostname'], $params['connection']['username'], $params['connection']['password'], $params['connection']['dbname']);
	
	$smarty->assign('databasetestresult', $result);
	$objResponse->addAssign("connection_options", "innerHTML", $smarty->fetch('databaseinsert.tpl'));
	
	$objResponse->addScript("new Effect.BlindDown('connection_options');");

	return $objResponse->getXML();
}

# vim:ts=4 sw=4 noet
?>
