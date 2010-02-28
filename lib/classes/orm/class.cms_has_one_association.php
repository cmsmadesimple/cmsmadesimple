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

/**
 * Class for handling a one-to-one assocation.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsHasOneAssociation extends CmsObjectRelationalAssociation
{
	var $children = array();
	var $child_class = '';
	var $child_field = '';

	/**
	 * Create a new has_one association.
	 *
	 * @author Ted Kulp
	 **/
	public function __construct($association_name)
	{
		parent::__construct($association_name);
	}
	
	/**
	 * Returns the associated has_one association's objects.
	 *
	 * @return mixed The object, if it exists.  If not, null.
	 * @author Ted Kulp
	 **/
	public function get_data(&$obj)
	{
		$child = null;
		if ($obj->has_association($this->association_name))
		{
			$child = $obj->get_association($this->association_name);
		}
		else
		{
			if ($this->child_class != '' && $this->child_field != '')
			{
				$class = cms_orm($this->child_class);
				if ($this->parent_class->{$this->parent_class->id_field} > -1)
				{
					$queryattrs = $this->extra_params;
					$conditions = "{$this->child_field} = ?";
					$params = array($this->parent_class->{$this->parent_class->id_field});
				
					if (array_key_exists('conditions', $this->extra_params))
					{
						$conditions = "({$conditions}) AND ({$this->extra_params['conditions'][0]})";
						if (count($this->extra_params['conditions']) > 1)
						{
							$params = array_merge($params, array_slice($this->extra_params['conditions'], 1));
						}
					}
					$queryattrs['conditions'] = array_merge(array($conditions), $params);
				
					$child = $class->find($queryattrs);
					$obj->set_association($this->association_name, $child);
				}
			}
		}
		return $child;
	}
}

# vim:ts=4 sw=4 noet
?>