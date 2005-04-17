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
                echo $sectionModule['key']."\">".$sectionModule['key']."</a>\n";
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
                echo "\">".$sectionModule['key']."</a>";
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
                $modList[$sectionModule['key']] = "moduleinterface.php?module=".
                    $sectionModule['key'];
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
<script type="text/JavaScript">
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


    function DoTopMenu($cms_top='',$url,$query)
    {
    	$menuItems = array();
    	if (strpos( $url, '/' ) === false)
    	   {
    	   	$script = $url;
    	   }
        else
            {
            $script = array_pop(explode('/',$url));
    	    }
        $count = 0;
    	$menuItems['main'][$count]['url'] = 'index.php';
    	$menuItems['main'][$count]['title'] = lang('main');
    	$menuItems['main'][$count++]['selected'] = ($cms_top=='main');

    	$count = 0;
        $menuItems['content'][$count]['url'] = 'topcontent.php';
    	$menuItems['content'][$count]['title'] = lang('content');
    	$menuItems['content'][$count++]['selected'] = ($cms_top=='content');

        $menuItems['content'][$count]['url'] = 'listcontent.php';
        $menuItems['content'][$count]['title'] = lang('pages');
        $menuItems['content'][$count++]['selected'] = ($script=='listcontent.php');

        if ($this->HasPerm('htmlPerms'))
            {
            $menuItems['content'][$count]['url'] = 'listhtmlblobs.php';
    	    $menuItems['content'][$count]['title'] = lang('htmlblobs');
    	    $menuItems['content'][$count++]['selected'] = ($script=='listhtmlblobs.php');
            }

        $tmpArray = $this->MenuListSectionModules('content');
        foreach ($tmpArray as $thisKey=>$thisVal)
            {
            $menuItems['content'][$count]['url'] = $thisVal;
            $menuItems['content'][$count]['title'] = $thisKey;
            $menuItems['content'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
            }

        #$count = 0;
        #$menuItems['pages'][$count]['url'] = 'listcontent.php';
    	#$menuItems['pages'][$count]['title'] = lang('pages');
    	#$menuItems['pages'][$count++]['selected'] = ($cms_top=='pages');

        if ($this->HasPerm('filePerms'))
        {
            $count = 0;
            $menuItems['files'][$count]['url'] = 'topfiles.php';
            $menuItems['files'][$count]['title'] = lang('files');
            $menuItems['files'][$count++]['selected'] = ($cms_top=='files');

            $menuItems['files'][$count]['url'] = 'files.php';
            $menuItems['files'][$count]['title'] = lang('filemanager');
            $menuItems['files'][$count++]['selected'] = ($script=='files.php');
            $menuItems['files'][$count]['url'] = 'imagefiles.php';
            $menuItems['files'][$count]['title'] = lang('imagemanager');
            $menuItems['files'][$count++]['selected'] = ($script=='imagefile.php');

            $tmpArray = $this->MenuListSectionModules('files');
            foreach ($tmpArray as $thisKey=>$thisVal)
                {
                $menuItems['files'][$count]['url'] = $thisVal;
                $menuItems['files'][$count]['title'] = $thisKey;
                $menuItems['files'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
                }
        }
        if ($this->HasPerm('layoutPerms'))
        {
            $count = 0;
            $menuItems['layout'][$count]['url'] = 'toplayout.php';
            $menuItems['layout'][$count]['title'] = lang('layout');
            $menuItems['layout'][$count++]['selected'] = ($cms_top=='layout');
            if ($this->HasPerm('templatePerms'))
            {
                $menuItems['layout'][$count]['url'] = 'listtemplates.php';
                $menuItems['layout'][$count]['title'] = lang('templates');
                $menuItems['layout'][$count++]['selected'] = ($script=='listtemplates.php');;
            }
            if ($this->HasPerm('cssPerms') || $themeObject->HasPerm('cssAssocPerms'))
            {
                $menuItems['layout'][$count]['url'] = 'listcss.php';
                $menuItems['layout'][$count]['title'] = lang('stylesheets');
                $menuItems['layout'][$count++]['selected'] = ($script=='listcss.php');;
            }
            $tmpArray = $this->MenuListSectionModules('layout');
            foreach ($tmpArray as $thisKey=>$thisVal)
                {
                $menuItems['layout'][$count]['url'] = $thisVal;
                $menuItems['layout'][$count]['title'] = $thisKey;
                $menuItems['layout'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
                }
        }
        if ($this->HasPerm('usersGroupsPerms'))
        {
            $count = 0;
            $menuItems['usersgroups'][$count]['url'] = 'topusers.php';
            $menuItems['usersgroups'][$count]['title'] = lang('usersgroups');
            $menuItems['usersgroups'][$count++]['selected'] = ($cms_top=='usersgroups');
            if ($this->HasPerm('userPerms'))
            {
                $menuItems['usersgroups'][$count]['url'] = 'listusers.php';
                $menuItems['usersgroups'][$count]['title'] = lang('users');
                $menuItems['usersgroups'][$count++]['selected'] = ($script=='listusers.php');;
            }
            if ($this->HasPerm('groupPerms'))
            {
                $menuItems['usersgroups'][$count]['url'] = 'listgroups.php';
                $menuItems['usersgroups'][$count]['title'] = lang('groups');
                $menuItems['usersgroups'][$count++]['selected'] = ($script=='listgroups.php');;
            }
            if ($this->HasPerm('groupMemberPerms'))
            {
                $menuItems['usersgroups'][$count]['url'] = 'changegroupassign.php';
                $menuItems['usersgroups'][$count]['title'] = lang('groupassignments');
                $menuItems['usersgroups'][$count++]['selected'] = ($script=='changegroupassign.php');;
            }
            if ($this->HasPerm('groupPermPerms'))
            {
                $menuItems['usersgroups'][$count]['url'] = 'changegroupperm.php';
                $menuItems['usersgroups'][$count]['title'] = lang('groupperms');
                $menuItems['usersgroups'][$count++]['selected'] = ($script=='changegroupperm.php');;
            }
            $tmpArray = $this->MenuListSectionModules('usersgroups');
            foreach ($tmpArray as $thisKey=>$thisVal)
                {
                $menuItems['usersgroups'][$count]['url'] = $thisVal;
                $menuItems['usersgroups'][$count]['title'] = $thisKey;
                $menuItems['usersgroups'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
                }

        }
        if ($this->HasPerm('extensionsPerms'))
        {
            $count = 0;
            $menuItems['extensions'][$count]['url'] = 'topextensions.php';
            $menuItems['extensions'][$count]['title'] = lang('extensions');
            $menuItems['extensions'][$count++]['selected'] = ($cms_top=='extensions');
            if ($this->HasPerm('modulePerms'))
            {
                $menuItems['extensions'][$count]['url'] = 'listmodules.php';
                $menuItems['extensions'][$count]['title'] = lang('modules');
                $menuItems['extensions'][$count++]['selected'] = ($script=='listmodules.php');;
            }
            $menuItems['extensions'][$count]['url'] = 'listtags.php';
            $menuItems['extensions'][$count]['title'] = lang('tags');
            $menuItems['extensions'][$count++]['selected'] = ($script=='listtags.php');
            if ($this->HasPerm('codeBlockPerms'))
            {
                $menuItems['extensions'][$count]['url'] = 'listusertags.php';
                $menuItems['extensions'][$count]['title'] = lang('usertags');
                $menuItems['extensions'][$count++]['selected'] = ($script=='listusertags.php');;
            }
            $tmpArray = $this->MenuListSectionModules('extensions');
            foreach ($tmpArray as $thisKey=>$thisVal)
                {
                $menuItems['extensions'][$count]['url'] = $thisVal;
                $menuItems['extensions'][$count]['title'] = $thisKey;
                $menuItems['extensions'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
                }
        }
        $count = 0;
    	$menuItems['preferences'][$count]['url'] = 'editprefs.php';
    	$menuItems['preferences'][$count]['title'] = lang('preferences');
    	$menuItems['preferences'][$count++]['selected'] = ($cms_top=='preferences');
        $tmpArray = $this->MenuListSectionModules('files');
        foreach ($tmpArray as $thisKey=>$thisVal)
            {
            $menuItems['preferences'][$count]['url'] = $thisVal;
            $menuItems['preferences'][$count]['title'] = $thisKey;
            $menuItems['preferences'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
            }


        if ($this->HasPerm('adminPerms'))
        {
            $count = 0;
            $menuItems['admin'][$count]['url'] = 'topadmin.php';
            $menuItems['admin'][$count]['title'] = lang('admin');
            $menuItems['admin'][$count++]['selected'] = ($cms_top=='admin');
            if ($this->HasPerm('sitePrefPerms'))
            {
                $menuItems['admin'][$count]['url'] = 'siteprefs.php';
                $menuItems['admin'][$count]['title'] = lang('preferences');
                $menuItems['admin'][$count++]['selected'] = ($script=='siteprefs.php');;
            }
            $menuItems['admin'][$count]['url'] = 'adminlog.php';
            $menuItems['admin'][$count]['title'] = lang('adminlog');
            $menuItems['admin'][$count++]['selected'] = ($script=='adminlog.php');;
            $tmpArray = $this->MenuListSectionModules('admin');
            foreach ($tmpArray as $thisKey=>$thisVal)
                {
                $menuItems['admin'][$count]['url'] = $thisVal;
                $menuItems['admin'][$count]['title'] = $thisKey;
                $menuItems['admin'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
                }
        }
        $count = 0;
    	$menuItems['viewsite'][$count]['url'] = '../index.php';
    	$menuItems['viewsite'][$count]['title'] = lang('viewsite');
    	$menuItems['viewsite'][$count]['selected'] = false;

    	$menuItems['logout'][$count]['url'] = 'logout.php';
    	$menuItems['logout'][$count]['title'] = lang('logout');
    	$menuItems['logout'][$count]['selected'] = false;

    	$menuItems['bookmarks'][$count]['url'] = 'makebookmark.php?title='.urlencode($this->title);
    	$menuItems['bookmarks'][$count]['title'] = '[+]';
    	$menuItems['bookmarks'][$count++]['selected'] = ($cms_top=='bookmarks');
        $tmpArray = $this->MenuListSectionModules('bookmarks');
        foreach ($tmpArray as $thisKey=>$thisVal)
            {
            $menuItems['bookmarks'][$count]['url'] = $thisVal;
            $menuItems['bookmarks'][$count]['title'] = $thisKey;
            $menuItems['bookmarks'][$count++]['selected'] = (strpos($query,$thisKey) !== false);
            }
        $this->DisplayTopMenu($menuItems);
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
    function DisplayTopMenu($menuItems)
    {
        echo "<div id=\"TopMenu\"><ul id=\"nav\">\n";
        foreach ($menuItems as $key=>$menuItem)
            {
            $count = count($menuItem);
            $counter = 1;
            foreach ($menuItem as $thisItem)
                {
                echo '<li><a href="'.$thisItem['url'].'"';
				if ($thisItem['url'] == '../index.php')
					{					echo ' target="_blank"';
					}                if ($thisItem['selected'])
                    {
                    echo ' class="selected"';
                    }
                echo ">".$thisItem['title']."</a>";
                if ($count > 1 && $counter == 1)
                    {
                    echo "<ul>\n";
                    }
                else if ($count > 1 && $count == $counter)
                    {
                    echo "</li></ul>\n";
                    }
                else
                    {
                    echo "</li>\n";
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
