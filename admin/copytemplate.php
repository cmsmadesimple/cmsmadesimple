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

$template = "";
if (isset($_POST["template"])) $template = $_POST["template"];

$template_id = -1;
if (isset($_POST["template_id"])) $template_id = $_POST["template_id"];
else if (isset($_GET["template_id"])) $template_id = $_GET["template_id"];

if (isset($_POST["cancel"])) {
	redirect("listtemplates.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Template');

if ($access) {

	if (isset($_POST["copytemplate"])) {

		$validinfo = true;
		if ($template == "") {
			$error .= "<li>".lang('nofieldgiven',array(lang('name')))."</li>";
			$validinfo = false;
		} else {
			$query = "SELECT template_id from ".cms_db_prefix()."templates WHERE template_name = " . $db->qstr($template);
			$result = $db->Execute($query);
			if ($result && $result->RowCount() > 0) {
				$error .= "<li>".lang('templateexists')."</li>";
				$validinfo = false;
			}
		}

		if ($validinfo) {

			$query = "SELECT * from ".cms_db_prefix()."templates WHERE template_id = " . $template_id;
			$result = $db->Execute($query);
			
			$row = $result->FetchRow();

			$content = $row["template_content"];
			$stylesheet = $row["stylesheet"];
			$active = $row["active"];

			$new_template_id = $db->GenID(cms_db_prefix()."templates_seq");
			$query = "INSERT INTO ".cms_db_prefix()."templates (template_id, template_name, template_content, stylesheet, active, create_date, modified_date) VALUES ($new_template_id, ".$db->qstr($template).", ".$db->qstr($content).", ".$db->qstr($stylesheet).", $active, ".$db->DBTimeStamp(time()).", ".$db->DBTimeStamp(time()).")";
			$result = $db->Execute($query);

			if ($result) {
				audit($_SESSION["cms_admin_user_id"], $_SESSION["cms_admin_username"], $new_template_id, $template, 'Copied Template');
				redirect("listtemplates.php");
				return;
			}
			else {
				$error .= "<li>".lang('errorcopyingtemplate')."</li>";
			}
		}

	}
}

include_once("header.php");

if (!$access) {
	print "<h3>".lang('noaccessto',array(lang('copytemplate')))."</h3>";
}
else {

	if ($error != "") {
		echo "<ul class=\"error\">".$error."</ul>";
	}

?>

<form method="post" action="copytemplate.php">

<div class="adminformSmall">

<h3><?php echo lang('copytemplate')?></h3>

<table border="0">

	<tr>
		<td><?php echo lang('newtemplatename')?>:</td>
		<td><input type="text" name="template" maxlength="25" value="<?php echo $template?>"></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="hidden" name="template_id" value="<?php echo $template_id?>"><input type="hidden" name="copytemplate" value="true">
		<input type="submit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>

</table>

</div>

</form>

<?php

}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
