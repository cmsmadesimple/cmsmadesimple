<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;
if( !$this->CheckPermission('Modify Modules') ) exit;

$_SESSION[$this->GetName()]['active_tab'] = 'modules';
if( !isset($params['name']) ) $this->Redirect($id,'defaultadmin');

$prefix = trim($params['name']);
$repmodules = modmgr_rep_client::get_repository_modules($prefix,FALSE,TRUE);
if( !is_array($repmodules) || $repmodules[0] === FALSE ) $this->Redirect($id,'defaultadmin'); // for some reason, nothing matched.

$repmodules = $repmodules[1];
$instmodules = '';
{
  $result = modmgr_utils::get_installed_modules();
  if( ! $result[0] ) {
    $this->_DisplayErrorPage( $id, $params, $returnid, $result[1] );
    return;
  }
    
  $instmodules = $result[1];
}

$caninstall = true;
if( FALSE == can_admin_upload() ) {
  echo $this->ShowErrors($this->Lang('error_permissions'));
  $caninstall = false;
}

$data = modmgr_utils::build_module_data($repmodules,$instmodules,false);
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
    $onerow->date = $row['date'];
    $onerow->age = modmgr_utils::get_status($row['date'], $row);
    $onerow->downloads = $row['downloads'];
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
	  $onerow->status = $this->CreateLink( $id, 'installmodule', $returnid,
					       $this->Lang('download'), 
					       array('name' => $row['name'],
						     'version' => $row['version'],
						     'filename' => $row['filename'],
						     'size' => $row['size']));
	}
	else {
	  $onerow->status = $this->Lang('cantdownload');
	}
      }
      break;

    case 'upgrade':
      {
	$mod = $moduledir.DIRECTORY_SEPARATOR.$row['name'];
	if( (($writable && is_dir($mod) && is_directory_writable( $mod )) ||
	     ($writable && !file_exists( $mod ) )) && $caninstall ) {
	  $onerow->status = $this->CreateLink( $id, 'installmodule', $returnid,
					       $this->Lang('upgrade'), 
					       array('name' => $row['name'],
						     'version' => $row['version'],
						     'filename' => $row['filename'],
						     'size' => $row['size']));
	}
	else {
	  $onerow->status = $this->Lang('cantdownload');
	}
      }
      break;
    }
	    
    $onerow->size = (int)((float) $row['size'] / 1024.0 + 0.5);
    if( isset( $row['description'] ) ) $onerow->description=$row['description'];
    if( isset( $row['php_min'] ) ) $onerow->php_min = $row['php_min'];
    if( isset( $row['php_max'] ) ) $onerow->php_max = $row['php_max'];
    if( isset( $row['cmsms_min'] ) ) $onerow->cmsms_min = $row['cmsms_min'];
    if( isset( $row['cmsms_max'] ) ) $onerow->cmsms_max = $row['cmsms_max'];
    if( isset( $row['cmsms_tested'] ) ) $onerow->cmsms_tested = $row['cmsms_tested'];
    $onerow->untested = isset($row['untested']) ? $row['untested'] : false;
    $onerow->incompatible = ($row['status'] === 'incompatible');
    $rowarray[] = $onerow;
  } // for
}

modmgr_utils::get_images();
$tpl = $smarty->CreateTemplate($this->GetTemplateResource('showmodule.tpl'), null, null, $smarty);
if( isset($rowarray) ) {
  $tpl->assign('items', $rowarray);
  $tpl->assign('itemcount', count($rowarray));
}
$tpl->assign('nametext',$this->Lang('nametext'));
$tpl->assign('vertext',$this->Lang('vertext'));
$tpl->assign('sizetext',$this->Lang('sizetext'));
$tpl->assign('statustext',$this->Lang('statustext'));
$tpl->assign('header',$this->Lang('versionsformodule',$prefix));
$tpl->display();
#
# EOF
#
?>