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

require_once("../include.php");

check_login();

$group_id="";
if (isset($_POST["group_id"])) $group_id = $_POST["group_id"];
else if (isset($_GET["group_id"])) $group_id = $_GET["group_id"];

$group_name="";

if (isset($_POST["cancel"])) {
	redirect("topusers.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Group Assignments');

$message = '';

if ($access)
{
	if ($group_id != '' && $group_id != '-1')
	{
		$query = "SELECT group_name FROM ".cms_db_prefix()."groups WHERE group_id = ".$group_id;
		$result = $db->Execute($query);

		if ($result && $result->RowCount() > 0) {
			$row = $result->FetchRow();
			$group_name = $row['group_name'];
		}

		if (isset($_POST["changeassign"])) {

			$query = "DELETE FROM ".cms_db_prefix()."user_groups WHERE group_id = " . $group_id;
			$result = $db->Execute($query);

			foreach ($_POST as $key=>$value) {
				if (strpos($key,"user-") == 0 && strpos($key,"user-") !== false) {
					$query = "INSERT INTO ".cms_db_prefix()."user_groups (group_id, user_id, create_date, modified_date) VALUES (".$db->qstr($group_id).", ".$db->qstr(substr($key,5)).", '".$db->DBTimeStamp(time())."', '".$db->DBTimeStamp(time())."')";
					$result = $db->Execute($query);
				}
			}

			audit($group_id, $group_name, 'Changed Group Assignments');
			$message = lang('assignmentschanged');
			#redirect("listgroups.php");
			#return;
		}
	}
}

include_once("header.php");

if (!$access) {
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto',array(lang('modifygrouppermissions')))."</p></div>";
}
else {

?>

<div class="pagecontainer">
	<p class="pageheader"><?php echo lang('usersassignedtogroup',array($group_name))?></p>
<?php

	$groups = GroupOperations::LoadGroups();
	if (count($groups) > 0)
	{
		echo '<form id="groupname" method="post" action="changegroupassign.php">';
		echo '<div class="pageoverflow">';
		echo '<p class="pagetext">Group Name:</p>';		
		echo '<p class="pageinput">';
		echo '<select name="group_id">';	
		echo '<option value="-1">Select a Group</option>';
		foreach ($groups as $onegroup)
		{
			echo '<option value="'.$onegroup->id.'"';
			if ($onegroup->id == $group_id)
			{
				echo ' selected="selected"';
			}
			echo '>'.$onegroup->name.'</option>';
		}
		echo '</select> <input type="submit" value="'.lang('selectgroup').'" />';
		echo '</p>';
		echo '</div>';
		echo '</form>';
		echo '<form method="post" action="changegroupassign.php">';
	}

	if ($group_id != '' && $group_id != '-1')
	{
	$query = "SELECT * FROM ".cms_db_prefix()."users ORDER BY username";
	$result = $db->Execute($query);

	if ($result && $result->RowCount()) {

		while($row = $result->FetchRow()) {

			$users[$row['username']] = false;
			$ids[$row['username']] = $row['user_id'];
		}

	}

	$query = "SELECT u.user_id, u.username FROM ".cms_db_prefix()."user_groups ug INNER JOIN ".cms_db_prefix()."users u ON u.user_id = ug.user_id WHERE group_id = " . $group_id;
	$result = $db->Execute($query);

	if ($result && $result->RowCount()) {

		while($row = $result->FetchRow()) {

			$users[$row['username']] = true; 
			$ids[$row['username']] = $row['user_id'];
		}

	}

	echo "<table cellspacing=\"0\" class=\"pagetable\">\n";
	echo '<thead>';
	echo "<tr>\n";
	echo "<th>".lang('assignments')."</th>\n";
	echo "<th class=\"pagew10\">&nbsp;</th>\n";
	echo "</tr>\n";
	echo '</thead>';
	echo '<tbody>';

	$currow = "row1";
	
	foreach ($users as $key => $value)
	{
		echo "<tr class=\"".$currow."\" onmouseover=\"this.className='".$currow.'hover'."';\" onmouseout=\"this.className='".$currow."';\">\n";
		echo '<td>'.$key.'</td>'."\n";
		echo '<td><input class="pagecheckbox" type="checkbox" name="user-'.$ids[$key].'" value="1" '.($value == true?" checked=\"checked\"":"").'/></td>'."\n";
		echo "</tr>\n";

		($currow=="row1"?$currow="row2":$currow="row1");	
	}
	echo '</tbody>';
	echo '</table>';


?>
		<div class="pageoptions">
			<p class="pageoptions">
				<input type="hidden" name="group_id" value="<?php echo $group_id?>" />
				<input type="submit" name="changeassign" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			</p>
		</div>
	</form>
<?php
	}
	echo '</div>';	
}
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
