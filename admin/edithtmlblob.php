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

$oldhtmlblob = "";
if (isset($_POST['oldhtmlblob'])) $oldhtmlblob = $_POST['oldhtmlblob'];

$content = "";
if (isset($_POST['content'])) $content = $_POST['content'];

$owner_id = "";
if (isset($_POST['owner_id'])) $owner_id = $_POST['owner_id'];

$htmlblob_id = -1;
if (isset($_POST["htmlblob_id"])) $htmlblob_id = $_POST["htmlblob_id"];
else if (isset($_GET["htmlblob_id"])) $htmlblob_id = $_GET["htmlblob_id"];

if (isset($_POST["cancel"])) {
	redirect("listhtmlblobs.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Html Blobs');
$adminaccess = check_permission($userid, 'Modify Any Content') || HtmlBlobOperations::CheckOwnership($htmlblob_id, $userid);

$htmlarea_flag = false;
$use_javasyntax = false;

if (get_preference($userid, 'use_wysiwyg') == "1")
{
	$htmlarea_flag = true;
    $use_javasyntax = false;
}
else if (get_preference($userid, 'use_javasyntax') == "1")
{
    $use_javasyntax = true;
}

if ($access)
{
	if (isset($_POST["edithtmlblob"]))
	{
		$validinfo = true;
		if ($htmlblob == "")
		{
			$error .= "<li>".lang('nofieldgiven', array(lang('edithtmlblob')))."</li>";
			$validinfo = false;
		}
		else if ($htmlblob != $oldhtmlblob && HtmlBlobOperations::CheckExistingHtmlBlobName($htmlblob))
		{
			$error .= "<li>".lang('blobexists')."</li>";
			$validinfo = false;
		}

		if ($validinfo)
		{
			$blobobj = new HtmlBlob();
			$blobobj->id = $htmlblob_id;
			$blobobj->name = $htmlblob;
			$blobobj->content = $content;
			$blobobj->owner = $owner_id;

			#Perform the edithtmlblob_pre callback
			foreach($gCms->modules as $key=>$value)
			{
				if (isset($gCms->modules[$key]['edithtmlblob_pre_function']) &&
					$gCms->modules[$key]['Installed'] == true &&
					$gCms->modules[$key]['Active'] == true)
				{
					call_user_func_array($gCms->modules[$key]['edithtmlblob_pre_function'], array(&$gCms, &$blobobj));
				}
			}

			$result = $blobobj->save();

			if ($result)
			{
				$blobobj->ClearAuthors();
				if (isset($_POST["additional_editors"])) {
					foreach ($_POST["additional_editors"] as $addt_user_id) {
						$blobobj->AddAuthor($addt_user_id);
					}
				}
				audit($blobobj->id, $blobobj->name, 'Edited Html Blob');

				#So pages recompile
				TemplateOperations::TouchAllTemplates($blobobj->name);

				#So pages recompile
				$query = "UPDATE ".cms_db_prefix()."pages SET modified_date = ? WHERE page_content like ?";
				$dbresult = $db->Execute($query,array($db->DBTimeStamp(time()),'%{html_blob name="'.$blobobj->name.'"}%'));

				#Perform the edithtmlblob_post callback
				foreach($gCms->modules as $key=>$value)
				{
					if (isset($gCms->modules[$key]['edithtmlblob_post_function']) &&
						$gCms->modules[$key]['Installed'] == true &&
						$gCms->modules[$key]['Active'] == true)
					{
						call_user_func_array($gCms->modules[$key]['edithtmlblob_post_function'], array(&$gCms, &$blobobj));
					}
				}

				redirect("listhtmlblobs.php");
				return;
			}
			else
			{
				$error .= "<li>".lang('errorinsertingblob')."</li>";
			}
		}
	}
	else if ($htmlblob_id != -1)
	{
		$onehtmlblob = HtmlBlobOperations::LoadHtmlBlobByID($htmlblob_id);
		$htmlblob = $onehtmlblob->name;
		$oldhtmlblob = $onehtmlblob->name;
		$owner_id = $onehtmlblob->owner;
		$content = $onehtmlblob->content;
	}
}

include_once("header.php");

$owners = "<select name=\"owner_id\">";

$query = "SELECT user_id, username FROM ".cms_db_prefix()."users ORDER BY username";
$result = $db->Execute($query);

while($row = $result->FetchRow())
{
	$owners .= "<option value=\"".$row["user_id"]."\"";
	if ($row["user_id"] == $owner_id)
	{
		$owners .= " selected=\"selected\"";
	}
	$owners .= ">".$row["username"]."</option>";
}

$owners .= "</select>";

$addt_users = "";

$query = "SELECT user_id, username FROM ".cms_db_prefix()."users WHERE user_id <> " . $userid . " ORDER BY username";
$result = $db->Execute($query);

while($row = $result->FetchRow())
{
	$addt_users .= "<option value=\"".$row["user_id"]."\"";
	$query = "SELECT * from ".cms_db_prefix()."additional_htmlblob_users WHERE user_id = ".$row["user_id"]." AND htmlblob_id = $htmlblob_id";
	$newresult = $db->Execute($query);
	if ($newresult && $newresult->RowCount() > 0)
	{
		$addt_users .= " selected=\"true\"";
	}
	$addt_users .= ">".$row["username"]."</option>";
}

if (!$access)
{
	print "<h3>".lang('noaccessto', array(lang('edithtmlblob')))."</h3>";
}
else
{
	if ($error != "")
	{
		echo "<ul class=\"error\">".$error."</ul>";
	}
?>

<form method="post" action="edithtmlblob.php">

<div class="adminform">

<h3><?php echo lang('edithtmlblob')?></h3>

<table border="0">

	<tr>
		<td>*<?php echo lang('name')?>:</td>
		<td><input type="text" name="htmlblob" maxlength="255" value="<?php echo $htmlblob?>" class="standard" /></td>
	</tr>
	<tr>
		<td>*<?php echo lang('content')?>:</td>
		<td><?php echo create_textarea($content, 'content', 'syntaxHighlight', 'content');?></td>
	</tr>
	<?php if ($adminaccess) { ?>
	<tr>
		<td><?php echo lang('owner')?>:</td>
		<td><?php echo $owners?></td>
	</tr>
	<?php } ?>
	<?php if ($addt_users != '') { ?>
	<tr>
		<td><?php echo lang('additionaleditors')?>:</td>
		<td><select name="additional_editors[]" multiple="multiple" size="3"><?php echo $addt_users?></select></td>
	</tr>
	<?php } ?>
	<tr>
		<td>&nbsp;</td>
		<td><input type="hidden" name="edithtmlblob" value="true" /><input type="hidden" name="oldhtmlblob" value="<?php echo $oldhtmlblob ?>" /><input type="hidden" name="htmlblob_id" value="<?php echo $htmlblob_id?>" />
		<input type="submit" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" />
		<?php if (!$adminaccess) { ?>
			<input type="hidden" name="owner_id" value="<?php echo $owner_id ?>" />
		<?php } ?>
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'" /></td>
	</tr>

</table>

</div>

</form>

<?php
}
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
