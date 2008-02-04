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
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

$id_to_assign = coalesce_key($_REQUEST, 'id_to_assign', '');
$template_id = coalesce_key($_REQUEST, 'template_id', '');
$stylesheet_id = coalesce_key($_REQUEST, 'stylesheet_id', '');
$type = coalesce_key($_REQUEST, 'type', ($stylesheet_id != '') ? 'stylesheet' : 'template');

if (isset($_REQUEST['cancel']))
{
	if ($type == 'stylesheet')
	{
		redirect('liststylesheets.php');
	}
	else
	{
		redirect('listtemplates.php');
	}
}

check_login();

//TODO: Check permissions
$modify_layout = true;

if (isset($_REQUEST['submitbutton']) && $modify_layout && $id_to_assign != '')
{
	if ($type == 'template')
	{
		$template = cms_orm('template')->find_by_id($template_id);
		if ($template)
		{
			$template->assign_stylesheet_by_id($id_to_assign);
		}
	}
	else if ($type == 'stylesheet')
	{
		$stylesheet = cms_orm('stylesheet')->find_by_id($stylesheet_id);
		if ($stylesheet)
		{
			$stylesheet->assign_template_by_id($id_to_assign);
		}
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'remove' && $modify_layout && $type != '')
{
	if ($type == 'template')
	{
		$template = cms_orm('template')->find_by_id($template_id);
		if ($template)
		{
			$template->remove_assigned_stylesheet_by_id($stylesheet_id);
		}
	}
	else if ($type == 'stylesheet')
	{
		$stylesheet = cms_orm('stylesheet')->find_by_id($stylesheet_id);
		if ($stylesheet)
		{
			$stylesheet->remove_assigned_template_by_id($template_id);
		}
	}
}

include_once("header.php");

$smarty = cms_smarty();
$smarty->assign('modify_layout', $modify_layout);
$smarty->assign('header_name', $themeObject->ShowHeader('assignedstylesheets'));
local_setup_smarty($page, $modify_layout, $type);
$smarty->display('assignstylesheets.tpl');

include_once("footer.php");

function orm_array_udiff_compare($a, $b)
{
	if ($a->id === $b->id)
	{
		return 0;
	}
	return ($a->id > $b->id) ? 1 : -1;
}

function local_setup_smarty($page, $modify_layout, $type)
{
	$smarty = cms_smarty();
	$userid = get_userid();
	
	$template_id = coalesce_key($_REQUEST, 'template_id', '');
	$stylesheet_id = coalesce_key($_REQUEST, 'stylesheet_id', '');
	
	$smarty->assign('template_id', $template_id);
	$smarty->assign('stylesheet_id', $stylesheet_id);
	
	//Are we assigning to a template or stylesheet?
	if ($type == 'template')
	{
		$template = cms_orm('template')->find_by_id($template_id);
		if ($template)
		{
			$assigned_stylesheets = $template->stylesheets;
			$all_stylesheets = cms_orm('stylesheet')->find_all(array('order' => 'name ASC'));
			$unassigned_stylesheets = array_udiff($all_stylesheets, $assigned_stylesheets->children, 'orm_array_udiff_compare');
			
			$smarty->assign('assigned', $assigned_stylesheets);
			$smarty->assign('all', $all_stylesheets);
			
			//Generate stuff for dropdown
			$dd_stuff = array();
			foreach ($unassigned_stylesheets as $obj)
			{
				$dd_stuff[$obj->id] = $obj->name;
			}
			$smarty->assign('unassigned', $dd_stuff);
			
			$smarty->assign('type', 'template');
		}
	}
	else if ($type == 'stylesheet')
	{
		$stylesheet = cms_orm('stylesheet')->find_by_id($stylesheet_id);
		if ($stylesheet)
		{
			$assigned_templates = $stylesheet->templates;
			$all_templates = cms_orm('template')->find_all(array('order' => 'name ASC'));
			$unassigned_templates = array_udiff($all_templates, $assigned_templates->children, 'orm_array_udiff_compare');
			
			$smarty->assign('assigned', $assigned_templates);
			$smarty->assign('all', $all_templates);
			
			//Generate stuff for dropdown
			$dd_stuff = array();
			foreach ($unassigned_templates as $obj)
			{
				$dd_stuff[$obj->id] = $obj->name;
			}
			$smarty->assign('unassigned', $dd_stuff);
			
			$smarty->assign('type', 'stylesheet');
		}
	}
	else
	{
		return;
	}
}

# vim:ts=4 sw=4 noet
?>
