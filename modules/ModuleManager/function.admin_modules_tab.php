<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;
if( !$this->CheckPermission('Modify Modules') ) exit;

if( !modmgr_utils::is_connection_ok() ) {
  echo $this->ShowErrors($this->Lang('error_request_problem'));
  return;
}

$caninstall = true;
if( FALSE == can_admin_upload() ) {
  echo $this->ShowErrors($this->Lang('error_permissions'));
  $caninstall = false;
}

$curletter = 'featured';
if( isset( $params['curletter'] ) ) {
  $curletter = $params['curletter'];
  $_SESSION['mm_curletter'] = $curletter;
}
else if (isset($_SESSION['mm_curletter'])) {
  $curletter = $_SESSION['mm_curletter'];
}

// build a letters list
$letters = array();
$letters['featured'] = $this->create_url($id,'defaultadmin',$returnid,array('curletter'=>'featured','__activetab'=>'modules'));
$tmp = explode(',','A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z');
foreach( $tmp as $i ) {
  $letters[$i] = $this->create_url($id,'defaultadmin',$returnid,array('curletter'=>$i,'__activetab'=>'modules'));
}

// If Featured is selected, show the featured card layout
if( $curletter === 'featured' ) {
  if( !$this->GetPreference('notice_dismissed', 0) ) {
    echo '<p id="mm-community-notice" class="pageinfo" style="margin:0 0 10px;padding:6px 10px;background:#f0f4f8;border-left:3px solid #5b9bd5;font-size:0.9em;">'.$this->Lang('community_notice').'<span onclick="this.parentNode.style.display=\'none\';$.get(\''.$this->create_url($id,'setprefs',$returnid,array('dismiss_notice'=>1)).'\')" style="cursor:pointer;float:right;font-weight:bold;margin-left:10px;">&times;</span></p>';
  }
  include(dirname(__FILE__).'/function.admin_featured_tab.php');
  return;
}

// get the modules available in the repository
$repmodules = '';
{
  $result = modmgr_rep_client::get_repository_modules($curletter);
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
    $onerow->rawname = $row['name'];
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
    $onerow->age = modmgr_utils::get_status($row['date'], $row);
    $onerow->date = $row['date'];
    $onerow->downloads = isset($row['downloads'])?$row['downloads']:$this->Lang('unknown');
    $onerow->candownload = FALSE;
    $onerow->untested = isset($row['untested']) ? $row['untested'] : false;
    $onerow->incompatible = ($row['status'] === 'incompatible');
    $onerow->cmsms_incompatible = (!empty($row['cmsms_max']) && version_compare(CMS_VERSION, $row['cmsms_max'].'.99') > 0);
    $onerow->php_incompatible = (!empty($row['php_max']) && version_compare(PHP_VERSION, $row['php_max'].'.99') > 0);
    if( !isset($onerow->cmsms_tested) ) $onerow->cmsms_tested = '';

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

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('adminpanel.tpl'), null, null, $smarty);
$tpl->assign('letter_urls',$letters);
$tpl->assign('curletter',$curletter);
$tpl->assign('show_incompatible',$this->GetPreference('show_incompatible',0));
$tpl->assign('nametext',$this->Lang('nametext'));
$tpl->assign('vertext',$this->Lang('vertext'));
$tpl->assign('sizetext',$this->Lang('sizetext'));
$tpl->assign('statustext',$this->Lang('statustext'));
$tpl->display();

#
# EOF
#
?>