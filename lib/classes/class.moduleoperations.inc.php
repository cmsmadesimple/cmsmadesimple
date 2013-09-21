<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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

/**
 * @package CMS 
 */

/**
 * @ignore
 */
define( "MODULE_DTD_VERSION", "1.3" );

/**
 * A singleton utility class to allow for working with modules.
 *
 * @since		0.9
 * @package		CMS
 */
final class ModuleOperations
{
	/**
	 * System Modules - a list (hardcoded) of all system modules
	 *
	 * @access private
	 * @internal
	 * @ignore
	 */
	protected $cmssystemmodules =  array( 'FileManager','MenuManager','ModuleManager','Search','CMSMailer','News','MicroTiny','CMSPrinting','ThemeManager' );

	static private $_instance = null;
	private $_modules = null;
	private $_moduleinfo;
	private $_moduledeps;
	private $_errors = null;
	
	private $xml_exclude_files = array('^\.svn' , '^CVS$' , '^\#.*\#$' , '~$', '\.bak$', '^\.git');
	private $xmldtd = '
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
  private function __construct() {}


  /**
   * Get the only permitted instance of this object.  It will be created if necessary
   *
   * @return object
   */
  public static function &get_instance()
  {
	  if( !isset(self::$_instance) ) {
		  $c = __CLASS__;
		  self::$_instance = new $c;
	  }
	  return self::$_instance;
  }


  /**
   * Set an error condition
   *
   * @ignore
   * @deprecated
   * @param string $str The string to set for the error
   * @return void
   */
  protected function SetError($str = '')
  {
	  $this->_errors = $str;
  }


  /**
   * Return the last error
   *
   * @ignore
   * @deprecated
   * @return string The last error, if any
   */
  public function GetLastError()
  {
	  return $this->_errors;
  }


  /**
   * Creates an xml data package from the module directory.
   *
   * @param mixed $modinstance The instance of the module object
   * @param string $message Reference to a string which will be filled with the message 
   *                        created by the run of the method
   * @param integer $filecount Reference to an interger which will be filled with the 
   *                           total # of files in the package
   * @return string an XML string comprising the module and its files
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
	  $xmltxt .= "	<help><![CDATA[".base64_encode($modinstance->GetHelpPage())."]]></help>\n";
	  $xmltxt .= "	<about><![CDATA[".base64_encode($modinstance->GetAbout())."]]></about>\n";
	  $desc = $modinstance->GetAdminDescription();
	  if( $desc != '' )
		  {
			  $xmltxt .= "	<description><![CDATA[".$desc."]]></description>\n";
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
 *
 * @param string $xml The xml data for the package
 * @param boolean $overwrite Should we overwrite files if they exist?
 * @param boolean $brief If set to true, less checking is done and no errors are returned
 * @return array A hash of details about the installed module
 */
  function ExpandXMLPackage( $xmluri, $overwrite = 0, $brief = 0 )
  {
	$gCms = cmsms();
	$this->SetError('');

	// first make sure that we can actually write to the module directory
	$dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules";

	if( !is_writable( $dir ) && $brief == 0 ) {
		// directory not writable
		$this->SetError( lang( 'errordirectorynotwritable' ) );
		return false;
	}

	$reader = new XMLReader();
	$ret = $reader->open($xmluri);
	if( $ret == 0 ) {
		$this->SetError( lang( 'errorcouldnotparsexml' ) );
		return false;
	}

	$this->SetError('');
	$havedtdversion = false;
	$moduledetails = array();
	if( is_file($xmluri) ) $moduledetails['size'] = filesize($xmluri);
	$required = array();
	while( $reader->read() ) {
		switch($reader->nodeType) {
		case XMLREADER::ELEMENT:
			switch( strtoupper($reader->localName) ) {
			case 'NAME':
				$reader->read();
				$moduledetails['name'] = $reader->value;
				// check if this module is already installed
				if( isset( $this->_modules[$moduledetails['name']] ) && $overwrite == 0 && $brief == 0 ) {
					$this->SetError( lang( 'moduleinstalled' ) );
					return TRUE;
				}
				break;

			case 'DTDVERSION':
				$reader->read();
				if( $reader->value != MODULE_DTD_VERSION ) {
					$this->SetError( lang( 'errordtdmismatch' ) );
					return false;
				}
				$havedtdversion = true;
				break;

			case 'VERSION':
				$reader->read();
				$moduledetails['version'] = $reader->value;
				$tmpinst = $this->get_module_instance($moduledetails['name']);
				if( $tmpinst && $brief == 0 ) {
					$version = $tmpinst->GetVersion();
					if( version_compare($moduledetails['version'],$version) < 0 ) {
						$this->SetError( lang('errorattempteddowngrade') );
						return false;
					}
					else if (version_compare($moduledetails['version'],$version) == 0 ) {
						$this->SetError( lang('moduleinstalled') );
						return TRUE;
					}
				}
				break;
		
			case 'MINCMSVERSION':
			case 'MAXCMSVERSION':
			case 'DESCRIPTION':
			case 'FILENAME':
			case 'ISDIR':
				$name = $reader->localName;
				$reader->read();
				$moduledetails[$name] = $reader->value;
				break;

			case 'HELP':
			case 'ABOUT':
				$name = $reader->localName;
				$reader->read();
				$moduledetails[$name] = base64_decode($reader->value);
				break;

			case 'REQUIREDNAME':
				$reader->read();
				$requires['name'] = $reader->value;
				break;

			case 'REQUIREDVERSION':
				$reader->read();
				$requires['version'] = $reader->value;
				break;

			case 'DATA':
				$reader->read();
				$moduledetails['filedata'] = $reader->value;
				break;
			}
			break;

		case XMLReader::END_ELEMENT:
			switch( strtoupper($reader->localName) ) {
			case 'REQUIRES':
				if( count($requires) != 2 ) continue;
				if( !isset( $moduledetails['requires'] ) ) $moduledetails['requires'] = array();
				$moduledetails['requires'][] = $requires;
				$requires = array();
				break;

			case 'FILE':
				if( $brief != 0 ) continue;

				// finished a first file
				if( !isset( $moduledetails['name'] )	   || !isset( $moduledetails['version'] ) ||
					!isset( $moduledetails['filename'] ) || !isset( $moduledetails['isdir'] ) ) {
					$this->SetError( lang('errorincompletexml') );
					return false;
				}

				// ready to go
				$moduledir=$dir.DIRECTORY_SEPARATOR.$moduledetails['name'];
				$filename=$moduledir.$moduledetails['filename'];
				if( !file_exists( $moduledir ) ) {
					if( !@mkdir( $moduledir ) && !is_dir( $moduledir ) ) {
						$this->SetError(lang('errorcantcreatefile').' '.$moduledir);
						break;
					}
				}
				else if( $moduledetails['isdir'] ) {
					if( !@mkdir( $filename ) && !is_dir( $filename ) ) {
						$this->SetError(lang('errorcantcreatefile').' '.$filename);
						break;
					}
				}
				else {
					$data = $moduledetails['filedata'];
					if( strlen( $data ) ) $data = base64_decode( $data );
					$fp = @fopen( $filename, "w" );
					if( !$fp ) $this->SetError(lang('errorcantcreatefile').' '.$filename);
					if( strlen( $data ) ) @fwrite( $fp, $data );
					@fclose( $fp );
				}
				unset( $moduledetails['filedata'] );
				unset( $moduledetails['filename'] );
				unset( $moduledetails['isdir'] );
				break;
			}
			break;
		}
	} // while

	$reader->close();
	if( $havedtdversion == false ) {
		$this->SetError( lang( 'errordtdmismatch' ) );
		return false;
	}

	// we've created the module's directory
	unset( $moduledetails['filedata'] );
	unset( $moduledetails['filename'] );
	unset( $moduledetails['isdir'] );

	if( $this->GetLastError() != "" ) return false;
	if( !$brief ) audit('','Module', 'Expanded module: '.$moduledetails['name'].' version '.$moduledetails['version']);

	return $moduledetails;
  }


 private function _install_module(CmsModule& $module_obj)
 {
	 debug_buffer('install_module '.$module_obj->GetName());

	 $gCms = cmsms(); // preserve the global.
	 $db = $gCms->GetDb();

	 // todo, check to make sure the module isn't already installed.

	 $result = $module_obj->Install();
	 if( !isset($result) || $result === FALSE) {
		 // install returned nothing, or FALSE
		 $query = 'DELETE FROM '.cms_db_prefix().'modules WHERE module_name = ?';
		 $dbr = $db->Execute($query,array($module_obj->GetName()));

		 $lazyload_fe    = (method_exists($module_obj,'LazyLoadFrontend') && $module_obj->LazyLoadFrontend())?1:0;
		 $lazyload_admin = (method_exists($module_obj,'LazyLoadAdmin') && $module_obj->LazyLoadAdmin())?1:0;
		 $query = 'INSERT INTO '.cms_db_prefix().'modules 
                   (module_name,version,status,admin_only,active,allow_fe_lazyload,allow_admin_lazyload)
                   VALUES (?,?,?,?,?,?,?)';
		 $dbr = $db->Execute($query,array($module_obj->GetName(),$module_obj->GetVersion(),'installed',
										  ($module_obj->IsAdminOnly()==true)?1:0,
										  1,$lazyload_fe,$lazyload_admin));

		 $deps = $module_obj->GetDependencies();
		 if( is_array($deps) && count($deps) ) {
			 $query = 'INSERT INTO '.cms_db_prefix().'module_deps
                       (parent_module,child_module,minimum_version,create_date,modified_date)
                       VALUES (?,?,?,NOW(),NOW())';
			 foreach( $deps as $depname => $depversion ) {
				 if( !$depname || !$depversion ) continue;
				 $dbr = $db->Execute($query,array($depname,$module_obj->GetName(),$depversion));
			 }
		 }

		 $this->_moduleinfo[$module_obj->GetName()] = array('module_name'=>$module_obj->GetName(),
															'version'=>$module_obj->GetVersion(),
															'status'=>'installed',
															'active'=>1,
															'admn_only'=>($module_obj->IsAdminOnly()==true)?1:0,
															'allow_fe_lazyload'=>($module_obj->LazyLoadFrontend()==TRUE)?1:0,
															'allow_admin_lazyload'=>($module_obj->LazyLoadAdmin()==TRUE)?1:0);

		 Events::SendEvent('Core', 'ModuleInstalled', array('name' => $module_obj->GetName(), 'version' => $module_obj->GetVersion()));
		 audit('',$module_obj->GetName(), 'Installed version '.$module_obj->GetVersion());
		 $gCms->clear_cached_files();

		 return array(TRUE,$module_obj->InstallPostMessage());
	 }

	 return array(FALSE,$result);
 }


  /**
   * Install a module into the database
   *
   * @internal
   * @param string $module The name of the module to install
   * @param boolean $loadifnecessary If true, loads the module before trying to install it
   * @return array Returns a tuple of whether the install was successful and a message if applicable
   */
  public function InstallModule($module, $loadifnecessary = false)
  {
	  // get an instance of the object (force it).
	  $modinstance = $this->get_module_instance($module,'',TRUE);
	  if( !$modinstance ) return array(false,lang('errormodulenotloaded'));

	  // check for dependencies
	  $deps = $modinstance->GetDependencies();
	  if( is_array($deps) && count($deps) ) {
		  foreach( $deps as $mname => $mversion ) {
			  if( $mname == '' || $mversion == '' ) continue; // invalid entry.
			  $newmod = $this->get_module_instance($mname);
			  if( !is_object($newmod) || version_compare($newmod->GetVersion(),$mversion) < 0 ) {
				  return array(false,lang('missingdependency').': '.$mname);
			  }
		  }
	  }

	  // do the actual installation stuff.
	  $res = $this->_install_module($modinstance);
	  if( $res[0] == FALSE && $res[1] == '') $res[1] = lang('errorinstallfailed');
	  return $res;
  }


  private function &_get_module_info()
  {
	  if( !is_array($this->_moduleinfo) || count($this->_moduleinfo) == 0 ) {
		  $query = 'SELECT * FROM '.cms_db_prefix().'modules ORDER BY module_name';
		  $db = cmsms()->GetDb();
		  $tmp = $db->GetArray($query);
		  if( is_array($tmp) ) {
			  $config = cmsms()->GetConfig();
			  $dir = $config['root_path'].'/modules';
			  $this->_moduleinfo = array();
			  for( $i = 0; $i < count($tmp); $i++ ) {
				  $name = $tmp[$i]['module_name'];
				  if( is_file($dir."/$name/$name.module.php") ) {
					  if( !isset($this->_moduleinfo[$name]) ) $this->_moduleinfo[$name] = $tmp[$i];
				  }
			  }
		  }
	  }

	  return $this->_moduleinfo;
  }



  private function _load_module($module_name,$force_load = FALSE,$dependents = TRUE)
  {
	  $config = cmsms()->GetConfig();
	  $dir = $config['root_path'].'/modules';

	  $info = $this->_get_module_info();
	  if( !isset($info[$module_name]) && !$force_load ) {
		  debug_buffer("Nothing is known about $module_name... cant load it");
		  return FALSE;
	  }
	  if( (!isset($info[$module_name]['active']) || $info[$module_name]['active'] == 0) && !$force_load ) {
		  debug_buffer('Requested deactivated module '.$module_name);
		  return FALSE;
	  }

	  global $CMS_INSTALL_PAGE;
	  global $CMS_VERSION;
	  global $CMS_PREVENT_AUTOINSTALL;
	  global $CMS_FORCE_MODULE_LOAD;
	  $allow_auto = (isset($CMS_PREVENT_AUTOINSTALL) && $CMS_PREVENT_AUTOINSTALL)?0:1;

	  $gCms = cmsms(); // backwards compatibility... set the global.
	  if( !class_exists($module_name) ) {
		  $fname = $dir."/$module_name/$module_name.module.php";
		  if( !is_file($fname) ) {
			  debug_buffer("Cannot load $module_name because the module file does not exist");
			  return FALSE;
		  }

		  debug_buffer('loading module '.$module_name);
		  require_once($fname); 
	  }

	  // load dependencies.
	  if( !isset($config['modules_noloaddependants']) && $dependents == TRUE ) {
		  $deps = $this->get_module_dependencies($module_name);
		  if( is_array($deps) && count($deps) ) {
			  $res = true;
			  foreach( $deps as $name => $ver ) {
				  // this is the start of a recursive routine.
				  // get_module_instance may call _load_module.
				  $obj2 = $this->get_module_instance($name,$ver);
				  if( !is_object($obj2) ) {
					  $res = false;
					  break;
				  }
			  }
			  if( !$res && !isset($CMS_FORCE_MODULE_LOAD)) {
				  audit('','Core',"Cannot load module $module_name ... Problem loading dependent module $name version $ver");
				  debug_buffer("Cannot load $module_name... cannot load it's dependants.");
				  unset($obj);
				  return FALSE;
			  }
		  }
	  }

	  $obj = new $module_name;
	  if( !is_object($obj) ) {
		  // oops, some problem loading.
		  audit('','Core',"Cannot load module $module_name ... some problem instantiating the class");
		  debug_buffer("Cannot load $module_name ... some problem instantiating the class");
		  return FALSE;
	  }

	  if (version_compare($obj->MinimumCMSVersion(),$CMS_VERSION) == 1 ) {
		  // oops, not compatible.... can't load.
		  audit('','Core','Cannot load module '.$module_name.' it is not compatible wth this version of CMSMS');
		  debug_buffer("Cannot load $module_name... It is not compatible with this version of CMSMS");
		  unset($obj);
		  return FALSE;
	  }

	  if( isset($info[$module_name]) && $info[$module_name]['status'] != 'installed' && 
		  (isset($CMS_INSTALL_PAGE) || $this->_is_queued_for_install($module_name)) ) {
		  // not installed, can we auto-install it?
		  if( (in_array($module_name,$this->cmssystemmodules) || $obj->AllowAutoInstall() == true ||
			   $this->_is_queued_for_install($module_name)) && $allow_auto ) {
			  $res = $this->_install_module($obj);
			  if( !isset($_SESSION['moduleoperations_result']) )  $_SESSION['moduleoperations_result'] = array();
			  $_SESSION['moduleoperations_result'][$module_name] = $res;
		  }
		  else if( !isset($CMS_FORCE_MODULE_LOAD) ) {
			  // nope, can't auto install...
			  unset($obj);
			  return FALSE;
		  }
	  }

	  // check to see if an upgrade is needed.
	  allow_admin_lang(TRUE); // isn't this ugly.
	  if( isset($info[$module_name]) && $info[$module_name]['status'] == 'installed' ) {
		  $dbversion = $info[$module_name]['version'];
		  if( version_compare($dbversion, $obj->GetVersion()) == -1 ) {
			  // upgrade is needed
			  if( ($obj->AllowAutoUpgrade() == TRUE || $this->_is_queued_for_install($module_name)) && $allow_auto ) {
				  // we're allowed to upgrade
				  $res = $this->_upgrade_module($obj);
				  if( !isset($_SESSION['moduleoperations_result']) ) $_SESSION['moduleoperations_result'] = array();
				  if( $res ) {
					  $res2 = array(TRUE,lang('moduleupgraded'));
					  $_SESSION['moduleoperations_result'][$module_name] = $res2;
					  return TRUE;
				  }
				  else {
					  $res2 = array(FALSE,lang('moduleupgradeerror'));
					  $_SESSION['moduleoperations_result'][$module_name] = $res2;
					  return FALSE;
				  }
				  if( !$res ) {
					  // upgrade failed
					  allow_admin_lang(FALSE); // isn't this ugly.
					  debug_buffer("Automatic upgrade of $module_name failed");
					  unset($obj);
					  return FALSE;
				  }
			  }
			  else if( !isset($CMS_FORCE_MODULE_LOAD) ) {
				  // nope, can't auto upgrade either
				  allow_admin_lang(FALSE); // isn't this ugly.
				  unset($obj);
				  return FALSE;
			  }
		  }
	  }

	  if( (isset($info[$module_name]) && $info[$module_name]['status'] == 'installed') || 
		  $force_load ) {
		  if( is_object($obj) ) $this->_modules[$module_name] = $obj;
		  return TRUE;
	  }

	  return FALSE;
  }


  /**
   * A function to return a list of all modules that appear to exist properly in the modules directory.
   *
   * @internal
   * @return array of complete specifications to module files
   */
  public function FindAllModules()
  {
	$dir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules";
	
	$result = array();
	if( $handle = @opendir($dir) ) {
		while( ($file = readdir($handle)) !== false ) {
			$fn = "$dir/$file/$file.module.php";
			if( @is_file($fn) )	$result[] = $file;
		}
	}
	
	sort($result);
	return $result;
  }


  /**
   * Finds all modules in the filesystem, and builds a database about them
   * 
   * @since 1.10
   * @access private
   * @internal
   */
  private function _load_all_modules()
  {
	  $this->_moduleinfo = array();
	  $info = $this->_get_module_info();
	  $names = $this->FindAllModules();
	  foreach( $names as $name ) {
		  if( isset($this->_moduleinfo[$name]) ) continue; // already know about this module.

		  // this module isn't in the database, but is in the filesystem... make up some dummy info.
		  $rec = array('module_name'=>$name,'status'=>'not installed','version'=>'0.0',
					   'admin_only'=>0,'active'=>0,'allow_fe_lazyload'=>0,'allow_admin_lazyload'=>0);
		  $this->_moduleinfo[$name] = $rec;
	  }
	  ksort($this->_moduleinfo);
  }


  /**
   * Finds all modules that are available to be loaded...
   * this method uses the information in the database to load the modules that are necessary to load
   * it also, will go through any queued installs/upgrades and force those modules to load, which 
   * will in turn do the upgrading and installing if necessary.
   *
   * @access public
   * @internal
   * @param loadall boolean indicates wether ALL modules in the filesystem should be loaded, default is false
   * @param noadmin boolean indicates that modules marked as admin_only in the database should not be loaded, default is false
   * @param no_lazyload boolean indicates that modules marked as lazy_loadable should be loaded anywayz, default is falze
   * @return void
   */
  public function LoadModules($loadall = false,$noadmin = false, $no_lazyload = false)
  {
	  if( $loadall ) $this->_load_all_modules();
	  global $CMS_ADMIN_PAGE;
	  global $CMS_STYLESHEET;
	  $config = cmsms()->GetConfig();

	  $allinfo = $this->_get_module_info();
	  if( !is_array($allinfo) ) return; // no modules installed, probably an empty database... edge case.
	  foreach( $allinfo as $module_name => $info ) {
		  if( $info['status'] != 'installed' ) continue;
		  if( !$info['active'] ) continue;
		  if( ($info['admin_only'] || (isset($info['allow_fe_lazyload']) && $info['allow_fe_lazyload'])) && !isset($CMS_ADMIN_PAGE) ) continue;
		  if( isset($config['admin_loadnomodules']) && isset($CMS_ADMIN_PAGE) ) continue;
		  if( isset($info['allow_admin_lazyload']) && $info['allow_admin_lazyload'] && isset($CMS_ADMIN_PAGE) ) continue;
		  if( isset($CMS_STYLESHEET) && !isset($CMS_STYLESHEET) ) continue;
		  $this->get_module_instance($module_name);
	  }
	  if( isset($_SESSION['moduleoperations']) && is_array($_SESSION['moduleoperations']) && count($_SESSION['moduleoperations']) ) {
		  // there are modules queued for install/upgrade that may not have been loaded.
		  foreach($_SESSION['moduleoperations'] as $module_name => $info ) {
			  if( !isset($allinfo[$module_name]) ) {
				  // we don't know about this module yet...
				  $rec = array('module_name'=>$module_name,'status'=>'not installed','version'=>'0.0',
							   'admin_only'=>0,'active'=>0,'allow_fe_lazyload'=>0,'allow_admin_lazyload'=>0);
				  $this->_moduleinfo[$module_name] = $rec;
			  }
			  $this->get_module_instance($module_name,'',TRUE);
			  unset($_SESSION['moduleiperations'][$module_name]);
		  }
		  // by here... we should not have anything left queued
		  unset($_SESSION['moduleoperations']);
	  }
	  return;
  }


  private function _upgrade_module( &$module_obj, $to_version = '' )
  {
	  // we can't upgrade a module if the schema is not up to date.
	  $db = cmsms()->GetDb();
	  $tmp = $db->GetOne('SELECT version FROM '.cms_db_prefix().'version');
	  if( $tmp && $tmp < CMS_SCHEMA_VERSION ) return FALSE;

	  $info = $this->_get_module_info();
	  $module_name = $module_obj->GetName();
	  $dbversion = $info[$module_name]['version'];

	  if( $to_version == '' ) $to_version = $module_obj->GetVersion();

	  $result = $module_obj->Upgrade($dbversion,$to_version);
	  if( $result !== FALSE ) {
		  $db = cmsms()->GetDb();
		  $lazyload_fe    = (method_exists($module_obj,'LazyLoadFrontend') && $module_obj->LazyLoadFrontend())?1:0;
		  $lazyload_admin = (method_exists($module_obj,'LazyLoadAdmin') && $module_obj->LazyLoadAdmin())?1:0;

		  $query = 'UPDATE '.cms_db_prefix().'modules SET version = ?, allow_fe_lazyload = ?,allow_admin_lazyload = ? WHERE module_name = ?';
		  $dbr = $db->Execute($query,array($module_obj->GetVersion(),$lazyload_fe,$lazyload_admin,$module_obj->GetName()));

		  $info[$module_obj->GetName()]['version'] = $module_obj->GetVersion();
		  audit('','Module', 'Upgraded module '.$module_obj->GetName().' to version '.$module_obj->GetVersion());
		  Events::SendEvent('Core', 'ModuleUpgraded', array('name' => $module_obj->GetName(), 'oldversion' => $dbversion, 'newversion' => $module_obj->GetVersion()));
		  cmsms()->clear_cached_files();
		  return TRUE;
	  }
	  return FALSE;
  }


  /**
   * Upgrade a module
   *
   * This is an internal method, subject to change in later releases.  It should never be called for upgrading arbitrary modules.
   * Any use of this function by third party code will not be supported.  Use at your own risk and do not report bugs or issues
   * related to your use of this module.
   *
   * @param string $module The name of the module to upgrade
   * @return boolean Whether or not the upgrade was successful
   */
  public function UpgradeModule( $module_name, $to_version = '')
  {
	  $module_obj = $this->get_module_instance($module_name);
	  if( !is_object($module_obj) ) return FALSE;
	  return $this->_upgrade_module($module_obj,$to_version);
  }


  /**
   * Uninstall a module
   *
   * @param string $module The name of the module to upgrade
   * @return boolean Whether or not the upgrade was successful
   * @internal
   */
  public function UninstallModule( $module)
  {
	  $gCms = cmsms();
	  $db = $gCms->GetDb();

	  $modinstance = cms_utils::get_module($module);
	  if( !$modinstance ) return FALSE;

	  $cleanup = $modinstance->AllowUninstallCleanup();
	  $result = $modinstance->Uninstall();

	  if (!isset($result) || $result === FALSE) {
		  // now delete the record
		  $query = "DELETE FROM ".cms_db_prefix()."modules WHERE module_name = ?";
		  $db->Execute($query, array($module));

		  // delete any dependencies
		  $query = "DELETE FROM ".cms_db_prefix()."module_deps WHERE child_module = ?";
		  $db->Execute($query, array($module));

		  // clean up, if permitted
		  if ($cleanup) {
			  $db->Execute('DELETE FROM '.cms_db_prefix().'module_templates where module_name=?',array($module));
			  $db->Execute('DELETE FROM '.cms_db_prefix().'event_handlers where module_name=?',array($module));
			  $db->Execute('DELETE FROM '.cms_db_prefix().'events where originator=?',array($module));
			  $db->Execute('DELETE FROM '.cms_db_prefix().'module_smarty_plugins where module=?',array($module));
			  $db->Execute('DELETE FROM '.cms_db_prefix().
						   "siteprefs WHERE sitepref_name LIKE '".
						   str_replace("'",'',$db->qstr($module)).
						   "_mapi_pref%'");
			  $db->Execute('DELETE FROM '.cms_db_prefix().'routes WHERE dest = ?',array($module));
			  $db->Execute('DELETE FROM '.cms_db_prefix().'module_smarty_plugins WHERE module = ?',array($module));
		  }

		  // clear the cache.
		  $gCms->clear_cached_files();

		  // Removing module from info
		  unset($this->_moduleinfo[$module]);

		  Events::SendEvent('Core', 'ModuleUninstalled', array('name' => $module));
		  audit('','Module','Uninstalled module '.$module);
	  }
	  else {
		  $this->setError($result);
		  return false;
	  }
	  return true;
  }


  /**
   * Test if a module is active
   */
  public function IsModuleActive($module_name)
  {
	  if( !$module_name ) return FALSE;
	  $info = $this->_get_module_info();
	  if( !isset($info[$module_name]) ) return FALSE;

	  return (bool)$info[$module_name]['active'];
  }


  /**
   * Activate a module
   *
   * @param string module name
   * @param boolean flag indicating wether to activate or deactivate the module
   * @return boolean
   */
  public function ActivateModule($module_name,$activate = true)
  {
	  if( !$module_name ) return FALSE;
	  $info = $this->_get_module_info();
	  if( !isset($info[$module_name]) ) return FALSE;

	  $o_state = $info['module_name']['active'];
	  if( $activate ) {
		  $info['module_name']['active'] = 1;
	  }
	  else {
		  $info['module_name']['active'] = 0;
	  }
	  if( $info['module_name']['active'] != $o_state ) {
		  $db = cmsms()->GetDb();
		  $query = 'UPDATE '.cms_db_prefix.' SET active = ? WHERE module_name = ?';
		  $dbr = $db->Execute($query,array($info['module_name']['active'],$module_name));
	  }
	  return TRUE;
  }


  /**
   * Returns a hash of all loaded modules.  This will include all
   * modules loaded into memory at the current time
   *
   * @return array The hash of all loaded modules
   */
  public function &GetLoadedModules()
  {
	  return $this->_modules;
  }


  /**
   * Return an array of the names of all modules that we currently know about
   */
  public function GetAllModuleNames()
  {
	  return array_keys($this->_get_module_info());
  }

  /**
   * Returns an array of the names of all installed modules.
   *
   * @return array of strings
   */
  public function GetInstalledModules($include_all = FALSE)
  {
	  $result = array();
	  $info = $this->_get_module_info();
	  if( is_array($info) ) {
		  foreach( $info as $name => $rec ) {
			  if( $rec['status'] != 'installed' ) continue;
			  if( !$rec['active'] && $include_all == FALSE ) continue;
			  $result[] = $name;
		  }
	  }
	  return $result;
  }


  /**
   * Returns an array of installed modules that have a certain capabilies
   * This method will force the loading of all modules regardless of the module settings.
   * 
   * @param string $capability The capability name
   * @param mixed $args Capability arguments
   * @return array List of all the module objects with that capability
   */
  public static function get_modules_with_capability($capability, $args= '')
  {
	  if( !is_array($args) ) {
		  if( !empty($args) ) {
			  $args = array($args);
		  }
		  else {
			  $args = array();
		  }
	  }
	  return module_meta::get_instance()->module_list_by_capability($capability,$args);
  }

  /**
   * A function to return a list of dependencies from a module.
   * this method works by reading the dependencies from the database.
   *
   * @since 1.11.8
   * @author Robert Campbell
   * @param string The module name
   * @return mixed.  Null if there are no dependencies.  Otherwise, a hash of dependent module names, and their versions.
   */
  public function get_module_dependencies($module_name)
  {
	  if( !$module_name ) return;

	  if( !is_array($this->_moduledeps) ) {
		  $fn = TMP_CACHE_LOCATION.'/f'.md5(__FILE__.'deps').'.dat';
		  if( file_exists($fn) ) {
			  $data = file_get_contents($fn);
			  $this->_moduledeps = unserialize($data);
		  }
		  else {
			  $this->_moduledeps = array();
			  $db = cmsms()->GetDb();
			  $query = 'SELECT parent_module,child_module,minimum_version FROM '.cms_db_prefix().'module_deps';
			  $dbr = $db->GetArray($query);
			  if( is_array($dbr) && count($dbr) ) {
				  foreach( $dbr as $row ) {
					  if( !isset($this->_moduledeps[$row['child_module']]) ) $this->_moduledeps[$row['child_module']] = array();
					  $this->_moduledeps[$row['child_module']][$row['parent_module']] = $row['minimum_version'];
				  }
			  }
			  file_put_contents($fn,serialize($this->_moduledeps));
		  }
	  }

	  if( isset($this->_moduledeps[$module_name]) ) return $this->_moduledeps[$module_name];
  }

  /**
   * A function to return the object reference to the module object
   * if the module is not already loaded, it will be loaded.  Version checks are done
   * with the module to allow only loading versions of modules that are greater than the 
   * specified value.
   *
   * @param string The module name
   * @param string an optional version string.
   * @param boolean an optional flag to indicate wether the module should be force loaded if necesary.
   * @return mixed  Null on failure, or an object of type CmsModule on success.
   */
  public function &get_module_instance($module_name,$version = '',$force = FALSE)
  {
	  if( empty($module_name) && isset($this->variables['module'])) {
		  $module_name = $this->variables['module'];
	  }

	  $obj = null;
	  if( isset($this->_modules[$module_name]) ) {
		  $obj =& $this->_modules[$module_name];
	  }
	  if( !is_object($obj) ) {
		  // gotta load it.
		  $res = $this->_load_module($module_name,$force);
		  if( $res ) {
			  $obj =& $this->_modules[$module_name];
		  }
	  }

	  if( is_object($obj) && !empty($version) ) {
		  $res = version_compare($obj->GetVersion(),$version);
		  if( $res < 0 OR $res === FALSE ) $obj = null;
	  }
	  return $obj;
  }


  /**
   * Test if the specified module name is a system module
   *
   * @param string The module name
   * @return boolean
   */
  public function IsSystemModule($module_name)
  {
	  return in_array($module_name,$this->cmssystemmodules);
  }



  /**
   * Return the current syntax highlighter module object
   * 
   * This method retrieves the specified syntax highlighter module, or uses the current current user preference for the syntax hightlighter module
   * for a name.
   *
   * @param string allows bypassing the automatic detection process and specifying a wysiwyg module.
   * @return null on failure, an object of type CmsModule On success.
   * @since 1.10
   */
  public function &GetSyntaxHighlighter($module_name = '')
  {
	  $obj = null;
	  if( !$module_name ) {
		  global $CMS_ADMIN_PAGE;
		  if( isset($CMS_ADMIN_PAGE) ) $module_name = get_preference(get_userid(FALSE),'syntaxhighlighter');
	  }

	  if( !$module_name ) return $obj;

	  $obj = $this->get_module_instance($module_name);
	  if( !$obj ) return $obj;
	  if( !$obj->IsSyntaxHighlighter() ) return $obj;

	  return $obj;
  }


  /**
   * Return the current wysiwyg module object
   * 
   * This method makes an attempt to find the appropriate wysiwyg module given the current request context
   * and admin user preference.
   *
   * @param string allows bypassing the automatic detection process and specifying a wysiwyg module.
   * @return null on failure, an object of type CmsModule On success.
   * @since 1.10
   * @deprecated
   */
  public function &GetWYSIWYGModule($module_name = '')
  {
	  global $CMS_ADMIN_PAGE;
	  $obj = null;
	  if( !$module_name ) {
		  if( !isset($CMS_ADMIN_PAGE) ) {
			  $module_name = get_site_preference('frontendwysiwyg');
		  }
		  else {
			  $module_name = get_preference(get_userid(FALSE),'wysiwyg');
		  }
	  }

	  if( !$module_name || $module_name == -1 ) return $obj;

	  $obj = $this->get_module_instance($module_name);
	  if( !$obj ) return $obj;
	  if( $obj->IsWYSIWYG() ) return $obj;

	  $obj = null;
	  return $obj;
  }


  /**
   * Return the current search module object
   *
   * This method returns module object for the currently selected search module.  
   *
   * @return null on failure, an object of type CmsModule on success
   * @since 1.10
   */
  public function &GetSearchModule()
  {
	  $obj = null;
	  $module_name = get_site_preference('searchmodule','Search');
	  if( $module_name && $module_name != 'none' && $module_name != '-1' ) {
		  $obj = $this->get_module_instance($module_name);
	  }
	  return $obj;
  }


  /**
   * Alias for the GetSyntaxHiglighter method.
   * 
   * @see GetSyntaxHighlighter
   * @deprecated
   * @since 1.10
   */
  public function &GetSyntaxModule($module_name = '')
  {
	  return $this->GetSyntaxHighlighter($module_name);
  }


  private function _is_queued_for_install($module_name)
  {
	  if( !isset($_SESSION['moduleoperations']) ) return FALSE;
	  if( !isset($_SESSION['moduleoperations'][$module_name]) ) return FALSE;
	  return TRUE;
  }


  /**
   * Queue a module for install
   * 
   * @internal
   * @since 1.10
   * @param string module name
   * @return void
   */
  public function QueueForInstall($module_name)
  {
	  if( !$module_name ) return;
	  if( !isset($_SESSION['moduleoperations']) ) $_SESSION['moduleoperations'] = array();

	  if( !isset($_SESSION['moduleoperations'][$module_name]) ) $_SESSION['moduleoperations'][$module_name] = 1;
  }


  /**
   * Get list of modules queued for install.
   * 
   * @internal
   * @since 1.10
   * @param string module name
   * @return void
   */
  public function GetQueueResults()
  {
	  if( isset($_SESSION['moduleoperations_result']) ) {
		  $data = $_SESSION['moduleoperations_result'];
		  unset($_SESSION['moduleoperations_result']);
		  return $data;
	  }
  }


  /**
   * Unload a module from memory
   *
   * @internal
   * @since 1.10
   * @param string module name
   * @return void
   */
  public function unload_module($module_name)
  {
	  if( !isset($this->_modules[$module_name]) || !is_object($this->_modules[$module_name]) )  return;
	  unset($this->_modules[$module_name]);
  }

} // end of class

# vim:ts=4 sw=4 noet
?>