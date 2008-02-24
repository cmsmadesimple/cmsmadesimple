<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
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

function smarty_cms_block_mod_form($params, $content, &$smarty, &$repeat)
{
	$module =& $smarty->get_template_vars('cms_mapi_module');
	$id = $smarty->get_template_vars('cms_mapi_id');
	$return_id = $smarty->get_template_vars('cms_mapi_return_id');
	
	if (!$repeat)
	{
		if (isset($content))
		{
			return $module->create_form_start(
				$id,
				coalesce_key($params, 'action', 'default'),
				$return_id, 
				coalesce_key($params, 'method', 'post'),
				coalesce_key($params, 'enctype', ''), 
				coalesce_key($params, 'inline', false),
				coalesce_key($params, 'id_suffix', ''), 
				coalesce_key($params, 'params', array()),
				coalesce_key($params, 'extra', ''),
				coalesce_key($params, 'id', ''),
				coalesce_key($params, 'use_current_page_as_action', false)) . 
				$content . $module->create_form_end();
		}
	}
}

?>
