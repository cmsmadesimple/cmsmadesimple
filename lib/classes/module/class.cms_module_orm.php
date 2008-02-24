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

class CmsModuleOrm extends CmsObjectRelationalMapping
{
	/**
	 * Variable to hold the name of the module that this class belongs to
	 *
	 * @var string Name of the module
	 **/
	public $module_name = '';
	
	/**
	 * Variable to hold the module that this object references.  This way, the
	 * ORM'd class has access to the modules methods, including lang().
	 *
	 * @var object The module object, if it exists and is installed/active.
	 **/
	var $mod = null;

	function __construct()
	{
		parent::__construct();
		
		if ($this->module_name == '')
		{
			trigger_error('Class ' . get_class($this) . ' doesn\'t define $module_name.  $this->mod will not be available');
		}
		else
		{
			$modules = cmsms()->modules;
			if ($modules != null)
			{
				foreach ($modules as $one_module => $v)
				{
					if ($one_module == $this->module_name)
					{
						$this->mod =& $v['object'];
						var_dump($this->mod);
					}
				}
			}
		}
	}
}

# vim:ts=4 sw=4 noet
?>