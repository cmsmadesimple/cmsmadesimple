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

class CmsTag extends CmsObjectRelationalMapping
{
	var $table = 'tags';

	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Returns number of objects tagged with the given tag name.
	 *
	 * @param string The tag_name to look up
	 * @return int Returns a count of the found objects with the given tag name
	 */
	public static function get_tagged_object_count($tag_name)
	{
		$cms_db_prefix = CMS_DB_PREFIX;
		return cms_db()->GetOne("select count(*) from cms_tag_objects o where o.tag_id in (select t.id from cms_tags t where t.name = ?)", array(strtolower($tag_name)));
	}
	
	/**
	 * Returns objects tagged with the given tag name.
	 *
	 * @param string The tag_name to look up
	 * @return array Returns an array of found objects with the given tag name
	 */
	public static function get_tagged_objects($tag_name)
	{
		$return = array();

		$cms_db_prefix = CMS_DB_PREFIX;
		$dbresult = cms_db()->Execute("select * from cms_tag_objects o where o.tag_id in (select t.id from cms_tags t where t.name = ?)", array(strtolower($tag_name)));
		while ($dbresult && !$dbresult->EOF)
		{
			$obj = cms_orm($dbresult->fields['type'])->find_by_id($dbresult->fields['object_id']);
			if ($obj != null)
				$return[] = $obj;

			$dbresult->MoveNext();
		}

		return $return;
	}
	
	public static function add_tagged_object($tag_name, $type, $object_id)
	{
		$cms_db_prefix = CMS_DB_PREFIX;

		$tag = cms_orm('cms_tag')->find_by_name($tag_name);
		if ($tag == null)
		{
			$tag = new CmsTag;
			$tag->name = $tag_name;
			$tag->save();
		}
		
		if ($tag->id != null && $tag->id > -1)
		{
			cms_db()->Execute("INSERT INTO {$cms_db_prefix}tag_objects (tag_id, type, object_id) VALUES (?, ?, ?)", array($tag->id, $type, $object_id));
		}
	}
	
	public static function remove_tagged_object($tag_name, $type, $object_id)
	{
		$cms_db_prefix = CMS_DB_PREFIX;

		$tag = cms_orm('cms_tag')->find_by_name($tag_name);
		if ($tag != null)
		{
			$result = cms_db()->Execute("DELETE FROM {$cms_db_prefix}tag_objects WHERE tag_id = ? AND type = ? AND object_id = ?", array($tag->id, $type, $object_id));
		}

		self::clean_out_unused_tags();
	}
	
	public static function remove_all_tags_for_object($type, $object_id)
	{
		$cms_db_prefix = CMS_DB_PREFIX;

		$result = cms_db()->Execute("DELETE FROM {$cms_db_prefix}tag_objects WHERE type = ? AND object_id = ?", array($type, $object_id));
		self::clean_out_unused_tags();
	}
	
	public static function clean_out_unused_tags()
	{
		$cms_db_prefix = CMS_DB_PREFIX;

		cms_db()->Execute("DELETE FROM {$cms_db_prefix}tags WHERE id not in (SELECT distinct tag_id FROM {$cms_db_prefix}tag_objects)");
	}

	/**
	 * parse_tags
	 *
	 *  Method to parse tags out of a string and into an array.
	 *  Taken from: http://code.google.com/p/freetag/
	 *
	 * @param string String to parse.
	 *
	 * @return array Returns an array of the raw "tags" parsed according to the freetag settings.
	 */
	public static function parse_tags($tag_string)
	{
		$newwords = array();
		if ($tag_string == '')
		{
			// If the tag string is empty, return the empty set.
			return $newwords;
		}

		# Perform tag parsing
		$query = strtolower(trim($tag_string));
		$words = preg_split('/(")/', $query, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
		$delim = 0;
		foreach ($words as $key => $word)
		{
			if ($word == '"')
			{
				$delim++;
				continue;
			}
			if (($delim % 2 == 1) && $words[$key - 1] == '"')
			{
				$newwords[] = $word;
			}
			else
			{
				$newwords = array_merge($newwords, preg_split('/[,\s]+/', $word, -1, PREG_SPLIT_NO_EMPTY));
			}
		}
		return $newwords;
	}
}

# vim:ts=4 sw=4 noet
?>