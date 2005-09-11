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
#$Id$

/**
 * Extend smarty for moduleinterface.php
 *
 * @package CMS
 */

function cms_mapi_create_permission($cms, $permission_name, $permission_text) {

	$db = $cms->db;

	$query = "SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name =" . $db->qstr($permission_name); 
	$result = $db->Execute($query);

	if ($result && $result->RowCount() < 1) {

		$new_id = $db->GenID(cms_db_prefix()."permissions_seq");
		$query = "INSERT INTO ".cms_db_prefix()."permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES ($new_id, ".$db->qstr($permission_name).",".$db->qstr($permission_text).",'".$db->DBTimeStamp(time())."','".$db->DBTimeStamp(time())."')";
		$db->Execute($query);
	}
}

# vim:ts=4 sw=4 noet
?>
