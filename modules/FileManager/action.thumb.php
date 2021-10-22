<?php
# FileManager. A plugin for CMS - CMS Made Simple
# Copyright (c) 2006-08 by Morten Poulsen <morten@poulsen.org>
#
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
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
  //$thumb = filemanager_utils::join_path($basedir,filemanager_utils::get_cwd(),'thumb_'.$filename); // by mistake for sure... (JM)
  $thumb = filemanager_utils::create_thumbnail($src, NULL, TRUE);
  
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
?>