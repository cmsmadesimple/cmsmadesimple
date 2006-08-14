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

include_once("../lib/classes/class.admintheme.inc.php");

require_once(dirname(dirname(__FILE__)) . '/lib/xajax/xajax.inc.php');
$xajax = new xajax();
$xajax->registerFunction('ajaxpreview');

$xajax->processRequests();
$headtext = $xajax->getJavascript('../lib/xajax')."\n";

if (isset($_POST["cancel"]))
{
	redirect("listcontent.php");
}

$error = FALSE;

$content_id = "";
if (isset($_REQUEST["content_id"])) $content_id = $_REQUEST["content_id"];

$pagelist_id = "1";
if (isset($_REQUEST["page"])) $pagelist_id = $_REQUEST["page"];

$preview = false;
if (isset($_POST["previewbutton"])) $preview = true;

$submit = false;
if (isset($_POST["submitbutton"])) $submit = true;

$apply = false;
if (isset($_POST["applybutton"])) $apply = true;

if ($preview || $apply)
{
	$CMS_EXCLUDE_FROM_RECENT=1;
}

function ajaxpreview($params)
{
	global $gCms;
	$config =& $gCms->GetConfig();

	$contentobj = UnserializeObject($params["serialized_content"]);
	$content_type = $params['content_type'];
	if (strtolower(get_class($contentobj)) != strtolower($content_type))
	{
		copycontentobj($contentobj, $content_type, $params);
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
	$adminaccess = check_ownership($userid, $contentobj->Id()) || check_permission($userid, 'Modify Any Page');
		
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
	if ($params == null)
		$params = $_POST;
	$contentobj->FillParams($params);
	$newcontenttype = strtolower($content_type);
	$tmpobj = new $newcontenttype;
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

	$data["content_id"] = $contentobj->Id();
	$data["title"] = $contentobj->Name();
	$data["menutext"] = $contentobj->MenuText();
	$data["content"] = $contentobj->Show();
	$data["template_id"] = $contentobj->TemplateId();
	$data["hierarchy"] = $contentobj->Hierarchy();
	
	$templateobj = TemplateOperations::LoadTemplateById($contentobj->TemplateId());
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

#Get a list of content types and pick a default if necessary
$existingtypes = ContentManager::ListContentTypes();
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
		$error = "<p>No content types loaded!</p>";	
	}
}

$contentobj = "";
if (isset($_POST["serialized_content"]))
{
	$contentobj = UnserializeObject($_POST["serialized_content"]);
	if (strtolower(get_class($contentobj)) != strtolower($content_type))
	{
		#Fill up the existing object with values in form
		#Create new object
		#Copy important fields to new object
		#Put new object on top of old one
		copycontentobj($contentobj, $content_type);
	}
}

#Get current userid and make sure they have permission to add something
$userid = get_userid();
$access = check_ownership($userid, $content_id) || check_permission($userid, 'Modify Any Page');
$adminaccess = $access;
if (!$access)
{
	$access = check_authorship($userid, $content_id);
}

if ($access)
{
	if ($submit || $apply)
	{
		#Fill contentobj with parameters
		$contentobj->FillParams($_POST);
		$error = $contentobj->ValidateData();

		if (isset($_POST["ownerid"]))
		{
			$contentobj->SetOwner($_POST["ownerid"]);
		}

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
		else if ($adminaccess)
		{
			$contentobj->SetAdditionalEditors(array());
		}

		if ($error === FALSE)
		{
			$contentobj->Save();
			ContentManager::SetAllHierarchyPositions();
			if ($submit)
			{
				audit($contentobj->Id(), $contentobj->Name(), 'Edited Content');
				redirect("listcontent.php?page=".$pagelist_id.'&message=contentupdated');
			}
		}
	}
	else if ($content_id != -1 && !$preview && strtolower(get_class($contentobj)) != strtolower($content_type))
	{
		$contentobj = ContentManager::LoadContentFromId($content_id);
		$content_type = $contentobj->Type();
		$contentobj->SetLastModifiedBy($userid);
	}
	else
	{
		updatecontentobj($contentobj, $preview);
	}
}

if (strlen($contentobj->Name()) > 0)
{
	$CMS_ADMIN_SUBTITLE = $contentobj->Name();
}

include_once("header.php");

$tmpfname = '';

if (!$access)
{
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto',array(lang('editpage')))."</p></div>";
}
else
{
	#Get a list of content_types and build the dropdown to select one
	$typesdropdown = '<select name="content_type" onchange="document.contentform.submit()" class="standard">';
	foreach ($existingtypes as $onetype=>$name)
	{
		$typesdropdown .= "<option value=\"$onetype\"";
		if ($onetype == $content_type)
		{
			$typesdropdown .= ' selected="selected"';
		}
		$typesdropdown .= ">".$name."</option>";
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

<?php

}

#$contentarray = $contentobj->EditAsArray(true, 0, $adminaccess);
#$contentarray2 = $contentobj->EditAsArray(true, 1, $adminaccess);

$tabnames = $contentobj->TabNames();

?>

<div class="pagecontainer pageoverflow">
	<?php
	echo $themeObject->ShowHeader('editcontent');
	?>
	<div id="page_tabs" style="width:100%;">
		<?php
		$count = 0;

		#We have preview, but no tabs
		if (count($tabnames) == 0)
		{
			?>
			<div id="editab0"><?php echo lang('content')?></div>
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
		
		#Make a preview tab
		if ($contentobj->mPreview)
		{
			echo '<div id="edittabpreview"'.($tmpfname!=''?' class="active"':'').' onclick="##INLINESUBMITSTUFFGOESHERE##xajax_ajaxpreview(xajax.getFormValues(\'contentform\'));return false;">'.lang('preview').'</div>';
		}

		?>
	</div>
	<div style="clear: both;"></div>
	<form method="post" action="editcontent.php<?php if (isset($content_id) && isset($pagelist_id)) echo "?content_id=$content_id&amp;page=$pagelist_id";?>" enctype="multipart/form-data" name="contentform" id="contentform"##FORMSUBMITSTUFFGOESHERE##>
<input type="hidden" id="serialized_content" name="serialized_content" value="<?php echo SerializeObject($contentobj); ?>" />
<input type="hidden" name="content_id" value="<?php echo $content_id?>" />
<input type="hidden" name="page" value="<?php echo $pagelist_id; ?>" />
<div id="page_content">
<?php
$submit_buttons = '<div class="pageoverflow">
<p class="pagetext">&nbsp;</p>
<p class="pageinput">
 <input type="submit" name="submitbutton" value="'.lang('submit').'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" title="'.lang('submitdescription').'" />';
if (isset($contentobj->mPreview) && $contentobj->mPreview == true)
  {
    $submit_buttons .= ' <input type="submit" name="previewbutton" value="'.lang('preview').'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" title="'.lang('previewdescription').'" onclick="##INLINESUBMITSTUFFGOESHERE##xajax_ajaxpreview(xajax.getFormValues(\'contentform\'));return false;" />';
  }
$submit_buttons .= ' <input type="submit" name="cancel" value="'.lang('cancel').'" class="pagebutton" onclick="return confirm(\''.lang('confirmcancel').'\');" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" title="'.lang('canceldescription').'" />';
$submit_buttons .= ' <input type="submit" name="applybutton" value="'.lang('apply').'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" title="'.lang('applydescription').'" />
</p>
</div>';
//echo $submit_buttons;
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
			
			$contentarray = $contentobj->EditAsArray(false, $currenttab, $adminaccess);
			for($i=0;$i<count($contentarray);$i++)
			{
				?>
				<div class="pageoverflow">
					<p class="pagetext"><?php echo $contentarray[$i][0]; ?></p>
					<p class="pageinput"><?php echo $contentarray[$i][1]; ?></p>
				</div>
				<?php
			}
            
			?>
			<div style="clear: both;"></div>
		</div>
		<?php
		}
		if ($contentobj->mPreview)
		{
			echo '<div class="pageoverflow"><div id="edittabpreview_c"'.($tmpfname!=''?' class="active"':'').'>';
				?>
					<iframe name="previewframe" class="preview" id="previewframe"<?php if ($tmpfname != '') { ?> src="<?php echo $config["root_url"] ?>/preview.php?tmpfile=<?php echo urlencode(basename($tmpfname))?>"<?php } ?>></iframe>
				<?php
			echo '</div></div>';
			echo '<div style="clear: both;"></div>';
		}
		echo $submit_buttons;
		?>
	</div>
	</form>
</div>

<?php

}

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
