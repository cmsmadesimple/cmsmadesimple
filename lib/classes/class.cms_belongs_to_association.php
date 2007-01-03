<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id: class.cms_has_many_association.php 3714 2007-01-03 04:40:17Z wishy $

/**
 * Class for handling a one-to-many assocation.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision: 3714 $
 * @modifiedby $LastChangedBy: wishy $
 * @lastmodified $Date: 2007-01-02 23:40:17 -0500 (Tue, 02 Jan 2007) $
 * @license GPL
 **/
class CmsBelongsToAssociation extends CmsObjectRelationalAssociation
{
	var $belongs_to_obj = null;
	var $belongs_to_class_name = '';
	var $child_field = '';

	/**
	 * Create a new has_many association.
	 *
	 * @author Ted Kulp
	 **/
	public function __construct(&$parent_class)
	{
		parent::__construct($parent_class);
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public function get_data()
	{
		if (!$this->loaded && $this->belongs_to_class_name != '' && $this->child_field != '')
		{
			$class = cmsms()->{$this->belongs_to_class_name};
			if ($this->parent_class->{$this->child_field} > -1)
			{
				$this->belongs_to_obj = call_user_func_array(array(&$class, 'find_by_id'), $this->parent_class->{$this->child_field});
			}
			$this->loaded = true;
		}
		return $this->belongs_to_obj;
	}
}

# vim:ts=4 sw=4 noet
?>