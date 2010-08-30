<?php

class cms_tree
{
  private $_parent;
  private $_tags;
  private $_children;

  public function cms_tree($key = '',$value = '')
  {
    if( $key )
      {
	if( is_string($key) )
	  {
	    $this->setTag($key,$value);
	  }
	else if( is_array($key) )
	  {
	    foreach( $key as $k => $v )
	      {
		$this->setTag($k,$v);
	      }
	  }
      }
  }


  // return a tree.
  public function sureGetNodeById($id)
  {
    return $this->find_by_tag('id',$id);
  }


  public function getNodeById($id)
  {
    return $this->find_by_tag('id',$id);
  }


  public function sureGetNodeByAlias($alias)
  {
    return $this->find_by_tag('alias',$alias);
  }


  public function getNodeByAlias($alias)
  {
    return $this->find_by_tag('alias',$alias);
  }


  function &getNodeByHierarchy($position)
  {
    $result = null;
    global $gCms;
    $contentops =& $gCms->GetContentOperations();
    $id = $contentops->GetPageIDFromHierarchy($position);
    if ($id)
    {
      $result =& $this->find_by_tag('id',$id);
    }
    return $result;
  }


  public function &find_by_tag($tag_name,$value)
  {
    $res = null;
    if( $this->_tags )
      {
	if( isset($this->_tags[$tag_name]) && $this->_tags[$tag_name] == $value )
	  {
	    return $this;
	  }
      }
    
    if( $this->hasChildren() )
      {
	for( $i = 0; $i < count($this->_children); $i++ )
	  {
	    $tmp = $this->_children[$i]->find_by_tag($tag_name,$value);
	    if( $tmp ) 
	      {
		$res = $tmp;
		break;
	      }
	  }
      }

    return $res;
  }


  public function hasChildren()
  {
    if( !is_array($this->_children) )
      {
	return FALSE;
      }
    return TRUE;
  }


  public function createFromList($data, $separator = '/')
  {
    die('todo');
  }


  public function setTag($key,$value)
  {
    if( !$this->_tags )
      {
	$this->_tags = array();
      }
    $this->_tags[$key] = $value;
  }


  public function getId()
  {
    return $this->getTag('id');
  }


  public function &getTag($key = 'id')
  {
    $res = null;
    if( !$this->_tags ) return $res;
    if( !isset($this->_tags[$key]) ) return $res;
    $res = $this->_tags[$key];
    return $res;
  }


  protected function removeNode(cms_tree &$node, $search_children = false)
  {
    if( !$this->hasChildren() ) return FALSE;

    for( $i = 0; $i < count($this->_children); $i++ )
      {
	if( $this->_children[$i] == $node )
	  {
	    // item found.
	    unset($this->_children[$i]);
	    $this->_children = @array_values($this->_children);
	    return TRUE;
	  }
	elseif ($search_children && $this->_children[$i]->hasChildren())
	  {
	    $res = $this->_children[$i]->removeNode($node,$search_children);
	    if( $res )
	      {
		return TRUE;
	      }
	  }
      }

    return FALSE;
  }


  public function remove()
  {
    if( is_null($this->_parent) ) return FALSE;

    return $this->_parent->_removeNode($this);
  }

  
  public function &getParentNode()
  {
    return $this->getParent();
  }


  public function &getParent()
  {
    return $this->_parent;
  }


  public function depth()
  {
    $depth = 0;
    $curr = &$this;

    while($curr->_parent)
      {
	$depth++;
	$curr = $curr->_parent;
      }
    return $depth;
  }

  
  public function addNode(cms_tree &$node)
  {
    if( !is_array($this->_children) )
      {
	$this->_children = array();
      }

    for( $i = 0; $i < count($this->_children); $i++ )
      {
	if( $this->_children[$i] == $node ) return FALSE;
      }
    $node->_parent = $this;
    $this->_children[] = $node;
  }


  public function &getContent($deep = false,$loadsiblings = true,$loadall = true)
  {
    if( cms_content_cache::content_exists($this->getTag('alias')) )
      {
	return cms_content_cache::get_content($this->getTag('alias'));
      }

    // not in cache
    $parent = $this->getParent();
    $content = null;
    if( !$loadsiblings || !$parent )
      {
	// only load this content object
	global $gCms;
	$contentops =& $gCms->GetContentOperations();
	// todo: LoadContentFromId should use content cache.
	$content =& $contentops->LoadContentFromId($this->getTag('id'), $deep);
	return $content;
      }
    else
      {
	$parent->getChildren($deep,$loadall);
	if( cms_content_cache::content_exists($this->getTag('alias')) )
	  {
	    return cms_content_cache::get_content($this->getTag('alias'));
	  }
      }
    return $content;    
  }


  public function getChildrenCount()
  {
    if( $this->hasChildren() )
      {
	return count($this->_children);
      }
    return 0;
  }


  public function getSiblingCount()
  {
    if( $this->_parent )
      {
	return $this->_parent->getChildrenCount();
      }
    return 0;
  }


  public function getLevel()
  {
    $n = -1;
    $node =& $this;
    while( $node->_parent )
      {
	$n++;
	$node = $node->_parent;
      }
    return $n;
  }


  public function &getChildren($deep = false,$all = false,$loadcontent = true)
  {
    if( $this->hasChildren() )
      {
	if( $loadcontent )
	  {
	    // check to see if we need to load anything.
	    $ids = array();
	    for( $i = 0; $i < count($this->_children); $i++ )
	      {
		if( !$this->_children[$i]->isContentCached() )
		  {
		    $ids[] = $this->_children[$i]->getTag('id');
		  }
	      }

	    if( count($ids) )
	      {
		global $gCms;
		$contentops = $gCms->GetContentOperations();
		$contentops->LoadChildren($this->GetTag('id'),$deep,$all,$ids);
	      }
	  }

	return $this->_children;
      }

    $res = null;
    return $res;
  }


  public function &getFlatList()
  {
    $result = array();

    if( $this->hasChildren() )
      {
	for( $i = 0; $i < count($this->_children); $i++ )
	  {
	    $result[] =& $this->_children[$i];
	    if( $this->_children[$i]->hasChildren() )
	      {
		$result = array_merge($result,$this->_children[$i]->getFlatList());
	      }
	  }
      }

    return $result;
  }


  public function isContentCached()
  {
    if( cms_content_cache::content_exists($this->getTag('id')) )
      {
	return TRUE;
      }

    return FALSE;
  }
} // end of class

?>