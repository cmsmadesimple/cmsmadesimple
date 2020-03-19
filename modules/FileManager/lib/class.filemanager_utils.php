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

final class filemanager_utils
{
    static private $_can_do_advanced = -1;

    protected function __construct() {}

    public static function is_valid_filename($name)
    {
        if( $name == '' ) return FALSE;
        if( strpos($name,'/') !== false ) return FALSE;
        if( strpos($name,'\\') !== false ) return FALSE;
        if( strpos($name,'..') !== false ) return FALSE;
        if( $name[0] == '.' || $name[0] == ' ' ) return FALSE;
	      if( endswith( $name, '.' ) ) return FALSE;
  
        $ext = strtolower(substr(strrchr($name, '.'), 1));
        if( startswith($ext,'php') || endswith($ext,'php') ) return FALSE;

        if( preg_match('/[\n\r\t\[\]\&\?\<\>\!\@\#\$\%\*\(\)\{\}\|\"\'\:\;\+]/',$name) ) {
            return FALSE;
        }
        return TRUE;
    }

    public static function can_do_advanced()
    {
        if( self::$_can_do_advanced < 0 ) {
            $filemod = cms_utils::get_module('FileManager');
            $config = cmsms()->GetConfig();
            if( startswith($config['uploads_path'],$config['root_path']) && $filemod->AdvancedAccessAllowed() ) {
                self::$_can_do_advanced = 1;
            }
            else {
                self::$_can_do_advanced = 0;
            }
        }
        return self::$_can_do_advanced;
    }

    public static function check_advanced_mode()
    {
        $filemod = cms_utils::get_module('FileManager');
        $a = self::can_do_advanced();
        $b = $filemod->GetPreference('advancedmode',0);
        if( $a && $b ) return TRUE;
        return FALSE;
    }

    public static function get_default_cwd()
    {
        $config = \cms_config::get_instance();
        $advancedmode=filemanager_utils::check_advanced_mode();
        $dir = $config['uploads_path'];
        if( !startswith($dir,CMS_ROOT_PATH) ) $dir = self::join_path(CMS_ROOT_PATH, 'uploads');
        if( $advancedmode ) $dir = CMS_ROOT_PATH;

        $dir = cms_relative_path( $dir, CMS_ROOT_PATH );
        return $dir;
    }

    public static function test_valid_path($path)
    {
        // returns false if invalid.
        $config = \cms_config::get_instance();
        $advancedmode = filemanager_utils::check_advanced_mode();

        $prefix = CMS_ROOT_PATH;
        if( $path === '/' ) $path = null;
        $path = self::join_path($prefix,$path);
        $rpath = realpath($path);
        if( $rpath === FALSE ) return false;

        if (!$advancedmode) {
            // uploading in 'non advanced mode', path has to start with the upload dir.
            $uprp = realpath($config['uploads_path']);
            if (startswith($rpath,$uprp)) return true;
        }
        else {
            // advanced mode, path has to start with the root path.
            $rprp = realpath($config['root_path']);
            if (startswith($path,$rprp)) return true;
        }
        return false;
    }

    public static function get_cwd()
    {
        // check the path
        $path = cms_userprefs::get('filemanager_cwd',self::get_default_cwd());
        if( !self::test_valid_path($path) ) {
            $path = self::get_default_cwd();
        }
        //if( $path == '' ) $path = '/'; causes double // in site root
        return $path;
    }

    public static function set_cwd($path)
    {
        if( startswith($path,CMS_ROOT_PATH) ) $path = cms_relative_path($path,CMS_ROOT_PATH);
        $advancedmode = self::check_advanced_mode();

        // validate the path.
        $tmp = self::join_path(CMS_ROOT_PATH,$path);
        $tmp = realpath($tmp);
        if( !$tmp || !is_dir($tmp) ) throw new Exception('Cannot set current working directory to an invalid path');
        $newpath = cms_relative_path($tmp,CMS_ROOT_PATH);
        if( !self::test_valid_path($newpath) ) throw new Exception('Cannot set current working directory to an invalid path');

        $newpath = str_replace('\\','/',$newpath);
        cms_userprefs::set('filemanager_cwd',$newpath);
    }


    public static function join_path()
    {
        $args = func_get_args();
        if( count($args) < 1 ) return;
        if( count($args) < 2 ) return $args[0];

        $args2 = array();
        for( $i = 0; $i < count($args); $i++ ) {
            if( $args[$i] == '' ) continue;
            if( $i != 0 && (startswith($args[$i],'/') || startswith($args[$i],'\\')) ) $args[$i] = substr($args[$i],1);
            if( endswith($args[$i],'/') || endswith($args[$i],'\\') ) $args[$i] = substr($args[$i],0,-1);
            $args2[] = $args[$i];
        }

        return implode(DIRECTORY_SEPARATOR,$args2);
    }

    public static function get_full_cwd()
    {
        $path = self::get_cwd();
        $config = cmsms()->GetConfig();
        if( !self::test_valid_path($path) ) $path = self::get_default_cwd();
        $base = $config['root_path'];
        $realpath = self::join_path($base,$path);
        return $realpath;
    }

    public static function get_cwd_url()
    {
        $path = self::get_cwd();
        $config = cmsms()->GetConfig();
        if( !self::test_valid_path($path) ) $path = self::get_default_cwd();
        $url = $config['root_url'].'/'.$path;
        return $url;
    }

    public static function is_image_file($file)
    {
        // it'd be nice to check mime type here.
        $ext = substr(strrchr($file, '.'), 1);
        if( !$ext ) return FALSE;

        $tmp = array('gif','jpg','jpeg','png');
        if( in_array(strtolower($ext),$tmp) ) return TRUE;
        return FALSE;
    }

    public static function is_archive_file($file)
    {
        $tmp = array('.tar.gz','.tar.bz2','.zip','.tgz');
        foreach( $tmp as $t2 ) {
            if( endswith(strtolower($file),$t2) ) return TRUE;
        }
        return FALSE;
    }

    public static function get_file_list($path = '')
    {
        if( !$path ) $path = self::get_cwd();
        $advancedmode = self::check_advanced_mode();
        $filemod = cms_utils::get_module('FileManager');
        $showhiddenfiles=$filemod->GetPreference('showhiddenfiles','1');
        $result=array();
        $config = \cms_config::get_instance();

        // convert the cwd into a real path... slightly different for advanced mode.
        $realpath = self::join_path($config['root_path'],$path);

        $dir=@opendir($realpath);
        if (!$dir) return false;
        while ($file=readdir($dir)) {
            if ($file=='.') continue;
            if ($file=='..') {
                // can we go up.
                if( $path == self::get_default_cwd() || $path == '/' ) continue;
            } else {
                if ($file[0]=='.' || $file[0] == '_' || $file[0] == '~') {
                    if (($showhiddenfiles!=1) || (!$advancedmode)) continue;
                }
            }

            if (substr($file,0,6)=='thumb_') {
                //Ignore thumbnail files of showing thumbnails is off
                if ($filemod->GetPreference('showthumbnails','1')=='1') continue;
            }

            // build the file info array.
            $fullname = self::join_path($realpath,$file);
            $info=array();
            $info['name']=$file;
            $info['dir'] = FALSE;
            $info['image'] = FALSE;
            $info['archive'] = FALSE;
            $info['mime'] = self::mime_content_type($fullname);
            $statinfo=stat($fullname);

            if (is_dir($fullname)) {
                $info['dir']=true;
                $info['ext']='';
                $info['fileinfo']=GetFileInfo($fullname,'',true);
            } else {
                $info['size']=$statinfo['size'];
                $info['date']=$statinfo['mtime'];
                $info['url']=self::join_path($config['root_url'], $path, $file);
                $info['url'] = str_replace('\\','/',$info['url']); // windoze both sucks, and blows.
                $explodedfile=explode('.', $file); $info['ext']=array_pop($explodedfile);
                $info['fileinfo']=GetFileInfo(self::join_path($realpath,$file),$info['ext'],false);
            }

            // test for archive
            $info['archive'] = self::is_archive_file($file);

            // test for image
            $info['image'] = self::is_image_file($file);

            if (function_exists('posix_getpwuid')) {
                $userinfo = @posix_getpwuid($statinfo['uid']);
                $info['fileowner']= isset($userinfo['name'])?$userinfo['name']:$filemod->Lang('unknown');
            } else {
                $info['fileowner']='N/A';
            }

            $info['writable']=is_writable(self::join_path($realpath,$file));
            if (function_exists('posix_getpwuid')) {
                $info['permissions']=filemanager_utils::format_permissions($statinfo['mode'],$filemod->GetPreference('permissionstyle','xxx'));
            } else {
                if ($info['writable']) {
                    $info['permissions']='R';
                } else {
                    $info['permissions']='R';
                }
            }

            $result[]=$info;
        }

        $tmp = usort($result,'filemanager_utils::_FileManagerCompareFiles');
        return $result;
    }

    private static function _FileManagerCompareFiles($a,$b,$forcesort="") {
        $filemod = cms_utils::get_module('FileManager');
        $sortby=$filemod->GetPreference("sortby","nameasc");
        if ($forcesort!="") $sortby=$forcesort;
        if ($a["name"]=="..") return -1;
        if ($b["name"]=="..") return 1;
        /*print_r($a);
          print_r($b);*/
        //Handle if only one is a dir
        if ($a["dir"] XOR $b["dir"]) {
            if ($a["dir"]) return -1; else return 1;
        }

        switch($sortby) {
        case "nameasc" : return strncasecmp($a["name"],$b["name"],strlen($a["name"]));
        case "namedesc" : return strncasecmp($b["name"],$a["name"],strlen($b["name"]));
        case "sizeasc" : {
            if ($a["dir"] && $b["dir"]) return self::_FileManagerCompareFiles($a,$b,"nameasc");
            return ($a["size"]>$b["size"]);
        }
        case "sizedesc" : {
            if ($a["dir"] && $b["dir"]) return self::_FileManagerCompareFiles($a,$b,"nameasc");
            return ($b["size"]>$a["size"]);
        }
        default : strncasecmp($a["name"],$b["name"],strlen($a["name"]));
        }
        return 0;
    }

    public static function mime_content_type($filename)
    {
        $mime_type = null;
        if( version_compare(phpversion(),'5.3','ge') && function_exists('finfo_open') ) {
            $fh = finfo_open(FILEINFO_MIME_TYPE);
            if( $fh ) {
                $mime_type = finfo_file($fh,$filename);
                finfo_close($fh);
                return $mime_type;
            }
        }

        if(!function_exists('mime_content_type')) {

            // Try to recreate a "very" simple mechanism for mime_content_type($filename);
            function mime_content_type($filename) {

                $mime_types = array(
                    'txt' => 'text/plain',
                    'htm' => 'text/html',
                    'html' => 'text/html',
                    'php' => 'text/html',
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'json' => 'application/json',
                    'xml' => 'application/xml',
                    'swf' => 'application/x-shockwave-flash',
                    'flv' => 'video/x-flv',

                    // images
                    'png' => 'image/png',
                    'jpe' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'jpg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'bmp' => 'image/bmp',
                    'ico' => 'image/vnd.microsoft.icon',
                    'tiff' => 'image/tiff',
                    'tif' => 'image/tiff',
                    'svg' => 'image/svg+xml',
                    'svgz' => 'image/svg+xml',

                    // archives
                    'zip' => 'application/zip',
                    'rar' => 'application/x-rar-compressed',
                    'exe' => 'application/x-msdownload',
                    'msi' => 'application/x-msdownload',
                    'cab' => 'application/vnd.ms-cab-compressed',

                    // audio/video
                    'mp3' => 'audio/mpeg',
                    'qt' => 'video/quicktime',
                    'mov' => 'video/quicktime',

                    // adobe
                    'pdf' => 'application/pdf',
                    'psd' => 'image/vnd.adobe.photoshop',
                    'ai' => 'application/postscript',
                    'eps' => 'application/postscript',
                    'ps' => 'application/postscript',

                    // ms office
                    'doc' => 'application/msword',
                    'rtf' => 'application/rtf',
                    'xls' => 'application/vnd.ms-excel',
                    'ppt' => 'application/vnd.ms-powerpoint',

                    // open office
                    'odt' => 'application/vnd.oasis.opendocument.text',
                    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
                    );

                $ext = explode('.',$filename);
                $ext = strtolower(array_pop($ext));
                if (array_key_exists($ext, $mime_types)) {
                    return $mime_types[$ext];
                }
                elseif (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME);
                    $mimetype = finfo_file($finfo, $filename);
                    finfo_close($finfo);
                    return $mimetype;
                }
                else {
                    //Nothing instead of "application/octet-stream"
                    return '';
                }
            }
        }

        // Now we can call this function
        return mime_content_type($filename);
    }

    // get post max size and give a portion of it to smarty for max chunk size.
    public static function str_to_bytes($val)
    {
        if(is_string($val) && $val != '') {
            $val = trim($val);
            $last = strtolower($val[strlen($val)-1]);
            if( $last < '<' || $last > 9 ) $val = substr($val,0,-1);
            $val = (int) $val;
            switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
            }
        }

        return (int) $val;
    }

    public static function get_dirlist()
    {
        $config = CmsApp::get_instance()->GetConfig();
        $mod = cms_utils::get_module('FileManager');
        $showhiddenfiles = $mod->GetPreference('showhiddenfiles');
        $startdir = $config['uploads_path'];
        $advancedmode = self::check_advanced_mode();
        if( $advancedmode ) $startdir = $config['root_path'];

        // now get a simple list of all of the directories we have 'write' access to.
        function fmutils_get_dirs($startdir,$prefix = '/') {
            $res = array();
            if( !is_dir($startdir) ) return;

            global $showhiddenfiles;
            $dh = opendir($startdir);
            while( false !== ($entry = readdir($dh)) ) {
                if( $entry == '.' ) continue;
                $full = filemanager_utils::join_path($startdir,$entry);
                if( !is_dir($full) ) continue;
                if( !$showhiddenfiles && ($entry[0] == '.' || $entry[0] == '_') ) continue;

                if( $entry == '.svn' || $entry == '.git' ) continue;
                $res[$prefix.$entry] = $prefix.$entry;
                $tmp = fmutils_get_dirs($full,$prefix.$entry.'/');
                if( is_array($tmp) && count($tmp) ) $res = array_merge($res,$tmp);
            }
            closedir($dh);
            return $res;
        }

        $output = fmutils_get_dirs($startdir,'/');
        if( is_array($output) && count($output) ) {
            ksort($output);
            $tmp = [];
            if( $advancedmode ) {
                $tmp['/'] = '/'.basename($startdir).' ('.$mod->Lang('site_root').')';
            }
            else {
                $tmp['/'] = '/'.basename($startdir).' ('.$mod->Lang('top').')';
            }
            $output = array_merge($tmp,$output);
        }
        return $output;
    }

    public static function create_thumbnail($src,$dest = null)
    {
        if( !file_exists($src) ) return FALSE;
        if( !$dest ) {
            $bn = basename($src);
            $dn = dirname($src);
            $dest = $dn.'/thumb_'.$bn;
        }
        if( file_exists($dest) && !is_writable($dest) ) return FALSE;

        $info = getimagesize($src);
        if( !$info || !isset($info['mime']) ) return FALSE;

        $i_src = imagecreatefromstring(file_get_contents($src));
        $width = cms_siteprefs::get('thumbnail_width',96);
        $height = cms_siteprefs::get('thumbnail_height',96);

        $i_dest = imagecreatetruecolor($width,$height);
        imagealphablending($i_dest,FALSE);
        $color = imageColorAllocateAlpha($i_src, 255, 255, 255, 127);
        imagecolortransparent($i_dest,$color);
        imagefill($i_dest,0,0,$color);
        imagesavealpha($i_dest,TRUE);
        imagecopyresampled($i_dest,$i_src,0,0,0,0,$width,$height,imagesx($i_src),imagesy($i_src));

        $res = null;
        switch( $info['mime'] ) {
        case 'image/gif':
            $res = imagegif($i_dest,$dest);
            break;
        case 'image/png':
            $res = imagepng($i_dest,$dest,9);
            break;
        case 'image/jpeg':
            $res = imagejpeg($i_dest,$dest,100);
            break;
        }

        if( !$res ) return FALSE;
        return TRUE;
    }

    public static function format_filesize($_size) {
        $mod = cms_utils::get_module('FileManager');
        $unit=$mod->Lang("bytes");
        $size=$_size;

        if ($size>10000 && $size<(1024*1024)) {
            $size=round($size/1024);
            $unit=$mod->Lang("kb");
        }

        if ($size>(1024*1024)) {
            $size=round($size/(1024*1024),1);
            $unit=$mod->Lang("mb");
        }

        $lcc = localeconv();
        $size = number_format($size,0,$lcc['decimal_point'],$lcc['thousands_sep']);

        $result=array();
        $result["size"]=$size;
        $result["unit"]=$unit;
        return $result;
    }

    public static function format_permissions($mode,$style='xxx') {
        switch ($style) {
        case 'xxx':
            $owner=0;
            if ($mode & 0400) $owner+=4;
            if ($mode & 0200) $owner+=2;
            if ($mode & 0100) $owner+=1;
            $group=0;
            if ($mode & 0040) $group+=4;
            if ($mode & 0020) $group+=2;
            if ($mode & 0010) $group+=1;
            $others=0;
            if ($mode & 0004) $others+=4;
            if ($mode & 0002) $others+=2;
            if ($mode & 0001) $others+=1;
            return $owner.$group.$others;

        case 'xxxxxxxxx':
            $owner="";
            if ($mode & 0400) $owner.="r"; else $owner.="-";
            if ($mode & 0200) $owner.="w"; else $owner.="-";
            if ($mode & 0100) $owner.="x"; else $owner.="-";
            $group="";
            if ($mode & 0040) $group.="r"; else $group.="-";
            if ($mode & 0020) $group.="w"; else $group.="-";
            if ($mode & 0010) $group.="x"; else $group.="-";
            $others="";
            if ($mode & 0004) $others.="r"; else $others.="-";
            if ($mode & 0002) $others.="w"; else $others.="-";
            if ($mode & 0001) $others.="x"; else $others.="-";
            return $owner.$group.$others;
        }
    }
} // end of class

#
# EOF
#
