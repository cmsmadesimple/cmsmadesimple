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
#
#$Id$

class defaultTheme extends CmsAdminTheme
{
	var $themeName = 'default';
	var $theme_name = 'default';
	
	function __construct()
	{
		parent::__construct();
	}

	function render_menu_section($node, $depth, $maxdepth)
	{
		if ($maxdepth > 0 && $depth > $maxdepth)
		{
			return;
		}
		if (!$node->show_in_menu)
		{
			return;
		}
		if (strlen($node->url) < 1)
		{
			echo "<li>".$node->title."</li>";
			return;
		}
		echo "<li><a href=\"";
		echo $node->url;
		echo "\"";
		
		if ($node->target != '')
		{
			echo ' target="'.$node->target.'" rel="external"';
		}
		
		$class = array();
		if ($node->selected)
		{
			$class[] = 'selected';
		}
		if ($node->first_module)
		{
			$class[] = 'first_module';
		}
		else if ($node->module)
		{
			$class[] = 'module';
		}
		if (count($class) > 0)
		{
			echo ' class="';
			for($i=0;$i<count($class);$i++)
			{
				if ($i > 0)
				{
					echo " ";
				}
				echo $class[$i];
			}
			echo '"';
		}
		echo ">";
		echo $node->title;
		echo "</a>";
		if ($node->has_children() && $this->has_displayable_children($node))
		{
			echo "<ul>";
			$children = $node->get_children();
			foreach ($children as &$child)
			{
				$this->render_menu_section($child, $depth+1, $maxdepth);
			}
			echo "</ul>";
		}
		echo "</li>";
		return;
	}

	function display_top_menu()
	{
		echo '<div><p class="logocontainer"><img src="themes/default/images/logo.gif" alt="" /><span class="logotext">'.lang('adminpaneltitle').'</span></p></div>';
		echo "<div class=\"topmenucontainer\">\n\t<ul id=\"nav\">";
		$sections = CmsAdminTree::get_instance()->get_root_node()->get_children();
		foreach ($sections as &$onesection)
		{
			echo "\n\t\t";
			$this->render_menu_section($onesection, 0, -1);
		}
		echo "\n\t</ul>\n";
		echo "\t<div class=\"clearb\"></div>\n";
		echo "</div>\n";
		echo '<div class="breadcrumbs"><p class="breadcrumbs">';
		$counter = 0;
		foreach ($this->breadcrumbs as $crumb)
		{
			if ($counter > 0) {
				echo " &#187; ";
			}
			if (str_replace('&amp;', '&', $crumb['url']) != basename($_SERVER['REQUEST_URI']))
			{
				echo '<a class="breadcrumbs" href="'.$crumb['url'];
				echo '">'.$crumb['title'];
				echo '</a>';
			}
			else
			{
				echo $crumb['title'];
			}
			$counter++;
		}

		echo '</p></div>';
		echo '<div class="hstippled">&nbsp;</div>';
	}

	function StartRighthandColumn()
	{
		echo '<div class="navt_menu">'."\n";
		echo '<div id="navt_display" class="navt_show" onclick="change(\'navt_display\', \'navt_hide\', \'navt_show\'); change(\'navt_container\', \'invisible\', \'visible\');"></div>'."\n";
		echo '<div id="navt_container" class="invisible">'."\n";
		echo '<div id="navt_tabs">'."\n";
		if (get_preference($this->userid, 'bookmarks'))
		{
				echo '<div id="navt_bookmarks">'.lang('bookmarks').'</div>'."\n";
		}
		echo '</div>'."\n";
		echo '<div style="clear: both;"></div>'."\n";
		echo '<div id="navt_content">'."\n";
	}

	function DisplayRecentPages()
	{
		if (get_preference($this->userid, 'recent'))
		{	
			echo '<div id="navt_recent_pages_c">'."\n";
			$counter = 0;
			foreach($this->recent as $pg)
			{
				echo "<a href=\"". $pg->url."\">".++$counter.'. '.$pg->title."</a><br />"."\n";
			}
			echo '</div>'."\n";
		}
	}

	function DisplayBookmarks($marks) {
		if (get_preference($this->userid, 'bookmarks'))
		{	
			echo '<div id="navt_bookmarks_c">'."\n";
			$counter = 0;
			foreach($marks as $mark)
			{
				echo "<a href=\"". $mark->url."\">".++$counter.'. '.$mark->title."</a><br />"."\n";
			}
			echo '</div>'."\n";
		}
	}	 

	function EndRighthandColumn()
	{
		echo '</div>'."\n";
		echo '</div>'."\n";
		echo '<div style="clear: both;"></div>'."\n";
		echo '</div>'."\n";
	}

	function DisplayDashboardCallout($file, $message = '')
	{
		if ($message == '')
			$message = lang('installdirwarning');

		if (file_exists($file))
		{
			echo "<div class=\"DashboardCallout\">\n";
			echo "<div class=\"pageerrorinstalldir\"><p class=\"pageerror\">".$message."</p></div>";
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
			if ($menuItem['url'] == 'index.php'  || strlen($menuItem['url']) < 1)
			{
				continue;
			}

			echo "<div class=\"itemmenucontainer\">";
			echo '<div class="itemoverflow">';
			echo '<p class="itemicon">';
			$iconSpec = $thisSection;
			if ($menuItem['url'] == '../index.php')
			{
				$iconSpec = 'viewsite';
			}
			echo '<a href="'.$menuItem['url'].'">';
			echo $this->DisplayImage('icons/topfiles/'.$iconSpec.'.gif', $iconSpec, '', '', 'itemicon');
			echo '</a>';
			echo '</p>';
			echo '<p class="itemtext">';
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
			echo '</p>';
			echo "</div>";
			echo '</div>';
		}
	}
	
	function ListSectionPages($section)
	{
		if (! isset($this->menuItems[$section]['children']) || count($this->menuItems[$section]['children']) < 1)
		{
			return;
		}
      
		if ($this->has_displayable_children($section))
		{
			echo " ".lang('subitems').": ";
			$count = 0;
			foreach($this->menuItems[$section]['children'] as $thisChild)
			{
				$thisItem = $this->menuItems[$thisChild];
				if (! $thisItem['show_in_menu'] || strlen($thisItem['url']) < 1)
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

	/* Functions that we want dont want the standard output from */
	function OutputFooterJavascript() {}	
}
?>