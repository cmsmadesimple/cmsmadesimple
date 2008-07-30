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
#$Id: supportinfo.php 4216 2007-10-06 19:28:55Z wishy $

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();
$userid = get_userid();
$access = check_permission($userid, "Modify Site Preferences");
if (!$access) {
	die('Permission Denied');
return;
}

include_once("header.php");

define('CMS_BASE', dirname(dirname(__FILE__)));
require_once cms_join_path(CMS_BASE, 'lib', 'test.functions.php');



function systeminfo_lang($params,&$smarty)
{
	if( count($params) )
	{
		$tmp = array();
		foreach( $params as $k=>$v)
		{
			$tmp[] = $v;
		}      
      
		$str = $tmp[0];
		$tmp2 = array();
		for( $i = 1; $i < count($tmp); $i++ )
			$tmp2[] = $params[$i];
		return lang($str,$tmp2);
	}
}

global $gCms;
$smarty =& $gCms->GetSmarty();
$smarty->register_function('si_lang','systeminfo_lang');
$smarty->caching = false;
$smarty->force_compile = true;
$db = &$gCms->GetDb();



//smartyfier
$smarty->assign('themename', $themeObject->themeName);
$smarty->assign('showheader', $themeObject->ShowHeader('systeminfo'));
$smarty->assign('backurl', $themeObject->BackUrl());



/* CMS Install Information */

$smarty->assign('cms_version', $GLOBALS['CMS_VERSION']);


$query = "SELECT * FROM ".cms_db_prefix()."modules WHERE active=1";
$modules = $db->GetArray($query);
$smarty->assign('installed_modules', $modules);


clearstatcache();
$tmp = array(0=>array(), 1=>array());

$tmp[0]['php_memory_limit'] = testConfig('php_memory_limit', 'php_memory_limit');
$tmp[0]['process_whole_template'] = testConfig('process_whole_template', 'process_whole_template');
$tmp[0]['max_upload_size'] = testConfig('max_upload_size', 'max_upload_size');
$tmp[0]['default_upload_permission'] = testConfig('default_upload_permission', 'default_upload_permission');
$tmp[0]['assume_mod_rewrite'] = testConfig('assume_mod_rewrite', 'assume_mod_rewrite');
$tmp[0]['page_extension'] = testConfig('page_extension', 'page_extension');
$tmp[0]['internal_pretty_urls'] = testConfig('internal_pretty_urls', 'internal_pretty_urls');
$tmp[0]['use_hierarchy'] = testConfig('use_hierarchy', 'use_hierarchy');

$tmp[1]['root_url'] = testConfig('root_url', 'root_url');
$tmp[1]['root_path'] = testConfig('root_path', 'root_path');
$tmp[1]['previews_path'] = testConfig('previews_path', 'previews_path', 'testDirWrite');
$tmp[1]['uploads_path'] = testConfig('uploads_path', 'uploads_path', 'testDirWrite');
$tmp[1]['uploads_url'] = testConfig('uploads_url', 'uploads_url');
$tmp[1]['image_uploads_path'] = testConfig('image_uploads_path', 'image_uploads_path', 'testDirWrite');
$tmp[1]['image_uploads_url'] = testConfig('image_uploads_url', 'image_uploads_url');
$tmp[1]['use_smarty_php_tags'] = testConfig('use_smarty_php_tags', 'use_smarty_php_tags');
$tmp[1]['debug'] = testConfig('debug', 'debug');
$tmp[1]['locale'] = testConfig('locale', 'locale');
$tmp[1]['default_encoding'] = testConfig('default_encoding', 'default_encoding');
$tmp[1]['admin_encoding'] = testConfig('admin_encoding', 'admin_encoding');

$smarty->assign('count_config_info', count($tmp[0]));
$smarty->assign('config_info', $tmp);



/* PHP Information */

$tmp = array(0=>array(), 1=>array());

$tmp[0]['safe_mode'] = testIniBoolean(0, 'safe_mode', 'safe_mode', '', true);

$tmp[1]['create_dir_and_file'] = testCreateDirAndFile(0, '', '');

$tmp[1]['open_basedir'] = testIniValue(0, 'open_basedir', 'open_basedir', '');

list($minimum, $recommended) = getTestValues('phpversion');
$tmp[0]['phpversion'] = testVersionRange(0, 'phpversion', phpversion(), $minimum, $recommended);

list($minimum, $recommended) = getTestValues('memory_limit');
$tmp[0]['memory_limit'] = testIniRange(0, 'memory_limit', 'memory_limit', $minimum, $recommended);

list($minimum, $recommended) = getTestValues('post_max_size');
$tmp[1]['post_max_size'] = testIniRange(0, 'post_max_size', 'post_max_size', $minimum, $recommended);

list($minimum, $recommended) = getTestValues('upload_max_filesize');
$tmp[1]['upload_max_filesize'] = testIniRange(0, 'upload_max_filesize', 'upload_max_filesize', $minimum, $recommended);

list($minimum, $recommended) = getTestValues('max_execution_time');
$tmp[0]['max_execution_time'] = testIniRange(0, 'max_execution_time', 'max_execution_time', $minimum, $recommended, '', false, 0);

list($minimum, $recommended) = getTestValues('gd_version');
$tmp[0]['gd_version'] = testGDVersion(0, 'gd_version', $minimum);

$dir = ini_get('session.save_path');
$open_basedir = ini_get('open_basedir');
if ( (ini_get('session.save_handler') == 'files') && (empty($dir)) )
{
	$tmp[0]['session_save_path'] = testDummy('session_save_path', '', 'yellow');
}
elseif (! empty($open_basedir))
{
	$tmp[0]['session_save_path'] = testDummy('session_save_path', '', 'yellow');
}
else
{
	if (strrpos($dir, ";") !== false) $dir = substr($dir, strrpos($dir, ";")+1); //Can be 5;777;/tmp
	$tmp[0]['session_save_path'] = testDirWrite(0, $dir, $dir);
}

$smarty->assign('count_php_information', count($tmp[0]));
$smarty->assign('php_information', $tmp);



/* Server Information */

$tmp = array(0=>array(), 1=>array());

$tmp[1]['server_software'] = testDummy('', $_SERVER['SERVER_SOFTWARE'], '');
$tmp[0]['server_api'] = testDummy('', PHP_SAPI, '');
$tmp[1]['server_os'] = testDummy('', PHP_OS . ' ' . php_uname('r') .' '. lang('on') .' '. php_uname('m'), '');

switch($config['dbms']) //workaroud: ServerInfo() is unsupported in adodblite
{
	case 'postgres7': $tmp[0]['server_db_type'] = testDummy('', 'PostgreSQL ('.$config['dbms'].')', '');
					$v = pg_version();
					$_server_db = (isset($v['server_version'])) ? $v['server_version'] : $v['client'];
					list($minimum, $recommended) = getTestValues('pgsqlversion');
					$tmp[0]['server_db_version'] = testVersionRange(0, 'server_db_version', $_server_db, $minimum, $recommended);
					break;
	case 'mysqli':	$v = $db->connectionId->server_info;
	case 'mysql':	if(!isset($v)) $v = mysql_get_server_info();
					$tmp[0]['server_db_type'] = testDummy('', 'MySQL ('.$config['dbms'].')', '');
					$_server_db = (false === strpos($v, "-")) ? $v : substr($v, 0, strpos($v, "-"));
					list($minimum, $recommended) = getTestValues('mysqlversion');
					$tmp[0]['server_db_version'] = testVersionRange(0, 'server_db_version', $_server_db, $minimum, $recommended);
					break;
}
$smarty->assign('count_server_info', count($tmp[0]));
$smarty->assign('server_info', $tmp);


$tmp = array(0=>array(), 1=>array());

$dir = $config['root_path'] . DIRECTORY_SEPARATOR . 'tmp';
$tmp[1]['tmp'] = testDirWrite(0, $dir, $dir);

$dir = TMP_TEMPLATES_C_LOCATION;
$tmp[1]['templates_c'] = testDirWrite(0, $dir, $dir);

$dir = $config['root_path'] . DIRECTORY_SEPARATOR . 'modules';
$tmp[1]['modules'] = testDirWrite(0, $dir, $dir);

$global_umask = get_site_preference('global_umask', '022');
$tmp[1][lang('global_umask')] = testUmask(0, lang('global_umask'), $global_umask);

$tmp[1]['config_file'] = testDummy('', substr(sprintf('%o', fileperms(CONFIG_FILE_LOCATION)), -4), (is_writable(CONFIG_FILE_LOCATION) ? 'red' : 'green') );

$smarty->assign('count_permission_info', count($tmp[0]));
$smarty->assign('permission_info', $tmp);



if(isset($_GET['cleanreport']) && $_GET['cleanreport'] == 1) echo $smarty->fetch('systeminfo.txt.tpl');
else echo $smarty->fetch('systeminfo.tpl');


include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
