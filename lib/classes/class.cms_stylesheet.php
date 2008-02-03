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
 * Represents a stylesheet in the database.
 *
 * @author Ted Kulp
 * @since 0.11
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsStylesheet extends CmsObjectRelationalMapping
{
	var $params = array('id' => -1, 'name' => '', 'value' => '', 'media_type' => 'all', 'active' => 1);
	var $table = 'stylesheets';
	var $media_types = array(
			array('name' => "all"),
			array('name' => "aural"),
			array('name' => "braille"),
			array('name' => "embossed"),
			array('name' => "handheld"),
			array('name' => "print"),
			array('name' => "projection"),
			array('name' => "screen"),
			array('name' => "tty"),
			array('name' => "tv")
		);
	
	public function validate()
	{
		$this->validate_not_blank('name', lang('nofieldgiven',array(lang('name'))));
		$this->validate_not_blank('value', lang('nofieldgiven',array(lang('content'))));
		if ($this->name != '')
		{
			$result = $this->find_all_by_name($this->name);
			if (count($result) > 0)
			{
				if ($result[0]->id != $this->id)
				{
					$this->add_validation_error(lang('stylesheetexists'));
				}
			}
		}
	}
	
	public function get_media_types_as_array()
	{
		return explode(', ', $this->media_type);
	}
	
	public function setup()
	{
		$this->create_has_and_belongs_to_many_association('templates', 'template', 'stylesheet_template_assoc', 'template_id', 'stylesheet_id', array('order' => 'order_num ASC'));
		$this->create_has_and_belongs_to_many_association('active_templates', 'template', 'stylesheet_template_assoc', 'template_id', 'stylesheet_id', array('order' => 'order_num ASC', 'conditions' => 'templates.active = 1'));
	}
	
	//Callback handlers
	function before_save()
	{
		CmsEvents::send_event( 'Core', ($this->id == -1 ? 'AddStylesheetPre' : 'EditStylesheetPre'), array('stylesheet' => &$this));
	}
	
	function after_save()
	{
		CmsEvents::send_event( 'Core', ($this->create_date == $this->modified_date ? 'AddStylesheetPost' : 'EditStylesheetPost'), array('stylesheet' => &$this));
	}
	
	function before_delete()
	{
		CmsEvents::send_event('Core', 'DeleteStylesheetPre', array('stylesheet' => &$this));
	}
	
	function after_delete()
	{
		CmsEvents::send_event('Core', 'DeleteStylesheetPost', array('stylesheet' => &$this));
	}
}

class Stylesheet extends CmsStylesheet {}

# vim:ts=4 sw=4 noet
?>