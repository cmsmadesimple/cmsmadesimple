<?php

# CMS - CMS Made Simple
# (c)2004 by Ted Kulp (tedkulp@users.sf.net)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * Class for static methods related to content
 *
 * @since		0.8
 * @package		CMS
 */

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.content.inc.php');

class ContentOperations
{
	function LoadContentType($type)
	{
		$type = strtolower($type);

		global $gCms;
		$contenttypes =& $gCms->contenttypes;
		
		if (isset($contenttypes[$type]))
		{
			$placeholder =& $contenttypes[$type];
			if ($placeholder->loaded == false)
			{
				include_once($placeholder->filename);
				$placeholder->loaded = true;
			}
			return true;
		}
		return false;
	}

	function &LoadContentFromSerializedData(&$data)
	{
	  if( !isset($data['content_type']) && !isset($data['serialized_content']) ) return FALSE;

	  $contenttype = 'content';
	  if( isset($data['content_type']) ) $contenttype = $data['content_type'];

	  $contentobj =& ContentOperations::CreateNewContent($contenttype);
	  $contentobj = unserialize($data['serialized_content']);
	  return $contentobj;
	}

	function &CreateNewContent($type)
	{
		$type = strtolower($type);

		$result = NULL;
		
		if (ContentOperations::LoadContentType($type))
		{
			$result =& new $type;
		}
		
		return $result;
	}
	
    /**
     * Determine proper type of object, load it and return it
     */
	function &LoadContentFromId($id,$loadprops=false)
	{
		$result = FALSE;

		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT * FROM ".cms_db_prefix()."content WHERE content_id = ?";
		$row = &$db->GetRow($query, array($id));
		if ($row)
		{
			#Make sure the type exists.  If so, instantiate and load
			if (in_array($row['type'], array_keys(ContentOperations::ListContentTypes())))
			{
				$classtype = strtolower($row['type']);
				$contentobj =& ContentOperations::CreateNewContent($classtype);
				if ($contentobj)
				{
					$contentobj->LoadFromData($row, $loadprops);
				}
				return $contentobj;
			}
			else
			{
				return $result;
			}
		}
		else
		{
			return $result;
		}
	}

	function &LoadContentFromAlias($alias, $only_active = false)
	{
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
			if (in_array($row['type'], array_keys(ContentOperations::ListContentTypes())))
			{
				$classtype = strtolower($row['type']);
				$contentobj =& ContentOperations::CreateNewContent($classtype);
				$contentobj->LoadFromData($row, TRUE);
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
     * Load the content of the object from a list of ID
     * Private method.
     * @param $ids	array of element ids
     * @param $loadProperties	whether to load or not the properties
     *
     * @returns array of content objects (empty if not found)
     */
	/*private*/ function &LoadMultipleFromId($ids, $loadProperties = false)
	{
		global $gCms, $sql_queries, $debug_errors;
		$cpt = count($ids);
		$contents=array();
		if ($cpt==0) 
		{
			return $contents;
		}
		$config = &$gCms->GetConfig();
		$db = &$gCms->GetDb();
		$id_list = '(';
		for ($i=0;$i<$cpt;$i++) 
		{
			$id_list .= (int)$ids[$i];
			if ($i<$cpt-1)
			{
				$id_list .= ',';
			}
		}
		$id_list .= ')';
		if ($id_list=='()') 
		{
		  return $contents;
		}
		$result = false;
		$query  = "SELECT * FROM ".cms_db_prefix()."content WHERE content_id IN $id_list";
		$rows   =& $db->Execute($query);

		if ($rows)
		{
			while (isset($rows) && $row = &$rows->FetchRow())
			{
				if (in_array($row['type'], array_keys(ContentOperations::ListContentTypes()))) 
				{
					$classtype = strtolower($row['type']);
					$contentobj =& ContentOperations::CreateNewContent($classtype);
					$contentobj->LoadFromData($row,false);
					$contents[]=$contentobj;
					$result = true;
				}
			}
			$rows->Close();
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
			foreach ($contents as $content) 
			{
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

		foreach ($contents as $content) 
		{
			$content->Load();
		}

		return $contents;
	}
	
    /**
     * Load the content of the object from a list of aliases
     * Private method.
     * @param $ids	array of element ids
     * Private method
     *
     * @param $alis				the alias of the element
     * @param $loadProperties	whether to load or not the properties
     *
     * @returns array of content objects (empty if not found)
     */
	/*private*/function &LoadMultipleFromAlias($ids, $loadProperties = false)
	{
		global $gCms, $sql_queries, $debug_errors;
		$contents=array();
		if (!is_array($ids) || count($ids) == 0)
		{
			return $contents;
		}
		$db = &$gCms->GetDb();
		$config =& $gCms->GetConfig();

		$param_qs = array();
		for ($i=0; $i<count($ids); $i++) 
		{
			$param_qs[] = '?';
		}

		$result = false;
		$query  = "SELECT * FROM ".cms_db_prefix()."content WHERE content_alias IN " . join(', ', $param_qs);
		$rows   =& $db->Execute($query, $ids);

		while (isset($rows) && $row=&$rows->FetchRow())
		{
			#Make sure the type exists.  If so, instantiate and load
			if (in_array($row['type'], array_keys(ContentOperations::ListContentTypes()))) 
			{
				$classtype = strtolower($row['type']);
				$contentobj =& ContentOperations::CreateNewContent($classtype);
				$contentobj->LoadFromData($row,false);
				$contents[] =& $contentobj;
				$result = true;
			}
		}

		if ($rows) $rows->Close();

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
			foreach ($contents as $content) 
			{
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
		foreach ($contents as $content) 
		{
			$content->Load();
		}
		return $contents;
	}


    /**
     * Display content
     */
	function DisplayContent($content)
	{
		//This should be straight forward, since the content will pretty much determine how it is displayed
		$content->Show();
	}

    /**
     * Determine if content should be loaded from cache
     */
    function IsCached($id)
    {
    }

	function & GetDefaultContent()
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
     * Returns a hash of valid content types (classes that extend ContentBase)
     * The key is the name of the class that would be saved into the dabase.  The
     * value would be the text returned by the type's FriendlyName() method.
     */
	function &ListContentTypes()
	{
		global $gCms;
		$contenttypes =& $gCms->contenttypes;
		
		if (isset($gCms->variables['contenttypes']))
		{
			$variables =& $gCms->variables;
			return $variables['contenttypes'];
		}
		
		$result = array();
		
		reset($contenttypes);
		while (list($key) = each($contenttypes))
		{
			$value =& $contenttypes[$key];
			$result[] = $value->type;
		}
		
		$variables =& $gCms->variables;
		$variables['contenttypes'] =& $result;

		return $result;
	}

    /**
     * Updates the hierarchy position of one item
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
	}

    /**
     * Updates the hierarchy position of all items
     */
	function SetAllHierarchyPositions()
	{
		global $gCms;
		$db = $gCms->GetDb();

		$query = "SELECT content_id FROM ".cms_db_prefix()."content";
		$dbresult = &$db->Execute($query);

		while ($dbresult && !$dbresult->EOF)
		{
			ContentOperations::SetHierarchyPosition($dbresult->fields['content_id']);
			$dbresult->MoveNext();
		}
		
		if ($dbresult) $dbresult->Close();
	}
	
	function &GetAllContentAsHierarchy($loadprops, $onlyexpanded=null, $loadcontent = false)
	{
		debug_buffer('', 'starting tree');

		require_once(dirname(dirname(__FILE__)).'/Tree/Tree.php');

		$nodes = array();
		global $gCms;
		$db = &$gCms->GetDb();

		$cachefilename = TMP_CACHE_LOCATION . '/contentcache.php';
		$usecache = true;
		if (isset($onlyexpanded) || isset($CMS_ADMIN_PAGE))
		{
			#$usecache = false;
		}

		$loadedcache = false;

		if ($usecache)
		{
			if (isset($gCms->variables['pageinfo']) && file_exists($cachefilename))
			{
				$pageinfo =& $gCms->variables['pageinfo'];
				//debug_buffer('content cache file exists... file: ' . filemtime($cachefilename) . ' content:' . $pageinfo->content_last_modified_date);
				if (isset($pageinfo->content_last_modified_date) && $pageinfo->content_last_modified_date < filemtime($cachefilename))
				{
					debug_buffer('file needs loading');

					$handle = fopen($cachefilename, "r");
					$data = fread($handle, filesize($cachefilename));
					fclose($handle);

					$tree = unserialize(substr($data, 16));

					#$variables =& $gCms->variables;
					#$variables['contentcache'] =& $tree;
					if (strtolower(get_class($tree)) == 'tree')
					{
						$loadedcache = true;
					}
					else
					{
						$loadedcache = false;
					}
				}
			}
		}

		if (!$loadedcache)
		{
			$query = "SELECT id_hierarchy FROM ".cms_db_prefix()."content ORDER BY hierarchy";
			$dbresult =& $db->Execute($query);

			if ($dbresult && $dbresult->RecordCount() > 0)
			{
				while ($row = $dbresult->FetchRow())
				{
					$nodes[] = $row['id_hierarchy'];
				}
			}

			$tree = &new Tree();
			debug_buffer('', 'Start Loading Children into Tree');
			$tree = &Tree::createFromList($nodes, '.');
			debug_buffer('', 'End Loading Children into Tree');
		}

		if (!$loadedcache && $usecache)
		{
			debug_buffer("Serializing...");
			$handle = fopen($cachefilename, "w");
			fwrite($handle, '<?php return; ?>'.serialize($tree));
			fclose($handle);
		}

		if( $loadcontent )
		  {
		    ContentOperations::LoadChildrenIntoTree(-1, $tree, false, true);
		  }

		debug_buffer('', 'ending tree');

		return $tree;
	}
	
	function LoadChildrenIntoTree($id, &$tree, $loadprops = false, $all = false)
	{	
		global $gCms;
		$db = &$gCms->GetDb();

		// get the content rows
		$query = "SELECT * FROM ".cms_db_prefix()."content WHERE parent_id = ? AND active = 1 ORDER BY hierarchy";
		if( $all )
		  $query = "SELECT * FROM ".cms_db_prefix()."content WHERE parent_id = ? ORDER BY hierarchy";
		$contentrows =& $db->GetArray($query, array($id));
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

		    if (!in_array($row['type'], array_keys(ContentOperations::ListContentTypes()))) continue;
		    $contentobj =& ContentOperations::CreateNewContent($row['type']);
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
			$contentcache =& $tree->content;
			$contentcache[$id] =& $contentobj;
		      }
		  }
	}

	/**
	*  Sets the default content as id
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

	function &GetAllContent($loadprops=true)
	{
		debug_buffer('get all content...');

		global $gCms;

		$contentcache = array();

		$db = &$gCms->GetDb();
		$query = "SELECT * FROM ".cms_db_prefix()."content ORDER BY hierarchy";
		$dbresult = &$db->Execute($query);

		$map = array();
		$count = 0;

		while ($dbresult && !$dbresult->EOF)
		{
			#Make sure the type exists.  If so, instantiate and load
			if (in_array($dbresult->fields['type'], array_keys(ContentOperations::ListContentTypes())))
			{
				$contentobj =& ContentOperations::CreateNewContent($dbresult->fields['type']);
				if (isset($contentobj))
				{
					$tmp = $dbresult->FetchRow();
					$contentobj->LoadFromData($tmp, false);
					$map[$contentobj->Id()] = $count;
					$contentcache[] = $contentobj;
					$count++;
				}
				else
				{
					$dbresult->MoveNext();
				}
			}
			else
			{
				$dbresult->MoveNext();
			}
		}

		if ($dbresult) $dbresult->Close();

		for ($i=0;$i<$count;$i++)
		{
			if ($contentcache[$i]->ParentId() != -1 && isset($map[$contentcache[$i]->ParentId()]))
			{
				$contentcache[$map[$contentcache[$i]->ParentId()]]->mChildCount++;
			}
		}

		return $contentcache;
	}

	function CreateHierarchyDropdown($current = '', $parent = '', $name = 'parent_id', $allowcurrent = 0, $use_perms = 0, $ignore_current = 0)
	{
		$result = '';
		$userid = -1;

		$allcontent =& ContentOperations::GetAllContent();

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


    // function to get the id of the default page
	function GetDefaultPageID()
	{
	  
	  global $gCms;
	  if( isset($gCms->variables['default_content_id']) )
	    {
	      return $gCms->variables['default_content_id'];
	    }

		$db = &$gCms->GetDb();

		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE default_content = 1";
		$row = &$db->GetRow($query);
		if (!$row)
		{
			return false;
		}
		$gCms->variables['default_content_id'] = $row['content_id'];
		return $row['content_id'];
	}


    // function to map an alias to a page id
    // returns false if nothing cound be found.
	function GetPageIDFromAlias( $alias )
	{
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
	
	function GetPageIDFromHierarchy($position)
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE hierarchy = ?";
		$row = $db->GetRow($query, array(ContentOperations::CreateUnfriendlyHierarchyPosition($position)));

		if (!$row)
		{
			return false;
		}
		return $row['content_id'];
	}


    // function to map an alias to a page id
    // returns false if nothing cound be found.
	function GetPageAliasFromID( $id )
	{
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
	
	function ClearCache()
	{
		global $gCms;
		$smarty =& $gCms->GetSmarty();

		$smarty->clear_all_cache();
		$smarty->clear_compiled_tpl();

		if (is_file(TMP_CACHE_LOCATION . '/contentcache.php'))
		{
			unlink(TMP_CACHE_LOCATION . '/contentcache.php');
		}

		@touch(cms_join_path(TMP_CACHE_LOCATION,'index.html'));
		@touch(cms_join_path(TMP_TEMPLATES_C_LOCATION,'index.html'));
	}

	function CreateFriendlyHierarchyPosition($position)
	{
		#Change padded numbers back into user-friendly values
		$tmp = '';
		$levels = split('\.', $position);
		foreach ($levels as $onelevel)
		{
			$tmp .= ltrim($onelevel, '0') . '.';
		}
		$tmp = rtrim($tmp, '.');
		return $tmp;
	}

	function CreateUnfriendlyHierarchyPosition($position)
	{
		#Change user-friendly values into padded numbers
		$tmp = '';
		$levels = split('\.', $position);
		foreach ($levels as $onelevel)
		{
			$tmp .= str_pad($onelevel, 5, '0', STR_PAD_LEFT) . '.';
		}
		$tmp = rtrim($tmp, '.');
		return $tmp;
	}
	
}

class ContentManager extends ContentOperations
{
}

?>
