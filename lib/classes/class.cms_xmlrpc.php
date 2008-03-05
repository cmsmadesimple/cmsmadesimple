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

require_once cms_join_path(ROOT_DIR,'lib','xmlrpc','xmlrpc.inc');
require_once cms_join_path(ROOT_DIR,'lib','xmlrpc','xmlrpcs.inc');

class CmsXmlrpc extends CmsObject
{
	static private $server = null;

	function __construct()
	{
		parent::__construct();
	}
	
	function create_server()
	{
		self::$server = new xmlrpc_server(null, false);
		self::$server->functions_parameters_type = 'phpvals';
	}
	
	function handle_requests()
	{
		if (self::$server == null)
			self::create_server();
		
		self::$server->service();
	}
	
	function add_method($name, $callback, $namespace = '')
	{
		if (self::$server == null)
			self::create_server();
			
		if ($namespace != '')
			$name = $namespace . "." . $name;

		self::$server->add_to_map($name, $callback);
	}
}

# vim:ts=4 sw=4 noet
?>