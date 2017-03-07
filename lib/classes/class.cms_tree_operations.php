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
 * A utility class to provide functions to populate a tree
 * @package CMS
 * @license GPL
 */

/**
 * A utility class to provide functions to populate a tree
 *
 * @package CMS
 * @license GPL
 * @author  Robert Campbell
 * @copyright Copyright (c) 2010, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.9
 */
class cms_tree_operations
{
  /**
   * @ignore
   */
  private static $_keys;

  /**
   * Add a unique key to the key index
   *
   * @internal
   * @access private
   * @param string key to add
   */
  public static function add_key($key)
  {
    if( !is_array(self::$_keys) ) self::$_keys = array();
    if( !in_array($key,self::$_keys) ) self::$_keys[] = $key;
  }


  /**
   * Load content tree from a flat array of hashes (from the database?)
   *
   * This method uses recursion to load the tree.
   *
   * @internal
   * @access private
   * @param array The data to import
   * @param int (optional) The parent id to load the tree from (default is -1)
   * @param cms_content_tree (optional) The cms_content_tree node to add generated objects to.
   * @return cms_content_tree
   */
  public static function load_from_list($data)
  {
      // create a tree object
      $tree = new cms_content_tree();
      $sorted = array();

      for( $i = 0, $n = count($data); $i < $n; $i++ ) {
          $row = $data[$i];

          // create new node.
          $node = new cms_content_tree(array('id'=>$row['content_id'],'alias'=>$row['content_alias'],'active'=>$row['active']));

          // find where to insert it.
          $parent_node = null;
          if( $row['parent_id'] < 1 ) {
              $parent_node = $tree;
          }
          else {
              $parent_node = $tree->find_by_tag('id',$row['parent_id'],FALSE,FALSE);
              if( !$parent_node ) {
                  // ruh-roh
                  throw new \LogicException('Problem with internal content organization... could not get a parent node for content with id '.$row['content_id']);
              }
          }

          // add it.
          $parent_node->add_node($node);
      }
      return $tree;
  }
}

 // end of class
?>