<?php

namespace cms_autoinstaller;

use cms_autoinstaller\utils;
use cms_autoinstaller\wizard_step;
use Exception;
use PharData;
use RecursiveIteratorIterator;
use function __appbase\get_app;
use function __appbase\lang;
use function __appbase\smarty;
use function file_put_contents;

class wizard_step7 extends wizard_step
{
    protected function process()
    {
        // nothing here
    }

    private function _createIndexHTML($filename)
    {
        $str = '<!-- DUMMY HTML FILE -->';
        file_put_contents($filename,$str);
    }

    private function detect_languages()
    {
        $this->message(lang('install_detectlanguages'));
        $destdir = get_app()->get_destdir();

        $nlsdir = "$destdir/lib/nls";
        $pattern = "$nlsdir/*nls.php";
        $files = glob($pattern);
        if( !is_array($files) || count($files) == 0 ) throw new Exception(lang('error_internal',750));

        foreach( $files as &$one ) {
            $fn = basename($one);
            $one = substr($fn,0,strlen($fn)-strlen('.nls.php'));
        }
        return $files;
    }

    private function do_index_html()
    {
        $this->message(lang('install_dummyindexhtml'));

        $destdir = get_app()->get_destdir();
        if( !$destdir ) throw new Exception(lang('error_internal',751));
        $archive = get_app()->get_archive();
        $phardata = new PharData($archive);
        $archive = basename($archive);
        foreach( new RecursiveIteratorIterator($phardata) as $file => $it ) {
            if( ($p = strpos($file,$archive)) === FALSE ) continue;
            $fn = substr($file,$p+strlen($archive));
            $dn = $destdir.dirname($fn);
            if( $dn == $destdir || $dn == $destdir.'/' ) continue;
            if( $dn == "$destdir/admin" ) continue;
            $idxfile = $dn.'/index.html';
            if( is_dir($dn) && !is_file($idxfile) )  $this->_createIndexHTML($idxfile);
        }
    }

    private function do_files($langlist = null)
    {
        $languages = array('en_US');
        $siteinfo = $this->get_wizard()->get_data('siteinfo');
        if(is_array($siteinfo) && is_array($siteinfo['languages']) && count($siteinfo['languages']) ) $languages = array_merge($languages,$siteinfo['languages']);
        if( is_array($langlist) && count($langlist) ) $languages = array_merge($languages,$langlist);
        $languages = array_unique($languages);

        $destdir = get_app()->get_destdir();
        if( !$destdir ) throw new Exception(lang('error_internal',601));
        $archive = get_app()->get_archive();

        $this->message(lang('install_extractfiles'));
        $phardata = new PharData($archive);
        $archive = basename($archive);
        $filehandler = new install_filehandler();
        $filehandler->set_languages($languages);
        $filehandler->set_destdir($destdir);
        $filehandler->set_output_fn('\cms_autoinstaller\wizard_step6::verbose');
        foreach( new RecursiveIteratorIterator($phardata) as $file => $it ) {
            if( ($p = strpos($file,$archive)) === FALSE ) continue;
            $fn = substr($file,$p+strlen($archive));
            $filehandler->handle_file($fn,$file,$it);
        }
    }

    private function do_manifests()
    {
        // get the list of all available versions that this upgrader knows about
        $app = get_app();
        $app_config = $app->get_config();
        $upgrade_dir =  $app->get_appdir().'/upgrade';
        if( !is_dir($upgrade_dir) ) throw new Exception(lang('error_internal',710));
        $destdir = $app->get_destdir();
        if( !$destdir ) throw new Exception(lang('error_internal',711));

        $version_info = $this->get_wizard()->get_data('version_info');
        $versions = utils::get_upgrade_versions();
        if( is_array($versions) && count($versions) ) {
            $this->message(lang('cleaning_files'));
            foreach( $versions as $one_version ) {
                if( version_compare($one_version, $version_info['version']) < 1 ) continue;

                // open the manifest
                // check the to version info
                $manifest = new manifest_reader("$upgrade_dir/$one_version");
                if( $one_version != $manifest->to_version() ) {
                    throw new Exception(lang('error_internal',712));
                }

                // delete all 'deleted' files
                // if they are supposed to be in the installation, the copy from the archive
                // will restore them.
                $deleted = $manifest->get_deleted();
                $ndeleted = 0;
                $nfailed = 0;
                $nmissing = 0;
                if( is_array($deleted) && count($deleted) ) {
                    foreach( $deleted as $rec ) {
                        $fn = "{$destdir}{$rec['filename']}";
                        if( !file_exists($fn) ) {
                            $this->verbose("file $fn does not exist... but we planned to delete it anyway");
                            $nmissing++;
                        }
                        else if( !is_writable($fn) ) {
                            $this->error("file $fn is not writable, could not delete it");
                            $nfailed++;
                        }
                        else {
                            if( is_dir($fn) ) {
                                if( is_file($fn.'/index.html') ) @unlink($fn.'/index.html');
                                                $res = @rmdir($fn);
                                if( !$res ) {
                                    $this->error('problem removing directory: '.$fn);
                                    $nfailed++;
                                }
                                else {
                                    $this->verbose('removed directory: '.$fn);
                                    $ndeleted++;
                                }
                            }
                            else {
                                $res = @unlink($fn);
                                if( !$res ) {
                                    $this->error("problem deleting: $fn");
                                    $nfailed++;
                                }
                                else {
                                    $this->verbose('removed file: '.$fn);
                                    $ndeleted++;
                                }
                            }
                        }
                    }
                }

                $this->message($ndeleted.' files/folders deleted for version '.$one_version.": ".$nmissing.' missing, '.$nfailed.' failed');
            }
        }
    }

    protected function display()
    {
        parent::display();
        $wiz = $this->get_wizard();
        $action = $wiz->get_data('action');

        $smarty = smarty();
        if( $action == 'freshen' ) {
            $smarty->assign('next_url',$wiz->step_url(9));
        } else {
            $smarty->assign('next_url',$wiz->next_url());
        }
        $smarty->display('wizard_step7.tpl');
        flush();

        try {
            switch( $action ) {
            case 'upgrade':
                $tmp = $wiz->get_data('version_info'); // valid only for upgrades
                if( is_array($tmp) && count($tmp) ) {
                    $languages = $this->detect_languages();
                    $this->do_manifests();
                    $this->do_files($languages);
                    break; 
                }
                else {
                    throw new Exception(lang('error_internal',703)); 
                }
                // no break here
            case 'freshen':
                $inst_languages = $this->detect_languages();
                $this->do_files($inst_languages);
                break;
            case 'install':
                $this->do_files();
                break;
            default:
                throw new Exception(lang('error_internal',705));
            }

            // create index.html files in directories.
            $this->do_index_html();
        }
        catch( Exception $e ) {
            $this->error($e->GetMessage());
        }

        $this->finish();
    }

} // end of class
