<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

class CmsModuleTabs extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}
	
	static public function start_tab_headers()
	{
		return '<div id="module_page_tabs"><ul>';
	}

	static public function set_tab_header($tabid, $title, $active=false)
	{
		return '<li><a href="#'.$tabid.'"><span>'.$title.'</span></a></li>';
	}

	static public function end_tab_headers()
	{
		return '</ul>';
	}

	static public function start_tab_content()
	{
		return '';
	}

	static public function end_tab_content()
	{
		return "</div>\n<script>\n<!--\n$('#module_page_tabs > ul').tabs();\n//-->\n</script>\n";
	}

	static public function start_tab($tabid)
	{
		return '<div id="' . $tabid . '">';
	}

	static public function end_tab()
	{
		return "</div> <!-- EndTab -->\n";
	}
}

# vim:ts=4 sw=4 noet
?>