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
echo $themeObject->ShowHeader('systeminfo');
echo '<div class="pageoverflow">';

$smarty->assign('cms_version',$GLOBALS['CMS_VERSION']);
$query = "SELECT * FROM ".cms_db_prefix()."modules WHERE active=1";
$modules = $db->GetArray($query);
$smarty->assign('installed_modules',$modules);

$tmp = array();
$safe_mode = ini_get_boolean('safe_mode');
$tmp['safe_mode'] = array(($safe_mode?lang('on'):lang('off')), ($safe_mode)?'red':'');
$tmp['phpversion'] = phpversion();
$tmp['memory_limit'] = get_cfg_var('memory_limit');
$tmp['post_max_size'] = get_cfg_var('post_max_size');
$tmp['upload_max_filesize'] = get_cfg_var('upload_max_filesize');
$tmp['max_execution_time'] = get_cfg_var('max_execution_time');
$tmp2 = session_save_path();
$tmp['session_save_path'] = array($tmp2, empty($tmp2)?'red':'');
$smarty->assign('php_information',$tmp);


// // Note: we should be displaying some funky stuff for postgres
// // Here too I guess.
// echo "<fieldset>\n";
// echo "<legend><strong>".lang('mysql_information')."</strong>: </legend>\n";
// nrow(lang('mysql_server_version'),mysql_get_server_info());
// echo "</fieldset><br/>\n";

$tmp = array();
$tmp['server_software'] = $_SERVER['SERVER_SOFTWARE'];
$tmp['server_api'] = PHP_SAPI;
$tmp['server_os'] = PHP_OS.' v '.php_uname('r').' '.lang('on').' '.php_uname('m');
$smarty->assign('server_info',$tmp);

clearstatcache();
$tmp = array();
$tmp['tmp'] = substr(sprintf('%o', fileperms($config['root_path'].DIRECTORY_SEPARATOR.'tmp')), -4);
$tmp['tmp/cache'] = substr(sprintf('%o', fileperms($config['root_path'].DIRECTORY_SEPARATOR.'tmp/cache')), -4);
$tmp['tmp/templates_c'] = substr(sprintf('%o', fileperms($config['root_path'].DIRECTORY_SEPARATOR.'tmp/templates_c')), -4);
$tmp['uploads'] = substr(sprintf('%o', fileperms($config['root_path'].DIRECTORY_SEPARATOR.'uploads')), -4);
$tmp['modules'] = substr(sprintf('%o', fileperms($config['root_path'].DIRECTORY_SEPARATOR.'modules')), -4);
$smarty->assign('permission_info',$tmp);

echo $smarty->fetch('systeminfo.tpl');

// Done
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
echo "</div></div>";
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
