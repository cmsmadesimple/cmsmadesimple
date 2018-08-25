<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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

require_once("../lib/include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
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


function installerHelpLanguage( $lang, $default_null=null )
{
        if( (!is_null($default_null)) && ($default_null == $lang) ) return '';
        return substr($lang, 0, 2);
}

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

$gCms = cmsms();
$smarty = $gCms->GetSmarty();
$smarty->register_function('si_lang','systeminfo_lang');
$smarty->caching = false;
$smarty->force_compile = true;
$db = $gCms->GetDb();



//smartyfier
$smarty->assign('themename', $themeObject->themeName);
$smarty->assign('showheader', $themeObject->ShowHeader('systeminfo'));
$smarty->assign('backurl', $themeObject->BackUrl());
$smarty->assign('systeminfo_cleanreport', 'systeminfo.php'.$urlext.'&amp;cleanreport=1');

/* Default help url */
$smarty->assign('cms_install_help_url', 'https://docs.cmsmadesimple.org/installation/installing/permissions-and-php-settings');


/* CMS Install Information */

$smarty->assign('cms_version', $GLOBALS['CMS_VERSION']);


$query = "SELECT * FROM ".cms_db_prefix()."modules WHERE active=1";
$modules = $db->GetArray($query);
asort($modules);
$smarty->assign('installed_modules', $modules);

clearstatcache();
$tmp = array(0=>array(), 1=>array());

$tmp[0]['php_memory_limit'] = testConfig('php_memory_limit', 'php_memory_limit');
$tmp[1]['debug'] = testConfig('debug', 'debug');

$tmp[0]['max_upload_size'] = testConfig('max_upload_size', 'max_upload_size');
$tmp[0]['url_rewriting'] = testConfig('url_rewriting', 'url_rewriting');
$tmp[0]['page_extension'] = testConfig('page_extension', 'page_extension');
$tmp[0]['query_var'] = testConfig('query_var', 'query_var');

$tmp[1]['root_url'] = testConfig('root_url', 'root_url');
$tmp[1]['ssl_url'] = testConfig('ssl_url', 'ssl_url');
$tmp[1]['root_path'] = testConfig('root_path', 'root_path', 'testDirWrite');
$tmp[1]['uploads_path'] = testConfig('uploads_path', 'uploads_path', 'testDirWrite');
$tmp[1]['uploads_url'] = testConfig('uploads_url', 'uploads_url');
$tmp[1]['image_uploads_path'] = testConfig('image_uploads_path', 'image_uploads_path', 'testDirWrite');
$tmp[1]['image_uploads_url'] = testConfig('image_uploads_url', 'image_uploads_url');
$tmp[1]['ssl_uploads_url'] = testConfig('ssl_uploads_url', 'ssl_uploads_url');
$tmp[0]['auto_alias_content'] = testConfig('auto_alias_content', 'auto_alias_content');
$tmp[0]['locale'] = testConfig('locale', 'locale');
//$tmp[0]['default_encoding'] = testConfig('default_encoding', 'default_encoding');
//$tmp[0]['admin_encoding'] = testConfig('admin_encoding', 'admin_encoding');
$tmp[0]['set_names'] = testConfig('set_names', 'set_names');
$tmp[0]['timezone'] = testConfig('timezone', 'timezone');
$tmp[0]['permissive_smarty'] = testConfig('permissive_smarty','permissive_smarty');
$smarty->assign('count_config_info', count($tmp[0]));
$smarty->assign('config_info', $tmp);

/* Performance Information */
$tmp = array(0=>array(), 1=>array());

$res = get_site_preference('allow_browser_cache',0);
$tmp[0]['allow_browser_cache'] = testBoolean(0, lang('allow_browser_cache'),$res,lang('test_allow_browser_cache'), FALSE);
$res = get_site_preference('browser_cache_expiry',60);
$tmp[0]['browser_cache_expiry'] = testRange(0, lang('browser_cache_expiry'),$res,lang('test_browser_cache_expiry'),1,60,FALSE);

if( version_compare(phpversion(),'5.5') >= 0 ) {
    $opcache = ini_get('opcache.enable');
    $tmp[0]['php_opcache'] = testBoolean(0, lang('php_opcache'), $opcache, '', false, false, 'opcache_enabled');
} else {
    $tmp[0]['php_opcache'] = testBoolean(0, lang('php_opcache'), false, '', false, false, 'opcache_notavailable');
}
$res = (bool) get_site_preference('use_smartycache', FALSE);
$tmp[0]['smarty_cache'] = testBoolean(0, lang('prompt_use_smartycaching'),$res,lang('test_smarty_caching'), FALSE);
$res = get_site_preference('use_smartycompilecheck', FALSE);
$tmp[0]['smarty_compilecheck'] = testBoolean(0, lang('prompt_smarty_compilecheck'),$res,lang('test_smarty_caching'),FALSE,TRUE);
$res = get_site_preference('auto_clear_cache_age', 0);
$tmp[0]['auto_clear_cache_age'] = testBoolean(0, lang('autoclearcache2'),$res,lang('test_auto_clear_cache_age'), FALSE);

$smarty->assign('performance_info', $tmp);


/* PHP Information */

$tmp = array(0=>array(), 1=>array());

$session_save_path = ini_get('session.save_path');
$open_basedir = ini_get('open_basedir');

list($minimum, $recommended) = getTestValues('php_version');
$tmp[0]['phpversion'] = testVersionRange(0, 'phpversion', phpversion(), '', $minimum, $recommended, false);

$tmp[0]['md5_function'] = testBoolean(0, 'md5_function', function_exists('md5'), '', false, false, 'Function_md5_disabled');
$tmp[0]['json_function'] = testBoolean(0, 'json_function', function_exists('json_decode'), '', false, false, 'json_disabled');

list($minimum, $recommended) = getTestValues('gd_version');
$tmp[0]['gd_version'] = testGDVersion(0, 'gd_version', $minimum, '', 'min_GD_version');

$tmp[0]['tempnam_function'] = testBoolean(0, 'tempnam_function', function_exists('tempnam'), '', false, false, 'Function_tempnam_disabled');

$tmp[0]['magic_quotes_runtime'] = testBoolean(0, 'magic_quotes_runtime', 'magic_quotes_runtime', lang('magic_quotes_runtime_on'), true, true, 'magic_quotes_runtime_On');
$tmp[0]['E_ALL'] = testIntegerMask(0,lang('test_error_eall'), 'error_reporting',E_ALL,lang('test_eall_failed'),true,false,false);
$tmp[0]['E_STRICT'] = testIntegerMask(0,lang('test_error_estrict'), 'error_reporting',E_STRICT,lang('test_estrict_failed'),true,true,false);
if( defined('E_DEPRECATED') ) {
    $tmp[0]['E_DEPRECATED'] =  testIntegerMask(0,lang('test_error_edeprecated'), 'error_reporting',E_DEPRECATED,lang('test_edeprecated_failed'),true,true,false);
  }

$_tmp = _testTimeSettings1();
$tmp[0]['test_file_timedifference'] = ($_tmp->value) ? testDummy('test_file_timedifference',lang('msg_notimedifference2'),'green') : testDummy('test_file_timedifference',lang('error_timedifference2'),'red');
$_tmp = _testTimeSettings2();
$tmp[0]['test_db_timedifference'] = ($_tmp->value) ? testDummy('test_db_timedifference',lang('msg_notimedifference2'),'green') : testDummy('test_file_timedifference',lang('error_timedifference2'),'red');

$tmp[0]['create_dir_and_file'] = testCreateDirAndFile(0, '', '');

list($minimum, $recommended) = getTestValues('memory_limit');
$tmp[0]['memory_limit'] = testRange(0, 'memory_limit', 'memory_limit', '', $minimum, $recommended, true, true, -1, 'memory_limit_range');

list($minimum, $recommended) = getTestValues('max_execution_time');
$tmp[0]['max_execution_time'] = testRange(0, 'max_execution_time', 'max_execution_time', '', $minimum, $recommended, true, false, 0, 'max_execution_time_range');

$tmp[0]['register_globals'] = testBoolean(0, lang('register_globals'), 'register_globals', '', true, true, 'register_globals_enabled');

$ob = ini_get('output_buffering');
if( strtolower($ob) == 'off' || strtolower($ob) == 'on' )
  {
    $tmp[0]['output_buffering'] = testBoolean(0, lang('output_buffering'), 'output_buffering', '', true, false, 'output_buffering_disabled');
  }
else
  {
    $tmp[0]['output_buffering'] = testInteger(0, lang('output_buffering'), 'output_buffering', '', true, true, 'output_buffering_disabled');
  }

$tmp[0]['disable_functions'] = testString(0, lang('disable_functions'), 'disable_functions', '', true, 'green', 'yellow', 'disable_functions_not_empty');

$tmp[0]['open_basedir'] = testString(0, lang('open_basedir'), $open_basedir, '', false, 'green', 'yellow', 'open_basedir_enabled');

$tmp[0]['test_remote_url'] = testRemoteFile(0, 'test_remote_url', '', lang('test_remote_url_failed'));

$tmp[0]['file_uploads'] = testBoolean(0, 'file_uploads', 'file_uploads', '', true, false, 'Function_file_uploads_disabled');

list($minimum, $recommended) = getTestValues('post_max_size');
$tmp[0]['post_max_size'] = testRange(0, 'post_max_size', 'post_max_size', '', $minimum, $recommended, true, true, null, 'min_post_max_size');

list($minimum, $recommended) = getTestValues('upload_max_filesize');
$tmp[0]['upload_max_filesize'] = testRange(0, 'upload_max_filesize', 'upload_max_filesize', '', $minimum, $recommended, true, true, null, 'min_upload_max_filesize');

$session_save_path = testSessionSavePath('');
if(empty($session_save_path))
{
	$tmp[0]['session_save_path'] = testDummy('session_save_path', lang('os_session_save_path'), 'yellow', '', 'session_save_path_empty', '');
}
elseif (! empty($open_basedir))
{
	$tmp[0]['session_save_path'] = testDummy('session_save_path', lang('open_basedir_active'), 'yellow', '', 'No_check_session_save_path_with_open_basedir', '');
}
else
{
	$tmp[0]['session_save_path'] = testDirWrite(0, lang('session_save_path'), $session_save_path, $session_save_path, 1);
}
$tmp[0]['session_use_cookies'] = testBoolean(0, 'session.use_cookies', 'session.use_cookies');

$tmp[0]['xml_function'] = testBoolean(1, 'xml_function', extension_loaded_or('xml'), '', false, false, 'Function_xml_disabled');
$tmp[0]['xmlreader_class'] = testBoolean(1,'xmlreader_class',class_exists('XMLReader',false),'',false,false,'class_xmlreader_unavailable');

#$tmp[1]['file_get_contents'] = testBoolean(0, 'file_get_contents', function_exists('file_get_contents'), '', false, false, 'Function_file_get_content_disabled');

$_log_errors_max_len = (ini_get('log_errors_max_len')) ? ini_get('log_errors_max_len').'0' : '99';
ini_set('log_errors_max_len', $_log_errors_max_len);
$result = (ini_get('log_errors_max_len') == $_log_errors_max_len);
$tmp[0]['check_ini_set'] = testBoolean(0, 'check_ini_set', $result, lang('check_ini_set_off'), false, false, 'ini_set_disabled');

$hascurl = 0;
$curlgood = 0;
$curl_version = '';
$min_curlversion = '7.19.7';
if( in_array('curl',get_loaded_extensions()) ) {
    $hascurl = 1;
    if( function_exists('curl_version') ) {
        $t = curl_version();
        if( isset($t['version']) ) {
            $curl_version = $t['version'];
            if( version_compare($t['version'],$min_curlversion) >= 0 ) {
                $curlgood = 1;
            }
        }
    }
}
if( !$hascurl ) {
    $tmp[0]['curl'] = testDummy('curl',lang('off'),'yellow','','curl_not_available','');
}
else {
    $tmp[0]['curl'] = testDummy('curl',lang('on'),'green');
    if( $curlgood ) {
        $tmp[1]['curlversion'] = testDummy('curlversion',
                                           lang('curl_versionstr',$curl_version,$min_curlversion),
                                           'green');
    }
    else {
        $tmp[1]['curlversion'] = testDummy('curlversion',lang('test_curlversion'),'yellow',
                                           lang('curl_versionstr',$curl_version,$min_curlversion));
    }
}
$smarty->assign('count_php_information', count($tmp[0]));
$smarty->assign('php_information', $tmp);



/* Server Information */

$tmp = array(0=>array(), 1=>array());

$tmp[0]['server_software'] = testDummy('', $_SERVER['SERVER_SOFTWARE'], '');
$tmp[0]['server_api'] = testDummy('', PHP_SAPI, '');
$tmp[0]['server_os'] = testDummy('', PHP_OS . ' ' . php_uname('r') .' '. lang('on') .' '. php_uname('m'), '');

switch($config['dbms']) { //workaround: ServerInfo() is unsupported in adodblite
 case 'mysqli':
 case 'mysql':
   $v = $db->GetOne('SELECT version()');
   $tmp[0]['server_db_type'] = testDummy('', 'MySQL ('.$config['dbms'].')', '');
   $_server_db = (false === strpos($v, "-")) ? $v : substr($v, 0, strpos($v, "-"));
   list($minimum, $recommended) = getTestValues('mysql_version');
   $tmp[0]['server_db_version'] = testVersionRange(0, 'server_db_version', $_server_db, '', $minimum, $recommended, false);

   $grants = $db->GetArray('SHOW GRANTS FOR CURRENT_USER');
   if( !is_array($grants) || count($grants) == 0 ) {
     $tmp[0]['server_db_grants'] = testDummy('db_grants',lang('os_db_grants'),'yellow','','error_no_grantall_info');
   }
   else {
     $found_grantall = 0;
     function __check_grant_all($item,$key)
     {
       $item = strtoupper($item);
       if( strstr($item,'GRANT ALL PRIVILEGES') !== FALSE )
	 {
	   global $found_grantall;
	   $found_grantall = 1;
	 }
     }
     array_walk_recursive($grants,'__check_grant_all');
     if( !$found_grantall ) {
       $tmp[0]['server_db_grants'] = testDummy('db_grants',lang('error_nograntall_found'),'yellow');
     }
     else {
       $tmp[0]['server_db_grants'] = testDummy('db_grants',lang('msg_grantall_found'),'green');
     }
   }
   break;
}


$smarty->assign('count_server_info', count($tmp[0]));
$smarty->assign('server_info', $tmp);


$tmp = array(0=>array(), 1=>array());

$dir = $config['root_path'] . DIRECTORY_SEPARATOR . 'tmp';
$tmp[0]['tmp'] = testDirWrite(0, $dir, $dir);

$dir = TMP_CACHE_LOCATION;
$tmp[0]['tmp_cache'] = testDirWrite(0, $dir, $dir);

$dir = TMP_TEMPLATES_C_LOCATION;
$tmp[0]['templates_c'] = testDirWrite(0, $dir, $dir);

$dir = $config['root_path'] . DIRECTORY_SEPARATOR . 'modules';
$tmp[0]['modules'] = testDirWrite(0, $dir, $dir);

$dir = $config['uploads_path'];
$tmp[0]['uploads'] = testDirWrite(0, $dir, $dir);

$global_umask = get_site_preference('global_umask', '022');
$tmp[0][lang('global_umask')] = testUmask(0, lang('global_umask'), $global_umask);

$result = is_writable(CONFIG_FILE_LOCATION);
#$tmp[1]['config_file'] = testFileWritable(0, lang('config_writable'), CONFIG_FILE_LOCATION, '');
$tmp[0]['config_file'] = testDummy('', substr(sprintf('%o', fileperms(CONFIG_FILE_LOCATION)), -4), (($result) ? 'red' : 'green'), (($result) ? lang('config_writable') : ''));

$smarty->assign('count_permission_info', count($tmp[0]));
$smarty->assign('permission_info', $tmp);


if(isset($_GET['cleanreport']) && $_GET['cleanreport'] == 1) {
  $orig_lang = CmsNlsOperations::get_current_language();
  CmsNlsOperations::set_language('en_US');
  echo $smarty->fetch('systeminfo.txt.tpl');
  CmsNlsOperations::set_language($orig_lang);
}
else echo $smarty->fetch('systeminfo.tpl');


include_once("footer.php");

?>
