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
#
#$Id: supportinfo.php 4216 2007-10-06 19:28:55Z wishy $

$CMS_ADMIN_PAGE=1;

require_once("../include.php");
include_once("header.php");

define('CMS_BASE', dirname(dirname(__FILE__)));
require_once cms_join_path(CMS_BASE, 'lib', 'test.functions.php');

function checksum_lang($params,&$smarty)
{
  if( isset($params['key']) )
    {
      return lang($params['key']);
    }
}

// check for login
check_login();

// Get ready
global $gCms;
$smarty =& $gCms->GetSmarty();
$smarty->register_function('lang','checksum_lang');
$smarty->caching = false;
$smarty->force_compile = true;
$db = &$gCms->GetDb();


// Display the output
echo $smarty->fetch('checksum.tpl');
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
