#!/usr/bin/php
<?php

//
// some debian based distros don't have gzopen (crappy)
//
if (!extension_loaded('zlib')) {
    die('Abort '.basename(__FILE__).' : Missing zlib extensions');
}
if (!function_exists('gzopen') && function_exists('gzopen64')) {
    function gzopen($filename , $mode, $use_include_path = 0) {
        return gzopen64($filename, $mode, $use_include_path);
    }
}

//
// begin application
//
$_debug = FALSE;
$_compress = TRUE;
$_scriptname = basename($argv[0]);
$_config = array('do_md5'=>FALSE,'repos_root'=>'http://svn.cmsmadesimple.org/svn/cmsmadesimple','repos_from'=>'','repos_to'=>'','mode'=>'f','outfile'=>'MANIFEST.DAT');
$_tmpdir = sys_get_temp_dir()."/{$_scriptname}.".getmypid();
$_tmpfile = "$_tmpdir/tmp.out";
$_configfile = get_config_filename();
$_writecfg = TRUE;
$_interactive = TRUE;
$_outfile = STDOUT;
$_notdeleted = null;

@pcntl_signal(SIGTERM,'sighandler');
@pcntl_signal(SIGINT,'sighandler');

$opts = getopt('mdc:r:f:t:o:h',array('md5','debug','config:','root:','from:','to:','nowrite','quick','mode','outfile','nocompress','dnd:'));
// parse arguments for config file.
foreach( $opts as $key => $val ) {
  switch( $key ) {
  case 'c':
  case 'config':
    $_configfile = $val;
    break;
  }
}

// attempt to read config file
if( $_configfile && $_configfile != '-' ) {
  if( !is_readable($_configfile) ) fatal("No valid config file at: $_configfile");
  $_config = parse_ini_file($_configfile);
  if( $_config === FALSE ) fatal("Problem processing config file: $_configfile");
  info('Read config file from '.$_configfile);
}

// parse arguments again
foreach( $opts as $key => $val ) {
  switch( $key ) {
  case 'd':
  case 'debug':
    $_debug = TRUE;
    break;

  case 'm':
  case 'md5':
    $_config['md5'] = TRUE;
    break;

  case 'r':
  case 'root':
    $_config['repos_root'] = trim($val);
    break;

  case 'f':
  case 'from':
    $_config['repos_from'] = trim($val);
    break;

  case 't':
  case 'to':
    $_config['repos_to'] = trim($val);
    break;

  case 'nowrite':
    $_writecfg = FALSE;
    break;

  case 'quick':
    $_interactive = FALSE;
    break;

  case 'mode':
    $val = trim($val);
    $val = strtolower($val[0]);
    switch( $val ) {
    case 'f':
    case 'd':
    case 'c':
    case 'a':
      $_config['mode'] = $val;
    }
    break;

  case 'o':
  case 'outfile':
    $val = trim($val);
    $_config['outfile'] = $val;
    break;

  case 'nocompress':
      $_compress = FALSE;
      break;

  case 'dnd':
      if( $val ) {
          $tmp = explode(',',$val);
          foreach( $tmp as $one ) {
              $one = trim($one);
              if( !startswith($one,'/') ) $one = '/'.$one;
              while( endswith($one,'*') || endswith($one,'/') ) $one = substr($one,0,-1);
              if( $one ) $_notdeleted[] = $one;
          }
      }
      break;

  case 'h':
  case 'help':
    usage();
    exit;
  }
}

// if we don't have a repos_to branch, find our current one.
if( !$_config['repos_to'] ) {
    $_config['repos_to'] =  get_svn_branch();
}

if( $_compress ) {
    if( !endswith( $_config['outfile'], '.gz' ) ) $_config['outfile'] = $_config['outfile'] . '.gz';
}
else {
    if( endswith( $_config['outfile'], '.gz' ) ) $_config['outfile'] = substr($_config['outfile'], 0, -3);
}

// interactive mode
if( $_interactive ) {
    $_done = false;
    $_config['repos_root'] = ask_string("Enter repository root",$_config['repos_root']);
    $_config['repos_from'] = ask_string("Enter from subpath",$_config['repos_from']);
    $_config['repos_to'] = ask_string("Enter to subpath",$_config['repos_to']);
    $_config['mode'] = ask_options("Enter manifest mode (d|n|c|f)",array('d','n','c','f'),$_config['mode']);
    $_config['outfile'] = ask_string("Enter output file",$_config['outfile']);
}


//
// begin doing the work
//
if( $_config['outfile'] != '-' && $_config['outfile'] != '' ) $_outfile = $_config['outfile'];

// validate the config
if( !isset($_config['repos_root']) || !$_config['repos_root'] ) fatal("No repository root found");
if( !isset($_config['repos_from']) || !$_config['repos_from'] ) fatal("No repository from subpath found");
if( !isset($_config['repos_to']) || !$_config['repos_to'] ) fatal("No repository to subpath found");
if( !endswith( $_config['repos_root'], '/' ) ) $_config['repos_root'] .= '/';
if( startswith( $_config['repos_from'], '/' ) ) $_config['repos_from'] = ltrim($_config['repos_from'],'/');
if( startswith( $_config['repos_to'], '/' ) ) $_config['repos_to'] = ltrim($_config['repos_to'],'/');

// create temp directory
@mkdir($_tmpdir);
$_fromdir = "{$_tmpdir}/_from";
$_todir = "{$_tmpdir}/_to";

// export from repository
$res = svn_export($_config['repos_root'].$_config['repos_from'],$_fromdir);
$res = svn_export($_config['repos_root'].$_config['repos_to'],$_todir);

// basic checks
if( !is_file($_fromdir."/lib/version.php") || !is_readable($_fromdir."/lib/classes/class.CMSModule.php" ) ) fatal("$_fromdir does not appear to be a CMSMS installation");
if( !is_file($_todir."/lib/version.php") || !is_readable($_todir."/lib/classes/class.CMSModule.php" ) ) fatal("$_todir does not appear to be a CMSMS installation");

// get our from version and to version
$_from_ver = null;
$_from_name = null;
$_to_ver = null;
$_to_name = null;
$_get_ver = function($basedir) {
    $_files = array("$basedir/lib/version.php","$basedir/version.php");
    foreach( $_files as $_one ) {
        if( is_file($_one) ) {
            @include($_one);
            $_ver = $CMS_VERSION;
            $_name = $CMS_VERSION_NAME;
            return array($_ver,$_name);
        }
    }
};
list($_from_ver,$_to_name) = $_get_ver($_fromdir);
list($_to_ver,$_to_name) = $_get_ver($_todir);

// do the comparison
$obj = new compare_dirs($_fromdir,$_todir);
$obj->ignore(array('.svn','svn-*'));
$obj->ignore(array('*.bak','*~'));
$obj->ignore(array('*.sh','*.pl'));
$obj->ignore('.#*');
$obj->ignore('#*');
$obj->ignore('tmp');
$obj->ignore('scripts');
$obj->ignore('install');
$obj->ignore('phar_installer');
$obj->ignore('config.php');
$obj->ignore('uploads');
if( $_notdeleted ) {
    foreach( $_notdeleted as $one ) {
        $obj->do_not_delete( $one );
    }
}

// begin output of manifest
output('MANIFEST GENERATED: '.time());
output("MANIFEST FROM VERSION: $_from_ver");
output("MANIFEST FROM NAME: $_from_name");
output("MANIFEST TO VERSION: $_to_ver");
output("MANIFEST TO NAME: $_to_name");
if( $_notdeleted ) output('MANIFEST SKIPPED: '.implode(', ',$_notdeleted));

if( $_config['mode'] == 'd' || $_config['mode'] == 'f' ) {
    $out = $obj->get_deleted_files();
    foreach( $out as $fn ) {
        $file = "${_fromdir}/{$fn}";
        $md5 = md5_file($file);
        $str = "DELETED :: $md5 :: $fn";
        output($str);
    }
}

if( $_config['mode'] == 'c' || $_config['mode'] == 'f' ) {
    $out = $obj->get_changed_files();
    foreach( $out as $fn ) {
        $file = "${_todir}/{$fn}";
        if( is_dir($file) ) continue;
        $md5 = md5_file($file);
        $str = "CHANGED :: $md5 :: $fn";
        output($str);
    }
}

if( $_config['mode'] == 'n' || $_config['mode'] == 'f' ) {
    $out = $obj->get_new_files();
    foreach( $out as $fn ) {
        $file = "${_todir}/{$fn}";
        $md5 = md5_file($file);
        $str = "ADDED :: $md5 :: $fn";
        output($str);
    }
}

$_configfile = get_config_filename(TRUE);
debug("configfile is $_configfile");
if( $_writecfg && $_configfile && ( !is_file($_configfile) || is_writable($_configfile) ) ) {
    info('Writing INI file to '.$_configfile);
    write_ini_file($_config,$_configfile);
}

if( $_compress ) {
    info('Compressing manifest');
    $_gzfile = $_tmpfile.'.gz';
    $_fh = gzopen( $_gzfile, 'w9' );
    gzwrite( $_fh, file_get_contents($_tmpfile) );
    gzclose( $_fh );
    copy( $_gzfile, $_tmpfile );
    @unlink( $_gzfile );
}

if( $_outfile == STDOUT ) {
    readfile($_tmpfile);
}
else {
    info('Copy manifst to '.$_outfile);
    copy($_tmpfile,$_outfile);
}

cleanup();
info("DONE");
exit(0);

/////////////////////////////////
// BEGIN CLASSES AND FUNCTIONS //
/////////////////////////////////

  //
  // simple functions
  //
  function usage()
  {
    global $_scriptname;
    echo <<<EOT
This is script compares two CMSMS svn sub-paths from the same svn repository to generate a manifest of which files have been added/changed/deleted between versions.  This file should be placed in the app/upgrade/<to_version>/MANIFEST.DAT file to facilitate the cleaning up, and verification of files during the upgrade process.

Ideally this script should be executed FROM the app/upgrade/<to_version> directory.

This script will export the two svn directories, verify that they are are indeed CMSMS root directories, and compare them.
EOT;

    echo "usage: php $_scriptname [options]\n";
    echo "options\n";
    echo "  -c|--config <string> = config file specification (enter a - to skip reading a saved config file)\n";
    echo "  -d|--debug           = enable debug mode\n";
    echo "  -r|--root <string>   = svn repository root\n";
    echo "  -f|--from <string>   = from repository sub-path\n";
    echo "  -t|--to <string>     = to repository sub-path\n";
    echo "  -m|--md5             = enable md5 file comparison\n";
    echo "  -o|--outfile         = set the output file name\n";
    echo "  -h|--help            = display this message and exit\n";
    echo "  --nocompress         = do not gz compress the output manifest file\n";
    echo "  --nowrite            = do not save the config file with entered values.\n";
    echo "  --quick              = disable interactive mode\n";
    echo "  --mode (d|n|c|f)     = output deleted/new/changed/full manifest\n";
    echo "  --dnd <string>       = specify a comma separated list of paths (relative to the CMSMS root) as DO NOT DELETE\n";
    echo "                         (no delete records will be output into the manifest for matching files)";
    echo "                         This is useful if files will be moved manually during the upgrade process";
    echo "\n";
  }

  function output($str)
  {
    global $_tmpfile;
    static $_mode = 'a';
    $fh = fopen($_tmpfile,$_mode);
    $_mode = 'a';
    if( !$fh ) fatal('Problem opening file ('.$_tmpfile.') for writing');
    fwrite($fh,"$str\n");
    fclose($fh);
  }

  function info($str)
  {
    fwrite(STDERR,"INFO: $str\n");
  }

  function debug($str)
  {
    global $_debug;
    if( $_debug ) fwrite(STDERR,"DEBUG: $str\n");
  }

  function fatal($str)
  {
    fwrite(STDERR,"FATAL: $str\n");
    cleanup();
    exit(1);
  }

  function startswith($haystack,$needle)
  {
      return ( substr($haystack,0,strlen($needle)) == $needle );
  }

  function endswith($haystack,$needle)
  {
    return (substr($haystack,-1*strlen($needle)) == $needle);
  }

  function get_svn_branch()
  {
    $cmd = "svn info | grep '^URL:' | egrep -o '(tags|branches)/[^/]+|trunk'";
    $out = exec($cmd);
    return $out;
  }

  function rrmdir($dir)
  {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
	if ($object != "." && $object != "..") {
	  if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
	}
      }
      reset($objects);
      rmdir($dir);
    }
  }

  function sighandler($signum)
  {
    info('Signal received');
    cleanup();
    exit(1);
  }

  function cleanup($signum = null)
  {
    global $_tmpdir;
    debug("Cleaning up");
    rrmdir($_tmpdir);
  }

  function svn_export($repos_url,$dest)
  {
    info("Running SVN export on $repos_url");
    $_cmd = escapeshellcmd("svn export $repos_url $dest");
    $_output = null;
    $_exitcode = null;
    debug($_cmd);
    $res = exec($_cmd,$_output,$_exitcode);
    if( $_exitcode != 0 ) fatal($res);
  }

  function ask_string($prompt,$dflt = null,$allow_empty = FALSE)
  {
    while( 1 ) {
      if( $dflt ) $prompt = $prompt." [default=$dflt]: ";
      if( !endswith($prompt,': ') || !endswith($prompt,' ') ) $prompt .= ': ';
      $tmp = trim(readline('INPUT: '.$prompt));
      if( $tmp ) return $tmp;

      if( $allow_empty ) return;
      if( $dflt ) return $dflt;
      echo "ERROR: Invalid input.  Please try again\n";
    }
  }

  function ask_options($prompt,$options,$dflt)
  {
    while( 1 ) {
      if( $dflt ) $prompt = $prompt." [default=$dflt] :";
      if( !endswith($prompt,': ') || !endswith($prompt,' ') ) $prompt .= ': ';
      $tmp = trim(readline('INPUT: '.$prompt));

      if( !$tmp ) $tmp = $dflt;
      if( in_array($tmp,$options) ) return $tmp;

      echo "ERROR: Invalid input.  Please enter one of the valid options\n";
    }
  }

  function write_ini_file($config_data,$filename)
  {
    @copy($filename,$filename.".bak");
    $fh = fopen($filename,'w');
    fwrite( $fh, '[config]' );
    foreach( $config_data as $key => $val ) {
      if( !is_numeric($val) ) $val = '"'.$val.'"';
      fwrite( $fh, "$key = $val\n" );
    }
    fclose( $fh );
  }

  function get_config_filename($skip_exists_check = FALSE) {
    // detect users home directory.
    global $_scriptname;
    $home = getenv('HOME');
    if( $home ) $home = realpath($home);
    if( is_dir($home) && is_readable($home) ) {
      $file = "$home/.{$_scriptname}";
      if( $skip_exists_check ) return $file;
      if( is_readable($file) ) return $file;
    }
  }

  //
  // a simple class to compare directories
  //
  class compare_dirs
  {
    private $_a;
    private $_b;
    private $_do_md5;
    private $_has_run = null;
    private $_base_dir;
    private $_ignored = array();
    private $_donotdelete;

    public function __construct($dir_a,$dir_b,$do_md5 = false)
    {
      if( !is_dir($dir_a) ) throw new Exception('Invalid directory '.$dir_a);
      if( !is_readable($dir_a) ) throw new Exception('Directory '.$dir_a.' is not readable');
      if( !is_dir($dir_b) ) throw new Exception('Invalid directory '.$dir_b);
      if( !is_readable($dir_b) ) throw new Exception('Directory '.$dir_b.' is not readable');

      $this->_a = $dir_a;
      $this->_b = $dir_b;
      $this->_do_md5 = (bool)$do_md5;
    }

    public function do_not_delete( $in )
    {
        $this->_donotdelete[] = $in;
    }

    public function ignore($str)
    {
        if( !$str ) return;
        if( is_string($str) ) $str = array($str);

        foreach( $str as $one ) {
            $one = trim($one);
            if( !$one ) continue;
            $this->_ignored[] = $one;
        }
    }

    private function _set_base($dir)
    {
      $this->_base_dir = $dir;
    }

    private function _get_base()
    {
      return $this->_base_dir;
    }

    private function _is_ignored($filename)
    {
      foreach( $this->_ignored as $pattern ) {
	if( $pattern == $filename ||  fnmatch($pattern,$filename,FNM_CASEFOLD) ) return TRUE;
      }
      return FALSE;
    }

    private function _read_dir($dir = null)
    {
      if( !$dir ) $dir = $this->_base_dir;
      if( !$dir ) throw new Exception('No directory specified to _read_dir');

      $out = array();
      $dh = opendir($dir);
      if( !$dh ) throw new Exception('Problem getting directory handle for '.$dir);

      while( ($file = readdir($dh)) !== false ) {
	if( $file == '.' || $file == '..' ) continue;
	$fn = "$dir/$file";

	if( $this->_is_ignored($file) ) continue;

	$base = substr($fn,strlen($this->_get_base()));
	if( is_dir($fn) ) {
	  $tmp = $this->_read_dir($fn);
	  $out = array_merge($out,$tmp);
	  $rec = array();
          $rec['size'] = @filesize($fn);
          $rec['mtime'] = @filemtime($fn);
	  if( $this->_do_md5 ) $rec['md5'] = md5_file($fn);
	  $out[$base] = $rec;
	  continue;
	}

	if( !is_readable($fn) ) {
        debug("$fn is not readable");
        continue;
	}

	$rec = array();
	$rec['size'] = @filesize($fn);
	$rec['mtime'] = @filemtime($fn);
	if( $this->_do_md5 ) $rec['md5'] = md5_file($fn);
	$out[$base] = $rec;
      }

      return $out;
    }

    public function run()
    {
      if( $this->_has_run ) return;
      $this->_has_run = TRUE;

      $this->_set_base($this->_a);
      $this->_list_a = $this->_read_dir();
      $this->_set_base($this->_b);
      $this->_list_b = $this->_read_dir($this->_b);
    }

    public function get_new_files()
    {
      $this->run();

      // get all the files in b that are not in a
      $tmp_a = array_keys($this->_list_a);
      $tmp_b = array_keys($this->_list_b);
      return array_diff($tmp_b,$tmp_a);
    }

    public function get_deleted_files()
    {
      $this->run();

      // get all the files in b that are not in a
      $tmp_a = array_keys($this->_list_a);
      $tmp_b = array_keys($this->_list_b);
      $out = array_diff($tmp_a,$tmp_b);
      if( !empty($out) && !empty($this->_donotdelete) ) {
          foreach( $out as $file ) {
              $skipped = false;
              foreach( $this->_donotdelete as $nd ) {
                  if( startswith( $file, $nd ) ) {
                      // skip this file at this stage.
                      $skipped = true;
                      break;
                  }
              }
              if( !$skipped ) {
                  $new_out[] = $file;
              }
              else {
                  debug('skipped '.$file.', it is in the notdeleted list');
              }
          }
          $out = $new_out;
      }
      return $out;
    }

    public function get_changed_files()
    {
      $this->run();

      $out = array();
      foreach($this->_list_a as $path => $rec_a ) {
	if( !isset($this->_list_b[$path]) ) continue; // deleted/moved in b.
	$rec_b = $this->_list_b[$path];
	if( $rec_a['size'] != $rec_b['size'] || $rec_a['mtime'] != $rec_b['mtime'] ||
	    (isset($rec_a['md5']) && isset($rec_b['md5']) && $rec_a['md5'] != $rec_b['md5']) ) {
	  $out[] = $path;
	}
      }
      return $out;
    }
  } // end of class

?>
