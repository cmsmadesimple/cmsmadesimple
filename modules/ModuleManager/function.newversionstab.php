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
if( !isset($gCms) ) exit;

global $CMS_VERSION;
$caninstall = true;

if( FALSE == can_admin_upload() ) {
    echo '<div class="pageerrorcontainer"><div class="pageoverflow"><p class="pageerror">'.$this->Lang('error_permissions').'</p></div></div>';
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
			if( version_compare($row['version'],$mver) > 0 ) {
				$modinst = cms_utils::get_module($row['name']);
				if( is_object($modinst) ) $onerow->haveversion = $modinst->GetVersion();

				$onerow->age = modmgr_utils::get_status($row['date']);
				$onerow->downloads = $row['downloads'];
				$onerow->date = $row['date'];
				$onerow->age = modmgr_utils::get_status($row['date']);

				$onerow->name = $this->CreateLink( $id, 'modulelist', $returnid, $row['name'], array('name'=>$row['name']));
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
					if( (!empty($row['maxcmsversion']) && version_compare($CMS_VERSION,$row['maxcmsversion']) > 0) ||
						(!empty($row['mincmsversion']) && version_compare($CMS_VERSION,$row['mincmsversion']) < 0) ) {
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
		}

		$results[] = $onerow;
	}
}

if( !count($results) ) {
    $smarty->assign('nvmessage',$this->Lang('all_modules_up_to_date'));
}
else {
    $smarty->assign('updatestxt',$this->Lang('available_updates'));
    $smarty->assign('items',$results);
    $smarty->assign('itemcount', count($results));
}

$smarty->assign('haveversion',$this->Lang('yourversion'));
$smarty->assign('nametext',$this->Lang('nametext'));
$smarty->assign('vertext',$this->Lang('vertext'));
$smarty->assign('sizetext',$this->Lang('sizetext'));
$smarty->assign('statustext',$this->Lang('statustext'));

echo $this->processTemplate('newversionstab.tpl');

# EOF