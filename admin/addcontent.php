<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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

define('XAJAX_DEFAULT_CHAR_ENCODING', $config['admin_encoding']);
require_once(dirname(dirname(__FILE__)) . '/lib/xajax/xajax_core/xajax.inc.php');
require_once(dirname(__FILE__).'/editcontent_extra.php');
$xajax = new xajax();
$xajax->register(XAJAX_FUNCTION,'ajaxpreview');
$headtext = $xajax->getJavascript('../lib/xajax')."\n";

if (isset($_POST["cancel"]))
{
	redirect("listcontent.php".$urlext);
}

require_once("header.php");

$tmpfname = '';

$error = FALSE;

$submit = false;
if (isset($_POST["submitbutton"])) $submit = true;

$apply = false;
if (isset($_POST["applybutton"])) $apply = true;

$contentobj = null;

#Get current userid and make sure they have permission to add something
$access = (check_permission($userid, 'Add Pages') || check_permission($userid, 'Manage All Content'));

#Get a list of content types and pick a default if necessary
$gCms = cmsms();
$contentops = $gCms->GetContentOperations();
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
	  $content_type = get_site_preference('default_contenttype','content');
	}
	else
	{
		$error = "No content types loaded!";
	}
}

$contentobj = null;
{
  $page_secure = get_site_preference('page_secure',0);
  $page_cachable = ((get_site_preference('page_cachable',"1")=="1")?true:false);
  $active = ((get_site_preference('page_active',"1")=="1")?true:false);
  $showinmenu = ((get_site_preference('page_showinmenu',"1")=="1")?true:false);
  $metadata = get_site_preference('page_metadata');

  $parent_id = get_preference($userid, 'default_parent', -2);
  if (isset($_GET["parent_id"])) $parent_id = $_GET["parent_id"];

  $contentobj = $contentops->CreateNewContent($content_type);
  $contentobj->SetAddMode();
  $contentobj->SetOwner($userid);
  $contentobj->SetCachable($page_cachable);
  $contentobj->SetActive($active);
  $contentobj->SetShowInMenu($showinmenu);
  $contentobj->SetLastModifiedBy($userid);

  $templateops = $gCms->GetTemplateOperations();
  $dflt = $templateops->LoadDefaultTemplate();
  if( isset($dflt) ) {
    $contentobj->SetTemplateId($dflt->id);
  }

  // this stuff should be changed somehow.
  $contentobj->SetMetadata($metadata);
  $contentobj->SetPropertyValue('content_en', get_site_preference('defaultpagecontent')); // why?

  if ($parent_id!=-1) $contentobj->SetParentId($parent_id);
  $contentobj->SetPropertyValue('searchable',get_site_preference('page_searchable',1));
  $contentobj->SetPropertyValue('extra1',get_site_preference('page_extra1',''));
  $contentobj->SetPropertyValue('extra2',get_site_preference('page_extra2',''));
  $contentobj->SetPropertyValue('extra3',get_site_preference('page_extra3',''));
  $tmp = get_site_preference('additional_editors');
  $tmp2 = array();
  if( !empty($tmp) ) {
    $tmp2 = explode(',',$tmp);
  }
  $contentobj->SetAdditionalEditors($tmp2);
}

$xajax->processRequest();

if ($access && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' )
  {
    try {
      if ($submit || $apply)
	{
	  // Fill contentobj with parameters
	  $contentobj->SetAddMode();
	  $contentobj->FillParams($_POST);
	  $contentobj->SetOwner($userid);
	    
	  $error = $contentobj->ValidateData();
	  if ($error === FALSE)
	    {
	      $contentobj->Save();
	      $gCms = cmsms();
	      $contentops = $gCms->GetContentOperations();
	      $contentops->SetAllHierarchyPositions();
	      if ($submit)
		{
		  // put mention into the admin log
		  audit($contentobj->Id(), 'Content Item: '.$contentobj->Name(), 'Added');
		  redirect('listcontent.php'.$urlext.'&message=contentadded');
		}
	    }
	}
      else 
	{
	  $contentobj->FillParams($_POST);
	}
    }
    catch( CmsEditContentException $e)
    {
      $error = $e->getMessage();
    }
  }


if (!$access)
{
  echo "<div class=\"pageerrorcontainer pageoverflow\"><p class=\"pageerror\">".lang('noaccessto',array(lang('addcontent')))."</p></div>";
}
else
{
  $tabnames = $contentobj->TabNames();

  // Get a list of content_types and build the dropdown to select one
  $typesdropdown = '<select name="content_type" onchange="document.Edit_Content.submit()" class="standard">';
  $cur_content_type = '';
  $content_types = $contentops->ListContentTypes(false,true);
  foreach ($content_types as $onetype => $onetypename)
    {
      if( $onetype == 'errorpage' && !check_permission($userid,'Manage All Content') ) 
	{
	  continue;
	}
      $typesdropdown .= '<option value="' . $onetype . '"';
      if ($onetype == $content_type)
	{
	  $typesdropdown .= ' selected="selected" ';
	  $cur_content_type = $onetype;
	}
      $typesdropdown .= ">".$onetypename."</option>";
    }
  $typesdropdown .= "</select>";
  cms_utils::set_app_data('editing_content',$contentobj);

if( empty($error) && $contentobj->GetError() )
{
  $error = $contentobj->GetError();
}
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

if ($contentobj->HasPreview())
		{
			echo '<div id="edittabpreview"'.($tmpfname!=''?' class="active"':'').' onclick="##INLINESUBMITSTUFFGOESHERE##xajax_ajaxpreview(xajax.getFormValues(\'Edit_Content\'));return false;">'.lang('preview').'</div>';
		}
		?>
	</div>
	<div style="clear: both;"></div>
	<form method="post" action="addcontent.php<?php echo $urlext ?>" name="Edit_Content" enctype="multipart/form-data" id="Edit_Content"##FORMSUBMITSTUFFGOESHERE##>
        <div class="hidden">
	<input type="hidden" id="serialized_content" name="serialized_content" value="<?php echo SerializeObject($contentobj); ?>" />
        </div>
	<div id="page_content">
		<div class="pageoverflow">	
		<?php
		
		$submit_buttons = '
			<div class="pagetext">&nbsp;</div>
			<div class="pageinput">';
		$submit_buttons .= ' <input type="submit" name="submitbutton" value="'.lang('submit').'" class="pagebutton" />';
		$submit_buttons .= ' <input type="submit" name="cancel" value="'.lang('cancel').'" class="pagebutton" /></div>';
		
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
 					<div class="pageinput"><?php echo $contentarray[$i][1]; if( isset($contentarray[$i][2]) ) { echo '<br/>'.$contentarray[$i][2]; } ?></div>
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
if ($contentobj->HasPreview())
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
