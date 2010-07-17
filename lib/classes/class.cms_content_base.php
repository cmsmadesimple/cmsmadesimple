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
 * Base content type class.  Extend this to create new content types for
 * the system.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsContentBase extends CmsObjectRelationalMapping
{
	var $table = 'content';
	
	function __construct()
	{
		parent::__construct();
		$this->type = get_class();
	}
	
	function friendly_name()
	{
		return 'No Content Type';
	}
	
	function render()
	{
		return '';
	}
	
	function get_edit_template()
	{
		return '';
	}
	
	protected function instantiate_class($type, $row)
	{
		$ret = null;
		
		if (isset($row['content_type']) && $row['content_type'] != '')
			$ret = CmsContentType::get_content_type_class_by_type($row['content_type'], true);
		
		if ($ret != null)
			return $ret;
		else
			return parent::instantiate_class($type, $row);
	}
	
	function get_edit_form($block_name = 'default', $tpl_file = null)
	{
		$file_name = underscore(get_class($this));
		if ($tpl_file == null)
		{
			if ($this->get_edit_template() != '')
			{
				$tpl_file = $this->get_edit_template();
			}
			else
			{
				$tpl_file = cms_join_path(dirname(__FILE__), 'content_types', 'templates', 'edit.' . $file_name . '.tpl');
			}
		}
		
		if (is_file($tpl_file))
		{
			cms_smarty()->assign_by_ref('obj', $this);
			cms_smarty()->assign('block_name', $block_name);
			return cms_smarty()->fetch($tpl_file);
		}
		return '';
	}
	
	function before_delete()
	{
		
	}
}

# vim:ts=4 sw=4 noet
?>
