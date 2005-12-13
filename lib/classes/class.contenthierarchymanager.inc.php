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
  var $index; // index for quick node access
  
  // -------------- CONSTRUCTOR AND CONSTRUCTOR HELPER ------------------
  
  /**
   *  Constructs the hierarchy index from a root node
   */
  function ContentHierarchyManager() {
    $this->index = array();
  }
  
  function setRoot(&$root) {
    $this->rootNode = &$root;
    $this->populateIndex($root);
  }
  
  /**
   *  Private function which populates the index
   */
  function populateIndex(&$root) {
    $content = &$root->getContent();
    if (isset($content)) {
      $this->index[$content->Id()]=&$root;
      if ($content->Alias()) {
        $this->index["".$content->Alias().""] = &$root; // ensure string index
      }
    }
    $children = &$root->getChildren();
    foreach ($children as $child) {
      $this->populateIndex($child);
    }
  }
  
  // ------------ GETTERS -------------------
  
  function getRootNode() {
    return $this->rootNode;
  }
  
  function getNodeById($id) {
    return $this->index[$id];
  }
  
  function getNodeByAlias($alias) {
    return $this->index["$alias"];
  }
  
}
?>
