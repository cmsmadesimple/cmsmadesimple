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
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();
$userid = get_userid();

require_once(dirname(__FILE__).'/editcontent_extra.php');
$cms_ajax = new CmsAjax();
$cms_ajax->register_function('ajaxpreview');

$headtext = $cms_ajax->get_javascript();
$cms_ajax->process_requests();

if (isset($_POST["cancel"]))
{
	redirect("listcontent.php".$urlext);
}

require_once("header.php");

$tmpfname = '';

$error = FALSE;

$firsttime = "1"; #Flag to make sure we're not trying to fill params on the first display
if (isset($_POST["firsttime"])) $firsttime = $_POST["firsttime"];

$submit = false;
if (isset($_POST["submitbutton"])) $submit = true;

$apply = false;
if (isset($_POST["applybutton"])) $apply = true;

$contentobj = '';

#Get current userid and make sure they have permission to add something
$access = (check_permission($userid, 'Add Pages') || check_permission($userid, 'Manage All Content'));

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
	$contentobj->SetAddMode();
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
  $page_cachable = ((get_site_preference('page_cachable',"1")=="1")?true:false);
  $active = ((get_site_preference('page_active',"1")=="1")?true:false);
  $showinmenu = ((get_site_preference('page_showinmenu',"1")=="1")?true:false);
  $metadata = get_site_preference('page_metadata');

  $parent_id = get_preference($userid, 'default_parent', -2);
  if (isset($_GET["parent_id"])) $parent_id = $_GET["parent_id"];

  $contentops =& $gCms->GetContentOperations();
  $contentobj = $contentops->CreateNewContent($content_type);
  $contentobj->SetAddMode();
  $contentobj->SetOwner($userid);
  $contentobj->SetCachable($page_cachable);
  $contentobj->SetActive($active);
  $contentobj->SetShowInMenu($showinmenu);
  $contentobj->SetLastModifiedBy($userid);

  {
    $templateops =& $gCms->GetTemplateOperations();
    $dflt = $templateops->LoadDefaultTemplate();
    if( isset($dflt) )
      {
	$contentobj->SetTemplateId($dflt->id);
      }
  }

  // this stuff should be changed somehow.
  $contentobj->SetMetadata($metadata);
  $contentobj->SetPropertyValue('content_en', get_site_preference('defaultpagecontent')); // why?

  if ($parent_id!=-1) $contentobj->SetParentId($parent_id);
  $contentobj->SetPropertyValue('searchable',
				get_site_preference('page_searchable',1));
  $contentobj->SetPropertyValue('extra1',
				get_site_preference('page_extra1',''));
  $contentobj->SetPropertyValue('extra2',
				get_site_preference('page_extra2',''));
  $contentobj->SetPropertyValue('extra3',
				get_site_preference('page_extra3',''));
  $tmp = get_site_preference('additional_editors');
  $tmp2 = array();
  if( !empty($tmp) )
    {
      $tmp2 = explode(',',$tmp);
    }
  $contentobj->SetAdditionalEditors($tmp2);
}

if ($access)
{
	if ($submit || $apply)
	{
		#Fill contentobj with parameters
	  $contentobj->SetAddMode();
		$contentobj->FillParams($_POST);
		$contentobj->SetOwner($userid);

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
				redirect('listcontent.php'.$urlext.'&message=contentadded');
			}
		}
	}
	else if ($firsttime == "0") #Either we're a preview or a template postback
	{
		$contentobj->FillParams($_POST);
// 		if (isset($_POST["additional_editors"]))
// 		{
// 			$addtarray = array();
// 			foreach ($_POST["additional_editors"] as $addt_user_id)
// 			{
// 				$addtarray[] = $addt_user_id;
// 			}
// 			$contentobj->SetAdditionalEditors($addtarray);
// 		}
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
  if( $onetype->type == 'errorpage' && !check_permission($userid,'Manage All Content') ) 
    {
      continue;
    }
	$contentops->LoadContentType($onetype->type);
	$type_obj = new $onetype->type;
	$typesdropdown .= '<option value="' . $onetype->type . '"';
	if ($onetype->type == $content_type)
	{
		$typesdropdown .= ' selected="selected" ';
		$cur_content_type = $onetype->type;
	}
	$typesdropdown .= ">".($type_obj->FriendlyName())."</option>";
}
$typesdropdown .= "</select>";

if (FALSE == empty($error))
{
	echo $themeObject->ShowErrors($error);
}
?>

<!--
<div class="pagecontainer">
	<p class="pageheader"><?php echo lang('preview')?></p>
	<iframe name="previewframe" class="preview" src="<?php echo $config["root_url"] ?>/index.php?<?php echo $config['query_var'] ?>=__CMS_PREVIEW_PAGE__"></iframe>
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

  if ($contentobj->Previewable())
		{
			echo '<div id="edittabpreview"'.($tmpfname!=''?' class="active"':'').' onclick="##INLINESUBMITSTUFFGOESHERE##cms_ajax_ajaxpreview(jQuery(\'#contentform\').serializeForCmsAjax());return false;">'.lang('preview').'</div>';
		}
		?>
	</div>
	<div style="clear: both;"></div>
	<form method="post" action="addcontent.php" name="contentform" enctype="multipart/form-data" id="contentform"##FORMSUBMITSTUFFGOESHERE##>
        <div class="hidden">
        <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" />
	<input type="hidden" id="serialized_content" name="serialized_content" value="<?php echo SerializeObject($contentobj); ?>" />
        </div>
	<div id="page_content">
		<div class="pageoverflow">	
		<?php
		
		$submit_buttons = '
			<div class="pagetext">&nbsp;</div>
			<div class="pageinput">';
		$submit_buttons .= ' <input type="submit" accesskey="s" name="submitbutton" value="'.lang('submit').'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" accesskey="s" onmouseout="this.className=\'pagebutton\'" />';
		$submit_buttons .= ' <input type="submit" accesskey="C" name="cancel" value="'.lang('cancel').'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" accesskey="c" onmouseout="this.className=\'pagebutton\'" /></div>';
		
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
					<div class="pagetext"><?php echo lang('contenttype'); ?>:</div>
					<div class="pageinput"><?php echo $typesdropdown; ?></div>
				</div>
				<?php
			}
			$contentarray = $contentobj->EditAsArray(true, $currenttab, $access);
			for($i=0;$i<count($contentarray);$i++)
			{
			  if( is_array($contentarray[$i]) ) {
				?>
				<div class="pageoverflow">
					<div class="pagetext"><?php echo $contentarray[$i][0]; ?></div>
					<div class="pageinput"><?php echo $contentarray[$i][1]; ?></div>
				</div>
				<?php
		            }
			}
			echo '<div class="hidden" ><input type="hidden" name="firsttime" value="0" /><input type="hidden" name="orig_content_type" value="'.$cur_content_type.'" /></div>';
					?>
			</div>
			<div style="clear: both;"></div>
				  <?php
		}
if ($contentobj->Previewable())
		{
			echo '<div class="pageoverflow"><div id="edittabpreview_c"'.($tmpfname!=''?' class="active"':'').'>';
				?>
			  <div class="pagewarning"><?php echo lang('info_preview_notice') ?></div>
					<iframe name="previewframe" class="preview" id="previewframe" src="<?php echo $config["root_url"] ?>/index.php?<?php echo $config['query_var'] ?>=__CMS_PREVIEW_PAGE__"></iframe>
				<?php
			echo '<div style="clear: both;"></div></div></div>';
		}
?>
</div>
<div class="pageoverflow">
<?php
echo $submit_buttons;
?>

</div></div>
</form>
</div>

<?php
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
