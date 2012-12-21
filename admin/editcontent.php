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

include_once("../lib/classes/class.admintheme.inc.php");

$dateformat = get_preference(get_userid(),'date_format_string','%x %X');

require_once(dirname(__FILE__).'/editcontent_extra.php');

if (isset($_POST["cancel"])) {
  redirect("listcontent.php".$urlext);
}

$error = FALSE;
$content_id = "";
$pagelist_id = "1";
$submit = false;
$apply = false;
$preview = false;
$ajax = false;

if (isset($_REQUEST["content_id"])) $content_id = $_REQUEST["content_id"];
if (isset($_REQUEST["page"])) $pagelist_id = $_REQUEST["page"];
if (isset($_POST["submitbutton"])) $submit = true;
if (isset($_POST["apply"])) $apply = true;
if (isset($_POST['preview'])) $preview = 1;
if (isset($_POST['ajax']) && $_POST['ajax']) $ajax = true;

#Get a list of content types and pick a default if necessary
$gCms = cmsms();
$contentops = $gCms->GetContentOperations();
$existingtypes = $contentops->ListContentTypes(false,true);

#Get current userid and make sure they have permission to add something
$userid = get_userid();
$access = check_ownership($userid, $content_id) || 
  check_permission($userid, 'Modify Any Page') ||
  check_permission($userid, 'Manage All Content');
$adminaccess = $access;
if (!$access) {
  $access = check_authorship($userid, $content_id);
}

if( !$access ) {
  die('Permission denied');
}

// get the content object.
$contentobj = '';
$content_type = 'content'; // default content type.
if( $content_id < 1 ) {
  // create a new content object.
  $page_secure = get_site_preference('page_secure',0);
  $page_cachable = ((get_site_preference('page_cachable',"1")=="1")?true:false);
  $active = ((get_site_preference('page_active',"1")=="1")?true:false);
  $showinmenu = ((get_site_preference('page_showinmenu',"1")=="1")?true:false);
  $metadata = get_site_preference('page_metadata');
  $design_id = get_site_preference('page_design',-1);
  $parent_id = get_preference($userid, 'default_parent', -2);

  $contentobj = $contentops->CreateNewContent($content_type);
  $contentobj->SetOwner($userid);
  $contentobj->SetCachable($page_cachable);
  $contentobj->SetActive($active);
  $contentobj->SetShowInMenu($showinmenu);
  $contentobj->SetLastModifiedBy($userid);

  {
    $design = CmsLayoutCollection::load_default();
    if( $design ) {
      $contentobj->SetPropertyValue('design_id',$design->get_id());
    }
    $tpl = CmsLayoutTemplate::load_dflt_by_type('__CORE__::page');
    if( $tpl ) {
      $contentobj->SetTemplateId($tpl->get_id());
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
  if( !empty($tmp) ) {
    $tmp2 = explode(',',$tmp);
  }
  $contentobj->SetAdditionalEditors($tmp2);
}
else {
  // load the content object from the database.
  $contentobj = $contentops->LoadContentFromId($content_id);
  $content_type = $contentobj->Type();
}

if( isset($_POST['content_type']) ) {
  $content_type = $_POST['content_type'];
}

// validate the content type we want...
if( isset($existingtypes) && count($existingtypes) > 0 && 
    in_array($content_type,array_keys($existingtypes)) ) {
  // woot, it's a valid content type
}
else {
  redirect("listcontent.php".$urlext."&page=".$pagelist_id.'&error=error_contenttype');
}

try {
  if( $content_id != -1 && strtolower(get_class($contentobj)) != strtolower($content_type) ) {
    // content type change...
    // this also updates the content object with the POST params.
    copycontentobj($contentobj, $content_type);
  }
  else if( strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' ) {
    // we posted... so update the content object.
    updatecontentobj($contentobj);
  }

  cms_utils::set_app_data('editing_content',$contentobj);
}
catch( CmsEditContentException $e ) {
  $error = $e->getMessage();
}

if( $ajax && $preview ) {
  $contentobj->FillParams($_POST,TRUE);
  $_SESSION['__cms_preview__'] = serialize($contentobj);
  $_SESSION['__cms_preview_type__'] = get_class($contentobj);
  exit;
}

if ($submit || $apply) {
  try {
    // Fill contentobj with parameters
    // $contentobj->SetProperties();  // calguy should not be necessary
    $contentobj->FillParams($_POST,true);
    $error = $contentobj->ValidateData();

    if ($error === FALSE) {
      $contentobj->SetLastModifiedBy(get_userid());
      $contentobj->Save();
      $contentops->SetAllHierarchyPositions();
      // put mention into the admin log
      audit($contentobj->Id(), 'Content Item: '.$contentobj->Name(), 'Edited');
      if ($submit) {
	redirect("listcontent.php".$urlext."&page=".$pagelist_id.'&message=contentupdated');
      }
    }
  }
  catch( CmsEditContentException $e ) {
    $error = $e->getMessage();
  }

  if ($ajax) {
    // todo: ajax results.
    $tmp = array('response'=>'Success','details'=>lang('contentupdated'));
    echo json_encode($tmp);
    exit;
  }
}

//
// BUILD THE DISPLAY
//

include_once("header.php");

$tmpfname = '';

#Get a list of content_types and build the dropdown to select one
$typesdropdown = '<select name="content_type" id="content_type" onchange="document.Edit_Content.submit()" class="standard">';
$cur_content_type = '';
foreach ($existingtypes as $onetype => $onetypename ) {
  if( $onetype == 'errorpage' && !check_permission($userid,'Manage All Content') ) {
    continue;
  }

  $typesdropdown .= '<option value="' . $onetype . '"';
  if ($onetype == $content_type) {
    $typesdropdown .= ' selected="selected" ';
    $cur_content_type = $onetype;
  }
  $typesdropdown .= ">".$onetypename."</option>";
}
$typesdropdown .= "</select>";

$tabnames = $contentobj->TabNames();
if (FALSE == empty($error)) {
  echo $themeObject->ShowErrors($error);
}

$numberoftabs = count($tabnames);
$tab_contents_array = array();
for ($currenttab = 0; $currenttab < $numberoftabs; $currenttab++) {
  $contentarray = $contentobj->EditAsArray(false, $currenttab, $adminaccess);
  $tab_contents_array[$currenttab] = $contentarray;
}

// give stuff to smarty
$config = cmsms()->GetConfig();
if( in_array('preview',$tabnames) ) $smarty->assign('has_preview',1);
$smarty->assign('content_id',$content_id);
$smarty->assign('page',$pagelist_id);
$smarty->assign('cur_content_type',$cur_content_type);
$smarty->assign('content_obj',$contentobj);
$smarty->assign('tabnames',$tabnames);
$smarty->assign('tab_contents_array',$tab_contents_array);
$modobj = cms_utils::get_wysiwyg_module();
if( $modobj ) {
  $smarty->assign('wysiwyg_submit_script',$modobj->WYSIWYGPageFormSubmit());
}
$smarty->assign('preview_url',"{$config['root_url']}/index.php?{$config['query_var']}=__CMS_PREVIEW_PAGE__");

echo $smarty->fetch('editcontent.tpl');

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
