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

	$newsid = (isset($_GET[$id."news_id"])?$_GET[$id."news_id"]:"").(isset($_POST[$id."news_id"])?$_POST[$id."news_id"]:"");
	$hiddenfields .= "<input type=\"hidden\" name=\"".$id."action\" value=\"completeedit\" /><input type=\"hidden\" name=\"".$id."news_id\" value=\"".$newsid."\" />";
	$query = "SELECT * FROM ".cms_db_prefix()."module_news WHERE news_id = $newsid";
	$dbresult = $db->Execute($query);
	if ($dbresult && $dbresult->RowCount() > 0) {
		$row = $dbresult->FetchRow();
		$title = $row["news_title"];
		$data = $row["news_data"];
	}

} else if ($moduleaction == "delete") {

	$query = "DELETE FROM ".cms_db_prefix()."module_news WHERE news_id = " . (isset($_GET[$id."news_id"])?$_GET[$id."news_id"]:"").(isset($_POST[$id."news_id"])?$_POST[$id."news_id"]:"");
	$dbresult = $db->Execute($query);
	cms_mapi_audit($cms, (isset($_GET[$id."news_id"])?$_GET[$id."news_id"]:"").(isset($_POST[$id."news_id"])?$_POST[$id."news_id"]:""), "News", "Deleted News Item");
	redirect("moduleinterface.php?module=News");

} else if ($moduleaction == "completeadd") {

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
		$new_id = $db->GenID(cms_db_prefix()."module_news_seq");
		$query = "INSERT INTO ".cms_db_prefix()."module_news (news_id, news_title, news_data, news_date, create_date) VALUES ($new_id, ".$db->qstr($title).", ".$db->qstr($data).",".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
		$dbresult = $db->Execute($query);
		cms_mapi_audit($cms, $new_id, "News", "Added News Item");
		redirect("moduleinterface.php?module=News");
		return;
	}

} else if ($moduleaction == "completeedit") {

	$newsid = (isset($_GET[$id."news_id"])?$_GET[$id."news_id"]:"").(isset($_POST[$id."news_id"])?$_POST[$id."news_id"]:"");
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
		$query = "UPDATE ".cms_db_prefix()."module_news SET news_title = ".$db->qstr($title).", news_data = ".$db->qstr($data).", modified_date = ".$db->DBTimeStamp(time())." WHERE news_id = $newsid";
		$dbresult = $db->Execute($query);
		cms_mapi_audit($cms, $newsid, "News", "Edited News Item");
		redirect("moduleinterface.php?module=News");
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

<?php echo cms_mapi_create_admin_form_start("News", $id)?>

<table width="100%" border="0">
	<tr>
		<td width="60">Title:</td>
		<td><input name="<?php echo $id?>newstitle" maxlength="255" value="<?php echo $title?>" class="standard"/></td>
	</tr>
	<tr>
		<td>Content:</td>
		<td><textarea id="<?php echo $id?>newscontent" name="<?php echo $id?>newscontent" cols="80" rows="12"><?php echo $data?></textarea></td>
	</tr>
	<tr>
		<td><?php echo $hiddenfields?>&nbsp;</td>
		<td>
			<input type="submit" name="<?php echo $id?>submit" value="Submit" />
			<input type="submit" name="<?php echo $id?>cancelsubmit" value="Cancel" />
		</td>
	</tr>
</table>

<?php echo cms_mapi_create_admin_form_end()?>

</div>

<?php

# vim:ts=4 sw=4 noet
?>
