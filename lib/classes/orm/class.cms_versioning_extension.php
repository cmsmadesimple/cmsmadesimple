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
abstract class CmsVersioningExtension extends CmsObjectRelationalMapping
{
	private $do_version = true;

	function __construct()
	{
		parent::__construct();
		$this->params['version'] = 1; //Nice default.  It'll get overridden if necessary.
	}
	
	protected function before_save_caller()
	{
		parent::before_save_caller(); //Calls the actual before save
		
		if (!$this->do_version)
			return;
		
		//TODO: Load the existing version from the database, serialize it,
		//      and then save it to the versioned_objects table.  This will 
		//      have the old version number.
		//
		//      Then, take that id, increment it, and assign it to the
		//      version here.  Then we should be able to save.
		$orig_obj = $this->find_by_id_and_version($this->id, $this->version);
		
		//Maybe we never had a version (like, say, this is version 1).
		//If so, then there isn't much to serialize.
		if ($orig_obj)
		{
			CmsProfiler::get_instance()->mark('found original object');
			if ($this->save_version($orig_obj))
			{
				CmsProfiler::get_instance()->mark('saved original object -- increment version');
				$this->version = $this->version + 1;
			}
		}
	}
	
	protected function after_save_caller()
	{
		parent::after_save_caller(); //Calls the actual after save
	}
	
	public function get_version($version_number, $object_id = null)
	{
		$result = null;

		$db = cms_db();
		$object_id = ($object_id == NULL ? $this->id : $object_id);

		$dbresult = $db->Execute("SELECT * FROM ".cms_db_prefix()."serialized_versions WHERE type = ? AND version = ? AND object_id = ?", array(get_class($this), $version_number, $object_id));
		while ($dbresult && !$dbresult->EOF)
		{
			$result = unserialize(base64_decode($dbresult->fields['data']));
			$dbresult->MoveNext();
		}

		if ($dbresult) $dbresult->Close();
		
		return $result;
	}
	
	public function get_versions($only_ids = true, $object_id = null)
	{
		$result = array();
		$db = cms_db();

		$object_id = ($object_id == NULL ? $this->id : $object_id);
		
		$dbresult = $db->Execute("SELECT version FROM ".cms_db_prefix()."serialized_versions WHERE type = ? AND object_id = ? ORDER by version DESC", array(get_class($this), $object_id));
		while ($dbresult && !$dbresult->EOF)
		{
			if ($only_ids)
				$result[] = $dbresult->fields['version'];
			else
				$result[] = $this->get_version($dbresult->fields['version'], $object_id);
			
			$dbresult->MoveNext();
		}
		
		return $result;
	}
	
	public function save($create_version = true)
	{
		$this->do_version = $create_version;
		return parent::save();
	}
	
	public function save_version(&$object)
	{
		$db = cms_db();
		$serialized = base64_encode(serialize($object));
		$date = $db->DBTimeStamp(time());
		return $db->Execute("INSERT INTO " . cms_db_prefix() . "serialized_versions (version, type, object_id, data, create_date, modified_date) VALUES (?, ?, ?, ?, {$date}, {$date})", array($object->version, get_class($object), $object->id, $serialized));
	}
}

# vim:ts=4 sw=4 noet
?>