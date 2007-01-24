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
$userid = get_userid();

require_once(dirname(dirname(__FILE__)) . '/lib/xajax/xajax.inc.php');
$xajax = new xajax();
$xajax->registerFunction('ajaxpreview');

$xajax->processRequests();
$headtext = $xajax->getJavascript('../lib/xajax')."\n";

if (isset($_POST["cancel"]))
{
	redirect("listcontent.php");
}

require_once("header.php");

$tmpfname = '';

$error = FALSE;

$firsttime = "1"; #Flag to make sure we're not trying to fill params on the first display
if (isset($_POST["firsttime"])) $firsttime = $_POST["firsttime"];

$preview = false;
if (isset($_POST["previewbutton"])) $preview = true;

$submit = false;
if (isset($_POST["submitbutton"])) $submit = true;

$apply = false;
if (isset($_POST["applybutton"])) $apply = true;

$parent_id = get_site_preference('default_parent_page', -1);
if (isset($_GET["parent_id"])) $parent_id = $_GET["parent_id"];

$contentobj = '';

function ajaxpreview($params)
{
	global $gCms;
	$config =& $gCms->GetConfig();
	$contentops =& $gCms->GetContentOperations();

	$contentobj = '';
	if (isset($params["serialized_content"]) && $params["serialized_content"] != '')
	{
		$content_type = $params['content_type'];
		$contentops->LoadContentType($content_type);
		$contentobj = UnserializeObject($params["serialized_content"]);
		if (strtolower(get_class($contentobj)) != strtolower($content_type))
		{
			copycontentobj($contentobj, $content_type, $params);
		}
	}
	else
	{
		$content_type = $params['content_type'];
		$contentobj = $contentops->CreateNewContent($content_type);
	}
	updatecontentobj($contentobj, true, $params);
	$tmpfname = createtmpfname($contentobj);
	$url = $config["root_url"].'/preview.php?tmpfile='.urlencode(basename($tmpfname));
	
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("previewframe", "src", $url);
	$objResponse->addAssign("serialized_content", "value", SerializeObject($contentobj));
	$count = 0;
	foreach ($contentobj->TabNames() as $tabname)
	{
		$objResponse->addScript("Element.removeClassName('editab".$count."', 'active');Element.removeClassName('editab".$count."_c', 'active');$('editab".$count."_c').style.display = 'none';");
		$count++;
	}
	$objResponse->addScript("Element.addClassName('edittabpreview', 'active');Element.addClassName('edittabpreview_c', 'active');$('edittabpreview_c').style.display = '';");
	return $objResponse->getXML();
}

function updatecontentobj(&$contentobj, $preview, $params = null)
{
	if ($params == null)
		$params = $_POST;

	$userid = get_userid();
	$adminaccess = (
        check_ownership($userid, $contentobj->Id()) ||
        check_permission($userid, 'Modify Any Page') || 
        check_permission($userid, 'Modify Page Structure')
	);
		
	#Fill contentobj with parameters
	$contentobj->FillParams($params);
	if ($preview)
	{
		$error = $contentobj->ValidateData();
	}

	if (isset($params["ownerid"]))
	{
		$contentobj->SetOwner($params["ownerid"]);
	}

	$contentobj->SetLastModifiedBy($userid);

	#Fill Additional Editors (kind of kludgy)
	if (isset($params["additional_editors"]))
	{
		$addtarray = array();
		foreach ($params["additional_editors"] as $addt_user_id)
		{
			$addtarray[] = $addt_user_id;
		}
		$contentobj->SetAdditionalEditors($addtarray);
	}
	else if ($adminaccess)
	{
		$contentobj->SetAdditionalEditors(array());
	}
}

function copycontentobj(&$contentobj, $content_type, $params = null)
{
	global $gCms;
	$contentops =& $gCms->GetContentOperations();
	
	if ($params == null)
		$params = $_POST;

	$newcontenttype = strtolower($content_type);
	$contentops->LoadContentType($newcontenttype);
	$contentobj->FillParams($params);
	$tmpobj = $contentops->CreateNewContent($newcontenttype);
	$tmpobj->SetId($contentobj->Id());
	$tmpobj->SetName($contentobj->Name());
	$tmpobj->SetMenuText($contentobj->MenuText());
	$tmpobj->SetTemplateId($contentobj->TemplateId());
	$tmpobj->SetParentId($contentobj->ParentId());
	$tmpobj->SetOldParentId($contentobj->OldParentId());
	$tmpobj->SetAlias($contentobj->Alias());
	$tmpobj->SetOwner($contentobj->Owner());
	$tmpobj->SetActive($contentobj->Active());
	$tmpobj->SetItemOrder($contentobj->ItemOrder());
	$tmpobj->SetOldItemOrder($contentobj->OldItemOrder());
	$tmpobj->SetShowInMenu($contentobj->ShowInMenu());
	//Some content types default to false for a reason... don't override it
	if (!(!$tmpobj->mCachable && $contentobj->Cachable()))
		$tmpobj->SetCachable($contentobj->Cachable());
	$tmpobj->SetHierarchy($contentobj->Hierarchy());
	$tmpobj->SetLastModifiedBy($contentobj->LastModifiedBy());
	$tmpobj->SetAdditionalEditors($contentobj->GetAdditionalEditors());
	$contentobj = $tmpobj;
}

function createtmpfname(&$contentobj)
{
	global $gCms;
	$config =& $gCms->GetConfig();
	$templateops =& $gCms->GetTemplateOperations();

	$data["content_id"] = $contentobj->Id();
	$data["title"] = $contentobj->Name();
	$data["menutext"] = $contentobj->MenuText();
	$data["content"] = $contentobj->Show();
	$data["template_id"] = $contentobj->TemplateId();
	$data["hierarchy"] = $contentobj->Hierarchy();
	
	$templateobj = $templateops->LoadTemplateById($contentobj->TemplateId());
	$data['template'] = $templateobj->content;

	$stylesheetobj = get_stylesheet($contentobj->TemplateId());
	$data['encoding'] = $stylesheetobj['encoding'];
	// $data['stylesheet'] = $stylesheetobj['stylesheet'];

	$tmpfname = '';
	if (is_writable($config["previews_path"]))
	{
		$tmpfname = tempnam($config["previews_path"], "cmspreview");
	}
	else
	{
		$tmpfname = tempnam(TMP_CACHE_LOCATION, "cmspreview");
	}
	$handle = fopen($tmpfname, "w");
	fwrite($handle, serialize($data));
	fclose($handle);
	
	return $tmpfname;
}

#Get current userid and make sure they have permission to add something
$access = (check_permission($userid, 'Add Pages') || check_permission($userid, 'Modify Page Structure'));

#Get a list of content types and pick a default if necessary
global $gCms;
$contentops =& $gCms->GetContentOperations();
$existingtypes = $contentops->ListContentTypes();
$content_type = "";
if (isset($_POST["content_type"]))
{
	$content_type = $_POST["content_type"];
}
else
{
	if (isset($existingtypes) && count($existingtypes) > 0)
	{
		$content_type = 'content';
	}
	else
	{
		$error = "No content types loaded!";
	}
}

$contentobj = "";
if (isset($_POST["serialized_content"]))
{
	$contentops =& $gCms->GetContentOperations();
	$contentops->LoadContentType($_POST['orig_content_type']);
	$contentobj = unserialize(base64_decode($_POST["serialized_content"]));
	if (strtolower(get_class($contentobj)) != $content_type)
	{
		#Fill up the existing object with values in form
		#Create new object
		#Copy important fields to new object
		#Put new object on top of old on
		copycontentobj($contentobj, $content_type);
	}
}
else
{
	$contentops =& $gCms->GetContentOperations();
	$contentobj = $contentops->CreateNewContent($content_type);
	$contentobj->SetOwner($userid);
	$contentobj->SetActive(True);
	$contentobj->SetShowInMenu(True);
	$contentobj->SetLastModifiedBy($userid);
	$contentobj->SetPropertyValue('content_en', get_site_preference('defaultpagecontent'));
	if ($parent_id!=-1) $contentobj->SetParentId($parent_id);
}

if ($access)
{
	if ($submit || $apply)
	{
		#Fill contentobj with parameters
		$contentobj->FillParams($_POST);
		$contentobj->SetOwner($userid);

		#Fill Additional Editors (kind of kludgy)
		if (isset($_POST["additional_editors"]))
		{
			$addtarray = array();
			foreach ($_POST["additional_editors"] as $addt_user_id)
			{
				$addtarray[] = $addt_user_id;
			}
			$contentobj->SetAdditionalEditors($addtarray);
		}

		$error = $contentobj->ValidateData();
		if ($error === FALSE)
		{
			$contentobj->Save();
			global $gCms;
			$contentops =& $gCms->GetContentOperations();
			$contentops->SetAllHierarchyPositions();
			if ($submit)
			{
				audit($contentobj->Id(), $contentobj->Name(), 'Added Content');
				redirect('listcontent.php?message=contentadded');
			}
		}
	}
	else if ($firsttime == "0") #Either we're a preview or a template postback
	{
		$contentobj->FillParams($_POST);
		if ($preview) #If preview, check for errors...
		{
			$error = $contentobj->ValidateData();
		}
		if (isset($_POST["additional_editors"]))
		{
			$addtarray = array();
			foreach ($_POST["additional_editors"] as $addt_user_id)
			{
				$addtarray[] = $addt_user_id;
			}
			$contentobj->SetAdditionalEditors($addtarray);
		}
	}
	else
	{
		$firsttime = "0";
	}
}

if (!$access)
{
	echo "<div class=\"pageerrorcontainer pageoverflow\"><p class=\"pageerror\">".lang('noaccessto',array(lang('addcontent')))."</p></div>";
}
else
{
#Get a list of content_types and build the dropdown to select one
$typesdropdown = '<select name="content_type" onchange="document.contentform.submit()" class="standard">';
$cur_content_type = '';
foreach ($gCms->contenttypes as $onetype)
{
	$typesdropdown .= '<option value="' . $onetype->type . '"';
	if ($onetype->type == $content_type)
	{
		$typesdropdown .= ' selected="selected" ';
		$cur_content_type = $onetype->type;
	}
	$typesdropdown .= ">".($onetype->friendlyname)."</option>";
}
$typesdropdown .= "</select>";

if (FALSE == empty($error))
{
	echo $themeObject->ShowErrors($error);
}
else if ($preview)
{
	$tmpfname = createtmpfname($contentobj);

?>

<!--
<div class="pagecontainer">
	<p class="pageheader"><?php echo lang('preview')?></p>
	<iframe name="previewframe" class="preview" src="<?php echo $config["root_url"] ?>/preview.php?tmpfile=<?php echo urlencode(basename($tmpfname))?>"></iframe>
</div>
-->
<?php
}

#$contentarray = $contentobj->EditAsArray(true, 0);
#$contentarray2 = $contentobj->EditAsArray(true, 1);

$tabnames = $contentobj->TabNames();

?>

<div class="pagecontainer">
<div class="pageoverflow">
	<?php
	echo $themeObject->ShowHeader('addcontent').'</div>';
	?>
	<div id="page_tabs">
		<?php
		$count = 0;

		#We have preview, but no tabs
		if (count($tabnames) == 0)
		{
			?>
			<div id="editab0" class="active"><?php echo lang('content')?></div>
			<?php
		}
		else
		{
			foreach ($tabnames as $onetab)
			{
				?>
				<div id="editab<?php echo $count?>"><?php echo $onetab?></div>
				<?php
				$count++;
			}
		}

		if ($contentobj->mPreview)
		{
			echo '<div id="edittabpreview"'.($tmpfname!=''?' class="active"':'').' onclick="##INLINESUBMITSTUFFGOESHERE##xajax_ajaxpreview(xajax.getFormValues(\'contentform\'));return false;">'.lang('preview').'</div>';
		}
		?>
	</div>
	<div style="clear: both;"></div>
	<form method="post" action="addcontent.php" name="contentform" enctype="multipart/form-data" id="contentform"##FORMSUBMITSTUFFGOESHERE##>
	<input type="hidden" id="serialized_content" name="serialized_content" value="<?php echo SerializeObject($contentobj); ?>" />			
	<div id="page_content">
		<div class="pageoverflow">	
		<?php
		
		$submit_buttons = '
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">';
		$submit_buttons .= ' <input type="submit" name="submitbutton" value="'.lang('submit').'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" />';
		if (isset($contentobj->mPreview) && $contentobj->mPreview == true) {
			$submit_buttons .= ' <input type="submit" name="previewbutton" value="'.lang('preview').'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" onclick="##INLINESUBMITSTUFFGOESHERE##xajax_ajaxpreview(xajax.getFormValues(\'contentform\'));return false;" />';
		}
		$submit_buttons .= ' <input type="submit" name="cancel" value="'.lang('cancel').'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" /></p>';
		
		$numberoftabs = count($tabnames);
		$showtabs = 1;
		if ($numberoftabs == 0)
		{
			$numberoftabs = 1;
			$showtabs = 1;
		}
		for ($currenttab = 0; $currenttab < $numberoftabs; $currenttab++)
		{
			if ($showtabs == 1)
			{
				?><div id="editab<?php echo $currenttab ?>_c"><?php
			}
			if ($currenttab == 0)
			{
				echo $submit_buttons;
				?>

				<div class="pageoverflow">
					<p class="pagetext"><?php echo lang('contenttype'); ?>:</p>
					<p class="pageinput"><?php echo $typesdropdown; ?></p>
				</div>
				<?php
			}
			$contentarray = $contentobj->EditAsArray(true, $currenttab, $access);
			for($i=0;$i<count($contentarray);$i++)
			{
				?>
				<div class="pageoverflow">
					<p class="pagetext"><?php echo $contentarray[$i][0]; ?></p>
					<p class="pageinput"><?php echo $contentarray[$i][1]; ?></p>
				</div>
				<?php
			}
			echo '<input type="hidden" name="firsttime" value="0" /><input type="hidden" name="orig_content_type" value="'.$cur_content_type.'" />';
					?>
			</div>
			<div style="clear: both;"></div>
				  <?php
		}
		if ($contentobj->mPreview)
		{
			echo '<div class="pageoverflow"><div id="edittabpreview_c"'.($tmpfname!=''?' class="active"':'').'>';
				?>
					<iframe name="previewframe" class="preview" id="previewframe"<?php if ($tmpfname != '') { ?> src="<?php echo $config["root_url"] ?>/preview.php?tmpfile=<?php echo urlencode(basename($tmpfname))?>"<?php } ?>></iframe>
				<?php
			echo '<div style="clear: both;"></div>';
		}
?>
</div>
</div>
<div class="pageoverflow">
<?php
echo $submit_buttons;
?>
</div>
</div>
</div>
</form>
</div>
<?php
}
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
