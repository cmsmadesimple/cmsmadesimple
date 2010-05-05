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

/**
 * Class to handle url routes for modules to handle pretty urls.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsRoute extends CmsObject
{
	var $module;
	var $regex;
	var $defaults;

	function __construct()
	{
		parent::__construct();
	}
	
	public static function match_route($page)
	{	
		if (strpos($page, '/') !== FALSE)
		{
			$routes =& cmsms()->variables['routes'];
			
			$response = CmsResponse::get_instance();

			$matched = false;
			foreach ($routes as $route)
			{
				$matches = array();
				if (preg_match($route->regex, $page, $matches))
				{
					//Now setup some assumptions
					if (!isset($matches['id']))
						$matches['id'] = 'cntnt01';
					if (!isset($matches['action']))
						$matches['action'] = 'defaulturl';
					if (!isset($matches['inline']))
						$matches['inline'] = 0;
					if (!isset($matches['returnid']))
						$matches['returnid'] = ''; #Look for default page
					if (!isset($matches['module']))
						$matches['module'] = $route->module;

					//Get rid of numeric matches
					foreach ($matches as $key=>$val)
					{
						if (is_int($key))
						{
							unset($matches[$key]);
						}
						else
						{
							if ($key != 'id')
								$response->_request[$matches['id'] . $key] = $_REQUEST[$matches['id'] . $key] = $val;
						}
					}

					//Now set any defaults that might not have been in the url
					if (isset($route->defaults) && count($route->defaults) > 0)
					{
						foreach ($route->defaults as $key=>$val)
						{
							$response->_request[$matches['id'] . $key] = $_REQUEST[$matches['id'] . $key] = $val;
						}
					}

					//Get a decent returnid
					if ($matches['returnid'] == '')
					{
						$matches['returnid'] = CmsContentOperations::get_default_page_id();
					}

					$response->_request['mact'] = $_REQUEST['mact'] = $matches['module'] . ',' . $matches['id'] . ',' . $matches['action'] . ',' . $matches['inline'];
					$response->_request[$matches['id'] . 'returnid'] = $_REQUEST[$matches['id'] . 'returnid'] = $matches['returnid'];
					$page = $matches['returnid'];
					$smarty->id = $matches['id'];

					$matched = true;
				}
			}

			/*
			if (!$matched)
			{
				$page = substr($page, strrpos($page, '/') + 1);
			}
			*/
		}

		return $page;
	}
}

# vim:ts=4 sw=4 noet
?>