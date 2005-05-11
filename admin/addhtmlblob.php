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

			#Perform the addhtmlblob_pre callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->AddHtmlBlobPre($blobobj);
				}
			}

			$result = $blobobj->save();

			if ($result) {
				if (isset($_POST["additional_editors"])) {
					foreach ($_POST["additional_editors"] as $addt_user_id) {
						$blobobj->AddAuthor($addt_user_id);
					}
				}
				audit($blobobj->id, $blobobj->name, 'Added Html Blob');
				TemplateOperations::TouchAllTemplates(); #So pages recompile

				#Perform the addhtmlblob_post callback
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->AddHtmlBlobPost($blobobj);
					}
				}

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

$query = "SELECT user_id, username FROM ".cms_db_prefix()."users WHERE user_id <> " . $userid . " ORDER BY username";
$result = $db->Execute($query);

if ($result && $result->RowCount() > 0) {
	while($row = $result->FetchRow()) {
		$addt_users .= "<option value=\"".$row["user_id"]."\">".$row["username"]."</option>";
	}
}else{
	$addt_users = "<option>&nbsp;</option>";
}

if (!$access)
{
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto', array(lang('addhtmlblob')))."</p></div>";
}
else
{
	if ($error != "") {
			echo "<div class=\"pageerrorcontainer\"><ul class=\"pageerror\">".$error."</ul></div>";
	}
?>

<div class="pagecontainer">
	<p class="pageheader"><?php echo lang('addhtmlblob')?></p>
	<form method="post" action="addhtmlblob.php">
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('name')?>:</p>
			<p class="pageinput"><input type="text" name="htmlblob" maxlength="255" value="<?php echo $htmlblob?>" class="standard" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('content')?>:</p>
			<p class="pageinputwysiwyg"><?php echo create_textarea(true, $content, 'foo', 'bar'); ?></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('additionaleditors')?>:</p>
			<p class="pageinput"><select name="additional_editors[]" multiple="multiple" size="3"><?php echo $addt_users?></select></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="addhtmlblob" value="true" />
				<input type="submit" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			</p>
		</div>
	</form>
</div>

<?php
}

echo '<p class="pageback"><a class="pageback" href="listhtmlblobs.php">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
