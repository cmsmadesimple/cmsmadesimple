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
$CMS_TOP_MENU=9;

require_once("../include.php");

check_login();

$content_id = -1;
if (isset($_GET["content_id"])) {

	$content_id = $_GET["content_id"];
	$userid = get_userid();
	$access = check_permission($userid, 'Remove Content');

	if ($access)
	{
		$contentobj = ContentManager::LoadContentFromId($content_id);

		if ($contentobj)
		{
			$title = $contentobj->Name();
	
			#Check for children
			if ($contentobj->HasChildren())
			{
				redirect("listcontent.php?message=".lang('errorchildcontent'));
			}
	
			#Check for default
			if ($contentobj->DefaultContent())
			{
				redirect("listcontent.php?message=".lang('errordefaultpage'));
			}
			
			$title = $contentobj->Name();
			$contentobj->Delete();
			ContentManager::SetAllHierarchyPositions();
			audit($content_id, $title, 'Deleted Content');
		}
	}
}

redirect("listcontent.php");

# vim:ts=4 sw=4 noet
?>
