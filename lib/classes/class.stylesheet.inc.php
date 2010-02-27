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
 * Generic stylesheet class. This can be used for any logged in stylesheet or stylesheet related function.
 *
 * @since		0.11
 * @package		CMS
 */
class Stylesheet
{
	var $id;
	var $name;
	var $value;
	var $media_type;

	function Stylesheet()
	{
		$this->SetInitialValues();
	}

	function SetInitialValues()
	{
		$this->id = -1;
		$this->name = '';
		$this->value = '';
		$this->media_type = '';
	}
	
	function Id()
	{
		return $this->id;
	}

	function Name()
	{
		return $this->name;
	}

	function Save()
	{
		$result = false;
		
		global $gCms;
		$styleops =& $gCms->GetStylesheetOperations();
		
		if ($this->id > -1)
		{
			$result = $styleops->UpdateStylesheet($this);
		}
		else
		{
			$newid = $styleops->InsertStylesheet($this);
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
			global $gCms;
			$styleops =& $gCms->GetStylesheetOperations();
			$result = $styleops->DeleteStylesheetByID($this->id);
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
