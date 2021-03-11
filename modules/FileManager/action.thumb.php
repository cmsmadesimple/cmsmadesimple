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

if (isset($params["cancel"])) $this->Redirect($id,"defaultadmin",$returnid,$params);

$selall = $params['selall'];
if( !is_array($selall) ) {
  $selall = unserialize($selall);
}
unset($params['selall']);

if (count($selall)==0) {
  $params["fmerror"]="nofilesselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
if (count($selall)>1) {
  $params["fmerror"]="morethanonefiledirselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}

$advancedmode = filemanager_utils::check_advanced_mode();

$config=cmsms()->GetConfig();
$basedir = $config['root_path'];
$filename=$this->decodefilename($selall[0]);
$src = filemanager_utils::join_path($basedir,filemanager_utils::get_cwd(),$filename);
if( !file_exists($src) ) {
  $params["fmerror"]="filenotfound";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
$thumb = filemanager_utils::join_path($basedir,filemanager_utils::get_cwd(),'thumb_'.$filename);

if( isset($params['submit']) ) {
  $thumb = filemanager_utils::join_path($basedir,filemanager_utils::get_cwd(),'thumb_'.$filename);
  $thumb = filemanager_utils::create_thumbnail($src);
  
  if( !$thumb ) {
    $params["fmerror"]="thumberror";
  }
  else {
    $params["fmmessage"]="thumbsuccess";
  }
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}

//
// build the form
//
$smarty->assign('filename',$filename);
$smarty->assign('filespec',$src);
$smarty->assign('thumb',$thumb);
$smarty->assign('thumbexists',file_exists($thumb));
if( is_array($selall) ) $params['selall'] = serialize($selall);
$smarty->assign('startform', $this->CreateFormStart($id, 'fileaction', $returnid,"post","",false,"",$params));
$smarty->assign('mod',$this);
$smarty->assign('endform', $this->CreateFormEnd());

echo $this->ProcessTemplate('filethumbnail.tpl');

#
# EOF
#