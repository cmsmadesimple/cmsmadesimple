<?php
#CMS - CMS Made Simple
#(c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * Generic template class. This can be used for any template or template related function.
 *
 * @since		0.6
 * @package		CMS
 */
class CmsTemplate extends CmsObjectRelationalMapping
{	
	var $params = array('id' => -1, 'name' => '', 'content' => '', 'stylesheet' => '', 'encoding' => '', 'active' => true, 'default' => false);
	var $field_maps = array('template_id' => 'id', 'template_name' => 'name', 'default_template' => 'default', 'template_content' => 'content');
	var $table = 'templates';
	var $sequence = 'templates_seq';

	function UsageCount()
	{
		return $this->usage_count();
	}
	
	function usage_count()
	{
		$templateops = cmsms()->GetTemplateOperations();
		if ($this->id > -1)
			return $templateops->UsageCount($this->id);
		else
			return 0;
	}
	
	function validate()
	{
		$this->validate_not_blank('name', lang('nofieldgiven',array(lang('title'))));
		$this->validate_not_blank('content', lang('nofieldgiven',array(lang('content'))));
		if ($this->name != '')
		{
			$result = $this->find_all_by_name($this->name);
			if (count($result) > 0)
			{
				if ($result[0]->id != $this->id)
				{
					$this->add_validation_error(lang('templateexists'));
				}
			}
		}
	}
	
	function after_save()
	{
		CmsPageCache::get_instance()->clear();
	}
}

class Template extends CmsTemplate {}

# vim:ts=4 sw=4 noet
?>