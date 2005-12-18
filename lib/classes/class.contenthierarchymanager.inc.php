<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#

class ContentHierarchyManager {
  var $rootNode; // root node of the hierarchy
  var $id_index; // index for quick node access
  var $alias_index; // index for aliases
  var $hier_index; // index for hierarchies
  var $size; // number of nodes
  
  // -------------- CONSTRUCTOR AND CONSTRUCTOR HELPER ------------------
  
  /**
   *  Constructs the hierarchy index from a root node
   */
  function ContentHierarchyManager() {
    $this->id_index = array();
    $this->alias_index = array();
    $this->hier_index = array();
    $this->size=0;
  }
  
  function setRoot(&$root) {
    $this->rootNode = &$root;
    $this->populateIndex($root);
  }
  
  /**
   *  Private function which populates the index
   */
  function populateIndex(&$root) {
    $this->size++;
    $content = &$root->getContent();
    if (isset($content)) {
      $this->id_index[intval($content->Id())]=$root;
      if ($content->Alias()) {
        $this->alias_index[$content->Alias()] = $root; // ensure string index
      }
      $this->hier_index[$content->Hierarchy()]=$root;
    }
    $children = &$root->getChildren();
    foreach ($children as $child) {
      $this->populateIndex($child);
    }
  }
  
  // ------------ GETTERS -------------------
  
  function &getRootNode() {
    return $this->rootNode;
  }
  
  function &getNodeById($id) {
    return $this->id_index[intval($id)];
  }
  
  function &getNodeByAlias($alias) {
    return $this->alias_index[$alias];
  }
  
  function &getNodeByHierarchy($hierarchy) {
    return $this->hier_index[$hierarchy];
  }
  
  function &getIndexedContent() {
    return $this->id_index;
  }
  
  function getNodeCount() {
    return $this->size;
  }
  
  // --------------- Tests --------------------
  
  function containsId($id) {
    return isset($this->id_index[intval($id)]);
  }
  
  function containsAlias($alias) {
    return isset($this->alias_index[$alias]);
  }
  
  function containsHierarchy($h) {
    return isset($this->hier_index[$h]);
  }
  
  
}
?>
