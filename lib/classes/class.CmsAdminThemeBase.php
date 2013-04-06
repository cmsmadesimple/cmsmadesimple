<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: class.admintheme.inc.php 7596 2011-12-24 22:50:52Z calguy1000 $

/**
 * @package CMS 
 */


/**
 * Base class for CMSMS Admin themes.
 * This is an abstract class that is used for building CMSMS Admin Themes.
 * This is also a singleton object.
 *
 * @package CMS
 * @version $Revision$
 * @license GPL
 * @since   1.11
 * @author  Robert Campbell
 */
abstract class CmsAdminThemeBase 
{
	private static $_instance;
	private $_perms;
	private $_menuItems;
	private $_nav_tree;
	private $_notifications;
	private $_breadcrumbs;
	private $_errors;
	private $_messages;
	private $_imageLink;
	private $_script;
	private $_url;
	private $_query;
	private $_data;

	// meta information
	private $_sectionCount;
	private $_modulesBySection;
	private $_active_item;
	
	// tab variables
	private $_activetab;
	private $_title;
	private $_subtitle;
	
	/**
	 * Constructor
	 */
	protected function __construct()
	{
		if( is_object(self::$_instance) ) {
			throw new CmsLogicExceptin('Only one instance of a theme object is permitted');
		}

		$this->_url = $_SERVER['SCRIPT_NAME'];
		$this->_query = (isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'');
		if( $this->_query == '' && isset($_POST['mact']) ) {
			$tmp = explode(',',$_POST['mact']);
			$this->_query = 'module='.$tmp[0];
		}
		if ($this->_query == '' && isset($_POST['module']) && $_POST['module'] != '') {
			$this->_query = 'module='.$_POST['module'];
		}
        if (strpos( $this->_url, '/' ) === false) {
			$this->_script = $this->_url;
		}
        else {
			$toam_tmp = explode('/',$this->_url);
			$toam_tmp2 = array_pop($toam_tmp);
			$this->_script = $toam_tmp2;
		}
	}

	/**
	 * __get()
	 * A magic function that is used for backwards compatibility only
	 *
	 * @deprecated
	 * @param string key - possible values are 'cms','themeName',and 'userid'
	 * @return mixed
	 */
	public function __get($key)
	{
		if( $key == 'cms' ) return cmsms();
		if( $key == 'themeName' ) {
			$class = get_class($this); 
			if( endswith($class,'Theme') ) $class = substr($class,0,strlen($class)-5);
			return $class;
		}
		if( $key == 'userid' ) return get_userid();
		if( $key == 'title' ) return $this->_title;
		if( $key == 'subtitle' ) return $this->_title;
	}

    /**
     * FixSpaces
     * This method converts spaces into a non-breaking space HTML entity.
     * It's used for making menus that work nicely
     *
     * @param str string to have its spaces converted
	 * @ignore
     */
    private function _FixSpaces($str)
    {
		$tmp = preg_replace('/\s+/u',"&nbsp;",$str); // PREG UTF8
		if(!empty($tmp)) return $tmp;
		else return preg_replace('/\s+/',"&nbsp;",$str); // bad UTF8
    }

	private function _fix_url_userkey($url)
	{
		$from = '/'.CMS_SECURE_PARAM_NAME.'=[a-zA-Z0-9]{8}/i';
		$to = CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
		$newurl = preg_replace($from,$to,$url);
		return $newurl;
	}

	/**
	 * _get_user_module_info
	 *
	 * Given the currently logged in user, this will read cache information representing info for all avallable modules
	 * for that particular user.   If cache information is not available, then modules will be loaded and the information
	 * will be gleaned from the module for that user.
	 *
	 * 
	 * @since 1.10
	 * @access private
	 * @ignore
	 * @author calguy1000
	 */
	private function _get_user_module_info()
	{
		$uid = get_userid(FALSE);
		if( ($data = cms_cache_handler::get_instance()->get('themeinfo'.$uid)) ) {
			$data = base64_decode($data);
			$data = unserialize($data);
		}
		else {
			// data doesn't exist.. gotta build it.
			$allmodules = ModuleOperations::get_instance()->GetInstalledModules();
			$usermoduleinfo = array();
			foreach( $allmodules as $key ) {
				$object = ModuleOperations::get_instance()->get_module_instance($key);
				if( is_object($object) && $object->HasAdmin() ) {
					$recs = $object->GetAdminMenuItems();
					$suffix = 1;
					if( is_array($recs) && count($recs) ) {
						foreach( $recs as $one ) {
							if( !$one->valid() ) continue;
							$one->url = str_replace('&amp;','&',$one->url);
							if( ModuleOperations::Get_instance()->IsSystemModule($object->GetName()) ) $one->system = TRUE;
							$key = $one->module.$suffix++;
							$usermoduleinfo[$key] = $one;
						}
					}
				}
			}

			// even if the array is empty... serialize the info.
			$data = $usermoduleinfo;
			$tmp = serialize($data);
			cms_cache_handler::get_instance()->set('themeinfo'.$uid,base64_encode($tmp));
		}
		return $data;
	}


    /**
     * _SetModuleAdminInterfaces
     *
     * This function sets up data structures to place modules in the proper Admin sections
     * for display on section pages and menus.
     *
	 * @since 1.10
	 * @access private
	 * @ignore
     */
    private function _SetModuleAdminInterfaces()
    {
		if( is_array($this->_sectionCount) ) return;

		$this->_sectionCount = array();
		$this->_modulesBySection = array();

		// get the info from the cache
		$usermoduleinfo = $this->_get_user_module_info();
		if( !is_array($usermoduleinfo) ) {
			// put mention into the admin log
			audit(get_userid(FALSE),'Admin Theme','No module information found for user');
		}
		else {
			// Are there any modules with an admin interface?
			foreach( $usermoduleinfo as $key => $obj ) {
				if( $obj->section == '' ) $obj->section = 'extensions';

				$section = $obj->section;
				if (! isset($this->_sectionCount[$section])) {
					$this->_sectionCount[$section] = 0;
				}

				// fix up the session key stuff.
				$obj->url = $this->_fix_url_userkey($obj->url);

				// find an icon for this thing.
				if( $obj->icon == '' ) {
					$config = cmsms()->GetConfig();
					$tmp = array("modules/{$key}/images/icon.gif",
								 "modules/{$key}/icons/icons.gif",
								 "modules/{$key}/images/icon.png",
								 "modules/{$key}/icons/icons.png");
					foreach( $tmp as $one ) {
						$fn = cms_join_path($config['root_path'],$one);
						if( file_exists($fn) ) {
							$obj->icon = $config['root_url'].'/'.$one;
							break;
						}
					}
				}

				$this->_modulesBySection[$section][] = $obj;
				$this->_sectionCount[$section]++;
			}
		}
    }


    /**
     * SetAggregatePermissions
     *
     * This function gathers disparate permissions to come up with the visibility of
     * various admin sections, e.g., if there is any content-related operation for
     * which a user has permissions, the aggregate content permission is granted, so
     * that menu item is visible.
     *
	 * @access private
	 * @ignore
     */
	private function _SetAggregatePermissions($force = FALSE)
	{
		$this->_SetModuleAdminInterfaces();
		if( is_array($this->_perms) && !$force ) return;

		$this->_perms = array();
		$this->_breadcrumbs = array();

        $this->_perms['pagePerms'] = (
									 check_permission($this->userid, 'Modify Any Page') ||
									 check_permission($this->userid, 'Add Pages') ||
									 check_permission($this->userid, 'Remove Pages') ||
									 check_permission($this->userid, 'Manage All Content')
									 );
		if( $this->_perms['pagePerms'] == FALSE ) {
			$thisUserPages = author_pages($this->userid);
			if (count($thisUserPages) > 0) {
				$this->_perms['pagePerms'] = true;
			}
		}
        $this->_perms['contentPerms'] = $this->_perms['pagePerms'] | 
			(isset($this->_sectionCount['content']) && $this->_sectionCount['content'] > 0);

		// layout        
        $this->_perms['templatePerms'] = check_permission($this->userid, 'Modify Templates');
        $this->_perms['cssPerms'] = check_permission($this->userid, 'Modify Stylesheets');
        $this->_perms['layoutPerms'] = $this->_perms['templatePerms'] |
			$this->_perms['cssPerms'] | 
			(isset($this->_sectionCount['layout']) && $this->_sectionCount['layout'] > 0);

		// file / image
        $this->_perms['filePerms'] = check_permission($this->userid, 'Modify Files') |
			(isset($this->_sectionCount['files']) && $this->_sectionCount['files'] > 0);
    
		// user/group
        $this->_perms['userPerms'] = check_permission($this->userid, 'Manage Users');
        $this->_perms['groupPerms'] = check_permission($this->userid, 'Manage Groups');
        $this->_perms['usersGroupsPerms'] = $this->_perms['userPerms'] |
			$this->_perms['groupPerms'] |
			(isset($this->_sectionCount['usersgroups']) &&
			 $this->_sectionCount['usersgroups'] > 0);

		// admin
        $this->_perms['sitePrefPerms'] = check_permission($this->userid, 'Modify Site Preferences') |
            (isset($this->_sectionCount['preferences']) && $this->_sectionCount['preferences'] > 0);
        $this->_perms['adminPerms'] = $this->_perms['sitePrefPerms'] |
            (isset($this->_sectionCount['admin']) && $this->_sectionCount['admin'] > 0);
        $this->_perms['siteAdminPerms'] = $this->_perms['sitePrefPerms'] |
			$this->_perms['adminPerms'] |
			(isset($this->_sectionCount['admin']) &&
			 $this->_sectionCount['admin'] > 0);

		// extensions
        $this->_perms['codeBlockPerms'] = check_permission($this->userid, 'Modify User-defined Tags');
        $this->_perms['modulePerms'] = check_permission($this->userid, 'Modify Modules');
        $this->_perms['eventPerms'] = check_permission($this->userid, 'Modify Events');
		$this->_perms['taghelpPerms'] = check_permission($this->userid, 'View Tag Help');
        $this->_perms['extensionsPerms'] = $this->_perms['codeBlockPerms'] |
            $this->_perms['modulePerms'] |
			$this->_perms['eventPerms'] |
			$this->_perms['taghelpPerms'] |
            (isset($this->_sectionCount['extensions']) && $this->_sectionCount['extensions'] > 0);

		$this->_perms['myaccount'] = check_permission($this->userid,'Manage My Settings') |
			check_permission($this->userid,'Manage My Account');
		$this->_perms['bookmarks'] = check_permission($this->userid,'Manage My Bookmarks');
		$this->_perms['myprefs'] = $this->_perms['myaccount'] | $this->_perms['bookmarks'];
	}


    /**
     * _MenuListSectionModules
     * This method reformats module information for display in menus. When passed the
     * name of the admin section, it returns an array of associations:
     * array['module-name']['url'] is the link to that module, and
     * array['module-name']['description'] is the language-specific short description of
     *   the module.
     *
     * @param section - section to display
	 * @access private
	 * @ignore 
     */
    private function _MenuListSectionModules($section)
    {
		die('this function is dead');
        if (isset($this->_sectionCount[$section]) && $this->_sectionCount[$section] > 0) {
			// Sort modules by name
            $names = array();
            foreach($this->_modulesBySection[$section] as $key => $row) {
            	$names[$key] = $this->_modulesBySection[$section][$key]['name'];
            }
            array_multisort($names, SORT_ASC, $this->_modulesBySection[$section]);
			return $this->_modulesBySection[$section];
		}
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
	 * @access private
     * @ignore 
     */
    private function _populate_admin_navigation($subtitle='')
    {
        if (count($this->_menuItems) > 0) {
			// we have already created the list
			return;
		}

		$config = cmsms()->GetConfig();
		debug_buffer('before populate admin navigation');	
		if( $subtitle ) $this->_subtitle = $subtitle;

		debug_buffer('before menu items');
		$this->_menuItems = array();
		$items =& $this->_menuItems;
		// base main menu ---------------------------------------------------------
		$items['main'] = array('url'=>'index.php','parent'=>-1,
							   'title'=>'CMS','priority'=>1,
							   'description'=>'','show_in_menu'=>true);
		$items['home'] = array('url'=>'index.php','parent'=>'main','priority'=>1,
							   'title'=>$this->_FixSpaces(lang('home')),
							   'description'=>'','show_in_menu'=>true);
		$items['viewsite'] = array('url'=>$config['root_url'].'/index.php','parent'=>'main',
								   'title'=>$this->_FixSpaces(lang('viewsite')),
								   'type'=>'external','priority'=>2,
								   'description'=>'','show_in_menu'=>true, 'target'=>'_blank');
		$items['logout'] = array('url'=>'logout.php','parent'=>'main',
								 'title'=>$this->_FixSpaces(lang('logout')),'priority'=>3,
								 'description'=>'','show_in_menu'=>true);
		// base content menu ---------------------------------------------------------
		$items['content'] = array('url'=>'index.php?section=content','parent'=>-1,
								  'priority'=>2,
								 'title'=>$this->_FixSpaces(lang('content')),
								 'description'=>lang('contentdescription'),
								 'show_in_menu'=>$this->HasPerm('contentPerms'));
		/*
		$items['pages'] = array('url'=>'listcontent.php','parent'=>'content',
								'title'=>$this->_FixSpaces(lang('pages')),
								'description'=>lang('pagesdescription'),
								'show_in_menu'=>$this->HasPerm('pagePerms'));
		$items['editpage'] = array('url'=>'editcontent.php','parent'=>'pages',
								   'title'=>$this->_FixSpaces(lang('editpage')),
								   'description'=>lang('editpage'),'show_in_menu'=>false);
		*/
		$items['images'] = array('url'=>'imagefiles.php','parent'=>'content',
								 'title'=>$this->_FixSpaces(lang('imagemanager')),
								 'description'=>lang('imagemanagerdescription'),
								 'show_in_menu'=>$this->HasPerm('filePerms'));
		// base layout menu ---------------------------------------------------------
		$items['layout'] = array('url'=>'index.php?section=layout','parent'=>-1,'priority'=>3,
								 'title'=>$this->_FixSpaces(lang('layout')),
								 'description'=>lang('layoutdescription'),
								 'show_in_menu'=>$this->HasPerm('layoutPerms'));
		// base user/groups menu ---------------------------------------------------------
		$items['usersgroups'] = array('url'=>'index.php?section=usersgroups','parent'=>-1,
									  'title'=>$this->_FixSpaces(lang('usersgroups')),'priority'=>4,
									  'description'=>lang('usersgroupsdescription'),
									  'show_in_menu'=>$this->HasPerm('usersGroupsPerms'));
		$items['users'] = array('url'=>'listusers.php','parent'=>'usersgroups',
								'title'=>$this->_FixSpaces(lang('users')),
								'description'=>lang('usersdescription'),
								'show_in_menu'=>$this->HasPerm('userPerms'));
		$items['adduser'] = array('url'=>'adduser.php','parent'=>'users',
								  'title'=>$this->_FixSpaces(lang('adduser')),
								  'description'=>lang('adduser'),'show_in_menu'=>false);
		$items['edituser'] = array('url'=>'edituser.php','parent'=>'users',
								   'title'=>$this->_FixSpaces(lang('edituser')),
								   'description'=>lang('edituser'),'show_in_menu'=>false);
		$items['groups'] = array('url'=>'listgroups.php','parent'=>'usersgroups',
								 'title'=>$this->_FixSpaces(lang('groups')),
								 'description'=>lang('groupsdescription'),
								 'show_in_menu'=>$this->HasPerm('groupPerms'));
		$items['addgroup'] = array('url'=>'addgroup.php','parent'=>'groups',
								   'title'=>$this->_FixSpaces(lang('addgroup')),
								   'description'=>lang('addgroup'),'show_in_menu'=>false);
		$items['editgroup'] = array('url'=>'editgroup.php','parent'=>'groups',
									'title'=>$this->_FixSpaces(lang('editgroup')),
									'description'=>lang('editgroup'),'show_in_menu'=>false);
		$items['groupmembers'] = array('url'=>'changegroupassign.php',
									   'parent'=>'usersgroups',
									   'title'=>$this->_FixSpaces(lang('groupassignments')),
									   'description'=>lang('groupassignmentdescription'),
									   'show_in_menu'=>$this->HasPerm('groupPerms'));
		$items['groupperms'] = array('url'=>'changegroupperm.php','parent'=>'usersgroups',
									 'title'=>$this->_FixSpaces(lang('groupperms')),
									 'description'=>lang('grouppermsdescription'),
									 'show_in_menu'=>$this->HasPerm('groupPerms'));
		// base extensions menu ---------------------------------------------------------
		$items['extensions'] = array('url'=>'index.php?section=extensions','parent'=>-1,
									 'title'=>$this->_FixSpaces(lang('extensions')),
									 'description'=>lang('extensionsdescription'),
									 'show_in_menu'=>$this->HasPerm('extensionsPerms'));
		$items['modules'] = array('url'=>'listmodules.php','parent'=>'extensions',
								  'title'=>$this->_FixSpaces(lang('modules')),
								  'description'=>lang('moduledescription'),
								  'show_in_menu'=>$this->HasPerm('modulePerms'));
		$items['tags'] = array('url'=>'listtags.php','parent'=>'extensions',
							   'title'=>$this->_FixSpaces(lang('tags')),
							   'description'=>lang('tagdescription'),
							   'show_in_menu'=>$this->HasPerm('taghelpPerms'));
		$items['usertags'] = array('url'=>'listusertags.php','parent'=>'extensions',
								   'title'=>$this->_FixSpaces(lang('usertags')),
								   'description'=>lang('usertagdescription'),
								   'show_in_menu'=>$this->HasPerm('codeBlockPerms'));
		$items['eventhandlers'] = array('url'=>'eventhandlers.php','parent'=>'extensions',
										'title'=>$this->_FixSpaces(lang('eventhandlers')),
										'description'=>lang('eventhandlerdescription'),
										'show_in_menu'=>$this->HasPerm('eventPerms'));
		$items['editeventhandler'] = array('url'=>'editevent.php','parent'=>'eventhandlers',
										   'title'=>$this->_FixSpaces(lang('editeventhandler')),
										   'description'=>lang('editeventshandler'),
										   'show_in_menu'=>false);
		$items['editusertag'] = array('url'=>'editusertag.php','parent'=>'usertags',
									  'title'=>$this->_FixSpaces(lang('editusertag')),
									  'description'=>lang('editusertag'),'show_in_menu'=>false);
		// base admin menu ---------------------------------------------------------
		$items['siteadmin'] = array('url'=>'index.php?section=siteadmin','parent'=>-1,
									'title'=>$this->_FixSpaces(lang('admin')),'priority'=>5,
									'description'=>lang('admindescription'),
									'show_in_menu'=>$this->HasPerm('siteAdminPerms'));
		$items['siteprefs'] = array('url'=>'siteprefs.php','parent'=>'siteadmin',
									'title'=>$this->_FixSpaces(lang('globalconfig')),
									'description'=>lang('preferencesdescription'),
									'show_in_menu'=>$this->HasPerm('sitePrefPerms'));
		/*
		$items['pagedefaults'] = array('url'=>'pagedefaults.php','parent'=>'siteadmin',
									   'title'=>$this->_FixSpaces(lang('pagedefaults')),
									   'description'=>lang('pagedefaultsdescription'),
									   'show_in_menu'=>$this->HasPerm('sitePrefPerms'));
		*/
		$items['systeminfo'] = array('url' => 'systeminfo.php', 'parent' => 'siteadmin',
									 'title' => $this->_FixSpaces(lang('systeminfo')),
									 'description' => lang('systeminfodescription'),
									 'show_in_menu' => $this->HasPerm('adminPerms'));
		$items['systemmaintenance'] = array('url' => 'systemmaintenance.php', 
											'parent' => 'siteadmin',
											'title' => $this->_FixSpaces(lang('systemmaintenance')),
											'description' => lang('systemmaintenancedescription'),
											'show_in_menu' => $this->HasPerm('adminPerms'));
		$items['checksum'] = array('url' => 'checksum.php', 'parent' => 'siteadmin',
								   'title' => $this->_FixSpaces(lang('system_verification')),
								   'description' => lang('checksumdescription'),
								   'show_in_menu' => $this->HasPerm('adminPerms'));
		$items['adminlog'] = array('url'=>'adminlog.php','parent'=>'siteadmin',
								   'title'=>$this->_FixSpaces(lang('adminlog')),
								   'description'=>lang('adminlogdescription'),
								   'show_in_menu'=>$this->HasPerm('adminPerms'));
		// base my prefs menu ---------------------------------------------------------
		$items['myprefs'] = array('url'=>'index.php?section=myprefs','parent'=>-1,
								  'title'=>$this->_FixSpaces(lang('myprefs')),
								  'description'=>lang('myprefsdescription'),'show_in_menu'=>$this->_perms['myprefs']);
		$items['myaccount'] = array('url'=>'myaccount.php','parent'=>'myprefs',
									'title'=>$this->_FixSpaces(lang('myaccount')),
									'description'=>lang('myaccountdescription'),
									'show_in_menu'=>$this->_perms['myaccount']);
		$items['managebookmarks'] = array('url'=>'listbookmarks.php','parent'=>'myprefs',
										  'title'=>$this->_FixSpaces(lang('managebookmarks')),
										  'description'=>lang('managebookmarksdescription'),
										  'show_in_menu'=>$this->_perms['bookmarks']);
		$items['addbookmark'] = array('url'=>'addbookmark.php','parent'=>'myprefs',
									  'title'=>$this->_FixSpaces(lang('addbookmark')),
									  'description'=>lang('addbookmark'),'show_in_menu'=>false);
		$items['editbookmark'] = array('url'=>'editbookmark.php','parent'=>'myprefs',
									   'title'=>$this->_FixSpaces(lang('editbookmark')),
									   'description'=>lang('editbookmark'),'show_in_menu'=>false);

		debug_buffer('after menu items');

		// slightly cleaner syntax
		$items['ecommerce'] = array('url'=>'index.php?section=ecommerce','parent'=>-1,
									'title'=>$this->_FixSpaces(lang('ecommerce')),
									'description'=>lang('ecommerce_desc'),
									'show_in_menu'=>true);

		// adjust all the urls to include the session key
		// and set an icon if we can. also mark them as system items.
		foreach( $this->_menuItems as $sectionKey => $sectionArray ) {
			if( isset($sectionArray['url']) && 
				(!isset($sectionArray['type']) || $sectionArray['type'] != 'external' )) {
				$url = $this->_menuItems[$sectionKey]['url'];
				if( strpos($url,'?') !== FALSE ) {
					$url .= '&amp;';
				}
				else {
					$url .= '?';
				}
				$before = $url;
				$url .= CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

				$this->_menuItems[$sectionKey]['url'] = $url;
			}
			$this->_menuItems[$sectionKey]['system'] = 1;
		}

		debug_buffer('before system modules');

		// add in all of the 'system' modules too
		$gCms = cmsms();
		foreach ($this->_menuItems as $sectionKey=>$sectionArray) {
			if( !isset($this->_modulesBySection[$sectionKey]) ) continue;
			$tmpArray = $this->_modulesBySection[$sectionKey];

			foreach ($tmpArray as $menuItem) {
				if( !$menuItem->system ) continue;

				// don't clobber existing keys
				$key = $menuItem->module;
				if (array_key_exists($key,$this->_menuItems)) {
				    $counter = 2;
					while (array_key_exists($key,$this->_menuItems)) {
						$key = $menuItem->module.$counter;
						$counter++;
					}
				}

				$this->_menuItems[$key]=array('url'=>$menuItem->url,
											  'parent'=>$sectionKey,
											  'title'=>$this->_FixSpaces($menuItem->title),
											  'description'=>$menuItem->description,
											  'show_in_menu'=>true,
											  'system'=>1,
											  'module'=>$menuItem->module);
			}
		}

		debug_buffer('before non system module menu items');

		// add in all of the non system modules
        foreach ($this->_menuItems as $sectionKey=>$sectionArray) {
			if( !isset($this->_modulesBySection[$sectionKey]) ) continue;
			$tmpArray = $this->_modulesBySection[$sectionKey];

			foreach ($tmpArray as $menuItem) {
				if( $menuItem->system ) continue;

				// don't clobber existing keys
				$key = $menuItem->module;
				if (array_key_exists($key,$this->_menuItems)) {
				    $counter = 2;
					while (array_key_exists($key,$this->_menuItems)) {
						$key = $menuItem->module.$counter;
						$counter++;
					}
				}

				$this->_menuItems[$key]=array('url'=>$menuItem->url,
											  'parent'=>$sectionKey,
											  'title'=>$this->_FixSpaces($menuItem->title),
											  'description'=>$menuItem->description,
											  'show_in_menu'=>true,
											  'module'=>$menuItem->module);
			}
		}
	
		debug_buffer('after non system module menu items');

		// remove any top level items that don't have children
		$parents = array();
		foreach ($this->_menuItems as $sectionKey=>$sectionArray) {
			if( $this->_menuItems[$sectionKey]['parent'] == -1 ) {
				$parents[] = $sectionKey;
			}
		}
		foreach( $parents as $oneparent ) {
			$found = 0;
			foreach ($this->_menuItems as $sectionKey=>$sectionArray) {
				if( $sectionArray['parent'] == $oneparent ) {
					$found = 1;
					break;
				}
			}
			if( !$found ) unset($this->_menuItems[$oneparent]);
		}

		// fix up all of the menu items to have the correct user key.
		
		// sort the menu items by system, priority, and name (case insensitive)
		$fn = function($a,$b) {
			$sa = isset($a['system'])?$a['system']:0;
			$sb = isset($b['system'])?$b['system']:0;
			if( $sa && !$sb ) return -1;
			if( $sb && !$sa ) return 1;

			$pa = isset($a['priority'])?$a['priority']:999;
			$pb = isset($b['priority'])?$b['priority']:999;
			if( $pa < $pb ) return -1;
			if( $pa > $pb ) return 1;

			return strcasecmp($a['title'],$b['title']);
		};
		uasort($this->_menuItems,$fn);

		// set everthing to be not selected.
		foreach ($this->_menuItems as $sectionKey=>$sectionArray) {
			$this->_menuItems[$sectionKey]['selected'] = FALSE;
			$this->_menuItems[$sectionKey]['children'] = array();
		}

		// resolve the tree to be doubly-linked,
		// and make sure the selections are selected            
		foreach ($this->_menuItems as $sectionKey=>$sectionArray) {
			// link the children to the parents; a little clumsy since we can't
			// assume php5-style references in a foreach.
			foreach ($this->_menuItems as $subsectionKey=>$subsectionArray) {
				if ($subsectionArray['parent'] == $sectionKey) {
					$this->_menuItems[$sectionKey]['children'][] = $subsectionKey;
				}
			}

			// set selected
			if( strstr($_SERVER['REQUEST_URI'],'moduleinterface.php') !== FALSE && 
				isset($_REQUEST['mact']) &&
				isset($sectionArray['module']) && $sectionArray['module'] ) {

				$u1 = new cms_url($sectionArray['url']);
				$v1 = array();
				parse_str($u1->get_query(),$v1);
				$u2 = new cms_url($_SERVER['REQUEST_URI']);
				$v2 = array();
				parse_str($u2->get_query(),$v2);
				if( $u1->get_path() == $u2->get_path() && 
					isset($v1['mact']) && isset($v2['mact']) && 
					$v1['mact'] == $v2['mact'] ) {
//				$tmp = explode(',',$_REQUEST['mact']);
// 				debug_display($sectionArray['url'],$_SERVER['REQUEST_URI']);
//				if( startswith($sectionArray['url'],$_SERVER['REQUEST_URI']) ) {
// 				if( strstr($_SERVER['REQUEST_URI'],$sectionArray['url']) !== FALSE ) {
					$this->_menuItems[$sectionKey]['selected'] = TRUE;
					$this->_active_item = $sectionKey;
					$this->_breadcrumbs[] = array('title'=>$this->_menuItems[$sectionKey]['title'], 
												  'url'=>$this->_menuItems[$sectionKey]['url']);
					if ($sectionArray['parent'] != -1) {
						$parent = $sectionArray['parent'];
						while ($parent != -1) {
							$this->_menuItems[$parent]['selected'] = TRUE;
							$this->_breadcrumbs[] = array('title'=>$this->_menuItems[$parent]['title'], 
														  'url'=>$this->_menuItems[$parent]['url']);
							$parent = $this->_menuItems[$parent]['parent'];
						}
					}
				}
// 				else if( $tmp[0] == $sectionArray['module'] && !$this->_active_item ) {
// 					// this will ensure we get to the right module, but not necessarily the right parent action.
// 					$this->_active_item = $sectionKey;
// 				}
			}
			else if (strstr($_SERVER['REQUEST_URI'],$sectionArray['url']) !== FALSE &&
					 (!isset($sectionArray['type']) || $sectionArray['type'] != 'external')) {
				$this->_menuItems[$sectionKey]['selected'] = TRUE;
				$this->_title .= $sectionArray['title'];
				$this->_active_item = $sectionKey;
				$this->_breadcrumbs[] = array('title'=>$this->_menuItems[$sectionKey]['title'], 
											  'url'=>$this->_menuItems[$sectionKey]['url']);
				if ($sectionArray['parent'] != -1) {
					$parent = $sectionArray['parent'];
					while ($parent != -1) {
						$this->_menuItems[$parent]['selected'] = TRUE;
						$this->_breadcrumbs[] = array('title'=>$this->_menuItems[$parent]['title'], 
													  'url'=>$this->_menuItems[$parent]['url']);
						$parent = $this->_menuItems[$parent]['parent'];
					}
				}
			}
		}
		$this->_breadcrumbs = array_reverse($this->_breadcrumbs);

		// fix subtitle, if any
		if ($subtitle != '') $this->_title .= ': '.$subtitle;
		

		/*
		$count = 0;
		if ($count > 0) {
			// and fix up the last breadcrumb...
			if ($this->_query != '' && 
				strpos($this->_breadcrumbs[$count-1]['url'],'&amp;') === false) {
				$this->_query = preg_replace('/\&/','&amp;',$this->_query);
				$pos = strpos($this->_breadcrumbs[$count-1]['url'],'?');
				$tmp = substr($this->_breadcrumbs[$count-1]['url'],0,$pos).'?'.$this->_query;
				$this->_breadcrumbs[$count-1]['url'] = $tmp;
			}
			unset($this->_breadcrumbs[$count-1]['url']);
			if ($this->_subtitle != '') {
				$this->_breadcrumbs[$count-1]['title'] .=  ': '.$this->_subtitle;
			}
		}
		*/
		debug_buffer('after populate admin navigation');
    }
    

	/**
	 * Set the page title.
	 * This is used in the admin to set the title for the page, and for the visible page header.
	 * Note: if no title is specified, the theme will try to calculate one automatically.
	 *
	 * @since 2.0
	 * @param string The page title.
	 * @return void
	 */
	public function SetTitle($str)
	{
		if( $str == '' ) $str = null;
		$this->_title = $str;
	}


	/**
	 * Set the page subtitle.
	 * This is used in the admin to set the title for the page, and for the visible page header.
	 * Note: if no title is specified, the theme will try to calculate one automatically.
	 *
	 * @since 2.0
	 * @param string The page subtitle.
	 * @return void
	 */
	public function SetSubTitle($str)
	{
		if( $str == '' ) $str = null;
		$this->_subtitle = $str;
	}


    /**
     * HasPerm
     *
     * Check if the user has one of the aggregate permissions
     * 
	 * @access private
     * @param permission the permission to check.
	 * @return boolean
     */
    protected function HasPerm($permission)
    {
		$this->_SetAggregatePermissions();

    	if (isset($this->_perms[$permission]) && $this->_perms[$permission]) {
    	   	return true;
		}
		return false;
    }

	/**
	 * A function to return the admin navigation
	 * This function returns a doubly linked list of arrays representing the admin navigation
	 *
	 * @deprecated
	 * @access protected
	 * @return array Doubly linked list of menu nodes.  The parent, and children members of each node represent the links.  a parent value of -1 represents a top level node.
	 */
	protected function get_admin_navigation()
	{
		$this->_populate_admin_navigation();
		return $this->_menuItems;
	}

	
	/**
	 * Return the menu items as a nested tree using recursion.
	 *
	 * @ignore
	 */
	private function _get_navigation_tree_sub($parent = -1,$maxdepth = -1, $depth = 0)
	{
		$result = array();
		$flatitems = $this->get_admin_navigation();
		foreach( $flatitems as $key => $one ) {
			if( !$one['show_in_menu'] ) continue;
			if( (!isset($one['parent']) && $parent == -1) || 
				(isset($one['parent']) && $one['parent'] == $parent) ) {
				if( isset($one['children']) ) {
					unset($one['children']);
				}

				if( $maxdepth < 0 || $depth + 1 < $maxdepth ) {
					$children = $this->_get_navigation_tree_sub($key,$maxdepth,$depth+1);
					if( is_array($children) && count($children) ) {
						$one['children'] = $children;
					}
				}
				$one['name'] = $key;
				$result[] = $one;
			}
		}
		return $result;
	}


	/**
	 * Retrieve the admin navigation tree
	 *
	 * @since 1.11
	 * @param string Indicates the parent to start at.  use a value of -1 to indicate the top node.
	 * @param int    The maximum depth of the tree.  -1 indicates no maximum depth
	 * @param bool   Indicates wether the cache should be used.  This should be FALSE when not retrieving the whole tree.
	 * @return array A nested array of menu nodes.  The children member represents the nesting.
	 */
	public function get_navigation_tree($parent = -1,$maxdepth = -1,$usecache = TRUE)
	{
		if( is_array($this->_nav_tree) && $usecache) return $this->_nav_tree;
		
		$nodes = $this->_get_navigation_tree_sub($parent,$maxdepth);
		if( $usecache ) $this->_nav_tree = $nodes;
		
		return $nodes;
	}

	/**
	 * A functon to return the name (key) of a menu item given it's title
	 * returns the first match.
	 *
	 * @access protected
	 * @param string The title to search for
	 * @return string The matching key, or null
	 */
	protected function find_menuitem_by_title($title)
	{
		$nav = $this->get_admin_navigation();
		foreach( $nav as $key => $rec ) {
			if( isset($rec['title']) && $rec['title'] == $title ) {
				return $key;
			}
		}
	}


	/**
	 * Return the list of bookmarks
	 *
	 * @param bool if False the shortcuts for adding and managing bookmarks are added to the list.
	 * @return array of Bookmark objects
	 */
	public function get_bookmarks($pure = FALSE)
	{
		$bookops = cmsms()->GetBookmarkOperations();
		$marks = array_reverse($bookops->LoadBookmarks($this->userid));

		if( !$pure ) {
			$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
			$mark= new Bookmark();
			$mark->title = lang('addbookmark');
			$mark->url = 'makebookmark.php'.$urlext.'&amp;title='.urlencode($this->_title);
			$marks[] = $mark;

			$mark = new Bookmark();
			$mark->title = lang('managebookmarks');
			$mark->url = 'listbookmarks.php'.$urlext;
			$marks[] = $mark;
		}
		return $marks;
	}

	/**
	 * Return list of breadcrumbs
	 *
	 * @return array of menu nodes representing the breadcrumb trail.
	 */
	public function get_breadcrumbs()
	{
		$this->_populate_admin_navigation();
		return $this->_breadcrumbs;
	}
	

	/**
	 * Return the title of the active item.
	 *
	 * @return string
	 */
	public function get_active_title()
	{
		$this->_populate_admin_navigation();
		if( $this->_active_item ) return $this->_menuItems[$this->_active_item]['title'];
	}

	/**
	 * Attach some data to the admin theme.
	 *
	 * @param string key
	 * @param mixed value
	 * @returns void
	 */
	public function set_value($key,$value)
	{
		if( is_null($value) && is_array($this->_data) && isset($this->_data[$key]) ) {
			unset($this->_data[$key]);
			return;
		}
		if( $value ) {
			if( !is_array($this->_data) ) $this->_data = array();
			$this->_data[$key] = $value;
		}
	}


	/**
	 * Return attached data
	 *
	 * @param string key
	 * @param mixed value
	 * @returns void
	 */
	public function get_value($key)
	{
		if( is_array($this->_data) && isset($this->_data[$key]) ) {
			return $this->_data[$key];
		}
	}


	/**
	 * HasDisplayableChildren
	 * This method returns a boolean, based upon whether the section in question
	 * has displayable children.
	 *
	 * @deprecated
	 * @param section - section to test
	 * @return boolean
	 */
	public function HasDisplayableChildren($section)
	{
		$displayableChildren=false;
		foreach($this->_menuItems[$section]['children'] as $thisChild) {
			$thisItem = $this->_menuItems[$thisChild];
			if ($thisItem['show_in_menu']) {
				$displayableChildren = true;
				break;
			}
		}
		return $displayableChildren;
	}


	/**
	 * DisplayImage will display the themed version of an image (if it exists),
	 * @param imageName - name of image
	 * @param alt - alt text
	 * @param width
	 * @param height
	 * @param class
	 */
	public function DisplayImage($imageName, $alt='', $width='', $height='', $class='')
	{
		// @todo: fix me...
		if( !is_array($this->_imageLink) ) $this->_imageLink = array();
		if (! isset($this->_imageLink[$imageName])) {
			if (strpos($imageName,'/') !== false) {
				$imagePath = substr($imageName,0,strrpos($imageName,'/')+1);
				$imageName = substr($imageName,strrpos($imageName,'/')+1);
			}
			else {
				$imagePath = '';
			}
    	   	
			$config = cmsms()->GetConfig();
			$str = dirname($config['root_path'].'/'.$config['admin_dir']."/themes/{$this->themeName}/images/{$imagePath}{$imageName}");
			if (file_exists("{$str}/{$imageName}")) {
				$str = "themes/{$this->themeName}/images/{$imagePath}{$imageName}";
				$this->_imageLink[$imageName] = $str;
			}
			else {
				// todo: uses default theme.
				$this->_imageLink[$imageName] = 'themes/default/images/' . $imagePath . $imageName;
			}
		}

		$retStr = '<img src="'.$this->_imageLink[$imageName].'"';
		if ($class != '') {
			$retStr .= ' class="'.$class.'"';
		}
		if ($width != '') {
			$retStr .= ' width="'.$width.'"';
		}
		if ($height != '') {
			$retStr .= ' height="'.$height.'"';
		}
		if ($alt != '') {
			$retStr .= ' alt="'.$alt.'" title="'.$alt.'"';
		}
		$retStr .= ' />';
		return $retStr;
	}


	/**
	 * Abstrct function for showing errors in the admin theme.
	 *
	 * @abstract
	 * @param mixed The errors, either a string, or an array of stri9ngs
	 * @param string An optional get variable name that can contain an error string key.  If specified, errors is ignored.
	 */
	abstract public function ShowErrors($errors,$get_var='');

	/**
	 * Abstrct function for showing messages in the admin theme.
	 *
	 * @abstract
	 * @param mixed The message, either a string, or an array of stri9ngs
	 * @param string An optional get variable name that can contain an error string key.  If specified, message param is ignored.
	 */
	abstract public function ShowMessage($message,$get_var='');

	/**
	 * Abstract method for showing a header in the content area of a theme
	 * This is usually an advanced function with some special behaviour based on the module_help_type
	 *
	 * @abstract
	 * @deprecated
	 * @param string The name to show on the header.  This will not be passed through the lang process if module_help_type is not FALSE.
	 * @param array  Extra language parameters to pass to the title_name.  Ignored if module_help_type is not FALSE
	 * @param string Text to show in the module help link (depends on the module_help_type param)
	 * @param mixed  Flag for how to display module help types.   Possible values are TRUE to display a simple link, FALSE for no help, and 'both' for both types of links
	 */
	abstract public function ShowHeader($title_name,$extra_lang_params = array(),$link_text = '',$module_help_type = FALSE);


	/**
	 * A function to return the name of the default admin theme.
	 *
	 * @returns string
	 */
	static public function GetDefaultTheme()
	{
		$tmp = self::GetAvailableThemes();
		if( is_array($tmp) && count($tmp) ) {
			$tmp = array_keys($tmp);
			$logintheme = get_site_preference('logintheme');
			if( $logintheme && in_array($logintheme,$tmp) )
				return $logintheme;
			return $tmp[0];
		}
	}

	
	/**
	 * Retrieve a list of the available admin themes.
	 *
	 * @return array a hash of strings.
	 */
	static public function GetAvailableThemes()
	{
		$config = cmsms()->GetConfig();
			
		$files = glob(cms_join_path($config['admin_path'],'themes').'/*');
		if( is_array($files) && count($files) ) {
			$res = array();
			foreach( $files as $file ) {
				if( !is_dir($file) ) continue;
				$name = basename($file);
				if( !is_readable(cms_join_path($file,"{$name}Theme.php")) ) continue;
				$res[$name] = $name;
			}
			return $res;
		}
	}


	/**
	 * A function to retrieve the global admin theme object.
	 * This method will create the admin theme object if has not yet been created.  It will read the cms preferences and cross reference with available themes.
	 *
	 * @param  String optional theme name.
	 * @return object Reference to the initialized admin theme.
	 */
	static public function &GetThemeObject($name = '')
	{
		if( is_object(self::$_instance) ) return self::$_instance;
		
		if( !$name ) $name = get_preference(get_userid(FALSE),'admintheme',self::GetDefaultTheme());
		if( class_exists($name) ) {
			self::$_instance = new $name;
		}
		else {
			$gCms = cmsms();
			$config = $gCms->GetConfig();
			$themeObjName = $name."Theme";
			$fn = $config['admin_path']."/themes/$name/{$themeObjName}.php";
			if( file_exists($fn) ) {
				include_once($fn);
				self::$_instance = new $themeObjName($gCms,get_userid(FALSE),$name);
			}
			else {
				// theme not found... use default
				$name = self::GetDefaultTheme();
				$themeObjName = $name."Theme";
				$fn = $config['admin_path']."/themes/$name/{$themeObjName}.php";
				if( file_exists($fn) ) {
					include_once($fn);
					self::$_instance = new $themeObjName($gCms,get_userid(FALSE),$name);
				}
				else {
					// still not found
					$res = null;
					return $res;
				}
			}
		}
		return self::$_instance;
	}


	/**
	 * A public function to add a notification for display in the theme.
	 *
	 * @param CmsAdminThemeNotification A reference to the new notification
	 */
	public function add_notification(CmsAdminThemeNotification& $notification)
	{
		if( !is_array($this->_notifications) ) {
			$this->_notifications = array();
		}
		$this->_notifications[] = $notification;
	}

	/**
	 * A public function to add a notification for display in the theme.
	 * This is simply a compatibility wrapper around the add_notification method.
	 *
	 * @deprecated
	 * @param int Priority level between 1 and 3
	 * @param string The module name.
	 * @param html The contents of the notification
	 */
	public function AddNotification($priority,$module,$html)
	{
	  $notification = new CmsAdminThemeNotification;
	  $notification->priority = max(1,min(3,$priority));
	  $notification->module = $module;
	  $notification->html = $html;
	  $this->add_notification($notification);
	}


	/**
	 * Retrieve the current list of notifications.
	 *
	 * @return Array of CmsAdminThemeNotification objects
	 */
	public function get_notifications()
	{
		return $this->_notifications;
	}


	/**
	 * Returns a select list of the pages in the system for use in
	 * various admin pages.
	 *
	 * @param string $name - The html name of the select box
	 * @param string $selected - If a matching id is found in the list, that item
	 *                           is marked as selected.
	 * @return string The select list of pages
	 */
	public function GetAdminPageDropdown($name,$selected,$id = '')
	{
		$opts = array();
		$opts[ucfirst(lang('none'))] = '';

		$depth = 0;
		$menuItems = $this->get_admin_navigation();
		foreach( $menuItems as $sectionKey=>$menuItem ) {
			if( $menuItem['parent'] != -1 ) {
				continue;
			}
			if( !$menuItem['show_in_menu'] || strlen($menuItem['url']) < 1 ) {
				continue;
			}
	     
			$opts[$menuItem['title']] = $menuItem['url'];

			if( is_array($menuItem['children']) && 
				count($menuItem['children']) ) {
				foreach( $menuItem['children'] as $thisChild ) {
					if( $thisChild == 'home' || $thisChild == 'logout' ||
						$thisChild == 'viewsite') {
						continue;
					}

					$menuChild = $menuItems[$thisChild];
					if( !$menuChild['show_in_menu'] || strlen($menuChild['url']) < 1 ) {
						continue;
					}

					//$opts['&nbsp;&nbsp;'.$menuChild['title']] = cms_htmlentities($menuChild['url']);
					$opts['&nbsp;&nbsp;'.$menuChild['title']] = $menuChild['url'];
				}
			}
		}

		$atext = '';
		if( $id != '' ) {
			$atext = ' id="'.trim($id).'"';
		}
		$output = '<select'.$atext.' name="'.$name.'">'."\n";
		foreach( $opts as $key => $value ) {
			if( $value == $selected ) {
				$output .= sprintf("<option selected=\"selected\" value=\"%s\">%s</option>\n",
								   $value,$key);
			}
			else {
				$output .= sprintf("<option value=\"%s\">%s</option>\n",
								   $value,$key);
			}
		}
		$output .= '</select>'."\n";
		return $output;
	}


	/**
	 *  BackUrl
	 *  "Back" Url - link to the next-to-last item in the breadcrumbs
	 *  for the back button.
	 */
	public function BackUrl()
	{
		$count = count($this->_breadcrumbs) - 2;
		$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
		if ($count > -1) {
			$txt = $this->_breadcrumbs[$count]['url'];
			return $txt;
		}
		else {
			// rely on base href to redirect back to the
			// admin home page
			return 'index.php'.$urlext;
		}
	}

	/**
	 * An abstract function to output the header html
	 * This function may not display anything, but may store data for use in the postprocess mechanism
	 * it should store, or output all of the information required for the head section of the page,
	 * and all admin navigation etc.  Many admin themes may not do anything here.
	 *
	 * @return string html contents.
	 */
	abstract public function do_header();

	/**
	 * An abstract function to output the themes footer html
	 * This function may not display anything, but may store data for use in the postprocess mechanism
	 * it should store, or output all of the information required for the head section of the page,
	 * and all admin navigation etc.  Many admin themes may not do anything here.
	 *
	 * @return string html contents.
	 */
	abstract public function do_footer();

	/**
	 * An abstract function to output the content for a top level navigation page
	 * This method is called when the user has browsed to the root of the admin site, or to any top level navigation item
	 *
	 * @param string The section name.  An empty string indicates that a navigation of all top level items should be created.
	 * @return string html contents.
	 */
	abstract public function do_toppage($section_name);

	/**
	 * An abstract function for processing the login form.
	 *
	 * @return string html contents for the login page.
	 */

	abstract public function do_login($params);
	/**
	 * An abstract function for processing the content area of the page.
	 * Many admin themes will do most of their work in this method (passing the html contents through a smarty template etc).
	 *
	 * @param string html contents
	 * @return string the HTML contents of the entire page.
	 */
	abstract public function postprocess($html);

	/**
	 * ------------------------------------------------------------------
	 * Tab Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Output a string suitable for staring tab headers
	 * i.e:  echo $this->StartTabHeaders();
	 *
	 * @final
	 * @return string
	 */ 
	public final function StartTabHeaders()
	{
		return '<div id="page_tabs">';
	}

	/**
	 * Set a specific tab header.
	 * i.e:  echo $this->SetTabHeader('preferences',$this->Lang('preferences'));
	 *
	 * @final
	 * @param string The tab id
	 * @param string The tab title
	 * @param boolean A flag indicating wether this tab is active.
	 * @return string
	 */ 
	public final function SetTabHeader($tabid,$title,$active=false)
	{
		$a="";
		if (TRUE == $active) {
			$a=" class='active'";
			$this->_activetab = $tabid;
		}
		
		$tabid = strtolower(str_replace(' ','_',$tabid));
		return '<div id="'.$tabid.'"'.$a.'>'.$title.'</div>';
	}

	/**
	 * Output a string to stop the output of headers and close the necessary XHTML div.
	 *
	 * @final
	 * @return string
	 */
	public final function EndTabHeaders()
	{
		return "</div><!-- EndTabHeaders -->";
	}

	/**
	 * Output a string to indicate the start of XHTML areas for tabs.
	 *
	 * @final
	 * @return string
	 */
	public final function StartTabContent()
	{
		return '<div class="clearb"></div><div id="page_content">';
	}

	/**
	 * Output a string to indicate the end of XHTML areas for tabs.
	 *
	 * @final
	 * @return string
	 */
	public final function EndTabContent()
	{
		return '</div> <!-- EndTabContent -->';
	}

	/**
	 * Output a string to indicate the start of the output for a specific tab
	 *
	 * @final
	 * @param string tabid (see SetTabHeader)
	 * @param arrray Parameters
	 * @return string
	 */
	public final function StartTab($tabid, $params = array())
	{
		if (FALSE == empty($this->_activetab) && $tabid == $this->_activetab && FALSE == empty($params['tab_message'])) {
			$message = $this->ShowMessage($this->Lang($params['tab_message']));
		} else {
			$message = '';
		}
		
		return '<div id="' . strtolower(str_replace(' ', '_', $tabid)) . '_c">'.$message;
	}

	/**
	 * Output a string to indicate the end of the output for a specific tab.
	 *
	 * @final
	 * @return string
	 */
	public final function EndTab()
	{
		return '</div> <!-- EndTab -->';
	}
} // end of class


/**
 * A class representing a simple notification.
 *
 * @package CMS
 * @version $Revision$
 * @license GPL
 * @since   1.11
 * @author  Robert Campbell
 */
class CmsAdminThemeNotification
{
	private $_module;
	private $_priority;
	private $_html;

	public function __get($key)
	{
		switch( $key ) {
		case 'module':
		case 'priority':
		case 'html':
			return $this->$key;
		}

		throw new Exception('Attempt to retrieve invalid property from CmsAdminThemeNotification');
	}

	public function __set($key,$value)
	{
		switch( $key ) {
		case 'module':
		case 'priority':
		case 'html':
			$this->$key = $value;
			return;
		}

		throw new Exception('Attempt to set invalid property from CmsAdminThemeNotification');
	}
} // end of class

#
# EOF
#
# vim:ts=4 sw=4 noet
?>