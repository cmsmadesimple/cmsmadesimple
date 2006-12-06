<?php
# CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

class BlockHtml extends Object
{
	function __construct()
	{
		parent::__construct();
	}
	
	function storage_name()
	{
		return 'html_block';
	}

	function friendly_name()
	{
		return 'HTML Text';
	}
	
	function show(&$page, $block_name)
	{
		return $page->get_property_value($block_name . '-content');
	}
	
	function validate(&$page, $block_name)
	{
		if ($page->get_property_value($block_name . '-content') == '')
		{
			$page->add_validation_error(lang('nofieldgiven', array(lang('content'))));	
		}
	}
	
	function block_add_template(&$page, $block_name, &$template)
	{
		return create_textarea(true, $page->get_property_value($block_name . '-content'), 'content[property][' . $block_name . '-content]', '', 'content_' . $block_name . '_block', '', '', '80', '15', '');
	}
	
	function block_edit_template(&$page, $block_name, &$template)
	{
		return $this->block_add_template($page, $block_name, $template);
	}
}

# vim:ts=4 sw=4 noet
?>