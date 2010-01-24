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
	global $gCms;
	$plugins = &$gCms->cmsplugins;
	$userplugins = &$gCms->userplugins;
	$userpluginfunctions = &$gCms->userpluginfunctions;
	$db = &$gCms->GetDb();
	if (isset($db) && $db->IsConnected())
	{
		#if (@is_dir(dirname(dirname(__FILE__))."/plugins/cache"))
		#{
		#	search_plugins($smarty, $plugins, dirname(dirname(__FILE__))."/plugins/cache", true);
		#}
		search_plugins($smarty, $plugins, dirname(dirname(__FILE__))."/plugins", false);

		$query = "SELECT * FROM ".cms_db_prefix()."userplugins";
		$result = $db->Execute($query);
		while ($result && !$result->EOF)
		{
			if (!in_array($result->fields['userplugin_name'], $plugins))
			{
				$plugins[] =& $result->fields['userplugin_name'];
				$userplugins[$result->fields['userplugin_name']] = $result->fields['userplugin_id'];
				$functionname = "cms_tmp_".$result->fields['userplugin_name']."_userplugin_function";
				//Only register valid code
				if (!(@eval('function '.$functionname.'($params, &$smarty) {'.$result->fields['code'].'}') === FALSE))
				{
					$smarty->register_function($result->fields['userplugin_name'], $functionname, false);

					//Register the function in a hash so that we can call it from other places by name
					$userpluginfunctions[$result->fields['userplugin_name']] = $functionname;
				}
			}
			$result->MoveNext();
		}
		sort($plugins);
	}
}

function search_plugins(&$smarty, &$plugins, $dir, $caching)
{
	global $CMS_LOAD_ALL_PLUGINS;

	$types=array('function','compiler','prefilter','postfilter','outputfilter','modifier','block');
	$handle=opendir($dir);
	while ($file = readdir($handle))
	{
		// This hides the dummy function.summarize.php
		// (function.summarize.php was renamed to modifier.summarize.php in 1.0.3)
		// This code can be deleted once the dummy is removed from the distribution
		// TODO: DELETE
		if (
			$file == 'function.summarize.php' &&
			substr(file_get_contents(cms_join_path($dir, $file)), 9, 9) == '__DUMMY__'
		)
		{
				continue;
		}
		// END TODO: DELETE

		$path_parts = pathinfo($file);
		if (isset($path_parts['extension']) && $path_parts['extension'] == 'php')
		{
			//Valid plugins will always have a 3 part filename
			$filearray = explode('.', $path_parts['basename']);
			if (count($filearray == 3))
			{
				$filename = cms_join_path($dir, $file);
				//The part we care about is the middle one...
				$file = $filearray[1];
				if (!isset($plugins[$file]) && in_array($filearray[0],$types))
				{
					$key=array_search($filearray[0],$types);
					$load=true;
					switch ($key)
					{
						case 0:
								if (isset($CMS_LOAD_ALL_PLUGINS))
									$smarty->register_function($file, "smarty_cms_function_" . $file, $caching);
								else $load=false;
								break;
						case 1:
								$smarty->register_compiler_function($file, "smarty_cms_compiler_" .  $file, $caching);
								break;
						case 2:
								$smarty->register_prefilter("smarty_cms_prefilter_" . $file);
								break;
						case 3:
								$smarty->register_postfilter("smarty_cms_postfilter_" . $file);
								break;
						case 4:
								$smarty->register_outputfilter("smarty_cms_outputfilter_" . $file);
								break;
						case 5:
								$smarty->register_modifier($file, "smarty_cms_modifier_" . $file);
								break;
						case 6:
								$smarty->register_block($file, "smarty_cms_block_" . $file);
								break;
					}
					if ($load){ $plugins[]=$file;
						require_once($filename);}
				}
			}
		}
	}
	closedir($handle);
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
