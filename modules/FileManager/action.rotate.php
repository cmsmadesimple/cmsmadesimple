<?php
# FileManager. A plugin for CMS - CMS Made Simple
#   Copyright (c) 2006-08 by Morten Poulsen <morten@poulsen.org>
# This file copyright (c) 2013 by Robert Campbell <calguy1000@cmsmadesimple.org>
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

$config=cmsms()->GetConfig();
$basedir = $config['root_path'];
$filename=$this->decodefilename($selall[0]);
$src = filemanager_utils::join_path($basedir,filemanager_utils::get_cwd(),$filename);
if( !file_exists($src) ) {
  $params["fmerror"]="filenotfound";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
$imageinfo = getimagesize($src);
if( !$imageinfo || !isset($imageinfo['mime']) || !startswith($imageinfo['mime'],'image') ) {
  $params["fmerror"]="filenotimage";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
if( !is_writable($src) ) {
  $params["fmerror"]="notwritable";
  $this->Redirect($id,"defaultadmin",$returnid,$params);
}
switch( $imageinfo['mime'] ) {
 case 'image/gif':
 case 'image/jpeg':
 case 'image/png':
   break;
 default:
   $params['fmerror'] = 'fileimagetype';
   $this->Redirect($id,"defaultadmin",$returnid,$params);
   break;
}
$width = $imageinfo[0];
$height = $imageinfo[1];
$postrotate = cms_userprefs::get($this->GetName().'rotate_postrotate',1);
$createthumb = cms_userprefs::get($this->GetName().'rotate_createthumb',0);

if( isset($params['save']) ) {
  // save prefs.
  $createthumb = (int)$params['createthumb'];
  $postrotate = trim($params['postrotate']);
  cms_userprefs::set($this->GetName().'rotate_postrotate',$postrotate);
  cms_userprefs::set($this->GetName().'rotate_createthumb',$createthumb);

  // do the work
  $angle = (int)$params['angle'];
  $angle = max(-180,min(180,$angle))*-1;
  $source = imagecreatefromstring(file_get_contents($src));
  imagealphablending($source, false);
  imagesavealpha($source, true);
  $bgcolor = imageColorAllocateAlpha($source, 255, 255, 255, 127);
  $rotated = imagerotate($source,$angle,$bgcolor);
  imagealphablending($rotated, false);
  imagesavealpha($rotated, true);

  if( $postrotate == 'crop' ) {
    // calculates crop dimensions based on center of image
    $x2 = (int)($width / 2);
    $y2 = (int)($height / 2);
    $new_w = imagesx($rotated);
    $new_h = imagesy($rotated);
    $center_x = (int)($new_w / 2);
    $center_y = (int)($new_h / 2);
    $x0 = max(0,$center_x - $x2);
    $y0 = max(0,$center_y - $y2);
    //die("width = $width, height = $height, new_w = $new_w, new_h = $new_h, x0 = $x0, y0 = $y0");

    $newimg = imagecreatetruecolor($width,$height);
    imagealphablending($newimg,FALSE);
    imagecolortransparent($newimg,$bgcolor);
    imagefill($newimg,0,0,$bgcolor);
    imagesavealpha($newimg,TRUE);
    imagecopy($newimg,$rotated,0,0,$x0,$y0,$width,$height);
    
    imagedestroy($rotated);
    $rotated = $newimg;
  }
  else if( $postrotate == 'resize' ) {
    $src_w = imagesx($rotated);
    $src_h = imagesy($rotated);

    if( $width < $height ) {
      // height is greater... 
      $new_h = $height;
      $new_w = round(($new_h / $src_h) * $src_w, 0);
    }
    else {
      // width is greater.
      $new_w = $width;
      $new_h = round(($new_w / $src_w) * $src_h, 0);
    }

    $x0 = (int)(($src_w - $new_w) / 2);
    $y0 = (int)(($src_h - $new_h) / 2);

    //die("rotated={$src_w}x{$src_h} orig={$width}x{$height} new={$new_w},{$new_h} offset = $x0,$y0");
    $newimg = imagecreatetruecolor($new_w,$new_h);
    imagealphablending($newimg,FALSE);
    imagecolortransparent($newimg,$bgcolor);
    imagefill($newimg,0,0,$bgcolor);
    imagesavealpha($newimg,TRUE);


    imagecopyresampled($newimg,$rotated,$x0,$y0,0,0,$new_w,$new_h,$src_w,$src_h);
    
    imagedestroy($rotated);
    $rotated = $newimg;
  }

  // save the thing.
  $res = null;
  switch( $imageinfo['mime'] ) {
  case 'image/gif':
    $res = imagegif($rotated,$src);
    break;
  case 'image/png':
    $res = imagepng($rotated,$src,9);
    break;
  case 'image/jpeg':
  default:
    $res = imagejpeg($rotated,$src,100);
    break;
  }

  if( $createthumb ) $thumb = filemanager_utils::create_thumbnail($src);

  $this->Redirect($id,"defaultadmin",$returnid,$params);
}

//
// build the form
//
$opts = array('none'=>$this->Lang('none'),
	      'crop'=>$this->Lang('crop'),
	      'resize'=>$this->Lang('resize'));
$smarty->assign('opts',$opts);
$url = filemanager_utils::get_cwd_url()."/$filename";
$smarty->assign('postrotate',$postrotate);
$smarty->assign('createthumb',$createthumb);
$smarty->assign('filename',$filename);
$smarty->assign('width',$width);
$smarty->assign('height',$height);
$smarty->assign('image',$url);
if( is_array($selall) ) $params['selall'] = serialize($selall);
$smarty->assign('startform',$this->CreateFormStart($id,'rotate',$returnid,'post','',false,'',$params));
$smarty->assign('endform',$this->CreateFormEnd());
echo $this->ProcessTemplate('filerotate.tpl');

#
# EOF
#
?>