<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_tree (c) 2010 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A simple php tree class.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
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
 * This file contains the definition for a simple PHP tree class
 *
 * @package CMS
 */

/**
 * A Simple PHP Tree class that allows storing associative data along with each node.
 *
 * @package CMS
 * @author  Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.9
 */
class cms_tree
{
  /**
   * @ignore
   */
  private $_parent;

  /**
   * @ignore
   */
  private $_tags;

  /**
   * @ignore
   */
  private $_children;


  /**
   * Construct a new tree, or node of a tree.
   *
   * @param string $key An optional key for a tag
   * @param mixed  $value An optional value for the tag.
   */
  public function __construct($key = '',$value = '')
  {
	  if( $key ) {
		  if( is_string($key) ) {
			  $this->set_tag($key,$value);
		  }
		  else if( is_array($key) ) {
			  foreach( $key as $k => $v ) {
				  $this->set_tag($k,$v);
			  }
		  }
      }
  }


  /**
   * Find a tree node given a specfied tag and value.
   *
   * @param string $tag_name The tag name to search for
   * @param mixed  $value The tag value to search for
   * @param bool $case_insensitive Wether the value should be treated as case insensitive.
   * @return cms_tree or null on failure.
   */
  public function &find_by_tag($tag_name,$value,$case_insensitive = FALSE)
  {
	  $res = null;
	  if( !is_string($tag_name) ) return $res;
	  if( !is_string($value) ) $case_insensitive = FALSE;

	  if( $this->_tags ) {
		  if( isset($this->_tags[$tag_name]) ) {
			  if( $case_insensitive ) {
				  if( isset($this->_tags[$tag_name]) && strtolower($this->_tags[$tag_name]) == strtolower($value) ) {
					  return $this;
				  }
			  }
			  else {
				  if( isset($this->_tags[$tag_name]) && $this->_tags[$tag_name] == $value ) {
					  return $this;
				  }
			  }
		  }
      }

	  if( $this->has_children() ) {
		  for( $i = 0, $n = count($this->_children); $i < $n; $i++ ) {
			  $tmp = $this->_children[$i]->find_by_tag($tag_name,$value);
			  if( $tmp ) return $tmp;
		  }
	  }

	  return $res;
  }


  /**
   * Test if this node has children.
   *
   * @return bool
   */
  public function has_children()
  {
	  if( !is_array($this->_children) ) return FALSE;
	  return TRUE;
  }


  /**
   * Set a tag value into this node
   *
   * @param string $key Tag name
   * @param mixed  $value Tag value
   */
  public function set_tag($key,$value)
  {
	  if( !$this->_tags ) $this->_tags = array();
	  $this->_tags[$key] = $value;
  }


  /**
   * Retrieve a tag for this node.
   *
   * @param string $key The tag name
   * @return mixed The tag value, or null
   */
  public function &get_tag($key)
  {
	  $res = null;
	  if( !$this->_tags ) return $res;
	  if( !isset($this->_tags[$key]) ) return $res;
	  $res = $this->_tags[$key];
	  return $res;
  }


  /**
   * Remove the specified node from the tree.
   *
   * Search through the children of this node (and optionally recursively through the tree)
   * for the specified node.  If found, remove it.
   *
   * Use this method with caution, as it is very easy to break your tree, corrupt memory
   * and have tree nodes hanging out there with no parents.
   *
   * @param cms_tree $node Reference to the node to be removed.
   * @param bool  $search_children Wether to recursively search children.
   * @return bool
   */
  protected function remove_node(cms_tree &$node, $search_children = false)
  {
	  if( !$this->has_children() ) return FALSE;

	  for( $i = 0, $n = count($this->_children); $i < $n; $i++ ) {
		  if( $this->_children[$i] == $node ) {
			  // item found.
			  unset($this->_children[$i]);
			  $this->_children = @array_values($this->_children);
			  return TRUE;
		  }
		  elseif ($search_children && $this->_children[$i]->has_children()) {
			  $res = $this->_children[$i]->remove_node($node,$search_children);
			  if( $res ) return TRUE;
		  }
	  }

	  return FALSE;
  }


  /**
   * Remove this node
   *
   * This is a convenience method that calls remove_node on the current object.
   *
   * @return bool
   */
  public function remove()
  {
	  if( is_null($this->_parent) ) return FALSE;
	  return $this->_parent->remove_node($this);
  }


  /**
   * Get a reference to the parent node.
   *
   * @return cms_tree Reference to the parent node, or null.
   * @since 2.0
   */
  public function &get_parent()
  {
	  return $this->_parent;
  }

  /**
   * Get a reference to the parent node.
   *
   * @return cms_tree Reference to the parent node, or null.
   * @deprecated
   */
  public function &getParent()
  {
	  return $this->_parent;
  }

  /**
   * Add the specified node as a child to this node.
   *
   * @param cms_tree $node The node to add
   */
  public function add_node(cms_tree &$node)
  {
	  if( !is_array($this->_children) ) $this->_children = array();

	  for( $i = 0, $n = count($this->_children); $i < $n; $i++ ) {
		  if( $this->_children[$i] == $node ) return FALSE;
      }
	  $node->_parent = $this;
	  $this->_children[] = $node;
  }


  /**
   * Count the number of direct children to this node.
   *
   * @return int
   */
  public function count_children()
  {
	  if( $this->has_children() ) return count($this->_children);
	  return 0;
  }


  /**
   * Count the number of siblings that this node has.
   *
   * @return int
   */
  public function count_siblings()
  {
	  if( $this->_parent ) return $this->_parent->count_children();
	  return 1;
  }


  /**
   * Count the total number of all nodes, including myself.
   *
   * @return int
   */
  public function count_nodes()
  {
	  $n = 1;
	  if( $this->has_children() ) {
		  foreach( $this->_children as &$one ) {
			  $n += $one->count_nodes();
		  }
	  }
	  return $n;
  }

  /**
   * Find the depth of the current node.
   *
   * This method counts all of the parents in the tree until there are no more parents.
   *
   * @return int
   */
  public function get_level()
  {
	  $n = 1;
	  $node = $this;
	  while( $node->_parent ) {
		  $n++;
		  $node = $node->_parent;
	  }
	  return $n;
  }


  /**
   * Return the children of this node.
   *
   * @return an array of cms_tree objects, or null if there are no children.
   */
  public function &get_children()
  {
	  $res = null;
	  if( !$this->has_children() ) return $res;
	  return $this->_children;
  }

} // end of class

?>