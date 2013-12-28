<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://www.cmsmadesimple.org
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
if( !isset($gCms) ) exit;
check_login(); // admin only.... but any admin

//
// initialization
//
$field = trim(get_parameter_value($_GET,'field'));
$type = 'any';
$filemanager = cms_utils::get_module('FileManager');
if( isset($_GET['type']) ) {
  $tmp = strtolower(trim($_GET['type']));
  if( $tmp == 'image' ) $type = 'image';
}

$cwd = filemanager_utils::get_cwd();
if( isset($_GET['subdir']) ) {
  // todo, make sure this can't go above /uploads
  $cwd .= '/' . trim($_GET['subdir']);
  filemanager_utils::set_cwd($cwd);
}
$cwd = filemanager_utils::get_cwd();

$starturl = $config->smart_root_url().'/'.$cwd;
$startdir = filemanager_utils::join_path($config['root_path'],$cwd);

$is_image = function($filename) {
  $ext = strtolower(substr($filename,strrpos($filename,'.')+1));
  if( in_array($ext,array('jpg','jpeg','bmp','wbmp','gif','png','webp')) ) return TRUE;
  return FALSE;
};

$sortfiles = function($file1,$file2) {
  if ($file1["isdir"] && !$file2["isdir"]) return -1;
  if (!$file1["isdir"] && $file2["isdir"]) return 1;
  return strnatcasecmp($file1["name"],$file2["name"]);
};

//
// get our file list
//
$files = array();
$dh = dir($startdir);
while( false !== ($filename = $dh->read()) ) {
  if( $filename == '.' ) continue;
  if( $filename != '..' && (startswith($filename,'.') || startswith($filename,'_')) && !$filemanager->GetPreference('showhiddenfiles') ) {
    continue;
  }
  if( startswith($filename,'thumb_') ) continue; // ignore thumbs for now.

  $fullname = cms_join_path($startdir,$filename);

  $file = array();
  $file['name'] = $filename;
  $file['fullpath'] = $fullname;
  $file['fullurl'] = $starturl.'/'.$filename;
  $file['isdir'] = is_dir($fullname);
  $file['ext'] = strtolower(substr($filename,strrpos($filename,".")+1));
  $file['is_image'] = $is_image($filename);
  $file['icon'] = $filemanager->GetFileIcon('.'.$file['ext'],$file['isdir']);
  $file['dimensions'] = '';
  if( $file['is_image'] ) {
    $file['thumbnail'] = microtiny_utils::GetThumbnailFile($filename,$startdir,$starturl);
    $imgsize = @getimagesize($fullname);
    if( $imgsize ) $file['dimensions'] = $imgsize[0].' x '.$imgsize[1];
  }
  $info = @stat($fullname);
  if( $info ) $file['size'] = $info['size'];
  if( $file['isdir'] ) {
    $url = $this->create_url($id,'filepicker',$returnid)."&showtemplate=false&subdir=$filename&type=$type&field=$field";
    $file['chdir_url'] = str_replace('&amp;','&',$url);
  }
  $files[] = $file;
}
// done the loop, now sort
usort($files,$sortfiles);


$smarty->assign('showthumbnails',(int)$filemanager->GetPreference('showthumbnails'));
$smarty->assign('startpath',$cwd);
$smarty->assign('field',$field);
$smarty->assign('files',$files);

echo $this->ProcessTemplate('filepicker.tpl');

#
# EOF
#
?>