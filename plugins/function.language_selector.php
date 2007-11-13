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

function smarty_cms_function_language_selector($params, &$smarty)
{
	$lang_list = CmsLanguage::get_language_list();
	$enabled_languages = CmsMultiLanguage::get_enabled_languages();
	$default_language = CmsMultiLanguage::get_default_language();
	
	$output = array();

	$content = cmsms()->content_base->find_by_id(cmsms()->variables['pageinfo']->content_id);
	
	foreach ($lang_list as $code=>$lang_name)
	{
		if(!in_array($code, $enabled_languages))
		{
			continue;
		}
		$output[] = '<a href="' . $content->get_url(true, $code) . '"><img src="' . CmsLanguage::get_flag_image($code) . '" alt="' . $lang_name . '" /></a>';
	}
	
	return implode('&nbsp;', $output);
}
