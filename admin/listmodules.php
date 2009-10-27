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

$CMS_ADMIN_PAGE=1;
$LOAD_ALL_MODULES=1;

require_once("../include.php");
require_once(cms_join_path($dirname,'lib','html_entity_decode_utf8.php'));

check_login();
$thisurl=basename(__FILE__).'?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

$module = "";
if (isset($_GET["module"])) $module = $_GET["module"];

$plugin = "";
if (isset($_GET["plugin"])) $plugin = $_GET["plugin"];

$action = "";
if (isset($_GET["action"])) $action = $_GET["action"];

$allowoverwritemodules = 0;
if (isset($_POST["allowoverwrite"])) $allowoverwritemodules = $_POST["allowoverwrite"];

$autoinstallupgrade = 0; // keep this here for a bit, just incase
$userid = get_userid();
$access = check_permission($userid, "Modify Modules");

$smarty = cms_smarty();
$db = cms_db();
if( isset($_SESSION['listmodule_message']) )
  {
    $smarty->assign('message',$_SESSION['listmodule_message']);
    unset($_SESSION['listmodule_message']);
  }
if( isset($_SESSION['listmodule_error']) )
  {
    $smarty->assign('error',$_SESSION['listmodule_error']);
    unset($_SESSION['listmodule_error']);
  }

//Messagestring (success) for module operations
$modulemessage="";

include_once("header.php");

if ($access)
{
  if ($action == "chmod" )
    {
      $result = chmod_r( $config['root_path'].DIRECTORY_SEPARATOR.
			 'modules'.DIRECTORY_SEPARATOR.$module, 0777 );
      if( !$result )
	{
	  $_SESSION['listmodule_error'] = lang('cantchmodfiles');
	}
      redirect($thisurl);
    }

  if ($action == "exportxml")
    {
      // get our xml
      $message = '';
      $files = 0;
      $modops =& $gCms->GetModuleOperations();
      $object = $gCms->modules[$module]['object'];
      $xmltxt = $modops->CreateXMLPackage($object,$message,$files);
      if( $files == 0 )
	{
	  $smarty->assign('error',lang('errornofilesexported'));
	}
      else 
	{
	  $xmlname = $object->GetName().'-'.$object->GetVersion().'.xml';

	  // and send the file
	  ob_end_clean();
	  header('Content-Description: File Transfer');
	  header('Content-Type: application/force-download');
	  header('Content-Disposition: attachment; filename='.$xmlname);
	  //     header('Content-Type: text/xml');
	  echo $xmltxt;
	  exit();
	}
    }

  if ($action == "importxml" )
    {
      $fieldName = "browse_xml";
      if (!isset ($_FILES[$fieldName]) || !isset ($_FILES)
	  || !is_array ($_FILES[$fieldName]) || !$_FILES[$fieldName]['name'])
	{
	  $smarty->assign('error',lang('noxmlfileuploaded'));
	}
      else
	{
	  // normalize the file variable
	  $file = $_FILES[$fieldName];

	  // $file['tmp_name'] is the file we have to parse
	  $xml = file_get_contents( $file['tmp_name'] );

	  // and parse it
	  $modops =& $gCms->GetModuleOperations();
	  $result = $modops->ExpandXMLPackage( $xml, $allowoverwritemodules );
	  // at this point, all of the files in that module may have become unusable
	  // in the current version of cms.
	  if( !$result )
	    {
	      $smarty->assign('error',lang($modops->GetLastError()));
	    }
	  else if( $autoinstallupgrade == 0 )
	    {
	      // no auto install or upgrade
	      redirect($thisurl);
	    }
	}  
    }

  if ($action == "install")
    {
      $modops =& $gCms->GetModuleOperations();
      $result = $modops->InstallModule($module,false);
      if( $result[0] == false )
	{
	  $_SESSION['listmodule_error'] = lang('moduleerrormessage',$module);
	}
      else
	{
	  $content = $gCms->modules[$module]['object']->InstallPostMessage();
	  if( $content != FALSE )
	    {
	      $_SESSION['listmodule_message'] = $content;
	    }
	}
      redirect($thisurl);
    }

  if ($action == 'remove')
    {
      $result = recursive_delete( $config['root_path'].DIRECTORY_SEPARATOR.
				  'modules'.DIRECTORY_SEPARATOR.$module );
      if( !$result )
	{
	  $smarty->assign('error',lang('cantremovefiles'));
	}
      else
	{
	  redirect($thisurl);
	}
    }

  if ($action == 'upgrade')
    {
      $modops =& $gCms->GetModuleOperations();
      $result = $modops->UpgradeModule( $module, $_GET['oldversion'], $_GET['newversion'] );	  
      if( !$result )
	{
	  @ob_start();
	  echo $modops->GetLastError();
	  $content = @ob_get_contents();
	  @ob_end_clean();
	  $smarty->assign('error',lang('moduleupgraderror'));
	}
      else
	{
	  $content = $gCms->modules[$module]['object']->UpgradePostMessage();
	  if( $content != FALSE )
	    {
	      //Redirect right away so that the installed module shows in the menu
	      if( $content != FALSE )
		{
		  $_SESSION['listmodule_message'] = $content;
		}
	    }
	  redirect($thisurl);
	}
    }


  if ($action == "uninstall")
    {
      if (isset($gCms->modules[$module]))
	{
	  $modinstance = $gCms->modules[$module]['object'];
	  $result = $modinstance->Uninstall();

	  // now insert a record
	  if (!isset($result) || $result === FALSE)
	    {
	      // now delete the record
	      $query = "DELETE FROM ".cms_db_prefix()."modules WHERE module_name = ?";
	      $db->Execute($query, array($module));

	      // delete any dependencies
	      $query = "DELETE FROM ".cms_db_prefix()."module_deps WHERE child_module = ?";
	      $db->Execute($query, array($module));

	      Events::SendEvent('Core', 'ModuleUninstalled', array('name' => $module));

	      $content = $modinstance->UninstallPostMessage();
	      if( $content !== FALSE )
		{
		  $_SESSION['listmodule_message'] = $content;
		}
	      redirect($thisurl);
	    }
	  else
	    {
	      //TODO: Echo error
	    }
	}
    }

  if ($action == "settrue")
    {
      $query = "UPDATE ".cms_db_prefix()."modules SET active = ? WHERE module_name = ?";
      $db->Execute($query, array(1,$module));
      redirect($thisurl);
    }

  if ($action == "setfalse")
    {
      $query = "UPDATE ".cms_db_prefix()."modules SET active = ? WHERE module_name = ?";
      $db->Execute($query, array(0,$module));
      redirect($thisurl);
    }

 } // if access

if ($action == "showmoduleabout")
  {
    if (isset($gCms->modules[$module]['object']))
      {
	$module_name = $gCms->modules[$module]['object']->GetName();
	$smarty->assign('module',$module_name);
	$smarty->assign('module_about_output',$gCms->modules[$module]['object']->GetAbout());
	$output = $smarty->fetch('module_about.tpl');
	$smarty->assign('body',$output);
      }
}
else if ($action == "showmodulehelp")
{
  if (isset($gCms->modules[$module]['object']))
    {
      $module_name = $gCms->modules[$module]['object']->GetName();
      // Turn ModuleName into _Module_Name
      // Include English translation of titles. (Can't find better way to get them)
      $dirname = dirname(__FILE__);
      include($dirname.'/lang/en_US/admin.inc.php');
      $section = $lang['admin'][$gCms->modules[$module]['object']->GetAdminSection()];
      if (FALSE == get_preference($userid, 'hide_help_links'))
	{
	  $moduleName =  preg_replace('/([A-Z])/', "_$1", $module_name);
	  $moduleName =  preg_replace('/_([A-Z])_/', "$1", $moduleName);
	  if ($moduleName{0} == '_')
	    {
	      $moduleName = substr($moduleName, 1);
	    }

	  // Clean up URL
	  $wikiUrl = $config['wiki_url'];
	  $wikiUrl .= '/'.$section.'/'.$moduleName;
	  $wikiUrl = str_replace(' ', '_', $wikiUrl);
	  $wikiUrl = str_replace('&amp;', 'and', $wikiUrl);
	  $image_help_external = $themeObject->DisplayImage('icons/system/info-external.gif', lang('help'),'','','systemicon');		
	  $smarty->assign('wiki_url',$wikiUrl);
	  $smarty->assign('ext_help_image',$image_help_external);
	}

      $smarty->assign('current_language',$gCms->current_language);
      $obj =& $gCms->modules[$module]['object'];
      if( isset($_GET['lang']) )
	{
	  $gCms->modules[$module]['object']->SetModuleLanguage($_GET['lang']);
	}
      $smarty->assign('module',$module_name);
      $smarty->assign('module_help_output',$gCms->modules[$module]['object']->GetHelpPage());
      $output = $smarty->fetch('module_help.tpl');
      if( isset($_GET['lang']) )
	{
	  $gCms->modules[$module]['object']->SetModuleLanguage();
	}
      $smarty->assign('body',$output);
    }
}
else if ($action == 'missingdeps')
{
  $deps = array();
  if (isset($gCms->modules[$module]))
    {
      $modinstance = $gCms->modules[$module]['object'];
      if (count($modinstance->GetDependencies()) > 0) #Check for any deps
	{
	  // Now check to see if we can satisfy any deps
	  debug_buffer($modinstance->GetDependencies(), 'deps in module');
	  foreach ($modinstance->GetDependencies() as $onedepkey=>$onedepvalue)
	    {
	      $havedep = false;

	      if (isset($gCms->modules[$onedepkey]) && 
		  $gCms->modules[$onedepkey]['installed'] == true &&
		  $gCms->modules[$onedepkey]['active'] == true &&
		  version_compare($gCms->modules[$onedepkey]['object']->GetVersion(), $onedepvalue) > -1)
		{
		  $havedep = true;
		}

	      $row = array();
	      $row['name'] = $onedepkey;
	      $row['version'] = $onedepvalue;
	      $row['installed'] = $havedep;
	      $deps[] = $row;
	    }
	}
    }

  $smarty->assign('module',$module);
  $smarty->assign('deps',$deps);
  $output = $smarty->fetch('module_deps.tpl');
  $smarty->assign('body',$output);
}
else
{
  if ($access) {
	
    if (count($gCms->modules) > 0) {

      $query = "SELECT * from ".cms_db_prefix()."modules";
      $result = $db->Execute($query);
      while ($result && $row = $result->FetchRow()) {
	$dbm[$row['module_name']]['Status'] = $row['status'];
	$dbm[$row['module_name']]['Version'] = $row['version'];
	$dbm[$row['module_name']]['Active'] = ($row['active'] == 1?true:false);
      }

      // construct true/false button images
      $image_true = $themeObject->DisplayImage('icons/system/true.gif', lang('true'),'','','systemicon');
      $image_false = $themeObject->DisplayImage('icons/system/false.gif', lang('false'),'','','systemicon');

      $themodules = array();
      foreach($gCms->modules as $key=>$value)
	{
	  $rec = array();
	  $rec['name'] = $key;
	  $rec['instance'] =& $value['object'];
	  $rec['sysmodule'] = (array_search( $key, $gCms->cmssystemmodules ) === FALSE)?0:1;
	  if( $rec['instance']->GetHelp() != '' )
	    {
	      $rec['help_url'] = "{$thisurl}&amp;action=showmodulehelp&amp;module={$key}";
	    }
	  $rec['about_url'] = "{$thisurl}&amp;action=showmoduleabout&amp;module={$key}";
	  $rec['xml_url'] = "{$thisurl}&amp;action=exportxml&amp;module={$key}";
	  if( isset($dbm[$key]) )
	    {
	      $rec['version'] = $dbm[$key]['Version'];
	      $rec['active'] = $dbm[$key]['Active'];
	      $rec['status'] = $dbm[$key]['Status'];
	    }
	  $rec['statuscol'] = array();
	  $rec['actioncol'] = array();
	  $rec['activecol'] = '&nbsp;';
	  $rec['status_spans'] = false;

	  // check these modules permissions to see if we can remove this thing
	  $permsok = is_directory_writable( $config['root_path'].DIRECTORY_SEPARATOR.
					    'modules'.DIRECTORY_SEPARATOR.$key );

	  $maxverok = version_compare($rec['instance']->MaximumCMSVersion(), $CMS_VERSION);
	  $maxverok = ($maxverok >= 0 )?1:0;
		  
	  // Make sure it's a valid module for this version of CMSMS
	  if (version_compare($rec['instance']->MinimumCMSVersion(), $CMS_VERSION) == 1)
	    {
	      // Fix undefined index error if module is not already installed.
	      $rec['statuscol'][] = '<span class="important">'.lang('minimumversionrequired').': '.$rec['instance']->MinimumCMSVersion().'</span>';
	      unset($rec['xml_url']);
	      $rec['status_spans'] = true;
	    }
	  else if( $maxverok == 0 )
	    {
	      // maximum cms version exceeded
	      unset($rec['xml_url']);
	      $rec['statuscol'][]  = '<span class="important">'.lang('maximumversionsupported').' = '.$rec['instance']->MaximumCMSVersion()."</span>";
	    }

	  if (!isset($dbm[$key])) #Not installed, lets put up the install button
	    {
	      $brokendeps = 0;
	      unset($rec['xml_url']);
		      
	      $dependencies = $rec['instance']->GetDependencies();
		      
	      if (count($dependencies) > 0) #Check for any deps
		{
		  // Now check to see if we can satisfy any deps
		  foreach ($dependencies as $onedepkey=>$onedepvalue)
		    {
		      if (!isset($gCms->modules[$onedepkey]) ||
			  $gCms->modules[$onedepkey]['installed'] != true ||
			  $gCms->modules[$onedepkey]['active'] != true ||
			  version_compare($gCms->modules[$onedepkey]['object']->GetVersion(), $onedepvalue) < 0)
			{
			  $brokendeps++;
			}
		    }
		}

	      $rec['version'] = $rec['instance']->GetVersion();
	      $rec['statuscol'][] = lang('notinstalled');

	      if ($brokendeps > 0)
		{
		  $rec['actioncol'][] = '<a href="'.$thisurl.'&amp;action=missingdeps&amp;module='.$key.'">'.lang('missingdependency').'</a>';
		}
	      else if( $maxverok == 1)
		{
		  $rec['actioncol'][] = "<a href=\"{$thisurl}&amp;action=install&amp;module=".$key."\">".lang('install')."</a>";
		  unset($rec['xml_url']);
		}

	      if( !$rec['sysmodule'] )
		{
		  if( $permsok )
		    {
		      $rec['actioncol'][] .= "<a href=\"{$thisurl}&amp;action=remove&amp;module=".$key."\" onclick=\"return confirm('".cms_html_entity_decode_utf8(lang('removeconfirm'),true)."');\">".lang('remove')."</a>";
		    }
			  
		  else
		    {
		      $rec['actioncol'][] = "<a href=\"{$thisurl}&amp;action=chmod&amp;module=".$key."\" onclick=\"return confirm('".cms_html_entity_decode_utf8(lang('changepermissionsconfirm'),true)."');\">".lang('changepermissions')."</a>";
		    }
		}
	    }
	  else if (version_compare($rec['instance']->GetVersion(), 
				   $dbm[$key]['Version']) == 1) 
	    // It's already installed, Check for an upgrade
	    {
	      unset($rec['xml_url']);
	      $rec['statuscol'][]  = '<span class="important">'.lang('needupgrade').'</span>';
	      $rec['activecol']  = ($dbm[$key]['Active']==true?"<a href='{$thisurl}&amp;action=setfalse&amp;module=".$key."'>".$image_true."</a>":"<a href='{$thisurl}&amp;action=settrue&amp;module=".$key."'>".$image_false."</a>");
	      if( $maxverok == 1)
		{
		  $rec['actioncol'][]  = "<a href=\"{$thisurl}&amp;action=upgrade&amp;module=".$key."&amp;oldversion=".$dbm[$key]['Version']."&amp;newversion=".$rec['instance']->GetVersion()."\" onclick=\"return confirm('".cms_html_entity_decode_utf8(lang('upgradeconfirm'),true)."');\">".lang('upgrade')."</a>";
		}
	    }
	  else // Must be installed
	    {
	      $rec['statuscol'][]  = lang('installed');

	      // Can't be removed if it has a dependency...
	      if (!$rec['instance']->CheckForDependents())
		{
		  $rec['activecol'] = ($dbm[$key]['Active']==true?"<a href='{$thisurl}&amp;action=setfalse&amp;module=".$key."'>".$image_true."</a>":"<a href='{$thisurl}&amp;action=settrue&amp;module=".$key."'>".$image_false."</a>");
		  $rec['actioncol'][] = "<a href=\"{$thisurl}&amp;action=uninstall&amp;module=".$key."\" onclick=\"return confirm('".($rec['instance']->UninstallPreMessage() !== FALSE? cms_utf8entities($rec['instance']->UninstallPreMessage()):lang('uninstallconfirm').' '.$key)."');\">".lang('uninstall')."</a>";
		}
	      else
		{
		  // HAS DEPENDENTS ===============
		  $result = $db->Execute("SELECT child_module from
					".cms_db_prefix()."module_deps WHERE parent_module='$key'");
			  
		  $dependentof = array();;
		  while ($result && $row = $result->FetchRow()) {
		    $dependentof[$row['child_module']] = "";
		  }
		  $str = implode(array_keys($dependentof),", ");
		  $rec['activecol'] = ($dbm[$key]['Active']==true?$image_true:"<a href='{$thisurl}&amp;action=settrue&amp;module=".$key."'>".$image_false."</a>");
		  $rec['statuscol'][] = lang('hasdependents')." (<strong>$str</strong>)";
		  // END HAS DEPENDENTS ===========
		}

	      if( !$permsok )
		{
		  // permissions are no good
		  $rec['statuscol'][] = lang('cantremove');
		  $rec['actioncol'][] = "<a href=\"{$thisurl}&amp;action=chmod&amp;module=".$key."\" onclick=\"return confirm('".cms_html_entity_decode_utf8(lang('changepermissionsconfirm'),true)."');\">".lang('changepermissions')."</a>";
		}

	    } // installed

// 	  $rec['actioncol'] = implode('<br/>',$rec['actioncol']);
// 	  $rec['statuscol'] = implode('<br/>',$rec['statuscol']);
	  $themodules[] = $rec;
	}
      $smarty->assign('modules',$themodules);

      // Only show XML upload form if the modules folder is writable
      //if (FALSE == is_writable($config['root_path'].DIRECTORY_SEPARATOR.'modules'))
      if (FALSE == can_admin_upload())
	{
	  echo $themeObject->ShowErrors(lang('modulesnotwritable'));
	}
      else
	{
	  $smarty->assign('import_url',"{$thisurl}&amp;action=imporxml");
	}
    }
  } // if access
  else {
   //now print the menssage
    $smarty->assign('error',lang('needpermissioto',array('Modify Modules')));
  }
	
}

echo $smarty->fetch('listmodules.tpl');
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>