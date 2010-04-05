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

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'cmsms.api.php');

check_login();
$smarty = cms_smarty();
include_once("header.php");

if (isset($_REQUEST['make_default']))
{
	$template = cms_orm('CmsModuleTemplate')->find_by_id(intval($_REQUEST['make_default']));
	if ($template != null)
	{
		$template->default = true;
		$template->save();
	}
}
else if (isset($_REQUEST['delete_template']))
{
	$template = cms_orm('CmsModuleTemplate')->find_by_id(intval($_REQUEST['delete_template']));
	if ($template != null)
	{
		$template->delete();
	}
}

$smarty->assign('templates', CmsModuleTemplate::get_all_templates());
$smarty->assign('back_url', $themeObject->BackUrl());
$smarty->display('listmodtemplates.tpl');

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>