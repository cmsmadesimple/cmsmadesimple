<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (wishy@users.sf.net)
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


/**
 * A function to call a module as a smarty plugin
 * This method is used by the {cms_module} plugin, and internally when {ModuleName} is called
 *
 * @internal
 * @access private
 * @param array A hash of parameters 
 * @param object The smarty object
 * @return string The module output
 */
function cms_module_plugin($params,&$smarty)
{
	$mid_cache = cms_utils::get_app_data('mid_cache');
	if( empty($mid_cache) )
	  {
	    $mid_cache = array();
	  }
	for( $i = 0; $i < 10; $i++ )
	{
	  $tmp = $i;
	  foreach($params as $key=>$value)
	    $tmp .= $key.'='.$value;
	  $id = 'm'.substr(md5($tmp),0,5);
	  if( !isset($mid_cache[$id]) )
	    {
	      $mid_cache[$id] = $id;
	      break;
	    }
	}
	cms_utils::set_app_data('mid_cache',$mid_cache);

	$returnid = '';
	$content_obj = cmsms()->get_variable('content_obj');
	if (isset($content_obj) && $content_obj->Id())
	  {
	    $returnid = $content_obj->Id();
	  }
	//$params = array_merge($params, GetModuleParameters($id));

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
	  if (!strcasecmp($mactmodulename,$params['module']))
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
	      $params = array_merge($params, GetModuleParameters($id));
		}
	    }
	}

	if( $action == '' ) $action = 'default'; // probably not needed, but safe

	class_exists($modulename);
	$module = cms_utils::get_module($modulename);
	if( $module && $module->isPluginModule() )
	{
	  @ob_start();
	  $result = $module->DoActionBase($action, $id, $params, $returnid);
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

# vim:ts=4 sw=4 noet
?>
