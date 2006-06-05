<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (tedkulp@users.sf.net)
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
 * Class for Admin Theme
 *
 * @package CMS
 */
class AdminTheme
{

    /**
     * CMS handle
     */
    var $cms;

	/**
	 * Title
	 */
	var $title;

    /**
     * Subtitle, for use in breadcrumb trails
     */
    var $subtitle;

	/**
	 * Url
	 */
	var $url;
	
    /**
	 * Script
	 */
	var $script;

	/**
	 * Query String, for use in breadcrumb trails
	 */
	var $query;

    /**
     * Aggregation of modules by section
     */
    var $modulesBySection;

    /**
     * count of modules in each section
     */
    var $sectionCount;

    /**
     * Aggregate Permissions
     */
    var $perms;

    /**
     * Recent Page List
     */
    var $recent;

    /**
     * Current Active User
     */
    var $user;

    /**
     * Admin Section Menu cache
     */
    var $menuItems;

    /**
     * Admin Section Image cache
     */
    var $imageLink;

    /**
     * Theme Name
     */
    var $themeName;

    /**
     * Breadcrumbs Array
     */
    var $breadcrumbs;

	/**
	 * Generic constructor.  Runs the SetInitialValues fuction.
	 */
	function AdminTheme($cms, $userid, $themeName)
	{
		$this->SetInitialValues($cms, $userid, $themeName);
	}

	/**
	 * Sets object to some sane initial values
	 */
	function SetInitialValues($cms, $userid, $themeName)
	{
		$this->title = '';
		$this->subtitle = '';
		$this->cms = $cms;
		$this->url = $_SERVER['SCRIPT_NAME'];
		$this->query = (isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:'');
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
    function MenuListSectionModules($section)
    {
    	$modList = array();
        if (isset($this->sectionCount[$section]) && $this->sectionCount[$section] > 0)
            {
            foreach($this->modulesBySection[$section] as $sectionModule)
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
     * SetModuleAdminInterfaces
     *
     * This function sets up data structures to place modules in the proper Admin sections
     * for display on section pages and menus.
     *
     */
    function SetModuleAdminInterfaces()
    {
    	# Are there any modules with an admin interface?
        $cmsmodules = $this->cms->modules;
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
                if (! isset($this->sectionCount[$section]))
                    {
                    $this->sectionCount[$section] = 0;
                    }
                $this->modulesBySection[$section][$this->sectionCount[$section]]['key'] = $key;
                if ($cmsmodules[$key]['object']->GetFriendlyName() != '')
                    {
                    $this->modulesBySection[$section][$this->sectionCount[$section]]['name'] =
                       $cmsmodules[$key]['object']->GetFriendlyName();
                    }
                else
                    {
                    $this->modulesBySection[$section][$this->sectionCount[$section]]['name'] = $key;
                    }
                if ($cmsmodules[$key]['object']->GetAdminDescription() != '')
                    {
                    $this->modulesBySection[$section][$this->sectionCount[$section]]['description'] =
                        $cmsmodules[$key]['object']->GetAdminDescription();
                    }
                else
                    {
                    $this->modulesBySection[$section][$this->sectionCount[$section]]['description'] = "";
                    }
                $this->sectionCount[$section]++;
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
     */
    function SetAggregatePermissions()
    {
        # Content Permissions
        $this->perms['htmlPerms'] = check_permission($this->userid, 'Add Global Content Blocks') |
                check_permission($this->userid, 'Modify Global Content Blocks') |
                check_permission($this->userid, 'Delete Global Content Blocks');
        $thisUserBlobs = HtmlBlobOperations::AuthorBlobs($this->userid);
        if (count($thisUserBlobs) > 0)
            {
            $this->perms['htmlPerms'] = true;
            }
        $this->perms['pagePerms'] = check_permission($this->userid, 'Modify Any Page') |
                check_permission($this->userid, 'Add Pages') |
                check_permission($this->userid, 'Remove Pages');
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
        $this->perms['extensionsPerms'] = $this->perms['codeBlockPerms'] |
            $this->perms['modulePerms'] |
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
            array_push($this->recent, $rp);
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
        $marks = array_reverse(BookmarkOperations::LoadBookmarks($this->userid));
        $tmpMark = new Bookmark();
        $tmpMark->title = lang('addbookmark');
        $tmpMark->url = 'makebookmark.php?title='. urlencode($this->title);
        array_push($marks,$tmpMark);
        $marks = array_reverse($marks);
        $tmpMark = new Bookmark();
        $tmpMark->title = lang('managebookmarks');
        $tmpMark->url = 'listbookmarks.php';
        array_push($marks,$tmpMark);
        $this->DisplayBookmarks($marks);
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
?>
<script type="text/javascript">
<!-- Needed for correct display in IE only -->
<!--
	cssHover = function() {
		var sfEls = document.getElementById("nav").getElementsByTagName("LI");
		for (var i=0; i<sfEls.length; i++) {
			sfEls[i].onmouseover=function() {
				this.className+=" cssHover";
			}
			sfEls[i].onmouseout=function() {
				this.className=this.className.replace(new RegExp(" cssHover\\b"), "");
			}
		}
	}
	if (window.attachEvent) window.attachEvent("onload", cssHover);
-->
</script>
<?php
        echo "<script type=\"text/javascript\" src=\"";
        echo $this->cms->config['root_url'];
        echo "/lib/dynamic_tabs/tabs.js\"></script>\n";
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
    	return preg_replace('/\s+/',"&nbsp;",$str);
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
    	    
    	$this->menuItems = array(
    	    // base main menu ---------------------------------------------------------
            'main'=>array('url'=>'index.php','parent'=>-1,
                    'title'=>$this->FixSpaces(lang('main')),
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
            'files'=>array('url'=>'files.php','parent'=>'content',
                    'title'=>$this->FixSpaces(lang('filemanager')),
                    'description'=>lang('filemanagerdescription'),'show_in_menu'=>$this->HasPerm('filePerms')),
            'images'=>array('url'=>'imagefiles.php','parent'=>'content',
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
                    'description'=>lang('tagdescription'),'show_in_menu'=>true),
            'eventhandlers'=>array('url'=>'eventhandlers.php','parent'=>'extensions',
                    'title'=>$this->FixSpaces(lang('eventhandlers')),
                    'description'=>lang('eventhandlerdescription'),'show_in_menu'=>true),
            'usertags'=>array('url'=>'listusertags.php','parent'=>'extensions',
                    'title'=>$this->FixSpaces(lang('usertags')),
                    'description'=>lang('usertagdescription'),'show_in_menu'=>$this->HasPerm('codeBlockPerms')),
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
             // base view site menu ---------------------------------------------------------
            'viewsite'=>array('url'=>'../index.php','parent'=>-1,
                    'title'=>$this->FixSpaces(lang('viewsite')),
                    'description'=>'','show_in_menu'=>true, 'target'=>'_blank'),
             // base logout menu ---------------------------------------------------------
             'logout'=>array('url'=>'logout.php','parent'=>-1,
                    'title'=>$this->FixSpaces(lang('logout')),
                    'description'=>'','show_in_menu'=>true),
    	);

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
            		array_push($this->menuItems[$sectionKey]['children'], $subsectionKey);
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
				    array_push($this->breadcrumbs, array('title'=>$menuItem['title'], 'url'=>$menuItem['url']));
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
     *  BackUrl
     *  "Back" Url - link to the next-to-last item in the breadcrumbs
     *  for the back button.
     */
     function BackUrl()
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
<a href="http://www.cmsmadesimple.org">CMS Made Simple</a> is Free Software released under the GNU/GPL License
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
    	echo "<html>\n";
    }

    /**
     * DisplayHTMLHeader
     * This method outputs the HEAD section of the html page in the admin section.
     */
    function DisplayHTMLHeader($showielink = false)
    {
		global $gCms;
		$config =& $gCms->GetConfig();
?><head>
<meta name="Generator" content="CMS Made Simple - Copyright (C) 2004-6 Ted Kulp. All rights reserved." />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title><?php echo $this->title ?></title>
<link rel="stylesheet" type="text/css" href="style.php" />
<?php
	if ($showielink) {
?>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="style.php?ie=1" />
<![endif]-->
<?php
	}
?>
<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->
<?php $this->OutputHeaderJavascript(); ?>
<base href="<?php echo $config['root_url'] . '/' . $config['admin_dir'] . '/'; ?>" />
</head>
<?php
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
        echo "</div>\n";
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
    	   	
    	   if (file_exists(dirname($this->cms->config['root_path'] . '/' . $this->cms->config['admin_dir'] .
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
     * @param title - page heading title
     */
    function ShowHeader($title_name, $extra_lang_param=array())
    {
        $wikiUrl = $this->cms->config['wiki_url'];
        // Include English translation of titles. (Can't find better way to get them)
        $dirname = dirname(__FILE__);
        include($dirname.'/../../'.$this->cms->config['admin_dir'].'/lang/en_US/admin.inc.php');
		// Check if this is a module
        if (FALSE == empty($_GET['module'])  || FALSE == empty($_REQUEST['mact'])) {
			$isModule = TRUE;
		} else {
			$isModule = FALSE;
		}
        foreach ($this->breadcrumbs AS $key => $value)
        {
            $title = $value['title'];
            // If this is a module and the last part of the breadcrumbs
            if (TRUE == $isModule && TRUE == empty($this->breadcrumbs[$key + 1]))
            {
				if (FALSE == empty($_GET['module'])) {
					$module_name = $_GET['module'];
				} else {
					$module_name = substr($_REQUEST['mact'], 0, strpos($_REQUEST['mact'], ','));
				}
				// Turn ModuleName into _Module_Name
				$moduleName =  preg_replace('/([A-Z])/', "_$1", $module_name);
				$moduleName =  preg_replace('/_([A-Z])_/', "$1", $moduleName);
				if ($moduleName{0} == '_') {
					$wikiUrl .= '/'.substr($moduleName, 1);
				} else {
					$wikiUrl .= '/'.$moduleName;
				}
            } else {
                // Remove colon and following (I.E. Turn "Edit Page: Title" into "Edit Page")
				$colonLocation = strrchr($title, ':');
				if ($colonLocation !== false)
				{
					$title = substr($title,0,strpos($title,':'));
				}
                // Get the key of the title so we can use the en_US version for the URL
                $title_key = $this->_ArraySearchRecursive($title, $this->menuItems);
                $wikiUrl .= '/'.$lang['admin'][$title_key[0]];
            }
        }
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

        $header  = '<p class="pageheader">';
        if (TRUE == $isModule) {
            $header .= $title_name;        
        } else {
            $header .= lang($title_name, $extra_lang_param);
        }  
        $image_help = $this->DisplayImage('icons/system/info.gif', lang('help'),'','','systemicon');
        $header .= ' <a href="'.$wikiUrl.'" target="_blank">'.$image_help.'</a></p>';
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
	 */
	function ShowErrors($errors)
	{
		$wikiUrl = $this->cms->config['wiki_url'];

		if (FALSE == empty($_REQUEST['module'])  || FALSE == empty($_REQUEST['mact'])) {
			if (FALSE == empty($_REQUEST['module'])) {
				$wikiUrl .= '/'.$_REQUEST['module'];
			} else {
					$wikiUrl .= '/'.substr($_REQUEST['mact'], 0, strpos($_REQUEST['mact'], ','));
			}
		}
		$wikiUrl .= '/Troubleshooting';
		$image_error = $this->DisplayImage('icons/system/stop.gif', '','','','systemicon');
		$output  = '<div class="pageerrorcontainer">';
		if (FALSE != is_array($errors)) {
			$output .= '<ul class="pageerror">';
			foreach ($errors as $oneerror)
			{
				$output .= '<li>'.$oneerror.'</li>';
			}
			$output .= '</ul>';
		} else {
			$image_error = $this->DisplayImage('icons/system/stop.gif', '','','','systemicon');
			$output  .= $image_error.' '.$errors;
		}
		$output .= ' <a href="'.$wikiUrl.'" target="_blank">'.lang('troubleshooting').'</a></div>';

		return $output;
	}

    /**
     * ShowMessage
     * Outputs a page status message
     *
     * @param message - Message to be shown
     */
    function ShowMessage($message)
    {
		$image_done = $this->DisplayImage('icons/system/accept.gif', '','','','systemicon');
		return '<div class="pagemcontainer"><p class="pagemessage">'.$image_done.' '.$message.'</p></div>';
	}
}

# vim:ts=4 sw=4 noet
?>
