<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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
 * Handles content related functions
 *
 * @package CMS
 */
$sorted_sections = array();
$sorted_content = array();

/**
 * Loads all plugins into the system
 *
 * @since 0.5
 */
function load_plugins(&$smarty)
{
	return CmsSmarty::load_plugins();
}

function search_plugins(&$smarty, &$plugins, $dir, $caching)
{
	return CmsSmarty::search_plugins($plugins, $dir, $caching);
}

function do_cross_reference($parent_id, $parent_type, $content)
{
	global $gCms;
	$db =& $gCms->GetDb();
	
	//Delete old ones from the database
	$query = 'DELETE FROM '.cms_db_prefix().'crossref WHERE parent_id = ? AND parent_type = ?';
	$db->Execute($query, array($parent_id, $parent_type));
	
	//Do global content blocks
	$matches = array();
	preg_match_all('/\{(?:html_blob|global_content).*?name=["\']([^"]+)["\'].*?\}/', $content, $matches);
	if (isset($matches[1]))
	{
		$selquery = 'SELECT htmlblob_id FROM '.cms_db_prefix().'htmlblobs WHERE htmlblob_name = ?';
		$insquery = 'INSERT INTO '.cms_db_prefix().'crossref (parent_id, parent_type, child_id, child_type, create_date, modified_date)
						VALUES (?,?,?,\'global_content\','.$db->DBTimeStamp(time()).','.$db->DBTimeStamp(time()).')';
		foreach ($matches[1] as $name)
		{
			$result = $db->Execute($selquery, array($name));
			while ($result && !$result->EOF)
			{
				$db->Execute($insquery, array($parent_id, $parent_type, $result->fields['htmlblob_id']));
				$result->MoveNext();
			}
			if ($result) $result->Close();
		}
	}
}

function remove_cross_references($parent_id, $parent_type)
{
	global $gCms;
	$db =& $gCms->GetDb();
	
	//Delete old ones from the database
	$query = 'DELETE FROM '.cms_db_prefix().'crossref WHERE parent_id = ? AND parent_type = ?';
	$db->Execute($query, array($parent_id, $parent_type));
}

function remove_cross_references_by_child($child_id, $child_type)
{
	global $gCms;
	$db =& $gCms->GetDb();
	
	//Delete old ones from the database
	$query = 'DELETE FROM '.cms_db_prefix().'crossref WHERE child_id = ? AND child_type = ?';
	$db->Execute($query, array($child_id, $child_type));
}

function global_content_regex_callback($matches)
{
	global $gCms;
	if (isset($matches[1]))
	{
		$gcbops =& $gCms->GetGlobalContentOperations();
		$oneblob = $gcbops->LoadHtmlBlobByName($matches[1]);
		if ($oneblob)
		{
			$text = $oneblob->content;
			Events::SendEvent('Core', 'GlobalContentPreCompile', array('content' => &$text));

			return $text;
		}
		else
		{
			return "<!-- Html blob '" . $matches[1] . "' does not exist  -->";
		}
	}
	else
	{
		return "<!-- Html blob has no name parameter -->";
	}
}


function is_sitedown()
{
	if( CmsApplication::get_preference('enablesitedownmessage', '0') !== '1' ) return FALSE;
	$excludes = CmsApplication::get_preference('sitedownexcludes','');
	if( !isset($_SERVER['REMOTE_ADDR']) ) return TRUE;
	if( empty($excludes) ) return TRUE;

	$ret = cms_ipmatches($_SERVER['REMOTE_ADDR'],$excludes);
	if( $ret ) return FALSE;
	return TRUE;
}

# vim:ts=4 sw=4 noet
?>
