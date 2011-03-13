<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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
#
#$Id$

/**
 * Template class definition
 *
 * @package CMS
 * @license GPL
 */


/**
 * Generic template class. This can be used for any template or template related function.
 *
 * @since		0.6
 * @package		CMS
 * @license GPL
 */
class Template
{
	var $id;
	var $name;
	var $content;
	var $stylesheet;
	var $encoding;
	var $active;
	var $default;

	function Template()
	{
		$this->SetInitialValues();
	}

	function SetInitialValues()
	{
		$this->id = -1;
		$this->name = '';
		$this->content = '';
		$this->stylesheet = '';
		$this->encoding = '';
		$this->active = false;
		$this->default = false;
	}

	function Id()
	{
		return $this->id;
	}

	function Name()
	{
		return $this->name;
	}

	function UsageCount()
	{
		$gCms = cmsms();
		$templateops = $gCms->GetTemplateOperations();
		if ($this->id > -1)
			return $templateops->UsageCount($this->id);
		else
			return 0;
	}

	function Save()
	{
		$result = false;
		
		$gCms = cmsms();
		$templateops = $gCms->GetTemplateOperations();
		
		if ($this->id > -1)
		{
			$result = $templateops->UpdateTemplate($this);
		}
		else
		{
			$newid = $templateops->InsertTemplate($this);
			if ($newid > -1)
			{
				$this->id = $newid;
				$result = true;
			}

		}

		return $result;
	}

	function Delete()
	{
		$result = false;

		if ($this->id > -1)
		{
			$gCms = cmsms();
			$templateops = $gCms->GetTemplateOperations();
			$result = $templateops->DeleteTemplateByID($this->id);
			if ($result)
			{
				$this->SetInitialValues();
			}
		}

		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>
