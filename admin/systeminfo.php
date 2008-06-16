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
include_once("header.php");
include_once("systeminfo.function.php");


check_login();

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
$db = &$gCms->GetDb();
$config = &$gCms->config;

echo '<div class="pagecontainer">';
//hardcoded: copy from adminlog. Text only or icon link also and here or under System Information (in ShowHeader('systeminfo'))?
if(empty($_GET['cleanreport'])) {
	echo '<p class="pageshowrows"><a href="systeminfo.php?cleanreport=1">Click here for copy/paste in forum posting</a></p>';
}
echo $themeObject->ShowHeader('systeminfo');
echo '<div class="pageoverflow">';

$smarty->assign('cms_version',$GLOBALS['CMS_VERSION']);
$query = "SELECT * FROM ".cms_db_prefix()."modules WHERE active=1";
$modules = $db->GetArray($query);
$smarty->assign('installed_modules',$modules);


//Note:
//$tmp[0][] display on all templates
//$tmp[1][] display on systemtype.tpl only


$tmp = array();
$safe_mode = ini_get_boolean('safe_mode');
$tmp[0]['safe_mode'] = array(($safe_mode?lang('on'):lang('off')), ($safe_mode)?'red':'');

$_phpversion = phpversion();
$tmp[0]['phpversion'] = array($_phpversion, systeminfo_version_compare($_phpversion, $range_phpversion));

$_memory_limit = get_cfg_var('memory_limit');
$_memory_limit2 = substr($_memory_limit,0,-1); //there is nnM
$tmp[0]['memory_limit'] = array($_memory_limit, systeminfo_range_numeric($_memory_limit2, $range_memory_limit));

$_post_max_size = get_cfg_var('post_max_size');
$_post_max_size2 = substr($_post_max_size,0,-1); //there is nnM
$tmp[0]['post_max_size'] = array($_post_max_size, systeminfo_range_numeric($_post_max_size2, $range_post_max_size));

$_upload_max_filesize = get_cfg_var('upload_max_filesize');
$_upload_max_filesize2 = substr($_upload_max_filesize,0,-1); //there is nnM
$tmp[1]['upload_max_filesize'] = array($_upload_max_filesize, systeminfo_range_numeric($_upload_max_filesize2, $range_upload_max_filesize));

$_max_execution_time = get_cfg_var('max_execution_time');
$tmp[0]['max_execution_time'] = array($_max_execution_time, systeminfo_range_numeric($_max_execution_time, $range_max_execution_time));

$_gd_version = systeminfo_gd_version();
$tmp[0]['gd_version'] = array($_gd_version, systeminfo_range_numeric($_gd_version, $range_gd_version));

$_session_save_path = session_save_path(); //Can be 5;0777;/tmp
if(strrpos($_session_save_path, ";") !== false) $_session_save_path = substr($_session_save_path, strrpos($_session_save_path, ";")+1);
$tmp[0]['session_save_path'] = array($_session_save_path.' ('.substr(sprintf('%o', fileperms($_session_save_path)), -4).')', systeminfo_dir_write($_session_save_path));

$smarty->assign('php_information',$tmp);



$tmp = array();
$tmp[0]['server_software'] = $_SERVER['SERVER_SOFTWARE'];
$tmp[0]['server_api'] = PHP_SAPI;
$tmp[0]['server_os'] = PHP_OS.' v '.php_uname('r').' '.lang('on').' '.php_uname('m');

switch($config['dbms']) //workaroud: ServerInfo() is unsupported in adodblite
{
	case 'postgres7': $tmp[0]['server_db_type'] = 'PostgreSQL ('.$config['dbms'].')';
					$v = pg_version();
					$_server_db = (isset($v['server_version'])) ? $v['server_version'] : $v['client'];
					$tmp[0]['server_db_version'] = array($_server_db, systeminfo_version_compare($_server_db, $range_pgsqlversion));
					break;
	case 'mysqli':	$v = $db->connectionId->server_info;
	case 'mysql':	if(!isset($v)) $v = mysql_get_server_info();
					$tmp[0]['server_db_type'] = 'MySQL ('.$config['dbms'].')';
					$_server_db = (false === strpos($v, "-")) ? $v : substr($v, 0, strpos($v, "-"));
					$tmp[0]['server_db_version'] = array($_server_db, systeminfo_version_compare($_server_db, $range_mysqlversion));
					break;
}
$smarty->assign('server_info',$tmp);


clearstatcache();
$tmp = array();
$tmp[0]['tmp'] = substr(sprintf('%o', fileperms($config['root_path'].DIRECTORY_SEPARATOR.'tmp')), -4);
$tmp[0]['tmp/cache'] = substr(sprintf('%o', fileperms($config['root_path'].DIRECTORY_SEPARATOR.'tmp/cache')), -4);
$tmp[0]['tmp/templates_c'] = substr(sprintf('%o', fileperms($config['root_path'].DIRECTORY_SEPARATOR.'tmp/templates_c')), -4);
$tmp[0]['uploads'] = substr(sprintf('%o', fileperms($config['root_path'].DIRECTORY_SEPARATOR.'uploads')), -4);
$tmp[0]['modules'] = substr(sprintf('%o', fileperms($config['root_path'].DIRECTORY_SEPARATOR.'modules')), -4);
$smarty->assign('permission_info',$tmp);


$tmp = array();
$tmp[0]['php_memory_limit'] = $config['php_memory_limit'];
$tmp[0]['process_whole_template'] = (true == $config['process_whole_template']) ? 'true' : 'false';
$tmp[1]['root_url'] = $config['root_url'];
$tmp[1]['root_path'] = array($config['root_path'].' ('.substr(sprintf('%o', fileperms($config['root_path'])), -4).')', systeminfo_dir_write($config['root_path']));
$tmp[1]['previews_path'] = array($config['previews_path'].' ('.substr(sprintf('%o', fileperms($config['previews_path'])), -4).')', systeminfo_dir_write($config['previews_path']));
$tmp[1]['uploads_path'] = array($config['uploads_path'].' ('.substr(sprintf('%o', fileperms($config['uploads_path'])), -4).')', systeminfo_dir_write($config['uploads_path']));
$tmp[1]['uploads_url'] = $config['uploads_url'];
$tmp[1]['image_uploads_path'] = array($config['image_uploads_path'].' ('.substr(sprintf('%o', fileperms($config['image_uploads_path'])), -4).')', systeminfo_dir_write($config['image_uploads_path']));
$tmp[1]['image_uploads_url'] = $config['image_uploads_url'];
$tmp[1]['debug'] = (true == $config['debug']) ? 'true' : 'false';
$tmp[1]['locale'] = $config['locale'];
$tmp[1]['default_encoding'] = $config['default_encoding'];
$tmp[1]['admin_encoding'] = $config['admin_encoding'];
$tmp[0]['max_upload_size'] = $config['max_upload_size'];
$tmp[0]['default_upload_permission'] = $config['default_upload_permission'];
$tmp[0]['use_smarty_php_tags'] = (true == $config['use_smarty_php_tags']) ? 'true' : 'false';
$tmp[0]['assume_mod_rewrite'] = (true == $config['assume_mod_rewrite']) ? 'true' : 'false';
$tmp[0]['page_extension'] = $config['page_extension'];
$tmp[0]['internal_pretty_urls'] = (true == $config['internal_pretty_urls']) ? 'true' : 'false';
$tmp[0]['use_hierarchy'] = (true == $config['use_hierarchy']) ? 'true' : 'false';
$smarty->assign('config_info',$tmp);


if(isset($_GET['cleanreport']) && $_GET['cleanreport'] == 1) echo $smarty->fetch('systeminfo.txt.tpl');
else echo $smarty->fetch('systeminfo.tpl');

// Done
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
echo "</div></div>";
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
