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

check_login($config);

$dodelete = true;

$section_id = -1;
if (isset($_GET["section_id"])) {

	$section_id = $_GET["section_id"];
	$seciton_name = "";
	$order = 1;
	$userid = get_userid();
	$access = check_permission($config, $userid, 'Remove Section');

	if ($access) {

		$query = "SELECT section_name, item_order, parent_id FROM ".$config->db_prefix."sections WHERE section_id = ".$section_id;
		$result = $dbnew->Execute($query);

		if ($result && $result->RowCount()) {
			$row = $result->FetchRow();
			$section_name = $row[section_name];
			$order = $row[item_order];
			$parent_id = $row[parent_id];
		}

		$query = "SELECT count(*) AS count FROM ".$config->db_prefix."pages WHERE section_id = $section_id";
		$result = $dbnew->Execute($query);
		$row = $result->FetchRow();
		if (isset($row["count"]) && $row["count"] > 0) {
			$dodelete = false;
		}
		
		# if there are subsection, we cannot delete
		$query = "SELECT count(*) AS count FROM ".$config->db_prefix."sections WHERE parent_id = $section_id";
		$result = $dbnew->Execute($query);
		$row = $result->FetchRow();
		if (isset($row["count"]) && $row["count"] > 0) {
			$dodelete = false;
		}
		
		if ($dodelete) {
			$query = "DELETE FROM ".$config->db_prefix."sections where section_id = $section_id";
			$result = $dbnew->Execute($query);

			#Fix the item_order if necessary
			$query = "UPDATE ".$config->db_prefix."sections SET item_order = item_order - 1 WHERE item_order > $order AND parent_id = $parent_id";
			$result = $dbnew->Execute($query);

			#This is so pages will not cache the menu changes
			$query = "UPDATE ".$config->db_prefix."templates SET modified_date = now()";
			$dbnew->Execute($query);
			audit($config, $_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $section_id, $section_name, 'Deleted Section');
		}
	}
}

if ($dodelete == true) {
	redirect("listsections.php");
}
else {
	redirect("listsections.php?message=".$gettext->gettext("Section still being used by content pages or subsections.  Please remove those first."));
}

# vim:ts=4 sw=4 noet
?>
