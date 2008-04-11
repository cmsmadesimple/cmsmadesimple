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
 
define( "MODULE_DTD_VERSION", "1.3" );

/**
 * Static module functions for internal use and module development.
 *
 * @author Ted Kulp
 * @since 0.9
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsModuleOperations extends CmsObject
{
	/**
	* A member to hold an error string
	*/
	var $error;

	/**
	 * A member to hold a list of core module names
	 */

	/**
	* A member to hold the id of the active tab
	*/
	var $mActiveTab = '';
	
	static private $instance = NULL;
	
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get an instance of this object, though most people should be using
	 * the static methods instead.  This is more for compatibility than
	 * anything else.
	 *
	 * @return CmsModuleOperations The instance of the singleton object.
	 * @author Ted Kulp
	 **/
	static public function get_instance()
	{
		if (self::$instance == NULL)
		{
			self::$instance = new CmsModuleOperations();
		}
		return self::$instance;
	}
	
	
	/**
	 * Returns whether or not a module is installed
	 *
	 * @param string Name of the module
	 * @return boolean Whether or not the module is installed
	 * @author Ted Kulp
	 **/
	public static function is_installed($module_name)
	{
		$modules = cmsms()->modules;
		return isset($modules[$module_name]) && $modules[$module_name]['installed'];
	}
	
	/**
	 * Returns whether or not a module is active and installed
	 *
	 * @param string Name of the module
	 * @return boolean Whether or not the module is active and installed
	 * @author Ted Kulp
	 **/
	public static function is_active($module_name)
	{
		$modules = cmsms()->modules;
		return isset($modules[$module_name]) && $modules[$module_name]['installed'] && $modules[$module_name]['active'];
	}
	
	/**
	 * Returns wether or not a module is a 'core' module or not
	 *
	 * @param string Name of the module
	 */
	public static function is_core($module_name)
	{
		return in_array($module_name,cmsms()->coremodules);
	}

	/**
	 * Returns the module object if it is installed and active.
	 *
	 * @param string Name of the module
	 * @return mixed The module object
	 * @author Ted Kulp
	 **/
	public static function get_module($module_name)
	{
		$modules = cmsms()->modules;
		if (self::is_active($module_name))
			return $modules[$module_name]['object'];
		return null;
	}
	
	public static function get_file_version($module_name)
	{
		$module = self::get_module($module_name);
		if ($module != null)
		{
			return $module->get_version();
		}

		return null;
	}
	
	public static function get_installed_version($module_name)
	{
		$cms_db_prefix = CMS_DB_PREFIX;
		return cms_db()->GetOne("SELECT version FROM {$cms_db_prefix}modules WHERE module_name = ?", array($module_name));
	}
	
	public static function get_installed_dependency_names($module_name)
	{
		$cms_db_prefix = CMS_DB_PREFIX;
		$result = array();

		$dbresult = cms_db()->Execute("SELECT parent_module FROM {$cms_db_prefix}module_deps WHERE child_module = ?", array($module_name));
		while ($dbresult && !$dbresult->EOF)
		{
			$result[] = $dbresult->fields['parent_module'];
			$dbresult->MoveNext();
		}
		
		return $result;
	}

	/**
	* ------------------------------------------------------------------
	* Error Functions
	* ------------------------------------------------------------------
	*/

	/**
	 * Sets an error condition for the system.  This is usually used for 
	 * module operations.
	 *
	 * @param string Error message to set
	 * @return void
	 * @author Ted Kulp
	 **/
	function set_error($str = '')
	{
		cmsms()->variables['error'] = $str;
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsModuleOperations::get_error instead.
	 **/
	function SetError($str = '')
	{
		self::set_error($str);
	}

	/**
	 * Returns the error message set by CmsModuleOperations::set_error().
	 *
	 * @return string The error message
	 * @author Ted Kulp
	 **/
	function get_last_error()
	{
		if(isset(cmsms()->variables['error']))
			return cmsms()->variables['error'];
		return "";
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsModuleOperations::get_last_error instead.
	 **/
	function GetLastError()
	{
		return self::get_last_error();
	}

	/**
	* Creates an xml data package from the module directory.
	*/
	function CreateXMLPackage( &$modinstance, &$message, &$filecount )
	{
		// get a file list
		$filecount = 0;
		$dir = cms_join_path(ROOT_DIR, 'modules', $modinstance->GetName());
		$files = get_recursive_file_list( $dir, $modinstance->xml_exclude_files );

		$xmltxt  = '<?xml version="1.0" encoding="ISO-8859-1"?>';
		$xmltxt .= $modinstance->xmldtd."\n";
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
		$dir = cms_join_path(ROOT_DIR, 'modules');

		if( !is_writable( $dir ) && $brief != 0 )
		{
			// directory not writable
			self::SetError( lang( 'errordirectorynotwritable' ) );
			return false;
		}

		// start parsing xml
		$parser = xml_parser_create();
		$ret = xml_parse_into_struct( $parser, $xml, $val, $xt );
		xml_parser_free( $parser );

		if( $ret == 0 )
		{
			self::SetError( lang( 'errorcouldnotparsexml' ) );
			return false;
		}

		self::SetError( "" );
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
						self::SetError( lang( 'moduleinstalled' ) );
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
						self::SetError( lang( 'errordtdmismatch' ) );
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
						if( $moduledetails['version'] < $version && $brief == 0)
						{
							self::SetError( lang('errorattempteddowngrade') );
							return false;
						}
						else if ($moduledetails['version'] == $version && $brief == 0 )
						{
							self::SetError( lang('moduleinstalled') );
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
						self::SetError( lang('errorincompletexml') );
						return false;
					}

					// ready to go
					$moduledir=$dir.DIRECTORY_SEPARATOR.$moduledetails['name'];
					$filename=$moduledir.$moduledetails['filename'];
					if( !file_exists( $moduledir ) )
					{
						if( !@mkdir( $moduledir ) && !is_dir( $moduledir ) )
						{
							self::SetError(lang('errorcantcreatefile').' '.$moduledir);
							break;
						}
					}
					if( $moduledetails['isdir'] )
					{
						if( !@mkdir( $filename ) && !is_dir( $filename ) )
						{
							self::SetError(lang('errorcantcreatefile').' '.$filename);
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
							self::SetError(lang('errorcantcreatefile').' '.$filename);
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
			self::SetError( lang( 'errordtdmismatch' ) );
		}

		// we've created the module's directory
		unset( $moduledetails['filedata'] );
		unset( $moduledetails['filename'] );
		unset( $moduledetails['isdir'] );

		if( self::GetLastError() != "" )
		{
			return false;
		}
		return $moduledetails;
	}


	/**
	* Install a module into the database
	*/
	function install_module($module, $load_if_necessary = false)
	{
		$gCms = cmsms();

		if (!isset( $gCms->modules[$module]))
		{
			if ($load_if_necessary == false)
			{
				self::set_error(lang('errormodulenotloaded'));
				return false;
			}
			else
			{
				if( !self::LoadNewModule( $module ) )
				{
					self::set_error(lang('errormodulewontload'));
					return false;
				}
			}
		}

		$db = cms_db();
		if (isset($gCms->modules[$module]))
		{
			$modinstance =& $gCms->modules[$module]['object'];
			$result = $modinstance->install();

			#now insert a record
			if (!isset($result) || $result === FALSE)
			{
				$query = "INSERT INTO ".cms_db_prefix()."modules (module_name, version, status, admin_only, active) VALUES (?,?,'installed',?,?)";
				$db->Execute($query, array($module,$modinstance->get_version(),($modinstance->is_admin_only()==true?1:0),1));

				#and insert any dependancies
				if (count($modinstance->get_dependencies()) > 0) #Check for any deps
				{
					#Now check to see if we can satisfy any deps
					foreach ($modinstance->get_dependencies() as $onedepkey=>$onedepvalue)
					{
						$time = $db->DBTimeStamp(time());
						$query = "INSERT INTO ".cms_db_prefix()."module_deps (parent_module, child_module, minimum_version, create_date, modified_date) VALUES (?,?,?,".$time.",".$time.")";
						$db->Execute($query, array($onedepkey, $module, $onedepvalue));
					}
				}

				#send an event saying the module has been installed
				CmsEvents::send_event('Core', 'ModuleInstalled', array('name' => $module, 'version' => $modinstance->get_version(), 'module' => &$modinstance));

				// and we're done
				return true;
			}
			else
			{
				if( trim($result) == "" )
				{
					$result = lang('errorinstallfailed');
				}

				self::set_error($result);
				return false;
			}
		}    
		else
		{
			self::set_error(lang('errormodulenotfound'));
			return false;
		}
	}
	
	/**
	 * @deprecated Deprecated.  Use CmsModuleOperations::install_module instead.
	 **/
	function InstallModule($module, $loadifnecessary = false)
	{
		return self::install_module($module, $loadifnecessary);
	}


	/**
	 * Load a module into the system.  It will be marked as not installed
	 * or active.
	 *
	 * @param string The name of the module to load
	 * @return boolean Whether or not the load was successful
	 * @author Ted Kulp
	 **/
	function load_new_module($module_name)
	{
		global $gCms;
		$db = cms_db();
		$cmsmodules = &$gCms->modules;

		$dir = cms_join_path(ROOT_DIR, 'modules');

		if (@is_file("$dir/$module_name/$module_name.module.php"))
		{
			// question: is this a potential XSS vulnerability
			include("$dir/$module_name/$module_name.module.php");
			if(!class_exists($module_name))
			{
				return false;
			}

			$newmodule = new $module_name;
			$name = $newmodule->get_name();
			$cmsmodules[$name]['object'] =& $newmodule;
			$cmsmodules[$name]['installed'] = false;
			$cmsmodules[$name]['active'] = false;
		}
		else
		{
			unset($cmsmodules[$module_name]);
			return false;
		}

		return true;
	}
	
	/**
	 * @deprecated Deprecated. Use CmsModuleOperations::load_new_module instead.
	 **/
	function LoadNewModule($modulename)
	{
		return self::load_new_module($modulename);
	}

	/**
	 * Updates a module from old_version to new_version.
	 *
	 * @param string The name of the module to upgrade
	 * @param string The version number of the old version
	 * @param string The version number of the new version
	 * @return boolean Whether or not the upgrade was successful
	 * @author Ted Kulp
	 **/
	function upgrade_module($module_name)
	{		
		$modinstance = self::get_module($module_name);
		if ($modinstance != null)
		{
			return false;
		}
		
		$old_version = self::get_installed_version($module_name);
		$new_version = self::get_file_version($module_name);

		$result = $modinstance->upgrade($old_version, $new_version);
		if( $result === FALSE )
		{
			return false;
		}

		$query = "UPDATE " . CMS_DB_PREFIX . "modules SET version = ?, admin_only = ? WHERE module_name = ?";
		cms_db()->Execute($query, array($new_version, ($modinstance->is_admin_only()==true?1:0), $module_name));

		CmsEventOperations::send_event('Core', 'ModuleUpgraded', array('name' => $module_name, 'oldversion' => $old_version, 'newversion' => $new_version));

		return true;
	}
	
	/**
	 * @deprecated Deprecated. Use CmsModuleOperations::upgrade_module instead.
	 *
	 * @return void
	 * @author Ted Kulp
	 **/
	function UpgradeModule( $module, $oldversion, $newversion )
	{
		return self::upgrade_module($module);
	}
	
	function uninstall_module($module_name)
	{
		$modinstance = self::get_module($module_name);
		if ($modinstance == null)
		{
			return false;
		}
		
		$result = $modinstance->uninstall();
		
		if ($result === FALSE)
		{
			return false;
		}

		#now delete the record
		$query = "DELETE FROM ".CMS_DB_PREFIX."modules WHERE module_name = ?";
		cms_db()->Execute($query, array($module_name));

		#delete any dependencies
		$query = "DELETE FROM ".CMS_DB_PREFIX."module_deps WHERE child_module = ?";
		cms_db()->Execute($query, array($module_name));

		Events::SendEvent('Core', 'ModuleUninstalled', array('name' => $module_name));
		
		return true;
	}
	
	public static function activate_module($module_name)
	{
		$query = "UPDATE ".CMS_DB_PREFIX."modules SET active = 1 WHERE module_name = ?";
		cms_db()->Execute($query, array($module_name));
	}
	
	public static function deactivate_module($module_name)
	{
		$query = "UPDATE ".CMS_DB_PREFIX."modules SET active = 0 WHERE module_name = ?";
		cms_db()->Execute($query, array($module_name));
	}

	/**
	* Returns a hash of all loaded modules.  This will include all
	* modules loaded by load_modules, which could either be all or them,
	* or just ones that are active and installed.
	*
	* @return array Hash of all the loaded modules
	* @author Ted Kulp
	*/
	function &get_all_modules()
	{
		$gCms = cmsms();
		$cms_modules = &$gCms->modules;
		return $cms_modules;
	}
	
	/**
	 * @deprecated Deprecated.  Please use CmsModuleOperations::get_all_modules instead.
	 **/
	function &GetAllModules()
	{
		$gCms = cmsms();
		$cmsmodules = &$gCms->modules;
		return $cmsmodules;
	}

}
?>