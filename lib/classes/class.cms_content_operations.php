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

class CmsContentOperations extends CmsObject
{	
	static private $instance = NULL;
	
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsContentOperations();
		}
		return self::$instance;
	}

	static function load_content_type($type)
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

	static function LoadContentType($type)
	{
		return ContentOperations::load_content_type($type);
	}
	
	static function find_content_types()
	{
		$contenttypes =& cmsms()->contenttypes;
		$dir = cms_join_path(dirname(__FILE__),'contenttypes');
		$handle=opendir($dir);
		while ($file = readdir ($handle)) 
		{
		    $path_parts = pathinfo($file);
		    if ($path_parts['extension'] == 'php')
		    {
				$obj = new CmsContentTypePlaceholder();
				$obj->type = strtolower(basename($file, '.inc.php'));
				$obj->filename = cms_join_path($dir,$file);
				$obj->loaded = false;
				$obj->friendlyname = basename($file, '.inc.php');
				$contenttypes[strtolower(basename($file, '.inc.php'))] = $obj;
		    }
		}
		closedir($handle);
	}

	function &CreateNewContent($type)
	{
		$type = strtolower($type);

		$result = NULL;
		
		if (ContentOperations::LoadContentType($type))
		{
			$result = new $type;
		}
		
		return $result;
	}
	
    /**
     * Determine proper type of object, load it and return it
     */
	function &LoadContentFromId($id,$loadprops=true)
	{
		$result = FALSE;

		global $gCms;
		$db = &$gCms->GetDb();
		
		$result = cmsms()->content_base->find_by_id($id);
		
		return $result;

		/*
		$query = "SELECT * FROM ".cms_db_prefix()."content WHERE id = ?";
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
					$contentobj->LoadFromData($row, FALSE);
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
		*/
	}

	function &LoadContentFromAlias($alias, $only_active = false)
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$row = '';

		if (is_numeric($alias) && strpos($alias,'.') === FALSE && strpos($alias,',') === FALSE) //Fix for postgres
		{
			$query = "SELECT * FROM ".cms_db_prefix()."content WHERE id = ?";
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
				return FALSE;
			}
		}
		else
		{
			return FALSE;
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
		global $gCms, $config, $sql_queries, $debug_errors;
		$cpt = count($ids);
		$contents=array();
		if ($cpt==0) 
		{
			return $contents;
		}
		$db = &$gCms->GetDb();
		$id_list = '(';
		for ($i=0;$i<$cpt;$i++) 
		{
			$id_list .= $ids[$i];
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
		$query  = "SELECT * FROM ".cms_db_prefix()."content WHERE id IN $id_list";
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
		global $gCms, $config, $sql_queries, $debug_errors;
		$cpt = count($ids);
		$contents=array();
		if ($cpt == 0)
		{
			return $contents;
		}
		$db = &$gCms->GetDb();
		$id_list = '(';
		for ($i=0; $i<$cpt; $i++) 
		{
			$id_list .= "'".$ids[$i]."'";
			if ($i<$cpt-1)
			{
				$id_list .= ',';
			}
		}
		$id_list .= ')';
		if ($id_list == '()')
		{
			return $contents;
		}
		$result = false;
		$query  = "SELECT * FROM ".cms_db_prefix()."content WHERE content_alias IN $id_list";
		$rows   =& $db->Execute($query);

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

	/**
	 * Returns the id of the page that is marked as the default
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public static function get_default_page_id()
	{
		$result = -1;

		$query = "SELECT id FROM ".cms_db_prefix()."content WHERE default_content = 1";
		$row = cms_db()->GetRow($query);
		if ($row)
		{
			$result = $row['id'];
		}
		else
		{
			#Just get something...
			$query = "SELECT id FROM ".cms_db_prefix()."content";
			$row = db()->GetRow($query);
			if ($row)
			{
				$result = $row['id'];
			}
		}

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
			$query = "SELECT item_order, parent_id, content_alias FROM ".cms_db_prefix()."content WHERE id = ?";
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

		$query = "UPDATE ".cms_db_prefix()."content SET hierarchy = ?, id_hierarchy = ?, hierarchy_path = ?, prop_names = ? WHERE id = ?";
		$db->Execute($query, array($current_hierarchy_position, $current_id_hierarchy_position, $current_hierarchy_path, implode(',', $prop_name_array), $contentid));
	}

    /**
     * Updates the hierarchy position of all items
     */
	function SetAllHierarchyPositions()
	{
		global $gCms;
		$db = $gCms->GetDb();

		$query = "SELECT id FROM ".cms_db_prefix()."content";
		$dbresult = &$db->Execute($query);

		while ($dbresult && !$dbresult->EOF)
		{
			ContentOperations::SetHierarchyPosition($dbresult->fields['id']);
			$dbresult->MoveNext();
		}
		
		if ($dbresult) $dbresult->Close();
	}
	
	function &GetAllContentAsHierarchy($loadprops, $onlyexpanded=null)
	{
		debug_buffer('', 'starting tree');

		//require_once(dirname(dirname(__FILE__)).'/Tree/Tree.php');

		$nodes = array();
		global $gCms;
		$db = &$gCms->GetDb();

		$loadedcache = false;

		/*
		$cachefilename = TMP_CACHE_LOCATION . '/contentcache.php';
		$usecache = true;
		if (isset($onlyexpanded) || isset($CMS_ADMIN_PAGE))
		{
			$usecache = false;
		}

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
		*/

		if (!$loadedcache)
		{
			$tree = new CmsPageTree();

			$query = "SELECT id_hierarchy, show_in_menu, active FROM ".cms_db_prefix()."content ORDER BY hierarchy";
			$dbresult =& $db->Execute($query);

			if ($dbresult && $dbresult->RecordCount() > 0)
			{
				/*
				while ($row = $dbresult->FetchRow())
				{
					$nodes[] = $row['id_hierarchy'];
				}
				*/
				$tree->fill_from_db($dbresult);
			}

			//debug_buffer('', 'Start Loading Children into Tree');
			
			//print_r($tree);
			//debug_buffer('', 'End Loading Children into Tree');
		}

		/*
		if (!$loadedcache && $usecache)
		{
			debug_buffer("Serializing...");
			$handle = fopen($cachefilename, "w");
			fwrite($handle, '<?php return; ?>'.serialize($tree));
			fclose($handle);
		}
		*/

		ContentOperations::LoadChildrenIntoTree(-1, $tree);

		debug_buffer('', 'ending tree');

		return $tree;
	}
	
	function LoadChildrenIntoTree($id, &$tree, $loadprops = false)
	{	
		$result = cmsms()->content_base->find_all_by_parent_id($id);
		
		$contentcache =& $tree->content;
		foreach ($result as $one)
		{
			if (!array_key_exists($one->id, $contentcache))
				$contentcache[$one->id] = $one;
		}
	}

	function get_all_content($loadprops=true)
	{
		return cmsms()->content_base->find_all(array('order' => 'hierarchy ASC'));
	}
	
	function GetAllContent($loadprops=true)
	{
		return CmsContentOperations::get_all_content($loadprops);
	}

	function CreateHierarchyDropdown($current = '', $parent = '', $name = 'parent_id')
	{
		$result = '';

		$allcontent = cmsms()->GetContentOperations()->GetAllContent(false);

		if ($allcontent !== FALSE && count($allcontent) > 0)
		{
			$result .= '<select name="'.$name.'">';
			$result .= '<option value="-1">None</option>';

			$curhierarchy = '';

			foreach ($allcontent as $one)
			{
				if ($one->id == $current)
				{
					#Grab hierarchy just in case we need to check children
					#(which will always be after)
					$curhierarchy = $one->hierarchy;

					#Then jump out.  We don't want ourselves in the list.
					continue;
				}
				#If it's a child of the current, we don't want to show it as it
				#could cause a deadlock.
				if ($curhierarchy != '' && strstr($one->hierarchy . '.', $curhierarchy . '.') == $one->hierarchy . '.')
				{
					continue;
				}
				#Don't include content types that do not want children either...
				if ($one->WantsChildren() == true)
				{
					$result .= '<option value="'.$one->id.'"';

					#Select current parent if it exists
					if ($one->id == $parent)
					{
						$result .= ' selected="selected"';
					}

					$result .= '>'.$one->hierarchy().'. - '.$one->name.'</option>';
				}
			}

			$result .= '</select>';
		}

		return $result;
	}


    // function to get the id of the default page
	function GetDefaultPageID()
	{
		$db = cms_db();

		$query = "SELECT * FROM ".cms_db_prefix()."content WHERE default_content = 1";
		$row = &$db->GetRow($query);
		if (!$row)
		{
			return false;
		}
		return $row['id'];
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
		$query = "SELECT * FROM ".cms_db_prefix()."content WHERE content_alias = ?";
		$row = $db->GetRow($query, $params);

		if (!$row)
		{
			return false;
		}
		
		return $row['id'];
	}
	
	function GetPageIDFromHierarchy($position)
	{
		global $gCms;
		$db = &$gCms->GetDb();

		$query = "SELECT * FROM ".cms_db_prefix()."content WHERE hierarchy = ?";
		$row = $db->GetRow($query, array(ContentOperations::CreateUnfriendlyHierarchyPosition($position)));

		if (!$row)
		{
			return false;
		}
		return $row['id'];
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
		$query = "SELECT * FROM ".cms_db_prefix()."content WHERE id = ?";
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
			$query = "SELECT * FROM ".cms_db_prefix()."content WHERE content_alias = ?";
			if ($content_id > -1)
			{
				$query .= " AND id != ?";
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
	
	static function clear_cache()
	{
		$smarty = smarty();

		$smarty->clear_all_cache();
		$smarty->clear_compiled_tpl();

		if (is_file(TMP_CACHE_LOCATION . '/contentcache.php'))
		{
			unlink(TMP_CACHE_LOCATION . '/contentcache.php');
		}
	}
	
	static function ClearCache()
	{
		CmsContentOperations::clear_cache();
	}
	
	public static function create_friendly_hierarchy_position($position)
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

	public static function CreateFriendlyHierarchyPosition($position)
	{
		return CmsContentOperations::create_friendly_hierarchy_position($position);
	}
	
	public static function create_unfriendly_hierarchy_position($position)
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

	public static function CreateUnfriendlyHierarchyPosition($position)
	{
		return CmsContentOperations::create_friendly_hierarchy_position($position);
	}
	
	public static function do_cross_reference($parent_id, $parent_type, $content)
	{
		$db = db();

		//Delete old ones from the database
		$query = 'DELETE FROM '.cms_db_prefix().'crossref WHERE parent_id = ? AND parent_type = ?';
		$db->Execute($query, array($parent_id, $parent_type));

		//Do global content blocks
		$matches = array();
		preg_match_all('/\{(?:html_blob|global_content).*?name=["\']([^"]+)["\'].*?\}/', $content, $matches);
		if (isset($matches[1]))
		{
			$selquery = 'SELECT htmlblob_id FROM '.cms_db_prefix().'htmlblobs WHERE htmlblob_name = ?';
			$insquery = 'INSERT INTO '.cms_db_prefix().'crossref (parent_id, parent_type, child_id, child_type, create_date, modified_date)
							VALUES (?,?,?,\'global_content\','.$db->DBTimeStamp(time()).','.$db->DBTimeStamp(time()).')';
			foreach ($matches[1] as $name)
			{
				$result = &$db->Execute($selquery, array($name));
				while ($result && !$result->EOF)
				{
					$db->Execute($insquery, array($parent_id, $parent_type, $result->fields['htmlblob_id']));
					$result->MoveNext();
				}
				if ($result) $result->Close();
			}
		}
	}

	public static function remove_cross_references($parent_id, $parent_type)
	{
		$db = db();

		//Delete old ones from the database
		$query = 'DELETE FROM '.cms_db_prefix().'crossref WHERE parent_id = ? AND parent_type = ?';
		$db->Execute($query, array($parent_id, $parent_type));
	}

	public static function remove_cross_references_by_child($child_id, $child_type)
	{
		$db = db();

		//Delete old ones from the database
		$query = 'DELETE FROM '.cms_db_prefix().'crossref WHERE child_id = ? AND child_type = ?';
		$db->Execute($query, array($child_id, $child_type));
	}

}

class ContentOperations extends CmsContentOperations
{
}

class ContentManager extends ContentOperations
{
}

?>