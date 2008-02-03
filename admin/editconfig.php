<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

<h3><?php echo lang('editconfiguration')?></h3>

<table cellpadding="2" border="1" cellspacing="0">
	<tr>
		<td><a class="collapseTitle" href="#config" onclick="expandcontent('databaseconfig')"><?php echo lang('database')?></a></td>
		<td><a class="collapseTitle" href="#config" onclick="expandcontent('urlconfig')"><?php echo lang('pathconfig')?></a></td>
		<td><a class="collapseTitle" href="#config" onclick="expandcontent('saveconfig')"><?php echo lang('saveconfig')?></a></td>
	</tr>
</table>

<p><p>

<div id="databaseconfig" class="expand">

<table border="0">

	<tr>
		<td align="right"><?php echo lang('databasetype')?>:</td>
		<td>
			<select name="dbms">
				<option value="mysql"<?php echo($config['dbms']=='mysql'?' checked':'')?>>MySQL</option>
				<option value="postgres7"<?php echo($config['dbms']=='postgres7'?' checked':'')?>>PostgreSQL 7</option>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right"><?php echo lang('hostname')?>:</td>
		<td><input type="text" name="db_hostname" value="<?php echo $config["db_hostname"]?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo lang('username')?>:</td>
		<td><input type="text" name="db_username" value="<?php echo $config["db_username"]?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo lang('password')?>:</td>
		<td><input type="text" name="db_password" value="<?php echo $config["db_password"]?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo lang('database')?>:</td>
		<td><input type="text" name="db_database" value="<?php echo $config["db_name"]?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo lang('databaseprefix')?>:</td>
		<td><input type="text" name="db_prefix" value="<?php echo $config["db_prefix"]?>"></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>

</table>

</div>

<div id="urlconfig" class="expand">

<table border="0">

	<tr>
		<td align="right"><?php echo lang('rooturl')?>:</td>
		<td><input type="text" name="root_url" value="<?php echo $config["root_url"]?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo lang('rootpath')?>:</td>
		<td><input type="text" name="root_path" value="<?php echo $config["root_path"]?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo lang('queryvar')?>:</td>
		<td><input type="text" name="query_var" value="<?php echo $config["query_var"]?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo lang('previewspath')?>:</td>
		<td><input type="text" name="previews_path" value="<?php echo $config["previews_path"]?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo lang('uploadspath')?>:</td>
		<td><input type="text" name="uploads_path" value="<?php echo $config["uploads_path"]?>"></td>
	</tr>
	<tr>
		<td align="right"><?php echo lang('uploadsurl')?>:</td>
		<td><input type="text" name="uploads_url" value="<?php echo $config["uploads_url"]?>"></td>
	</tr>

</table>

</div>

<div id="saveconfig" class="expand">

<table border="0">

	<tr>
		<td><?php echo lang('saveconfig')?>:</td>
		<td><input type="submit" value="Submit"></td>
	</tr>

</table>

</div>

</div>

</form>

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
