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

$error = "";
$hiddenfields = "";

$db = $cms->db;

$title = "";
if (isset($_POST[$id."newstitle"])) $title = $_POST[$id."newstitle"];
$data = "";
if (isset($_POST[$id."newscontent"])) $data = $_POST[$id."newscontent"];

if (isset($_POST[$id."cancelsubmit"])) {
	redirect("moduleinterface.php?module=News");
	return;
}

if ($moduleaction == "edit") {

	$newsid = $_GET[$id."news_id"].$_POST[$id."news_id"];
	$hiddenfields .= "<input type=\"hidden\" name=\"".$id."action\" value=\"completeedit\" /><input type=\"hidden\" name=\"".$id."news_id\" value=\"".$newsid."\" />";
	$query = "SELECT * FROM ".$cms->config->db_prefix."module_news WHERE news_id = $newsid";
	$dbresult = $db->Execute($query);
	if ($dbresult && $dbresult->RowCount() > 0) {
		$row = $dbresult->FetchRow();
		$title = $row["news_title"];
		$data = $row["news_data"];
	}

} else if ($moduleaction == "delete") {

	$query = "DELETE FROM ".$cms->config->db_prefix."module_news WHERE news_id = " . $_GET[$id."news_id"].$_POST[$id."news_id"];
	$dbresult = $db->Execute($query);
	redirect("moduleinterface.php?module=News");

} else if ($moduleaction == "completeadd") {

	echo "completeadd";
	$hiddenfields .= '<input type="hidden" name="'.$id.'action" value="completeadd" />';
	$validinfo = true;

	if ($title === "") {
		$error .= "<li>No title given</li>";
		$validinfo = false;
	}
	if ($data === "") {
		$error .= "<li>No content given</li>";
		$validinfo = false;
	}

	if ($validinfo) {
		$db = $cms->db;
		$new_id = $db->GenID($cms->config->db_prefix."module_news_seq");
		$query = "INSERT INTO ".$cms->config->db_prefix."module_news (news_id, news_title, news_data, news_date, create_date) VALUES ($new_id, ".$db->qstr($title).", ".$db->qstr($data).",".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
		$dbresult = $db->Execute($query);
		redirect("moduleinterface?module=News");
		return;
	}

} else if ($moduleaction == "completeedit") {

	$newsid = $_GET[$id."news_id"].$_POST[$id."news_id"];
	$hiddenfields .= "<input type=\"hidden\" name=\"".$id."action\" value=\"completeedit\" /><input type=\"hidden\" name=\"".$id."news_id\" value=\"".$newsid."\" />";

	$validinfo = true;

	if ($title === "") {
		$error .= "<li>No title given</li>";
		$validinfo = false;
	}
	if ($data === "") {
		$error .= "<li>No content given</li>";
		$validinfo = false;
	}
	if ($validinfo) {
		$db = $cms->db;
		$query = "UPDATE ".$cms->config->db_prefix."module_news SET news_title = ".$db->qstr($title).", news_data = ".$db->qstr($title).", modified_date = ".$db->DBTimeStamp(time())." WHERE news_id = $newsid";
		$dbresult = $db->Execute($query);
		redirect("moduleinterface?module=News");
		return;
	}
} else {
	$hiddenfields .= '<input type="hidden" name="'.$id.'action" value="completeadd" />';
}

if ($error != "") {
	echo "<ul class=\"error\">".$error."</ul>";
}

?>

<div class="adminform">

<?=create_module_admin_start_form("News", $id)?>

<table>
	<tr>
		<td>Title:</td>
		<td><input name="<?=$id?>newstitle" maxlength="255" value="<?=$title?>" /></td>
	</tr>
	<tr>
		<td>Content:</td>
		<td><textarea name="<?=$id?>newscontent" cols="45" rows="8"><?=$data?></textarea></td>
	</tr>
	<tr>
		<td><?=$hiddenfields?>&nbsp;</td>
		<td>
			<input type="submit" name="<?=$id?>submit" value="Submit" />
			<input type="submit" name="<?=$id?>cancelsubmit" value="Cancel" />
		</td>
	</tr>
</table>

<?=create_module_admin_end_form()?>

</div>

<?php

# vim:ts=4 sw=4 noet
?>
