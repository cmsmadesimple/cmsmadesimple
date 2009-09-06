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
 * Generic html blob class. This can be used for any logged in blob or blob related function.
 *
 * @since		0.6
 * @package		CMS
 */
class CmsGlobalContent extends CmsObjectRelationalMapping
{
	var $params = array('id' => -1, 'name' => '', 'owner' => -1, 'content' => '');
	var $field_maps = array('htmlblob_name' => 'name', 'html' => 'content', 'htmlblob_id' => 'id');
	var $table = 'htmlblobs';
	var $sequence = 'htmlblobs_seq';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function validate()
	{
		$this->validate_not_blank('name', lang('nofieldgiven', array(lang('name'))));
		$this->validate_not_blank('content', lang('nofieldgiven', array(lang('content'))));
		if ($this->name != '')
		{
			$result = $this->find_all_by_name($this->name);
			if (count($result) > 0)
			{
				if ($result[0]->id != $this->id)
				{
					$this->add_validation_error(lang('htmlblobexists'));
				}
			}
		}
	}

	function IsOwner($user_id)
	{
		$result = false;
		
		global $gCms;
		$gcbops =& $gCms->GetGlobalContentOperations();

		if ($this->id > -1)
		{
			$result = $gcbops->CheckOwnership($this->id, $user_id);
		}

		return $result;
	}

	function IsAuthor($user_id)
	{
		$result = false;
		
		global $gCms;
		$gcbops =& $gCms->GetGlobalContentOperations();

		if ($this->id > -1)
		{
			$result = $gcbops->CheckAuthorship($this->id, $user_id);
		}

		return $result;
	}

	function ClearAuthors()
	{
		$result = false;
		
		global $gCms;
		$gcbops =& $gCms->GetGlobalContentOperations();

		if ($this->id > -1)
		{
			$gcbops->ClearAdditionalEditors($this->id);
			$result = true;
		}

		return $result;
	}

	function AddAuthor($user_id)
	{
		$result = false;
		
		global $gCms;
		$gcbops =& $gCms->GetGlobalContentOperations();

		if ($this->id > -1)
		{
			$gcbops->InsertAdditionalEditors($this->id, $user_id);
			$result = true;
		}

		return $result;
	}
	
	//Callback handlers
	function before_save()
	{
		Events::SendEvent('Core', ($this->id == -1 ? 'AddGlobalContentPre' : 'EditGlobalContentPre'), array('global_content' => &$this));
	}
	
	function after_save()
	{
		Events::SendEvent('Core', ($this->create_date == $this->modified_date ? 'AddGlobalContentPost' : 'EditGlobalContentPost'), array('global_content' => &$this));
		//CmsCache::clear();
	}
	
	function before_delete()
	{
		Events::SendEvent('Core', 'DeleteGlobalContentPre', array('global_content' => &$this));
	}
	
	function after_delete()
	{
		Events::SendEvent('Core', 'DeleteGlobalContentPost', array('global_content' => &$this));
		//CmsCache::clear();
	}
}

/**
 * @deprecated Deprecated.  Use CmsGlobalContent instead.
 */
class GlobalContent extends CmsGlobalContent
{
}

class HtmlBlob extends CmsGlobalContent
{
}

# vim:ts=4 sw=4 noet
?>
