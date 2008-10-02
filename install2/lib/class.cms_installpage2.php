<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
#$Id$

class CmsInstallPage extends CmsObject
{
	function __construct()
	{
		parent::__construct();
	}

	static function process_content(&$smarty, $debug)
	{
		$errors = array();

		$return = testGlobal(array(true, false), true);
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
			$settings['info']['mod_security'] = getApacheModules('mod_security') ? lang('On') : lang('Off');
		}



		/*
		 * Required Settings
		 */
		list($minimum, $recommended) = getTestValues('phpversion');
		$settings['required'][] =
			testVersionRange(true, lang('test_check_php', $minimum) .'<br />'. lang('test_min_recommend', $minimum, $recommended),
				phpversion(), lang('test_requires_php_version', phpversion(), $recommended), $minimum, $recommended, false);

		$settings['required'][] = testBoolean(1, lang('test_check_md5_func'), function_exists('md5'), '', false);

		list($minimum, $recommended) = getTestValues('gd_version');
		$settings['required'][] = testGDVersion(1, lang('test_check_gd'), $minimum, lang('test_check_gd_failed'));

		$settings['required'][] =
			testFileWritable(1, lang('test_check_write') . ' config.php', CONFIG_FILE_LOCATION, lang('test_may_not_exist'), $debug);

		$settings['required'][] =
			testBoolean(1, lang('test_check_magic_quotes_runtime'), 'magic_quotes_runtime', lang('test_check_magic_quotes_runtime_failed'), true, true);

		$settings['required'][] =
			testSupportedDatabase(1, lang('test_check_db_drivers'), false, lang('test_check_db_drivers_failed'));

		$settings['required'][] =
			testBoolean(1, lang('test_check_xml_func'),
				extension_loaded_or('xml'), lang('test_check_xml_failed'), false, false);

		$settings['required'][] =
			testBoolean(1, lang('test_check_simplexml_func'),
				extension_loaded_or('simplexml'), lang('test_check_simplexml_failed'), false, false);

		if( ('1' != $safe_mode) && (! isset($_SESSION['allowsafemode'])) )
		{
			$settings['required'][] = testCreateDirAndFile(1, lang('test_create_dir_and_file'), lang('info_create_dir_and_file'), $debug);
		}



		/*
		 * Recommended Settings
		 */
		list($minimum, $recommended) = getTestValues('memory_limit');
		$settings['recommended'][] =
			testRange(0, lang('test_check_memory') .'<br />'. lang('test_min_recommend', $minimum, $recommended),
				'memory_limit', lang('test_check_memory_failed'), $minimum, $recommended, true, true);

		list($minimum, $recommended) = getTestValues('max_execution_time');
		$settings['recommended'][] =
			testRange(0, lang('test_check_time_limit') .'<br />'. lang('test_min_recommend', $minimum, $recommended),
				'max_execution_time', lang('test_check_time_limit_failed'), $minimum, $recommended, true, false, 0);

		$settings['recommended'][] = testBoolean(0, lang('test_check_register_globals'), 'register_globals', lang('test_check_register_globals_failed'), true, true);

		$settings['recommended'][] = testBoolean(0, lang('test_check_output_buffering'), 'output_buffering', lang('test_check_output_buffering_failed'), true, true);

		$settings['recommended'][] = testString(0, lang('test_check_disable_functions'), 'disable_functions', lang('test_check_disable_functions_failed'), true, 'yellow');

		if(! isset($_SESSION['allowsafemode']))
		{
			$settings['recommended'][] = testBoolean(0, lang('test_check_safe_mode'), 'safe_mode', lang('test_check_safe_mode_failed'), true, true);
		}

		$settings['recommended'][] = testString(0, lang('test_check_open_basedir'), $open_basedir, lang('test_check_open_basedir_failed'), false, 'yellow');

		$settings['recommended'][] = testRemoteFile(0, lang('test_remote_url'), '', lang('test_remote_url_failed'), $debug);

		$settings['recommended'][] = testBoolean(0, lang('test_check_file_upload'), 'file_uploads', lang('test_check_file_failed'), true, false);

		list($minimum, $recommended) = getTestValues('post_max_size');
		$settings['recommended'][] =
			testRange(0, lang('test_check_post_max') .'<br />'. lang('test_min_recommend', $minimum, $recommended),
				'post_max_size', lang('test_check_post_max_failed'), $minimum, $recommended, true, true);

		list($minimum, $recommended) = getTestValues('upload_max_filesize');
		$settings['recommended'][] =
			testRange(0, lang('test_check_upload_max') .'<br />'. lang('test_min_recommend', $minimum, $recommended),
				'upload_max_filesize', lang('test_check_upload_max_failed'), $minimum, $recommended, true, true);

		$f = cms_join_path(CMS_BASE, 'uploads');
		$settings['recommended'][] = testDirWrite(0, lang('test_check_writable', $f), $f, lang('test_check_upload_failed'), 0, $debug);

		$f = cms_join_path(CMS_BASE, 'uploads' . DIRECTORY_SEPARATOR . 'images');
		$settings['recommended'][] = testDirWrite(0, lang('test_check_writable', $f), $f, lang('test_check_images_failed'), 0, $debug);

		$f = cms_join_path(CMS_BASE, 'modules');
		$settings['recommended'][] = testDirWrite(0, lang('test_check_writable', $f), $f, lang('test_check_modules_failed'), 0, $debug);

		$session_save_path = testSessionSavePath('');
		if(empty($session_save_path))
		{
			$settings['recommended'][] = testDummy(lang('test_check_session_save_path'), '', 'yellow', lang('test_empty_session_save_path'));
		}
		elseif (! empty($open_basedir))
		{
			$settings['recommended'][] = testDummy(lang('test_check_session_save_path'), '', 'yellow', lang('test_open_basedir_session_save_path'));
		}
		else
		{
			$settings['recommended'][] =
				testDirWrite(0, lang('test_check_session_save_path'), $session_save_path,
					lang('test_check_session_save_path_failed', $session_save_path), 1, $debug);
		}

		$settings['recommended'][] =
			testBoolean(0, lang('test_check_file_get_contents'),
				function_exists('file_get_contents'), lang('test_check_file_get_contents_failed'), false);

		$settings['recommended'][] =
			testBoolean(0, lang('test_check_magic_quotes_gpc'),
				'magic_quotes_gpc', lang('test_check_magic_quotes_gpc_failed'), true, true);

		$_log_errors_max_len = (ini_get('log_errors_max_len')) ? ini_get('log_errors_max_len').'0' : '99';
		ini_set('log_errors_max_len', $_log_errors_max_len);
		$result = (ini_get('log_errors_max_len') == $_log_errors_max_len);
		$settings['recommended'][] = testBoolean(0, lang('test_check_ini_set'), $result, lang('test_check_ini_set_failed'), false);



		// assign settings
		list($continueon, $special_failed) = testGlobal(array(true, false), true);

		$smarty->assign('settings', $settings);
		$smarty->assign('special_failed', $special_failed);
		if(isset($_SESSION['advanceduser']))
		{
			$smarty->assign('continueon', true);
		}
		else
		{
			$smarty->assign('continueon', $continueon);
		}
		$smarty->assign('phpinfo', getEmbedPhpInfo(INFO_CONFIGURATION | INFO_MODULES));

		return $errors;
	}
}
# vim:ts=4 sw=4 noet
?>
