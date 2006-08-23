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

global $gCms;
$gcbops =& $gCms->GetGlobalContentOperations();

$userid = get_userid();
$adminaccess = check_permission($userid, 'Modify Global Content Blocks');
$access = check_permission($userid, 'Modify Global Content Blocks') || $gcbops->CheckOwnership($htmlblob_id, $userid) ||
$gcbops->CheckAuthorship($htmlblob_id, $userid);

/*
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
*/
$gcb_wysiwyg = get_preference($userid, 'gcb_wysiwyg', 1);

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
		else if ($htmlblob != $oldhtmlblob && $gcbops->CheckExistingHtmlBlobName($htmlblob))
		{
			$error .= "<li>".lang('blobexists')."</li>";
			$validinfo = false;
		}

		if ($validinfo)
		{
			$blobobj =& new GlobalContent();
			$blobobj->id = $htmlblob_id;
			$blobobj->name = $htmlblob;
			$blobobj->content = $content;
			$blobobj->owner = $owner_id;

			$blobobj->ClearAuthors();
			if (isset($_POST["additional_editors"])) {
					foreach ($_POST["additional_editors"] as $addt_user_id) {
						$blobobj->AddAuthor($addt_user_id);
					}
			}
			$blobobj->AddAuthor($userid);

			#Perform the edithtmlblob_pre callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->EditHtmlBlobPre($blobobj);
				}
			}
			
			Events::SendEvent('Core', 'EditGlobalContentPre', array('global_content' => &$blobobj));

			$result = $blobobj->save();

			if ($result)
			{
				audit($blobobj->id, $blobobj->name, 'Edited Html Blob');

				#Clear cache
				$smarty = new Smarty_CMS($config);
				$smarty->clear_all_cache();
				$smarty->clear_compiled_tpl();

				#Perform the edithtmlblob_post callback
				foreach($gCms->modules as $key=>$value)
				{
					if ($gCms->modules[$key]['installed'] == true &&
						$gCms->modules[$key]['active'] == true)
					{
						$gCms->modules[$key]['object']->EditHtmlBlobPost($blobobj);
					}
				}
				
				Events::SendEvent('Core', 'EditGlobalContentPost', array('global_content' => &$blobobj));

				if (!isset($_POST['apply'])) {
					redirect('listhtmlblobs.php');
					return;
				}
			}
			else
			{
				$error .= "<li>".lang('errorinsertingblob')."</li>";
			}
		}
	}
	else if ($htmlblob_id != -1)
	{
		$onehtmlblob = $gcbops->LoadHtmlBlobByID($htmlblob_id);
		$htmlblob = $onehtmlblob->name;
		$oldhtmlblob = $onehtmlblob->name;
		$owner_id = $onehtmlblob->owner;
		$content = $onehtmlblob->content;
	}
}

if (strlen($htmlblob) > 0)
    {
    $CMS_ADMIN_SUBTITLE = $htmlblob;
    }

include_once("header.php");

$owners = "<select name=\"owner_id\">";

$query = "SELECT user_id, username FROM ".cms_db_prefix()."users ORDER BY username";
$result = $db->Execute($query);

while($result && $row = $result->FetchRow())
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

$query = "SELECT user_id, username FROM ".cms_db_prefix()."users WHERE user_id <> ? ORDER BY username";
$result = $db->Execute($query,array($userid));

while($result && $row = $result->FetchRow())
{
	$addt_users .= "<option value=\"".$row["user_id"]."\"";
	$query = "SELECT * from ".cms_db_prefix()."additional_htmlblob_users WHERE user_id = ".$row["user_id"]." AND htmlblob_id = ?";
	$newresult = $db->Execute($query,array($htmlblob_id));
	if ($newresult && $newresult->RecordCount() > 0)
	{
		$addt_users .= " selected=\"true\"";
	}
	$addt_users .= ">".$row["username"]."</option>";
}

if (!$access)
{
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto', array(lang('edithtmlblob')))."</p></div>";
}
else
{
	if ($error != "")
	{
		echo "<div class=\"pageerrorcontainer\"><ul class=\"pageerror\">".$error."</ul></div>";
	}
?>


<div class="pagecontainer">
	<?php echo $themeObject->ShowHeader('edithtmlblob'); ?>
	<form method="post" action="edithtmlblob.php">
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('name')?>:</p>
			<p class="pageinput"><input type="text" name="htmlblob" maxlength="255" value="<?php echo $htmlblob?>" class="standard" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('content')?>:</p>
			<p class="pageinput"><?php echo create_textarea($gcb_wysiwyg, $content, 'content', 'wysiwyg', 'content');?></p>
		</div>
	<?php if ($adminaccess) { ?>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('owner')?>:</p>
			<p class="pageinput"><?php echo $owners?></p>
		</div>
	<?php } ?>
	<?php if ($addt_users != '') { ?>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('additionaleditors')?>:</p>
			<p class="pageinput"><select name="additional_editors[]" multiple="multiple" size="3"><?php echo $addt_users?></select></p>
		</div>
	<?php } ?>
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="edithtmlblob" value="true" />
				<input type="hidden" name="oldhtmlblob" value="<?php echo $oldhtmlblob; ?>" />
				<input type="hidden" name="htmlblob_id" value="<?php echo $htmlblob_id; ?>" />
				<input type="submit" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<?php if (!$adminaccess) { ?>
					<input type="hidden" name="owner_id" value="<?php echo $owner_id ?>" />
				<?php } ?>
				<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<input type="submit" name="apply" value="<?php echo lang('apply')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
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
