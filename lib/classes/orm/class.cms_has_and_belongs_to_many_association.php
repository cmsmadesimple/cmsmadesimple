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
class CmsHasAndBelongsToManyAssociation extends CmsObjectRelationalAssociation
{
	var $child_class = '';
	var $join_table = '';
	var $join_other_id_field = '';
	var $join_this_id_field = '';

	/**
	 * Create a new has_many association.
	 *
	 * @author Ted Kulp
	 **/
	public function __construct($association_name)
	{
		parent::__construct($association_name);
	}
	
	/**
	 * Returns the associated has_many association's objects.
	 *
	 * @return array Any array of objects, if they exist.
	 * @author Ted Kulp
	 **/
	public function get_data(&$obj)
	{
		return $this->fill_data($obj);
	}
	
	private function fill_data(&$obj)
	{
		$ary = null;
		if ($obj->has_association($this->association_name))
		{
			var_dump('found');
			$ary = $obj->get_association($this->association_name);
		}
		else
		{
			$ary = new CmsAssociationCollection();
			if ($this->child_class != '' && $this->join_table != '')
			{
				$class = cms_orm()->{$this->child_class};
				$table = $class->get_table();
				$other_id_field = $class->id_field;
			
				if ($obj->{$obj->id_field} > -1)
				{
					$queryattrs = $this->extra_params;
					$queryattrs['joins'] = "INNER JOIN {$this->join_table} ON {$this->join_table}.{$this->join_other_id_field} = {$table}.{$other_id_field}";
					$queryattrs['conditions'] = array("{$this->join_table}.{$this->join_this_id_field} = ?", $obj->{$obj->id_field});
					$ary->children = $class->find_all($queryattrs);
					$obj->set_association($this->association_name, $ary);
				}
			}
		}
		return $ary;
	}
}

# vim:ts=4 sw=4 noet
?>