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
#$Id$

class CMSInstallerPage1 extends CMSInstallerPage
{
	var $continueon;
	var $special_failed;

	/**
	 * Class constructor
	 * @var object $smarty
	 * @var array  $errors
	 */
	function CMSInstallerPage1(&$smarty, $errors)
	{
		$this->CMSInstallerPage(1, $smarty, $errors);
		$return = testGlobal(array(true, false), true);
	}

	function assignVariables()
	{
		$settings = array('info'=>array(), 'required'=>array(), 'recommended'=>array());


		/*
		 * Info Settings
		 */
		$settings['info']['server_software'] = $_SERVER['SERVER_SOFTWARE'];
		$settings['info']['server_api'] = PHP_SAPI;
		$settings['info']['server_os'] = PHP_OS.' '.php_uname('r').' '.lang('on').' '.php_uname('m');


		/*
		 * Required Settings
		 */
		list($minimum, $recommended) = getTestValues('phpversion');
		$settings['required'][] =
			testVersionRange(true, lang('test_check_php'),
				phpversion(), $minimum, $recommended,
				lang('test_requires_php_version', array(phpversion(), $recommended)));

		$settings['required'][] = testBoolean(1, lang('test_check_md5_func'), function_exists('md5'), '');

		$settings['required'][] = testIniBoolean(1, lang('test_check_safe_mode'), 'safe_mode', lang('test_check_safe_mode_failed'), true);

		$settings['required'][] = testBoolean(1, lang('test_check_tokenizer'), function_exists('token_get_all'), lang('test_check_tokenizer_failed'));

		list($minimum, $recommended) = getTestValues('gd_version');
		$settings['required'][] = testGDVersion(1, lang('test_check_gd'), $minimum, lang('test_check_gd_failed'));

		$settings['required'][] =
			testBoolean(1,
				lang('test_check_write') .' '. CONFIG_FILE_LOCATION,
				@is_writable(CONFIG_FILE_LOCATION),
				lang('test_may_not_exist'), $this);


		/*
		 * Recommended Settings
		 */
		list($minimum, $recommended) = getTestValues('memory_limit');
		$settings['recommended'][] = testIniRange(0, lang('test_check_memory'), 'memory_limit', $minimum, $recommended, lang('test_check_memory_failed'), true);

		list($minimum, $recommended) = getTestValues('max_execution_time');
		$settings['recommended'][] = testIniRange(0, lang('test_check_time_limit'), 'max_execution_time', $minimum, $recommended, lang('test_check_time_limit_failed'), true);

		$settings['recommended'][] = testIniBoolean(0, lang('test_check_file_upload'), 'file_uploads', lang('test_check_file_failed'), false);

		list($minimum, $recommended) = getTestValues('post_max_size');
		$settings['recommended'][] = testIniRange(0, lang('test_check_post_max'), 'post_max_size', $minimum, $recommended, lang('test_check_post_max_failed'), true);

		list($minimum, $recommended) = getTestValues('upload_max_filesize');
		$settings['recommended'][] = testIniRange(0, lang('test_check_upload_max'), 'upload_max_filesize', $minimum, $recommended, lang('test_check_upload_max_failed'), true);

		$f = cms_join_path(CMS_BASE, 'uploads');
		$settings['recommended'][] = testDirWrite(0, lang('test_check_writable', $f), $f, lang('test_check_upload_failed'));

		$f = cms_join_path(CMS_BASE, 'uploads' . DIRECTORY_SEPARATOR . 'images');
		$settings['recommended'][] = testDirWrite(0, lang('test_check_writable', $f), $f, lang('test_check_images_failed'));

		$f = cms_join_path(CMS_BASE, 'modules');
		$settings['recommended'][] = testDirWrite(0, lang('test_check_writable', $f), $f, lang('test_check_modules_failed'));

		$dir = ini_get('session.save_path');
		if ( (ini_get('session.save_handler') == 'files') && (empty($dir)) )
		{
			$settings['recommended'][] = testDummy(lang('test_check_session_save_path'), '', 'yellow', lang('test_empty_session_save_path'));
		}
		else
		{
			$settings['recommended'][] = testDirWrite(0, lang('test_check_session_save_path'), session_save_path(), lang('test_check_session_save_path_failed', session_save_path()));
		}

		$settings['recommended'][] = testBoolean(0, lang('test_check_xml_func'), function_exists('xml_parser_create'), lang('test_check_xml_failed'));

		$settings['recommended'][] = testBoolean(0, lang('test_check_file_get_contents'), function_exists('file_get_contents'), lang('test_check_file_get_contents_failed'));
		ini_set('max_execution_time', '123');

		$result = (ini_get('max_execution_time') == 123);
		$settings['recommended'][] = testBoolean(0, lang('test_check_ini_set'), $result, lang('test_check_ini_set_failed'));

		// assign settings
		list($this->continueon, $this->special_failed) = testGlobal(array(true, false), true);

		$this->smarty->assign('settings', $settings);
		$this->smarty->assign('continueon', $this->continueon);
		$this->smarty->assign('special_failed', $this->special_failed);
	}
}
?>
