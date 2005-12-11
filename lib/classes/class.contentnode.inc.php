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

  function ContentNode(&$content=null, &$parentNode=null) {
    $this->content = $content;
    $this->parentNode = $parentNode;
    $this->children = array();
    $this->level = isset($parentNode)?($parentNode->getLevel()+1):0;
  }

  function getChildrenCount() {
    return count($this->children);
  }
  
  function getContent() {
    return $this->content;
  }
  
  function getParentNode() {
    return $this->parentNode;
  }
  
  function getChildren() {
    return $this->children;
  }
  
  function hasChildren() {
    return (count($this->children)>0);
  }
  
  function getLevel() {
    return $this->level;
  }

  function addChild(&$node) {
    $this->children[] = $node;
  }
  
}
?>
