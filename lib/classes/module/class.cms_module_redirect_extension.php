<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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

class CmsModuleRedirectExtension extends CmsModuleExtension
{
	function __construct($module)
	{
		parent::__construct($module);
	}
	
	public function module_url($params = array(), $check_keys = false)
	{
		$default_params = array(
			'action' => coalesce_key($params, 'action', 'default', FILTER_SANITIZE_URL),
			'inline' => coalesce_key($params, 'inline', false, FILTER_VALIDATE_BOOLEAN),
			'params' => coalesce_key($params, 'params', array()),
			'id' => coalesce_key($params, 'id', $this->id),
			'return_id' => coalesce_key($params, 'return_id', $this->return_id)
		);
		
		if ($check_keys && !are_all_keys_valid($params, $default_params))
			throw new CmsInvalidKeyException(invalid_key($params, $default_params));

		//Combine EVERYTHING together into a big managerie
		//Merge in anything if it was passed in the params key to the method
		$extra_params = strip_extra_params($params, $default_params, 'params');
		
		if (!$params['inline'] && $params['return_id'] != '')
			$params['id'] = 'cntnt01';
		
		$href = ($params['return_id'] != '' ? 'index.php' : 'moduleinterface.php');
		$href .= '?mact=' . implode(',', array($this->module->get_name(), $params['id'], $params['action'], ($inline == true?1:0)));
		$href .= ($returnid != '' ? '&'.$params['id'].'returnid='.$params['return_id'] : '&'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY]);
		
		foreach ($extra_params as $key => $value)
		{
			$href .= '&'.$params['id'].$key.'='.rawurlencode($value);
		}
		
		CmsResponse::redirect($href);
	}
}

# vim:ts=4 sw=4 noet
?>