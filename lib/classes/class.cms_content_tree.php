<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_content_tree (c) 2010 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A caching tree for CMSMS content objects.
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
 * Classes and utilities for managing the CMSMS content tree.
 *
 * @package CMS
 * @license GPL
 */

/**
 * A tree class that allows backward compatibility (somewhat) to the old Tree class used
 * in CMSMS versions prior to 1.9, and provides content retrieval abilities, with interface
 * into the content cache.
 *
 * @package CMS
 * @license GPL
 * @author  Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.9
 */
class cms_content_tree extends cms_tree
{
	/**
	 * Find a tree node given a specfied tag and value.
	 *
	 * @param string $tag_name The tag name to search for
	 * @param mixed  $value The tag value to search for
	 * @param bool $case_insensitive Wether the value should be treated as case insensitive.
     * @param bool $usequick Optionally, when searching by id... uise the quickfind method if possible.
	 * @return cms_tree
	 */
	public function &find_by_tag($tag_name,$value,$case_insensitive = FALSE,$usequick = TRUE)
	{
		if( $usequick && $tag_name == 'id' && $case_insensitive == FALSE && ($this->get_parent() == null || $this->get_tag('id') == '') ) {
			$res = ContentOperations::get_instance()->quickfind_node_by_id($value);
			return $res;
		}
		return parent::find_by_tag($tag_name,$value,$case_insensitive);
	}

	/**
	 * Retrieve a node by it's id.
	 * A backwards compatibility method.
	 *
	 * @deprecated
	 * @param int $id
	 * @return cms_content_tree
	 */
	public function sureGetNodeById($id)
	{
        return ContentOperations::get_instance()->quickfind_node_by_id($id);
	}


	/**
	 * Retrieve a node by it's id.
	 *
	 * A backwards compatibility method.
	 *
	 * @deprecated
	 * @param int $id
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
	 * @param string $alias
	 * @return cms_content_tree
	 */
	public function sureGetNodeByAlias($alias)
	{
		if( $alias == '' ) return;
		if( (int)$alias > 0 && is_numeric($alias) ) return $this->find_by_tag('id',$alias,true);
		return $this->find_by_tag('alias',$alias,true);
	}


	/**
	 * Retrieve a node by it's alias
	 *
	 * A backwards compatibility method.
	 *
	 * @deprecated
	 * @param string $alias
	 * @return cms_content_tree
	 */
	public function getNodeByAlias($alias)
	{
		return $this->find_by_tag('alias',$alias,true);
	}


	/**
	 * Retrieve a node by hierarchy position.
	 *
	 * @param string $position
	 * @return cms_content_tree or null.
	 */
	function getNodeByHierarchy($position)
	{
		$result = null;
		$id = ContentOperations::get_instance()->GetPageIDFromHierarchy($position);
		if ($id) $result = $this->find_by_tag('id',$id);
		return $result;
	}


	/**
	 * Test if this node has children.
	 *
	 * A backwards compatibility method.
	 *
	 * @deprecated
	 * @see cms_tree:has_children()
	 * @return bool
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
	 * @param string $key The tag name/key
	 * @param mixed  $value The tag value
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
	 * @return int The node id.
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
	 * @param  string $key Tag name/key
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
	 * @param bool $deep load all child proeprties for the content object if loading is required.
	 * @param bool $loadsiblings load all the siblings for the selected content object at the same time (a preformance optimization)
	 * @param bool $loadall If loading siblings, include inactive/disabled pages.
	 */
	public function &getContent($deep = false,$loadsiblings = true,$loadall = false)
	{
		if( !cms_content_cache::content_exists($this->get_tag('id')) ) {
			// not in cache
			$parent = $this->getParent();
			if( !$loadsiblings || !$parent ) {
				// only load this content object
				// todo: LoadContentFromId should use content cache.
				$content = ContentOperations::get_instance()->LoadContentFromId($this->get_tag('id'), $deep);
				return $content;
			}
			else {
				$parent->getChildren($deep,$loadall);
				if( cms_content_cache::content_exists($this->get_tag('id')) ) return cms_content_cache::get_content($this->get_tag('id'));
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
	 * @return int
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
	 * @return int
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
	 * @return int
	 */
	public function getLevel()
	{
		return $this->get_level();
	}


	/**
	 * Get the children for this node.
	 *
	 * This method will retrieve a list of the children of this node, loading
	 * their content objects at the same time (as a preformance enhancement).
	 *
	 * This method takes advantage of the content cache.
	 *
	 * @param bool $deep Optionally load the properties of the children (only used when loadcontent is true)
	 * @param bool $all Load all children, including inactive/disabled ones (only used when loadcontent is true)
	 * @param bool $loadcontent Load content objects for children
	 * @return Array of cms_tree objects.
	 */
	public function &getChildren($deep = false,$all = false,$loadcontent = true)
	{
		$children = $this->get_children();
		if( is_array($children) && count($children) && $loadcontent ) {
			// check to see if we need to load anything.
			$ids = array();
			for( $i = 0, $n = count($children); $i < $n; $i++ ) {
				if( !$children[$i]->isContentCached() ) $ids[] = $children[$i]->get_tag('id');
			}

			if( count($ids) ) {
				// load the children that aren't loaded yet.
                ContentOperations::get_instance()->LoadChildren($this->get_tag('id'),$deep,$all,$ids);
			}
		}

		return $children;
	}


    /**
     * @ignore
     */
    private function &_buildFlatList()
    {
        $result = array();

        if( $this->get_tag('id') > 0 ) $result[] = $this;
        if( $this->has_children() ) {
            $children = $this->get_children();
            for( $i = 0, $n = count($children); $i < $n; $i++ ) {
                $result[$children[$i]->get_tag('id')] =& $children[$i];
                if( $children[$i]->has_children() ) {
                    $tmp = $children[$i]->_buildFlatList();
                    foreach( $tmp as $key => $node ) {
                        if( $key > 0 ) $result[$key] = $node;
                    }
                }
            }
        }

        return $result;
    }

	/**
	 * A function to build an array of cms_tree nodes, containing this node and all of the
	 * children.
	 *
	 * @return Array of cms_tree nodes.
	 */
	public function &getFlatList()
	{
        static $result = null;
        if( is_null($result) ) {
            $result = $this->_buildFlatList();
        }
		return $result;
	}


	/**
	 * A method to indicate wether the content object for this node is cached.
	 *
	 * @return bool
	 */
	public function isContentCached()
	{
		if( cms_content_cache::content_exists($this->get_tag('id')) ) return TRUE;
		return FALSE;
	}


	/**
	 * A recursive method to find the (estimated) hierarchy position of this node.
	 *
	 * @return string
	 */
	private function _getPeerIndex()
	{
		// find index of current node amongst peers.
		$parent = $this->get_parent();
		if( $parent ) {
			$children = $parent->get_children();
			for( $i = 0, $n = count($children); $i < $n; $i++ ) {
				if( $children[$i]->get_tag('id') == $this->get_tag('id') ) return $i+1;
			}
		}
	}

	/**
	 * @ignore
	 */
	private function _getHierarchyArray()
	{
		$list = array();
		$list[] = $this->_getPeerIndex();
		$parent = $this->get_parent();
		if( $parent ) {
			$out = $parent->_getHierarchyArray();
			if( $out ) $list = array_merge($list,$out);
		}
		return $list;
	}

	/**
	 * Get the hierarchy position of this node
	 *
	 * @return string
	 */
	public function getHierarchy()
	{
		if( ($hier = $this->get_tag('hierarchy')) != '' ) return $hier;

		$list = $this->_getHierarchyArray();
		foreach( $list as &$one ) {
			$one = sprintf('%05d',$one);
		}
		$out = implode('.',array_reverse(array_splice($list,0,-1)));
		$this->set_tag('hierarchy',$out);

		return $out;
	}

} // end of class.

?>