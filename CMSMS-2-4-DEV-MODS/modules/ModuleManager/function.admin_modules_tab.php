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
# Visit our homepage at: http://www.cmsmadesimple.org
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
if( !$this->CheckPermission('Modify Modules') ) exit;

if( !modmgr_utils::is_connection_ok() ) {
  echo $this->ShowErrors($this->Lang('error_request_problem'));
  return;
}

$caninstall = true;
if( FALSE == can_admin_upload() ) {
  echo '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror">'.$this->Lang('error_permissions').'</p></div></div>';
  $caninstall = false;
}

$curletter = 'A';
if( isset( $params['curletter'] ) ) {
  $curletter = $params['curletter'];
  $_SESSION['mm_curletter'] = $curletter;
}
else if (isset($_SESSION['mm_curletter'])) {
  $curletter = $_SESSION['mm_curletter'];
}


// get the modules available in the repository
$repmodules = '';
{
  $result = modulerep_client::get_repository_modules($curletter);
  if( ! $result[0] ) {
    $this->_DisplayErrorPage( $id, $params, $returnid, $result[1] );
    return;
  }
  $repmodules = $result[1];
}

// get the modules that are already installed
$instmodules = '';
{
  $result = modmgr_utils::get_installed_modules();
  if( ! $result[0] ) {
    $this->_DisplayErrorPage( $id, $params, $returnid, $result[1] );
    return;
  }

  $instmodules = $result[1];
}

// build a letters list
$letters = array();
$tmp = explode(',','A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z');
foreach( $tmp as $i ) {
  $letters[$i] = $this->create_url($id,'defaultadmin',$returnid,array('curletter'=>$i,'__activetab'=>'modules'));
}

// cross reference them
$data = array();
if( count($repmodules ) ) $data = modmgr_utils::build_module_data($repmodules, $instmodules);
if( count( $data ) ) {
  $size = count($data);

  // check for permissions
  $moduledir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules";
  $writable = is_writable( $moduledir );

  // build the table
  $rowarray = array();
  $newestdisplayed="";
  foreach( $data as $row ) {
    $onerow = new stdClass();
    foreach( $row as $key => $value ) {
      $onerow->$key = $value;
    }
    $onerow->name = $this->CreateLink( $id, 'modulelist', $returnid, $row['name'], array('name'=>$row['name']));
    $onerow->version = $row['version'];
    $onerow->help_url = $this->create_url( $id, 'modulehelp', $returnid,
					   array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename']));

    $onerow->helplink = $this->CreateLink( $id, 'modulehelp', $returnid,
					   $this->Lang('helptxt'),
					   array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename']));

    $onerow->depends_url = $this->create_url( $id, 'moduledepends', $returnid,
					      array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename']));

    $onerow->dependslink = $this->CreateLink( $id, 'moduledepends', $returnid,
					      $this->Lang('dependstxt'),
					      array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename']));

    $onerow->about_url = $this->create_url( $id, 'moduleabout', $returnid,
					    array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename']));

    $onerow->aboutlink = $this->CreateLink( $id, 'moduleabout', $returnid,
					    $this->Lang('abouttxt'),
					    array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename']));
    $onerow->age = modmgr_utils::get_status($row['date']);
    $onerow->date = $row['date'];
    $onerow->downloads = isset($row['downloads'])?$row['downloads']:$this->Lang('unknown');
    $onerow->candownload = FALSE;

    switch( $row['status'] ) {
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
	     ($writable && !file_exists( $mod ) )) && $caninstall ) {
	  $onerow->candownload = TRUE;
	  $onerow->status = $this->CreateLink( $id, 'installmodule', $returnid,
					       $this->Lang('download'),
					       array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename'],
						     'size' => $row['size']));
	}
	else {
	  $onerow->status = $this->Lang('cantdownload');
	}
	break;
      }
    case 'upgrade':
      {
	$mod = $moduledir.DIRECTORY_SEPARATOR.$row['name'];
	if( (($writable && is_dir($mod) && is_directory_writable( $mod )) ||
	     ($writable && !file_exists( $mod ) )) && $caninstall ) {
	  $onerow->candownload = TRUE;
	  $onerow->status = $this->CreateLink( $id, 'installmodule', $returnid,
					       $this->Lang('upgrade'),
					       array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename'],
						     'size' => $row['size']));
	}
	else {
	  $onerow->status = $this->Lang('cantdownload');
	}
	break;
      }
    }

    $onerow->size = (int)((float) $row['size'] / 1024.0 + 0.5);
    if( isset( $row['description'] ) ) $onerow->description=$row['description'];
    $rowarray[] = $onerow;
  } // for

  $smarty->assign('items', $rowarray);
  $smarty->assign('itemcount', count($rowarray));
}
else {
  $smarty->assign('message', $this->Lang('error_connectnomodules'));
}

// Setup search form
$searchstart = $this->CreateFormStart( $id, 'searchmod', $returnid );
$searchend = $this->CreateFormEnd();
$searchfield = $this->CreateInputText($id, 'search_input', "Doesn't Work",  30, 100); //todo
$searchsubmit = $this->CreateInputSubmit( $id, 'submit', 'Search'); // todo -- $this->Lang('search'));
$smarty->assign('search',$searchstart.$searchfield.$searchsubmit.$searchend);

// and display our page
$smarty->assign('letter_urls',$letters);
$smarty->assign('curletter',$curletter);
$smarty->assign('nametext',$this->Lang('nametext'));
$smarty->assign('vertext',$this->Lang('vertext'));
$smarty->assign('sizetext',$this->Lang('sizetext'));
$smarty->assign('statustext',$this->Lang('statustext'));
echo $this->processTemplate('adminpanel.tpl');

#
# EOF
#
?>