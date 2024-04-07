<?php

namespace cms_autoinstaller;

class wizard_step1 extends \cms_autoinstaller\wizard_step
{
    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        if( !\class_exists('PharData') )
        {
            throw new \RuntimeException(
              'It appears that the phar extensions have not been enabled in this version of php.  Please correct this.'
            );
        }
    }
    
    /**
     * @throws \__appbase\langtools_Exception
     */
    protected function process()
    {
        if( isset($_POST['lang']) ) {
            $lang = \trim(\__appbase\utils::clean_string($_POST['lang']));
            if( $lang ) \__appbase\translator()->set_selected_language($lang);
        }

        if( isset($_POST['destdir']) ) {
            $app = \__appbase\get_app();
            $app->set_destdir($_POST['destdir']);
        }

        $verbose = 0;
        if( isset($_POST['verbose']) ) $verbose = (int)$_POST['verbose'];
        $this->get_wizard()->set_data('verbose',$verbose);

        if( isset($_POST['next']) ) {
            // redirect to the next step.
            \__appbase\utils::redirect($this->get_wizard()->next_url());
        }
        return TRUE;
    }

    private function get_valid_install_dirs()
    {
        $app = \__appbase\get_app();
        $start = \realpath($app->get_rootdir());
        $parent = \realpath(dirname($start));

        $_is_valid_dir = function($dir) {
            // this routine attempts to exclude most cmsms core directories
            // from appearing in the dropdown for directory choosers
            $bn = \basename($dir);
            switch( $bn ) {
            case 'lang':
                if( \file_exists("$dir/en_US.php") ) return FALSE;
                break;

            case 'ext':
                if( \file_exists("$dir/fr_FR.php") ) return FALSE;
                break;

            case 'plugins':
                if( \file_exists("$dir/function.cms_selflink.php") ) return FALSE;
                break;

            case 'install':
                if( \is_dir("$dir/schemas") ) return FALSE;
                break;

            case 'tmp':
                if( \is_dir("$dir/cache") ) return FALSE;
                break;

            case 'phar_installer':
            case 'doc':
            case 'build':
            case 'admin':
            case 'module_custom':
            case 'out':
                return FALSE;

            case 'lib':
                if( \is_dir("$dir/smarty") ) return FALSE;
                break;

            case 'app':
                if( \file_exists("$dir/class.cms_install.php") ) return FALSE;
                break;

            case 'modules':
                if(\is_dir("$dir/CMSMailer") || \is_dir("$dir/AdminSearch") ) return FALSE;
                break;

            case 'data':
                if( \file_exists("$dir/data.tar.gz") ) return FALSE;
                break;
            }
            return TRUE;
        };
        
        /** @var string $CMS_VERSION from version.php (deprecated) or from lib/version.php */
        
        $_get_annotation = function($dir) {
            if(!\is_dir($dir) || !\is_readable($dir) ) return;
            $bn = \basename($dir);
            if( $bn != 'lib' && \is_file("$dir/version.php" ) ) {
                @include("$dir/version.php"); // defines in this file can throw notices
  
                if( isset($CMS_VERSION) ) return "CMSMS $CMS_VERSION"; # this should be deprecated in the future
            } else if( \is_file("$dir/lib/version.php") ) {
                @include("$dir/lib/version.php"); // defines in this file can throw notices
                if( isset($CMS_VERSION) ) return "CMSMS $CMS_VERSION";
            }

            if(\is_dir("$dir/app") && \is_file("$dir/app/class.cms_install.php") ) {
                return "CMSMS installation assistant";
            }
        };

        $_find_dirs = static function($start, $depth = 0) use( &$_find_dirs, &$_get_annotation, $_is_valid_dir ) {
            if( !\is_readable($start ) ) return;
            $dh = \opendir($start);
            if( !$dh ) return;
            $out = [];
            while( ($file = readdir($dh)) !== FALSE ) {
                if( $file == '.' || $file == '..' ) continue;
                if( \__appbase\startswith($file,'.') || \__appbase\startswith($file,'_') ) continue;
                $dn = $start.DIRECTORY_SEPARATOR.$file;  // cuz windows blows, and windoze guys are whiners :)
                if( !@\is_readable($dn) ) continue;
                if( !@\is_dir($dn) ) continue;
                if( !$_is_valid_dir( $dn ) ) continue;
                $str = $dn;
                $ann = $_get_annotation( $dn );
                if( $ann ) $str .= " ($ann)";

                $out[$dn] = $str;
                if( $depth < 3 ) {
                    $tmp = $_find_dirs($dn,$depth + 1); // recursion
                    if(\is_array($tmp) && \count($tmp) ) $out = \array_merge($out, $tmp); # revise this to be more efficient (merging arrays in a loop is slow and causes high CPU usage.)
                }
            }
            return $out;
            //if( count($out) ) return $out;
        };

        $out = [];
        if( $_is_valid_dir($parent) ) $out[$parent] = $parent;
        $tmp = $_find_dirs($parent);
        if( \count($tmp) ) $out = \array_merge($out, $tmp);
        \asort($out);
        return $out;
    }

    protected function display()
    {
        parent::display();

        // get the list of directories we can install to.
        $smarty = \__appbase\smarty();
        $app = \__appbase\get_app();
        if( !$app->in_phar() ) {
            // get the list of directories we can install to
            $dirlist = $this->get_valid_install_dirs();
            if( !$dirlist ) throw new \Exception('No possible installation directories found.  This could be a permissions issue');
            $smarty->assign('dirlist',$dirlist);

            $custom_destdir = $app->has_custom_destdir();
            $smarty->assign('custom_destdir',$custom_destdir);
            $smarty->assign('destdir',$app->get_destdir());
        }
        $smarty->assign('verbose',$this->get_wizard()->get_data('verbose',0));
        $smarty->assign('languages',\__appbase\translator()->get_language_list(\__appbase\translator()->get_allowed_languages()));
        $smarty->assign('curlang',\__appbase\translator()->get_current_language());
        $smarty->assign('yesno',array(0=>\__appbase\lang('no'),1=>\__appbase\lang('yes')));
        $smarty->display('wizard_step1.tpl');

        $this->finish();
    }

} // end of class

?>
