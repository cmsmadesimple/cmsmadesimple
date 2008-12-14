<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

require_once("../include.php");

check_login();
$userid = get_userid();

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

if (isset($gCms->modules[$module]) && $gCms->modules[$module]['object']->is_wysiwyg())
{
	if (get_preference($userid, 'use_wysiwyg') == "1")
	{
		$htmlarea_flag = "true";
		$htmlarea_replaceall = true;
	}
}

$USE_OUTPUT_BUFFERING = true;
if (isset($gCms->modules[$module]['object']) && $gCms->modules[$module]['object']->has_admin_buffering() == false)
{
	$USE_OUTPUT_BUFFERING = false;
}
else if (isset($_REQUEST[$id . 'disable_buffer']))
{
	$USE_OUTPUT_BUFFERING = false;
}
else if (isset($_REQUEST['disable_buffer']))
{
	$USE_OUTPUT_BUFFERING = false;
}

$USE_THEME = true;
if( isset( $_REQUEST[$id . 'disable_theme'] ))
{
	$USE_THEME = false;
}
else if( isset( $_REQUEST['disable_theme'] ))
{
	$USE_THEME = false;
}
if (isset($gCms->modules[$module]['object']) && $gCms->modules[$module]['object']->get_header_html() != false)
  {
    $headtext =  $gCms->modules[$module]['object']->get_header_html();
  }
 else
   {
     $headtext = '';
   }

if (isset($gCms->modules[$module]['object']) && $gCms->modules[$module]['object']->suppress_admin_output($_REQUEST) != false)
	{
	$suppressOutput = true;
	}
else
	{
	include_once("header.php");
 	}

if (count($gCms->modules) > 0)
{
	if (isset($USE_THEME) && $USE_THEME == false)
	{
		echo '';
	}
	else
	{
	  $params = GetModuleParameters($id);
	  if (FALSE == empty($params['module_message']))
	    {
	      echo $themeObject->show_message($params['module_message']);
	    }
	  if (FALSE == empty($params['module_error']))
	    {
	      echo $themeObject->show_errors($params['module_error']);
	    }
	  if (!$suppressOutput)
	    {
	      echo '<div class="pagecontainer">';
	      echo '<div class="pageoverflow">';
	      echo $themeObject->show_header($gCms->modules[$module]['object']->get_friendly_name(), '', '', 'both').'</div>';
	    }
	}

	if (isset($gCms->modules[$module]))
	{
		if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false))
		{
			@ob_start();
		}
		$id = 'm1_';
		$params = GetModuleParameters($id);
		$request = $gCms->modules[$module]['object']->create_request_instance($id);
		echo $request->do_action_base($action, $params);
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
		redirect("index.php");
	}
}

if (isset($USE_THEME) && $USE_THEME == false)
{
	echo '';
}
elseif (!$suppressOutput)
{
	echo '<p class="pageback"><a class="pageback" href="'.$themeObject->back_url().'">&#171; '.lang('back').'</a></p>';
}
if (!$suppressOutput)
	{
	include_once("footer.php");
 	}
# vim:ts=4 sw=4 noet
?>
