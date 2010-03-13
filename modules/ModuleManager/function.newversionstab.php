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

global $CMS_VERSION;
$caninstall = true;
if( FALSE == can_admin_upload() )
{
	echo '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror">'.$this->Lang('error_permissions').'</p></div></div>';
	$caninstall = false;
}

$modules = array_keys($gCms->modules);
if( !count($modules ) )
  {
    $smarty->assign('nvmessage',$this->Lang('error_nomodules'));
  }
else
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
    $repversion = $nu_soapclient->call('ModuleRepository.soap_version');
    if( $err = $nu_soapclient->GetError() )
    {
      $this->_DisplayErrorPage( $id, $params, $returnid,$this->Lang('error_soaperror').' ('.$url.'): '.$err);
      return;
    }
    if( version_compare( $repversion, MINIMUM_REPOSITORY_VERSION ) < 0 )
    {
      $this->_DisplayErrorPage( $id, $params, $returnid,$this->Lang('error_minimumrepository'));
      return;
    }
    $qparms['names'] =  implode(',',$modules);
    $qparms['newest'] = '1';
    $qparms['clientcmsversion'] = $CMS_VERSION;
    $versions = $nu_soapclient->call('ModuleRepository.soap_upgradelistgetall',$qparms);
    if( $err = $nu_soapclient->GetError() )
      {
        $this->_DisplayErrorPage( $id, $params, $returnid,
                'SOAP Error: '.$err);
        echo htmlspecialchars($nu_soapclient->response);
        return;
      }
    if( !$versions )
      {
        $this->_DisplayErrorPage( $id, $params, $returnid,
                $this->Lang('error_nomatchingmodules') );
        return;
      }
      
    $moduledir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules";
    $writable = is_writable( $moduledir );	
    $results = array();
    $rowclass = 'row1';
    foreach( $versions as $row )
    {
      $txt = '';
      $onerow = new stdClass();
      $mod =& $this->GetModuleInstance($row['name']);
      if( !is_object($mod) )
      {
        $onerow->txt = $this->Lang('error_module_object',$row['name']);
      }
      else
      {
        $mver = $mod->GetVersion();
        if( version_compare($row['version'],$mver) > 0 )
        {
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
      $onerow->size = (int)((float) $row['size'] / 1024.0 + 0.5);
	    $onerow->rowclass = $rowclass;
	    if( isset( $row['description'] ) )
      {
        $onerow->description=$row['description'];
      }
	    $rowarray[] = $onerow;
	    ($rowclass == "row1" ? $rowclass = "row2" : $rowclass = "row1");
          $onerow->txt= $this->Lang('upgrade_available',$row['version'],$mver);
          $moddir = $moduledir.DIRECTORY_SEPARATOR.$row['name'];
          if( (($writable && is_dir($moddir) && is_directory_writable( $moddir )) ||
               ($writable && !file_exists( $moddir ) )) && $caninstall )
          {
            	if( (!empty($row['maxcmsversion']) && 
                 version_compare($CMS_VERSION,$row['maxcmsversion']) > 0) ||
                (!empty($row['mincmsversion']) &&
                 version_compare($CMS_VERSION,$row['mincmsversion']) < 0) )
              {
                $onerow->status = 'incompatible';
              }else
              {
                $onerow->status = $this->CreateLink( $id, 'upgrademodule', $returnid,
                             $this->Lang('upgrade'), 
                             array('name' => $row['name'],
                             'version' => $row['version'],
                             'filename' => $row['filename'],
                             'size' => $row['size']));
              }
          }
          else
          {
            $onerow->status = $this->Lang('cantdownload');
          }
        }
      }

      if( !empty($onerow->txt) )
      {
        $results[] = $onerow;
      }
    }

    if( !count($results) )
    {
      $smarty->assign('nvmessage',$this->Lang('all_modules_up_to_date'));
    }
    else
    {
      $smarty->assign('updatestxt',$this->Lang('available_updates'));
      $smarty->assign('items',$results);
      $smarty->assign('itemcount', count($results));
    }
  }

  $smarty->assign('nametext',$this->Lang('nametext'));
  $smarty->assign('vertext',$this->Lang('vertext'));
  $smarty->assign('sizetext',$this->Lang('sizetext'));
  $smarty->assign('statustext',$this->Lang('statustext'));
		
  echo $this->Template->process('newversionstab.tpl');

# EOF
?>
