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
 * Extend smarty for moduleinterface.php
 *
 * @package CMS
 */

/* a duplicate function?
function cms_mapi_create_permission($cms, $permission_name, $permission_text) {

	$db =& $cms->db;

	$query = "SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name =" . $db->qstr($permission_name); 
	$result = $db->Execute($query);

	if ($result && $result->RecordCount() < 1) {

		$new_id = $db->GenID(cms_db_prefix()."permissions_seq");
		$query = "INSERT INTO ".cms_db_prefix()."permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES ($new_id, ".$db->qstr($permission_name).",".$db->qstr($permission_text).",".$db->DBTimeStamp(time()).",".$db->DBTimeStamp(time()).")";
		$db->Execute($query);
	}
}
*/

function cms_module_plugin($params,&$smarty)
{
	global $gCms;
	$cmsmodules = &$gCms->modules;

	$id = 'm' . ++$gCms->variables["modulenum"];

	$returnid = '';
	if (isset($gCms->variables['pageinfo']) && isset($gCms->variables['pageinfo']->content_id))
	  {
	    $returnid = $gCms->variables['pageinfo']->content_id;
	  }
	$params = array_merge($params, GetModuleParameters($id));

	$modulename = '';
	$action = 'default';
	$inline = false;

	$checkid = '';

	if (isset($params['module'])) 
	  $modulename = $params['module'];
	else
	  return '<!-- ERROR: module name not specified -->';

	if (isset($params['idprefix'])) $id = trim($params['idprefix']);
	if (isset($params['action']) && $params['action'] != '')
	{
	  // action was set in the module tag
	  $action = $params['action'];
	}

	if (isset($_REQUEST['id'])) //Not really needed now...
	{
	  $checkid = $_REQUEST['id'];
	}
	else if (isset($_REQUEST['mact']))
	{
	  // we're handling an action
	  $ary = explode(',', cms_htmlentities($_REQUEST['mact']), 4);
	  $mactmodulename = (isset($ary[0])?$ary[0]:'');
	  if (strtolower($mactmodulename) == strtolower($params['module']))
	    {
	      $checkid = (isset($ary[1])?$ary[1]:'');
	      $mactaction = (isset($ary[2])?$ary[2]:'');
	    }
	  $mactinline = (isset($ary[3]) && $ary[3] == 1?true:false);
	  
	  if ($checkid == $id)
	    {
	      // the action is for this instance of the module
	      $inline = $mactinline;
	      if( $inline == true )
		{
		  // and we're inline (the results are supposed to replace
		  // the tag, not {content}
		  $action = $mactaction;
		}
	    }
	}

	if( $action == '' ) $action = 'default'; // probably not needed, but safe

	if (isset($cmsmodules))
	{
		$modulename = $params['module'];

		foreach ($cmsmodules as $key=>$value)
		{
			if (strtolower($modulename) == strtolower($key))
			{
				$modulename = $key;
			}
		}

		if (isset($modulename))
		{
			if (isset($cmsmodules[$modulename]))
			{
				if (isset($cmsmodules[$modulename]['object'])
					&& $cmsmodules[$modulename]['installed'] == true
					&& $cmsmodules[$modulename]['active'] == true
					&& $cmsmodules[$modulename]['object']->IsPluginModule())
				{
					@ob_start();

					$result = $cmsmodules[$modulename]['object']->DoActionBase($action, $id, $params, $returnid);
					if ($result !== FALSE)
					{
						echo $result;
					}
					$modresult = @ob_get_contents();
					@ob_end_clean();

					if( isset($params['assign']) )
					  {
					    $smarty->assign(trim($params['assign']),$modresult);
					    return;
					  }
					return $modresult;

				}
				else
				{
					return "<!-- Not a tag module -->\n";
				}
			}
		}
	}
}

# vim:ts=4 sw=4 noet
?>
