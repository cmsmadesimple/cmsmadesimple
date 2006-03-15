<?php
# CMS - CMS Made Simple
# (c)2004 by Ted Kulp (tedkulp@users.sf.net)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * "Static" module functions for internal use and module development.  CMSModule
 * extends this so that it has internal access to the functions.
 *
 * @since		0.9
 * @package		CMS
 */
define( "MODULE_DTD_VERSION", "1.2" );

class ModuleOperations
{
  /**
   * A member to hold an error string
   */
  var $error;

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
    return $gCms->variables['error'];
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

    if( !is_writable( $dir ) && $brief != 0 )
      {
	// directory not writable
	ModuleOperations::SetError( lang( 'errordirectorynotwritable' ) );
	return false;
      }

    // start parsing xml
    $parser = xml_parser_create();
    xml_parse_into_struct( $parser, $xml, $val, $xt );
    xml_parser_free( $parser );

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
	    }
	    break;

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
		  if( $moduledetails['version'] < $version && $brief == 0)
		    {
		      ModuleOperations::SetError( lang('errorattempteddowngrade') );
		      return false;
		    }
                  else if ($moduledetails['version'] == $version && $brief == 0 )
                    {
                      ModuleOperations::SetError( lang('moduleinstalled') );
                      return false;
                    }
		}
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
	      array_push( $moduledetails['requires'], $requires );
	      $required = array();
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
	      if( !isset( $moduledetails['name'] )     || !isset( $moduledetails['version'] ) ||
		  !isset( $moduledetails['filename'] ) || !isset( $moduledetails['isdir'] ) )
		{
		  print_r( $moduledetails );
		  ModuleOperations::SetError( lang('errorincompletexml') );
		  return false;
		}

	      // ready to go
	      $moduledir=$dir.DIRECTORY_SEPARATOR.$moduledetails['name'];
	      $filename=$moduledir.DIRECTORY_SEPARATOR.$moduledetails['filename'];
	      if( !file_exists( $moduledir ) )
		{
		  @mkdir( $moduledir );
		}
	      if( $moduledetails['isdir'] )
		{
		  @mkdir( $filename );
		}
	      else
		{
		  $data = $moduledetails['filedata'];
		  if( strlen( $data ) )
		    {
		      $data = base64_decode( $data );
		    }
		  $fp = fopen( $filename, "w" );
		  if( !$fp )
		    {
		      ModuleOperations::SetError(lang('errorcanptcreatefile'));
		    }
		  if( strlen( $data ) )
		    {
		      fwrite( $fp, $data );
		    }
		  fclose( $fp );
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
      }

    if( $havedtdversion == false )
      {
	ModuleOperations::SetError( lang( 'errordtdmismatch' ) );
	return false;
      }

    // we've created the modules directory
    // now we either have to upgrade or install
    unset( $moduledetails['filedata'] );
    unset( $moduledetails['filename'] );
    unset( $moduledetails['isdir'] );
    return $moduledetails;
  }


	/**
	 * Loads modules from the filesystem.  If loadall is true, then it will load all
	 * modules whether they're installed, or active.  If it is false, then it will
	 * only load modules which are installed and active.
	 */
	function LoadModules($loadall = false)
	{
		global $gCms;
		$db =& $gCms->GetDb();
		$cmsmodules = &$gCms->modules;

		$dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules";

		if ($loadall == true)
		{
			$ls = dir($dir);
			while (($file = $ls->read()) != "")
			{
				if (is_dir("$dir/$file") && (strpos($file, ".") === false || strpos($file, ".") != 0))
				{
					if (is_file("$dir/$file/$file.module.php"))
					{
						include("$dir/$file/$file.module.php");
					}
					else
					{
						unset($cmsmodules[$file]);
					}
				}
			}
			//Find modules and instantiate them
			$allmodules = @ModuleOperations::FindModules();
			foreach ($allmodules as $onemodule)
			{
				if (class_exists($onemodule))
				{
					$newmodule = new $onemodule;
					$name = $newmodule->GetName();
					$cmsmodules[$name]['object'] = $newmodule;
					$cmsmodules[$name]['installed'] = false;
					$cmsmodules[$name]['active'] = false;
				}
				else
				{
					unset($cmsmodules[$name]);
				}
			}
		}

		#Figger out what modules are active and/or installed
		#Load them if loadall is false
		if (isset($db))
		{
			$query = "SELECT * FROM ".cms_db_prefix()."modules ORDER BY module_name";
			$result = &$db->Execute($query);
			while ($result && !$result->EOF)
			{
				if (isset($result->fields['module_name']))
				{
					$modulename = $result->fields['module_name'];
					if (isset($modulename))
					{
						if ($loadall == true)
						{
							if (isset($cmsmodules[$modulename]))
							{
								$cmsmodules[$modulename]['installed'] = true;
								$cmsmodules[$modulename]['active'] = ($result->fields['active'] == 1?true:false);
							}
						}
						else
						{
							if ($result->fields['active'] == 1)
							{
								if (is_file("$dir/$modulename/$modulename.module.php"))
								{
									#var_dump('loading module:' . $modulename);
									include("$dir/$modulename/$modulename.module.php");
									if (class_exists($modulename))
									{
										$newmodule = new $modulename;
										$name = $newmodule->GetName();

										global $CMS_VERSION;
										$dbversion = $result->fields['version'];

										#Check to see if there is an update and wether or not we should perform it
										if (version_compare($dbversion, $newmodule->GetVersion()) == -1 && $newmodule->AllowAutoUpgrade() == TRUE)
										{
											$newmodule->Upgrade($dbversion, $newmodule->GetVersion());
											$query = "UPDATE ".cms_db_prefix()."modules SET version = ? WHERE module_name = ?";
											$db->Execute($query, array($newmodule->GetVersion(), $name));
											$dbversion = $newmodule->GetVersion();
										}

										#Check to see if version in db matches file version
										if ($dbversion == $newmodule->GetVersion() && version_compare($newmodule->MinimumCMSVersion(), $CMS_VERSION) != 1)
										{
											$cmsmodules[$name]['object'] = $newmodule;
											$cmsmodules[$name]['installed'] = true;
											$cmsmodules[$name]['active'] = ($result->fields['active'] == 1?true:false);
										}
										else
										{
											unset($cmsmodules[$name]);
										}
									}
									else //No point in doing anything with it
									{
										unset($cmsmodules[$name]);
									}
								}
								else
								{
									unset($cmsmodules[$modulename]);
								}
							}
						}
					}
					$result->MoveNext();
				}
			}
		}
	}

	/**
	 * Finds all classes extending cmsmodule for loading
	 */
	function FindModules()
	{
		$result = array();

		foreach (get_declared_classes() as $oneclass)
		{
			if (strtolower(get_parent_class($oneclass)) == 'cmsmodule')
			{
				array_push($result, strtolower($oneclass));
			}
		}

		sort($result);

		return $result;
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

	/**
	 * Returns all parameters sent that are destined for the module with
	 * the given $id
	 */
	function GetModuleParameters($id)
	{
		$params = array();

		foreach ($_REQUEST as $key=>$value)
		{
			if (strpos($key, (string)$id) !== FALSE && strpos($key, (string)$id) == 0)
			{
				$key = str_replace($id, '', $key);
				$params[$key] = $value;
			}
		}

		return $params;
	}
}

/**
 * Base module class.
 *
 * All modules should inherit and extend this class with their functionality.
 *
 * @since		0.9
 * @package		CMS
 */
class CMSModule
{
	/**
	 * ------------------------------------------------------------------
	 * Initialization Functions and parameters
	 * ------------------------------------------------------------------
	 */
	var $cms;
	var $curlang;
	var $langhash;
	var $params;
	var $wysiwygactive=false;
  var $xml_exclude_files = array('^\.svn' , '^CVS$' , '^\#.*\#$' , '~$', '\.bak$' );
	var $xmldtd = '
<!DOCTYPE module [
  <!ELEMENT module (dtdversion,name,version,description*,help*,about*,requires*,file+)>
  <!ELEMENT dtdversion (#PCDATA)>
  <!ELEMENT name (#PCDATA)>
  <!ELEMENT version (#PCDATA)>
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

	function CMSModule()
	{
		global $gCms;
		$this->cms =& $gCms;
		$this->config =& $gCms->GetConfig();

		global $CMS_ADMIN_PAGE;
		if (isset($CMS_ADMIN_PAGE))
		{
			$this->curlang = '';
		}
		else
		{
			$this->curlang = (isset($config['locale']) && $config['locale'] != 'en_US' ? $config['locale'] : '');
		}
		$this->langhash = array();
		$this->params = array();
		$this->error = '';
		array_push($this->params, array(
			'name' => 'lang',
			'default' => 'en_US',
			'help' => lang('langparam'),
			'optional' => true
		));

		#$smarty = new CMSModuleSmarty($config, $this->GetName());
		$this->smarty = &$gCms->GetSmarty();

		$this->SetParameters();
	}

	/**
	 * ------------------------------------------------------------------
	 * Basic Functions.  Name and Version MUST be overridden.
	 * ------------------------------------------------------------------
	 */

	/**
	 * Returns a sufficient about page for a module
	 */
	function GetAbout()
	{
	  $str = '';
	  if ($this->GetAuthor() != '')
	    {
	      $str .= "<br />".lang('author').": " . $this->GetAuthor();
	      if ($this->GetAuthorEmail() != '')
		{
		  $str .= ' &lt;' . $this->GetAuthorEmail() . '&gt;';
		}
	      $str .= "<br />";
	    }
	  $str .= "<br />".lang('version').": " .$this->GetVersion() . "<br />";

	  if ($this->GetChangeLog() != '')
	    {
	      $str .= "<br />".lang('changehistory').":<br />";
	      $str .= $this->GetChangeLog() . '<br />';
	    }
	  return $str;
	}

	/**
	 * Returns a sufficient help page for a module
	 * this function should not be overridden
	 */
	function GetHelpPage()
	{
	  $str = '';
	  @ob_start();
	  echo $this->GetHelp();
	  $str .= @ob_get_contents();
	  @ob_end_clean();
	  $dependencies = $this->GetDependencies();
	  if (count($dependencies) > 0 )
	    {
	      $str .= '<h3>'.lang('dependencies').'</h3>';
	      $str .= '<ul>';
	      foreach( $dependencies as $dep => $ver )
		{
		  $str .= '<li>';
		  $str .= $dep.' =&gt; '.$ver;
		  $str .= '</li>';
		}
	      $str .= '</ul>';
	    }
	  $paramarray = $this->GetParameters();
	  if (count($paramarray) > 0)
	    {
	      $str .= '<h3>'.lang('parameters').'</h3>';
	      $str .= '<ul>';
	      foreach ($paramarray as $oneparam)
		{
		  $str .= '<li>';
		  if ($oneparam['optional'] == true)
		    {
		      $str .= '<em>(optional)</em> ';
		    }
		  $str .= $oneparam['name'].'="'.$oneparam['default'].'" - '.$oneparam['help'].'</li>';
		}
	      $str .= '</ul>';
	    }
	  return $str;
	}

	/**
	 * Returns the name of the module
	 */
	function GetName()
	{
		return 'unset';
	}

	/**
	 * Returns the full path of the module directory.
	 */
	function GetModulePath()
	{
		if (is_subclass_of($this, 'CMSModule'))
		{
			return dirname(dirname(__FILE__)) . '/modules/' . $this->GetName();
		}
		else
		{
			return dirname(__FILE__);
		}
	}

	/**
	 * Returns a translatable name of the module.  For modulues who's names can
	 * probably be translated into another language (like News)
	 */
	function GetFriendlyName()
	{
		return $this->GetName();
	}

	/**
	 * Returns the version of the module
	 */
	function GetVersion()
	{
		return '0.0.0.1';
	}

	/**
	 * Returns the minimum version necessary to run this version of the module.
	 */
	function MinimumCMSVersion()
	{
		global $CMS_VERSION;
		return $CMS_VERSION;
	}

	/**
	 * Returns the help for the module
	 *
	 * @param string Optional language that the admin is using.  If that language
	 * is not defined, use en_US.
	 */
	function GetHelp($lang = 'en_US')
	{
		return '';
	}

	/**
 	 * Returns a list of parameters and their help strings in a hash.  This is generally
     * used internally.
	 */
	function GetParameters()
	{
		return $this->params;
	}

	/**
	 * Setup you parameters here.  It doesn't have to be here, but it makes the
     * code more legible.
     */
	function SetParameters()
	{
	}

	function CreateParameter($param, $defaultval='', $helpstring='', $optional=true)
	{
		array_unshift($this->params, array(
			'name' => $param,
			'default' => $defaultval,
			'help' => $helpstring,
			'optional' => $optional
		));
	}

	/**
	 * Returns a short description of the module
	 *
	 * @param string Optional language that the admin is using.  If that language
	 * is not defined, use en_US.
	 */
	function GetDescription($lang = 'en_US')
	{
		return '';
	}

	/**
	 * Returns a description of what the admin link does.
	 *
	 * @param string Optional language that the admin is using.  If that language
	 * is not defined, use en_US.
	 */
	function GetAdminDescription($lang = 'en_US')
	{
		return '';
	}

	/**
	 * Returns the changelog for the module
	 */
	function GetChangeLog()
	{
		return '';
	}

	/**
	 * Returns the name of the author
	 */
	function GetAuthor()
	{
		return '';
	}

	/**
	 * Returns the email address of the author
	 */
	function GetAuthorEmail()
	{
		return '';
	}

	/**
	 * ------------------------------------------------------------------
	 * Reference functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Returns the cms->config object as a reference
	 */
	function & GetConfig()
	{
		global $gCms;
		$config = &$gCms->GetConfig();
		return $config;
	}

	/**
	 * Returns the cms->db object as a reference
	 */
	function & GetDb()
	{
		global $gCms;
		$db = &$gCms->GetDb();
		return $db;
	}

	/**
	 * Returns the cms->variables as a reference
	 */
	function & GetVariables()
	{
		return $this->cms->variables;
	}

	/**
	 * ------------------------------------------------------------------
	 * Content Type Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Does this module support a custom content type?
	 */
	function HasContentType()
	{
		return FALSE;
	}

	/**
	 * Return an instance of the new content type
	 */
	function GetContentTypeInstance()
	{
		return FALSE;
	}

	/**
	 * ------------------------------------------------------------------
	 * Installation Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Function that will get called as module is installed. This function should
	 * do any initialization functions including creating database tables. It
	 * should return a string message if there is a failure. Returning nothing (FALSE)
	 * will allow the install procedure to proceed.
	 */
	function Install()
	{
		return FALSE;
	}

	/**
	 * Display a message after a successful installation of the module.
	 */
	function InstallPostMessage()
	{
		return FALSE;
	}

	/**
	 * Function that will get called as module is uninstalled. This function should
	 * remove any database tables that it uses and perform any other cleanup duties.
	 * It should return a string message if there is a failure. Returning nothing
	 * (FALSE) will allow the uninstall procedure to proceed.
	 */
	function Uninstall()
	{
		return FALSE;
	}

	/**
	 * Display a message and a Yes/No dialog before doing an uninstall.  Returning noting
	 * (FALSE) will go right to the uninstall.
	 */
	function UninstallPreMessage()
	{
		return FALSE;
	}

	/**
	 * Display a message after a successful uninstall of the module.
	 */
	function UninstallPostMessage()
	{
		return FALSE;
	}

	/**
	 * Function to perform any upgrade procedures. This is mostly used to for
	 * updating databsae tables, but can do other duties as well. It should
	 * return a string message if there is a failure. Returning nothing (FALSE)
	 * will allow the upgrade procedure to proceed. Upgrades should have a path
	 * so that they can be upgraded from more than one version back.  While not
	 * a requirement, it makes like easy for your users.
	 *
	 * @param string The version we are upgrading from
	 * @param string The version we are upgrading to
	 */
	function Upgrade($oldversion, $newversion)
	{
		return FALSE;
	}

	/**
	 * Returns whether or not modules should be autoupgraded while upgrading
	 * CMS versions.  Generally only useful for modules included with the CMS
	 * base install, but there could be a situation down the road where we have
	 * different distributions with different modules included in them.  Defaults
	 * to TRUE, as there is not many reasons to not allow it.
	 */
	function AllowAutoInstall()
	{
		return TRUE;
	}

	/**
	 * Returns whether or not modules should be autoupgraded while upgrading
	 * CMS versions.  Generally only useful for modules included with the CMS
	 * base install, but there could be a situation down the road where we have
	 * different distributions with different modules included in them.  Defaults
	 * to TRUE, as there is not many reasons to not allow it.
	 */
	function AllowAutoUpgrade()
	{
		return TRUE;
	}

 	/**
	 * Returns a list of dependencies and minimum versions that this module
	 * requires. It should return an hash, eg.
	 * return array('somemodule'=>'1.0', 'othermodule'=>'1.1');
	 */
	function GetDependencies()
	{
		return array();
	}

 	/**
	 * Checks to see if currently installed modules depend on this module.  This is
	 * used by the plugins.php page to make sure that a module can't be uninstalled
	 * before any modules depending on it are uninstalled first.
	 */
	function CheckForDependents()
	{
		global $gCms;
		$db =& $gCms->GetDb();

		$result = false;

		$query = "SELECT * FROM ".cms_db_prefix()."module_deps WHERE parent_module = ?";
		$dbresult = $db->Execute($query, array($this->GetName()));

		if ($dbresult && $dbresult->RecordCount() > 0)
		{
			$result = true;
		}

		return $result;
	}


	/**
	 * Creates an xml data package from the module directory.
	 */
	function CreateXMLPackage( &$message, &$filecount )
	{
	  // get a file list
	  $filecount = 0;
	  $dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules".DIRECTORY_SEPARATOR.$this->GetName();
	  $files = get_recursive_file_list( $dir, $this->xml_exclude_files );

	  $xmltxt  = '<?xml version="1.0" encoding="ISO-8859-1"?>';
	  $xmltxt .= $this->xmldtd."\n";
	  $xmltxt .= "<module>\n";
          $xmltxt .= "  <dtdversion>".MODULE_DTD_VERSION."</dtdversion>\n";
	  $xmltxt .= "  <name>".$this->GetName()."</name>\n";
	  $xmltxt .= "  <version>".$this->GetVersion()."</version>\n";
	  $xmltxt .= "  <help>".base64_encode($this->GetHelpPage())."</help>\n";
	  $xmltxt .= "  <about>".base64_encode($this->GetAbout())."</about>\n";
	  $desc = $this->GetAdminDescription();
	  if( $desc != '' )
	    {
	      $xmltxt .= "  <description>".$desc."</description>\n";
	    }
	  $depends = $this->GetDependencies();
	  foreach( $depends as $key=>$val )
	    {
	      $xmltxt .= "  <requires>\n";
              $xmltxt .= "    <requiredname>$key</requiredname>\n";
              $xmltxt .= "    <requiredversion>$val</requiredversion>\n";
	      $xmltxt .= "  </requires>\n";
	    }
	  foreach( $files as $file )
	    {
	      // strip off the beginning
	      if (substr($file,0,strlen($dir)) == $dir)
	         {
             $file = substr($file,strlen($dir));
             }
	      if( $file == '' ) continue;

	      $xmltxt .= "  <file>\n";
	      $filespec = $dir.DIRECTORY_SEPARATOR.$file;
	      $xmltxt .= "    <filename>$file</filename>\n";
	      if( is_dir( $filespec ) )
		{
		  $xmltxt .= "    <isdir>1</isdir>\n";
		}
	      else
		{
		  $xmltxt .= "    <isdir>0</isdir>\n";
		  $data = base64_encode(file_get_contents($filespec));
		  $xmltxt .= "    <data><![CDATA[".$data."]]></data>\n";
		}

	      $xmltxt .= "  </file>\n";
	      ++$filecount;
	    }
          $xmltxt .= "</module>\n";
	  $message = 'XML package of '.strlen($xmltxt).' bytes created for '.$this->GetName();
	  $message .= ' including '.$filecount.' files';
	  return $xmltxt;
	}


	/**
	 * Return true if there is an admin for the module.  Returns false by
	 * default.
	 */
	function HasAdmin()
	{
		return false;
	}

	/**
	 * Returns which admin section this module belongs to.
	 * this is used to place the module in the appropriate admin navigation
	 * section. Valid options are currently:
	 *
	 * content, layout, files, usersgroups, extensions, preferences, admin
     *
	 */
	function GetAdminSection()
	{
		return 'extensions';
	}

    /**
     * Returns true or false, depending on whether the user has the
     * right permissions to see the module in their Admin menus.
     *
     * Defaults to true.
     */
    function VisibleToAdminUser()
    {
    	return true;
    }

	/**
	 * Returns true if the module should be treated as a content module.
	 * Returns false by default.
	 */
	function IsContentModule()
	{
		return false;
	}

	/**
	 * Returns true if the module should be treated as a plugin module (like
	 * {cms_module module='name'}.  Returns false by default.
	 */
	function IsPluginModule()
	{
		return false;
	}

	/**
	 * Returns true if the module acts as a soap server
	 */
	function IsSoapModule()
	{
		return false;
	}

	/**
	 * ------------------------------------------------------------------
	 * Login Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Called after a successful login.  It sends the user object.
	 *
	 * @param User The user that just logged in
	 */
	function LoginPost(&$user)
	{
	}

	/**
	 * Called after a successful logout.
	 */
	function LogoutPost()
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * User Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Called before a user is added to the database.  Sends the user object.
	 *
	 * @param User The user that was just created
	 */
	function AddUserPre(&$user)
	{
	}

	/**
	 * Called after a user is added to the database.  Sends the user object.
	 *
	 * @param User The user that was just created
	 */
	function AddUserPost(&$user)
	{
	}

	/**
	 * Called before a user is saved to the database.  Sends the user object.
	 *
	 * @param User The user that was just edited
	 */
	function EditUserPre(&$user)
	{
	}

	/**
	 * Called after a user is saved to the database.  Sends the user object.
	 *
	 * @param User The user that was just edited
	 */
	function EditUserPost(&$user)
	{
	}

	/**
	 * Called before a user is deleted from the database.  Sends the user object.
	 *
	 * @param User The user that was just deleted
	 */
	function DeleteUserPre(&$user)
	{
	}

	/**
	 * Called after a user is deleted from the database.  Sends the user object.
	 *
	 * @param User The user that was just deleted
	 */
	function DeleteUserPost(&$user)
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * Group Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Called before a group is added to the database.  Sends the group object.
	 *
	 * @param Group The group that was just created
	 */
	function AddGroupPre(&$group)
	{
	}

	/**
	 * Called after a group is added to the database.  Sends the group object.
	 *
	 * @param Group The group that was just created
	 */
	function AddGroupPost(&$group)
	{
	}

	/**
	 * Called before a group is saved to the database.  Sends the group object.
	 *
	 * @param Group The group that was just edited
	 */
	function EditGroupPre(&$group)
	{
	}

	/**
	 * Called after a group is saved to the database.  Sends the group object.
	 *
	 * @param Group The group that was just edited
	 */
	function EditGroupPost(&$group)
	{
	}

	/**
	 * Called before a group is deleted from the database.  Sends the group object.
	 *
	 * @param Group The group that was just deleted
	 */
	function DeleteGroupPre(&$group)
	{
	}

	/**
	 * Called after a group is deleted from the database.  Sends the group object.
	 *
	 * @param Group The group that was just deleted
	 */
	function DeleteGroupPost(&$group)
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * Template Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Called before a template is added to the database.  Sends the template
	 * object.
	 *
	 * @param Template The template that was just created
	 */
	function AddTemplatePre(&$template)
	{
	}

	/**
	 * Called after a template is added to the database.  Sends the template
	 * object.
	 *
	 * @param Template The template that was just created
	 */
	function AddTemplatePost(&$template)
	{
	}

	/**
	 * Called before a template is saved to the database.  Sends the template
	 * object.
	 *
	 * @param Template The template that was just edited
	 */
	function EditTemplatePre(&$template)
	{
	}

	/**
	 * Called after a template is saved to the database.  Sends the template
	 * object.
	 *
	 * @param Template The template that was just edited
	 */
	function EditTemplatePost(&$template)
	{
	}

	/**
	 * Called before a template is deleted from the database.  Sends the template
	 * object.
	 *
	 * @param Template The template that was just deleted
	 */
	function DeleteTemplatePre(&$template)
	{
	}

	/**
	 * Called after a template is deleted from the database.  Sends the template
	 * object.
	 *
	 * @param Template The template that was just deleted
	 */
	function DeleteTemplatePost(&$template)
	{
	}

	function TemplatePreCompile(&$template)
	{
	}

	function TemplatePostCompile(&$template)
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * General Content Related Functions
	 * ------------------------------------------------------------------
	 */

	function ContentEditPre(&$content)
	{
	}

	function ContentEditPost(&$content)
	{
	}

	function ContentDeletePre(&$content)
	{
	}

	function ContentDeletePost(&$content)
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * Stylesheet Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Called before a Stylesheet is added to the database.  Sends the stylesheet
	 * object.
	 *
	 * @param Stylesheet The stylesheet that was just created
	 */
	function AddStylesheetPre(&$stylesheet)
	{
	}

	/**
	 * Called after a stylesheet is added to the database.  Sends the stylesheet
	 * object.
	 *
	 * @param Stylesheet The stylesheet that was just created
	 */
	function AddStylesheetPost(&$stylesheet)
	{
	}

	/**
	 * Called before a stylesheet is saved to the database.  Sends the stylesheet
	 * object.
	 *
	 * @param stylesheet The stylesheet that was just edited
	 */
	function EditStylesheetPre(&$stylesheet)
	{
	}

	/**
	 * Called after a stylesheet is saved to the database.  Sends the stylesheet
	 * object.
	 *
	 * @param stylesheet The stylesheet that was just edited
	 */
	function EditStylesheetPost(&$stylesheet)
	{
	}

	/**
	 * Called before a stylesheet is deleted from the database.  Sends the stylesheet
	 * object.
	 *
	 * @param stylesheet The stylesheet that was just deleted
	 */
	function DeleteStylesheetPre(&$stylesheet)
	{
	}

	/**
	 * Called after a stylesheet is deleted from the database.  Sends the stylesheet
	 * object.
	 *
	 * @param stylesheet The stylesheet that was just deleted
	 */
	function DeleteStylesheetPost(&$stylesheet)
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * HTML Blob Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Called before an HTML blob is added to the database.  Sends the html blob
	 * object.
	 *
	 * @param HtmlBlob The HTML blob that was just created
	 */
	function AddHtmlBlobPre(&$htmlblob)
	{
	}

	/**
	 * Called after an HTML blob is added to the database.  Sends the html blob
	 * object.
	 *
	 * @param HtmlBlob The HTML blob that was just created
	 */
	function AddHtmlBlobPost(&$htmlblob)
	{
	}

	/**
	 * Called before an HTML blob is saved to the database.  Sends the html blob
	 * object.
	 *
	 * @param HtmlBlob The HTML blob that was just edited
	 */
	function EditHtmlBlobPre(&$htmlblob)
	{
	}

	/**
	 * Called after an HTML blob is saved to the database.  Sends the html blob
	 * object.
	 *
	 * @param HtmlBlob The HTML blob that was just edited
	 */
	function EditHtmlBlobPost(&$htmlblob)
	{
	}

	/**
	 * Called before an HTML blob is deleted from the database.  Sends the html
	 * blob object.
	 *
	 * @param HtmlBlob The HTML blob that was just deleted
	 */
	function DeleteHtmlBlobPre(&$htmlblob)
	{
	}

	/**
	 * Called after an HTML blob is deleted from the database.  Sends the html
	 * blob object.
	 *
	 * @param HtmlBlob The HTML blob that was just deleted
	 */
	function DeleteHtmlBlobPost(&$htmlblob)
	{
	}

	function GlobalContentPreCompile(&$gc)
	{
	}

	function GlobalContentPostCompile(&$gc)
	{
	}


	/**
	 * ------------------------------------------------------------------
	 * Content Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Called with the content of the template before it's sent to smarty
	 * for processing.
	 *
	 * Deprecated:  This isn't called anymore.
	 *
	 * @param string The template text
	 */
	function ContentTemplate(&$template)
	{
	}

	/**
	 * Called with the content of the stylesheet before it is pasted into the
	 * template.
	 *
	 * @param string The stylesheet text
	 */
	function ContentStylesheet(&$stylesheet)
	{
	}

	/**
	 * Called with the title before it is pasted into the template.
	 *
	 * Deprecated:  This isn't called anymore.
	 *
	 * @param string The title text
	 */
	function ContentTitle(&$title)
	{
	}

	/**
	 * Called with the content data before it is pasted into the template.
	 *
	 * Deprecated:  This isn't called anymore.  Use ContentPreCompile.
	 *
	 * @param string The content text
	 */
	function ContentData(&$content)
	{
	}

	/**
	 * Called with the content of the html blob before it is pasted into the
	 * template (but after content is pasted in)
	 *
	 * Deprecated:  This isn't called anymore.  Use GlobalContentPreCompile.
	 *
	 * @param string The html blob text
	 */
	function ContentHtmlBlob(&$htmlblob)
	{
	}

	/**
	 * Called before the pasted together template/content/html blobs/etc are
	 * sent to smarty for processing.
	 *
	 * Deprecated:  Not useful anymore, since it's all handled separately now
	 *
	 * @param string The prerendered text
	 */
	function ContentPreRender(&$content)
	{
	}

	/**
	 * Called before the content is sent off to smarty for processing.  Basically 
	 * overlaps with ContentPreRender, but it makes more sense to be named 
	 * PreCompile.
	 *
	 * @param string The precompiled text
	 */
	function ContentPreCompile(&$content)
	{
	}

	/**
	 * Called right after smarty is done processing and ready to head off to the 
	 * cache.  Does the same as PostRenderNonCached, but with a better name.
	 *
	 * @param string The postcompiled text
	 */
	function ContentPostCompile(&$content)
	{
	}

	/**
	 * This serves no purpose anymore.  Template, content and html blobs are 
	 * never pushed together at any point and cached.
	 *
	 * Deprecated
	 *
	 * @param string The postrendered text
	 */
	function ContentPostRenderNonCached(&$content)
	{
	}

	/**
	 * Called after content is sent to smarty for processing and right before
	 * display.  Cached content will still call this function before display,
	 * but it is called EVERY time a page is requested.
	 *
	 * @param string The postrendered text
	 */
	function ContentPostRender(&$content)
	{
	}

	/**
	 * Called before any smarty "template" (content blocks/content tempaltes/modules)
	 * gets pushed off for compilation.
	 * (new in 0.12)
	 *
	 * @param string The precompiled text
	 */
	function SmartyPreCompile(&$content)
	{
	}

	/**
	 * Called after any smarty "template" (content blocks/content tempaltes/modules)
	 * is done being compiled by smarty, but before caching.
	 * (new in 0.12)
	 *
	 * @param string The precompiled text
	 */
	function SmartyPostCompile(&$content)
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * WYSIWYG Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Returns true if this module should be treated as a WYSIWYG module. It
	 * returns false be default.
	 */
	function IsWYSIWYG()
	{
		return false;
	}

	/**
	 * Returns true if this wysiwyg should be considered active, eventhough it's
	 * not the choice of the user. Used for forcing a wysiwyg.
	 * returns false be default.
	 */
	function WYSIWYGActive()
	{
		return $this->wysiwygactive;
	}

	/**
	 * Returns content destined for the <form> tag.  It's useful if javascript is
	 * needed for the onsubmit of the form.
	 */
	function WYSIWYGPageForm()
	{
		return '';
	}

	/**
	 * This is a function that would be called before a form is submitted.
	 * Generally, a dropdown box or something similar that would force a submit
	 * of the form via javascript should put this in their onchange line as well
	 * so that the WYSIWYG can do any cleanups before the actual form submission
	 * takes place.
	 */
	 function WYSIWYGPageFormSubmit()
	 {
	 	return '';
	 }

	 /**
	  * Returns header code specific to this WYSIWYG
	  *
	  * @param string The html-code of the page before replacing WYSIWYG-stuff
	  */
	  function WYSIWYGGenerateHeader($htmlresult='')
	  {
	  	return '';
	  }

	 /**
	  * Returns body code specific to this WYSIWYG
	  */
	  function WYSIWYGGenerateBody()
	  {
	  	return '';
	  }

	/**
	 * Returns the textarea specific for this WYSIWYG.
	 *
	 * @param string HTML name of the textarea
	 * @param int Number of columns wide that the textarea should be
	 * @param int Number of rows long that the textarea should be
	 * @param string Encoding of the content
	 * @param string Content to show in the textarea
	 * @param string Stylesheet for content, if available
	 */
	function WYSIWYGTextarea($name='textarea',$columns='80',$rows='15',$encoding='',$content='',$stylesheet='')
	{
		$this->wysiwygactive=true;
		return '<textarea name="'.$name.'" cols="'.$columns.'" rows="'.$rows.'">'.$content.'</textarea>';
	}

	/**
	 * Returns whether or not this module should show in any module lists generated by a WYSIWYG.
     */
	function ShowInWYSIWYG()
	{
		return true;
	}

	/**
	 * ------------------------------------------------------------------
	 * Navigation Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Used for navigation between "pages" of a module.  Forms and links should
	 * pass an action with them so that the module will know what to do next.
	 * By default, DoAction will be passed 'default' and 'defaultadmin',
	 * depending on where the module was called from.  If being used as a module
	 * or content type, 'default' will be passed.  If the module was selected
	 * from the list on the admin menu, then 'defaultadmin' will be passed.
	 *
	 * @param string Name of the action to perform
	 * @param string The ID of the module
	 * @param string The parameters targeted for this module
	 */
	function DoAction($name, $id, $params, $returnid='')
	{
		if ($name != '')
		{
			$filename = dirname(dirname(dirname(__FILE__))) . '/modules/'.$this->GetName().'/action.' . $name . '.php';
			if (is_file($filename))
			{
				{
					global $gCms;
					$db =& $gCms->GetDb();
					$config =& $gCms->GetConfig();
					$smarty =& $gCms->GetSmarty();

					include($filename);

				}
			}
		}
	}

	function DoActionBase($name, $id, $params, $returnid='')
	{
		if (isset($params['lang']))
		{
			$this->curlang = $params['lang'];
		}
        if( !isset($params['action']) )
        {
            $params['action'] = $name;
        }
		return $this->DoAction($name, $id, $params, $returnid);
	}

	/**
	 * Returns the start of a module form
	 *
	 * @param string The id given to the module on execution
	 * @param string The action that this form should do when the form is submitted
	 * @param string The id to eventually return to when the module is finished it's task
	 * @param string Method to use for the form tag.  Defaults to 'post'
	 * @param string Optional enctype to use, Good for situations where files are being uploaded
	 * @param boolean A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
	 * @param string Text to append to the end of the id and name of the form
	 */
	function CreateFormStart($id, $action='default', $returnid='', $method='post', $enctype='', $inline=false, $idsuffix='')
	{
		global $gCms;

		$formcount = 1;
		$variables = &$gCms->variables;

		if (isset($variables['formcount']))
			$formcount = $variables['formcount'];

		if ($idsuffix == '')
			$idsuffix = $formcount;

		$goto = ($returnid==''?'moduleinterface.php':'index.php');
		#$goto = 'moduleinterface.php';
		if ($inline && $returnid != '')
		{
			#$goto = 'index.php?module='.$this->GetName().'&amp;id='.$id.'&amp;'.$id.'action='.$action;
			#$goto = 'index.php?mact='.$this->GetName().','.$id.','.$action;
			#$goto .= '&amp;'.$id.'returnid='.$returnid;
			#$goto .= '&amp;'.$this->cms->config['query_var'].'='.$returnid;
		}
		$text = '<form id="'.$id.'moduleform-'.$idsuffix.'" name="'.$id.'moduleform-'.$idsuffix.'" method="'.$method.'" action="'.$goto.'"';//moduleinterface.php
		if ($enctype != '')
		{
			$text .= ' enctype="'.$enctype.'"';
		}
		/*
		$text .= '><div class="hidden"><input type="hidden" name="module" value="'.$this->GetName().'" /><input type="hidden" name="id" value="'.$id.'" />';
		if ($action != '')
		{
			$text .= '<input type="hidden" name="'.$id.'action" value="'.$action.'" />';
		}
		*/
		$text .= '><div class="hidden"><input type="hidden" name="mact" value="'.$this->GetName().','.$id.','.$action.','.($inline == true?1:0).'" />';
		if ($returnid != '')
		{
			$text .= '<input type="hidden" name="'.$id.'returnid" value="'.$returnid.'" />';
			if ($inline)
			{
				$text .= '<input type="hidden" name="'.$this->cms->config['query_var'].'" value="'.$returnid.'" />';
			}
		}
		$text .= "</div>\n";

		$formcount = $formcount + 1;
		$variables['formcount'] = $formcount;

		return $text;
	}

	/**
	 * Returns the end of the a module form.  This is basically just a wrapper around </form>, but
	 * could be extended later on down the road.  It's here mainly for consistency.
	 */
	function CreateFormEnd()
	{
		return '</form>'."\n";
	}

	/**
	 * Returns the xhtml equivalent of an input textbox.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The predefined value of the textbox, if any
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string The maximum number of characters that should be allowed to be entered
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateInputText($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
	{
		$value = str_replace('"', '&quot;', $value);
		$text = '<input type="text" name="'.$id.$name.'" value="'.$value.'" size="'.$size.'" maxlength="'.$maxlength.'"';
		if ($addttext != '')
		{
			$text .= ' ' . $addttext;
		}
		$text .= " />\n";
		return $text;
	}

	/**
	 * Returns the xhtml equivalent of a file-selector field.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The MIME-type to be accepted, default is all
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateInputFile($id, $name, $accept='', $size='10',$addttext='')
	{
		$text='<input type="file" name="'.$id.$name.'" size="'.$size.'"';
		if ($accept != '')
		{
			$text .= ' ' . $accept;
		}
		if ($addttext != '')
		{
			$text .= ' ' . $addttext;
		}
		$text .= " />\n";
		return $text;
	}

	/**
	 * Returns the xhtml equivalent of an input password-box.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The predefined value of the textbox, if any
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string The maximum number of characters that should be allowed to be entered
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateInputPassword($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
	{
		$value = str_replace('"', '&quot;', $value);
		$text = '<input type="password" name="'.$id.$name.'" value="'.$value.'" size="'.$size.'" maxlength="'.$maxlength.'"';
		if ($addttext != '')
		{
			$text .= ' ' . $addttext;
		}
		$text .= " />\n";
		return $text;
	}

	/**
	 * Returns the xhtml equivalent of a hidden field.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the hidden field
	 * @param string The predefined value of the field, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateInputHidden($id, $name, $value='', $addttext='')
	{
		$value = str_replace('"', '&quot;', $value);
		$text = '<input type="hidden" name="'.$id.$name.'" value="'.$value.'"';
		if ($addttext != '')
		{
			$text .= ' '.$addttext;
		}
		$text .= " />\n";
		return $text;
	}

	/**
	 * Returns the xhtml equivalent of a checkbox.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the checkbox
	 * @param string The predefined value of the field, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateInputCheckbox($id, $name, $value='', $selectedvalue='', $addttext='')
	{
		$text = '<input type="checkbox" name="'.$id.$name.'" value="'.$value.'"';
		if ($selectedvalue == $value)
		{
			$text .= ' ' . 'checked="checked"';
		}
		if ($addttext != '')
		{
			$text .= ' '.$addttext;
		}
		$text .= " />\n";
		return $text;
	}


	/**
	 * Returns the xhtml equivalent of a submit button.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the button
	 * @param string The predefined value of the button, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 * @param string Use an image instead of a regular button
	 */
	function CreateInputSubmit($id, $name, $value='', $addttext='', $image='', $confirmtext='')
	{
		global $gCms;

		$text = '<input name="'.$id.$name.'" value="'.$value.'" type=';

		if ($image != '')
		{
			$text .= '"image"';
			$img = $gCms->config['root_url'] . '/' . $image;
			$text .= ' src="'.$img.'"';
		}
		else
		{
			$text .= '"submit"';
		}
		if ($confirmtext != '' )
		  {
		    $text .= 'onclick="return confirm(\''.$confirmtext.'\');"';
		  }
		if ($addttext != '')
		{
			$text .= ' '.$addttext;
		}

		$text .= ' />';

		return $text . "\n";
	}

	/**
	 * Returns the xhtml equivalent of a reset button.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the button
	 * @param string The predefined value of the button, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateInputReset($id, $name, $value='Reset', $addttext='')
	{
		$text = '<input type="reset" name="'.$id.$name.'" value="'.$value.'"';
		if ($addttext != '')
		{
			$text .= ' '.$addttext;
		}
		$text .= ' />';
		return $text . "\n";
	 }

	/**
	 * Returns the xhtml equivalent of a file upload input.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the input
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateFileUploadInput($id, $name, $addttext='')
	{
		$text = '<input type="file" name="'.$id.$name.'"';
		if ($addttext != '')
		{
			$text .= ' '.$addttext;
		}
		$text .= ' />';
		return $text . "\n";
	}


	/**
	 * Returns the xhtml equivalent of a dropdown list.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it is xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the dropdown list
	 * @param string An array of items to put into the dropdown list... they should be $key=>$value pairs
	 * @param string The default selected index of the dropdown list.  Setting to -1 will result in the first choice being selected
	 * @param string The default selected value of the dropdown list.  Setting to '' will result in the first choice being selected
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateInputDropdown($id, $name, $items, $selectedindex=-1, $selectedvalue='', $addttext='')
	{
		$text = '<select name="'.$id.$name.'"';
		if ($addttext != '')
		{
			$text .= ' ' . $addttext;
		}
		$text .= '>';
		$count = 0;
		foreach ($items as $key=>$value)
		{
			$text .= '<option value="'.$value.'"';
			if ($selectedindex == $count || $selectedvalue == $value)
			{
				$text .= ' ' . 'selected="selected"';
			}
			$text .= '>';
			$text .= $key;
			$text .= '</option>';
			$count++;
		}
		$text .= '</select>'."\n";

		return $text;
	}

	/**
	 * Returns the xhtml equivalent of a multi-select list.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it is xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the select list
	 * @param string An array of items to put into the list... they should be $key=>$value pairs
	 * @param string An array of items in the list that should default to selected.
	 * @param string The number of rows to be visible in the list (before scrolling).
	 * @param string Any additional text that should be added into the tag when rendered
	 * @param boolean indicates wether multiple selections are allowed (defaults to true)
	 */
	function CreateInputSelectList($id, $name, $items, $selecteditems=array(), $size=3, $addttext='', $multiple = true)
	{
		$text = '<select name="'.$id.$name.'"';
		if ($addttext != '')
		{
			$text .= ' ' . $addttext;
		}
		if( $multiple )
		  {
		    $text .= ' multiple="multiple"';
		  }
		$text .= 'size="'.$size.'">';
		$count = 0;
		foreach ($items as $key=>$value)
		{
			$text .= '<option value="'.$value.'"';
			if (in_array($value, $selecteditems))
			{
				$text .= ' ' . 'selected="selected"';
			}
			$text .= '>';
			$text .= $key;
			$text .= '</option>';
			$count++;
		}
		$text .= '</select>'."\n";

		return $text;
	}

	/**
	 * Returns the xhtml equivalent of a set of radio buttons.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it is xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the radio group
	 * @param string An array of items to create as radio buttons... they should be $key=>$value pairs
	 * @param string The default selected index of the radio group.  Setting to -1 will result in the first choice being selected
	 * @param string Any additional text that should be added into the tag when rendered
	 * @param string A delimiter to throw between each radio button, e.g., a <br /> tag or something for formatting
	 */
	function CreateInputRadioGroup($id, $name, $items, $selectedvalue='', $addttext='', $delimiter='')
	{
		$text = '';
		foreach ($items as $key=>$value)
		{
			$text .= '<input type="radio" name="'.$id.$name.'" value="'.$value.'"';
			if ($addttext != '')
			{
				$text .= ' ' . $addttext;
			}
			if ($selectedvalue == $value)
			{
				$text .= ' ' . 'checked="checked"';
			}
			$text .= ' />';
			$text .= $key . $delimiter;
		}

		return $text;
	}

	/**
	 * Returns the xhtml equivalent of a textarea.  Also takes WYSIWYG preference into consideration if it's called from the admin side.
	 *
	 * @param bool Should we try to create a WYSIWYG for this textarea?
	 * @param string The id given to the module on execution
	 * @param string The text to display in the textarea's content
	 * @param string The html name of the textarea
	 * @param string The CSS class to associate this textarea to
	 * @param string The html id to give to this textarea
	 * @param string The encoding to use for the content
	 * @param string The text of the stylesheet associated to this content.  Only used for certain WYSIWYGs
	 * @param string The number of characters wide (columns) the resulting textarea should be
	 * @param string The number of characters high (rows) the resulting textarea should be
	 * @param string The wysiwyg-system to be forced even if the user has chosen another one
	 */
	function CreateTextArea($enablewysiwyg, $id, $text, $name, $classname='', $htmlid='', $encoding='', $stylesheet='', $width='80', $cols='15',$forcewysiwyg="")
	{
		return create_textarea($enablewysiwyg, $text, $id.$name, $classname, $htmlid, $encoding, $stylesheet, $width, $cols,$forcewysiwyg);
	}

	/**
	 * Returns the xhtml equivalent of an href link  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The action that this form should do when the link is clicked
	 * @param string The id to eventually return to when the module is finished it's task
	 * @param string The text that will have to be clicked to follow the link
	 * @param string An array of params that should be inlucded in the URL of the link.  These should be in a $key=>$value format.
	 * @param string Text to display in a javascript warning box.  If they click no, the link is not followed by the browser.
	 * @param boolean A flag to determine if only the href section should be returned
	 * @param boolean A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateLink($id, $action, $returnid='', $contents='', $params=array(), $warn_message='', $onlyhref=false, $inline=false, $addttext='', $targetcontentonly=false)
	{
		$text = '';
		if ($targetcontentonly || ($returnid != '' && !$inline))
		{
			$id = 'cntnt01';
		}
		$goto = 'index.php';
		$class = (isset($params['class'])?$params['class']:'');
		if ($returnid == '')
		{
			$goto = 'moduleinterface.php';
		}
		if (!$onlyhref)
		{
			$text .= '<a';
			if ($class != '')
			{
				$text .= ' class="'.$class.'"';
			}
			$text .= ' href="';
		}
		$text .= $this->cms->config['root_url'];
		if (!($returnid != '' && $returnid > -1))
		{
			$text .= '/'.$this->cms->config['admin_dir'];
		}

		#$text .= '/'.$goto.'?module='.$this->GetName().'&amp;id='.$id.'&amp;'.$id.'action='.$action;
		$text .= '/'.$goto.'?mact='.$this->GetName().','.$id.','.$action.','.($inline == true?1:0);

		foreach ($params as $key=>$value)
		{
			if ($key != 'module')
				$text .= '&amp;'.$id.$key.'='.rawurlencode($value);
		}
		if ($returnid != '')
		{
			$text .= '&amp;'.$id.'returnid='.$returnid;
			if ($inline)
			{
				$text .= '&amp;'.$this->cms->config['query_var'].'='.$returnid;
			}
		}

		if (!$onlyhref)
		{
			$text .= "\"";
			if ($warn_message != '')
			{
				$text .= ' onclick="return confirm(\''.$warn_message.'\');"';
			}

			if ($addttext != '')
			{
				$text .= ' ' . $addttext;
			}

			$text .= '>'.$contents.'</a>';

			debug_buffer($text);
		}
		return $text;
	}

	/**
	 * Returns the xhtml equivalent of an href link for content links.  This is basically a nice
         * little wrapper to make sure that we go back to where we want and that it's xhtml complient
         *
         * @param string the page id of the page we want to direct to
	 */
        function CreateContentLink($pageid,$contents='')
        {
	  global $gCms;
	  $config = &$gCms->config;
	  $text = '<a href="';
	  if ($config["assume_mod_rewrite"])
	    {
	      # mod_rewrite
	      $alias = ContentManager::GetPageAliasFromID( $pageid );
	      if( $alias == false )
		{
		  return '<!-- ERROR: could not get an alias for pageid='.$pageid.'-->';
		}
              else
		{
		  $text .= $config["root_url"]."/".$alias.
		    (isset($config['page_extension'])?$config['page_extension']:'.shtml');
		}
	    }
	  else
	    {
	      # mod rewrite
	      $text .= $config["root_url"]."/index.php?".$config["query_var"]."=".$pageid;
	    }
	  $text .= '">'.$contents.'</a>';
	  return $text;
	}


	/**
	 * Returns the xhtml equivalent of an href link for Content links.  This is basically a nice little wrapper
	 * to make sure that we go back to where we want to and that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The id to return to when the module is finished it's task
	 * @param string The text that will have to be clicked to follow the link
	 * @param string An array of params that should be inlucded in the URL of the link.  These should be in a $key=>$value format.
	 * @param boolean A flag to determine if only the href section should be returned
	 */
	function CreateReturnLink($id, $returnid, $contents='', $params=array(), $onlyhref=false)
	{
		$text = '';
		global $gCms;
		$config = &$gCms->config;
		$manager =& $gCms->GetHierarchyManager();
		$node =& $manager->sureGetNodeById($returnid);
		$content =& $node->GetContent();

		if (isset($content))
		{
			if ($content->GetURL(false) != '')
			{
				if (!$onlyhref)
				{
					$text .= '<a href="';
				}
				$text .= $content->GetURL(false);

				$count = 0;
				foreach ($params as $key=>$value)
				{
					if ($count > 0)
					{
						if ($config["assume_mod_rewrite"] && $rewrite == true)
							$text .= '?';
						else
							$text .= '&amp;';
					}
					else
					{
						$text .= '&amp;';
					}
					$text .= $id.$key.'='.$value;
					$count++;
				}
				if (!$onlyhref)
				{
					$text .= "\"";
					$text .= '>'.$contents.'</a>';
				}
			}
		}

		return $text;
	}


	/**
	 * Redirects the user to another action of the module.
	 *
	 * @param string The id given to the module on execution
	 * @param string The action that this form should do when the form is submitted
	 * @param string The id to eventually return to when the module is finished it's task
	 * @param string An array of params that should be inlucded in the URL of the link.  These should be in a $key=>$value format.
	 * @param boolean A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
	 */
	function Redirect($id, $action, $returnid='', $params=array(), $inline=false)
	{
		global $gCms;
		$config = $gCms->config;

		$name = $this->GetName();

		#Suggestion by Calguy to make sure 2 actions don't get sent
		if (isset($params['action']))
		{
			unset($params['action']);
		}
		if (isset($params['id']))
		{
			unset($params['id']);
		}
		if (isset($params['module']))
		{
			unset($params['module']);
		}

		if (!$inline && $returnid != '')
			$id = 'cntnt01';

		$text = '';
		if ($returnid != '')
		{
			$text .= 'index.php';
		}
		else
		{
			$text .= 'moduleinterface.php';
		}
		#$text .= '?module='.$name.'&'.$id.'action='.$action.'&id='.$id;
		$text .= '?mact='.$name.','.$id.','.$action.','.($inline == true?1:0);
		if ($returnid != '')
		{
			$text .= '&'.$id.'returnid='.$returnid;
		}
		foreach ($params as $key=>$value)
		{
			$text .= '&'.$id.$key.'='.rawurlencode($value);
		}
		#var_dump($text);
		redirect($text);
	}

	/**
	 * Redirects the user to a content page outside of the module.  The passed around returnid is
	 * frequently used for this so that the user will return back to the page from which they first
	 * entered the module.
	 *
	 * @param string Content id to redirect to.
	 */
	function RedirectContent($id)
	{
		#$content = ContentManager::LoadContentFromId($id);
 	        global $gCms;
		$manager =& $gCms->GetHierarchyManager();
		$node =& $manager->sureGetNodeById($id);
		$content =& $node->GetContent();
		if (isset($content))
		{
			if ($content->GetUrl() != '')
			{
				redirect($content->GetUrl());
			}
		}
	}

	/**
	 * ------------------------------------------------------------------
	 * Intermodule Functions
	 * ------------------------------------------------------------------
	 */

	function &GetModuleInstance($module)
	{
		global $gCms;

		if (isset($gCms->modules[$module]) &&
			$gCms->modules[$module]['installed'] == true &&
			$gCms->modules[$module]['active'] == true)
		{
			return $gCms->modules[$module]['object'];
		}

		return FALSE;
	}

	/**
	 * ------------------------------------------------------------------
	 * Language Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Sets the default language (usually en_US) for the module.  There
	 * should be at least a language file for this language if the Lang()
	 * function is used at all.
	 */
	function DefaultLanguage()
	{
		return 'en_US';
	}

	/**
	 * Returns the corresponding translated string for the id given.
	 *
	 * @param string Id of the string to lookup and return
	 * @param array Corresponding params for string that require replacement.
	 *        These params use the vsprintf command and it's style of replacement.
	 */
	function Lang($name, $params=array())
	{
		global $gCms;
		if ($this->curlang == '' && isset($gCms->current_language))
		{
			$this->curlang = $gCms->current_language;
		}
		$ourlang = $this->curlang;

		#Load the language if it's not loaded
		if ((is_array($this->langhash) && count(array_keys($this->langhash)) == 0) || !isset($this->langhash) || !is_array($this->langhash))
		{
			$dir = $gCms->config['root_path'];
			if (is_file("$dir/modules/".$this->GetName()."/lang/$ourlang/$ourlang.php"))
			{
				include("$dir/modules/".$this->GetName()."/lang/$ourlang/$ourlang.php");
				$this->langhash = &$lang;
			}
			else if (is_file("$dir/modules/".$this->GetName()."/lang/ext/$ourlang.php"))
			{
				include("$dir/modules/".$this->GetName()."/lang/ext/$ourlang.php");
				$this->langhash = &$lang;
			}
			else if (is_file("$dir/modules/".$this->GetName()."/lang/$ourlang.php"))
			{
				include("$dir/modules/".$this->GetName()."/lang/$ourlang.php");
				$this->langhash = &$lang;
			}
			else if (is_file("$dir/modules/".$this->GetName()."/lang/".$this->DefaultLanguage()."/".$this->DefaultLanguage().".php"))
			{
				include("$dir/modules/".$this->GetName()."/lang/".$this->DefaultLanguage()."/".$this->DefaultLanguage().".php");
				$this->langhash = &$lang;
			}
			else if (is_file("$dir/modules/".$this->GetName()."/lang/".$this->DefaultLanguage().".php"))
			{
				include("$dir/modules/".$this->GetName()."/lang/".$this->DefaultLanguage().".php");
				$this->langhash = &$lang;
			}
			else
			{
				# Sucks to be here...  Don't use Lang unless there are language files...
				# Get ready for a lot of Add Me's
			}
			# try to load an admin modifiable version of the lang file if one exists
			if( is_file("$dir/module_custom/".$this->GetName()."/lang/".$this->DefaultLanguage().".php") )
			{
 			        include("$dir/module_custom/".$this->GetName()."/lang/".$this->DefaultLanguage().".php");
				$this->langhash = &$lang;
			}
		}

		$result = '';

		if (isset($this->langhash[$name]))
		{
			if (count($params))
			{
				$result = vsprintf($this->langhash[$name], $params);
			}
			else
			{
				$result = $this->langhash[$name];
			}
		}
		else
		{
			$result = "--Add Me - module:".$this->GetName()." string:$name--";
		}

		if (isset($gCms->config['admin_encoding']) && isset($gCms->variables['convertclass']))
		{
			if (strtolower(get_encoding('', false)) != strtolower($gCms->config['admin_encoding']))
			{
				$class =& $gCms->variables['convertclass'];
				$result = $class->Convert($result, get_encoding('', false), $gCms->config['admin_encoding']);
			}
		}

		return $result;
	}

	/**
	 * ------------------------------------------------------------------
	 * Template/Smarty Functions
	 * ------------------------------------------------------------------
	 */

	function ListTemplates($modulename = '')
	{
		global $gCms;

		$db =& $gCms->GetDb();
		$config =& $gCms->GetConfig();

		$retresult = array();

		$query = 'SELECT * from '.cms_db_prefix().'module_templates WHERE module_name = ? ORDER BY module_name ASC';
		$result =& $db->Execute($query, array($modulename != ''?$modulename:$this->GetName()));

		while (isset($result) && !$result->EOF)
		{
			array_push($retresult, $result->fields['template_name']);
			$result->MoveNext();
		}

		return $retresult;
	}

	/**
	 * Returns a database saved template.  This should be used for admin functions only, as it doesn't
	 * follow any smarty caching rules.
	 */
	function GetTemplate($tpl_name, $modulename = '')
	{
		global $gCms;

		$db =& $gCms->GetDb();
		$config =& $gCms->GetConfig();

		$query = 'SELECT * from '.cms_db_prefix().'module_templates WHERE module_name = ? and template_name = ?';
		$result = $db->Execute($query, array($modulename != ''?$modulename:$this->GetName(), $tpl_name));

		if ($result && $result->RecordCount() > 0)
		{
			$row = $result->FetchRow();
			return $row['content'];
		}

		return '';
	}


	function SetTemplate($tpl_name, $content, $modulename = '')
	{
		global $gCms;
		$db =& $gCms->GetDB();

		$query = 'SELECT module_name FROM '.cms_db_prefix().'module_templates WHERE module_name = ? and template_name = ?';
		$result = $db->Execute($query, array($modulename != ''?$modulename:$this->GetName(), $tpl_name));

		if ($result && $result->RecordCount() < 1)
		{
			$query = 'INSERT INTO '.cms_db_prefix().'module_templates (module_name, template_name, content, create_date, modified_date) VALUES (?,?,?,?,?)';
			$db->Execute($query, array($modulename != ''?$modulename:$this->GetName(), $tpl_name, $content, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
		}
		else
		{
			$query = 'UPDATE '.cms_db_prefix().'module_templates SET content = ?, modified_date = ? WHERE module_name = ? AND template_name = ?';
			$db->Execute($query, array($content, $db->DBTimeStamp(time()), $modulename != ''?$modulename:$this->GetName(), $tpl_name));
		}
	}

	function DeleteTemplate($tpl_name, $modulename = '')
	{
		global $gCms;
		$db =& $gCms->GetDB();

		$query = "DELETE FROM ".cms_db_prefix()."module_templates WHERE module_name = ? and template_name = ?";
		$result = $db->Execute($query, array($modulename != ''?$modulename:$this->GetName(), $tpl_name));
	}

	function IsFileTemplateCached($tpl_name, $designation = '', $timestamp = '', $cacheid = '')
	{
		global $gCms;
		$smarty = &$this->smarty;
		$oldcache = $smarty->caching;
		$smarty->caching = 2;
		$result = $smarty->is_cached('module_file_tpl:'.$this->GetName().';'.$tpl_name, $cacheid, ($designation != ''?$designation:$this->GetName()));

		if ($result == true && $timestamp != '' && intval($smarty->_cache_info['timestamp']) < intval($timestamp))
		{
			$smarty->clear_cache('module_file_tpl:'.$this->GetName().';'.$tpl_name, $cacheid, ($designation != ''?$designation:$this->GetName()));
			$result = false;
		}

		$smarty->caching = $oldcache;
		return $result;
	}

	function ProcessTemplate($tpl_name, $designation = '', $cache = false, $cacheid = '')
	{
		$smarty = &$this->smarty;

		$oldcache = $smarty->caching;
		$smarty->caching = ($cache == true?2:false);

		$result = $smarty->fetch('module_file_tpl:'.$this->GetName().';'.$tpl_name, $cacheid, ($designation != ''?$designation:$this->GetName()));
		$smarty->caching = $oldcache;

		return $result;
	}

	function IsDatabaseTemplateCached($tpl_name, $designation = '', $timestamp = '')
	{
		global $gCms;
		$smarty = &$this->smarty;
		$oldcache = $smarty->caching;
		$smarty->caching = 2;
		$result = $smarty->is_cached('module_db_tpl:'.$this->GetName().';'.$tpl_name, '', ($designation != ''?$designation:$this->GetName()));

		if ($result == true && $timestamp != '' && intval($smarty->_cache_info['timestamp']) < intval($timestamp))
		{
			$smarty->clear_cache('module_file_tpl:'.$this->GetName().';'.$tpl_name, '', ($designation != ''?$designation:$this->GetName()));
			$result = false;
		}

		$smarty->caching = $oldcache;
		return $result;
	}

	function ProcessTemplateFromDatabase($tpl_name, $designation = '', $cache = false)
	{
		$smarty = &$this->smarty;

		$oldcache = $smarty->caching;
		$smarty->caching = ($cache == true?2:false);

		$result = $smarty->fetch('module_db_tpl:'.$this->GetName().';'.$tpl_name, '', ($designation != ''?$designation:$this->GetName()));

		$smarty->caching = $oldcache;

		return $result;
	}

	/**
	 * ------------------------------------------------------------------
	 * Tab Functions
	 * ------------------------------------------------------------------
	 */
	function StartTabHeaders()
	{
		return '<div id="page_tabs">';
	}

	function SetTabHeader($tabid,$title,$active=false)
	{
		$a="";
		if ($active) $a=" class='active'";
	  return '<div id="'.$tabid.'"'.$a.'>'.$title.'</div>';
	}

	function EndTabHeaders()
	{
		return "</div><!-- EndTabHeaders -->";
	}

	function StartTabContent()
	{
		return '<div class="clearb"></div><div id="page_content">';
	}

	function EndTabContent()
	{
		return '</div> <!-- EndTabContent -->';
	}

	function StartTab($tabid)
	{
		return '<div id="' . strtolower(str_replace(' ', '_', $tabid)) . '_c">';
	}

	function EndTab()
	{
		return '</div> <!-- EndTab -->';
	}

	/**
	 * ------------------------------------------------------------------
	 * Other Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Set the content-type header.
	 *
	 * @param string Value to set the content-type header too
	 */
	function SetContentType($contenttype)
	{
		$variables = &$this->cms->variables;
		$variables['content-type'] = $contenttype;
	}

	/**
	 * Put an event into the audit (admin) log.  This should be
	 * done on most admin events for consistency.
	 */
	function Audit($itemid, $itemname, $action)
	{
		#$userid = get_userid();
		#$username = $_SESSION["cms_admin_username"];
        audit($itemid,$itemname,$action);
    }

	/**
	 * Create's a new permission for use by the module.
	 *
	 * @param string Name of the permission to create
	 * @param string Description of the permission
	 */
	function CreatePermission($permission_name, $permission_text)
	{
		global $gCms;
		$db =& $gCms->GetDB();

		$query = "SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name = ?";
		$count = $db->GetOne($query, array($permission_name));

		if (intval($count) == 0)
		{
			$new_id = $db->GenID(cms_db_prefix()."permissions_seq");
			$query = "INSERT INTO ".cms_db_prefix()."permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (?,?,?,?,?)";
			$db->Execute($query, array($new_id, $permission_name, $permission_text, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
		}
	}

	/**
	 * Checks a permission against the currently logged in user.
	 *
	 * @param string The name of the permission to check against the current user
	 */
	function CheckPermission($permission_name)
	{
		$userid = get_userid();
		return check_permission($userid, $permission_name);
	}

	/**
	 * Removes a permission from the system.  If recreated, the
	 * permission would have to be set to all groups again.
	 *
	 * @param string The name of the permission to remove
	 */
	function RemovePermission($permission_name)
	{
		global $gCms;
		$db =& $gCms->GetDB();

		$query = "SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name = ?";
		$row = &$db->GetRow($query, array($permission_name));

		if ($row)
		{
			$id = $row["permission_id"];

			$query = "DELETE FROM ".cms_db_prefix()."group_perms WHERE permission_id = ?";
			$db->Execute($query, array($id));

			$query = "DELETE FROM ".cms_db_prefix()."permissions WHERE permission_id = ?";
			$db->Execute($query, array($id));
		}
	}

	/**
	 * Returns a module preference if it exists.
	 *
	 * @param string The name of the preference to check
	 * @param string The default value, just in case it doesn't exist
	 */
	function GetPreference($preference_name, $defaultvalue='')
	{
		return get_site_preference($this->GetName() . "_mapi_pref_" . $preference_name, $defaultvalue);
	}

	/**
	 * Sets a module preference.
	 *
	 * @param string The name of the preference to set
	 * @param string The value to set it to
	 */
	function SetPreference($preference_name, $value)
	{
		return set_site_preference($this->GetName() . "_mapi_pref_" . $preference_name, $value);
	}

	/**
	 * Removes a module preference.
	 * @param string The name of the preference to remove
	 */
	function RemovePreference($preference_name)
	{
		return remove_site_preference($this->GetName() . "_mapi_pref_" . $preference_name);
	}

	/**
	 * Creates a string containing links to all the pages.
	 * @param string The id given to the module on execution
	 * @param string The action that this form should do when the form is submitted
	 * @param string The id to eventually return to when the module is finished it's task
	 * @param string the current page to display
	 * @param string the amount of items being listed
	 * @param string the amount of items to list per page
	 * @param boolean A flag to determine if actions should be handled inline (no moduleinterface.php -- only works for frontend)
	 */
	function CreatePagination($id, $action, $returnid, $page, $totalrows, $limit, $inline=false)
	{
		$goto = 'index.php';
		if ($returnid == '')
		{
			$goto = 'moduleinterface.php';
		}
		$link = '<a href="'.$goto.'?module='.$this->GetName().'&amp;'.$id.'returnid='.$id.$returnid.'&amp;'.$id.'action=' . $action .'&amp;'.$id.'page=';
		if ($inline)
		{
			$link .= '&amp;'.$this->cms->config['query_var'].'='.$returnid;
		}
		$page_string = "";
		$from = ($page * $limit) - $limit;
		$numofpages = floor($totalrows / $limit);
		if ($numofpages * $limit < $totalrows)
		{
			$numofpages++;
		}

		if ($numofpages > 1)
		{
			if($page != 1)
			{
				$pageprev = $page-1;
				$page_string .= $link.$pageprev."\">".lang('previous')."</a>&nbsp;";
			}
			else
			{
				$page_string .= lang('previous')." ";
			}

			for($i = 1; $i <= $numofpages; $i++)
			{
				if($i == $page)
				{
					 $page_string .= $i."&nbsp;";
				}
				else
				{
					 $page_string .= $link.$i."\">$i</a>&nbsp;";
				}
			}

			if (($totalrows - ($limit * $page)) > 0)
			{
				$pagenext = $page+1;
				$page_string .= $link.$pagenext."\">".lang('next')."</a>";
			}
			else
			{
				$page_string .= lang('next')." ";
			}
		}

		return $page_string;
	}
}

/**
 * Class that module defined content types must extend.
 *
 * @since		0.9
 * @package		CMS
 */
class CMSModuleContentType extends ContentBase
{
	//What module do I belong to?  (needed for things like Lang to work right)
	function ModuleName()
	{
		return '';
	}

	function Lang($name, $params=array())
	{
		global $gCms;
		$cmsmodules = &$gCms->modules;
		if (array_key_exists($this->ModuleName(), $cmsmodules))
		{
			return $cmsmodules[$this->ModuleName()]['object']->Lang($name, $params);
		}
		else
		{
			return 'ModuleName() not defined properly';
		}
	}
}

# vim:ts=4 sw=4 noet
?>
