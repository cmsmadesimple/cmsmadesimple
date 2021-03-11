<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

if (!function_exists("cmsms")) exit;
if (!$this->CheckPermission("Modify Files") && !$this->AdvancedAccessAllowed()) exit;

if(!isset($params["dirname"]) || !isset($params["path"])) {
	$this->Redirect($id, 'defaultadmin');
}

if( !filemanager_utils::test_valid_path($params['path']) ) {
  $this->Redirect($id, 'defaultadmin',$returnid,array("fmerror"=>"fileoutsideuploads"));
}

$config = $gCms->GetConfig();
$fullname=$this->Slash($params["path"],$params["dirname"]);
$fullname=$this->Slash($config["root_path"],$fullname);

function chmodRecursive($path,$newmode,&$module) {
	$dir = opendir($path);
	while ($entry = readdir($dir)) {
		if ($entry=="." || $entry=="..") continue;

		if (is_file( "$path/$entry")) {
			$module->SetMode($newmode,$path,$entry);
					//echo "hi";die();
		} elseif (is_dir("$path/$entry") && $entry!='.' && $entry!='..') {
			chmodRecursive("$path/$entry",$newmode,$module);
		}
	}
	closedir($dir);
	return $module->SetMode($newmode,$path);
}

function isEmpty($path) {
	$empty=true;
	$dir = opendir($path) ;
	while ($entry = readdir($dir)) {
		if ($entry!="." && $entry!=".." && $entry!="\\" && $entry!="/") {
			return false;
		}
	}
	return true;
}

$emptydir=isEmpty($fullname);

if (isset($params["newmode"])) {
	if (isset($params["cancel"])) {
		$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"module_message"=>$this->Lang("chmodcancelled")));
	} else {
		$newmode=$this->GetModeFromTable($params);
		if (isset($params["quickmode"]) && ($params["quickmode"]!="")) {
			$newmode=$params["quickmode"];
		}
		if (isset($params["recurse"]) && $params["recurse"]=="1" && !$emptydir) {
			if (chmodRecursive($fullname,$newmode,$this)) {
				$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"fmmessage"=>"dirchmodsuccessmulti"));
			} else {
				$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"fmerror"=>"dirchmodfailmulti"));
			}
		} else {
			//No recursion
			if ($this->SetMode($newmode,$fullname)) {
				$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"fmmessage"=>"dirchmodsuccess"));
			} else {
				$this->Redirect($id,"defaultadmin",$returnid,array("path"=>$params["path"],"fmerror"=>"dirchmodfailure"));
			}
		}
	}
} else {
	$currentmode=$this->GetMode($params["path"],$params["dirname"]);
	$this->smarty->assign('startform', $this->CreateFormStart($id, 'chmoddir', $returnid));

	$this->smarty->assign('filename', $this->CreateInputHidden($id,"dirname",$params["dirname"]));
	$this->smarty->assign('path', $this->CreateInputHidden($id,"path",$params["path"]));
	$this->smarty->assign('endform', $this->CreateFormEnd());
	$this->smarty->assign('newmodetext', $this->Lang("newpermissions"));

	$this->smarty->assign('recurseinputtext', $this->Lang("recursetext"));
	$this->smarty->assign('recurseinput', $this->CreateInputCheckbox($id,"recurse","1"));

	$this->smarty->assign('newmode', $this->CreateInputHidden($id,"newmode","newset"));

	$this->smarty->assign('quickmodetext', $this->Lang("quickmode"));
	$this->smarty->assign('quickmodeinput', $this->CreateInputText($id,"quickmode","",3,3));

	$this->smarty->assign('modetable', $this->GetModeTable($id,$this->GetPermissions($params["path"],$params["dirname"])));

	$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('setpermissions')));
	$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));
	echo $this->ProcessTemplate('chmoddir.tpl');

}

#
# EOF
#