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

// Fetch recent modules from CDN
$cdn_url = 'https://cdn.cmsmadesimple.org/repository/cache/moduledetailsgetall_recent.json';
$req = new modmgr_cached_request();
$req->execute($cdn_url, array(), 1440);
$status = $req->getStatus();
$result = $req->getResult();

if( $status != 200 || $result == '' ) {
  echo $this->ShowErrors($this->Lang('error_request_problem'));
  return;
}

$repmodules = json_decode($result, true);
if( !is_array($repmodules) || !count($repmodules) ) {
  $tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_featured.tpl'), null, null, $smarty);
  $tpl->assign('message', $this->Lang('error_connectnomodules'));
  if( isset($letters) ) $tpl->assign('letter_urls', $letters);
  if( isset($curletter) ) $tpl->assign('curletter', $curletter);
  $tpl->display();
  return;
}

// get installed modules
$result = modmgr_utils::get_installed_modules();
if( !$result[0] ) {
  $this->_DisplayErrorPage( $id, $params, $returnid, $result[1] );
  return;
}
$instmodules = $result[1];

// build module data with compat filtering
$data = modmgr_utils::build_module_data($repmodules, $instmodules);

if( !count($data) ) {
  $tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_featured.tpl'), null, null, $smarty);
  $tpl->assign('message', $this->Lang('error_connectnomodules'));
  if( isset($letters) ) $tpl->assign('letter_urls', $letters);
  if( isset($curletter) ) $tpl->assign('curletter', $curletter);
  $tpl->display();
  return;
}

$moduledir = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules";
$writable = is_writable( $moduledir );

$rowarray = array();
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
  case 'upgrade':
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

  $onerow->size = (int)((float) $row['size'] / 1024.0 + 0.5);
  if( isset( $row['description'] ) ) $onerow->description=$row['description'];
  $rowarray[] = $onerow;
}

shuffle($rowarray);
$tpl = $smarty->CreateTemplate($this->GetTemplateResource('admin_featured.tpl'), null, null, $smarty);
$tpl->assign('items', $rowarray);
$tpl->assign('itemcount', count($rowarray));
if( isset($letters) ) $tpl->assign('letter_urls', $letters);
if( isset($curletter) ) $tpl->assign('curletter', $curletter);
$tpl->assign('nametext',$this->Lang('nametext'));
$tpl->assign('vertext',$this->Lang('vertext'));
$tpl->assign('sizetext',$this->Lang('sizetext'));
$tpl->assign('statustext',$this->Lang('statustext'));
$tpl->display();

#
# EOF
#
?>
