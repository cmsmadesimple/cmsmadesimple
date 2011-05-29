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

/**
 * Loads all plugins into the system
 *
 * @since 0.5
 * @param object The smarty object
 * @return void
 */


/**
 * A function to generate cross references between content types
 * This function can be used to generate which global content blocks are used by which content object
 *
 * @ignore
 * @access private
 * @param int the parent object id (usually a page template id or a page id.
 * @param string The parent object type
 * @param string The test content
 * @return void
 */
function do_cross_reference($parent_id, $parent_type, $content)
{
  $gCms = cmsms();
	$db = $gCms->GetDb();
	
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
			$result = &$db->Execute($selquery, array($name));
			while ($result && !$result->EOF)
			{
				$db->Execute($insquery, array($parent_id, $parent_type, $result->fields['htmlblob_id']));
				$result->MoveNext();
			}
			if ($result) $result->Close();
		}
	}
}

/**
 * A function to remove all cross references for a parent object
 *
 * @ignore
 * @access private
 * @param int The parent object id
 * @param string The parent object type
 * @return void
 */
function remove_cross_references($parent_id, $parent_type)
{
  $gCms = cmsms();
	$db = $gCms->GetDb();
	
	//Delete old ones from the database
	$query = 'DELETE FROM '.cms_db_prefix().'crossref WHERE parent_id = ? AND parent_type = ?';
	$db->Execute($query, array($parent_id, $parent_type));
}

/**
 * A function to remove all cross references for a child
 *
 * @ignore
 * @access private
 * @param int The child object id
 * @param string The child object type
 * @return void
 */
function remove_cross_references_by_child($child_id, $child_type)
{
  $gCms = cmsms();
	$db = $gCms->GetDb();
	
	//Delete old ones from the database
	$query = 'DELETE FROM '.cms_db_prefix().'crossref WHERE child_id = ? AND child_type = ?';
	$db->Execute($query, array($child_id, $child_type));
}

/**
 * A utility function to load the specified global content blocks and call the GlobalContentPrecompile method.
 *
 * @ignore
 * @access private
 * @param array Array containing the name of 1 global content block
 * @return void
 */
function global_content_regex_callback($matches)
{
  $gCms = cmsms();
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


/**
 * A convenience function to test if the site is marked as down according to the config panel.
 * This method includes handling the preference that indicates that site-down behaviour should
 * be disabled for certain IP address ranges.
 *
 * @return boolean
 */
function is_sitedown()
{
  global $CMS_INSTALL_PAGE;

  if( isset($CMS_INSTALL_PAGE) ) return TRUE;

  if( get_site_preference('enablesitedownmessage') !== '1' ) return FALSE;

  if( get_site_preference('sitedownexcludeadmins') )
    {
      $uid = get_userid(FALSE);
      if( $uid ) return FALSE;
    }

  if( !isset($_SERVER['REMOTE_ADDR']) ) return TRUE;
  $excludes = get_site_preference('sitedownexcludes','');
  if( empty($excludes) ) return TRUE;
 
  $tmp = explode(',',$excludes);
  $ret = cms_ipmatches($_SERVER['REMOTE_ADDR'],$excludes);
  if( $ret ) return FALSE;
  return TRUE;
}


function cms_call_udt($params,&$smarty)
{
  if( !isset($params['udt']) ) return;

  $udt = $params['udt'];
  unset($params['udt']);

  return UserTagOperations::get_instance()->CallUserTag($udt,$params);
}
# vim:ts=4 sw=4 noet
?>
