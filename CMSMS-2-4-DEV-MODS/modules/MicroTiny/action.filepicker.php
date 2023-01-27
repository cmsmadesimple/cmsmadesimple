<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOpUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
if( !isset($gCms) ) exit;
if( !check_login(FALSE) ) exit; // admin only.... but any admin

//$handlers = ob_list_handlers();
//for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

//
// initialization
//
$field = trim(get_parameter_value($_GET,'field'));
$type = 'any';
$filemanager = cms_utils::get_module('FileManager');
if( isset($_GET['type']) ) {
  $tmp = strtolower(trim($_GET['type']));
  if( $tmp == 'image' ) $type = 'image';
  if( $tmp == 'media' ) $type = 'media';
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

$is_media = function($filename) {
  $ext = strtolower(substr($filename,strrpos($filename,'.')+1));
  if( in_array($ext,array('swf','dcr','mov','qt','mpg','mp3','mp4','ogg','mpeg','wmp','avi','wmv',
			  'wm','asf','asx','wmx','rm','ra','ram')) ) {
    return TRUE;
  }
  return FALSE;
};

$sortfiles = function($file1,$file2) {
  if ($file1["isdir"] && !$file2["isdir"]) return -1;
  if (!$file1["isdir"] && $file2["isdir"]) return 1;
  return strnatcasecmp($file1["name"],$file2["name"]);
};

$accept_file = function($type,$cwd,$path,$filename) use (&$filemanager,&$is_image,&$is_media) {
  if( $filename == '.' ) return FALSE;
  if( $filename == '..' ) {
    if( $cwd == filemanager_utils::get_default_cwd() ) return FALSE;
    return TRUE;
  }
  if( (startswith($filename,'.') || startswith($filename,'_')) && !$filemanager->GetPreference('showhiddenfiles') ) return FALSE;
  if( is_dir(cms_join_path($path,$filename)) ) return TRUE;

  switch( $type ) {
    case 'image':
      if( $is_image($filename) ) return TRUE;
      return FALSE;

    case 'media':
      if( $is_media($filename) ) return TRUE;
      return FALSE;

    case 'file':
    case 'any':
    default:
      return TRUE;
  }
};

/*
 * A quick check for a file type based on extension
 * @String $ext
 */
function set_filetype($ext) {

	$ext = strtolower($ext);
	$filetype = 'file'; // default to all file
	$imgext = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg', 'wbmp', 'webp'); // images
	$videoext = array('mov', 'mpeg', 'mp4', 'avi', 'mpg','wma', 'flv', 'webm', 'wmv', 'qt', 'ogg'); // videos
	$audioext = array('mp3', 'm4a', 'ac3', 'aiff', 'mid', 'wav'); // audio
	$archiveext = array('zip', 'rar', 'gz', 'tar', 'iso', 'dmg'); // archives

	if(in_array($ext, $imgext)) {
		$filetype = 'image';
	} elseif(in_array($ext, $videoext)) {
		$filetype = 'video';
	} elseif(in_array($ext, $audioext)) {
		$filetype = 'audio';
	} elseif(in_array($ext, $archiveext)) {
		$filetype = 'archive';
	}

	return $filetype;
}


//
// get our file list
//
$files = array();
$dh = dir($startdir);
while( false !== ($filename = $dh->read()) ) {
  if( !$accept_file( $type, $cwd, $startdir, $filename ) ) continue;
  $fullname = cms_join_path($startdir,$filename);

  $file = array();
  $file['name'] = $filename;
  $file['fullpath'] = $fullname;
  $file['fullurl'] = $starturl.'/'.$filename;
  $file['isdir'] = is_dir($fullname);
  $file['ext'] = strtolower(substr($filename,strrpos($filename,".")+1));
  $file['is_image'] = $is_image($filename);
  $file['icon'] = $filemanager->GetFileIcon('.'.$file['ext'],$file['isdir']);
  $file['filetype'] = set_filetype($file['ext']);
  $file['dimensions'] = '';
  if( $file['is_image'] ) {
    $file['thumbnail'] = microtiny_utils::GetThumbnailFile($filename,$startdir,$starturl);
    $imgsize = @getimagesize($fullname);
    if( $imgsize ) $file['dimensions'] = $imgsize[0].' x '.$imgsize[1];
  }
  $info = @stat($fullname);
  $filesizename = array(" Bytes", " KB", " MB");
  if( $info && $info['size'] > 0) {
  	$file['size'] = round($info['size']/pow(1024, ($i = floor(log($info['size'], 1024)))), 2) . $filesizename[$i];
  } else {
  	$file['size'] = null;
  }
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
$smarty->assign('type', $type);

echo $this->ProcessTemplate('filepicker.tpl');

#
# EOF
#
?>
