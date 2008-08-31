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
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * Extension to the ORM system to handle automatic versioning of saved
 * objects and some methods to retrieve those versioned instances.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 */
class CmsActsAsVersioned extends CmsActsAs
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function before_save(&$obj)
	{
		if ($obj->dirty)
		{
			if ($obj->params['version'] == null || $obj->params['version'] < 1)
				$obj->version = 1;

			//TODO: Load the existing version from the database, serialize it,
			//      and then save it to the versioned_objects table.  This will 
			//      have the old version number.
			//
			//      Then, take that id, increment it, and assign it to the
			//      version here.  Then we should be able to save.
			$orig_obj = $obj->find_by_id_and_version($obj->id, $obj->version);
		
			//Maybe we never had a version (like, say, this is version 1).
			//If so, then there isn't much to serialize.
			if ($orig_obj)
			{
				CmsProfiler::get_instance()->mark('found original object');
				if ($obj->save_version($orig_obj))
				{
					CmsProfiler::get_instance()->mark('saved original object -- increment version');
					$obj->version = $obj->version + 1;
				}
			}
		}
	}
	
	public function get_version(&$obj, $version_number)
	{
		$result = null;

		$db = cms_db();

		$dbresult = $db->Execute("SELECT * FROM ".cms_db_prefix()."serialized_versions WHERE type = ? AND version = ? AND object_id = ?", array(get_class($obj), $version_number, $obj->id));
		while ($dbresult && !$dbresult->EOF)
		{
			$result = unserialize(base64_decode($dbresult->fields['data']));
			$dbresult->MoveNext();
		}

		if ($dbresult) $dbresult->Close();
		
		return $result;
	}
	
	public function get_versions(&$obj, $only_ids = true)
	{
		$result = array();
		$db = cms_db();
		
		$dbresult = $db->Execute("SELECT version FROM ".cms_db_prefix()."serialized_versions WHERE type = ? AND object_id = ? ORDER by version DESC", array(get_class($obj), $obj->id));
		while ($dbresult && !$dbresult->EOF)
		{
			if ($only_ids)
				$result[] = $dbresult->fields['version'];
			else
				$result[] = $obj->get_version($dbresult->fields['version'], $object_id);
			
			$dbresult->MoveNext();
		}
		
		return $result;
	}
	
	public function save_version(&$obj)
	{
		$db = cms_db();
		$serialized = base64_encode(serialize($obj));
		$date = $db->DBTimeStamp(time());
		return $db->Execute("INSERT INTO " . cms_db_prefix() . "serialized_versions (version, type, object_id, data, create_date, modified_date) VALUES (?, ?, ?, ?, {$date}, {$date})", array($obj->version, get_class($obj), $obj->id, $serialized));
	}
}

# vim:ts=4 sw=4 noet
?>