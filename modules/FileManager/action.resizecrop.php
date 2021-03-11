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
if( !is_array($selall) ) $selall = unserialize($selall);
unset($params['selall']);

if (count($selall)==0) {
  $params["fmerror"]="nofilesselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
if (count($selall)>1) {
  $params["fmerror"]="morethanonefiledirselected";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}

$config = cmsms()->getConfig();
$basedir = $config['root_path'];
$filename=$this->decodefilename($selall[0]);
$src = filemanager_utils::join_path($basedir,filemanager_utils::get_cwd(),$filename);
if( !file_exists($src) ) {
  $params["fmerror"]="filenotfound";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
$imageinfo = getimagesize($src);
if( !$imageinfo || !isset($imageinfo['mime']) || !startswith($imageinfo['mime'],'image') ) {
    $this->SetError($this->Lang('filenotimage'));
    $this->Redirect($id,"defaultadmin",$returnid);
}
if( !is_writable($src) ) {
    $this->SetError($this->Lang('filenotimage'));
    $this->Redirect($id,"defaultadmin",$returnid);
}

//
// handle submit action(s).
//

if(empty($params['reset'])
   && !empty($params['cx']) && !empty($params['cy'])
   && !empty($params['cw']) && !empty($params['ch'])
   && !empty($params['iw']) && !empty($params['ih'])) {

  //Get the mimeType
  $mimeType = imageEditor::getMime($src);

  //Open new Instance
  $instance = imageEditor::open($src);

  //Resize it if necessary
  if( !empty($params['iw']) && !empty($params['ih']) ) {
      $instance = imageEditor::resize($instance, $mimeType, $params['iw'], $params['ih']);
  }

  //Crop it if necessary
  if( !empty($params['cx']) && !empty($params['cy']) && !empty($params['cw']) && !empty($params['ch']) ) {
      $instance = imageEditor::crop($instance, $mimeType, $params['cx'], $params['cy'], $params['cw'], $params['ch']);
  }

  //Save it
  $res = imageEditor::save($instance, $src, $mimeType);
  if( $this->GetPreference('create_thumbnails') ) filemanager_utils::create_thumbnail($src);

  $this->Redirect($id,"defaultadmin",$returnid);
}


//
// build the form
//
if( is_array($selall) ) $params['selall'] = serialize($selall);
$smarty->assign('formstart',$this->CreateFormStart($id,'resizecrop',$returnid,'post','',false,'',$params));
$smarty->assign('formend',$this->CreateFormEnd());
$smarty->assign('filename',$filename);
$url = filemanager_utils::get_cwd_url()."/$filename";
$smarty->assign('image',$url);
$smarty->assign('image_width',$imageinfo[0]);

echo $this->ProcessTemplate('pie.tpl');

#
# EOF
#