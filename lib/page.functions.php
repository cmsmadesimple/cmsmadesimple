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

/**
 * Page related functions.  Generally these are functions not necessarily
 * related to content, but more to the underlying mechanisms of the system.
 *
 * @package CMS
 */
/**
 * Checks to see if the user is logged in.   If not, redirects the browser
 * to the admin login.
 *
 * @since 0.1
 */
function check_login() {

	global $gCms;
	$config = $gCms->config;

	if (!isset($_COOKIE["cms_admin_user_id"])) {
		redirect($config["root_url"]."/admin/login.php");
	}
}

/**
 * Gets the userid of the currently logged in user.
 *
 * @since 0.1
 */
function get_userid() {
	if (isset($_COOKIE["cms_admin_user_id"])) {
		return $_COOKIE["cms_admin_user_id"];
	}
}

/**
 * Checks to see that the given userid has access to
 * the given permission.
 *
 * @since 0.1
 */
function check_permission($userid, $permname) {

	$check = false;

	global $gCms;
	$db = $gCms->db;

	$query = "SELECT * FROM ".cms_db_prefix()."user_groups ug INNER JOIN ".cms_db_prefix()."group_perms gp ON gp.group_id = ug.group_id INNER JOIN ".cms_db_prefix()."permissions p ON p.permission_id = gp.permission_id WHERE ug.user_id = ".$userid." AND permission_name = ".$db->qstr($permname);
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0) {
		$check = true;
	}

	return $check;
}

/**
 * Checks that the given userid is the owner of the given pageid.
 *
 * @since 0.1
 */
function check_ownership($userid, $pagename, $pageid = "") {

	$check = false;

	global $gCms;
	$db = $gCms->db;

	$query = "SELECT * FROM ".cms_db_prefix()."pages WHERE owner = ".$userid." AND page_id = ".$pageid;
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0) {
		$check = true;
	}

	return $check;
}

/**
 * Checks that the given userid has access to modify the given
 * pageid.  This would mean that they were set as additional
 * authors/editors by the owner.
 *
 * @since 0.2
 */
function check_authorship($userid, $pageid) {

	$check = false;

	global $gCms;
	$db = $gCms->db;

	$query = "SELECT * FROM ".cms_db_prefix()."additional_users WHERE page_id = $pageid AND user_id = $userid";
	$result = $db->Execute($query);
	if ($result && $result->RowCount() > 0) {
		$check = true;
	}

	return $check;
}

/**
 * Put an event into the audit (admin) log.  This should be
 * done on most admin events for consistency.
 *
 * @since 0.3
 */
function audit($userid, $username, $itemid, $itemname, $action) {

	global $gCms;
	$db = $gCms->db;

	if (!isset($userid) || $userid == "") {
		$userid = 0;
	}
	$query = "INSERT INTO ".cms_db_prefix()."adminlog (timestamp, user_id, username, item_id, item_name, action) VALUES (".time().", $userid, ".$db->qstr($username).", $itemid, ".$db->qstr($itemname).", ".$db->qstr($action).")";
	$db->Execute($query);
}

/**
 * Gets the given preference for the given userid.
 *
 * @since 0.3
 */
function get_preference($userid, $prefname) {

	$value = "";

	global $gCms;
	$db = $gCms->db;

	$query = "SELECT value from ".cms_db_prefix()."userprefs WHERE user_id = $userid AND preference = ".$db->qstr($prefname);
	$result = $db->query($query);
	
	if ($result && $result->RowCount() > 0) {
		$row = $result->FetchRow();
		$value = $row["value"];
	}

	return $value;
}

/**
 * Sets the given perference for the given userid with the given value.
 *
 * @since 0.3
 */
function set_preference($userid, $prefname, $value) {

	$doinsert = true;

	global $gCms;
	$db = $gCms->db;

	$query = "SELECT value from ".cms_db_prefix()."userprefs WHERE user_id = $userid AND preference = ".$db->qstr($prefname);
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0) {
		$doinsert = false;
	}

	if ($doinsert) {
		$query = "INSERT INTO ".cms_db_prefix()."userprefs (user_id, preference, value) VALUES ($userid, ".$db->qstr($prefname).", ".$db->qstr($value).")";
		$db->Execute($query);
	} else {
		$query = "UPDATE ".cms_db_prefix()."userprefs SET value = ".$db->qstr($value)." WHERE user_id = $userid AND preference = ".$db->qstr($prefname);
		$db->Execute($query);
	}
}

/**
 * Returns the stylesheet for the given templateid.
 *
 * @since 0.1
 */
function get_stylesheet($templateid) {

	$css = "";

	global $gCms;
	$db = $gCms->db;

	$query = "SELECT stylesheet FROM ".cms_db_prefix()."templates WHERE template_id = ".$templateid;
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0) {
		$line = $result->FetchRow();
		$css = $line[stylesheet];
		$css = preg_replace("/[\r\n]/", "", $css);
	}

	return $css;
}

/**
 * Strips slashes from an array of values.
 *
 * @since 0.1
 */
function & strip_slashes(&$str) {

	if(is_array($str)) {
		while(list($key, $val) = each($str)) {
			$str[$key] = strip_slashes($val);
		}
	}
	else {
		$str = stripslashes($str);
	}

	return $str;
}

# vim:ts=4 sw=4 noet
?>
