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
#$Id: editcontent.php 4931 2008-08-08 19:00:07Z calguy1000 $

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();
$userid = get_userid();

include_once("header.php");

// Get the source object
$fromid = (int)$_GET['content_id'];
$gCms = cmsms();
$contentops = $gCms->GetContentOperations();
$config = $gCms->GetConfig();
$fromobj = $contentops->LoadContentFromId($fromid,true);
$fromobj->GetAdditionalEditors();
$fromobj->Properties();
$parentobj = $contentops->LoadContentFromId($fromobj->ParentId());

// handle form submission
if( isset($_GET['cancel']) )
{
   redirect('listcontent.php'.$urlext);
}
if( isset($_GET['submit']) )
{
  $to_alias = '';
  if( isset($_GET['to_alias']) ) 
    {
      $to_alias = trim($_GET['to_alias']);
    }
  $to_title = '';
  if( isset($_GET['to_title']) ) 
    {
      $to_title = trim($_GET['to_title']);
    }
  $to_menutext = '';
  if( isset($_GET['to_menutext']) ) 
    {
      $to_menutext = trim($_GET['to_menutext']);
    }
  $to_parentid = '';
  if( isset($_GET['to_parentid']) ) 
    {
      $to_parentid = (int)$_GET['to_parentid'];
    }
  $to_accesskey = '';
  if( isset($_GET['to_accesskey']) ) 
    {
      $to_accesskey = trim($_GET['to_accesskey']);
    }

  //
  // Now do the copy
  //
  $tmpobj = clone $fromobj;

  // Stuff that needs to be changed
  $tmpobj->SetURL('');
  $tmpobj->SetName($to_title);
  $tmpobj->SetMenuText($to_menutext);
  $tmpobj->SetAlias($to_alias);
  //$tmpobj->mOldAlias = ''; // no method for this.
  $tmpobj->SetParentId($to_parentid);
  $tmpobj->SetOldParentId($to_parentid);
  $tmpobj->SetAccessKey($to_accesskey);
  
  $tmpobj->SetDefaultContent(0);
  $tmpobj->SetOwner(get_userid());
  $tmpobj->SetLastModifiedBy(get_userid());

  // This shouldn't be needed because the object was copied
  $tmpobj->SetShowInMenu($fromobj->ShowInMenu());
  $tmpobj->SetAdditionalEditors($fromobj->GetAdditionalEditors());
  $tmpobj->SetActive($fromobj->Active());

  // Now make sure everything is okay, and move forward.
  $res = $tmpobj->ValidateData();
  if( $res === FALSE )
    {
      // everything is okay... save it
      // and make sure the hierarchy stuff works.
      $tmpobj->Save();
      $contentops->SetAllHierarchyPositions();

      // put mention into the admin log
      audit($fromobj->Id(), 'Content Item: '.$fromobj->Alias(), 'Copied to: '.$tmpobj->Alias());

      // and redirect
      redirect('listcontent.php'.$urlext);
    }
  else
    {
      echo $themeObject->ShowErrors($res);
    }
}

// and give it to smarty
$smarty->assign('fromid',$fromid);
$smarty->assign('fromobj',$fromobj);
if( is_object($parentobj) )
{
  $smarty->assign('parentinfo',
    sprintf("%s (%s - %d)",$parentobj->Name(),$parentobj->Alias(),$parentobj->Id()));
}
else
{
  $smarty->assign('parentinfo',lang('none'));
}

// build the output
$smarty->assign('cms_secure_param_name',CMS_SECURE_PARAM_NAME);
$smarty->assign('cms_user_key',$_SESSION[CMS_USER_KEY]);
$smarty->assign('showheader', $themeObject->ShowHeader('copycontent'));
$smarty->assign('lang_pageid',lang('itemid'));
$smarty->assign('lang_copyfrom',lang('copy_from'));
$smarty->assign('lang_copyto',lang('copy_to'));
$smarty->assign('lang_pagetype',lang('type'));
$smarty->assign('lang_pagetitle',lang('title'));
$smarty->assign('lang_pagealias',lang('pagealias'));
$smarty->assign('lang_pageparent',lang('parent'));
$smarty->assign('lang_pagemenutext',lang('menutext'));
$smarty->assign('lang_submit',lang('submit'));
$smarty->assign('lang_cancel',lang('cancel'));

if( check_permission(get_userid(),'Manage All Content') )
  {
    $smarty->assign('lang_pageaccesskey',lang('accesskey'));
  }

$tmp = 	$contentops->CreateHierarchyDropdown(-100, $fromobj->ParentId(), 'to_parentid',1,1,1,true);
if( empty($tmp) )
  {
    $tmp = '<input type="hidden" name="to_parentid" value="'.$fromobj->Id().'"/>'.$fromobj->Hierarchy().'&nbsp;'.$fromobj->Name();
  }
$smarty->assign('input_parentdropdown',$tmp);

$basic_attributes = explode(',',get_site_preference('basic_attributes','template'));
if( in_array('alias',$basic_attributes) || ($config['auto_alias_content'] != true) ||
    check_permission($userid, 'Manage All Content'))
  {
    $smarty->assign('info_pagealias',lang('info_pagealias'));
    if( $config['auto_alias_content'] == true )
      {
	$smarty->assign('info_alias',lang('info_autoalias'));
      }
  }

echo $smarty->fetch('copycontent.tpl');

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
