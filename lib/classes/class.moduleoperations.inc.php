<?php
# CMS - CMS Made Simple
# (c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA	02111-1307	USA
#
#$Id$

/**
 * "Static" module functions for internal use and module development.  CMSModule
 * extends this so that it has internal access to the functions.
 *
 * @since		0.9
 * @package		CMS
 */
define( "MODULE_DTD_VERSION", "1.3" );

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'class.module.inc.php');

class ModuleOperations
{
  /**
   * A member to hold an error string
   */
  var $error;

  /**
   * A member to hold the id of the active tab
   */
  var $mActiveTab = '';

  var $xml_exclude_files = array('^\.svn' , '^CVS$' , '^\#.*\#$' , '~$', '\.bak$' );
  var $xmldtd = '
<!DOCTYPE module [
  <!ELEMENT module (dtdversion,name,version,description*,help*,about*,requires*,file+)>
  <!ELEMENT dtdversion (#PCDATA)>
  <!ELEMENT name (#PCDATA)>
  <!ELEMENT version (#PCDATA)>
  <!ELEMENT mincmsversion (#PCDATA)>
  <!ELEMENT description (#PCDATA)>
  <!ELEMENT help (#PCDATA)>
  <!ELEMENT about (#PCDATA)>
  <!ELEMENT requires (requiredname,requiredversion)>
  <!ELEMENT requiredname (#PCDATA)>
  <!ELEMENT requiredversion (#PCDATA)>
  <!ELEMENT file (filename,isdir,data)>
  <!ELEMENT filename (#PCDATA)>
  <!ELEMENT isdir (#PCDATA)>
  <!ELEMENT data (#PCDATA)>
]>';

  /**
   * ------------------------------------------------------------------
   * Error Functions
   * ------------------------------------------------------------------
   */

  /**
   * Set an error condition
   */
  function SetError($str = '')
  {
	global $gCms;
	$gCms->variables['error'] = $str;
  }

  /**
   * Return the last error
   */
  function GetLastError()
  {
	global $gCms;
	if( isset( $gCms->variables['error'] ) )
	  return $gCms->variables['error'];
	return "";
  }

	/**
	 * Creates an xml data package from the module directory.
	 */
	function CreateXMLPackage( &$modinstance, &$message, &$filecount )
	{
	  // get a file list
	  $filecount = 0;
	  $dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$modinstance->GetName();
	  $files = get_recursive_file_list( $dir, $this->xml_exclude_files );

	  $xmltxt  = '<?xml version="1.0" encoding="ISO-8859-1"?>';
	  $xmltxt .= $this->xmldtd."\n";
	  $xmltxt .= "<module>\n";
		  $xmltxt .= "	<dtdversion>".MODULE_DTD_VERSION."</dtdversion>\n";
	  $xmltxt .= "	<name>".$modinstance->GetName()."</name>\n";
	  $xmltxt .= "	<version>".$modinstance->GetVersion()."</version>\n";
	  $xmltxt .= "  <mincmsversion>".$modinstance->MinimumCMSVersion()."</mincmsversion>\n";
	  $xmltxt .= "	<help>".base64_encode($modinstance->GetHelpPage())."</help>\n";
	  $xmltxt .= "	<about>".base64_encode($modinstance->GetAbout())."</about>\n";
	  $desc = $modinstance->GetAdminDescription();
	  if( $desc != '' )
		{
		  $xmltxt .= "	<description>".$desc."</description>\n";
		}
	  $depends = $modinstance->GetDependencies();
	  foreach( $depends as $key=>$val )
		{
		  $xmltxt .= "	<requires>\n";
			  $xmltxt .= "	  <requiredname>$key</requiredname>\n";
			  $xmltxt .= "	  <requiredversion>$val</requiredversion>\n";
		  $xmltxt .= "	</requires>\n";
		}
	  foreach( $files as $file )
		{
		  // strip off the beginning
		  if (substr($file,0,strlen($dir)) == $dir)
			 {
			 $file = substr($file,strlen($dir));
			 }
		  if( $file == '' ) continue;

		  $xmltxt .= "	<file>\n";
		  $filespec = $dir.DIRECTORY_SEPARATOR.$file;
		  $xmltxt .= "	  <filename>$file</filename>\n";
		  if( @is_dir( $filespec ) )
		{
		  $xmltxt .= "	  <isdir>1</isdir>\n";
		}
		  else
		{
		  $xmltxt .= "	  <isdir>0</isdir>\n";
		  $data = base64_encode(file_get_contents($filespec));
		  $xmltxt .= "	  <data><![CDATA[".$data."]]></data>\n";
		}

		  $xmltxt .= "	</file>\n";
		  ++$filecount;
		}
		  $xmltxt .= "</module>\n";
	  $message = 'XML package of '.strlen($xmltxt).' bytes created for '.$modinstance->GetName();
	  $message .= ' including '.$filecount.' files';
	  return $xmltxt;
	}

  /**
   * Unpackage a module from an xml string
   * does not touch the database
   */
  function ExpandXMLPackage( $xml, $overwrite = 0, $brief = 0 )
  {
	global $gCms;

	// first make sure that we can actually write to the module directory
	$dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules";

	if( !is_writable( $dir ) && $brief == 0 )
	  {
	    // directory not writable
	    ModuleOperations::SetError( lang( 'errordirectorynotwritable' ) );
	    return false;
	  }

	// start parsing xml
	$parser = xml_parser_create();
	$ret = xml_parse_into_struct( $parser, $xml, $val, $xt );
	xml_parser_free( $parser );

	if( $ret == 0 )
	  {
	    ModuleOperations::SetError( lang( 'errorcouldnotparsexml' ) );
	    return false;
	  }

	ModuleOperations::SetError( "" );
	$havedtdversion = false;
	$moduledetails = array();
	$moduledetails['size'] = strlen($xml);
	$required = array();
	foreach( $val as $elem )
	  {
	    $value = (isset($elem['value'])?$elem['value']:'');
	    $type = (isset($elem['type'])?$elem['type']:'');
	    switch( $elem['tag'] )
	      {
	      case 'NAME':
		{
		  if( $type != 'complete' && $type != 'close' )
		    {
		      continue;
		    }
		  // check if this module is already installed
		  if( isset( $gCms->modules[$value] ) && $overwrite == 0 && $brief == 0 )
		    {
		      ModuleOperations::SetError( lang( 'moduleinstalled' ) );
		      return false;
		    }
		  $moduledetails['name'] = $value;
		  break;
		}

	      case 'DTDVERSION':
		{
		  if( $type != 'complete' && $type != 'close' )
		    {
		      continue;
		    }
		  if( $value != MODULE_DTD_VERSION )
		    {
		      ModuleOperations::SetError( lang( 'errordtdmismatch' ) );
		      return false;
		    }
		  $havedtdversion = true;
		  break;
		}

	      case 'VERSION':
		{
		  if( $type != 'complete' && $type != 'close' )
		    {
		      continue;
		    }
		  $moduledetails['version'] = $value;
		  if( isset( $gCms->modules[$moduledetails['name']] ) )
		    {
		      $version = $gCms->modules[$moduledetails['name']]['object']->GetVersion();
		      if( version_compare($moduledetails['version'],$version) < 0 && $brief == 0)
			{
			  ModuleOperations::SetError( lang('errorattempteddowngrade') );
			  return false;
			}
		      else if (version_compare($moduledetails['version'],$version) == 0 && $brief == 0 )
			{
			  ModuleOperations::SetError( lang('moduleinstalled') );
			  return false;
			}
		    }
		  break;
		}
		
	      case 'MINCMSVERSION':
		{
		  if( $type != 'complete' && $type != 'close' )
		    {
		      continue;
		    }
		  $moduledetails['mincmsversion'] = $value;
		  break;
		}
		
	      case 'MAXCMSVERSION':
		{
		  if( $type != 'complete' && $type != 'close' )
		    {
		      continue;
		    }
		  $moduledetails['maxcmsversion'] = $value;
		  break;
		}
		
	      case 'DESCRIPTION':
		{
		  if( $type != 'complete' && $type != 'close' )
		    {
		      continue;
		    }
		  $moduledetails['description'] = $value;
		  break;
		}
		
	      case 'HELP':
		{
		  if( $type != 'complete' && $type != 'close' )
		    {
		      continue;
		    }
		  $moduledetails['help'] = base64_decode($value);
		  break;
		}
		
	      case 'ABOUT':
		{
		  if( $type != 'complete' && $type != 'close' )
		    {
		      continue;
		    }
		  $moduledetails['about'] = base64_decode($value);
		  break;
		}
	    
	      case 'REQUIRES':
		{
		  if( $type != 'complete' && $type != 'close' )
		    {
		      continue;
		    }
		  if( count($requires) != 2 )
		    {
		      continue;
		    }
		  if( !isset( $moduledetails['requires'] ) )
		    {
		      $moduledetails['requires'] = array();
		    }
		  $moduledetails['requires'][] = $requires;
		  $requires = array();
		}
	    
	      case 'REQUIREDNAME':
		$requires['name'] = $value;
		break;

	      case 'REQUIREDVERSION':
		$requires['version'] = $value;
		break;

	      case 'FILE':
		{
		  if( $type != 'complete' && $type != 'close' )
		    {
		      continue;
		    }
		  if( $brief != 0 )
		    {
		      continue;
		    }

		  // finished a first file
		  if( !isset( $moduledetails['name'] )	   || !isset( $moduledetails['version'] ) ||
		      !isset( $moduledetails['filename'] ) || !isset( $moduledetails['isdir'] ) )
		    {
		      print_r( $moduledetails );
		      ModuleOperations::SetError( lang('errorincompletexml') );
		      return false;
		    }

		  // ready to go
		  $moduledir=$dir.DIRECTORY_SEPARATOR.$moduledetails['name'];
		  $filename=$moduledir.$moduledetails['filename'];
		  if( !file_exists( $moduledir ) )
		    {
		      if( !@mkdir( $moduledir ) && !is_dir( $moduledir ) )
			{
			  ModuleOperations::SetError(lang('errorcantcreatefile').' '.$moduledir);
			  break;
			}
		    }
		  else if( $moduledetails['isdir'] )
		    {
		      if( !@mkdir( $filename ) && !is_dir( $filename ) )
			{
			  ModuleOperations::SetError(lang('errorcantcreatefile').' '.$filename);
			  break;
			}
		    }
		  else
		    {
		      $data = $moduledetails['filedata'];
		      if( strlen( $data ) )
			{
			  $data = base64_decode( $data );
			}
		      $fp = @fopen( $filename, "w" );
		      if( !$fp )
			{
			  ModuleOperations::SetError(lang('errorcantcreatefile').' '.$filename);
			}
		      if( strlen( $data ) )
			{
			  @fwrite( $fp, $data );
			}
		      @fclose( $fp );
		    }
		  unset( $moduledetails['filedata'] );
		  unset( $moduledetails['filename'] );
		  unset( $moduledetails['isdir'] );
		}

	      case 'FILENAME':
		$moduledetails['filename'] = $value;
		break;

	      case 'ISDIR':
		$moduledetails['isdir'] = $value;
		break;

	      case 'DATA':
		if( $type != 'complete' && $type != 'close' )
		  {
		    continue;
		  }
		$moduledetails['filedata'] = $value;
		break;
	      }
	  } // foreach

	if( $havedtdversion == false )
	  {
	    ModuleOperations::SetError( lang( 'errordtdmismatch' ) );
	  }

	// we've created the module's directory
	unset( $moduledetails['filedata'] );
	unset( $moduledetails['filename'] );
	unset( $moduledetails['isdir'] );

	if( ModuleOperations::GetLastError() != "" )
	  {
	    return false;
	  }
	return $moduledetails;
  }


  /**
   * Install a module into the database
   */
  function InstallModule($module, $loadifnecessary = false)
  {
    global $gCms;
    if( !isset( $gCms->modules[$module] ) )
      {
	if( $loadifnecessary == false )
	  {
	    return array(false,lang('errormodulenotloaded'));
	  }
	else
	  {
	    if( !ModuleOperations::LoadNewModule( $module ) )
	      {
		return array(false,lang('errormodulewontload'));
	      }
	  }
      }
 
    $db =& $gCms->GetDb();
    if (isset($gCms->modules[$module]))
      {
	$modinstance =& $gCms->modules[$module]['object'];
	$result = $modinstance->Install();
	
        #now insert a record
	if (!isset($result) || $result === FALSE)
	  {
	    $query = "INSERT INTO ".cms_db_prefix()."modules (module_name, version, status, admin_only, active) VALUES (?,?,'installed',?,?)";
	    $db->Execute($query, array($module,$modinstance->GetVersion(),($modinstance->IsAdminOnly()==true?1:0),1));
	    
            #and insert any dependancies
	    if (count($modinstance->GetDependencies()) > 0) #Check for any deps
	      {
                #Now check to see if we can satisfy any deps
		foreach ($modinstance->GetDependencies() as $onedepkey=>$onedepvalue)
		  {
		    $time = $db->DBTimeStamp(time());
		    $query = "INSERT INTO ".cms_db_prefix()."module_deps (parent_module, child_module, minimum_version, create_date, modified_date) VALUES (?,?,?,".$time.",".$time.")";
		    $db->Execute($query, array($onedepkey, $module, $onedepvalue));
		  }
	      }
	    
            #send an event saying the module has been installed
	    Events::SendEvent('Core', 'ModuleInstalled', array('name' => &$module, 'version' => $modinstance->GetVersion()));
	    
	    // and we're done
	    return array(true);
	  }
	else
	  {
	    if( trim($result) == "" )
	      {
		$result = lang('errorinstallfailed');
	      }
	    return array(false,$result);
	  }
      }    
    else
      {
	return array(false,lang('errormodulenotfound'));
      }
  }


  /**
   * Load a single module from the filesystem
   */
  function LoadNewModule( $modulename )
  {
    global $gCms;
    $db =& $gCms->GetDb();
    $cmsmodules = &$gCms->modules;
    
    $dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules";

    if (@is_file("$dir/$modulename/$modulename.module.php"))
      {
	// question: is this a potential XSS vulnerability
	include("$dir/$modulename/$modulename.module.php");
	if( !class_exists( $modulename ) )
	  {
	    return false;
	  }

	$newmodule =& new $modulename;
	$name = $newmodule->GetName();
	$cmsmodules[$name]['object'] =& $newmodule;
	$cmsmodules[$name]['installed'] = false;
	$cmsmodules[$name]['active'] = false;
      }
    else
      {
	unset($cmsmodules[$modulename]);
	return false;
      }
    return true;
  }

  /**
   * Upgrade a module
   */
  function UpgradeModule( $module, $oldversion, $newversion )
  {
    global $gCms;
    $db =& $gCms->GetDb();

    if (!isset($gCms->modules[$module]))
      {
	return false;
      }

    $modinstance = $gCms->modules[$module]['object'];
    $result = $modinstance->Upgrade($oldversion, $newversion);
    if( $result === FALSE )
      {
	return false;
      }

    $query = "UPDATE ".cms_db_prefix()."modules SET version = ?, admin_only = ? WHERE module_name = ?";
    $db->Execute($query,array($newversion,($modinstance->IsAdminOnly()==true?1:0),$module));
    
    Events::SendEvent('Core', 'ModuleUpgraded', array('name' => $module, 
						      'oldversion' => $oldversion, 
						      'newversion' => $oldversion));

    return true;
  }

	/**
	 * Returns a hash of all loaded modules.  This will include all
	 * modules loaded by LoadModules, which could either be all or them,
	 * or just ones that are active and installed.
	 */
	function & GetAllModules()
	{
		global $gCms;
		$cmsmodules = &$gCms->modules;
		return $cmsmodules;
	}

}
