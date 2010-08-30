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
 * @package CMS
 */

/**
 * @ignore
 */
define('CMS_CONTENT_HIDDEN_NAME','--------');

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
     
     var $mPropertiesLoaded;

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
     * The metadata (head tags) fir this content
     */
    var $mMetadata;

    var $mTitleAttribute;
    var $mAccessKey;
    var $mTabIndex;

    /**
     * The full hierarchy of the content
     * String of the form : 1.4.3
     */
    var $mHierarchy;

    /**
     * The full hierarchy of the content ids
     * String of the form : 1.4.3
     */
    var $mIdHierarchy;

    /**
     * The full path through the hierarchy
     * String of the form : parent/parent/child
     */
    var $mHierarchyPath;

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
     * Secure?
     */
    protected $mSecure;

    /**
     * URL
     */
    protected $mURL;

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
	
    /*
     * state or meta information
     */
    var $mReadyForEdit;
    var $_attributes;
    var $_prop_defaults;
    var $_add_mode;
    var $_error;

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
	$this->mReadyForEdit = false;
    }

    /**
     * Sets object to some sane initial values
     */
    function SetInitialValues()
    {
	$this->mId             = -1 ;
	$this->mName           = "" ;
	$this->mAlias          = "" ;
	$this->mOldAlias       = "" ;
	$this->mType           = strtolower(get_class($this)) ;
	$this->mOwner          = -1 ;
	$this->mProperties     = new ContentProperties();
	$this->mParentId       = -2 ;
	$this->mOldParentId    = -1 ;
	$this->mTemplateId     = -1 ;
	$this->mItemOrder      = -1 ;
	$this->mOldItemOrder   = -1 ;
	$this->mLastModifiedBy = -1 ;
	$this->mHierarchy      = "" ;
	$this->mIdHierarchy    = "" ;
	$this->mHierarchyPath  = "" ;
	$this->mMetadata       = "" ;
	$this->mTitleAttribute = "" ;
	$this->mAccessKey      = "" ;
	$this->mTabIndex       = "" ;
	$this->mActive         = false ;
	$this->mDefaultContent = false ;
	$this->mShowInMenu     = false ;
	$this->mCachable       = true;
	$this->mSecure         = 0;
	$this->mMenuText       = "" ;
	$this->mCreationDate   = "" ;
	$this->mModifiedDate   = "" ;
	$this->mPreview        = false ;
	$this->mMarkup         = 'html' ;
	$this->mChildCount     = 0;
	$this->mPropertiesLoaded = false;
        $this->mURL = '';
    }

    /**
     * Subclasses should override this to set their property types using a lot
     * of mProperties.Add statements
     */
    function SetProperties()
    {
      $this->AddContentProperty('target',10);
      $this->AddBaseProperty('title',1,1);
      $this->AddBaseProperty('menutext',2,1);
      $this->AddBaseProperty('page_url',3);
      $this->AddBaseProperty('parent',4,1);
      $this->AddBaseProperty('active',5);
      $this->AddBaseProperty('showinmenu',5);
      $this->AddBaseProperty('secure',6);
      $this->AddBaseProperty('cachable',7);
      $this->AddBaseProperty('alias',10);
      $this->AddBaseProperty('titleattribute',55);
      $this->AddBaseProperty('accesskey',56);
      $this->AddBaseProperty('tabindex',57);
      $this->AddBaseProperty('owner',90);
      $this->AddBaseProperty('additionaleditors',91);

      $this->AddContentProperty('image',50);
      $this->AddContentProperty('thumbnail',50);
      $this->AddContentProperty('extra1',80);
      $this->AddContentProperty('extra2',81);
      $this->AddContentProperty('extra3',82);
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

    function SetId($id)
    {
	$this->DoReadyForEdit();
	$this->mId = $id;
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

    function SetName($name)
    {
	$this->DoReadyForEdit();
	$this->mName = $name;
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

    function SetType($type)
    {
	$this->DoReadyForEdit();
	$this->mType = strtolower($type);
    }

    /**
     * Returns the Owner
     */
    function Owner()
    {
	return $this->mOwner;
    }

    function SetOwner($owner)
    {
	$this->DoReadyForEdit();
	$this->mOwner = $owner;
    }

    /**
     * Returns the Metadata
     */
    function Metadata()
    {
	return $this->mMetadata;
    }

    /**
     * Content object handles the alias
     */
    function HandlesAlias()
    {
      return false;
    }

    function SetMetadata($metadata)
    {
	$this->DoReadyForEdit();
	$this->mMetadata = $metadata;
    }

    function TabIndex()
    {
	return $this->mTabIndex;
    }

    function SetTabIndex($tabindex)
    {
	$this->DoReadyForEdit();
	$this->mTabIndex = $tabindex;
    }

    function TitleAttribute()
    {
	return $this->mTitleAttribute;
    }

    function GetCreationDate()
    {
	return $this->mCreationDate;
    }
	
    function GetModifiedDate()
    {
	return $this->mModifiedDate;
    }

    function SetTitleAttribute($titleattribute)
    {
	$this->DoReadyForEdit();
	$this->mTitleAttribute = $titleattribute;
    }

    function AccessKey()
    {
	return $this->mAccessKey;
    }

    function SetAccessKey($accesskey)
    {
	$this->DoReadyForEdit();
	$this->mAccessKey = $accesskey;
    }

    /**
     * Returns the ParentId
     */
    function ParentId()
    {
	return $this->mParentId;
    }

    function SetParentId($parentid)
    {
	$this->DoReadyForEdit();
	$this->mParentId = $parentid;
    }

    function OldParentId()
    {
	return $this->mOldParentId;
    }

    function SetOldParentId($parentid)
    {
	$this->DoReadyForEdit();
	$this->mOldParentId = $parentid;
    }

    function TemplateId()
    {
	return $this->mTemplateId;
    }

    function SetTemplateId($templateid)
    {
	$this->DoReadyForEdit();
	$this->mTemplateId = $templateid;
    }

    /**
     * Returns the ItemOrder
     */
    function ItemOrder()
    {
	return $this->mItemOrder;
    }

    function SetItemOrder($itemorder)
    {
	$this->DoReadyForEdit();
	$this->mItemOrder = $itemorder;
    }

    function OldItemOrder()
    {
	return $this->mOldItemOrder;
    }

    function SetOldItemOrder($itemorder)
    {
	$this->DoReadyForEdit();
	$this->mOldItemOrder = $itemorder;
    }

    /**
     * Returns the Hierarchy
     */
    function Hierarchy()
    {
		global $gCms;
		$contentops =& $gCms->GetContentOperations();
		return $contentops->CreateFriendlyHierarchyPosition($this->mHierarchy);
    }

    function SetHierarchy($hierarchy)
    {
	$this->DoReadyForEdit();
	$this->mHierarchy = $hierarchy;
    }

    /**
     * Returns the Hierarchy
     */
    function IdHierarchy()
    {
	return $this->mIdHierarchy;
    }


    function SetIdHierarchy($idhierarchy)
    {
	$this->DoReadyForEdit();
	$this->mIdHierarchy = $idhierarchy;
    }


    /**
     * Returns the Hierarchy
     */
    function HierarchyPath()
    {
	return $this->mHierarchyPath;
    }


    function SetHierarchyPath($hierarchypath)
    {
	$this->DoReadyForEdit();
	$this->mHierarchyPath = $hierarchypath;
    }


    /**
     * Returns the Active state
     */
    function Active()
    {
	return $this->mActive;
    }


    function SetActive($active)
    {
	$this->DoReadyForEdit();
	$this->mActive = $active;
    }


    /**
     * Returns whether it should show in the menu
     */
    function ShowInMenu()
    {
	return $this->mShowInMenu;
    }


    function SetShowInMenu($showinmenu)
    {
        $this->DoReadyForEdit();
	$this->mShowInMenu = $showinmenu;
    }


    /**
     * Returns if the page is the default
     */
    function DefaultContent()
    {
	return $this->mDefaultContent;
    }


    function SetDefaultContent($defaultcontent)
    {
	$this->DoReadyForEdit();
	$this->mDefaultContent = $defaultcontent;
    }


    function Cachable()
    {
	return $this->mCachable;
    }


    function SetCachable($cachable)
    {
	$this->DoReadyForEdit();
	$this->mCachable = $cachable;
    }


    function Secure()
    {
      return $this->mSecure;
    }

    function SetSecure($secure)
    {
	$this->DoReadyForEdit();
	$this->mSecure = $secure;
    }
	
    public function URL()
    {
      return $this->mURL;
    }

    public function SetURL($url)
    {
      $this->DoReadyForEdit();
      $this->mURL = $url;
    }

	function Markup()
	{
		return $this->mMarkup;
	}

	function SetMarkup($markup)
	{
		$this->DoReadyForEdit();
		$this->mMarkup = $markup;
	}

	function LastModifiedBy()
	{
		return $this->mLastModifiedBy;
	}

	function SetLastModifiedBy($lastmodifiedby)
	{
		$this->DoReadyForEdit();
		$this->mLastModifiedBy = $lastmodifiedby;
	}

	function RequiresAlias()
	{
	  return TRUE;
	}

	function IsViewable()
	{
	  return TRUE;
	}

	function SetAlias($alias, $doAutoAliasIfEnabled = true)
	{
		$this->DoReadyForEdit();
		global $gCms;
		$config =& $gCms->GetConfig();

		$tolower = false;

		if ($alias == '' && $doAutoAliasIfEnabled && $config['auto_alias_content'] == true)
		{
			$alias = trim($this->mMenuText);
			if ($alias == '')
			{
			    $alias = trim($this->mName);
			}
			
			$tolower = true;
			$alias = munge_string_to_url($alias, $tolower);
// 			if( empty($alias) )
// 			  {
// 			    // calguy: quick hack so that the alias is never empty...
// 			    $alias = 'alias';
// 			  }
			// Make sure auto-generated new alias is not already in use on a different page, if it does, add "-2" to the alias
			$contentops =& $gCms->GetContentOperations();
			$error = $contentops->CheckAliasError($alias);
			if ($error !== FALSE)
			{
				if (FALSE == empty($alias))
				{
					$alias_num_add = 2;
					// If a '-2' version of the alias already exists
					// Check the '-3' version etc.
					while ($contentops->CheckAliasError($alias.'-'.$alias_num_add) !== FALSE)
					{
						$alias_num_add++;
					}
					$alias .= '-'.$alias_num_add;
				}
				else
				{
					$alias = '';
				}
			}
		}

		$this->mAlias = munge_string_to_url($alias, $tolower);
	} 
	
    /**
     * Returns the menu text for this content
     */
	function MenuText()
	{
		return $this->mMenuText;
	}

	function SetMenuText($menutext)
	{
		$this->DoReadyForEdit();
		$this->mMenuText = $menutext;
	}

    /**
     * Returns number of immediate child-content items of this content
     */
	function ChildCount()
	{
		return $this->mChildCount;
	}

    /**
     * Returns the properties
     */
	function Properties()
	{
		debug_buffer('properties called');
		if ($this->mPropertiesLoaded == false)
		{
			$this->mProperties->Load($this->mId);
			$this->mPropertiesLoaded = true;
		}
		return $this->mProperties;
	}

	function HasProperty($name)
	{
		return $this->mProperties->HasProperty($name);
	}

	function GetPropertyValue($name)
	{
		if ($this->mProperties->HasProperty($name))
		{
			if ($this->mPropertiesLoaded == false)
			{
				$this->mProperties->Load($this->mId);
				$this->mPropertiesLoaded = true;
			}
			return $this->mProperties->GetValue($name);
		}
		return '';
	}
    
	function SetPropertyValue($name, $value)
	{
		debug_buffer('setpropertyvalue called');
		$this->DoReadyForEdit();
		if ($this->mPropertiesLoaded == false)
		{
			$this->mProperties->Load($this->mId);
			$this->mPropertiesLoaded = true;
		}
		$this->mProperties->SetValue($name, $value);
	}
	
    /**
     * Function content types to use to say whether or not they should show
     * up in lists where parents of content are set.  This will default to true,
     * but should be used in cases like Separator where you don't want it to 
     * have any children.
     * 
     * @since 0.11
     */
    function WantsChildren()
    {
	return true;
    }

    /**
     * Should this link be used in various places where a link is the only
     * useful output?  (Like next/previous links in cms_selflink, for example)
     */
    function HasUsableLink()
    {
	return true;
    }

    /**
     * Is this content type copyable ?
     */
    function IsCopyable()
    {
      return FALSE;
    }

	function IsSystemPage()
	{
		return FALSE;
	}

    /************************************************************************/
    /* The rest																*/
    /************************************************************************/

    /**
     * This is a callback function to handle any things that might need to be done before content is
     * edited.
     */
    function ReadyForEdit()
    {
    }

	function DoReadyForEdit()
	{
		if ($this->mReadyForEdit == false)
		{
			$this->ReadyForEdit();
			$this->mReadyForEdit = true;
		}
	}

    /**
     * Load the content of the object from an ID
     *
     * @param $id		the ID of the element
     * @param $loadProperties	whether to load or not the properties
     *
     * @returns bool		If it fails, the object comes back to initial values and returns FALSE
     *				If everything goes well, it returns TRUE
     */
	function LoadFromId($id, $loadProperties = false)
	{
		global $gCms, $sql_queries, $debug_errors;
		$db = &$gCms->GetDb();
		$config =& $gCms->GetConfig();

		$result = false;

		if (-1 < $id)
		{
			$query = "SELECT * FROM ".cms_db_prefix()."content WHERE content_id = ?";
			$row =& $db->Execute($query, array($id));

			if ($row && !$row->EOF)
			{
				$this->mId                         = $row->fields["content_id"];
				$this->mName                       = $row->fields["content_name"];
				$this->mAlias                      = $row->fields["content_alias"];
				$this->mOldAlias                   = $row->fields["content_alias"];
				$this->mType                       = strtolower($row->fields["type"]);
				$this->mOwner                      = $row->fields["owner_id"];
				#$this->mProperties                = new ContentProperties();
				$this->mParentId                   = $row->fields["parent_id"];
				$this->mOldParentId                = $row->fields["parent_id"];
				$this->mTemplateId                 = $row->fields["template_id"];
				$this->mItemOrder                  = $row->fields["item_order"];
				$this->mOldItemOrder               = $row->fields["item_order"];
				$this->mMetadata                   = $row->fields['metadata'];
				$this->mHierarchy                  = $row->fields["hierarchy"];
				$this->mIdHierarchy                = $row->fields["id_hierarchy"];
				$this->mHierarchyPath              = $row->fields["hierarchy_path"];
				$this->mProperties->mPropertyNames = explode(',',$row->fields["prop_names"]);
				$this->mMenuText                   = $row->fields['menu_text'];
				$this->mMarkup                     = $row->fields['markup'];
				$this->mTitleAttribute             = $row->fields['titleattribute'];
				$this->mAccessKey                  = $row->fields['accesskey'];
				$this->mTabIndex                   = $row->fields['tabindex'];
				$this->mActive                     = ($row->fields["active"] == 1          ? true : false);
				$this->mDefaultContent             = ($row->fields["default_content"] == 1 ? true : false);
				$this->mShowInMenu                 = ($row->fields["show_in_menu"] == 1    ? true : false);
				$this->mCachable                   = ($row->fields["cachable"] == 1        ? true : false);
				$this->mSecure                     = $row->fields['secure'];
				$this->mURL                        = $row->fields['page_url'];
				$this->mLastModifiedBy             = $row->fields["last_modified_by"];
				$this->mCreationDate               = $row->fields["create_date"];
				$this->mModifiedDate               = $row->fields["modified_date"];

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

			if ($row) $row->Close();

			if ($result && $loadProperties)
			{
				if ($this->mPropertiesLoaded == false)
				{
					debug_buffer("load from id is loading properties");
					$this->mProperties->Load($this->mId);
					$this->mPropertiesLoaded = true;
				}

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

		$this->Load();

		return $result;
	}

    /**
     * Load the content of the object from an array
     *
     * There is no check on the data provided, because this is the job of
     * ValidateData
     *
     * @returns	bool		If it fails, the object comes back to initial values and returns FALSE
     *				If everything goes well, it returns TRUE
     */
	function LoadFromData(&$data, $loadProperties = false)
	{
	  global $gCms, $debug_errors;
	  $config =& $gCms->GetConfig();

		$result = true;

		$this->mId                         = $data["content_id"];
		$this->mName                       = $data["content_name"];
		$this->mAlias                      = $data["content_alias"];
		$this->mOldAlias                   = $data["content_alias"];
		$this->mType                       = strtolower($data["type"]);
		$this->mOwner                      = $data["owner_id"];
		#$this->mProperties                = new ContentProperties();
		$this->mParentId                   = $data["parent_id"];
		$this->mOldParentId                = $data["parent_id"];
		$this->mTemplateId                 = $data["template_id"];
		$this->mItemOrder                  = $data["item_order"];
		$this->mOldItemOrder               = $data["item_order"];
		$this->mMetadata                   = $data['metadata'];
		$this->mHierarchy                  = $data["hierarchy"];
		$this->mIdHierarchy                = $data["id_hierarchy"];
		$this->mHierarchyPath              = $data["hierarchy_path"];
		$this->mProperties->mPropertyNames = explode(',',$data["prop_names"]);
		$this->mMenuText                   = $data['menu_text'];
		$this->mMarkup                     = $data['markup'];
		$this->mTitleAttribute             = $data['titleattribute'];
		$this->mAccessKey                  = $data['accesskey'];
		$this->mTabIndex                   = $data['tabindex'];
		$this->mDefaultContent             = ($data["default_content"] == 1 ? true : false);
		$this->mActive                     = ($data["active"] == 1          ? true : false);
		$this->mShowInMenu                 = ($data["show_in_menu"] == 1    ? true : false);
		$this->mCachable                   = ($data["cachable"] == 1        ? true : false);
		if( isset($data['secure']) )
		  $this->mSecure                   = $data["secure"];
		if( isset($data['page_url']) )
		  $this->mURL                      = $data["page_url"];
		$this->mLastModifiedBy             = $data["last_modified_by"];
		$this->mCreationDate               = $data["create_date"];
		$this->mModifiedDate               = $data["modified_date"];

		if ($loadProperties == true)
		{
			#$this->mProperties = ContentManager::LoadPropertiesFromData(strtolower($this->mType), $data);
			if ($this->mPropertiesLoaded == false)
			{
				debug_buffer("load from data is loading properties");
				$this->mProperties->Load($this->mId);
				$this->mPropertiesLoaded = true;
			}

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

		$this->Load();

		return $result;
	}

    /**
     * Callback function for content types to use to preload content or other things if necessary.  This
     * is called right after the properties are loaded.
     */
    function Load()
    {
    }

    /**
     * Save or update the content
     */
    # :TODO: This function should return something
	function Save()
	{
		global $gCms;
		foreach($gCms->modules as $key=>$value)
		{
			if ($gCms->modules[$key]['installed'] == true &&
			$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->ContentEditPre($this);
			}
		}

		Events::SendEvent('Core', 'ContentEditPre', array('content' => &$this));

		if ($this->mPropertiesLoaded == false)
		{
			debug_buffer('save is loading properties');
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

		foreach($gCms->modules as $key=>$value)
		{		
			if ($gCms->modules[$key]['installed'] == true &&
			$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->ContentEditPost($this);
			}
		}

		Events::SendEvent('Core', 'ContentEditPost', array('content' => &$this));
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
		global $gCms, $sql_queries, $debug_errors;
		$db = &$gCms->GetDb();
		$config =& $gCms->GetConfig();

		$result = false;

		#Figure out the item_order (if necessary)
		if ($this->mItemOrder < 1)
		{
			$query = "SELECT ".$db->IfNull('max(item_order)','0')." as new_order FROM ".cms_db_prefix()."content WHERE parent_id = ?";
			$row = &$db->GetRow($query,array($this->mParentId));

			if ($row)
			{
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

		$this->mModifiedDate = trim($db->DBTimeStamp(time()), "'");

		$query = "UPDATE ".cms_db_prefix()."content SET content_name = ?, owner_id = ?, type = ?, template_id = ?, parent_id = ?, active = ?, default_content = ?, show_in_menu = ?, cachable = ?, secure = ?, page_url = ?, menu_text = ?, content_alias = ?, metadata = ?, titleattribute = ?, accesskey = ?, tabindex = ?, modified_date = ?, item_order = ?, markup = ?, last_modified_by = ? WHERE content_id = ?";
		$dbresult = $db->Execute($query, array(
			$this->mName,
			$this->mOwner,
			strtolower($this->mType),
			$this->mTemplateId,
			$this->mParentId,
			($this->mActive == true         ? 1 : 0),
			($this->mDefaultContent == true ? 1 : 0),
			($this->mShowInMenu == true     ? 1 : 0),
			($this->mCachable == true       ? 1 : 0),
			$this->mSecure,
			$this->mURL,
			$this->mMenuText,
			$this->mAlias,
			$this->mMetadata,
			$this->mTitleAttribute,
			$this->mAccessKey,
			$this->mTabIndex,
			$this->mModifiedDate,
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
			$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order - 1 WHERE parent_id = ? AND item_order > ?";
			$result = $db->Execute($query, array($this->mOldParentId,$this->mOldItemOrder));

			$this->mOldParentId = $this->mParentId;
			$this->mOldItemOrder = $this->mItemOrder;
		}

		if (isset($this->mAdditionalEditors))
		{
			$query = "DELETE FROM ".cms_db_prefix()."additional_users WHERE content_id = ?";
			$db->Execute($query, array($this->Id()));

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
			debug_buffer('save from ' . __LINE__);
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
		global $gCms, $sql_queries, $debug_errors;
		$db = &$gCms->GetDb();
		$config =& $gCms->GetConfig();

		$result = false;

		#Figure out the item_order
		if ($this->mItemOrder < 1)
		{
			$query = "SELECT max(item_order) as new_order FROM ".cms_db_prefix()."content WHERE parent_id = ?";
			$row = &$db->Getrow($query, array($this->mParentId));

			if ($row)
			{
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

		$this->mModifiedDate = $this->mCreationDate = trim($db->DBTimeStamp(time()), "'");

		$query = "INSERT INTO ".$config["db_prefix"]."content (content_id, content_name, content_alias, type, owner_id, parent_id, template_id, item_order, hierarchy, id_hierarchy, active, default_content, show_in_menu, cachable, secure, page_url, menu_text, markup, metadata, titleattribute, accesskey, tabindex, last_modified_by, create_date, modified_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

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
			$this->mIdHierarchy,
			($this->mActive == true         ? 1 : 0),
			($this->mDefaultContent == true ? 1 : 0),
			($this->mShowInMenu == true     ? 1 : 0),
			($this->mCachable == true       ? 1 : 0),
			$this->mSecure,
			$this->mURL,
			$this->mMenuText,
			$this->mMarkup,
			$this->mMetadata,
			$this->mTitleAttribute,
			$this->mAccessKey,
			$this->mTabIndex,
			$this->mLastModifiedBy,
			$this->mModifiedDate,
			$this->mCreationDate
			));

		if (! $dbresult)
		{
		  die($db->sql.'<br/>'.$db->ErrorMsg);
			if ($config["debug"] == true)
			{
				# :TODO: Translate the error message
				$debug_errors .= "<p>Error inserting content</p>\n";
			}
		}

		if (NULL != $this->mProperties)
		{
			# :TODO: There might be some error checking there
			debug_buffer('save from ' . __LINE__);
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
	  $errors = array();

	  if ($this->mParentId < -1) 
	    {
	      $errors[] = lang('invalidparent');
	      $result = false;
	    }
	  
	  if ($this->mName == '')
	    {
	      if ($this->mMenuText != '')
		{
		  $this->mName = $this->mMenuText;
		}
	      else
		{
		  $errors[]= lang('nofieldgiven',array(lang('title')));
		  $result = false;
		}
	    }
	  
	  if ($this->mMenuText == '')
	    {
	      if ($this->mName != '')
		{
		  $this->mMenuText = $this->mName;
		}
	      else
		{
		  $errors[]=lang('nofieldgiven',array(lang('menutext')));
		  $result = false;
		}
	    }
		
	  if (!$this->HandlesAlias())
	    {
	      if ($this->mAlias != $this->mOldAlias || ($this->mAlias == '' && $this->RequiresAlias()) ) 
		{
		  global $gCms;
		  $contentops =& $gCms->GetContentOperations();
		  $error = $contentops->CheckAliasError($this->mAlias, $this->mId);
		  if ($error !== FALSE)
		    {
		      $errors[]= $error;
		      $result = false;
		    }
		}
	    }

	  $auto_type = content_assistant::auto_create_url();
	  if( $this->mURL == '' && get_site_preference('content_autocreate_urls') )
	    {
	      // create a valid url.
	      if( !$this->DefaultContent() )
		{
		  if( get_site_preference('content_autocreate_flaturls',0) )
		    {
		      // the default url is the alias... but not synced to the alias.
		      $this->mURL = $this->mAlias;
		    }
		  else
		    {
		      // if it don't explicitly say 'flat' we're creating a hierarchical url.
		      $this->mURL = $this->HierarchyPath();
		    }
		}
	    }
	  if( $this->mURL == '' && get_site_preference('content_mandatory_urls') && !$this->mDefaultContent )
	    {
	      // page url is empty and mandatory
	      $errors[] = lang('content_mandatory_urls');
	    }
	  else if( $this->mURL != '' )
	    {
	      // page url is not empty, check for validity.
	      $this->mURL = trim($this->mURL," /\t\r\n\0\x08"); // silently delete bad chars.
	      if( $this->mURL != '' && !content_assistant::is_valid_url($this->mURL,$this->mId) )
		{
		  // and validate the URL.
		  $errors[] = lang('invalid_url');
		}
	    }

	  return (count($errors) > 0?$errors:FALSE);
	}

    /**
     * Delete the content
     */
    # :TODO: This function should return something
	function Delete()
	{
		global $gCms, $sql_queries, $debug_errors;
		$config =& $gCms->GetConfig();

		foreach($gCms->modules as $key=>$value)
		{
			if ($gCms->modules[$key]['installed'] == true &&
			$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->ContentDeletePre($this);
			}
		}

		Events::SendEvent('Core', 'ContentDeletePre', array('content' => &$this));

		$db = &$gCms->GetDb();

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
			$query = "DELETE FROM ".cms_db_prefix()."content WHERE content_id = ?";
			$dbresult = $db->Execute($query, array($this->mId));

			if (! $dbresult)
			{
				if (true == $config["debug"])
				{
					# :TODO: Translate the error message
					$debug_errors .= "<p>Error deleting content</p>\n";
				}
			}

			#Fix the item_order if necessary
			$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order - 1 WHERE parent_id = ? AND item_order > ?";
			$result = $db->Execute($query,array($this->ParentId(),$this->ItemOrder()));

			#Remove the cross references
			remove_cross_references($this->mId, 'content');

			$cachefilename = TMP_CACHE_LOCATION . '/contentcache.php';
			@unlink($cachefilename);

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

		foreach($gCms->modules as $key=>$value)
		{
			if ($gCms->modules[$key]['installed'] == true &&
			$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->ContentDeletePost($this);
			}
		}

		Events::SendEvent('Core', 'ContentDeletePost', array('content' => &$this));
	}

    /**
     * Function for the subclass to parse out data for it's parameters (usually from $_POST)
     */
    function FillParams($params)
    {
      // content property parameters
      $parameters = array('extra1','extra2','extra3','image','thumbnail');
      foreach ($parameters as $oneparam)
	{
	  if (isset($params[$oneparam]))
	    {
	      $this->SetPropertyValue($oneparam, $params[$oneparam]);
	    }
	}

      // go through the list of base parameters
      // setting them from params
      
      // title
      if (isset($params['title']))
	{
	  $this->mName = $params['title'];
	}

      // menu text
      if (isset($params['menutext']))
	{
	  $this->mMenuText = $params['menutext'];
	}

      // parent id
      if( isset($params['parent_id']) )
	{
	  if ($this->mParentId != $params['parent_id'])
	    {
	      $this->mHierarchy = '';
	      $this->mItemOrder = -1;
	    }
	  $this->mParentId = $params['parent_id'];
	}

      // active
      if (isset($params['active']))
	{
	  $this->mActive = $params['active'];
	  if( $this->DefaultContent() )
	    {
	      $this->mActive = 1;
	    }
	}
      
      // show in menu
      if (isset($params['showinmenu']))
	{
	  $this->mShowInMenu = $params['showinmenu'];
	}

      // alias
      $tmp = '';
      if (isset($params['alias']))
	{
	  $tmp = trim($params['alias']);
	  $this->SetAlias($tmp);
	}

      // target
      if (isset($params['target']))
	{
	  $val = $params['target'];
	  if( $val == '---' )
	    {
	      $val = '';
	    }
	  $this->SetPropertyValue('target', $val);
	} 

      // title attribute
      if (isset($params['titleattribute']))
	{
	  $this->mTitleAttribute = $params['titleattribute'];
	}

      // accesskey
      if (isset($params['accesskey']))
	{
	  $this->mAccessKey = $params['accesskey'];
	}

      // tab index
      if (isset($params['tabindex']))
	{
	  $this->mTabIndex = $params['tabindex'];
	}

      // cachable
      if (isset($params['cachable']))
	{
	  $this->mCachable = $params['cachable'];
	}
      else
	{
	  $this->_handleRemovedBaseProperty('cachable','mCachable');
	}

      // secure
      if (isset($params['secure']))
	{
	  $this->mSecure = $params['secure'];
	}
      else
	{
	  $this->_handleRemovedBaseProperty('secure','mSecure');
	}

      // url
      if (isset($params['page_url']))
	{
	  $this->mURL = $params['page_url'];
	}
      else
	{
	  $this->_handleRemovedBaseProperty('page_url','mURL');
	}

      // owner
      if (isset($params["ownerid"]))
	{
	  $this->SetOwner($params["ownerid"]);
	}
	 
      // additional editors
      if (isset($params["additional_editors"]))
	{
	  $addtarray = array();
	  if( is_array($params['additional_editors']) )
	    {
	      foreach ($params["additional_editors"] as $addt_user_id)
		{
		  $addtarray[] = $addt_user_id;
		}
	    }
	  $this->SetAdditionalEditors($addtarray);
	}
    }

    /**
     * Function for content types to override to set their proper generated URL
     */
	function GetURL($rewrite = true)
	{
		global $gCms;
		$config = &$gCms->GetConfig();
		$url = "";
		$alias = ($this->mAlias != ''?$this->mAlias:$this->mId);

		$base_url = $config['root_url'];
		if( $this->Secure() )
		  {
		    if( isset($config['ssl_url']) )
		      {
			$base_url = $config['ssl_url'];
		      }
		    else
		      {
			$base_url = str_replace('http://','https://',$base_url);
		      }
		  }

		/* use root_url for default content */
		if($this->mDefaultContent) {
			$url =  $base_url . '/';
			return $url;
		}

		if ($config["url_rewriting"] == 'mod_rewrite' && $rewrite == true)
		{
		  $str = $this->HierarchyPath();
		  if( $this->mURL != '')
		    {
		      // we have a url path
		      $str = $this->mURL;
		    }
		  $url = $base_url. '/' . $str . (isset($config['page_extension'])?$config['page_extension']:'.html');
		}
		else if (isset($_SERVER['PHP_SELF']) && $config['url_rewriting'] == 'internal' && $rewrite == true)
		{
		  $str = $this->HierarchyPath();
		  if( $this->mURL != '')
		    {
		      // we have a url path
		      $str = $this->mURL;
		    }
		  $url = $base_url . '/index.php/' . $str . (isset($config['page_extension'])?$config['page_extension']:'.html');
		}
		else
		  {
		    $url = $base_url . '/index.php?' . $config['query_var'] . '=' . $alias;
		  }
		return $url;
	}


    /**
     * Show the content
     */
    function Show($param = '')
    {
    }
	
    /**
     * Handle Auto Aliasing 
     */
    function DoAutoAlias()
    {
      return $this->_add_mode;
    }

    /**
    * allow the content module to handle custom tags. Typically used for parameters in {content} tags
    */
    function ContentPreRender($tpl_source)
    {
	return $tpl_source;
    }

    /**
     * Returns the tab names used in the add and edit content page.  If it's an empty array, then
     * the tabs won't show at all.
     */
    function TabNames()
    {
        return array();
    }

    function EditAsArray($adding = false, $tab = 0, $showadmin = false)
    {
	# :TODO:
	return array(array('Error','Edit Not Defined!'));
    }

    /**
     * Show the Edit interface
     */
    function Edit($adding = false, $tab = 0, $showadmin = false)
    {
        $text = '';
        $val = $this->EditAsArray($adding, $tab, $showadmin);
        foreach ($val as $thisRow)
	{
            $text .= '<tr><th>'.$thisRow[0].'</th><td>'.$thisRow[1].'</td></tr>';
            $text .= "\n";
	}
	return $text;
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
	function HasChildren($activeonly = false)
	{
		if( $activeonly && !($this->Active() || $this->ShowInMenu()))
			{
				return false;
			}

		global $gCms, $sql_queries, $debug_errors;
		$db = &$gCms->GetDb();
		$config =& $gCms->GetConfig();

		$result = false;

		$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE parent_id = ?";
		$parms = array($this->mId);
		if( $activeonly )
			{
				$query .= ' AND show_in_menu = 1 AND active = 1';
			}

		$row = &$db->GetRow($query, array($parms));

		if ($row)
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
			$db = &$gCms->GetDb();

			$this->mAdditionalEditors = array();

			$query = "SELECT user_id FROM ".cms_db_prefix()."additional_users WHERE content_id = ?";
			$dbresult = &$db->Execute($query,array($this->mId));

			while ($dbresult && !$dbresult->EOF)
			{
				$this->mAdditionalEditors[] = $dbresult->fields['user_id'];
				$dbresult->MoveNext();
			}

			if ($dbresult) $dbresult->Close();
		}
		return $this->mAdditionalEditors;
	}

	function SetAdditionalEditors($editorarray)
	{
		$this->mAdditionalEditors = $editorarray;
	}

	function ShowAdditionalEditors($addteditors = '')
	{
		$ret = array();

		$ret[] = lang('additionaleditors');
		$text = '<input name="additional_editors" type="hidden" value=""/>';
		$text .= '<select name="additional_editors[]" multiple="multiple" size="5">';

		global $gCms;
		$userops =& $gCms->GetUserOperations();
		$groupops =& $gCms->GetGroupOperations();
		$allusers =& $userops->LoadUsers();
		$allgroups =& $groupops->LoadGroups();
		if( $addteditors == '' )
		  {
		    $addteditors = $this->GetAdditionalEditors();
		  }
		foreach ($allgroups as $onegroup)
		  {
		    if( $onegroup->id == 1 ) continue;
		    $val = $onegroup->id*-1;
		    $text .= '<option value="'.$val.'"';
		    if( in_array($val,$addteditors) )
		      {
			$text .= ' selected="selected"';
		      }
		    $text .= '>'.lang('group').': '.$onegroup->name."</option>";
		   
		  }

		foreach ($allusers as $oneuser)
		{
			if ($oneuser->id != $this->Owner() && $oneuser->id != 1)
			{
				$text .= '<option value="'.$oneuser->id.'"';
				if (in_array($oneuser->id, $addteditors))
				{
					$text .= ' selected="selected"';
				}
				$text .= '>'.$oneuser->username.'</option>';
			}
		}

		$text .= '</select>';
		$ret[] = $text;
		return $ret;
	}

	function IsDefaultPossible()
	{
		return FALSE;
	}

	function SetAddMode($flag = true)
	{
	  $this->_add_mode = $flag;
	}

	/* private */
	function _handleRemovedBaseProperty($name,$member)
	{
		if( !is_array($this->_attributes) ) return FALSE;
		if( !in_array($name,$this->_attributes) )
		{
			if( isset($this->_prop_defaults[$name]) )
			{
				$this->$member = $this->_prop_defaults[$name];
				return TRUE;
			}
		}
		return FALSE;
	}

	/* private */
	function RemoveProperty($name,$dflt)
	{
	  if( !is_array($this->_attributes) ) return;
	  $tmp = array();
	  for( $i = 0; $i < count($this->_attributes); $i++ )
	    {
	      if( is_array($this->_attributes[$i]) && $this->_attributes[$i][0] == $name )
		{
		  continue;
		}
	      $tmp[] = $this->_attributes[$i];
	    }
	  $this->_attributes = $tmp;
	  $this->_prop_defaults[$name] = $dflt;
	}

	/* private */
	/*
	 * Add a property that is directly associtated with a field in the content table.
	 */
	function AddBaseProperty($name,$priority,$is_required = 0,$type = 'string')
	{
	  if( !is_array($this->_attributes) )
	    {
	      $this->_attributes = array();
	    }

	  $this->_attributes[] = array($name,$priority,$is_required);
	}

	/* private */
	/*
	 * Add a property to the content_props table for this content item
	 * This property will not be effected by the basic_attributes list.
	 */
	function AddExtraProperty($name,$type='string')
	{
	  $this->mProperties->add($type,$name);
	}

	/* private */
	/*
	 * Add a property to the content_props table for this content item
	 */
	function AddContentProperty($name,$priority,$is_required = 0,$type = 'string')
	{
	  if( !is_array($this->_attributes) )
	    {
	      $this->_attributes = array();
	    }
	  $this->mProperties->add($type,$name);
	  $this->_attributes[] = array($name,$priority,$is_required);
	}


	/* private */
	/*
	 * Given a string, see if it's a known property name.
	 */
	function is_known_property($str)
	{
	  $tmp = array();
	  foreach( $this->_attributes as $one )
	    {
	      $tmp[] = $one[0];
	    }
	  
// 	  $props = $this->Properties();
// 	  $tmp = array_merge(array_keys($props->mPropertyTypes),$tmp);
// 	  $tmp = array_unique($tmp);
	  return in_array($str,$tmp);
	}

	/* private */
	/*
	 * Given the list of registered properties, cross reference with our
	 * basic attributes list, and figure out which ones should be displayed
	 */
	function display_attributes($adding,$negative = 0)
	{
	  // get our required attributes
	  $basic_attributes = array();
	  foreach( $this->_attributes as $one )
	    {
	      if( $one[2] == 1 ) $basic_attributes[] = $one;
	    }

	  // merge in preferred basic attributes
	  $tmp = get_site_preference('basic_attributes');
	  if( !empty($tmp) )
	    {
	      $tmp = explode(',',$tmp);
	      foreach( $tmp as $basic )
		{
		  $found = NULL;
		  foreach( $this->_attributes as $one )
		    {
		      if( $one[0] == $basic ) {
			$found = $one;
			break;
		      }
		    }
		  if( $found )
		    {
		      $basic_attributes[] = $found;
		    }
		}
	    }

	  $attrs = $basic_attributes;
	  if( $negative )
	    {
	      // build a new list of all properties... except those in the basic_attributes
	      $attrs = array();
	      foreach( $this->_attributes as $one )
		{
		  $found = 0;
		  foreach( $basic_attributes as $basic )
		    {
		      if( $basic[0] == $one[0] )
			{
			  $found = 1;
			}
		    }

		  if( !$found )
		    {
		      $attrs[] = $one;
		    }
		}
	    }

	  // remove any duplicates
	  $tmp = array();
	  foreach( $attrs as $one )
	    {
	      $found = 0;
	      foreach( $tmp as $t1 )
		{
		  if( $one[0] == $t1[0] )
		    {
		      $found = 1;
		      break;
		    }
		}
	      if( !$found )
		{
		  $tmp[] = $one;
		}
	    }
	  $attrs = $tmp;

	  // sort the attributes on the 2nd element...
	  $fn = create_function('$a,$b','if( $a[1] < $b[1] ) return -1; else if( $a[1] == $b[1] ) return 0; else return $b;');
	  usort($attrs,$fn);

	  $tmp = $this->display_admin_attributes($attrs,$adding);
	  return $tmp;
	}

	/* private */
	function display_admin_attributes($attributelist,$adding)
	{
	  // sort the attributes

	  $ret = array();
	  foreach( $attributelist as $one )
	    {
	      $ret[] = $this->display_single_element($one[0],$adding);
	    }
	  return $ret;
	}

	/* protected */
	function display_single_element($one,$adding)
	{
	  global $gCms;
	  $config =& $gCms->GetConfig();

	  switch( $one )
	    {
	    case 'cachable':
	      return array(lang('cachable').':','<input type="hidden" name="cachable" value="0"/><input class="pagecheckbox" type="checkbox" value="1" name="cachable"'.($this->mCachable?' checked="checked"':'').' />',lang('help_page_cachable'));
	      break;
	
	    case 'title':
	      {
		return array(lang('title').':','<input type="text" name="title" value="'.cms_htmlentities($this->mName).'" />');
	      }
	      break;
	      
	    case 'menutext':
	      {
		return array(lang('menutext').':','<input type="text" name="menutext" value="'.cms_htmlentities($this->mMenuText).'" />');
	      }
	      break;
	      
	    case 'parent':
		{
		  $contentops =& $gCms->GetContentOperations();
		  $tmp = $contentops->CreateHierarchyDropdown($this->mId, $this->mParentId, 'parent_id', 0, 1, 0, 1);
		  if( empty($tmp) && !check_permission(get_userid(),'Manage All Content') )
		    return array('','<input type="hidden" name="parent_id" value="'.$this->mParentId.'" />');
		  if( !empty($tmp) ) return array(lang('parent').':',$tmp);
		}
	      break;

	    case 'active':
	      if( !$this->DefaultContent() )
		{
		  return array(lang('active').':','<input type="hidden" name="active" value="0"/><input class="pagecheckbox" type="checkbox" name="active" value="1"'.($this->mActive?' checked="checked"':'').' />');
		}
	      break;
	      
	    case 'showinmenu':
	      return array(lang('showinmenu').':','<input type="hidden" name="showinmenu" value="0"/><input class="pagecheckbox" type="checkbox" value="1" name="showinmenu"'.($this->mShowInMenu?' checked="checked"':'').' />');
	      break;
	      
	    case 'target':
	      {
		$text = '<option value="---">'.lang('none').'</option>';
		$text .= '<option value="_blank"'.($this->GetPropertyValue('target')=='_blank'?' selected="selected"':'').'>_blank</option>';
		$text .= '<option value="_parent"'.($this->GetPropertyValue('target')=='_parent'?' selected="selected"':'').'>_parent</option>';
		$text .= '<option value="_self"'.($this->GetPropertyValue('target')=='_self'?' selected="selected"':'').'>_self</option>';
		$text .= '<option value="_top"'.($this->GetPropertyValue('target')=='_top'?' selected="selected"':'').'>_top</option>';
		return array(lang('target').':','<select name="target">'.$text.'</select>');
		
	      }
	      break;
	      
	    case 'alias':
	      return array(lang('pagealias').':','<input type="text" name="alias" value="'.$this->mAlias.'" />',lang('help_page_alias'));
	      break;
	      
	    case 'secure':
	      {
		$opt = '';
		if( $this->mSecure )
		  {
		    $opt = ' checked="checked"';
		  }
		$str  = '<input type="hidden" name="secure" value="0"/>';
                $str .= '<input type="checkbox" name="secure" value="1"'.$opt.'/>';
		return array(lang('secure_page').':',$str);
	      }
	      break;

	    case 'page_url':
	      {
		$str = '<input type="text" name="page_url" value="'.$this->mURL.'" size="50" maxlength="255"/>';
		$prompt = lang('page_url').':';
		if( get_site_preference('content_mandatory_urls',0) )
		  {
		    $prompt = '*'.$prompt;
		  }
		return array($prompt,$str);
	      }
	      break;

	    case 'image':
	      {
		$dir = cms_join_path($config['image_uploads_path'],get_site_preference('content_imagefield_path'));
		$data = $this->GetPropertyValue('image');
		$dropdown = create_file_dropdown('image',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_');
		if( !$dropdown ) return;
		return array(lang('image').':',$dropdown);
	      }
	      break;
	      
	    case 'thumbnail':
	      {
		$dir = cms_join_path($config['image_uploads_path'],get_site_preference('content_thumbnailfield_path'));
		$data = $this->GetPropertyValue('thumbnail');
		$dropdown = create_file_dropdown('thumbnail',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_',0);
		if( !$dropdown ) return FALSE;
		return array(lang('thumbnail').':',$dropdown);
	      }
	      break;
	      
	    case 'titleattribute':
	      {
		return array(lang('titleattribute').':','<input type="text" name="titleattribute" maxlength="255" size="80" value="'.cms_htmlentities($this->mTitleAttribute).'" />');
	      }
	      break;
	      
	    case 'accesskey':
	      {
		return array(lang('accesskey').':','<input type="text" name="accesskey" maxlength="5" value="'.cms_htmlentities($this->mAccessKey).'" />');
	      }
	      break;

	    case 'tabindex':
	      {
		return array(lang('tabindex').':','<input type="text" name="tabindex" maxlength="5" value="'.cms_htmlentities($this->mTabIndex).'" />');
	      }
	      break;
	      
	    case 'extra1':
	      {
		return array(lang('extra1').':','<input type="text" name="extra1" maxlength="255" size="80" value="'.cms_htmlentities($this->GetPropertyValue('extra1')).'" />');
	      }
	      break;
	      
	    case 'extra2':
	      {
		return array(lang('extra2').':','<input type="text" name="extra2" maxlength="255" size="80" value="'.cms_htmlentities($this->GetPropertyValue('extra2')).'" />');
	      }
	      break;
	      
	    case 'extra3':
	      {
		return array(lang('extra3').':','<input type="text" name="extra3" maxlength="255" size="80" value="'.cms_htmlentities($this->GetPropertyValue('extra3')).'" />');
	      }
	      break;

	    case 'owner':
	      {
		$showadmin = check_ownership(get_userid(), $this->Id());
		$userops =& $gCms->GetUserOperations();
		if (!$adding && $showadmin)
		  {
		    return array(lang('owner').':', $userops->GenerateDropdown($this->Owner()));
		  }
	      }
	      break;

	    case 'additionaleditors':
	      {
		// do owner/additional-editor stuff
		if( $adding || check_ownership(get_userid(),$this->Id()) )
		  {
		    return $this->ShowAdditionalEditors();
		  }
	      }
	      break;

	    default:
	      stack_trace();
	      die('unknown property '.$one);
	    }
	}

	protected function SetError($str)
	{
	  $this->_error = $str;
	}

	public function GetError()
	{
	  return $this->_error;
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
    var $mPropertyNames;
    var $mPropertyTypes;
    var $mPropertyValues;
     
    /**
     * The (content type specific) allowed properties of the content.
    */
    var $mAllowedPropertyNames;

    /**
     * Generic constructor. Runs the SetInitialValues fuction.
     */
	function ContentProperties()
	{
		$this->SetInitialValues();
		$this->SetAllowedPropertyNames(NULL);
	}

    /**
     * Sets object to some sane initial values
     */
	function SetInitialValues()
	{
		$this->mPropertyNames = array();
		$this->mPropertyTypes = array();
		$this->mPropertyValues = array();
	}

	function HasProperty($name)
	{
		#debug_buffer($this->mPropertyNames);
		if (!isset($this->mPropertyNames))
		  $this->mPropertyNames = array();
		return in_array($name, $this->mPropertyNames);
	}

	function Add($type, $name, $defaultvalue='')
	{
		//Handle names separately
		if (!in_array($name, $this->mPropertyNames))
		{
			$this->mPropertyNames[] = $name;
		}
		if (!array_key_exists($name, $this->mPropertyValues))
		{
			$this->mPropertyTypes[$name] = $type;
			$this->mPropertyValues[$name] = $defaultvalue;
		}
	}

	function GetValue($name)
	{
		if (in_array($name, $this->mPropertyNames))
		{
			if (count($this->mPropertyValues) > 0)
			{
				if (array_key_exists($name, $this->mPropertyValues))
				{
					return $this->mPropertyValues[$name];
				}
			}
		}
	}

	function SetValue($name, $value)
	{
		if (count($this->mPropertyValues) > 0)
		{
			if (in_array($name, $this->mPropertyNames))
			{
				$this->mPropertyValues[$name] = $value;
			}
		}
	}

	function Load($content_id)
	{
		if (count($this->mPropertyNames) > 0)
		{
			global $gCms, $sql_queries, $debug_errors;
			$db = &$gCms->GetDb();

			$query = "SELECT * FROM ".cms_db_prefix()."content_props WHERE content_id = ?";
			$dbresult = &$db->Execute($query, array($content_id));

			while ($dbresult && !$dbresult->EOF)
			{
				$prop_name = $dbresult->fields['prop_name'];
//				if ($this->GetAllowedPropertyNames() == NULL || in_array($prop_name, $this->GetAllowedPropertyNames()))
//				{
					if (!in_array($prop_name, $this->mPropertyNames))
					{
						$this->mPropertyNames[] = $prop_name;
					}
					$this->mPropertyTypes[$prop_name] = $dbresult->fields['type'];
					$this->mPropertyValues[$prop_name] = $dbresult->fields['content'];
//				}
				$dbresult->MoveNext();
			}
			
			if ($dbresult) $dbresult->Close();
		}
	}

	function Save($content_id)
	{
		if (count($this->mPropertyValues) > 0)
		{
			global $gCms, $sql_queries, $debug_errors;
			
			$db        =& $gCms->GetDb();
			$config    =& $gCms->GetConfig();
			$concat    =  '';
			$timestamp =  $db->DBTimeStamp(time());

			$insquery = "
				INSERT INTO ".cms_db_prefix()."content_props 
				(
					content_id, 
					type, 
					prop_name, 
					param1, 
					param2, 
					param3, 
					content,
					modified_date
				) 
					VALUES 
				(
					?,?,?,'','','',?,$timestamp
				)
			";

			foreach ($this->mPropertyValues as $key=>$value)
			{
//				if ($this->GetAllowedPropertyNames() == NULL || in_array($key, $this->GetAllowedPropertyNames()))
//				{
					$delquery = "DELETE FROM ".cms_db_prefix()."content_props WHERE content_id = ? AND prop_name = ?";
					$dbresult = $db->Execute($delquery, array($content_id, $key));
					if (true == $config["debug"])
					  {
					    $sql_queries .= "<p>".$db->sql."</p>\n";
					  }
					
					$sql_vars = array(
						$content_id,
						$this->mPropertyTypes[$key],
						$key,
						$this->mPropertyValues[$key]
					);
					$dbresult = $db->Execute($insquery, $sql_vars);
					if (true == $config["debug"])
					  {
					    $sql_queries .= "<p>".$db->sql."</p>\n";
					  }

					$concat .= $this->mPropertyValues[$key];

					if (! $dbresult)
					{
						if (true == $config["debug"])
						{
							# :TODO: Translate the error message
							$debug_errors .= "<p>Error updating content property</p>\n";
						}
					}
//				}
			}

			if ($concat != '')
			{
				do_cross_reference($content_id, 'content', $concat);
			}
		}
	}

    function Delete($content_id)
    {
	if (count($this->mPropertyValues) > 0)
	{
	    global $gCms, $sql_queries, $debug_errors;
	    $db = &$gCms->GetDb();

	    $query = "DELETE FROM ".cms_db_prefix()."content_props WHERE content_id = ?";
	    $db->Execute($query, array($content_id));
	}
    }

    /**
     * Subclasses should fill this array with strings containing the name of
     * the allowed property
     * @param array
    */
    function SetAllowedPropertyNames($array)
    {
        $this->mAllowedPropertyNames = $array;
    }
    
    /**
     * Subclasses should fill this array with strings containing the name of
     * the allowed property
     * @return array
    */
    function GetAllowedPropertyNames()
    {
        return $this->mAllowedPropertyNames;
    }

} // end of class ContentProperties

/**
 * Class that module defined content types must extend.
 *
 * @since		0.9
 * @package		CMS
 */
class CMSModuleContentType extends ContentBase
{
	//What module do I belong to?  (needed for things like Lang to work right)
	function ModuleName()
	{
		return '';
	}

	function Lang($name, $params=array())
	{
		global $gCms;
		$cmsmodules = &$gCms->modules;
		if (array_key_exists($this->ModuleName(), $cmsmodules))
		{
			return $cmsmodules[$this->ModuleName()]['object']->Lang($name, $params);
		}
		else
		{
			return 'ModuleName() not defined properly';
		}
	}

	/*
	* Returns the instance of the module this content type belongs to
	*
	*/
	function GetModuleInstance() 
	{
		global $gCms;
		$cmsmodules = &$gCms->modules;
		if (array_key_exists($this->ModuleName(), $cmsmodules))
		{
			return $cmsmodules[$this->ModuleName()]['object'];
		}
		else
		{
			return 'ModuleName() not defined properly';
		}
	}
}

# vim:ts=4 sw=4 noet
?>
