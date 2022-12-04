<?php
/**
 * Smarty plugin
 * Type:     modifier<br>
 * Name:     cms_date_format<br>
 * Purpose:  format a supplied date-time string using PHP date() or strftime()-replacment
 * Input:<br>
 *          - string: input date string
 *          - format: strftime()-compatible or date()-compatible format for output
 *          - default_date: default date if $string is empty
 *
 * @link http://www.smarty.net/manual/en/language.modifier.date.format.php date_format (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param mixed $string       input date/time, a UNIX timestamp or other format supported by PHP strtotime()
 * @param string $format      strftime()- or date()-compatible format for output
 * @param mixed $default_date default date if $string is empty
 * @return string | void
 *
 * Modified by Tapio Löytty <stikki@cmsmadesimple.org>
 */
function smarty_modifier_cms_date_format($string, $format = '', $default_date = '')
{
	if($format == '') {
		$format = get_site_preference('defaultdateformat');
		if($format == '') $format = '%b j, Y';
		if(!CmsApp::get_instance()->is_frontend_request()) {
			if($uid = get_userid(false)) {
				$tmp = get_preference($uid, 'date_format_string');
				if($tmp != '') $format = $tmp;
			}
		}
	}

	if (strpos($format, '%') !== false) {
		require_once __DIR__.DIRECTORY_SEPARATOR.'modifier.localedate_format.php';
		$out = smarty_modifier_localedate_format($string, $format, $default_date);
	} else {
		$fn = cms_join_path(SMARTY_PLUGINS_DIR, 'modifier.date_format.php');
		if (!is_file($fn)) exit;
		include_once $fn;
		$out = smarty_modifier_date_format($string, $format, $default_date);
	}
	return $out;
}
// EOF
?>
