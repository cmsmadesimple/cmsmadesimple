<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

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

#
# EOF
#