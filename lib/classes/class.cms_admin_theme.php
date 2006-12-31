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
#$Id: class.cms_config.php 3687 2006-12-31 16:36:30Z wishy $

class CmsAdminTheme extends CmsObject
{
	static private $instance = NULL;
	public $breadcrumbs = array();
	public $menuItems;
	public $script;
	public $headtext = '';
	public $section_count = array();
	public $modules_by_section = array();

	function __construct()
	{
		parent::__construct();
	}
	
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = self::get_theme_for_user();
		}
		return self::$instance;
	}
	
	static function get_theme_for_user()
	{
		$gCms = cmsms();
		$userid = get_userid();

		$themeName=get_preference(get_userid(), 'admintheme', 'default');
		$themeObjectName = $themeName."Theme";
		$userid = get_userid();

		if (file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."admin/themes/${themeName}/${themeObjectName}.php"))
		{
			include(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."admin/themes/${themeName}/${themeObjectName}.php");
			$themeObject = new $themeObjectName($gCms, $userid, $themeName);
		}
		else
		{
			$themeObject = new CmsAdminTheme($gCms, $userid, $themeName);
		}
		
		$themeObject->userid = get_userid();
		$themeObject->set_module_admin_interfaces();
		$themeObject->set_aggrigate_permissions();
		$themeObject->populate_admin_navigation();

		return $themeObject;
	}
	
	static public function start()
	{
		@ob_start();
	}
	
	static public function end()
	{
		$result = @ob_get_clean();
		
		$smarty = cms_smarty();
		$smarty->assign('admin_content', $result);
		$smarty->template_dir = dirname(dirname(dirname(__FILE__))) . '/admin/themes/'.self::get_instance()->themeName.'/templates/';
		
		@ob_start();
		self::get_instance()->display_top_menu();
		$topmenu = @ob_get_clean();
		$smarty->assign('admin_topmenu', $topmenu);
		
		$smarty->assign('headtext', self::get_instance()->headtext);
		
		$smarty->display('overall.tpl');
		
		echo '<div id="_DebugFooter">';
		echo CmsProfiler::get_instance()->report();
		echo '</div> <!-- end DebugFooter -->';
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
					$title_key = $this->_ArraySearchRecursive($title, $this->menuItems);
					$wikiUrl .= '/'. CmsLanguage::translate($title_key[0], array(), 'core', 'en_US');
					$help_title = $title;
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
					$header .= '<a href="'.$wikiUrl.'" target="_blank">'.$image_help_external.'</a> <a href="'.$wikiUrl.'" target="_blank">'.lang('wikihelp').'</a>  ('.lang('new_window').')</span>';
				}
				else
				{
					$header .= '<span class="helptext"><a href="'.$wikiUrl.'" target="_blank">'.$image_help_external.'</a> <a href="'.$wikiUrl.'" target="_blank">'.lang('help').'</a> ('.lang('new_window').')</span>';
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
		if (count($this->menuItems) > 0)
		{
			// we have already created the list
			return;
		}
        $this->subtitle = $subtitle;
    	    
    	$this->menuItems = array(
    	    // base main menu ---------------------------------------------------------
            'main'=>array('url'=>'index.php','parent'=>-1,
                    'title'=>$this->fix_spaces(lang('main')),
                    'description'=>'','show_in_menu'=>true),
            // base content menu ---------------------------------------------------------
            'content'=>array('url'=>'topcontent.php','parent'=>-1,
                    'title'=>$this->fix_spaces(lang('content')),
                    'description'=>lang('contentdescription'),'show_in_menu'=>$this->has_permission('contentPerms')),
            'pages'=>array('url'=>'listcontent.php','parent'=>'content',
                    'title'=>$this->fix_spaces(lang('pages')),
                    'description'=>lang('pagesdescription'),'show_in_menu'=>$this->has_permission('pagePerms')),
            'addcontent'=>array('url'=>'addcontent.php','parent'=>'pages',
                    'title'=>$this->fix_spaces(lang('addcontent')),
                    'description'=>lang('addcontent'),'show_in_menu'=>false),
            'editpage'=>array('url'=>'editcontent.php','parent'=>'pages',
                    'title'=>$this->fix_spaces(lang('editpage')),
                    'description'=>lang('editpage'),'show_in_menu'=>false),
            'files'=>array('url'=>'files.php','parent'=>'content',
                    'title'=>$this->fix_spaces(lang('filemanager')),
                    'description'=>lang('filemanagerdescription'),'show_in_menu'=>$this->has_permission('filePerms')),
            'images'=>array('url'=>'imagefiles.php','parent'=>'content',
                    'title'=>$this->fix_spaces(lang('imagemanager')),
                    'description'=>lang('imagemanagerdescription'),'show_in_menu'=>$this->has_permission('filePerms')),
            'blobs'=>array('url'=>'listhtmlblobs.php','parent'=>'content',
                    'title'=>$this->fix_spaces(lang('htmlblobs')),
                    'description'=>lang('htmlblobdescription'),'show_in_menu'=>$this->has_permission('htmlPerms')),
            'addhtmlblob'=>array('url'=>'addhtmlblob.php','parent'=>'blobs',
                    'title'=>$this->fix_spaces(lang('addhtmlblob')),
                    'description'=>lang('addhtmlblob'),'show_in_menu'=>false),
            'edithtmlblob'=>array('url'=>'edithtmlblob.php','parent'=>'blobs',
                    'title'=>$this->fix_spaces(lang('edithtmlblob')),
                    'description'=>lang('edithtmlblob'),'show_in_menu'=>false),
             // base layout menu ---------------------------------------------------------
            'layout'=>array('url'=>'toplayout.php','parent'=>-1,
                    'title'=>$this->fix_spaces(lang('layout')),
                    'description'=>lang('layoutdescription'),'show_in_menu'=>$this->has_permission('layoutPerms')),
            'template'=>array('url'=>'listtemplates.php','parent'=>'layout',
                    'title'=>$this->fix_spaces(lang('templates')),
                    'description'=>lang('templatesdescription'),'show_in_menu'=>$this->has_permission('templatePerms')),
            'addtemplate'=>array('url'=>'addtemplate.php','parent'=>'template',
                    'title'=>$this->fix_spaces(lang('addtemplate')),
                    'description'=>lang('addtemplate'),'show_in_menu'=>false),
            'edittemplate'=>array('url'=>'edittemplate.php','parent'=>'template',
                    'title'=>$this->fix_spaces(lang('edittemplate')),
                    'description'=>lang('edittemplate'),'show_in_menu'=>false),
            'currentassociations'=>array('url'=>'listcssassoc.php','parent'=>'template',
                    'title'=>$this->fix_spaces(lang('currentassociations')),
                    'description'=>lang('currentassociations'),'show_in_menu'=>false),
            'copytemplate'=>array('url'=>'copyemplate.php','parent'=>'template',
                    'title'=>$this->fix_spaces(lang('copytemplate')),
                    'description'=>lang('copytemplate'),'show_in_menu'=>false),
            'stylesheets'=>array('url'=>'listcss.php','parent'=>'layout',
                    'title'=>$this->fix_spaces(lang('stylesheets')),
                    'description'=>lang('stylesheetsdescription'),
                    'show_in_menu'=>($this->has_permission('cssPerms') || $this->has_permission('cssAssocPerms'))),
            'addcss'=>array('url'=>'addcss.php','parent'=>'stylesheets',
                    'title'=>$this->fix_spaces(lang('addstylesheet')),
                    'description'=>lang('addstylesheet'),'show_in_menu'=>false),
            'editcss'=>array('url'=>'editcss.php','parent'=>'stylesheets',
                    'title'=>$this->fix_spaces(lang('editcss')),
                    'description'=>lang('editcss'),'show_in_menu'=>false),
            'templatecss'=>array('url'=>'templatecss.php','parent'=>'stylesheets',
                    'title'=>$this->fix_spaces(lang('templatecss')),
                    'description'=>lang('templatecss'),'show_in_menu'=>false),
             // base user/groups menu ---------------------------------------------------------
            'usersgroups'=>array('url'=>'topusers.php','parent'=>-1,
                    'title'=>$this->fix_spaces(lang('usersgroups')),
                    'description'=>lang('usersgroupsdescription'),'show_in_menu'=>$this->has_permission('usersGroupsPerms')),
            'users'=>array('url'=>'listusers.php','parent'=>'usersgroups',
                    'title'=>$this->fix_spaces(lang('users')),
                    'description'=>lang('usersdescription'),'show_in_menu'=>$this->has_permission('userPerms')),
            'adduser'=>array('url'=>'adduser.php','parent'=>'users',
                    'title'=>$this->fix_spaces(lang('adduser')),
                    'description'=>lang('adduser'),'show_in_menu'=>false),
            'edituser'=>array('url'=>'edituser.php','parent'=>'users',
                    'title'=>$this->fix_spaces(lang('edituser')),
                    'description'=>lang('edituser'),'show_in_menu'=>false),
            'groups'=>array('url'=>'listgroups.php','parent'=>'usersgroups',
                    'title'=>$this->fix_spaces(lang('groups')),
                    'description'=>lang('groupsdescription'),'show_in_menu'=>$this->has_permission('groupPerms')),
            'addgroup'=>array('url'=>'addgroup.php','parent'=>'groups',
                    'title'=>$this->fix_spaces(lang('addgroup')),
                    'description'=>lang('addgroup'),'show_in_menu'=>false),
            'editgroup'=>array('url'=>'editgroup.php','parent'=>'groups',
                    'title'=>$this->fix_spaces(lang('editgroup')),
                    'description'=>lang('editgroup'),'show_in_menu'=>false),
            'groupmembers'=>array('url'=>'changegroupassign.php','parent'=>'usersgroups',
                    'title'=>$this->fix_spaces(lang('groupassignments')),
                    'description'=>lang('groupassignmentdescription'),'show_in_menu'=>$this->has_permission('groupMemberPerms')),                    
            'groupperms'=>array('url'=>'changegroupperm.php','parent'=>'usersgroups',
                    'title'=>$this->fix_spaces(lang('groupperms')),
                    'description'=>lang('grouppermsdescription'),'show_in_menu'=>$this->has_permission('groupPermPerms')),                    
             // base extensions menu ---------------------------------------------------------
            'extensions'=>array('url'=>'topextensions.php','parent'=>-1,
                    'title'=>$this->fix_spaces(lang('extensions')),
                    'description'=>lang('extensionsdescription'),'show_in_menu'=>$this->has_permission('extensionsPerms')),
            'modules'=>array('url'=>'listmodules.php','parent'=>'extensions',
                    'title'=>$this->fix_spaces(lang('modules')),
                    'description'=>lang('moduledescription'),'show_in_menu'=>$this->has_permission('modulePerms')),
            'tags'=>array('url'=>'listtags.php','parent'=>'extensions',
                    'title'=>$this->fix_spaces(lang('tags')),
                    'description'=>lang('tagdescription'),'show_in_menu'=>true),
            'eventhandlers'=>array('url'=>'eventhandlers.php','parent'=>'extensions',
                    'title'=>$this->fix_spaces(lang('eventhandlers')),
                    'description'=>lang('eventhandlerdescription'),'show_in_menu'=>true),
            'editeventhandler'=>array('url'=>'editevent.php','parent'=>'eventhandlers',
                    'title'=>$this->fix_spaces(lang('editeventhandler')),
                    'description'=>lang('editeventshandler'),'show_in_menu'=>false),
            'usertags'=>array('url'=>'listusertags.php','parent'=>'extensions',
                    'title'=>$this->fix_spaces(lang('usertags')),
                    'description'=>lang('usertagdescription'),'show_in_menu'=>$this->has_permission('codeBlockPerms')),
            'addusertag'=>array('url'=>'adduserplugin.php','parent'=>'usertags',
                    'title'=>$this->fix_spaces(lang('addusertag')),
                    'description'=>lang('addusertag'),'show_in_menu'=>false),
            'editusertag'=>array('url'=>'edituserplugin.php','parent'=>'usertags',
                    'title'=>$this->fix_spaces(lang('editusertag')),
                    'description'=>lang('editusertag'),'show_in_menu'=>false),
             // base admin menu ---------------------------------------------------------
            'siteadmin'=>array('url'=>'topadmin.php','parent'=>-1,
                    'title'=>$this->fix_spaces(lang('admin')),
                    'description'=>lang('admindescription'),'show_in_menu'=>$this->has_permission('siteAdminPerms')),
            'siteprefs'=>array('url'=>'siteprefs.php','parent'=>'siteadmin',
                    'title'=>$this->fix_spaces(lang('globalconfig')),
                    'description'=>lang('preferencesdescription'),'show_in_menu'=>$this->has_permission('sitePrefPerms')),
            'adminlog'=>array('url'=>'adminlog.php','parent'=>'siteadmin',
                    'title'=>$this->fix_spaces(lang('adminlog')),
                    'description'=>lang('adminlogdescription'),'show_in_menu'=>$this->has_permission('adminPerms')),
             // base my prefs menu ---------------------------------------------------------
            'myprefs'=>array('url'=>'topmyprefs.php','parent'=>-1,
                    'title'=>$this->fix_spaces(lang('myprefs')),
                    'description'=>lang('myprefsdescription'),'show_in_menu'=>true),
            'myaccount'=>array('url'=>'edituser.php','parent'=>'myprefs',
                    'title'=>$this->fix_spaces(lang('myaccount')),
                    'description'=>lang('myaccountdescription'),'show_in_menu'=>true),
            'preferences'=>array('url'=>'editprefs.php','parent'=>'myprefs',
                    'title'=>$this->fix_spaces(lang('adminprefs')),
                    'description'=>lang('adminprefsdescription'),'show_in_menu'=>true),
            'managebookmarks'=>array('url'=>'listbookmarks.php','parent'=>'myprefs',
                    'title'=>$this->fix_spaces(lang('managebookmarks')),
                    'description'=>lang('managebookmarksdescription'),'show_in_menu'=>true),
            'addbookmark'=>array('url'=>'addbookmark.php','parent'=>'myprefs',
                    'title'=>$this->fix_spaces(lang('addbookmark')),
                    'description'=>lang('addbookmark'),'show_in_menu'=>false),
            'editbookmark'=>array('url'=>'editbookmark.php','parent'=>'myprefs',
                    'title'=>$this->fix_spaces(lang('editbookmark')),
                    'description'=>lang('editbookmark'),'show_in_menu'=>false),
             // base view site menu ---------------------------------------------------------
            'viewsite'=>array('url'=>'../','parent'=>-1,
                    'title'=>$this->fix_spaces(lang('viewsite')),
                    'description'=>'','show_in_menu'=>true, 'target'=>'_blank'),
             // base logout menu ---------------------------------------------------------
             'logout'=>array('url'=>'logout.php','parent'=>-1,
                    'title'=>$this->fix_spaces(lang('logout')),
                    'description'=>'','show_in_menu'=>true),
    	);

		// add in all of the 'system' modules todo
		$gCms = cmsms();
		foreach ($this->menuItems as $sectionKey=>$sectionArray)
		{
			$tmpArray = $this->menu_list_section_modules($sectionKey);
			$first = true;
			foreach ($tmpArray as $thisKey=>$thisVal)
			{
				$thisModuleKey = $thisKey;
				$counter = 0;

				// don't clobber existing keys
				if (array_key_exists($thisModuleKey,$this->menuItems))
				{
					while (array_key_exists($thisModuleKey,$this->menuItems))
					{
						$thisModuleKey = $thisKey.$counter;
						$counter++;
					}
				}

				// if it's not a system module...
				if (array_search($thisModuleKey, $gCms->cmssystemmodules) !== FALSE)
				{
					$this->menuItems[$thisModuleKey]=array('url'=>$thisVal['url'],
						'parent'=>$sectionKey,
						'title'=>$this->fix_spaces($thisVal['name']),
						'description'=>$thisVal['description'],
						'show_in_menu'=>true);


				}
			}
		}

		// add in all of the modules
		foreach ($this->menuItems as $sectionKey=>$sectionArray)
		{
			$tmpArray = $this->menu_list_section_modules($sectionKey);
			$first = true;
			foreach ($tmpArray as $thisKey=>$thisVal)
			{
				$thisModuleKey = $thisKey;
				$counter = 0;

				// don't clobber existing keys
				if (array_key_exists($thisModuleKey,$this->menuItems))
				{
					while (array_key_exists($thisModuleKey,$this->menuItems))
					{
						$thisModuleKey = $thisKey.$counter;
						$counter++;
					}
					if( $counter > 0 )
					{
						continue;
					}
				}
				$this->menuItems[$thisModuleKey]=array('url'=>$thisVal['url'],
					'parent'=>$sectionKey,
					'title'=>$this->fix_spaces($thisVal['name']),
					'description'=>$thisVal['description'],
					'show_in_menu'=>true);

				if ($first)
				{
					$this->menuItems[$thisModuleKey]['firstmodule'] = 1;
					$first = false;
				}
				else
				{
					$this->menuItems[$thisModuleKey]['module'] = 1;
				}
			}
		}
	
		// resolve the tree to be doubly-linked,
		// and make sure the selections are selected            
		foreach ($this->menuItems as $sectionKey=>$sectionArray)
		{
			// link the children to the parents; a little clumsy since we can't
			// assume php5-style references in a foreach.
			$this->menuItems[$sectionKey]['children'] = array();
			foreach ($this->menuItems as $subsectionKey=>$subsectionArray)
			{
				if ($subsectionArray['parent'] == $sectionKey)
				{
					$this->menuItems[$sectionKey]['children'][] = $subsectionKey;
				}
			}
            // set selected
			if ($this->script == 'moduleinterface.php')
			{
				$a = preg_match('/(module|mact)=([^&,]+)/',$this->query,$matches);
				if ($a > 0 && $matches[2] == $sectionKey)
				{
					$this->menuItems[$sectionKey]['selected'] = true;
					$this->title .= $sectionArray['title'];
					if ($sectionArray['parent'] != -1)
					{
						$parent = $sectionArray['parent'];
						while ($parent != -1)
						{
							$this->menuItems[$parent]['selected'] = true;
							$parent = $this->menuItems[$parent]['parent'];
						}
					}
				}
				else
				{
					$this->menuItems[$sectionKey]['selected'] = false;
				}
			}
			else if ($sectionArray['url'] == $this->script)
			{
				$this->menuItems[$sectionKey]['selected'] = true;
				$this->title .= $sectionArray['title'];
				if ($sectionArray['parent'] != -1)
				{
					$parent = $sectionArray['parent'];
					while ($parent != -1)
					{
						$this->menuItems[$parent]['selected'] = true;
						$parent = $this->menuItems[$parent]['parent'];
					}
				}
			}
			else
			{
				$this->menuItems[$sectionKey]['selected'] = false;
			}
		}
		// fix subtitle, if any
		if ($subtitle != '')
		{
			$this->title .= ': '.$subtitle;
		}
		// generate breadcrumb array

		$count = 0;
		foreach ($this->menuItems as $key=>$menuItem)
		{
			if ($menuItem['selected'])
			{
				$this->breadcrumbs[] = array('title'=>$menuItem['title'], 'url'=>$menuItem['url']);
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
				&& $cmsmodules[$key]['object']->HasAdmin()
				&& $cmsmodules[$key]['object']->VisibleToAdminUser())
			{
				$section = $cmsmodules[$key]['object']->GetAdminSection();
				if (! isset($this->section_count[$section]))
				{
					$this->section_count[$section] = 0;
				}
				$this->modules_by_section[$section][$this->section_count[$section]]['key'] = $key;
				if ($cmsmodules[$key]['object']->GetFriendlyName() != '')
				{
					$this->modules_by_section[$section][$this->section_count[$section]]['name'] =
					$cmsmodules[$key]['object']->GetFriendlyName();
				}
				else
				{
					$this->modules_by_section[$section][$this->section_count[$section]]['name'] = $key;
				}
				if ($cmsmodules[$key]['object']->GetAdminDescription() != '')
				{
					$this->modules_by_section[$section][$this->section_count[$section]]['description'] =
					$cmsmodules[$key]['object']->GetAdminDescription();
				}
				else
				{
					$this->modules_by_section[$section][$this->section_count[$section]]['description'] = "";
				}
				$this->section_count[$section]++;
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
				$modList[$sectionModule['key']]['url'] = "moduleinterface.php?module=".
				$sectionModule['key'];
				$modList[$sectionModule['key']]['description'] = $sectionModule['description'];
				$modList[$sectionModule['key']]['name'] = $sectionModule['name'];
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
		$gcbops =& $gCms->GetGlobalContentOperations();

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

		$thisUserPages = author_pages($this->userid);
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
     function has_displayable_children($section)
     {
        $displayableChildren=false;
        foreach($this->menuItems[$section]['children'] as $thisChild)
            {
            $thisItem = $this->menuItems[$thisChild];
            if ($thisItem['show_in_menu'])
                {
                $displayableChildren = true;
                }
            }
        return $displayableChildren;
     }

    /**
     * DoBookmarks
     * Method for displaying admin bookmarks (shortcuts) & help links.
     */
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
			echo '<p>'.$message.'</p>';
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
		$wikiLink = ' <a href="'.$wikiUrl.'" target="_blank">'.lang('troubleshooting').'</a>';
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
     * DisplaySectionMenuDivStart
     * Outputs the open div tag for the main section pages.
     */
    function DisplaySectionMenuDivStart()
    {
        echo "<div class=\"MainMenu\">\n";
    }

    /**
     * DisplaySectionMenuDivEnd
     * Outputs the close div tag for the main section pages.
     */
    function DisplaySectionMenuDivEnd()
    {
        echo "</div>\n";
    }

    /**
     * display_all_section_pages
     *
     * Shows all admin section pages and modules. This is used to display the
     * admin "main" page.
     *
     */
	function display_all_section_pages()
	{
		if (count($this->menuItems) < 1)
		{
			// menu should be initialized before this gets called.
			// TODO: try to do initialization.
			// Problem: current page selection, url, etc?
			return -1;
		}
		foreach ($this->menuItems as $thisSection=>$menuItem)
		{
			if ($menuItem['parent'] != -1)
			{
				continue;
			}
			if (! $menuItem['show_in_menu'])
			{
				continue;
			}
			echo "<div class=\"MainMenuItem\">\n";
			echo "<a href=\"".$menuItem['url']."\"";
			if (array_key_exists('target', $menuItem))
			{
				echo " target=" . $menuItem['target'];
			}
			if ($menuItem['selected'])
			{
				echo " class=\"selected\"";
			}
			echo ">".$menuItem['title']."</a>\n";
			echo "<span class=\"description\">";
			if (isset($menuItem['description']) && strlen($menuItem['description']) > 0)
			{
				echo $menuItem['description'];
			}
			$this->ListSectionPages($thisSection);
			echo "</span>\n";
			echo "</div>\n";
		}
	}
	
	function DisplayAllSectionPages()
	{
		return $this->display_all_section_pages();
	}
}

# vim:ts=4 sw=4 noet
?>