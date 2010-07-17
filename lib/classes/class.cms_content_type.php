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

/**
 * ORM class for handling content type lookups in the database.
 * The confusing aspect to this is that the content types themsevles
 * are actually implemented by different classes -- classes that extend
 * CmsContentBase.  We do this because modules may implement their content
 * types in a totally independant way than a 1:1 relation.  It also allows us
 * to use the ORM to our advantage by using the $type lookup trick to easily
 * instantiate the right content object of the right type in one swoop.
 *
 * This particular class is only meant as a mapping to the records in the
 * content_types table.  This class also performs some basic lookups, and
 * content type related methods.
 *
 * @package CMS
 * @author Ted Kulp
 */
class CmsContentType extends CmsObjectRelationalMapping
{
	var $table = 'content_types';
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Returns an array of all the registered content types in the
	 * system.
	 *
	 * @return array List of content types.
	 */
	public static function get_all_content_types()
	{
		return cms_orm('ContentType')->find_all();
	}
	
	/**
	 * Returns the "default" content type for the system. Currently, just
	 * grabs the first in the database table. This will later be controlled
	 * by a preference.
	 *
	 * @return mixed The default content type.
	 * @author Ted Kulp
	 */
	public static function get_default_content_type()
	{
		return cms_orm('ContentType')->find(array('order' => 'id'));
	}
	
	/**
	 * Returns the name of the class for the given content type.
	 *
	 * @param string $name The name of the content type to look up
	 * @return string The name of the class for that content type
	 * @author Ted Kulp
	 */
	public static function get_content_type_class_by_type($name, $instantiate = false)
	{
		$type = cms_orm('CmsContentType')->find_by_name($name);
		if ($type != null && is_a($type, 'CmsContentType'))
		{
			if ($instantiate)
				return self::get_content_type_obj($type);
			else
				return self::get_content_type_class($type);
		}
		return null;
	}

	/**
	 * Returns the implementing object for the given content type
	 * ORM object.
	 *
	 * @param CmsContentType $type The class to look up
	 * @return mixed An instance of the implmenting object
	 * @author Ted Kulp
	 */
	public static function get_content_type_obj(CmsContentType $type)
	{
		if ($type)
		{
			if ($type->module == 'Core')
			{
				return new $type->class;
			}
		}
		return null;
	}
	
	/**
	 * Returns the implementing classname for the given content type
	 * ORM object.
	 *
	 * @param CmsContentType $type The class to look up
	 * @return string The name of the implmenting class
	 * @author Ted Kulp
	 */
	public static function get_content_type_class(CmsContentType $type)
	{
		if ($type)
		{
			if ($type->module == 'Core')
			{
				return $type->class;
			}
		}
		return null;
	}
}

# vim:ts=4 sw=4 noet
?>