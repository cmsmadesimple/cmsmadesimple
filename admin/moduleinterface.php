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
#but WITHOUT ANY WARRANthe TY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

$CMS_ADMIN_PAGE=1;
$CMS_MODULE_PAGE=1;

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'cmsms.api.php');

$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

$userid = CmsLogin::get_userid(true);

if( isset($_SESSION['cms_passthru']) )
{
	$_REQUEST = array_merge($_REQUEST,$_SESSION['cms_passthru']);
	unset($_SESSION['cms_passthru']);
}
$smarty = cms_smarty();
$smarty->assign('date_format_string',get_preference($userid,'date_format_string','%x %X'));

$id = '';
$module = '';
$action = 'defaultadmin';
$suppressOutput = false;
if (isset($_REQUEST['module'])) $module = $_REQUEST['module'];
if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
if (isset($_REQUEST['id']))
{
	$id = $_REQUEST['id'];
}
elseif (isset($_REQUEST['mact']))
{
	$ary = explode(',', cms_htmlentities($_REQUEST['mact']), 4);
	$module = (isset($ary[0])?$ary[0]:'');
	$id = (isset($ary[1])?$ary[1]:'');
	$action = (isset($ary[2])?$ary[2]:'');
}

$module_obj = CmsModuleLoader::get_module_class($module);

if ($module_obj && CmsModuleLoader::get_module_info($module, 'wysiwyg'))
{
	if (get_preference($userid, 'use_wysiwyg') == "1")
	{
		$htmlarea_flag = "true";
		$htmlarea_replaceall = true;
	}
}

$USE_OUTPUT_BUFFERING = true;
$USE_THEME = true;
/*
if ($module_obj && $module_obj->HasAdminBuffering() == false)
{
	$USE_OUTPUT_BUFFERING = false;
}
else */if (isset($_REQUEST[$id . 'disable_buffer']))
{
	$USE_OUTPUT_BUFFERING = false;
}
else if (isset($_REQUEST['disable_buffer']))
{
	$USE_OUTPUT_BUFFERING = false;
}

if( isset( $_REQUEST[$id . 'disable_theme'] ))
{
	$USE_THEME = false;
}
else if( isset( $_REQUEST['disable_theme'] ))
{
	$USE_THEME = false;
}

if( isset($_REQUEST['showtemplate']) && ($_REQUEST['showtemplate'] == 'false'))
{
  // for simplicity and compatibility with the frontend.
  $USE_THEME = false;
  $USE_OUTPUT_BUFFERING = false;
}

if ($module_obj)
{
	$txt = $module_obj->get_header_html(true);
	if ($txt !== false)
	{
		$headtext = $txt;
	}
}
else
{
	$headtext = '';
}

if ($module_obj && ($module_obj->suppress_admin_output($_REQUEST) != false || isset($_REQUEST['suppressoutput'])))
{
	$suppressOutput = true;
}
else
{
	include_once("header.php");
}

if (!$module_obj)
{
	trigger_error('Module '.$module.' not found in memory. This could indicate that the module is in need of upgrade or that there are other problems');
	//redirect("index.php".$urlext);

}
if (isset($USE_THEME) && $USE_THEME == false)
{
	echo '';
}
else
{
	$params = GetModuleParameters($id);
	if (FALSE == empty($params['module_message']))
	{
		echo $themeObject->ShowMessage($params['module_message']);
	}
	if (FALSE == empty($params['module_error']))
	{
		echo $themeObject->ShowErrors($params['module_error']);
	}
	if (!$suppressOutput)
	{
		echo '<div class="pagecontainer">';
		echo '<div class="pageoverflow">';
		echo $themeObject->ShowHeader(CmsLanguage::translate($module_obj->get_name(), array(), $module_obj->get_name()), '', '', 'both');
		echo '</div>';
	}
}

if ($module_obj)
{
	if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false))
	{
		@ob_start();
	}
	$id = 'm1_';
	$params = GetModuleParameters($id);
	if (is_subclass_of($module_obj, 'CmsModuleBase'))
	{
		$module_obj->set_id($id, ''); //admin gets no return id
		echo $module_obj->run_action($action, $params);
	}
	else
	{
		$module_obj->DoActionBase($action, $id, $params, $returnid);
	}
	if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false))
	{
		$content = @ob_get_contents();
		@ob_end_clean();
		echo $content;
	}
	if (!$suppressOutput)
	{
		echo '</div>';
	}
}
else
{
	//redirect("index.php".$urlext);
}

if (isset($USE_THEME) && $USE_THEME == false)
{
	echo '';
}
elseif (!$suppressOutput)
{
	echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
}

if (!$suppressOutput)
{
	include_once("footer.php");
}
# vim:ts=4 sw=4 noet
?>
