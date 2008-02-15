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
 * Represents a user defined tag in the database.
 *
 * @author Ted Kulp
 * @since 0.1
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
 
class CmsUserTag extends CmsObjectRelationalMapping
{	
	var $params = array('id' => -1, 'name' => '', 'code' => '');
	var $field_maps = array('userplugin_name' => 'name', 'content' => 'code');
	var $table = 'userplugins';

	public function validate()
	{
		// Blank field validation
		$this->validate_not_blank('name', lang('nofieldgiven',array(lang('name'))));
		$this->validate_not_blank('code', lang('nofieldgiven',array(lang('code'))));		
		
		// Duplicate name validation
		if ($this->name != '')
		{
			$result = $this->find_all_by_name($this->name);
			if (count($result) > 0)
			{
				if ($result[0]->id != $this->id)
				{
					$this->add_validation_error(lang('usertagexists'));
				}
			}
		}
		
		// PHP code validation
		srand();
		ob_start();
		if (eval('function testfunction'.rand().'() {'.$this->code.'}') === FALSE)
		{
			$this->add_validation_error(lang('invalidcode'));
            $buffer = ob_get_clean();
			$this->add_validation_error(preg_replace('/<br \/>/', '', $buffer ));
		}
		else
		{
			ob_end_clean();
		}
	}
	
	public function call(&$params)
	{
		$result = null;
		$smarty = cms_smarty();

		$functionname = "tmpcallusertag_".$this->name."_userplugin_function";
		if (function_exists($functionname) || 
			!(@eval('function '.$functionname.'(&$params, &$smarty) {'.$this->code.'}') === FALSE))
		{
			
			$result = call_user_func_array($functionname, array(&$params, &$smarty));
		}
		
		return $result;
	}
	
	//Callback handlers
	function before_save()
	{
		CmsEvents::send_event( 'Core', ($this->id == -1 ? 'AddUserDefinedTagPre' : 'EditUserDefinedTagPre'), array('user_tag' => &$this));
	}
	
	function after_save()
	{
		CmsEvents::send_event( 'Core', ($this->create_date == $this->modified_date ? 'AddUserDefinedTagPost' : 'EditUserDefinedTagPost'), array('user_tag' => &$this));
		CmsCache::clear();
	}
	
	function before_delete()
	{
		CmsEvents::send_event('Core', 'DeleteUserDefinedTagPre', array('user_tag' => &$this));
	}
	
	function after_delete()
	{
		CmsEvents::send_event('Core', 'DeleteUserDefinedTagPost', array('user_tag' => &$this));
		CmsCache::clear();
	}
}

/**
 * @deprecated Deprecated.  Use CmsUserTag instead.
 **/
/*
class UserTag extends CmsUserTag
{
}
*/

/**
 * @deprecated Deprecated.  Use CmsUserTag instead.
 **/
class UserTag extends CmsUserTag
{
}

# vim:ts=4 sw=4 noet
?>
