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

if (isset($_POST["cancel"]))
{
	redirect("listcontent.php");
}


check_login();

$error = FALSE;

$content_id = "";
if (isset($_POST["content_id"])) $content_id = $_POST["content_id"];
else if (isset($_GET["content_id"])) $content_id = $_GET["content_id"];

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
	$contentobj = unserialize(base64_decode($_POST["serialized_content"]));
	if (strtolower(get_class($contentobj)) != strtolower($content_type))
	{
		#Fill up the existing object with values in form
		#Create new object
		#Copy important fields to new object
		#Put new object on top of old on

		$contentobj->FillParams($_POST);
		$newcontenttype = strtolower($content_type);
		$tmpobj = new $newcontenttype;
		$tmpobj->mId = $contentobj->mId;
		$tmpobj->mName = $contentobj->mName;
		$tmpobj->mMenuText = $contentobj->mMenuText;
		$tmpobj->mTemplateId = $contentobj->mTemplateId;
		$tmpobj->mParentId = $contentobj->mParentId;
		$tmpobj->mOldParentId = $contentobj->mOldParentId;
		$tmpobj->mAlias = $contentobj->mAlias;
		$tmpobj->mOwner = $contentobj->mOwner;
		$tmpobj->mActive = $contentobj->mActive;
		$tmpobj->mItemOrder = $contentobj->mItemOrder;
		$tmpobj->mOldItemOrder = $contentobj->mOldItemOrder;
		$tmpobj->mShowInMenu = $contentobj->mShowInMenu;
		$tmpobj->mCachable = $contentobj->mCachable;
		$tmpobj->mHierarchy = $contentobj->mHierarchy;
		$tmpobj->mLastModifiedBy = $contentobj->mLastModifiedBy;
		$tmpobj->SetAdditionalEditors($contentobj->GetAdditionalEditors());
		$contentobj = $tmpobj;
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
			$contentobj->mOwner = $_POST["ownerid"];
		}

		#Fill Additional Editors (kind of kludgy)
		if (isset($_POST["additional_editors"]))
		{
			$addtarray = array();
			foreach ($_POST["additional_editors"] as $addt_user_id)
			{
				array_push($addtarray, $addt_user_id);
			}
			$contentobj->SetAdditionalEditors($addtarray);
		}

		if ($error === FALSE)
		{
			$contentobj->Save();
			ContentManager::SetAllHierarchyPositions();
			if ($submit)
			{
				audit($contentobj->Id(), $contentobj->Name(), 'Edited Content');
				redirect("listcontent.php");
			}
		}
	}
	else if ($content_id != -1 && !$preview && strtolower(get_class($contentobj)) != strtolower($content_type))
	{
		$contentobj = ContentManager::LoadContentFromId($content_id);
		$content_type = $contentobj->Type();
		$contentobj->mLastModifiedBy = $userid;
	}
	else
	{
		#Fill contentobj with parameters
		$contentobj->FillParams($_POST);
		if ($preview)
		{
			$error = $contentobj->ValidateData();
		}

		if (isset($_POST["ownerid"]))
		{
			$contentobj->mOwner = $_POST["ownerid"];
		}

		$contentobj->mLastModifiedBy = $userid;

		#Fill Additional Editors (kind of kludgy)
		if (isset($_POST["additional_editors"]))
		{
			$addtarray = array();
			foreach ($_POST["additional_editors"] as $addt_user_id)
			{
				array_push($addtarray, $addt_user_id);
			}
			$contentobj->SetAdditionalEditors($addtarray);
		}
	}
}

if (strlen($contentobj->mName)> 0)
    {
    $CMS_ADMIN_SUBTITLE = $contentobj->mName;
    }
include_once("header.php");


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

	if (isset($error) && $error !== FALSE)
	{
		echo "<div class=\"pageerrorcontainer\"><ul class=\"pageerror\">";
		foreach ($error as $oneerror)
		{
			echo '<li>'.$oneerror.'</li>';
		}
		echo "</ul></div>";
	}
	else if ($preview)
	{
		$data["content_id"] = $contentobj->Id();
		$data["title"] = $contentobj->Name();
		$data["content"] = $contentobj->Show();
		$data["template_id"] = $contentobj->TemplateId();
		$data["hierarchy"] = $contentobj->Hierarchy();
		
		$templateobj = TemplateOperations::LoadTemplateById($contentobj->TemplateId());
		$data['template'] = $templateobj->content;

		$stylesheetobj = get_stylesheet($contentobj->TemplateId());
		$data['encoding'] = $stylesheetobj['encoding'];
		$data['stylesheet'] = $stylesheetobj['stylesheet'];

		$tmpfname = '';
		if (is_writable($config["previews_path"]))
		{
			$tmpfname = tempnam($config["previews_path"], "cmspreview");
		}
		else
		{
			$tmpfname = tempnam(dirname(dirname(__FILE__)) . '/tmp/cache', "cmspreview");
		}
		$handle = fopen($tmpfname, "w");
		fwrite($handle, serialize($data));
		fclose($handle);

	?>
<div class="pagecontainer">
	<p class="pageheader"><?php echo lang('preview')?></p>
	<iframe name="previewframe" class="preview" src="<?php echo $config["root_url"] ?>/preview.php?tmpfile=<?php echo urlencode(basename($tmpfname))?>"></iframe>
</div>
<?php

}

$contentarray = $contentobj->EditAsArray(true, 0, $adminaccess);
$contentarray2 = $contentobj->EditAsArray(true, 1, $adminaccess);

?>

<div class="pagecontainer">
	<p class="pageheader"><?php echo lang('editcontent')?></p>
	<div id="page_tabs">
		<div id="main">Main</div>
		<div id="options">Options</div>
	</div>
	<div style="clear: both;"></div>
	<form method="post" action="editcontent.php" name="contentform" id="contentform"##FORMSUBMITSTUFFGOESHERE##>
<input type="hidden" name="serialized_content" value="<?php echo base64_encode(serialize($contentobj)) ?>" /> 	                  <input type="hidden" name="content_id" value="<?php echo $content_id?>" />
	<div id="page_content">
		<div id="main_c">  
		
					<div class="pageoverflow">
				<p class="pagetext"><?php echo lang('contenttype'); ?>:</p>
				<p class="pageinput"><?php echo $typesdropdown; ?></p>
			</div>

		<?php
		for($i=0;$i<count($contentarray);$i++)
		{
		  ?><div class="pageoverflow">
				<p class="pagetext"><?php echo $contentarray[$i][0]; ?></p>
				<p class="pageinput"><?php echo $contentarray[$i][1]; ?></p>
			</div>                                            
		  <?php
		}
		?>
			<div class="pageoverflow">
				<p class="pagetext">&nbsp;</p>
				<p class="pageinput">
					<?php if (isset($contentobj->mPreview) && $contentobj->mPreview == true) { ?>
						<input type="submit" name="previewbutton" value="<?php echo lang('preview')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'">
					<?php } ?>
					<input type="submit" name="submitbutton" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'">
					<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'">
				</p>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div id="options_c">

		<?php
		for($i=0;$i<count($contentarray2);$i++)
		{
		  ?><div class="pageoverflow">
				<p class="pagetext"><?php echo $contentarray2[$i][0]; ?></p>
				<p class="pageinput"><?php echo $contentarray2[$i][1]; ?></p>
			</div>                                            
		  <?php
		}
		 ?>
			<div class="pageoverflow">
				<p class="pagetext">&nbsp;</p>
				<p class="pageinput">
					<?php if (isset($contentobj->mPreview) && $contentobj->mPreview == true) { ?>
						<input type="submit" name="previewbutton" value="<?php echo lang('preview')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'">
					<?php } ?>
					<input type="submit" name="submitbutton" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'">
					<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'">
				</p>
			</div>
			<div style="clear: both;"></div>			
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
