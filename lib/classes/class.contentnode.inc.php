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

class ContentNode {
  var $content;
  var $parentNode;
  var $children;
  var $level;

  function ContentNode() {  
    $this->children = array();
    $this->level=0;
    $this->parentNode=null;
    $this->content=null;
  }

  function init(&$content, &$parentNode) {
    $this->content = &$content;
    $this->parentNode = &$parentNode;
    if (isset($parentNode)) {
      $this->level=$parentNode->getLevel()+1;
    }
  }
  
  function setParentNode(&$node) {
    $this->parentNode = &$node;
    if (isset($node)) {
      $this->level=$node->getLevel()+1;
    }
  }
  
  function setContent(&$content) {
    $this->content = &$content;
  }
  
  function getChildrenCount() {
    return count($this->children);
  }
  
  function &getContent() {
    return $this->content;
  }
  
  function &getParentNode() {
    return $this->parentNode;
  }
  
  function &getChildren() {
    return $this->children;
  }
  
  function hasChildren() {
    return (count($this->children)>0);
  }
  
  function getLevel() {
    return $this->level;
  }

  function addChild(&$node) {
    $content = &$node->getContent();
    //echo "Adding ".$content->Hierarchy()." to level $this->level<br/>";
    $this->children[$content->ItemOrder()] = &$node;
    ksort($this->children);
    //echo "Total nodes of level $this->level = ".count($this->children)."<br/>";
  }
  
  /**
   * Returns the position of a node into the list of children
   * This method is a workaround for a PHP4 bug where reference testing
   * returns a circular reference fatal error
   * @param $node the node to find into the list of children
   */
  function findChildNodeIndex(&$node) {
    $i=0;
    foreach ($this->children as $child) {
      if ($child->getContent()==$node->getContent()) return $i;
      $i++;
    }
    return -1;
  }
  
}
?>
