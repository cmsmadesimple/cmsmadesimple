<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
# CMS - CMS Made Simple
# (c)2004 by Ted Kulp (tedkulp@users.sf.net)
# This project's homepage is: http://www.cmsmadesimple.org
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
#$Id: class.content.inc.php 6905 2011-02-20 22:23:40Z calguy1000 $

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
abstract class ContentBase
{
  const TAB_MAIN = 'aa_main_tab__';
  const TAB_NAV = 'zz_1nav_tab__';
  const TAB_LOGIC = 'zz_2logic_tab__';
  const TAB_OPTIONS = 'zz_3options_tab__';
  const TAB_PERMS = 'zz_4perms_tab__';
  
  /**
   * The unique ID identifier of the element
   * Integer
   *
   * @internal
   */
  protected $mId = -1;

  /**
   * The name of the element (like a filename)
   * String
   */
  protected $mName = '';

  /**
   * The owner of the content
   * Integer
   *
   * @internal
   */
  protected $mOwner = -1;

  /**
   * The properties part of the content. This is an object of the good type.
   * It should contain all treatments specific to this type of content
   *
   * @internal
   */
  protected $_props;

  /**
   * The ID of the parent, 0 if none
   * Integer
   */
  protected $mParentId = -2;

  /**
   * The old parent id... only used on update
   *
   * @internal
   */
  protected $mOldParentId = -1;

  /**
   * This is used too often to not be part of the base class
   *
   * @internal
   */
  protected $mTemplateId = -1;

  /**
   * The item order of the content in his level
   * Integer
   */
  protected $mItemOrder = -1;

  /**
   * The old item order... only used on update
   *
   * @internal
   */
  protected $mOldItemOrder = -1;

  /**
   * The metadata (head tags) for this content
   *
   * @internal
   */
  protected $mMetadata = '';

  /**
   * @internal
   */
  protected $mTitleAttribute = '';

  /**
   * @internal
   */
  protected $mAccessKey = '';

  /**
   * @internal
   */
  protected $mTabIndex = '';

  /**
   * The full hierarchy of the content
   * String of the form : 1.4.3
   */
  protected $mHierarchy = '';

  /**
   * The full hierarchy of the content ids
   * String of the form : 1.4.3
   */
  protected $mIdHierarchy = '';

  /**
   * The full path through the hierarchy
   * String of the form : parent/parent/child
   *
   * @internal
   */
  protected $mHierarchyPath = '';

  /**
   * What should be displayed in a menu
   *
   * @internal
   */
  protected $mMenuText = '';

  /**
   * Is the content active ?
   * Integer : 0 / 1
   *
   * @internal
   */
  protected $mActive = false;

  /**
   * Alias of the content
   *
   * @internal
   */
  protected $mAlias;

  /**
   * Old Alias of the content
   *
   * @internal
   */
  protected $mOldAlias;

  /**
   * Cachable?
   *
   * @internal
   */
  protected $mCachable;

  /**
   * Secure?
   *
   * @internal
   */
  protected $mSecure = 0;

  /**
   * URL
   *
   * @internal
   */
  protected $mURL = '';

  /**
   * Should it show up in the menu?
   *
   * @internal
   */
  protected $mShowInMenu = false;

  /**
   * Is this page the default?
   *
   * @internal
   */
  protected $mDefaultContent = false;
	
  /**
   * Last user to modify this content
   *
   * @internal
   */
  protected $mLastModifiedBy = -1;

  /**
   * Creation date
   * Date
   *
   * @internal
   */
  protected $mCreationDate = '';

  /**
   * Modification date
   * Date
   *
   * @internal
   */
  protected $mModifiedDate = '';
    
  /**
   * Additional Editors Array
   *
   * @internal
   */
  protected $mAdditionalEditors;
	
  /*
   * state or meta information
   *
   * @internal
   */
  private $_attributes;
  private $_prop_defaults;
  private $_editable_properties;

  /************************************************************************/
  /* Constructor related													*/
  /************************************************************************/

  /**
   * Generic constructor. Runs the SetInitialValues fuction.
   */
  public function __construct()
  {
    $this->SetInitialValues();
    $this->SetProperties();
  }

  /**
   * Sets object to some sane initial values
   *
   * @abstract
   * @internal
   */
  protected function SetInitialValues()
  {
  }

  /**
   * Subclasses should override this to set their property types using a lot
   * of mProperties.Add statements
   *
   * @abstract
   */
  protected function SetProperties()
  {
    $this->AddProperty('title',1,self::TAB_MAIN,1);

    $this->AddProperty('parent',2,self::TAB_NAV,1);
    $this->AddProperty('menutext',1,self::TAB_NAV,1);
    $this->AddProperty('page_url',3,self::TAB_NAV);
    $this->AddProperty('showinmenu',4,self::TAB_NAV);
    $this->AddProperty('titleattribute',5,self::TAB_NAV);
    $this->AddProperty('accesskey',6,self::TAB_NAV);
    $this->AddProperty('tabindex',7,self::TAB_NAV);	  
    $this->AddProperty('target',8,self::TAB_NAV);      

    $this->AddProperty('alias',1,self::TAB_OPTIONS);
    $this->AddProperty('active',2,self::TAB_OPTIONS);
    $this->AddProperty('secure',3,self::TAB_OPTIONS);
    $this->AddProperty('cachable',4,self::TAB_OPTIONS);
    $this->AddProperty('image',5,self::TAB_OPTIONS);
    $this->AddProperty('thumbnail',6,self::TAB_OPTIONS);
    $this->AddProperty('extra1',7,self::TAB_OPTIONS);
    $this->AddProperty('extra2',8,self::TAB_OPTIONS);
    $this->AddProperty('extra3',9,self::TAB_OPTIONS);
	  
    $this->AddProperty('owner',1,self::TAB_PERMS);
    $this->AddProperty('additionaleditors',2,self::TAB_PERMS);
  }

    
  /************************************************************************/
  /* Functions giving access to needed elements of the content			*/
  /************************************************************************/
  
  public function __clone()
  {
    $this->mId = -1;
    $this->mItemOrder = -1;
    $this->mOldItemOrder = -1;
    $this->mURL = '';
    $this->mAlias = '';
  }

  /**
   * Returns the ID
   */
  public function Id()
  {
    return $this->mId;
  }

  /**
   * Set the numeric id of the content item
   *
   * @param integer Integer id
   * @access private
   * @internal
   */
  public function SetId($id)
  {
    $this->mId = $id;
  }

  /**
   * Returns a friendly name for this content type
   *
   * @abstract
   * @return string
   */
  abstract public function FriendlyName();

  /**
   * Returns the Name
   *
   * @return string
   */
  public function Name()
  {
    return $this->mName;
  }

  /**
   * Set the the page name
   *
   * @param string The name.
   */
  public function SetName($name)
  {
    $this->mName = $name;
  }

  /**
   * Returns the Alias
   *
   * @return string
   */
  public function Alias()
  {
    return $this->mAlias;
  }

  /**
   * Returns the OldAlias
   * The old alias is used when editing pages
   *
   * @internal
   * @deprecated
   * @return string
   */
  public function OldAlias()
  {
    return $this->mOldAlias;
  }

  /**
   * Returns the Type
   *
   * @return string
   */
  public function Type()
  {
    return strtolower(get_class($this));
  }

  /**
   * Returns the Owner
   *
   * @return integer
   */
  public function Owner()
  {
    return $this->mOwner;
  }

  /**
   * Set the page owner.
   * No validation is performed.
   *
   * @param integer Owner's user id
   */
  public function SetOwner($owner)
  {
    $owner = (int)$owner;
    if( $owner <= 0 ) return;
    $this->mOwner = $owner;
  }

  /**
   * Returns the Metadata
   *
   * @return string
   */
  public function Metadata()
  {
    return $this->mMetadata;
  }

  /**
   * Content object handles the alias
   *
   * @abstract
   * @return boolean default is false.
   */
  public function HandlesAlias()
  {
    return false;
  }

  /**
   * Set the page metadata
   * 
   * @param string The metadata
   */
  public function SetMetadata($metadata)
  {
    $this->mMetadata = $metadata;
  }

  /**
   * Return the page tabindex value
   *
   * @return integer
   */
  public function TabIndex()
  {
    return $this->mTabIndex;
  }

  /**
   * Set the page tabindex value
   *
   * @param integer tabindex
   */
  public function SetTabIndex($tabindex)
  {
    $this->mTabIndex = $tabindex;
  }

  /**
   * Return the page title attribute
   *
   * @return string
   */
  public function TitleAttribute()
  {
    return $this->mTitleAttribute;
  }

  /**
   * Retrieve the creation date of this content object.
   *
   * @return int Unix Timestamp of the creation date
   */
  public function GetCreationDate()
  {
    return strtotime($this->mCreationDate);
  }

  /**
   * Retrieve the date of the last modification of this content object.
   *
   * @return int Unix Timestamp of the modification date.
   */
  public function GetModifiedDate()
  {
    return strtotime($this->mModifiedDate);
  }

  /**
   * Set the title attribute of the page
   *
   * @param string The title attribute
   */
  public function SetTitleAttribute($titleattribute)
  {
    $this->mTitleAttribute = $titleattribute;
  }

  /**
   * Get the access key (for accessibility) for this page.
   *
   * @return string
   */
  public function AccessKey()
  {
    return $this->mAccessKey;
  }

  /**
   * Set the access key (for accessibility) for this page
   *
   * @param string Access Key
   */
  public function SetAccessKey($accesskey)
  {
    $this->mAccessKey = $accesskey;
  }

  /**
   * Returns the id of this pages parent.
   *
   * @return int -1 if this page has no parent.  Positive integer otherwise.
   */
  public function ParentId()
  {
    return $this->mParentId;
  }

  /**
   * Sets the parent of this page.
   *
   * @param int The numeric page parent id.  Use -1 for no parent.
   */
  public function SetParentId($parentid)
  {
    $this->mParentId = $parentid;
  }

   /**
    * Return the id of the page template associated with this content page.
    *
    * @return integer.
    */
   public function TemplateId()
   {
     return $this->mTemplateId;
   }

   /**
    * Set the id of the page template associated with this content page.
    *
    * @param integer
    */
   public function SetTemplateId($templateid)
   {
     $this->mTemplateId = $templateid;
   }

   /**
    * Returns the ItemOrder
    * The ItemOrder is used to specify the order of this page within the parent.
    *
    * @return int
    */
   public function ItemOrder()
   {
     return $this->mItemOrder;
   }

   /**
    * Sets the ItemOrder
    * The ItemOrder is used to specify the order of this page within the parent.
    *
    * @internal
    * @param int the itemorder.
    */
   public function SetItemOrder($itemorder)
   {
     $this->mItemOrder = $itemorder;
   }


   /**
    * Returns the OldItemOrder
    * The OldItemOrder is used to specify the item order before changes were done
    *
    * @deprecated
    * @return int
    */
   public function OldItemOrder()
   {
     return $this->mItemOrder;
   }

   /**
    * Sets the ItemOrder
    * The ItemOrder is used to specify the order of this page within the parent.
    * The OldItemOrder is used when editing pages.
    *
    * @internal
    * @deprecated
    * @param int the itemorder.
    */
   public function SetOldItemOrder($itemorder)
   {
     $this->mOldItemOrder = $itemorder;
   }

   /**
    * Returns the Hierarchy
    *
    * @return string
    */
   public function Hierarchy()
   {
      $gCms = cmsms();
      $contentops = $gCms->GetContentOperations();
      return $contentops->CreateFriendlyHierarchyPosition($this->mHierarchy);
   }

   /**
    * Sets the Hierarchy
    *
    * @internal
    * @param string
    */
   public function SetHierarchy($hierarchy)
   {
     $this->mHierarchy = $hierarchy;
   }

   /**
    * Returns the Id Hierarchy
    *
    * @return string
    */
   final public function IdHierarchy()
   {
     return $this->mIdHierarchy;
   }


   /**
    * Returns the Hierarchy Path
    *
    * @return string
    */
   final public function HierarchyPath()
   {
     return $this->mHierarchyPath;
   }

   /**
    * Returns the Active state
    *
    * @return boolean
    */
   public function Active()
   {
     return $this->mActive;
   }

   /**
    * Sets this page as active
    *
    * @param boolean
    */
   public function SetActive($active)
   {
     $this->mActive = $active;
   }

   /**
    * Returns whether preview should be available for this content type
    *
    * @abstract
    * @return boolean
    */
   public function HasPreview()
   {
     return FALSE;
   }

   /**
    * Returns whether it should show in the menu
    *
    * @return boolean
    */
   public function ShowInMenu()
   {
     return $this->mShowInMenu;
   }

   /**
    * Sets whether this page should be shown in menus
    *
    * @param boolean
    */
   public function SetShowInMenu($showinmenu)
   {
     $this->mShowInMenu = $showinmenu;
   }

   /**
    * Returns if the page is the default
    *
    * @return boolean
    */
   final public function DefaultContent()
   {
     if( !$this->IsDefaultPossible() ) return FALSE;
     return $this->mDefaultContent;
   }

   /**
    * Sets if this page should be considered the default
    * Note: does not modify the flags for any other content page.
    *
    * @param boolean
    */
   public function SetDefaultContent($defaultcontent)
   {
     $this->mDefaultContent = $defaultcontent;
   }

   /**
    * Return wether this page is cachable
    *
    * @return boolean
    */
   public function Cachable()
   {
     return $this->mCachable;
   }

   /**
    * Set wether this page is cachable
    *
    * @param boolean
    */
    public function SetCachable($cachable)
    {
		$this->mCachable = $cachable;
    }

   /**
    * Return wether this page should be accessed via a secure protocol.
    * The secure flag effectsw wether the ssl protocol and appropriate config entries are used when generating urls to this page.
    *
    * @return boolean
    */
    public function Secure()
    {
      return $this->mSecure;
    }

   /**
    * Set wether this page should be accessed via a secure protocol.
    * The secure flag effectsw wether the ssl protocol and appropriate config entries are used when generating urls to this page.
    *
    * @return boolean
    */
    public function SetSecure($secure)
    {
		$this->mSecure = $secure;
    }

    /**
     * Return the page url (if any) associated with this content page.
     * Note: some content types do not support page urls.
     *
     * @return string
     */
    public function URL()
    {
      return $this->mURL;
    }

    /**
     * Set the page url (if any) associated with this content page.
     * Note: some content types do not support page urls.
     * The url should be relative to the root url.  i.e: /some/path/to/the/page
     *
     * @param string
     */
    public function SetURL($url)
    {
      $this->mURL = $url;
    }

    /**
     * Return the last modified user for this item
     * This is usually set on save.
     * 
     * @return Integer admin user id.
     */
    public function LastModifiedBy()
    {
      return $this->mLastModifiedBy;
    }

    /**
     * Set the last modified date for this item
     * This is usually set on save.
     * 
     * @param Date
     */
    public function SetLastModifiedBy($lastmodifiedby)
    {
      $this->mLastModifiedBy = $lastmodifiedby;
    }

    /**
     * Indicates wether this content type requires an alias
     *
     * @abstract
     * @return boolean
     */
    public function RequiresAlias()
    {
      return TRUE;
    }

    /**
     * Indicates wether this content type is viewable (i.e: can be rendered)
     * some content types (like redirection links) are not viewable.
     *
     * @abstract
     * @return boolean Default is True
     */
    public function IsViewable()
    {
      return TRUE;
    }

    /**
     * Indicates wether this content type can be the default page for a CMSMS website
     *
     * @abstract
     * @returns boolean Default is false
     */
    public function IsDefaultPossible()
    {
      return FALSE;
    }

    /**
     * Set the page alias for this content page.  
     * If an empty alias is supplied, and depending upon the doAutoAliasIfEnabled flag, and config entries
     * a suitable alias may be calculated from other data in the page object
     * This method relies on the menutext and the name of the content page already being set.
     *
     * @param string The alias
     * @param boolean Wether an alias should be calculated or not.
     */
    public function SetAlias($alias = '', $doAutoAliasIfEnabled = true)
    {
		$gCms = cmsms();
		$config = $gCms->GetConfig();

		$tolower = false;

		if ($alias == '' && $doAutoAliasIfEnabled && $config['auto_alias_content'] == true) {
			$alias = trim($this->mMenuText);
			if ($alias == '') {
				$alias = trim($this->mName);
			}

			$tolower = true;
			$alias = munge_string_to_url($alias, $tolower);
			// Make sure auto-generated new alias is not already in use on a different page, if it does, add "-2" to the alias
			$contentops = $gCms->GetContentOperations();
			$error = $contentops->CheckAliasError($alias, $this->Id());
			if ($error !== FALSE) {
				if (FALSE == empty($alias)) {
					$alias_num_add = 2;
					// If a '-2' version of the alias already exists
					// Check the '-3' version etc.
					while ($contentops->CheckAliasError($alias.'-'.$alias_num_add) !== FALSE) {
						$alias_num_add++;
					}
					$alias .= '-'.$alias_num_add;
				}
				else {
					$alias = '';
				}
			}
		}

		$this->mAlias = munge_string_to_url($alias, $tolower);
    } 
	
    /**
     * Returns the menu text for this content page
     *
     * @return string
     */
    public function MenuText()
    {
      return $this->mMenuText;
    }

    /**
     * Sets the menu text for this content page
     *
     * @param string
     */
    public function SetMenuText($menutext)
    {
      $this->mMenuText = $menutext;
    }

    /**
     * Returns number of immediate child-content items of this content
     *
     * @return integer
     */
    public function ChildCount()
    {
      $hm = cmsms()->GetHierarchyManager();
      $node = $hm->getNodeById($this->mId);
      if( $node ) {
	return $node->count_children();
      }
    }

    /**
     * Returns the properties
     *
     * @return array
     */
    public function Properties()
    {
      return $this->_props;
    }

    /**
     * Test wether this content page has the named property
     * Properties will be loaded from the database if necessary.
     *
     * @return boolean
     */
    public function HasProperty($name)
    {
      if( !is_array($this->_props) ) {
	$this->_load_properties();
      }

      if( !is_array($this->_props) ) {
	return FALSE;
      }
      return in_array($name,array_keys($this->_props));
    }
    
    /**
     * Get the value for the named property
     *
     * @return mixed String value, or null if the property does not exist.
     */
    public function GetPropertyValue($name)
    {
      if( $this->HasProperty($name) ) {
	return $this->_props[$name];
      }
    }

    private function _load_properties()
    {
      if( $this->mId <= 0 ) return FALSE;

      $this->_props = array();
      $db = cmsms()->GetDb();
      $query = 'SELECT * FROM '.cms_db_prefix().'content_props WHERE content_id = ?';
      $dbr = $db->GetArray($query,array($this->mId));

      foreach( $dbr as $row ) {
	$this->_props[$row['prop_name']] = $row['content'];
      }
      return TRUE;
    }

    private function _save_properties()
    {
      if( $this->mId <= 0 ) return FALSE;
      if( !is_array($this->_props) || count($this->_props) == 0 ) return FALSE;
	    
      $db = cmsms()->GetDb();
      $query = 'SELECT prop_name FROM '.cms_db_prefix().'content_props WHERE content_id = ?';
      $gotprops = $db->GetCol($query,array($this->mId));

      $now = $db->DbTimeStamp(time());
      $iquery = 'INSERT INTO '.cms_db_prefix()."content_props
                    (content_id,type,prop_name,content,modified_date)
                    VALUES (?,?,?,?,$now)";
      $uquery = 'UPDATE '.cms_db_prefix()."content_props SET content = ?, modified_date = $now WHERE content_id = ? AND prop_name = ?";
	  
      foreach( $this->_props as $key => $value ) {
	if( in_array($key,$gotprops) ) {
	  // update
	  $dbr = $db->Execute($uquery,array($value,$this->mId,$key));
	}
	else {
	  // insert
	  $dbr = $db->Execute($iquery,array($this->mId,'string',$key,$value));
	}
      }
      return TRUE;
    }

    /**
     * Set the value of a the named property.
     * This method will load properties for this content page if necessary.
     *
     * @param string The property name
     * @param string  The property value.
     */
    public function SetPropertyValue($name, $value)
    {
      if( !is_array($this->_props) ) $this->_load_properties();
      $this->_props[$name] = $value;
    }
	
    /**
     * Set the value of a the named property.
     * This method will not load properties
     *
     * @param string The property name
     * @param string  The property value.
     */
    public function SetPropertyValueNoLoad($name,$value)
    {
      if( !is_array($this->_props) ) $this->_props = array();
      $this->_props[$name] = $value;
    }

    /**
     * Function content types to use to say whether or not they should show
     * up in lists where parents of content are set.  This will default to true,
     * but should be used in cases like Separator where you don't want it to 
     * have any children.
     * 
     * @since 0.11
     * @abstract
     * @return boolean Default TRUE
     */
    public function WantsChildren()
    {
      return true;
    }

    /**
     * Should this link be used in various places where a link is the only
     * useful output?  (Like next/previous links in cms_selflink, for example)
     *
     * @abstract
     * @return boolean Default TRUE
     */
    public function HasUsableLink()
    {
      return true;
    }

    /**
     * Is this content type copyable ?
     *
     * @abstract
     * @return boolean default FALSE
     */
    public function IsCopyable()
    {
      return FALSE;
    }

    /**
     * Indicates wether this is a system page type.
     *
     * @abstract
     * @return boolean default FALSE
     */
    public function IsSystemPage()
    {
      return FALSE;
    }

    /**
     * Indicates wether ths page type uses a template.
     * i.e: some content types like sectionheader and separator do not.
     *
     * @since 2.0
     * @abstract
     * @return boolean default FALSE
     */
    public function HasTemplate()
    {
      return FALSE;
    }

    /************************************************************************/
    /* The rest																*/
    /************************************************************************/

    /**
     * Load the content of the object from an array
     * This method modifies the current object.
     *
     * There is no check on the data provided, because this is the job of
     * ValidateData
     *
     * @returns	bool		If it fails, the object comes back to initial values and returns FALSE
     *				If everything goes well, it returns TRUE
     */
    function LoadFromData(&$data, $loadProperties = false)
    {
      $result = true;
      $this->mId                         = $data["content_id"];
      $this->mName                       = $data["content_name"];
      $this->mAlias                      = $data["content_alias"];
      $this->mOldAlias                   = $data["content_alias"];
      $this->mOwner                      = $data["owner_id"];
      $this->mParentId                   = $data["parent_id"];
      $this->mOldParentId                = $data["parent_id"];
      $this->mTemplateId                 = $data["template_id"];
      $this->mItemOrder                  = $data["item_order"];
      $this->mOldItemOrder               = $data["item_order"];
      $this->mMetadata                   = $data['metadata'];
      $this->mHierarchy                  = $data["hierarchy"];
      $this->mIdHierarchy                = $data["id_hierarchy"];
      $this->mHierarchyPath              = $data["hierarchy_path"];
      $this->mMenuText                   = $data['menu_text'];
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

      if ($loadProperties == true) {
	$this->_load_properties();

	if (!is_array($this->_props) ) {
	  $result = false;
	}
      }

      if (false == $result) {
	$this->SetInitialValues();
      }

      $this->Load();
      return $result;
    }

    /**
     * Callback function for content types to use to preload content or other things if necessary.  This
     * is called right after the content is loaded from the database.
     *
     * @abstract
     */
    protected function Load()
    {
    }

    /**
     * Save or update the content
     *
     * @todo This function should return something (or throw an exception)
     */
    public function Save()
    {
      Events::SendEvent('Core', 'ContentEditPre', array('content' => &$this));

      if( !is_array($this->_props) ) {
	debug_buffer('save is loading properties');
	$this->_load_properties();
      }

      if (-1 < $this->mId) {
	$this->Update();
      }
      else {
	$this->Insert();
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
     *
     * @todo this function should return something, or throw an exception.
     */
    protected function Update()
    {
      $gCms = cmsms();
      $db = $gCms->GetDb();
      $config = $gCms->GetConfig();
      $result = false;

      // Figure out the item_order (if necessary)
      if ($this->mItemOrder < 1) {
	$query = "SELECT ".$db->IfNull('max(item_order)','0')." as new_order FROM ".cms_db_prefix()."content WHERE parent_id = ?";
	$row = $db->GetRow($query,array($this->mParentId));

	if ($row) {
	  if ($row['new_order'] < 1) {
	    $this->mItemOrder = 1;
	  }
	  else {
	    $this->mItemOrder = $row['new_order'] + 1;
	  }
	}
      }

      $this->mModifiedDate = trim($db->DBTimeStamp(time()), "'");

      $query = "UPDATE ".cms_db_prefix()."content SET content_name = ?, owner_id = ?, type = ?, template_id = ?, parent_id = ?, active = ?, default_content = ?, show_in_menu = ?, cachable = ?, secure = ?, page_url = ?, menu_text = ?, content_alias = ?, metadata = ?, titleattribute = ?, accesskey = ?, tabindex = ?, modified_date = ?, item_order = ?, last_modified_by = ? WHERE content_id = ?";
      $dbresult = $db->Execute($query, array(
					     $this->mName,
					     $this->mOwner,
					     $this->Type(),
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
					     $this->mLastModifiedBy,
					     $this->mId
					     ));

      if ($this->mOldParentId != $this->mParentId) {
	// Fix the item_order if necessary
	$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order - 1 WHERE parent_id = ? AND item_order > ?";
	$result = $db->Execute($query, array($this->mOldParentId,$this->mOldItemOrder));

	$this->mOldParentId = $this->mParentId;
	$this->mOldItemOrder = $this->mItemOrder;
      }

      if (isset($this->mAdditionalEditors)) {
	$query = "DELETE FROM ".cms_db_prefix()."additional_users WHERE content_id = ?";
	$db->Execute($query, array($this->Id()));

	foreach ($this->mAdditionalEditors as $oneeditor) {
	  $new_addt_id = $db->GenID(cms_db_prefix()."additional_users_seq");
	  $query = "INSERT INTO ".cms_db_prefix()."additional_users (additional_users_id, user_id, content_id) VALUES (?,?,?)";
	  $db->Execute($query, array($new_addt_id, $oneeditor, $this->Id()));
	}
      }

      if( is_array($this->_props) && count($this->_props) ) {
	// :TODO: There might be some error checking there
	$this->_save_properties();
      }

      cms_route_manager::del_static('','__CONTENT__',$this->mId);
      if( $this->mURL != '' ) {
	$route = CmsRoute::new_builder($this->mURL,'__CONTENT__',$this->mId,null,TRUE);;
	cms_route_manager::add_static($route);
      }
    }

    /**
     * Insert the content in the db
     *
     * @todo this function should return something
     */
    # :TODO: This function should return something
    # :TODO: Take care bout hierarchy here, it has no value !
    # :TODO: Figure out proper item_order
    protected function Insert()
    {
      $gCms = cmsms();
      $db = $gCms->GetDb();
      $config = $gCms->GetConfig();

      $result = false;

      // Figure out the item_order
      if ($this->mItemOrder < 1) {
	$query = "SELECT max(item_order) as new_order FROM ".cms_db_prefix()."content WHERE parent_id = ?";
	$row = $db->Getrow($query, array($this->mParentId));

	if ($row) {
	  if ($row['new_order'] < 1) {
	    $this->mItemOrder = 1;
	  }
	  else {
	    $this->mItemOrder = $row['new_order'] + 1;
	  }
	}
      }

      $newid = $db->GenID(cms_db_prefix()."content_seq");
      $this->mId = $newid;

      $this->mModifiedDate = $this->mCreationDate = trim($db->DBTimeStamp(time()), "'");

      $query = "INSERT INTO ".cms_db_prefix()."content (content_id, content_name, content_alias, type, owner_id, parent_id, template_id, item_order, hierarchy, id_hierarchy, active, default_content, show_in_menu, cachable, secure, page_url, menu_text, metadata, titleattribute, accesskey, tabindex, last_modified_by, create_date, modified_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

      $dbresult = $db->Execute($query, array(
					     $newid,
					     $this->mName,
					     $this->mAlias,
					     $this->Type(),
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
					     $this->mMetadata,
					     $this->mTitleAttribute,
					     $this->mAccessKey,
					     $this->mTabIndex,
					     $this->mLastModifiedBy,
					     $this->mModifiedDate,
					     $this->mCreationDate
					     ));

      if (! $dbresult) {
	die($db->sql.'<br/>'.$db->ErrorMsg());
      }

      if (is_array($this->_props) && count($this->_props)) {
	// :TODO: There might be some error checking there
	debug_buffer('save from ' . __LINE__);
	$this->_save_properties();
      }
      if (isset($this->mAdditionalEditors)) {
	foreach ($this->mAdditionalEditors as $oneeditor) {
	  $new_addt_id = $db->GenID(cms_db_prefix()."additional_users_seq");
	  $query = "INSERT INTO ".cms_db_prefix()."additional_users (additional_users_id, user_id, content_id) VALUES (?,?,?)";
	  $db->Execute($query, array($new_addt_id, $oneeditor, $this->Id()));
	}
      }

      if( $this->mURL != '' ) {
	$route = CmsRoute::new_builder($this->mURL,'__CONTENT__',$this->mId,'',TRUE);
	cms_route_manager::add_static($route);
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
     * @abstract
     * @returns	FALSE if data is ok, and an array of invalid parameters else
     */
    public function ValidateData()
    {
      $errors = array();

      if ($this->mParentId < -1) {
	$errors[] = lang('invalidparent');
	$result = false;
      }
	  
      if ($this->mName == '') {
	if ($this->mMenuText != '') {
	  $this->mName = $this->mMenuText;
	}
	else {
	  $errors[]= lang('nofieldgiven',array(lang('title')));
	  $result = false;
	}
      }
	  
      if ($this->mMenuText == '') {
	if ($this->mName != '') {
	  $this->mMenuText = $this->mName;
	}
	else {
	  $errors[]=lang('nofieldgiven',array(lang('menutext')));
	  $result = false;
	}
      }
		
      if (!$this->HandlesAlias()) {
	if ($this->mAlias != $this->mOldAlias || ($this->mAlias == '' && $this->RequiresAlias()) ) {
	  $gCms = cmsms();
	  $contentops = $gCms->GetContentOperations();
	  $error = $contentops->CheckAliasError($this->mAlias, $this->mId);
	  if ($error !== FALSE) {
	    $errors[]= $error;
	    $result = false;
	  }
	}
      }

      $auto_type = content_assistant::auto_create_url();
      if( $this->mURL == '' && get_site_preference('content_autocreate_urls') ) {
	// create a valid url.
	if( !$this->DefaultContent() ) {
	  if( get_site_preference('content_autocreate_flaturls',0) ) {
	    // the default url is the alias... but not synced to the alias.
	    $this->mURL = $this->mAlias;
	  }
	  else {
	    // if it don't explicitly say 'flat' we're creating a hierarchical url.
	    $gCms = cmsms();
	    $tree = $gCms->GetHierarchyManager();
	    $node = $tree->find_by_tag('id',$this->ParentId());
	    $stack = array($this->mAlias);
	    $parent_url = '';
	    $count = 0;
	    while( $node ) {
	      $tmp_content = $node->GetContent();
	      if( $tmp_content ) {
		$tmp = $tmp_content->URL();
		if( $tmp != '' && $count == 0 ) {
		  // try to build the url out of the parent url.
		  $parent_url = $tmp;
		  break;
		}
		array_unshift($stack,$tmp_content->Alias());
	      }
	      $node = $node->GetParent();
	      $count++;
	    }

	    if( $parent_url != '' ) {
	      // woot, we got a prent url.
	      $this->mURL = $parent_url.'/'.$this->mAlias;
	    }
	    else {
	      $this->mURL = implode('/',$stack);
	    }
	  }
	}
      }
      if( $this->mURL == '' && 
	  get_site_preference('content_mandatory_urls') && 
	  !$this->mDefaultContent &&
	  $this->HasUsableLink() ) {
	// page url is empty and mandatory
	$errors[] = lang('content_mandatory_urls');
      }
      else if( $this->mURL != '' ) {
	// page url is not empty, check for validity.
	$this->mURL = strtolower(trim($this->mURL," /\t\r\n\0\x08")); // silently delete bad chars. and convert to lowercase.
	if( $this->mURL != '' && !content_assistant::is_valid_url($this->mURL,$this->mId) ) {
	  // and validate the URL.
	  $errors[] = lang('invalid_url2');
	}
      }

      return (count($errors) > 0?$errors:FALSE);
    }

    /**
     * Delete the current content object from the database.
     *
     * @todo this function should return something, or throw an exception
     */
    function Delete()
    {
      $gCms = cmsms();
      $config = $gCms->GetConfig();
      Events::SendEvent('Core', 'ContentDeletePre', array('content' => &$this));
      $db = $gCms->GetDb();
      $result = false;

      if (-1 > $this->mId) {
	// invalid content id.
      }
      else {
	$query = "DELETE FROM ".cms_db_prefix()."content WHERE content_id = ?";
	$dbresult = $db->Execute($query, array($this->mId));

	// Fix the item_order if necessary
	$query = "UPDATE ".cms_db_prefix()."content SET item_order = item_order - 1 WHERE parent_id = ? AND item_order > ?";
	$result = $db->Execute($query,array($this->ParentId(),$this->ItemOrder()));

	cms_cache_handler::get_instance()->erase('contentcache');

	// DELETE properties
	$query = 'DELETE FROM '.cms_db_prefix().'content_props WHERE content_id = ?';
	$result = $db->Execute($query,array($this->mId));
	$this->_props = null;

	// Delete additional editors.
	$query = 'DELETE FROM '.cms_db_prefix().'additional_users WHERE content_id = ?';
	$result = $db->Execute($query,array($this->mId));
	$this->mAdditionalEditors = null;

	// Delete route
	if( $this->mURL != '' ) {
	  cms_route_manager::del_static($this->mURL);
	}
      }

      Events::SendEvent('Core', 'ContentDeletePost', array('content' => &$this));
    }

    /**
     * Function for the subclass to parse out data for it's parameters (usually from $_POST)
     * This method is typically called from an editor form to allow modifying the content object from 
     * form input fields.
     *
     * @abstract
     * @return void
     */
    public function FillParams($params,$editing = false)
    {
      // content property parameters
      $parameters = array('extra1','extra2','extra3','image','thumbnail');
      foreach ($parameters as $oneparam) {
	if (isset($params[$oneparam])) {
	  $this->SetPropertyValue($oneparam, $params[$oneparam]);
	}
      }

      // go through the list of base parameters
      // setting them from params

      // title
      if (isset($params['title'])) {
	$this->mName = $params['title'];
      }

      // menu text
      if (isset($params['menutext'])) {
	$this->mMenuText = $params['menutext'];
      }

      // parent id
      if( isset($params['parent_id']) ) {
	if ($this->mParentId != $params['parent_id']) {
	  $this->mHierarchy = '';
	  $this->mItemOrder = -1;
	}
	$this->mParentId = $params['parent_id'];
      }

      // active
      if (isset($params['active'])) {
	$this->mActive = $params['active'];
	if( $this->DefaultContent() ) {
	  $this->mActive = 1;
	}
      }

      // show in menu
      if (isset($params['showinmenu'])) {
	$this->mShowInMenu = $params['showinmenu'];
      }

      // alias
      $tmp = '';
      if( isset($params['alias']) ) {
	$tmp = trim($params['alias']);
      }
      if( !$editing || isset($params['alias']) ) {
	// the alias param may not exist (depending upon permissions)
	// this method will set the alias to the supplied value if it is set
	// or auto-generate one, when adding a new page.
	$this->SetAlias($tmp);
      }

      // target
      if (isset($params['target'])) {
	$val = $params['target'];
	if( $val == '---' ) {
	  $val = '';
	}
	$this->SetPropertyValue('target', $val);
      } 

      // title attribute
      if (isset($params['titleattribute'])) {
	$this->mTitleAttribute = $params['titleattribute'];
      }

      // accesskey
      if (isset($params['accesskey'])) {
	$this->mAccessKey = $params['accesskey'];
      }

      // tab index
      if (isset($params['tabindex'])) {
	$this->mTabIndex = $params['tabindex'];
      }

      // cachable
      if (isset($params['cachable'])) {
	$this->mCachable = $params['cachable'];
      }
      else {
	$this->_handleRemovedBaseProperty('cachable','mCachable');
      }

      // secure
      if (isset($params['secure'])) {
	$this->mSecure = $params['secure'];
      }
      else {
	$this->_handleRemovedBaseProperty('secure','mSecure');
      }

      // url
      if (isset($params['page_url'])) {
	$this->mURL = $params['page_url'];
      }
      else {
	$this->_handleRemovedBaseProperty('page_url','mURL');
      }

      // owner
      if (isset($params["ownerid"])) {
	$this->SetOwner($params["ownerid"]);
      }

      // additional editors
      if (isset($params["additional_editors"])) {
	$addtarray = array();
	if( is_array($params['additional_editors']) ) {
	  foreach ($params["additional_editors"] as $addt_user_id) {
	    $addtarray[] = $addt_user_id;
	  }
	}
	$this->SetAdditionalEditors($addtarray);
      }
    }

    /**
     * A function to get the internally generated URL for this content type.
     * This method may be overridden by content types.
     *
     * @param boolean if true, and mod_rewrite is enabled, build a URL suitable for mod_rewrite.
     * @return string
     */
    public function GetURL($rewrite = true)
    {
      $gCms = cmsms();
      $config = $gCms->GetConfig();
      $url = "";
      $alias = ($this->mAlias != ''?$this->mAlias:$this->mId);

      $base_url = $config['root_url'];
      if( $this->Secure() ) {
	$base_url = $config['ssl_url'];
      }

      /* use root_url for default content */
      if($this->mDefaultContent) {
	$url =  $base_url . '/';
	return $url;
      }

      if ($config["url_rewriting"] == 'mod_rewrite' && $rewrite == true) {
	$str = $this->HierarchyPath();
	if( $this->mURL != '') {
	  // we have a url path
	  $str = $this->mURL;
	}
	$url = $base_url. '/' . $str . (isset($config['page_extension'])?$config['page_extension']:'.html');
      }
      else if (isset($_SERVER['PHP_SELF']) && $config['url_rewriting'] == 'internal' && $rewrite == true) {
	$str = $this->HierarchyPath();
	if( $this->mURL != '') {
	  // we have a url path
	  $str = $this->mURL;
	}
	$url = $base_url . '/index.php/' . $str . (isset($config['page_extension'])?$config['page_extension']:'.html');
      }
      else {
	$url = $base_url . '/index.php?' . $config['query_var'] . '=' . $alias;
      }
      return $url;
    }

    /**
     * Move this content up, or down with respect to its peers.
     *
     * Note: This method modifies two content objects.
     *
     * @since 2.0
     * @param integer direction. negative value indicates up, positive value indicates down.
     * @return void
     */
    public function ChangeItemOrder($direction)
    {
      $db = cmsms()->GetDb();
      $time = $db->DBTimeStamp(time());
      $parentid = $this->ParentId();
      $order = $this->ItemOrder();
      if( $direction < 0 && $this->ItemOrder() > 1 ) {
	// up
	$query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order + 1), modified_date = '.$time.' 
                  WHERE item_order = ? AND parent_id = ?';
	$db->Execute($query,array($order-1,$parentid));
	$query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order - 1), modified_date = '.$time.'
                  WHERE content_id = ?';
	$db->Execute($query,array($this->Id()));
      }
      else if( $direction > 0 ) {
	// down.
	$query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order - 1), modified_date = '.$time.'
                  WHERE item_order = ? AND parent_id = ?';
	$db->Execute($query,array($order+1,$parentid));
	$query = 'UPDATE '.cms_db_prefix().'content SET item_order = (item_order + 1), modified_date = '.$time.'
                  WHERE content_id = ?';
	$db->Execute($query,array($this->Id()));
      }
    }

    /**
     * Show the content
     *
     * @abstract
     * @param string An optional property name to display.
     */
    public function Show($param = '')
    {
    }

    /**
     * Used to get the list of all editable properties for this content page.
     * Other content types may override this method, but should call the base method at the start.
     *
     * @abstract
     * @return an array of stdclass objects containing name (string), tab (string), priority (integer), required (boolean) members
     */
    public function GetEditableProperties()
    {
      return $this->_attributes;

      // filter out the elements that this user isn't allowed to see.
    }

    private function _GetEditableProperties()
    {
      if( isset($this->_editable_properties) ) {
	return $this->_editable_properties;
      }

      $props = $this->GetEditableProperties();

      // remove properties based on permissions

      // sort the properties.
      // sort the attributes by tab, priority, name...
      usort($props,function($a,$b) {
	      if( !isset($a->tab) || $a->tab == '' ) $a->tab = ContentBase::TAB_MAIN;
	      if( !isset($b->tab) || $b->tab == '' ) $b->tab = ContentBase::TAB_MAIN;

	      // sort elements by tabname, and then priority
	      $atab = $a->tab;
	      $btab = $b->tab;

	      $res = null;
	      if( ($r = strcmp($atab,$btab)) != 0 ) $res = $r;
	      else if( $a->priority < $b->priority ) $res = -1;
	      else if( $a->priority > $b->priority ) $res = 1;
	      else $res = strcmp($a->name,$b->name);
	      return $res;
      });

      $this->_editable_properties = $props;
      return $props;
    }

    /**
     * Used from a page that allows content editing. This method the list of distinct sections
     * that devides up the various logical sections that this content type supports for editing.
     *
     * @abstract
     * @return array List of tab name strings.
     */
    public function GetTabNames($keys = FALSE)
    {
      $props = $this->_GetEditableProperties();
      $arr = array();
      foreach( $props as $one ) {
	if( !isset($one->tab) || $one->tab == '' ) $one->tab = self::TAB_MAIN;
	if( !isset($arr[$one->tab]) ) $arr[$one->tab] = 0;
	$arr[$one->tab]++;
      }
      
      $arr = array_keys($arr);
      if( !$keys ) {
	for( $i = 0; $i < count($arr); $i++ ) {
	  $one =& $arr[$i];
	  if( endswith($one,'_tab__') ) {
	    $one = lang($one);
	  }
	}
      }
      return $arr;
    }

    /**
     * Get an optional message for each tab.
     *
     * @abstract
     * @since 2.0
     * @param mixed either a tab name (untranslated) or an integer index
     * @return string html text to display at the top of the tab.
     */
    public function GetTabMessage($key)
    {
      if( (int)$key >= 0 ) {
	$tabs = $this->GetTabNames(TRUE);
	if( $key >= count($tabs) ) $key = 0;
	$key = $tabs[$key];
      }

      switch( $key ) {
      case self::TAB_PERMS:
	return '<div class="information">'.lang('msg_permstab').'</div>';
	break;
      }
    }

    /**
     * Get the attributes for a specific tab
     *
     * @param mixed either a tab name (untranslated) or an integer index
     * @return An array of arrays.  Index 0 of each element should be a prompt field, and index 1 should be the input field for the prompt.
     */
    public function GetTabElements($key,$adding = FALSE)
    {
      if( (int)$key >= 0 ) {
	$tabs = $this->GetTabNames(TRUE);
	if( $key >= count($tabs) ) $key = 0;
	$key = $tabs[$key];
      }

      $props = $this->_GetEditableProperties();

      $out = array();
      foreach( $props as $one ) {
	if( !isset($one->tab) || $one->tab == '' ) $one->tab = self::TAB_MAIN;
	if( $key != $one->tab ) continue;

	$out[] = $this->display_single_element($one->name,$adding);
      }
      return $out;
    }

    /**
     * Method to indicate wether the current page has children.
     *
     * @param boolean should we test only for active children.
     * @return booelan
     */
    public function HasChildren($activeonly = false)
    {
      $hm = cmsms()->GetHierarchyManager();
      $node = $hm->getNodeById($this->mId);
      if( !$node->has_children() ) return false;
      if( $activeonly == false) return true;

      $children = $node->get_children();
      if( $children ) {
	for( $i = 0; $i < count($children); $i++ ) {
	  $content = $children[$i]->getContent();
	  if( $content->Active() ) return true;
	}
      }

      return false;
    }

    /**
     * Return a list of additional editors
     * Note: in the returned array, group id's are specified as negative integers.
     *
     * @return mixed  Array of uids and group ids, or null
     */
    public function GetAdditionalEditors()
    {
      if (!isset($this->mAdditionalEditors)) {
	$gCms = cmsms();
	$db = $gCms->GetDb();

	$this->mAdditionalEditors = array();

	$query = "SELECT user_id FROM ".cms_db_prefix()."additional_users WHERE content_id = ?";
	$dbresult = $db->Execute($query,array($this->mId));

	while ($dbresult && !$dbresult->EOF) {
	  $this->mAdditionalEditors[] = $dbresult->fields['user_id'];
	  $dbresult->MoveNext();
	}

	if ($dbresult) $dbresult->Close();
      }
      return $this->mAdditionalEditors;
    }

    /**
     * Set the list of additional editors
     * Note: in the provided array, group id's are specified as negative integers.
     *
     * @param mixed  Array of uids and group ids, or null
     */
    public function SetAdditionalEditors($editorarray)
    {
      $this->mAdditionalEditors = $editorarray;
    }

    static public function GetAdditionalEditorOptions()
    {
      $opts = array();
      $gCms = cmsms();
      $userops = $gCms->GetUserOperations();
      $groupops = $gCms->GetGroupOperations();
      $allusers = $userops->LoadUsers();
      $allgroups = $groupops->LoadGroups();
      foreach ($allusers as $oneuser) {
	$opt[$oneuser->id] = $oneuser->username;
      }
      foreach ($allgroups as $onegroup) {
	if( $onegroup->id == 1 ) continue; // exclude admin group (they have all privileges anyways)
	$val = $onegroup->id*-1;
	$opts[$val] = lang('group').': '.$onegroup->name;
      }
      return $opts;
    }

    static public function GetAdditionalEditorInput($addteditors,$owner_id = -1)
    {
      $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_addteditor');
      $ret[] = '<label for="addteditors">'.lang('additionaleditors').':</label>'.$help;
      $text = '<input name="additional_editors" type="hidden" value=""/>';
      $text .= '<select id="addteditors" name="additional_editors[]" multiple="multiple" size="5">';

      $topts = self::GetAdditionalEditorOptions();
      foreach( $topts as $k => $v ) {
	if( $k == $owner_id ) continue;
	$text .= CmsFormUtils::create_option($k,$v,$addteditors);
      }
      
      
      $text .= '</select>';
      $ret[] = $text;
      return $ret;
    }


    /**
     * Provides an input element to display the list of additional editors.
     * This method is usually called from within this object.
     *
     * @param mixed An optional array of additional editor id's (group ids specified with negative values)
     * @return string The input element.
     */
    public function ShowAdditionalEditors($addteditors = '')
    {
      $ret = array();
      if( $addteditors == '' ) {
	$addteditors = $this->GetAdditionalEditors();
      }
      return self::GetAdditionalEditorInput($addteditors,$this->Owner());
    }

    /* private */
    private function _handleRemovedBaseProperty($name,$member)
    {
      if( !is_array($this->_attributes) ) return FALSE;
      if( !in_array($name,$this->_attributes) ) {
	if( isset($this->_prop_defaults[$name]) ) {
	  $this->$member = $this->_prop_defaults[$name];
	  return TRUE;
	}
      }
      return FALSE;
    }

    /**
     * Remove a property from the known property list.
     * Specify a default value to use if the property is called.
     *
     * @param string The property name
     * @param string The default value.
     */
    protected function RemoveProperty($name,$dflt)
    {
      if( !is_array($this->_attributes) ) return;
      $tmp = array();
      for( $i = 0; $i < count($this->_attributes); $i++ ) {
	if( is_object($this->_attributes[$i]) && $this->_attributes[$i]->name == $name ) {
	  continue;
	}
	$tmp[] = $this->_attributes[$i];
      }
      $this->_attributes = $tmp;
      $this->_prop_defaults[$name] = $dflt;
    }

    /**
     * Add a property
     *
     * @since 1.11
     */
    protected function AddProperty($name,$priority,$tab,$required = FALSE)
    {
      $ob = new StdClass;
      $ob->name = $name;
      $ob->priority = $priority;
      $ob->tab = $tab;
      $ob->required = $required;

      if( !is_array($this->_attributes) ) {
	$this->_attributes = array();
      }

      $this->_attributes[] = $ob;
    }

    /*
     * Add a property that is directly associtated with a field in the content table.
     *
     * @param string The property name
     * @param integer The priority
     * @param boolean Whether this field is required for this content type
     * @param string  (optional) unused.
     * @deprecated
     */
    protected function AddBaseProperty($name,$priority,$is_required = 0)
    {
      $this->AddProperty($name,$priority,self::TAB_OPTIONS,$is_required);
    }

    /*
     * Alias for AddBaseProperty
     *
     * @deprecated
     */
    protected function AddContentProperty($name,$priority,$is_required = 0)
    {
      return $this->AddProperty($name,$priority,self::TAB_OPTIONS,$is_required);
    }

    /**
     * A method to display a single input element for an object basic, or extended property.
     *
     * @abstract
     * @param string The property name
     * @param boolean Wether or not we are in add or edit mode.
     * @return array consisting of two elements.  A label, and the input element.
     */
    protected function display_single_element($one,$adding)
    {
      $gCms = cmsms();
      $config = $gCms->GetConfig();

      switch( $one ) {
      case 'cachable':
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_cachable');
	return array('<label for="in_cachable">'.lang('cachable').':</label>'.$help,
		     '<input type="hidden" name="cachable" value="0"/><input id="in_cachable" class="pagecheckbox" type="checkbox" value="1" name="cachable"'.($this->mCachable?' checked="checked"':'').' />');

      case 'title':
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_title');
	return array('<label for="in_title">*'.lang('title').':</label>'.$help,
		     '<input type="text" id="in_title" name="title" value="'.cms_htmlentities($this->mName).'" />');
	      
      case 'menutext':
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_menutext');
	return array('<label for="in_menutext">*'.lang('menutext').':</label>'.$help,
		     '<input type="text" name="menutext" id="in_menutext" value="'.cms_htmlentities($this->mMenuText).'" />');

      case 'parent':
	$contentops = $gCms->GetContentOperations();
	$tmp = $contentops->CreateHierarchyDropdown($this->mId, $this->mParentId, 'parent_id', 0, 1, 0, 1,get_site_preference('listcontent_showtitle',true) );
	if( empty($tmp) && !check_permission(get_userid(),'Manage All Content') )
	  return array('','<input type="hidden" name="parent_id" value="'.$this->mParentId.'" />');
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_parent');
	if( !empty($tmp) ) return array('<label for="parent_id">*'.lang('parent').':</label>'.$help,$tmp);
	break;

      case 'active':
	if( !$this->DefaultContent() ) {
	  $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_active');
	  return array('<label for="id_active">'.lang('active').':</label>'.$help,'<input type="hidden" name="active" value="0"/><input class="pagecheckbox" type="checkbox" name="active" id="id_active" value="1"'.($this->mActive?' checked="checked"':'').' />');
	}
	break;

      case 'showinmenu':
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_active');
	return array('<label for="showinmenu">'.lang('showinmenu').':</label>'.$help,
		     '<input type="hidden" name="showinmenu" value="0"/><input class="pagecheckbox" type="checkbox" value="1" name="showinmenu" id="showinmenu"'.($this->mShowInMenu?' checked="checked"':'').' />');

      case 'target':
	$text = '<option value="---">'.lang('none').'</option>';
	$text .= '<option value="_blank"'.($this->GetPropertyValue('target')=='_blank'?' selected="selected"':'').'>_blank</option>';
	$text .= '<option value="_parent"'.($this->GetPropertyValue('target')=='_parent'?' selected="selected"':'').'>_parent</option>';
	$text .= '<option value="_self"'.($this->GetPropertyValue('target')=='_self'?' selected="selected"':'').'>_self</option>';
	$text .= '<option value="_top"'.($this->GetPropertyValue('target')=='_top'?' selected="selected"':'').'>_top</option>';
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_target');
	return array('<label for="target">'.lang('target').':</label>'.$help,
		     '<select name="target" id="target">'.$text.'</select>');

      case 'alias':
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_page_alias');
	return array('<label for="alias">'.lang('pagealias').':</label>'.$help,
		     '<input type="text" name="alias" id="alias" value="'.$this->mAlias.'" />');

      case 'secure':
	$opt = '';
	if( $this->mSecure ) {
	  $opt = ' checked="checked"';
	}
	$str  = '<input type="hidden" name="secure" value="0"/>';
	$str .= '<input type="checkbox" name="secure" id="secure" value="1"'.$opt.'/>';
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_secure');
	return array('<label for="secure">'.lang('secure_page').':</label>'.$help,$str);

      case 'page_url':
	if( !$this->DefaultContent() ) {
	  $str = '<input type="text" name="page_url" id="page_url" value="'.$this->mURL.'" size="50" maxlength="255"/>';
	  $prompt = '<label for="page_url">'.lang('page_url').':</label>';
	  if( get_site_preference('content_mandatory_urls',0) ) {
	    $prompt = '*'.$prompt;
	  }
	  $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_page_url');
	  return array($prompt.$help,$str);
	}
	break;

      case 'image':
	$dir = cms_join_path($config['image_uploads_path'],get_site_preference('content_imagefield_path'));
 	$data = $this->GetPropertyValue('image');
	$dropdown = create_file_dropdown('image',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_',1,1);
	if( !$dropdown ) return;
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_image');
	return array('<label for="image">'.lang('image').':</label>'.$help,$dropdown);

      case 'thumbnail':
	$dir = cms_join_path($config['image_uploads_path'],get_site_preference('content_thumbnailfield_path'));
	$data = $this->GetPropertyValue('thumbnail');
	$dropdown = create_file_dropdown('thumbnail',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_',0,1);
	if( !$dropdown ) return FALSE;
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_thumbnail');
	return array('<label for="thumbnail">'.lang('thumbnail').':</label>'.$help,$dropdown);

      case 'titleattribute':
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_titleattribute');
	return array('<label for="titleattribute">'.lang('titleattribute').':</label>'.$help,
		     '<input type="text" name="titleattribute" id="titleattribute" maxlength="255" size="80" value="'.cms_htmlentities($this->mTitleAttribute).'" />');

      case 'accesskey':
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_accesskey');
	return array('<label for="accesskey">'.lang('accesskey').':</label>'.$help,
		     '<input type="text" name="accesskey" id="accesskey" maxlength="5" value="'.cms_htmlentities($this->mAccessKey).'" />');

      case 'tabindex':
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_tabindex');
	return array('<label for="tabindex">'.lang('tabindex').':</label>'.$help,
		     '<input type="text" name="tabindex" id="tabindex" maxlength="5" value="'.cms_htmlentities($this->mTabIndex).'" />');

      case 'extra1':
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_extra1');
	return array('<label for="extra1">'.lang('extra1').':</label>'.$help,
		     '<input type="text" name="extra1" id="extra1" maxlength="255" size="80" value="'.cms_htmlentities($this->GetPropertyValue('extra1')).'" />');

      case 'extra2':
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_extra2');
	return array('<label for="extra2">'.lang('extra2').':</label>'.$help,
		     '<input type="text" name="extra2" id="extra2" maxlength="255" size="80" value="'.cms_htmlentities($this->GetPropertyValue('extra2')).'" />');
	      
      case 'extra3':
	$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_extra3');
	return array('<label for="extra3">'.lang('extra3').':</label>'.$help,
		     '<input type="text" name="extra3" id="extra3" maxlength="255" size="80" value="'.cms_htmlentities($this->GetPropertyValue('extra3')).'" />');

      case 'owner':
	$showadmin = check_ownership(get_userid(), $this->Id());
	$userops = $gCms->GetUserOperations();
	if (!$adding && ($showadmin || check_permission(get_userid(),'Manage All Content')) ) {
	  $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_owner');
	  return array('<label for="owner">'.lang('owner').':</label>'.$help, $userops->GenerateDropdown($this->Owner()));
	}
	break;

      case 'additionaleditors':
	// do owner/additional-editor stuff
	if( $adding || check_ownership(get_userid(),$this->Id()) || 
	    check_permission(get_userid(),'Manage All Content')) {
	  return $this->ShowAdditionalEditors();
	}
	break;

      default:
	throw new CmsInvalidDataException('Attempt to display invalid property '.$one);
      }
    }
} // end of class

# vim:ts=4 sw=4 noet
?>