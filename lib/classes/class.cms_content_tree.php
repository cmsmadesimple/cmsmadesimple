<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_content_tree (c) 2010 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  A caching tree for CMSMS content objects.
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

/**
 * @package CMS
 */

/**
 * A tree class that allows backward compatibility (somewhat) to the old Tree class used
 * in CMSMS versions prior to 1.9, and provides content retrieval abilities, with interface
 * into the content cache.
 *
 * @package CMS
 * @author  Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.9
 */
class cms_content_tree extends cms_tree
{
  /**
   * Find a tree node given a specfied tag and value.
   *
   * @param string The tag name to search for
   * @param mixed  The tag value to search for
   * @param boolean Wether the value should be treated as case insensitive.
   * @return cms_tree or null on failure.
   */
  public function find_by_tag($tag_name,$value,$case_insensitive = FALSE)
  {
	  if( $tag_name == 'id' && $case_insensitive == FALSE && ($this->getParent() == null || $this->get_tag('id') == '') ) {
		  return cmsms()->GetContentOperations()->quickfind_node_by_id($value);
	  }
	  return parent::find_by_tag($tag_name,$value,$case_insensitive);
  }

  /**
   * Retrieve a node by it's id.
   *
   * A backwards compatibility method.
   *
   * @deprecated
   * @return cms_content_tree
   */
  public function sureGetNodeById($id)
  {
    return $this->find_by_tag('id',$id);
  }


  /**
   * Retrieve a node by it's id.
   *
   * A backwards compatibility method.
   *
   * @deprecated
   * @return cms_content_tree
   */
  public function getNodeById($id)
  {
    return $this->find_by_tag('id',$id);
  }


  /**
   * Retrieve a node by it's alias
   *
   * A backwards compatibility method.
   *
   * @deprecated
   * @return cms_content_tree
   */
  public function sureGetNodeByAlias($alias)
  {
	  if( is_numeric($alias) )
	  {
		  return $this->find_by_tag('id',$alias,true);
	  }
	  return $this->find_by_tag('alias',$alias,true);
  }


  /**
   * Retrieve a node by it's alias
   *
   * A backwards compatibility method.
   *
   * @deprecated
   * @return cms_content_tree
   */
  public function getNodeByAlias($alias)
  {
	  return $this->find_by_tag('alias',$alias,true);
  }


  /**
   * Retrieve a node by hierarchy position.
   *
   * @return cms_content_tree or null.
   */
  function getNodeByHierarchy($position)
  {
    $result = null;
	$gCms = cmsms();
    $contentops = $gCms->GetContentOperations();
    $id = $contentops->GetPageIDFromHierarchy($position);
    if ($id)  $result = $this->find_by_tag('id',$id);
    return $result;
  }


  /**
   * Test if this node has children.
   *
   * A backwards compatibility method.
   *
   * @deprecated
   * @see cms_tree:has_children()
   * @return boolean.
   */
  public function hasChildren()
  {
    return $this->has_children();
  }


  /**
   * Set a tag value
   *
   * A backwards compatibility method
   *
   * @deprecated
   * @see cms_tree::set_tag
   * @param string The tag name/key
   * @param mixed  The tag value
   */
  public function setTag($key,$value)
  {
    return $this->set_tag($key,$value);
  }


  /**
   * Get this nodes id.
   *
   * A backwards compatibility method
   *
   * @deprecated
   * @see cms_tree::get_tag('id')
   * @return integer The node id.
   */
  public function getId()
  {
    return $this->get_tag('id');
  }


  /**
   * Get a node tag.
   *
   * A backwards compatibility method
   *
   * @deprecated
   * @see cms_tree::get_tag('id')
   * @param  string Tag name/key
   * @return mixed Node value.
   */
  public function &getTag($key = 'id')
  {
    return $this->get_tag($key);
  }



  /**
   * Get this nodes parent.
   *
   * A backwards compatibility method
   *
   * @deprecated
   * @see cms_tree::get_parent()
   * @return cms_tree or null.
   */
  public function &getParentNode()
  {
    return $this->getParent();
  }


  /**
   * Add a node to the tree
   *
   * A backwards compatibility method.
   *
   * @deprecated
   * @see cms_tree::add_node()
   * @param cms_content_tree The node to add
   */
  public function addNode(cms_content_tree &$node)
  {
    return $this->add_node($node);
  }


  /**
   * Retrieve the content object associated with this node.
   *
   * This method will return the content object associated with this node, loading it
   * if necessary, and placing it in the cache for subsequent requests.
   *
   * @param bool Optionally load all child proeprties for the content object if loading is required.
   * @param bool Optionally load all the siblings for the selected content object at the same time (a preformance optimization)
   * @param bool If loading siblings, include inactive/disabled pages.
   */
  public function &getContent($deep = false,$loadsiblings = true,$loadall = false) // loadall used to be true: calguy1000, June 25 2011
  {
	  if( !cms_content_cache::content_exists($this->get_tag('id')) ) {
		  // not in cache
		  $parent = $this->getParent();
		  if( !$loadsiblings || !$parent ) {
			  // only load this content object
			  $gCms = cmsms();
			  $contentops = $gCms->GetContentOperations();
			  // todo: LoadContentFromId should use content cache.
			  $content = $contentops->LoadContentFromId($this->get_tag('id'), $deep);
			  return $content;
		  }
		  else {
			  $parent->getChildren($deep,$loadall);
			  if( cms_content_cache::content_exists($this->get_tag('id')) ) {
				  return cms_content_cache::get_content($this->get_tag('id'));
			  }
		  }
      }
	  return cms_content_cache::get_content($this->get_tag('id'));
  }

 
  /**
   * Count the number of children
   *
   * A backwards compatibility function
   *
   * @deprecated
   * @see cms_tree::count_children()
   * @return integer
   */
  public function getChildrenCount()
  {
    return $this->count_children();
  }


  /**
   * Count the number of siblings
   *
   * A backwards compatibility function
   *
   * @deprecated
   * @see cms_tree::count_siblings()
   * @return integer
   */
  public function getSiblingCount()
  {
    return $this->count_siblings();
  }


  /**
   * Get this nodes depth in the tree
   *
   * A backwards compatibility function
   *
   * @deprecated
   * @see cms_tree::get_level()
   * @return integer
   */
  public function getLevel()
  {
    return $this->get_level();
  }


  /**
   * Get the children for this node.
   *
   * This method will retrieve a list of the children of this node, loading
   * their content objects at the same time (as a preformance improvement).
   *
   * This method takes advantage of the content cache.
   *
   * @param boolean Optionally load the properties of the children (only used when loadcontent is true)
   * @param boolean Load all children, including inactive/disabled ones (only used when loadcontent is true)
   * @param boolean Load content objects for children
   * @return Array of cms_tree objects.
   */
  public function &getChildren($deep = false,$all = false,$loadcontent = true)
  {
    $children = $this->get_children();
    if( is_array($children) && count($children) && $loadcontent ) {
		// check to see if we need to load anything.
		$ids = array();
		for( $i = 0; $i < count($children); $i++ ) {
			if( !$children[$i]->isContentCached() ) $ids[] = $children[$i]->get_tag('id');
		}
		  
		if( count($ids) ) {
			// load the children that aren't loaded yet.
			$gCms = cmsms();
			$contentops = $gCms->GetContentOperations();
			$contentops->LoadChildren($this->get_tag('id'),$deep,$all,$ids);
		}
	}

    return $children;
  }


  /**
   * A function to build an array of cms_tree nodes, containing this node and all of the
   * children.
   *
   * This method changed in CMSMS 1.11.8 to return a hash instead of a flat array.  The keys of the hash
   * are the content ids.
   *
   * @return Array of cms_tree nodes.
   */
  public function &getFlatList()
  {
    $result = array();

    if( $this->has_children() ) {
		$children = $this->get_children();
		for( $i = 0; $i < count($children); $i++ ) {
			$result[$children[$i]->get_tag('id')] =& $children[$i];
			if( $children[$i]->has_children() ) {
				$tmp = $children[$i]->getFlatList();
				foreach( $tmp as $key => &$node ) {
					$result[$key] = $node;
				}
			}
		}
    }

    return $result;
  }


  /**
   * A method to indicate wether the content object for this node is cached.
   *
   * @return boolean
   */
  public function isContentCached()
  {
	  if( cms_content_cache::content_exists($this->get_tag('id')) ) return TRUE;
	  return FALSE;
  }
} // end of class.

?>
