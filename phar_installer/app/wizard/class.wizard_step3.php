<?php

namespace cms_autoinstaller;
use \__appbase\tests as _tests_;

class wizard_step3 extends \cms_autoinstaller\wizard_step
{
    protected function process()
    {
        die('foo');
    }

    protected function perform_tests($verbose,&$infomsg,&$tests)
    {
        $app = \__appbase\get_app();
        $version_info = $this->get_wizard()->get_data('version_info');
        $action = $this->get_wizard()->get_data('action');
        $informational = array();
        $tests = array();

        // informational messages...
        $informational[] = new _tests_\informational_test('server_software',$_SERVER['SERVER_SOFTWARE'],'info_server_software');
        $informational[] = new _tests_\informational_test('server_api',PHP_SAPI,'info_server_api');
        $informational[] = new _tests_\informational_test('server_os',array(PHP_OS,php_uname('r'),php_uname('m')));

        // required test for php version
        $obj = new _tests_\version_range_test('php_version',phpversion());
        $obj->minimum = '7.1';
        $obj->recommended = '7.2';
        $obj->fail_msg = \__appbase\lang('pass_php_version',$obj->minimum,$obj->recommended,phpversion());
        $obj->warn_msg = \__appbase\lang('msg_yourvalue',phpversion());
        $obj->pass_msg = \__appbase\lang('msg_yourvalue',phpversion());
        $obj->required = true;
        $tests[] = $obj;

        // required test... check if most files are writable.
        {
            $dirs = array('modules','lib','plugins','admin','uploads','doc','scripts','install','tmp','assets');
            $failed = array();
            $list = glob($app->get_destdir().'/*');
            foreach( $list as $one ) {
                $basename = basename($one);
                if( is_file($one) ) {
                    $relative = substr($one,strlen($app->get_destdir())+1);
                    if( !is_writable($one) ) $failed[] = $relative;
                }
                else if( in_array($basename,$dirs) ) {
                    $b = \__appbase\utils::is_directory_writable($one,TRUE);
                    if( !$b ) {
                        $tmp = \__appbase\utils::get_writable_error();
                        $failed = array_merge($failed,\__appbase\utils::get_writable_error());
                    }
                }
            }
        }

        // required test... tmpfile
        $fh = tmpfile();
        $b = ($fh === FALSE)?FALSE:TRUE;
        $obj = new _tests_\boolean_test('tmpfile',$b);
        $obj->required = true;
        if( !$b ) $obj->fail_msg = \__appbase\lang('fail_tmpfile');
        $tests[] = $obj;
        unset($fh);

        // its an upgrade
        if( $version_info ) {
            // config file must be writable.
            $obj = new _tests_\boolean_test('config_writable',is_writable($version_info['config_file']));
            $obj->required = true;
            $obj->fail_key = 'fail_config_writable';
            $tests[] = $obj;

            if( $action == 'upgrade' && version_compare($version_info['version'],'2.2') < 0 ) {
                $dir = $app->get_destdir().'/assets';
                if( is_dir($dir) ) {
                    $obj = new _tests_\boolean_test('assets_dir_exists',FALSE);
                    $obj->fail_key = 'fail_assets_dir';
                    $obj->warn_key = 'fail_assets_dir';
                    $obj->required = 0;
                    $tests[] = $obj;
                }
            }
        } else {
            $dest = $app->get_destdir();
            $config_file = $dest.'/config.php';
            $obj = new _tests_\boolean_test('config_writable',!is_file($config_file) || is_writable($config_file));
            $obj->required = true;
            $obj->fail_key = 'fail_config_writable';
            $tests[] = $obj;

            $is_dir_empty = function($dir) {
                $dir = trim($dir);
                if( !$dir ) return FALSE;  // fail on invalid dir
                if( !is_dir($dir) ) return TRUE; // pass on dir not existing yet
                $files = glob($dir.'/*' );
                if( !count($files) ) return TRUE; // no files yet.
                if( count($files) > 1 ) return FALSE; // morre than one file
                // trivial check for index.html
                $bn = strtolower(basename($files[0]));
                if( fnmatch('index.htm*',$bn) ) return TRUE;
                return FALSE;
            };
            $res = true;
            if( $res && !$is_dir_empty($dest.'/tmp/cache') ) $res = false;
            if( $res && !$is_dir_empty($dest.'/tmp/templates_c') ) $res = false;

            $obj = new _tests_\boolean_test('tmp_dirs_empty',$res);
            $obj->required = true;
            $obj->fail_key = 'fail_tmp_dirs_empty';
            $tests[] = $obj;
        }

        // required test... gd version 2
        $obj = new _tests_\version_range_test('gd_version',$this->_GDVersion());
        $obj->minimum = 2;
        $obj->required = 1;
        $obj->fail_msg = \__appbase\lang('msg_yourvalue',$this->_GDVersion());
        $tests[] = $obj;

        // required test ... tempnam function
        $obj = new _tests_\boolean_test('func_tempnam',function_exists('tempnam'));
        $obj->required = 1;
        $obj->fail_key = 'fail_func_tempnam';
        $tests[] = $obj;

        // required test ... some sort of gzopen/gzopen64 combo
        $obj = new _tests_\boolean_test('func_gzopen',function_exists('gzopen') || function_exists('gzopen64'));
        $obj->required = true;
        $obj->fail_key = 'fail_func_gzopen';
        $tests[] = $obj;

        // required test ... ZipArchive
        $obj = new _tests_\boolean_test('func_ziparchive',class_exists('ZipArchive'));
        $obj->required = true;
        $obj->fail_key = 'fail_func_ziparchive';
        $tests[] = $obj;

        // required test...  magic_quotes_runtime
        $obj = new _tests_\boolean_test('magic_quotes_runtime',!get_magic_quotes_runtime());
        $obj->required = 1;
        $obj->fail_key = 'fail_magic_quotes_runtime';
        $tests[] = $obj;

        // required test... multibyte extensions
        $obj = new _tests_\boolean_test('multibyte_support',_tests_\test_extension_loaded('mbstring') && function_exists('mb_get_info'));
        $obj->required = 1;
        $obj->fail_key = 'fail_multibyte_support';
        $tests[] = $obj;

        // required test... at least one supported database driver.
        $obj = new _tests_\matchany_test('database_support');
        $obj->required = 1;
        $t1 = new _tests_\boolean_test('mysql',_tests_\test_extension_loaded('mysql'));
        $obj->add_child($t1);
        $t1 = new _tests_\boolean_test('mysqli',_tests_\test_extension_loaded('mysqli'));
        $obj->add_child($t1);
        $obj->fail_key = 'fail_database_support';
        $tests[] = $obj;

        // required test ... md5 function
        $obj = new _tests_\boolean_test('func_md5',function_exists('md5'));
        $obj->fail_key = 'fail_func_md5';
        $obj->required = 1;
        $tests[] = $obj;

        // required test ... json function
        $obj = new _tests_\boolean_test('func_json',function_exists('json_decode'));
        $obj->fail_key = 'pass_func_json';
        $obj->required = 1;
        $tests[] = $obj;

        // open basedir is recommended
        $obj = new _tests_\boolean_test('open_basedir',ini_get('open_basedir') == '');
        $obj->warn_key = 'warn_open_basedir';
        $obj->fail_key = 'fail_open_basedir';
        $tests[] = $obj;

        // required test... sessions must use cookies
        $t0 = new _tests_\boolean_test('session_use_cookies',ini_get('session.use_cookies'));
        $t0->required = 1;
        $t0->fail_key = 'fail_session_use_cookies';
        $tests[] = $t0;

        if( ini_get('session.save_handler') == 'files' ) {
            $open_basedir = ini_get('open_basedir');
            if( $open_basedir ) {
                // open basedir restrictions are in effect, can't test if the session save path is writable
                // so just talk about it.
                // note: if we got here, sessions are probably working just fine.
                $t2 = new _tests_\boolean_test('open_basedir_session_save_path',0);
                $t2->warn_key = 'warn_open_basedir_session_savepath';
                $t2->msg = \__appbase\lang('info_open_basedir_session_save_path');
                $tests[] = $t2;
            }
            else {
                // test if the session save path is writable.
                $tmp = $this->_get_session_save_path();
                if( $tmp ) {
                    // session save path can be empty which should use the system temporary directory
                    $t2 = new _tests_\boolean_test('session_save_path_exists',@is_dir($tmp));
                    $t2->required = 1;
                    $t2->fail_key = 'fail_session_save_path_exists';
                    $tests[] = $t2;

                    $t3 = new _tests_\boolean_test('session_save_path_writable',@is_writable($tmp));
                    $t3->required = 1;
                    $t3->fail_key = 'fail_session_save_path_writable';
                    $tests[] = $t3;
                }
            }
        }

        // recommended test ... E_STRICT disabled
        $orig_error_level = $app->get_orig_error_level();
        $obj = new _tests_\boolean_test('errorlevel_estrict',!($orig_error_level & E_STRICT));
        $obj->warn_key = 'estrict_enabled';
        $tests[] = $obj;

        // recommended test ... E_DEPRECATED disabled
        $obj = new _tests_\boolean_test('errorlevel_edeprecated',!($orig_error_level & E_DEPRECATED));
        $obj->warn_key = 'edeprecated_enabled';
        $tests[] = $obj;

        // required test ... MEMORY LIMIT
        $memory_limit = ini_get('memory_limit');
        if( $memory_limit !== '-1' ) {
            $obj = new _tests_\range_test('memory_limit',$memory_limit);
            $obj->minimum = '16M';
            $obj->recommended = '32M';
            $obj->pass_msg = ini_get('memory_limit');
            $obj->fail_msg = \__appbase\lang('fail_memory_limit',ini_get('memory_limit'),$obj->minimum,$obj->recommended);
            $obj->warn_msg = \__appbase\lang('warn_memory_limit',ini_get('memory_limit'),$obj->minimum,$obj->recommended);
            $obj->required = 1;
            $tests[] = $obj;
        } else {
            $obj = new _tests_\boolean_test('memory_limit',true);
            $obj->pass_msg = \__appbase\lang('pass_memory_limit_nolimit');
            $obj->required = 1;
            $tests[] = $obj;
        }

        // required test ... safe mode
        $obj = new _tests_\boolean_test('safe_mode',_tests_\test_is_false(ini_get('safe_mode')));
        $obj->required = 1;
        $obj->fail_key = 'fail_safe_mode';
        $tests[] = $obj;

        // required test ... file upload
        $obj = new _tests_\boolean_test('file_uploads',_tests_\test_is_true(ini_get('file_uploads')));
        $obj->required = 1;
        $obj->fail_key = 'fail_file_uploads';
        $tests[] = $obj;

        // required test curl extension
        $obj = new _tests_\boolean_test('curl_extension',_tests_\test_extension_loaded('curl'));
        $obj->fail_key = 'fail_curl_extension';
        $obj->required = 1;
        $tests[] = $obj;

        // upload max filesize
        $obj = new _tests_\range_test('upload_max_filesize',ini_get('upload_max_filesize'));
        $obj->minimum = '1M';
        $obj->recommended = '10M';
        $obj->required = 1;
        $obj->warn_msg = \__appbase\lang('warn_upload_max_filesize',ini_get('upload_max_filesize'),$obj->recommended);
        $tests[] = $obj;

        // xml extension
        $obj = new _tests_\boolean_test('xml_functions',_tests_\test_extension_loaded('xml'));
        $obj->required = 1;
        $obj->fail_key = 'fail_xml_functions';
        $tests[] = $obj;

        // recommended test ... default charset
        $default_charset = ini_get('default_charset');
        $obj = new _tests_\boolean_test('default_charset',(strtolower($default_charset) == 'utf-8'));
        $obj->required = 0;
        $obj->warn_msg = \__appbase\lang('warn_default_charset',$default_charset);
        $tests[] = $obj;

        // recommended test ... max_execution_time
        $v = (int) ini_get('max_execution_time');
        if( $v !== 0 ) {
            $obj = new _tests_\range_test('max_execution_time',$v);
            $obj->minimum = 30;
            $obj->recommended = 60;
            $obj->required = 1;
            $obj->warn_msg = \__appbase\lang('warn_max_execution_time',ini_get('max_execution_time'),$obj->minimum,$obj->recommended);;
            $obj->fail_msg = \__appbase\lang('fail_max_execution_time',ini_get('max_execution_time'),$obj->minimum,$obj->recommended);;
            $tests[] = $obj;
        }

        // recommended test ... post_max_size
        $obj = new _tests_\range_test('post_max_size',ini_get('post_max_size'));
        $obj->minimum = '2M';
        $obj->recommended = '10M';
        $obj->warn_msg = \__appbase\lang('warn_post_max_size',ini_get('post_max_size'),$obj->minimum,$obj->recommended);
        $obj->fail_key = 'fail_post_max_size';
        $tests[] = $obj;

        // recommended test (register globals)
        $obj = new _tests_\boolean_test('register_globals',!ini_get('register_globals'));
        $obj->required = 1;
        $obj->fail_key = 'fail_register_globals';
        $tests[] = $obj;

        // recommended test ... output buffering
        $obj = new _tests_\boolean_test('output_buffering',ini_get('output_buffering'));
        $obj->fail_key = 'fail_output_buffering';
        $tests[] = $obj;

        // recommended test .... disable functions
        $obj = new _tests_\boolean_test('disable_functions',ini_get('disable_functions') == '');
        $obj->warn_msg = \__appbase\lang('warn_disable_functions',str_replace(',',', ',ini_get('disable_functions')));
        $tests[] = $obj;

        // recommended test... remote_url
        $obj = new _tests_\boolean_test('remote_url',_tests_\test_remote_file('https://www.cmsmadesimple.org/latest_version.php',3,'cmsmadesimple'));
        $obj->fail_key = 'fail_remote_url';
        $obj->warn_key = 'fail_remote_url';
        $tests[] = $obj;

        // file get contents.
        $obj = new _tests_\boolean_test('file_get_contents',function_exists('file_get_contents'));
        $obj->required = 1;
        $obj->fail_key = 'fail_file_get_contents';
        $tests[] = $obj;

        // test ini set
        {
            $val = (ini_get('log_errors_max_len')) ? ini_get('log_errors_max_len').'0':'99';
            ini_set('log_errors_max_len',$val);
            $obj = new _tests_\boolean_test('ini_set',ini_get('log_errors_max_len') == $val);
            $obj->fail_key = 'fail_ini_set';
            $tests[] = $obj;
        }

        //
        // now run the tests
        // if all tests pass
        //   display warm fuzzy message
        //   user can continue
        // else if a required test fails
        //   display failed tests (or all tests for verbose mode)
        //   user cant continue
        // otherwise
        //   display failed tests (or all tests for verbose mode)
        //   user can continue
        $can_continue = TRUE;
        $tests_failed = FALSE;
        $results = array();
        for( $i = 0; $i < count($tests); $i++ ) {
            $res = $tests[$i]->run();
            if( $res == $tests[$i]::TEST_FAIL ) {
                $tests_failed = TRUE;
                $results[] = $tests[$i];
                if( $tests[$i]->required ) {
                    $can_continue = FALSE;
                }
                else {
                    $tests[$i]->status = $tests[$i]::TEST_WARN;
                }
            }
        }
        if( !$verbose ) $tests = $results;
        return array($tests_failed,$can_continue);
    }

    protected function display()
    {
        parent::display();
        $verbose = $this->get_wizard()->get_data('verbose',0);
        $informational = '';
        $tests = '';
        list($tests_failed,$can_continue) = $this->perform_tests($verbose,$informational,$tests);

        $app = \__appbase\get_app();
        $smarty = \__appbase\smarty();
        $smarty->assign('tests_failed',$tests_failed);
        $smarty->assign('can_continue',$can_continue);
        $smarty->assign('verbose',$verbose);
        $smarty->assign('retry_url',$_SERVER['REQUEST_URI']);
        if( $verbose ) $smarty->assign('information',$informational);
        if( count($tests) )	$smarty->assign('tests',$tests);
        $url = $this->get_wizard()->next_url();
        $smarty->assign('next_url',$url);

        // todo: urls for retry, and enable verbose mode.
        $smarty->display('wizard_step3.tpl');
        $this->finish();
    }

    private function _get_session_save_path()
    {
        $path = ini_get('session.save_path');
        if( ($pos = strpos($path,';')) !== FALSE) $path = substr($path,$pos+1);

        if( $path ) return $path;
    }

    private function _GDVersion()
    {
        static $gd_version_number = null;

        if(is_null($gd_version_number)) {
            if(extension_loaded('gd')) {
                if(defined('GD_MAJOR_VERSION')) {
                    $gd_version_number = GD_MAJOR_VERSION;
                    return $gd_version_number;
                }
                $gdinfo = @gd_info();
                if(preg_match('/\d+/', $gdinfo['GD Version'], $gdinfo)) {
                    $gd_version_number = (int) $gdinfo[0];
                } else {
                    $gd_version_number = 1;
                }
                return $gd_version_number;
            }
            $gd_version_number = 0;
        }

        return $gd_version_number;
    }

} // end of class

?>
