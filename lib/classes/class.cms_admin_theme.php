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

/**
 * Class to extend in order to create a theme for the admin panel.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsAdminTheme extends CmsObject
{
	static private $instance = NULL;
	public $breadcrumbs = array();
	public $menuItems;
	public $script;
	public $headtext = '';
	public $section_count = array();
	public $modules_by_section = array();
	public $url = '';
	public $query = '';
	public $subtitle = '';
	public $theme_template_dir = '';
	public $theme_name = '';
	public $themeName = '';
	public $errors = array();
	public $messages = array();

	function __construct($userid, $theme_name = 'default')
	{
		parent::__construct();
		
		$this->theme_name = $theme_name;
		
		$this->url = $_SERVER['SCRIPT_NAME'];
		$this->query = (isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'');
		if ($this->query == '' && isset($_POST['module']) && $_POST['module'] != '')
		{
			$this->query = 'module='.$_POST['module'];
		}
		if (strpos( $this->url, '/' ) === false)
		{
			$this->script = $this->url;
		}
		else
		{
			$toam_tmp = explode('/',$this->url);
			$toam_tmp2 = array_pop($toam_tmp);
			$this->script = $toam_tmp2;
			//$this->script = array_pop(@explode('/',$this->url));
		}

		$this->theme_template_dir = dirname(dirname(dirname(__FILE__))) . '/' . CmsConfig::get('admin_dir') . '/themes/' . $this->theme_name . '/templates/';
	}
	
	static public function get_instance($is_anonymous = false)
	{
		if (self::$instance == NULL)
		{
			self::$instance = self::get_theme_for_user($is_anonymous);
		}
		return self::$instance;
	}
	
	static function get_theme_for_user($is_anonymous = false)
	{
		$gCms = cmsms();
		
		$theme_name = 'default';
		$userid = -1;

		if (!$is_anonymous)
		{
			$user = CmsLogin::get_current_user();
			$userid = $user->id;

			$theme_name = get_preference($userid, 'admintheme', 'default');
		}
		$theme_file_name = "{$theme_name}_theme";
		$theme_object_name = ucfirst(camelize($theme_file_name));

		if (file_exists(dirname(dirname(dirname(__FILE__))) . DS . "admin/themes/${theme_name}/${theme_file_name}.php"))
		{
			include(dirname(dirname(dirname(__FILE__))) . DS . "admin/themes/${theme_name}/${theme_file_name}.php");
			$themeObject = new $theme_object_name($userid, $theme_name);
		}
		else
		{
			include(dirname(dirname(dirname(__FILE__))) . DS . "admin/themes/default/default_theme.php");
			$themeObject = new CmsAdminTheme($userid);
		}
		
		$themeObject->userid = $userid;
		if (!$is_anonymous)
		{
			$themeObject->set_module_admin_interfaces();
			$themeObject->set_aggrigate_permissions();
			$themeObject->populate_admin_navigation();
		}

		return $themeObject;
	}
	
	static public function start($is_anonymous = false)
	{
		CmsEventOperations::send_event('Core', 'AdminDisplayStart');

		@ob_start();
		
		$admin_theme = self::get_instance($is_anonymous);
		
		$smarty = cms_smarty();
		$smarty->assign_by_ref('admin_theme', $admin_theme);
		$smarty->assign('adminpaneltitle', lang('adminpaneltitle'));
		$smarty->assign('baseurl', CmsConfig::get('root_url') . '/' . CmsConfig::get('admin_dir') . '/');
	}
	
	static public function end()
	{
		global $gCms;
		// Add in stuff needed WYSIWYG editors
		foreach($gCms->modules as $key=>$value)
		{
			if ($gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true &&
				$gCms->modules[$key]['object']->is_wysiwyg()
				)
			{
				$loadit=false;
				if ($gCms->modules[$key]['object']->WYSIWYGActive())
				{
					$loadit=true;
				}
				else
				{
					if (get_preference(get_userid(), 'wysiwyg')==$gCms->modules[$key]['object']->get_name())
					{
						$loadit=true;
					}
				}
				if ($loadit)
				{
					//$bodytext.=$gCms->modules[$key]['object']->WYSIWYGGenerateBody();
					// Add to header
					CmsAdminTheme::inject_header_text($gCms->modules[$key]['object']->WYSIWYGGenerateHeader());				
					//$formtext.=$gCms->modules[$key]['object']->WYSIWYGPageForm();
					//$formsubmittext.=$gCms->modules[$key]['object']->WYSIWYGPageFormSubmit();
				}
			}
		}

		$result = @ob_get_clean();
		
		$smarty = cms_smarty();
		$smarty->assign('admin_content', $result);
		
		@ob_start();
		self::get_instance()->display_top_menu();
		$topmenu = @ob_get_clean();
		$smarty->assign('admin_topmenu', $topmenu);
		
		$smarty->assign('headtext', self::get_instance()->headtext);
		$smarty->assign_by_ref('theme_object', self::get_instance());
		
		$smarty->display(self::get_instance()->theme_template_dir . 'overall.tpl');
		
		//Now that it's all done, send out an event telling everyone
		CmsEventOperations::send_event('Core', 'AdminDisplayFinish');
		
		if (CmsConfig::get('debug'))
		{
			echo '<div id="_DebugFooter">';
			echo CmsProfiler::get_instance()->report();
			echo '</div> <!-- end DebugFooter -->';
		}
	}
	
	static public function inject_header_text($text)
	{
		$instance = self::get_instance();
		$instance->headtext .= $text;
	}
	
	public function ShowHeader($title_name, $extra_lang_param=array(), $link_text = '', $module_help_type = FALSE)
	{
		return $this->show_header($title_name, $extra_lang_param, $link_text, $module_help_type);
	}
	
	public function show_header($title_name, $extra_lang_param=array(), $link_text = '', $module_help_type = FALSE)
	{
		$cms = cmsms();
		$config = cms_config();             
		$header  = '<div class="pageheader">';
		if (FALSE != $module_help_type)
		{
			$header .= $title_name;
		}
		else
		{
			$header .= lang($title_name, $extra_lang_param);
		}
		if (FALSE == empty($this->breadcrumbs))
		{
			$wikiUrl = $config['wiki_url'];
			// Include English translation of titles. (Can't find better way to get them)
			$dirname = dirname(__FILE__);
			foreach ($this->breadcrumbs AS $key => $value)
			{
				$title = $value['title'];
				// If this is a module and the last part of the breadcrumbs
				if (FALSE != $module_help_type && TRUE == empty($this->breadcrumbs[$key + 1]))
				{
					$help_title = $title;
					if (FALSE == empty($_GET['module']))
					{
						$module_name = $_GET['module'];
					}
					else
					{
						$module_name = substr($_REQUEST['mact'], 0, strpos($_REQUEST['mact'], ','));
					}
					// Turn ModuleName into _Module_Name
					$moduleName =  preg_replace('/([A-Z])/', "_$1", $module_name);
					$moduleName =  preg_replace('/_([A-Z])_/', "$1", $moduleName);
					if ($moduleName{0} == '_')
					{
						$moduleName = substr($moduleName, 1);
					}
					$wikiUrl .= '/'.$moduleName;
				}
				else
				{
					// Remove colon and following (I.E. Turn "Edit Page: Title" into "Edit Page")
					$colonLocation = strrchr($title, ':');
					if ($colonLocation !== false)
					{
						$title = substr($title,0,strpos($title,':'));
					}
					// Get the key of the title so we can use the en_US version for the URL
					$node = CmsAdminTree::find_node_by_title($title);
					if ($node)
					{
						$wikiUrl .= '/'. CmsLanguage::translate($node->english_title, array(), 'core', 'en_US');
						$help_title = $title;	
					}
				}
			}
			if (FALSE == get_preference($this->userid, 'hide_help_links'))
			{
				// Clean up URL
				$wikiUrl = str_replace(' ', '_', $wikiUrl);
				$wikiUrl = str_replace('&amp;', 'and', $wikiUrl);
				// Make link to go the translated version of page if lang is not en_US
				/* Disabled as suggested by westis
				$lang = get_preference($this->cms->variables['user_id'], 'default_cms_language');
				if ($lang != 'en_US') {
					$wikiUrl .= '/'.substr($lang, 0, 2);
				}
				*/
				if (FALSE == empty($link_text))
				{
					$help_title = $link_text;
				}
				else
				{
					$help_title = lang('help_external');
				}
				$image_help = $this->DisplayImage('icons/system/info.gif', lang('help'),'','','systemicon');
				$image_help_external = $this->DisplayImage('icons/system/info-external.gif', lang('help'),'','','systemicon');		
				if ('both' == $module_help_type)
				{
					$module_help_link = $config['root_url'].'/'.$config['admin_dir'].'/listmodules.php?action=showmodulehelp&amp;module='.$module_name;
					$header .= '<span class="helptext"><a href="'.$module_help_link.'">'.$image_help.'</a> <a href="'.$module_help_link.'">'.lang('help').'</a> | ';
					$header .= '<a href="'.$wikiUrl.'" rel="external">'.$image_help_external.'</a> <a href="'.$wikiUrl.'" rel="external">'.lang('wikihelp').'</a>  ('.lang('new_window').')</span>';
				}
				else
				{
					$header .= '<span class="helptext"><a href="'.$wikiUrl.'" rel="external">'.$image_help_external.'</a> <a href="'.$wikiUrl.'" rel="external">'.lang('help').'</a> ('.lang('new_window').')</span>';
				}
			}
		}
		$header .= '</div>';
		return $header;
	}
	
    /**
     *  BackUrl
     *  "Back" Url - link to the next-to-last item in the breadcrumbs
     *  for the back button.
     */
	public function back_url()
	{
		$count = count($this->breadcrumbs) - 2;
		if ($count > -1)
		{
			return $this->breadcrumbs[$count]['url'];
		}
		else
		{
			return '';
		}
	}
	
	public function BackURL()
	{
		return $this->back_url();
	}
	
    /**
     * PopulateAdminNavigation
     * This method populates a big array containing the Navigation Taxonomy
     * for the admin section. This array is then used to create menus and
     * section main pages. It uses aggregate permissions to hide sections for which
     * the user doesn't have permissions, and highlights the current section so
     * menus can show the user where they are.
     *
     * @param subtitle any info to add to the page title
     *
     */
    function populate_admin_navigation($subtitle='')
    {
		//Fill in the initial tree from the xml file
		CmsAdminTree::fill_from_file($this);

        $this->subtitle = $subtitle;

		$root_path = CmsConfig::get('root_path');
		$root_url = CmsConfig::get('root_url');

		//Add in modules and icons
		$children = CmsAdminTree::get_instance()->get_root_node()->get_children();
		foreach ($children as &$basenode)
		{
			//Set icon for this item
			$basenode->icon_url = $this->find_icon_url($basenode->name);
			
			//Set icons on children
			$grandchildren = $basenode->get_children();
			foreach ($grandchildren as &$childnode)
			{
				$childnode->icon_url = $this->find_icon_url($childnode->name);
			}

			$first = true;
			foreach ($this->menu_list_section_modules($basenode->name) as $module)
			{
				$newnode = CmsAdminTree::get_instance()->create_node();
				$newnode->name = $module['name'];
				$newnode->title = $module['title'];
				$newnode->url = $module['url'];
				$newnode->description = $module['description'];
				$newnode->show_in_menu = true;
				$newnode->first_module = $first;
				$newnode->module = true;
				
				$imageSpec = $root_path . '/modules/' . $module['name'] . '/images/icon.gif';
				if (file_exists($imageSpec))
				{
					$newnode->icon_url = $root_url . '/modules/' . $module['name'] . '/images/icon.gif';
				}
				else
				{
					if (isset($basenode->icon))
						$newnode->icon_url = $basenode->icon;
				}

				$basenode->add_child($newnode);
				$first = false;
			}
		}
		
		//Now go through and find the selected
		$flatlist = CmsAdminTree::get_instance()->get_flat_list();
		foreach ($flatlist as &$onenode)
		{
			if ($this->script == 'moduleinterface.php')
			{
				$a = preg_match('/(module|mact)=([^&,]+)/',$this->query,$matches);
				if ($a > 0 && $matches[2] == $onenode->name)
				{
					$onenode->selected = true;
					$this->title = $onenode->title;
					$onenode->get_parent()->selected = true;
				}
			}
			else if ($onenode->url == $this->script)
			{
				$onenode->selected = true;
				$this->title = $onenode->title;
				$onenode->get_parent()->selected = true;
			}
		}

		// fix subtitle, if any
		if ($subtitle != '')
		{
			$this->title .= ': '.$subtitle;
		}

		// generate breadcrumb array
		$count = 0;
		$flatlist = CmsAdminTree::get_instance()->get_flat_list();
		foreach ($flatlist as &$onenode)
		{
			if ($onenode->selected)
			{
				$this->breadcrumbs[] = array('title'=>$onenode->title, 'url'=>$onenode->url);
				$count++;
			}
		}

		if ($count > 0)
		{
			// and fix up the last breadcrumb...
			if ($this->query != '' && strpos($this->breadcrumbs[$count-1]['url'],'?') === false)
			{
				$this->query = preg_replace('/\&/','&amp;',$this->query);
				$this->breadcrumbs[$count-1]['url'] .= '?'.$this->query;
			}
			if ($this->subtitle != '')
			{
				$this->breadcrumbs[$count-1]['title'] .=  ': '.$this->subtitle;
			}
		}
	}
	
    /**
     * fix_spaces
     * This method converts spaces into a non-breaking space HTML entity.
     * It's used for making menus that work nicely
     *
     * @param str string to have its spaces converted
     */
    function fix_spaces($str)
    {
    	return preg_replace('/\s+/',"&nbsp;",$str);
    }
    /**
     * unfix_spaces
     * This method converts non-breaking space HTML entities into char(20)s.
     *
     * @param str string to have its spaces converted
     */
    function unfix_spaces($str)
    {
    	return preg_replace('/&nbsp;/'," ",$str);
    }

    /**
     * has_permission
     *
     * Check if the user has one of the aggregate permissions
     * 
     * @param permission the permission to check.
     */
	function has_permission($permission)
	{
		if (isset($this->perms[$permission]) && $this->perms[$permission])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	* SetModuleAdminInterfaces
	*
	* This function sets up data structures to place modules in the proper Admin sections
	* for display on section pages and menus.
	*
	*/
	function set_module_admin_interfaces()
	{
		# Are there any modules with an admin interface?
		$cmsmodules = cmsms()->modules;
		reset($cmsmodules);
		while (list($key) = each($cmsmodules))
		{
			$value =& $cmsmodules[$key];
			if (isset($cmsmodules[$key]['object'])
				&& $cmsmodules[$key]['installed'] == true
				&& $cmsmodules[$key]['active'] == true
				&& $cmsmodules[$key]['object']->has_admin()
				&& $cmsmodules[$key]['object']->visible_to_admin_user())
			{
				foreach ($cmsmodules[$key]['object']->get_admin_menu_items() as $one_item)
				{
					$section = $one_item[3];
					if (! isset($this->section_count[$section]))
					{
						$this->section_count[$section] = 0;
					}
					$this->modules_by_section[$section][$this->section_count[$section]]['key'] = $key;
					$this->modules_by_section[$section][$this->section_count[$section]]['name'] = $key;
					$this->modules_by_section[$section][$this->section_count[$section]]['action'] = $one_item[2];
					$this->modules_by_section[$section][$this->section_count[$section]]['title'] = $one_item[0];
					if ($one_item[1] != '')
					{
						$this->modules_by_section[$section][$this->section_count[$section]]['description'] = $one_item[1];
					}
					else
					{
						$this->modules_by_section[$section][$this->section_count[$section]]['description'] = "";
					}
					$this->section_count[$section]++;
				}
			}
		}
	}
	
    /**
     * menu_list_section_modules
     * This method reformats module information for display in menus. When passed the
     * name of the admin section, it returns an array of associations:
     * array['module-name']['url'] is the link to that module, and
     * array['module-name']['description'] is the language-specific short description of
     *   the module.
     *
     * @param section - section to display
     */
	function menu_list_section_modules($section)
	{
		$modList = array();
		if (isset($this->section_count[$section]) && $this->section_count[$section] > 0)
		{
			# Sort modules by name
			$names = array();
			foreach($this->modules_by_section[$section] as $key => $row)
			{
				$names[$key] = $this->modules_by_section[$section][$key]['name'];
			}
			array_multisort($names, SORT_ASC, $this->modules_by_section[$section]);

			foreach($this->modules_by_section[$section] as $sectionModule)
			{
				$modList[$sectionModule['key']]['url'] = "moduleinterface.php?mact={$sectionModule['key']},m1_,{$sectionModule['action']},0";
				$modList[$sectionModule['key']]['description'] = $sectionModule['description'];
				$modList[$sectionModule['key']]['name'] = $sectionModule['name'];
				$modList[$sectionModule['key']]['title'] = $sectionModule['title'];
			}
		}
		return $modList;
	}
	
	/**
	* SetAggregatePermissions
	*
	* This function gathers disparate permissions to come up with the visibility of
	* various admin sections, e.g., if there is any content-related operation for
	* which a user has permissions, the aggregate content permission is granted, so
	* that menu item is visible.
	*
	*/
	function set_aggrigate_permissions()
	{
		# Content Permissions
		$this->perms['htmlPerms'] = check_permission($this->userid, 'Add Global Content Blocks') |
			check_permission($this->userid, 'Modify Global Content Blocks') |
			check_permission($this->userid, 'Delete Global Content Blocks');

		global $gCms;
		$gcbops = $gCms->GetGlobalContentOperations();

		$thisUserBlobs = $gcbops->AuthorBlobs($this->userid);
		if (count($thisUserBlobs) > 0)
		{
			$this->perms['htmlPerms'] = true;
		}

		$this->perms['pagePerms'] = (
			check_permission($this->userid, 'Modify Any Page') ||
			check_permission($this->userid, 'Add Pages') ||
			check_permission($this->userid, 'Remove Pages') ||
			check_permission($this->userid, 'Modify Page Structure')
			);

		$thisUserPages = array(); //TODO: Fix me!!!
		if (count($thisUserPages) > 0)
		{
			$this->perms['pagePerms'] = true;
		}
		$this->perms['contentPerms'] = $this->perms['pagePerms'] | $this->perms['htmlPerms'] | 
			(isset($this->section_count['content']) && $this->section_count['content'] > 0);

		# layout        

		$this->perms['templatePerms'] = check_permission($this->userid, 'Add Templates') |
			check_permission($this->userid, 'Modify Templates') |
			check_permission($this->userid, 'Remove Templates');
		$this->perms['cssPerms'] = check_permission($this->userid, 'Add Stylesheets') |
			check_permission($this->userid, 'Modify Stylesheets') |
			check_permission($this->userid, 'Remove Stylesheets');
		$this->perms['cssAssocPerms'] = check_permission($this->userid, 'Add Stylesheet Assoc') |
			check_permission($this->userid, 'Modify Stylesheet Assoc') |
			check_permission($this->userid, 'Remove Stylesheet Assoc');
		$this->perms['layoutPerms'] = $this->perms['templatePerms'] |
			$this->perms['cssPerms'] | $this->perms['cssAssocPerms'] |
			(isset($this->section_count['layout']) && $this->section_count['layout'] > 0);

		# file / image
		$this->perms['filePerms'] = check_permission($this->userid, 'Modify Files') |
			(isset($this->section_count['files']) && $this->section_count['files'] > 0);

		# user/group
		$this->perms['userPerms'] = check_permission($this->userid, 'Add Users') |
			check_permission($this->userid, 'Modify Users') |
			check_permission($this->userid, 'Remove Users');
		$this->perms['groupPerms'] = check_permission($this->userid, 'Add Groups') |
			check_permission($this->userid, 'Modify Groups') |
			check_permission($this->userid, 'Remove Groups');
		$this->perms['groupPermPerms'] = check_permission($this->userid, 'Modify Permissions');
		$this->perms['groupMemberPerms'] =  check_permission($this->userid, 'Modify Group Assignments');
		$this->perms['usersGroupsPerms'] = $this->perms['userPerms'] |
			$this->perms['groupPerms'] |
			$this->perms['groupPermPerms'] |
			$this->perms['groupMemberPerms'] |
			(isset($this->section_count['usersgroups']) &&
			$this->section_count['usersgroups'] > 0);

		# admin
		$this->perms['sitePrefPerms'] = check_permission($this->userid, 'Modify Site Preferences') |
			(isset($this->section_count['preferences']) && $this->section_count['preferences'] > 0);
		$this->perms['adminPerms'] = $this->perms['sitePrefPerms'] |
			(isset($this->section_count['admin']) && $this->section_count['admin'] > 0);
		$this->perms['siteAdminPerms'] = $this->perms['sitePrefPerms'] |
			$this->perms['adminPerms'] |
			(isset($this->section_count['admin']) &&
			$this->section_count['admin'] > 0);


		# extensions
		$this->perms['codeBlockPerms'] = check_permission($this->userid, 'Modify User-defined Tags');
		$this->perms['modulePerms'] = check_permission($this->userid, 'Modify Modules');
			$this->perms['extensionsPerms'] = $this->perms['codeBlockPerms'] |
			$this->perms['modulePerms'] |
			(isset($this->section_count['extensions']) && $this->section_count['extensions'] > 0);
	}
	
    /**
     * has_displayable_children
     * This method returns a boolean, based upon whether the section in question
     * has displayable children.
     *
     * @param section - section to test
     */
     function has_displayable_children(&$node)
     {
		$children = $node->get_children();
		foreach ($children as &$child)
		{
			if ($child->show_in_menu)
				return true;
		}
        return false;
     }

    /**
     * DoBookmarks
     * Method for displaying admin bookmarks (shortcuts) & help links.
     */
	 #########TODO fix me########
	public function show_shortcuts()
	{
		if (get_preference($this->userid, 'bookmarks'))
		{
			echo '<div class="itemmenucontainer shortcuts" style="float:left;">';
			echo '<div class="itemoverflow">';
			echo '<h2>'.lang('bookmarks').'</h2>';
			echo '<p><a href="listbookmarks.php">'.lang('managebookmarks').'</a></p>';
			global $gCms;
			$bookops =& $gCms->GetBookmarkOperations();
			$marks = array_reverse($bookops->LoadBookmarks($this->userid));
			$marks = array_reverse($marks);
			if (FALSE == empty($marks))
			{
				echo '<h3 style="margin:0">'.lang('user_created').'</h3>';
				echo '<ul style="margin:0">';
				foreach($marks as $mark)
				{
					echo "<li><a href=\"". $mark->url."\">".$mark->title."</a></li>\n";
				}
				echo "</ul>\n";
			}
			echo '<h3 style="margin:0;">'.lang('help').'</h3>';
			echo '<ul style="margin:0;">';
			echo '<li><a href="http://forum.cmsmadesimple.org/">'.lang('forums').'</a></li>';
			echo '<li><a href="http://wiki.cmsmadesimple.org/">'.lang('wiki').'</a></li>';
			echo '<li><a href="http://cmsmadesimple.org/main/support/IRC">'.lang('irc').'</a></li>';
			echo '<li><a href="http://wiki.cmsmadesimple.org/index.php/User_Handbook/Admin_Panel/Extensions/Modules">'.lang('module_help').'</a></li>';
			echo '</ul>';
			echo '</div>';
			echo '</div>';
		}
	}
	
	public function ShowShortcuts()
	{
		return $this->show_shortcuts();
	}
	#########end ########
    /**
     * DisplayDashboardCallout
     * Outputs warning if the install directory is still there.
     *
     * @param file file or dir to check for
	 * @param message to display if it does exist
     */
	function DisplayDashboardCallout($file, $message = '')
	{
		if ($message == '')
			$message = lang('installdirwarning');

		echo "<div class=\"DashboardCallout\">\n";
		if (file_exists($file))
		{
			echo "<div class=\"pageerrorinstalldir\"><p class=\"pageerror\">".$message."</p></div>";
		}
		echo "</div> <!-- end DashboardCallout -->\n";
	}

    /**
     * DisplayImage will display the themed version of an image (if it exists),
     * or the version from the default theme otherwise.
     * @param imageName - name of image
     * @param alt - alt text
     * @param width
     * @param height
     */
	function display_image($imageName, $alt='', $width='', $height='', $class='')
	{
		if (! isset($this->imageLink[$imageName]))
		{
			if (strpos($imageName,'/') !== false)
			{
				$imagePath = substr($imageName,0,strrpos($imageName,'/')+1);
				$imageName = substr($imageName,strrpos($imageName,'/')+1);
			}
			else
			{
				$imagePath = '';
			}

			if (file_exists(dirname(CmsConfig::get('root_path') . '/' . CmsConfig::get('admin_dir') .
			'/themes/' . $this->themeName . '/images/' . $imagePath . $imageName) . '/'. $imageName))
			{
				$this->imageLink[$imageName] = 'themes/' .
				$this->themeName . '/images/' . $imagePath . $imageName;
			}
			else
			{
				$this->imageLink[$imageName] = 'themes/default/images/' . $imagePath . $imageName;
			}
		}

		$retStr = '<img src="'.$this->imageLink[$imageName].'"';
		if ($class != '')
		{
			$retStr .= ' class="'.$class.'"';
		}
		if ($width != '')
		{
			$retStr .= ' width="'.$width.'"';
		}
		if ($height != '')
		{
			$retStr .= ' height="'.$height.'"';
		}
		if ($alt != '')
		{
			$retStr .= ' alt="'.$alt.'" title="'.$alt.'"';
		}
		$retStr .= ' />';
		return $retStr;
	}
	
	function DisplayImage($imageName, $alt='', $width='', $height='', $class='')
	{
		return $this->display_image($imageName, $alt, $width, $height, $class);
	}
	
	/**
	 * Returns a url to an icon for the given name.  If the icon doesn't exist in
	 * the current theme, it gives the url for the default theme instead.
	 *
	 * @param string The name of the icon
	 * @return string The url to the icon
	 * @author Ted Kulp
	 **/
	function find_icon_url($name)
	{
		$name .= '.gif';
		if (file_exists(CmsConfig::get('root_path') . '/' . CmsConfig::get('admin_dir') . '/themes/' . $this->theme_name . '/images/icons/topfiles/' . $name))
		{
			return CmsConfig::get('root_url') . '/' . CmsConfig::get('admin_dir') . '/themes/' . $this->theme_name . '/images/icons/topfiles/' . $name;
		}
		else
		{
			return CmsConfig::get('root_url') . '/' . CmsConfig::get('admin_dir') . '/themes/default/images/icons/topfiles/' . $name;
		}
	}
	
	public function add_error($error_string)
	{
		$this->errors[] = $error_string;
	}
	
	public function has_errors()
	{
		return count($this->errors) > 0;
	}
	
	public function add_message($message_string)
	{
		$this->messages[] = $message_string;
	}
	
	public function has_messages()
	{
		return count($this->messages) > 0;
	}
	
    /**
     * ShowError
     * Outputs supplied errors with a link to the wiki for troublshooting.
     *
     * @param errors - array or string of 1 or more errors to be shown
     * @param get_var - Name of the _GET variable that contains the 
     *                  name of the message lang string
     */
	function show_errors($errors, $get_var='')
	{
		global $gCms;
		$config =& $gCms->GetConfig();
		$wikiUrl = $config['wiki_url'];

		if (FALSE == empty($_REQUEST['module'])  || FALSE == empty($_REQUEST['mact']))
		{
			if (FALSE == empty($_REQUEST['module']))
			{
				$wikiUrl .= '/'.$_REQUEST['module'];
			}
			else
			{
				$wikiUrl .= '/'.substr($_REQUEST['mact'], 0, strpos($_REQUEST['mact'], ','));
			}
		}
		$wikiUrl .= '/Troubleshooting';
		$wikiLink = ' <a href="'.$wikiUrl.'" rel="external">'.lang('troubleshooting').'</a>';
		if (FALSE != is_array($errors))
		{
			$output = '<ul class="pageerrorcontainer">';
			foreach ($errors as $oneerror)
			{
				$output .= '<li>'.$oneerror.'</li>';
			}
			$output .= '<li>'.$wikiLink.'</li>';
			$output .= '</ul>';
		}
		else
		{
			$output  = '<div class="pageerrorcontainer"';
			if (FALSE == empty($get_var))
			{
				if (FALSE == empty($_GET[$get_var]))
				{
					$errors = cleanValue(lang(cleanValue($_GET[$get_var])));
				}
				else
				{
					$errors = '';
					$output .= ' style="display:none;"';
				}
			}
			$output .= '><div class="pageoverflow">';
			$output  .= $errors.$wikiLink.'</div></div>';
		}
		return $output;
    }

	function ShowErrors($errors, $get_var = '')
	{
		return $this->show_errors($errors, $get_var);
	}
	
    /**
     * ShowMessage
     * Outputs a page status message
     *
     * @param message - Message to be shown
     * @param get_var - Name of the _GET variable that contains the 
     *                  name of the message lang string
     */
	function show_message($message, $get_var = '')
	{
		$output = '<div class="pagemcontainer"';
		if (FALSE == empty($get_var))
		{
			if (FALSE == empty($_GET[$get_var]))
			{
				$message = lang(cleanValue($_GET[$get_var]));
			}
			else
			{
				$message = '';
				$output .= ' style="display:none;"';
			}
		}
		$output .= '><p class="pagemessage">'.$message.'</p></div>';
		return $output;
	}
	
	function ShowMessage($message, $get_var = '')
	{
		return $this->show_message($message, $get_var);
	}

    /**
     * Displays the list of sections and subitems for the main menu.
     *
	 * @return void
	 * @author Ted Kulp
     */
	function display_all_section_pages()
	{
		$smarty = cms_smarty();
		#########TODO fix me########
          	global $gCms;
			$bookops =& $gCms->GetBookmarkOperations();
			$marks = array_reverse($bookops->LoadBookmarks($this->userid));
			$marks = array_reverse($marks);
		    $smarty->assign('show_admin_shortcuts',get_preference($this->userid, 'bookmarks'));
            $smarty->assign('marks', $marks);
          #########end ########
		$root_node = CmsAdminTree::get_instance()->get_root_node();

		$smarty->assign('subitems', lang('subitems'));
		$smarty->assign_by_ref('root_node', $root_node);
		$smarty->display(self::get_instance()->theme_template_dir . 'indexcontent.tpl');
	}
	
	/**
	 * Displays the subitems for the given section name.  It's used for the
	 * admin "top*" pages.
	 *
	 * @param name The name of the section to display
	 * @return void
	 * @author Ted Kulp
	 **/
	function display_section_pages($section)
	{	
		$children = CmsAdminTree::get_instance()->get_root_node()->get_children();
		$node = null;
		foreach ($children as &$basenode)
		{
			if ($basenode->name == $section)
			{
				$node =& $basenode;
				break;
			}
		}
		
		if ($node == null)
		{
			return;
		}
		
		$smarty = cms_smarty();
		#########TODO fix me########
          	global $gCms;
			$bookops =& $gCms->GetBookmarkOperations();
			$marks = array_reverse($bookops->LoadBookmarks($this->userid));
			$marks = array_reverse($marks);
		    $smarty->assign('show_admin_shortcuts',get_preference($this->userid, 'bookmarks'));
            $smarty->assign('marks', $marks);
           #########end ########
		$smarty->assign_by_ref('top_node', $node);
		$smarty->display(self::get_instance()->theme_template_dir . 'sectiontop.tpl');
	}

	/**
	 * Displays the html that makes up the admin menu.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	function display_top_menu()
	{
		$smarty = cms_smarty();
		$root_node = CmsAdminTree::get_instance()->get_root_node();
		$smarty->assign('admin_panel_title', lang('admin_panel_title'));
		$smarty->assign_by_ref('root_node', $root_node);
		$smarty->assign('breadcrumbs', $this->breadcrumbs);
		$smarty->display(self::get_instance()->theme_template_dir . 'topmenu.tpl');
	}
	
	function StartRighthandColumn()
	{
		echo '<div class="navt_menu">'."\n";
		echo '<div id="navt_display" class="navt_show" onclick="change(\'navt_display\', \'navt_hide\', \'navt_show\'); change(\'navt_container\', \'invisible\', \'visible\');"></div>'."\n";
		echo '<div id="navt_container" class="invisible">'."\n";
		echo '<div id="navt_tabs">'."\n";
		if (get_preference($this->userid, 'bookmarks'))
		{
			echo '<div id="navt_bookmarks">'.lang('bookmarks').'</div>'."\n";
		}
		echo '</div>'."\n";
		echo '<div style="clear: both;"></div>'."\n";
		echo '<div id="navt_content">'."\n";
	}

	function DisplayRecentPages()
	{
		if (get_preference($this->userid, 'recent'))
		{	
			echo '<div id="navt_recent_pages_c">'."\n";
			$counter = 0;
			foreach($this->recent as $pg)
			{
				echo "<a href=\"". $pg->url."\">".++$counter.'. '.$pg->title."</a><br />"."\n";
			}
			echo '</div>'."\n";
		}
	}

	function DisplayBookmarks($marks)
	{
		if (get_preference($this->userid, 'bookmarks'))
		{	
			echo '<div id="navt_bookmarks_c">'."\n";
			$counter = 0;
			foreach($marks as $mark)
			{
				echo "<a href=\"". $mark->url."\">".++$counter.'. '.$mark->title."</a><br />"."\n";
			}
			echo '</div>'."\n";
		}
	}	 

	function EndRighthandColumn()
	{
		echo '</div>'."\n";
		echo '</div>'."\n";
		echo '<div style="clear: both;"></div>'."\n";
		echo '</div>'."\n";
	}

	/* Functions that we want dont want the standard output from */
	function OutputFooterJavascript() {}
}

# vim:ts=4 sw=4 noet
?>