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

#error_reporting(E_ALL);

global $gCms;

$CMS_INSTALL_PAGE=1;
$LOAD_ALL_MODULES = true;
$DONT_LOAD_DB = false;

set_magic_quotes_runtime(false);

define('CMS_INSTALL_BASE', dirname(__FILE__));
define('CMS_BASE', dirname(CMS_INSTALL_BASE));
require_once CMS_BASE . DIRECTORY_SEPARATOR . 'fileloc.php';
require_once CMS_BASE . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'misc.functions.php';
require_once cms_join_path(CMS_BASE, 'lib', 'test.functions.php');
require_once cms_join_path(CMS_INSTALL_BASE, 'lib', 'classes', 'CMSInstaller.class.php');


// Set for avoid SESSION problem on a few servers
$sess_save_path = ini_get('session.save_path');
if ( ('files' == ini_get('session.save_handler')) && (empty($sess_save_path)) )
{
	// Session to level SO, no check for writable dir
	//ini_set('session.use_only_cookies', 1);
}


$installer =& new CMSInstaller();


@session_start();
// Test for smarty and sessions
if (! isset($_GET['sessiontest']) && (! isset($_POST['page'])) )
{
	$_SESSION['test'] = true;

	$path_smarty_class = CMS_BASE . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR . 'Smarty.class.php';
	if (! is_readable($path_smarty_class))
	{
		$installer->showErrorPage($path_smarty_class . ' cannot be found! Check your installation, exiting');
		exit;
	}

	$path_smarty_dirs = array(TMP_TEMPLATES_C_LOCATION, TMP_CACHE_LOCATION);
	foreach($path_smarty_dirs as $dir)
	{
		if (! is_writable($dir))
		{
			$installer->showErrorPage($dir . ' is not writable! Check your installation, exiting');
			exit;
		}
	}

	// Uncomment this three rows for disabled languages
#	$scheme = ((! isset($_SERVER['HTTPS'])) || strtolower($_SERVER['HTTPS']) != 'on') ? 'http' : 'https';
#	$redirect = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?sessiontest=1&' . SID;
#	header("Location: $redirect");


	require_once $path_smarty_class;
	$smarty =& new Smarty();
	$smarty->compile_dir = TMP_TEMPLATES_C_LOCATION;
	$smarty->cache_dir = TMP_CACHE_LOCATION;
	$smarty->template_dir = CMS_INSTALL_BASE . DIRECTORY_SEPARATOR . 'templates';
	$smarty->caching = false;
	$smarty->force_compile = true;

	$smarty->assign_by_ref('languages', $installer->dropdown_lang());
	$smarty->display('installer_start.tpl');	// display page start
	$smarty->display('page0.tpl');			// display page0
	$smarty->display('installer_end.tpl');		// display page end
	exit;
}
else if (! isset($_SESSION['test']))
{
	$installer->showErrorPage("SESSION not working, you have problem with some modules and functionality! Ask your provider, exiting");
	exit;
}



// First checks ok
require_once cms_join_path(CMS_BASE, 'include.php');

if (isset($_POST['default_cms_lang']))
{
	$frontendlang = $_POST['default_cms_lang'];
}
require_once cms_join_path(CMS_INSTALL_BASE, 'lang.php');
$smarty->assign('default_cms_lang', $frontendlang);
$smarty->register_function('lang_install','smarty_lang');


$installer->run();
?>
