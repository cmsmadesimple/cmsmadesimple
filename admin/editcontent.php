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

if (isset($_POST["cancel"]))
{
	redirect("listcontent.php".$urlext);
}

include_once("../lib/classes/class.admintheme.inc.php");

$dateformat = get_preference(get_userid(),'date_format_string','%x %X');

require_once(dirname(__FILE__).'/editcontent_extra.php');

$cms_ajax = new CmsAjax();
$cms_ajax->register_function('ajaxpreview');
$cms_ajax->register_function('editcontent_apply');

$headtext = $cms_ajax->get_javascript();

function check_editcontent_perms($content_id,$adminonly = false)
{
  $userid = get_userid();
  $access = check_ownership($userid, $content_id) || check_permission($userid, 'Modify Any Page') ||
    check_permission($userid, 'Manage All Content');
  if( $adminonly ) return $access;
  if (!$access)
    {
      $access = check_authorship($userid, $content_id);
    }
  return $access;
}

function do_save_content($editor,$data)
{
  $editor->fill_from_form_data($data);
  $error = $editor->validate();

  if( $error == FALSE )
    {
      $editor->save();
    }

  return $error;
}

function editcontent_apply($params)
{
  $content_id = $_REQUEST['content_id'];
  $resp = new CmsAjaxResponse();
  $resp->script("jQuery('#applybutton').attr('disabled','');");
  if( check_editcontent_perms($content_id) )
    {
      $gCms = cmsms();
      $contentops =& $gCms->GetContentOperations();

      $contentobj = $contentops->LoadContentFromId($content_id);
      $editortype = $contentops->get_content_editor_type($contentobj);
      $editor = new $editortype($contentobj);

      $contentobj = $contentops->LoadContentFromId($content_id);
      $error = do_save_content($editor,$params);

      if( $error === FALSE )
        {
	  $str = '<div class="pagemcontainer"><ul class="pagemessage">'.lang('contentupdated').'</p></div>';
	  $resp->replace_html('#Edit_Content_Result',$str);
	}
      else
	{
	  $list = '<li>'.explode('</li><li>',$error).'</li>';
	  $str = '<div class="pageerrorcontainer"><ul class="pageerror">'.$list.'</ul></div>';
	  $resp->replace_html('#Edit_Content_Result',$str);
	}
    }
  return $resp->get_result();
}


$error = FALSE;

$content_id = "";
if (isset($_REQUEST["content_id"])) $content_id = $_REQUEST["content_id"];

$pagelist_id = "1";
if (isset($_REQUEST["page"])) $pagelist_id = $_REQUEST["page"];

$submit = false;
if (isset($_POST["submitbutton"])) $submit = true;

$apply = false;
if (isset($_POST["applybutton"])) $apply = true;

#Get a list of content types and pick a default if necessary
$gCms = cmsms();
$contentops =& $gCms->GetContentOperations();
$content_type = "";
if (isset($_POST["content_type"]))
{
	$content_type = $_POST["content_type"];
}
else
{
    $existingtypes = $contentops->ListContentTypes();
	if (isset($existingtypes) && count($existingtypes) > 0)
	{
		$content_type = 'content';
	}
	else
	{
		$error = "<p>No content types loaded!</p>";	
	}
}


# Get the content object, and instantiate the ditor.
$contentobj = $contentops->LoadContentFromId($content_id);

if (check_editcontent_perms($content_id))
{
  if ($submit || $apply)
    {
      $error = do_save_content($editor,$_POST);
      
      if ($error === FALSE)
	{
	  if ($submit)
	    {
	      redirect("listcontent.php".$urlext."&page=".$pagelist_id.'&message=contentupdated');
	    }
	}
    }
  else if (isset($_POST["serialized_content"]))
    {
      // content type changed.
      global $gCms;
      $contentops =& $gCms->GetContentOperations();
      $contentops->load_content_type($_POST['orig_content_type']);

      $contentobj = UnserializeObject($_POST["serialized_content"]);
      if (strtolower(get_class($contentobj)) != strtolower($content_type))
	{
	  // Fill up the existing object with values in form
	  // Create new object
	  // Copy important fields to new object
	  // Put new object on top of old one
	  copycontentobj($contentobj, $content_type);
 	}
    }
//   else if ($content_id != -1 || !is_object($contentobj) )
//     {
//       // loading content.
//       global $gCms;
//       $contentops =& $gCms->GetContentOperations();
//       $contentobj = $contentops->LoadContentFromId($content_id);
//       $content_type = $contentobj->yype();
//     }
//   else
//     {
//       die('unknwon condition');
//     }
}

$editortype = $contentops->get_content_editor_type($contentobj);
$editor = new $editortype($contentobj);
if (strlen($contentobj->name()) > 0)
{
	$CMS_ADMIN_SUBTITLE = $contentobj->name();
}

// Detect if a WYSIWYG is in use, and grab its form submit action
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

$headtext .= <<<EOSCRIPT
<script type="text/javascript">
// <![CDATA[
jQuery(document).ready(function(){
   jQuery('#applybutton').click(function(){

      jQuery('#Edit_Content_Result').html('');
      jQuery(this).attr('disabled','disabled');
      var data = jQuery('#contentform').serializeForCmsAjax();
      cms_ajax_editcontent_apply(data);
      return false;
   });
});
  // ]]>
</script>
EOSCRIPT;
include_once("header.php");
$cms_ajax->process_requests();

// AJAX result container
print '<div id="Edit_Content_Result"></div>';

$tmpfname = '';
$tabnames = '';

if (!check_editcontent_perms($content_id))
{
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto',array(lang('editpage')))."</p></div>";
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
	  $typesdropdown .= ">".($type_obj->friendly_name())."</option>";
	}
	$typesdropdown .= "</select>";

	if (FALSE == empty($error))
	{
	  echo $themeObject->ShowErrors($error);
	}

?>

<div class="pagecontainer pageoverflow">
	<?php
	echo $themeObject->ShowHeader('editcontent');
	?>
	<div id="page_tabs" style="width:100%;">
		<?php
		$count = 0;

	$tabnames = $editor->get_tab_names();
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
		if ($contentobj->is_previewable())
		{
			echo '<div id="edittabpreview"'.($tmpfname!=''?' class="active"':'').' onclick="##INLINESUBMITSTUFFGOESHERE##cms_ajax_ajaxpreview(jQuery(\'#contentform\').serializeForCmsAjax()); return false;">'.lang('preview').'</div>';
		}

		?>
	</div>
	<div style="clear: both;"></div>
	<form method="post" action="editcontent.php<?php if (isset($content_id) && isset($pagelist_id)) echo "?content_id=$content_id&amp;page=$pagelist_id";?>" enctype="multipart/form-data" name="contentform" id="contentform"##FORMSUBMITSTUFFGOESHERE##>
<div>
  <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" />
  <input type="hidden" id="serialized_content" name="serialized_content" value="<?php echo SerializeObject($contentobj); ?>" />
  <input type="hidden" name="content_id" value="<?php echo $content_id?>" />
  <input type="hidden" name="page" value="<?php echo $pagelist_id; ?>" />
  <input type="hidden" name="orig_content_type" value="<?php echo $cur_content_type ?>" />
</div>
<div id="page_content">
<?php
$submit_buttons = '<div class="pageoverflow">
<p class="pagetext">&nbsp;</p>
<p class="pageinput">
 <input type="submit" name="submitbutton" accesskey="s" value="'.lang('submit').'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" title="'.lang('submitdescription').'" />';
$submit_buttons .= ' <input type="submit" accesskey="c" name="cancel" value="'.lang('cancel').'" class="pagebutton" onclick="return confirm(\''.lang('confirmcancel').'\');" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" title="'.lang('canceldescription').'" />';
$submit_buttons .= ' <input type="submit" accesskey="a" id="applybutton" name="applybutton" value="'.lang('apply').'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" title="'.lang('applydescription').'" />';
 if( $contentobj->is_viewable() && $contentobj->active() ) {
   $submit_buttons .= ' <a rel="external" href="'.$contentobj->get_url().'">'.$themeObject->DisplayImage('icons/system/view.gif',lang('view_page'),'','','systemicon').'</a>';
 }
$submit_buttons .= '</p></div>';

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
					<div class="pagetext"><?php echo lang('contenttype'); ?>:</div>
					<div class="pageinput"><?php echo $typesdropdown; ?></div>
				</div>
				<?php
			}

			$contentarray = $editor->get_tab_elements($currenttab);

			for($i=0;$i<count($contentarray);$i++)
			{
			  $tmp =& $contentarray[$i];
				?>
				<div class="pageoverflow">
					<div class="pagetext"><?php echo $tmp[0]; ?></div>
					<div class="pageinput"><?php echo $tmp[1]; ?></div>
				</div>
				<?php
			}
            
			?>
			<div style="clear: both;"></div>
		</div>
		<?php
		}
		if ($contentobj->is_previewable())
		{
			echo '<div class="pageoverflow"><div id="edittabpreview_c"'.($tmpfname!=''?' class="active"':'').'>';
				?>
			  <div class="pagewarning"><?php echo lang('info_preview_notice') ?></div>
			  <iframe name="previewframe" class="preview" id="previewframe"<?php if ($tmpfname != '') { ?> src="<?php echo "{$config['root_url']}/index.php?{$config['query_var']}=__CMS_PREVIEW_PAGE__"; } ?>></iframe>
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
