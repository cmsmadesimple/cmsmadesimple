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

require_once("../include.php");

check_login($config);

$page_id = -1;
if (isset($_GET["page_id"])) {

	$page_id = $_GET["page_id"];
	$userid = get_userid();
	$access = check_permission($config, $userid, 'Remove Content');

	if ($access)  {
		$db = new DB($config);

		$order = 1;
		$section_id = 1;
		#Grab necessary info for fixing the item_order
		$query = "SELECT item_order, section_id FROM ".$config->db_prefix."pages WHERE page_id = $page_id";
		$result = $db->query($query);
		$row = $db->getresulthash($result);
		if (isset($row["item_order"])) {
			$order = $row["item_order"];	
		}
		if (isset($row["section_id"])) {
			$section_id = $row["section_id"];	
		}
		$db->freeresult($result);
		#Remove the page
		$query = "DELETE FROM ".$config->db_prefix."pages where page_id = $page_id";
		$result = $db->query($query);
		$query = "DELETE FROM ".$config->db_prefix."additional_users where page_id = $page_id";
		$result = $db->query($query);
		#Fix the item_order if necessary
		$query = "UPDATE ".$config->db_prefix."pages SET item_order = item_order - 1 WHERE section_id = $section_id AND item_order > $order";
		$result = $db->query($query);
		#This is so pages will not cache the menu changes
		$query = "UPDATE ".$config->db_prefix."templates SET modified_date = now()";
		$db->query($query);
		$db->close();
	}
}

redirect("listcontent.php");

# vim:ts=4 sw=4 noet
?>
