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

class ModuleManager extends CmsModuleBase
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
    return '1.3.2';
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
	function setup()
	{
		$this->includes = array('nusoap' => 'nuSOAP');
		$this->restrict_unknown_params();
		
		$this->create_parameter('module_name', '', '', FILTER_SANITIZE_STRING);
		
		/*
		$this->SetParameterType('curletter',CLEAN_STRING);
		$this->SetParameterType('name',CLEAN_STRING);
		$this->SetParameterType('version',CLEAN_STRING);
		$this->SetParameterType('filename',CLEAN_STRING);
		$this->SetParameterType('size',CLEAN_INT);
		*/
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
    cms_smarty()->assign('title_error', $this->Lang('error'));
    cms_smarty()->assign_by_ref('message', $message);
	  
    // Display the populated template
    echo $this->Template->process('error.tpl');
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
    return "1.6-beta1";
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
	      $this->_DoAdminInstallModule( $id, $params, $returnid );
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
    if( $this->Preference->get('onlynewest',"1") == "1" )
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
      }    

    // get the modules available in the repository
    $repmodules = '';
    {
      $newest = $this->Preference->get('onlynewest',1);
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
		//$letters[] = $this->CreateLink($id,'defaultadmin',$returnid, $i, array('curletter'=>$i,'active_tab'=>'modules'));
		$letters[] = $this->Url->link(array('action' => 'defaultadmin', 'contents' => $i, 'params' => array('curletter' => $i, 'active_tab' => 'modules')));
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
	    /*$onerow->helplink = $this->CreateLink( $id, 'modulehelp', $returnid,
						   $this->Lang('helptxt'), 
						   array('name' => $row['name'],
							 'version' => $row['version'],
							 'filename' => $row['filename']));*/
		$onerow->helplink = $this->Url->link(array('action' => 'modulehelp', 'contents' => $this->lang('helptxt'), 'params' => array('name' => $row['name'], 'version' => $row['version'], 'filename' => $row['filename'])));
	    /*$onerow->dependslink = $this->CreateLink( $id, 'moduledepends', $returnid,
						   $this->Lang('dependstxt'), 
						   array('name' => $row['name'],
							 'version' => $row['version'],
							 'filename' => $row['filename']));*/
		$onerow->dependslink = $this->Url->link(array('action' => 'moduledepends', 'contents' => $this->lang('dependstxt'), 'params' => array('name' => $row['name'], 'version' => $row['version'], 'filename' => $row['filename'])));
		/*$onerow->aboutlink = $this->CreateLink( $id, 'moduleabout', $returnid,
						    $this->Lang('abouttxt'), 
						    array('name' => $row['name'],
							  'version' => $row['version'],
							  'filename' => $row['filename']));*/
		$onerow->aboutlink = $this->Url->link(array('action' => 'moduleabout', 'contents' => $this->lang('abouttxt'), 'params' => array('name' => $row['name'], 'version' => $row['version'], 'filename' => $row['filename'])));

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
				/*$onerow->status = $this->CreateLink( $id, 'installmodule', $returnid,
							   $this->Lang('download'), 
							   array('name' => $row['name'],
								 'version' => $row['version'],
								 'filename' => $row['filename'],
								 'size' => $row['size']));*/
				$onerow->status = $this->Url->link(array('action' => 'installmodule', 'contents' => $this->lang('download'), 'params' => array('name' => $row['name'], 'version' => $row['version'], 'filename' => $row['filename'], 'size' => $row['size'])));
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

	cms_smarty()->assign('items', $rowarray);
	cms_smarty()->assign('itemcount', count($rowarray));
      }
    else
      {
	cms_smarty()->assign('message', $this->Lang('error_connectnomodules'));
      }
    // Setup search form
    $searchstart = $this->Form->form_start(array('action' => 'searchmod'));
    $searchend = $this->Form->form_end();
    $searchfield = $this->Form->input_text(array('action' => 'search_input', 'contents' => "Doesn't Work", 'size' => 30, 'maxlength' => 100)); //todo
    $searchsubmit = $this->Form->input_submit(array('name' => 'submit', 'value' => 'Search')); // todo -- $this->Lang('search'));
    cms_smarty()->assign('search',$searchstart.$searchfield.$searchsubmit.$searchend);

    // and display our page
    cms_smarty()->assign('letters', implode('&nbsp;',array_values($letters)));
    cms_smarty()->assign('nametext',$this->Lang('nametext'));
    cms_smarty()->assign('vertext',$this->Lang('vertext'));
    cms_smarty()->assign('sizetext',$this->Lang('sizetext'));
    cms_smarty()->assign('statustext',$this->Lang('statustext'));
    echo $this->Template->process('adminpanel.tpl');
  }


  /*---------------------------------------------------------
   _DisplayAdminPrefsTab()
   ---------------------------------------------------------*/
  function _DisplayAdminPrefsTab($id, &$params, $returnid)
  {
    cms_smarty()->assign('formstart', $this->Form->form_start(array('action' => 'setprefs')));
    cms_smarty()->assign('formend', $this->Form->form_end());
    cms_smarty()->assign('prompt_url', $this->lang('prompt_repository_url'));
    cms_smarty()->assign('input_url', $this->Form->input_text(array('action' => 'url', 'contents' => $this->Preference->get('module_repository'), 'size' => 80, 'maxlength' => 255)));
    cms_smarty()->assign('extratext_url', $this->lang('text_repository_url'));
    
    cms_smarty()->assign('prompt_onlynewest', $this->lang('onlynewesttext'));
    cms_smarty()->assign('input_onlynewest', $this->Form->input_checkbox(array('action' => 'onlynewest', 'value' => "1", 'checked' => $this->Preference->get("onlynewest","1"))));
    
    cms_smarty()->assign('prompt_reseturl', $this->lang('prompt_reseturl'));
    cms_smarty()->assign('input_reseturl', $this->Form->input_submit(array('name' => 'reseturl', 'value' => $this->lang('reset'))));
    
    

    cms_smarty()->assign('prompt_chunksize', $this->lang('prompt_dl_chunksize'));
    cms_smarty()->assign('input_chunksize', $this->Form->input_text(array('action' => 'input_dl_chunksize', 'contents' => $this->Preference->get('dl_chunksize', 256), 'size' => 3, 'maxlength' => 3)));
    cms_smarty()->assign('extratext_chunksize', $this->lang('text_dl_chunksize'));

    cms_smarty()->assign('prompt_resetcache', $this->lang('prompt_resetcache'));
    cms_smarty()->assign('input_resetcache', $this->Form->input_submit(array('name' => 'resetcache', 'value' => $this->lang('reset'))));
			  
    cms_smarty()->assign('submit', $this->Form->input_submit(array('name' => 'submit', 'value' => $this->lang('submit'))));
    cms_smarty()->assign('prompt_otheroptions', $this->lang('prompt_otheroptions'));
    cms_smarty()->assign('prompt_settings', $this->lang('prompt_settings'));
    echo $this->Template->process('adminprefs.tpl');
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

    $this->nusoap->Load();
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

    cms_smarty()->assign('title',$this->Lang('dependstxt'));
    cms_smarty()->assign('moduletext',$this->Lang('nametext'));
    cms_smarty()->assign('vertext',$this->Lang('vertext'));
    cms_smarty()->assign('xmltext',$this->Lang('xmltext'));
    cms_smarty()->assign('modulename',$name);
    cms_smarty()->assign('moduleversion',$version);
    cms_smarty()->assign('xmlfile',$xmlfile);

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
    cms_smarty()->assign('content',$txt);
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

    $this->nusoap->Load();
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
    cms_smarty()->assign('title',$this->Lang('helptxt'));
    cms_smarty()->assign('moduletext',$this->Lang('nametext'));
    cms_smarty()->assign('vertext',$this->Lang('vertext'));
    cms_smarty()->assign('xmltext',$this->Lang('xmltext'));
    cms_smarty()->assign('modulename',$name);
    cms_smarty()->assign('moduleversion',$version);
    cms_smarty()->assign('xmlfile',$xmlfile);
    cms_smarty()->assign('content',$help[1]);
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

    $this->nusoap->Load();
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
    cms_smarty()->assign('title',$this->Lang('abouttxt'));
    cms_smarty()->assign('moduletext',$this->Lang('nametext'));
    cms_smarty()->assign('vertext',$this->Lang('vertext'));
    cms_smarty()->assign('xmltext',$this->Lang('xmltext'));
    cms_smarty()->assign('modulename',$name);
    cms_smarty()->assign('moduleversion',$version);
    cms_smarty()->assign('xmlfile',$xmlfile);
    cms_smarty()->assign('content',$about[1]);
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

    $this->nusoap->Load();
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
    if( is_array($depends[1]) )
      {
	global $gCms;
	// check dependencies against what is installed.
	$tmp = array();
	foreach( $depends[1] as $onedep )
	  {
	    if( isset($gCms->modules[$onedep['name']]) ) 
	      {
		$mod =& $gCms->modules[$onedep['name']]['object'];
		if( is_object($mod) )
		  {
		    if( version_compare($mod->GetVersion(),$onedep['version']) >= 0 )
		      {
			continue;
		      }
		  }
	      }
	    $tmp[] = $onedep;
	  }

	if( count($tmp) )
	  {
	    $txt = $this->Lang('error_depends').'<br/>';
	    $txt .= '<ul>';
	    foreach( $tmp as $one )
	      {
		$txt  .= '<li>'.$one['name'].' => '.$one['version'].'<br/>';
	      }
            $txt .= '</ul>';
	    $this->_DisplayErrorPage( $id, $params, $returnid, $txt );
	    return;
	  }
      }

    // get the xml file from soap
    $xml = '';
    $result = $this->_GetRepositoryXML($nu_soapclient,$xmlfile,$size,$xml);
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
    $clientmd5 = md5( $xml );

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
    if( !$modoperations->ExpandXMLPackage( $xml ) )
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
		echo $msg;
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


  /*----------------------------------------------------------
   _GetInstalledModules
   build a hash of info about the installed modules.
   ---------------------------------------------------------*/
  function _GetInstalledModules()
  {
    global $gCms;
    $results = array();
    foreach( $gCms->modules as $key => $value )
      {
	$modinstance = $value['object'];
	$details = array();
	$details['name'] = $modinstance->GetName();
	$details['description'] = $modinstance->GetDescription();
	$details['version'] = $modinstance->GetVersion();
	// todo, here, check to see if there's write permission
	// for the httpd user to every one of the files
	// in the module's directory.
	$results[] = $details;
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
	// we're downloading at one shot
	$xml = $nu_soapclient->call('ModuleRepository.soap_modulexml',
				    array('name' => $filename ));
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
	$xml = @file_get_contents($tmpname);
      }
    return true;
  }


  /*----------------------------------------------------------
   _GetRepositoryModules
   Get the xml modules from the repository, in an array of
   hashes, each hash has name and version keys
   ---------------------------------------------------------*/
  function _GetRepositoryModules($prefix = '',$newest = 1)
  {
    global $CMS_VERSION;

    $url = $this->Preference->get('module_repository');
    if( $url == '' )
      {
	return array(false,$this->Lang('error_norepositoryurl'));
      }

    $this->nusoap->Load();
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
