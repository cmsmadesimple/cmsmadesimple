<?php
#Modern Theme - A theme for CMS Made Simple
#(c)2005 by Alexander Endresen - alexander@endresen.org
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

class modernTheme extends AdminTheme
{

	function renderMenuSection($section, $depth, $maxdepth)
	{
		if ($maxdepth > 0 && $depth > $maxdepth)
			{
			return;
			}
		if (! $this->menuItems[$section]['show_in_menu'])
			{
			return;
			}
		echo "<li><a href=\"";
		echo $this->menuItems[$section]['url'];
		echo "\"";
		if (array_key_exists('target', $this->menuItems[$section]))
			{
			echo ' rel="external"';
			}
		if ($this->menuItems[$section]['selected'])
			{
			echo ' class="selected"';
			}
		echo ">";
		echo $this->menuItems[$section]['title'];
		echo "</a>";
		if (count($this->menuItems[$section]['children']) > 0)
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


    function DisplayTopMenu()
    {
		echo '<div><p class="logocontainer"><img src="themes/modern/images/logo.gif" alt="" /><span class="logotext">CMS Administration Console</span></p></div>';
        echo "<div class=\"topmenucontainer\">\n\t<ul id=\"nav\">";
		$breadcrumbs = array();
        foreach ($this->menuItems as $key=>$menuItem) {
            $count = count($menuItem);
            $counter = 1;
			if ($menuItem['selected']) {
				array_push($breadcrumbs, array($menuItem['title'] => $menuItem['url']));
			}
			if ($counter == 1) {
				echo "\n\t\t";
			}
        	if ($menuItem['parent'] == -1) {
        		$this->renderMenuSection($key, 0, -1);
        	}
            $counter++;
            }
        echo "\n\t</ul>\n";
		echo "\t<div class=\"clearb\"></div>\n";
		echo "</div>\n";
		echo '<div class="b-white"><p class="breadcrumbs">';
		$counter = 0;
		while ($menutext = array_shift($breadcrumbs)) {
			if ($counter > 0) {
				echo " &#187; ";
			}
			foreach ($menutext as $key=>$value) {
				echo '<a class="breadcrumbs" href="'.$value.'">'.$key.'</a>';
			}
			$counter++;
		}
		echo '</p></div>';
		echo '<div class="hstippled">&nbsp;</div>';
    }

	function DisplayFooter() {
		echo '<p class="footer"><a class="footer" href="http://www.cmsmadesimple.org">CMS Made Simple</a> is free software released under the General Public Licence.</p>';
	}
	
	function OutputHeaderJavascript() {
		echo '<script type="text/javascript" src="themes/modern/includes/standard.js"></script>';
	}

	function StartRighthandColumn() {
		echo '<div class="navt_menu">'."\n";
		echo '<div id="navt_display" class="navt_show" onclick="change(\'navt_display\', \'navt_hide\', \'navt_show\'); change(\'navt_container\', \'invisible\', \'visible\');"></div>'."\n";
		echo '<div id="navt_container" class="invisible">'."\n";
		echo '<div id="navt_tabs">'."\n";
		if (get_preference($this->userid, 'bookmarks')) {
				echo '<div id="navt_bookmarks">'.lang('bookmarks').'</div>'."\n";
		}
		echo '</div>'."\n";
		echo '<div style="clear: both;"></div>'."\n";
		echo '<div id="navt_content">'."\n";
	}

	function DisplayRecentPages()	 {
		if (get_preference($this->userid, 'recent')) {	
			echo '<div id="navt_recent_pages_c">'."\n";
			$counter = 0;
			foreach($this->recent as $pg) {
				echo "<a href=\"". $pg->url."\">".++$counter.'. '.$pg->title."</a><br />"."\n";
				}
			echo '</div>'."\n";
		}
	}

	function DisplayBookmarks($marks) {
		if (get_preference($this->userid, 'bookmarks')) {	
			echo '<div id="navt_bookmarks_c">'."\n";
			$counter = 0;
			foreach($marks as $mark) {
				echo "<a href=\"". $mark->url."\">".++$counter.'. '.$mark->title."</a><br />"."\n";
				}
			echo '</div>'."\n";
		}
	}	 

	function EndRighthandColumn() {
		echo '</div>'."\n";
		echo '</div>'."\n";
		echo '<div style="clear: both;"></div>'."\n";
		echo '</div>'."\n";
	}

	function DisplayDocType() {
	
		echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		echo '<!DOCTYPE html'."\n";
		echo '	PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"'."\n";
		echo '	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n";
	}

   function DisplayHTMLStartTag() {
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
   }

    function DisplayDashboardCallout($file)
    {
        if (file_exists($file))
        {
	       echo "<div class=\"DashboardCallout\">\n";
	       echo '<p>'.lang('installdirwarning').'</p>';
	       echo "</div> <!-- end DashboardCallout -->\n";
        }
    }
	
	function DisplayAllSectionPages()
	{
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
            if ($menuItem['url'] == 'index.php')
            	{
            	continue;
            	}
				
			$itemicons = array (
				"topcontent.php" => "topcontent.gif",
				"toplayout.php" => "toplayout.gif",
				"topusers.php" => "topusers.gif",
				"topextensions.php" => "topextensions.gif",
				"editprefs.php" => "editprefs.gif",
				"topadmin.php" => "topadmin.gif",
				"../index.php" => "viewsite.gif",
				"logout.php" => "logout.gif"
			);		
            echo "<div class=\"itemmenu\">\n";
            if (array_key_exists($menuItem['url'],$itemicons)) {
			     echo '<a href="'.$menuItem['url'].'"><img class="itemicon" src="themes/modern/images/icons/topfiles/'.$itemicons[$menuItem['url']].'" alt="" /></a>';
			}
            echo "<a class=\"itemlink\" href=\"".$menuItem['url']."\"";
			if (array_key_exists('target', $menuItem))
				{
				echo ' rel="external"';
				}

            echo ">".$menuItem['title']."</a><br />\n";
            if (isset($menuItem['description']) && strlen($menuItem['description']) > 0)
                {
                echo $menuItem['description']."<br />";
                }
            $this->ListSectionPages($thisSection);
            echo "</div>\n";
            }
    }

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
            if (! $thisItem['show_in_menu'])
            	{
            	continue;
            	}
				
			$itemicons = array (
				"listcontent.php" => "listcontent.gif",
				"files.php" => "files.gif",
				"imagefiles.php" => "imagefiles.gif",
				"moduleinterface.php?module=News" => "newsmodule.gif",
				"listtemplates.php" => "listtemplates.gif",
				"listcss.php" => "listcss.gif",
				"listhtmlblobs.php" => "listhtmlblobs.gif",
				"listusers.php" => "listusers.gif",
				"listgroups.php" => "listgroups.gif",
				"changegroupassign.php" => "changegroupassign.gif",
				"changegroupperm.php" => "changegroupperm.gif",
				"listmodules.php" => "listmodules.gif",
				"listtags.php" => "listtags.gif",
				"listusertags.php" => "listusertags.gif",
				"moduleinterface.php?module=TinyMCE" => "wysiwyg.gif",
				"siteprefs.php" => "siteprefs.gif",
				"adminlog.php" => "adminlog.gif"
			);
			
            echo "<div class=\"itemmenu\">\n";
            if (array_key_exists($thisItem['url'],$itemicons))
            	{
				echo '<a href="'.$thisItem['url'].'"><img class="itemicon" src="themes/modern/images/icons/topfiles/'.$itemicons[$thisItem['url']].'" alt="" /></a>';
				}
            echo "<a class=\"itemlink\" href=\"".$thisItem['url']."\"";
			if (array_key_exists('target', $thisItem))
				{
				echo ' rel="external"';
				}
            echo ">".$thisItem['title']."</a><br />\n";
            if (isset($thisItem['description']) && strlen($thisItem['description']) > 0)
                {
                echo $thisItem['description']."<br />";
                }
            echo "</div>\n";
        }
    }
	
   function ListSectionPages($section)
    {
        if (! isset($this->menuItems[$section]['children']) || count($this->menuItems[$section]['children']) < 1)
            {
            return;
            }
        $displayableChildren=false;
        foreach($this->menuItems[$section]['children'] as $thisChild)
            {
            $thisItem = $this->menuItems[$thisChild];
            if ($thisItem['show_in_menu'])
                {
                $displayableChildren = true;
                }
            }

        if ($displayableChildren)
            {
            echo " ".lang('subitems').": ";
            $count = 0;
            foreach($this->menuItems[$section]['children'] as $thisChild)
                {
                $thisItem = $this->menuItems[$thisChild];
                if (! $thisItem['show_in_menu'])
                    {
                    continue;
                    }
                if ($count++ > 0)
                    {
                    echo ", ";
                    }
                echo "<a class=\"itemsublink\" href=\"".$thisItem['url'];
                echo "\">".$thisItem['title']."</a>";
                }
            }
    }

	function DisplayImage($imageName, $alt='', $width='', $height='')
    {
        if (! isset($this->imageLink[$imageName]))
    	   {
    	   if (file_exists(dirname($this->cms->config['root_path'] . '/' . $this->cms->config['admin_dir'] .
                '/themes/' . $this->themeName . '/images/icons/system/' . $imageName) . '/'. $imageName))
    	       {
                $this->imageLink[$imageName] = 'themes/' .
                    $this->themeName . '/images/icons/system/' . $imageName;
    	       }
    	   else
    	       {
    	       $this->imageLink[$imageName] = 'themes/default/images/' . $imageName;
    	       }
    	   }

        $retStr = '<img src="'.$this->imageLink[$imageName].'"';
		$retStr .= 'class="pageicon"';
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
        $retStr .= '/>';
        return $retStr;
    }
	
	/* Functions that we want dont want the standard output from */
	function OutputFooterJavascript() {}	
}
?>
