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
 * Generic content class.
 *
 * As for some treatment we don't need the extra properties of the content
 * we load them only when required. However, each function which makes use
 * of extra properties should first test if the properties object exist.
 *
 * @since		0.8
 * @package		CMS
 */
define('CMS_CONTENT_HIDDEN_NAME','--------');
class CmsContentBase extends CmsObjectRelationalMapping
{
    private $_attributes = array();
    private $_prop_defaults;
    private $_add_mode;

	var $table = 'content';
	var $params = array('id' => -1, 'template_id' => -1, 'active' => true, 'default_content' => false, 'parent_id' => -1, 'lft' => 1, 'rgt' => 1);
	var $field_maps = array('content_alias' => 'alias', 'titleattribute' => 'title_attribute', 'accesskey' => 'access_key', 'tabindex' => 'tab_index', 'content_name' => 'name', 'content_id' => 'id');
	var $unused_fields = array();
	var $sequence = 'content_seq';

	var $mProperties = null;

	var $preview = false;
	
	var $props_loaded = false;

	#Stuff needed to do a doppleganger for CmsNode -- Multiple inheritence would rock right now
	public $tree = null;
	public $parentnode = null;
	public $children = array();
	public $children_loaded = false;

    /************************************************************************/
    /* Constructor related													*/
    /************************************************************************/

    /**
     * Generic constructor. Runs the SetInitialValues fuction.
     */
	function __construct()
	{
		parent::__construct();
		//$this->SetInitialValues();
		$this->mProperties = new ContentProperties();
		$this->SetProperties();
		$this->mPropertiesLoaded = false;
		$this->mReadyForEdit = false;
	}
	
	function setup()
	{
		//$this->create_belongs_to_association('template', 'cms_template', 'template_id');
		$this->assign_acts_as('NestedSet');
		//$this->assign_acts_as('Acl');
	}
	
	public function GetContent()
	{
		return $this;
	}

    /**
     * Sets object to some sane initial values
     */
    public function SetInitialValues()
    {
	/*
	$this->id             = -1 ;
	$this->mLft            = -1 ;
	$this->mRgt            = -1 ;
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
	$this->idHierarchy    = "" ;
	$this->mHierarchyPath  = "" ;
	$this->mMetadata       = "" ;
	$this->mTitleAttribute = "" ;
	$this->mAccessKey      = "" ;
	$this->mTabIndex       = "" ;
	$this->mActive         = false ;
	$this->mDefaultContent = false ;
	$this->mShowInMenu     = false ;
	$this->mCachable       = true;
	$this->mMenuText       = "" ;
	$this->mCreationDate   = "" ;
	$this->mModifiedDate   = "" ;
	$this->mPreview        = false ;
	$this->mMarkup         = 'html' ;
	$this->mChildCount     = 0;
	$this->mPropertiesLoaded = false;
	*/
    }

    /**
     * Subclasses should override this to set their property types using a lot
     * of mProperties.Add statements
     */
	public function SetProperties()
	{
		$this->AddContentProperty('target',10);
		$this->AddBaseProperty('title',1,1);
		$this->AddBaseProperty('menutext',2,1);
		$this->AddBaseProperty('parent',3,1);
		$this->AddBaseProperty('active',5);
		$this->AddBaseProperty('showinmenu',5);
		$this->AddBaseProperty('alias',6);
		$this->AddBaseProperty('secure',7);
		$this->AddBaseProperty('cachable',10);
		$this->AddBaseProperty('titleattribute',55);
		$this->AddBaseProperty('accesskey',56);
		$this->AddBaseProperty('tabindex',57);
		$this->AddBaseProperty('owner',90);
		$this->AddBaseProperty('additionaleditors',91);

		$this->AddContentProperty('image',50);
		$this->AddContentProperty('thumbnail',50);
		$this->AddContentProperty('extra1',80);
		$this->AddContentProperty('extra2',80);
		$this->AddContentProperty('extra3',80);
	}

    
    /************************************************************************/
    /* Public Functions giving access to needed elements of the content			*/
    /************************************************************************/

    /**
     * Returns the ID
     */
    public function Id()
    {
		return $this->id;
    }

    public function SetId($id)
    {
		$this->id = $id;
    }

    /**
     * Returns a friendly name for this content type
     */
    public function FriendlyName()
    {
		return '';
    }

    /**
     * Returns the Type
     */
    public function Type()
    {
		return strtolower($this->type);
    }

    public function SetType($type)
    {
		$this->type = strtolower($type);
    }

    /**
     * Returns the Owner
     */
    public function Owner()
    {
		return $this->owner_id;
    }

    public function SetOwner($owner)
    {
		$this->owner_id = $owner;
    }

    /**
     * Returns the Owner
     */
    public function Name()
    {
		return $this->name;
    }

    public function SetName($name)
    {
		$this->name = $name;
    }

    /**
     * Content object handles the alias
     */
    public function HandlesAlias()
    {
		return false;
    }

    public function TabIndex()
    {
		return $this->tabindex;
    }

    public function SetTabIndex($tabindex)
    {
		$this->tabindex = $tabindex;
    }

    public function TitleAttribute()
    {
		return $this->titleattribute;
    }

    public function GetCreationDate()
    {
		return $this->create_date;
    }
	
    public function GetModifiedDate()
    {
		return $this->modified_date;
    }

    public function SetTitleAttribute($titleattribute)
    {
		$this->titleattribute = $titleattribute;
    }

    public function AccessKey()
    {
		return $this->accesskey;
    }

    public function SetAccessKey($accesskey)
    {
		$this->accesskey = $accesskey;
    }

    /**
     * Returns the ParentId
     */
    public function ParentId()
    {
		return $this->parent_id;
    }

    public function SetParentId($parentid)
    {
		$this->parent_id = $parentid;
    }

    public function OldParentId()
    {
		return $this->old_parent_id;
    }

    public function SetOldParentId($parentid)
    {
		$this->old_parent_id = $parentid;
    }

    public function TemplateId()
    {
		return $this->template_id;
    }

    public function SetTemplateId($templateid)
    {
		$this->template_id = $templateid;
    }

    /**
     * Returns the ItemOrder
     */
    public function ItemOrder()
    {
		return $this->item_order;
    }

    public function SetItemOrder($itemorder)
    {
		$this->item_order = $itemorder;
    }

    public function OldItemOrder()
    {
		return $this->old_item_order;
    }

    public function SetOldItemOrder($itemorder)
    {
		$this->DoReadyForEdit();
		$this->old_item_order = $itemorder;
    }

    /**
     * Returns the Hierarchy
     */
    public function Hierarchy()
    {
		global $gCms;
		$contentops =& $gCms->GetContentOperations();
		return $contentops->CreateFriendlyHierarchyPosition($this->hierarchy);
    }

    /**
     * Returns the Hierarchy
     */
    public function IdHierarchy()
    {
		return $this->id_hierarchy;
    }

    public function SetIdHierarchy($idhierarchy)
    {
		$this->id_hierarchy = $idhierarchy;
    }

    /**
     * Returns the Hierarchy
     */
    public function HierarchyPath()
    {
		return $this->hierarchy_path;
    }

    public function SetHierarchyPath($hierarchypath)
    {
		$this->hierarchy_path = $hierarchypath;
    }

    /**
     * Returns whether it should show in the menu
     */
    public function ShowInMenu()
    {
	return $this->show_in_menu;
    }

    public function SetShowInMenu($showinmenu)
    {
		$this->show_in_menu = $showinmenu;
    }

    public function Previewable()
    {
      return $this->preview;
    }

    public function SetPreviewable($flag)
    {
      return $this->preview = $flag;
    }

	public function LastModifiedBy()
	{
		return $this->last_modified_by;
	}

	public function SetLastModifiedBy($lastmodifiedby)
	{
		$this->last_modified_by = $lastmodifiedby;
	}

	public function RequiresAlias()
	{
	  return TRUE;
	}

	public function IsViewable()
	{
	  return TRUE;
	}

	public function SetSecurePage($flag)
	{
	  $this->secure = ($flag)?1:0;
	}

	public function IsSecurePage()
	{
	  return $this->secure;
	}

	public function set_alias($alias, $doAutoAliasIfEnabled = true)
	{
		//$this->DoReadyForEdit();
		global $gCms;
		$config =& $gCms->GetConfig();

		$tolower = false;

		if ($alias == '' && $doAutoAliasIfEnabled && $config['auto_alias_content'] == true)
		{
			$alias = trim($this->menu_text);
			if ($alias == '')
			{
			    $alias = trim($this->content_name);
			}
			
			$tolower = true;
			$alias = munge_string_to_url($alias, $tolower);
			if( empty($alias) )
			  {
			    // calguy: quick hack so that the alias is never empty...
			    $alias = 'alias';
			  }
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

		$this->alias = munge_string_to_url($alias, $tolower);
	}
	
	public function SetAlias($alias, $doAutoAliasIfEnabled = true)
	{
		return $this->set_alias($alias, $doAutoAliasIfEnabled);
	}
	
    /**
     * Returns the menu text for this content
     */
	public function MenuText()
	{
		return $this->menu_text;
	}

	public function SetMenuText($menutext)
	{
		$this->menu_text = $menutext;
	}

    /**
     * Returns number of immediate child-content items of this content
     */
	public function ChildCount()
	{
		return $this->mChildCount;
	}

    /**
     * Returns the properties
     */
	public function Properties()
	{
		debug_buffer('properties called');
		if ($this->mPropertiesLoaded == false)
		{
			$this->mProperties->Load($this->id);
			$this->mPropertiesLoaded = true;
		}
		return $this->mProperties;
	}

	public function HasProperty($name)
	{
		return $this->mProperties->HasProperty($name);
	}

	public function GetPropertyValue($name)
	{
		if ($this->mProperties->HasProperty($name))
		{
			if ($this->mPropertiesLoaded == false)
			{
				$this->mProperties->Load($this->id);
				$this->mPropertiesLoaded = true;
			}
			return $this->mProperties->GetValue($name);
		}
		return '';
	}
    
	public function SetPropertyValue($name, $value)
	{
		debug_buffer('setpropertyvalue called');
		$this->DoReadyForEdit();
		if ($this->mPropertiesLoaded == false)
		{
			$this->mProperties->Load($this->id);
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
    public function WantsChildren()
    {
	return true;
    }

    /**
     * Should this link be used in various places where a link is the only
     * useful output?  (Like next/previous links in cms_selflink, for example)
     */
    public function HasUsableLink()
    {
	return true;
    }

    /**
     * Is this content type copyable ?
     */
    public function IsCopyable()
    {
      return FALSE;
    }


    /**
     * Is this content object a system page,i.e: handling an error or some type of system function.
     */
    public function IsSystemPage()
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
    public function ReadyForEdit()
    {
    }

	public function DoReadyForEdit()
	{
		if ($this->mReadyForEdit == false)
		{
			$this->ReadyForEdit();
			$this->mReadyForEdit = true;
		}
	}
	
	protected function after_load()
	{
		$this->mProperties->mPropertyNames = explode(',',$this->prop_names);
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
	public function LoadFromId($id, $loadProperties = false)
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
				$this->id                         = $row->fields["content_id"];
				$this->mLft                        = $row->fields['lft'];
				$this->mRgt                        = $row->fields['rgt'];
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
				$this->idHierarchy                = $row->fields["id_hierarchy"];
				$this->mHierarchyPath              = $row->fields["hierarchy_path"];
				//$this->mProperties->mPropertyNames = explode(',',$row->fields["prop_names"]);
				$this->mMenuText                   = $row->fields['menu_text'];
				$this->mMarkup                     = $row->fields['markup'];
				$this->mTitleAttribute             = $row->fields['titleattribute'];
				$this->mAccessKey                  = $row->fields['accesskey'];
				$this->mTabIndex                   = $row->fields['tabindex'];
				$this->mSecure                     = $row->fields['secure'];
				$this->mActive                     = ($row->fields["active"] == 1          ? true : false);
				$this->mDefaultContent             = ($row->fields["default_content"] == 1 ? true : false);
				$this->mShowInMenu                 = ($row->fields["show_in_menu"] == 1    ? true : false);
				$this->mCachable                   = ($row->fields["cachable"] == 1        ? true : false);
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
					$this->mProperties->Load($this->id);
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
	public function LoadFromData(&$data, $loadProperties = false)
	{
	  global $gCms, $debug_errors;
	  $config =& $gCms->GetConfig();

		$result = true;

		$this->id                         = $data["content_id"];
		$this->mLft                        = $data['lft'];
		$this->mRgt                        = $data['rgt'];
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
		$this->idHierarchy                = $data["id_hierarchy"];
		$this->mHierarchyPath              = $data["hierarchy_path"];
		//$this->mProperties->mPropertyNames = explode(',',$data["prop_names"]);
		$this->mMenuText                   = $data['menu_text'];
		$this->mMarkup                     = $data['markup'];
		$this->mTitleAttribute             = $data['titleattribute'];
		$this->mAccessKey                  = $data['accesskey'];
		$this->mTabIndex                   = $data['tabindex'];
		$this->mDefaultContent             = ($data["default_content"] == 1 ? true : false);
		$this->mActive                     = ($data["active"] == 1          ? true : false);
		$this->mSecure                     = $data['secure'];
		$this->mShowInMenu                 = ($data["show_in_menu"] == 1    ? true : false);
		$this->mCachable                   = ($data["cachable"] == 1        ? true : false);
		$this->mLastModifiedBy             = $data["last_modified_by"];
		$this->mCreationDate               = $data["create_date"];
		$this->mModifiedDate               = $data["modified_date"];

		if ($loadProperties == true)
		{
			#$this->mProperties = ContentManager::LoadPropertiesFromData(strtolower($this->mType), $data);
			if ($this->mPropertiesLoaded == false)
			{
				debug_buffer("load from data is loading properties");
				$this->mProperties->Load($this->id);
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
    public function Load()
    {
    }

    /**
     * Save or update the content
     */
    # :TODO: This function should return something
	/*
	public function Save()
	{
		global $gCms;
		Events::SendEvent('Core', 'ContentEditPre', array('content' => &$this));

		if ($this->mPropertiesLoaded == false)
		{
			debug_buffer('save is loading properties');
			$this->mProperties->Load($this->id);
			$this->mPropertiesLoaded = true;
		}

		if (-1 < $this->id)
		{
			$this->Update();
		}
		else
		{
			$this->Insert();
		}

		Events::SendEvent('Core', 'ContentEditPost', array('content' => &$this));
	}
	*/
	
	function before_save()
	{
		Events::SendEvent('Core', 'ContentEditPre', array('content' => &$this));
		
		if ($this->mPropertiesLoaded == false)
		{
			debug_buffer('save is loading properties');
			$this->mProperties->Load($this->id);
			$this->mPropertiesLoaded = true;
		}
	}
    
	function after_save(&$result)
	{
		if ($result)
		{
			$db = cms_db();
			
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
			
			if ($this->mProperties != NULL)
			{
				$this->mProperties->Save($this->id);
			}
			
			Events::SendEvent('Core', 'ContentEditPost', array('content' => &$this));
			CmsCache::clear();
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
	public function Update()
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

		$query = "UPDATE ".cms_db_prefix()."content SET content_name = ?, owner_id = ?, type = ?, template_id = ?, parent_id = ?, active = ?, default_content = ?, show_in_menu = ?, cachable = ?, menu_text = ?, content_alias = ?, metadata = ?, titleattribute = ?, accesskey = ?, tabindex = ?, modified_date = ?, item_order = ?, markup = ?, lft = ?, rgt = ?, secure = ?, last_modified_by = ? WHERE content_id = ?";
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
			$this->mMenuText,
			$this->mAlias,
			$this->mMetadata,
			$this->mTitleAttribute,
			$this->mAccessKey,
			$this->mTabIndex,
			$this->mModifiedDate,
			$this->mItemOrder,
			$this->mMarkup,
			$this->mLft,
			$this->mRgt,
			$this->mSecure,
			$this->mLastModifiedBy,
			$this->id
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
			$this->mProperties->Save($this->id);
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
	public function Insert()
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
		$this->id = $newid;

		$this->mModifiedDate = $this->mCreationDate = trim($db->DBTimeStamp(time()), "'");

		$query = "INSERT INTO ".$config["db_prefix"]."content (content_id, content_name, content_alias, type, owner_id, parent_id, template_id, item_order, hierarchy, id_hierarchy, active, default_content, show_in_menu, cachable, menu_text, markup, metadata, titleattribute, accesskey, tabindex, lft, rgt, secure, last_modified_by, create_date, modified_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

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
			$this->idHierarchy,
			($this->mActive == true         ? 1 : 0),
			($this->mDefaultContent == true ? 1 : 0),
			($this->mShowInMenu == true     ? 1 : 0),
			($this->mCachable == true       ? 1 : 0),
			$this->mMenuText,
			$this->mMarkup,
			$this->mMetadata,
			$this->mTitleAttribute,
			$this->mAccessKey,
			$this->mTabIndex,
			$this->mLft,
			$this->mRgt,
			$this->mSecure,
			$this->mLastModifiedBy,
			$this->mModifiedDate,
			$this->mCreationDate
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
	public function ValidateData()
	{
	  $errors = array();

	  if ($this->parent_id < -1) 
	    {
	      $errors[] = lang('invalidparent');
	      $result = false;
	    }
	  
	  if ($this->name == '')
	    {
	      if ($this->menu_text != '')
		{
		  $this->name = $this->menu_text;
		}
	      else
		{
		  $errors[]= lang('nofieldgiven',array(lang('title')));
		  $result = false;
		}
	    }
	  
	  if ($this->menu_text == '')
	    {
	      if ($this->name != '')
		{
		  $this->menu_text = $this->name;
		}
	      else
		{
		  $errors[]=lang('nofieldgiven',array(lang('menutext')));
		  $result = false;
		}
	    }
		
	  if (!$this->HandlesAlias())
	    {
	      if ($this->alias != $this->mOldAlias || ($this->alias == '' && $this->RequiresAlias()) ) 
		{
		  global $gCms;
		  $contentops =& $gCms->GetContentOperations();
		  $error = $contentops->CheckAliasError($this->alias, $this->id);
		  if ($error !== FALSE)
		    {
		      $errors[]= $error;
		      $result = false;
		    }
		}
	    }

	  return (count($errors) > 0?$errors:FALSE);
	}

    /**
     * Delete the content
     */
    # :TODO: This function should return something
	public function Delete()
	{
		global $gCms, $sql_queries, $debug_errors;
		$config =& $gCms->GetConfig();

		Events::SendEvent('Core', 'ContentDeletePre', array('content' => &$this));

		$db = &$gCms->GetDb();

		$result = false;

		if (-1 > $this->id)
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
			$dbresult = $db->Execute($query, array($this->id));

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
			remove_cross_references($this->id, 'content');

			$cachefilename = TMP_CACHE_LOCATION . '/contentcache.php';
			@unlink($cachefilename);

			if (NULL != $this->mProperties)
			{
				# :TODO: There might be some error checking there
				$this->mProperties->Delete($this->id);
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

		Events::SendEvent('Core', 'ContentDeletePost', array('content' => &$this));
	}

    /**
     * Function for the subclass to parse out data for it's parameters (usually from $_POST)
     */
    public function FillParams($params)
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
	  $this->name = $params['title'];
	}

      // menu text
      if (isset($params['menutext']))
	{
	  $this->menu_text = $params['menutext'];
	}

      // parent id
      if( isset($params['parent_id']) )
	{
	  if ($this->parent_id != $params['parent_id'])
	    {
	      $this->hierarchy = '';
	      $this->item_order = -1;
	    }
	  $this->parent_id = $params['parent_id'];
	}

      // active
      if (isset($params['active']))
	{
	  $this->active = $params['active'];
	}
      
      // secure
      if (isset($params['secure']))
	{
	  $this->secure = $params['secure'];
	}
      
      // show in menu
      if (isset($params['showinmenu']))
	{
	  $this->show_in_menu = $params['showinmenu'];
	}

      // alias
      if (isset($params['alias']))
	{
	  $tmp = trim($params['alias']);
	  if( empty($tmp) )
	    {
	      $this->set_alias($tmp);
	    }
	  else
	    {
	      $this->set_alias($tmp, $this->DoAutoAlias());
	    }
	}
      else if($this->RequiresAlias() && $this->DoAutoAlias())
	{
	  $this->set_alias('');
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
	  $this->title_attribute = $params['titleattribute'];
	}

      // accesskey
      if (isset($params['accesskey']))
	{
	  $this->access_eky = $params['accesskey'];
	}

      // tab index
      if (isset($params['tabindex']))
	{
	  $this->tab_index = $params['tabindex'];
	}

      // cachable
      if (isset($params['cachable']))
	{
	  $this->cachable = $params['cachable'];
	}
      else
	{
	  $this->_handleRemovedBaseProperty('cachable','mCachable');
	}

      // owner
      if (isset($params["ownerid"]))
	{
	  $this->owner_id = $params["ownerid"];
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
	public function GetURL($rewrite = true)
	{
		global $gCms;
		$config = &$gCms->GetConfig();
		$url = "";
		$alias = ($this->alias != ''?$this->alias:$this->id);

		$root_url = $config['root_url'];
		if ($this->IsSecurePage())
		  {
			$root_url = $config['secure_root_url'];
		  }

		/* use root_url for default content */
		if($this->default_content) {
			$url =  $root_url . '/';
			return $url;
		}

		if ($config["url_rewriting"] == 'mod_rewrite' && $rewrite == true)
		{
		  $url = $root_url . '/' . $this->HierarchyPath() . (isset($config['page_extension'])?$config['page_extension']:'.html');
		}
		else
		{
			if (isset($_SERVER['PHP_SELF']) && $config['url_rewriting'] == 'internal')
			{
			  $url = $root_url . '/index.php/' . $this->HierarchyPath() . (isset($config['page_extension'])?$config['page_extension']:'.html');
			}
			else
			{
			  $url = $root_url . '/index.php?' . $config['query_var'] . '=' . $alias;
			}
		}
		return $url;
	}


    /**
     * Show the content
     */
    public function Show($param = '')
    {
    }
	
    /**
     * Handle Auto Aliasing 
     */
    public function DoAutoAlias()
    {
      return $this->_add_mode;
    }

    /**
     * Returns the tab names used in the add and edit content page.  If it's an empty array, then
     * the tabs won't show at all.
     */
    public function TabNames()
    {
        return array();
    }

    public function EditAsArray($adding = false, $tab = 0, $showadmin = false)
    {
	# :TODO:
	var_dump($this);
	return array(array('Error','Edit Not Defined!'));
    }

    /**
     * Show the Edit interface
     */
    public function Edit($adding = false, $tab = 0, $showadmin = false)
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
    public function Help()
    {
    	# :TODO:
    	return "<tr><td>Help Not Defined</td></tr>";
    }

    /**
     * Does this have children?
     */
	public function has_children($activeonly = false)
	{
		if( $activeonly && !($this->Active() || $this->ShowInMenu()))
		{
			return false;
		}

		if (!$activeonly)
		{
			return $this->rgt - $this->lft > 1;
		}
		else
		{
			global $gCms, $sql_queries, $debug_errors;
			$db = &$gCms->GetDb();
			$config =& $gCms->GetConfig();

			$result = false;

			$query = "SELECT content_id FROM ".cms_db_prefix()."content WHERE parent_id = ?";
			$parms = array($this->id);
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
	}
	
	public function HasChildren($activeonly = false)
	{
		return $this->has_children($activeonly);
	}

	public function GetAdditionalEditors()
	{
		if (!isset($this->mAdditionalEditors))
		{
			global $gCms;
			$db = &$gCms->GetDb();

			$this->mAdditionalEditors = array();

			$query = "SELECT user_id FROM ".cms_db_prefix()."additional_users WHERE content_id = ?";
			$dbresult = &$db->Execute($query,array($this->id));

			while ($dbresult && !$dbresult->EOF)
			{
				$this->mAdditionalEditors[] = $dbresult->fields['user_id'];
				$dbresult->MoveNext();
			}

			if ($dbresult) $dbresult->Close();
		}
		return $this->mAdditionalEditors;
	}

	public function SetAdditionalEditors($editorarray)
	{
		$this->mAdditionalEditors = $editorarray;
	}

	public function ShowAdditionalEditors($addteditors = '')
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

	public function IsDefaultPossible()
	{
		return FALSE;
	}

	public function SetAddMode($flag = true)
	{
	  $this->_add_mode = $flag;
	}

	/* private */
	private function _handleRemovedBaseProperty($name,$member)
	{
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
	protected function RemoveProperty($name,$dflt)
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
	protected function AddBaseProperty($name,$priority,$is_required = 0,$type = 'string')
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
	protected function AddExtraProperty($name,$type='string')
	{
	  $this->mProperties->add($type,$name);
	}

	/* private */
	/*
	 * Add a property to the content_props table for this content item
	 */
	protected function AddContentProperty($name,$priority,$is_required = 0,$type = 'string')
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
	 * Given the list of registered properties, cross reference with our
	 * basic attributes list, and figure out which ones should be displayed
	 */
	protected function display_attributes($adding,$negative = 0)
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
	private function display_admin_attributes($attributelist,$adding)
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
	protected function display_single_element($one,$adding)
	{
	  global $gCms;
	  $config =& $gCms->GetConfig();

	  switch( $one )
	    {
	    case 'cachable':
	      return array(lang('cachable').':','<input type="hidden" name="cachable" value="0"/><input class="pagecheckbox" type="checkbox" value="1" name="cachable"'.($this->Cachable()?' checked="checked"':'').' />');
	      break;
	
	    case 'title':
	      {
		return array(lang('title').':','<input type="text" name="title" value="'.cms_htmlentities($this->name).'" />');
	      }
	      break;
	      
	    case 'menutext':
	      {
		return array(lang('menutext').':','<input type="text" name="menutext" value="'.cms_htmlentities($this->menu_text).'" />');
	      }
	      break;
	      
	    case 'parent':
		{
		  $contentops =& $gCms->GetContentOperations();
		  $tmp = $contentops->CreateHierarchyDropdown($this->id, $this->parent_id, 'parent_id', 0, 1);
		  if( empty($tmp) && !check_permission(get_userid(),'Manage All Content') )
		    return array('','<input type="hidden" name="parent_id" value="'.$this->parent_id.'" />');
		  if( !empty($tmp) ) return array(lang('parent').':',$tmp);
		}
	      break;

	    case 'active':
	      return array(lang('active').':','<input type="hidden" name="active" value="0"/><input class="pagecheckbox" type="checkbox" name="active" value="1"'.($this->active?' checked="checked"':'').' />');
	      break;
	      
	    case 'secure':
	      return array(lang('secure_page').':','<input type="hidden" name="secure" value="0"/><input class="pagecheckbox" type="checkbox" name="secure" value="1"'.($this->secure?' checked="checked"':'').' />');
	      break;
	      
	    case 'showinmenu':
	      return array(lang('showinmenu').':','<input type="hidden" name="showinmenu" value="0"/><input class="pagecheckbox" type="checkbox" value="1" name="showinmenu"'.($this->show_in_menu?' checked="checked"':'').' />');
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
	      return array(lang('pagealias').':','<input type="text" name="alias" value="'.$this->alias.'" />');
	      break;
	      
	    case 'image':
	      {
		$dir = $config['image_uploads_path'];
		$data = $this->GetPropertyValue('image');
		$dropdown = create_file_dropdown('image',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_');
		return array(lang('image').':',$dropdown);
	      }
	      break;
	      
	    case 'thumbnail':
	      {
		$dir = $config['image_uploads_path'];
		$data = $this->GetPropertyValue('thumbnail');
		$dropdown = create_file_dropdown('thumbnail',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_',0);
		return array(lang('thumbnail').':',$dropdown);
	      }
	      break;
	      
	    case 'titleattribute':
	      {
		return array(lang('titleattribute').':','<input type="text" name="titleattribute" maxlength="255" size="80" value="'.cms_htmlentities($this->titleattribute).'" />');
	      }
	      break;
	      
	    case 'accesskey':
	      {
		return array(lang('accesskey').':','<input type="text" name="accesskey" maxlength="5" value="'.cms_htmlentities($this->accesskey).'" />');
	      }
	      break;

	    case 'tabindex':
	      {
		return array(lang('tabindex').':','<input type="text" name="tabindex" maxlength="5" value="'.cms_htmlentities($this->tab_index).'" />');
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

	function add_child($node)
	{
		$node->set_parent($this);
		$node->tree = $this->tree;
		$this->children[] = $node;
	}

	function get_tree()
	{
		return $this->tree;
	}

	function depth()
	{
		$depth = 0;
		$currLevel = &$this;

		while ($currLevel->parentnode)
		{
			$depth++;
			$currLevel = &$currLevel->parentnode;
		}

		return $depth;
	}

	function get_level()
	{
		return $this->depth();
	}

	function getLevel()
	{
		return $this->depth();
	}

	function get_parent()
	{
		return $this->parentnode;
	}

	function set_parent($node)
	{
		$this->parentnode = $node;
	}

	function get_children_count()
	{
		return count($this->children);
	}

	function getChildrenCount()
	{
		return $this->get_children_count();
	}

	function &get_children()
	{
		if ($this->has_children())
		{
			//We know there are children, but no nodes have been
			//created yet.  We should probably do that.
			if (!$this->children_loaded)
			{
				$this->tree->load_child_nodes(-1, $this->lft, $this->rgt);
				//$this->tree->load_child_nodes($this->id);
			}
		}
		return $this->children;
	}

	function &get_flat_list()
	{
		$return = array();

		if ($this->has_children())
		{
			for ($i=0; $i<count($this->children); $i++)
			{
				$return[] = &$this->children[$i];
				$return = array_merge($return, $this->children[$i]->get_flat_list());
			}
		}

		return $return;
	}

	function &getFlatList()
	{
		$tmp =& $this->get_flat_list();
		return $tmp;
	}

	function get_content()
	{
		return $this;
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
	public function ContentProperties()
	{
		$this->SetInitialValues();
		$this->SetAllowedPropertyNames(NULL);
	}

    /**
     * Sets object to some sane initial values
     */
	public function SetInitialValues()
	{
		$this->mPropertyNames = array();
		$this->mPropertyTypes = array();
		$this->mPropertyValues = array();
	}

	public function HasProperty($name)
	{
		#debug_buffer($this->mPropertyNames);
		if (!isset($this->mPropertyNames))
		$this->mPropertyNames = array();
		return in_array($name, $this->mPropertyNames);
	}

	public function Add($type, $name, $defaultvalue='')
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

	public function GetValue($name)
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

	public function SetValue($name, $value)
	{
		if (count($this->mPropertyValues) > 0)
		{
			if (in_array($name, $this->mPropertyNames))
			{
				$this->mPropertyValues[$name] = $value;
			}
		}
	}

	public function Load($content_id)
	{
		debug_buffer('load properties called');
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

	public function Save($content_id)
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
					    //$sql_queries .= "<p>".$db->sql."</p>\n";
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
					    //$sql_queries .= "<p>".$db->sql."</p>\n";
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

    public function Delete($content_id)
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
    public function SetAllowedPropertyNames($array)
    {
        $this->mAllowedPropertyNames = $array;
    }
    
    /**
     * Subclasses should fill this array with strings containing the name of
     * the allowed property
     * @return array
    */
    public function GetAllowedPropertyNames()
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
class CMSModuleContentType extends CmsContentBase
{
	//What module do I belong to?  (needed for things like Lang to work right)
	public function ModuleName()
	{
		return '';
	}

	public function Lang($name, $params=array())
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
	public function GetModuleInstance() 
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

class ContentBase extends CmsContentBase
{
	
}

# vim:ts=4 sw=4 noet
?>
