<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
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
#$Id: CMSInstallPage2.class.php 296 2010-10-17 22:31:18Z calguy1000 $

class CMSInstallerPage2 extends CMSInstallerPage
{
	var $continueon;
	var $special_failed;

	/**
	 * Class constructor
	 * @var object $smarty
	 * @var array  $errors
	 * @var bool   $debug
	 */
	function CMSInstallerPage2(CMSInstaller& $installer, $number, &$smarty, $errors, $debug)
	{
	  $this->CMSInstallerPage($installer, 2, $smarty, $errors, $debug);
		$return = testGlobal(array(true, false), true);
	}

	function assignVariables()
	{
		$settings = array('info'=>array(), 'required'=>array(), 'recommended'=>array());

		$safe_mode = ini_get('safe_mode');
		$open_basedir = ini_get('open_basedir');


		/*
		 * Info Settings
		 */
		$settings['info']['server_software'] = $_SERVER['SERVER_SOFTWARE'];
		$settings['info']['server_api'] = PHP_SAPI;
		$settings['info']['server_os'] = PHP_OS .' '. php_uname('r') .' '. ilang('on') .' '. php_uname('m');
		if(extension_loaded_or('apache2handler'))
		{
			$settings['info']['mod_security'] = getApacheModules('mod_security') ? ilang('on') : ilang('off');
		}


		/*
		 * Required Settings
		 */
		list($minimum, $recommended) = getTestValues('php_version');
		$settings['required'][] =
			testVersionRange(1, ilang('test_check_php', $minimum) .'<br />'. ilang('test_min_recommend', $minimum, $recommended),
				phpversion(), ilang('test_requires_php_version', phpversion(), $recommended), $minimum, $recommended, false);

		$settings['required'][] = testBoolean(1, ilang('test_check_md5_func'), function_exists('md5'), '', false, false, 'Function_md5_disabled');

		list($minimum, $recommended) = getTestValues('gd_version');
		$settings['required'][] = testGDVersion(1, ilang('test_check_gd'), $minimum, ilang('test_check_gd_failed'), 'min_GD_version');

		$settings['required'][] =
			testFileWritable(1, ilang('test_check_write') . ' config.php', CONFIG_FILE_LOCATION, ilang('test_may_not_exist'), $this->debug);

		$settings['required'][] = testBoolean(1, ilang('test_check_tempnam'), function_exists('tempnam'), '', false, false, 'Function_tempnam_disabled');

		$settings['required'][] =
			testBoolean(1, ilang('test_check_magic_quotes_runtime'), 'magic_quotes_runtime', ilang('test_check_magic_quotes_runtime_failed'), true, true, 'magic_quotes_runtime_On');

		$settings['required'][] =
			testSupportedDatabase(1, ilang('test_check_db_drivers'), false, ilang('test_check_db_drivers_failed'));

		if( ('1' != $safe_mode) && (! isset($_SESSION['allowsafemode'])) )
		{
			$settings['required'][] = testCreateDirAndFile(1, ilang('test_create_dir_and_file'), ilang('info_create_dir_and_file'), $this->debug);
		}


		/*
		 * Recommended Settings
		 */
		list($minimum, $recommended) = getTestValues('memory_limit');
		$settings['recommended'][] =
		  testIntegerMask(0,ilang('test_error_estrict'), 'error_reporting',E_STRICT,ilang('test_estrict_failed'),true,true,false);

		if( defined('E_DEPRECATED') )
		  {
		$settings['recommended'][] =
		  testIntegerMask(0,ilang('test_error_edeprecated'), 'error_reporting',E_DEPRECATED,ilang('test_edeprecated_failed'),true,true,false);
		  }


		$settings['recommended'][] =
			testRange(0, ilang('test_check_memory') .'<br />'. ilang('test_min_recommend', $minimum, $recommended),
				'memory_limit', ilang('test_check_memory_failed'), $minimum, $recommended, true, true, null, 'memory_limit_range');

		list($minimum, $recommended) = getTestValues('max_execution_time');
		$settings['recommended'][] =
			testRange(0, ilang('test_check_time_limit') .'<br />'. ilang('test_min_recommend', $minimum, $recommended),
				'max_execution_time', ilang('test_check_time_limit_failed'), $minimum, $recommended, true, false, 0, 'max_execution_time_range');

		$settings['recommended'][] = testBoolean(0, ilang('test_check_register_globals'), 'register_globals', ilang('test_check_register_globals_failed'), true, true, 'register_globals_enabled');

		$settings['recommended'][] = testInteger(0, ilang('test_check_output_buffering'), 'output_buffering', ilang('test_check_output_buffering_failed'), true, true, 'output_buffering_disabled');

		$settings['recommended'][] = testString(0, ilang('test_check_disable_functions'), 'disable_functions', ilang('test_check_disable_functions_failed'), true, 'green', 'yellow', 'disable_functions_not_empty');

		if(! isset($_SESSION['allowsafemode']))
		{
			$settings['recommended'][] = testBoolean(0, ilang('test_check_safe_mode'), 'safe_mode', ilang('test_check_safe_mode_failed'), true, true, 'safe_mode_enabled');
		}

		$settings['recommended'][] = testString(0, ilang('test_check_open_basedir'), $open_basedir, ilang('test_check_open_basedir_failed'), false, 'green', 'yellow', 'open_basedir_enabled');

		if(! isset($_SESSION['skipremote']))
		{
			$settings['recommended'][] = testRemoteFile(0, ilang('test_remote_url'), '', ilang('test_remote_url_failed'), $this->debug);
		}

		$settings['recommended'][] = testBoolean(0, ilang('test_check_file_upload'), 'file_uploads', ilang('test_check_file_failed'), true, false, 'Function_file_uploads_disabled');

		list($minimum, $recommended) = getTestValues('post_max_size');
		$settings['recommended'][] =
			testRange(0, ilang('test_check_post_max') .'<br />'. ilang('test_min_recommend', $minimum, $recommended),
				'post_max_size', ilang('test_check_post_max_failed'), $minimum, $recommended, true, true, null, 'min_post_max_size');

		list($minimum, $recommended) = getTestValues('upload_max_filesize');
		$settings['recommended'][] =
			testRange(0, ilang('test_check_upload_max') .'<br />'. ilang('test_min_recommend', $minimum, $recommended),
				'upload_max_filesize', ilang('test_check_upload_max_failed'), $minimum, $recommended, true, true, null, 'min_upload_max_filesize');

		$f = cms_join_path(CMS_BASE, 'uploads');
		$settings['recommended'][] = testDirWrite(0, ilang('test_check_writable', $f), $f, ilang('test_check_upload_failed'), 0, $this->debug);

		$f = cms_join_path(CMS_BASE, 'uploads' . DIRECTORY_SEPARATOR . 'images');
		$settings['recommended'][] = testDirWrite(0, ilang('test_check_writable', $f), $f, ilang('test_check_images_failed'), 0, $this->debug);

		$f = cms_join_path(CMS_BASE, 'modules');
		$settings['recommended'][] = testDirWrite(0, ilang('test_check_writable', $f), $f, ilang('test_check_modules_failed'), 0, $this->debug);

		$session_save_path = testSessionSavePath('');
		if(empty($session_save_path))
		{
			$settings['recommended'][] = testDummy(ilang('test_check_session_save_path'), '', 'yellow', ilang('test_empty_session_save_path'), 'session_save_path_empty', '');
		}
		elseif (! empty($open_basedir))
		{
			$settings['recommended'][] = testDummy(ilang('test_check_session_save_path'), '', 'yellow', ilang('test_open_basedir_session_save_path'), 'No_check_session_save_path_with_open_basedir', '');
		}
		else
		{
			$settings['recommended'][] =
				testDirWrite(0, ilang('test_check_session_save_path'), $session_save_path,
					ilang('test_check_session_save_path_failed', $session_save_path), 1, $this->debug);
		}
		$settings['recommended'][] = testBoolean(0, 'session.use_cookies', 'session.use_cookies', ilang('session_use_cookies'));

		$settings['recommended'][] =
			testBoolean(0, ilang('test_check_xml_func'),
				extension_loaded_or('xml'), ilang('test_check_xml_failed'), false, false, 'Function_xml_disabled');

		$settings['recommended'][] =
		  testBoolean(0,ilang('test_xmlreader_class'),class_exists('XMLReader',false), 
			      ilang('test_xmlreader_failed'), false, false, 'class_xmlreader_unavailable');

		$settings['recommended'][] =
			testBoolean(0, ilang('test_check_file_get_contents'),
				function_exists('file_get_contents'), ilang('test_check_file_get_contents_failed'), false, false, 'Function_file_get_content_disabled');

#		$settings['recommended'][] =
#			testBoolean(0, ilang('test_check_magic_quotes_gpc'),
#				'magic_quotes_gpc', ilang('test_check_magic_quotes_gpc_failed'), true, true, 'magic_quotes_gpc_On');

		$_log_errors_max_len = (ini_get('log_errors_max_len')) ? ini_get('log_errors_max_len').'0' : '99';
		ini_set('log_errors_max_len', $_log_errors_max_len);
		$result = (ini_get('log_errors_max_len') == $_log_errors_max_len);
		$settings['recommended'][] = testBoolean(0, ilang('test_check_ini_set'), $result, ilang('test_check_ini_set_failed'), false, false, 'ini_set_disabled');

		// curl tests
		$hascurl = 0;
		$curlgood = 0;
		$curl_version = '';
		$min_curlversion = '7.19.7';
		if( in_array('curl',get_loaded_extensions()) ) {
		  $hascurl = 1;
		  if( function_exists('curl_version') ) {
		    $t = curl_version();
		    if( isset($t['version']) ) {
		      $curl_version = $t['version'];
		      if( version_compare($t['version'],$min_curlversion) >= 0 ) {
			$curlgood = 1;
		      }
		    }
		  }
		}
		if( !$hascurl ) {
		  $settings['recommended'][] = testDummy(lang('curl'),lang('off'),'yellow','','curl_not_available','');
		}
		else {
		  $settings['recommended'][] = testDummy(lang('curl'),lang('on'),'green');
		  if( $curlgood ) {
		    $settings['recommended'][] = testDummy(lang('curlversion'),
							   lang('curl_versionstr',$curl_version,$min_curlversion),
							   'green');
		  }
		  else {
		    $settings['recommended'][] = testDummy(lang('curlversion'),lang('test_curlversion'),'yellow',
							   lang('curl_versionstr',$curl_version,$min_curlversion));
		  }
		}


		// assign settings
		list($this->continueon, $this->special_failed) = testGlobal(array(true, false), true);

		$this->smarty->assign('caution',ilang('caution'));
		$this->smarty->assign('settings', $settings);
		$this->smarty->assign('special_failed', $this->special_failed);
		if(isset($_SESSION['advanceduser']))
		{
			$this->smarty->assign('continueon', true);
		}
		else
		{
			$this->smarty->assign('continueon', $this->continueon);
		}
		$this->smarty->assign('phpinfo', getEmbedPhpInfo(INFO_CONFIGURATION | INFO_MODULES));

		$this->smarty->assign('errors', $this->errors);
	}
}
# vim:ts=4 sw=4 noet
?>
