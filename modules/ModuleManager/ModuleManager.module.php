<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: ModuleManager (c) 2008 by Robert Campbell 
#         (calguy1000@cmsmadesimple.org)
#  An addon module for CMS Made Simple to allow browsing remotely stored
#  modules, viewing information about them, and downloading or upgrading
# 
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin 
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE
if (!isset($gCms)) exit;

define('MINIMUM_REPOSITORY_VERSION','1.3');

function uasort_cmp_details( $e1, $e2 )
{
  if( strtolower($e1['name']) < strtolower($e2['name']) )
    {
      return -1;
    }
  else if( strtolower($e1['name']) > strtolower($e2['name']) )
    {
      return 1;
    }
  return version_compare( $e2['version'], $e1['version'] );
}

class ModuleManager extends CMSModule
{

  function GetName()
  {
    return 'ModuleManager';
  }


  /*---------------------------------------------------------
   GetFriendlyName()
   ---------------------------------------------------------*/
  function GetFriendlyName()
  {
    return $this->Lang('friendlyname');
  }

	
  /*---------------------------------------------------------
   GetVersion()
   ---------------------------------------------------------*/
  function GetVersion()
  {
    return '1.4.2';
  }


  /*---------------------------------------------------------
   GetHelp()
   ---------------------------------------------------------*/
  function GetHelp()
  {
    return $this->Lang('help');
  }


  /*---------------------------------------------------------
   GetAuthor()
   ---------------------------------------------------------*/
  function GetAuthor()
  {
    return 'calguy1000';
  }


  /*---------------------------------------------------------
   GetAuthorEmail()
   ---------------------------------------------------------*/
  function GetAuthorEmail()
  {
    return 'calguy1000@hotmail.com';
  }


  /*---------------------------------------------------------
   GetChangeLog()
   ---------------------------------------------------------*/
  function GetChangeLog()
  {
    return $this->Lang('changelog');
  }


  /*---------------------------------------------------------
   IsPluginModule()
   ---------------------------------------------------------*/
  function IsPluginModule()
  {
    return false;
  }


  /*---------------------------------------------------------
   HasAdmin()
   ---------------------------------------------------------*/
  function HasAdmin()
  {
    return true;
  }


  /*---------------------------------------------------------
   IsAdminOnly()
   ---------------------------------------------------------*/
  function IsAdminOnly()
  {
    return true;
  }


  /*---------------------------------------------------------
   GetAdminSection()
   ---------------------------------------------------------*/
  function GetAdminSection()
  {
    return 'extensions';
  }


  /*---------------------------------------------------------
   GetAdminDescription()
   ---------------------------------------------------------*/
  function GetAdminDescription()
  {
    return $this->Lang('admindescription');
  }


  /*---------------------------------------------------------
   VisibleToAdminUser()
   ---------------------------------------------------------*/
  function VisibleToAdminUser()
  {
    if( $this->CheckPermission('Modify Site Preferences') ||
	$this->CheckPermission('Modify Modules') )
      {
	return true;
      }
    return false;
  }
	

  /*---------------------------------------------------------
   SetParameters()
   ---------------------------------------------------------*/
  function SetParameters()
  {
	  $this->RestrictUnknownParams();
	  $this->SetParameterType('curletter',CLEAN_STRING);
	  $this->SetParameterType('name',CLEAN_STRING);
	  $this->SetParameterType('version',CLEAN_STRING);
	  $this->SetParameterType('filename',CLEAN_STRING);
	  $this->SetParameterType('size',CLEAN_INT);
  }
  


  /*---------------------------------------------------------
   CheckAccess()
   ---------------------------------------------------------*/
  function CheckAccess($id, $params, $returnid,$perm = 'Modify Modules')
  {
    if (! $this->CheckPermission($perm))
      {
	$this->_DisplayErrorPage($id, $params, $returnid,
				$this->Lang('accessdenied'));
	return false;
      }
    return true;
  }
	
  /*---------------------------------------------------------
   _DisplayErrorPage()
   This is a simple function for generating error pages.
   ---------------------------------------------------------*/
  function _DisplayErrorPage($id, &$params, $returnid, $message='')
  {
    $this->smarty->assign('title_error', $this->Lang('error'));
    $this->smarty->assign_by_ref('message', $message);
	$this->smarty->assign('link_back',$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager')));	
	  
    // Display the populated template
    echo $this->ProcessTemplate('error.tpl');
  }
	

  /*---------------------------------------------------------
   GetDependencies()
   ---------------------------------------------------------*/
  function GetDependencies()
  {
    return array('nuSOAP'=>'1.0.1');
  }


  /*---------------------------------------------------------
   MinimumCMSVersion()
   ---------------------------------------------------------*/
  function MinimumCMSVersion()
  {
    return "1.8-beta1";
  }


  /*---------------------------------------------------------
   Install()
   ---------------------------------------------------------*/
  function Install()
  {
    $this->SetPreference('module_repository','http://modules.cmsmadesimple.org/soap.php?module=ModuleRepository');

    // put mention into the admin log
    $this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('installed',$this->GetVersion()));
  }

  /*---------------------------------------------------------
   InstallPostMessage()
   ---------------------------------------------------------*/
  function InstallPostMessage()
  {
    return $this->Lang('postinstall');
  }


  /*---------------------------------------------------------
   UninstallPostMessage()
   ---------------------------------------------------------*/
  function UninstallPostMessage()
  {
    return $this->Lang('postuninstall');
  }


  /*---------------------------------------------------------
   Upgrade()
   ---------------------------------------------------------*/
  function Upgrade($oldversion, $newversion)
  {
    $current_version = $oldversion;
    switch($current_version)
      {
      case "1.0":
	$this->SetPreference('module_repository','http://modules.cmsmadesimple.org/soap.php?module=ModuleRepository');
	break;
      }
		
    // put mention into the admin log
    $this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('upgraded',$this->GetVersion()));
  }


  /**
   * UninstallPreMessage()
   */
  function UninstallPreMessage()
  {
    return $this->Lang('really_uninstall');
  }

	
  /*---------------------------------------------------------
   Uninstall()
   ---------------------------------------------------------*/
  function Uninstall()
  {
    $this->RemovePreference();

    // put mention into the admin log
    $this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));
  }


  /*---------------------------------------------------------
   DoAction($action, $id, $params, $returnid)
   ---------------------------------------------------------*/
  function DoAction($action, $id, $params, $returnid=-1)
  {
    switch ($action)
      {
      case 'installmodule':
	{
	  if ($this->CheckAccess($id, $params, $returnid,'Modify Modules'))
	    {
	      $this->_DoRecursiveInstall( $id, $params, $returnid );
	    }
	  break;
	}

      case 'doinstall':
	{
	  if ($this->CheckAccess($id, $params, $returnid,'Modify Modules'))
	    {
	      $this->_DoInstallLoop( $id, $params, $returnid );
	    }
	  break;
	}

      case 'modulehelp':
	{
	  $this->_DisplayAdminSoapModuleHelp( $id, $params, $returnid );
	}
	break;

      case 'moduledepends':
	{
	  $this->_DisplayAdminSoapModuleDepends( $id, $params, $returnid );
	}
	break;

      case 'moduleabout':
	{
	  $this->_DisplayAdminSoapModuleAbout( $id, $params, $returnid );
	}
	break;
	 case 'recurseinstall':
		{
		$this->_DoRecursiveInstall( $id, $params, $returnid);
		}

      // fallback through to call the action.xxxx.php file
      default:
	parent::DoAction( $action, $id, $params, $returnid );
	break;
      }
  }


  /*----------------------------------------------------------
   _BuildModuleData( $xmldetails, $installdetails );
   build a hash of info about the installed modules.
   ---------------------------------------------------------*/
  function _BuildModuleData( &$xmldetails, &$installdetails )
  {
    global $CMS_VERSION;
    if( !is_array($xmldetails) ) return;

    // sort
    uasort( $xmldetails, 'uasort_cmp_details' );

    //
    // Process the xmldetails, and only keep the latest version
    // of each (according to a preference)
    //
    // Note: should be redundant with 1.2, but kept in here for
    // a while just in case..
    $thexmldetails = $xmldetails;
    if( $this->GetPreference('onlynewest',"1") == "1" )
      {
	$thexmldetails = array();
	$prev = '';
	foreach( $xmldetails as $det )
	  {
	    if( is_array($prev) && $prev['name'] == $det['name'] )
	      {
		continue;
	      }

	    $prev = $det;
	    $thexmldetails[] = $det;
	  }
      }

    $results = array();
    foreach( $thexmldetails as $det1 )
      {
	$found = 0;
	foreach( $installdetails as $det2 )
	  {
	    if( $det1['name'] == $det2['name'] )
	      {
		$found = 1;
		// if the version of the xml file is greater than that of the
		// installed module, we have an upgrade
		$res = version_compare( $det1['version'], $det2['version'] );
		if( $res == 1 )
		  {
		    $det1['status'] = 'upgrade';
		  }
		else if( $res == 0 )
		  {
		    $det1['status'] = 'uptodate';
		  }
		else
		  {
		    $det1['status'] = 'newerversion';
		  }

		$results[] = $det1;
		break;
	      }
	  }
	if( $found == 0 )
	  {
	    // we don't have this module installed
	    $det1['status'] = 'notinstalled';
	    $results[] = $det1;
	  }
      }

    //
    // Do a third loop
    // and check min and max cms version
    //
    $results2 = array();
    foreach( $results as $oneresult )
      {
	if( (!empty($oneresult['maxcmsversion']) && 
	     version_compare($CMS_VERSION,$oneresult['maxcmsversion']) > 0) ||
	    (!empty($oneresult['mincmsversion']) &&
	     version_compare($CMS_VERSION,$oneresult['mincmsversion']) < 0) )
	  {
	    $oneresult['status'] = 'incompatible';
	  }
	$results2[] = $oneresult;
      }
    $results = $results2;

    // now we have everything
    // let's try sorting it
    uasort( $results, 'uasort_cmp_details' );
    return $results;
  }


  /*---------------------------------------------------------
   _DisplayAdminModulesTab()
   ---------------------------------------------------------*/
  function _DisplayAdminModulesTab($id, &$params, $returnid)
  {
    $caninstall = true;
    if( FALSE == can_admin_upload() )
      {
	echo '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror">'.$this->Lang('error_permissions').'</p></div></div>';
	$caninstall = false;
      }

    $curletter = 'A';
    if( isset( $params['curletter'] ) )
      {
	  $curletter = $params['curletter'];
	  $_SESSION['mm_curletter'] = $curletter;
      }
    else if (isset($_SESSION['mm_curletter']))
      {
	  $curletter = $_SESSION['mm_curletter'];
      }

    // get the modules available in the repository
    $repmodules = '';
    {
      $newest = $this->GetPreference('onlynewest',1);
      $result = $this->_GetRepositoryModules($curletter,$newest);
       if( ! $result[0] )
 	{
 	  $this->_DisplayErrorPage( $id, $params, $returnid, $result[1] );
 	  return;
	}
      $repmodules = $result[1];
    }

    // get the modules that are already installed
    $instmodules = '';
    {
      $result = $this->_GetInstalledModules();
      if( ! $result[0] )
	{
	  $this->_DisplayErrorPage( $id, $params, $returnid, $result[1] );
	  return;
	}
      
      $instmodules = $result[1];
    }

    // build a letters list
    $letters = array();
    $tmp = explode(',','A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z');
    foreach( $tmp as $i )
      {
	if( $i == $curletter )
	  {
	    $letters[] = "<strong>$i</strong>";
	  }
	else
	  {
	    $letters[] = $this->CreateLink($id,'defaultadmin',$returnid, $i, array('curletter'=>$i,'active_tab'=>'modules'));
	  }
      }

    // cross reference them
    $data = array();
    if( count($repmodules ) )
      {
	$data = $this->_BuildModuleData( $repmodules, $instmodules );
      }
    if( count( $data ) )
      {
	$size = count($data);

	// check for permissions
	$moduledir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules";
	$writable = is_writable( $moduledir );	

	// build the table
	$rowarray = array();
	$rowclass = 'row1';
	$newestdisplayed="";
	foreach( $data as $row )
	  {
	    $onerow = new stdClass();
	    $onerow->name = $row['name'];
	    $onerow->version = $row['version'];
	    $onerow->helplink = $this->CreateLink( $id, 'modulehelp', $returnid,
						   $this->Lang('helptxt'), 
						   array('name' => $row['name'],
							 'version' => $row['version'],
							 'filename' => $row['filename']));
	    $onerow->dependslink = $this->CreateLink( $id, 'moduledepends', $returnid,
						   $this->Lang('dependstxt'), 
						   array('name' => $row['name'],
							 'version' => $row['version'],
							 'filename' => $row['filename']));
	    $onerow->aboutlink = $this->CreateLink( $id, 'moduleabout', $returnid,
						    $this->Lang('abouttxt'), 
						    array('name' => $row['name'],
							  'version' => $row['version'],
							  'filename' => $row['filename']));

	    switch( $row['status'] ) 
	      {
	      case 'incompatible':
		$onerow->status = $this->Lang('incompatible');
		break;
	      case 'uptodate':
		$onerow->status = $this->Lang('uptodate');
		break;
	      case 'newerversion':
		$onerow->status = $this->Lang('newerversion');
		break;
	      case 'notinstalled':
		{
		  $mod = $moduledir.DIRECTORY_SEPARATOR.$row['name'];
		  if( (($writable && is_dir($mod) && is_directory_writable( $mod )) ||
		       ($writable && !file_exists( $mod ) )) && $caninstall )
		    {
		      $onerow->status = $this->CreateLink( $id, 'installmodule', $returnid,
							   $this->Lang('download'), 
							   array('name' => $row['name'],
								 'version' => $row['version'],
								 'filename' => $row['filename'],
								 'size' => $row['size']));
		    }

		  else
		    {
		      $onerow->status = $this->Lang('cantdownload');
		    }
		  break;
		}
	      case 'upgrade':
		{
		  $mod = $moduledir.DIRECTORY_SEPARATOR.$row['name'];
		  if( (($writable && is_dir($mod) && is_directory_writable( $mod )) ||
		       ($writable && !file_exists( $mod ) )) && $caninstall )
		    {
		      $onerow->status = $this->CreateLink( $id, 'upgrademodule', $returnid,
							   $this->Lang('upgrade'), 
							   array('name' => $row['name'],
								 'version' => $row['version'],
								 'filename' => $row['filename'],
								 'size' => $row['size']));
		    }
		  else
		    {
		      $onerow->status = $this->Lang('cantdownload');
		    }
		  break;
		}
	      }
	    
	    $onerow->size = (int)((float) $row['size'] / 1024.0 + 0.5);
	    $onerow->rowclass = $rowclass;
	    if( isset( $row['description'] ) )
	      {
		$onerow->description=$row['description'];
	      }
	    $rowarray[] = $onerow;
	    ($rowclass == "row1" ? $rowclass = "row2" : $rowclass = "row1");
	  } // for

	$this->smarty->assign('items', $rowarray);
	$this->smarty->assign('itemcount', count($rowarray));
      }
    else
      {
	$this->smarty->assign('message', $this->Lang('error_connectnomodules'));
      }
    // Setup search form
    $searchstart = $this->CreateFormStart( $id, 'searchmod', $returnid );
    $searchend = $this->CreateFormEnd();
    $searchfield = $this->CreateInputText($id, 'search_input', "Doesn't Work",  30, 100); //todo
    $searchsubmit = $this->CreateInputSubmit( $id, 'submit', 'Search'); // todo -- $this->Lang('search'));
    $this->smarty->assign('search',$searchstart.$searchfield.$searchsubmit.$searchend);

    // and display our page
    $this->smarty->assign('letters', implode('&nbsp;',array_values($letters)));
    $this->smarty->assign('nametext',$this->Lang('nametext'));
    $this->smarty->assign('vertext',$this->Lang('vertext'));
    $this->smarty->assign('sizetext',$this->Lang('sizetext'));
    $this->smarty->assign('statustext',$this->Lang('statustext'));
    echo $this->processTemplate('adminpanel.tpl');
  }


  /*---------------------------------------------------------
   _DisplayAdminPrefsTab()
   ---------------------------------------------------------*/
  function _DisplayAdminPrefsTab($id, &$params, $returnid)
  {
    $this->smarty->assign('formstart',$this->CreateFormStart( $id, 'setprefs', $returnid ));
    $this->smarty->assign('formend',$this->CreateFormEnd());
    $this->smarty->assign('prompt_url',$this->Lang('prompt_repository_url'));
    $this->smarty->assign('input_url',$this->CreateInputText($id, 'url', 
						       $this->GetPreference('module_repository'),
						       80, 255 ));
    $this->smarty->assign('extratext_url',$this->Lang('text_repository_url'));
    
    $this->smarty->assign('prompt_onlynewest',$this->Lang('onlynewesttext'));
    $this->smarty->assign('input_onlynewest',$this->CreateInputCheckBox($id,'onlynewest',"1",$this->GetPreference("onlynewest","1")));
    
    $this->smarty->assign('prompt_reseturl',$this->Lang('prompt_reseturl'));
    $this->smarty->assign('input_reseturl',$this->CreateInputSubmit($id,'reseturl',$this->Lang('reset')));
    
    

    $this->smarty->assign('prompt_chunksize',$this->Lang('prompt_dl_chunksize'));
    $this->smarty->assign('input_chunksize',$this->CreateInputText($id, 'input_dl_chunksize',
								   $this->GetPreference('dl_chunksize',256),3,3));
    $this->smarty->assign('extratext_chunksize',$this->Lang('text_dl_chunksize'));

    $this->smarty->assign('prompt_resetcache',$this->Lang('prompt_resetcache'));
    $this->smarty->assign('input_resetcache',$this->CreateInputSubmit($id,'resetcache',$this->Lang('reset')));
			  
    $this->smarty->assign('submit',$this->CreateInputSubmit( $id, 'submit', $this->Lang('submit')));
    $this->smarty->assign('prompt_otheroptions',$this->Lang('prompt_otheroptions'));
    $this->smarty->assign('prompt_settings',$this->Lang('prompt_settings'));
    echo $this->ProcessTemplate('adminprefs.tpl');
  }

  /*---------------------------------------------------------
   _DisplayAdminSoapModuleDepends()
   Display the help for an a module on the repository
   ---------------------------------------------------------*/
  function _DisplayAdminSoapModuleDepends($id, &$params, $returnid)
  {
    if( !isset( $params['name'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_insufficientparams'));
	return;
      }
    $name = $params['name'];

    if( !isset( $params['version'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_insufficientparams'));
	return;
      }
    $version = $params['version'];

    $url = $this->GetPreference('module_repository');
    if( $url == '' )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_norepositoryurl'));
	return;
      }

    if( !isset($params['filename'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_nofilename'));
	return;
      }
    $xmlfile = $params['filename'];

    $nusoap =& $this->GetModuleInstance('nuSOAP');
    $nusoap->Load();
    $nu_soapclient = new nu_soapclient($url,false,false,false,false,false,90,90);
    if( $err = $nu_soapclient->GetError() )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  'SOAP Error: '.$err);
	return;
      }

    $depends = $nu_soapclient->call('ModuleRepository.soap_moduledepends',array('name' => $xmlfile ));
    if( $err = $nu_soapclient->GetError() )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  'SOAP Error: '.$err);
	echo htmlspecialchars($nu_soapclient->response);
	return;
      }
    if( $depends[0] == false )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $depends[1] );
	return;
      }

    $this->smarty->assign('title',$this->Lang('dependstxt'));
    $this->smarty->assign('moduletext',$this->Lang('nametext'));
    $this->smarty->assign('vertext',$this->Lang('vertext'));
    $this->smarty->assign('xmltext',$this->Lang('xmltext'));
    $this->smarty->assign('modulename',$name);
    $this->smarty->assign('moduleversion',$version);
    $this->smarty->assign('xmlfile',$xmlfile);
	$this->smarty->assign('link_back',$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager')));	

    $txt = '';
    if( is_array($depends[1]) )
      {
	$txt = '<ul>';
	foreach( $depends[1] as $one )
	  {
	    $txt .= '<li>'.$one['name'].' => '.$one['version'].'</li>';
	  }
	$txt .= '</ul>';
      }
    else
      {
	$txt = $this->Lang('msg_nodependencies');
      }
    $this->smarty->assign('content',$txt);
    echo $this->ProcessTemplate('remotecontent.tpl');
  }


  /*---------------------------------------------------------
   _DisplayAdminSoapModuleHelp()
   Display the help for an a module on the repository
   ---------------------------------------------------------*/
  function _DisplayAdminSoapModuleHelp($id, &$params, $returnid)
  {
    if( !isset( $params['name'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_insufficientparams'));
	return;
      }
    $name = $params['name'];

    if( !isset( $params['version'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_insufficientparams'));
	return;
      }
    $version = $params['version'];

    $url = $this->GetPreference('module_repository');
    if( $url == '' )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_norepositoryurl'));
	return;
      }

    if( !isset($params['filename'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_nofilename'));
	return;
      }
    $xmlfile = $params['filename'];

    $nusoap =& $this->GetModuleInstance('nuSOAP');
    $nusoap->Load();
    $nu_soapclient = new nu_soapclient($url,false,false,false,false,false,90,90);
    if( $err = $nu_soapclient->GetError() )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  'SOAP Error: '.$err);
	return;
      }
    $help = $nu_soapclient->call('ModuleRepository.soap_modulehelp',array('name' => $xmlfile ));
    if( $err = $nu_soapclient->GetError() )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  'SOAP Error: '.$err);
	echo htmlspecialchars($nu_soapclient->response);
	return;
      }
    if( $help[0] == false )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $help[1] );
	return;
      }
    $this->smarty->assign('title',$this->Lang('helptxt'));
    $this->smarty->assign('moduletext',$this->Lang('nametext'));
    $this->smarty->assign('vertext',$this->Lang('vertext'));
    $this->smarty->assign('xmltext',$this->Lang('xmltext'));
    $this->smarty->assign('modulename',$name);
    $this->smarty->assign('moduleversion',$version);
    $this->smarty->assign('xmlfile',$xmlfile);
    $this->smarty->assign('content',$help[1]);
	$this->smarty->assign('link_back',$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager')));	

    echo $this->ProcessTemplate('remotecontent.tpl');
  }


  /*---------------------------------------------------------
   _DisplayAdminSoapModuleAbout()
   Display the about info for an a module on the repository
   ---------------------------------------------------------*/
  function _DisplayAdminSoapModuleAbout($id, &$params, $returnid)
  {
    if( !isset( $params['name'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_insufficientparams'));
	return;
      }
    $name = $params['name'];

    if( !isset( $params['version'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_insufficientparams'));
	return;
      }
    $version = $params['version'];

    $url = $this->GetPreference('module_repository');
    if( $url == '' )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_norepositoryurl'));
	return;
      }

    if( !isset($params['filename'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_nofilename'));
	return;
      }
    $xmlfile = $params['filename'];

    $nusoap =& $this->GetModuleInstance('nuSOAP');
    $nusoap->Load();
    $nu_soapclient = new nu_soapclient($url,false,false,false,false,false,90,90);
    if( $err = $nu_soapclient->GetError() )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  'SOAP Error: '.$err);
	return;
      }
    $about = $nu_soapclient->call('ModuleRepository.soap_moduleabout',array('name' => $xmlfile ));
    if( $err = $nu_soapclient->GetError() )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  'SOAP Error: '.$err);
	echo htmlspecialchars($nu_soapclient->response);
	return;
      }
    if( $about[0] == false )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $about[1] );
	return;
      }
    $this->smarty->assign('title',$this->Lang('abouttxt'));
    $this->smarty->assign('moduletext',$this->Lang('nametext'));
    $this->smarty->assign('vertext',$this->Lang('vertext'));
    $this->smarty->assign('xmltext',$this->Lang('xmltext'));
    $this->smarty->assign('modulename',$name);
    $this->smarty->assign('moduleversion',$version);
    $this->smarty->assign('xmlfile',$xmlfile);
    $this->smarty->assign('content',$about[1]);
    echo $this->ProcessTemplate('remotecontent.tpl');
  }


  /*----------------------------------------------------------
   _DoAdminInstallModule( $id, &$params, $returnid )
   do a module installation
   ---------------------------------------------------------*/
  function _DoAdminInstallModule($id, &$params, $returnid)
  {
    global $gCms;

    if( !isset( $params['name'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_insufficientparams'));
	return;
      }
    $name = $params['name'];

    if( !isset( $params['version'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_insufficientparams'));
	return;
      }
    $version = $params['version'];

    $url = $this->GetPreference('module_repository');
    if( $url == '' )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_norepositoryurl'));
	return;
      }

    if( !isset($params['size'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_nofilesize'));
	return;
      }
    $size = $params['size'];

    if( !isset($params['filename'] ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_nofilename'));
	return;
      }
    $xmlfile = $params['filename'];

    $nusoap =& $this->GetModuleInstance('nuSOAP');
    $nusoap->Load();
    $nu_soapclient = new nu_soapclient($url,false,false,false,false,false,90,90);
    if( $err = $nu_soapclient->GetError() )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  'SOAP Error: '.$err);
	return;
      }

    // get the dependencies from soap
    $depends = $nu_soapclient->call('ModuleRepository.soap_moduledepends',array('name' => $xmlfile ));
    if( $err = $nu_soapclient->GetError() )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  'SOAP Error: '.$err);
	echo htmlspecialchars($nu_soapclient->response);
	return;
      }
    if( $depends[0] == false )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $depends[1] );
	return;
      }
    if (! $this->_DetermineUnfulfilledDependencies($depends[1]))
		{
		
    	if( is_array($depends[1]) )
			{
			$tmp = array();
			foreach( $depends[1] as $onedep )
	  			{
	    		if ($onedep['status'] != 's') 
	      			{
	    			$tmp[] = $onedep;
	  				}
				}
				
			if (count($tmp) )
	  			{
				$txt = $this->Lang('error_depends').'<br/>';
				$txt .= '<ul>';
				foreach ( $tmp as $one )
	      			{
					switch ($one['status'])
					 	{
						case 'i':
							$txt .= '<li>'.$this->Lang('depend_install',array($one['name'],$one['version'])).'</li>';
							break;
						case 'u':
							$txt .= '<li>'.$this->Lang('depend_upgrade',array($one['name'],$one['version'])).'</li>';
							break;
						case 'a':
							$txt .= '<li>'.$this->Lang('depend_activate',$one['name']).'</li>';
							break;
						}
	      			}
            	$txt .= '</ul>';
				$txt .= '<br />'.$this->CreateLink($id,'recurseinstall',$returnid, $this->Lang('install_with_deps'), array('filename'=>$params['filename'])).'<br /><br />';
	    		$this->_DisplayErrorPage( $id, $params, $returnid, $txt );
	    		return;
	  			}
      		}
		}

    // get the xml file from soap
    $xml = '';
    $xml_file = $this->_GetRepositoryXML($nu_soapclient,$xmlfile,$size,$xml);
    if( $err = $nu_soapclient->GetError() )
      {
	echo "<pre>".htmlspecialchars($nu_soapclient->response)."</pre><br/>";
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  'SOAP Error: '.$err);
	return;
      }

    // get the md5sum from soap
    $svrmd5 = $nu_soapclient->call('ModuleRepository.soap_modulemd5sum',array('name' => $xmlfile));
    if( $err = $nu_soapclient->GetError() )
      {
	
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  'SOAP Error: '.$err);
	return;
      }


    // calculate our own md5sum
    // and compare
    $clientmd5 = md5_file( $xml_file );

    if( $clientmd5 != $svrmd5 )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $this->Lang('error_checksum'));
	echo "expected: $svrmd5 and got $clientmd5<br/>";
	return;
      }

    // woohoo, we're ready to rock and roll now
    // just gotta expand the module
    $modoperations = $gCms->GetModuleOperations();
    if( !$modoperations->ExpandXMLPackage( $xml_file ) )
      {
	$this->_DisplayErrorPage( $id, $params, $returnid,
				  $modoperations->GetLastError());
	return;
      }

    // and install it
    $result = $modoperations->InstallModule( $name, true );
    if( $result[0] == true )
      {
	if( !isset( $gCms->modules[$name]['object'] ) )
	  {
	    // hopefully this will never happen
	    $this->_DisplayErrorPage($id, $params, $returnid,
				     $this->Lang('error_moduleinstallfailed'));
	  }
	else
	  {
	    $cmsmodules = &$gCms->modules;
	    $msg = $cmsmodules[$name]['object']->InstallPostMessage();
	    if( $msg != FALSE )
	      {
		echo $msg."<br /><br />".$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager'));
	      }
	    else
	      {
		$this->Redirect( $id, 'defaultadmin', $returnid );
	      }
	  }
      }
    else
      {
	$this->_DisplayErrorPage($id, $params, $returnid,
				 $this->Lang('error_moduleinstallfailed')."&nbsp;:".$result[1]);
      }
  }


  /* Given an array of module dependencies (as returned from _DoRecursiveInstall),
	 this returns a boolean indicating whether they're all satisfied.
	 It also modified the incoming array to add a status for each dependency, indicating
	 what, if anything, needs to be done:
		s - satisfied
		a - needs to be activated
		i - needs to be installed
		u - needs to be upgraded
  */
  function _DetermineUnfulfilledDependencies(&$depends)
  {
	$satisfied = true;
	if( is_array($depends) )
		{
		$installed = $this->_GetInstalledModules(true,true);
		if (is_array($installed[1]))
			{
			// check dependencies against what is installed.
			foreach( $depends as $depkey=>$onedep )
				{
				if (isset($installed[1][$onedep['name']]) ) 
					{
					$mod = $installed[1][$onedep['name']];
					if ($mod['active'])
						{
						if (version_compare($mod['version'],$onedep['version']) >= 0 )
							{
							$depends[$depkey]['status'] = 's';
							continue;
							}
						else
							{
							$depends[$depkey]['status'] = 'u';
							}							
						}
					else
						{
						$depends[$depkey]['status'] = 'a';
						}
			  		}
				if (! isset($depends[$depkey]['status']))
					{
					$depends[$depkey]['status'] = 'i';
					}
				$satisfied = false;
				}
	    	}
		}
	return $satisfied;
	}


	function _DoRecursiveInstall($id, &$params, $returnid)
		{
		$allmods = $this->_GetRepositoryModules('',0);
		$deps = array(array('name'=>$params['name'],'version'=>$params['version'],'filename'=>$params['filename'],
							'by'=>'','size'=>$params['size']));
		if (! $allmods[0])
			{
		 	 $this->_DisplayErrorPage( $id, $params, $returnid, $allmods[1] );
		 	 return;
			}
		$ret = $this->_AddDependenciesToList($params['filename'], $allmods, $deps);
		if (!$ret[0])
			{
			$this->_DisplayErrorPage( $id, $params, $returnid, $ret[1]);
			return;
			}
		// de-dupe list
		$deps = $this->_DeDupeDependencies($deps);
		
		$this->_DetermineUnfulfilledDependencies($deps);
		$tmp = array();
		foreach( $deps as $onedep )
		  	{
		    if ($onedep['status'] != 's') 
		      	{
		    	$tmp[] = $onedep;
		  		}
			}
		if (count($tmp) > 1)
		  	{
			$txt = '<p>'.$this->Lang('notice_depends', $this->_ModNameFromFile($allmods[1],$params['filename'])).'</p>';
			$txt .= '<ul>';
			foreach ( $tmp as $one )
		      	{
				if (isset($one['version']) && $one['filename'] != $params['filename'])
					{
					switch ($one['status'])
						{
						case 'i':
							$txt .= '<li>'.$this->Lang('depend_install',array($one['name'],$one['version'])).'</li>';
							break;
						case 'u':
							$txt .= '<li>'.$this->Lang('depend_upgrade',array($one['name'],$one['version'])).'</li>';
							break;
						case 'a':
							$txt .= '<li>'.$this->Lang('depend_activate',$one['name']).'</li>';
							break;
						}
			      	}
				}
	        $txt .= '</ul>';

		    $this->smarty->assign('title_installation', $this->Lang('title_installation'));
		    $this->smarty->assign('message', $txt);
			$this->smarty->assign('link_back',$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager')));	
		
		    $this->smarty->assign('form_start',$this->CreateFormStart($id, 'doinstall', $returnid).
				$this->CreateInputHidden($id,'modlist',base64_encode(serialize($deps))));
			$this->smarty->assign('time_warning',$this->Lang('time_warning'));
			$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('install_submit')));
			$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
		
		    echo $this->ProcessTemplate('installinfo.tpl');
			return;
			}

		$params['modlist']=base64_encode(serialize($deps));
		$this->_DoInstallLoop($id, $params, $returnid);
		}
		
	
	
   function _DoInstallLoop($id, &$params, $returnid)
		{
		global $gCms;
		$db = $gCms->GetDb();
		
		if (isset($params['cancel']))
			{
			return $this->DoAction('defaultadmin', $id, $params, $returnid);
			}
		
		@set_time_limit(999);
		$installs = array_reverse(unserialize(base64_decode($params['modlist'])));
	    $url = $this->GetPreference('module_repository');
	    if( $url == '' )
			{
			$this->_DisplayErrorPage( $id, $params, $returnid,
					  $this->Lang('error_norepositoryurl'));
			return;
			}
		
		$nusoap =& $this->GetModuleInstance('nuSOAP');
	    $nusoap->Load();
	    $nu_soapclient = new nu_soapclient($url,false,false,false,false,false,90,90);
	    if( $err = $nu_soapclient->GetError() )
	      {
			$this->_DisplayErrorPage( $id, $params, $returnid,
					  $this->Lang('soaperror',$err));
			return;
	      }
		$messages = array();
		$ok = true;
		foreach($installs as $this_inst)
			{
			$thisRes = new stdClass();
			$thisRes->success = false;
			$thisRes->module_name = $this_inst['name'];
			if ($ok)
				{
				if ($this_inst['status'] == 'a')
					{
					// activating
					$query = "UPDATE ".cms_db_prefix()."modules SET active = ? WHERE module_name = ?";
					$db->Execute($query, array(1,$this_inst['name']));
					$thisRes->message = $this->Lang('action_activated',$this_inst['name']);
					$thisRes->success = true;
					}
				else if ($this_inst['status'] == 'u')
					{
					// upgrading	
					list($success, $msgs) = $this->_DoInstallOperation($this_inst, $nu_soapclient, true);
					if (!$success)
						{
						$ok = false;
						}
					else
						{
						$thisRes->success = true;
						}
					$thisRes->message = $msgs;
					}
				else if ($this_inst['status'] == 'i')
					{
					// installing
					list($success, $msgs) = $this->_DoInstallOperation($this_inst, $nu_soapclient, false);
					if (!$success)
						{
						$ok=false;
						}
					else
						{
						$thisRes->success = true;
						}
					$thisRes->message = $msgs;
					}
				}
			else
				{
				$thisRes->message = $this->Lang('error_skipping',$this_inst['name']);
				}
			if ($this_inst['status'] != 's')
				{
				$messages[] = $thisRes;
				}
			}
		if ($ok)
			{
			$this->smarty->assign('title_installation_complete', $this->Lang('title_installation_complete'));	
			}
		else
			{
			$this->smarty->assign('title_installation_complete', $this->Lang('error_moduleinstallfailed'));
			}
	    
	    $this->smarty->assign_by_ref('messages', $messages);
		$this->smarty->assign('link_back',$this->CreateLink($id,'defaultadmin',$returnid, $this->Lang('back_to_module_manager')));	
	    echo $this->ProcessTemplate('postinstall.tpl');
		return;
		
		}

	/* actual install or upgrade process; adapted from Calguy's code, extended madly */
	function _DoInstallOperation(&$mod, &$nu_soapclient, $upgrade=false)
	{
		global $gCms;
		$db = $gCms->GetDb();
		
		// get the xml file from soap
	    $xml = '';
	    $xml_file = $this->_GetRepositoryXML($nu_soapclient,$mod['filename'],$mod['size'],$xml);
	    if( $err = $nu_soapclient->GetError() )
			{
			return array(false,"<pre>".htmlspecialchars($nu_soapclient->response)."</pre><br/>");
			}

	    // get the md5sum from soap
	    $svrmd5 = $nu_soapclient->call('ModuleRepository.soap_modulemd5sum',array('name' => $mod['filename']));
	    if( $err = $nu_soapclient->GetError() )
			{
			return array(false,$this->Lang('soaperror',$err));
			}

		// calculate our own md5sum
		// and compare
		$clientmd5 = md5_file( $xml_file );

		if( $clientmd5 != $svrmd5 )
			{
		 	return array(false,$this->Lang('error_checksum',array($svrmd5,$clientmd5)));
			}

		// woohoo, we're ready to rock and roll now
		// just gotta expand the module
		$modoperations = $gCms->GetModuleOperations();
		if (!$modoperations->ExpandXMLPackage( $xml_file, 1 ) )
			{
			return array(false,$modoperations->GetLastError());
			}
		if ($upgrade)
			{
			// upgrade module
			$query = "SELECT * FROM ".cms_db_prefix()."modules WHERE module_name = ?";
		  	$dbresult = $db->Execute( $query, array( $mod['name'] ) );
		  	$row = $dbresult->FetchRow();
		  	$oldversion = $row['version'];
			if ( !$modoperations->UpgradeModule( $mod['name'], $oldversion, $mod['version'] ) )
				{
				return array(false,$this->Lang('error_upgrade',$mod['name']).' '.$modoperations->GetLastError());	
				}
			return array(true,$this->Lang('action_upgraded',array($mod['name'])));
			}
		else
			{
			// and install it
			$result = $modoperations->InstallModule( $mod['name'], true );	
			}
	 
		if( $result[0] == true )
			{
			if( !isset( $gCms->modules[$mod['name']]['object'] ) )
				{
	    		// hopefully this will never happen
				return array(false,$this->Lang('error_moduleinstallfailed'));
				}
			else
	  			{
				$cmsmodules = &$gCms->modules;
				$msg = $cmsmodules[$mod['name']]['object']->InstallPostMessage();
				return array(true,$msg);
				}
			}
		else
			{
			return array(false,$this->Lang('error_moduleinstallfailed')."&nbsp;:".$result[1]);
			}
		}
	
   function _IsDependencyKnown(&$deps,$filename)
   {
		foreach ($deps as $dkey=>$td)
			{
			if ($td['filename'] == $filename)
				{
				return true;
				}
			}
		return false;
   }

	function _DeDupeDependencies($deps)
	{
		// inconsistent behavior of array_splice in some PHP versions forces this clumsy approach
		$dl = array_reverse($deps);
		$known = array();
		$deps = array();
		foreach ($dl as $dkey=>$dval)
			{
			if (! isset($known[$dval['filename']]))
				{
				$deps[] = $dval;
				$known[$dval['filename']] = true;
				}
			}
		return array_reverse($deps);
	}
		
   function _ModNameFromFile(&$allmods,$filename)
	{
		foreach ($allmods as $tm)
			{
			if ($tm['filename'] == $filename)
				{
				return $this->Lang('mod_name_ver',array($tm['name'],$tm['version']));
				}
			}
		return $this->Lang('unknown');
	}
		
   function _AddDependenciesToList($startspec, &$allmods, &$deplist)
	{
		list($res, $this_file_deps) = $this->_GetFileDependencies($startspec);
		if (! $res)
			{
		 	 return array(false, $this_file_deps);				
			}
		if (is_array($this_file_deps))
			{
			foreach($this_file_deps as $this_dep)
				{
				$found = false;
				foreach ($allmods[1] as $tm)
					{
					if ($tm['name'] == $this_dep['name'] && version_compare($tm['version'], $this_dep['version']) >= 0)
						{
						// found the module - add to list; we'll de-dupe later
						$found = true;
						$newDep = array('filename'=>$tm['filename'],'name'=>$tm['name'],
										'version'=>$tm['version'],'by'=>$startspec,
										'size'=>$tm['size']);
						$deplist[] = $newDep;
						$this->_AddDependenciesToList($tm['filename'], $allmods, $deplist);
						}
					}
				if (! $found)
					{
				 	 return array(false, $this->Lang('error_unsatisfiable_dependency', array($this_dep['name'],$this_dep['version'], $this->_ModNameFromFile($allmods[1],$startspec) )));				
					}
				}
			}
		return array(true,'');
	}

   function _GetFileDependencies($filename)
	{
	    $url = $this->GetPreference('module_repository');
	    if( $url == '' )
	      {
			$this->_DisplayErrorPage( $id, $params, $returnid,
					  $this->Lang('error_norepositoryurl'));
			return;
	      }
	    $nusoap =& $this->GetModuleInstance('nuSOAP');
	    $nusoap->Load();
	    $nu_soapclient = new nu_soapclient($url,false,false,false,false,false,90,90);
	    if( $err = $nu_soapclient->GetError() )
	      {
			$this->_DisplayErrorPage( $id, $params, $returnid,
					  'SOAP Error: '.$err);
			return;
	      }

	    // get the dependencies from soap
	    $depends = $nu_soapclient->call('ModuleRepository.soap_moduledepends',array('name' => $filename ));
	    if( $err = $nu_soapclient->GetError() )
	      {
			$this->_DisplayErrorPage( $id, $params, $returnid,
					  'SOAP Error: '.$err);
			echo htmlspecialchars($nu_soapclient->response);
			return;
	      }
	    if( $depends[0] == false )
	      {
			$this->_DisplayErrorPage( $id, $params, $returnid,
					  $depends[1] );
			return;
	      }
		
	return ($depends);
	}


  /*----------------------------------------------------------
   _GetInstalledModules
   build a hash of info about the installed modules.
   ---------------------------------------------------------*/
  function _GetInstalledModules($include_inactive=false, $as_hash=false)
  {
    global $gCms;
	if ($include_inactive)
		{
		// try to force a reload of modules; if we fail, just use the list of active modules that we have
		$ml = $gCms->GetModuleLoader();
		if ($ml)
			{
			$ml->LoadModules(true);
			}
		}
    $results = array();
    foreach( $gCms->modules as $key => $value )
      {
	$modinstance = $value['object'];
	$details = array();
	$details['name'] = $modinstance->GetName();
	$details['description'] = $modinstance->GetDescription();
	$details['version'] = $modinstance->GetVersion();
	if ($include_inactive)
		{
		$details['active'] = $value['active'];
		}
	else
		{
		$details['active'] = true;	
		}
	// todo, here, check to see if there's write permission
	// for the httpd user to every one of the files
	// in the module's directory.
	if ($as_hash)
		{
		$results[$details['name']] = $details;	
		}
	else
		{
		$results[] = $details;	
		}
      }
    return array(true,$results);
  }


  /*----------------------------------------------------------
   _GetRepositoryXML
   Get the xml file for a specific module from the repository
   
   if the expected file size is less than the block size
   then the file will be downloaded in it's entirety
   otherwise it will be downloaded in chunks
   ---------------------------------------------------------*/
  function _GetRepositoryXML( &$nu_soapclient, $filename, $size, &$xml )
  {
    $orig_chunksize = $this->GetPreference('dl_chunksize',256);
    $chunksize = $orig_chunksize * 1024;
    if( $size <= $chunksize ) 
      {
	$tmpname = tempnam(TMP_CACHE_LOCATION,'modmgr_');
	if( $tmpname === FALSE )
	  {
	    return false;
	  }
	$fh = fopen($tmpname,'w');
	// we're downloading at one shot
	$nbytes = @fwrite($fh, $nu_soapclient->call('ModuleRepository.soap_modulexml',
				    array('name' => $filename )));
	@fflush($fh);
	@fclose($fh);
	if( $nu_soapclient->GetError() )
	  {
	    return false;
	  }
      }
    else
      {
	global $gCms;
	$tmpname = tempnam(TMP_CACHE_LOCATION,'modmgr_');
	if( $tmpname === FALSE )
	  {
	    return false;
	  }

	// we're downloading in chunks
	// to a temporary file someplace
	// that we will delete afterwards
	$fh = fopen($tmpname,'w');
	$nchunks = (int)($size / $chunksize) + 1;
	for( $i = 0; $i < $nchunks; $i++ )
	  {
 	    $data = $nu_soapclient->call('ModuleRepository.soap_modulegetpart',
 					 array('name' => $filename,
 					       'partnum' => $i,
 					       'sizekb' => $orig_chunksize));
 	    if( $nu_soapclient->GetError() )
 	      {
		echo $nu_soapclient->GetError()."<br/><br/>";
		echo htmlspecialchars($nu_soapclient->response);
 		@fclose($fh);
 		@unlink($tmpname);
 		return false;
 	      }
	    $data = base64_decode( $data );
 	    $nbytes = @fwrite($fh,$data);
// 	    if( $nbytes != strlen($data) )
// 	      {
// 		// ugh-oh
// 	      }
	  }
	// we got here so everything theoretically worked
	@fflush($fh);
	@fclose($fh);
	//$xml = @file_get_contents($tmpname);
      }
    return $tmpname;
  }


  /*----------------------------------------------------------
   _GetRepositoryModules
   Get the xml modules from the repository, in an array of
   hashes, each hash has name and version keys
   ---------------------------------------------------------*/
  function _GetRepositoryModules($prefix = '',$newest = 1)
  {
    global $CMS_VERSION;

    $url = $this->GetPreference('module_repository');
    if( $url == '' )
      {
	return array(false,$this->Lang('error_norepositoryurl'));
      }

    $nusoap =& $this->GetModuleInstance('nuSOAP');
    $nusoap->Load();
    $nu_soapclient = new nu_soapclient($url,false,false,false,false,false,90,90);
    if( $err = $nu_soapclient->GetError() )
      {
	return array(false,$this->Lang('error_nosoapconnect'));
      }

    $allmoduledetails = array();
    $repversion = $nu_soapclient->call('ModuleRepository.soap_version');
    if( $err = $nu_soapclient->GetError() )
      {
	return array(false,$this->Lang('error_soaperror').' ('.$url.'): '.$err);
      }
    if( version_compare( $repversion, MINIMUM_REPOSITORY_VERSION ) < 0 )
      {
	return array(false,$this->Lang('error_minimumrepository'));
      }

    $qparms = array();
    if( !empty($prefix) )
      {
	$qparms['prefix'] = $prefix;
      }
    $qparms['newest'] = $newest;
    $qparms['clientcmsversion'] = $CMS_VERSION;
    $allmoduledetails = $nu_soapclient->call('ModuleRepository.soap_moduledetailsgetall',$qparms);
    if( $err = $nu_soapclient->GetError() )
      {
	return array(false,$this->Lang('error_soaperror').' ('.$url.'): '.$err);
      }
//     if( !is_array( $allmoduledetails ) || count( $allmoduledetails ) == 0 )
//        {
//  	return array(false,$this->Lang('error_connectnomodules').' ('.$url.'): '.$err);
//        }
    return array(true,$allmoduledetails);
  }
}

?>
