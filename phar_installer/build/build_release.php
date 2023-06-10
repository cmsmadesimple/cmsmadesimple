#!/usr/bin/php
<?php
$cli = (php_sapi_name() == 'cli');
//if( !$cli ) throw new Exception('This script must be executed via the CLI');
//if( $cli && !isset($argv) ) throw new Exception('This script must be executed via the CLI');
if( ini_get('phar.readonly') ) throw new Exception('phar.readonly must be turned OFF in the php.ini');
// make sure we are in the correct directory
//if( $cli ) $script_file = basename($argv[0]);
$owd = getcwd();
//if( !file_exists("$owd/$script_file") ) throw new Exception('This script must be executed from the same directory as the '.$script_file.' script');
$rootdir = dirname(__DIR__);
$repos_root='http://svn.cmsmadesimple.org/svn/cmsmadesimple';
//$repos_branch = "/trunk";
$repos_branch = '';
$srcdir = $rootdir;
$tmpdir = $rootdir.'/tmp';
$datadir = $rootdir.'/data';
$outdir = $rootdir.'/out';
//TODO update these lists, per the following variables
//do not skip class.cms_config.php or Smarty files like smarty_internal_method*config.php
$exclude_patterns = array('/\.svn\//','/^ext\//','/^build\/.*/','/.*~$/','/tmp\/.*/','/\.\#.*/','/\#.*/','/^out\//','/^README*TXT/');
$exclude_from_zip = array('*~','tmp/','.#*','#*'.'*.bak');
$src_excludes = array('/\/phar_installer\//','/\/config\.php$/', '/\/find-mime$/', '/\/installer\//', '/^\/tmp\/.*/', '/^#.*/', '/^\/scripts\/.*/', '/\.git/', '/\.svn/', '/svn-.*/',
                      '/^\/tests\/.*/', '/^\/build\/.*/', '/^\.htaccess/', '/\.svn/', '/^config\.php$/','/.*~$/', '/\.\#.*/', '/\#.*/', '/.*\.bak/');

// regex patterns for source files/dirs to NOT be processed by the installer.
// all exclusion checks are against sources-tree root-dir-relative filepaths,
// after converting any windoze path-sep's to *NIX form
// NOTE: otherwise empty folders retain, or are given, respective index.html's
// so that they are not ignored by PharData when processing
$all_excludes = [
'~\.git.*~',
'~\.md$~i',
'~\.svn~',
'~svn\-~',
'~index\.html?$~',
'~[\\/]config\.php$~',
'~siteuuid\.dat$~',
'~\.htaccess$~',
'~web\.config$~',
'~\.bak$~',
'/~$/',
'~\.#~',
'~UNUSED~',
'~DEVELOP~',
'~HIDE~',
];

// members of $src_excludes which need double-check before exclusion to confirm they're 'ours'
$src_checks = ['scripts', 'tmp', 'tests'];

$s = basename($rootdir);
$src_excludes = [
-4 => "~$s~",
-3 => '~scripts~',
-2 => '~tmp~',
-1 => '~tests~',
] + $all_excludes;

// root-relative sub-paths of source dirs whose actual contents are NOT for installation with sources in general.
// instead their real contents will be handled by the site-importer, and pending that, just an empty 'index.html'
$folder_excludes = [
'assets/templates',
'assets/styles',
'assets/themes',
'assets/user_plugins',
];
/*
$phar_excludes = [
'~build~',
'~data~',
'~out~',
] + $all_excludes;
//'/README.*  REMOVE THIS GAP IF UNCOMMENTED  /',
$phar_excludes = $all_excludes;
// members of $phar_excludes which need double-check before exclusion to confirm they're 'ours'
$phar_checks = ['build', 'data', 'out'];
*/

$archive_only = 0;
$checksums = 1;
$clean = 0;
$indir = ''; // hence default to latest release in svn
$priv_file = __DIR__.'/priv.pem'; //seems unused
$pub_file = __DIR__.'/pub.pem'; //seems unused
$rename = 1;
$sourceuri = 'file://'; // sources file-set locator
$verbose = 0;
$version_num = '';
$zip = 1;
// custom settings
$fp = __DIR__.DIRECTORY_SEPARATOR.'build_release.ini';
$xconfig = ( is_file($fp) ) ? parse_ini_file($fp, false, INI_SCANNER_TYPED) : [];
foreach( $xconfig as $k => $v ) {
    switch( $k ) {
    case 'archive_only':
        $archive_only = (int)$v;
        break;
    case 'checksums':
        $checksums = (int)$v;
        break;
    case 'clean':
        $clean = (int)$v;
        break;
    case 'pack':
        $zip = (int)$v;
        break;
    case 'rename':
        $rename = (int)$v;
        break;
    case 'sourceuri':
        $v = trim($v);
        if( $v == "file://local" ) {
            $indir = dirname($rootdir);
        }
        else {
            //TODO parse 'file://*','svn://*','git://*'
            //$indir =
        }
        break;
    case 'verbose':
        $verbose = (int)$v;
        break;
    case 'version_num':
        $version_num = trim($v);
        break;
    default:
        break;
    }
}
// command-line overrides
$options = getopt('ab:nckhrvozs:',array('archive','branch:','help','clean','checksums','verbose','src:','rename','nobuild','out:','zip'));
if( is_array($options) && count($options) ) {
  foreach( $options as $k => $v ) {
      switch( $k ) {
      case 'a':
      case 'archive':
          $archive_only = 1;
          break;

      case 'b':
      case 'branch':
          $repos_branch = $v;
          break;

      case 'c':
      case 'clean':
          $clean = 1;
          break;

      case 'k':
      case 'checksums':
          $checksums = !$checksums;
          break;

      case 'v':
      case 'verbose':
          $verbose++;
          break;

      case 'o':
      case 'out':
          if( !is_dir($v) ) throw new Exception("$v is not a valid directory for the out parameter");
          $outdir = $v;
          break;

      case 's':
      case 'src':
          if( !is_dir($v) ) throw new Exception("$v is not a valid directory for the src parameter");
          $indir = $v;
          break;

      case 'h':
      case 'help':
          output_usage();
          exit;

      case 'r':
      case 'rename':
          $rename = !$rename;
          break;

      case 'z':
      case 'zip':
          $zip = !$zip;
          break;
      }
  }
}

$svn_url = $repos_root;
if( !$repos_branch ) {
    // attempt to get repository branch from cwd.
    $repos_branch = get_svn_branch();
}
$svn_url = "$repos_root/$repos_branch";

function output_usage()
{
    global $svn_url;
    echo "php build_phar.php [options]\n";
    echo "options:\n";
    echo "  -h / --help:     show this message\n";
    echo "  -a / --archive   only create the data archive, do not create phar archives\n";
    echo "  -b / --branch:   specify the branch or tag to create archive from, relative to the cmsms svn root.  Default is trunk";
    echo "  -c / --clean     toggle cleaning of old output directories (default is off)\n";
    echo "  -k / --checksums toggle creation of checksum files (default is on)\n";
    echo "  -r / --rename:   toggle renaming of .phar file to .php (default is on)\n";
    echo "  -s / --src:      specify source directory for files (otherwise export from svn url: {$svn_url}\n";
    echo "  -o / --out:      specify destination directory for the phar file.\n";
    echo "  -v / --verbose:  increment verbosity level (can be used multiple times)\n";
    echo "  -z / --zip:      toggle zipping the output (phar or php) into a .zip file) (default is on)\n";
}

function startswith($haystack,$needle)
{
    return (strncmp($haystack,$needle,strlen($needle)) == 0);
}

function endswith($haystack,$needle)
{
    $o = strlen($needle);
    return ($o > 0) ? substr_compare($haystack,$needle,-$o,$o) : false;
}

function rrmdir($dir)
{
    if( is_dir($dir) ) {
        $objects = scandir($dir);
        foreach( $objects as $object ) {
            if( $object != '.' && $object != '..' ) {
                $fp = "$dir/$object";
                if( filetype($fp) == "dir" ) {
                    rrmdir($fp);
                }
                else {
                    //TODO deal with links to dirs?
                    unlink($fp);
                }
            }
        }
//      reset($objects);
        rmdir($dir);
    }
}

function export_source_files()
{
    global $svn_url,$tmpdir;
    echo "INFO: exporting data from SVN ($svn_url)\n";
    $cmd = "svn export -q $svn_url $tmpdir";
    $cmd = escapeshellcmd($cmd);
    system($cmd);
}

//this function seems useless ATM, and perhaps always so (formerly, no $svn_url)
function get_svn_branch()
{
    global $svn_url;
    echo "INFO: identifying SVN branch\n";
//BAD TODO $cmd = "svn info $svn_url | grep '^URL:' | egrep -o '(tags|branches)/[^/]+|trunk'";
    $cmd = "svn info $svn_url | grep '^URL:'";
//BAD $cmd = escapeshellcmd($cmd);
    $out = system($cmd);
    return $out;
}

function copy_source_files()
{
  global $indir,$tmpdir,$src_excludes;
  $excludes = $src_excludes;
  // contents to be skipped but not in $excludes ?
  rrmdir($indir.'/tmp/cache');
  rrmdir($indir.'/tmp/templates_c');
  $l = strlen($indir);
  @mkdir($tmpdir);
  echo "INFO: Copying source files from $indir to $tmpdir\n";

  $rdi = new RecursiveDirectoryIterator($indir,
      FilesystemIterator::KEY_AS_FILENAME |
      FilesystemIterator::CURRENT_AS_PATHNAME |
      FilesystemIterator::FOLLOW_SYMLINKS |
      FilesystemIterator::UNIX_PATHS |
      FilesystemIterator::SKIP_DOTS);
  $rii = new RecursiveIteratorIterator($rdi);
  foreach( $rii as $name => $fp ) {
      foreach( $excludes as $patn ) {
          if( preg_match($patn,$fp) ) {
              verbose(1,"EXCLUDED: $name (matched pattern $patn)");
              continue 2;
          }
      }

      $tp = $tmpdir.substr($fp, $l);
      $dir = dirname($tp);
      @mkdir($dir,0777,TRUE);
      copy($fp,$tp);
      verbose(2,"COPIED $name to $tmpdir");
  }
}

function cleanup_source_files()
{
    global $tmpdir,$src_excludes;
    echo "INFO: Cleaning source files we don't need to package\n";
    $excludes = $src_excludes;
    chdir($tmpdir);
    $l = strlen($tmpdir);

    $rdi = new RecursiveDirectoryIterator($tmpdir,
        FilesystemIterator::KEY_AS_FILENAME |
        FilesystemIterator::CURRENT_AS_PATHNAME |
        FilesystemIterator::FOLLOW_SYMLINKS |
        FilesystemIterator::UNIX_PATHS |
        FilesystemIterator::SKIP_DOTS);
    $rii = new RecursiveIteratorIterator($rdi);
    foreach( $rii as $name => $fp ) {
        $tmp = substr($fp, $l);
        foreach( $excludes as $patn ) {
            if( preg_match($patn,$tmp) ) {
                @unlink($fp);
                verbose(1,"DELETED: $name");
            }
        }
    }

    // now clean empty directories (bottom up)
    $_remove_empty_subdirs = function($dir) use(&$_remove_empty_subdirs) {
        $empty = true;
        foreach( glob($dir.DIRECTORY_SEPARATOR.'*') as $file ) {
            if( is_dir($file) ) {
                if( !$_remove_empty_subdirs($file) ) $empty = false;
            }
            else {
                $empty = false;
            }
        }
        if( $empty ) rmdir($dir);
        return $empty;
    };
    $_remove_empty_subdirs($tmpdir);
}

function get_version_php($startdir)
{
    if( file_exists("$startdir/version.php") ) return "$startdir/version.php";
    if( file_exists("$startdir/lib/version.php") ) return "$startdir/lib/version.php";
}

function create_checksum_dat()
{
    global $checksums,$outdir,$tmpdir,$version_num;
    if( !$checksums ) return;

    $version_php = get_version_php($tmpdir);
    if( !file_exists($version_php) ) throw new Exception('Could not find version.php file in tmpdir... It is possible the wrong svn path was detected.');
    if( !file_exists("$tmpdir/index.php") ) throw new Exception('Could not find index.php file in tmpdir');

    echo "INFO: Creating checksum file\n";
    $salt = md5_file($version_php).md5_file("$tmpdir/index.php");

    $_create_checksums = function($dir,$salt) use (&$_create_checksums,$tmpdir) {
        $l = strlen($tmpdir);
        $out = array();
        $dh = opendir($dir);
        while( ($file = readdir($dh)) !== FALSE ) {
            if( $file == '.' || $file == '..' ) continue;

            $fs = "$dir/$file";
            if( is_dir($fs) ) {
                $tmp = $_create_checksums($fs,$salt);
                if( is_array($tmp) && count($tmp) ) $out = array_merge($out,$tmp);
            }
            else {
                $relpath = substr($fs,$l);
                $out[$relpath] = md5($salt.md5_file($fs));
            }
        }
        return $out;
    };

    $out = $_create_checksums($tmpdir,$salt);
    $outfile = "$outdir/cmsms-{$version_num}-checksum.dat";

    $xfh = fopen($outfile,'w');
    if( !$xfh ) {
       echo "WARNING: problem opening $outfile for writing\n";
    }
    else {
      foreach( $out as $key => $val ) {
        fprintf($xfh,"%s --::-- %s\n",$val,$key);
      }
      fclose($xfh);
    }
}

function create_source_archive()
{
    global $clean,$tmpdir,$owd,$datadir,$indir,$version_num;
    if( $clean && is_dir($tmpdir) ) {
        echo "INFO: removing old temporary files\n";
        rrmdir($tmpdir);
    }

    if( $indir == '' ) {
        export_source_files();
    }
    else {
        copy_source_files();
    }
    $version_php = get_version_php($tmpdir);
    if( !is_file($version_php) ) {
        throw new Exception('Could not find version.php file');
    }
    @include($version_php);
    $version_num = $CMS_VERSION;
    echo "INFO: found version: $version_num\n";

    // here we would build any externals etc.
    cleanup_source_files();
    create_checksum_dat();

    echo "INFO: Creating tar.gz archive of core files\n";
    chdir($tmpdir);
    $cmd = escapeshellcmd("tar -zcf $datadir/data.tar.gz") . ' *';
    system($cmd);

    chdir($owd);
    @copy($version_php,"$datadir/version.php");
    rrmdir($tmpdir);
    return $version_num;
}

function verbose($lvl,$msg)
{
    global $verbose;
    if( $verbose >= $lvl ) echo "VERBOSE: ".$msg."\n";
}

// this is the main function.
try {
    if( !is_dir($srcdir) && !is_file($srcdir.'/index.php') ) throw new Exception('Problem finding source files in '.$srcdir);

    if( is_dir($outdir) ) {
        echo "INFO: Removing old output file(s)\n";
        if( $clean ) {
            rrmdir($outdir);
        }
        else {
            array_map('unlink', glob("$outdir/*"));
        }
    }

    @mkdir($outdir);
    @mkdir($datadir);
    if( !is_dir($datadir) || !is_dir($outdir) ) throw new Exception('Problem creating working directories: '.$datadir.' and '.$outdir);

    $tmp = 'cmsms-'.create_source_archive().'-install';
    if( !$archive_only ) {
        $basename = $tmp;
        $destname = $tmp.'.phar';
        $destname2 = $tmp.'.php';

        $fn = "$srcdir/app/build.ini";
        $fh = fopen($fn,'w');
        if( $fh ) {
            echo "INFO: Writing build.ini\n";
            fwrite($fh,"[build]\n");
            fwrite($fh,'build_time = '.time()."\n");
            fwrite($fh,'build_user = '.get_current_user()."\n");
            fwrite($fh,'build_host = '.gethostname()."\n");
            fclose($fh);
        }
        else {
            echo "DEBUG: Failed to save $srcdir/app/build.ini\n";
        }

        // change permissions
        echo "INFO: Recursively applying more-restrictive permissions\n";
        $cmd = "chmod -R g-w,o-w {$srcdir}";
        echo "DEBUG: $cmd\n";
        $junk = null;
        $cmd = escapeshellcmd($cmd);
        exec($cmd,$junk);

        $l = strlen($srcdir) + 1;
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        // a brand new phar file.
        $phar = new Phar("$outdir/$destname");
        $phar->startBuffering();

        echo "INFO: Creating phar file\n";
        $rdi = new RecursiveDirectoryIterator($srcdir,
            FilesystemIterator::KEY_AS_FILENAME |
            FilesystemIterator::CURRENT_AS_PATHNAME |
            FilesystemIterator::FOLLOW_SYMLINKS |
            FilesystemIterator::UNIX_PATHS |
            FilesystemIterator::SKIP_DOTS);
        $rii = new RecursiveIteratorIterator($rdi);
        foreach( $rii as $name => $fp ) {
            if( !is_file($fp) ) {
                continue;
            }
            // trivial exclusion.
            foreach( $exclude_patterns as $patn ) {
                if( preg_match($patn,$fp) ) {
                    continue 2;
                }
            }

            $relname = substr($fp,$l);
            verbose(1,"ADDING: $relname to the archive");
            $phar[$relname] = file_get_contents($fp);

            $p = strrpos($relname,'.');
            if( $p !== FALSE ) {
                $extension = substr($relname,$p+1);
            }
            else {
                $extension = '';
            }
            switch( strtolower($extension) ) {
                case 'inc':
                case 'php':
                case 'php4':
                case 'php5':
                case 'phps':
                    $mimetype = Phar::PHP;
                    break;

                case 'js':
                    $mimetype = 'text/javascript';
                    break;

                case 'css':
                    $mimetype = 'text/css';
                    break;

                default:
                    if( $finfo ) {
                        $mimetype = finfo_file($finfo,$fp);
                        if( !$mimetype ) {
                            $mimetype = 'application/octet-stream';
                        }
                    }
                    else {
                        $mimetype = 'application/octet-stream';
                    }
                    break;
            }
            $phar[$relname]->setMetaData(array('mime-type'=>$mimetype));
        }
        if( $finfo ) {
            finfo_close($finfo);
        }

        $phar->setMetaData(array('bootstrap'=>'index.php'));
//      $stub = $phar->createDefaultStub('index.php','index.php');
        $stub = Phar::createDefaultStub('index.php','index.php');
        $phar->setStub($stub);
        $phar->setSignatureAlgorithm(Phar::SHA1);
        $phar->stopBuffering();
        unset($phar);

        // rename it to a php file so it's executable on pretty much all hosts
        if( $rename ) {
            echo "INFO: Renaming phar file to php for execution purposes\n";
            rename("$outdir/$destname","$outdir/$destname2");
        }

        if( $zip ) {
            $infile = "$outdir/$destname";
            if( $rename ) $infile = "$outdir/$destname2";
            $outfile = "$outdir/$basename.zip";

            echo "INFO: zipping phar file into $outfile\n";
            $arch = new ZipArchive;
            $arch->open($outfile,ZipArchive::OVERWRITE | ZipArchive::CREATE );
            $arch->addFile($infile,basename($infile));
            $arch->setExternalAttributesName(basename($infile), ZipArchive::OPSYS_UNIX, 0644 << 16);
            $arch->addFile("$rootdir/README-PHAR.TXT",'README-PHAR.TXT');
            $arch->setExternalAttributesName('README-PHAR.TXT', ZipArchive::OPSYS_UNIX, 0644 << 16);
            $arch->close();
            @unlink($infile);

            // zip up the install dir itself. (uses shell)
            $tmpfile = '/tmp/zip_excludes.dat'; // hackish, but relatively safe.
            $str = implode("\n",$exclude_from_zip);
            file_put_contents($tmpfile,$str);
            chdir($rootdir);
            $outfile = "$outdir/$basename.expanded.zip";
            echo "INFO: zipping install directory into $outfile\n";
            $cmd = "zip -q -r -x@{$tmpfile} $outfile README.TXT index.php app lib data";
            $cmd = escapeshellcmd($cmd);
            system($cmd);
            unlink($tmpfile);
        } // zip
        rrmdir($datadir);
    } // !archive only
    echo "INFO: Done, see files in $outdir\n";
}
catch( Exception $e ) {
    echo "ERROR: Problem building phar file ".$outdir.": ".$e->GetMessage()."\n";
}
