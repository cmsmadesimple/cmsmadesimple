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

require_once(dirname(__FILE__)."/classes/class.user.inc.php");

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

	#For testing xhtml compliance
	#generate_user_object(1);
	#return;

	global $gCms;
	$config = $gCms->config;

	if (!isset($_COOKIE["cms_admin_user_id"]))
	{
		$_SESSION["redirect_url"] = $_SERVER["REQUEST_URI"];
		redirect($config["root_url"]."/admin/login.php");
	}
	else if (!isset($_SESSION["cms_admin_user_id"]))
	{
		if (!generate_user_object($_COOKIE["cms_admin_user_id"]))
		{
			$_SESSION["redirect_url"] = $_SERVER["REQUEST_URI"];
			redirect($config["root_url"]."/admin/login.php");
		}
	}
}

/**
 * Gets the userid of the currently logged in user.
 *
 * @returns If they're logged in, the user id.  If not logged in, false.
 * @since 0.1
 */
function get_userid() {
	if (isset($_COOKIE["cms_admin_user_id"])) {
		return $_COOKIE["cms_admin_user_id"];
	}
	else
	{
		return false;
	}
}

/**
 * Regenerates the user session information from a userid.  This is basically used
 * so that if the session expires, but the cookie still remains (site is left along
 * for 20+ minutes with no interaction), the user won't have to relogin to regenerate
 * the details.
 *
 * @returns mixed If successful, true.  If it fails, false.
 * @since 0.5
 */
function generate_user_object($userid)
{
	$check = false;

	global $gCms;
	$db = $gCms->db;

	$oneuser = UserOperations::LoadUserByID($userid);

	if ($oneuser)
	{
		#$row = $result->FetchRow();

		$_SESSION["cms_admin_user_id"] = $userid;
		$_SESSION["cms_admin_username"] = $oneuser->username;
		//$_SESSION["cms_passhash"] = $row["password"]; //So we have something to check

		$check = true;
	}

	return $check;
}

/**
 * Checks to see that the given userid has access to
 * the given permission.
 *
 * @returns mixed If they have perimission, true.  If they do not, false.
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
 * @returns mixed If they have ownership, true.  If they do not, false.
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
 * @returns mixed If they have authorship, true.  If they do not, false.
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
function audit($itemid, $itemname, $action) {

	global $gCms;
	$db = $gCms->db;

	$userid = 0;
	$username = '';

	if ($_SESSION["cms_admin_user_id"])
	{
		$userid = $_SESSION["cms_admin_user_id"];
	}

	if ($_SESSION["cms_admin_username"])
	{
		$username = $_SESSION["cms_admin_username"];
	}

	if (!isset($userid) || $userid == "") {
		$userid = 0;
	}

	$query = "INSERT INTO ".cms_db_prefix()."adminlog (timestamp, user_id, username, item_id, item_name, action) VALUES (?,?,?,?,?,?)";
	$db->Execute($query,array(time(),$userid,$username,$itemid,$itemname,$action));
}

/**
 * Loads a cache of site preferences so we only have to do it once.
 *
 * @since 0.6
 */
function load_site_preferences()
{
	$value = "";

	global $gCms;
	$db = $gCms->db;
	$siteprefs = &$gCms->siteprefs;

	if ($db)
	{
		$query = "SELECT sitepref_name, sitepref_value from ".cms_db_prefix()."siteprefs";
		$result = $db->Execute($query);
		
		if ($result && $result->RowCount() > 0) {
			while ($row = $result->FetchRow())
			{
				$siteprefs[$row['sitepref_name']] = $row['sitepref_value'];
			}
		}
	}

	return $value;
}

/**
 * Gets the given site prefernce
 *
 * @since 0.6
 */
function get_site_preference($prefname) {

	$value = "";

	global $gCms;
	$siteprefs = $gCms->siteprefs;

	if (isset($siteprefs[$prefname]))
	{
		$value = $siteprefs[$prefname];
	}

	return $value;
}

/**
 * Sets the given site perference with the given value.
 *
 * @since 0.6
 */
function set_site_preference($prefname, $value) {

	$doinsert = true;

	global $gCms;
	$db = $gCms->db;

	$query = "SELECT sitepref_value from ".cms_db_prefix()."siteprefs WHERE sitepref_name = ".$db->qstr($prefname);
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0) {
		$doinsert = false;
	}

	if ($doinsert) {
		$query = "INSERT INTO ".cms_db_prefix()."siteprefs (sitepref_name, sitepref_value) VALUES (".$db->qstr($prefname).", ".$db->qstr($value).")";
		$db->Execute($query);
	} else {
		$query = "UPDATE ".cms_db_prefix()."siteprefs SET sitepref_value = ".$db->qstr($value)." WHERE sitepref_name = ".$db->qstr($prefname);
		$db->Execute($query);
	}
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

	$query = "SELECT stylesheet, encoding FROM ".cms_db_prefix()."templates WHERE template_id = ".$templateid;
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0) {
		$line = $result->FetchRow();
		$css = $line['stylesheet'];
		header("Content-Type: text/html; charset=" . (isset($line['encoding']) && $line['encoding'] != ''?$line['encoding']:get_encoding()));
	}

	# add linked CSS if any
	$cssquery = "SELECT css_text FROM ".cms_db_prefix()."css, ".cms_db_prefix()."css_assoc
		WHERE	css_id		= assoc_css_id
		AND		assoc_type	= 'template'
		AND		assoc_to_id = '$templateid'";
	$cssresult = $db->Execute($cssquery);
	if ($cssresult && $cssresult->RowCount() > 0)
	{
		while ($cssline = $cssresult->FetchRow())
		{
			$css .= "\n".$cssline['css_text']."\n";
		}
	}

	$css = preg_replace("/[\r\n]/", "", $css);

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

/*
 * creates a textarea that does syntax highlighting on the source code.
 * The following also needs to be added to the <form> tag for submit to work.
 * if($use_javasyntax){echo 'onSubmit="textarea_submit(
 * this, \'custom404,sitedown\');"';}
 */
function textarea_highlight($use_javasyntax, $text, $name,
    $class_name="syntaxHighlight", $syntax_type="HTML (Complex)", $id="", $encoding=''){
            
    if ($use_javasyntax){
        $text = ereg_replace("\r\n", "<CMSNewLine>", $text);
        $text = ereg_replace("\r", "<CMSNewLine>", $text);
        $text = cms_htmlentities(ereg_replace("\n", "<CMSNewLine>", $text));
                
        // possible values for syntaxType are: Java, C/C++, LaTeX, SQL, 
        // Java Properties, HTML (Simple), HTML (Complex)
        
        $output = '<applet name="CMSSyntaxHighlight"
            code="org.CMSMadeSimple.Syntax.Editor.class" width="100%">
                <param name="cache_option" VALUE="Plugin">
                <param name="cache_archive" VALUE="SyntaxHighlight.jar">
                <param name="cache_version" VALUE="612.0.0.0">
                <param name="content" value="'.$text.'">
                <param name="syntaxType" value="'.$syntax_type.'">
                Sorry, the syntax highlighted textarea will not work with your
                browser. Please use a different browser or turn off syntax 
                highlighting under user preferences.
            </applet>
            <input type="hidden" name="'.$name.'" value="">';
       
    }else{
        $output = '<textarea name="'.$name.'" cols="80" rows="24" 
            class="'.$class_name.'"';
        if ($id<>"")
            $output.=' id="'.$id.'"';
        $output.='>'.cms_htmlentities($text,ENT_NOQUOTES,get_encoding($encoding)).'</textarea>';
    }
    
    return $output;
}


/*
 * Checks to see if password protected (frontend)
 * @return int - page number
 */
function password_protected($page){
	global $gCms;
	$db = $gCms->db;

	$query = "SELECT password_protected,page_id FROM ".cms_db_prefix()."pages WHERE page_id = ".$page." OR page_alias = ".$page;
	$result = $db->Execute($query);

	if ($result && $result->RowCount() > 0){
		$row = $result->FetchRow();
		if ($row['password_protected'] == 1)
			return $row['page_id'];
	}
	return false;
}

/*
 * Displays the login form (frontend)
 */
function display_login_form(){
	echo '<form method=post action="'.$_SERVER['PHP_SELF'].'">'.
	'Name: <input type="text" name="login_name"><br>'.
	'Password: <input type="password" name="login_password"><br>'.
	'<input type="submit">'.
	'</form>';
}

/*
 * check if the person has access to this file (frontend)
 */
function check_access($page_id){
	global $gCms;
	$db = $gCms->db;
	
	if (isset($_SESSION['login_name']) && isset($_SESSION['login_password'])) {
		return true;
	}

	if (isset($_POST['login_password']) && isset($_POST['login_name'])){
		$login_password = trim($_POST['login_password']);
		$login_name = trim($_POST['login_name']);
		$query = 'SELECT user_id FROM '.cms_db_prefix().'frontend_users WHERE page_id = '.$page_id;
		$result = $db->Execute($query);
		if ($result && $result->RowCount() > 0){
			$query = 'SELECT user_id from '.cms_db_prefix().'users WHERE `username`=\''.$login_name.'\' AND `password`=\''.md5($login_password).'\'';
			$result = $db->Execute($query);
			if ($result && $result->RowCount() > 0) {
				$_SESSION['login_name'] = $login_name;
				$_SESSION['login_password'] = $login_password;
				return true;
			}
		}
	}
	return false;
}
# vim:ts=4 sw=4 noet
?>
