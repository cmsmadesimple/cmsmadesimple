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

$error = "";

$userid = get_userid();

if (isset($_POST["cancel"])) {
	redirect("index.php");
	return;
}

require_once("header.php");

?>

<form method="post" action="editconfig.php">

<div class="adminform">

<h3><?=$gettext->gettext("Edit Configuration")?></h3>

<table border="0">

	<tr>
		<td colspan="2" class="configsection">Database Configuration</td>
	</tr>
	<tr>
		<td>Database Type:</td>
		<td>
			<select name="db_type">
				<option value="mysql">MySQL</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Hostname:</td>
		<td><input type="text" name="db_hostname" value="<?=$config["db_hostname"]?>"></td>
	</tr>
	<!--
	<tr>
		<td>Port:</td>
		<td><input type="text" name="db_port" value="<?=$config["db_hostname"]?>"></td>
	</tr>
	-->
	<tr>
		<td>Username:</td>
		<td><input type="text" name="db_username" value="<?=$config["db_username"]?>"></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type="text" name="db_password" value="<?=$config["db_password"]?>"></td>
	</tr>
	<tr>
		<td>Database:</td>
		<td><input type="text" name="db_database" value="<?=$config["db_name"]?>"></td>
	</tr>
	<tr>
		<td>Database Prefix:</td>
		<td><input type="text" name="db_prefix" value="<?=$config["db_prefix"]?>"></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>

</table>

</div>

</form>

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
