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

/**
 * @package CMS
 */

/**
 * A simple class for building, and managing content lists.
 *
 * This is an internal class.  Not intended for use by external third parties.
 *
 * @package CMS
 * @internal
 * @ignore
 * @author Robert Campbell
 * @copyright Copyright (c) 2013, Robert Campbell <calguy1000@cmsmadesimple.org>
 */
final class ContentListBuilder 
{
  private $_opened_array = array();
  private $_module;
  private $_userid;
  private $_use_perms = TRUE;
  private $_pagelimit = 500;
  private $_offset    = 0;
  private $_pagelist;

  /**
   * Constructor
   *
   * Caches the opened pages, and userid
   */
  public function __construct(CMSModule $mod)
  {
    if( get_class($mod) != 'CMSContentManager' ) {
      throw new CmsInvalidDataException('Expected ContentEditor object, got: '.get_class($mod));
    }

    $this->_module = $mod;
    $this->_userid = get_userid();
    $tmp = cms_userprefs::get('opened_pages');
    if( $tmp ) $this->_opened_array = explode(',',$tmp);
  }

  /**
   * Expand a section, given a parent page_id.  Results in the children of this page being visible.
   */
  public function expand_section($parent_page_id)
  {
    $parent_page_id = (int)$parent_page_id;
    if( $parent_page_id < 1 ) return;

    $tmp = $this->_opened_array;
    $tmp[] = $parent_page_id;
    asort($tmp);
    $this->_opened_array = array_unique($tmp);
    cms_userprefs::set('opened_pages',implode(',',$this->_opened_array));
  }

  /**
   * Marks all parent pages as expanded.  Results in all content pages being visible.
   */
  public function expand_all()
  {
    $hm = cmsms()->GetHierarchyManager();
    // find all the pages (recursively) that have children.
    
    // anonymous, recursive function.
    $func = function($node) use(&$func) {
      $out = null;
      if( $node->has_children() ) {
	$out = array();
	if( $node->get_tag('id') ) $out[] = $node->get_tag('id');
        $children = $node->get_children();
	for( $i = 0; $i < count($children); $i++ ) {
	  $tmp = $func($children[$i]);
          if( is_array($tmp) && count($tmp) ) {
	    $out = array_merge($out,$tmp);
	  }
	}
	$out = array_unique($out);
      }
      return $out;
    }; // function.

    $this->_opened_array = $func($hm);
    cms_userprefs::set('opened_pages',implode(',',$this->_opened_array));
  }

  /**
   * Marks all parent pages as collapsed.  Results in no child pages beng visible.
   */
  public function collapse_all()
  {
    $this->_opened_array = array();
    cms_userprefs::remove('opened_pages');
  }

  /**
   * Collapse a parent page, results in its child pages not being visible in the content list.
   */
  public function collapse_section($parent_page_id)
  {
    $parent_page_id = (int)$parent_page_id;
    if( $parent_page_id < 1 ) return FALSE;

    $tmp = array();
    foreach( $this->_opened_array as $one ) {
      if( $one != $parent_page_id ) $tmp[] = $one;
    }
    asort($tmp);
    $this->_opened_array = array_unique($tmp);
    if( count($this->_opened_array) ) {
      cms_userprefs::set('opened_pages',implode(',',$this->_opened_array));
    }
    else {
      cms_userprefs::remove('opened_pages');
    }
    return TRUE;
  }

  /**
   * Toggle the active state of a content page
   */
  public function set_active($page_id,$state = TRUE)
  {
    $state = (bool)$state;
    $page_id = (int)$page_id;
    if( $page_id < 1 ) return FALSE;

    if( !$this->_module->CheckPermission('Manage All Content') ) {
      return FALSE;
    }

    $hm = cmsms()->GetHierarchyManager();
    $node = $hm->find_by_tag('id',$page_id);
    if( !$node ) return FALSE;
    $content = $node->GetContent(FALSE,FALSE,FALSE);
    if( !$content ) return FALSE;

    $content->SetActive($state);
    $content->Save();
    return TRUE;
  }

  /**
   * Set the page limit.
   * This must be called BEFORE get_content_list() is called.
   *
   * @param integer The page limit (min 1, max 500)
   * @return void
   */
  public function set_pagelimit($n)
  {
    if( !is_int($n) ) return;
    $n = max(1,min(500,$n));
    $this->_pagelimit = $n;
  }


  /**
   * Get the page limit.
   *
   * @return integer
   */
  public function get_pagelimit()
  {
    return $this->_pagelimit;
  }

  /**
   * Set the page offset
   * This must be called before get_content_list() is called.
   *
   * @param int page offset (min 0, max is set by get_content_list())
   */
  public function set_offset($n)
  {
    $n = (int)$n;
    $n = max(0,$n);
    $this->_offset = $n;
  }

  /**
   * Get the current offset
   *
   * @return integer
   */
  public function get_offset()
  {
    return $this->_offset;
  }

  public function set_page($n)
  {
    $n = (int)$n;
    $n = max(1,$n);
    $this->_offset = $this->_pagelimit * ($n-1);
  }

  /**
   * Get the number of pages.
   * Can only be called AFTER get_content_list has been called.
   *
   * @return integer
   */
  public function get_numpages()
  {
    if( !is_array($this->_pagelist) ) return;
    $npages = (int)(count($this->_pagelist) / $this->_pagelimit);
    if( count($this->_pagelist) % $this->_pagelimit  != 0 ) $npages++;
    return $npages;
  }

  /**
   * Set the specified page as the default page
   */
  public function set_default($page_id)
  {
    $page_id = (int)$page_id;
    if( $page_id < 1 ) return FALSE;

    if( !$this->_module->CheckPermission('Manage All Content') ) return;

    $hm = cmsms()->GetHierarchyManager();
    $node = $hm->find_by_tag('id',$page_id);
    if( !$node ) return FALSE;
    $content1 = $node->GetContent(FALSE,FALSE,FALSE);
    if( !$content1 ) return FALSE;
    if( !$content1->IsDefaultPossible() ) return FALSE;
    if( !$content1->Active() ) return FALSE;

    $page_id2 = ContentOperations::get_instance()->GetDefaultContent();
    $node = $hm->find_by_tag('id',$page_id2);
    if( !$node ) return FALSE;
    $content2 = $node->GetContent(FALSE,FALSE,FALSE);
    if( !$content2 ) return FALSE;

    $content1->SetDefaultContent(TRUE);
    $content1->Save();
    $content2->SetDefaultContent(FALSE);
    $content2->Save();
    return TRUE;
  }

  /**
   * Move a content page up, or down wrt it's peers.
   */
  public function move_content($page_id,$direction)
  {
    $page_id = (int)$page_id;
    if( $page_id < 1 ) return FALSE;
    $direction = (int)$direction;
    if( $direction == 0 ) return FALSE;

    $test = FALSE;
    if( $this->_module->CheckPermission('Manage All Content') ) {
      $test = TRUE;
    }
    else if( $this->_module->CheckPermission('Reorder Content') && check_peer_authorship($this->_userid,$page_id) ) {
      $test = TRUE;
    }

    if( !$test ) return FALSE;
    
    $hm = cmsms()->GetHierarchyManager();
    $node = $hm->find_by_tag('id',$page_id);
    if( !$node ) return FALSE;
    $content1 = $node->GetContent(FALSE,FALSE,FALSE);
    if( !$content1 ) return FALSE;

    $content1->ChangeItemOrder($direction);
    $contentops = ContentOperations::get_instance();
    $contentops->SetAllHierarchyPositions();
    $contentops->ClearCache();
    return TRUE;
  }

  /**
   * Delete a content page.
   *
   * returns error message on failure.  null on success;
   */
  public function delete_content($page_id)
  {
    $page_id = (int)$page_id;
    if( $page_id < 1 ) return $this->_module->Lang('error_invalidpageid');

    $test = FALSE;
    if( $this->_module->CheckPermission('Manage All Content') ) {
      $test = TRUE;
    }
    else if( $this->_module->CheckPermission('Remove Pages') && check_authorship($this->_userid,$page_id) ) {
      $test = TRUE;
    }

    if( !$test ) return $this->_module->Lang('error_delete_permission');

    $contentops = cmsms()->GetContentOperations();
    $hm = cmsms()->GetHierarchyManager();
    $node = $hm->find_by_tag('id',$page_id);
    if( !$node ) return $this->_module->Lang('error_invalidpageid');

    if( $node->has_children() ) return $this->_module->Lang('error_delete_haschildren');

    $parent = $node->get_parent();
    $parent_id = $node->get_tag('id');
    $childcount = 0;
    if( $parent ) $childcount = $parent->count_children();

    $content = $node->GetContent(FALSE,FALSE,FALSE);
    if( $content->DefaultContent() ) return $this->_module->Lang('error_delete_defaultcontent');

    $content->Delete();
    $contentops->SetAllHierarchyPositions();

    if( $childcount == 1 && $parent_id > -1 ) {
      $this->collapse_section($parent_id);
    }
    $this->collapse_section($page_id);

    $contentops->ClearCache();
  }

  /**
   * Get the columns that are visible to display in the content list
   */
  public function get_display_columns()
  {
    $mod = $this->_module;
    $cols = explode(',',$mod->GetPreference('list_visiblecolumns'));

    $columnstodisplay = array();
    $columnstodisplay['expand'] = in_array('expand',$cols);
    $columnstodisplay['hier'] = in_array('hier',$cols);
    $columnstodisplay['page'] = in_array('page',$cols);
    $columnstodisplay['alias'] = in_array('alias',$cols) && get_site_preference('listcontent_showalias',1);
    $columnstodisplay['url'] = in_array('url',$cols) && get_site_preference('listcontent_showurl',1);
    $columnstodisplay['template'] = in_array('template',$cols);
    $columnstodisplay['friendlyname'] = in_array('friendlyname',$cols);
    $columnstodisplay['owner'] = in_array('owner',$cols);
    $columnstodisplay['active'] = in_array('active',$cols) && $mod->CheckPermission('Manage All Content');
    $columnstodisplay['default'] = in_array('default',$cols) && $mod->CheckPermission('Manage All Content');
    $columnstodisplay['move'] = in_array('move',$cols) && ($mod->CheckPermission('Manage All Content') || $mod->CheckPermission('Reorder Content'));
    $columnstodisplay['view'] = in_array('view',$cols);
    $columnstodisplay['copy'] = in_array('copy',$cols) && ($mod->CheckPermission('Add Pages') || $mod->CheckPermission('Manage All Content'));
    $columnstodisplay['edit'] = in_array('edit',$cols);
    $columnstodisplay['delete'] = in_array('delete',$cols) && ($mod->CheckPermission('Remove Pages') || $mod->CheckPermission('Manage All Content'));
    $columnstodisplay['multiselect'] = in_array('multiselect',$cols) && ($mod->CheckPermission('Remove Pages') || $mod->CheckPermission('Manage All Content'));
    return $columnstodisplay;
  }

  /**
   * Recursive function to generate a list of all content pages.
   */
  private function _get_all_pages(cms_tree $node)
  {
    $out = array();
    if( $node->get_tag('id') ) $out[] = $node->get_tag('id');
    if( $node->has_children() ) {
      $children = $node->get_children();
      for( $i = 0; $i < count($children); $i++ ) {
	$child = $children[$i];
	$tmp = $this->_get_all_pages($child);
	if( is_array($tmp) && count($tmp) ) {
	  $out = array_merge($out,$tmp);
	}
      }
    }
    return $out;
  }

  /**
   * Load all content that the user has access to.
   */
  private function _load_editable_content()
  {
    $pagelist = null;
    if( $this->_use_perms && 
	($this->_module->CheckPermission('Manage All Content') || $this->_module->CheckPermission('Modify Any Page')) ) {
      // get all of the content ids
      $hm = cmsms()->GetHierarchyManager();
      $pagelist = $this->_get_all_pages($hm);
    }
    else {
      $pagelist = author_pages($this->_userid);
    }

    // remove children of pages that a: have children, and b: are not in the opened_array
    $hm = cmsms()->GetHierarchyManager();
    $remove = array();
    for( $i = 0; $i < count($pagelist); $i++ ) {
      $node = $hm->find_by_tag('id',$pagelist[$i]);
      if( $node && $node->has_children() && !in_array($pagelist[$i],$this->_opened_array) ) {
	$children = $node->get_children();
	if( $children ) {
	  foreach( $children as $child ) {
	    if( in_array($child->get_tag('id'),$pagelist) ) {
	      $remove[] = $child->get_tag('id');
	    }
	  }
	}
      }
    }

    $display = array();
    for( $i = 0; $i < count($pagelist); $i++ ) {
      if( !in_array($pagelist[$i],$remove) ) $display[] = $pagelist[$i];
    }

    $this->_pagelist = $display;
    $offset = min(count($pagelist),$this->_offset);
    $display = array_slice($display,$offset,$this->_pagelimit);

    ContentOperations::get_instance()->LoadChildren(-1,FALSE,TRUE,$display);
    return $display;
  }

  /**
   * Given a content id, and a userid indicate wether the user has access to all peers of the content page.
   */
  private function _check_peer_authorship($content_id,$userid = null)
  {
    if( $content_id < 1 ) return FALSE;
    if( $this->_module->CheckPermission('Modify Any Page') ) return TRUE;

    $hm = cmsms()->GetHierarchyManager();
    $node = $hm->getNodeById($content_id);
    if( !$node ) return FALSE;

    $parent = $node->getParentNode();
    if( !$node ) {
      // no parent means that $contentid is at the root level
      $parent = $hm;
    }
    
    $children = $parent->get_children();
    if( !$children ) return FALSE;

    if( $userid <= 0 ) $userid = $this->_userid;
    $mypages = author_pages($userid);
    for( $i = 0; $i < count($children); $i++ ) {
      $the_id = $chidren[$i]->get_tag('id');
      if( !quick_check_authorship($the_id,$mypages) ) return FALSE;
    }
    return TRUE;
  }

  /**
   * Checks if the specified user is the author of the specified content page
   */
  private function _check_authorship($content_id,$userid = null)
  {
    if( $userid <= 0 ) $userid = $this->_userid;
    $mypages = author_pages($userid);
    return quick_check_authorship($content_id,$mypages);
  }

  /**
   * Load, and cache all users
   */
  private function _get_users()
  {
    static $_users = null;
    if( !$_users ) {
      $tmp = UserOperations::get_instance()->LoadUsers();
      if( is_array($tmp) && count($tmp) ) {
	$_users = array();
	for( $i = 0; $i < count($tmp); $i++ ) {
	  $oneuser = $tmp[$i];
	  $_users[$oneuser->id] = $oneuser;
	}
      }
    }
    return $_users;
  }

  /**
   * Givena list of displayable pages, builds display info for each page.
   */
  private function _get_display_data($page_list)
  {
    $users = $this->_get_users();
    $hm = cmsms()->GetHierarchyManager();
    $mod = $this->_module;
    $columns = $this->get_display_columns();
    $userid = $this->_userid;

    // preload the templates.
    $tpl_list = array();
    foreach( $page_list as $page_id ) {
      $node = $hm->find_by_tag('id',$page_id);
      if( !$node ) continue;
      $content = $node->GetContent(FALSE,FALSE,TRUE);
      if( !$content ) continue;
      $tpl_list[] = $content->TemplateId();
    }
    $tpl_list = array_values(array_unique(array_values($tpl_list)));
    $tpls = CmsLayoutTemplate::load_bulk($tpl_list);

    $out = array();
    foreach( $page_list as $page_id ) {
      $node = $hm->find_by_tag('id',$page_id);
      if( !$node ) continue;
      $content = $node->GetContent(FALSE,FALSE,TRUE);
      if( !$content ) continue;

      $rec = array();
      $rec['depth'] = $node->get_level();
      $rec['hasusablelink'] = $content->HasUsableLink();
      $rec['hastemplate'] = $content->HasTemplate();
      $rec['menutext'] = $content->MenuText();
      $rec['title'] = $content->Name();
      $rec['template_id'] = $content->TemplateId();
      $rec['can_edit_tpl'] = $mod->CheckPermission('Modify Templates');
      $rec['id'] = $content->Id();
      $rec['lastmodified'] = $content->GetModifiedDate();
      $rec['created'] = $content->GetCreationDate();
      $rec['secure'] = $content->Secure();
      $rec['cachable'] = $content->Cachable();
      if( $content->LastModifiedBy() > 0 && isset($users[$content->LastModifiedBy()]) ) {
	$rec['lastmodifiedby'] = $users[$content->LastModifiedBy()]->username;
      }
      $rec['can_edit'] = $mod->CheckPermission('Modify Any Page') || $mod->CheckPermission('Manage All Content') ||
	$this->_check_authorship();

      foreach( array_keys($columns) as $column ) {
	switch( $column ) {
	case 'expand':
	  $rec[$column] = 'none';
	  if( $node->has_children() ) {
	    if( in_array($page_id,$this->_opened_array) ) {
	      $rec[$column] = 'open';
	    } else {
	      $rec[$column] = 'closed';
	    }
	  }
	  break;

	case 'hier':
	  $rec[$column] = $content->Hierarchy();
	  break;

	case 'page':
	  if( $content->MenuText() == CMS_CONTENT_HIDDEN_NAME ) continue;
	  $rec[$column] = $content->MenuText();
	  if( $mod->GetPreference('list_namecolumn','title') == 'title' ) {
	    $rec[$column] = $content->Name();
	  }
	  break;

	case 'alias':
	  if( $content->HasUsableLink() && $content->Alias() != '' ) {
	    $rec[$column] = $content->Alias();
	  }
	  break;

	case 'url':
	  $rec[$column] = '';
	  if( $content->HasUsableLink() && $content->URL() != '' ) {
	    $rec[$column] = $content->URL();
	  }
	  break;

	case 'template':
	  if( $content->IsViewable() ) {
	    $template = CmsLayoutTemplate::load($content->TemplateId());
	    $rec[$column] = $template->get_name();
	  }
	  break;

	case 'friendlyname':
	  $rec[$column] = $content->FriendlyName();
	  break;

	case 'owner':
	  if( $content->Owner() > 0 ) {
	    $rec[$column] = $users[$content->Owner()]->username;
	  }
	  break;

	case 'active':
	  if( $mod->CheckPermission('Manage All Content') && !$content->IsSystemPage() ) {
	    if( $content->Active() ) {
	      $rec[$column] = 'active';
	      if( $content->DefaultContent() ) {
		$rec[$column] = 'default';
	      }
	    } else {
	      $rec[$column] = 'inactive';
	    }
	  }
	  break;

	case 'default':
	  $rec[$column] = '';
	  if( $this->_module->CheckPermission('Manage All Content') ) {
	    if( $content->IsDefaultPossible() && $content->Active() ) {
	      $rec[$column] = ($content->DefaultContent())?'yes':'no';
	    }
	  }
	  break;

	case 'move':
	  if( $this->_check_peer_authorship($content->Id()) && ($nsiblings = $node->count_siblings()) > 1 ) {
	    if( $content->ItemOrder() == 1 ) {
	      $rec[$column] = 'down';
	    }
	    else if( $content->ItemOrder() == $nsiblings ) {
	      $rec[$column] = 'up';
	    }
	    else {
	      $rec[$column] = 'both';
	    }
	  }
	  break;

	case 'view':
	  $rec[$column] = '';
	  if( $content->HasUsableLink() && $content->IsViewable() && $content->Active() ) {
	    $rec[$column] = $content->GetURL();
	  }
	  break;

	case 'copy':
	  $rec[$column] = '';
	  if( $content->IsCopyable() ) {
	    if( $mod->CheckPermission('Manage All Content') ) {
	      $rec[$column] = 'yes';
	    }
	    else if( $mod->CheckPermission('Add Pages') && $this->_check_authorship($content->Id()) ) {
	      $rec[$column] = 'yes';
	    }
	  }
	  break;

	case 'edit':
	  $rec[$column] = '';
	  if( $rec['can_edit'] ) $rec[$column] = 'yes';
	  break;

	case 'delete':
	  $rec[$column] = '';
	  if( !$content->DefaultContent() ) {
	    if( !$node->has_children() ) {
	      if( $mod->CheckPermission('Manage All Content') ) {
		$rec[$column] = 'yes';
	      }
	      else if( $mod->CheckPermission('Remove Pages') && $this->_check_authorship($content->Id()) ) {
		$rec[$column] = 'yes';
	      }
	    }
	  }
	  break;

	case 'multiselect':
	  $rec[$column] = '';
	  if( !$content->IsSystemPage() ) {
	    if( $mod->CheckPermission('Manage All Content') || $this->CheckPermission('Modify Any Page') ) {
	      $rec[$column] = 'yes';
	    }
	    else if( $mod->CheckPermission('Remove Pages') && $this->_check_authorship($content->Id()) ) {
	      $rec[$column] = 'yes';
	    }
	    else if( $this->_check_authorship($content->Id()) ) {
	      $rec[$column] = 'yes';
	    }
	  }
	  break;
	} // switch
      } // foreach

      $out[] = $rec;
    } // foreach

    return $out;
  }

  /**
   * Master function, returns an array of display data for viewable/editable content
   */
  public function get_content_list()
  {
    $pagelist = $this->_load_editable_content();

    if( is_array($pagelist) && count($pagelist) ) {
      return $this->_get_display_data($pagelist);
    }
  }

} // end of class

#
# EOF
#
?>