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

include_once("header.php");

$userid = get_userid();

$query = "SELECT * from ".$config->db_prefix."adminlog ORDER BY timestamp DESC limit 30";
$result = $dbnew->Execute($query);

if ($result) {

?>

<h3><?=$gettext->gettext("Admin Log")?></h3>

<table cellspacing="0" class="admintable">
	<tr>
		<td><?=$gettext->gettext("User")?></td>
		<td><?=$gettext->gettext("Item ID")?></td>
		<td><?=$gettext->gettext("Item Name")?></td>
		<td><?=$gettext->gettext("Action")?></td>
		<td><?=$gettext->gettext("Date")?></td>
	</tr>

<?php

	$currow = "row1";

	while ($row = $result->FetchRow()) {

		echo "<tr class=\"$currow\">\n";
		echo "<td>".$row["username"]."</td>\n";
		echo "<td>".($row["item_id"]!=-1?$row["item_id"]:"&nbsp;")."</td>\n";
		echo "<td>".$row["item_name"]."</td>\n";
		echo "<td>".$row["action"]."</td>\n";
		echo "<td>".date("D M j G:i:s T Y", $row["timestamp"])."</td>\n";
		echo "</tr>\n";

		($currow == "row1"?$currow="row2":$currow="row1");

	}
}

?>

</table>

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
