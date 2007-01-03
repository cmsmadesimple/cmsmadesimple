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
 * Base class for ORM assocations.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
abstract class CmsObjectRelationalAssociation extends CmsObject
{
	var $loaded = false;
	var $parent_class = null;

	/**
	 * Base constructor.  Doesn't really do anything, but
	 * gives methods extending CmsObject something to call.
	 *
	 * @author Ted Kulp
	 **/
	public function __construct(&$parent_class)
	{
		parent::__construct();
		$this->parent_class = $parent_class;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	public function get_data()
	{
		
	}
}

# vim:ts=4 sw=4 noet
?>