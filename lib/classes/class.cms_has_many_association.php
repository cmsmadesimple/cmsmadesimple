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
#$Id$

/**
 * Class for handling a one-to-many assocation.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsHasManyAssociation extends CmsObjectRelationalAssociation
{
	var $children = array();
	var $child_class = '';
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
	 * Returns the associated has_many association's objects.
	 *
	 * @return array Any array of objects, if they exist.
	 * @author Ted Kulp
	 **/
	public function get_data()
	{
		if (!$this->loaded && $this->child_class != '' && $this->child_field != '')
		{
			$class = cmsms()->{$this->child_class};
			if ($this->parent_class->{$this->parent_class->id_field} > -1)
			{
				$this->children = call_user_func_array(array(&$class, 'find_all_by_' . $this->child_field), $this->parent_class->{$this->parent_class->id_field});
			}
			$this->loaded = true;
		}
		return $this->children;
	}
}

# vim:ts=4 sw=4 noet
?>