<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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
 * Classes and utilities for the base CMS Admin theme
 * @package CMS
 * @license GPL
 */


/**
 * Base class for CMSMS Admin themes.
 * This is an abstract class that is used for building CMSMS Admin Themes.
 * This is also a singleton object.
 *
 * @package CMS
 * @license GPL
 * @since   1.11
 * @author  Robert Campbell
 * @property-read string $themeName Return the theme name
 * @property-read int $userid Return the current logged in userid (deprecated)
 * @property-read string $title The current page title
 * @property-read string $subtitle The current page subtitle
 */
abstract class CmsAdminThemeBase
{
	/**
	 * @ignore
	 */
	private static $_instance;

	/**
	 * @ignore
	 */
	private $_perms;

	/**
	 * @ignore
	 */
	private $_menuItems;

	/**
	 * @ignore
	 */
	private $_notifications;

	/**
	 * @ignore
	 */
	private $_breadcrumbs;

	/**
	 * @ignore
	 */
	private $_errors;

	/**
	 * @ignore
	 */
	private $_messages;

	/**
	 * @ignore
	 */
	private $_imageLink;

	/**
	 * @ignore
	 */
	private $_script;

	/**
	 * @ignore
	 */
	private $_url;

	/**
	 * @ignore
	 */
	private $_query;

	/**
	 * @ignore
	 */
	private $_data;

	/**
	 * @ignore
	 */
	private $_action_module;

	// meta information

	/**
	 * @ignore
	 */
	private $_sectionCount;

	/**
	 * @ignore
	 */
	private $_modulesBySection;

	/**
	 * @ignore
	 */
	private $_active_item;

	/**
	 * @ignore
	 */
	private $_activetab;

	/**
	 * @ignore
	 */
	private $_title;

	/**
	 * @ignore
	 */
	private $_subtitle;

    /**
     * @ignore
     */
    private $_headtext;

	/**
	 * @ignore
	 */
	private $_valid_sections = array('main','content','layout','files','usersgroups','extensions','siteadmin','ecommerce','myprefs');

	/**
	 * @ignore
	 */
	protected function __construct()
	{
		if( is_object(self::$_instance) ) throw new CmsLogicExceptin('Only one instance of a theme object is permitted');

		$this->_url = $_SERVER['SCRIPT_NAME'];
		$this->_query = (isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'');
		if( $this->_query == '' && isset($_POST['mact']) ) {
			$tmp = explode(',',$_POST['mact']);
			$this->_query = 'module='.$tmp[0];
		}
		if ($this->_query == '' && isset($_POST['module']) && $_POST['module'] != '') $this->_query = 'module='.$_POST['module'];
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
	 * @ignore
	 */
	public function __get($key)
	{
		if( $key == 'themeName' ) {
			$class = get_class($this);
			if( endswith($class,'Theme') ) $class = substr($class,0,strlen($class)-5);
			return $class;
		}
		if( $key == 'userid' ) return get_userid();
		if( $key == 'title' ) return $this->_title;
		if( $key == 'subtitle' ) return $this->_subtitle;
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

	/**
	 * @ignore
	 */
	private function _fix_url_userkey($url)
	{
		$newurl = $url;
		if( strpos($url,CMS_SECURE_PARAM_NAME) !== FALSE ) {
			$from = '/'.CMS_SECURE_PARAM_NAME.'=[a-zA-Z0-9]{16,19}/i';
			$to = CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
			$newurl = preg_replace($from,$to,$url);
		}
		elseif( startswith($url,CMS_ROOT_URL) || !startswith($url,'http') ) {
			$prefix = '?';
			if( strpos($url,'?') !== FALSE ) $prefix = '&amp;';
			$newurl .= $prefix.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
		}
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
			$data = @unserialize($data);
		}
        if( !$data ) {
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
							if( ModuleOperations::Get_instance()->IsSystemModule($object->GetName()) ) {
								$one->system = TRUE;
							}
							else {
								$one->system = FALSE;
							}
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
                if( !in_array($section,$this->_valid_sections) ) $section = 'extensions';
				if (! isset($this->_sectionCount[$section])) $this->_sectionCount[$section] = 0;

				// fix up the session key stuff.
				$obj->url = $this->_fix_url_userkey($obj->url);

				// find an icon for this thing.
				if( $obj->icon == '' ) {
					$tmp = array("modules/{$key}/images/icon.gif",
								 "modules/{$key}/icons/icons.gif",
								 "modules/{$key}/images/icon.png",
								 "modules/{$key}/icons/icons.png");
					foreach( $tmp as $one ) {
						$fn = cms_join_path(CMS_ROOT_PATH,$one);
						if( is_file($fn) ) {
							$obj->icon = CMS_ROOT_URL.'/'.$one;
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
		if( is_array($this->_perms) && !$force ) return;

		$this->_perms = array();
		$this->_breadcrumbs = array();

		// content section.
		$this->_perms['contentPerms'] = (isset($this->_sectionCount['content']) && $this->_sectionCount['content'] > 0);

		// layout
        $this->_perms['layoutPerms'] = (isset($this->_sectionCount['layout']) && $this->_sectionCount['layout'] > 0);

		// file
        $this->_perms['filePerms'] = (isset($this->_sectionCount['files']) && $this->_sectionCount['files'] > 0);

		// user/group
        $this->_perms['userPerms'] = check_permission($this->userid, 'Manage Users');
        $this->_perms['groupPerms'] = check_permission($this->userid, 'Manage Groups');
        $this->_perms['usersGroupsPerms'] = $this->_perms['userPerms'] |
			$this->_perms['groupPerms'] | (isset($this->_sectionCount['usersgroups']) && $this->_sectionCount['usersgroups'] > 0);

		// admin
        $this->_perms['sitePrefPerms'] = check_permission($this->userid, 'Modify Site Preferences') |
            (isset($this->_sectionCount['preferences']) && $this->_sectionCount['preferences'] > 0);
        $this->_perms['adminPerms'] = $this->_perms['sitePrefPerms'] |
            (isset($this->_sectionCount['siteadmin']) && $this->_sectionCount['siteadmin'] > 0);
        $this->_perms['siteAdminPerms'] = $this->_perms['sitePrefPerms'] |
			$this->_perms['adminPerms'] | (isset($this->_sectionCount['siteadmin']) && $this->_sectionCount['siteadmin'] > 0);

		// extensions
        $this->_perms['codeBlockPerms'] = check_permission($this->userid, 'Modify User-defined Tags');
        $this->_perms['modulePerms'] = check_permission($this->userid, 'Modify Modules');
        $this->_perms['eventPerms'] = check_permission($this->userid, 'Modify Events');
		$this->_perms['taghelpPerms'] = check_permission($this->userid, 'View Tag Help');
        $this->_perms['extensionsPerms'] = $this->_perms['codeBlockPerms'] |
            $this->_perms['modulePerms'] | $this->_perms['eventPerms'] | $this->_perms['taghelpPerms'] |
            (isset($this->_sectionCount['extensions']) && $this->_sectionCount['extensions'] > 0);

		$this->_perms['myaccount'] = check_permission($this->userid,'Manage My Settings') |
			check_permission($this->userid,'Manage My Account');
		$this->_perms['bookmarks'] = check_permission($this->userid,'Manage My Bookmarks');
		$this->_perms['myprefs'] = $this->_perms['myaccount'] | $this->_perms['bookmarks'];
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
        if( $this->_menuItems && !empty($this->_menuItems) ) return;
		// note: it would be interesting if we could cache these menuItems in the session
		// then clear this data when the cache is cleared (for when modules become available)

		debug_buffer('before populate admin navigation');
		if( $subtitle ) $this->_subtitle = $subtitle;

		$this->_SetModuleAdminInterfaces();

		debug_buffer('before menu items');
		$this->_menuItems = array();
		$items =& $this->_menuItems;
		// base main menu ---------------------------------------------------------
		$items['main'] = array('url'=>'index.php','parent'=>-1,'title'=>'CMS','priority'=>1,'description'=>'','show_in_menu'=>true);
		$items['home'] = array('url'=>'index.php','parent'=>'main','priority'=>1,'title'=>$this->_FixSpaces(lang('home')),
							   'description'=>'','show_in_menu'=>true);
		$items['viewsite'] = array('url'=>CMS_ROOT_URL.'/index.php','parent'=>'main',
								   'title'=>$this->_FixSpaces(lang('viewsite')),'type'=>'external','priority'=>2,
								   'description'=>'','show_in_menu'=>true, 'target'=>'_blank');
		$items['logout'] = array('url'=>'logout.php','parent'=>'main','title'=>$this->_FixSpaces(lang('logout')),'priority'=>3,
								 'description'=>'','show_in_menu'=>true);
		// base content menu ---------------------------------------------------------
		$items['content'] = array('url'=>'index.php?section=content','parent'=>-1,'priority'=>2,
								 'title'=>$this->_FixSpaces(lang('content')),'description'=>lang('contentdescription'),
								  'show_in_menu'=>$this->HasPerm('contentPerms'));

		// base layout menu ---------------------------------------------------------
		$items['layout'] = array('url'=>'index.php?section=layout','parent'=>-1,'priority'=>3,
								 'title'=>$this->_FixSpaces(lang('layout')),'description'=>lang('layoutdescription'),
								 'show_in_menu'=>$this->HasPerm('layoutPerms'));

		// base filest menu --------------------------------------------------------------
		$items['files'] = array('url'=>'index.php?section=files','parent'=>-1,'priority'=>4,
							     'title'=>$this->_FixSpaces(lang('files')),'description'=>lang('filesdescription'),
								 'show_in_menu'=>$this->HasPerm('filePerms'));

		// base user/groups menu ---------------------------------------------------------
		$items['usersgroups'] = array('url'=>'index.php?section=usersgroups','parent'=>-1,
									  'title'=>$this->_FixSpaces(lang('usersgroups')),'priority'=>5,
									  'description'=>lang('usersgroupsdescription'),'show_in_menu'=>$this->HasPerm('usersGroupsPerms'));
		$items['users'] = array('url'=>'listusers.php','parent'=>'usersgroups',
								'title'=>$this->_FixSpaces(lang('users')),'description'=>lang('usersdescription'),
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
		$items['extensions'] = array('url'=>'index.php?section=extensions','parent'=>-1,'priority'=>6,
									 'title'=>$this->_FixSpaces(lang('extensions')),
									 'description'=>lang('extensionsdescription'),
									 'show_in_menu'=>$this->HasPerm('extensionsPerms'));
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
										   'description'=>lang('editeventhandlerdescription'),
										   'show_in_menu'=>false);
		$items['editusertag'] = array('url'=>'editusertag.php','parent'=>'usertags',
									  'title'=>$this->_FixSpaces(lang('editusertag')),
									  'description'=>lang('editusertag'),'show_in_menu'=>false);
		// base admin menu ---------------------------------------------------------
		$items['siteadmin'] = array('url'=>'index.php?section=siteadmin','parent'=>-1,
									'title'=>$this->_FixSpaces(lang('admin')),'priority'=>7,
									'description'=>lang('admindescription'),
									'show_in_menu'=>$this->HasPerm('siteAdminPerms'));
		$items['siteprefs'] = array('url'=>'siteprefs.php','parent'=>'siteadmin','priority'=>1,
									'title'=>$this->_FixSpaces(lang('globalconfig')),
									'description'=>lang('preferencesdescription'),
									'show_in_menu'=>$this->HasPerm('sitePrefPerms'));
		$items['systeminfo'] = array('url' => 'systeminfo.php', 'parent' => 'siteadmin','priority'=>2,
									 'title' => $this->_FixSpaces(lang('systeminfo')),
									 'description' => lang('systeminfodescription'),
									 'show_in_menu' => $this->HasPerm('adminPerms'));
		$items['systemmaintenance'] = array('url' => 'systemmaintenance.php','priority'=>1,'parent' => 'siteadmin',
											'title' => $this->_FixSpaces(lang('systemmaintenance')),
											'description' => lang('systemmaintenancedescription'),
											'show_in_menu' => $this->HasPerm('adminPerms'));
		$items['checksum'] = array('url' => 'checksum.php', 'parent' => 'siteadmin','priority'=>4,
								   'title' => $this->_FixSpaces(lang('system_verification')),
								   'description' => lang('checksumdescription'),
								   'show_in_menu' => $this->HasPerm('adminPerms'));
		$items['adminlog'] = array('url'=>'adminlog.php','parent'=>'siteadmin','priority'=>10,
								   'title'=>$this->_FixSpaces(lang('adminlog')),
								   'description'=>lang('adminlogdescription'),
								   'show_in_menu'=>$this->HasPerm('adminPerms'));
		// base my prefs menu ---------------------------------------------------------
		$items['myprefs'] = array('url'=>'index.php?section=myprefs','parent'=>-1,'priority'=>8,
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
		$items['ecommerce'] = array('url'=>'index.php?section=ecommerce','parent'=>-1,'priority'=>9,
									'title'=>$this->_FixSpaces(lang('ecommerce')),
									'description'=>lang('ecommerce_desc'),
									'show_in_menu'=>true);

		// adjust all the urls to include the session key
		// and set an icon if we can. also mark them as system items.
		foreach( $this->_menuItems as $sectionKey => $sectionArray ) {
			if( isset($sectionArray['url']) && (!isset($sectionArray['type']) || $sectionArray['type'] != 'external' )) {
				$this->_menuItems[$sectionKey]['url'] = $this->_fix_url_userkey($this->_menuItems[$sectionKey]['url']);
			}
			$this->_menuItems[$sectionKey]['system'] = 1;
		}

		debug_buffer('before system modules');

		// add in all of the 'system' modules next
		$moduleops = ModuleOperations::get_instance();
		// todo: cleanup
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

				$this->_menuItems[$key]=array('url'=>$menuItem->url,'parent'=>$sectionKey,'title'=>$this->_FixSpaces($menuItem->title),
											  'description'=>$menuItem->description,'show_in_menu'=>true,'system'=>1,
											  'module'=>$menuItem->module,'priority'=>1);
			}
		}

		debug_buffer('before non system module menu items');

		// add in all of the non system modules
		// non system modules cannot have a priority less than 2
		// todo: cleanup
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

				$this->_menuItems[$key]=array('url'=>$menuItem->url,'parent'=>$sectionKey,'title'=>$this->_FixSpaces($menuItem->title),
											  'description'=>$menuItem->description, 'show_in_menu'=>true,'module'=>$menuItem->module,
											  'priority'=>($menuItem->priority > 0)?max(2,$menuItem->priority):999);
			}
		}

		debug_buffer('after non system module menu items');

		// remove any menu items that don't fit into our valid sections
		foreach ($this->_menuItems as $sectionKey=>$sectionArray) {
			if( isset($sectionArray['system']) && $sectionArray['system'] ) continue;
			if( $sectionArray['parent'] == -1 && in_array($sectionKey,$this->_valid_sections) ) continue;
			if( isset($sectionArray['parent']) && in_array($sectionArray['parent'],$this->_valid_sections) ) continue;
			unset($this->_menuItems[$sectionKey]);
		}

		// remove any top level items that don't have children
		$parents = array();
		foreach ($this->_menuItems as $sectionKey=>$sectionArray) {
			if( $this->_menuItems[$sectionKey]['parent'] == -1 ) $parents[] = $sectionKey;
		}
		foreach( $parents as $oneparent ) {
			$found = 0;
			foreach ($this->_menuItems as $sectionKey=>$sectionArray) {
				if( $sectionArray['parent'] == $oneparent) {
					$found = 1;
					break;
				}
			}
			if( !$found ) unset($this->_menuItems[$oneparent]);
		}

		// sort the menu items by root level, system, priority, and then name (case insensitive)
		$fn = function($a,$b) {
			$a1 = (int)$a['parent'];
			$a2 = (int)$b['parent'];
			if( $a1 < $a2 ) return -1;
			if( $a1 > $a2 ) return 1;

			$sa = isset($a['system'])?$a['system']:0;
			$sb = isset($b['system'])?$b['system']:0;
			if( $sa && !$sb ) return -1;
			if( $sb && !$sa ) return 1;

			$pa = isset($a['priority'])?$a['priority']:999;
			$pb = isset($b['priority'])?$b['priority']:999;
			if( $pa < $pb ) return -1;
			if( $pa > $pb ) return 1;

			return strnatcmp($a['title'],$b['title']);
		};
		uasort($this->_menuItems,$fn);

		// set everything to be not selected.
		// resolve the tree to be doubly-linked,
		foreach ($this->_menuItems as $sectionKey=>$sectionArray) {
			$this->_menuItems[$sectionKey]['selected'] = FALSE;
			$this->_menuItems[$sectionKey]['children'] = array();

			// link the children to the parents; a little clumsy.
			foreach ($this->_menuItems as $subsectionKey=>$subsectionArray) {
				if ($subsectionArray['parent'] == $sectionKey) {
					$this->_menuItems[$sectionKey]['children'][] = $subsectionKey;
				}
			}
		}

        // find the selected menu item.
        $selected_key = null;
        $pending_selected_key = null;
        $req_url = new cms_url($_SERVER['REQUEST_URI']);
        $req_vars = array();
        if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mact']) && !isset($_GET['mact']) ) {
            // if mact is available via post and not via get we fake it, so that comparisons
            // can get the mact from the query
            $req_url->set_queryvar('mact',$_REQUEST['mact']);
            $req_url = new cms_url((string)$req_url);
        }
        parse_str($req_url->get_query(),$req_vars);

		foreach ($this->_menuItems as $sectionKey=>$sectionArray) {
			if( strstr($_SERVER['REQUEST_URI'],'moduleinterface.php') !== FALSE &&
				isset($_REQUEST['mact']) &&
				isset($sectionArray['module']) && $sectionArray['module'] ) {

                // note, this is kludgy
                // we compare each and every menu item against the request.
                // if the mact is set for both, and the module portion of the mact
                // are the same, we add a reference to the option to our 'matches'
                // list.  at the end, we re-compare
				$u1 = new cms_url(cms_html_entity_decode($sectionArray['url']));
				$v1 = array();
				parse_str($u1->get_query(),$v1);
				if( $u1->get_path() == $req_url->get_path() &&
					isset($v1['mact']) && isset($req_vars['mact']) ) {

                    $t1 = explode(',',$v1['mact']);
                    $t2 = explode(',',$req_vars['mact']);
                    if( $t1[0] == $t2[0] ) {
                        if( $t1[1] == $t2[1] && $t1[2] == $t2[2] ) {
                            // requested action is for the same module and same action, we're done.
                            $selected_key = $sectionKey;
                            break;
                        }
                        else if( !$pending_selected_key ) {
                            // requested action is for the same module, but different actions
                            // we continue, but set a pending key (only once)
                            $pending_selected_key = $sectionKey;
                        }
                    }
				}
			}
			else if(strstr($_SERVER['REQUEST_URI'], htmlspecialchars_decode($sectionArray['url']))!== FALSE &&
					 (!isset($sectionArray['type']) || $sectionArray['type'] != 'external') ){
                // this handles selecting internal actions that are not part of modules
                // i.e (admin/somefile.php stuff)
                $selected_key = $sectionKey;
                break;
            }
        }

        if( $selected_key || $pending_selected_key ) {
            // if we only have a pending key, we use it... not ideal.
            if( !$selected_key && $pending_selected_key ) $selected_key = $pending_selected_key;

            // we have the sectionKey of the selected match
            // now set it active, and set the parent all the way to the top
            // and build breadcrumbs
            $item =& $this->_menuItems[$selected_key];
            $item['selected'] = TRUE;
            $this->_title .= $item['title'];
            $this->_active_item = $selected_key;
            $this->_breadcrumbs[] = array('title'=>$item['title'],'url'=>$item['url']);
            if( $item['parent'] != -1 ) {
                $parent = $item['parent'];
                // walk up to the root.
                while( $parent != -1 ) {
                    $this->_menuItems[$parent]['selected'] = TRUE;
                    $this->_breadcrumbs[] = array('title'=>$this->_menuItems[$parent]['title'],
                                                  'url'=>$this->_menuItems[$parent]['url']);
                    $parent = $this->_menuItems[$parent]['parent'];
                }
        
      }
      else
      {
        // so we are in a section?
        // if it's the cms / home section we'll have to force it
        // otherwise no need to do anything...
        
        if($selected_key == 'main')
        {
          $item                 =& $this->_menuItems[$selected_key];
          $item['selected']     = TRUE;
          $this->_active_item   = $selected_key;
          $this->_menuItems[$item['parent']]['selected'] = TRUE;
            }
      }
      
      $this->_breadcrumbs = array_reverse($this->_breadcrumbs);
    }
    else
    {
      // we didn't get anything? we most likelly are in admin home right after loggin in
      // we'll have to force the default settings
      $selected_key         = 'home';
      $item                 =& $this->_menuItems[$selected_key];
      $item['selected']     = TRUE;
      $this->_title         .= $item['title'];
      $this->_active_item   = $selected_key;
      $this->_menuItems[$item['parent']]['selected'] = TRUE;
      $this->_breadcrumbs[] = array('title' => $item['title'], 'url' => $item['url']);
            $this->_breadcrumbs = array_reverse($this->_breadcrumbs);
        }

		// fix subtitle, if any
		if ($subtitle != '') $this->_title .= ': '.$subtitle;
		debug_buffer('after populate admin navigation');
    }


	/**
	 * Set the page title.
	 * This is used in the admin to set the title for the page, and for the visible page header.
	 * Note: if no title is specified, the theme will try to calculate one automatically.
	 *
	 * @since 2.0
	 * @param string $str The page title.
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
	 * @param string $str The page subtitle.
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
     * @param string $permission the permission to check.
	 * @return bool
     */
    protected function HasPerm($permission)
    {
		$this->_SetAggregatePermissions();

    	if (isset($this->_perms[$permission]) && $this->_perms[$permission]) return true;
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
        $smarty = \Smarty_CMS::get_instance();
		$smarty->assign('secureparam', CMS_SECURE_PARAM_NAME . '=' . $_SESSION[CMS_USER_KEY]);
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
			if( empty($one['show_in_menu']) ) continue;
			if( (!isset($one['parent']) && $parent == -1) ||
				(isset($one['parent']) && $one['parent'] == $parent) ) {
				if( isset($one['children']) ) unset($one['children']);

				if( $maxdepth < 0 || $depth + 1 < $maxdepth ) {
					$children = $this->_get_navigation_tree_sub($key,$maxdepth,$depth+1);
					if( is_array($children) && count($children) ) $one['children'] = $children;
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
	 * @param string $parent Indicates the parent to start at.  use a value of -1 to indicate the top node.
	 * @param int $maxdepth The maximum depth of the tree.  -1 indicates no maximum depth
	 * @return array A nested array of menu nodes.  The children member represents the nesting.
	 */
	public function get_navigation_tree($parent = -1,$maxdepth = -1)
	{
		$nodes = $this->_get_navigation_tree_sub($parent,$maxdepth);
		return $nodes;
	}

	/**
	 * Set the current action module
	 *
	 * @since 2.0
	 * @param string $module_name the module name.
	 */
	public function set_action_module($module_name)
	{
		if( !$module_name ) return;
		$this->_action_module = $module_name;
	}

	/**
	 * Determine the module name (if any) associated with the current request.
	 *
	 * @since 2.0
	 * @access protected
	 * @return string the module name for the current request, if any.
	 */
	protected function get_action_module()
	{
		if( $this->_action_module ) return $this->_action_module;
		// todo: if this is empty, get it from the mact in the request.
	}

	/**
	 * Get the help URL for a module.
	 *
	 * @since 2.0
	 * @access protected
	 * @param string $module_name
	 */
	protected function get_module_help_url($module_name = null)
	{
		if( !$module_name ) $module_name = $this->get_action_module();
		if( !$module_name ) return;

		$modman = cms_utils::get_module('ModuleManager');
		if( !is_object($modman) ) return;
		return $modman->create_url('m1_','defaultadmin','',array('modulehelp'=>$module_name));
	}

	/**
	 * A function to return the name (key) of a menu item given it's title
	 * returns the first match.
	 *
	 * @access protected
	 * @param string $title The title to search for
	 * @return string The matching key, or null
	 */
	protected function find_menuitem_by_title($title)
	{
		$nav = $this->get_admin_navigation();
		foreach( $nav as $key => $rec ) {
			if( isset($rec['title']) && $rec['title'] == $title ) return $key;
		}
	}


	/**
	 * Return the list of bookmarks
	 *
	 * @param bool $pure if False the shortcuts for adding and managing bookmarks are added to the list.
	 * @return array Array of Bookmark objects
	 */
	public function get_bookmarks($pure = FALSE)
	{
		$bookops = CmsApp::get_instance()->GetBookmarkOperations();
		$marks = array_reverse($bookops->LoadBookmarks($this->userid));
		
		if( !$pure ) {
			$source = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			//$url = str_replace(CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY], '', $source);
			$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
			$mark= new Bookmark();
			$mark->title = lang('addbookmark');
			$mark->url = 'makebookmark.php'.$urlext.'&amp;title='.urlencode($this->_title).'&amp;ref='.base64_encode($source);
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
	 * @return array Array of menu nodes representing the breadcrumb trail.
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
	 * @param string $key
	 * @param mixed $value
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
	 * @param string $key
	 * @returns void
	 */
	public function get_value($key)
	{
		if( is_array($this->_data) && isset($this->_data[$key]) ) return $this->_data[$key];
	}


	/**
	 * HasDisplayableChildren
	 * This method returns a boolean, based upon whether the section in question
	 * has displayable children.
	 *
	 * @deprecated
	 * @param string $section section to test
	 * @return bool
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
	 * @param string $imageName name of image
	 * @param string $alt alt text
	 * @param int $width width
	 * @param int $height height
	 * @param string $class class
	 */
	public function DisplayImage($imageName, $alt='', $width='', $height='', $class='')
	{
		// @todo: fix me...
		if( !is_array($this->_imageLink) ) $this->_imageLink = array();
		if (! isset($this->_imageLink[$imageName])) {
			$imagePath = '';
			if (strpos($imageName,'/') !== false) {
				$imagePath = substr($imageName,0,strrpos($imageName,'/')+1);
				$imageName = substr($imageName,strrpos($imageName,'/')+1);
			}

			$config = \cms_config::get_instance();
			$str = dirname(CMS_ROOT_PATH.'/'.$config['admin_dir']."/themes/{$this->themeName}/images/{$imagePath}{$imageName}");
			if (file_exists("{$str}/{$imageName}")) {
				$str = "themes/{$this->themeName}/images/{$imagePath}{$imageName}";
				$this->_imageLink[$imageName] = $str;
			}
			else {
				// todo: uses default theme.
				$this->_imageLink[$imageName] = 'themes/OneEleven/images/' . $imagePath . $imageName;
			}
		}

		$retStr = '<img src="'.$this->_imageLink[$imageName].'"';
		if ($class != '') $retStr .= ' class="'.$class.'"';
		if ($width != '') $retStr .= ' width="'.$width.'"';
		if ($height != '') $retStr .= ' height="'.$height.'"';
		if ($alt != '') $retStr .= ' alt="'.$alt.'" title="'.$alt.'"';
		$retStr .= ' />';
		return $retStr;
	}


	/**
	 * Abstract function for showing errors in the admin theme.
	 *
	 * @abstract
	 * @param mixed $errors The errors, either a string, or an array of strings
	 * @param string $get_var An optional get variable name that can contain an error string key.  If specified, errors is ignored.
	 */
	abstract public function ShowErrors($errors,$get_var='');

	/**
	 * Abstrct function for showing messages in the admin theme.
	 *
	 * @abstract
	 * @param mixed $message The message, either a string, or an array of stri9ngs
	 * @param string $get_var An optional get variable name that can contain an error string key.  If specified, message param is ignored.
	 */
	abstract public function ShowMessage($message,$get_var='');

	/**
	 * Abstract method for showing a header in the content area of a theme
	 * This is usually an advanced function with some special behaviour based on the module_help_type
	 *
	 * @abstract
	 * @deprecated
	 * @param string $title_name The name to show on the header.  This will not be passed through the lang process if module_help_type is not FALSE.
	 * @param array  $extra_lang_params Extra language parameters to pass to the title_name.  Ignored if module_help_type is not FALSE
	 * @param string $link_text Text to show in the module help link (depends on the module_help_type param)
	 * @param mixed  $module_help_type Flag for how to display module help types.   Possible values are TRUE to display a simple link, FALSE for no help, and 'both' for both types of links
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
			if( $logintheme && in_array($logintheme,$tmp) )	return $logintheme;
			return $tmp[0];
		}
	}


	/**
	 * Retrieve a list of the available admin themes.
	 *
	 * @return array A hash of strings.
	 */
	static public function GetAvailableThemes()
	{
		$config = \cms_config::get_instance();

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
	 * @param string $name optional theme name.
	 * @return CmsAdminThemeBase Reference to the initialized admin theme.
	 */
	static public function GetThemeObject($name = '')
	{
		if( is_object(self::$_instance) ) return self::$_instance;

		if( !$name ) $name = cms_userprefs::get_for_user(get_userid(FALSE),'admintheme',self::GetDefaultTheme());
		if( class_exists($name) ) {
			self::$_instance = new $name;
		}
		else {
			$gCms = CmsApp::get_instance();
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
	 * @param CmsAdminThemeNotification $notification A reference to the new notification
	 */
	public function add_notification(CmsAdminThemeNotification& $notification)
	{
		if( !is_array($this->_notifications) ) $this->_notifications = array();
		$this->_notifications[] = $notification;
	}

	/**
	 * A public function to add a notification for display in the theme.
	 * This is simply a compatibility wrapper around the add_notification method.
	 *
	 * @deprecated
	 * @param int $priority priority level between 1 and 3
	 * @param string $module The module name.
	 * @param string $html The contents of the notification
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
	 * @return Array Array of CmsAdminThemeNotification objects
	 */
	public function get_notifications()
	{
		return $this->_notifications;
	}


    /**
	 * Return an array of admin pages, suitable for use in a dropdown.
	 *
	 * @internal
	 * @since 1.12
	 * @param bool $none A flag indicating wether 'none' should be the first option.
	 * @return array The keys of the array are langified strings to display to the user.  The values are URLS.
	 */
    public function GetAdminPages($none = TRUE)
	{
		$opts = array();
		if( $none ) $opts[ucfirst(lang('none'))] = '';

		$depth = 0;
		$menuItems = $this->get_admin_navigation();
		foreach( $menuItems as $sectionKey=>$menuItem ) {
			if( $menuItem['parent'] != -1 ) continue; // only parent pages
			if( !$menuItem['show_in_menu'] || strlen($menuItem['url']) < 1 ) continue; // only visible stuff

			$opts[$menuItem['title']] = CmsAdminUtils::get_generic_url($menuItem['url']);

			if( is_array($menuItem['children']) && count($menuItem['children']) ) {
				foreach( $menuItem['children'] as $thisChild ) {
					if( $thisChild == 'home' || $thisChild == 'logout' || $thisChild == 'viewsite') {
						continue;
					}

					$menuChild = $menuItems[$thisChild];
					if( !$menuChild['show_in_menu'] || strlen($menuChild['url']) < 1 ) {
						continue;
					}

					//$opts['&nbsp;&nbsp;'.$menuChild['title']] = cms_htmlentities($menuChild['url']);
					$url = $menuChild['url'];
					$url = CmsAdminUtils::get_generic_url($url);
					$opts['&nbsp;&nbsp;'.$menuChild['title']] = $url;
				}
			}
		}
		return $opts;
	}

	/**
	 * Returns a select list of the pages in the system for use in
	 * various admin pages.
	 *
	 * @internal
	 * @param string $name - The html name of the select box
	 * @param string $selected - If a matching id is found in the list, that item
	 *                           is marked as selected.
	 * @return string The select list of pages
	 */
	public function GetAdminPageDropdown($name,$selected,$id = '')
	{
		$opts = $this->GetAdminPages();
        $attrs = array('name'=>trim((string)$name));
        if( $id ) $attrs['id'] = trim((string)$id);
        $output = '<select ';
        foreach( $attrs as $key => $val ) {
            $output .= ' '.$key.'='.$val;
        }
        $output .= '>';

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
		$count = $this->_breadcrumbs ? count($this->_breadcrumbs) - 2 : -1;
		$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
		if ($count > -1) {
			$txt = $this->_breadcrumbs[$count]['url'];
			return $txt;
		}
		// rely on base href to redirect back to the
		// admin home page
		return 'index.php'.$urlext;
	}

    /**
     * Add text to the head section of the output
     *
     * The CMSMS core code calls this method to add text and javascript to output in the head section required for various functionality.
     *
     * @param string $txt The text to add to the head section.
     * @since 2.2
     * @author Robert Campbell
     */
    public function add_headtext($txt)
    {
        $txt = trim($txt);
        if( $txt ) $this->_headtext .= "\n".$txt;
    }

    /**
     * Get text that needs to be injected into the head section of the output.
     *
     * This method is typically called by the admin theme itself to get the text to render.
     *
     * @return string
     * @since 2.2
     * @author Robert Campbell
     */
    public function get_headtext()
    {
        return $this->_headtext;
    }

    /**
     * Add text to the footer of the output, immediately before the </body> tag.
     *
     * @param string $txt The text to add to the end of the output.
     * @since 2.2
     * @author Robert Campbell
     */
    public function add_footertext($txt)
    {
        $txt = trim($txt);
        if( $txt ) $this->_footertext .= "\n".$txt;
    }

    /**
     * Get text that needs to be injected into the footer section of the output.
     *
     * This method is typically called by the admin theme itself to get the text to render.
     *
     * @return string
     * @since 2.2
     * @author Robert Campbell
     */
    public function get_footertext()
    {
        return $this->_footertext;
    }

	/**
	 * An abstract function to output the header html
	 * This function may not display anything, but may store data for use in the postprocess mechanism
	 * it should store, or output all of the information required for the head section of the page,
	 * and all admin navigation etc.  Many admin themes may not do anything here.
	 *
	 * @return string html contents.
     * @deprecated
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
	 * @param string $section_name The section name.  An empty string indicates that a navigation of all top level items should be created.
	 * @return string html contents.
	 */
	abstract public function do_toppage($section_name);

	/**
	 * An abstract function for processing the login form.
	 *
	 * @param  array $params
	 * @return string html contents for the login page.
	 */
	abstract public function do_login($params);

	/**
	 * An abstract function for processing the content area of the page.
	 * Many admin themes will do most of their work in this method (passing the html contents through a smarty template etc).
	 *
	 * @param string $html HTML contents
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
	 * @param string $tabid The tab id
	 * @param string $title The tab title
	 * @param bool $active A flag indicating wether this tab is active.
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
	 * @param string $tabid The tabid (see SetTabHeader)
	 * @param array $params Parameters
	 * @return string
	 */
	public final function StartTab($tabid, $params = array())
	{
		$message = '';
		if (FALSE == empty($this->_activetab) && $tabid == $this->_activetab && FALSE == empty($params['tab_message'])) {
			$message = $this->ShowMessage($this->Lang($params['tab_message']));
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
 * @license GPL
 * @since   1.11
 * @author  Robert Campbell
 * @property string $module Module name
 * @property int $priority Priority between 1 and 3
 * @property string $html HTML contents of the notification
 */
class CmsAdminThemeNotification
{

	/**
	 * @ignore
	 */
	private $_module;

	/**
	 * @ignore
	 */
	private $_priority;

	/**
	 * @ignore
	 */
	private $_html;


	/**
	 * @ignore
	 */
	public function __get($key)
	{
		switch( $key ) {
		case 'module':
		case 'priority':
		case 'html':
			return $this->$key;
		}

		throw new CmsInvalidDataException('Attempt to retrieve invalid property from CmsAdminThemeNotification');
	}


	/**
	 * @ignore
	 */
	public function __set($key,$value)
	{
		switch( $key ) {
		case 'module':
		case 'priority':
		case 'html':
			$this->$key = $value;
			return;
		}

		throw new CmsInvalidDataException('Attempt to set invalid property from CmsAdminThemeNotification');
	}
} // end of class

#
# EOF
#
?>
