<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
#$Id$

/**
 * Content related functions.
 *
 * @package CMS
 * @license GPL
 */

/**
 * Include the content class definition
 */
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.content.inc.php');

/**
 * Class for static methods related to content
 *
 * @since 0.8
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class ContentOperations
{
	private $_content_types;

	/**
	 * Return a content object for the currently requested page.
	 *
	 * @since 1.9
	 * @return getContentObject()
	 */
	function &getContentObject()
	{
		global $gCms;
		$res = null;
		if( isset($gCms->variables['content_obj']) && is_object($gCms->variables['content_obj']) )
		{
			$res = $gCms->variables['content_obj'];
		}
		return $res;
	}


	/**
	 * Given an array of content_type and seralized_content, reconstructs a 
	 * content object.  It will handled loading the content type if it hasn't
	 * already been loaded.
	 *
	 * @return mixed The unserialized content object
	 */
	function &LoadContentFromSerializedData(&$data)
	{
	  if( !isset($data['content_type']) && !isset($data['serialized_content']) ) return FALSE;

	  $contenttype = 'content';
	  if( isset($data['content_type']) ) $contenttype = $data['content_type'];

	  $contentobj =& $this->CreateNewContent($contenttype);
	  $contentobj = unserialize($data['serialized_content']);
	  return $contentobj;
	}


	/**
	 * Load a specific content type
	 *
	 * This method is called from the autoloader.  There is no need to call it internally
	 *
	 * @internal
	 * @final
	 * @since 1.9
	 * @param mixed The type.  Either a string, or an instance of CmsContentTypePlaceHolder
	 */
	final public function LoadContentType($type)
	{
		if( is_object($type) && $type instanceof CmsContentTypePlaceHolder ) 
			{
				$type = $type->type;
			}

		$ctph = $this->_get_content_type($type);
		if( is_object($ctph) )
			{
				if( !class_exists( $ctph->class ) && file_exists( $ctph->filename ) )
					{
						include_once( $ctph->filename );
					}
			}

	}

	/**
	 * Creates a new, empty content object of the given type.
	 *
	 * if the content type is registered with the system,
	 * and the class does not exist, the appropriate filename will be included
	 * and then, if possible a new object of the designated type will be
	 * instantiated.
	 *
	 * @param mixed The type.  Either a string, or an instance of CmsContentTypePlaceHolder
	 * @return mixed The new content object
	 */
	function &CreateNewContent($type)
	{
		if( is_object($type) && $type instanceof CmsContentTypePlaceHolder ) 
			{
				$type = $type->type;
			}
		$result = NULL;
		$ctph = $this->_get_content_type($type);
		if( is_object($ctph) )
			{
				if( class_exists($ctph->class) )
					{
						$result = new $ctph->class;
					}
			}
		return $result;
	}

	
    /**
     * Given a content id, load and return the loaded content object.
     *
     * @param integer $id The id of the content object to load
     * @param boolean $loadprops Also load the properties of that content object. Defaults to false.
     * @return mixed The loaded content object. If nothing is found, returns FALSE.
     */
	function &LoadContentFromId($id,$loadprops=false)
	{
		if( cms_content_cache::content_exists($id) )
		{
			return cms_content_cache::get_content($id);
		}

		$result = FALSE;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT * FROM ".cms_db_prefix()."content WHERE content_id = ?";
		$row = &$db->GetRow($query, array($id));
		if ($row)
		{
			$classtype = strtolower($row['type']);
			$contentobj =& $this->CreateNewContent($classtype);
			if ($contentobj)
			{
				$contentobj->LoadFromData($row, $loadprops);
				cms_content_cache::add_content($id,$row['content_alias'],$contentobj);
				return $contentobj;
			}
		}
		return $result;
	}


    /**
     * Given a content alias, load and return the loaded content object.
     *
     * @param integer $id The id of the content object to load
     * @param boolean $only_active If true, only return the object if it's active flag is true. Defaults to false.
     * @return mixed The loaded content object. If nothing is found, returns NULL.
     */
	function &LoadContentFromAlias($alias, $only_active = false)
	{
		if( cms_content_cache::content_exists($alias) )
		{
			return cms_content_cache::get_content($alias);
		}

		global $gCms;
		$db = &$gCms->GetDb();

		$row = '';
		if (is_numeric($alias) && strpos($alias,'.') === FALSE && strpos($alias,',') === FALSE) //Fix for postgres
		{
			$query = "SELECT * FROM ".cms_db_prefix()."content WHERE content_id = ?";
			if ($only_active == true)
			{
				$query .= " AND active = 1";
			}
			$row = &$db->GetRow($query, array($alias));
		}
		else
		{
			$query = "SELECT * FROM ".cms_db_prefix()."content WHERE content_alias = ?";
			if ($only_active == true)
			{
				$query .= " AND active = 1";
			}
			$row = &$db->GetRow($query, array($alias));
		}

		if ($row)
		{
			#Make sure the type exists.  If so, instantiate and load
			if (in_array($row['type'], array_keys($this->ListContentTypes())))
			{
				$classtype = strtolower($row['type']);
				$contentobj =& $this->CreateNewContent($classtype);
				$contentobj->LoadFromData($row, TRUE);
				cms_content_cache::add_content($row['content_id'],$row['content_alias'],$contentobj);
				return $contentobj;
			}
			else
			{
			  $tmp = NULL; return $tmp;
			}
		}
		else
		{
		  $tmp = NULL; return $tmp;
		}
	}


	/**
	 * Returns the id of the content marked as default.
	 *
	 * @return integer The id of the default content page
	 */
	function GetDefaultContent()
	{
	  global $gCms;
	  if( isset($gCms->variables['default_content_id']) )
	    {
	      return $gCms->variables['default_content_id'];
	    }
		$db =& $gCms->GetDb();

		$result = -1;

		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE default_content = 1";
		$row = &$db->GetRow($query);
		if ($row)
		{
			$result = $row['content_id'];
		}
		else
		{
			#Just get something...
			$query = "SELECT content_id FROM ".cms_db_prefix()."content";
			$row = &$db->GetRow($query);
			if ($row)
			{
				$result = $row['content_id'];
			}
		}

		$gCms->variables['default_content_id'] = $result;
		return $result;
	}


	/**
	 * Load standard CMS content types
	 *
	 * This internal method looks through the contenttypes directory
	 * and loads the placeholders for them.
	 *
	 * @since 1.9
	 * @access private
	 * @internal
	 */
	private function _load_std_content_types()
	{
		if (isset(cmsms()->variables['contenttypes'])) 
		{
			return;
		}

		$result = array();
		$dir = dirname(__FILE__).'/contenttypes';
		$files = glob($dir.'/*.inc.php');
		if( is_array($files) )
		{
			foreach( $files as $one )
			{
				$obj = new CmsContentTypePlaceHolder();
				$class = basename($one,'.inc.php');
				$type  = strtolower($class);

				$obj->class = $class;
				$obj->type = strtolower($class);
				$obj->filename = $one;
				$obj->loaded = false;
				if( $obj->type == 'link' )
					{
						// cough... big hack... cough.
						$obj->friendlyname_key = 'contenttype_redirlink';
					}
				else
					{
						$obj->friendlyname_key = 'contenttype_'.$obj->type;
					}
				//$obj->friendlyname = $class;
				$result[$type] = $obj;
			}
		}

		cmsms()->variables['contenttypes'] = $result;
	}


	/**
	 * Function to return a content type given it's name
	 *
	 * @since 1.9
	 * @access private
	 * @internal
	 * @param string The content type name
	 * @return CmsContentTypePlaceHolder placeholder object.
	 */
	private function _get_content_type($name)
	{
		$name = strtolower($name);
		$this->_load_std_content_types();
		$types = cmsms()->variables['contenttypes'];
		if( is_array($types) )
		{
			foreach( $types as $key => $obj )
			{
				if( $key == $name && $obj instanceof CmsContentTypePlaceHolder )
					{
						return $obj;
					}
			}
		}
	}


	/**
	 * Register a new content type
	 *
	 * @since 1.9
	 * @param CmsContentTypePlaceHolder Reference to placeholder object
	 */
	public function register_content_type(CmsContentTypePlaceHolder& $obj)
	{
		$this->_load_std_content_types();
		$types = cmsms()->variables['contenttypes'];
		if( isset($types[$obj->type]) ) return;

		$types[$obj->type] = $obj;
		cmsms()->variables['contenttypes'] = $types;
	}



	/**
     * Returns a hash of valid content types (classes that extend ContentBase)
     * The key is the name of the class that would be saved into the database.  The
     * value would be the text returned by the type's FriendlyName() method.
	 *
	 * @param boolean optionally return keys as class names.
	 * @return array List of content types registered in the system.
	 */
	function ListContentTypes($byclassname = false)
	{
		$this->_load_std_content_types();
		$types = cmsms()->variables['contenttypes'];
		if ( isset($types) )
		{
			$result = array();
			foreach( $types as $obj )
				{
					global $CMS_ADMIN_PAGE;
					if( !isset($obj->friendlyname) && isset($obj->friendlyname_key) && isset($CMS_ADMIN_PAGE) )
						{
							$txt = lang($obj->friendlyname_key); 
							$obj->friendlyname = $txt;
						}
					if( $byclassname )
						{
							$result[$obj->class] = $obj->friendlyname;
						}
					else
						{
							$result[$obj->type] = $obj->friendlyname;
						}
				}
			return $result;
		}
	}


    /**
     * Updates the hierarchy position of one item
	 *
	 * @param integer $contentid The content id to update
	 * @return void
     */
	function SetHierarchyPosition($contentid)
	{
		global $gCms;
		$db =& $gCms->GetDb();

		$current_hierarchy_position = '';
		$current_id_hierarchy_position = '';
		$current_hierarchy_path = '';
		$current_parent_id = $contentid;
		$count = 0;

		while ($current_parent_id > -1)
		{
			$query = "SELECT item_order, parent_id, content_alias FROM ".cms_db_prefix()."content WHERE content_id = ?";
			$row = &$db->GetRow($query, array($current_parent_id));
			if ($row)
			{
				$current_hierarchy_position = str_pad($row['item_order'], 5, '0', STR_PAD_LEFT) . "." . $current_hierarchy_position;
				$current_id_hierarchy_position = $current_parent_id . '.' . $current_id_hierarchy_position;
				$current_hierarchy_path = $row['content_alias'] . '/' . $current_hierarchy_path;
				$current_parent_id = $row['parent_id'];
				$count++;
			}
			else
			{
				$current_parent_id = -1;
			}
		}

		if (strlen($current_hierarchy_position) > 0)
		{
			$current_hierarchy_position = substr($current_hierarchy_position, 0, strlen($current_hierarchy_position) - 1);
		}
		if (strlen($current_id_hierarchy_position) > 0)
		{
			$current_id_hierarchy_position = substr($current_id_hierarchy_position, 0, strlen($current_id_hierarchy_position) - 1);
		}
		if (strlen($current_hierarchy_path) > 0)
		{
			$current_hierarchy_path = substr($current_hierarchy_path, 0, strlen($current_hierarchy_path) - 1);
		}

		$query = "SELECT prop_name FROM ".cms_db_prefix()."content_props WHERE content_id = ?";
		$prop_name_array = $db->GetCol($query, array($contentid));

		debug_buffer(array($current_hierarchy_position, $current_id_hierarchy_position, implode(',', $prop_name_array), $contentid));

		$query = "UPDATE ".cms_db_prefix()."content SET hierarchy = ?, id_hierarchy = ?, hierarchy_path = ?, prop_names = ? WHERE content_id = ?";
		$db->Execute($query, array($current_hierarchy_position, $current_id_hierarchy_position, $current_hierarchy_path, implode(',', $prop_name_array), $contentid));

		cms_content_cache::clear();
	}


	/**
	 * Updates the hierarchy position of all items
	 *
	 * @return void
	 */
	function SetAllHierarchyPositions()
	{
		global $gCms;
		$db = $gCms->GetDb();

		$query = "SELECT content_id FROM ".cms_db_prefix()."content";
		$dbresult = &$db->Execute($query);

		while ($dbresult && !$dbresult->EOF)
		{
		    $this->SetHierarchyPosition($dbresult->fields['content_id']);
			$dbresult->MoveNext();
		}
		
		if ($dbresult) $dbresult->Close();
		cms_content_cache::clear();
	}

	
	/**
	 * Loads a set of content objects into the cached tree.
	 *
	 * @param boolean $loadprops If true, load the properties of those content objects
	 * @param boolean $onlyexpanded Not implemented
	 * @param boolean $loadcontent If false, only create the nodes in the tree, 
	 *                             don't load the content objects
	 * @return mixed The cached tree of content
	 */
	function &GetAllContentAsHierarchy($loadcontent = false)
	{
		debug_buffer('', 'starting tree');

		global $gCms;
		$db = &$gCms->GetDb();
		$tree = null;
		$cachefilename = TMP_CACHE_LOCATION . '/contentcache.php';
		$loadedcache = false;

		if (isset($gCms->variables['pageinfo']) && file_exists($cachefilename))
		{
			$pageinfo =& $gCms->variables['pageinfo'];
			if (isset($pageinfo->content_last_modified_date) && $pageinfo->content_last_modified_date < filemtime($cachefilename))
			{
				debug_buffer('file needs loading');
				
				$handle = fopen($cachefilename, "r");
				$data = fread($handle, filesize($cachefilename));
				fclose($handle);
				
				$tree = unserialize(substr($data, 16));
				
				if (strtolower(get_class($tree)) == 'cms_content_tree')
				{
					$loadedcache = true;
				}
				else
				{
					$loadedcache = false;
				}
			}
		}

		if (!$loadedcache)
		{
			$query = "SELECT id_hierarchy,content_alias FROM ".cms_db_prefix()."content ORDER BY hierarchy";
			$dbresult =& $db->Execute($query);
			$nodes = array();
			if ($dbresult && $dbresult->RecordCount() > 0)
			{
				while ($row = $dbresult->FetchRow())
				{
					$nodes[] = $row['id_hierarchy'].','.$row['content_alias'];
				}
			}

			debug_buffer('', 'Start Loading Children into Tree');
			$tree = cms_tree_operations::load_from_list($nodes);
			debug_buffer('', 'End Loading Children into Tree');
		}

		if (!$loadedcache)
		{
			debug_buffer("Serializing...");
			@file_put_contents($cachefilename, '<?php return; ?>'.serialize($tree));
		}

		if( $loadcontent )
		  {
		    $this->LoadChildren(-1, true, true);
		  }

		debug_buffer('', 'ending tree');

		return $tree;
	}

	
	/**
	 * Loads additional, active children into a given tree object
	 *
	 * @param integer $id The parent of the content objects to load into the tree
	 * @param boolean $loadprops If true, load the properties of all loaded content objects
	 * @param boolean $all If true, load all content objects, even inactive ones.
	 * @return void
	 * @author Ted Kulp
	 */
	function LoadChildren($id, $loadprops = false, $all = false, $explicit_ids = array() )
	{	
		global $gCms;
		$db = &$gCms->GetDb();

		$contentrows = '';
		if( is_array($explicit_ids) && count($explicit_ids) )
		{
			$expr = 'content_id IN ('.implode(',',$explicit_ids).')';
			if( !$all ) $expr .= ' AND active = 1';

			$query = 'SELECT * FROM '.cms_db_prefix().'content WHERE '.$expr.' ORDER BY hierarchy';
			$contentrows =& $db->GetArray($query);
		}
		else 
		{
			if( !$id ) { $id = -1; }
			// get the content rows
			$query = "SELECT * FROM ".cms_db_prefix()."content WHERE parent_id = ? AND active = 1 ORDER BY hierarchy";
			if( $all )
				$query = "SELECT * FROM ".cms_db_prefix()."content WHERE parent_id = ? ORDER BY hierarchy";
			$contentrows =& $db->GetArray($query, array($id));
		}
		$contentprops = '';

		// get the content ids from the returned data
		if( $loadprops )
		  {
		    $child_ids = array();
		    for( $i = 0; $i < count($contentrows); $i++ )
		      {
				  $child_ids[] = $contentrows[$i]['content_id'];
		      }
		    
		    // get all the properties for the child_ids
		    $query = 'SELECT * FROM '.cms_db_prefix().'content_props WHERE content_id IN ('.implode(',',$child_ids).') ORDER BY content_id';
		    $tmp =& $db->GetArray($query);

		    // re-organize the tmp data into a hash of arrays of properties for each content id.
		    if( $tmp )
		      {
				  $contentprops = array();
				  for( $i = 0; $i < count($contentrows); $i++ )
					  {
						  $content_id = $contentrows[$i]['content_id'];
						  $t2 = array();
						  for( $j = 0; $j < count($tmp); $j++ )
							  {
								  if( $tmp[$j]['content_id'] == $content_id )
									  {
										  $t2[] = $tmp[$j];
									  }
							  }
						  $contentprops[$content_id] = $t2;
					  }
		      }
		  }
		

		// build the content objects
		for( $i = 0; $i < count($contentrows); $i++ )
		  {
		    $row =& $contentrows[$i];
		    $id = $row['content_id'];

		    if (!in_array($row['type'], array_keys($this->ListContentTypes()))) continue;
		    $contentobj =& $this->CreateNewContent($row['type']);

		    if ($contentobj)
		      {
				  $contentobj->LoadFromData($row, false);
				  if( $loadprops && $contentprops && isset($contentprops[$id]) )
					  {
						  // load the properties from local cache.
						  $props =& $contentprops[$id];
						  $obj =& $contentobj->mProperties;
						  $obj->mPropertyNames = array();
						  $obj->mPropertyTypes = array();
						  $obj->mPropertyValues = array();
						  foreach( $props as $oneprop )
							  {
								  $obj->mPropertyNames[] = $oneprop['prop_name'];
								  $obj->mPropertyTypes[$oneprop['prop_name']] = $oneprop['type'];
								  $obj->mPropertyValues[$oneprop['prop_name']] = $oneprop['content'];
							  }
						  $contentobj->mPropertiesLoaded = true;
					  }
				  
				  // cache the content objects
				  cms_content_cache::add_content($id,$contentobj->Alias(),$contentobj);
		      }
		  }
	}

	/**
	 * Sets the default content to the given id
	 *
	 * @param integer $id The id to set as default
	 * @return void
	 * @author Ted Kulp
	 */
	function SetDefaultContent($id) {
		global $gCms;
		$db = &$gCms->GetDb();
		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE default_content=1";
		$old_id = $db->GetOne($query);
		if (isset($old_id)) 
		{
			$one = new Content();
			$one->LoadFromId($old_id);
			$one->SetDefaultContent(false);
			debug_buffer('save from ' . __LINE__);
			$one->Save();
		}
		$one = new Content();
		$one->LoadFromId($id);
		$one->SetDefaultContent(true);
		debug_buffer('save from ' . __LINE__);
		$one->Save();
	}


	/**
	 * Returns an array of all content objects in the system, active or not.
	 *
	 * @param boolean $loadprops Not implemented
	 * @return array The array of content objects
	 */
	function &GetAllContent($loadprops=true)
	{
		debug_buffer('get all content...');

		global $gCms;
		$tree = $gCms->GetHierarchyManager();
		$list = $tree->getFlatList();

		$output = array();
		foreach( $list as &$one )
		{
			$tmp = $one->GetContent();
			if( is_object($tmp) )
				$output[] = $tmp;
		}

		debug_buffer('end get all content...');
		return $output;
	}


	/**
	 * Create a hierarchical ordered dropdown of all the content objects in the system for use
	 * in the admin and various modules.  If $current or $parent variables are passed, care is taken
	 * to make sure that children which could cause a loop are hidden, in cases of when you're creating
	 * a dropdown for changing a content object's parent.
	 *
	 * @param string $current The currently selected content object.  If none is given, we show all items.
	 * @param string $parent The parent of the currently selected content object. If none is given, we show all items.
	 * @param string $name The html name of the dropdown
	 * @param boolean $allowcurrent Overrides the logic if $current and/or $parent are passed. Defaults to false.
	 * @param boolean $use_perms If true, checks authorship permissions on pages and only shows those the current
	 *                user has access to.
	 * @param boolean $ignore_current Ignores the value of $current totally by not marking any items as invalid.
	 * @param boolean $allow_all If true, show all items, even if the content object 
	 *                           doesn't have a valid link. Defaults to false.
	 * @return string The html dropdown of the hierarchy
	 */
	function CreateHierarchyDropdown($current = '', $parent = '', $name = 'parent_id', $allowcurrent = 0, $use_perms = 0, $ignore_current = 0, $allow_all = false)
	{
		$result = '';
		$userid = -1;

		$allcontent =& $this->GetAllContent(false);

		if ($allcontent !== FALSE && count($allcontent) > 0)
		{
			if( $use_perms )
			  {
			    $userid = get_userid();
			  }
			if( ($userid > 0 && check_permission($userid,'Manage All Content')) || 
			    $userid == -1 ||
			    $parent == -1 )
			  {
			    $result .= '<option value="-1">'.lang('none').'</option>';
			  }
			$curhierarchy = '';

			foreach ($allcontent as $one)
 			{
				if( !is_object($one) ) continue;
			  $value = $one->Id();
			  if ($value == $current)
			    {
			      // Grab hierarchy just in case we need to check children
			      // (which will always be after)
			      $curhierarchy = $one->Hierarchy();
			      
			      if( !$allowcurrent )
				{
				  // Then jump out.  We don't want ourselves in the list.
				  continue;
				}
			      $value = -1;
			    }

			  // If it doesn't have a valid link...
			  // don't include it.
			  if( !$allow_all && !$one->HasUsableLink() )
			    {
			      continue;
			    }

			  // If it's a child of the current, we don't want to show it as it
			  // could cause a deadlock.
			  if (!$allowcurrent && 
			      $curhierarchy != '' && 
			      strstr($one->Hierarchy() . '.', $curhierarchy . '.') == $one->Hierarchy() . '.')
			    {
			      continue;
			    }

                          // If we have a valid userid... only include pages where this user
                          // has write access... or is an admin user... or has appropriate permission.
			  if( $userid > 0 && $one->Id() != $parent)
			    {
			      if( !check_permission($userid,'Manage All Content') && 
				  !check_authorship($userid,$one->Id()) )
				{
				  continue;
				}
			    }				

			  // Don't include content types that do not want children either...
			  if (!$one->WantsChildren()) continue;
			  {
			    $result .= '<option value="'.$value.'"';
			    
			    // Select current parent if it exists
			    if ($one->Id() == $parent)
			      {
				$result .= ' selected="selected"';
			      }
			    
			    if( ($value == -1) && ($ignore_current == 0) )
			      {
				$result .= '>'.$one->Hierarchy().'. - '.$one->Name().' ('.lang('invalid').')</option>';
			      }
			    else
			      {
				$result .= '>'.$one->Hierarchy().'. - '.$one->Name().'</option>';
			      }
			  }
			}

		}

		if( !empty($result) )
			{
				$result = '<select name="'.$name.'">'.$result.'</select>';
			}

		return $result;
	}


	/**
	 * Gets the content id of the page marked as default
	 *
	 * @return integer The id of the default page. false if not found.
	 */
	function GetDefaultPageID()
	{
		return $this->GetDefaultContent();
	}


	/**
	 * Returns the content id given a valid content alias.
	 *
	 * @param string $alias The alias to query
	 * @return integer The resulting id.  false if not found.
	 */
	function GetPageIDFromAlias( $alias )
	{
		$res = cms_content_cache::get_id_from_alias($alias);
		if( $res !== FALSE ) return $res;

		global $gCms;
		$db = &$gCms->GetDb();

		if (is_numeric($alias) && strpos($alias,'.') == FALSE && strpos($alias,',') == FALSE)
		{
			return $alias;
		}

		$params = array($alias);
		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE content_alias = ?";
		$row = $db->GetRow($query, $params);

		if (!$row)
		{
			return false;
		}
		
		return $row['content_id'];
	}

	
	/**
	 * Returns the content id given a valid hierarchical position.
	 *
	 * @param string $position The position to query
	 * @return integer The resulting id.  false if not found.
	 */
	function GetPageIDFromHierarchy($position)
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE hierarchy = ?";
		$row = $db->GetRow($query, array($this->CreateUnfriendlyHierarchyPosition($position)));

		if (!$row)
		{
			return false;
		}
		return $row['content_id'];
	}


	/**
	 * Returns the content alias given a valid content id.
	 *
	 * @param integer $id The content id to query
	 * @return string The resulting content alias.  false if not found.
	 */
	function GetPageAliasFromID( $id )
	{
		$res = cms_content_cache::get_alias_from_id($alias);
		if( $res !== FALSE ) return $res;

		global $gCms;
		$db = &$gCms->GetDb();

		if (!is_numeric($id) && strpos($id,'.') == TRUE && strpos($id,',') == TRUE)
		{
			return $id;
		}

		$params = array($id);
		$query = "SELECT content_alias FROM ".cms_db_prefix()."content WHERE content_id = ?";
		$row = $db->GetRow($query, $params);

		if ( !$row )
		{
			return false;
		}
		return $row['content_alias'];
	}


	/**
	 * Checks to see if a content alias is valid and not in use.
	 *
	 * @param string $alias The content alias to check
	 * @param string $content_id The id of the current page, for used alias checks on existing pages
	 * @return string The error, if any.  If there is no error, returns FALSE.
	 */
	function CheckAliasError($alias, $content_id = -1)
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$error = FALSE;

		if (preg_match('/^\d+$/', $alias))
		{
			$error = lang('aliasnotaninteger');
		}
		else if (!preg_match('/^[\-\_\w]+$/', $alias))
		{
			$error = lang('aliasmustbelettersandnumbers');
		}
		else
		{
			$params = array($alias);
			$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE content_alias = ?";
			if ($content_id > -1)
			{
				$query .= " AND content_id != ?";
				$params[] = $content_id;
			}
			$row = &$db->GetRow($query, $params);

			if ($row)
			{
				$error = lang('aliasalreadyused');
			}
		}

		return $error;
	}
	
	/**
	 * Clears the content cache
	 *
	 * @return void
	 */
	function ClearCache()
	{
		global $gCms;
		$smarty =& $gCms->GetSmarty();

		cms_content_cache::clear();
		unset($gCms->hrinstance);
		$smarty->clear_all_cache();
		$smarty->clear_compiled_tpl();

		if (is_file(TMP_CACHE_LOCATION . '/contentcache.php'))
		{
			unlink(TMP_CACHE_LOCATION . '/contentcache.php');
		}

		@touch(cms_join_path(TMP_CACHE_LOCATION,'index.html'));
		@touch(cms_join_path(TMP_TEMPLATES_C_LOCATION,'index.html'));
	}

	/**
	 * Converts a friendly hierarchy (1.1.1) to an unfriendly hierarchy (00001.00001.00001) for
	 * use in the database.
	 *
	 * @param string $position The hierarchy position to convert
	 * @return string The unfriendly version of the hierarchy string
	 */
	function CreateFriendlyHierarchyPosition($position)
	{
		#Change padded numbers back into user-friendly values
		$tmp = '';
    $levels = preg_split('/\./', $position);
    
		foreach ($levels as $onelevel)
		{
			$tmp .= ltrim($onelevel, '0') . '.';
		}
		$tmp = rtrim($tmp, '.');
		return $tmp;
	}

	/**
	 * Converts an unfriendly hierarchy (00001.00001.00001) to a friendly hierarchy (1.1.1) for
	 * use in the database.
	 *
	 * @param string $position The hierarchy position to convert
	 * @return string The friendly version of the hierarchy string
	 */
	function CreateUnfriendlyHierarchyPosition($position)
	{
		#Change user-friendly values into padded numbers
		$tmp = '';
        
    $levels = preg_split('/\./', $position);
    
		foreach ($levels as $onelevel)
		{
			$tmp .= str_pad($onelevel, 5, '0', STR_PAD_LEFT) . '.';
		}
		$tmp = rtrim($tmp, '.');
		return $tmp;
	}


	/**
	 * Grab URLs from the content table and register them with the route manager.
	 *
	 * @since 1.9
	 * @author Robert Campbell <calguy1000@hotmail.com>
	 * @internal
	 */
	public function register_routes()
	{
 		global $gCms;
 		$db = $gCms->GetDb();

		$query = 'SELECT content_id,page_url FROM '.cms_db_prefix().'content
                   WHERE active = 1 AND default_content = 0 AND page_url != \'\'';
		$data = $db->GetArray($query);
 		if( is_array($data) )
 		{
 			foreach( $data as $onerow )
 			{
 				$route = new CmsRoute($onerow['page_url'],$onerow['content_id'],'',TRUE);
				cms_route_manager::register($route);
			}
		}
	}
}

/**
 * @package CMS
 * @ignore
 */
class ContentManager extends ContentOperations
{
}

?>
