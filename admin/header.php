<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

$current_user = CmsLogin::get_current_user();
if ($current_user->is_anonymous())
{
	$_SESSION["redirect_url"] = $_SERVER["REQUEST_URI"];
	redirect($config["root_url"]."/".$config['admin_dir']."/login.php");
}

CmsAdminTheme::start();

$themeObject = CmsAdminTheme::get_instance();

cmsms()->variables['admintheme'] = CmsAdminTheme::get_instance();

/*
if (!(isset($USE_OUTPUT_BUFFERING) && $USE_OUTPUT_BUFFERING == false))
{
	@ob_start();
}
*/

//$themeObject = CmsAdminTheme::get_theme_for_user(get_userid());

/*
include_once("../lib/classes/class.admintheme.inc.php");

if (isset($USE_THEME) && $USE_THEME == false)
{
	echo '<!-- admin theme disabled -->';
}
else
{
	$themeObject = AdminTheme::get_theme_for_user(get_userid());

	$gCms->variables['admintheme']=&$themeObject;
	if (isset($gCms->config['admin_encoding']) && $gCms->config['admin_encoding'] != '')
	{
		$themeObject->SendHeaders(isset($charsetsent), $gCms->config['admin_encoding']);
	}
	else
	{
		$themeObject->SendHeaders(isset($charsetsent), get_encoding('', false));
	}
	$themeObject->PopulateAdminNavigation(isset($CMS_ADMIN_SUBTITLE)?$CMS_ADMIN_SUBTITLE:'');

	$themeObject->DisplayDocType();
	$themeObject->DisplayHTMLStartTag();
	$themeObject->DisplayHTMLHeader(false, isset($headtext)?$headtext:'');
	$themeObject->DisplayBodyTag();
	$themeObject->DoTopMenu();
	$themeObject->DisplayMainDivStart();
	// we've removed the Recent Pages stuff, but other things could go in this box
	// so I'll leave some of the logic there. We can remove it later if it makes sense. SjG
	$marks = get_preference(get_userid(), 'bookmarks');
	if ($marks)
	{
		$themeObject->StartRighthandColumn();
		if ($marks)
		{
			$themeObject->DoBookmarks();
		}

		$themeObject->EndRighthandColumn();
	}
}
*/
?>
