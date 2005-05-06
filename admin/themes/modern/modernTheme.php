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
    function DisplayTopMenu()
    {
		echo '<div><p class="logocontainer"><img src="themes/modern/images/logo.gif" alt="" /><span class="logotext">CMS Administration Console</span></p></div>';
        echo "<div class=\"topmenucontainer\">\n\t<ul id=\"nav\">";
		$breadcrumbs = array();
        foreach ($this->menuItems as $key=>$menuItem)
            {
            $count = count($menuItem);
            $counter = 1;
            foreach ($menuItem as $thisItem)
                {
				if ($thisItem['selected']) {
					array_push($breadcrumbs, array($thisItem['title'] => $thisItem['url']));
				}
				
                if (! get_preference(get_userid(), 'bookmarks') &&
                      preg_match('/makebookmark\.php/',$thisItem['url']))
                    {
                    continue;
                    }
				if ($counter == 1) {
					echo "\n\t\t";
				}
                echo "<li";
                if ($thisItem['selected'])
                    {
                    echo ' class="selected"';
                    }
                echo '><a href="'.$thisItem['url'].'"';
				if ($thisItem['url'] == '../index.php')
					{
					echo ' rel="external"';
					}
                echo ">".$thisItem['title']."</a>";
                if ($count > 1 && $counter == 1)
                    {
                    echo "\n\t\t\t<ul>";
                    }
                else if ($count > 1 && $count == $counter)
                    {
                    echo "</li></ul></li>";
                    }
				else { 
					echo "</li>";
					}
                $counter++;
                }	
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
				echo '<a class="f-graym" href="'.$value.'">'.$key.'</a>';
			}
			$counter++;
		}
		echo '</p></div>';
		echo '<div class="hstippled">&nbsp;</div>';
    }

	function DisplayFooter() {
		echo '<div class="footer"><a class="f-white" href="http://www.cmsmadesimple.org">CMS Made Simple</a> is free software released under the General Public Licence.</div>';
	}
	
	function OutputHeaderJavascript() {
		echo '<script type="text/javascript" src="themes/modern/includes/standard.js"></script>';
	}

	function StartRighthandColumn() {
		echo '<div class="navt_menu">'."\n";
		echo '<div id="navt_display" class="navt_show" onclick="change(\'navt_display\', \'navt_hide\', \'navt_show\'); change(\'navt_container\', \'invisible\', \'visible\');"></div>'."\n";
		echo '<div id="navt_container" class="invisible">'."\n";
		echo '<div id="navt_tabs">'."\n";
		if (get_preference($userid, 'recent')) {
			echo '<div id="navt_recent_pages">'.lang('recentpages').'</div>'."\n";
		}
		if (get_preference($userid, 'bookmarks')) {
				echo '<div id="navt_bookmarks">'.lang('bookmarks').'</div>'."\n";
		}
		echo '</div>'."\n";
		echo '<div style="clear: both;"></div>'."\n";
		echo '<div id="navt_content">'."\n";
	}

	function DisplayRecentPages()	 {
		if (get_preference($userid, 'recent')) {	
			echo '<div id="navt_recent_pages_c">'."\n";
			$counter = 0;
			foreach($this->recent as $pg) {
				echo "<a href=\"". $pg->url."\">".++$counter.'. '.$pg->title."</a><br />"."\n";
				}
			echo '</div>'."\n";
		}
	}

	function DisplayBookmarks($marks) {
		if (get_preference($userid, 'bookmarks')) {	
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
            if (strpos(strtolower($menuItem[0]['title']),'main') == 0 && strlen($menuItem[0]['title'])==4)
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
			echo '<a href="'.$menuItem[0]['url'].'"><img class="itemicon" src="themes/modern/images/icons/topfiles/'.$itemicons[$menuItem[0]['url']].'" alt="" /></a>';
            echo "<a class=\"itemlink\" href=\"".$menuItem[0]['url']."\"";
            if ($menuItem[0]['url'] == '../index.php')
                {
                echo ' rel="external"';
                }
            echo ">".$menuItem[0]['title']."</a><br />\n";
            if (isset($menuItem[0]['description']) && strlen($menuItem[0]['description']) > 0)
                {
                echo $menuItem[0]['description']."<br />";
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
        $count = 0;
        foreach ($this->menuItems[$section] as $thisItem)
            {
            if ($count++ < 1)
                {
                // skip the first item
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
			echo '<a href="'.$thisItem['url'].'"><img class="itemicon" src="themes/modern/images/icons/topfiles/'.$itemicons[$thisItem['url']].'" alt="" /></a>';
            echo "<a class=\"itemlink\" href=\"".$thisItem['url']."\"";
			if ($thisItem['url'] == '../index.php')
				{
				echo ' target="_blank"';
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