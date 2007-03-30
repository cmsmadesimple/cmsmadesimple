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

global $gCms;

$LOAD_ALL_MODULES = true;
$DONT_LOAD_DB = false;

set_magic_quotes_runtime(false);

define('CMS_INSTALL_BASE', dirname(__FILE__));
define('CMS_BASE', dirname(CMS_INSTALL_BASE));
require_once CMS_BASE . DIRECTORY_SEPARATOR . 'fileloc.php';
require_once CMS_BASE . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'misc.functions.php';
require_once cms_join_path(CMS_INSTALL_BASE, 'lib', 'classes', 'CMSInstaller.class.php');

$installer =& new CMSInstaller();

if ($installer->currentPage > 3)
{
	require_once cms_join_path(CMS_BASE, 'include.php');
}

$installer->run();

?>