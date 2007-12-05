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

$ajax = false;
if (isset($_POST['ajax']) && $_POST['ajax']) $ajax = true;

$htmlblob_id = -1;
if (isset($_POST["htmlblob_id"])) $htmlblob_id = $_POST["htmlblob_id"];
else if (isset($_GET["htmlblob_id"])) $htmlblob_id = $_GET["htmlblob_id"];

if (isset($_POST["cancel"])) {
	redirect("listhtmlblobs.php");
	return;
}

$gCms = cmsms();
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
		else if ($htmlblob != $oldhtmlblob && CmsGlobalContentOperations::check_existing_name($htmlblob))
		{
			$error .= "<li>".lang('blobexists')."</li>";
			$validinfo = false;
		}

		if ($validinfo)
		{
			$blobobj = cmsms()->global_content->find_by_id($htmlblob_id);
			$blobobj->name = $htmlblob;
			$blobobj->set_multi_language_content('content', 'en_US', $content);
			$blobobj->owner = $owner_id;

			$blobobj->ClearAuthors();
			if (isset($_POST["additional_editors"])) {
					foreach ($_POST["additional_editors"] as $addt_user_id) {
						$blobobj->AddAuthor($addt_user_id);
					}
			}
			$blobobj->AddAuthor($userid);
			$result = $blobobj->save();

			if ($result)
			{
				audit($blobobj->id, $blobobj->name, 'Edited Html Blob');

				if (!isset($_POST['apply'])) {
					CmsResponse::redirect('listhtmlblobs.php');
				}
			}
			else
			{
				$error .= "<li>".lang('errorinsertingblob')."</li>";
			}
		}

		if ($ajax)
		{
			header('Content-Type: text/xml');
			print '<?xml version="1.0" encoding="UTF-8"?>';
			print '<EditBlob>';
			if ($error)
			{
				print '<Response>Error</Response>';
				print '<Details><![CDATA[' . $error . ']]></Details>';
			}
			else
			{
				print '<Response>Success</Response>';
				print '<Details><![CDATA[' . lang('edithtmlblobsuccess') . ']]></Details>';
			}
			print '</EditBlob>';
			exit;
		}
	}
	else if ($htmlblob_id != -1)
	{
		$onehtmlblob = cmsms()->global_content->find_by_id($htmlblob_id);
		$htmlblob = $onehtmlblob->name;
		$oldhtmlblob = $onehtmlblob->name;
		$owner_id = $onehtmlblob->owner;
		$content = $onehtmlblob->get_multi_language_content('content', 'en_US');
	}
}

if (strlen($htmlblob) > 0)
{
	$CMS_ADMIN_SUBTITLE = $htmlblob;
}

// Detect if a WYSIWYG is in use, and grab its form submit action (copied from editcotent.php)
$addlScriptSubmit = '';
foreach (array_keys($gCms->modules) as $moduleKey)
{
	$module =& $gCms->modules[$moduleKey];
	if (!($module['installed'] && $module['active'] && $module['object']->IsWYSIWYG()))
	{
		continue;
	}

	if ($module['object']->WYSIWYGActive() or get_preference(get_userid(), 'wysiwyg') == $module['object']->GetName())
	{
		$addlScriptSubmit .= $module['object']->WYSIWYGPageFormSubmit();
	}
}

$headtext = <<<EOSCRIPT
<script type="text/javascript">
window.Edit_Blob_Apply = function(button)
{
	$addlScriptSubmit
	$('Edit_Blob_Result').innerHTML = '';
	button.disabled = 'disabled';

	var data = new Array();
	data.push('ajax=1');
	data.push('apply=1');
	var elements = Form.getElements($('Edit_Blob'));
	for (var cnt = 0; cnt < elements.length; cnt++)
	{
		var elem = elements[cnt];
		if (elem.type == 'submit')
		{
			continue;
		}
		var query = Form.Element.serialize(elem);
		data.push(query);
	}

	new Ajax.Request(
		'{$_SERVER['REQUEST_URI']}'
		, {
			method: 'post'
			, parameters: data.join('&')
			, onSuccess: function(t)
			{
				button.removeAttribute('disabled');
				var response = t.responseXML.documentElement.childNodes[0];
				var details = t.responseXML.documentElement.childNodes[1];
				if (response.textContent) { response = response.textContent; } else { response = response.text; } 
				if (details.textContent) { details = details.textContent; } else { details = details.text; }

				var htmlShow = '';
				if (response == 'Success')
				{
					htmlShow = '<div class="pagemcontainer"><p class="pagemessage">' + details + '</p></div>';
				}
				else
				{
					htmlShow = '<div class="pageerrorcontainer"><ul class="pageerror">';
					htmlShow += details;
					htmlShow += '</ul></div>';
				}
				$('Edit_Blob_Result').innerHTML = htmlShow;
			}
			, onFailure: function(t)
			{
				alert('Could not save: ' + t.status + ' -- ' + t.statusText);
			}
		}
	);

	return false;
}
</script>
EOSCRIPT;

include_once("header.php");

// Holder for AJAX apply result
print '<div id="Edit_Blob_Result"></div>';

$db = cms_db();

$owners = "<select name=\"owner_id\">";

$query = "SELECT id, username FROM ".cms_db_prefix()."users ORDER BY username";
$result = $db->Execute($query);

while($result && $row = $result->FetchRow())
{
	$owners .= "<option value=\"".$row["id"]."\"";
	if ($row["id"] == $owner_id)
	{
		$owners .= " selected=\"selected\"";
	}
	$owners .= ">".$row["username"]."</option>";
}

$owners .= "</select>";

$addt_users = "";

$query = "SELECT id, username FROM ".cms_db_prefix()."users WHERE id <> ? ORDER BY username";
$result = $db->Execute($query,array($userid));

while($result && $row = $result->FetchRow())
{
	$addt_users .= "<option value=\"".$row["id"]."\"";
	$query = "SELECT * from ".cms_db_prefix()."additional_htmlblob_users WHERE user_id = ".$row["id"]." AND htmlblob_id = ?";
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
	<form id="Edit_Blob" method="post" action="edithtmlblob.php">
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
			<input type="submit" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
				<input type="submit" onclick="return window.Edit_Blob_Apply(this);" name="apply" value="<?php echo lang('apply')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			</p>
		</div>
	<div id="page_tabs" style="margin-top:0.5em;">
		<ul>
			<li><a href="#content"><span>Content</span></a></li>
			<li><a href="#permissions"><span>Permissions</span></a></li>
		</ul>
		<div id="content">		
		
		<div class="row">
			<label><?php echo lang('name')?>:</label>
			<input type="text" name="htmlblob" maxlength="255" value="<?php echo $htmlblob?>" class="standard" />
		</div>
		<div class="row">
			<label>*<?php echo lang('content')?>:</label>
			<?php echo create_textarea($gcb_wysiwyg, $content, 'content', 'wysiwyg', 'content');?>
		</div>
	</div>
	<div id="permissions">
	<?php if ($adminaccess) { ?>
		<div class="row">
			<label><?php echo lang('owner')?>:</label>
			<?php echo $owners?>
		</div>
	<?php } ?>
	<?php if ($addt_users != '') { ?>
		<div class="row">
			<label><?php echo lang('additionaleditors')?>:</label>
			<select name="additional_editors[]" multiple="multiple" size="3"><?php echo $addt_users?></select>
		</div>
	<?php } ?>
	</div>
	</div>
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
				<input type="submit" onclick="return window.Edit_Blob_Apply(this);" name="apply" value="<?php echo lang('apply')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			</p>
		</div>
	</form>
</div>
<script type="text/javascript">
<!--
	$('#page_tabs').tabs('content');
//-->
</script>

<?php
}
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>