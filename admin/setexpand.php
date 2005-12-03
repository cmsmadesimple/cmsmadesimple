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
#$Id$

$CMS_ADMIN_PAGE=1;
$CMS_EXCLUDE_FROM_RECENT=1;

require_once("../include.php");

check_login();
$userid = get_userid();
$openedArray=array();
if (get_preference($userid, 'collapse', '') != '')
	{
	$tmp  = explode('.',get_preference($userid, 'collapse'));
	foreach ($tmp as $thisCol)
		{
		$colind = substr($thisCol,0,strpos($thisCol,'='));
		$openedArray[$colind] = 1;
		}
	}
$error = FALSE;

$content_id = "";
if (isset($_POST["content_id"])) $content_id = $_POST["content_id"];
else if (isset($_GET["content_id"])) $content_id = $_GET["content_id"];

$pagelist_id = "1";
if (isset($_POST["page"])) $pagelist_id = $_POST["page"];
else if (isset($_GET["page"])) $pagelist_id = $_GET["page"];

if (isset($_POST['expandall']) || isset($_GET['expandall']))
	{
	$all = ContentManager::GetAllContent(false);
	$cs = '';
	foreach ($all as $thisitem)
		{
        if ($thisitem->HasChildren())
            {
		    $cs .= $thisitem->Id().'=1.';
		    }
		}	
	set_preference($userid, 'collapse', $cs);
	}
else if (isset($_POST['collapseall']) || isset($_GET['collapseall']))
	{
	set_preference($userid, 'collapse', '');
	}
else
	{
	$collapse = true;
	if (isset($_POST["col"]))
		{
		if ($_POST["col"] == 0)
			{
			$collapse = false;
			}
		}
	else if (isset($_GET["col"]))
		{
		if ($_GET["col"] == 0)
			{
			$collapse = false;
			}
		}
	
	if ($collapse)
		{
		$openedArray[$content_id] = 0;
		}
	else
		{
		$openedArray[$content_id] = 1;
		}
	$cs = '';
	foreach ($openedArray as $key=>$val)
		{
		if ($val == 1)
			{
			$cs .= $key.'=1.';
			}
		}
	set_preference($userid, 'collapse', $cs);
	//ContentManager::SetCollapse($content_id, $collapse);
	}
redirect("listcontent.php?page=".$pagelist_id);
# vim:ts=4 sw=4 noet
?>
