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

class CmsModuleExtension extends CmsObject
{
	protected $module = null;
	
	public $id = null;
	public $return_id = null;
	
	function __construct($module)
	{
		parent::__construct();
		$this->set_module($module);
	}
	
	public function set_id($id, $return_id = '')
	{
		$this->id = $id;
		$this->return_id = $return_id;
	}
	
	function get_module()
	{
		return $this->module;
	}
	
	function set_module($module)
	{
		$this->module = $module;
	}
}

# vim:ts=4 sw=4 noet
?>