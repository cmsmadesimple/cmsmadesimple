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
#$Id: editcss.php 4631 2008-06-14 18:03:26Z nuno $

/**
 * This page is both the interface of the CSS editing, and used for actually
 * updating the CSS in the DB. The first part checks that all parameters are
 * valids, and then insert into the DB and redirect.
 *
 * The second part show the form to edit the CSS
 *
 * It takes one argument when called externally :
 * - $css_id : the id of the css to edit
 *
 * @since	0.6
 * @author	calexico
 */


$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();

global $gCms;

function GetCoreDashboardOutput() {
	echo "This is core-output relevant for dashboard";
}
?>