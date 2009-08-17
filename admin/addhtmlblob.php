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
//require_once("../lib/classes/class.htmlblob.inc.php");
require_once("../lib/classes/class.template.inc.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

$error = "";

$htmlblob = "";
if (isset($_POST['htmlblob'])) $htmlblob = $_POST['htmlblob'];

$content = "";
if (isset($_POST['content'])) $content = $_POST['content'];

if (isset($_POST["cancel"])) {
	redirect("listhtmlblobs.php".$urlext);
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Add Global Content Blocks');

/*
$use_javasyntax = false;
if (get_preference($userid, 'use_wysiwyg') == "1") {
	$htmlarea_flag = true;
    $use_javasyntax = false;
}else if (get_preference($userid, 'use_javasyntax') == "1"){
    $use_javasyntax = true;
}
*/
$gcb_wysiwyg = (get_site_preference('nogcbwysiwyg','0') == '0') ? 1 : 0;
if( $gcb_wysiwyg )
  {
    $gcb_wysiwyg = get_preference($userid, 'gcb_wysiwyg', 1);
  }


if ($access) {
	if (isset($_POST["addhtmlblob"])) {
		
		global $gCms;
		$gcbops =& $gCms->GetGlobalContentOperations();
		$templateops =& $gCms->GetTemplateOperations();

		$validinfo = true;
		if ($htmlblob == ""){
			$error .= "<li>".lang('nofieldgiven', array('addhtmlblob'))."</li>";
			$validinfo = false;
		}
		else if ($gcbops->CheckExistingHtmlBlobName($htmlblob)){
			$error .= "<li>".lang('blobexists')."</li>";
			$validinfo = false;
		}

		if ($validinfo) {
			global $gCms;
			$gcbops =& $gCms->GetGlobalContentOperations();

			$blobobj = new GlobalContent();
			$blobobj->name = $htmlblob;
			$blobobj->content = $content;
			$blobobj->owner = $userid;

			Events::SendEvent('Core', 'AddGlobalContentPre', array('global_content' => &$blobobj));

			$result = $blobobj->save();

			if ($result) {
				if (isset($_POST["additional_editors"])) {
					foreach ($_POST["additional_editors"] as $addt_user_id) {
						$blobobj->AddAuthor($addt_user_id);
					}
				}
				audit($blobobj->id, $blobobj->name, 'Added Html Blob');

				Events::SendEvent('Core', 'AddGlobalContentPost', array('global_content' => &$blobobj));

				redirect("listhtmlblobs.php".$urlext);
				return;
			}
			else {
				$error .= "<li>".lang('errorinsertingblob')."</li>";
			}
		}
	}
}

include_once("header.php");

global $gCms; $db =& $gCms->GetDb();

// fill out additional users array
$addt_users = "";
$groupops =& $gCms->GetGroupOperations();
$groups = $groupops->LoadGroups();
foreach( $groups as $onegroup )
{
  if( $onegroup->id == 1 ) continue;
  $addt_users .= "<option value=\"".($onegroup->id*-1)."\">".lang('group').":&nbsp;{$onegroup->name}</option>";
}
$query = "SELECT user_id, username FROM ".cms_db_prefix()."users WHERE user_id <> ? ORDER BY username";
$result = $db->Execute($query, array($userid));
if ($result && $result->RecordCount() > 0) {
  while($row = $result->FetchRow()) {
    if( $row['user_id'] == 1 ) continue;
		$addt_users .= "<option value=\"".$row["user_id"]."\">".$row["username"]."</option>";
	}
}else{
	$addt_users .= "<option>&nbsp;</option>";
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
	<?php echo $themeObject->ShowHeader('addhtmlblob'); ?>
	<form method="post" action="addhtmlblob.php">
          <div>
          <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" />
        </div>
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('name')?>:</p>
			<p class="pageinput"><input type="text" name="htmlblob" maxlength="255" value="<?php echo $htmlblob?>" class="standard" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('content')?>:</p>
			<p class="pageinput"><?php echo create_textarea($gcb_wysiwyg, $content, 'content', 'wysiwyg', 'content'); ?></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('additionaleditors')?>:</p>
			<p class="pageinput"><select name="additional_editors[]" multiple="multiple" size="6"><?php echo $addt_users?></select></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="addhtmlblob" value="true" />
				<input type="submit" accesskey="s" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<input type="submit" accesskey="c" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			</p>
		</div>
	</form>
</div>

<?php
}

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
