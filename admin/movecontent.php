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

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

$page_id = -1;
$page = 1;
if (isset($_GET["content_id"])) {

	$content_id = $_GET["content_id"];
	$parent_id = $_GET["parent_id"];
	$direction = $_GET["direction"];
	$page = $_GET['page'];
	$userid = get_userid();
	$access = check_permission($userid, 'Modify Any Content');

	if ($access)
	{
		$order = 1;

		#Grab necessary info for fixing the item_order
		$query = "SELECT item_order FROM ".cms_db_prefix()."content WHERE content_id = $content_id";
		$result = $db->Execute($query);
		$row = $result->FetchRow();
		if (isset($row["item_order"]))
		{
			$order = $row["item_order"];	
		}

		if ($direction == "down")
		{
			$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order - 1 WHERE item_order = " . ($order + 1) .
				" AND parent_id = $parent_id";
			#echo $query;
			$db->Execute($query);
			$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order + 1 WHERE content_id = " . $content_id .
				" AND parent_id = $parent_id";
			#echo $query;
			$db->Execute($query);
		}
		else if ($direction == "up")
		{
			$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order + 1 WHERE item_order = " . ($order - 1) .
				" AND parent_id = $parent_id";
			#echo $query;
			$db->Execute($query);
			$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order - 1 WHERE content_id = " . $content_id .
				" AND parent_id = $parent_id";
			#echo $query;
			$db->Execute($query);
		}
		ContentManager::SetAllHierarchyPositions();
	}
}

redirect("listcontent.php?page=$page");

# vim:ts=4 sw=4 noet
?>
