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

	/* Functions that we want dont want the standard output from */
	function OutputFooterJavascript() {}	
}
?>