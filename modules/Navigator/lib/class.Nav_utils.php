<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: Navigator (c) 2013 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  An module for CMS Made Simple to allow building hierarchical navigations.
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
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
#$Id: News.module.php 2114 2005-11-04 21:51:13Z wishy $

final class Nav_utils 
{
  private function __construct() {}

  public static function fill_node(cms_content_tree $node,$deep,$nlevels,$show_all,$collapse = FALSE,$depth = 0)
  {
    if( !is_object($node) ) return;
    $gCms = cmsms();
    $hm = $gCms->GetHierarchyManager();
    $content = $node->getContent(TRUE,TRUE);
    if( is_object($content) ) {
      if( !$content->Active() ) return;
      if( !$content->ShowInMenu() && !$show_all ) return;

      $obj = new stdClass();
      $obj->id = $content->Id();
      $obj->url = $content->GetURL();
      $obj->accesskey = $content->AccessKey();
      $obj->type = strtolower($content->Type());
      $obj->tabindex = $content->TabIndex();
      $obj->titleattribute = $content->TitleAttribute();
      $obj->modified = $content->GetModifiedDate();
      $obj->created = $content->GetCreationDate();
      $obj->hierarchy = $content->Hierarchy();
      $obj->depth = $depth+1;
      $obj->menutext = cms_htmlentities($content->MenuText());
      $obj->raw_menutext = $content->MenuText();
      $obj->target = '';
      $obj->alias = $content->Alias();
      $obj->current = FALSE;
      $obj->parent = FALSE;
      $obj->has_children = FALSE;

      if( ($tmp = cmsms()->get_content_id()) && $obj->id == $tmp ) {
	$obj->current = true;
      }
      else {
	$tmp_node = $hm->find_by_tag('id',$tmp);
	while( $tmp_node ) {
	  if( $tmp_node->get_tag('id') == $obj->id ) {
	    $obj->parent = TRUE;
	    break;
	  }
	  $tmp_node = $tmp_node->get_parent();
	}
      }

      if( $content->DefaultContent() ) {
	$obj->default = 1;
      }
      if( $deep ) {
	if ($content->HasProperty('target')) $obj->target = $content->GetPropertyValue('target');
	$config = cmsms()->GetConfig();
	$obj->extra1 = $content->GetPropertyValue('extra1');
	$obj->extra2 = $content->GetPropertyValue('extra2');
	$obj->extra3 = $content->GetPropertyValue('extra3');
	$tmp = $content->GetPropertyValue('image');
	if( !empty($tmp) && $tmp != -1 ) {
	  $url = get_site_preference('content_imagefield_path').'/'.$tmp;
	  if( !startswith($url,'/') ) $url = '/'.$url;
	  $url = $config['image_uploads_url'].$url;
	  $obj->image = $url;
	}
	$tmp = $content->GetPropertyValue('thumbnail');
	if( !empty($tmp) && $tmp != -1 ) {
	  $url = get_site_preference('content_thumbnailfield_path').'/'.$tmp;
	  if( !startswith($url,'/') ) $url = '/'.$url;
	  $url = $config['image_uploads_url'].$url;
	  $obj->thumbnail = $url;
	}
      }

      $children = null;
      if( $node->has_children() ) $children = $node->get_children();
      
      // are we recursing?
     if( is_array($children) && count($children) && ($nlevels < 0 || $depth+1 < $nlevels) && 
	 (($collapse && ($obj->parent || $obj->current)) || !$collapse) ) {
	$children = $node->get_children();
	$child_nodes = array();
	for( $i = 0; $i < count($children); $i++ ) {
	  $tmp = self::fill_node($children[$i],$deep,$nlevels,$show_all,$depth+1);
	  if( is_object($tmp) ) {
	    $child_nodes[] = $tmp;
	  }
	}
	if( count($child_nodes) ) {
	  $obj->children = $child_nodes;
	}
      }

      return $obj;
    }
  }
} // end of class

#
# EOF
#
?>