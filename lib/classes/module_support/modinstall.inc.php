<?php
# CMS - CMS Made Simple
# (c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA	02111-1307	USA
#
#$Id$

/**
 * Methods for modules to do install related functions
 *
 * @since		1.0
 * @package		CMS
 */

	
/**
 * Function that will get called as module is installed. This function should
 * do any initialization functions including creating database tables. It
 * should return a string message if there is a failure. Returning nothing (FALSE)
 * will allow the install procedure to proceed.
 */
function cms_module_Install(&$modinstance)
{
	$filename = dirname(dirname(dirname(__FILE__))) . '/modules/'.$modinstance->GetName().'/method.install.php';
	if (@is_file($filename))
	{
		{
			global $gCms;
			$db =& $gCms->GetDb();
			$config =& $gCms->GetConfig();
			$smarty =& $gCms->GetSmarty();

			include($filename);
		}
	}
	else
	{
		return FALSE;
	}
}

/**
 * Function that will get called as module is uninstalled. This function should
 * remove any database tables that it uses and perform any other cleanup duties.
 * It should return a string message if there is a failure. Returning nothing
 * (FALSE) will allow the uninstall procedure to proceed.
 */
function cms_module_Uninstall(&$modinstance)
{
	$filename = dirname(dirname(dirname(__FILE__))) . '/modules/'.$modinstance->GetName().'/method.uninstall.php';
	if (@is_file($filename))
	{
		{
			global $gCms;
			$db =& $gCms->GetDb();
			$config =& $gCms->GetConfig();
			$smarty =& $gCms->GetSmarty();

			include($filename);
		}
	}
	else
	{
		return FALSE;
	}
}

/**
 * Function to perform any upgrade procedures. This is mostly used to for
 * updating databsae tables, but can do other duties as well. It should
 * return a string message if there is a failure. Returning nothing (FALSE)
 * will allow the upgrade procedure to proceed. Upgrades should have a path
 * so that they can be upgraded from more than one version back.  While not
 * a requirement, it makes life easy for your users.
 *
 * @param string The version we are upgrading from
 * @param string The version we are upgrading to
 */
function cms_module_Upgrade(&$modinstance, $oldversion, $newversion)
{
	$filename = dirname(dirname(dirname(__FILE__))) . '/modules/'.$modinstance->GetName().'/method.upgrade.php';
	if (@is_file($filename))
	{
		{
			global $gCms;
			$db =& $gCms->GetDb();
			$config =& $gCms->GetConfig();
			$smarty =& $gCms->GetSmarty();

			include($filename);
		}
	}
}

?>