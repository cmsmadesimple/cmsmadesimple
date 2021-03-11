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

if (isset($params["cancel"])) {
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}

$selall = $params['selall'];
if( !is_array($selall) ) {
  $selall = unserialize($selall);
}
if (count($selall)==0) {
  $params["fmerror"]="nofilesselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
//echo count($selall);
if (count($selall)>1) {
  //echo "hi";die();
  $params["fmerror"]="morethanonefiledirselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}

$config=cmsms()->GetConfig();

$oldname=$this->decodefilename($selall[0]);
$newname=$oldname; //for initial input box

if (isset($params["newname"])) {
  $newname=strip_tags(trim($params["newname"]));
  if (!filemanager_utils::is_valid_filename($newname)) {
    echo $this->ShowErrors($this->Lang("invaliddestname"));
  } else {
    $cwd = filemanager_utils::get_cwd();
    $fullnewname = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),trim($params['newname']));
    if (file_exists($fullnewname)) {
      echo $this->ShowErrors($this->Lang("namealreadyexists"));
      //fallthrough
    } else {
      $fulloldname = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),$oldname);
      if (@rename($fulloldname,$fullnewname)) {
	$thumboldname = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),'thumb_'.$oldname);
	$thumbnewname = filemanager_utils::join_path(filemanager_utils::get_full_cwd(),'thumb_'.trim($params['newname']));
	if( file_exists($thumboldname) ) {
	  @rename($thumboldname,$thumbnewname);
	}
	$this->SetMessage($this->Lang('renamesuccess'));
	$this->Audit('',"File Manager", "Renamed file: ".$fullnewname);
	$this->Redirect($id,"defaultadmin",$returnid,$paramsnofiles);
      } else {
	$this->SetError($this->Lang('renameerror'));
	$this->Redirect($id,"defaultadmin",$returnid,$params);
      }
    }
  }
 }

if( is_array($params['selall']) ) {
  $params['selall'] = serialize($params['selall']);
}
$this->smarty->assign('startform', $this->CreateFormStart($id, 'fileaction', $returnid,"post","",false,"",$params));
//$this->CreateInputHidden($id,"fileaction","rename");
$this->smarty->assign('newnametext',$this->lang("newname"));
$smarty->assign('newname',$newname);
$this->smarty->assign('newnameinput',$this->CreateInputText($id,"newname",$newname,40));

$this->smarty->assign('endform', $this->CreateFormEnd());

$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('rename')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));

echo $this->ProcessTemplate('renamefile.tpl');

#
# EOF
#