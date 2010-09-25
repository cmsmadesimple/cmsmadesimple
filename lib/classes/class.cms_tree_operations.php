<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: cms_tree (c) 2010 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  A simple php tree class.
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
 * A utility class to provide functions to populate a tree
 *
 * @package CMS
 * @author  Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.9
 */
class cms_tree_operations
{
  private static $_keys;


  /**
   * Add a unique key to the key index
   *
   * @internal
   * @param string key to add
   */
  public static function add_key($key)
  {
    if( !is_array(self::$_keys) )
      {
	self::$_keys = array();
      }
    if( !in_array($key,self::$_keys) )
      {
	self::$_keys[] = $key;
      }
  }


  /**
   * Load tree from a flat, CSV encoded list.
   *
   * This function accepts an array of ASCII encoded lines like:
   * the_alias,gr_grandparent.grandparent.parent
   *
   * @internal
   * @param array The csv data to import
   * @param char  The delimiter separating hierarchy paths
   * @param char  The delimiter separating the node alias from the path.
   * @return cms_tree
   */
  public static function load_from_list($data,$path_delim = '.',$alias_delim = ',')
  {
    $tree = new cms_content_tree();
    $count = 0;
    $nodelist = array();

    if( !is_array($data) ) return $count;

    for( $i = 0; $i < count($data); $i++ )
      {
	$line =& $data[$i];
	list($path,$alias) = explode($alias_delim,$line,2);
	$path_parts = explode($path_delim,$path);

	if( count($path_parts) == 1 )
	  {
	    if( isset($node_list[$path]) ) continue;
	    
	    $obj = new cms_content_tree(array('id'=>(int)$path_parts[0],'alias'=>$alias));
	    $node_list[$path] =& $obj;
	    $tree->add_node($obj);
	  }
	else
	  {
	    $parent = $tree;
	    for( $j = 0; $j < count($path_parts); $j++ )
	      {
		$cur_path = implode($path_delim, array_slice($path_parts, 0, $j+1));
		if( isset($node_list[$cur_path]) )
		  {
		    continue;
		  }
		$parent_id = (int)$path_parts[$j-1];
		$parent = $tree->find_by_tag('id',$parent_id);
		$obj = new cms_content_tree(array('id'=>(int)$path_parts[$j],'alias'=>$alias));
		$node_list[$cur_path] = $obj;
		$parent->add_node($obj);
	      }
	  }
      }

    return $tree;
  }

}

 // end of class
?>