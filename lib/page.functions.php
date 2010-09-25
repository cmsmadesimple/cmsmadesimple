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
 * @param string no_redirect - If true, then don't redirect if not logged in
 * @return boolean
 */
function check_login($no_redirect = false)
{
	global $gCms;
	$config = $gCms->GetConfig();

	//Handle a current login if one is in queue in the SESSION
	if (isset($_SESSION['login_user_id']))
	{
		debug_buffer("Found login_user_id.  Going to generate the user object.");
		generate_user_object($_SESSION['login_user_id']);
		unset($_SESSION['login_user_id']);
	}

	if (isset($_SESSION['login_cms_language']))
	{
		debug_buffer('Setting language to: ' . $_SESSION['login_cms_language']);
		setcookie('cms_language', $_SESSION['login_cms_language']);
		unset($_SESSION['login_cms_language']);
	}

	if (!isset($_SESSION["cms_admin_user_id"]))
	{
		debug_buffer('No session found.  Now check for cookies');
		if (isset($_COOKIE["cms_admin_user_id"]) && isset($_COOKIE["cms_passhash"]))
		{
			debug_buffer('Cookies found, do a passhash check');
			if (check_passhash($_COOKIE["cms_admin_user_id"], $_COOKIE["cms_passhash"]))
			{
				debug_buffer('passhash check succeeded...  creating session object');
				generate_user_object($_COOKIE["cms_admin_user_id"]);
			}
			else
			{
				debug_buffer('passhash check failed...  redirect to login');
				$_SESSION["redirect_url"] = $_SERVER["REQUEST_URI"];
				if (false == $no_redirect)
				  {
				    redirect($config["root_url"]."/".$config['admin_dir']."/login.php");
				  }
				return false;
			}
		}
		else
		{
			debug_buffer('No cookies found.  Redirect to login.');
			$_SESSION["redirect_url"] = $_SERVER["REQUEST_URI"];
			if (false == $no_redirect)
			  {
			    redirect($config["root_url"]."/".$config['admin_dir']."/login.php");
			  }
			return false;
		}
	}

	debug_buffer('Session found.  Moving on...');

	global $CMS_ADMIN_PAGE;
	if( ($config['debug'] === false) && isset($CMS_ADMIN_PAGE) )
	  {
	    if( !isset($_SESSION[CMS_USER_KEY]) )
	      {
		// it's not in the session, try to grab something from cookies
		if( isset($_COOKIE[CMS_SECURE_PARAM_NAME]) )
		  {
		    $_SESSION[CMS_USER_KEY] = $_COOKIE[CMS_SECURE_PARAM_NAME];
		  }
	      }

	    // now we've got to check the request
	    // and make sure it matches the session key
	    if( !isset($_SESSION[CMS_USER_KEY]) || 
		!isset($_GET[CMS_SECURE_PARAM_NAME]) ||
                !isset($_POST[CMS_SECURE_PARAM_NAME]) )
	      {
		$v = '<no$!tgonna!$happen>';
		if( isset($_GET[CMS_SECURE_PARAM_NAME]) )
		  {
		    $v = $_GET[CMS_SECURE_PARAM_NAME];
		  }
		else if( isset($_POST[CMS_SECURE_PARAM_NAME]) )
		  {
		    $v = $_POST[CMS_SECURE_PARAM_NAME];
		  }

		if( $v != $_SESSION[CMS_USER_KEY] && !isset($config['stupidly_ignore_xss_vulnerability']) )
		  {
		    debug_buffer('Session key mismatch problem... redirect to login');
		    if (false == $no_redirect)
		      {
			redirect($config["root_url"]."/".$config['admin_dir']."/login.php");
		      }
		    return false;
		  }
	      }
	  }
	return true;
}


/**
 * Gets the userid of the currently logged in user.
 *
 * @since 0.1
 * @param  boolean Redirect to the admin login page if the user is not logged in.
 * @return integer The UID of the logged in administrator, otherwise FALSE
 */
function get_userid($check = true)
{
	if ($check)
	{
		check_login(); //It'll redirect out to login if it fails
	}

	if (isset($_SESSION["cms_admin_user_id"]))
	{
		return $_SESSION["cms_admin_user_id"];
	}
	else
	{
		return false;
	}
}

/**
 * A function to check if the checksum provided can be used to validate the user to this site
 *
 * @internal
 * @access private
 * @param int The admin userid.
 * @param string The checksum variable
 * @return boolean
 */
function check_passhash($userid, $checksum)
{
	$check = false;

	global $gCms;
	$db =& $gCms->GetDb();
	$config = $gCms->GetConfig();

	$userops =& $gCms->GetUserOperations();
	$oneuser =& $userops->LoadUserByID($userid);

	if ($oneuser && (string)$checksum != '' && $checksum == md5(md5($config['root_path'] . '--' . $oneuser->password)))
	{
		$check = true;
	}

	return $check;
}

/**
 * Regenerates the user session information from a userid.  This is basically used
 * so that if the session expires, but the cookie still remains (site is left along
 * for 20+ minutes with no interaction), the user won't have to relogin to regenerate
 * the details.
 *
 * @internal
 * @access private
 * @since 0.5
 * @param integer The admin user id
 * @return void
 */
function generate_user_object($userid)
{
	global $gCms;
	$db =& $gCms->GetDb();
	$config = $gCms->GetConfig();

	$userops =& $gCms->GetUserOperations();
	$oneuser =& $userops->LoadUserByID($userid);

	if ($oneuser)
	{
		$_SESSION['cms_admin_user_id'] = $userid;
		$_SESSION['cms_admin_username'] = $oneuser->username;
		setcookie('cms_admin_user_id', $oneuser->id);
		setcookie('cms_passhash', md5(md5($config['root_path'] . '--' . $oneuser->password)));
		//setcookie(CMS_SECURE_PARAM_NAME, '', time() - 3600);
	}
}


/**
 * A function to send lost password recovery email to a specified admin user (by name)
 *
 * @internal
 * @access private
 * @param string the username
 * @return results from the attempt to send a message.
 */
function send_recovery_email($username)
{
	global $gCms;
	$config = $gCms->GetConfig();
	$userops =& $gCms->GetUserOperations();
	$user = $userops->LoadUserByUsername($username);
	
	//Grab CMSMailer -- *cough* hack
	$obj = null;
	if (isset($gCms->modules['CMSMailer']) &&
		$gCms->modules['CMSMailer']['installed'] == true &&
		$gCms->modules['CMSMailer']['active'] == true)
	{
		$obj = $gCms->modules['CMSMailer']['object'];
	}
	
	if ($obj == null)
	{
		return false;
	}
	
	$obj->AddAddress($user->email, $firstname . ' ' . $lastname);
	$obj->SetSubject(lang('lostpwemailsubject',$gCms->siteprefs['sitename']));
	
	$url = $config['root_url'] . '/' . $config['admin_dir'] . '/login.php?recoverme=' . md5(md5($config['root_path'] . '--' . $user->username . md5($user->password)));
	$body = lang('lostpwemail',$gCms->siteprefs['sitename'], $user->username, $url);
	
	$obj->SetBody($body);
	
	return $obj->Send();
}

/**
 * A function find a matching user id given an identity hash
 *
 * @internal
 * @access private
 * @param string the hash
 * @return object The matching user object if found, or null otherwise.
 */
function find_recovery_user($hash)
{
	global $gCms;
	$config = $gCms->GetConfig();
	$userops =& $gCms->GetUserOperations();
	
	foreach ($userops->LoadUsers() as $user)
	{
		if ($hash == md5(md5($config['root_path'] . '--' . $user->username . md5($user->password))))
		{
			return $user;
		}
	}
	
	return null;
}

/**
 * Loads all permissions for a particular user into a global variable so we don't hit the db for every one.
 *
 * @internal
 * @access private
 * @since 0.8
 * @param int The user id
 * @return void
 */
function load_all_permissions($userid)
{
	global $gCms;
	$db = &$gCms->GetDb();
	$variables = &$gCms->variables;

	$perms = array();

	$query = "SELECT DISTINCT permission_name FROM ".cms_db_prefix()."user_groups ug INNER JOIN ".cms_db_prefix()."group_perms gp ON gp.group_id = ug.group_id INNER JOIN ".cms_db_prefix()."permissions p ON p.permission_id = gp.permission_id INNER JOIN ".cms_db_prefix()."groups gr ON gr.group_id = ug.group_id WHERE ug.user_id = ? AND gr.active = 1";
	$result = &$db->Execute($query, array($userid));
	while ($result && !$result->EOF)
	{
		$perms[] =& $result->fields['permission_name'];
		$result->MoveNext();
	}
	
	if ($result) $result->Close();

	$variables['userperms'] = $perms;
}

/**
 * Checks to see that the given userid has access to the given permission.
 * Members of the admin group have all permissions.
 *
 * @since 0.1
 * @param int The user id
 * @param string The permission name
 * @return boolean
 */
function check_permission($userid, $permname)
{
	$check = false;

	global $gCms;
	$userops =& $gCms->GetUserOperations();
	$adminuser = $userops->UserInGroup($userid,1);

	if (!isset($gCms->variables['userperms']))
	{
		load_all_permissions($userid);
	}

	if (isset($gCms->variables['userperms']))
	{
		if (in_array($permname, $gCms->variables['userperms']) || 
		    $adminuser || ($userid == 1) )
		{
			$check = true;
		}
	}

	return $check;
}


/**
 * Checks that the given userid is the owner of the given contentid.
 * (members of the admin group have all permission)
 *
 * @internal
 * @since 0.1
 * @param   integer  The User ID
 * @param   integer  The content id
 * @param   boolean  use strict checking (ignored)
 * @return  boolean 
 */
function check_ownership($userid, $contentid = '', $strict = false)
{
	$check = false;
	global $gCms;

	$userops =& $gCms->GetUserOperations();
	$adminuser = $userops->UserInGroup($userid,1);
	if( $adminuser ) return true;

	if (!isset($gCms->variables['ownerpages']))
	{
		$db =& $gCms->GetDb();

		$variables = &$gCms->variables;
		$variables['ownerpages'] = array();

		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE owner_id = ?";
		$result = &$db->Execute($query, array($userid));

		while ($result && !$result->EOF)
		{
			$variables['ownerpages'][] =& $result->fields['content_id'];
			$result->MoveNext();
		}
		
		if ($result) $result->Close();
	}

	if (isset($gCms->variables['ownerpages']))
	{
		if (in_array($contentid, $gCms->variables['ownerpages']))
		{
			$check = true;
		}
	}

	return $check;
}

/**
 * Checks that the given userid has access to modify the given
 * pageid.  This would mean that they were set as additional
 * authors/editors by the owner.
 *
 * @internal
 * @since 0.2
 * @param  integer The admin user id
 * @param  integer A valid content id.
 * @return boolean
 */
function check_authorship($userid, $contentid = '')
{
	$check = false;
	global $gCms;

	if (!isset($gCms->variables['authorpages']))
	{
	  author_pages($userid);
	}

	if (isset($gCms->variables['authorpages']))
	{
	  if (in_array($contentid, $gCms->variables['authorpages']))
	    {
	      $check = true;
	    }
	}

	return $check;
}

/**
 * Prepares an array with the list of the pages $userid is an author of
 *
 * @internal
 * @since 0.11
 * @param  integer The user id.
 * @return array   An array of pages this user is an author of.
 */
function author_pages($userid)
{
	global $gCms;
	$db =& $gCms->GetDb();
	$userops =& $gCms->GetUserOperations();
        $variables = &$gCms->variables;
	if (!isset($variables['authorpages']))
	{
		$db = &$gCms->GetDb();
		$variables['authorpages'] = array();
		
		// Get all of the pages this user owns
		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE owner_id = ?";
		$result =& $db->Execute($query, array($userid));
		
		while ($result && !$result->EOF)
		{
			$variables['authorpages'][] =& $result->fields['content_id'];
			$result->MoveNext();
		}
		
		if ($result) $result->Close();

		// Get all of the pages this user has access to.
		$query = "SELECT user_id,content_id FROM ".cms_db_prefix()."additional_users";
		$result = &$db->Execute($query);

		while ($result && !$result->EOF)
		{
		  $uid = $result->fields['user_id'];
		  $content_id = $result->fields['content_id'];
		  if( $uid == $userid )
		    {
		      $variables['authorpages'][] = $content_id;
		    }
		  else if( $uid < 0 )
		    {
		      $gid = $uid * -1;
		      if( $userops->UserInGroup($userid,$gid) )
			{
			  $variables['authorpages'][] = $content_id;
			}
		    }
		  $result->MoveNext();
		}
		
		if ($result) $result->Close();
	}

	return $variables['authorpages'];
}

/**
 * Quickly checks that the given userid has access to modify the given
 * pageid.  This would mean that they were set as additional
 * authors/editors by the owner.
 *
 * @since 0.11
 * @internal
 * @param   integer The content id to test with
 * @param   array   A list of the authors pages.
 * @return  boolean
 */
function quick_check_authorship($contentid, $hispages)
{
	$check = false;

	if (in_array($contentid, $hispages))
	{
		$check = true;
	}

	return $check;
}


/**
 * Put an event into the audit (admin) log.  This should be
 * done on most admin events for consistency.
 *
 * @since 0.3
 * @param integer The item id (perhaps a content id, or a record id from a module)
 * @param string  The item name (perhaps Content, or the module name)
 * @param string  The action that needs to be audited
 * @return void
 */
function audit($itemid, $itemname, $action)
{
	global $gCms;
	$db =& $gCms->GetDb();

	$userid = 0;
	$username = '';

	if (isset($_SESSION["cms_admin_user_id"]))
	{
		$userid = $_SESSION["cms_admin_user_id"];
	}
	else
	{
	    if (isset($_SESSION['login_user_id']))
	    {
		$userid = $_SESSION['login_user_id'];
		$username = $_SESSION['login_user_username'];
	    }
	}

	if (isset($_SESSION["cms_admin_username"]))
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
 * @internal
 * @return void
 */
function load_site_preferences()
{
	global $gCms;
	$db = &$gCms->GetDb();
	$siteprefs = &$gCms->siteprefs;

	if ($db)
	{
		$query = "SELECT sitepref_name, sitepref_value from ".cms_db_prefix()."siteprefs";
		$result = &$db->Execute($query);

		while ($result && !$result->EOF)
		{
			$siteprefs[$result->fields['sitepref_name']] = $result->fields['sitepref_value'];
			$result->MoveNext();
		}
		
		if ($result) $result->Close();
	}
}


/**
 * Gets the given site prefernce
 *
 * @since 0.6
 * @param string The preference name
 * @param mixed  The default value if the preference does not exist
 * @return mixed
 */
function get_site_preference($prefname, $defaultvalue = '') {

	$value = $defaultvalue;

	global $gCms;
	$siteprefs =& $gCms->siteprefs;
	
	if (count($siteprefs) == 0)
	{
		load_site_preferences();
	}

	if (isset($siteprefs[$prefname]))
	{
		$value = $siteprefs[$prefname];
	}

	return $value;
}


/**
 * Removes the given site preference
 *
 * @param string Preference name to remove
 * @param boolean Wether or not to remove all preferences that are LIKE the supplied name
 * @return void
 */
function remove_site_preference($prefname,$uselike=false)
{
	global $gCms;
	$db =& $gCms->GetDb();
$db->debug = true;
	$siteprefs = &$gCms->siteprefs;

	$query = "DELETE from ".cms_db_prefix()."siteprefs WHERE sitepref_name = ?";
	if( $uselike == true )
	  {
	    $query = "DELETE from ".cms_db_prefix()."siteprefs WHERE sitepref_name LIKE ?";
		$prefname .= '%';
	  }
	$result = $db->Execute($query, array($prefname));

	if (isset($siteprefs[$prefname]))
	{
		unset($siteprefs[$prefname]);
	}
	
	if ($result) $result->Close();
}


/**
 * Sets the given site perference with the given value.
 *
 * @since 0.6
 * @param string The preference name
 * @param mixed  The preference value (will be stored as a string)
 * @return void
 */
function set_site_preference($prefname, $value)
{
	$doinsert = true;

	global $gCms;
	$db =& $gCms->GetDb();

	$siteprefs = &$gCms->siteprefs;

	$query = "SELECT sitepref_value from ".cms_db_prefix()."siteprefs WHERE sitepref_name = ".$db->qstr($prefname);
	$result = $db->Execute($query);

	if ($result && $result->RecordCount() > 0)
	{
		$doinsert = false;
	}
	
	if ($result) $result->Close();

	if ($doinsert)
	{
		$query = "INSERT INTO ".cms_db_prefix()."siteprefs (sitepref_name, sitepref_value) VALUES (".$db->qstr($prefname).", ".$db->qstr($value).")";
		$db->Execute($query);
	}
	else
	{
		$query = "UPDATE ".cms_db_prefix()."siteprefs SET sitepref_value = ".$db->qstr($value)." WHERE sitepref_name = ".$db->qstr($prefname);
		$db->Execute($query);
	}
	$siteprefs[$prefname] = $value;
}


/**
 * A function to load all user preferences for the specified user ID
 *
 * @internal
 * @access private
 * @param integer The User ID
 * @return void
 */
function load_all_preferences($userid)
{
	global $gCms;
	$db = &$gCms->GetDb();
	$variables = &$gCms->userprefs;

	$query = 'SELECT preference, value FROM '.cms_db_prefix().'userprefs WHERE user_id = ?';
	$result = &$db->Execute($query, array($userid));

	while ($result && !$result->EOF)
	{
		$variables[$result->fields['preference']] = $result->fields['value'];
		$result->MoveNext();
	}
	
	if ($result) $result->Close();
}

/**
 * Gets the given preference for the given userid.
 *
 * @since 0.3
 * @param integer The user id
 * @param string  The preference name
 * @param mixed   The default value if the preference is not set for the given user id.
 * @return mixed.
 */
function get_preference($userid, $prefname, $default='')
{
	global $gCms;
	$db =& $gCms->GetDb();

	$result = '';

	if (!isset($gCms->userprefs))
	{
		load_all_preferences($userid);
	}

	$userprefs = &$gCms->userprefs;
	if (isset($gCms->userprefs))
	{
		if (isset($userprefs[$prefname]))
		{
			$result = $userprefs[$prefname];
		}
		else
		{
			$result = $default;
		}
	}

	return $result;
}

/**
 * Sets the given perference for the given userid with the given value.
 *
 * @since 0.3
 * @param integer The user id
 * @param string  The preference name
 * @param mixed   The preference value (will be stored as a string)
 * @return void
 */
function set_preference($userid, $prefname, $value)
{
	$doinsert = true;

	global $gCms;
	$db =& $gCms->GetDb();

	if (!isset($gCms->userprefs))
	{
		load_all_preferences($userid);
	}


	$userprefs = &$gCms->userprefs;
	$userprefs[$prefname] = $value;

	$query = "SELECT value from ".cms_db_prefix()."userprefs WHERE user_id = ? AND preference = ?";
	$result = $db->Execute($query, array($userid, $prefname));

	if ($result && $result->RecordCount() > 0)
	{
		$doinsert = false;
	}
	
	if ($result) $result->Close();

	if ($doinsert)
	{
		$query = "INSERT INTO ".cms_db_prefix()."userprefs (user_id, preference, value) VALUES (?,?,?)";
		$db->Execute($query, array($userid, $prefname, $value));
	}
	else
	{
		$query = "UPDATE ".cms_db_prefix()."userprefs SET value = ? WHERE user_id = ? AND preference = ?";
		$db->Execute($query, array($value, $userid, $prefname));
	}
}


/**
 * Returns the stylesheet for the given templateid.  Returns a hash with encoding and stylesheet entries.
 *
 * @since 0.1
 * @internal
 * @deprecated
 * @param integer template id
 * @param string  An optional media type
 * @return string
 */
function get_stylesheet($template_id, $media_type = '')
{
	$result = array();
	$css = "";

	global $gCms;
	$db =& $gCms->GetDb();
	$templateops =& $gCms->GetTemplateOperations();

	$templateobj = FALSE;

	#Grab template id and make sure it's actually "somewhat" valid
	if (isset($template_id) && is_numeric($template_id) && $template_id > -1)
	{
		#Ok, it's valid, let's load the bugger
		$templateobj =& $templateops->LoadTemplateById($template_id);
	}

	#If it's valid after loading, then start the process...
	if ($templateobj !== FALSE && ($templateobj->active == '1' || $templateobj->active == TRUE) )
	{
		#Grab the encoding
		if ($templateobj->encoding !== FALSE && $templateobj->encoding != '')
		{
			$result['encoding'] = $templateobj->encoding;
		}
		else
		{
			$result['encoding'] = get_encoding();
		}

		#Load in the "standard" template CSS if media type is empty
		if ($media_type == '')
		{
			if (isset($templateobj->stylesheet) && $templateobj->stylesheet != '')
			{
				$css .= $templateobj->stylesheet;
			}
		}

		#Handle "advanced" CSS Management
		$cssquery = "SELECT css_text FROM ".cms_db_prefix()."css c, ".cms_db_prefix()."css_assoc ca
			WHERE	css_id		= assoc_css_id
			AND		assoc_type	= 'template'
			AND		assoc_to_id = ?
			AND		c.media_type = ? ORDER BY ca.create_date";
		$cssresult =& $db->Execute($cssquery, array($template_id, $media_type));

		while ($cssresult && $cssline = $cssresult->FetchRow())
		{
			$css .= "\n".$cssline['css_text']."\n";
		}
		
		if ($cssresult) $cssresult->Close();
	}
	else
	{
		$result['nostylesheet'] = true;
		$result['encoding'] = get_encoding();
	}

	#$css = preg_replace("/[\r\n]/", "", $css); //hack for tinymce
	$result['stylesheet'] = $css;

	return $result;
}


/**
 * Return a list of the media types supported for the specified template
 *
 * @internal
 * @access private
 * @param integer The template ID
 * @return array
 */
function get_stylesheet_media_types($template_id)
{
	$result = array();

	global $gCms;
	$db =& $gCms->GetDb();
	$templateops =& $gCms->GetTemplateOperations();

	$templateobj = FALSE;

	#Grab template id and make sure it's actually "somewhat" valid
	if (isset($template_id) && is_numeric($template_id) && $template_id > -1)
	{
		#Ok, it's valid, let's load the bugger
		$templateobj = $templateops->LoadTemplateById($template_id);
		if (isset($templateobj->stylesheet) && $templateobj->stylesheet != '')
		{
			$result[] = '';
		}
	}

	#If it's valid after loading, then start the process...
	if ($templateobj !== FALSE && ($templateobj->active == '1' || $templateobj->active == TRUE) )
	{
		#Handle "advanced" CSS Management
		$cssquery = "SELECT DISTINCT media_type FROM ".cms_db_prefix()."css c, ".cms_db_prefix()."css_assoc
			WHERE	css_id		= assoc_css_id
			AND		assoc_type	= 'template'
			AND		assoc_to_id = ?";
		$cssresult = &$db->Execute($cssquery, array($template_id));

		while ($cssresult && !$cssresult->EOF)
		{
			if (!in_array($cssresult->fields['media_type'], $result))
				$result[] =& $cssresult->fields['media_type'];
			$cssresult->MoveNext();
		}
		
		if ($cssresult) $cssresult->Close();
	}

	return $result;
}


/**
 * Strips slashes from an array of values.
 *
 * @internal
 * @param array A reference to an array of strings
 * @return reference to the cleaned values
 */
function & stripslashes_deep(&$value) 
{ 
        if (is_array($value)) 
        { 
                $value = array_map('stripslashes_deep', $value); 
        } 
        elseif (!empty($value) && is_string($value)) 
        { 
                $value = stripslashes($value); 
        } 
        return $value;
}

/**
 * A method to create a text area control
 *
 * @internal
 * @access private
 * @param boolean Wether the currently selected wysiwyg area should be enabled (depends on user, and site preferences
 * @param string  The contents of the text area
 * @param string  The name of the text area
 * @param string  An optional class name
 * @param string  An optional ID (HTML ID) value
 * @param string  The optional encoding
 * @param string  Optional style information
 * @param integer Width (the number of columns) (CSS can and will override this)
 * @param integer Hieght (the number of rows) (CSS can and will override this)
 * @param string  A flag to indicate that the wysiwyg should be forced to a different type independant of user settings
 * @param string  The name of the syntax hilighter to use (if empty it is assumed that a wysiwyg text area is requested instead of a syntax hiliter)
 * @param string  Optional additional text to include in the textarea tag
 * @return string
 */
function create_textarea($enablewysiwyg, $text, $name, $classname='', $id='', $encoding='', $stylesheet='', $width='80', $height='15',$forcewysiwyg='',$wantedsyntax='',$addtext='')
{
	global $gCms;
	$result = '';

	if ($enablewysiwyg == true)
	{
		reset($gCms->modules);
		while (list($key) = each($gCms->modules))
		{
			$value =& $gCms->modules[$key];
			if ($gCms->modules[$key]['installed'] == true && //is the module installed?
				$gCms->modules[$key]['active'] == true &&			 //us the module active?
				$gCms->modules[$key]['object']->IsWYSIWYG())   //is it a wysiwyg module?
			{
			  
				if ($forcewysiwyg=='') {
				  
				  if (get_userid(false)==false) {

				    //echo "admin";
					  //get_preference(get_userid(), 'wysiwyg')!="" && //not needed as it won't match the wisiwyg anyway					  
				    if ($gCms->modules[$key]['object']->GetName()==get_site_preference('frontendwysiwyg')) {
				      
				      $result=$gCms->modules[$key]['object']->WYSIWYGTextarea($name,$width,$height,$encoding,$text,$stylesheet,$addtext);
					  }					  
				  } else {
				    
				    if ($gCms->modules[$key]['object']->GetName()==get_preference(get_userid(false), 'wysiwyg')) {
				      $result=$gCms->modules[$key]['object']->WYSIWYGTextarea($name,$width,$height,$encoding,$text,$stylesheet,$addtext);
					  }
				  }	 
				} else {
					if ($gCms->modules[$key]['object']->GetName()==$forcewysiwyg) {
					  $result=$gCms->modules[$key]['object']->WYSIWYGTextarea($name,$width,$height,$encoding,$text,$stylesheet,$addtext);
					}
				}
			}
		}
	}
	
  if (($result=="") && ($wantedsyntax!=''))
	{	  
		reset($gCms->modules);
		while (list($key) = each($gCms->modules))
		{
			$value =& $gCms->modules[$key];
			if ($gCms->modules[$key]['installed'] == true && //is the module installed?
				$gCms->modules[$key]['active'] == true &&			 //us the module active?
				$gCms->modules[$key]['object']->IsSyntaxHighlighter())   //is it a syntaxhighlighter module module?
			{
				if ($forcewysiwyg=='') {
					//get_preference(get_userid(), 'wysiwyg')!="" && //not needed as it won't match the wisiwyg anyway
					if ($gCms->modules[$key]['object']->GetName()==get_preference(get_userid(false), 'syntaxhighlighter')) {
					  $result=$gCms->modules[$key]['object']->SyntaxTextarea($name,$wantedsyntax,$width,$height,$encoding,$text,$addtext);
					}
				} else {
					if ($gCms->modules[$key]['object']->GetName()==$forcewysiwyg) {
					  $result=$gCms->modules[$key]['object']->SyntaxTextarea($name,$wantedsyntax,$width,$height,$encoding,$text,$addtext);
					}
				}
			}
		}
	}

	if ($result == '')
	{
		$result = '<textarea name="'.$name.'" cols="'.$width.'" rows="'.$height.'"';
		if ($classname != '')
		{
			$result .= ' class="'.$classname.'"';
		}
		else
                {
		  $result .= ' class="cms_textarea"';
		}
		if ($id != '')
		{
			$result .= ' id="'.$id.'"';
		}
		if( !empty( $addtext ) )
		  {
		    $result .= ' '.$addtext;
		  }

		$result .= '>'.cms_htmlentities($text,ENT_NOQUOTES,get_encoding($encoding)).'</textarea>';
	}

	return $result;
}

/*
 * creates a textarea that does syntax highlighting on the source code.
 * The following also needs to be added to the <form> tag for submit to work.
 * if($use_javasyntax){echo 'onSubmit="textarea_submit(
 * this, \'custom404,sitedown\');"';}
 */
/*
OBSOLETE!!!

function textarea_highlight($use_javasyntax, $text, $name, $class_name="syntaxHighlight", $syntax_type="HTML (Complex)", $id="", $encoding='')
{
    if ($use_javasyntax)
	{
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

    }
	else
	{
        $output = '<textarea name="'.$name.'" cols="80" rows="24"
            class="'.$class_name.'"';
        if ($id<>"")
            $output.=' id="'.$id.'"';
        $output.='>'.cms_htmlentities($text,ENT_NOQUOTES,get_encoding($encoding)).'</textarea>';
    }

    return $output;
}
*/


/**
 * Creates a string containing links to all the pages.
 *
 * @deprecated
 * @param page - the current page to display
 * @param totalrows - the amount of items being listed
 * @param limit - the amount of items to list per page
 * @return a string containing links to all the pages (ex. next 1,2 prev)
 */
 function pagination($page, $totalrows, $limit)
 {
   $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
	$page_string = "";
	$from = ($page * $limit) - $limit;
	$numofpages = (int)($totalrows / $limit);
	if( ($totalrows % $limit) != 0 ) ++$numofpages;
	if ($numofpages > 1)
	{
		if($page != 1)
		{
			$pageprev = $page-1;
			$page_string .= '<a href="'.$_SERVER['PHP_SELF'].$urlext.'&amp;page=1">'.lang('first').'</a>&nbsp;';
			$page_string .= "<a href=\"".$_SERVER['PHP_SELF'].$urlext."&amp;page=$pageprev\">".lang('previous')."</a>&nbsp;";
		}
		else
		{
			$page_string .= lang('first')." ";
			$page_string .= lang('previous')." ";
		}

		$page_string .= '&nbsp;'.lang('page')."&nbsp;$page&nbsp;".lang('of')."&nbsp;$numofpages&nbsp;";
		// links to individual pages
// 		for($i = 1; $i <= $numofpages; $i++)
// 		{
// 			if($i == $page)
// 			{
// 				$page_string .= $i."&nbsp;";
// 			}
// 			else
// 			{
// 				$page_string .= "<a href=\"".$_SERVER['PHP_SELF']."?page=$i\">$i</a>&nbsp;";
// 			}
// 		}

// 		if(($totalrows % $limit) != 0)
// 		{
// 			if($i == $page)
// 			{
// 				$page_string .= $i."&nbsp;";
// 			}
// 			else
// 			{
// 				$page_string .= "<a href=\"".$_SERVER['PHP_SELF']."?page=$i\">$i</a>&nbsp;";
// 			}
// 		}

		if(($totalrows - ($limit * $page)) > 0)
		{
			$pagenext = $page+1;
			$page_string .= "<a href=\"".$_SERVER['PHP_SELF'].$urlext."&amp;page=$pagenext\">".lang('next')."</a>&nbsp;";
			$page_string .= '<a href="'.$_SERVER['PHP_SELF'].$urlext.'&amp;page='.$numofpages.'">'.lang('last').'</a>';
		}
		else
		{
			$page_string .= lang('next')." ";
			$page_string .= lang('last')." ";
		}
	}
	return $page_string;
 }



/**
 * Returns the currently configured database prefix.
 *
 * @since 0.4
 * @return string
 */
function cms_db_prefix() {
  global $gCms;
  $config = $gCms->GetConfig();
  return $config["db_prefix"];
}


/**
 * Create a dropdown form element containing a list of files that match certain conditions
 *
 * @internal
 * @param string The name for the select element.
 * @param string The directory name to search for files.
 * @param string The name of the file that should be selected
 * @param string A comma separated list of extensions that should be displayed in the list
 * @param string An optional string with which to prefix each value in the output by
 * @param boolean Wether 'none' should be an allowed option
 * @param string Text containing additional parameters for the dropdown element
 * @param string A prefix to use when filtering files
 * @param boolean A flag indicating wether the files matching the extension and the prefix should be included or excluded from the result set
 * @return string
 */
function create_file_dropdown($name,$dir,$value,$allowed_extensions,$optprefix='',$allownone=false,$extratext='',
			      $fileprefix='',$excludefiles=1)
{
  $files = array();
  $files = get_matching_files($dir,$allowed_extensions,true,true,$fileprefix,$excludefiles);
  if( $files === false ) return false;
  $out = "<select name=\"{$name}\" {$extratext}>\n";
  if( $allownone )
    {
      $txt = '';
      if( empty($value) )
	{
	  $txt = 'selected="selected"';
	}
      $out .= "  <option value=\"-1\" $txt>--- ".lang('none')." ---</option>\n";
    }

  foreach( $files as $file )
    {
      $txt = '';
      $opt = $file;
      if( !empty($optprefix) )
	{
	  $opt = $optprefix.'/'.$file;
	}
      if( $opt == $value )
	{
	  $txt = 'selected="selected"';
	}
      $out .= "  <option value=\"{$opt}\" {$txt}>{$file}</option>\n";
    }
  $out .= "</select>";
  return $out;
}


/**
 * A function tat, given the current request information will return
 * a pageid or an alias that should be used for the display
 * This method also handles matching routes and specifying which module
 * should be called with what parameters
 *
 * @internal
 * @access private
 * @return string
 */
function get_pageid_or_alias_from_url()
{
  global $gCms;
  $config = $gCms->GetConfig();
  $contentops =& $gCms->GetContentOperations();
  $smarty = &$gCms->smarty;

  $params =& $_REQUEST;
  if (isset($params['mact']))
    {
      $ary = explode(',', cms_htmlentities($params['mact']), 4);
      $smarty->id = (isset($ary[1])?$ary[1]:'');
    }
  else
    { // old?
      $smarty->id = (isset($params['id'])?intval($params['id']):'');
    }

  $page = '';
  if (isset($smarty->id) && isset($params[$smarty->id . 'returnid']))
    {
      // get page from returnid parameter in module action
      $page = $params[$smarty->id . 'returnid'];
    }
  else if (isset($config["query_var"]) && $config["query_var"] != '' && isset($_GET[$config["query_var"]]))
    {
      // using non friendly urls... get the page alias/id from the query var.
      $page = $_GET[$config["query_var"]];    
    }
  else
    {
      // either we're using pretty urls
      // or this is the default page.
      if (isset($_SERVER["REQUEST_URI"]) && !endswith($_SERVER['REQUEST_URI'], 'index.php'))
	{
	  $matches = array();
	  if (preg_match('/.*index\.php\/(.*?)$/', $_SERVER['REQUEST_URI'], $matches))
	    {
	      // pretty urls... grab all the stuff after the index.php
	      $page = $matches[1];
	    }
	}
    }


  // by here, if page is empty, use the default page id
  if ($page == '')
    {
      // assume default content
      $page = $contentops->GetDefaultContent();
    }

  // by here if page is an int, we're done.
  if( is_numeric($page) ) return $page;

  // some kind of a pretty url.
  // strip off GET params.
  if( ($tmp = strpos($page,'?')) !== FALSE )
    {
      $page = substr($page,0,$tmp);
    }

  // strip off page extension
  if ($config['page_extension'] != '' && endswith($page, $config['page_extension']))
    {   
      $page = substr($page, 0, strlen($page) - strlen($config['page_extension']));
    }

  // trim trailing /
  $page = rtrim($page, '/');

  // see if there's a route that matches.
  $matched = false;
  $route = cms_route_manager::find_match($page);
  if( is_object($route) )
    {
      $matched = true;
      if( $route->is_content() )
	{
	  // a route to a page.
	  $page = $route->get_content();
	}
      else
	{
	  $matches = $route->get_results();

	  // it's a module route
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
	    $matches['module'] = $route->get_dest();

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
		    $_REQUEST[$matches['id'] . $key] = $val;
		}
	    }

	  //Now set any defaults that might not have been in the url
	  $tmp = $route->get_defaults();
	  if (is_array($tmp) && count($tmp) > 0)
	    {
	      foreach ($tmp as $key=>$val)
		{
		  $_REQUEST[$matches['id'] . $key] = $val;
		  if (array_key_exists($key, $matches))
		    { 
		      $matches[$key] = $val;
		    }
		}
	    }

	  //Get a decent returnid
	  if ($matches['returnid'] == '') {
	    $matches['returnid'] = $contentops->GetDefaultPageID();
	  }

	  // Put the resulting mact into the request so that the subsequent smarty plugins
	  // can grab it...
	  $_REQUEST['mact'] = $matches['module'] . ',' . $matches['id'] . ',' . $matches['action'] . ',' . $matches['inline'];

	  $page = $matches['returnid'];
	  $smarty->id = $matches['id'];
	}
    }

  // if no route matched... grab the alias from the last /
  if( ($pos = strrpos($page,'/')) !== FALSE && $matched == false )
    {
      $page = substr($page, $pos + 1);
    }

  // if there's nothing use the default content.
  if( empty($page) )
    {
      // maybe it's the home page.
      $page = $contentops->GetDefaultContent();
    }

  return $page;
}


/**
 * Function to return the current page id.
 * 
 * Will return FALSE if in an admin action.
 *
 * @return integer pageid indicating the current page, or FALSE.
 */
function cms_get_current_pageid()
{
  global $gCms;

  if( isset($gCms->variables['page_id']) )
    {
      return (int)$gCms->variables['page_id'];
    }
  return FALSE;
}
# vim:ts=4 sw=4 noet
?>
