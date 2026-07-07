<?php
#--------------------------------------------------
# See DOCS/LICENSE for full license information.
#--------------------------------------------------
if (!defined('CMS_VERSION')) exit;

$caninstall = true;

if( FALSE == can_admin_upload() ) {
    echo $this->ShowErrors($this->Lang('error_permissions'));
    $caninstall = false;
}

$moduledir = $config['root_path'].DIRECTORY_SEPARATOR.'modules';
$writable = is_writable( $moduledir );
$results = array();

if( !empty($newversions) ) {
	foreach( $newversions as $row ) {
		$txt = '';
		$onerow = new stdClass();
		$onerow->txt = $onerow->error = $onerow->age = $onerow->depends_url = $onerow->about_url = $onerow->help_url = $onerow->helplink = $onerow->aboutlink = $onerow->dependslink = null;
		foreach( $row as $key => $val ) {
			$onerow->$key = $val;
		}

		$mod = $this->GetModuleInstance($row['name']);
		if( !is_object($mod) ) {
			$onerow->error = $this->Lang('error_module_object',$row['name']);
		}
		else {
			$mver = $mod->GetVersion();
			if( version_compare($row['version'],$mver) <= 0 ) continue;
				$modinst = cms_utils::get_module($row['name']);
				if( is_object($modinst) ) $onerow->haveversion = $modinst->GetVersion();

				$onerow->age = modmgr_utils::get_status($row['date']);
				$onerow->downloads = $row['downloads'];
				$onerow->date = $row['date'];
				$onerow->age = modmgr_utils::get_status($row['date']);

				$onerow->name = $this->CreateLink( $id, 'modulelist', $returnid, $row['name'], array('name'=>$row['name']));
				$onerow->rawname = $row['name'];
				$onerow->version = $row['version'];

				$onerow->help_url = $this->create_url($id,'modulehelp',$returnid,
													  array('name' => $row['name'],'version' => $row['version'],'filename' => $row['filename']));
				$onerow->helplink = $this->CreateLink( $id, 'modulehelp', $returnid, $this->Lang('helptxt'),
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

				$onerow->size = (int)((float) $row['size'] / 1024.0 + 0.5);
				if( isset( $row['description'] ) ) $onerow->description=$row['description'];
				$onerow->txt= $this->Lang('upgrade_available',$row['version'],$mver);
				$moddir = $moduledir.DIRECTORY_SEPARATOR.$row['name'];
				if( (($writable && is_dir($moddir) && is_directory_writable( $moddir )) ||
					 ($writable && !file_exists( $moddir ) )) && $caninstall ) {
					if( (!empty($row['maxcmsversion']) && version_compare(CMS_VERSION,$row['maxcmsversion']) > 0) ||
						(!empty($row['mincmsversion']) && version_compare(CMS_VERSION,$row['mincmsversion']) < 0) ) {
						$onerow->status = 'incompatible';
					} else {
						$onerow->status = $this->CreateLink( $id, 'installmodule', $returnid,
															 $this->Lang('upgrade'),
															 array('name' => $row['name'],'version' => $row['version'],
																   'filename' => $row['filename'],'size' => $row['size'],
																   'active_tab'=>'newversions','reset_prefs' => 1));
					}
				}
				else {
					$onerow->status = $this->Lang('cantdownload');
				}
		}

		$results[] = $onerow;
	}
}

$tpl = $smarty->CreateTemplate($this->GetTemplateResource('newversionstab.tpl'), null, null, $smarty);

if( !count($results) ) {
    $tpl->assign('nvmessage',$this->Lang('all_modules_up_to_date'));
}
else {
    $tpl->assign('updatestxt',$this->Lang('available_updates'));
    $tpl->assign('items',$results);
    $tpl->assign('itemcount', count($results));
}

$tpl->assign('haveversion',$this->Lang('yourversion'));
$tpl->assign('nametext',$this->Lang('nametext'));
$tpl->assign('vertext',$this->Lang('vertext'));
$tpl->assign('sizetext',$this->Lang('sizetext'));
$tpl->assign('statustext',$this->Lang('statustext'));

$tpl->display();

# EOF