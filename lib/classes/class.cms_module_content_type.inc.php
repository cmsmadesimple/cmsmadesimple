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

/**
 * Class that module defined content types must extend.
 *
 * @since		0.9
 * @package		CMS
 */
class CmsModuleContentType extends ContentBase
{
	//What module do I belong to?  (needed for things like Lang to work right)
	function ModuleName()
	{
		return '';
	}

	function Lang($name, $params=array())
	{
		$cmsmodules =& cmsms()->modules;
		if (array_key_exists($this->ModuleName(), $cmsmodules))
		{
			return $cmsmodules[$this->ModuleName()]['object']->Lang($name, $params);
		}
		else
		{
			return 'ModuleName() not defined properly';
		}
	}

	/*
	* Returns the instance of the module this content type belongs to
	*
	*/
	function GetModuleInstance() 
	{
		$cmsmodules =& cmsms()->modules;
		if (array_key_exists($this->ModuleName(), $cmsmodules))
		{
			return $cmsmodules[$this->ModuleName()]['object'];
		}
		else
		{
			return 'ModuleName() not defined properly';
		}
	}
}

//CMSModuleContentType::register_orm_class('CMSModuleContentType');

?>