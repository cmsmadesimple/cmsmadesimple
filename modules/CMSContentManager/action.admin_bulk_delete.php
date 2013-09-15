<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Content (c) 2013 by Robert Campbell 
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

if( !isset($params['multicontent']) || !isset($params['action']) || $params['action'] != 'admin_bulk_delete' ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}

function cmscm_admin_bulk_delete_get_children($node)
{
  $out = array();

  if( $node->has_children() ) {
    $children = $node->get_children();
    foreach( $children as $child_node ) {
      $out[] = $child_node->get_tag('id');
      if( $child_node->has_children() ) {
	$out2 = cmscm_admin_bulk_delete_get_children($child_node);
	$out = array_merge($out,$out2);
      }
    }
  }

  return $out;
}

if( isset($params['cancel']) ) {
  $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab();
}
if( isset($params['submit']) ) {
//   if( !isset($params['deletecontent']) ) {
//     $this->SetError($this->Lang('error_missingparam'));
//     $this->RedirectToAdminTab();
//   }

  if( isset($params['confirm1']) && isset($params['confirm2']) && $params['confirm1'] == 1  && $params['confirm2'] == 1 ) {
    //
    // do the real work
    //
    $pagelist = unserialize(base64_decode($params['multicontent']));

    try {
      $contentops = ContentOperations::get_instance();
      $hm = cmsms()->GetHierarchyManager();
      foreach( $pagelist as $pid ) {
	$node = $contentops->quickfind_node_by_id($pid);
	if( !$node ) continue;
	$content = $node->getContent(FALSE,FALSE,TRUE);
	if( !is_object($content) ) continue;
	$content->Delete();
	$i++;
      }
      if( $i > 0 ) {
	$contentops->SetAllHierarchyPositions();
	$contentops->SetContentModified();
	audit('','Core','Deleted '.$i.' pages');
	$this->SetMessage($this->Lang('msg_bulk_successful'));
      }
    }
    catch( Exception $e ) {
      $this->SetError($e->GetMessage());
    }
    $this->RedirectToAdminTab();
  }
  else {
    $this->SetError($this->Lang('error_notconfirmed'));
    $this->RedirectToAdminTab();
  }
}


//
// expand $params['multicontent'] to also include children, place it in $pagelist
//
$multicontent = array();
if( $this->CheckPermission('Manage All Content') || $this->CheckPermission('Modify Any Page') ) {
  $multicontent = unserialize(base64_decode($params['multicontent']));
}
else {
  foreach( unserialize(base64_decode($params['multicontent'])) as $pid ) {
    if( !check_authorship(get_userid(),$pid) ) continue;
    $multicontent[] = $pid;
  }
}

if( count($multicontent) == 0 ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}

$contentops = ContentOperations::get_instance();
$pagelist = array();
foreach( $multicontent as $pid ) {
  $node = $contentops->quickfind_node_by_id($pid);
  if( !$node ) continue;
  $pagelist[] = $pid;
  $tmp = cmscm_admin_bulk_delete_get_children($node);
  $pagelist = array_merge($pagelist,$tmp);
}

//
// build the confirmation display
//
$contentops->LoadChildren(-1,FALSE,FALSE,$pagelist);
$displaydata =  array();
foreach( $pagelist as $pid ) {
  $node = $contentops->quickfind_node_by_id($pid);
  if( !$node ) continue;  // this should not happen, but hey.
  $content = $node->getContent(FALSE,FALSE,FALSE);
  if( !is_object($content) ) continue; // this should never happen either
  
  if( $content->DefaultContent() ) {
    echo $this->ShowErrors($this->Lang('error_delete_defaultcontent'));
    continue;
  }

  $rec = array();
  $rec['id'] = $content->Id();
  $rec['name'] = $content->Name();
  $rec['menutext'] = $content->MenuText();
  $rec['owner'] = $content->Owner();
  $rec['alias'] = $content->Alias();
  $displaydata[] = $rec;
}

if( count($displaydata) == 0 ) {
  $this->SetError($this->Lang('error_delete_novalidpages'));
  $this->RedirectToAdminTab();
}

$smarty->assign('multicontent',base64_encode(serialize($pagelist)));
$smarty->assign('displaydata',$displaydata);
echo $this->ProcessTemplate('admin_bulk_delete.tpl');
#
# EOF
#
?>