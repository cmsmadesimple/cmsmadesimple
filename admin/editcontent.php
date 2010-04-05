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
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'cmsms.api.php');

$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();

if (isset($_POST["cancel"]))
{
	redirect("listcontent.php".$urlext);
}

include_once("../lib/classes/class.admintheme.inc.php");
require_once(dirname(__FILE__).'/editcontent_extra.php');

$cms_ajax = new CmsAjax();
$cms_ajax->register_function('ajaxpreview');
$cms_ajax->register_function('editcontent_apply');

$headtext = $cms_ajax->get_javascript();

function check_editcontent_perms($page_id,$adminonly = false)
{
  $userid = get_userid();
  $access = check_ownership($userid, $page_id) || check_permission($userid, 'Modify Any Page') ||
    check_permission($userid, 'Manage All Content');
  if( $adminonly ) return $access;
  if (!$access)
    {
      $access = check_authorship($userid, $page_id);
    }
  return $access;
}

function do_save_content($editor,$data)
{
  $editor->fill_from_form_data($data);
  $error = $editor->validate();

  if( $error === FALSE )
    {
      $editor->save();
    }

  return $error;
}

function editcontent_apply($params)
{
  $page_id = $_REQUEST['page_id'];
  $resp = new CmsAjaxResponse();
  $resp->script("jQuery('.applybutton').attr('disabled','');");
  if( check_editcontent_perms($page_id) )
    {
      $gCms = cmsms();
      $contentops =& $gCms->GetContentOperations();

      $contentobj = $contentops->LoadContentFromId($page_id);
      $editortype = $contentops->get_content_editor_type($contentobj);
      $editor = new $editortype($contentobj);
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


$userid = get_userid();
$gCms = cmsms();
$contentops =& $gCms->GetContentOperations();
$error = FALSE;
$page_id = "";
if (isset($_REQUEST["page_id"])) $page_id = $_REQUEST["page_id"];
$submit = false;
if (isset($_POST["submitbutton"])) $submit = true;
$apply = false;
if (isset($_POST["applybutton"])) $apply = true;

if( !check_editcontent_perms($page_id) )
  {
    echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto',array(lang('editpage')))."</p></div>";
    echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

    include_once("footer.php");
    return;
  }


//
// GET THE CONTENT OBJECT AND INSTANTIATE THE EDITOR
//
$editor = '';
$content_type = 'Content';
$orig_content_type = 'Content';
$contentobj = '';
if( isset($_POST['serialized_content']) )
  {
    // we're here probably because of a submit, apply, template
    // or content type change.
    if (isset($_POST["content_type"]))
      {
	$content_type = $_POST["content_type"];
      }
    if( isset($params['orig_content_type']) )
      {
	$orig_content_type = $params['orig_content_type'];
      }

    CmsContentOperations::load_content_types();
    $contentobj = UnserializeObject($_POST['serialized_content']);
    if( get_class($contentobj) != $content_type )
      {
	// content type has changed.
	copycontentobj($contentobj,$content_type);
      }
    else
      {
	$editortype = $contentops->get_content_editor_type($contentobj);
	$editor = new $editortype($contentobj);
	$editor->fill_from_form_data($_POST);
      }
  }
else if($page_id)
  {
    // we're loading this content from scratch.
    $contentobj = $contentops->LoadContentFromId($page_id);
    if( !is_object($contentobj) ) die("debug... wheres the content for $page_id");
    $content_type = get_class($contentobj);
    $orig_content_type = $content_type;
  }
else
  {
    // we're creating a new content object
    CmsContentOperations::load_content_types();
    $contentobj = new $content_type();
    $contentobj->set_cachable(get_site_preference('page_cachable',1));
    $contentobj->set_active(get_site_preference('page_active',1));
    $contentobj->set_show_in_menu(get_site_preference('page_showinmenu',1));
    $contentobj->set_owner($userid);
    $contentobj->set_last_modified_by($userid);
    $contentobj->set_metadata(get_site_preference('page_metadata'));
    $contentobj->set_secure(get_site_preference('page_secure',0));

    $contentobj->set_property_value('content_en',get_site_preference('defaultpagecontent'));
    $contentobj->set_property_value('searchable',get_site_preference('page_searchable',1));
    $contentobj->set_property_value('extra1',get_site_preference('page_extra1'));
    $contentobj->set_property_value('extra2',get_site_preference('page_extra2'));
    $contentobj->set_property_value('extra3',get_site_preference('page_extra3'));

    $parent_id = get_preference($userid, 'default_parent', -1);
    if (isset($_GET["parent_id"])) $parent_id = $_GET["parent_id"];
    $contentobj->set_parent_id($parent_id);

    {
      $templateops =& $gCms->GetTemplateOperations();
      $dflt = $templateops->LoadDefaultTemplate();
      if( isset($dflt) )
	{
	  $contentobj->set_template_id($dflt->id);
	}
    }

    
  }

if( !is_object($editor) )
  {
    // make sure we have a valid editor object going forward.
    $editortype = $contentops->get_content_editor_type($contentobj);
    $editor = new $editortype($contentobj);
  }

//
// HANDLE SUBMIT OR APPLY
//
if ($submit || $apply)
  {
    $error = do_save_content($editor,$_POST);
    
    if ($error === FALSE)
      {
	if ($submit)
	  {
	    redirect("listcontent.php".$urlext.'&message=contentupdated');
	  }
      }
  }


//
// BEGIN THE CODE TO GENERATE THE PAGE
//
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
   jQuery('.applybutton').click(function(){

      // code to get text from wysiwyg editor
      $addlScriptSubmit
      
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
$themeObject = $gCms->variables['admintheme'];

// AJAX result container
print '<div id="Edit_Content_Result"></div>';

$tmpfname = '';
$tabnames = '';

if (!check_editcontent_perms($page_id))
{
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto',array(lang('editpage')))."</p></div>";
}
else
{
	#Get a list of content_types and build the dropdown to select one
	$typesdropdown = '<select name="content_type" onchange="document.contentform.submit()" class="standard">';
	$contenttypes = CmsContentOperations::list_content_types();
	foreach ($contenttypes as $onetype => $label)
	{
	  if( $onetype == 'ErrorPage' && !check_permission($userid,'Manage All Content') ) 
	    {
	      continue;
	    }

	  $typesdropdown .= '<option value="' . $onetype . '"';
	  if ($onetype == $content_type)
	    {
	      $typesdropdown .= ' selected="selected" ';
	      $orig_content_type = $onetype;
	    }
	  $typesdropdown .= ">".$label."</option>";
	}
	$typesdropdown .= "</select>";

	if (FALSE == empty($error))
	{
	  echo $themeObject->ShowErrors($error);
	}

	echo '<div class="pagecontainer pageoverflow">'."\n";
	echo $themeObject->ShowHeader('editcontent');
	echo '<div id="page_tabs" style="width:100%;">'."\n";

	$tabnames = $editor->get_tab_names();
	if (count($tabnames) == 0)
	  {
	    // this is a problem.
	  }
	else
	  {
	    foreach ($tabnames as $onetab => $label)
	      {
		echo '<div id="edittab'.$onetab.'">'.$label.'</div>'."\n";
	      }
	  }
	
	// Make a preview tab
	if ($contentobj->is_previewable())
	  {
	    echo '<div id="edittabpreview"'.($tmpfname!=''?' class="active"':'').' onclick="##INLINESUBMITSTUFFGOESHERE##cms_ajax_ajaxpreview(jQuery(\'#contentform\').serializeForCmsAjax()); return false;">'.lang('preview').'</div>';
	  }

	$turl = 'editcontent.php';
	if(isset($page_id)) $turl .= "?page_id=$page_id";
		?>
	</div>
	<div style="clear: both;"></div>
	<form method="post" action="<?php echo $turl; ?>" enctype="multipart/form-data" name="contentform" id="contentform"##FORMSUBMITSTUFFGOESHERE##>
<div>
  <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" />
  <input type="hidden" id="serialized_content" name="serialized_content" value="<?php echo SerializeObject($contentobj); ?>" />
  <input type="hidden" name="page_id" value="<?php echo $page_id?>" />
  <input type="hidden" name="orig_content_type" value="<?php echo $orig_content_type ?>" />
</div>
<div id="page_content">
<?php
$submit_buttons = '<div class="pageoverflow">
<p class="pagetext">&nbsp;</p>
<p class="pageinput">
 <input type="submit" name="submitbutton" accesskey="s" value="'.lang('submit').'" class="pagebutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" title="'.lang('submitdescription').'" />';
$submit_buttons .= ' <input type="submit" accesskey="c" name="cancel" value="'.lang('cancel').'" class="pagebutton" onclick="return confirm(\''.lang('confirmcancel').'\');" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" title="'.lang('canceldescription').'" />';
$submit_buttons .= ' <input type="submit" accesskey="a" name="applybutton" value="'.lang('apply').'" class="pagebutton applybutton" onmouseover="this.className=\'pagebuttonhover\'" onmouseout="this.className=\'pagebutton\'" title="'.lang('applydescription').'" />';
if( $contentobj->is_viewable() && $contentobj->active() ) {
  $submit_buttons .= ' <a rel="external" href="'.$contentobj->get_url().'">'.$themeObject->DisplayImage('icons/system/view.gif',lang('view_page'),'','','systemicon').'</a>';
 }
$submit_buttons .= '</p></div>';

 $tabindex = 0;
 foreach( $tabnames as $onetab => $label )
   {
     echo '<div id="edittab'.$onetab.'_c">'."\n";
     if ($tabindex == 0)
       {
	 echo $submit_buttons;
	 
	 ?>
	   <div class="pageoverflow">
           <div class="pagetext"><?php echo lang('contenttype'); ?>:</div>
	   <div class="pageinput"><?php echo $typesdropdown; ?></div>
	   </div>
 	 <?php
       }
     $tabindex++;

     $contentarray = $editor->get_tab_elements($onetab);
     
     for($i=0;$i<count($contentarray);$i++)
       {
	 $tmp =& $contentarray[$i];
	 ?>
	   <div class="pageoverflow">
	      <div class="pagetext"><?php echo $tmp[0]; ?></div>
	      <div class="pageinput"><?php echo $tmp[1]; ?></div>
           </div>
	 <div style="clear: both;"></div>
	 <?php
       }

     echo '</div>'."\n";
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
