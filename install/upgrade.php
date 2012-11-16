<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
#$Id: newupgrade.php 83 2008-09-20 14:41:52Z alby $
$CMS_INSTALL_PAGE=1;

require_once("../include.php");
$gCms = cmsms();

$LOAD_ALL_MODULES = true;
$process = 'upgrade';
$max_pages = 7;

define('CMS_INSTALL_HELP_URL', 'http://docs.cmsmadesimple.org/installation/requirements');
define('CMS_UPGRADE_HELP_URL', 'http://docs.cmsmadesimple.org/upgrading');
define('CMS_INSTALL_BASE', dirname(__FILE__));
define('CMS_BASE', dirname(CMS_INSTALL_BASE));

require_once CMS_BASE . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'misc.functions.php';
require_once cms_join_path(CMS_BASE, 'fileloc.php');
require_once cms_join_path(CMS_BASE, 'lib', 'test.functions.php');
require_once cms_join_path(CMS_INSTALL_BASE, 'lib', 'functions.php');
require_once cms_join_path(CMS_INSTALL_BASE, 'translation.functions.php');
require_once cms_join_path(CMS_INSTALL_BASE, 'lib', 'classes', 'CMSInstaller.class.php');

/* Check SESSION */
if(! extension_loaded_or('session') )
{
	installerShowErrorPage('Session module is disabled or missing in your PHP, you have problem with some modules and functionality! Ask your provider, exiting!', 'Session_module_is_disable_or_missing');
}
@session_start();


/* UNDOCUMENTED features... if this values are set in the session */
/* Set DEBUG */
$debug = false;
if( (isset($_GET['debug'])) || (isset($_SESSION['debug'])) )
{
	if(! isset($_SESSION['debug'])) $_SESSION['debug'] = 1;
	@ini_set('display_errors', 1);
	@error_reporting(E_ALL);
	$debug = true;
}
/* Set memory_limit without add in file */
if( (isset($_GET['memory_limit'])) || (isset($_SESSION['memory_limit'])) )
{
	if(! isset($_SESSION['memory_limit'])) $_SESSION['memory_limit'] = $_GET['memory_limit'];
	ini_set('memory_limit', $_SESSION['memory_limit']);
}
/* Skip safe mode tests */
if(isset($_GET['allowsafemode']))
{
	$_SESSION['allowsafemode'] = 1;
}
/* Skip testremote tests */
if(isset($_GET['skipremote']))
{
	$_SESSION['skipremote'] = 1;
}
/* Skip blocking test. For advanced users ONLY */
if(isset($_GET['advanceduser']))
{
	$_SESSION['advanceduser'] = 1;
}


$installer = new CMSInstaller($max_pages, $debug);

require_once cms_join_path(CMS_BASE, 'include.php');

// Initial Tests
if(! isset($_GET['sessiontest']) && (! isset($_POST['page'])) )
{
	// Test for session
	$_SESSION['test'] = true;

	// Tests for smarty
	if(! extension_loaded_or('tokenizer') )
	{
		installerShowErrorPage('Tokenizer extension is disabled or missing in your PHP, this could cause pages to render as purely white. We required you have this installed! Check your installation, exiting!', 'Tokenizer_extension_is_disable_or_missing');
	}

	@clearstatcache();
	$pathSmartClass = cms_join_path(CMS_BASE, 'lib', 'smarty', 'Smarty.class.php');
	if(! is_readable($pathSmartClass))
	{
		installerShowErrorPage('Smarty.class.php cannot be found or not readable! Check your installation for '. $pathSmartClass .', exiting!', 'Smarty.class.php_cannot_be_found_or_not_readable');
	}

	$test_writables = array(TMP_TEMPLATES_C_LOCATION, TMP_CACHE_LOCATION);
	foreach($test_writables as $d)
	{
		$test = testDirWrite('', '', $d, '', 0, $debug);
		if($test->res == 'red')
		{
			installerShowErrorPage('Directory not writable! '. $d .'<br />Please correct by executing: <em>chmod 777</em> or set writing permission for php process, exiting!', 'Directory_not_writable');
		}
	}

	require_once $pathSmartClass;
	$smarty = new Smarty();
	$smarty->compile_dir = TMP_TEMPLATES_C_LOCATION;
	$smarty->cache_dir = TMP_CACHE_LOCATION;
	$smarty->template_dir = cms_join_path(CMS_INSTALL_BASE, 'templates');
	$smarty->caching = false;
	$smarty->force_compile = true;
	$smarty->debugging = false;

	$smarty->assign('languages', $installer->dropdown_lang());
	
	if (file_exists(cms_join_path(CMS_INSTALL_BASE, 'releasenotes.txt')))
		$smarty->assign('release_notes', get_file_contents(cms_join_path(CMS_INSTALL_BASE, 'releasenotes.txt')));
	
	$smarty->display('installer_start.tpl');
	$smarty->display('pagestart.tpl');
	$smarty->display('installer_end.tpl');
	exit;
}
else if(! isset($_SESSION['test']))
{
	installerShowErrorPage('Session not working, you have problem with some modules and functionality! Ask your provider, exiting!', 'Session_not_working');
}


// First checks ok

if(isset($_POST['default_cms_lang']))
{
	$frontendlang = $_POST['default_cms_lang'];
}
require_once cms_join_path(CMS_INSTALL_BASE, 'lang.php');
$smarty->register_function('lang_install','smarty_lang');
$smarty->assign('default_cms_lang', $frontendlang);
$smarty->assign('languages', $installer->dropdown_lang());

$help_lang = installerHelpLanguage($frontendlang, 'en_US');
$help_lang = (empty($help_lang)) ? '' : '/'.$help_lang;
$smarty->assign('cms_upgrade_help_url', CMS_UPGRADE_HELP_URL . $help_lang);

$installer->run($process);

function get_file_contents($filename)
{
	if (!function_exists('file_get_contents'))
	{
		$fhandle = fopen($filename, "r");
		$fcontents = fread($fhandle, filesize($filename));
		fclose($fhandle);
	}
	else
	{
		$fcontents = file_get_contents($filename);
	}
	return $fcontents;
}

?>
