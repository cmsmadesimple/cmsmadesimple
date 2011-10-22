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
if( !isset($gCms) ) exit;

global $CMS_VERSION;
$caninstall = true;
if( FALSE == can_admin_upload() )
{
  echo '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror">'.$this->Lang('error_permissions').'</p></div></div>';
  $caninstall = false;
}

// see if there are saved results
$search_data = '';
$term = '';
$advanced = 0;
if( isset($_SESSION['modmgr_search']) )
  {
    $search_data = unserialize($_SESSION['modmgr_search']);
  }
if( isset($_SESSION['modmgr_searchterm']) )
  {
    $term = $_SESSION['modmgr_searchterm'];
  }
if( isset($_SESSION['modmgr_searchadv']) )
  {
    $advanced = $_SESSION['modmgr_searchadv'];
  }

// get the modules that are already installed
$instmodules = '';
{
  $result = modmgr_utils::get_installed_modules();
  if( ! $result[0] )
    {
      $this->_DisplayErrorPage( $id, $params, $returnid, $result[1] );
      return;
    }
      
  $instmodules = $result[1];
}

if( isset($params['submit']) )
  {
    $url = $this->GetPreference('module_repository');
    $error = 0;
    $term = trim($params['term']);
    if( strlen($term) < 3 )
      {
	echo $this->ShowErrors($this->Lang('error_searchterm'));
	return;
      }
    $advanced = (int)$params['advanced'];
    
    $res = modulerep_client::search($term,$advanced);
    if( !is_array($res) || $res[0] == FALSE )
      {
	echo $this->ShowErrors($this->Lang('error_search').' '.$res[1]);
	return;
      }

    if( !is_array($res[1]) )
      {
	echo $this->ShowMessage($this->Lang('search_noresults'));
	return;
      }

    $res = $res[1];
    $data = array();
    if( count($res) )
      {
	$res = modmgr_utils::build_module_data($res, $instmodules);
      }

    $search_data = array();
    for( $i = 0; $i < count($res); $i++ )
      {
	$row =& $res[$i];
	$obj = new stdClass();
	foreach( $row as $k => $v )
	  {
	    $obj->$k = $v;
	  }
	
	$obj->name = $this->CreateLink( $id, 'modulelist', $returnid, $row['name'],
					array('name'=>$row['name']));

	$obj->dependslink = $this->CreateLink( $id, 'moduledepends', $returnid,
					       $this->Lang('dependstxt'), 
					       array('name' => $row['name'],
						     'version' => $row['version'],
						     'filename' => $row['filename']));

	$obj->helplink = $this->CreateLink( $id, 'modulehelp', $returnid,
					    $this->Lang('helptxt'),
					    array('name'=>$row['name'],
						  'version'=>$row['version'],
						  'filename'=>$row['filename']));
	$obj->size = (int)((float) $row['size'] / 1024.0 + 0.5);


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
		  $obj->status = $this->CreateLink( $id, 'installmodule', $returnid,
						       $this->Lang('download'), 
						       array('name' => $row['name'],
							     'version' => $row['version'],
							     'filename' => $row['filename'],
							     'size' => $row['size']));
		}

	      else
		{
		  $obj->status = $this->Lang('cantdownload');
		}
	      break;
	    }
	  case 'upgrade':
	    {
	      $mod = $moduledir.DIRECTORY_SEPARATOR.$row['name'];
	      if( (($writable && is_dir($mod) && is_directory_writable( $mod )) ||
		   ($writable && !file_exists( $mod ) )) && $caninstall )
		{
		  $obj->status = $this->CreateLink( $id, 'upgrademodule', $returnid,
						       $this->Lang('upgrade'), 
						       array('name' => $row['name'],
							     'version' => $row['version'],
							     'filename' => $row['filename'],
							     'size' => $row['size']));
		}
	      else
		{
		  $obj->status = $this->Lang('cantdownload');
		}
	      break;
	    }
	  }

// 	$moddir = $moduledir.DIRECTORY_SEPARATOR.$row['name'];
// 	if( (($writable && is_dir($moddir) && is_directory_writable( $moddir )) ||
// 	     ($writable && !file_exists( $moddir ) )) && $caninstall )
// 	  {
// 	    if( (!empty($row['maxcmsversion']) && version_compare($CMS_VERSION,$row['maxcmsversion']) > 0) ||
// 		(!empty($row['mincmsversion']) && version_compare($CMS_VERSION,$row['mincmsversion']) < 0) )
// 	      {
// 		$obj->status = 'incompatible';
// 	      }
// 	    else
// 	      {
// 		$obj->status = $this->CreateLink( $id, 'installmodule', $returnid,
// 						     $this->Lang('upgrade'), 
// 						     array('name' => $row['name'],
// 							   'version' => $row['version'],
// 							   'filename' => $row['filename'],
// 							   'size' => $row['size'],
// 							   'active_tab'=>'newversions',
// 							   'reset_prefs' => 1));
// 	      }
// 	  }
// 	else
// 	  {
// 	    $obj->status = $this->Lang('cantdownload');
// 	  }

	$obj->rowclass = $rowclass;
	if( isset( $row['description'] ) )
	  {
	    $obj->description=$row['description'];
	  }
	
	$search_data[] = $obj;
      }
    $_SESSION['modmgr_search'] = serialize($search_data);
    $_SESSION['mogmgr_searchterm'] = $term;
    $_SESSION['modmgr_searchadv'] = $params['advanced'];
  }

if( is_array($search_data) )
  {  
    $smarty->assign('search_data',$search_data);
  }
$smarty->assign('term',$term);
$smarty->assign('advanced',$advanced);
$smarty->assign('formstart',$this->CreateFormStart($id,'defaultadmin','','post','',false,'',
						   array('active_tab'=>'search')));
$smarty->assign('formend',$this->CreateFormEnd());
$smarty->assign('actionid',$id);
$smarty->assign('mod',$this);

echo $this->ProcessTemplate('admin_search_tab.tpl');
#
# EOF
#
?>