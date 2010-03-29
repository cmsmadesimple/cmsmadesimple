<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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
#$Id: CMSUpgradePage2.class.php 215 2009-07-18 07:04:18Z alby $

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
	function CMSInstallerPage2(&$smarty, $errors, $debug)
	{
		$this->CMSInstallerPage(2, $smarty, $errors, $debug);
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
		$settings['info']['server_os'] = PHP_OS .' '. php_uname('r') .' '. lang('on') .' '. php_uname('m');
		if(extension_loaded_or('apache2handler'))
		{
			$settings['info']['mod_security'] = getApacheModules('mod_security') ? lang('on') : lang('off');
		}


		/*
		 * Required Settings
		 */
		list($minimum, $recommended) = getTestValues('php_version');
		$settings['required'][] =
			testVersionRange(1, lang('test_check_php', $minimum) .'<br />'. lang('test_min_recommend', $minimum, $recommended),
				phpversion(), lang('test_requires_php_version', phpversion(), $recommended), $minimum, $recommended, false);

		$settings['required'][] = testBoolean(1, lang('test_check_md5_func'), function_exists('md5'), '', false, false, 'Function_md5_disabled');

		list($minimum, $recommended) = getTestValues('gd_version');
		$settings['required'][] = testGDVersion(1, lang('test_check_gd'), $minimum, lang('test_check_gd_failed'), 'min_GD_version');

		$settings['required'][] =
			testFileWritable(1, lang('test_check_write') . ' config.php', CONFIG_FILE_LOCATION, lang('test_may_not_exist'), $this->debug);

		$settings['required'][] = testBoolean(1, lang('test_check_tempnam'), function_exists('tempnam'), '', false, false, 'Function_tempnam_disabled');

		$settings['required'][] =
			testBoolean(1, lang('test_check_magic_quotes_runtime'), 'magic_quotes_runtime', lang('test_check_magic_quotes_runtime_failed'), true, true, 'magic_quotes_runtime_On');

		$settings['required'][] =
			testSupportedDatabase(1, lang('test_check_db_drivers'), false, lang('test_check_db_drivers_failed'));

		if( ('1' != $safe_mode) && (! isset($_SESSION['allowsafemode'])) )
		{
			$settings['required'][] = testCreateDirAndFile(1, lang('test_create_dir_and_file'), lang('info_create_dir_and_file'), $this->debug);
		}



		/*
		 * Recommended Settings
		 */
		list($minimum, $recommended) = getTestValues('memory_limit');
		$settings['recommended'][] =
			testRange(0, lang('test_check_memory') .'<br />'. lang('test_min_recommend', $minimum, $recommended),
				'memory_limit', lang('test_check_memory_failed'), $minimum, $recommended, true, true, null, 'memory_limit_range');

		list($minimum, $recommended) = getTestValues('max_execution_time');
		$settings['recommended'][] =
			testRange(0, lang('test_check_time_limit') .'<br />'. lang('test_min_recommend', $minimum, $recommended),
				'max_execution_time', lang('test_check_time_limit_failed'), $minimum, $recommended, true, false, 0, 'max_execution_time_range');

		$settings['recommended'][] = testBoolean(0, lang('test_check_register_globals'), 'register_globals', lang('test_check_register_globals_failed'), true, true, 'register_globals_enabled');

		$settings['recommended'][] = testInteger(0, lang('test_check_output_buffering'), 'output_buffering', lang('test_check_output_buffering_failed'), true, true, 'output_buffering_disabled');

		$settings['recommended'][] = testString(0, lang('test_check_disable_functions'), 'disable_functions', lang('test_check_disable_functions_failed'), true, 'green', 'yellow', 'disable_functions_not_empty');

		if(! isset($_SESSION['allowsafemode']))
		{
			$settings['recommended'][] = testBoolean(0, lang('test_check_safe_mode'), 'safe_mode', lang('test_check_safe_mode_failed'), true, true, 'safe_mode_enabled');
		}

		$settings['recommended'][] = testString(0, lang('test_check_open_basedir'), $open_basedir, lang('test_check_open_basedir_failed'), false, 'green', 'yellow', 'open_basedir_enabled');

		if(! isset($_SESSION['skipremote']))
		{
			$settings['recommended'][] = testRemoteFile(0, lang('test_remote_url'), '', lang('test_remote_url_failed'), $this->debug);
		}

		$settings['recommended'][] = testBoolean(0, lang('test_check_file_upload'), 'file_uploads', lang('test_check_file_failed'), true, false, 'Function_file_uploads_disabled');

		list($minimum, $recommended) = getTestValues('post_max_size');
		$settings['recommended'][] =
			testRange(0, lang('test_check_post_max') .'<br />'. lang('test_min_recommend', $minimum, $recommended),
				'post_max_size', lang('test_check_post_max_failed'), $minimum, $recommended, true, true, null, 'min_post_max_size');

		list($minimum, $recommended) = getTestValues('upload_max_filesize');
		$settings['recommended'][] =
			testRange(0, lang('test_check_upload_max') .'<br />'. lang('test_min_recommend', $minimum, $recommended),
				'upload_max_filesize', lang('test_check_upload_max_failed'), $minimum, $recommended, true, true, null, 'min_upload_max_filesize');

		$f = cms_join_path(CMS_BASE, 'tmp');
		$settings['recommended'][] = testDirWrite(0, lang('test_check_writable', $f), $f, lang('test_check_tmp_failed'), 0, $this->debug);

		$f = cms_join_path(CMS_BASE, 'uploads');
		$settings['recommended'][] = testDirWrite(0, lang('test_check_writable', $f), $f, lang('test_check_upload_failed'), 0, $this->debug);

		$f = cms_join_path(CMS_BASE, 'uploads' . DIRECTORY_SEPARATOR . 'images');
		$settings['recommended'][] = testDirWrite(0, lang('test_check_writable', $f), $f, lang('test_check_images_failed'), 0, $this->debug);

		$f = cms_join_path(CMS_BASE, 'modules');
		$settings['recommended'][] = testDirWrite(0, lang('test_check_writable', $f), $f, lang('test_check_modules_failed'), 0, $this->debug);

		$session_save_path = testSessionSavePath('');
		if(empty($session_save_path))
		{
			$settings['recommended'][] = testDummy(lang('test_check_session_save_path'), '', 'yellow', lang('test_empty_session_save_path'), 'session_save_path_empty', '');
		}
		elseif (! empty($open_basedir))
		{
			$settings['recommended'][] = testDummy(lang('test_check_session_save_path'), '', 'yellow', lang('test_open_basedir_session_save_path'), 'No_check_session_save_path_with_open_basedir', '');
		}
		else
		{
			$settings['recommended'][] =
				testDirWrite(0, lang('test_check_session_save_path'), $session_save_path,
					lang('test_check_session_save_path_failed', $session_save_path), 1, $this->debug);
		}

		$settings['recommended'][] =
			testBoolean(0, lang('test_check_xml_func'),
				extension_loaded_or('xml'), lang('test_check_xml_failed'), false, false, 'Function_xml_disabled');

		$settings['recommended'][] =
			testBoolean(0, lang('test_check_file_get_contents'),
				function_exists('file_get_contents'), lang('test_check_file_get_contents_failed'), false, false, 'Function_file_get_content_disabled');

#		$settings['recommended'][] =
#			testBoolean(0, lang('test_check_magic_quotes_gpc'),
#				'magic_quotes_gpc', lang('test_check_magic_quotes_gpc_failed'), true, true, 'magic_quotes_gpc_On');

		$_log_errors_max_len = (ini_get('log_errors_max_len')) ? ini_get('log_errors_max_len').'0' : '99';
		ini_set('log_errors_max_len', $_log_errors_max_len);
		$result = (ini_get('log_errors_max_len') == $_log_errors_max_len);
		$settings['recommended'][] = testBoolean(0, lang('test_check_ini_set'), $result, lang('test_check_ini_set_failed'), false, false, 'ini_set_disabled');



		// assign settings
		list($this->continueon, $this->special_failed) = testGlobal(array(true, false), true);

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
