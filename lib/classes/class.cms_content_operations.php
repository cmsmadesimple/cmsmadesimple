<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
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
 * Class for static methods related to content
 *
 * @author Ted Kulp
 * @since 0.8
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
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
	
	/**
	 * Returns an array of available content types.
	 *
	 * @return array Array of CmsContenttypePlaceholder objects
	 * @author Ted Kulp
	 **/
	public static function find_content_types()
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
	
	/**
	 * Returns an array of the available block types.
	 *
	 * @return array Array of CmsBlockTypePlaceholder objects
	 * @author Ted Kulp
	 **/
	public static function find_block_types()
	{
		$blocktypes =& cmsms()->blocktypes;
		
		#Load block types
		$dir = cms_join_path(ROOT_DIR,'lib','classes','blocktypes');
		$handle=opendir($dir);
		while ($file = readdir ($handle)) 
		{
		    $path_parts = pathinfo($file);
		    if ($path_parts['extension'] == 'php')
		    {
				$obj = new CmsBlockTypePlaceholder();
				$obj->type = str_replace('block.', '', strtolower(basename($file, '.inc.php')));
				$obj->filename = cms_join_path($dir, $file);
				$obj->loaded = false;
				$obj->friendlyname = str_replace('block.', '', basename($file, '.inc.php'));
				$blocktypes[str_replace('block.', '', strtolower(basename($file, '.inc.php')))] = $obj;
		    }
		}
		closedir($handle);
		
		return $blocktypes;
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
	 * @deprecated Deprecated.  Use cmsms()->content_base->find_by_id($id) instead.
	 **/
	function LoadContentFromId($id,$loadprops=true)
	{	
		return cmsms()->content_base->find_by_id($id);
	}

	/**
	 * Returns the id of the page that is marked as the default
	 *
	 * @return integer Id of the default page
	 * @author Ted Kulp
	 **/
	public static function get_default_page_id()
	{
		return CmsCache::get_instance()->call('CmsContentOperations::_get_default_page_id');
	}
	
	/**
	 * Non-cached version of get_default_page_id.
	 *
	 * @return integer Id of the default page
	 * @author Ted Kulp
	 **/
	public static function _get_default_page_id()
	{
		$page = cmsms()->content_base->find_by_default_content(1);
		if ($page)
		{
			return $page->id;
		}
		return -1;
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
	public static function SetHierarchyPosition($contentid)
	{
		global $gCms;
		$db = $gCms->GetDb();

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
	public static function SetAllHierarchyPositions()
	{
		global $gCms;
		$db = $gCms->GetDb();

		$query = "SELECT id FROM ".cms_db_prefix()."content";
		$dbresult = &$db->Execute($query);

		while ($dbresult && !$dbresult->EOF)
		{
			self::SetHierarchyPosition($dbresult->fields['id']);
			$dbresult->MoveNext();
		}
		
		if ($dbresult) $dbresult->Close();
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsContentOperations::load_children_into_tree($id, $tree) instead.
	 **/
	function LoadChildrenIntoTree($id, &$tree, $loadprops = false)
	{
		return CmsContentOperations::load_children_into_tree($id, $tree);
	}

	public static function get_all_content($loadprops=true)
	{
		return cmsms()->content_base->find_all(array('order' => 'lft ASC'));
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsContentOperations::get_all_content($loadprops) instead.
	 **/
	function GetAllContent($loadprops=true)
	{
		return CmsContentOperations::get_all_content($loadprops);
	}

	function CreateHierarchyDropdown($current = '', $parent = '', $name = 'parent_id')
	{
		$result = '<select name="'.$name.'">';
		$result .= '<option value="-1">None</option>';

		$allcontent = cmsms()->GetContentOperations()->GetAllContent(false);

		if ($allcontent !== FALSE && count($allcontent) > 0)
		{
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
		}

		$result .= '</select>';

		return $result;
	}


	/**
	 * @deprecated Deprecated.  Use CmsContentOperations::get_default_page_id instead.
	 **/
	function GetDefaultPageID()
	{
		return CmsContentOperations::get_default_page_id();
	}

	/**
	 * Function to map an alias to a page id.  Returns null if
	 * no page is found.
	 *
	 * @param string Alias to look up
	 * @return integer The id of the page found.  Null is no page is found.
	 * @author Ted Kulp
	 **/
	public static function get_page_id_from_alias( $alias )
	{
		if (is_numeric($alias) && strpos($alias,'.') == FALSE && strpos($alias,',') == FALSE)
		{
			return $alias;
		}
		else
		{
			$result = cmsms()->content_base->find_by_alias($alias);
			if ($result)
			{
				return $result->id;
			}
		}
		
		return null;
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsContentOperations::get_page_id_from_alias($alias) instead.
	 **/
	function GetPageIDFromAlias( $alias )
	{
		return CmsContentOperations::get_page_id_from_alias($alias);
	}
	
	/**
	 * Function to map a hierarchy to a page id.  Returns null if
	 * no page is found.
	 *
	 * @param string Hierarchy position to look up
	 * @return integer The id of the page found.  Null is no page is found.
	 * @author Ted Kulp
	 **/
	public static function get_page_id_from_hierarchy($position)
	{
		$result = cmsms()->content_base->find_by_hierarchy(CmsContentOperations::create_unfriendly_hierarchy_position($position));
		if ($result)
		{
			return $result->id;
		}
		
		return null;
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsContentOperations::get_page_id_from_hierarchy($position) instead.
	 **/
	function GetPageIDFromHierarchy($position)
	{
		return CmsContentOperations::get_page_id_from_hierarchy($position);
	}

	/**
	 * Function to map a page id to an alias.  Returns null if
	 * no page is found.
	 *
	 * @param string Id to look up
	 * @return string The alias of the page found.  Null is no page is found.
	 * @author Ted Kulp
	 **/
	public static function get_page_alias_from_id($id)
	{
		if (!is_numeric($id) && strpos($id,'.') == TRUE && strpos($id,',') == TRUE)
		{
			return $id;
		}
		else
		{
			$result = cmsms()->content_base->find_by_id($id);
			if ($result)
			{
				return $result->alias;
			}
		}
		
		return null;
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsContentOperations::get_page_alias_from_id($id) instead.
	 **/
	function GetPageAliasFromID( $id )
	{
		return $this->get_page_alias_from_id($id);
	}

	function CheckAliasError($alias, $content_id = -1)
	{
		global $gCms;
		$db = cms_db();

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
	
	public static function clear_cache()
	{
		$smarty = cms_smarty();

		$smarty->clear_all_cache();
		$smarty->clear_compiled_tpl();
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsContentOperations::clear_cache() instead.
	 **/
	function ClearCache()
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

	/**
	 * @deprecated Deprecated.  Use CmsContentOperations::create_friendly_hierarchy_position($position) instead.
	 **/
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

	/**
	 * @deprecated Deprecated.  Use CmsContentOperations::create_unfriendly_hierarchy_position($position) instead.
	 **/
	public static function CreateUnfriendlyHierarchyPosition($position)
	{
		return CmsContentOperations::create_unfriendly_hierarchy_position($position);
	}
	
	public static function do_cross_reference($parent_id, $parent_type, $content)
	{
		$db = cms_db();

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

/**
 * @deprecated Deprecated.  Use CmsContentOperations instead.
 **/
class ContentOperations extends CmsContentOperations
{
}

/**
 * @deprecated Deprecated.  Use CmsContentOperations instead.
 **/
class ContentManager extends CmsContentOperations
{
}

?>