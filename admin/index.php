<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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

$CMS_ADMIN_PAGE=1;
$CMS_TOP_MENU='main';
$CMS_ADMIN_TITLE='adminhome';
$CMS_ADMIN_TITLE='mainmenu';
$CMS_EXCLUDE_FROM_RECENT=1;

require_once("../include.php");

// if this page was accessed directly, and the secure param name is not in the URL
// but it is in the session, assume it is correct.
if( isset($_SESSION[CMS_USER_KEY]) && !isset($_GET[CMS_SECURE_PARAM_NAME]) ) {
  $_GET[CMS_SECURE_PARAM_NAME] = $_SESSION[CMS_USER_KEY];
}

check_login();

include_once("header.php");
$section = (isset($_GET['section'])) ? trim($_GET['section']) : '';
$themeObject->do_toppage($section);
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
