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

debug_buffer('Start Navigator default action');
$items = null;
$nlevels = -1;
$show_all = FALSE;
$show_root_siblings = FALSE;
$start_element = null;
$start_page = null;
$start_level = null;
$childrenof = null;
$deep = TRUE;
$collapse = FALSE;

$template = null;
if( isset($params['template']) ) {
  $template = trim($params['template']);
}
else {
  $tpl = CmsLayoutTemplate::load_dflt_by_type('Navigator::navigation');
  if( !is_object($tpl) ) {
    audit('',$this->GetName(),'No default template found');
    return;
  }
  $template = $tpl->get_name();
}

$hm = cmsms()->GetHierarchyManager();
$cache_id = '|nav'.md5(serialize($params));
$compile_id = '';

if( !$smarty->isCached($this->GetTemplateResource($template),$cache_id,$compile_id) ) {
  foreach( $params as $key => $value ) {
    switch( $key ) {
    case 'loadprops':
      $deep = cms_to_bool($value);
      break;

    case 'items':
      // hardcoded list of items (and their children)
      Nav_utils::clear_excludes();
      $items = trim($value);
      $nlevels = 1;
      $start_element = null;
      $start_page = null;
      $start_level = null;
      $childrenof = null;
      break;

    case 'includeprefix':
      Nav_utils::clear_excludes();
      $list = explode(',',$value);
      if( is_array($list) && count($list) ) {
	foreach( $list as &$one ) {
	  $one = trim($one);
	}
	$list = array_unique($list);
	if( count($list) ) {
	  $flatlist = $hm->getFlatList();
	  if( is_array($flatlist) && count($flatlist) ) {
	    $tmp = array();
	    foreach( $flatlist as $id => &$node ) {
	      $alias = $node->get_tag('alias');
	      foreach( $list as $one ) {
		if( startswith( $alias, $one ) ) $tmp[] = $alias;
	      }
	    }
	    if( is_array($tmp) && count($tmp) ) $items = implode(',',$tmp);
	  }
	}
      }
      $nlevels = 1;
      $start_element = null;
      $start_page = null;
      $start_level = null;
      $childrenof = null;
      break;

    case 'excludeprefix':
      Nav_utils::set_excludes($value);
      $items = null;
      break;

    case 'nlevels':
    case 'number_of_levels':
      // a maximum number of levels;
      if( (int)$value > 0 ) $nlevels = (int)$value;
      break;

    case 'show_all':
      // show all items, even if marked as 'not shown in menu'
      $show_all = cms_to_bool($value);
      break;

    case 'show_root_siblings':
      // given a start element or start page ... show it's siblings too
      $show_root_siblings = cms_to_bool($value);
      break;

    case 'start_element':
      $start_element = trim($value);
      $start_page = null;
      $start_level = null;
      $childrenof = null;
      $items = null;
      break;

    case 'start_page':
      $start_element = null;
      $start_page = trim($value);
      $start_level = null;
      $childrenof = null;
      $items = null;
      break;

    case 'start_level':
      $start_element = null;
      $start_page = null;
      $value = (int)$value;
      $start_level = (int)$value;
      $items = null;
      break;

    case 'childrenof':
      $start_page = null;
      $start_element = null;
      $start_level = null;
      $childrenof = trim($value);
      $items = null;
      break;

    case 'collapse':
      $collapse = (int)$value;
      break;
    }
  } // params

  if( $items ) $collapse = FALSE;

  $rootnodes = array();
  if( $start_element ) {
    // get an alias... from a hierarchy level.
    $tmp = $hm->getNodeByHierarchy($start_element);
    if( is_object($tmp) ) {
      if( $show_root_siblings ) {
	$tmp = $tmp->getParent();
      }
      if( is_object($tmp) ) {
	$rootnodes[] = $tmp;
      }
    }
  }
  else if( $start_page ) {
    $tmp = $hm->sureGetNodeByAlias($start_page);
    if( is_object($tmp) ) {
      if( $show_root_siblings ) {
	$tmp = $tmp->getParent();
      }
      if( is_object($tmp) ) {
	$rootnodes[] = $tmp;
      }
    }
  }
  else if( $start_level > 0 ) {
    $tmp = $hm->find_by_tag('id',cmsms()->get_content_id());
    $arr = array();
    $arr2 = array();
    while( $tmp ) {
      $id = $tmp->get_tag('id');
      if( !$id ) break;
      $arr[] = $tmp;
      $arr2[] = $id;
      $tmp = $tmp->get_parent();
    }
    if( $start_level - 1 < count($arr) ) {
      // now do a childrenof the last element.
      $tmp = $arr[$start_level-1];
      if( $tmp->has_children() ) {
	$children = $tmp->get_children();
	foreach( $children as $one ) {
	  $rootnodes[] = $one;
	}
      }
    }
  }
  else if( $childrenof ) {
    $tmp = $hm->sureGetNodeByAlias(trim($childrenof));
    if( is_object($tmp) ) {
      if( $tmp->has_children() ) {
	$children = $tmp->get_children();
	foreach( $children as $one ) {
	  $rootnodes[] = $one;
	}
      }
    }
  }
  else if( $items ) {
    if( $nlevels < 1 ) $nlevels = 1;
    $items = explode(',',$items);
    foreach( $items as $item ) {
      $item = trim($item);
      $tmp = $hm->sureGetNodeByAlias(trim($oneitem));
      if( $tmp ) $rootnodes[] = $tmp;
    }
  }
  else {
    // start at the top
    if( $hm->has_children() ) {
      $children = $hm->get_children();
      for( $i = 0; $i < count($children); $i++ ) {
	$rootnodes[] = $children[$i];
      }
    }
  }

  if( count($rootnodes) == 0 ) return; // nothing to do.

  // preload all active content
  if( !cms_content_cache::have_preloaded() ) ContentOperations::get_instance()->LoadAllContent($deep,$show_all);

  // ready to fill the nodes
  $outtree = array();
  foreach( $rootnodes as $node ) {
    if( Nav_utils::is_excluded($node->get_tag('alias')) ) continue;
    $tmp = Nav_utils::fill_node($node,$deep,$nlevels,$show_all,$collapse);
    if( $tmp ) $outtree[] = $tmp;
  }

  Nav_utils::clear_excludes();
  $smarty->assign('nodes',$outtree);
}
echo $smarty->fetch($this->GetTemplateResource($template),$cache_id,$compile_id);
debug_buffer('End Navigator default action');
#
# EOF
#
?>