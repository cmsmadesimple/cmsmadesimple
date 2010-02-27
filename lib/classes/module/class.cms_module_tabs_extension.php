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
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

class CmsModuleTabsExtension extends CmsModuleExtension
{
	function __construct($module)
	{
		parent::__construct($module);
		$this->module->mActiveTab = '';
	}
	
	public function start_tab_headers()
	{
		return '<!-- start_tab_headers --> <div id="page_tabs">';
	}

	public function set_tab_header($tab_id, $title, $active = false)
	{
		$a = "";
		if (TRUE == $active)
		{
			$a = " class='active'";
			$this->module->mActiveTab = $tab_id;
		}
		return '<div id="' . $tab_id . '"' . $a . '>' . $title . '</div>';
	}

	public function end_tab_headers()
	{
		return "</div> <!-- end_tab_headers -->";
	}

	public function start_tab_content()
	{
		return '<div class="clearb"></div><div id="page_content">';
	}

	public function end_tab_content()
	{
		return '</div> <!-- end_tab_content -->';
	}

	public function start_tab($tab_id, $params = array())
	{
		if (FALSE == empty($this->module->mActiveTab) &&
				$tab_id == $this->module->mActiveTab &&
				FALSE == empty($params['tab_message']))
		{
			$message = $this->module->ShowMessage($this->module->Lang($params['tab_message']));
		}
		else
		{
			$message = '';
		}
		return '<!-- start_tab --> <div id="' . strtolower(str_replace(' ', '_', $tab_id)) . '_c">' . $message;
	}

	public function end_tab()
	{
		return '</div> <!-- end_tab -->';
	}
}

# vim:ts=4 sw=4 noet
?>