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

include_once("header.php");

if (isset($_GET["message"])) {
	echo "<p class=\"error\">".$_GET["message"]."</p>";
}

?>
<h3><?=$gettext->gettext("Current Templates")?></h3>
<?php

	$userid = get_userid();
	$edit = check_permission($config, $userid, 'Modify Template');
	$remove = check_permission($config, $userid, 'Remove Template');

	$query = "SELECT template_id, template_name, active FROM ".$config->db_prefix."templates ORDER BY template_id";
	$result = $dbnew->Execute($query);

	if ($result) {

		echo '<table cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<td>".$gettext->gettext("Template")."</td>\n";
		echo "<td>".$gettext->gettext("Active")."</td>\n";
		if ($edit)
			echo "<td>&nbsp;</td>\n";
		if ($remove)
			echo "<td>&nbsp;</td>\n";
		echo "</tr>\n";

		$currow = "row1";

		while($row = $result->FetchRow()) {

			echo "<tr class=\"$currow\">\n";
			echo "<td>".$row["template_name"]."</td>\n";
			echo "<td>".($row["active"] == 1?$gettext->gettext("True"):$gettext->gettext("False"))."</td>\n";
			if ($edit)
				echo "<td><a href=\"edittemplate.php?template_id=".$row["template_id"]."\">".$gettext->gettext("Edit")."</a></td>\n";
			if ($remove)
				echo "<td><a href=\"deletetemplate.php?template_id=".$row["template_id"]."\" onclick=\"return confirm('".$gettext->gettext("Are you sure you want to delete?")."');\">".$gettext->gettext("Delete")."</a></td>\n";
			echo "</tr>\n";

			($currow=="row1"?$currow="row2":$currow="row1");

		}

		echo "</table>\n";

	}

if (check_permission($config, $userid, 'Add Template')) {
?>

<div class=button><a href="addtemplate.php"><?=$gettext->gettext("Add New
Template")?></div></p>

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
