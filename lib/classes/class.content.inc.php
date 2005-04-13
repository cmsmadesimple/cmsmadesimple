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
 * This file respect the PHP Coding Standards : http://alltasks.net/code/php_coding_standard.html
 */

/**
 * Generic content class.
 *
 * As for some treatment we don't need the extra properties of the content
 * we load them only when required. However, each function which makes use
 * of extra properties should first test if the properties object exist.
 *
 * @since		0.8
 * @package		CMS
 */
class ContentBase
{
	/**
	 * The unique ID identifier of the element
	 * Integer
	 */
	var $mId;

	/**
	 * The name of the element (like a filename)
	 * String
	 */
	 var $mName;

	/**
	 * The type of content (page, url, etc..)
	 * String
	 */
	var $mType;

	/**
	 * The owner of the content
	 * Integer
	 */
	var $mOwner;

	/**
	 * The properties part of the content. This is an object of the good type.
	 * It should contain all treatments specific to this type of content
	 */
	 var $mProperties;

	/**
	 * The ID of the parent, 0 if none
	 * Integer
	 */
	var $mParentId;

	/**
	 * The old parent id... only used on update
	 */
	var $mOldParentId;

	/**
	 * This is used too often to not be part of the base class
	 */
	var $mTemplateId;

	/**
	 * The item order of the content in his level
	 * Integer
	 */
	var $mItemOrder;

	/**
	 * The old item order... only used on update
	 */
	var $mOldItemOrder;

	/**
	 * The full hierarchy of the content
	 * String of the form : 1.4.3
	 */
	var $mHierarchy;

	/**
	 * What should be displayed in a menu
	 */
	var $mMenuText;

	/**
	 * Is the content active ?
	 * Integer : 0 / 1
	 */
	var $mActive;

	/**
	 * Alias of the content
	 */
	var $mAlias;

	var $mOldAlias;

	/**
	 * Cachable?
	 */
	var $mCachable;

	/**
	 * Does this content have a preview function?
	 */
	var $mPreview;

	/**
	 * Should it show up in the menu?
	 */
	var $mShowInMenu;

	/**
	 * Is this page the default?
	 */
	var $mDefaultContent;
	
	/**
	 * What type of markup is ths?  HTML is the default
	 */
	var $mMarkup;

	/**
	 * Last user to modify this content
	 */
	var $mLastModifiedBy;

	/**
	 * Creation date
	 * Date
	 */
	var $mCreationDate;

	/**
	 * Modification date
	 * Date
	 */
	var $mModifiedDate;

	var $mAdditionalEditors;

	/************************************************************************/
	/* Constructor related													*/
	/************************************************************************/

	/**
	 * Generic constructor. Runs the SetInitialValues fuction.
	 */
	function ContentBase()
	{
		$this->SetInitialValues();
		$this->SetProperties();
		$this->mPropertiesLoaded = false;
	}

	/**
	 * Sets object to some sane initial values
	 */
	function SetInitialValues()
	{
		$this->mId				= -1 ;
		$this->mName			= "" ;
		$this->mAlias			= "" ;
		$this->mOldAlias		= "" ;
		$this->mType			= strtolower(get_class($this)) ;
		$this->mOwner			= -1 ;
		$this->mProperties		= new ContentProperties();
		$this->mParentId		= -1 ;
		$this->mOldParentId		= -1 ;
		$this->mTemplateId		= -1 ;
		$this->mItemOrder		= -1 ;
		$this->mOldItemOrder	= -1 ;
		$this->mLastModifiedBy	= -1 ;
		$this->mHierarchy		= "" ;
		$this->mActive			= false ;
		$this->mDefaultContent	= false ;
		$this->mShowInMenu		= false ;
		$this->mCachable		= true;
		$this->mMenuText		= "" ;
		$this->mCreationDate	= "" ;
		$this->mModifiedDate	= "" ;
		$this->mPreview         = false ;
		$this->mMarkup			= 'html' ;
	}

	/**
	 * Subclasses should override this to set their property types using a lot
	 * of mProperties.Add statements
	 */
	function SetProperties()
	{
	}

	/************************************************************************/
	/* Functions giving access to needed elements of the content			*/
	/************************************************************************/

	/**
	 * Returns the ID
	 */
	function Id()
	{
		return $this->mId;
	}

	/**
	 * Returns a friendly name for this content type
	 */
	function FriendlyName()
	{
		return '';
	}

	/**
	 * Returns the Name
	 */
	function Name()
	{
		return $this->mName;
	}

	/**
	 * Returns the Alias
	 */
	function Alias()
	{
		return $this->mAlias;
	}

	/**
	 * Returns the Type
	 */
	function Type()
	{
		return strtolower($this->mType);
	}

	/**
	 * Returns the Owner
	 */
	function Owner()
	{
		return $this->mOwner;
	}

	/**
	 * Returns the ParentId
	 */
	function ParentId()
	{
		return $this->mParentId;
	}

	function TemplateId()
	{
		return $this->mTemplateId;
	}

	/**
	 * Returns the ItemOrder
	 */
	function ItemOrder()
	{
		return $this->mItemOrder;
	}

	/**
	 * Returns the Hierarchy
	 */
	function Hierarchy()
	{
		return ContentManager::CreateFriendlyHierarchyPosition($this->mHierarchy);
	}

	/**
	 * Returns the Active state
	 */
	function Active()
	{
		return $this->mActive;
	}

	/**
	 * Returns whether it should show in the menu
	 */
	function ShowInMenu()
	{
		return $this->mShowInMenu;
	}

	/**
	 * Returns if the page is the default
	 */
	function DefaultContent()
	{
		return $this->mDefaultContent;
	}

	function Cachable()
	{
		return $this->mCachable;
	}
	
	function Markup()
	{
		return $this->mMarkup;
	}

	function LastModifiedBy()
	{
		return $this->mLastModifiedBy;
	}
	
	function SetAlias($alias)
	{
		global $gCms;

		if ($gCms->config['auto_alias_content'] && $alias == '')
		{
			$alias = trim($this->mMenuText);
			$alias = preg_replace("/[_-\W]+/", "_", $alias);
			$alias = trim($alias, '_');
		}

		$this->mAlias = $alias;
	}

	/**
	 * Returns the menu text for this content
	 */
	function MenuText()
	{
		return $this->mMenuText;
	}

	/**
	 * Returns the properties
	 */
	function Properties()
	{
		if ($this->mPropertiesLoaded == false)
		{
			$this->mProperties->Load($this->mId);
			$this->mPropertiesLoaded = true;
		}
		return $this->mProperties;
	}

	function GetPropertyValue($name)
	{
		if ($this->mPropertiesLoaded == false)
		{
			$this->mProperties->Load($this->mId);
			$this->mPropertiesLoaded = true;
		}
		return $this->mProperties->GetValue($name);
	}

	function SetPropertyValue($name, $value)
	{
		if ($this->mPropertiesLoaded == false)
		{
			$this->mProperties->Load($this->mId);
			$this->mPropertiesLoaded = true;
		}
		$this->mProperties->SetValue($name, $value);
	}

	/************************************************************************/
	/* The rest																*/
	/************************************************************************/

	/**
	 * Load the content of the object from an ID
	 *
	 * @param $id				the ID of the element
	 * @param $loadProperties	whether to load or not the properties
	 *
	 * @returns bool			If it fails, the object comes back to initial values and returns FALSE
	 *							If everything goes well, it returns TRUE
	 */
	function LoadFromId($id, $loadProperties = false)
	{
		global $gCms, $config, $sql_queries, $debug_errors;
		$db = &$gCms->db;

		$result = false;

		if (-1 < $id)
		{
			$query		= "SELECT * FROM ".cms_db_prefix()."content WHERE content_id = ?";
			$dbresult	= $db->Execute($query, array($id));

			if ($dbresult && (1 == $dbresult->RowCount()))
			{
				$row = $dbresult->FetchRow();

				$this->mId				= $row["content_id"];
				$this->mName			= $row["content_name"];
				$this->mAlias			= $row["content_alias"];
				$this->mOldAlias		= $row["content_alias"];
				$this->mType			= strtolower($row["type"]);
				$this->mOwner			= $row["owner_id"];
				#$this->mProperties		= new ContentProperties();
				$this->mParentId		= $row["parent_id"];
				$this->mOldParentId		= $row["parent_id"];
				$this->mTemplateId		= $row["template_id"];
				$this->mItemOrder		= $row["item_order"];
				$this->mOldItemOrder	= $row["item_order"];
				$this->mHierarchy		= $row["hierarchy"];
				$this->mMenuText		= $row['menu_text'];
				$this->mMarkup			= $row['markup'];
				$this->mActive			= ($row["active"] == 1?true:false);
				$this->mDefaultContent	= ($row["default_content"] == 1?true:false);
				$this->mShowInMenu		= ($row["show_in_menu"] == 1?true:false);
				$this->mCachable		= ($row["cachable"] == 1?true:false);
				$this->mLastModifiedBy	= $row["last_modified_by"];
				$this->mCreationDate	= $row["create_date"];
				$this->mModifiedDate	= $row["modified_date"];

				$result = true;
			}
			else
			{
				if (true == $config["debug"])
				{
					# :TODO: Translate the error message
					$debug_errors .= "<p>Could not retrieve content from db</p>\n";
				}
			}

			if ($result && $loadProperties)
			{
				$this->mProperties->Load($this->mId);
				$this->mPropertiesLoaded = true;

				if (NULL == $this->mProperties)
				{
					$result = false;

					# debug mode
					if (true == $config["debug"])
					{
						# :TODO: Translate the error message
						$debug_errors .= "<p>Could not load properties for content</p>\n";
					}
				}
			}

			if (false == $result)
			{
				$this->SetInitialValues();
			}
		}
		else
		{
			# debug mode
			if ($config["debug"] == true)
			{
				# :TODO: Translate the error message
				$debug_errors .= "<p>The id wasn't valid : $id</p>\n";
			}
		}

		return $result;
	}

	/**
	 * Load the content of the object from an array
	 *
	 * There is no check on the data provided, because this is the job of
	 * ValidateData
	 *
	 * @returns	bool			If it fails, the object comes back to initial values and returns FALSE
	 *							If everything goes well, it returns TRUE
	 */
	function LoadFromData($data, $loadProperties = false)
	{
		global $config, $debug_errors;

		$result = true;

		$this->mId				= $data["content_id"];
		$this->mName			= $data["content_name"];
		$this->mAlias			= $data["content_alias"];
		$this->mOldAlias		= $data["content_alias"];
		$this->mType			= strtolower($data["type"]);
		$this->mOwner			= $data["owner_id"];
		#$this->mProperties		= new ContentProperties();
		$this->mParentId		= $data["parent_id"];
		$this->mOldParentId		= $data["parent_id"];
		$this->mTemplateId		= $data["template_id"];
		$this->mItemOrder		= $data["item_order"];
		$this->mOldItemOrder	= $data["item_order"];
		$this->mHierarchy		= $data["hierarchy"];
		$this->mMenuText		= $data['menu_text'];
		$this->mMarkuop			= $data['markup'];
		$this->mDefaultContent	= ($data["default_content"] == 1?true:false);
		$this->mActive			= ($data["active"] == 1?true:false);
		$this->mShowInMenu		= ($data["show_in_menu"] == 1?true:false);
		$this->mCachable		= ($data["cachable"] == 1?true:false);
		$this->mLastModifiedBy	= $data["last_modified_by"];
		$this->mCreationDate	= $data["create_date"];
		$this->mModifiedDate	= $data["modified_date"];

		if ($loadProperties)
		{
			#$this->mProperties = ContentManager::LoadPropertiesFromData(strtolower($this->mType), $data);
			$this->mProperties->Load($this->mId);
			$this->mPropertiesLoaded = true;

			if (NULL == $this->mProperties)
			{
				$result = false;

				# debug mode
				if (true == $config["debug"])
				{
					# :TODO: Translate the error message
					$debug_errors .= "<p>Could not load properties for content</p>\n";
				}
			}
		}

		if (false == $result)
		{
			$this->SetInitialValues();
		}

		return $result;
	}

	/**
	 * Save or update the content
	 */
	# :TODO: This function should return something
	function Save()
	{
		if ($this->mPropertiesLoaded == false)
		{
			$this->mProperties->Load($this->mId);
			$this->mPropertiesLoaded = true;
		}
		if (-1 < $this->mId)
		{
			$this->Update();
		}
		else
		{
			$this->Insert();
		}
	}

	/**
	 * Update the content
	 * We can notice, that only a few things are updated
	 * We do not care about hierarchy for example. This is because hierarchy,
	 * order or parents management is the job of the content manager.
	 * Remember that a content is like a file, and a file don't know where it is
	 * on the disk, it only knows its own content. It's the same here.
	 */
	# :TODO: This function should return something
	function Update()
	{
		global $gCms, $config, $sql_queries, $debug_errors;
		$db = &$gCms->db;

		$result = false;

		#Figure out the item_order (if necessary)
		if ($this->mItemOrder < 1)
		{
			$query = "SELECT ".$db->IfNull('max(item_order)','0')." as new_order FROM ".cms_db_prefix()."content WHERE parent_id = ".$this->mParentId;
			$dbresult = $db->Execute($query);

			if ($dbresult && (1 == $dbresult->RowCount()))
			{
				$row = $dbresult->FetchRow();
				if ($row['new_order'] < 1)
				{
					$this->mItemOrder = 1;
				}
				else
				{
					$this->mItemOrder = $row['new_order'] + 1;
				}
			}
		}

		$query = "UPDATE ".cms_db_prefix()."content SET content_name = ?, owner_id = ?, type = ?, template_id = ?, parent_id = ?, active = ?, default_content = ?, show_in_menu = ?, cachable = ?, menu_text = ?, content_alias = ?, modified_date = ?, item_order = ?, markup = ?, last_modified_by = ? WHERE content_id = ?";

		$dbresult = $db->Execute($query, array(
			$this->mName,
			$this->mOwner,
			strtolower($this->mType),
			$this->mTemplateId,
			$this->mParentId,
			($this->mActive==true?1:0),
			($this->mDefaultContent==true?1:0),
			($this->mShowInMenu==true?1:0),
			($this->mCachable==true?1:0),
			$this->mMenuText,
			$this->mAlias,
			$db->DBTimeStamp(time()),
			$this->mItemOrder,
			$this->mMarkup,
			$this->mLastModifiedBy,
			$this->mId
			));

		if (!$dbresult)
		{
			if (true == $config["debug"])
			{
				# :TODO: Translate the error message
				$debug_errors .= "<p>Error updating content</p>\n";
			}
		}

		if ($this->mOldParentId != $this->mParentId)
		{
			#Fix the item_order if necessary
			$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order - 1 WHERE parent_id = ".$this->mOldParentId." AND item_order > ".$this->mOldItemOrder;
			$result = $db->Execute($query);

			$this->mOldParentId = $this->mParentId;
			$this->mOldItemOrder = $this->mItemOrder;
		}

		if (isset($this->mAdditionalEditors))
		{
			$query = "DELETE FROM ".cms_db_prefix()."additional_users WHERE content_id = ".$this->Id();
			$db->Execute($query);

			foreach ($this->mAdditionalEditors as $oneeditor)
			{
				$new_addt_id = $db->GenID(cms_db_prefix()."additional_users_seq");
				$query = "INSERT INTO ".cms_db_prefix()."additional_users (additional_users_id, user_id, content_id) VALUES (?,?,?)";
				$db->Execute($query, array($new_addt_id, $oneeditor, $this->Id()));
			}
		}

		if (NULL != $this->mProperties)
		{
			# :TODO: There might be some error checking there
			$this->mProperties->Save($this->mId);
		}
		else
		{
			if (true == $config["debug"])
			{
				# :TODO: Translate the error message
				$debug_errors .= "<p>Error updating : the content has no properties</p>\n";
			}
		}
	}

	/**
	 * Insert the content in the db
	 */
	# :TODO: This function should return something
	# :TODO: Take care bout hierarchy here, it has no value !
	# :TODO: Figure out proper item_order
	function Insert()
	{
		global $gCms, $config, $sql_queries, $debug_errors;
		$db = &$gCms->db;

		$result = false;

		#Figure out the item_order
		if ($this->mItemOrder < 1)
		{
			$query = "SELECT max(item_order) as new_order FROM ".cms_db_prefix()."content WHERE parent_id = ".$this->mParentId;
			$dbresult = $db->Execute($query);

			if ($dbresult && (1 == $dbresult->RowCount()))
			{
				$row = $dbresult->FetchRow();
				if ($row['new_order'] < 1)
				{
					$this->mItemOrder = 1;
				}
				else
				{
					$this->mItemOrder = $row['new_order'] + 1;
				}
			}
		}

		$newid = $db->GenID(cms_db_prefix()."content_seq");
		$this->mId = $newid;

		$query = "INSERT INTO ".$config["db_prefix"]."content (content_id, content_name, content_alias, type, owner_id, parent_id, template_id, item_order, hierarchy, active, default_content, show_in_menu, cachable, menu_text, markup, last_modified_by, create_date, modified_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

		$dbresult = $db->Execute($query, array(
			$newid,
			$this->mName,
			$this->mAlias,
			strtolower($this->mType),
			$this->mOwner,
			$this->mParentId,
			$this->mTemplateId,
			$this->mItemOrder,
			$this->mHierarchy,
			($this->mActive==true?1:0),
			($this->mDefaultContent==true?1:0),
			($this->mShowInMenu==true?1:0),
			($this->mCachable==true?1:0),
			$this->mMenuText,
			$this->mMarkup,
			$this->mLastModifiedBy,
			$db->DBTimeStamp(time()),
			$db->DBTimeStamp(time())
			));

		if (! $dbresult)
		{
			if ($config["debug"] == true)
			{
				# :TODO: Translate the error message
				$debug_errors .= "<p>Error inserting content</p>\n";
			}
		}

		if (NULL != $this->mProperties)
		{
			# :TODO: There might be some error checking there
			$this->mProperties->Save($newid);
		}
		else
		{
			if (true == $config["debug"])
			{
				# :TODO: Translate the error message
				$debug_errors .= "<p>Error inserting : the content has no properties</p>\n";
			}
		}
		if (isset($this->mAdditionalEditors))
		{
			foreach ($this->mAdditionalEditors as $oneeditor)
			{
				$new_addt_id = $db->GenID(cms_db_prefix()."additional_users_seq");
				$query = "INSERT INTO ".cms_db_prefix()."additional_users (additional_users_id, user_id, content_id) VALUES (?,?,?)";
				$db->Execute($query, array($new_addt_id, $oneeditor, $this->Id()));
			}
		}
	}

	/**
	 * Test if the array given contains valid data for the object
	 * This function is used to check that no compulsory argument
	 * has been forgotten by the user
	 *
	 * We do not check the Id because there can be no Id (new content)
	 * That's up to Save to check this.
	 *
	 * @returns	FALSE if data is ok, and an array of invalid parameters else
	 */
	function ValidateData()
	{
		return FALSE;
	}

	/**
	 * Delete the content
	 */
	# :TODO: This function should return something
	function Delete()
	{
		global $gCms, $config, $sql_queries, $debug_errors;
		$db = &$gCms->db;

		$result = false;

		if (-1 > $this->mId)
		{
			if (true == $config["debug"])
			{
				# :TODO: Translate the error message
				$debug_errors .= "<p>Could not delete content : invalid Id</p>\n";
			}
		}
		else
		{
			$query		= "DELETE FROM ".cms_db_prefix()."content WHERE content_id = ?";
			$dbresult	= $db->Execute($query, array($this->mId));

			if (! $dbresult)
			{
				if (true == $config["debug"])
				{
					# :TODO: Translate the error message
					$debug_errors .= "<p>Error deleting content</p>\n";
				}
			}

			#Fix the item_order if necessary
			$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order - 1 WHERE parent_id = ".$this->ParentId()." AND item_order > ".$this->ItemOrder();
			$result = $db->Execute($query);

			if (NULL != $this->mProperties)
			{
				# :TODO: There might be some error checking there
				$this->mProperties->Delete($this->mId);
			}
			else
			{
				if (true == $config["debug"])
				{
					# :TODO: Translate the error message
					$debug_errors .= "<p>Error deleting : the content has no properties</p>\n";
				}
			}
		}
	}

	/**
	 * Function for the subclass to parse out data for it's parameters (usually from $_POST)
	 */
	function FillParams($params)
	{
	}

	/**
	 * Function for content types to override to set their proper generated URL
	 */
	function GetURL()
	{
	}

	/**
	 * Show the content
	 */
	function Show()
	{
		# :TODO:
		return "<tr><td>Show Not Defined</td></tr>";
	}
	
	/**
	* allow the content module to handle custom tags. Typically used for parameters in {content} tags
	*/
	function ContentPreRender($tpl_source)
	{
		return $tpl_source;
	}

	/**
	 * Returns a list of tab names that should be used when adding or editing this type of content
	 */
	function GetTabDefinitions()
	{
		return array();
	}

	/**
	 * Show the Edit interface
	 */
	function Edit($adding = false, $tab = 0, $showadmin = false)
	{
		# :TODO:
		return "<tr><td>Edit Not Defined</td></tr>";
	}

	/**
	 * Show the Advanced Edit interface
	 */
	function AdvancedEdit($adding = false)
	{
		# :TODO:
		return "<tr><td>Advanced Edit Not Defined</td></tr>";
	}

	/**
	 * Show Help
	 */
	function Help()
	{
		# :TODO:
		return "<tr><td>Help Not Defined</td></tr>";
	}

	/**
	 * Does this have children?
	 */
	function HasChildren()
	{
		global $gCms, $config, $sql_queries, $debug_errors;
		$db = &$gCms->db;

		$result = false;

		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE parent_id = ".$this->mId;
		$dbresult = $db->Execute($query);

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			$result = true;
		}

		return $result;
	}

	function GetAdditionalEditors()
	{
		if (!isset($this->mAdditionalEditors))
		{
			global $gCms;
			$db = &$gCms->db;

			$this->mAdditionalEditors = array();

			$query = "SELECT user_id FROM ".cms_db_prefix()."additional_users WHERE content_id = ".$this->mId;
			$dbresult = $db->Execute($query);

			if ($dbresult && $dbresult->RowCount() > 0)
			{
				while ($row = $dbresult->FetchRow())
				{
					array_push($this->mAdditionalEditors, $row['user_id']);
				}
			}
		}
		return $this->mAdditionalEditors;
	}

	function SetAdditionalEditors($editorarray)
	{
		$this->mAdditionalEditors = $editorarray;
	}

	function ShowAdditionalEditors()
	{
		$text = '';

		$text .= '<tr><th>Additional Editors:</th>';
		$text .= '<td><select name="additional_editors[]" multiple="multiple" size="5">';

		$allusers = UserOperations::LoadUsers();
		$addteditors = $this->GetAdditionalEditors();
		foreach ($allusers as $oneuser)
		{
			if ($oneuser->id != $this->Owner())
			{
				$text .= '<option value="'.$oneuser->id.'"';
				if (in_array($oneuser->id, $addteditors))
				{
					$text .= ' selected="selected"';
				}
				$text .= '>'.$oneuser->username.'</option>';
			}
		}

		$text .= '</select></td></tr>';

		return $text;
	}

	function IsDefaultPossible()
	{
		return FALSE;
	}
}

/**
 * Class to represent content properties.  These are pretty much
 * separate beings that get used by a content object instance.
 *
 * @since		0.8
 * @package		CMS
 */
class ContentProperties
{
	var $mPropertyTypes;
	var $mPropertyValues;

	/**
	 * Generic constructor. Runs the SetInitialValues fuction.
	 */
	function ContentProperties()
	{
		$this->SetInitialValues();
	}

	/**
	 * Sets object to some sane initial values
	 */
	function SetInitialValues()
	{
		$this->mPropertyTypes = array();
		$this->mPropertyValues = array();
	}

	function Add($type, $name, $defaultvalue='')
	{
		if (!array_key_exists($name, $this->mPropertyValues))
		{
			$this->mPropertyTypes[$name] = $type;
			$this->mPropertyValues[$name] = $defaultvalue;
		}
	}

	function GetValue($name)
	{
		if (count($this->mPropertyValues) > 0)
		{
			if (array_key_exists($name, $this->mPropertyValues))
			{
				return $this->mPropertyValues[$name];
			}
		}
	}

	function SetValue($name, $value)
	{
		if (count($this->mPropertyValues) > 0)
		{
			if (array_key_exists($name, $this->mPropertyValues))
			{
				$this->mPropertyValues[$name] = $value;
			}
		}
	}

	function Load($content_id)
	{
		if (count($this->mPropertyValues) > 0)
		{
			global $gCms, $config, $sql_queries, $debug_errors;
			$db = &$gCms->db;

			$query		= "SELECT * FROM ".cms_db_prefix()."content_props WHERE content_id = ?";
			$dbresult	= $db->Execute($query, array($content_id));

			if ($dbresult && $dbresult->RowCount() > 0)
			{
				while ($row = $dbresult->FetchRow())
				{
					$prop_name = $row['prop_name'];
					if (isset($this->mPropertyValues[$prop_name]))
					{
						$this->mPropertyTypes[$prop_name] = $row['type'];
						$this->mPropertyValues[$prop_name] = $row['content'];
					}
				}
			}
		}
	}

	function Save($content_id)
	{
		if (count($this->mPropertyValues) > 0)
		{
			global $gCms, $config, $sql_queries, $debug_errors;
			$db = &$gCms->db;

			$query = "DELETE FROM ".cms_db_prefix()."content_props WHERE content_id = ?";
			$dbresult = $db->Execute($query, array($content_id));

			foreach ($this->mPropertyValues as $key=>$value)
			{
				$query = "INSERT INTO ".cms_db_prefix()."content_props (content_id, type, prop_name, param1, param2, param3, content) VALUES (?,?,?,?,?,?,?)";

				$dbresult = $db->Execute($query, array(
					$content_id,
					$this->mPropertyTypes[$key],
					$key,
					'',
					'',
					'',
					$this->mPropertyValues[$key],
					));

				# debug mode
				if (true == $config["debug"])
				{
					$sql_queries .= "<p>$query</p>\n";
				}

				if (! $dbresult)
				{
					if (true == $config["debug"])
					{
						# :TODO: Translate the error message
						$debug_errors .= "<p>Error updating content property</p>\n";
					}
				}
			}
		}
	}

	function Delete($content_id)
	{
		if (count($this->mPropertyValues) > 0)
		{
			global $gCms, $config, $sql_queries, $debug_errors;
			$db = &$gCms->db;

			$query = "DELETE FROM ".cms_db_prefix()."content_props WHERE content_id = ?";
			$db->Execute($query, array($content_id));
		}
	}
}

/**
 * Class for static methods related to content
 *
 * @since		0.8
 * @package		CMS
 */
class ContentManager
{
	/**
	 * Determine proper type of object, load it and return it
	 */
	function LoadContentFromId($id,$loadprops=true)
	{
		global $gCms;
		$db = &$gCms->db;

		$query = "SELECT * FROM ".cms_db_prefix()."content WHERE content_id = ?";
		$dbresult = $db->Execute($query, array($id));
		if ($dbresult && $dbresult->RowCount() > 0)
		{
			$row = $dbresult->FetchRow();

			#Make sure the type exists.  If so, instantiate and load
			if (in_array($row['type'], array_keys(@ContentManager::ListContentTypes())))
			{
				$classtype = strtolower($row['type']);
				$contentobj = new $classtype; 
				$contentobj->LoadFromData($row,$loadprops);
				return $contentobj;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function LoadContentFromAlias($alias, $only_active = false)
	{
		global $gCms;
		$db = &$gCms->db;

		$dbresult = '';

		if (is_numeric($alias) && strpos($alias,'.') === FALSE && strpos($alias,',') === FALSE) //Fix for postgres
		{
			$query = "SELECT * FROM ".cms_db_prefix()."content WHERE content_id = ? OR content_alias = ?";
			if ($only_active == true)
			{
				$query .= " AND active = 1";
			}
			$dbresult = $db->Execute($query, array($alias,$alias));
		}
		else
		{
			$query = "SELECT * FROM ".cms_db_prefix()."content WHERE content_alias = ?";
			if ($only_active == true)
			{
				$query .= " AND active = 1";
			}
			$dbresult = $db->Execute($query, array($alias));
		}

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			$row = $dbresult->FetchRow();

			#Make sure the type exists.  If so, instantiate and load
			if (in_array($row['type'], array_keys(@ContentManager::ListContentTypes())))
			{
				$classtype = strtolower($row['type']);
				$contentobj = new $classtype;
				$contentobj->LoadFromData($row,true);
				return $contentobj;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Display content
	 */
	function DisplayContent($content)
	{
		//This should be straight forward, since the content will pretty much determine how it is displayed
		$content->Show();
	}

	/**
	 * Determine if content should be loaded from cache
	 */
	function IsCached($id)
	{
	}

	function GetDefaultContent()
	{
		global $gCms;
		$db = &$gCms->db;

		$result = -1;

		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE default_content = 1";
		$dbresult = $db->Execute($query);
		if ($dbresult && $dbresult->RowCount() > 0)
		{
			$row = $dbresult->FetchRow();
			$result = $row['content_id'];
		}
		else
		{
			#Just get something...
			$query = "SELECT content_id FROM ".cms_db_prefix()."content";
			$dbresult = $db->Execute($query);
			if ($dbresult && $dbresult->RowCount() > 0)
			{
				$row = $dbresult->FetchRow();
				$result = $row['content_id'];
			}
		}

		return $result;
	}

	/**
	 * Returns a hash of valid content types (classes that extend ContentBase)
	 * The key is the name of the class that would be saved into the dabase.  The
	 * value would be the text returned by the type's FriendlyName() method.
	 */
	function ListContentTypes()
	{
		$result = array();

		foreach (get_declared_classes() as $oneclass)
		{
			if (strtolower(get_parent_class($oneclass)) == 'contentbase' || strtolower(get_parent_class($oneclass)) == 'cmsmodulecontenttype')
			{
				if (strtolower($oneclass) != 'cmsmodulecontenttype')
				{
					#array_push($result, strtolower($oneclass));
					$tmpobj = new $oneclass;
					$result[strtolower($oneclass)] = $tmpobj->FriendlyName();
				}
			}
		}

		return $result;
	}

	/**
	 * Updates the hierarchy position of one item
	 */
	function SetHierarchyPosition($contentid)
	{
		global $gCms;
		$db = $gCms->db;

		$current_hierarchy_position = "";
		$current_parent_id = $contentid;
		$count = 0;

		while ($current_parent_id > -1)
		{
			$query = "SELECT item_order, parent_id FROM ".cms_db_prefix()."content WHERE content_id = ?";
			$dbresult = $db->Execute($query, array($current_parent_id));
			if ($dbresult && $dbresult->RowCount())
			{
				$row = $dbresult->FetchRow();
				$current_hierarchy_position = str_pad($row['item_order'], 5, '0', STR_PAD_LEFT) . "." . $current_hierarchy_position;
				$current_parent_id = $row['parent_id'];
				$count++;
			}
			else
			{
				$current_parent_id = 0;
			}
		}

		if (strlen($current_hierarchy_position) > 0)
		{
			$current_hierarchy_position = substr($current_hierarchy_position, 0, strlen($current_hierarchy_position) - 1);
		}

		$query = "UPDATE ".cms_db_prefix()."content SET hierarchy = ? WHERE content_id = ?";
		$db->Execute($query, array($current_hierarchy_position, $contentid));
	}

	/**
	 * Updates the hierarchy position of all items
	 */
	function SetAllHierarchyPositions()
	{
		global $gCms;
		$db = $gCms->db;

		$query = "SELECT content_id FROM ".cms_db_prefix()."content";
		$dbresult = $db->Execute($query);

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			while ($row = $dbresult->FetchRow())
			{
				ContentManager::SetHierarchyPosition($row['content_id']);
			}
		}
	}

	function GetAllContent($loadprops=true)
	{
		global $gCms;
		$db = $gCms->db;

		$result = array();

		$query = "SELECT * FROM ".cms_db_prefix()."content ORDER BY hierarchy";
		$dbresult = $db->Execute($query);

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			while ($row = $dbresult->FetchRow())
			{
				#Make sure the type exists.  If so, instantiate and load
				if (in_array($row['type'], array_keys(@ContentManager::ListContentTypes())))
				{
					$contentobj = new $row['type'];
					$contentobj->LoadFromData($row, $loadprops);
					array_push($result, $contentobj);
				}
			}
		}

		return $result;
	}

	function CreateHierarchyDropdown($current = '', $parent = '', $name = 'parent_id')
	{
		$result = '';

		$allcontent = ContentManager::GetAllContent(false);

		if ($allcontent !== FALSE && count($allcontent) > 0)
		{
			$result .= '<select name="'.$name.'">';
			$result .= '<option value="-1">None</option>';

			foreach ($allcontent as $one)
			{
				#Don't include ourselves or separators
				if ($one->Id() != $current && $one->Type() != 'separator')
				{
					$result .= '<option value="'.$one->Id().'"';

					#Select current parent if it exists
					if ($one->Id() == $parent)
					{
						$result .= ' selected="true"';
					}

					$result .= '>'.$one->Hierarchy().'. - '.$one->Name().'</option>';
				}
			}

			$result .= '</select>';
		}

		return $result;
	}
	
	function CheckAliasError($alias, $content_id = -1)
	{
		global $gCms;
		$db = &$gCms->db;

		$error = FALSE;

		if (preg_match('/^\d+$/', $alias))
		{
			$error = lang('aliasnotaninteger');
		}
		else if (!preg_match('/^[\-\_\w]+$/', $alias))
		{
			$error = lang('aliasmustbelettersandnumbers');
		}
		else
		{
			$params = array($alias);
			$query = "SELECT * FROM ".cms_db_prefix()."content WHERE content_alias = ?";
			if ($content_id > -1)
			{
				$query = " AND content_id != ?";
				array_push($params, $content_id);
			}
			$dbresult = $db->Execute($query, $params);
	
			if ($dbresult && $dbresult->RowCount() > 0)
			{
				$error = lang('aliasalreadyused');
			}
		}

		return $error;
	}

	function CreateFriendlyHierarchyPosition($position)
	{
		#Change padded numbers back into user-friendly values
		$tmp = '';
		$levels = split('\.', $position);
		foreach ($levels as $onelevel)
		{
			$tmp .= ltrim($onelevel, '0') . '.';
		}
		$tmp = rtrim($tmp, '.');
		return $tmp;
	}
}

# vim:ts=4 sw=4 noet
?>
