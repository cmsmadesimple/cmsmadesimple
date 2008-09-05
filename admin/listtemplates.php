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

require_once("../include.php");

check_login();

include_once("header.php");

$gCms = cmsms();
$db = cms_db();
$smarty = cms_smarty();

$error = "";
$messages = "";

if (isset($_GET["message"]))
{
	$message = preg_replace('/\</','',$_GET['message']);
	$messages .=  '<li>' .$message.'</li>';
}



	$userid	= get_userid();
	$current_user = CmsLogin::get_current_user();
	$add = CmsAcl::check_core_permission('Add', $current_user, 'Template');
	$edit = check_permission($userid, 'Modify Templates');
	$all = check_permission($userid, 'Modify Any Page');
	$remove	= check_permission($userid, 'Remove Templates');
	
	
	$smarty->assign('add', $add);
	$smarty->assign('edit', $edit);
	$smarty->assign('all', $all);
	$smarty->assign('remove', $remove);
	

	
	global $gCms;
	$templateops = $gCms->GetTemplateOperations();

	if ($all && isset($_GET["action"]) && $_GET["action"] == "setallcontent")
	{
		if (isset($_GET["template_id"]))
		{
			$query = "UPDATE ".cms_db_prefix()."content SET template_id = ?";
			$result = $db->Execute($query, array($_GET['template_id']));
			if ($result)
			{
				$query = "UPDATE ".cms_db_prefix()."content SET modified_date = ".$db->DBTimeStamp(time());
				$db->Execute($query);
				
				$messages .=  '<li>' .lang('allpagesmodified').'</li>';
			}
			else
			{
				$error .=  '<li>' .lang('errorupdatingpages').'</li>';
				
			}
		}
	}

	if (isset($_GET['setdefault']))
	{
		$templatelist = cmsms()->template->find_all();
		foreach ($templatelist as $onetemplate)
		{
			if ($onetemplate->id == $_GET['setdefault'])
			{
				$onetemplate->default = 1;
				$onetemplate->active = 1;
				$result = $onetemplate->save();
				if (!$result)
				{
					echo '<ul>';
					foreach ($onetemplate->validation_errors as $err)
					{
						echo '<li>'.$err.'</li>';
					}
					echo '</ul>';
				}
			}
			else
			{
				$onetemplate->default = 0;
				$result = $onetemplate->save();
				if (!$result)
				{
					echo '<ul>';
					foreach ($onetemplate->validation_errors as $err)
					{
						echo '<li>'.$err.'</li>';
					}
					echo '</ul>';
				}
			}
		}
	}

	if (isset($_GET['setactive']) || isset($_GET['setinactive']))
	{
		$theid = '';
		$active = false;
		if (isset($_GET['setactive']))
		{
			$theid = $_GET['setactive'];
			$active = true;
		}
		if (isset($_GET['setinactive']))
		{
			$theid = $_GET['setinactive'];
		}
		$thetemplate = cmsms()->template->find_by_id($theid);
		if ($thetemplate)
		{
			$thetemplate->active = $active;
			$thetemplate->save();
		}
	}

	$templatelist = $templateops->LoadTemplates();
	
	$page = 1;
	if (isset($_GET['page'])) $page = $_GET['page'];
	$limit = 20;
	if (count($templatelist) > $limit)
	{
		$smarty->assign('pagination', pagination($page, count($templatelist), $limit));
	}

$smarty->assign('header_name', $themeObject->ShowHeader('currenttemplates'));
$smarty->assign('limit', $limit);
$smarty->assign('page', $page);
$smarty->assign('templatelist', $templatelist);
$smarty->assign('messages', $messages);
$smarty->assign('error_msg', $error);
$smarty->assign('back_url', $themeObject->BackUrl());
$smarty->display('listtemplates.tpl');

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
