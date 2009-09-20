<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

class CmsContentTypePlaceholder
{
	var $type;
	var $filename;
	var $classname;
	var $friendlyname;
}


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

	/**
	 * Returns an array of available content types.
	 *
	 * @return array Array of CmsContenttypePlaceholder objects
	 * @author Ted Kulp
	 **/
	public static function find_content_types()
	{
		$gCms = cmsms();
		$variables =& $gCms->variables;
		if (isset($variables['contenttypes']))
		{
			return $variables['contenttypes'];
		}

		$variables['contenttypes'] = array();
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
				$obj->classname = basename($file, '.inc.php');
				$obj->friendlyname = basename($file, '.inc.php'); // todo, could get this from the object.
				$variables['contenttypes'][$obj->classname] = $obj;
		    }
		}
		closedir($handle);
	}
	

	public static function load_content_types()
	{
		$gCms = cmsms();
		$variables =& $gCms->variables;
		if (!isset($variables['contenttypes']))
		{
			self::find_content_types();
		}

		foreach( $variables['contenttypes'] as $key => $obj )
		{
			if( $obj->filename && file_exists($obj->filename) )
			{
				require_once($obj->filename);
			}
		}
	}


    /**
     * Returns a hash of valid content types (classes that extend ContentBase)
     * The key is the name of the class that would be saved into the dabase.  The
     * value would be the text returned by the type's FriendlyName() method.
     */
	static public function &list_content_types()
	{
		$gCms = cmsms();
		if (!isset($gCms->variables['contenttypes']))
		{
			self::find_content_types();
		}

		$result = array();
		$contenttypes =& $gCms->variables['contenttypes'];
		foreach( $contenttypes as $key => $placeholder )
		{
			$result[$placeholder->classname] = $placeholder->friendlyname;
		}
		
		return $result;
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

	
	function LoadContentFromId($id,$loadprops=true)
	{	
		return self::load_content_from_id($id);
	}
	
	public static function load_content_from_id($id)
	{
		self::load_content_types();
		return cms_orm('CmsContentBase')->find_by_id($id);
	}

	public static function load_multiple_from_parent_id($parent_id, $loadProperties = false)
	{
		self::load_content_types();
		return cms_orm('CmsContentBase')->find_all_by_parent_id($parent_id, array('order' => 'lft ASC'));
	}
	
	public static function LoadMultipleFromParentId($parent_id, $loadProperties = false)
	{
		return self::load_multiple_from_parent_id($parent_id, $loadProperties);
	}
	
	public static function load_multiple_from_left_and_right($lft, $rgt, $loadProperties = false)
	{
		return cms_orm('CmsContentBase')->find_all(array('conditions' => array('lft > ? AND rgt < ?', array($lft, $rgt)), 'order' => 'lft ASC'));
	}
	
	public static function LoadMultipleFromLeftAndRight($lft, $rgt, $loadProperties = false)
	{
		return self::load_multiple_from_left_and_right($lft, $rgt, $loadProperties);
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
		$page = cms_orm('CmsContentBase')->find_by_default_content(1);
		if ($page)
		{
			return $page->id;
		}
		return -1;
	}

    /**
     * Updates the hierarchy position of one item
     */
	public static function set_hierarchy_position($contentid)
	{
		global $gCms;
		$db = $gCms->GetDb();

		$current_hierarchy_position = '';
		$current_id_hierarchy_position = '';
		$current_hierarchy_path = '';
		$current_parent_id = $contentid;
		$count = 0;

		while ($current_parent_id > 1)
		{
			$query = "SELECT item_order, parent_id, content_alias FROM ".cms_db_prefix()."content WHERE content_id = ?";
			$row = &$db->GetRow($query, array($current_parent_id));
			if ($row)
			{
				$current_hierarchy_position = $row['item_order'] . '.' . $current_hierarchy_position;
				$current_id_hierarchy_position = $current_parent_id . '.' . $current_id_hierarchy_position;
				$current_hierarchy_path = $row['content_alias'] . '/' . $current_hierarchy_path;
				$current_parent_id = $row['parent_id'];
				$count++;
			}
			else
			{
				$current_parent_id = 1;
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
		$db->Execute($query, array(CmsContentOperations::create_unfriendly_hierarchy_position($current_hierarchy_position), $current_id_hierarchy_position, $current_hierarchy_path, implode(',', $prop_name_array), $contentid));
	}
	
	public static function SetHierarchyPosition($contentid)
	{
		return self::set_hierarchy_position($contentid);
	}

    /**
     * Updates the hierarchy position of all items
     */
	public static function set_all_hierarchy_positions($lft = -1)
	{
		global $gCms;
		$db = $gCms->GetDb();

		if ($lft > -1)
			$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE lft >= " . $db->qstr($lft);
		else
			$query = "SELECT content_id FROM ".cms_db_prefix()."content";

		$dbresult = $db->Execute($query);

		while ($dbresult && !$dbresult->EOF)
		{
			self::set_hierarchy_position($dbresult->fields['content_id']);
			$dbresult->MoveNext();
		}
		
		if ($dbresult) $dbresult->Close();
		
		CmsContentOperations::reset_nested_set();
	}
	
	public static function SetAllHierarchyPositions($lft = -1)
	{
		return self::set_all_hierarchy_positions($lft);
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
		return cms_orm('CmsContentBase')->find_all(array('order' => 'lft ASC'));
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsContentOperations::get_all_content($loadprops) instead.
	 **/
	function GetAllContent($loadprops=true)
	{
		return CmsContentOperations::get_all_content($loadprops);
	}

	function CreateHierarchyDropdown($current = '', $parent = '', $name = 'parent_id', $addt_content = '')
	{
		$result = '<select name="'.$name.'"';
		if ($addt_content != '')
		{
			$result .= ' ' . $addt_content;
		}
		$result .= '>';
		$result .= '<option value="-1">None</option>';

		$allcontent = CmsContentOperations::get_all_content(false);

		if ($allcontent !== FALSE && count($allcontent) > 0)
		{
			$curhierarchy = '';

			foreach ($allcontent as $one)
			{
				if ($one->id() == $current)
				{
					#Grab hierarchy just in case we need to check children
					#(which will always be after)
					$curhierarchy = $one->hierarchy();

					#Then jump out.  We don't want ourselves in the list.
					continue;
				}

				#If it's a child of the current, we don't want to show it as it
				#could cause a deadlock.
				if ($curhierarchy != '' && strstr($one->hierarchy() . '.', $curhierarchy . '.') == $one->hierarchy() . '.')
				{
					continue;
				}

				#Don't include content types that do not want children either...
				if (!$one->wants_children())
				{
					continue;
				}

				$result .= '<option value="'.$one->id().'"';
                #Select current parent if it exists
				if ($one->id == $parent)
					{
						$result .= ' selected="selected"';
					}
				$result .= '>'.$one->hierarchy().'. - '.$one->name().'</option>';
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
			$result = cms_orm('CmsContentBase')->find_by_alias($alias);
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
		$result = cms_orm('CmsContentBase')->find_by_hierarchy(CmsContentOperations::create_unfriendly_hierarchy_position($position));
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
	
	public static function reindex_content()
	{
		$content = cms_orm('CmsContentBase')->find_all();
		foreach ($content as $one_item)
		{
			$one_item->index();
		}
		CmsSearch::get_instance()->commit();
	}
	
	function reset_nested_set()
	{
		if (!function_exists('get_first_child'))
		{
			function get_first_child($set, $check)
			{
				foreach ($set as $one)
				{
					if ($one != $check && starts_with($one, $check . '.'))
					{
						return $one;
					}
				}
			
				return FALSE;
			}
		}
		
		if (!function_exists('get_next_sibling'))
		{
			function get_next_sibling($set, $check)
			{
				//Figure out next sibling
			
				//Increment the pos by 1
				$pos = CmsContentOperations::CreateFriendlyHierarchyPosition($check); //Turn into pure numbers
				$ary = explode('.', $pos); //Split into an array
				$pos_num = count($ary) - 1; //Get last item in array
				$ary[$pos_num] = $ary[$pos_num] + 1; //Inc by 1
				$pos = CmsContentOperations::CreateUnfriendlyHierarchyPosition(implode('.', $ary)); //Put back into format from string
			
				//Return it if it exists
				$pos = array_search($pos, $set);
				if ($pos !== FALSE && isset($set[$pos]))
					return $set[$pos];
			
				return FALSE;
			}
		}
		
		if (!function_exists('get_parent'))
		{
			function get_parent($set, $check)
			{
				$pos = CmsContentOperations::CreateFriendlyHierarchyPosition($check); //Turn into pure numbers
				$ary = explode('.', $pos); //Split into an array
			
				//This is the top level
				if (count($ary) < 2)
				{
					return FALSE;
				}
			
				//Pull off the last item
				array_pop($ary);
			
				//Return the new string
				return CmsContentOperations::CreateUnfriendlyHierarchyPosition(implode('.', $ary));
			}
		}
		
		$db = cms_db();
		$cms_db_prefix = cms_db_prefix();
		
		$hierarchy = array();
		
		$orig_hierarchy = $db->GetCol("SELECT hierarchy FROM {$cms_db_prefix}content ORDER BY hierarchy ASC");
		
		foreach ($orig_hierarchy as $one)
		{
			$hierarchy[$one] = array(-1, -1);
		}
		
		$counter = 0;
		$current = array_shift(array_keys($hierarchy));
		$done = false;
		
		//Logic goes as follows...
		
		//Set the left to counter, no matter what
		//If there's a child, go to child, repeat logic
		//If there's a sibling, set right to counter, move to sibling
		//If there's no sibling, set right to counter, move to parent
		//If returns from child, set right to counter, repeat child/sibling logic
		
		do
		{
			if ($hierarchy[$current][0] == -1)
			{
				$counter++;
				$hierarchy[$current][0] = $counter;
			}
			
			$child = get_first_child(array_keys($hierarchy), $current);
			$sibling = get_next_sibling(array_keys($hierarchy), $current);
			$parent = get_parent(array_keys($hierarchy), $current);
			
			if ($child && $hierarchy[$child][0] == -1)
			{
				$current = $child;
			}
			else if ($sibling)
			{
				$counter++;
				$hierarchy[$current][1] = $counter;
				$current = $sibling;
			}
			else if ($parent)
			{
				$counter++;
				$hierarchy[$current][1] = $counter;
				$current = $parent;
			}
			else
			{
				$counter++;
				$hierarchy[$current][1] = $counter;
				$done = true;
			}
		}
		while (!$done);
		
		$db->BeginTrans();
		foreach ($hierarchy as $k=>$v)
		{
			$db->Execute("UPDATE {$cms_db_prefix}content SET lft = ?, rgt = ? WHERE hierarchy = ?", array($v[0], $v[1], $k));
		}
		$db->CommitTrans();
	}

	public function get_content_editor_type($content_obj)
	{
		$classname = get_class($content_obj);
		$gCms = cmsms();
		$contenttypes =& $gCms->variables['contenttypes'];
		$found = '';
		foreach( $contenttypes as $name => $type )
			{
				if( $type->classname == $classname )
					{
						$found = 'Cms'.$classname.'Editor';
						break;
					}
			}
		if( !$found || !class_exists($found))
			{
				$found = 'CmsContentEditorBase';
			}
		return $found;
	}

	static public function clone_content_as($content_obj,$contenttype)
	{
		$gCms = cmsms();
		self::load_content_types();
		$contenttypes =& $gCms->variables['contenttypes'];
		foreach( $contenttypes as $name => $obj )
			{
				if( $name == $contenttype )
					{
						$tmpobj = new $contenttype;
						$tmpobj->dirty = true;
						$tmpobj->params = $content_obj->params;
						$tmpobj->mProperties = $content_obj->mProperties;
						return $tmpobj;
					}
			}
		return null;
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
