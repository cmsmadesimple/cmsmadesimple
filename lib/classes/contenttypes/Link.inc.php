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

class Link extends ContentBase
{
	var $unused_fields = array('cachable', 'alias', 'metadata', 'template_id');

	function __construct()
	{
		parent::__construct();
		$this->cachable = false;
	}

	function friendly_name()
	{
		return 'Link';
	}
	
	function validate()
	{
		parent::validate();
		if ($this->get_property_value('url') == '')
		{
			$this->add_validation_error(lang('nofieldgiven',array(lang('url'))));	
		}
	}

	function get_url($rewrite = true)
	{
		return cms_htmlentities($this->get_property_value('url'));
	}
	
	function is_default_possible()
	{
		return false;
	}
	
	function add_template(&$smarty)
	{
		$smarty->assign('link_targets', array('' => '(none)', '_blank' => '_blank', '_parent' => '_parent', '_self' => '_self', '_top' => '_top'));
		return array(cms_join_path(dirname(__FILE__), 'Link.tpl'));
	}
	
	function edit_template(&$smarty)
	{
		$smarty->assign('link_targets', array('' => '(none)', '_blank' => '_blank', '_parent' => '_parent', '_self' => '_self', '_top' => '_top'));
		return array(cms_join_path(dirname(__FILE__), 'Link.tpl'));
	}
}

Link::register_orm_class('Link');

# vim:ts=4 sw=4 noet
?>