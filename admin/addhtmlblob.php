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
require_once("../lib/classes/class.htmlblob.inc.php");
require_once("../lib/classes/class.template.inc.php");

check_login();

$error = "";

$htmlblob = "";
if (isset($_POST['htmlblob'])) $htmlblob = $_POST['htmlblob'];

$content = "";
if (isset($_POST['content'])) $content = $_POST['content'];

if (isset($_POST["cancel"])) {
	redirect("listhtmlblobs.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Add Html Blobs');

$use_javasyntax = false;
if (get_preference($userid, 'use_wysiwyg') == "1") {
	$htmlarea_flag = true;
    $use_javasyntax = false;
}else if (get_preference($userid, 'use_javasyntax') == "1"){
    $use_javasyntax = true;
}

if ($access) {
	if (isset($_POST["addhtmlblob"])) {

		$validinfo = true;
		if ($htmlblob == ""){
			$error .= "<li>".lang('nofieldgiven', array('addhtmlblob'))."</li>";
			$validinfo = false;
		}
		else if (HtmlBlobOperations::CheckExistingHtmlBlobName($htmlblob)){
			$error .= "<li>".lang('blobexists')."</li>";
			$validinfo = false;
		}

		if ($validinfo) {
			$blobobj = new HtmlBlob();
			$blobobj->name = $htmlblob;
			$blobobj->content = $content;
			$blobobj->owner = $userid;
			$result = $blobobj->save();

			if ($result) {
				if (isset($_POST["additional_editors"])) {
					foreach ($_POST["additional_editors"] as $addt_user_id) {
						$blobobj->AddAuthor($addt_user_id);
					}
				}
				audit($blobobj->id, $blobobj->name, 'Added Html Blob');
				TemplateOperations::TouchAllTemplates(); #So pages recompile
				redirect("listhtmlblobs.php");
				return;
			}
			else {
				$error .= "<li>".lang('errorinsertingblob')."</li>";
			}
		}
	}
}

include_once("header.php");

$addt_users = "";

$query = "SELECT user_id, username FROM ".cms_db_prefix()."users WHERE user_id <> " . $userid;
$result = $db->Execute($query);

if ($result && $result->RowCount() > 0) {
	while($row = $result->FetchRow()) {
		$addt_users .= "<option value=\"".$row["user_id"]."\">".$row["username"]."</option>";
	}
}else{
	$addt_users = "<option>&nbsp;</option>";
}

if (!$access) {
	print "<h3>".lang('noaccessto', array(lang('addhtmlblob')))."</h3>";
} else {
	if ($error != "")
		echo "<ul class=\"error\">".$error."</ul>";
?>

<form method="post" action="addhtmlblob.php" <?php if(isset($use_javasyntax) && $use_javasyntax){echo 'onSubmit="textarea_submit(this, \'content\');"';}?>>

<div class="adminform">

<h3><?php echo lang('addhtmlblob')?></h3>

<table border="0">

	<tr>
		<td>*<?php echo lang('name')?>:</td>
		<td><input type="text" name="htmlblob" maxlength="255" value="<?php echo $htmlblob?>" class="standard"></td>
	</tr>
	<tr>
		<td>*<?php echo lang('content')?>:</td>
		<td><?php echo textarea_highlight((isset($use_javasyntax)?$use_javasyntax:false), $content, "content", "syntaxHighlight", "HTML (Complex)", "content") ?></td>
	</tr>
	<tr>
		<td><?php echo lang('additionaleditors')?>:</td>
		<td><select name="additional_editors[]" multiple size="3"><?php echo $addt_users?></select></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" name="addhtmlblob" value="true">
			<input type="submit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
			<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
		</td>
	</tr>

</table>

</div>

</form>

<?php
}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
