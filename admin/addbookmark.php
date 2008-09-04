<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
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
$smarty = cms_smarty();

$error = "";

$title= "";
if (isset($_POST["title"])) $title = $_POST["title"];
$url = "";
if (isset($_POST["url"])) $url = $_POST["url"];

if (isset($_POST["cancel"]))
{
	redirect("listbookmarks.php");
	return;
}

$userid = get_userid();

if (isset($_POST["addbookmark"]))
{
	$validinfo = true;

	if ($title == "")
	{
		$error .= "<li>". lang('nofieldgiven', array('addbookmark')) . "</li>" ;
		
		$validinfo = false;
	}
	
	if ($url == "")
	{
		$error .=  "<li>". lang('nofieldgiven', array('url')) . "</li>";
		$validinfo = false;
	}
	

	if ($validinfo)
	{
		$markobj = new CmsBookmark();
		$markobj->title = $title;
		$markobj->url = $url;
		$markobj->user_id = $userid;

		$result = $markobj->save();

		if ($result)
		{
			CmsResponse::redirect("listbookmarks.php");
			return;
		}
		else
		{
			$error .=  "<li>" . lang('errorinsertingbookmark'). "</li>";
		}
	}
}
include_once("header.php");

		
		    $smarty->assign('header_name', $themeObject->ShowHeader('addbookmark'));
	        $smarty->assign('title', $title);
			$smarty->assign('url', $url);
			$smarty->assign('error_msg', $error);
			$smarty->assign('back_url', $themeObject->BackUrl());
			

$smarty->display('addbookmark.tpl');


include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
