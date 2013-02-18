<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CMSContentManager (c) 2013 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  A module for managing content in CMSMS.
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin 
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE
if( !isset($gCms) ) exit;

$this->SetCurrentTab('pages');
if( isset($params['cancel']) ) {
  unset($_SESSION['__cms_copy_obj__']);
  $this->RedirectToAdminTab();
}

//
// init
//
$this->SetCurrentTab('pages');
$user_id = get_userid();
$content_id = null;
$content_obj = null;
$pagedefaults = CmsContentManagerUtils::get_pagedefaults();
$content_type = $pagedefaults['contenttype'];
$error = null;

if( isset($params['cancel']) ) {
  $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab();
}

if( isset($params['content_id']) ) $content_id = (int)$params['content_id'];

if( !$this->CanEditContent($content_id) ) {
  // nope, can't edit this page anyways.
  $this->SetError($this->Lang('error_editpage_permission'));
  $this->RedirectToAdminTab();
}

// Get a list of content types and pick a default if necessary
$gCms = cmsms();
$contentops = $gCms->GetContentOperations();
$existingtypes = $contentops->ListContentTypes(false,true);

//
// load or create the initial content object
//
try {
  if( $content_id == 'copy' && isset($_SESSION['__cms_copy_obj__']) ) {
    // we're copying a content object.
    $content_obj = unserialize($_SESSION['__cms_copy_obj__']);
  }
  else if( $content_id < 1 ) {
    // creating a new content object
    if( isset($params['content_type']) ) $content_type = trim($params['content_type']);
    $content_obj = $contentops->CreateNewContent($content_type);
    $content_obj->SetOwner($user_id);
    $content_obj->SetLastModifiedBy($user_id);
    $content_obj->SetActive($pagedefaults['active']);
    $content_obj->SetSecure($pagedefaults['secure']);
    $content_obj->SetCachable($pagedefaults['cachable']);
    $content_obj->SetShowInMenu($pagedefaults['showinmenu']);
    $content_obj->SetPropertyValue('design_id',$pagedefaults['design_id']);
    $content_obj->SetTemplateId($pagedefaults['template_id']);
    $content_obj->SetPropertyValue('searchable',$pagedefaults['searchable']);
    $content_obj->SetPropertyValue('content_en',$pagedefaults['content']);
    $content_obj->SetPropertyValue('pagemetadata',$pagedefaults['metadata']);
    $content_obj->SetPropertyValue('extra1',$pagedefaults['extra1']);
    $content_obj->SetPropertyValue('extra2',$pagedefaults['extra2']);
    $content_obj->SetPropertyValue('extra3',$pagedefaults['extra3']);
    $content_obj->SetAdditionalEditors($pagedefaults['addteditors']);
  }
  else {
    // editint an existing content object
    $content_obj = $contentops->LoadContentFromId($content_id);
    $content_type = $content_obj->Type();
    if( isset($params['content_type']) ) $content_type = trim($params['content_type']);
  }

  // validate the content type.
  if( is_array($existingtypes) && count($existingtypes) > 0 && !in_array($content_type,array_keys($existingtypes)) ) {
    $this->SetError($this->Lang('error_editpage_contenttype'));
    $this->RedirectToAdminTab();
  }
}
catch( Exception $e ) {
  $this->SetError($e->getMessage());
  $this->RedirectToAdminTab();
}

//
// handle changing content types
// or a POST
//
try {
  if( $content_id != -1 && $content_type != $content_obj->Type() ) {
    // content type changed. create a new content object, but preserve the id.
    $tmpobj = $contentops->CreateNewContent($content_type);
    $tmpobj->SetId($contentobj->Id());
    $tmpobj->SetName($contentobj->Name());
    $tmpobj->SetMenuText($contentobj->MenuText());
    $tmpobj->SetTemplateId($contentobj->TemplateId());
    $tmpobj->SetParentId($contentobj->ParentId());
    $tmpobj->SetAlias($contentobj->Alias());
    $tmpobj->SetOwner($contentobj->Owner());
    $tmpobj->SetActive($contentobj->Active());
    $tmpobj->SetItemOrder($contentobj->ItemOrder());
    $tmpobj->SetShowInMenu($contentobj->ShowInMenu());
    $tmpobj->SetCachable($contentobj->Cachable());
    $tmpobj->SetHierarchy($contentobj->Hierarchy());
    $tmpobj->SetLastModifiedBy($contentobj->LastModifiedBy());
    $tmpobj->SetAdditionalEditors($contentobj->GetAdditionalEditors());
    $tmpobj->Properties();
    $content_obj = $tmpobj;
  }

  if( strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' ) {
    // if we're in a POST action, another item may have changed that requires reloading the page
    // filling the params will make sure that no edited content was lost.
    $content_obj->FillParams($_POST,($content_id < 1));
  }
  
  if( isset($params['submit']) || isset($params['apply']) || isset($params['preview']) ) {
    $error = $content_obj->ValidateData();

    if( $error ) {
      if( isset($params['ajax']) ) {
	$tmp = array('response'=>'Error','details'=>$error);
	echo json_encode($tmp);
	exit;
      }
      // error, but no ajax... fall through
    }
    else if( isset($params['submit']) || isset($params['apply']) ) {
      $content_obj->SetLastModifiedBy(get_userid());
      $content_obj->Save();
      $contentops->SetAllHierarchyPositions();
      unset($_SESSION['__cms_copy_obj__']);
      audit($content_obj->Id(),'Content Item: '.$content_obj->Name(),' Edited');
      if( isset($params['submit']) ) {
	$this->SetMessage($this->Lang('msg_editpage_success'));
	$this->RedirectToAdminTab();
      }

      if( isset($params['ajax']) ) {
	$tmp = array('response'=>'Success','details'=>$this->Lang('msg_editpage_success'));
	echo json_encode($tmp);
	exit;
      }
    }
    else if( isset($params['preview']) && $content_obj->HasPreview() ) {
      debug_to_log($content_obj->GetPropertyValue('content_en','content_en'));
      $_SESSION['__cms_preview__'] = serialize($content_obj);
      $_SESSION['__cms_preview_type__'] = $content_type;
      exit;
    }
  }
}
catch( CmsEditContentException $e ) {
  $this->SetError($e->getMessage());
  $this->RedirectToAdminTab();
}

//
// BUILD THE DISPLAY
//
if( $error ) echo $this->ShowErrors($error);

$tabnames = $content_obj->GetTabNames();
$numberoftabs = count($tabnames);
$tab_contents_array = array();
$tab_message_array = array();
for( $currenttab = 0; $currenttab < $numberoftabs; $currenttab++ ) {
  $tmp = $content_obj->GetTabMessage($currenttab);
  if( $tmp ) {
    $tab_message_array[$currenttab] = $tmp;
  }
  
  $contentarray = $content_obj->GetTabElements($currenttab);
  if( $currenttab == 0 ) {
    // first tab... add the content type selector.
    $tmp = array('<label for="content_type">'.$this->Lang('prompt_editpage_contenttype').'</label>');
    $tmp2 = "<select id=\"content_type\" name=\"{$id}content_type\">";
    foreach( $existingtypes as $k => $v ) {
      if( $k == 'errorpage' && !$this->CheckPermission('Manage All Content') ) {
	// this is ugly... we should know if the type is a system type.
	continue;
      }
      $tmp2 .= CmsFormUtils::create_option($k,$v,$content_type);  // todo: add title here.
    }
    $tmp2 .= '</select>';
    $tmp[] = $tmp2;

    $contentarray = array_merge(array($tmp),$contentarray);
  }
  $tab_contents_array[$currenttab] = $contentarray;
}

// give stuff to smarty.
if( $content_obj->HasPreview() ) {
  $config = cmsms()->GetConfig();
  $smarty->assign('has_preview',1);
  $smarty->assign('preview_url',"{$config['root_url']}/index.php?{$config['query_var']}=__CMS_PREVIEW_PAGE__");
}
$smarty->assign('content_id',$content_id);
$smarty->assign('content_obj',$content_obj);
$smarty->assign('tab_names',$tabnames);
$smarty->assign('tab_contents_array',$tab_contents_array);
$smarty->assign('tab_message_array',$tab_message_array);
$modobj = cms_utils::get_wysiwyg_module();
if( is_object($modobj) ) {
  $smarty->assign('wysiwyg_submit_script',$modobj->WYSIWYGPageFormSubmit());
}

echo $this->ProcessTemplate('admin_editcontent.tpl');

#
# EOF
#
?>