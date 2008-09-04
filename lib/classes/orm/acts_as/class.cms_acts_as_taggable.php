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

class CmsActsAsTaggable extends CmsActsAs
{
	function __construct()
	{
		parent::__construct();
	}
	
	function before_save(&$obj)
	{
		$obj->begin_transaction();
		
		CmsTag::remove_all_tags_for_object(get_class($obj), $obj->id);
	}
	
	function after_save(&$obj, &$result)
	{
		foreach (CmsTag::parse_tags($obj->tags) as $one_tag)
		{
			CmsTag::add_tagged_object($one_tag, get_class($obj), $obj->id);
		}
		
		$result = $obj->complete_transaction();
	}
	
	public function before_delete(&$obj)
	{
		CmsTag::remove_all_tags_for_object(get_class($obj), $obj->id);
	}
	
	function check_variables_are_set(&$obj)
	{
		if (!isset($obj->tags))
		{
			die('Must set the $tags variables to use CmsActsAsTaggable');
		}
	}
}

# vim:ts=4 sw=4 noet
?>