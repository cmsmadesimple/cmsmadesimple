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
    $this->rootNode = new ContentNode(); // creates a default root node
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
    $this->indexNode($root);
    $children = &$root->getChildren();
    foreach ($children as $child) {
      $this->populateIndex($child);
    }
  }
  
  function indexNode(&$node) {
    $this->size++;
    $content = &$node->getContent();
    if (isset($content)) {
      $this->id_index[intval($content->Id())]=$node;
      if ($content->Alias()) {
        $this->alias_index[$content->Alias()] = $node; // ensure string index
      }
      $this->hier_index[$content->Hierarchy()]=$node;
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
  
  /**
   *  Opens a node for the specified content_id
   *  If the parent node is not loaded in the hierarchy
   *  then the parent node is automatically loaded
   *  @param id the content id
   */
  function openNodeWithId($ids) {
    if (!is_array($ids)) $ids = array($ids);
    $to_be_loaded = array();
    foreach ($ids as $id) {
      if (!$this->containsId($id)) $to_be_loaded[] = $id;
    }
    $contents = &$this->LoadMultipleFromId($to_be_loaded);
    $to_be_loaded=array();
    foreach ($contents as $content) {
        $path = explode('.',$content->IdHierarchy());
        // build the list of content elements to be loaded
        foreach ($path as $element) {
          if (($element!=$content->Id()) && (!in_array($element,$to_be_loaded)) && (!$this->containsId($element))) {
            $to_be_loaded[] = $element;
          }
        }
    }
    // load the missing elements
    $missing=&$this->LoadMultipleFromId($to_be_loaded);
    $complete=array_merge($contents,$missing);

    // now we have all the contents, and we have to link them all
    
    // first loop adds the elements to the hierarchy
    // do not use foreach since PHP4 can't manage the foreach ($complete as &$item) syntax
    $cpt = count($complete);
    for ($i=0; $i<$cpt; $i++) {
      $content = &$complete[$i];
      $node = new ContentNode();
      $node->setContent($content); // parent is still not set !
      $this->indexNode($node); // add it to the hierarchy
    }
    
    // second loop will link the nodes
    for ($i=0; $i<$cpt; $i++) {
      $content = &$complete[$i];
      $parent_id = $content->ParentId();
      if ($parent_id==-1) {
        $parentNode = &$this->rootNode;
      } else {
        $parentNode = &$this->getNodeById($parent_id);
      }
      $node = &$this->getNodeById($content->Id());
      $node->setParentNode($parentNode);
      $parentNode->addChild($node);
    }
  }
 
   /**
   *  Opens a node for the specified content alias
   *  If the parent node is not loaded in the hierarchy
   *  then the parent node is automatically loaded
   *  @param id the content id
   */
  function openNodeWithAlias($aliases) {
    if (!is_array($aliases)) $aliases = array($aliases);
    $to_be_loaded = array();
    foreach ($aliases as $alias) {
      if (!$this->containsAlias($alias)) $to_be_loaded[] = $alias;
    }
    $contents = &$this->LoadMultipleFromAlias($to_be_loaded);
    $to_be_loaded=array();
    foreach ($contents as $content) {
        $path = explode('.',$content->IdHierarchy());
        // build the list of content elements to be loaded
        foreach ($path as $element) {
          if (($element!=$content->Id()) && (!in_array($element,$to_be_loaded))&& (!$this->containsId($element))) {
            $to_be_loaded[] = $element;
          }
        }
    }
    // load the missing elements
    $missing=&$this->LoadMultipleFromId($to_be_loaded);
    $complete=array_merge($contents,$missing);

    // now we have all the contents, and we have to link them all
    
    // first loop adds the elements to the hierarchy
    // do not use foreach since PHP4 can't manage the foreach ($complete as &$item) syntax
    $cpt = count($complete);
    for ($i=0; $i<$cpt; $i++) {
      $content = &$complete[$i];
      $node = new ContentNode();
      $node->setContent($content); // parent is still not set !
      $this->indexNode($node); // add it to the hierarchy
    }
    
    // second loop will link the nodes
    for ($i=0; $i<$cpt; $i++) {
      $content = &$complete[$i];
      $parent_id = $content->ParentId();
      if ($parent_id==-1) {
        $parentNode = &$this->rootNode;
      } else {
        $parentNode = &$this->getNodeById($parent_id);
      }
      $node = &$this->getNodeById($content->Id());
      $node->setParentNode($parentNode);
      $parentNode->addChild($node);
    }
  }
  
  # The following methods try to retrieve a content node
  # If the node is not found in the index, then it will try to load it
  
  function &sureGetNodeById($id) {
    $node = &$this->getNodeById($id);
    if (!isset($node)) { // not found !
      $this->openNodeWithId($id); // try to load it
      $node=&$this->getNodeById($id); // and get it
    }
    return $node;
  }
  
  function &sureGetNodeByAlias($alias) {
    $node = &$this->getNodeById($alias);
    if (!isset($node)) { // not found !
      $this->openNodeWithAlias($alias); // try to load it
      $node=&$this->getNodeByAlias($alias); // and get it
    }
    return $node;
  }
  
  # The following method allows loading multiple instances of content in a single SQL request
  # Maybe should be moved to class.content.inc.php ?
  
  /**
	 * Load the content of the object from an ID
	 *
	 * @param $id				the ID of the element
	 * @param $loadProperties	whether to load or not the properties
	 *
	 * @returns bool			If it fails, the object comes back to initial values and returns FALSE
	 *							If everything goes well, it returns TRUE
	 */
	function &LoadMultipleFromId($ids, $loadProperties = false)
	{
		global $gCms, $config, $sql_queries, $debug_errors;
    $cpt = count($ids);
		if ($cpt==0) return array();
    $db = &$gCms->db;
    $id_list = '(';
    for ($i=0;$i<$cpt;$i++) {
      $id_list .= $ids[$i];
      if ($i<$cpt-1) $id_list .= ',';
    }
    $id_list .= ')';
    if ($id_list=='()') return array();
    $contents=array();
		$result = false;
		$query		= "SELECT * FROM ".cms_db_prefix()."content WHERE content_id IN $id_list";
		$rows		=& $db->Execute($query);

			while ($row=&$rows->FetchRow())
			{
				#Make sure the type exists.  If so, instantiate and load
			  if (in_array($row['type'], array_keys(@ContentManager::ListContentTypes()))) {
  				$classtype = strtolower($row['type']);
  				$contentobj = new $classtype; 
  				$contentobj->LoadFromData($row,false);
          $contents[]=$contentobj;
  				$result = true;
        }
			}
			if (!$result)
			{
				if (true == $config["debug"])
				{
					# :TODO: Translate the error message
					$debug_errors .= "<p>Could not retrieve content from db</p>\n";
				}
			}

			if ($result && $loadProperties)
			{
				foreach ($contents as $content) {
          if ($content->mPropertiesLoaded == false)
  				{
  					debug_buffer("load from id is loading properties");
  					$content->mProperties->Load($content->mId);
  					$content->mPropertiesLoaded = true;
  				}
  
  				if (NULL == $content->mProperties)
  				{
  					$result = false;
  
  					# debug mode
  					if (true == $config["debug"])
  					{
  						# :TODO: Translate the error message
  						$debug_errors .= "<p>Could not load properties for content</p>\n";
  					}
  				}
				}
			}
    
    foreach ($contents as $content) {
     		$content->Load();
    }

		return $contents;
	}
	
	/**
	 * Load the content of the object from an alias
	 *
	 * @param $alis				the alias of the element
	 * @param $loadProperties	whether to load or not the properties
	 *
	 * @returns bool			If it fails, the object comes back to initial values and returns FALSE
	 *							If everything goes well, it returns TRUE
	 */
	function &LoadMultipleFromAlias($ids, $loadProperties = false)
	{
		global $gCms, $config, $sql_queries, $debug_errors;
    $cpt = count($ids);
		if ($cpt==0) return array();
    $db = &$gCms->db;
    $id_list = '(';
    for ($i=0;$i<$cpt;$i++) {
      $id_list .= "'".$ids[$i]."'";
      if ($i<$cpt-1) $id_list .= ',';
    }
    $id_list .= ')';
    if ($id_list=='()') return array();
    $contents=array();
		$result = false;
		$query		= "SELECT * FROM ".cms_db_prefix()."content WHERE content_alias IN $id_list";
		$rows		=& $db->Execute($query);

			while ($row=&$rows->FetchRow())
			{
				#Make sure the type exists.  If so, instantiate and load
			  if (in_array($row['type'], array_keys(@ContentManager::ListContentTypes()))) {
  				$classtype = strtolower($row['type']);
  				$contentobj = new $classtype; 
  				$contentobj->LoadFromData($row,false);
          $contents[]=$contentobj;
  				$result = true;
        }
			}
			if (!$result)
			{
				if (true == $config["debug"])
				{
					# :TODO: Translate the error message
					$debug_errors .= "<p>Could not retrieve content from db</p>\n";
				}
			}

			if ($result && $loadProperties)
			{
				foreach ($contents as $content) {
          if ($content->mPropertiesLoaded == false)
  				{
  					debug_buffer("load from id is loading properties");
  					$content->mProperties->Load($content->mId);
  					$content->mPropertiesLoaded = true;
  				}
  
  				if (NULL == $content->mProperties)
  				{
  					$result = false;
  
  					# debug mode
  					if (true == $config["debug"])
  					{
  						# :TODO: Translate the error message
  						$debug_errors .= "<p>Could not load properties for content</p>\n";
  					}
  				}
				}
			}
    
    foreach ($contents as $content) {
     		$content->Load();
    }

		return $contents;
	}
	
}
?>
