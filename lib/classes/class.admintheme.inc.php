<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id$

/**
 * @package CMS 
 */

/**
 * Class for Admin Theme
 *
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class AdminTheme
{
	/**
	 * Title
	 */
	protected $title;

    /**
     * Subtitle, for use in breadcrumb trails
     */
    protected $subtitle;

	/**
	 * Url
	 */
	protected $url;
	
    /**
	 * Script
	 */
	protected $script;

	/**
	 * Query String, for use in breadcrumb trails
	 */
	protected $query;

    /**
     * Aggregation of modules by section
     */
    protected $modulesBySection;

    /**
     * count of modules in each section
     */
    protected $sectionCount;

    /**
     * Aggregate Permissions
     */
    protected $perms;

    /**
     * Recent Page List
     */
    protected $recent;

    /**
     * Current Active User
     */
    protected $user;

    /**
     * Admin Section Menu cache
     */
    protected $menuItems;

    /**
     * Admin Section Image cache
     */
    protected $imageLink;

    /**
     * Theme Name
     */
    protected $themeName;

    /**
     * Breadcrumbs Array
     */
    protected $breadcrumbs;

    /**
     * Notification Items
     */
    protected $_notificationitems;

    /**
     * View Site URL
     */
    protected $_viewsite_url;

	/**
	 * __get()
	 */
	public function __get($key)
	{
		if( $key == 'cms' ) return cmsms();
		if( $key == 'themeName' ) return $this->themeName;
		trigger_error("Attempt to access invalid member $key of admin theme object");
	}

	/**
	 * Generic constructor.  Runs the SetInitialValues fuction.
	 */
	public function __construct($cms, $userid, $themeName)
	{
		$this->SetInitialValues($cms, $userid, $themeName);
	}

	/**
	 * Sets object to some sane initial values
	 */
	private function SetInitialValues($cms, $userid, $themeName)
	{
		$gCms = cmsms();
 		$this->_viewsite_url = $gCms->config['root_url'];

		$this->title = '';
		$this->subtitle = '';
		$this->url = $_SERVER['SCRIPT_NAME'];
		$this->query = (isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'');
		if( $this->query == '' && isset($_POST['mact']) )
		  {
		    $tmp = explode(',',$_POST['mact']);
		    $this->query = 'module='.$tmp[0];
		  }
		if ($this->query == '' && isset($_POST['module']) && $_POST['module'] != '')
		  {
		  $this->query = 'module='.$_POST['module'];
		  }
		$this->userid = $userid;
		$this->themeName = $themeName;
		$this->perms = array();
		$this->recent = array();
		$this->menuItems = array();
		$this->breadcrumbs = array();
		$this->imageLink = array();
		$this->modulesBySection = array();
		$this->sectionCount = array();
        $this->SetModuleAdminInterfaces();
        $this->SetAggregatePermissions();
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

	}

    /**
     * Send admin page HTTP headers.
     *
     * @param alreadySentCharset boolean have we already sent character encoding?
     * @param encoding string what encoding should we set?
     *
     */
    function SendHeaders($alreadySentCharset, $encoding)
    {
        // Date in the past
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        // always modified
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
 
        // HTTP/1.1
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);

        // HTTP/1.0
        header("Pragma: no-cache");
        
        // Language shizzle
        if (! $alreadySentCharset)
        {
	       header("Content-Type: text/html; charset=$encoding");
        }
    }
    
    /**
     * MenuListSectionModules
     * This method reformats module information for display in menus. When passed the
     * name of the admin section, it returns an array of associations:
     * array['module-name']['url'] is the link to that module, and
     * array['module-name']['description'] is the language-specific short description of
     *   the module.
     *
     * @param section - section to display
     */
    private function MenuListSectionModules($section)
    {
    	$modList = array();
        if (isset($this->sectionCount[$section]) && $this->sectionCount[$section] > 0)
            {
            # Sort modules by name
            $names = array();
            foreach($this->modulesBySection[$section] as $key => $row)
            {
            	$names[$key] = $this->modulesBySection[$section][$key]['name'];
            }
            array_multisort($names, SORT_ASC, $this->modulesBySection[$section]);

            foreach($this->modulesBySection[$section] as $sectionModule)
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
	 * _get_user_module_info
	 *
	 * Given the currently logged in user, this will read cache information representing info for all avallable modules
	 * for that particular user.   If cache information is not available, then modules will be loaded and the information
	 * will be gleaned from the module for that user.
	 *
	 * 
	 * @since 1.10
	 * @access private
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
			$modules = ModuleOperations::get_instance()->GetInstalledModules();
			$usermoduleinfo = array();
			foreach( $modules as $key )
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
				ModuleOperations::get_instance()->unload_module($key);
				unset($object);
			}
			unset($modules);

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
     * SetModuleAdminInterfaces
     *
     * This function sets up data structures to place modules in the proper Admin sections
     * for display on section pages and menus.
     *
     */
    private function SetModuleAdminInterfaces()
    {
		// get the info from the cache
		$usermoduleinfo = $this->_get_user_module_info();
		if( !is_array($usermoduleinfo) ) 
		{
			audit(get_userid(FALSE),'Admin Theme','No module information found for user');
		}

		// Are there any modules with an admin interface?
		foreach( $usermoduleinfo as $key => $rec )
		{
			$section = $rec['adminsection'];
			if( $section == '' ) $section == 'extensions';

			if (! isset($this->sectionCount[$section]))
			{
				$this->sectionCount[$section] = 0;
			}
			$this->modulesBySection[$section][$this->sectionCount[$section]]['key'] = $key;
			if ($rec['friendlyname'] != '')
			{
				$this->modulesBySection[$section][$this->sectionCount[$section]]['name'] = $rec['friendlyname'];
			}
			else
			{
				$this->modulesBySection[$section][$this->sectionCount[$section]]['name'] = $key;
			}
			if ($rec['admindescription'] != '')
			{
				$this->modulesBySection[$section][$this->sectionCount[$section]]['description'] =
					$rec['admindescription'];
			}
			else
			{
				$this->modulesBySection[$section][$this->sectionCount[$section]]['description'] = "";
			}
			$this->sectionCount[$section]++;
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
     */
    private function SetAggregatePermissions()
    {
        # Content Permissions
        $this->perms['htmlPerms'] = check_permission($this->userid, 'Add Global Content Blocks') |
                check_permission($this->userid, 'Modify Global Content Blocks') |
                check_permission($this->userid, 'Delete Global Content Blocks');

		$gCms = cmsms();
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
                check_permission($this->userid, 'Manage All Content')
        );
        $thisUserPages = author_pages($this->userid);
        if (count($thisUserPages) > 0)
            {
            $this->perms['pagePerms'] = true;
            }
        $this->perms['contentPerms'] = $this->perms['pagePerms'] | $this->perms['htmlPerms'] | 
                (isset($this->sectionCount['content']) && $this->sectionCount['content'] > 0);

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
                (isset($this->sectionCount['layout']) && $this->sectionCount['layout'] > 0);

        # file / image
        $this->perms['filePerms'] = check_permission($this->userid, 'Modify Files') |
                (isset($this->sectionCount['files']) && $this->sectionCount['files'] > 0);
    
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
                (isset($this->sectionCount['usersgroups']) &&
                    $this->sectionCount['usersgroups'] > 0);

        # admin
        $this->perms['sitePrefPerms'] = check_permission($this->userid, 'Modify Site Preferences') |
            (isset($this->sectionCount['preferences']) && $this->sectionCount['preferences'] > 0);
        $this->perms['adminPerms'] = $this->perms['sitePrefPerms'] |
            (isset($this->sectionCount['admin']) && $this->sectionCount['admin'] > 0);
        $this->perms['siteAdminPerms'] = $this->perms['sitePrefPerms'] |
                $this->perms['adminPerms'] |
                (isset($this->sectionCount['admin']) &&
                    $this->sectionCount['admin'] > 0);


        # extensions
        $this->perms['codeBlockPerms'] = check_permission($this->userid, 'Modify User-defined Tags');
        $this->perms['modulePerms'] = check_permission($this->userid, 'Modify Modules');
        $this->perms['eventPerms'] = check_permission($this->userid, 'Modify Events');
	$this->perms['taghelpPerms'] = check_permission($this->userid, 'View Tag Help');
        $this->perms['extensionsPerms'] = $this->perms['codeBlockPerms'] |
            $this->perms['modulePerms'] |
	    $this->perms['eventPerms'] |
	    $this->perms['taghelpPerms'] |
            (isset($this->sectionCount['extensions']) && $this->sectionCount['extensions'] > 0);
    }
    
    /**
     * HasPerm
     *
     * Check if the user has one of the aggregate permissions
     * 
     * @param permission the permission to check.
     */
    function HasPerm($permission)
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
     * LoadRecentPages
     * This method loads a list of recently-accessed pages from the database.
     * This list is stored in this object's variable "recent" as an array of
     * associations. See ../lib/classes/class.recentpage.inc.php for more
     * information on the array's format.
     *
     */
    function LoadRecentPages()
    {
        require_once("../lib/classes/class.recentpage.inc.php");
        $this->recent = RecentPageOperations::LoadRecentPages($this->userid);
    }

    /**
     * AddAsRecentPage
     * Adds this page to the list of recently-visited pages. It attempts to
     * filter out top-level pages, and to avoid adding the same page multiple times.
     *
     */
    function AddAsRecentPage()
    {
    	if (count($this->recent) < 1)
    	   {
    	   	$this->LoadRecentPages();
    	   }

        $addToRecent = true;
        foreach ($this->recent as $thisPage)
            {
            if ($thisPage->url == $this->url)
                {
                $addToRecent = false;
                }
            if ($thisPage->title == $this->title)
                {
                $addToRecent = false;
                }
            }
        if (preg_match('/moduleinterface/', $this->url))
        	{
        	if (! preg_match('/module=/', $this->url))
        		{
        		$addToRecent = false;
        		}
			}
        if ($addToRecent)
            {
            $rp = new RecentPage();
            $rp->setValues($this->title, $this->url, $this->userid);
            $rp->Save();
            $this->recent = array_reverse($this->recent);
            $this->recent[] = $rp;
            if (count($this->recent) > 5)
                {
                array_shift($this->recent);
                }
            $this->recent = array_reverse($this->recent);
            $rp->PurgeOldPages($this->userid,5);
            }
    }

    /**
     * DoBookmarks
     * Setup method for displaying admin bookmarks.
     */
    function DoBookmarks()
    {
		$gCms = cmsms();
      $bookops = $gCms->GetBookmarkOperations();
      $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
      $marks = array_reverse($bookops->LoadBookmarks($this->userid));
      $tmpMark = new Bookmark();
      $tmpMark->title = lang('addbookmark');
      $tmpMark->url = 'makebookmark.php'.$urlext.'&amp;title='. urlencode($this->title);
      $marks[] = $tmpMark;
      $marks = array_reverse($marks);
      $tmpMark = new Bookmark();
      $tmpMark->title = lang('managebookmarks');
      $tmpMark->url = 'listbookmarks.php'.$urlext;
      $marks[] = $tmpMark;
      $this->DisplayBookmarks($marks);
    }


    /**
     * DoBookmarks
     * Method for displaying admin bookmarks (shortcuts) & help links.
     */
    function ShowShortcuts()
    {
      if (get_preference($this->userid, 'bookmarks')) {
	$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
	echo "<div class=\"sections\">\n";
	echo '<div class="itemmenucontainer shortcuts" style="float:left;">';
	echo '<div class="itemoverflow">';
	echo '<h2>'.lang('bookmarks').'</h2>';
	echo '<p><a href="listbookmarks.php'.$urlext.'">'.lang('managebookmarks').'</a></p>';
	$gCms = cmsms();
	$bookops = $gCms->GetBookmarkOperations();
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
	echo '<li><a rel="external" href="http://forum.cmsmadesimple.org/">'.lang('forums').'</a></li>';
	echo '<li><a rel="external" href="http://wiki.cmsmadesimple.org/">'.lang('wiki').'</a></li>';
	echo '<li><a rel="external" href="http://cmsmadesimple.org/main/support/IRC">'.lang('irc').'</a></li>';
	echo '<li><a rel="external" href="http://wiki.cmsmadesimple.org/index.php/User_Handbook/Admin_Panel/Extensions/Modules">'.lang('module_help').'</a></li>';
	echo '</ul>';
	echo '</div>';
	echo '</div>';
      }
    }
    

    /**
     * DisplayBookmarks
     * Output bookmark data. Over-ride this to alter display of Bookmark information.
     * Bookmark objects contain two useful fields: title and url
     *
     *
     * @param marks - this is an array of Bookmark Objects
     */
    function DisplayBookmarks($marks)
    {
        //echo "<div id=\"BookmarkCallout\">";
        echo '<div class="tab-content"><h2 class="tab">'.lang('bookmarks').'</h2>';
        echo "<p class=\"DashboardCalloutTitle\">";
        echo lang('bookmarks');
        echo "</p>\n";

        echo "<ul>";
        foreach($marks as $mark)
            {
            echo "<li><a href=\"". $mark->url."\">".$mark->title."</a></li>\n";
            }
        echo "</ul>\n";
        echo "</div>\n";
    }




    /**
     * StartRighthandColumn
     * Override this for different behavior or special functionality
     * for the righthand column. Usual use would be a div open tag.
     */
    function StartRighthandColumn()
    {
    	echo '<div class="rightcol">';
    	echo "\n";
    	echo '<div id="admin-tab-container">';
    }

    /**
     * EndRighthandColumn
     * Override this for different behavior or special functionality
     * for the righthand column. Usual use would be a div close tag.
     */
    function EndRighthandColumn()
    {
    	echo "</div>\n</div>\n";
    }



    /**
     * DoRecentPages
     * Setup method for displaying recent pages.
     */
    function DoRecentPages()
    {
    	if (count($this->recent) < 1)
    	   {
    	   	$this->LoadRecentPages();
    	   }
        $this->DisplayRecentPages();
    }

    /**
     * DisplayRecentPages
     * Output Recent Page data. Over-ride this to alter display of Recent Pages information.
     * Recent page information is available in $this->recent, which is an array of RecentPage
     * objects.
     * RecentPage objects contain two useful fields: title and url
     *
     */
    function DisplayRecentPages()
    {
        //echo "<div id=\"RecentPageCallout\">\n";
        echo '<div class="tab-content"><h2 class="tab">'.lang('recentpages').'</h2>';
        echo "<p class=\"DashboardCalloutTitle\">".lang('recentpages')."</p>\n";
        echo "<ul>";
        foreach($this->recent as $pg)
            {
            echo "<li><a href=\"". $pg->url."\">".$pg->title."</a></li>\n";
            }
        echo "</ul>\n";
        echo "</div>\n";
    }

    /**
     * OutputHeaderJavascript
     * This method can be used to dump out any javascript you'd like into the
     * Admin page header. In fact, it can be used to put just about anything into
     * the page header. It's recommended that you leave or copy the javascript
     * below into your own method if you override this -- it's used by the dropdown
     * menu in IE.
     */
    function OutputHeaderJavascript()
    {
		$ssl = isset($_SERVER['https']) && (strtolower($_SERVER['https']) == 'on' || $_SERVER['https'] == 1);
		$config = cmsms()->GetConfig();
	    $ssl = $ssl && isset($config['ssl_url']);
		return cms_get_jquery('',$ssl);
	}

    /**
     * OutputFooterJavascript
     * This method can be used to dump out any javascript you'd like into the
     * Admin page footer.
     * It's recommended that you leave or copy the javascript below into your
     * own method if you override this -- it's used by bookmarks/recent pages tabs.
     */
    function OutputFooterJavascript()
    {
        echo "<script type=\"text/javascript\">BuildTabs('admin-tab-container','admin-tab-header','admin-tab-list');ActivateTab(0,'admin-tab-container','admin-tab-list');</script>";
    }

    /**
     * FixSpaces
     * This method converts spaces into a non-breaking space HTML entity.
     * It's used for making menus that work nicely
     *
     * @param str string to have its spaces converted
     */
    function FixSpaces($str)
    {
		$tmp = preg_replace('/\s+/u',"&nbsp;",$str); // PREG UTF8
		if(!empty($tmp)) return $tmp;
		else return preg_replace('/\s+/',"&nbsp;",$str); // bad UTF8
    }
    /**
     * UnFixSpaces
     * This method converts non-breaking space HTML entities into char(20)s.
     *
     * @param str string to have its spaces converted
     */
    function UnFixSpaces($str)
    {
    	return preg_replace('/&nbsp;/'," ",$str);
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
    function PopulateAdminNavigation($subtitle='')
    {
        if (count($this->menuItems) > 0)
            {
            // we have already created the list
            return;
            }
        $this->subtitle = $subtitle;

debug_buffer('before menu items');
    	    
    	$this->menuItems = array(
    	    // base main menu ---------------------------------------------------------
            'main'=>array('url'=>'index.php','parent'=>-1,
			  'title'=>'CMS',
			  'description'=>'','show_in_menu'=>true),
	    'home'=>array('url'=>'index.php','parent'=>'main',
		    'title'=>$this->FixSpaces(lang('home')),
                    'description'=>'','show_in_menu'=>true),
//	    'dashboard'=>array('url'=>'dashboard.php','parent'=>'main',
//			       'title'=>$this->FixSpaces(lang('dashboard')),
//			       'description'=>'','show_in_menu'=>true),
            'viewsite'=>array('url'=>$this->_viewsite_url,'parent'=>'main',
			      'title'=>$this->FixSpaces(lang('viewsite')),
			      'type'=>'external',
                    'description'=>'','show_in_menu'=>true, 'target'=>'_blank'),
             'logout'=>array('url'=>'logout.php','parent'=>'main',
			     'title'=>$this->FixSpaces(lang('logout')),
			     'description'=>'','show_in_menu'=>true),
            // base content menu ---------------------------------------------------------
            'content'=>array('url'=>'topcontent.php','parent'=>-1,
                    'title'=>$this->FixSpaces(lang('content')),
                    'description'=>lang('contentdescription'),'show_in_menu'=>$this->HasPerm('contentPerms')),
            'pages'=>array('url'=>'listcontent.php','parent'=>'content',
                    'title'=>$this->FixSpaces(lang('pages')),
                    'description'=>lang('pagesdescription'),'show_in_menu'=>$this->HasPerm('pagePerms')),
            'addcontent'=>array('url'=>'addcontent.php','parent'=>'pages',
                    'title'=>$this->FixSpaces(lang('addcontent')),
                    'description'=>lang('addcontent'),'show_in_menu'=>false),
            'editpage'=>array('url'=>'editcontent.php','parent'=>'pages',
                    'title'=>$this->FixSpaces(lang('editpage')),
                    'description'=>lang('editpage'),'show_in_menu'=>false),
        /*    'files'=>array('url'=>'files.php','parent'=>'content',
                    'title'=>$this->FixSpaces(lang('filemanager')),
                    'description'=>lang('filemanagerdescription'),'show_in_menu'=>$this->HasPerm('filePerms')),
          */  'images'=>array('url'=>'imagefiles.php','parent'=>'content',
                    'title'=>$this->FixSpaces(lang('imagemanager')),
                    'description'=>lang('imagemanagerdescription'),'show_in_menu'=>$this->HasPerm('filePerms')),
            'blobs'=>array('url'=>'listhtmlblobs.php','parent'=>'content',
                    'title'=>$this->FixSpaces(lang('htmlblobs')),
                    'description'=>lang('htmlblobdescription'),'show_in_menu'=>$this->HasPerm('htmlPerms')),
            'addhtmlblob'=>array('url'=>'addhtmlblob.php','parent'=>'blobs',
                    'title'=>$this->FixSpaces(lang('addhtmlblob')),
                    'description'=>lang('addhtmlblob'),'show_in_menu'=>false),
            'edithtmlblob'=>array('url'=>'edithtmlblob.php','parent'=>'blobs',
                    'title'=>$this->FixSpaces(lang('edithtmlblob')),
                    'description'=>lang('edithtmlblob'),'show_in_menu'=>false),
             // base layout menu ---------------------------------------------------------
            'layout'=>array('url'=>'toplayout.php','parent'=>-1,
                    'title'=>$this->FixSpaces(lang('layout')),
                    'description'=>lang('layoutdescription'),'show_in_menu'=>$this->HasPerm('layoutPerms')),
            'template'=>array('url'=>'listtemplates.php','parent'=>'layout',
                    'title'=>$this->FixSpaces(lang('templates')),
                    'description'=>lang('templatesdescription'),'show_in_menu'=>$this->HasPerm('templatePerms')),
            'addtemplate'=>array('url'=>'addtemplate.php','parent'=>'template',
                    'title'=>$this->FixSpaces(lang('addtemplate')),
                    'description'=>lang('addtemplate'),'show_in_menu'=>false),
            'edittemplate'=>array('url'=>'edittemplate.php','parent'=>'template',
                    'title'=>$this->FixSpaces(lang('edittemplate')),
                    'description'=>lang('edittemplate'),'show_in_menu'=>false),
            'currentassociations'=>array('url'=>'listcssassoc.php','parent'=>'template',
                    'title'=>$this->FixSpaces(lang('currentassociations')),
                    'description'=>lang('currentassociations'),'show_in_menu'=>false),
            'copytemplate'=>array('url'=>'copyemplate.php','parent'=>'template',
                    'title'=>$this->FixSpaces(lang('copytemplate')),
                    'description'=>lang('copytemplate'),'show_in_menu'=>false),
            'stylesheets'=>array('url'=>'listcss.php','parent'=>'layout',
                    'title'=>$this->FixSpaces(lang('stylesheets')),
                    'description'=>lang('stylesheetsdescription'),
                    'show_in_menu'=>($this->HasPerm('cssPerms') || $this->HasPerm('cssAssocPerms'))),
            'addcss'=>array('url'=>'addcss.php','parent'=>'stylesheets',
                    'title'=>$this->FixSpaces(lang('addstylesheet')),
                    'description'=>lang('addstylesheet'),'show_in_menu'=>false),
            'editcss'=>array('url'=>'editcss.php','parent'=>'stylesheets',
                    'title'=>$this->FixSpaces(lang('editcss')),
                    'description'=>lang('editcss'),'show_in_menu'=>false),
            'templatecss'=>array('url'=>'templatecss.php','parent'=>'stylesheets',
                    'title'=>$this->FixSpaces(lang('templatecss')),
                    'description'=>lang('templatecss'),'show_in_menu'=>false),
             // base user/groups menu ---------------------------------------------------------
            'usersgroups'=>array('url'=>'topusers.php','parent'=>-1,
                    'title'=>$this->FixSpaces(lang('usersgroups')),
                    'description'=>lang('usersgroupsdescription'),'show_in_menu'=>$this->HasPerm('usersGroupsPerms')),
            'users'=>array('url'=>'listusers.php','parent'=>'usersgroups',
                    'title'=>$this->FixSpaces(lang('users')),
                    'description'=>lang('usersdescription'),'show_in_menu'=>$this->HasPerm('userPerms')),
            'adduser'=>array('url'=>'adduser.php','parent'=>'users',
                    'title'=>$this->FixSpaces(lang('adduser')),
                    'description'=>lang('adduser'),'show_in_menu'=>false),
            'edituser'=>array('url'=>'edituser.php','parent'=>'users',
                    'title'=>$this->FixSpaces(lang('edituser')),
                    'description'=>lang('edituser'),'show_in_menu'=>false),
            'groups'=>array('url'=>'listgroups.php','parent'=>'usersgroups',
                    'title'=>$this->FixSpaces(lang('groups')),
                    'description'=>lang('groupsdescription'),'show_in_menu'=>$this->HasPerm('groupPerms')),
            'addgroup'=>array('url'=>'addgroup.php','parent'=>'groups',
                    'title'=>$this->FixSpaces(lang('addgroup')),
                    'description'=>lang('addgroup'),'show_in_menu'=>false),
            'editgroup'=>array('url'=>'editgroup.php','parent'=>'groups',
                    'title'=>$this->FixSpaces(lang('editgroup')),
                    'description'=>lang('editgroup'),'show_in_menu'=>false),
            'groupmembers'=>array('url'=>'changegroupassign.php','parent'=>'usersgroups',
                    'title'=>$this->FixSpaces(lang('groupassignments')),
                    'description'=>lang('groupassignmentdescription'),'show_in_menu'=>$this->HasPerm('groupMemberPerms')),                    
            'groupperms'=>array('url'=>'changegroupperm.php','parent'=>'usersgroups',
                    'title'=>$this->FixSpaces(lang('groupperms')),
                    'description'=>lang('grouppermsdescription'),'show_in_menu'=>$this->HasPerm('groupPermPerms')),                    
             // base extensions menu ---------------------------------------------------------
            'extensions'=>array('url'=>'topextensions.php','parent'=>-1,
                    'title'=>$this->FixSpaces(lang('extensions')),
                    'description'=>lang('extensionsdescription'),'show_in_menu'=>$this->HasPerm('extensionsPerms')),
            'modules'=>array('url'=>'listmodules.php','parent'=>'extensions',
                    'title'=>$this->FixSpaces(lang('modules')),
                    'description'=>lang('moduledescription'),'show_in_menu'=>$this->HasPerm('modulePerms')),
            'tags'=>array('url'=>'listtags.php','parent'=>'extensions',
                    'title'=>$this->FixSpaces(lang('tags')),
			  'description'=>lang('tagdescription'),'show_in_menu'=>$this->HasPerm('taghelpPerms')),
            'usertags'=>array('url'=>'listusertags.php','parent'=>'extensions',
                    'title'=>$this->FixSpaces(lang('usertags')),
                    'description'=>lang('usertagdescription'),'show_in_menu'=>$this->HasPerm('codeBlockPerms')),
            'eventhandlers'=>array('url'=>'eventhandlers.php','parent'=>'extensions',
                    'title'=>$this->FixSpaces(lang('eventhandlers')),
                    'description'=>lang('eventhandlerdescription'),'show_in_menu'=>$this->HasPerm('eventPerms')),
            'editeventhandler'=>array('url'=>'editevent.php','parent'=>'eventhandlers',
                    'title'=>$this->FixSpaces(lang('editeventhandler')),
                    'description'=>lang('editeventshandler'),'show_in_menu'=>false),
            'addusertag'=>array('url'=>'adduserplugin.php','parent'=>'usertags',
                    'title'=>$this->FixSpaces(lang('addusertag')),
                    'description'=>lang('addusertag'),'show_in_menu'=>false),
            'editusertag'=>array('url'=>'edituserplugin.php','parent'=>'usertags',
                    'title'=>$this->FixSpaces(lang('editusertag')),
                    'description'=>lang('editusertag'),'show_in_menu'=>false),
             // base admin menu ---------------------------------------------------------
            'siteadmin'=>array('url'=>'topadmin.php','parent'=>-1,
                    'title'=>$this->FixSpaces(lang('admin')),
                    'description'=>lang('admindescription'),'show_in_menu'=>$this->HasPerm('siteAdminPerms')),
            'siteprefs'=>array('url'=>'siteprefs.php','parent'=>'siteadmin',
                    'title'=>$this->FixSpaces(lang('globalconfig')),
                    'description'=>lang('preferencesdescription'),'show_in_menu'=>$this->HasPerm('sitePrefPerms')),
            'pagedefaults'=>array('url'=>'pagedefaults.php','parent'=>'siteadmin',
                    'title'=>$this->FixSpaces(lang('pagedefaults')),
                    'description'=>lang('pagedefaultsdescription'),'show_in_menu'=>$this->HasPerm('sitePrefPerms')),
	    'systeminfo'=>array('url'=>'systeminfo.php','parent'=>'siteadmin',
				'title'=>$this->FixSpaces(lang('systeminfo')),
				'description'=>lang('systeminfodescription'),
				'show_in_menu'=>$this->HasPerm('adminPerms')),
	    'checksum'=>array('url'=>'checksum.php','parent'=>'siteadmin',
				'title'=>$this->FixSpaces(lang('system_verification')),
				'description'=>lang('checksumdescription'),
				'show_in_menu'=>$this->HasPerm('adminPerms')),
            'adminlog'=>array('url'=>'adminlog.php','parent'=>'siteadmin',
                    'title'=>$this->FixSpaces(lang('adminlog')),
                    'description'=>lang('adminlogdescription'),'show_in_menu'=>$this->HasPerm('adminPerms')),
             // base my prefs menu ---------------------------------------------------------
            'myprefs'=>array('url'=>'topmyprefs.php','parent'=>-1,
			     'title'=>$this->FixSpaces(lang('myprefs')),
			     'description'=>lang('myprefsdescription'),'show_in_menu'=>true),
            'myaccount'=>array('url'=>'edituser.php','parent'=>'myprefs',
                    'title'=>$this->FixSpaces(lang('myaccount')),
                    'description'=>lang('myaccountdescription'),'show_in_menu'=>true),
            'preferences'=>array('url'=>'editprefs.php','parent'=>'myprefs',
                    'title'=>$this->FixSpaces(lang('adminprefs')),
                    'description'=>lang('adminprefsdescription'),'show_in_menu'=>true),
            'managebookmarks'=>array('url'=>'listbookmarks.php','parent'=>'myprefs',
                    'title'=>$this->FixSpaces(lang('managebookmarks')),
                    'description'=>lang('managebookmarksdescription'),'show_in_menu'=>true),
            'addbookmark'=>array('url'=>'addbookmark.php','parent'=>'myprefs',
                    'title'=>$this->FixSpaces(lang('addbookmark')),
                    'description'=>lang('addbookmark'),'show_in_menu'=>false),
            'editbookmark'=>array('url'=>'editbookmark.php','parent'=>'myprefs',
                    'title'=>$this->FixSpaces(lang('editbookmark')),
                    'description'=>lang('editbookmark'),'show_in_menu'=>false),
    	);

debug_buffer('after menu items');


	// slightly cleaner syntax
	$this->menuItems['ecommerce'] = array('url'=>'topecomm.php','parent'=>-1,
					      'title'=>$this->FixSpaces(lang('ecommerce')),
					      'description'=>lang('ecommerce_desc'),
					      'show_in_menu'=>true);
	
	
	// adjust all the urls to include the session key
	foreach( $this->menuItems as $sectionKey => $sectionArray )
	  {
	    if( isset($sectionArray['url']) && 
		(!isset($sectionArray['type']) || $sectionArray['type'] != 'external' ))
	      {
		$url = $this->menuItems[$sectionKey]['url'];
		if( strpos($url,'?') !== FALSE )
		  {
		    $url .= '&';
		  }
		else
		  {
		    $url .= '?';
		  }
		$url .= CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
		$this->menuItems[$sectionKey]['url'] = $url;
	      }
	  }
		
	debug_buffer('before syste modules');

	// add in all of the 'system' modules too
	$gCms = cmsms();
	foreach ($this->menuItems as $sectionKey=>$sectionArray)
	  {
		  $tmpArray = $this->MenuListSectionModules($sectionKey);
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
				  if( ModuleOperations::get_instance()->IsSystemModule($thisModuleKey) )
					  {
						  $this->menuItems[$thisModuleKey]=array('url'=>$thisVal['url'],
																 'parent'=>$sectionKey,
																 'title'=>$this->FixSpaces($thisVal['name']),
																 'description'=>$thisVal['description'],
																 'show_in_menu'=>true);
						  
					  }
			  }
	  }
	
	debug_buffer('before module menu items');

	// add in all of the modules
        foreach ($this->menuItems as $sectionKey=>$sectionArray)
	  {
            $tmpArray = $this->MenuListSectionModules($sectionKey);
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
						       'title'=>$this->FixSpaces($thisVal['name']),
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
	
	debug_buffer('after module menu items');

	// remove any top level items that don't have children
	$parents = array();
	foreach ($this->menuItems as $sectionKey=>$sectionArray)
	  {
	    if( $this->menuItems[$sectionKey]['parent'] == -1 )
	      {
		$parents[] = $sectionKey;
	      }
	  }
	foreach( $parents as $oneparent )
	  {
	    $found = 0;
	    foreach ($this->menuItems as $sectionKey=>$sectionArray)
	      {
		if( $sectionArray['parent'] == $oneparent )
		  {
		    $found = 1;
		    break;
		  }
	      }
	    if( !$found ) unset($this->menuItems[$oneparent]);
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
		  else if (strstr($sectionArray['url'],$this->script) !== FALSE &&
				   (!isset($sectionArray['type']) || $sectionArray['type'] != 'external'))
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
	    if ($this->query != '' && strpos($this->breadcrumbs[$count-1]['url'],'&amp;') === false)
	      {
		$this->query = preg_replace('/\&/','&amp;',$this->query);
		$pos = strpos($this->breadcrumbs[$count-1]['url'],'?');
		$tmp = substr($this->breadcrumbs[$count-1]['url'],0,$pos).'?'.$this->query;
		$this->breadcrumbs[$count-1]['url'] = $tmp;
	      }
	    unset($this->breadcrumbs[$count-1]['url']);
	    if ($this->subtitle != '')
	      {
		$this->breadcrumbs[$count-1]['title'] .=  ': '.$this->subtitle;
	      }
	  }
    }
    
    /**
     *  BackUrl
     *  "Back" Url - link to the next-to-last item in the breadcrumbs
     *  for the back button.
     */
    function BackUrl()
     {
     	$count = count($this->breadcrumbs) - 2;
	$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
     	if ($count > -1)
	  {
     	    $txt = $this->breadcrumbs[$count]['url'];
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
     * DoTopMenu
     * Setup function for displaying the top menu.
     *
     */
    function DoTopMenu()
    {
        $this->DisplayTopMenu();
    }

    /**
     * DisplaySectionPages
     * Shows admin section pages in the specified section, wrapped in a
     * MainMenuItem div. This is used in the top-level section pages.
     *
     * You can override this if you want to change the
     * way it is shown.
     *
     * @param section - section to display
     */
    function DisplaySectionPages($section)
    {
      if (count($this->menuItems) < 1)
	{
	  // menu should be initialized before this gets called.
	  // TODO: try to do initialization.
	  // Problem: current page selection, url, etc?
	  return -1;
	}

      foreach ($this->menuItems[$section]['children'] as $thisChild)
	{
	  $thisItem = $this->menuItems[$thisChild];
	  if (! $thisItem['show_in_menu'] || strlen($thisItem['url']) < 1)
	    {
	      continue;
	    }

	  echo "<div class=\"MainMenuItem\">\n";
	  echo "<a href=\"".$thisItem['url']."\"";
	  if (array_key_exists('target', $thisItem))
	    {
	      echo " target=" . $thisItem['target'];
	    }
	  if ($thisItem['selected'])
	    {
	      echo " class=\"selected\"";
	    }
	  echo ">".$thisItem['title']."</a>\n";
	  if (isset($thisItem['description']) && strlen($thisItem['description']) > 0)
	    {
	      echo "<span class=\"description\">";
	      echo $thisItem['description'];
	      echo "</span>\n";
	    }
	  echo "</div>\n";
        }
    }

    /**
     * HasDisplayableChildren
     * This method returns a boolean, based upon whether the section in question
     * has displayable children.
     *
     * @param section - section to test
     */
     function HasDisplayableChildren($section)
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
     * TopParent
     * This method returns the menu node that is the top-level parent of the node you pass
     * to it.
     *
     * @param section - section (menu tag) to find top-level parent
     */
     function TopParent($section)
     {
     	$next = $section;
		$node = $this->menuItems[$next];
        while ($node['parent'] != -1)
        	{
        	$next = $node['parent'];
        	$node = $this->menuItems[$next];
        	}
        return $next;
     }


    /**
     * ListSectionPages
     * This method presents a nice, human-readable list of admin pages and 
     * modules that are in the specified admin section.
     *
     *
     * @param section - section to display
     */
    function ListSectionPages($section)
    {
        if (! isset($this->menuItems[$section]['children']) || count($this->menuItems[$section]['children']) < 1)
            {
            return;
            }

        if ($this->HasDisplayableChildren($section))
            {
            echo " ".lang('subitems').": ";
            $count = 0;
            foreach($this->menuItems[$section]['children'] as $thisChild)
                {
                $thisItem = $this->menuItems[$thisChild];
                if (! $thisItem['show_in_menu']  || strlen($thisItem['url']) < 1)
                    {
                    continue;
                    }
                if ($count++ > 0)
                    {
                    echo ", ";
                    }
                echo "<a href=\"".$thisItem['url'];
                echo "\">".$thisItem['title']."</a>";
                }
            }
    }



    /**
     * DisplayAllSectionPages
     *
     * Shows all admin section pages and modules. This is used to display the
     * admin "main" page.
     *
     */
    function DisplayAllSectionPages()
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



	function renderMenuSection($section, $depth, $maxdepth)
	{
		if ($maxdepth > 0 && $depth> $maxdepth)
			{
			return;
			}
		if (! $this->menuItems[$section]['show_in_menu'])
			{
			return;
			}
		if (strlen($this->menuItems[$section]['url']) < 1)
		    {
            echo "<li>".$this->menuItems[$section]['title']."</li>";
            return;
            }
		echo "<li><a href=\"";
		echo $this->menuItems[$section]['url'];
		echo "\"";
		if (array_key_exists('target', $this->menuItems[$section]))
			{
			echo " target=" . $this->menuItems[$section]['target'];
			}
		if ($this->menuItems[$section]['selected'])
			{
			echo " class=\"selected\"";
			}
		echo ">";
		echo $this->menuItems[$section]['title'];
		echo "</a>";
		if ($this->HasDisplayableChildren($section))
			{
			echo "<ul>";
			foreach ($this->menuItems[$section]['children'] as $child)
				{
				$this->renderMenuSection($child, $depth+1, $maxdepth);
				}
			echo "</ul>";
			}
		echo "</li>";
		return;
	}


    /**
     * DisplayTopMenu
     * Output Top Menu data. Over-ride this to alter display of the top menu.
     *
     * @param menuItems an array of associated items; each element has a section, title,
     * url, and selection where title and url are strings, and selection is a boolean
     * to indicate this is the current selection. You can use the "section" to trap for
     * javascript links, etc.
     *
     * Cruftily written to only support a depth of two levels
     *
     */
    function DisplayTopMenu()
    {
        echo "<div id=\"TopMenu\"><ul id=\"nav\">\n";
        foreach ($this->menuItems as $key=>$menuItem)
        	{
        	if ($menuItem['parent'] == -1)
        		{
        		$this->renderMenuSection($key, 0, -1);
        		}
        	}
        echo "</ul></div>\n";
    }

    /**
     * DisplayFooter
     * Displays an end-of-page footer.
     */
    function DisplayFooter()
    {
?>
<div id="Footer">
<a href="http://www.cmsmadesimple.org">CMS Made Simple&trade;</a> is Free Software released under the GNU/GPL License
</div>
<?php
    }
    

    /**
     * DisplayDocType
     * If you rewrite the admin section to output pure, beautiful, unadulterated XHTML, you can
     * change the body tag so that it proudly proclaims that there is none of the evil transitional
     * cruft.
     */
    function DisplayDocType()
    {
    	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
    }

    /**
     * DisplayHTMLStartTag
     * Outputs the html open tag. Override at your own risk :)
     */
    function DisplayHTMLStartTag()
    {
		$gCms = cmsms();
		$nls = $gCms->nls;
    	echo (isset($nls['direction']) && $nls['direction'] == 'rtl') ? "<html dir=\"rtl\"\n>" : "<html>\n";
    }
	
	/**
	 * @since 1.9
     * ThemeHeader
     * This method outputs the HEAD section of the html page in the admin Theme section,
     * after OutputHeaderJavascript() and before $addt.
     */
	function ThemeHeader(){
		
		if(file_exists('themes/'. $this->themeName. '/includes/standard.js'))
		{
			return '<script type="text/javascript" src="themes/'. $this->themeName. '/includes/standard.js"></script>'."\n";
		}else{
			return '<script type="text/javascript" src="themes/default/includes/standard.js"></script>'."\n";
		}
	}

    /**
     * DisplayHTMLHeader
     * This method outputs the HEAD section of the html page in the admin section.
     */
    function DisplayHTMLHeader($showielink = false, $addt = '')
    {
		$config = cmsms()->GetConfig();
		$urlext = CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
		$title = get_site_preference('sitename').' - '.$this->title;
		$str = "<head>\r\n";
		$str .=
<<<EOT
	<title>{$title}</title>
	<base href="{$config['admin_url']}/" />
	<meta name="Generator" content="CMS Made Simple - Copyright (C) 2004-11 Ted Kulp. All rights reserved." />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<link rel="stylesheet" type="text/css" href="style.php?{$urlext}" />
EOT;

		if ($showielink) {
			$str .=
<<<EOT
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="style.php?ie=1&{$urlext}" />
<![endif]-->
EOT;
		}
		$str .= "<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->\n";
		$str .= $this->OutputHeaderJavascript()."\n";
		
		ob_start(); 
		$tmp1 = $this->ThemeHeader();
		$tmp2 = ob_get_contents();
		ob_end_clean();
		if( $tmp1 )
		{
			// data is returned
			$str .= $tmp1;
		}
		else
		{
			// assume data is echoed
			$str .= $tmp2;
		}
		
		$str .= $addt."\n";
		$str .= "</head>\n"; // fix to correct and add the end header tag -- JLB
		echo $str;
    }

    /**
     * DisplayBodyTag
     * Outputs the admin page body tag. Leave in the funny text if you want this
     * to work properly.
     */
    function DisplayBodyTag()
    {
        echo "<body##BODYSUBMITSTUFFGOESHERE##>\n";
    }

    
    /**
     * DisplayMainDivStart
     *
     * Used to output the start of the main div that contains the admin page content
     */
    function DisplayMainDivStart()
    {
    	echo "<div id=\"MainContent\">\n";
    }


    /**
     * DisplayMainDivEnd
     *
     * Used to output the end of the main div that contains the admin page content
     */
    function DisplayMainDivEnd()
    {
      echo '<div class="clearb"></div>';
      echo "</div><!-- end MainContent -->\n";
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
        echo "</div><!-- END .MainMenu-->\n";
		if (get_preference($this->userid, 'bookmarks')) {
			echo "</div><!-- END .sections-->\n";
		}
    }


    /**
     * AddToDashboard
     */
    function AddNotification($priority,$module,$html)
    {
      if( !is_array($this->_notificationitems) )
	{
	  $this->_notificationitems = array(array(),array(),array());
	}
      if( $priority < 1 ) $priority = 1;
      if( $priority > 3 ) $priority = 3;

      $this->_notificationitems[$priority-1][] = array($module,$html);
    }

	/**
	 * Display the available notifications
	 *
	 * @param string $priority Priority threshold
	 * @return void
	 */
    function DisplayNotifications($priority=2)
    {
      if( !is_array($this->_notificationitems) ) return;
      
      // count the total number of notifications
      $count=0;
      for( $i = 1; $i <= $priority; $i++ )
      {
        $count += count($this->_notificationitems[$i-1]);
      }
	  
	  // Define that is singular or plural
	  $singular_or_plural = $count;
	  
	  if($singular_or_plural > 1)
	  {
	  $notifications = lang('notifications_to_handle',$count);
	  }
	  else
	  {
	  $notifications = lang('notification_to_handle',$count);
	  } 
	  // remove html tags like <b>2</b>
	  $no_html_tags = preg_replace('/(<\/?)(\w+)([^>]*>)/e','',$notifications);
	  
      echo '<div class="full-Notifications clear">'."\n";
      echo '<div class="Notifications-title">' . $notifications . '</div>'."\n";
	  echo '<div title="'.$no_html_tags.'" id="notifications-display" class="notifications-show" onclick="change(\'notifications-display\', \'notifications-hide\', \'notifications-show\'); change(\'notifications-container\', \'invisible\', \'visible\');"></div>'."\n";
	   
	 /*  echo "<div title='Notifications' class=\"Notifications-arrow\" href=\"#\" onclick=\"togglecollapse('Notifications-area'); return false;\" >""</div>\n";*/
	 
	  echo '<div id="notifications-container" class="invisible">'."\n";
	  
      echo "<ul id=\"Notifications-area\">\n";
      for( $i = 1; $i <= $priority; $i++ )
	{
	  if( count($this->_notificationitems) < $i ) break;
	  if( count($this->_notificationitems[$i-1]) == 0 ) continue;
	  foreach( $this->_notificationitems[$i-1] as $data )
	    {
              echo '<li class="NotificationsItem NotificationsPriority'.$i.'">';
	      echo '<span class="NotificationsItemModuleName">'."\n";
	      echo $data[0]."\n";
	      echo "</span>\n";
	      echo '<span class="NotificationsItemData">'."\n";
	      echo $data[1]."\n";
	      echo "</span>\n";
	      echo '</li>';		  
	    }
	}
      echo "</ul>";
	   echo "</div><!-- notifications-container -->\n";
      echo "</div><!-- full-Notifications -->\n";
	   echo "<div class=\"clearb\">&nbsp;</div>\n";
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
     * DisplayDashboardPageItem
     * Outputs an item on the dashboard page
     *
     * @param itemtype to display, start/end/core/module
	 * @param output to display
     */
    function DisplayDashboardPageItem($item="module", $title='', $content = '')
    {
    	switch ($item) {
    		case "start" : return;
    		case "end" : return;
    		case "core" :
    		case "module" : {
    			echo "<div class='dashboardpageitem'>";
    			echo "<h3>".$title."</h3>";
    			echo $content."</div>";
    		}
    	}
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
    function DisplayImage($imageName, $alt='', $width='', $height='', $class='')
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
    	   	
    	   if (file_exists(dirname(cmsms()->config['root_path'] . '/' . cmsms()->config['admin_dir'] .
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


   /**
	* ShowHeader
	* Outputs the page header title along with a help link to that section in the wiki.
	* 
	* @param title_name - page heading title
	* @param extra_lang_param - extra parameters to pass to lang() (I don't think this parm is needed)
	* @param link_text - Override the text to use for the help link.
	* @param module_help_type - FALSE if this is not a module, 'both' if link to
	*                           both the wiki and module help and 'builtin' if link to to the builtin help
	*/
    function ShowHeader($title_name, $extra_lang_param=array(), $link_text = '', $module_help_type = FALSE)
    {
      $cms = cmsms();
      $config = $cms->GetConfig();             
      $header  = '<div class="pageheader">';
      if (FALSE != $module_help_type)
	{
          $module = '';
	  if( isset($_REQUEST['module']) )
	    {
	      $module = $_REQUEST['module'];
	    }
	  else if( isset($_REQUEST['mact']) )
	    {
	      $tmp = explode(',',$_REQUEST['mact']);
	      $module = $tmp[0];
	    }
	  $icon = "modules/{$module}/images/icon.gif";
	  $path = cms_join_path(cmsms()->config['root_path'],$icon);
	  if( file_exists($path) )
	    {
	      $header .= "<img src=\"{$config['root_url']}/{$icon}\" class=\"itemicon\" alt=\"{$icon}\" />&nbsp;";
	    }
	  $header .= $title_name;
	}
      else
	{
	  $header .= lang($title_name, $extra_lang_param);
	}
      if (FALSE == empty($this->breadcrumbs))
	{
	  $wikiUrl = $config['wiki_url'];
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
		} else {
		// Remove colon and following (I.E. Turn "Edit Page: Title" into "Edit Page")
		$colonLocation = strrchr($title, ':');
		if ($colonLocation !== false)
		  {
		    $title = substr($title,0,strpos($title,':'));
		  }
		// Get the key of the title so we can use the en_US version for the URL
		$title_key = $this->_ArraySearchRecursive($title, $this->menuItems);
		$wikiUrl .= '/'.lang($title_key[0]);
		$help_title = $title;
	      }
	    }
	  if (FALSE == get_preference($this->userid, 'hide_help_links')) {
	    // Clean up URL
	    $wikiUrl = str_replace(' ', '_', $wikiUrl);
	    $wikiUrl = str_replace('&amp;', 'and', $wikiUrl);
	    // Make link to go the translated version of page if lang is not en_US
	    if (FALSE == empty($link_text))
	      {
			  $help_title = $link_text;
	      }
	    else
	      {
			  $help_title = lang('help_external');
	      }

	    $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
	    $image_help = $this->DisplayImage('icons/system/info.gif', lang('module_help'),'','','systemicon');
	    $image_help_external = $this->DisplayImage('icons/system/info-external.gif', lang('wikihelp'),'','','systemicon');		
	    if ('both' == $module_help_type)
	      {
			  $module_help_link = $config['admin_url'].'/listmodules.php'.$urlext.'&amp;action=showmodulehelp&amp;module='.$module_name;
			  $header .= '<span class="helptext"><a href="'.$module_help_link.'" title="'.lang('module_help').'">'.$image_help.'</a> <a href="'.$module_help_link.'">'.lang('module_help').'</a></span>';
	      }
// 	    else
// 	      {
// 			  //$header .= '<span class="helptext"><a href="'.$wikiUrl.'" target="_blank">'.$image_help_external.'</a> <a href="'.$wikiUrl.'" target="_blank">'.lang('help').'</a> ('.lang('new_window').')</span>';
// 	      }
	  }
    }
	  $header .= '</div>';
      return $header;     
    }


    /**
     * _ArraySearchRecursive
     * recursively descend an arbitrarily deep multidimensional
     * array, stopping at the first occurence of scalar $needle.
     * return the path to $needle as an array (list) of keys
     * if not found, return null.
     * (will infinitely recurse on self-referential structures)
     * From: http://us3.php.net/function.array-search
     */
    function _ArraySearchRecursive($needle, $haystack)
    {
       $path = NULL;
       $keys = array_keys($haystack);
       while (!$path && (list($toss,$k)=each($keys))) {
         $v = $haystack[$k];
         if (is_scalar($v)) {
             if ($v===$needle) {
               $path = array($k);
             }
         } elseif (is_array($v)) {
             if ($path=$this->_ArraySearchRecursive( $needle, $v )) {
               array_unshift($path,$k);
             }
         }
       }
       return $path;
    }


    /**
     * ShowError
     * Outputs supplied errors with a link to the wiki for troublshooting.
     *
     * @param errors - array or string of 1 or more errors to be shown
     * @param get_var - Name of the _GET variable that contains the 
     *                  name of the message lang string
     */
    function ShowErrors($errors, $get_var = '')
    {
		$config = cmsms()->GetConfig();
      $wikiUrl = $config['wiki_url'];
      if ($wikiUrl !='none'){
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
      }//wiki check
      $image_error = $this->DisplayImage('icons/system/stop.gif', lang('error'),'','','systemicon');
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
      if (FALSE != is_array($errors))
	{
	  $output .= '<ul class="pageerror">';
	  foreach ($errors as $oneerror)
	    {
	      $output .= '<li>'.$oneerror.'</li>';
	    }
	  $output .= '</ul>';
	}
      else
	{
	  $output  .= $image_error.' '.$errors;
	}
	 if ($wikiUrl !='none'){
      $output .= ' <a href="'.$wikiUrl.'" target="_blank">'.lang('troubleshooting').'</a>';
      }//wiki check
      $output .= '</div></div>';
      return $output;
    }
    
    /**
     * ShowMessage
     * Outputs a page status message
     *
     * @param message - Message to be shown
     * @param get_var - Name of the _GET variable that contains the 
     *                  name of the message lang string
     */
    function ShowMessage($message, $get_var = '')
    {
      $image_done = $this->DisplayImage('icons/system/accept.gif', lang('success'), '','','systemicon');
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
      $output .= '><p class="pagemessage">'.$image_done.' '.$message.'</p></div>';
      return $output;
    }
    
	/**
	 * Based on the current user's prefernces, get the current theme object.
	 */
	static function &GetThemeObject()
	{
		$gCms = cmsms();
		$config = $gCms->GetConfig();
		$themeName = get_preference(get_userid(), 'admintheme', 'default');
		$themeObjectName = $themeName."Theme";
		$userid = get_userid();
	
		if (file_exists(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.$config['admin_dir']."/themes/${themeName}/${themeObjectName}.php"))
		{
			include(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.$config['admin_dir']."/themes/${themeName}/${themeObjectName}.php");
			$themeObject = new $themeObjectName($gCms, $userid, $themeName);
		}
		else
		{
			$themeObject = new AdminTheme($gCms, $userid, $themeName);
		}

		$gCms->variables['admintheme'] = $themeObject;
		
		return $themeObject;
	
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
	function GetAdminPageDropdown($name,$selected)
	{
	  $opts = array();
	  $opts[ucfirst(lang('none'))] = '';

	  $depth = 0;
	  foreach( $this->menuItems as $sectionKey=>$menuItem )
	    {
	      if( $menuItem['parent'] != -1 )
		{
		  continue;
		}
	      if( !$menuItem['show_in_menu'] || strlen($menuItem['url']) < 1 )
		{
		  continue;
		}
	      
	      $opts[$menuItem['title']] = $menuItem['url'];

	      if( is_array($menuItem['children']) && 
		  count($menuItem['children']) )
		{
		  foreach( $menuItem['children'] as $thisChild )
		    {
		      if( $thisChild == 'home' || $thisChild == 'logout' ||
			  $thisChild == 'viewsite')
			{
			  continue;
			}

		      $menuChild = $this->menuItems[$thisChild];
		      if( !$menuChild['show_in_menu'] || strlen($menuChild['url']) < 1 )
			{
			  continue;
			}

		      $opts['&nbsp;&nbsp;'.$menuChild['title']] = cms_htmlentities($menuChild['url']);
		    }
		}
	    }

	  $output = '<select name="'.$name.'">';
	  foreach( $opts as $key => $value )
	    {
	      if( $value == $selected )
		{
		  $output .= sprintf("<option selected=\"selected\" value=\"%s\">%s</option>",
				     $value,$key);
		}
	      else
		{
		  $output .= sprintf("<option value=\"%s\">%s</option>",
				 $value,$key);
		}
	    }
	  $output .= '</select>';
	  return $output;
	}
}

# vim:ts=4 sw=4 noet
?>
