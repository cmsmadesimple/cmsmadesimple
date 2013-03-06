<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2011 by Ted Kulp (ted@cmsmadesimple.org)
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
	
	// tab variables
	private $_activetab;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_url = $_SERVER['SCRIPT_NAME'];
		$this->_query = (isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'');
		if( $this->_query == '' && isset($_POST['mact']) )
			{
				$tmp = explode(',',$_POST['mact']);
				$this->_query = 'module='.$tmp[0];
			}
		if ($this->_query == '' && isset($_POST['module']) && $_POST['module'] != '')
			{
				$this->_query = 'module='.$_POST['module'];
			}
        if (strpos( $this->_url, '/' ) === false)
            {
				$this->_script = $this->_url;
            }
        else
            {
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
		if( $key == 'themeName' ) 
			{
				$class = get_class($this); 
				if( endswith($class,'Theme') ) $class = substr($class,0,strlen($class)-5);
				return $class;
			}
		if( $key == 'userid' ) return get_userid();
		//trigger_error("Attempt to access invalid member $key of admin theme object");
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
		$fn = TMP_CACHE_LOCATION.'/themeinfo_'.$uid.'.cache';
		$data = null;
		if( !file_exists($fn) )
		{
			// data doesn't exist.. gotta build it.
			$allmodules = ModuleOperations::get_instance()->GetInstalledModules();
			$usermoduleinfo = array();
			foreach( $allmodules as $key )
			{
				$object = ModuleOperations::get_instance()->get_module_instance($key);
				if( $object && $object->HasAdmin() && $object->VisibleToAdminUser() )
				{
					$rec = array();
					$rec['adminsection'] = $object->GetAdminSection();
					$rec['friendlyname'] = $object->GetFriendlyName();
					$rec['admindescription'] = $object->GetAdminDescription();
					$usermoduleinfo[$key] = $rec;
				}
				//ModuleOperations::get_instance()->unload_module($key);
				//unset($object);
			}

			// even if the array is empty... serialize the info.
			$data = $usermoduleinfo;
			$tmp = serialize($data);
			file_put_contents($fn,base64_encode($tmp));
		}
		else
		{
			$data = file_get_contents($fn);
			$data = base64_decode($data);
			$data = unserialize($data);
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
		if( !is_array($usermoduleinfo) ) 
		{
			// put mention into the admin log
			audit(get_userid(FALSE),'Admin Theme','No module information found for user');
		}

		// Are there any modules with an admin interface?
		foreach( $usermoduleinfo as $key => $rec )
		{
			$section = $rec['adminsection'];
			if( $section == '' ) $section == 'extensions';

			if (! isset($this->_sectionCount[$section]))
			{
				$this->_sectionCount[$section] = 0;
			}

			$data = array();
			$data['key'] = $key;
			$data['friendlyname'] = (isset($rec['friendlyname']))?$rec['friendlyname']:$key;
			$data['name'] = $data['friendlyname'];
			$data['description'] = ($rec['admindescription']!='')?$rec['admindescription']:'';
			$config = cmsms()->GetConfig();

			$tmp = array("modules/{$key}/images/icon.gif",
						 "modules/{$key}/icons/icons.gif",
						 "modules/{$key}/images/icon.png",
						 "modules/{$key}/icons/icons.png");
			foreach( $tmp as $one )
			{
				$fn = cms_join_path($config['root_path'],$one);
				if( file_exists($fn) )
				{
					$data['icon'] = $config['root_url'].'/'.$one;
					break;
				}
			}

			$this->_modulesBySection[$section][] = $data;
			$this->_sectionCount[$section]++;
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
		if( is_array($this->_perms) && $force == FALSE ) return;

		$this->_perms = array();
		$this->_breadcrumbs = array();

		// Content Permissions
        $this->_perms['htmlPerms'] = check_permission($this->userid, 'Add Global Content Blocks') |
			check_permission($this->userid, 'Modify Global Content Blocks') |
			check_permission($this->userid, 'Delete Global Content Blocks');

		$gCms = cmsms();
		$gcbops = $gCms->GetGlobalContentOperations();

        $thisUserBlobs = $gcbops->AuthorBlobs($this->userid);
        if (count($thisUserBlobs) > 0)
            {
				$this->_perms['htmlPerms'] = true;
            }
        $this->_perms['pagePerms'] = (
									 check_permission($this->userid, 'Modify Any Page') ||
									 check_permission($this->userid, 'Add Pages') ||
									 check_permission($this->userid, 'Remove Pages') ||
									 check_permission($this->userid, 'Manage All Content')
									 );
        $thisUserPages = author_pages($this->userid);
        if (count($thisUserPages) > 0)
            {
				$this->_perms['pagePerms'] = true;
            }
        $this->_perms['contentPerms'] = $this->_perms['pagePerms'] | $this->_perms['htmlPerms'] | 
			(isset($this->_sectionCount['content']) && $this->_sectionCount['content'] > 0);

		// layout        
        $this->_perms['templatePerms'] = check_permission($this->userid, 'Add Templates') |
			check_permission($this->userid, 'Modify Templates') |
			check_permission($this->userid, 'Remove Templates');
        $this->_perms['cssPerms'] = check_permission($this->userid, 'Add Stylesheets') |
			check_permission($this->userid, 'Modify Stylesheets') |
			check_permission($this->userid, 'Remove Stylesheets');
        $this->_perms['cssAssocPerms'] = check_permission($this->userid, 'Add Stylesheet Assoc') |
			check_permission($this->userid, 'Modify Stylesheet Assoc') |
			check_permission($this->userid, 'Remove Stylesheet Assoc');
        $this->_perms['layoutPerms'] = $this->_perms['templatePerms'] |
			$this->_perms['cssPerms'] | $this->_perms['cssAssocPerms'] |
			(isset($this->_sectionCount['layout']) && $this->_sectionCount['layout'] > 0);

		// file / image
        $this->_perms['filePerms'] = check_permission($this->userid, 'Modify Files') |
			(isset($this->_sectionCount['files']) && $this->_sectionCount['files'] > 0);
    
		// user/group
        $this->_perms['userPerms'] = check_permission($this->userid, 'Add Users') |
			check_permission($this->userid, 'Modify Users') |
			check_permission($this->userid, 'Remove Users');
        $this->_perms['groupPerms'] = check_permission($this->userid, 'Add Groups') |
			check_permission($this->userid, 'Modify Groups') |
			check_permission($this->userid, 'Remove Groups');
        $this->_perms['groupPermPerms'] = check_permission($this->userid, 'Modify Permissions');
        $this->_perms['groupMemberPerms'] =  check_permission($this->userid, 'Modify Group Assignments');
        $this->_perms['usersGroupsPerms'] = $this->_perms['userPerms'] |
			$this->_perms['groupPerms'] |
			$this->_perms['groupPermPerms'] |
			$this->_perms['groupMemberPerms'] |
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
    	$modList = array();
        if (isset($this->_sectionCount[$section]) && $this->_sectionCount[$section] > 0)
            {
            # Sort modules by name
            $names = array();
            foreach($this->_modulesBySection[$section] as $key => $row)
            {
            	$names[$key] = $this->_modulesBySection[$section][$key]['name'];
            }
            array_multisort($names, SORT_ASC, $this->_modulesBySection[$section]);

            foreach($this->_modulesBySection[$section] as $sectionModule)
	      {
                $modList[$sectionModule['key']]['url'] = "moduleinterface.php?".CMS_SECURE_PARAM_NAME."=".$_SESSION[CMS_USER_KEY]."&amp;module=".
		  $sectionModule['key'];
                $modList[$sectionModule['key']]['description'] = $sectionModule['description'];
                $modList[$sectionModule['key']]['name'] = $sectionModule['name'];
	      }
            }
        return $modList;
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
        if (count($this->_menuItems) > 0)
            {
				// we have already created the list
				return;
            }

		$config = cmsms()->GetConfig();
		debug_buffer('before populate admin navigation');	
        $this->subtitle = $subtitle;

		debug_buffer('before menu items');
    	    
    	$this->_menuItems = array(
								 // base main menu ---------------------------------------------------------
								 'main'=>array('url'=>'index.php','parent'=>-1,
											   'title'=>'CMS',
											   'description'=>'','show_in_menu'=>true),
								 'home'=>array('url'=>'index.php','parent'=>'main',
											   'title'=>$this->_FixSpaces(lang('home')),
											   'description'=>'','show_in_menu'=>true),
								 //	    'dashboard'=>array('url'=>'dashboard.php','parent'=>'main',
								 //			       'title'=>$this->_FixSpaces(lang('dashboard')),
								 //			       'description'=>'','show_in_menu'=>true),
								 'viewsite'=>array('url'=>$config['root_url'].'/index.php','parent'=>'main',
												   'title'=>$this->_FixSpaces(lang('viewsite')),
												   'type'=>'external',
												   'description'=>'','show_in_menu'=>true, 'target'=>'_blank'),
								 'logout'=>array('url'=>'logout.php','parent'=>'main',
												 'title'=>$this->_FixSpaces(lang('logout')),
												 'description'=>'','show_in_menu'=>true),
								 // base content menu ---------------------------------------------------------
								 'content'=>array('url'=>'index.php?section=content','parent'=>-1,
												  'title'=>$this->_FixSpaces(lang('content')),
												  'description'=>lang('contentdescription'),'show_in_menu'=>$this->HasPerm('contentPerms')),
								 'pages'=>array('url'=>'listcontent.php','parent'=>'content',
												'title'=>$this->_FixSpaces(lang('pages')),
												'description'=>lang('pagesdescription'),'show_in_menu'=>$this->HasPerm('pagePerms')),
								 'addcontent'=>array('url'=>'addcontent.php','parent'=>'pages',
													 'title'=>$this->_FixSpaces(lang('addcontent')),
													 'description'=>lang('addcontent'),'show_in_menu'=>false),
								 'editpage'=>array('url'=>'editcontent.php','parent'=>'pages',
												   'title'=>$this->_FixSpaces(lang('editpage')),
												   'description'=>lang('editpage'),'show_in_menu'=>false),
								 'images'=>array('url'=>'imagefiles.php','parent'=>'content',
												 'title'=>$this->_FixSpaces(lang('imagemanager')),
												 'description'=>lang('imagemanagerdescription'),'show_in_menu'=>$this->HasPerm('filePerms')),
								 'blobs'=>array('url'=>'listhtmlblobs.php','parent'=>'content',
												'title'=>$this->_FixSpaces(lang('htmlblobs')),
												'description'=>lang('htmlblobdescription'),'show_in_menu'=>$this->HasPerm('htmlPerms')),
								 'addhtmlblob'=>array('url'=>'addhtmlblob.php','parent'=>'blobs',
													  'title'=>$this->_FixSpaces(lang('addhtmlblob')),
													  'description'=>lang('addhtmlblob'),'show_in_menu'=>false),
								 'edithtmlblob'=>array('url'=>'edithtmlblob.php','parent'=>'blobs',
													   'title'=>$this->_FixSpaces(lang('edithtmlblob')),
													   'description'=>lang('edithtmlblob'),'show_in_menu'=>false),
								 // base layout menu ---------------------------------------------------------
								 'layout'=>array('url'=>'index.php?section=layout','parent'=>-1,
												 'title'=>$this->_FixSpaces(lang('layout')),
												 'description'=>lang('layoutdescription'),'show_in_menu'=>$this->HasPerm('layoutPerms')),
								 'template'=>array('url'=>'listtemplates.php','parent'=>'layout',
												   'title'=>$this->_FixSpaces(lang('templates')),
												   'description'=>lang('templatesdescription'),'show_in_menu'=>$this->HasPerm('templatePerms')),
								 'addtemplate'=>array('url'=>'addtemplate.php','parent'=>'template',
													  'title'=>$this->_FixSpaces(lang('addtemplate')),
													  'description'=>lang('addtemplate'),'show_in_menu'=>false),
								 'edittemplate'=>array('url'=>'edittemplate.php','parent'=>'template',
													   'title'=>$this->_FixSpaces(lang('edittemplate')),
													   'description'=>lang('edittemplate'),'show_in_menu'=>false),
								 'currentassociations'=>array('url'=>'listcssassoc.php','parent'=>'template',
															  'title'=>$this->_FixSpaces(lang('currentassociations')),
															  'description'=>lang('currentassociations'),'show_in_menu'=>false),
								 'copytemplate'=>array('url'=>'copyemplate.php','parent'=>'template',
													   'title'=>$this->_FixSpaces(lang('copytemplate')),
													   'description'=>lang('copytemplate'),'show_in_menu'=>false),
								 'stylesheets'=>array('url'=>'listcss.php','parent'=>'layout',
													  'title'=>$this->_FixSpaces(lang('stylesheets')),
													  'description'=>lang('stylesheetsdescription'),
													  'show_in_menu'=>($this->HasPerm('cssPerms') || $this->HasPerm('cssAssocPerms'))),
								 'addcss'=>array('url'=>'addcss.php','parent'=>'stylesheets',
												 'title'=>$this->_FixSpaces(lang('addstylesheet')),
												 'description'=>lang('addstylesheet'),'show_in_menu'=>false),
								 'editcss'=>array('url'=>'editcss.php','parent'=>'stylesheets',
												  'title'=>$this->_FixSpaces(lang('editcss')),
												  'description'=>lang('editcss'),'show_in_menu'=>false),
								 'templatecss'=>array('url'=>'templatecss.php','parent'=>'stylesheets',
													  'title'=>$this->_FixSpaces(lang('templatecss')),
													  'description'=>lang('templatecss'),'show_in_menu'=>false),
								 // base user/groups menu ---------------------------------------------------------
								 'usersgroups'=>array('url'=>'index.php?section=usersgroups','parent'=>-1,
													  'title'=>$this->_FixSpaces(lang('usersgroups')),
													  'description'=>lang('usersgroupsdescription'),'show_in_menu'=>$this->HasPerm('usersGroupsPerms')),
								 'users'=>array('url'=>'listusers.php','parent'=>'usersgroups',
												'title'=>$this->_FixSpaces(lang('users')),
												'description'=>lang('usersdescription'),'show_in_menu'=>$this->HasPerm('userPerms')),
								 'adduser'=>array('url'=>'adduser.php','parent'=>'users',
												  'title'=>$this->_FixSpaces(lang('adduser')),
												  'description'=>lang('adduser'),'show_in_menu'=>false),
								 'edituser'=>array('url'=>'edituser.php','parent'=>'users',
												   'title'=>$this->_FixSpaces(lang('edituser')),
												   'description'=>lang('edituser'),'show_in_menu'=>false),
								 'groups'=>array('url'=>'listgroups.php','parent'=>'usersgroups',
												 'title'=>$this->_FixSpaces(lang('groups')),
												 'description'=>lang('groupsdescription'),'show_in_menu'=>$this->HasPerm('groupPerms')),
								 'addgroup'=>array('url'=>'addgroup.php','parent'=>'groups',
												   'title'=>$this->_FixSpaces(lang('addgroup')),
												   'description'=>lang('addgroup'),'show_in_menu'=>false),
								 'editgroup'=>array('url'=>'editgroup.php','parent'=>'groups',
													'title'=>$this->_FixSpaces(lang('editgroup')),
													'description'=>lang('editgroup'),'show_in_menu'=>false),
								 'groupmembers'=>array('url'=>'changegroupassign.php','parent'=>'usersgroups',
													   'title'=>$this->_FixSpaces(lang('groupassignments')),
													   'description'=>lang('groupassignmentdescription'),'show_in_menu'=>$this->HasPerm('groupMemberPerms')),                    
								 'groupperms'=>array('url'=>'changegroupperm.php','parent'=>'usersgroups',
													 'title'=>$this->_FixSpaces(lang('groupperms')),
													 'description'=>lang('grouppermsdescription'),'show_in_menu'=>$this->HasPerm('groupPermPerms')),                    
								 // base extensions menu ---------------------------------------------------------
								 'extensions'=>array('url'=>'index.php?section=extensions','parent'=>-1,
													 'title'=>$this->_FixSpaces(lang('extensions')),
													 'description'=>lang('extensionsdescription'),'show_in_menu'=>$this->HasPerm('extensionsPerms')),
								 'modules'=>array('url'=>'listmodules.php','parent'=>'extensions',
												  'title'=>$this->_FixSpaces(lang('modules')),
												  'description'=>lang('moduledescription'),'show_in_menu'=>$this->HasPerm('modulePerms')),
								 'tags'=>array('url'=>'listtags.php','parent'=>'extensions',
											   'title'=>$this->_FixSpaces(lang('tags')),
											   'description'=>lang('tagdescription'),'show_in_menu'=>$this->HasPerm('taghelpPerms')),
								 'usertags'=>array('url'=>'listusertags.php','parent'=>'extensions',
												   'title'=>$this->_FixSpaces(lang('usertags')),
												   'description'=>lang('usertagdescription'),'show_in_menu'=>$this->HasPerm('codeBlockPerms')),
								 'eventhandlers'=>array('url'=>'eventhandlers.php','parent'=>'extensions',
														'title'=>$this->_FixSpaces(lang('eventhandlers')),
														'description'=>lang('eventhandlerdescription'),'show_in_menu'=>$this->HasPerm('eventPerms')),
								 'editeventhandler'=>array('url'=>'editevent.php','parent'=>'eventhandlers',
														   'title'=>$this->_FixSpaces(lang('editeventhandler')),
														   'description'=>lang('editeventshandler'),'show_in_menu'=>false),
								 'addusertag'=>array('url'=>'adduserplugin.php','parent'=>'usertags',
													 'title'=>$this->_FixSpaces(lang('addusertag')),
													 'description'=>lang('addusertag'),'show_in_menu'=>false),
								 'editusertag'=>array('url'=>'edituserplugin.php','parent'=>'usertags',
													  'title'=>$this->_FixSpaces(lang('editusertag')),
													  'description'=>lang('editusertag'),'show_in_menu'=>false),
								 // base admin menu ---------------------------------------------------------
								 'siteadmin'=>array('url'=>'index.php?section=siteadmin','parent'=>-1,
													'title'=>$this->_FixSpaces(lang('admin')),
													'description'=>lang('admindescription'),'show_in_menu'=>$this->HasPerm('siteAdminPerms')),
								 'siteprefs'=>array('url'=>'siteprefs.php','parent'=>'siteadmin',
													'title'=>$this->_FixSpaces(lang('globalconfig')),
													'description'=>lang('preferencesdescription'),'show_in_menu'=>$this->HasPerm('sitePrefPerms')),
								 'pagedefaults'=>array('url'=>'pagedefaults.php','parent'=>'siteadmin',
													   'title'=>$this->_FixSpaces(lang('pagedefaults')),
													   'description'=>lang('pagedefaultsdescription'),'show_in_menu'=>$this->HasPerm('sitePrefPerms')),
								 'systeminfo' => array('url' => 'systeminfo.php', 'parent' => 'siteadmin',
													   'title' => $this->_FixSpaces(lang('systeminfo')),
													   'description' => lang('systeminfodescription'),
													   'show_in_menu' => $this->HasPerm('adminPerms')),
								 'systemmaintenance' => array('url' => 'systemmaintenance.php', 'parent' => 'siteadmin',
															  'title' => $this->_FixSpaces(lang('systemmaintenance')),
															  'description' => lang('systemmaintenancedescription'),
															  'show_in_menu' => $this->HasPerm('adminPerms')),
								 'checksum' => array('url' => 'checksum.php', 'parent' => 'siteadmin',
													 'title' => $this->_FixSpaces(lang('system_verification')),
													 'description' => lang('checksumdescription'),
													 'show_in_menu' => $this->HasPerm('adminPerms')),
								 'adminlog'=>array('url'=>'adminlog.php','parent'=>'siteadmin',
												   'title'=>$this->_FixSpaces(lang('adminlog')),
												   'description'=>lang('adminlogdescription'),'show_in_menu'=>$this->HasPerm('adminPerms')),
								 // base my prefs menu ---------------------------------------------------------
								 'myprefs'=>array('url'=>'index.php?section=myprefs','parent'=>-1,
												  'title'=>$this->_FixSpaces(lang('myprefs')),
												  'description'=>lang('myprefsdescription'),'show_in_menu'=>true),
								 'myaccount'=>array('url'=>'myaccount.php','parent'=>'myprefs',
													'title'=>$this->_FixSpaces(lang('myaccount')),
													'description'=>lang('myaccountdescription'),'show_in_menu'=>true),
								/* 'preferences'=>array('url'=>'editprefs.php','parent'=>'myprefs',
													  'title'=>$this->_FixSpaces(lang('adminprefs')),
													  'description'=>lang('adminprefsdescription'),'show_in_menu'=>$this->HasPerm('sitePrefPerms')),*/
								 'managebookmarks'=>array('url'=>'listbookmarks.php','parent'=>'myprefs',
														  'title'=>$this->_FixSpaces(lang('managebookmarks')),
														  'description'=>lang('managebookmarksdescription'),'show_in_menu'=>true),
								 'addbookmark'=>array('url'=>'addbookmark.php','parent'=>'myprefs',
													  'title'=>$this->_FixSpaces(lang('addbookmark')),
													  'description'=>lang('addbookmark'),'show_in_menu'=>false),
								 'editbookmark'=>array('url'=>'editbookmark.php','parent'=>'myprefs',
													   'title'=>$this->_FixSpaces(lang('editbookmark')),
													   'description'=>lang('editbookmark'),'show_in_menu'=>false),
								 );

		debug_buffer('after menu items');


		// slightly cleaner syntax
		$this->_menuItems['ecommerce'] = array('url'=>'index.php?section=ecommerce','parent'=>-1,
											  'title'=>$this->_FixSpaces(lang('ecommerce')),
											  'description'=>lang('ecommerce_desc'),
											  'show_in_menu'=>true);
	
	
		// adjust all the urls to include the session key
		// and set an icon if we can.
		foreach( $this->_menuItems as $sectionKey => $sectionArray )
			{
				if( isset($sectionArray['url']) && 
					(!isset($sectionArray['type']) || $sectionArray['type'] != 'external' ))
					{
						$url = $this->_menuItems[$sectionKey]['url'];
						if( strpos($url,'?') !== FALSE )
							{
								$url .= '&amp;';
							}
						else
							{
								$url .= '?';
							}
						$url .= CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

						$this->_menuItems[$sectionKey]['url'] = $url;
					}
			}
		
		debug_buffer('before syste modules');

		// add in all of the 'system' modules too
		$gCms = cmsms();
		foreach ($this->_menuItems as $sectionKey=>$sectionArray)
			{
				$tmpArray = $this->_MenuListSectionModules($sectionKey);
				$first = true;
				foreach ($tmpArray as $thisKey=>$thisVal)
					{
						$thisModuleKey = $thisKey;
						$counter = 0;
				  
						// don't clobber existing keys
						if (array_key_exists($thisModuleKey,$this->_menuItems))
							{
								while (array_key_exists($thisModuleKey,$this->_menuItems))
									{
										$thisModuleKey = $thisKey.$counter;
										$counter++;
									}
							}
				  
						// if it's a system module...
						if( ModuleOperations::get_instance()->IsSystemModule($thisModuleKey) )
							{
								$this->_menuItems[$thisModuleKey]=array('url'=>$thisVal['url'],
																	   'parent'=>$sectionKey,
																	   'title'=>$this->_FixSpaces($thisVal['name']),
																	   'description'=>$thisVal['description'],
																	   'show_in_menu'=>true);
						  
							}
					}
			}
	
		debug_buffer('before module menu items');

		// add in all of the modules
        foreach ($this->_menuItems as $sectionKey=>$sectionArray)
			{
				$tmpArray = $this->_MenuListSectionModules($sectionKey);
				$first = true;
				foreach ($tmpArray as $thisKey=>$thisVal)
					{
						$thisModuleKey = $thisKey;
						$counter = 0;

						// don't clobber existing keys
						if (array_key_exists($thisModuleKey,$this->_menuItems))
							{
								while (array_key_exists($thisModuleKey,$this->_menuItems))
									{
										$thisModuleKey = $thisKey.$counter;
										$counter++;
									}
								if( $counter > 0 )
									{
										continue;
									}
							}
						$this->_menuItems[$thisModuleKey]=array('url'=>$thisVal['url'],
															   'parent'=>$sectionKey,
															   'title'=>$this->_FixSpaces($thisVal['name']),
															   'description'=>$thisVal['description'],
															   'show_in_menu'=>true);
						if ($first)
							{
								$this->_menuItems[$thisModuleKey]['firstmodule'] = 1;
								$first = false;
							}
						else
							{
								$this->_menuItems[$thisModuleKey]['module'] = 1;
							}
					}
			}
	
		debug_buffer('after module menu items');

		// remove any top level items that don't have children
		$parents = array();
		foreach ($this->_menuItems as $sectionKey=>$sectionArray)
			{
				if( $this->_menuItems[$sectionKey]['parent'] == -1 )
					{
						$parents[] = $sectionKey;
					}
			}
		foreach( $parents as $oneparent )
			{
				$found = 0;
				foreach ($this->_menuItems as $sectionKey=>$sectionArray)
					{
						if( $sectionArray['parent'] == $oneparent )
							{
								$found = 1;
								break;
							}
					}
				if( !$found ) unset($this->_menuItems[$oneparent]);
			}
	
		// resolve the tree to be doubly-linked,
		// and make sure the selections are selected            
		foreach ($this->_menuItems as $sectionKey=>$sectionArray)
			{
				// link the children to the parents; a little clumsy since we can't
				// assume php5-style references in a foreach.
				$this->_menuItems[$sectionKey]['children'] = array();
				foreach ($this->_menuItems as $subsectionKey=>$subsectionArray)
					{
						if ($subsectionArray['parent'] == $sectionKey)
							{
								$this->_menuItems[$sectionKey]['children'][] = $subsectionKey;
							}
					}

				// set selected
				if ($this->_script == 'moduleinterface.php')
					{
						$a = preg_match('/(module|mact)=([^&,]+)/',$this->_query,$matches);
						if ($a > 0 && $matches[2] == $sectionKey)
							{
								$this->_menuItems[$sectionKey]['selected'] = true;
								$this->title .= $sectionArray['title'];
								if ($sectionArray['parent'] != -1)
									{
										$parent = $sectionArray['parent'];
										while ($parent != -1)
											{
												$this->_menuItems[$parent]['selected'] = true;
												$parent = $this->_menuItems[$parent]['parent'];
											}
									}
							}
						else
							{
								$this->_menuItems[$sectionKey]['selected'] = false;
							}
					}
				else if (strstr($_SERVER['REQUEST_URI'],$sectionArray['url']) !== FALSE &&
						 (!isset($sectionArray['type']) || $sectionArray['type'] != 'external'))
					{
						$this->_menuItems[$sectionKey]['selected'] = true;
						$this->title .= $sectionArray['title'];
						if ($sectionArray['parent'] != -1)
							{
								$parent = $sectionArray['parent'];
								while ($parent != -1)
									{
										$this->_menuItems[$parent]['selected'] = true;
										$parent = $this->_menuItems[$parent]['parent'];
									}
							}
					}
				else
					{
						$this->_menuItems[$sectionKey]['selected'] = false;
					}
			}
		// fix subtitle, if any
		if ($subtitle != '')
			{
				$this->title .= ': '.$subtitle;
			}
		// generate breadcrumb array

		$count = 0;
		foreach ($this->_menuItems as $key=>$menuItem)
			{
				if ($menuItem['selected'])
					{
						$this->_breadcrumbs[] = array('title'=>$menuItem['title'], 'url'=>$menuItem['url']);
						$this->title = $menuItem['title'];
						$count++;
					}
			}
		if ($count > 0)
			{
				// and fix up the last breadcrumb...
				if ($this->_query != '' && strpos($this->_breadcrumbs[$count-1]['url'],'&amp;') === false)
					{
						$this->_query = preg_replace('/\&/','&amp;',$this->_query);
						$pos = strpos($this->_breadcrumbs[$count-1]['url'],'?');
						$tmp = substr($this->_breadcrumbs[$count-1]['url'],0,$pos).'?'.$this->_query;
						$this->_breadcrumbs[$count-1]['url'] = $tmp;
					}
				unset($this->_breadcrumbs[$count-1]['url']);
				if ($this->subtitle != '')
					{
						$this->_breadcrumbs[$count-1]['title'] .=  ': '.$this->subtitle;
					}
			}
		  debug_buffer('after populate admin navigation');
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

    	if (isset($this->_perms[$permission]) && $this->_perms[$permission])
    	   {
    	   	return true;
    	   }
    	else
    	   {
    	   	return false;
    	   }
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
		foreach( $flatitems as $key => $one )
			{
				if( !$one['show_in_menu'] ) continue;
				if( (!isset($one['parent']) && $parent == -1) || (isset($one['parent']) && $one['parent'] == $parent) )
					{
						if( isset($one['children']) )
							{
								unset($one['children']);
							}

						if( $maxdepth < 0 || $depth + 1 < $maxdepth )
							{
								$children = $this->_get_navigation_tree_sub($key,$maxdepth,$depth+1);
								if( is_array($children) && count($children) )
									{
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
		foreach( $nav as $key => $rec )
			{
				if( isset($rec['title']) && $rec['title'] == $title )
					{
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

		if( !$pure )
			{
				$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
				$mark= new Bookmark();
				$mark->title = lang('addbookmark');
				$mark->url = 'makebookmark.php'.$urlext.'&amp;title='.urlencode($this->title);
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
	 * Attach some data to the admin theme.
	 *
	 * @param string key
	 * @param mixed value
	 * @returns void
	 */
	public function set_value($key,$value)
	{
		if( is_null($value) && is_array($this->_data) && isset($this->_data[$key]) )
			{
				unset($this->_data[$key]);
				return;
			}
		if( $value )
			{
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
		if( is_array($this->_data) && isset($this->_data[$key]) )
			{
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
		foreach($this->_menuItems[$section]['children'] as $thisChild)
			{
				$thisItem = $this->_menuItems[$thisChild];
				if ($thisItem['show_in_menu'])
					{
						$displayableChildren = true;
						break;
					}
			}
		return $displayableChildren;
	}


	/**
	 * DisplayImage will display the themed version of an image (if it exists),
	 * or the version from the default theme otherwise.
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
	 * This is usually an advanced function with some speciial behaviour based on the module_help_type
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
		if( is_array($tmp) && count($tmp) ) 
			{
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
		if( is_array($files) && count($files) )
			{
				$res = array();
				foreach( $files as $file )
					{
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
		if( class_exists($name) )
			{
				self::$_instance = new $name;
			}
		else
			{
				$gCms = cmsms();
				$config = $gCms->GetConfig();
				$themeObjName = $name."Theme";
				$fn = $config['admin_path']."/themes/$name/{$themeObjName}.php";
				if( file_exists($fn) )
					{
						include_once($fn);
						self::$_instance = new $themeObjName($gCms,get_userid(FALSE),$name);
					}
				else
					{
						// theme not found... use default
						$name = self::GetDefaultTheme();
						$themeObjName = $name."Theme";
						$fn = $config['admin_path']."/themes/$name/{$themeObjName}.php";
						if( file_exists($fn) )
							{
								include_once($fn);
								self::$_instance = new $themeObjName($gCms,get_userid(FALSE),$name);
							}
						else
							{
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
		if( !is_array($this->_notifications) )
			{
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
	public function GetAdminPageDropdown($name,$selected)
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

		$output = '<select name="'.$name.'">'."\n";
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
		if ($count > -1)
			{
				$txt = $this->_breadcrumbs[$count]['url'];
				return $txt;
			}
		else
			{
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
		if (TRUE == $active)
		{
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
		switch( $key )
		{
		case 'module':
		case 'priority':
		case 'html':
			return $this->$key;
		}

		throw new Exception('Attempt to retrieve invalid property from CmsAdminThemeNotification');
	}

	public function __set($key,$value)
	{
		switch( $key )
		{
		case 'module':
		case 'priority':
		case 'html':
			$this->$key = $value;
			return;
		}

		throw new Exception('Attempt to set invalid property from CmsAdminThemeNotification');
	}
}

#
# EOF
#
# vim:ts=4 sw=4 noet
?>
