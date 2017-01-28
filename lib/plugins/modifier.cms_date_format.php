<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty date_format modifier plugin
 *
 * Type:     modifier<br>
 * Name:     cms_date_format<br>
 * Purpose:  format datestamps via strftime<br>
 * Input:<br>
 *          - string: input date string
 *          - format: strftime format for output
 *          - default_date: default date if $string is empty
 *
 * @link http://www.smarty.net/manual/en/language.modifier.date.format.php date_format (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param string $string       input date string
 * @param string $format       strftime format for output
 * @param string $default_date default date if $string is empty
 * @param string $formatter    either 'strftime' or 'auto'
 * @return string |void
 * @uses smarty_make_timestamp()
 *
 * Modified by Tapio Löytty <stikki@cmsmadesimple.org>
 */
function smarty_modifier_cms_date_format($string, $format = '', $default_date = '')
{
	if($format == '') {
		$format = get_site_preference('defaultdateformat');
		if($format == '') $format = '%b %e, %Y';
		if(!CmsApp::get_instance()->is_frontend_request()) {
			if($uid = get_userid(false)) {
				$tmp = get_preference($uid, 'date_format_string');
				if($tmp != '') $format = $tmp;
			}
		}
	}

	$fn = cms_join_path(SMARTY_PLUGINS_DIR, 'modifier.date_format.php');
	if(!file_exists($fn)) die();

	require_once( $fn );

	return smarty_modifier_date_format($string,$format,$default_date);
}
// EOF
?>
