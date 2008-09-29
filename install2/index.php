<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

$thisProcess = 'install'; // install or upgrade
$numberOfPages = 7;



define('CMS_INSTALL_BASE', dirname(__FILE__));
define('CMS_BASE', dirname(CMS_INSTALL_BASE));

require_once(CMS_BASE . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'cmsms.api.php');
require_once cms_join_path(CMS_INSTALL_BASE, 'lib', 'class.cms_install_operations.php');
#require_once cms_join_path(CMS_BASE, 'lib', 'test.functions.php');
require_once cms_join_path(CMS_INSTALL_BASE, 'test.functions.php');



/* Check SESSION */
if(! extension_loaded_or('session') )
{
	CmsInstallOperations::show_error_msg("SESSION module is disabled or missing in your PHP, you have problem with some modules and functionality! Ask your provider, exiting");
}
$session_key = substr(md5(dirname(__FILE__)), 0, 8);

#Setup session with different id and start it
@session_name('CMSSESSID' . $session_key);
@ini_set('url_rewriter.tags', '');
@ini_set('session.use_trans_sid', 0);
@session_start();



CmsRequest::setup();
$postPage = CmsRequest::get('page');
$getSessionTest = CmsRequest::get('sessiontest');



/* UNDOCUMENTED features... if this values are set in the session */
/* Set DEBUG */
$getDebug = CmsRequest::get('debug', true, true);
if(! empty($getDebug))
{
	if(! isset($_SESSION['debug'])) $_SESSION['debug'] = 1;
	@ini_set('display_errors', 1);
	@error_reporting(E_ALL);
}
/* Set memory_limit without add in file */
$getMemoryLimit = CmsRequest::get('memory_limit', true, true);
if(! empty($getMemoryLimit))
{
	if(! isset($_SESSION['memory_limit'])) $_SESSION['memory_limit'] = $getMemoryLimit;
	@ini_set('memory_limit', $_SESSION['memory_limit']);
}
/* Skip safe mode tests */
if(CmsRequest::get('allowsafemode'))
{
	$_SESSION['allowsafemode'] = 1;
}
/* Skip blocking test. For advanced users ONLY */
if(CmsRequest::get('advanceduser'))
{
	$_SESSION['advanceduser'] = 1;
}



// Initial Tests
if(empty($getSessionTest) && (empty($postPage)) )
{
	// Test for session
	$_SESSION['test'] = true;

	// Tests for smarty
	if(! extension_loaded_or('tokenizer') )
	{
		CmsInstallOperations::show_error_msg('Not having the tokenizer extension could cause pages to render as purely white. We required you have this installed! Check your installation, exiting');
	}

	@clearstatcache();
	$pathSmartyClass = cms_join_path(CMS_BASE, 'lib', 'smarty', 'Smarty.class.php');
	if(! is_readable($pathSmartyClass))
	{
		CmsInstallOperations::show_error_msg($pathSmartyClass . ' cannot be found or not readable! Check your installation, exiting');
	}

	$test_writables = array(TMP_TEMPLATES_C_LOCATION, TMP_CACHE_LOCATION);
	foreach($test_writables as $dir)
	{
		$test = testDirWrite(0, '', $dir, '', 0, $getDebug);
		if($test->res == 'red')
		{
			CmsInstallOperations::show_error_msg($test->error . '<br />Please correct by executing: <em>chmod 777</em> or set writing permission for php process, exiting');
		}
	}

	// Smarty tests ok
	$smarty = CmsSmarty::get_instance(false);
	$smarty->compile_dir = TMP_TEMPLATES_C_LOCATION;
	$smarty->cache_dir = TMP_CACHE_LOCATION;
	$smarty->template_dir = cms_join_path(CMS_INSTALL_BASE,'templates'.DS);
	$smarty->caching = false;
	$smarty->debugging = false;
	$smarty->force_compile = true;
	$smarty->plugins_dir = array(cms_join_path(CMS_BASE,'lib','smarty','plugins'.DS), cms_join_path(CMS_INSTALL_BASE,'plugins'.DS));

	$smarty->assign('languages', CmsInstallOperations::get_language_list());
	$smarty->assign('selected_language', CmsInstallOperations::get_language_cookie());
	$smarty->display('installer_start.tpl');
	$smarty->display('installer_body.tpl');
	$smarty->display('installer_end.tpl');
	exit;
}
else if(! isset($_SESSION['test']))
{
	CmsInstallOperations::show_error_msg("SESSION not working, you have problem with some modules and functionality! Ask your provider, exiting");
}



// First checks ok
$smarty = CmsSmarty::get_instance(false);
$smarty->compile_dir = TMP_TEMPLATES_C_LOCATION;
$smarty->cache_dir = TMP_CACHE_LOCATION;
$smarty->template_dir = cms_join_path(CMS_INSTALL_BASE,'templates'.DS);
$smarty->caching = false;
$smarty->debugging = false;
$smarty->force_compile = true;
$smarty->plugins_dir = array(cms_join_path(CMS_BASE,'lib','smarty','plugins'.DS), cms_join_path(CMS_INSTALL_BASE,'plugins'.DS));

$xajax = new CmsAjax();
$xajax->register_function('test_connection');
$xajax->process_requests();

// Assign smarty variables
$smarty->assign('xajax_header', $xajax->get_javascript());
$smarty->assign('number_of_pages', $numberOfPages);
$smarty->assign('current_page', $postPage);
$smarty->assign('cms_version', CMS_VERSION);
$smarty->assign('cms_version_name', CMS_VERSION_NAME);

if(! empty($postPage))
{
	$smarty->assign('page', $postPage);
	$smarty->assign('include_file', $thisProcess . $postPage . '.tpl');
	$smarty->display($thisProcess . 'page.tpl');
}
else
{
	CmsInstallOperations::show_error_msg('Don\'t find page variable! Exiting.');
}

# vim:ts=4 sw=4 noet
?>
