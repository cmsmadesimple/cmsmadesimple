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
require_once("../lib/classes/class.group.inc.php");

check_login();

include_once("header.php");

?>
<h3><?php echo lang('currentgroups')?></h3>
<?php

	$userid = get_userid();
	$perm = check_permission($userid, 'Modify Permissions');
	$assign = check_permission($userid, 'Modify Group Assignments');
	$edit = check_permission($userid, 'Modify Group');
	$remove = check_permission($userid, 'Remove Group');

	#$query = "SELECT group_id, group_name, active FROM ".cms_db_prefix()."groups ORDER BY group_id";
	#$result = $db->Execute($query);

	$grouplist = GroupOperations::LoadGroups();

	if (count($grouplist) > 0) {

		echo "<table cellspacing=\"0\" class=\"admintable\">\n";
		echo "<tr>\n";
		echo "<td>".lang('name')."</td>\n";
		echo "<td width=\"7%\" align=\"center\">".lang('active')."</td>\n";
		if ($perm)
			echo "<td width=\"7%\" align=\"center\">&nbsp;</td>\n";
		if ($assign)
			echo "<td width=\"7%\" align=\"center\">&nbsp;</td>\n";
		if ($edit)
			echo "<td width=\"16\">&nbsp;</td>\n";
		if ($remove)
			echo "<td width=\"16\">&nbsp;</td>\n";
		echo "</tr>\n";

		$currow = "row1";

		#while ($row = $result->FetchRow()) {
		foreach ($grouplist as $onegroup)
		{
			echo "<tr class=\"$currow\">\n";
			echo "<td><a href=\"editgroup.php?group_id=".$onegroup->id."\">".$onegroup->name."</a></td>\n";
			echo "<td align=\"center\">".($onegroup->active == 1?lang('true'):lang('false'))."</td>\n";
			if ($perm)
				echo "<td align=\"center\"><a href=\"changegroupperm.php?group_id=".$onegroup->id."\">".lang('permissions')."</a></td>\n";
			if ($assign)
				echo "<td align=\"center\"><a href=\"changegroupassign.php?group_id=".$onegroup->id."\">".lang('assignments')."</a></td>\n";
			if ($edit)
				echo "<td width=\"16\"><a href=\"editgroup.php?group_id=".$onegroup->id."\"><img src=\"../images/cms/edit.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('edit')."\"></a></td>\n";
			if ($remove)
				echo "<td width=\"16\"><a href=\"deletegroup.php?group_id=".$onegroup->id."\" onclick=\"return confirm('".lang('deleteconfirm')."');\"><img src=\"../images/cms/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".lang('delete')."\"></a></td>\n";
			echo "</tr>\n";

			($currow == "row1"?$currow="row2":$currow="row1");
		}

		echo "</table>\n";

	}

if (check_permission($userid, 'Add Group')) {
?>

<div class=button><a href="addgroup.php"><?php echo lang('addgroup')?></a></div><br>

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
