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

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'cmsms.api.php');

if (isset($_POST["cancel"]))
{
	redirect("listmodtemplates.php");
}

$gCms = cmsms();
$smarty = cms_smarty();
$smarty->assign('action', 'editmodtemplate.php');

require_once("header.php");

$submit = array_key_exists('submitbutton', $_REQUEST);
$apply = array_key_exists('applybutton', $_REQUEST);

$template_id = coalesce_key($_REQUEST, 'template_id', '-1');
$template_obj = cms_orm('CmsModuleTemplate')->find_by_id($template_id);

if ($template_obj && ($submit || $apply))
{
	$template_obj->update_parameters($_REQUEST['template']);
	if ($template_obj->save())
	{
		redirect("listmodtemplates.php");
	}
}

$smarty->assign('header_name', $themeObject->show_header('editmoduletemplate'));
$smarty->assign_by_ref('template_object', $template_obj);

$smarty->display('addmodtemplate.tpl');

include_once("footer.php");
?>