<?php
#CMS - CMS Made Simple
#(c)2013 by Robert Campbell (calguy1000@cmsmadesimple.org)
#Visit our homepage at: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANthe TY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: moduleinterface.php 8558 2012-12-10 00:59:49Z calguy1000 $

$CMS_ADMIN_PAGE=1;
require_once("../include.php");
$ruid = get_userid();

debug_to_log('ajax_content');
debug_to_log($_REQUEST);

$op = 'pageinfo';
if( isset($_REQUEST['op']) ) $op = trim($_REQUEST['op']);
$contentops = cmsms()->GetContentOperations();

$display = 'title';
$mod = cms_utils::get_module('CMSContentManager');
if( $mod ) {
  $display = CmsContentManagerUtils::get_pagenav_display();
}

$out = null;
$error = null;
switch( $op ) {
 case 'childrenof':
   debug_to_log('in childrenof');
   if( !isset($_REQUEST['page']) ) {
     $error = 'missingparams';
   }
   else {
     $page = (int)$_REQUEST['page'];
     if( $page < 1 ) $page = -1;
     debug_to_log('page is '.$page);
     $hm = cmsms()->GetHierarchyManager();
     $node = null;
     if( $page == -1 ) {
       $node = $hm;
     }
     else {
       $node = $hm->find_by_tag('id',$page);
     }
     if( $node ) {
       $children = $node->getChildren(FALSE,TRUE);
       if( is_array($children) && count($children) ) {
	 $out = array();
	 foreach( $children as $child ) {
	   $res = $child->getContent(FALSE)->ToData();
	   $res['display'] = $res['menu_text'];
	   if( $display == 'title' ) $res['display'] = $res['content_name'];
	   $out[] = $res;
	 }
       }
     }
   }
   break;

 case 'pageinfo':
   if( !isset($_REQUEST['page']) ) {
     $error = 'missingparams';
   }
   else {
     $page = (int)$_REQUEST['page'];
     if( $page < 1 ) {
       $error = 'missingparams';
     }
     else {
       // get the page info.
       $contentobj = $contentops->LoadContentFromId($page);
       if( !is_object($contentobj) ) {
	 $error = 'errorgettingcontent';
       }
       else {
	 $out = $contentobj->ToData();
	 $out['display'] = $out['menu_text'];
	 if( $display == 'title' ) $out['display'] = $out['content_name'];
       }
     }
   }
   break;

 case 'pagepeers':
   if( !isset($_REQUEST['pages']) || !is_array($_REQUEST['pages']) ) {
     $error = 'missingparams';
   }
   else {
     // clean up the data a bit
     $tmp = array();
     foreach( $_REQUEST['pages'] as $one ) {
       $one = (int)$one;
       if( $one > 0 ) $tmp[] = $one;
     }
     $peers = array_unique($tmp);

     // get the parent pages
     $db = cmsms()->GetDb();
     $query = 'SELECT parent_id FROM '.cms_db_prefix().'content WHERE content_id IN ('.implode(',',$peers).')';
     $tmp = $db->GetArray($query);
     $parents = array();
     foreach( $tmp as $one ) {
       $parents[] = $one['parent_id'];
     }
     
     if( count($parents) != count($peers) || count($peers) == 0 || count($parents) == 0 ) {
       $error = 'internalerror';
     }
     else {
       // get the peers for all of these as one huge list
       $query = 'SELECT * FROM '.cms_db_prefix().'content WHERE parent_id IN ('.implode(',',$parents).') ORDER BY hierarchy';
       $tmp = $db->GetArray($query);
       $data = array();
       $prev_parent_id = -1;
       for( $i = 0; $i < count($tmp); $i++ ) {
	 $row = $tmp[$i];
	 if( !isset($data[$row['parent_id']]) ) $data[$row['parent_id']] = array();
	 $row['display'] = $row['menu_text'];
	 if( $display == 'title' ) $row['display'] = $row['content_name'];
	 $data[$row['parent_id']][] = $row;
       }

       $out = array();
       for( $i = 0; $i < count($peers); $i++ ) {
	 $parent = $parents[$i];
	 $peer = $peers[$i];
	 $out[$peer] = $data[$parent];
       }
     }
   }
   break;
}

if( $error ) {
  $out = array('status'=>'error','message'=>lang($error));
}
else {
  $out = array('status'=>'success','op'=>$op,'data'=>$out);
}
debug_to_log($out);
header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private',false);
header('Content-Type: application/json');
echo json_encode($out);
exit;

#
# EOF
#
?>