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

function smarty_cms_function_mod_link($params, &$smarty)
{
	$module =& $smarty->get_template_vars('cms_mapi_module');
	$id = $smarty->get_template_vars('cms_mapi_id');
	$return_id = $smarty->get_template_vars('cms_mapi_return_id');
	$translate = coalesce_key($params, 'translate', true, FILTER_VALIDATE_BOOLEAN);

	$value = ($translate === true) ? $module->lang($params['value']) : $params['value'];
	$warn_message = ($translate === true) ? $module->lang($params['warn_message']) : $params['warn_message'];

	if (isset($params['theme_image']))
	{
		$themeObject = CmsAdminTheme::get_instance();
		$image = $themeObject->display_image($params['theme_image'], $value,'','','systemicon');
		if( isset($params['showtext']) )
		{
			$value = $image.'&nbsp;'.$value;
		}
		else
		{
			$value = $image;
		}
	}

	$other_params = remove_keys($params, array('action', 'value', 'warn_message', 'translate', 'only_href', 
						'inline', 'additional_text', 'target_container_only', 'pretty_url', 'theme_image'));

	$blah = $module->create_link($id, $params['action'], $return_id, $value, 
				     $other_params, $warn_message, coalesce_key($params, 'only_href', false, FILTER_VALIDATE_BOOLEAN), 
				     coalesce_key($params, 'inline', false, FILTER_VALIDATE_BOOLEAN), coalesce_key($params, 'additional_text', ''), 
				     coalesce_key($params, 'target_container_only', false, FILTER_VALIDATE_BOOLEAN), coalesce_key($params, 'pretty_url', ''));
						
	return $blah;
}

# vim:ts=4 sw=4 noet
?>
