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

$module = "";
if (isset($_GET["module"])) $module = $_GET["module"];

$action = "";
if (isset($_GET["action"])) $action = $_GET["action"];

$userid = get_userid();
$access = check_permission($userid, $gettext->gettext("Modify Modules"));

if ($access) {
	if ($action == "install") {
		#run install on it (if there is one)
		if (isset($gCms->modules[$module]['install_function'])) {
			call_user_func_array($gCms->modules[$module]['install_function'], array($gCms));
		}

		#now insert a record
		$query = "INSERT INTO ".cms_db_prefix()."modules (module_name, version, status, active) VALUES (".$db->qstr($module).",".$db->qstr($gCms->modules[$module]['Version']).",'Installed',1)";
		$db->Execute($query);
		redirect("modules.php");
	}

	if ($action == "uninstall") {
		#run uninstall on it (if there is one)
		if (isset($gCms->modules[$module]['uninstall_function'])) {
			call_user_func_array($gCms->modules[$module]['uninstall_function'], array($gCms));
		}

		#now delete the record
		$query = "DELETE FROM ".cms_db_prefix()."modules WHERE module_name = ".$db->qstr($module);
		$db->Execute($query);
		redirect("modules.php");
	}

	if ($action == "settrue") {
		$query = "UPDATE ".cms_db_prefix()."modules SET active = 1 WHERE module_name = ".$db->qstr($module);
		$db->Execute($query);
		redirect("modules.php");
	}

	if ($action == "setfalse") {
		$query = "UPDATE ".cms_db_prefix()."modules SET active = 0 WHERE module_name = ".$db->qstr($module);
		$db->Execute($query);
		redirect("modules.php");
	}
}
include_once("header.php");

if ($action != "" && !$access) {
	echo "<p class=\"error\">".$gettext->gettext("You need the 'Modify Modules' permission to perform that function.")."</p>";
}

if (count($gCms->modules) > 0) {

	$query = "SELECT * from ".cms_db_prefix()."modules";
	$result = $db->Execute($query);
	while ($row = $result->FetchRow()) {
		$dbm[$row['module_name']]['Status'] = $row['status'];
		$dbm[$row['module_name']]['Version'] = $row['version'];
		$dbm[$row['module_name']]['Active'] = $row['active'];
	}

?>

	<div class="content">

	<h3><?=$gettext->gettext("Module Admin")?></h3>

	<table cellspacing="0" class="admintable">
		<tr>
			<td><?=$gettext->gettext("Module Name")?></td>
			<td width="10%"><?=$gettext->gettext("Version")?></td>
			<td width="10%"><?=$gettext->gettext("Status")?></td>
			<td width="10%"><?=$gettext->gettext("Active")?></td>
			<td width="10%"><?=$gettext->gettext("Action")?></td>
		</tr>

<?

	$curclass = "row1";

	foreach($gCms->modules as $key=>$value) {

		echo "<tr class=\"$curclass\">\n";
		echo "<td>$key</td>\n";
		if (!isset($dbm[$key])) { #Not installed, lets put up the install button
			echo "<td>&nbsp</td>";
			echo "<td>".$gettext->gettext("Not Installed")."</td>";
			echo "<td>&nbsp;</td>";
			echo "<td><a href=\"modules.php?action=install&module=".$key."\">".$gettext->gettext("Install")."</a></td>";
		} else { #Must be installed
			echo "<td>".$dbm[$key]['Version']."</td>";
			echo "<td>".$dbm[$key]['Status']."</td>";
			echo "<td>".($dbm[$key]['Active']==="1"?"<a href='modules.php?action=setfalse&module=".$key."'>".$gettext->gettext("True")."</a>":"<a href='modules.php?action=settrue&module=".$key."'>".$gettext->gettext("False")."</a>")."</td>";
			echo "<td><a href=\"modules.php?action=uninstall&module=".$key."\" onclick=\"return confirm('".$gettext->gettext("Are you sure you want to uninstall this module?")."');\">".$gettext->gettext("Uninstall")."</a></td>";
		}
	
		echo "</tr>\n";

		($curclass=="row1"?$curclass="row2":$curclass="row1");
	}

?>

	</table>

	</div>

<?

}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
