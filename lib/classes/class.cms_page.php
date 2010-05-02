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

define('CMS_CONTENT_HIDDEN_NAME','--------');


/**
 * Base content type class.  Extend this to create new content types for
 * the system.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsPage extends CmsObjectRelationalMapping
{
	var $table = 'pages';
	var $params = array('id' => -1, 'template_id' => -1, 'active' => true, 'default_content' => false, 'parent_id' => -1, 'lft' => 1, 'rgt' => 1, 'blocks' => array());
	var $field_maps = array('content_alias' => 'alias', 'titleattribute' => 'title_attribute', 'accesskey' => 'access_key', 'tabindex' => 'tab_index', 'content_name' => 'name');
	var $unused_fields = array();
	var $mProperties = array();

	var $props_loaded = false;
    var $mAdditionalUsers = array();
	var $addusers_loaded = false;
	
	#Stuff needed to do a doppleganger for CmsNode -- Multiple inheritence would rock right now
	var $tree = null;
	var $parentnode = null;
	var $children = array();
	
	public function __construct()
	{
		parent::__construct();
		$template = CmsTemplate::get_default_template();
		if ($template)
		{
			$this->params['template_id'] = $template->id;
		}
	}
	
	public function setup($first_time = false)
	{
		$this->create_belongs_to_association('template', 'CmsTemplate', 'template_id');
		$this->assign_acts_as('NestedSet');
		//$this->assign_acts_as('Acl');
	}
	
	/*
	function __sleep()
	{
		//This way if we serialize out to, say, a versioning piece,
		//all the properties are stored as well...
		$this->load_properties();
		return array_merge(parent::__sleep(), array('mProperties', 'props_loaded', 'preview', 'unused_fields', 'field_maps', 'table', 'params'));
	}
	*/
	
	//////////////////////////////////////////////////
	// methods that should be overridden
	//////////////////////////////////////////////////
	
	public function friendly_name()
	{
		stack_trace();
		die('ERROR: friendly_name not overriden');
	}

// 	public function get_type()
// 	{
// 		die('ERROR: type not overriden');
// 	}

    /**
     * Function content types to use to say whether or not they should show
     * up in lists where parents of content are set.  This will default to true,
     * but should be used in cases like Separator where you don't want it to 
     * have any children.
     * 
     * @since 0.11
     */
	public function wants_children()
	{
		return true;
	}

    /**
     * Should this link be used in various places where a link is the only
     * useful output?  (Like next/previous links in cms_selflink, for example)
     */
	public function has_usable_link()
	{
		return true;
	}

	public function is_default_possible()
	{
		return false;
	}

	public function is_copyable()
	{
		return true;
	}

	public function is_system_page()
	{
		return false;
	}

	public function is_previewable()
	{
		return false;
	}

	public function is_viewable()
	{
		return false;
	}

	/**
	 * Method to override for indexing content in the search index.  If this is not overridden,
	 * then the content type won't be searchable.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public function index()
	{
	}
	
	protected function validate()
	{
		$this->validate_numericality_of('parent_id',lang('invalidparent'));
		//$this->validate_not_blank('name', lang('nofieldgiven %s',array(lang('title'))));
		$this->validate_not_blank('menu_text', lang('nofieldgiven %s',array(lang('menutext'))));
		$this->validate_not_blank('alias', lang('nofieldgiven %s',array(lang('alias'))));
		$this->validate_not_blank('url_text', lang('nofieldgiven %s',array(lang('url_text'))));
	}
	
	function friendly_hierarchy()
	{
		return CmsContentOperations::create_friendly_hierarchy_position($this->hierarchy());
	}
	
	function display()
	{
		cms_smarty()->assign_by_ref('current_page', $this);
		$template_content = $this->template->process();
		return $template_content;
	}
	
	function get_content_block($block_name = 'default', $can_return_new = false)
	{
		if (isset($this->blocks[$block_name]))
		{
			if (isset($this->blocks[$block_name]['id']))
			{
				$content = cms_orm('CmsContentBase')->find_by_id($this->blocks[$block_name]['id']);
				if ($content != null)
				{
					return $content;
				}
			}
		}
		
		if ($can_return_new)
		{
			$obj = new CmsHtmlContentType(); //TODO: Fix me
			return $obj;
		}
		else
			return null;
	}
	
	function render_block($block_name = 'default')
	{
		$block = $this->get_content_block($block_name);
		if ($block != null)
		{
			return $block->render();
		}
		
		return '<!-- No content found for ' . $block_name . ' -->';
	}
	

	//////////////////////////////////////////////////
	// methods that do not need to be overridden
	//////////////////////////////////////////////////
	
	function after_load()
	{
		/*
		if (empty($this->content_props))
		{
			$val = cms_db()->GetAll("SELECT prop_name, content FROM {content_props} WHERE content_id = ?", array($this->id));
			if ($val)
			{
				foreach ($val as $row)
				{
					$this->set_property_value($row['prop_name'], $row['content']);
				}
			}
		}
		*/
	}
	
	protected function before_save()
	{
		//CmsEvents::send_event('Core', 'ContentEditPre', array('content' => &$this));
	}
	
	protected function after_save(&$result)
	{
		if ($result)
		{
			$concat = '';
			/*
			foreach ($this->mProperties as &$prop)
			{
				if ($prop->content_id != $this->id)
					$prop->content_id = $this->id;
				$prop->save();
				$concat .= $prop->content;
			}
			*/

			// save additional editors
			$users = cms_orm('CmsAdditionalEditor')->find_all_by_content_id($this->id);
			foreach( $users as &$user )
				{
					$user->delete();
				}
			foreach ($this->mAdditionalUsers as &$user)
			{
				if( $user->content_id != $this->id )
				{
					$user->content_id = $thid->id;
				}
				$user->save();
			}
		
			if ($concat != '')
				CmsContentOperations::do_cross_reference($this->id, 'content', $concat);
			
			CmsPage::set_all_hierarchy_positions(/*$this->lft, $this->rgt*/);
		
			//CmsEvents::send_event('Core', 'ContentEditPost', array('content' => &$this));
			CmsCache::clear();
		}
	}

	private function load_additional_users($force = false)
	{
		if( $force || !$this->addusers_loaded )
		{
			$users = cms_orm('CmsAdditionalEditor')->find_all_by_content_id($this->id);
			foreach ($users as &$user)
				{
					$this->mAdditionalUsers[] = $user;
				}
			$this->addusers_loaded = true;
		}
	}

	public function get_additional_users()
	{
		$this->load_additional_users();
		$result = array();
		foreach( $this->mAdditionalUsers as &$user )
		{
			$result[] = $user->user_id;
		}
		return $result;
	}

	public function set_additional_users($user_ids)
	{
		$this->load_additional_users();
		if( !is_array($user_ids) )
		{
			$this->mAdditionalUsers = array();
			return;
		}

		foreach( $user_ids as $one_user )
		{
			$found = false;
			foreach( $this->mAdditionalUsers as &$user )
			{
				if( $user->user_id == $one_user ) 
					{
						$found = true;
					}
			}
			if( !$found )
			{
				// create a new user
				$newuser = new CmsAdditionalEditor;
				$newuser->user_id = $one_user;
				$newuser->content_id = $this->id;
				$newuser->page_id = $this->id;
				$this->mAdditionalUsers[] = $newuser;
			}
		}
	}

    /**
     * Does this have children?
     */
	public function has_children()
	{
		return $this->rgt > ($this->lft + 1);
	}
	
	public function get_sibling_count()
	{
		if ($this->get_parent()->has_children())
		{
			return $this->get_parent()->get_children_count();
		}
		return 0;
	}
	
	public function shift_position($direction = 'up')
	{
		if ($direction == 'up')
			$this->move_up();
		else
			$this->move_down();
		
		CmsPage::set_all_hierarchy_positions();
		CmsCache::clear();
	}
	
    public function get_url($rewrite = true, $lang = '')
    {
		$config = cms_config();
		$url = "";
		$alias = ($this->alias != ''?$this->alias:$this->id);
		
		if ($this->default_content)
		{
			return $config['root_url'] . '/';
		}
		
		if ($config["assume_mod_rewrite"] && $rewrite == true)
		{
			$url = $config['root_url']. '/' . ($lang != '' ? "$lang/" : '') . $this->hierarchy_path() . (isset($config['page_extension'])?$config['page_extension']:'.html');
		}
		else
		{
		    if (isset($_SERVER['PHP_SELF']) && $config['internal_pretty_urls'] == true)
		    {
				$url = $config['root_url'] . '/index.php/' . ($lang != '' ? "$lang/" : '') . $this->hierarchy_path() . (isset($config['page_extension']) ? $config['page_extension'] : '.html');
		    }
		    else
		    {
				$url = $config['root_url'] . '/index.php?' . $config['query_var'] . '=' . $alias . ($lang != '' ? '&amp;lang=' . $lang : '');
		    }
		}
		return $url;
    }

	public function set_alias($alias = '')
	{
		$config = cms_config();

		$tolower = false;

		if ($alias == '' && $config['auto_alias_content'] == true)
		{
			/*
			$alias = trim($this->get_property_value('menu_text', CmsMultiLanguage::get_default_language()));
			if ($alias == '')
			{
			    $alias = trim($this->get_property_value('name', CmsMultiLanguage::get_default_language()));
			}
			*/
			$alias = trim($this->params['name']);
			if ($alias == '')
			{
				$alias = trim($this->params['menu_text']);
			}
			
			$tolower = true;
			$alias = munge_string_to_url($alias, $tolower);

			// Make sure auto-generated new alias is not already in use on a different page, if it does, add "-2" to the alias
			$error = cmsms()->GetContentOperations()->CheckAliasError($alias);
			if ($error !== FALSE)
			{
				if (FALSE == empty($alias))
				{
					$alias_num_add = 2;
					// If a '-2' version of the alias already exists
					// Check the '-3' version etc.
					while (cmsms()->GetContentOperations()->CheckAliasError($alias.'-'.$alias_num_add) !== FALSE)
					{
						$alias_num_add++;
					}
					$alias .= '-'.$alias_num_add;
				}
				else
				{
					$alias = '';
				}
			}
		}

		$this->params['alias'] = munge_string_to_url($alias, $tolower);
	}
	
	public function WantsChildren()
    {
		$this->wants_children();
    }
	
	protected function before_delete()
	{
		if( $this->has_children() ) return false;
		if( $this->default_content() ) return false;

		Events::SendEvent('Core', 'ContentDeletePre', array('content' => &$this));
		return true;
	}
	
	protected function after_delete()
	{
		$items =& cms_orm('CmsContentProperty')->find_all_by_content_id($this->id);
		foreach ($items as &$item)
		{
			$item->delete();
		}
		
		// delete all the additional editors.
		$users =& cms_orm('CmsAdditionalEditor')->find_all_by_content_id($this->id);
		foreach( $users as &$user )
		{
			$user->delete();
		}

		#Remove the cross references
		CmsContentOperations::remove_cross_references($this->id, 'content');

		CmsCache::clear();
		CmsPage::set_all_hierarchy_positions();

		Events::SendEvent('Core', 'ContentDeletePost', array('content' => &$this));
		audit($this->id,$this->name(),'Deleted Content');
	}
	
	public function template_name()
	{
		try
		{
			return cms_orm('CmsTemplate')->find_by_template_id($this->template_id)->name;
		}
		catch (Exception $e)
		{
			return '';
		}
	}
	
	/**
	 * Renders the given pageinfo object based on it's assigned content and template
	 * parts.
	 *
	 * @return string The rendered html
	 **/
	public function render()
	{
		return $this->display();
		/*
		$html = '';
		$cached = false;
		
		$gCms = cmsms();
		$smarty = cms_smarty();
		
		$id = cms_smarty()->id;

		#If this is a case where a module doesn't want a template to be shown, just disable caching
		if (isset($id) && $id != '' && isset($_REQUEST[$id.'showtemplate']) && ($_REQUEST[$id.'showtemplate'] == 'false' || $_REQUEST[$id.'showtemplate'] == false))
		{
			$html = $smarty->fetch('template:notemplate') . "\n";
		}
		else
		{
			//$smarty->caching = false;
			//$smarty->compile_check = true;
			($smarty->is_cached('template:'.$this->template_id)?$cached="":$cached="not ");
			// Added HTTP_HOST here to work with multisites - SK
			$html = $smarty->fetch('template:'.$this->template_id) . "\n";
			if (isset($_REQUEST['tmpfile']))
			{
				$smarty->clear_compiled_tpl('template:'.$this->template_id);
			}
		}

		if (!$cached)
		{
			Events::SendEvent('Core', 'ContentPostRenderNonCached', array(&$html));
		}

		Events::SendEvent('Core', 'ContentPostRender', array('content' => &$html));
		
		return $html;
		*/
	}
	
	public function add_child($node)
	{
		$node->set_parent($this);
		$node->tree = $this->tree;
		$this->children[] = $node;
	}
	
	public function get_tree()
	{
		return $this->tree;
	}
	
	public function depth()
	{
		if ($this->hierarchy)
		{
			return count(explode('.', $this->hierarchy)) - 1;
		}
		else
		{
			$depth = 0;
			$currLevel = &$this;
			
			while ($currLevel->parentnode)
			{
				$depth++;
				$currLevel = &$currLevel->parentnode;
			}
			
			return $depth;
		}
	}
	
	public function get_level()
	{
		return $this->depth();
	}
	
	// note this is not the parent_id
	public function get_parent()
	{
		return $this->parentnode;
	}

	// note this is not the parent_id
	public function set_parent($node)
	{
		$this->parentnode = $node;
	}
	
	public function get_children_count()
	{
		return count($this->children);
	}

	// not needed?
	public function getChildrenCount()
	{
		return $this->get_children_count();
	}

	public function &get_children()
	{
		if ($this->has_children())
		{
			//We know there are children, but no nodes have been
			//created yet.  We should probably do that.
			if (!$this->children_loaded())
			{
				//$this->tree->load_child_nodes(-1, $this->lft, $this->rgt);
				$this->tree->load_child_nodes($this->id);
			}
		}
		return $this->children;
	}
	
    public function &get_flat_list()
    {
        $return = array();

		if ($this->has_children())
		{
			for ($i=0; $i<count($this->children); $i++)
			{
				$return[] = &$this->children[$i];
				$return = array_merge($return, $this->children[$i]->get_flat_list());
			}
		}
        
        return $return;
    }

	/**
	 * Backwards compatiblity function.  Just returns $this.
	 *
	 * @return object Returns $this
	 * @author Ted Kulp
	 */
	public function get_content()
	{
		return $this;
	}

	public function check_edit_permission($uid)
	{
		if( $uid == $this->owner_id ) return true;
		$addteditors = $this->get_additional_users();
		if( in_array($uid,$addteditors) ) return true;
		return false;
	}
	
	public static function get_all_pages($loadprops = true)
	{
		return cms_orm('CmsPage')->find_all(array('order' => 'lft ASC'));
	}
	
	public static function set_all_hierarchy_positions($lft = -1, $rgt = -1)
	{
		$gCms = cmsms();
		$db = cms_db();

		if ($lft > -1)
		{
			if ($rgt > -1)
				$query = "SELECT id FROM ".cms_db_prefix()."pages WHERE lft >= " . $db->qstr($lft) . ' AND rgt <= ' . $db->qstr($rgt);
			else
				$query = "SELECT id FROM ".cms_db_prefix()."pages WHERE lft >= " . $db->qstr($lft);
		}
		else
			$query = "SELECT id FROM ".cms_db_prefix()."pages";

		$dbresult = array();
		//try
		//{
			$dbresult = $db->GetCol($query);
		//}
		//catch (Exception $ex)
		//{
		
		//}
		
		foreach ($dbresult as $one_id)
		{
			self::set_hierarchy_position($one_id);
		}
	}
	
    /**
     * Updates the hierarchy position of one item
     */
	public static function set_hierarchy_position($page_id)
	{
		$gCms = cmsms();
		$db = cms_db();

		$current_hierarchy_position = '';
		$current_id_hierarchy_position = '';
		$current_hierarchy_path = '';
		$current_parent_id = $page_id;
		$count = 0;

		while ($current_parent_id > -1)
		{
			$query = "SELECT item_order, parent_id, url_text FROM ".cms_db_prefix()."pages WHERE id = ?";
			$row = $db->GetRow($query, array($current_parent_id));
			if ($row)
			{
				$current_hierarchy_position = $row['item_order'] . '.' . $current_hierarchy_position;
				$current_id_hierarchy_position = $current_parent_id . '.' . $current_id_hierarchy_position;
				$current_hierarchy_path = $row['url_text'] . '/' . $current_hierarchy_path;
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

		//debug_buffer(array($current_hierarchy_position, $current_id_hierarchy_position, implode(',', $prop_name_array), $page_id));

		$query = "UPDATE {pages} SET hierarchy = ?, id_hierarchy = ?, hierarchy_path = ? WHERE id = ?";
		$db->Execute($query, array($current_hierarchy_position, $current_id_hierarchy_position, $current_hierarchy_path, $page_id));
	}
	
	public static function create_hierarchy_dropdown($current = '', $parent = '', $name = 'parent_id', $addt_content = '')
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
				if (!$one->wants_children())
				{
					continue;
				}

				$result .= '<option value="' . $one->id . '"';
                #Select current parent if it exists
				if ($one->id == $parent)
				{
					$result .= ' selected="selected"';
				}
				$result .= '>'.$one->hierarchy.'. - '.$one->name['en_US'].'</option>';
			}
		}

		$result .= '</select>';

		return $result;
	}
}


?>
