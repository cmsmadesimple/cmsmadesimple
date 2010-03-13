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
{
  // upgrade a module
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
  
  $xmlfile = $name.'-'.$version.'.xml';

  $nusoap =& $this->GetModuleInstance('nuSOAP');
  $nusoap->Load();
  $nu_soapclient =& new nu_soapclient($url,false,false,false,false,false,0,90);
  if( $err = $nu_soapclient->GetError() )
    {
      $this->_DisplayErrorPage( $id, $params, $returnid,
				'SOAP Error: '.$err);
      return;
    }
  
  // get the xml file from soap
  $xml = $nu_soapclient->call('ModuleRepository.soap_modulexml',array('name' => $xmlfile ));
  if( $err = $nu_soapclient->GetError() )
    {
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
      return;
    }
  
  // woohoo, we're ready to rock and roll now
  // just gotta expand the module
  $modoperations = $gCms->GetModuleOperations();
  if( !$modoperations->ExpandXMLPackage( $xml, 1 ) )
    {
      $this->_DisplayErrorPage( $id, $params, $returnid,
				$modoperations->GetLastError());
      return;
    }

  // we get the version number from the database
  // just incase the module is inactive or something and therefore
  // not loaded.
  $query = "SELECT * FROM ".cms_db_prefix()."modules WHERE module_name = ?";
  $dbresult = $db->Execute( $query, array( $name ) );
  $row = $dbresult->FetchRow();
  $oldversion = $row['version'];

  if( !isset( $gCms->modules[$name] ) )
    {
      echo "DEBUG: module not loaded<br/>";
      if( !$modoperations->LoadNewModule( $name ) )
	{
	  // error
	}
    }

  if( !$modoperations->UpgradeModule( $name, $oldversion, $version ) )
    {
      // error
    }

  // and redirect back to the start
  $this->Redirect( $id, 'defaultadmin', $returnid );
}
?>