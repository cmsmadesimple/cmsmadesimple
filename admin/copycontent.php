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
#$Id: editcontent.php 4931 2008-08-08 19:00:07Z calguy1000 $

$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();
$userid = get_userid();

include_once("header.php");

// Get the source object
$fromid = (int)$_GET['content_id'];
global $gCms;
$contentops =& $gCms->GetContentOperations();
$fromobj = $contentops->LoadContentFromId($fromid,true);
$fromobj->GetAdditionalEditors();
$parentobj = $contentops->LoadContentFromId($fromobj->ParentId());

// handle form submission
if( isset($_GET['cancel']) )
{
   redirect('listcontent.php');
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

  //
  // Now do the copy
  //
  $tmpobj = $fromobj;

  // trick some of the variables to handle
  // an insert properly.
  $tmpobj->SetId(-1); // force new object
  $tmpobj->SetItemOrder(-1);
  $tmpobj->SetOldItemOrder(-1);

  // Stuff that needs to be changed
  $tmpobj->SetAlias($to_alias);
  $tmpobj->mOldAlias = ''; // no method for this.
  $tmpobj->SetName($to_title);
  $tmpobj->SetParentId($to_parentid);
  $tmpobj->SetOldParentId($to_parentid);
  $tmpobj->SetMenuText($to_menutext);
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

      // something for the audit log
      audit($fromobj->Id(),$fromobj->Alias(),'Content Item Copied to '.$tmpobj->Alias());

      // and redirect
      redirect('listcontent.php');
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
$smarty->assign('input_parentdropdown',$contentops->CreateHierarchyDropdown($fromobj->Id(),$fromobj->ParentId(),'to_parentid'));

echo $smarty->fetch('copycontent.tpl');

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>