<?php
# CMS - CMS Made Simple
# (c)2004 by Ted Kulp (tedkulp@users.sf.net)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * Base module class.
 *
 * All modules should inherit and extend this class with their functionality.
 *
 * @since		0.9
 * @package		CMS
 */
class CMSModule extends ModuleOperations
{
	/**
	 * ------------------------------------------------------------------
	 * Initialization Functions and parameters
	 * ------------------------------------------------------------------
	 */
	var $cms;

	function CMSModule()
	{
		global $gCms;
		$this->cms = &$gCms;
	}

	/**
	 * ------------------------------------------------------------------
	 * Basic Functions.  Name and Version MUST be overridden.
	 * ------------------------------------------------------------------
	 */

	/**
	 * Returns the name of the module
	 */
	function GetName()
	{
		return 'unset';
	}

	/**
	 * Returns the version of the module
	 */
	function GetVersion()
	{
		return '0.0.0.1';
	}

	/**
	 * Returns the help for the module
	 *
	 * @param string Optional language that the admin is using.  If that language
	 * is not defined, use en_US.
	 */
	function GetHelp($lang = 'en_US')
	{
		return '';
	}

	/**
	 * Returns a short description of the module
	 *
	 * @param string Optional language that the admin is using.  If that language
	 * is not defined, use en_US.
	 */
	function GetDescription($lang = 'en_US')
	{
		return '';
	}

	/**
	 * Returns the changelog for the module
	 */
	function GetChangeLog()
	{
		return '';
	}

	/**
	 * Returns the name of the author
	 */
	function GetAuthorName()
	{
		return '';
	}

	/**
	 * Returns the email address of the author
	 */
	function GetAuthorEmail()
	{
		return '';
	}

	/**
	 * ------------------------------------------------------------------
	 * Content Type Related Functions
	 * ------------------------------------------------------------------
	 */
	
	/**
	 * Does this module support a custom content type?
	 */
	function HasContentType()
	{
		return FALSE;
	}

	/**
	 * Return an instance of the new content type
	 */
	function GetContentTypeInstance()
	{
		return FALSE;
	}

	/**
	 * ------------------------------------------------------------------
	 * Installation Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Function that will get called as module is installed. This function should
	 * do any initialization functions including creating database tables. It
	 * should return a string message if there is a failure. Returning nothing (FALSE)
	 * will allow the install procedure to proceed.
	 */
	function Install()
	{
		return FALSE;
	}

	/**
	 * Display a message after a successful installation of the module.
	 */
	function InstallPostMessage()
	{
		return FALSE;
	}

	/**
	 * Function that will get called as module is uninstalled. This function should
	 * remove any database tables that it uses and perform any other cleanup duties.
	 * It should return a string message if there is a failure. Returning nothing
	 * (FALSE) will allow the uninstall procedure to proceed.
	 */
	function Uninstall()
	{
		return FALSE;
	}

	/**
	 * Display a message after a successful uninstall of the module.
	 */
	function UninstallPostMessage()
	{
		return FALSE;
	}

	/**
	 * Function to perform any upgrade procedures. This is mostly used to for
	 * updating databsae tables, but can do other duties as well. It should
	 * return a string message if there is a failure. Returning nothing (FALSE)
	 * will allow the upgrade procedure to proceed. Upgrades should have a path
	 * so that they can be upgraded from more than one version back.  While not
	 * a requirement, it makes like easy for your users.
	 *
	 * @param string The version we are upgrading from
	 * @param string The version we are upgrading to
	 */
	function Upgrade($oldversion, $newversion)
	{
		return FALSE;
	}
 
 	/**
	 * Returns a list of dependencies and minimum versions that this module
	 * requires. It should return an hash, eg.
	 * return array('somemodule'=>'1.0', 'othermodule'=>'1.1');
	 */
	function GetDependencies()
	{
		return array();
	}
 
 	/**
	 * Checks to see if currently installed modules depend on this module.  This is
	 * used by the plugins.php page to make sure that a module can't be uninstalled
	 * before any modules depending on it are uninstalled first.
	 */
	function CheckForDependents()
	{
		global $gCms;
		$db = $gCms->db;

		$result = false;

		$query = "SELECT * FROM ".cms_db_prefix()."module_deps WHERE parent_module = ?";
		$dbresult = $db->Execute($query, array($this->GetName()));

		if ($dbresult && $dbresult->RowCount() > 0)
		{   
			$result = true;
		}

		return $result;
	}

	/**
	 * Return true if there is an admin for the module.  Returns false by
	 * default.
	 */
	function HasAdmin()
	{
		return false;
	}

	/**
	 * Returns true if the module should be treated as a content module.
	 * Returns false by default.
	 */
	function IsContentModule()
	{
		return false;
	}

	/**
	 * Returns true if the module should be treated as a plugin module (like
	 * {cms_module module='name'}.  Returns false by default.
	 */
	function IsPluginModule()
	{
		return false;
	}

	/**
	 * ------------------------------------------------------------------
	 * Login Related Functions
	 * ------------------------------------------------------------------
	 */
	
	/**
	 * Called after a successful login.  It sends the user object.
	 *
	 * @param User The user that just logged in
	 */
	function LoginPost(&$user)
	{
	}

	/**
	 * Called after a successful logout.
	 */
	function LogoutPost()
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * User Related Functions
	 * ------------------------------------------------------------------
	 */
	
	/**
	 * Called before a user is added to the database.  Sends the user object.
	 *
	 * @param User The user that was just created
	 */
	function AddUserPre(&$user)
	{
	}

	/**
	 * Called after a user is added to the database.  Sends the user object.
	 *
	 * @param User The user that was just created
	 */
	function AddUserPost(&$user)
	{
	}

	/**
	 * Called before a user is saved to the database.  Sends the user object.
	 *
	 * @param User The user that was just edited
	 */
	function EditUserPre(&$user)
	{
	}

	/**
	 * Called after a user is saved to the database.  Sends the user object.
	 *
	 * @param User The user that was just edited 
	 */
	function EditUserPost(&$user)
	{
	}

	/**
	 * Called before a user is deleted from the database.  Sends the user object.
	 *
	 * @param User The user that was just deleted 
	 */
	function DeleteUserPre(&$user)
	{
	}

	/**
	 * Called after a user is deleted from the database.  Sends the user object.
	 *
	 * @param User The user that was just deleted 
	 */
	function DeleteUserPost(&$user)
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * Group Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Called before a group is added to the database.  Sends the group object.
	 *
	 * @param Group The group that was just created
	 */
	function AddGroupPre(&$group)
	{
	}

	/**
	 * Called after a group is added to the database.  Sends the group object.
	 *
	 * @param Group The group that was just created
	 */
	function AddGroupPost(&$group)
	{
	}

	/**
	 * Called before a group is saved to the database.  Sends the group object.
	 *
	 * @param Group The group that was just edited
	 */
	function EditGroupPre(&$group)
	{
	}

	/**
	 * Called after a group is saved to the database.  Sends the group object.
	 *
	 * @param Group The group that was just edited 
	 */
	function EditGroupPost(&$group)
	{
	}

	/**
	 * Called before a group is deleted from the database.  Sends the group object.
	 *
	 * @param Group The group that was just deleted
	 */
	function DeleteGroupPre(&$group)
	{
	}

	/**
	 * Called after a group is deleted from the database.  Sends the group object.
	 *
	 * @param Group The group that was just deleted 
	 */
	function DeleteGroupPost(&$group)
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * Template Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Called before a template is added to the database.  Sends the template
	 * object.
	 *
	 * @param Template The template that was just created
	 */
	function AddTemplatePre(&$template)
	{
	}

	/**
	 * Called after a template is added to the database.  Sends the template
	 * object.
	 *
	 * @param Template The template that was just created
	 */
	function AddTemplatePost(&$template)
	{
	}

	/**
	 * Called before a template is saved to the database.  Sends the template
	 * object.
	 *
	 * @param Template The template that was just edited
	 */
	function EditTemplatePre(&$template)
	{
	}

	/**
	 * Called after a template is saved to the database.  Sends the template
	 * object.
	 *
	 * @param Template The template that was just edited 
	 */
	function EditTemplatePost(&$template)
	{
	}

	/**
	 * Called before a template is deleted from the database.  Sends the template
	 * object.
	 *
	 * @param Template The template that was just deleted
	 */
	function DeleteTemplatePre(&$template)
	{
	}

	/**
	 * Called after a template is deleted from the database.  Sends the template
	 * object.
	 *
	 * @param Template The template that was just deleted 
	 */
	function DeleteTemplatePost(&$template)
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * HTML Blob Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Called before an HTML blob is added to the database.  Sends the html blob
	 * object.
	 *
	 * @param HtmlBlob The HTML blob that was just created
	 */
	function AddHtmlBlobPre(&$htmlblob)
	{
	}

	/**
	 * Called after an HTML blob is added to the database.  Sends the html blob
	 * object.
	 *
	 * @param HtmlBlob The HTML blob that was just created
	 */
	function AddHtmlBlobPost(&$htmlblob)
	{
	}

	/**
	 * Called before an HTML blob is saved to the database.  Sends the html blob
	 * object.
	 *
	 * @param HtmlBlob The HTML blob that was just edited
	 */
	function EditHtmlBlobPre(&$htmlblob)
	{
	}

	/**
	 * Called after an HTML blob is saved to the database.  Sends the html blob
	 * object.
	 *
	 * @param HtmlBlob The HTML blob that was just edited 
	 */
	function EditHtmlBlobPost(&$htmlblob)
	{
	}

	/**
	 * Called before an HTML blob is deleted from the database.  Sends the html
	 * blob object.
	 *
	 * @param HtmlBlob The HTML blob that was just deleted
	 */
	function DeleteHtmlBlobPre(&$htmlblob)
	{
	}

	/**
	 * Called after an HTML blob is deleted from the database.  Sends the html
	 * blob object.
	 *
	 * @param HtmlBlob The HTML blob that was just deleted 
	 */
	function DeleteHtmlBlobPost(&$htmlblob)
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * Content Related Functions
	 * ------------------------------------------------------------------
	 */
	
	/**
	 * Called with the content of the template before content, html blobs, etc
	 * are pasted in.
	 *
	 * @param string The template text
	 */
	function ContentTemplate(&$template)
	{
	}

	/**
	 * Called with the content of the stylesheet before it is pasted into the
	 * template.
	 *
	 * @param string The stylesheet text
	 */
	function ContentStylesheet(&$stylesheet)
	{
	}

	/**
	 * Called with the title before it is pasted into the template.
	 *
	 * @param string The title text
	 */
	function ContentTitle(&$title)
	{
	}

	/**
	 * Called with the content data before it is pasted into the template.
	 *
	 * @param string The content text
	 */
	function ContentData(&$content)
	{
	}

	/**
	 * Called with the content of the html blob before it is pasted into the
	 * template (but after content is pasted in)
	 *
	 * @param string The html blob text
	 */
	function ContentHtmlBlob(&$htmlblob)
	{
	}

	/**
	 * Called before the pasted together template/content/html blobs/etc are
	 * sent to smarty for processing.
	 *
	 * @param string The prerendered text
	 */
	function ContentPreRender(&$content)
	{
	}

	/**
	 * Called after content is sent to smarty for processing and right before
	 * display.  Cached content will still call this function before display.
	 *
	 * @param string The postrendered text
	 */
	function ContentPostRender(&$content)
	{
	}

	/**
	 * ------------------------------------------------------------------
	 * WYSIWYG Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Returns true if this module should be treated as a WYSIWYG module. It
	 * returns false be default.
	 */
	function IsWYSIWYG()
	{
		return false;
	}

	/**
	 * Returns content destined for the <form> tag.  It's useful if javascript is
	 * needed for the onsubmit of the form.
	 */
	function WYSIWYGPageForm()
	{
		return '';
	}

	/**
	 * This is a function that would be called before a form is submitted.
	 * Generally, a dropdown box or something similar that would force a submit
	 * of the form via javascript should put this in their onchange line as well
	 * so that the WYSIWYG can do any cleanups before the actual form submission
	 * takes place.
	 */
	 function WYSIWYGPageFormSubmit()
	 {
	 	return '';
	 }
	 
	 /**
	  * Returns header code specific to this WYSIWYG
	  */
	  function WYSIWYGGenerateHeader()
	  {
	  	return '';
	  }

	 /**
	  * Returns body code specific to this WYSIWYG
	  */
	  function WYSIWYGGenerateBody()
	  {
	  	return '';
	  }
	
	/**
	 * Returns the textarea specific for this WYSIWYG.
	 *
	 * @param string HTML name of the textarea
	 * @param int Number of columns wide that the textarea should be
	 * @param int Number of rows long that the textarea should be
	 * @param string Encoding of the content
	 * @param string Content to show in the textarea
	 * @param string Stylesheet for content, if available
	 */
	function WYSIWYGTextarea($name='textarea',$columns='80',$rows='15',$encoding='',$content='',$stylesheet='')
	{
		return '<textarea name="'.$name.'" cols="'.$columns.'" rows="'.$rows.'">'.$content.'</textarea>';
	}

	/**
	 * ------------------------------------------------------------------
	 * Navigation Related Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Used for navigation between "pages" of a module.  Forms and links should
	 * pass an action with them so that the module will know what to do next.
	 * By default, DoAction will be passed 'default' and 'defaultadmin',
	 * depending on where the module was called from.  If being used as a module
	 * or content type, 'default' will be passed.  If the module was selected
	 * from the list on the admin menu, then 'defaultadmin' will be passed.
	 *
	 * @param string Name of the action to perform
	 * @param string The ID of the module
	 * @param string The parameters targeted for this module
	 */
	function DoAction($name, $id, $parameters, $returnid='')
	{
		return '';
	}

	/**
	 * Returns the start of a module form
	 *
	 * @param string The id given to the module on execution
	 * @param string The action that this form should do when the form is submitted
	 * @param string The id to eventually return to when the module is finished it's task
	 * @param string Method to use for the form tag.  Defaults to 'post'
	 * @param string Optional enctype to use, Good for situations where files are being uploaded
	 */
	function CreateFormStart($id, $action='default', $returnid='', $method='post', $enctype='')
	{
		$text = '<form name="'.$id.'moduleform" method="'.$method.'" action="moduleinterface.php"';
		if ($enctype != '')
		{
			$text .= ' enctype="'.$enctype.'"';
		}
		$text .= '><input type="hidden" name="module" value="'.$this->GetName().'" /><input type="hidden" name="id" value="'.$id.'" />';
		if ($action != '')
		{
			$text .= '<input type="hidden" name="'.$id.'action" value="'.$action.'" />';
		}
		if ($returnid != '')
		{
			$text .= '<input type="hidden" name="'.$id.'returnid" value="'.$returnid.'" />';
		}
		$text .= "\n";
		return $text;
	}

	/**
	 * Returns the end of the a module form.  This is basically just a wrapper around </form>, but
	 * could be extended later on down the road.  It's here mainly for consistency.
	 */
	function CreateFormEnd()
	{
		return '</form>'."\n";
	}

	/**
	 * Returns the xhtml equivalent of an input textbox.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the textbox
	 * @param string The predefined value of the textbox, if any
	 * @param string The number of columns wide the textbox should be displayed
	 * @param string The maximum number of characters that should be allowed to be entered
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateInputText($id, $name, $value='', $size='10', $maxlength='255', $addttext='')
	{
		$text = '<input type="text" name="'.$id.$name.'" value="'.$value.'" size="'.$size.'" maxlength="'.$maxlength.'"';
		if ($addttext != '')
		{
			$text .= ' ' . $addttext;
		}
		$text .= " />\n";
		return $text;
	}

	/**
	 * Returns the xhtml equivalent of a hidden field.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the hidden field 
	 * @param string The predefined value of the field, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateInputHidden($id, $name, $value='', $addttext='')
	{
		$text = '<input type="hidden" name="'.$id.$name.'" value="'.$value.'"';
		if ($addttext != '')
		{
			$text .= ' '.$addttext;
		}
		$text .= " />\n";
		return $text;
	}

	/**
	 * Returns the xhtml equivalent of a submit button.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the button
	 * @param string The predefined value of the button, if any
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateInputSubmit($id, $name, $value='', $addttext='')
	{
		$text = '<input type="submit" name="'.$id.$name.'" value="'.$value.'"';
		if ($addttext != '')
		{
			$text .= ' '.$addttext;
		}
		$text .= ' />';
		return $text . "\n";
	}

	/**
	 * Returns the xhtml equivalent of a dropdown list.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it is xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The html name of the dropdown list
	 * @param string An array of items to put into the dropdown list... they should be $key=>$value pairs
	 * @param string The default selected index of the dropdown list.  Setting to -1 will result in the first choice being selected
	 * @param string The default selected value of the dropdown list.  Setting to '' will result in the first choice being selected
	 * @param string Any additional text that should be added into the tag when rendered
	 */
	function CreateInputDropdown($id, $name, $items, $selectedindex=-1, $selectedvalue='', $addttext='')
	{
		$text = '<select name="'.$id.$name.'"';
		if ($addttext != '')
		{
			$text .= ' ' . $addttext;
		}
		$text .= '>';
		$count = 0;
		foreach ($items as $key=>$value)
		{
			$text .= '<option value="'.$value.'"';
			if ($selectedindex == $count || $selectedvalue == $value)
			{
				$text .= ' ' . 'selected="selected"';
			}
			$text .= '>';
			$text .= $key;
			$text .= '</option>';
			$count++;
		}
		$text .= '</select>'."\n";

		return $text;
	}

	function CreateTextArea($enablewysiwyg, $id, $text, $name, $classname, $htmlid='', $encoding='', $stylesheet='')
	{
		return create_textarea($enablewysiwyg, $text, $id.$name, $classname, $htmlid, $encoding, $stylesheet);
	}

	/**
	 * Returns the xhtml equivalent of an href link  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 *
	 * @param string The id given to the module on execution
	 * @param string The action that this form should do when the form is submitted
	 * @param string The id to eventually return to when the module is finished it's task
	 * @param string The text that will have to be clicked to follow the link
	 * @param string An array of params that should be inlucded in the URL of the link.  These should be in a $key=>$value format.
	 * @param string Text to display in a javascript warning box.  If they click no, the link is not followed by the browser.
	 */
	function CreateLink($id, $action, $returnid='', $contents='', $params=array(), $warn_message='', $onlyhref=false)
	{
		if (!$onlyhref)
		{
			$text = '<a href="';
		}
		$text .= $this->cms->config['root_url'];
		if (!($returnid != '' && $returnid > -1))
		{
			$text .= '/admin';
		}
		$text .= '/moduleinterface.php?module='.$this->GetName().'&amp;id='.$id.'&amp;'.$id.'action='.$action;
		foreach ($params as $key=>$value)
		{
			$text .= '&amp;'.$id.$key.'='.$value;
		}
		if ($returnid != '')
		{
			$text .= '&amp;'.$id.'returnid='.$returnid;
		}
		$text .= "\"";
		if (!$onlyhref)
		{
			if ($warn_message !== '')
			{
				$text .= ' onclick="return confirm(\''.$warn_message.'\');"';
			}
			$text .= '>'.$contents.'</a>';
		}
		return $text;
	}

	/**
	 * Redirects the user to another action of the module. 
	 *
	 * @param string The id given to the module on execution
	 * @param string The action that this form should do when the form is submitted
	 * @param string The id to eventually return to when the module is finished it's task
	 */
	function Redirect($id, $action, $returnid='')
	{
		global $gCms;
		$config = $gCms->config;

		$name = $this->GetName();

		$text = 'moduleinterface.php?module='.$name.'&amp;'.$id.'action='.$action.'&amp;id='.$id;
		if ($returnid != '')
		{
			$text .= '&amp;'.$id.'returnid='.$returnid;
		}
		redirect($text);
	}

	/**
	 * Redirects the user to a content page outside of the module.  The passed around returnid is
	 * frequently used for this so that the user will return back to the page from which they first
	 * entered the module.
	 *
	 * @param string Content id to redirect to.
	 */
	function RedirectContent($id)
	{
		$content = ContentManager::LoadContentFromId($id);
		if (isset($content))
		{
			if ($content->GetUrl() != '')
			{
				redirect($content->GetUrl());
			}
		}
	}

	/**
	 * ------------------------------------------------------------------
	 * Intermodule Functions
	 * ------------------------------------------------------------------
	 */
	
	function GetMoudleInstance($module)
	{
		global $gCms;

		if (isset($gCms->modules[$module]) &&
			$gCms->modules[$module]['installed'] == true &&
			$gCms->modules[$module]['active'] == true)
		{
			return $gCms->modules[$module]['object'];
		}

		return FALSE;
	}

	/**
	 * ------------------------------------------------------------------
	 * Other Functions
	 * ------------------------------------------------------------------
	 */

	/**
	 * Put an event into the audit (admin) log.  This should be
	 * done on most admin events for consistency.
	 */
	function Audit($itemid, $itemname, $action)
	{
		$userid = get_userid();
		$username = $_SESSION["cms_admin_username"];
		audit($userid, $username, $itemid, $itemname, $action);
	}

	/**
	 * Create's a new permission for use by the module.
	 *
	 * @param string Name of the permission to create
	 * @param string Description of the permission
	 */
	function CreatePermission($permission_name, $permission_text)
	{
		$db = $this->cms->db;

		$query = "SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name =" . $db->qstr($permission_name); 
		$result = $db->Execute($query);

		if ($result && $result->RowCount() < 1)
		{
			$new_id = $db->GenID(cms_db_prefix()."permissions_seq");
			$query = "INSERT INTO ".cms_db_prefix()."permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES ($new_id, ".$db->qstr($permission_name).",".$db->qstr($permission_text).",'".$db->DBTimeStamp(time())."','".$db->DBTimeStamp(time())."')";
			$db->Execute($query);
		}
	}

	/**
	 * Checks a permission against the currently logged in user.
	 *
	 * @param string The name of the permission to check against the current user
	 */
	function CheckPermission($permission_name)
	{
		$userid = get_userid();
		return check_permission($userid, $permission_name);
	}

	/** 
	 * Removes a permission from the system.  If recreated, the
	 * permission would have to be set to all groups again.
	 *
	 * @param string The name of the permission to remove
	 */
	function RemovePermission($permission_name)
	{
		$db = $this->cms->db;

		$query = "SELECT permission_id FROM ".cms_db_prefix()."permissions WHERE permission_name = " . $db->qstr($permission_name); 
		$result = $db->Execute($query);

		if ($result && $result->RowCount() > 0)
		{
			$row = $result->FetchRow();
			$id = $row["permission_id"];

			$query = "DELETE FROM ".cms_db_prefix()."group_perms WHERE permission_id = $id";
			$db->Execute($query);

			$query = "DELETE FROM ".cms_db_prefix()."permissions WHERE permission_id = $id";
			$db->Execute($query);
		}
	}

	/**
	 * Returns a module preference if it exists.
	 *
	 * @param string The name of the preference to check
	 * @param string The default value, just in case it doesn't exist
	 */
	function GetPreference($preference_name, $defaultvalue='')
	{
		return get_site_preference($this->GetName() . "_mapi_pref_" . $preference_name, $defaultvalue);
	}

	/**
	 * Sets a module preference.
	 *
	 * @param string The name of the preference to check
	 */
	function SetPreference($preference_name, $value)
	{
		return set_site_preference($this->GetName() . "_mapi_pref_" . $preference_name, $value);
	}

	/**
	 * Creates a string containing links to all the pages.

	 * @param string the current page to display
	 * @param string the amount of items being listed
	 * @param string the amount of items to list per page
	 */
	function CreatePagination($id, $action, $returnid, $page, $totalrows, $limit)
	{
		$link = '<a href="moduleinterface.php?module='.$this->GetName().'&amp;'.$id.'returnid='.$id.$returnid.'&amp;'.$id.'page=';
		$page_string = "";
		$from = ($page * $limit) - $limit;
		$numofpages = floor($totalrows / $limit);
		if ($numofpages * $limit < $totalrows)
		{
			$numofpages++;
		}

		if ($numofpages > 1)
		{
			if($page != 1)
			{
				$pageprev = $page-1;
				$page_string .= $link.$pageprev."\">".lang('previous')."</a>&nbsp;";
			}
			else
			{
				$page_string .= lang('previous')." ";
			}

			for($i = 1; $i <= $numofpages; $i++)
			{
				if($i == $page)
				{
					 $page_string .= $i."&nbsp;";
				}
				else
				{
					 $page_string .= $link.$i."\">$i</a>&nbsp;";
				}
			}

			if (($totalrows - ($limit * $page)) > 0)
			{
				$pagenext = $page+1;
				$page_string .= $link.$pagenext."\">".lang('next')."</a>";
			}
			else
			{
				$page_string .= lang('next')." ";
			}
		}

		return $page_string;
	}
}

/**
 * "Static" module functions for internal use and module development.  CMSModule
 * extends this so that it has internal access to the functions.
 *
 * @since		0.9
 * @package		CMS
 */
class ModuleOperations
{
	/**
	 * Loads modules from the filesystem.  If loadall is true, then it will load all
	 * modules whether they're installed, or active.  If it is false, then it will
	 * only load modules which are installed and active.
	 */
	function LoadModules($loadall = false)
	{
		global $gCms;
		$db = $gCms->db;
		$cmsmodules = &$gCms->modules;

		$dir = dirname(dirname(dirname(__FILE__)))."/modules";

		if ($loadall == true)
		{
			$ls = dir($dir);
			while (($file = $ls->read()) != "")
			{
				if (is_dir("$dir/$file") && (strpos($file, ".") === false || strpos($file, ".") != 0))
				{
					if (is_file("$dir/$file/$file.module.php"))
					{
						include_once("$dir/$file/$file.module.php");
					}
					else
					{
						unset($cmsmodules[$file]);
					}
				}
			}
			//Find modules and instantiate them
			$allmodules = @ModuleOperations::FindModules();
			foreach ($allmodules as $onemodule)
			{
				if (class_exists($onemodule))
				{
					$newmodule = new $onemodule;
					$name = $newmodule->GetName();
					$cmsmodules[$name]['object'] = $newmodule;
					$cmsmodules[$name]['installed'] = false;
					$cmsmodules[$name]['active'] = false;
				}
				else
				{
					unset($cmsmodules[$name]);
				}
			}
		}

		#Figger out what modules are active and/or installed
		#Load them if loadall is false
		if (isset($db))
		{
			$query = "SELECT * FROM ".cms_db_prefix()."modules";
			$result = $db->Execute($query);
			if ($result)
			{
				while ($row = $result->FetchRow())
				{
					if (isset($row['module_name']))
					{
						$modulename = $row['module_name'];
						if (isset($modulename))
						{
							if ($loadall == true)
							{
								if (isset($cmsmodules[$modulename]))
								{
									$cmsmodules[$modulename]['installed'] = true;
									$cmsmodules[$modulename]['active'] = ($row['active']=='1'?true:false);
								}
							}
							else
							{
								if ($row['active'] == '1')
								{
									if (is_file("$dir/$modulename/$modulename.module.php"))
									{
										include_once("$dir/$modulename/$modulename.module.php");
										if (class_exists($modulename))
										{
											$newmodule = new $modulename;
											$name = $newmodule->GetName();

											#Check to see if version in db matchs file version
											if ($row['version'] == $newmodule->GetVersion())
											{
												$cmsmodules[$name]['object'] = $newmodule;
												$cmsmodules[$name]['installed'] = true;
												$cmsmodules[$name]['active'] = ($row['active']=='1'?true:false);
											}
											else
											{
												unset($cmsmodules[$name]);
											}
										}
										else //No point in doing anything with it
										{
											unset($cmsmodules[$name]);
										}
									}
									else
									{
										unset($cmsmodules[$modulename]);
									}
								}
							}

							/*
							if ($row['active'] == '1')
							{
								#Try generating a content class for this module
								cms_mapi_create_module_content_class($modulename);
							}
							*/
						}
					}
				}
			}
		}
	}

	/**
	 * Finds all classes extending cmsmodule for loading
	 */
	function FindModules()
	{
		$result = array();

		foreach (get_declared_classes() as $oneclass)
		{
			if (strtolower(get_parent_class($oneclass)) == 'cmsmodule')
			{
				array_push($result, strtolower($oneclass));
			}
		}

		return $result;
	}

	/**
	 * Returns a hash of all loaded modules.  This will include all
	 * modules loaded by LoadModules, which could either be all or them,
	 * or just ones that are active and installed.
	 */
	function GetAllModules()
	{
		global $gCms;
		$cmsmodules = &$gCms->modules;
		return $cmsmodules;
	}

	function GetModuleParameters($id)
	{
		$params = array();

		foreach ($_REQUEST as $key=>$value)
		{
			if (strpos($key, $id) !== FALSE && strpos($key, $id) == 0)
			{
				$key = str_replace($id, '', $key);
				$params[$key] = $value;
			}
		}

		return $params;
	}
}

class CMSModuleContentType extends ContentBase
{
}

# vim:ts=4 sw=4 noet
?>
