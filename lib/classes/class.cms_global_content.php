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
 * Represents a global content block in the database.
 *
 * @author Ted Kulp
 * @since 0.6
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsGlobalContent extends CmsVersioningExtension
{	
	var $params = array('id' => -1, 'name' => '', 'content' => '', 'owner' => -1);
	var $field_maps = array('htmlblob_id' => 'id', 'htmlblob_name' => 'name', 'html' => 'content');
	var $table = 'htmlblobs';
	var $sequence = 'htmlblobs_seq';

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
		CmsEvents::send_event( 'Core', ($this->id == -1 ? 'AddGlobalContentPre' : 'EditGlobalContentPre'), array('global_content' => &$this));
	}
	
	function after_save()
	{
		CmsEvents::send_event( 'Core', ($this->create_date == $this->modified_date ? 'AddGlobalContentPost' : 'EditGlobalContentPost'), array('global_content' => &$this));
		CmsCache::get_instance()->clear();
	}
	
	function before_delete()
	{
		CmsEvents::send_event('Core', 'DeleteGlobalContentPre', array('global_content' => &$this));
	}
	
	function after_delete()
	{
		CmsEvents::send_event('Core', 'DeleteGlobalContentPost', array('global_content' => &$this));
		CmsCache::get_instance()->clear();
	}
}

/**
 * @deprecated Deprecated.  Use CmsGlobalContent instead.
 **/
/*
class GlobalContent extends CmsGlobalContent
{
}
*/

/**
 * @deprecated Deprecated.  Use CmsGlobalContent instead.
 **/
class HtmlBlob extends CmsGlobalContent
{
}

# vim:ts=4 sw=4 noet
?>
