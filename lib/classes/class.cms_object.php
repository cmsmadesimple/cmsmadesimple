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
 * Base class.  Allows me to implement things that belong to all
 * classes inheriently.  All classes in CMSMS should extend this
 * if possible.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
abstract class CmsObject
{
	/**
	 * Base constructor.  Doesn't really do anything, but
	 * gives methods extending CmsObject something to call.
	 *
	 * @author Ted Kulp
	 **/
	public function __construct()
	{
		//echo 'instantiate - ', $this->__toString(), '<br />';
	}
	
	/**
	 * Base __sleep method.  Makes sure we're only setting variables
	 * that are not null before serialization.  This will increase
	 * performance and keep the db a bit smaller when we serialize
	 * for versioning.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	/*
	protected function __sleep()
	{
		$vars = (array)$this;
		foreach ($vars as $key => $val)
		{
			if (is_null($val))
			{
				unset($vars[$key]);
			}
		}   
		return array_keys($vars);
	}
	*/
	
	public function __toString()
	{
		sscanf((string)$this, "Object id #%d", $id);
		return "Object(".get_class($this).") id #$id";
	}
}

/**
 * @deprecated Deprecated.  Use CmsObject instead.
 **/
abstract class Object extends CmsObject
{
}

?>