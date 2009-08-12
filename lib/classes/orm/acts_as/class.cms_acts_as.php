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
 * Base class for "acts as" ORM model extensions
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsActsAs extends CmsObject
{
	/**
	 * Create a new acts_as.
	 *
	 * @author Ted Kulp
	 **/
	public function __construct()
	{
		parent::__construct();
	}
	
	public function setup(&$obj)
	{
		
	}
	
	public function before_load($type, $fields)
	{

	}
	
	public function after_load(&$obj)
	{

	}
	
	public function before_validation(&$obj)
	{
		
	}
	
	public function before_save(&$obj)
	{
		
	}
	
	public function after_save(&$obj, &$result)
	{
		
	}
	
	public function before_delete(&$obj)
	{
		
	}
	
	public function after_delete(&$obj)
	{
		
	}

}

# vim:ts=4 sw=4 noet
?>