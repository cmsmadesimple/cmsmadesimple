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

$module = "";
if (isset($_GET["module"])) $module = $_GET["module"];

$action = "";
if (isset($_GET["action"])) $action = $_GET["action"];

$userid = get_userid();
$access = check_permission($config, $userid, $gettext->gettext("Modify Modules"));

if ($access) {
	if ($action == "install") {
		#run install on it (if there is one)
		if (isset($cmsmodules[$module]['install_function'])) {
			call_user_func_array(&$cmsmodules[$module]['install_function'], array($modulecmsobj));
		}

		#now insert a record
		$query = "INSERT INTO ".$config->db_prefix."modules (module_name, version, status, active) VALUES (".$dbnew->qstr($module).",".$dbnew->qstr($cmsmodules[$module]['Version']).",'Installed',1)";
		$dbnew->Execute($query);
	}

	if ($action == "uninstall") {
		#run uninstall on it (if there is one)
		if (isset($cmsmodules[$module]['uninstall_function'])) {
			call_user_func_array(&$cmsmodules[$module]['uninstall_function'], array($modulecmsobj));
		}

		#now delete the record
		$query = "DELETE FROM ".$config->db_prefix."modules WHERE module_name = ".$dbnew->qstr($module);
		$dbnew->Execute($query);
	}

	if ($action == "settrue") {
		$query = "UPDATE ".$config->db_prefix."modules SET active = 1 WHERE module_name = ".$dbnew->qstr($module);
		$dbnew->Execute($query);
	}

	if ($action == "setfalse") {
		$query = "UPDATE ".$config->db_prefix."modules SET active = 0 WHERE module_name = ".$dbnew->qstr($module);
		$dbnew->Execute($query);
	}
}
include_once("header.php");

if ($action != "" && !$access) {
	echo $gettext->gettext("<p class=\"error\">You need the 'Modify Modules' permission to perform that function.</p>");
}

if (count($cmsmodules) > 0) {

	$query = "SELECT * from ".$config->db_prefix."modules";
	$result = $dbnew->Execute($query);
	while ($row = $result->FetchRow()) {
		$dbm[$row['module_name']]['Status'] = $row['status'];
		$dbm[$row['module_name']]['Version'] = $row['version'];
		$dbm[$row['module_name']]['Active'] = $row['active'];
	}

?>

	<div class="content">

	<h3>Module Admin</h3>

	<table cellspacing="0" class="admintable">
		<tr>
			<td>Module Name</td>
			<td width="10%">Version</td>
			<td width="10%">Status</td>
			<td width="10%">Active</td>
			<td width="10%">Action</td>
		</tr>

<?

	$curclass = "row1";

	foreach($cmsmodules as $key=>$value) {

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

?>
