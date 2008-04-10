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
#$Id$

$CMS_ADMIN_PAGE=1;
$LOAD_ALL_MODULES=1;

require_once("../include.php");

check_login();
$smarty = cms_smarty();

include_once("header.php");

$action = coalesce_key($_REQUEST, 'action', '');
$module = coalesce_key($_REQUEST, 'module', '');
$plugin = coalesce_key($_REQUEST, 'plugin', '');

$show_list = true;

if ($action == 'install' && $module != '')
{
	$result = CmsModuleOperations::install_module($module, false);
	if ($result)
	{
		if (cmsms()->modules[$module]['object']->install_post_message())
		{
			CmsResponse::redirect("listmodules.php?action=show_post_install&module={$module}");
		}
		CmsResponse::redirect('listmodules.php');
	}
	else
	{
		$themeObject->add_error(CmsModuleOperations::get_last_error());
	}
}
else if ($action == 'show_post_install' && $module != '')
{
	if (cmsms()->modules[$module]['object']->install_post_message() !== FALSE)
	{
		$themeObject->add_message(cmsms()->modules[$module]['object']->install_post_message());
	}
}
else if ($action == 'uninstall' && $module != '')
{
	$result = CmsModuleOperations::uninstall_module($module);
	if ($result)
	{
		if (cmsms()->modules[$module]['object']->uninstall_post_message())
		{
			CmsResponse::redirect("listmodules.php?action=show_post_uninstall&module={$module}");
		}
		CmsResponse::redirect('listmodules.php');
	}
	else
	{
		$themeObject->add_error(CmsModuleOperations::get_last_error());
	}
}
else if ($action == 'show_post_uninstall' && $module != '')
{
	if (cmsms()->modules[$module]['object']->uninstall_post_message() !== FALSE)
	{
		$themeObject->add_message(cmsms()->modules[$module]['object']->uninstall_post_message());
	}
}
else if ($action == 'upgrade' && $module != '')
{
	$result = CmsModuleOperations::upgrade_module($module);
	if ($result)
	{
		CmsResponse::redirect('listmodules.php');
	}
	else
	{
		$themeObject->add_error(CmsModuleOperations::get_last_error());
	}
}
else if ($action == 'deactivate' && $module != '')
{
	CmsModuleOperations::deactivate_module($module);
	CmsResponse::redirect('listmodules.php');
}
else if ($action == 'activate' && $module != '')
{
	CmsModuleOperations::activate_module($module);
	CmsResponse::redirect('listmodules.php');
}
else if ($action == 'show_about' && $module != '')
{
	echo cmsms()->modules[$module]['object']->get_about();
	$show_list = false;
}
else if ($action == 'show_help' && $module != '')
{
	echo cmsms()->modules[$module]['object']->get_help_page();
	$show_list = false;
}

$image_true = $themeObject->display_image('icons/system/true.gif', lang('true'), '', '', 'systemicon');
$image_false = $themeObject->display_image('icons/system/false.gif', lang('false'), '', '', 'systemicon');

$module_list = array();
foreach (CmsModuleOperations::get_all_modules() as $k => $v)
{
	$module = array();
	$module['name'] = $k;

	if ($v['object']->get_help() != '')
	{
		$module['name'] = '<a href="listmodules.php?action=showmodulehelp&amp;module=' . $k . '">' . $k . '</a>';
	}
	$module['version'] = $v['object']->get_version();
	$module['object'] = $v['object'];
	$module['status'] = $v['installed'] ? lang('installed') : lang('notinstalled');
	$module['use_span'] = false;

	$module['active'] = '<a href="listmodules.php?action=deactivate&amp;module=' . $k . '">' . $image_true . '</a>';
	if (!$v['active'])
	{
		$module['active'] = '<a href="listmodules.php?action=activate&amp;module=' . $k . '">' . $image_false . '</a>';
	}

	$module['action'] = '&nbsp;';
	if (!$module['object']->meets_minimum_requirements()) //Show the core isn't the right version message
	{
		$module['status'] = '<span class="important">' . lang('minimumversionrequired') . ': ' . $module['object']->minimum_core_version() . '</span>';
		$module['use_span'] = true;
	}
	else if (!CmsModuleOperations::is_installed($k)) //We're not installed
	{
		if ($module['object']->is_installable()) //All deps, show install button
		{
			$module['action'] = '<a href="listmodules.php?action=install&amp;module=' . $k . '">' . lang('install') . '</a>';
		}
		else //Broken deps
		{
			$module['action'] = '<a href="listmodules.php?action=missingdeps&amp;module=' . $k . '">' . lang('missingdependency') . '</a>';
		}
	}
	else //We're installed
	{
		//Display any deps
		$deps = CmsModuleOperations::get_installed_dependency_names($k);
		if (count($deps) > 4)
			$module['status'] .= '<br />' . lang('hasdependents') . ' (' . implode(', ', $deps) . ')';
		else if (count($deps) > 0)
			$module['status'] .= '<br />' . lang('hasdependents') . ' (' . implode(', ', $deps) . ')';

		if ($module['object']->needs_upgrade()) //Show the upgrade button
		{
			$module['status'] = '<span class="important">' . lang('needupgrade') . '</span>';
			$module['action'] = '<a href="listmodules.php?action=upgrade&amp;module=' . $k . '">' . lang('upgrade') . '</a>';
			$module['active'] = $image_false;
			$module['version'] = CmsModuleOperations::get_installed_version($k); //Show the version # CMSMS thinks is installed
		}
		else if ($module['object']->is_uninstallable()) //We can uninstall, show the button
		{
			$module['action'] = '<a href="listmodules.php?action=uninstall&amp;module=' . $k . '" onclick="return confirm(\'' . ($v['object']->uninstall_pre_message() !== FALSE ?  cms_utf8entities($v['object']->uninstall_pre_message()) : lang('uninstallconfirm') . ' ' . $k) . '\');">' . lang('uninstall') . '</a>';
		}
		else //Can't break deps, so can't uninstall -- don't display anything?
		{
		}
	}

	$module['helplink'] = '&nbsp;';
	if ($v['object']->get_help() != '')
	{
		$module['helplink'] = '<a href="listmodules.php?action=show_help&amp;module=' . $k . '">' . lang('help') . '</a>';
	}
	$module['aboutlink'] = '<a href="listmodules.php?action=show_about&amp;module=' . $k . '">' . lang('about') . '</a>';
	$module_list[] = $module;
}

if ($show_list)
{
	$smarty->assign_by_ref('modules', $module_list);
	$smarty->display('listmodules.tpl');
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
