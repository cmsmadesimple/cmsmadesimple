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
	 * Url
	 */
	var $url;

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
	 * Generic constructor.  Runs the SetInitialValues fuction.
	 */
	function AdminTheme($cms, $userid)
	{
		$this->SetInitialValues($cms, $userid);
	}

	/**
	 * Sets object to some sane initial values
	 */
	function SetInitialValues($cms, $userid)
	{
		$this->title = '';
		$this->cms = $cms;
		$this->url = $_SERVER['REQUEST_URI'];
		$this->userid = $userid;
		$this->perms = array();
		$this->recent = array();
		$this->menuItems = array();
		$this->modulesBySection = array();
		$this->sectionCount = array();
        $this->SetModuleAdminInterfaces();
        $this->SetAggregatePermissions();
	}

    /**
     * Send admin page headers.
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
    

    function SetModuleAdminInterfaces()
    {
    	# Are there any modules with an admin interface?
        $cmsmodules = $this->cms->modules;
        foreach ($cmsmodules as $key=>$value)
            {
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
    
    function SetAggregatePermissions()
    {
        # Content Permissions
        $this->perms['pagePerms'] = check_permission($this->userid, 'Modify Any Page') |
                check_permission($this->userid, 'Add Pages') |
                check_permission($this->userid, 'Remove Pages');
        $this->perms['htmlPerms'] = check_permission($this->userid, 'Add Html Blobs') |
                check_permission($this->userid, 'Modify Html Blobs') |
                check_permission($this->userid, 'Delete Html Blobs');
        $this->perms['contentPerms'] = $this->perms['pagePerms'] |
                $this->perms['htmlPerms'] |
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


        # extensions
        $this->perms['codeBlockPerms'] = check_permission($this->userid, 'Modify Code Blocks');
        $this->perms['modulePerms'] = check_permission($this->userid, 'Modify Modules');
        $this->perms['extensionsPerms'] = $this->perms['codeBlockPerms'] |
            $this->perms['modulePerms'] |
            (isset($this->sectionCount['extensions']) && $this->sectionCount['extensions'] > 0);
    }
    
    /**
     * Check aggregate permission
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
     * Display Section Modules
     *
     * @param section - section to display
     */
    function DisplaySectionModules($section)
    {
        if (isset($this->sectionCount[$section]) && $this->sectionCount[$section] > 0)
        {
            foreach($this->modulesBySection[$section] as $sectionModule)
                {
                echo "<div class=\"MainMenuItem\">\n";
                echo "<a href=\"moduleinterface.php?module=";
                echo $sectionModule['key']."\">".$sectionModule['name']."</a>\n";
                if ($sectionModule['description'] != '')
                    {
                    echo '<span class="description">'.$sectionModule['description'].'</span>';
                    }
                echo "</div>\n";
                }
        }
    }

    /**
     * List Section Modules
     *
     * @param section - section to display
     * @param firstSubitem - boolean, is this the first subitem to be displayed in the list?
     */
    function ListSectionModules($section, $firstSubitem=false)
    {
        if (isset($this->sectionCount[$section]) && $this->sectionCount[$section] > 0)
            {
            if ($firstSubitem)
                {
                echo " ".lang('subitems').": ";
                }
            foreach($this->modulesBySection[$section] as $sectionModule)
                {
                echo ", <a href=\"moduleinterface.php?module=".$sectionModule['key'];
                echo "\">".$sectionModule['name']."</a>";
                }
            }
    }

    /**
     * Menu List Section Modules
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
                }
            }
        return $modList;
    }


    function LoadRecentPages()
    {
        require_once("../lib/classes/class.recentpage.inc.php");
        $this->recent = RecentPageOperations::LoadRecentPages($this->userid);
    }

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
     * Setup method for displaying admin bookmarks
     */
    function DoBookmarks()
    {
        $marks = BookmarkOperations::LoadBookmarks($this->userid);
        if (count($marks) > 0)
            {
            $tmpMark = new Bookmark();
            $tmpMark->title = lang('managebookmarks');
            $tmpMark->url = 'listbookmarks.php';
            }
        else
            {
            $tmpMark = new Bookmark();
            $tmpMark->title = lang('addbookmark');
            $tmpMark->url = 'makebookmark.php?title='. urlencode($this->title);
            }
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
     * Override this for different behavior or special functionality
     * for the righthand column. Usual use would be a div close tag.
     */
    function EndRighthandColumn()
    {
    	echo "</div>\n</div>\n";
    }



    /**
     * setup method for displaying recent pages
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
<?php    }

    function OutputFooterJavascript()
    {
        echo "<script type=\"text/javascript\" src=\"";
        echo $this->cms->config['root_url'];
        echo "/lib/dynamic_tabs/tabs.js\"></script>\n";
        echo "<script type=\"text/javascript\">BuildTabs('admin-tab-container','admin-tab-header','admin-tab-list');ActivateTab(0,'admin-tab-container','admin-tab-list');</script>";
    }


    function FixSpaces($str)
    {
    	return preg_replace('/\s+/',"&nbsp;",$str);
    }

    /**
     * Populates array containing Navigation Taxonomy
     *
     */
    function PopulateAdminNavigation($cms_top='',$url,$query)
    {
        if (count($this->menuItems) > 0)
            {
            // we have already created the list
            return;
            }
    	if (strpos( $url, '/' ) === false)
    	   {
    	   	$script = $url;
    	   }
        else
            {
            $script = array_pop(explode('/',$url));
    	    }
        $count = 0;
    	$this->menuItems['main'][$count]['url'] = 'index.php';
    	$this->menuItems['main'][$count]['title'] = $this->FixSpaces(lang('main'));
        $this->menuItems['main'][$count]['description'] = '';
    	$this->menuItems['main'][$count++]['selected'] = ($cms_top=='main');

    	$count = 0;
        $this->menuItems['content'][$count]['url'] = 'topcontent.php';
    	$this->menuItems['content'][$count]['title'] = $this->FixSpaces(lang('content'));
    	$this->menuItems['content'][$count]['description'] = lang('contentdescription');
        $this->menuItems['content'][$count++]['selected'] = ($cms_top=='content');

        $this->menuItems['content'][$count]['url'] = 'listcontent.php';
        $this->menuItems['content'][$count]['title'] = $this->FixSpaces(lang('pages'));
        $this->menuItems['content'][$count]['description'] = lang('pagesdescription');
        $this->menuItems['content'][$count++]['selected'] = ($script=='listcontent.php');

        if ($this->HasPerm('filePerms'))
        {
            $this->menuItems['content'][$count]['url'] = 'files.php';
            $this->menuItems['content'][$count]['title'] = $this->FixSpaces(lang('filemanager'));
            $this->menuItems['content'][$count]['description'] = lang('filemanagerdescription');
            $this->menuItems['content'][$count++]['selected'] = ($script=='files.php');

            $this->menuItems['content'][$count]['url'] = 'imagefiles.php';
            $this->menuItems['content'][$count]['title'] = $this->FixSpaces(lang('imagemanager'));
            $this->menuItems['content'][$count]['description'] = lang('imagemanagerdescription');
            $this->menuItems['content'][$count++]['selected'] = ($script=='imagefile.php');
        }

        $tmpArray = $this->MenuListSectionModules('content');
        foreach ($tmpArray as $thisKey=>$thisVal)
            {
            $this->menuItems['content'][$count]['url'] = $thisVal['url'];
            $this->menuItems['content'][$count]['title'] = $this->FixSpaces($thisKey);
            $this->menuItems['content'][$count]['description'] = $thisVal['description'];
            $this->menuItems['content'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
            }

        if ($this->HasPerm('layoutPerms'))
        {
            $count = 0;
            $this->menuItems['layout'][$count]['url'] = 'toplayout.php';
            $this->menuItems['layout'][$count]['title'] = $this->FixSpaces(lang('layout'));
            $this->menuItems['layout'][$count]['description'] = lang('layoutdescription');
            $this->menuItems['layout'][$count++]['selected'] = ($cms_top=='layout');
            if ($this->HasPerm('templatePerms'))
            {
                $this->menuItems['layout'][$count]['url'] = 'listtemplates.php';
                $this->menuItems['layout'][$count]['title'] = $this->FixSpaces(lang('templates'));
                $this->menuItems['layout'][$count]['description'] = lang('templatesdescription');
                $this->menuItems['layout'][$count++]['selected'] = ($script=='listtemplates.php');;
            }
            if ($this->HasPerm('cssPerms') || $themeObject->HasPerm('cssAssocPerms'))
            {
                $this->menuItems['layout'][$count]['url'] = 'listcss.php';
                $this->menuItems['layout'][$count]['title'] = $this->FixSpaces(lang('stylesheets'));
                $this->menuItems['layout'][$count]['description'] = lang('stylesheetsdescription');
                $this->menuItems['layout'][$count++]['selected'] = ($script=='listcss.php');;
            }
            if ($this->HasPerm('htmlPerms'))
            {
                $this->menuItems['layout'][$count]['url'] = 'listhtmlblobs.php';
                $this->menuItems['layout'][$count]['title'] = $this->FixSpaces(lang('htmlblobs'));
                $this->menuItems['layout'][$count]['description'] = lang('htmlblobdescription');
                $this->menuItems['layout'][$count++]['selected'] = ($script=='listhtmlblobs.php');
            }

            $tmpArray = $this->MenuListSectionModules('layout');
            foreach ($tmpArray as $thisKey=>$thisVal)
                {
                $this->menuItems['layout'][$count]['url'] = $thisVal['url'];
                $this->menuItems['layout'][$count]['title'] = $this->FixSpaces($thisKey);
                $this->menuItems['layout'][$count]['description'] = $thisVal['description'];
                $this->menuItems['layout'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
                }
        }
        if ($this->HasPerm('usersGroupsPerms'))
        {
            $count = 0;
            $this->menuItems['usersgroups'][$count]['url'] = 'topusers.php';
            $this->menuItems['usersgroups'][$count]['title'] = $this->FixSpaces(lang('usersgroups'));
            $this->menuItems['usersgroups'][$count]['description'] = lang('usersgroupsdescription');
            $this->menuItems['usersgroups'][$count++]['selected'] = ($cms_top=='usersgroups');
            if ($this->HasPerm('userPerms'))
            {
                $this->menuItems['usersgroups'][$count]['url'] = 'listusers.php';
                $this->menuItems['usersgroups'][$count]['title'] = $this->FixSpaces(lang('users'));
                $this->menuItems['usersgroups'][$count]['description'] = lang('usersdescription');
                $this->menuItems['usersgroups'][$count++]['selected'] = ($script=='listusers.php');;
            }
            if ($this->HasPerm('groupPerms'))
            {
                $this->menuItems['usersgroups'][$count]['url'] = 'listgroups.php';
                $this->menuItems['usersgroups'][$count]['title'] = $this->FixSpaces(lang('groups'));
                $this->menuItems['usersgroups'][$count]['description'] = lang('groupsdescription');
                $this->menuItems['usersgroups'][$count++]['selected'] = ($script=='listgroups.php');;
            }
            if ($this->HasPerm('groupMemberPerms'))
            {
                $this->menuItems['usersgroups'][$count]['url'] = 'changegroupassign.php';
                $this->menuItems['usersgroups'][$count]['title'] = $this->FixSpaces(lang('groupassignments'));
                $this->menuItems['usersgroups'][$count]['description'] = lang('groupassignmentdescription');
                $this->menuItems['usersgroups'][$count++]['selected'] = ($script=='changegroupassign.php');;
            }
            if ($this->HasPerm('groupPermPerms'))
            {
                $this->menuItems['usersgroups'][$count]['url'] = 'changegroupperm.php';
                $this->menuItems['usersgroups'][$count]['title'] = $this->FixSpaces(lang('groupperms'));
                $this->menuItems['usersgroups'][$count]['description'] = lang('grouppermsdescription');
                $this->menuItems['usersgroups'][$count++]['selected'] = ($script=='changegroupperm.php');;
            }
            $tmpArray = $this->MenuListSectionModules('usersgroups');
            foreach ($tmpArray as $thisKey=>$thisVal)
                {
                $this->menuItems['usersgroups'][$count]['url'] = $thisVal['url'];
                $this->menuItems['usersgroups'][$count]['title'] = $this->FixSpaces($thisKey);
                $this->menuItems['usersgroups'][$count]['description'] = $thisVal['description'];
                $this->menuItems['usersgroups'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
                }

        }
        if ($this->HasPerm('extensionsPerms'))
        {
            $count = 0;
            $this->menuItems['extensions'][$count]['url'] = 'topextensions.php';
            $this->menuItems['extensions'][$count]['title'] = $this->FixSpaces(lang('extensions'));
            $this->menuItems['extensions'][$count]['description'] = lang('extensionsdescription');
            $this->menuItems['extensions'][$count++]['selected'] = ($cms_top=='extensions');
            if ($this->HasPerm('modulePerms'))
            {
                $this->menuItems['extensions'][$count]['url'] = 'listmodules.php';
                $this->menuItems['extensions'][$count]['title'] = $this->FixSpaces(lang('modules'));
                $this->menuItems['extensions'][$count]['description'] = lang('moduledescription');
                $this->menuItems['extensions'][$count++]['selected'] = ($script=='listmodules.php');;
            }
            $this->menuItems['extensions'][$count]['url'] = 'listtags.php';
            $this->menuItems['extensions'][$count]['title'] = $this->FixSpaces(lang('tags'));
            $this->menuItems['extensions'][$count]['description'] = lang('tagdescription');
            $this->menuItems['extensions'][$count++]['selected'] = ($script=='listtags.php');
            if ($this->HasPerm('codeBlockPerms'))
            {
                $this->menuItems['extensions'][$count]['url'] = 'listusertags.php';
                $this->menuItems['extensions'][$count]['title'] = $this->FixSpaces(lang('usertags'));
                $this->menuItems['extensions'][$count]['description'] = lang('usertagdescription');
                $this->menuItems['extensions'][$count++]['selected'] = ($script=='listusertags.php');;
            }
            $tmpArray = $this->MenuListSectionModules('extensions');
            foreach ($tmpArray as $thisKey=>$thisVal)
                {
                $this->menuItems['extensions'][$count]['url'] = $thisVal['url'];
                $this->menuItems['extensions'][$count]['title'] = $this->FixSpaces($thisKey);
                $this->menuItems['extensions'][$count]['description'] = $thisVal['description'];
                $this->menuItems['extensions'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
                }
        }
        $count = 0;
    	$this->menuItems['preferences'][$count]['url'] = 'editprefs.php';
    	$this->menuItems['preferences'][$count]['title'] = $this->FixSpaces(lang('preferences'));
        $this->menuItems['preferences'][$count]['description'] = lang('preferencesdescription');
        $this->menuItems['preferences'][$count++]['selected'] = ($cms_top=='preferences');
        $tmpArray = $this->MenuListSectionModules('files');
        foreach ($tmpArray as $thisKey=>$thisVal)
            {
            $this->menuItems['preferences'][$count]['url'] = $thisVal['url'];
            $this->menuItems['preferences'][$count]['title'] = $this->FixSpaces($thisKey);
            $this->menuItems['preferences'][$count]['description'] = $thisVal['description'];
            $this->menuItems['preferences'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
            }


        if ($this->HasPerm('adminPerms'))
        {
            $count = 0;
            $this->menuItems['admin'][$count]['url'] = 'topadmin.php';
            $this->menuItems['admin'][$count]['title'] = $this->FixSpaces(lang('admin'));
            $this->menuItems['admin'][$count]['description'] = lang('admindescription');
            $this->menuItems['admin'][$count++]['selected'] = ($cms_top=='admin');
            if ($this->HasPerm('sitePrefPerms'))
            {
                $this->menuItems['admin'][$count]['url'] = 'siteprefs.php';
                $this->menuItems['admin'][$count]['title'] = $this->FixSpaces(lang('preferences'));
                $this->menuItems['admin'][$count]['description'] = lang('preferencesdescription');
                $this->menuItems['admin'][$count++]['selected'] = ($script=='siteprefs.php');;
            }
            $this->menuItems['admin'][$count]['url'] = 'adminlog.php';
            $this->menuItems['admin'][$count]['title'] = $this->FixSpaces(lang('adminlog'));
            $this->menuItems['admin'][$count]['description'] = lang('adminlogdescription');
            $this->menuItems['admin'][$count++]['selected'] = ($script=='adminlog.php');;
            $tmpArray = $this->MenuListSectionModules('admin');
            foreach ($tmpArray as $thisKey=>$thisVal)
                {
                $this->menuItems['admin'][$count]['url'] = $thisVal['url'];
                $this->menuItems['admin'][$count]['title'] = $this->FixSpaces($thisKey);
                $this->menuItems['admin'][$count]['description'] = $thisVal['description'];
                $this->menuItems['admin'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
                }
        }
        $count = 0;
    	$this->menuItems['viewsite'][$count]['url'] = '../index.php';
    	$this->menuItems['viewsite'][$count]['title'] = $this->FixSpaces(lang('viewsite'));
        $this->menuItems['viewsite'][$count]['description'] = '';
        $this->menuItems['viewsite'][$count]['selected'] = false;

    	$this->menuItems['logout'][$count]['url'] = 'logout.php';
    	$this->menuItems['logout'][$count]['title'] = $this->FixSpaces(lang('logout'));
        $this->menuItems['logout'][$count]['description'] = '';
        $this->menuItems['logout'][$count]['selected'] = false;

    	$this->menuItems['bookmarks'][$count]['url'] = 'makebookmark.php?title='.urlencode($this->title);
    	$this->menuItems['bookmarks'][$count]['title'] = '[+]';
        $this->menuItems['bookmarks'][$count]['description'] = '';
    	$this->menuItems['bookmarks'][$count++]['selected'] = ($cms_top=='bookmarks');
        $tmpArray = $this->MenuListSectionModules('bookmarks');
        foreach ($tmpArray as $thisKey=>$thisVal)
            {
            $this->menuItems['bookmarks'][$count]['url'] = $thisVal['url'];
            $this->menuItems['bookmarks'][$count]['title'] = $this->FixSpaces($thisKey);
            $this->menuItems['bookmarks'][$count]['description'] = $thisVal['description'];
            $this->menuItems['bookmarks'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
            }
        }


    function DoTopMenu($cms_top='',$url,$query)
    {
    	$this->PopulateAdminNavigation($cms_top,$url,$query);
        $this->DisplayTopMenu();
    }

    /**
     * Display Section Pages - shows admin section pages
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
        $count = 0;
        foreach ($this->menuItems[$section] as $thisItem)
            {
            if ($count++ < 1)
                {
                // skip the first item
                continue;
                }
            echo "<div class=\"MainMenuItem\">\n";
            echo "<a href=\"".$thisItem['url']."\"";
			if ($thisItem['url'] == '../index.php')
				{
				echo ' target="_blank"';
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
     * List Section Pages
     *
     * @param section - section to display
     */
    function ListSectionPages($section)
    {
        if (isset($this->menuItems[$section]) && count($this->menuItems[$section]) > 1)
            {
            echo " ".lang('subitems').": ";
            $count = 0;
            foreach($this->menuItems[$section] as $thisItem)
                {
                if ($count++ < 1)
                    {
                    // skip the first item
                    continue;
                    }
                if ($count > 2)
                    {
                    echo ", ";
                    }
                echo "<a href=\"".$thisItem['url'];
                echo "\">".$thisItem['title']."</a>";
                }
            }
    }



    /**
     * Display Section Pages - shows all admin section pages
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
            if (preg_match('/makebookmark\.php/',$menuItem[0]['url']))
                {
                continue;
                }
            echo "<div class=\"MainMenuItem\">\n";
            echo "<a href=\"".$menuItem[0]['url']."\"";
            if ($menuItem[0]['url'] == '../index.php')
                {
                echo ' target="_blank"';
                }
            echo ">".$menuItem[0]['title']."</a>\n";
            echo "<span class=\"description\">";
            if (isset($menuItem[0]['description']) && strlen($menuItem[0]['description']) > 0)
                {
                echo $menuItem[0]['description'];
                }
            $this->ListSectionPages($thisSection);
            echo "</span>\n";
            echo "</div>\n";
            }
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
            $count = count($menuItem);
            $counter = 1;
            foreach ($menuItem as $thisItem)
                {
                if (! get_preference(get_userid(), 'bookmarks') &&
                      preg_match('/makebookmark\.php/',$thisItem['url']))
                    {
                    continue;
                    }
                echo '<li><a href="'.$thisItem['url'].'"';
				if ($thisItem['url'] == '../index.php')
					{
					echo ' target="_blank"';
					}
                if ($thisItem['selected'])
                    {
                    echo ' class="selected"';
                    }
                echo ">".$thisItem['title']."</a>";
                if ($count > 1 && $counter == 1)
                    {
                    echo "<ul>";
                    }
                else if ($count > 1 && $count == $counter)
                    {
                    echo "</li></ul>";
                    }
                else
                    {
                    echo "</li>";
                    }
                $counter++;
                }
            }
        echo "</ul></div>\n";
    }

    function DisplayFooter()
    {
?>
<div id="Footer">
<a href="http://www.cmsmadesimple.org">CMS Made Simple</a> is Free Software released under the GNU/GPL License
</div>
<?php
    }

}

# vim:ts=4 sw=4 noet
?>
