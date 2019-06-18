#!/usr/bin/php
<?php
// WARNING: this is certainly not my best code.
// todo: make this interactive via cli.

//  check to make sure we are in the correct directory.
$owd = getcwd();
if( php_sapi_name() != 'cli' ) throw new Exception('This script must be executed via the CLI');
if( !isset($argv) ) throw new Exception('This script must be executed via the CLI');
if( ini_get('phar.readonly') ) throw new Exception('phar.readonly must be turned OFF in the php.ini');

$script_file = basename($argv[0]);
$owd = getcwd();
if( !file_exists("$owd/$script_file") ) throw new Exception('This script must be executed from the same directory as the '.$script_file.' script');
$rootdir = dirname(dirname(__FILE__));
$repos_root='http://svn.cmsmadesimple.org/svn/cmsmadesimple';
$repos_branch="/trunk";
$repos_branch = null;
$srcdir = $rootdir;
$tmpdir = $rootdir.'/tmp';
$datadir = $rootdir.'/data';
$outdir = $rootdir."/out";
$exclude_patterns = array('/\.svn\//','/^ext\//','/^build\/.*/','/.*~$/','/tmp\/.*/','/\.\#.*/','/\#.*/','/^out\//','/^README*TXT/');
$exclude_from_zip = array('*~','tmp/','.#*','#*'.'*.bak' );
$src_excludes = array('/\/APIDOC\//', '/\/phar_installer\//', '/\/find-mime$/', '/\/install\//', '/^\/tmp\/.*/', '/^#.*/', '/^\/scripts\/.*/', '/\.git/', '/\.svn/', '/svn-.*/', '/^\/assets\/.*/', '/^\/phpcs\.xml$/', '/^\/\.phpcs\.xml$/',
                      '/^\/tests\/.*/', '/^\/build\/.*/', '/^\.htaccess/', '/\.svn/', '/^config\.php$/','/.*~$/', '/\.\#.*/', '/\#.*/', '/.*\.bak/');
$priv_file = dirname(__FILE__).'/priv.pem';
$pub_file = dirname(__FILE__).'/pub.pem';
$verbose = 0;
$rename = 1;
$indir = '';
$archive_only = 0;
$zip = 1;
$clean = 0;
$checksums = 1;
$version_num = null;

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
    return (substr($haystack,0,strlen($needle)) == $needle);
}

function endswith($haystack,$needle)
{
    return (substr($haystack,-1*strlen($needle)) == $needle);
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

function export_source_files()
{
    global $svn_url,$tmpdir;
    echo "INFO: exporting data from SVN ($svn_url)\n";
    $cmd = "svn export -q $svn_url $tmpdir";
    $cmd = escapeshellcmd($cmd);

    system($cmd);
}

function get_svn_branch()
{
    $cmd = "svn info | grep '^URL:' | egrep -o '(tags|branches)/[^/]+|trunk'";
    $out = exec($cmd);
    return $out;
}

function copy_source_files()
{
  global $indir,$tmpdir,$src_excludes;
  $excludes = $src_excludes;
  echo "INFO: Copying source files from $indir to $tmpdir\n";
  @mkdir($tmpdir);
  $dir = new RecursiveDirectoryIterator($indir,
					FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::FOLLOW_SYMLINKS | FilesystemIterator::SKIP_DOTS );

  rrmdir($indir.'/tmp/cache');
  rrmdir($indir.'/tmp/templates_c');
  foreach( $it = new RecursiveIteratorIterator($dir) as $file => $inf ) {
      $file = substr($file,strlen($indir));
      $test = false;
      foreach( $excludes as $excl ) {
          if( preg_match($excl,$file) ) {
              verbose(1,"EXCLUDED: $file (matched pattern $excl)");
              $test = TRUE;
              break;
          }
      }
      if( $test ) continue;
      $dir = $tmpdir.'/'.dirname($file);
      @mkdir($dir,0777,TRUE);
      copy($indir.$file,$tmpdir.'/'.$file);
      verbose(2,"COPIED $file to {$tmpdir}/{$file}");
  }
}

function cleanup_source_files()
{
    global $tmpdir,$src_excludes;
    echo "INFO: Cleaning files we dont need to package from directory\n";
    $excludes = $src_excludes;
    chdir($tmpdir);

    $dir = new RecursiveDirectoryIterator($tmpdir,
                                          FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::FOLLOW_SYMLINKS | FilesystemIterator::SKIP_DOTS );
    foreach( $it = new RecursiveIteratorIterator($dir) as $name => $ob ) {
        foreach($excludes as $excl) {
            $tmp = substr($name,strlen($tmpdir));
            if( !preg_match($excl,$tmp) ) continue;
            @unlink($name);
            verbose(1,"DELETED: $name");
        }
    }

    // now clean empty directories (bottom up)
    $_remove_empty_subdirs = function($dir) use(&$_remove_empty_subdirs) {
        $empty = true;
        foreach(glob($dir.DIRECTORY_SEPARATOR.'*') AS $file) {
            if( is_dir($file) ) {
                if( !$_remove_empty_subdirs($file) ) $empty = false;
            } else {
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
    if( is_file("$startdir/lib/version.php") ) return "$startdir/lib/version.php";
    if( is_file("$startdir/version.php") ) return "$startdir/version.php";
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
                $relpath = substr($fs,strlen($tmpdir));
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
    if( !is_file($version_php) ) throw new Exception('Could not find version.php file');

    {
        @include($version_php);
        $version_num = $CMS_VERSION;
        echo "INFO: found version: $version_num\n";
    }

    cleanup_source_files();
    create_checksum_dat();

    echo "INFO: Creating tar archive of CMSMS core files\n";
    chdir($tmpdir);
    $cmd = "tar zcCf $tmpdir $datadir/data.tar.gz *";
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
    if( !is_writable($rootdir) ) throw new Exception('No write access to '.$rootdir.' (for creating directories)');

    if( $clean && is_dir($outdir) ) {
        echo "INFO: Removing old output file(s)\n";
        rrmdir($outdir);
    }

    @mkdir($outdir);
    @mkdir($datadir);
    if( !is_dir($datadir) || !is_dir($outdir) ) throw new Exception('Problem creating working directories: '.$datadir.' and '.$outdir);

    $tmp = 'cmsms-'.create_source_archive().'-install';
    if( !$archive_only ) {
        $basename = $tmp;
        $destname = $tmp.'.phar';
        $destname2 = $tmp.'.php';

        echo "INFO: Writing build.ini\n";
        $fn = "$srcdir/app/build.ini";
        $fh = fopen($fn,"w");
        fwrite($fh,"[build]\n");
        fwrite($fh,"build_time = ".time()."\n");
        fwrite($fh,"build_user = ".get_current_user()."\n");
        fwrite($fh,"build_host = ".gethostname()."\n");
        fclose($fh);

        // change permissions
        echo "INFO: Recursively changing permissions to be more restrictive\n";
        $cmd = "chmod -R g-w,o-w {$srcdir}";
        echo "DEBUG: $cmd\n";
        $junk = null;
        $cmd = escapeshellcmd($cmd);
        exec($cmd,$junk);

        // a brand new phar file.
        $phar = new Phar("$outdir/$destname");
        $phar->startBuffering();

        echo "INFO: Creating PHAR file\n";
        $rdi = new RecursiveDirectoryIterator($srcdir,
                                              FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::FOLLOW_SYMLINKS | FilesystemIterator::SKIP_DOTS );
        $rii = new RecursiveIteratorIterator($rdi); // make the directories flat

        foreach( $rii as $fname => $file ) {
            $relname = substr($fname,strlen($srcdir)+1);
            $relpath = dirname($relname);
            $extension = substr($relname,strrpos($relname,'.')+1);

            // trivial exclusion.
            $found = 0;
            foreach( $exclude_patterns as $pattern ) {
                if( preg_match($pattern,$relname) ) {
                    $found = 1;
                    break;
                }
            }
            if( $found ) continue;

            verbose(1,"ADDING: $relname to the archive");
            $phar[$relname] = file_get_contents($fname);
            $mimetype = 'unknown';
            $tmp = finfo_open(FILEINFO_MIME_TYPE);
            if( $tmp ) {
                $mimetype = finfo_file($tmp,$fname);
                finfo_close($tmp);
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
            }
            if( $mimetype == 'unknown' || $mimetype == 0 ) $mimetype = 'text/plain';
            $phar[$relname]->setMetaData(array('mime-type'=>$mimetype));
        }

        $phar->setMetaData(array('bootstrap'=>'index.php'));
        $stub = $phar->createDefaultStub('cli.php','index.php');
        $phar->setSignatureAlgorithm(Phar::SHA1);
        $phar->setStub($stub);
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
    } // archive only

    echo "INFO: Done\n";
}
catch( Exception $e ) {
    echo "ERROR: Problem building phar file ".$outdir.": ".$e->GetMessage()."\n";
}
