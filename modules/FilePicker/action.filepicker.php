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
use \FilePicker\TemporaryInstanceStorage;
use \FilePicker\TemporaryProfileStorage;
use \FilePicker\PathAssistant;
use \FilePicker\utils;
use \CMSMS\FileType;
if( !isset($gCms) ) exit;
if( !check_login(FALSE) ) exit; // admin only.... but any admin

//$handlers = ob_list_handlers();
//for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

$clean_str = function( $in ) {
    $in = cleanValue($in);
    $in = strip_tags($in);
    return trim($in);
};

//
// initialization
//
$sesskey = md5(__FILE__);
if( isset($_GET['_enc']) ) {
   $parms = json_decode(base64_decode($_GET['_enc']),TRUE);
   if( is_array($parms) && count($parms) ) $_GET = array_merge($_GET,$parms);
   unset($_GET['_enc']);
}

$inst = get_parameter_value($_GET,'inst');
$sig = $clean_str(get_parameter_value($_GET,'sig'));
$nosub = (int) get_parameter_value($_GET,'nosub');
$profile = null;
if( $sig ) $profile = TemporaryProfileStorage::get($sig);
if( !$profile ) $profile = $this->get_default_profile();
/*
if( !$sig && $profile ) {
    //$profile = $profile->overrideWith( [ 'type'=>$type ] );
    $sig = TemporaryProfileStorage::set($profile);
}
*/
if( !$this->CheckPermission('Modify Files') ) {
    $parms = ['can_upload'=>FALSE, 'can_delete'=>FALSE, 'can_mkdir'=>FALSE ];
    $profile = $profile->overrideWith( $parms );
}
$useprefix = cms_to_bool(get_parameter_value($_GET,'useprefix'));
if( $useprefix ) {
    $prefix = $profile->reltop;
    if (!empty($prefix)) {
        $profile = $profile->overrideWith( [ 'prefix'=>$prefix.'/' ] );
    }
}

$filemanager = cms_utils::get_module('FileManager');

// get our absolute top directory, and it's matching url
$topdir = $profile->top;
if( !$topdir ) $topdir = $config['uploads_path'];
$assistant = new PathAssistant($config,$topdir);

// get our current working directory relative to $topdir
// use cwd stored in session first... then if necessary the profile topdir, then if necessary, the absolute topdir
$cwd = '';
if( isset($_SESSION[$sesskey]) ) $cwd = trim($_SESSION[$sesskey]);
if( !$cwd && $profile->top ) $cwd = $assistant->to_relative($profile->top);
if( !$nosub && isset($_GET['subdir']) ) {
    try {
        $cwd .= '/' . cms_html_entity_decode(trim(cleanValue($_GET['subdir'])));
        $cwd = $assistant->to_relative($assistant->to_absolute($cwd));
    }
    catch( \Exception $e ) {
        // ignore
    }
}
// failsave, if we don't have a valid working directory, set it to the $topdir;
if( $cwd && !$assistant->is_valid_relative_path( $cwd ) ) {
    $cwd = '';
}
//if( $cwd ) $_SESSION[$sesskey] = $cwd;
$_SESSION[$sesskey] = $cwd;

// now we're set to go.
$starturl = $assistant->relative_path_to_url($cwd);
$startdir = $assistant->to_absolute($cwd);

$sortfiles = function($file1,$file2) {
    if ($file1["isdir"] && !$file2["isdir"]) return -1;
    if (!$file1["isdir"] && $file2["isdir"]) return 1;
    return strnatcasecmp($file1["name"],$file2["name"]);
};

$accept_file = function(\CMSMS\FilePickerProfile $profile,$cwd,$path,$filename) use (&$filemanager,&$assistant) {
    if( $filename == '.' ) return FALSE;
    $fullpath = cms_join_path($path,$filename);
    if( $filename == '..' ) {
        if( !$assistant->is_relative($fullpath) ) return FALSE;
        return TRUE;
    }
    if( is_dir($fullpath) ) {
        if( !$profile->show_hidden && ( startswith($filename,'.') || startswith($filename,'_') ) ) return FALSE;
        if( !$assistant->is_relative( $fullpath ) ) return FALSE;
        return TRUE;
    }
    $res = $this->is_acceptable_filename( $profile, $filename );
    if( !$res ) return FALSE;
    if( is_dir($fullpath) && !$assistant->is_relative($fullpath) ) return FALSE;
    return TRUE;
};

$get_thumbnail_tag = function($file,$path,$url) {
    $imagetag = null;
    $imagepath = $path.'/thumb_'.$file;
    $imageurl = $url.'/thumb_'.$file;
    if( is_file($imagepath) ) $imagetag="<img src='".$imageurl."' alt='".$file."' title='".$file."' />";
    return $imagetag;
};

/*
 * A quick check for a file type based on extension
 * @String $filename
 */
$get_filetype = function($filename) use (&$is_image,&$is_archive) {
    $ext = strtolower(substr($filename,strrpos($filename,".")+1));
	$filetype = 'file'; // default to all file
	$imgext = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg', 'wbmp', 'webp'); // images
	$videoext = array('mov', 'mpeg', 'mp4', 'avi', 'mpg','wma', 'flv', 'webm', 'wmv', 'qt', 'ogg'); // videos
	$audioext = array('mp3', 'm4a', 'ac3', 'aiff', 'mid', 'wav'); // audio
	$archiveext = array('zip', 'rar', 'gz', 'tar', 'iso', 'dmg'); // archives

	if( $this->_typehelper->is_image($filename) ) {
		$filetype = 'image';
	} elseif(in_array($ext, $videoext)) {
		$filetype = 'video';
	} elseif(in_array($ext, $audioext)) {
		$filetype = 'audio';
	} elseif( $is_archive($filename) ) {
		$filetype = 'archive';
	}

	return $filetype;
};

//
// get our file list
//
$files = $thumbs = [];
$dh = dir($startdir);
while( false !== ($filename = $dh->read()) ) {
    if( !$accept_file( $profile, $cwd, $startdir, $filename ) ) continue;
    $fullname = cms_join_path($startdir,$filename);

    $file = array();
    $file['name'] = $filename;
    $file['fullpath'] = $fullname;
    $file['fullurl'] = $starturl.'/'.$filename;
    $file['isdir'] = is_dir($fullname);
    $file['isparent'] = false;
    if( $file['isdir'] ) {
        if( $filename == '..' ) $file['isparent'] = true;
        $file['relurl'] = $file['fullurl'];
    } else {
        $file['relurl'] = $assistant->to_relative($fullname);
    }
    $file['ext'] = strtolower(substr($filename,strrpos($filename,".")+1));
    $file['is_image'] = $this->_typehelper->is_image($fullname);
    $file['icon'] = $filemanager->GetFileIcon('.'.$file['ext'],$file['isdir']);
    $file['filetype'] = $this->_typehelper->get_file_type($fullname);
    $file['is_thumb'] = $this->_typehelper->is_thumb($filename);
    $file['dimensions'] = '';
    if( $file['is_image'] && !$file['is_thumb'] ) {
        $file['thumbnail'] = $get_thumbnail_tag($filename,$startdir,$starturl);
        $thumbs[] = 'thumb_'.$filename;
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
        $parms = [ 'subdir'=>$filename, 'inst'=>$inst, 'sig'=>$sig ];
        //if( $type ) $parms['type'] = $type;
        $url = $this->create_url($id,'filepicker',$returnid)."&showtemplate=false&_enc=".base64_encode(json_encode($parms));
	$file['chdir_url'] = $url;
    }
    $files[$filename] = $file;
}

if( $profile->show_thumbs && count($thumbs) ) {
    // remove thumbnails that are not orphaned from the list
    foreach( $thumbs as $thumb ) {
        if( isset($files[$thumb]) ) unset($files[$thumb]);
    }
}
// done the loop, now sort
usort($files,$sortfiles);

$cwd_for_display = null;
$assistant2 = new PathAssistant($config,$config['root_path']);
$cwd_for_display = $assistant2->to_relative( $startdir );
$css_files = [ '/lib/css/filepicker.css', '/lib/css/filepicker.min.css' ];
$mtime = -1;
$sel_file = null;
foreach( $css_files as $file ) {
    $fp = $this->GetModulePath().'/'.$file;
    if( is_file($fp) ) {
        $fmt = filemtime($fp);
        if( $fmt > $mtime ) {
            $mtime = $fmt;
            $sel_file = $file;
        }
    }
}
$smarty->assign('cssurl',$this->GetModuleURLPath().$sel_file);
$smarty->assign('cwd_for_display',$cwd_for_display);
$smarty->assign('cwd',$cwd);
$smarty->assign('files',$files);
$smarty->assign('sig',$sig);
$smarty->assign('inst',$inst);
$smarty->assign('mod',$this);
$smarty->assign('profile',$profile);
$lang = [];
$lang['confirm_delete'] = $this->Lang('confirm_delete');
$lang['ok'] = $this->Lang('ok');
$lang['error_problem_upload'] = $this->Lang('error_problem_upload');
$lang['error_failed_ajax'] = $this->Lang('error_failed_ajax');
$smarty->assign('lang_js',json_encode($lang));
echo $this->ProcessTemplate('filepicker.tpl');

#
# EOF
#
