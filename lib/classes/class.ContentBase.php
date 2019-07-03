<?php
# CMS - CMS Made Simple
# (c)2004 by Ted Kulp (tedkulp@users.sf.net)
# Visit our homepage at: http://www.cmsmadesimple.org
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
 * This file provides the basic abstract content class
 * @package CMS
 */
use CMSMS\internal\content_assistant;

/**
 * @ignore
 */
define('CMS_CONTENT_HIDDEN_NAME','--------');

/**
 * @ignore
 */
define('__CMS_PREVIEW_PAGE__',-100);

/**
 * Base level content object.
 *
 * This is the base level content class.  It is an abstract object and cannot be instantiated directly.
 * All content pages in the database are required to be drived from this class.
 *
 * @since 0.8
 * @package CMS
 */
abstract class ContentBase
{
    /**
     * @ignore
     */
    const TAB_MAIN = 'aa_main_tab__';

    /**
     * @ignore
     */
    const TAB_NAV = 'zz_1nav_tab__';

    /**
     * @ignore
     */
    const TAB_LOGIC = 'zz_2logic_tab__';

    /**
     * @ignore
     */
    const TAB_OPTIONS = 'zz_3options_tab__';

    /**
     * @ignore
     */
    const TAB_PERMS = 'zz_4perms_tab__';

    /**
     * The unique ID identifier of the element
     * Integer
     *
     * @ignore
     */
    protected $mId = -1;

    /**
     * The name of the element (like a filename)
     * String
     *
     * @internal
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
     *
     * @internal
     */
    protected $mHierarchy = '';

    /**
     * The full hierarchy of the content ids
     * String of the form : 1.4.3
     *
     * @internal
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

    /**
     * state or meta information
     *
     * @internal
     */
    private $_attributes;

    /**
     * @internal
     */
    private $_prop_defaults;

    /**
     * @internal
     */
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

    /**
     * @ignore
     */
    public function __clone()
    {
        $this->mId = -1;
        $this->mItemOrder = -1;
        $this->mURL = '';
        $this->mAlias = '';
    }

    /**
     * Returns the ID
     */
    public function Id()
    {
        $t = (int) $this->mId;
        if( $t > 0 ) return $t;
        return $this->mId;
    }

    /**
     * Set the numeric id of the content item
     *
     * @param int Integer id
     * @access private
     * @internal
     */
    public function SetId(int $id)
    {
        $this->mId = $id;
    }

    /**
     * Returns a friendly name for this content type
     *
     * Normally the content type returns a string representing the name of the content type translated into the users current language
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
     * @param string $name The name.
     */
    public function SetName(string $name)
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
     * Returns the Type
     *
     * @return string
     */
    public function Type()
    {
        // todo: fix this.... the type name and class name cruft.
        $classname = get_class($this);
        $contentops = cmsms()->GetContentOperations();
        $ph = $contentops->GetContentTypePlaceholderByClassname( $classname );
        return $ph->type;
    }

    /**
     * Returns the Owners user id
     *
     * @return int
     */
    public function Owner()
    {
        return $this->mOwner;
    }

    /**
     * Set the page owner.
     * No validation is performed.
     *
     * @param int $owner Owner's user id
     */
    public function SetOwner(int $owner)
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
     * @return bool default is false.
     */
    public function HandlesAlias()
    {
        return false;
    }

    /**
     * Set the page metadata
     *
     * @param string $metadata The metadata
     */
    public function SetMetadata(string $metadata)
    {
        $this->mMetadata = $metadata;
    }

    /**
     * Return the page tabindex value
     *
     * @return int
     */
    public function TabIndex()
    {
        return $this->mTabIndex;
    }

    /**
     * Set the page tabindex value
     *
     * @param int $tabindex tabindex
     */
    public function SetTabIndex(int $tabindex)
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
     * Return the creation date (as a string)
     *
     * @since 2.3
     * @return string
     */
    public function CreationDate()
    {
        return $this->mCreationDate;
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
     * Return the modification date (as a string)
     *
     * @since 2.3
     * @return string
     */
    public function ModifiedDate()
    {
        return $this->mModifiedDate;
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
     * The title attribue can be used in navigations to set the "title=" attribute of a link
     * some menu templates may ignore this.
     *
     * @param string $titleattribute The title attribute
     */
    public function SetTitleAttribute(string $titleattribute)
    {
        $this->mTitleAttribute = $titleattribute;
    }

    /**
     * Get the access key (for accessibility) for this page.
     *
     * @see http://www.w3schools.com/tags/att_global_accesskey.asp
     * @return string
     */
    public function AccessKey()
    {
        return $this->mAccessKey;
    }

    /**
     * Set the access key (for accessibility) for this page
     *
     * @see http://www.w3schools.com/tags/att_global_accesskey.asp
     * @param string $accesskey
     */
    public function SetAccessKey(string $accesskey)
    {
        $this->mAccessKey = $accesskey;
    }

    /**
     * Returns the id of this pages parent.
     * The parent id may be -2 to indicate a new page.
     * A parent id value of -1 indicates that the page has no parent.
     * oterwise a positive integer is returned.
     *
     * @return int
     */
    public function ParentId()
    {
        return $this->mParentId;
    }

    /**
     * Sets the parent of this page.
     *
     * @param int $parentid The numeric page parent id.  Use -1 for no parent.
     */
    public function SetParentId(int $parentid)
    {
        $parentid = (int) $parentid;
        if( $parentid < 1 ) $parentid = -1;
        $this->mParentId = $parentid;
    }

    /**
     * Return the id of the template associated with this content page.
     *
        * @deprecated
     * @return int.
     */
    public function TemplateId()
    {
        return $this->mTemplateId;
    }

    /**
     * Return a smarty resource string for the template assigned to this page.
     *
     * @since 2.3
     * @abstract
     * @return string
     */
    public function TemplateResource()
    {
        // nothing here
    }

    /**
     * Set the id of the template associated with this content page.
     *
     * @since 2.3
     * @abstract
     * @param string $rsrc;
     */
    public function SetTemplateResource(string $rsrc)
    {
        // nothing here.
    }

    /**
     * Set the id of the template associated with this content page.
     *
     * @deprecated
     * @see SetTemplateResource
     * @param int $templateid
     */
    public function SetTemplateId(int $templateid)
    {
        $templateid = (int)$templateid;
        if( $templateid > 0 ) $this->mTemplateId = $templateid;
    }

    /**
     * Returns the ItemOrder
     * The ItemOrder is used to specify the order of this page amongst its peers
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
     * A value of -1 indicates that a new item order will be calculated on save.
     * Otherwise a positive integer is expected.
     *
     * @internal
     * @param int $itemorder
     */
    public function SetItemOrder(int $itemorder)
    {
        $itemorder = (int)$itemorder;
        if( $itemorder > 0 || $itemorder == -1 ) $this->mItemOrder = $itemorder;
    }

    /**
     * Returns the Hierarchy of the current page.
     * A string like #.##.## indicating the path to this page and it's order
     * this value uses the item order when calculating the output i.e:  3.3.3
     * to indicate the third grandghild of the third child of the third root page.
     *
     * @return string
     */
    public function Hierarchy()
    {
        // todo: fix me (no contentops dependency)
        $contentops = cmsms()->GetContentOperations();
        return $contentops->CreateFriendlyHierarchyPosition($this->mHierarchy);
    }

    /**
     * Sets the Hierarchy
     *
     * @internal
     * @param string $hierarchy
     */
    public function SetHierarchy(string $hierarchy)
    {
        $this->mHierarchy = $hierarchy;
    }

    /**
     * Returns the Id Hierarchy.
     * A string like #.##.## indicating the path to the page and it's order
     * this property uses the id's of pages when calculating the output i.e: 21.5.17
     * to indicate that page id 17 is the child of page with id 5 which is inturn the
        * child of the page with id 21
     *
     * @return string
     */
    final public function IdHierarchy()
    {
        return $this->mIdHierarchy;
    }


    /**
     * Returns the Hierarchy Path.
     * Similar to the Hierarchy and IdHierarchy this string uses page aliases
     * and outputs a string like root_alias/parent_alias/page_alias
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
     * @return bool
     */
    public function Active()
    {
        return $this->mActive;
    }

    /**
     * Sets this page as active
     *
     * @param bool $active
     */
    public function SetActive($active)
    {
        $this->mActive = (bool)$active;
    }

    /**
     * Returns whether preview should be available for this content type
     *
     * @abstract
     * @return bool
     */
    public function HasPreview()
    {
        return FALSE;
    }

    /**
     * Returns whether this content item should (by default) be shown in navigation menus.
     *
     * @abstract
     * @return bool
     */
    public function ShowInMenu()
    {
        return $this->mShowInMenu;
    }

    /**
     * Sets whether this page should be (by default) shown in menus
     *
     * @param bool $showinmenu
     */
    public function SetShowInMenu($showinmenu)
    {
        $this->mShowInMenu = (bool) $showinmenu;
    }

    /**
     * Returns if the page is the default.
     * The default page is the one that is displayed when no alias or pageid is specified in the route
     * Only one content page can be the default.
     *
     * @return bool
     */
    final public function DefaultContent()
    {
        if( !$this->IsDefaultPossible() ) return FALSE;
        return $this->mDefaultContent;
    }

    /**
     * Sets if this page should be considered the default.
     * Note: does not modify the flags for any other content page.
     *
     * @deprecated
     * @param bool $defaultcontent
     */
    public function SetDefaultContent(bool $defaultcontent)
    {
        if( $this->IsDefaultPossible() ) $this->mDefaultContent = (bool) $defaultcontent;
    }

    /**
     * Return whether this page is cachable.
     * Cachable pages (when enabled in global settings) are cached by the browser
     * (also server side caching of HTML output may be enabled)
     *
     * @return bool
     */
    public function Cachable()
    {
        return $this->mCachable;
    }

    /**
     * Set whether this page is cachable
     *
     * @param bool $cachable
     */
    public function SetCachable(bool $cachable)
    {
        $this->mCachable = (bool) $cachable;
    }


    /**
     * Return the page url (if any) associated with this content page.
     * The page url is not the complete URL to the content page, but merely the 'stub' or 'slug' appended after the root url when accessing the site
     * If the page is specified as the default page then the "page url" will be ignored.
     * Some content types do not support page urls.
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
     * @param string $url
     */
    public function SetURL($url)
    {
        $this->mURL = $url;
    }

    /**
     * Return the integer id of the admin user that last modified this content item.
     *
     * @return int
     */
    public function LastModifiedBy()
    {
        return $this->mLastModifiedBy;
    }

    /**
     * Set the last modified date for this item
     *
     * @param int $lastmodifiedby
     */
    public function SetLastModifiedBy(int $lastmodifiedby)
    {
        $lastmodifiedby = (int)$lastmodifiedby;
        if( $lastmodifiedby > 0 ) $this->mLastModifiedBy = $lastmodifiedby;
    }

    /**
     * Indicates whether this content type requires an alias.
     * Some content types that are not directly navigable do not require page aliases.
     *
     * @abstract
     * @return bool
     */
    public function RequiresAlias()
    {
        return TRUE;
    }

    /**
     * Indicates whether this content type is viewable (i.e: can be rendered).
     * some content types (like redirection links) are not viewable.
     *
     * @abstract
     * @return bool Default is True
     */
    public function IsViewable()
    {
        return TRUE;
    }

    /**
     * Indicates wether the current user is permitted to view this content page.
     *
     * @since 1.11.12
     * @abstract
     * @return boolean
     */
    public function IsPermitted()
    {
        return TRUE;
    }

    /**
     * Indicates whether this content type is searchable.
     *
     * Searchable pages can be indexed by the search module.
     *
     * This function by default uses a combination of other abstract methods to determine if the page is searchable
     * but extended content types can override this.
     *
     * @since 2.0
     * @return bool
     */
    public function IsSearchable()
    {
        if( !$this->isPermitted() || !$this->IsViewable() || !$this->HasTemplate() || $this->IsSystemPage() ) return FALSE;
        return $this->HasSearchableContent();
    }

    /**
     * Indicates whether this content type may have content that can be used by a search module.
     *
     * Content types should override this method if they are special purpose content types and they cannot support searchable content
     * in any way.  Content types such as ErrorPage, Section Header, and Separator are examples.
     *
     * @since 2.0
     * @abstract
     * @return bool
     */
    protected function HasSearchableContent()
    {
        return TRUE;
    }

    /**
     * Indicates whether this content type can be the default page for a CMSMS website.
     *
     * The content editor module may adjust it's user interface to not allow setting pages that return false for this method as the default page.
     *
     * @abstract
     * @returns bool Default is false
     */
    public function IsDefaultPossible()
    {
        return FALSE;
    }

    /**
     * Set the page alias for this content page.
     * If an empty alias is supplied a suitable alias may be calculated from other data in the page object
     *
     * @param string $alias The alias
     */
    public function SetAlias($alias = null)
    {
        // todo: this stuff should not be here.  should be in an editor class that is instantiated somewhere.
        $contentops = cmsms()->GetContentOperations();
        $config = cmsms()->GetConfig();
        if( !$alias && $config['auto_alias_content'] == true ) {
            $alias = trim($this->MenuText());
            $alias = trim($this->Name());
        }

        if( $alias ) $this->mAlias = $contentops->calculate_unused_content_alias($this, $alias);
    }

    /**
     * Returns the menu text for this content page.
     * The MenuText is by default used as the text portion of a navigation link.
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
     * @param string $menutext
     */
    public function SetMenuText(string $menutext)
    {
        $this->mMenuText = $menutext;
    }

    /**
     * Returns number of immediate child content items of this content item.
     *
     * @return int
     */
    public function ChildCount()
    {
        $hm = cmsms()->GetHierarchyManager();
        $node = $hm->getNodeById($this->mId);
        if( $node ) return $node->count_children();
    }

    /**
     * Content page objects only directly store enough information to build a basic navigation from content objects.
     * This method will return all of the other parts of the content object.
     *
     * Note: this method does not directly load properties.
     *
     * @return array
     */
    public function Properties()
    {
        return $this->_props;
    }

    /**
     * Test whether this content page has the named property.
     * Properties will be loaded from the database if necessary.
     *
     * @param string $name
     * @return bool
     */
    public function HasProperty(string $name)
    {
        if( !$name ) return FALSE;
        if( !is_array($this->_props) ) return FALSE;
        return in_array($name,array_keys($this->_props));
    }

    /**
     * Get the value for the named property.
     * Properties will be loaded from the database if necessary.
     *
     * @param string $name
     * @return mixed String value, or null if the property does not exist.
     */
    public function GetPropertyValue(string $name)
    {
        if( $this->HasProperty($name) ) return $this->_props[$name];
    }

    /**
     * Set the value of a the named property.
     * This method will load properties for this content page if necessary.
     *
     * @param string $name The property name
     * @param string $value The property value.
     */
    public function SetPropertyValue(string $name, string $value = null)
    {
        $this->_props[$name] = $value;
    }

    /**
     * An abstract method that extended content types can use to indicate whether or not they want children.
     * Some content types, such as a separator do not want to have any children.
     *
     * @since 0.11
     * @abstract
     * @return bool Default TRUE
     */
    public function WantsChildren()
    {
        return true;
    }

    /**
     * An abstract method that indicates that this content type is navigable and generates a useful URL.
     *
     * @abstract
     * @return bool Default TRUE
     */
    public function HasUsableLink()
    {
        return true;
    }

    /**
     * An abstract method indicating whether the content type is copyable.
     *
     * @abstract
     * @return bool default FALSE
     */
    public function IsCopyable()
    {
        return FALSE;
    }

    /**
     * An abstract method to indicate whether this content type generates a system page.
     * System pages are used to handle things like 404 errors etc.
     *
     * @abstract
     * @return bool default FALSE
     */
    public function IsSystemPage()
    {
        return FALSE;
    }

    /**
     * Indicates whether ths page type uses a template.
     * i.e: some content types like sectionheader and separator do not.
     *
     * @since 2.0
     * @abstract
     * @return bool default FALSE
     */
    public function HasTemplate()
    {
        return FALSE;
    }

    /**
     * Get general info that is displayable about this particular content item
     * at various times in the admin.
     *
     * @since 2.3
     * @abstract
     * @return string default null
     */
    public function GetAdminInfo()
    {
        // nothing here
    }

    /************************************************************************/
    /* The rest																*/
    /************************************************************************/

    /**
     * Load the content of the object from an array.
     * This method modifies the current object.
     *
     * There is no check on the data provided, because this is the job of
     * ValidateData
     *
     * Upon failure the object comes back to initial values and returns FALSE
     *
     * @internal
     * @param array $data Data as loaded from the database
     */
    public function LoadFromData(array $data)
    {
        $this->mId                         = $data["content_id"];
        $this->mName                       = $data["content_name"];
        $this->mAlias                      = $data["content_alias"];
        $this->mOldAlias                   = $data["content_alias"];
        $this->mOwner                      = $data["owner_id"];
        $this->mParentId                   = $data["parent_id"];
        $this->mTemplateId                 = $data["template_id"];
        $this->mItemOrder                  = $data["item_order"];
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
        if( isset($data['page_url']) ) $this->mURL  = $data["page_url"];
        $this->mLastModifiedBy             = $data["last_modified_by"];
        $this->mCreationDate               = $data["create_date"];
        $this->mModifiedDate               = $data["modified_date"];

        $this->SetInitialValues();
        if( isset($data['_props']) && is_array($data['_props']) && count($data['_props']) ) {
            foreach( $data['_props'] as $one ) {
                $this->_props[$one['prop_name']] = $one['content'];
            }
        }
        if( isset($data['_editors']) && is_array($data['_editors']) && count($data['_editors']) ) {
            $this->mAdditionalEditors = [];
            foreach( $data['_editors'] as $one ) {
                $this->mAdditionalEditors[] = $one['user_id'];
            }
        }

        $this->Load();
    }

    /**
     * Convert the current object to an array.
        *
        * This can be considered a simple DTO (Data Transfer Object)
     *
     * @since 2.0
     * @author Robert Campbell
     * @return array
     */
    public function ToData()
    {
        $out = array();
        $out['content_id'] = $this->mId;
        $out['content_name'] = $this->mName;
        $out['content_alias'] = $this->mAlias;
        $out['owner_id'] = $this->mOwner;
        $out['parent_id'] = $this->mParentId;
        $out['template_id'] = $this->mTemplateId;
        $out['item_order'] = $this->mItemOrder;
        $out['metadata'] = $this->mMetadata;
        $out['hierarchy'] = $this->mHierarchy;
        $out['id_hierarchy'] = $this->mIdHierarchy;
        $out['hierarchy_path'] = $this->mHierarchyPath;
        $out['menu_text'] = $this->mMenuText;
        $out['titleattribute'] = $this->mTitleAttribute;
        $out['accesskey'] = $this->mAccessKey;
        $out['tabindex'] = $this->mTabIndex;
        $out['default_content'] = ($this->mActive)?1:0;
        $out['active'] = ($this->mActive)?1:0;
        $out['show_in_menu'] = ($this->mShowInMenu)?1:0;
        $out['cachable'] = ($this->mCachable)?1:0;
        $out['page_url'] = ($this->mURL)?1:0;
        $out['last_modified_by'] = $this->mLastModifiedBy;
        $out['create_date'] = $this->mCreationDate;
        $out['modified_date'] = $this->mModifiedDate;
        $out['wants_children'] = $this->WantsChildren();
        $out['has_usable_link'] = $this->HasUsableLink();
        return $out;
    }

    /**
     * Callback function for content types to use to preload content or other things if necessary.
     * This is called right after the content is loaded from the database.
     *
     * @abstract
     */
    protected function Load()
    {
    }

    /**
     * Test if the content object is valid.
     * This function is used to check that no compulsory argument
     * has been forgotten by the user
     *
     * We do not check the Id because there can be no Id (new content)
     * That's up to Save to check this.
     *
     * @abstract
     * @returns	mixed On error returns an array of strings, otherwise FALSE
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
                $contentops = cmsms()->GetContentOperations();
                $error = $contentops->CheckAliasError($this->mAlias, $this->mId);
                if ($error !== FALSE) {
                    $errors[]= $error;
                    $result = false;
                }
            }
        }

        $auto_type = content_assistant::auto_create_url();
        if( $this->mURL == '' && cms_siteprefs::get('content_autocreate_urls') ) {
            // create a valid url.
            if( !$this->DefaultContent() ) {
                if( cms_siteprefs::get('content_autocreate_flaturls',0) ) {
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

                    $this->mURL = implode('/',$stack);
                    if( $parent_url != '' ) {
                        // woot, we got a prent url.
                        $this->mURL = $parent_url.'/'.$this->mAlias;
                    }
                }
            }
        }
        if( $this->mURL == '' && cms_siteprefs::get('content_mandatory_urls') && !$this->mDefaultContent &&
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
     * Function for the subclass to parse out data for it's parameters.
     * This method is typically called from an editor form to allow modifying the content object from
     * form input fields (usually $_POST)
     *
     * @param array $params The input array (usually from $_POST)
     * @param bool  $editing Indicates wether this is an edit or add operation.
     * @abstract
     */
    public function FillParams(array $params, bool $editing = false)
    {
        // content property parameters
        $parameters = array('extra1','extra2','extra3','image','thumbnail');
        foreach ($parameters as $oneparam) {
            if (isset($params[$oneparam])) $this->SetPropertyValue($oneparam, $params[$oneparam]);
        }

        // go through the list of base parameters
        // setting them from params

        // title
        if (isset($params['title'])) $this->mName = strip_tags($params['title']);

        // menu text
        if (isset($params['menutext'])) $this->mMenuText = strip_tags(trim($params['menutext']));

        // parent id
        if( isset($params['parent_id']) ) {
            if( $params['parent_id'] == -2 && !$editing ) $params['parent_id'] = -1;
            if ($this->mParentId != $params['parent_id']) {
                $this->mHierarchy = '';
                $this->mItemOrder = -1;
            }
            $this->mParentId = (int) $params['parent_id'];
        }

        // active
        if (isset($params['active'])) {
            $this->mActive = (int) $params['active'];
            if( $this->DefaultContent() ) $this->mActive = 1;
        }

        // show in menu
        if (isset($params['showinmenu'])) $this->mShowInMenu = (int) $params['showinmenu'];

        // alias
        // alias field can exist if the user has manage all content... OR alias is a basic property
        // and this user has other edit rights to the content page.
        // empty value on the alias field means we need to generate a new alias
        $new_alias = null;
        $alias_field_exists = isset( $params['alias'] );
        if( $alias_field_exists ) $new_alias = trim(strip_tags($params['alias']));
        // if we are adding or we have a new alias, set alias to the field value, or calculate one, adjust as needed
        if( $new_alias || $alias_field_exists ) {
            $this->SetAlias($new_alias);
        }

        // target
        if (isset($params['target'])) {
            $val = strip_tags($params['target']);
            if( $val == '---' ) $val = '';
            $this->SetPropertyValue('target', $val);
        }

        // title attribute
        if (isset($params['titleattribute'])) $this->mTitleAttribute = trim(strip_tags($params['titleattribute']));

        // accesskey
        if (isset($params['accesskey'])) $this->mAccessKey = strip_tags($params['accesskey']);

        // tab index
        if (isset($params['tabindex'])) $this->mTabIndex = (empty($params['tabindex'])) ? '' : (int) $params['tabindex'];

        // cachable
        if (isset($params['cachable'])) {
            $this->mCachable = (int) $params['cachable'];
        }
        else {
            $this->_handleRemovedBaseProperty('cachable','mCachable');
        }

        // url
        if (isset($params['page_url'])) {
            $this->mURL = trim(strip_tags($params['page_url']));
        }
        else {
            $this->_handleRemovedBaseProperty('page_url','mURL');
        }

        // owner
        if (isset($params["ownerid"])) $this->SetOwner((int) $params["ownerid"]);

        // additional editors
        if (isset($params["additional_editors"])) {
            $addtarray = array();
            if( is_array($params['additional_editors']) ) {
                foreach ($params["additional_editors"] as $addt_user_id) {
                    $addtarray[] = (int) $addt_user_id;
                }
            }
            $this->SetAdditionalEditors($addtarray);
        }
    }

    /**
     * A function to get the internally generated URL for this content type.
     * This method may be overridden by content types.
     *
     * @param bool $rewrite if true, and mod_rewrite is enabled, build a URL suitable for mod_rewrite.
     * @return string
     */
    public function GetURL($rewrite = true)
    {
        $config = cmsms()->GetConfig();
        $url = "";
        $alias = ($this->mAlias != ''?$this->mAlias:$this->mId);

        $base_url = CMS_ROOT_URL;

        /* use root_url for default content */
        if($this->DefaultContent()) {
            $url =  $base_url . '/';
            return $url;
        }

        if( $rewrite ) {
            $url_rewriting = $config['url_rewriting'];
            $page_extension = $config['page_extension'];
            if ($url_rewriting == 'mod_rewrite') {
                $str = $this->HierarchyPath();
                if( $this->mURL != '') $str = $this->mURL;	// we have a url path
                $url = $base_url . '/' . $str . $page_extension;
                return $url;
            }
            else if (isset($_SERVER['PHP_SELF']) && $url_rewriting == 'internal') {
                  $str = $this->HierarchyPath();
                  if( $this->mURL != '') $str = $this->mURL; // we have a url path
                  $url = $base_url . '/index.php/' . $str . $page_extension;
                  return $url;
            }
        }

        $url = $base_url . '/index.php?' . $config['query_var'] . '=' . $alias;
        return $url;
    }

    /**
     * Return the raw value for a content property.
     * If no proeprty name is specified 'content_en' is assumed
     *
     * @abstract
     * @param string $propname An optional property name to display.  If none specified, the system should assume content_en.
     * @return string
     */
    public function Show(string $propname = 'content_en')
    {
    }

    /**
     * Return a list of all of the properties that may be edited by the current user when editing this content item
     * in a content editor form.
     *
     * Other content types may override this method, but should call the base method at the start.
     *
     * @abstract
     * @return array Array of stdclass objects containing name (string), tab (string), priority (integer), required (bool) members
     */
    public function GetEditableProperties()
    {
        if( !check_permission(get_userid(),'Manage All Content') ) {
            $basic_attributes = array('title','parent');
            $tmp_basic_attributes = cms_siteprefs::get('basic_attributes');
            if( $tmp_basic_attributes ) {
                $tmp_basic_attributes = explode(',',$tmp_basic_attributes);
                $basic_attributes = array_merge($tmp_basic_attributes,$basic_attributes);
            }

            $out = [];
            foreach( $this->_attributes as $one ) {
                if( $one->basic || in_array($one->name,$basic_attributes) ) $out[] = $one;
            }
            return $out;
        }
        return $this->_attributes;
        // todo: filter out the elements that this user isn't allowed to see.
    }

    /**
     * @ignore
     */
    private function _SortProperties(array $props)
    {
        // sort the properties.
        // sort the attributes by tab, priority, name...
        usort($props,function($a,$b) {
                if( !isset($a->tab) || $a->tab == '' ) $a->tab = ContentBase::TAB_MAIN;
                if( !isset($b->tab) || $b->tab == '' ) $b->tab = ContentBase::TAB_MAIN;

                // sort elements by tabname, and then priority
                $atab = $a->tab;
                $btab = $b->tab;

                $res = null;
                if( ($r = strcmp($atab,$btab)) != 0 ) return $r;
                if( $a->priority != $b->priority ) return $a->priority - $b->priority;
                return strcmp($a->name,$b->name);
        });

        return $props;
    }

    /**
     * @ignore
     */
    private function _GetEditableProperties()
    {
        if( isset($this->_editable_properties) ) return $this->_editable_properties;

        $props = $this->_SortProperties($this->GetEditableProperties());
        $this->_editable_properties = $props;
        return $props;
    }

    /**
     * Used from a page that allows content editing.
     * This method provides a list of distinct sections that devides up the various logical sections
     * that this content type supports for editing.
     *
     * @abstract
     * @return Array associative array list of tab keys and labels.
     */
    public function GetTabNames()
    {
        $props = $this->_GetEditableProperties();
        $arr = array();
        foreach( $props as $one ) {
            if( !isset($one->tab) || $one->tab == '' ) $one->tab = self::TAB_MAIN;
            $key = $lbl = $one->tab;
            if( endswith($key,'_tab__') ) $lbl = lang($key);
            $arr[$key] = $lbl;
        }
        return $arr;
    }

    /**
     * Get an optional message for each tab.
     *
     * @abstract
     * @since 2.0
     * @param string $key the tab key (as returned with GetTabNames)
     * @return string html text to display at the top of the tab.
     */
    public function GetTabMessage($key)
    {
        switch( $key ) {
            case self::TAB_PERMS:
                return '<div class="information">'.lang('msg_permstab').'</div>';
        }
    }

    /**
     * Get the elements for a specific tab.
     *
     * @param string $key tab key
     * @param bool   $adding  Whether this is an add or edit operation.
     * @return array An array of arrays.  Index 0 of each element should be a prompt field, and index 1 should be the input field for the prompt.
     */
    public function GetTabElements(string $key,bool $adding = FALSE)
    {
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
     * Method to indicate whether the current page has children.
     *
     * @internal
     * @param bool $activeonly Should we test only for active children.
     * @return bool
     */
    public function HasChildren(bool $activeonly = false)
    {
        die(__METHOD__);
        // todo: remove me.
        $node = cmsms()->GetContentOperations()->quickfind_node_by_id($id);
        if( !$node->has_children() ) return false;
        if( $activeonly == false) return true;

        $children = $node->get_children();
        if( $children ) {
            for( $i = 0, $n = count($children); $i < $n; $i++ ) {
                $content = $children[$i]->getContent();
                if( $content->Active() ) return true;
            }
        }

        return false;
    }

    /**
     * Return a list of additional editors.
     * Note: in the returned array, group id's are specified as negative integers.
     *
     * @return mixed Array of uids and group ids, or null
     */
    public function GetAdditionalEditors()
    {
        if( !is_array($this->mAdditionalEditors) ) return [];
        return $this->mAdditionalEditors;
    }

    /**
     * Set the list of additional editors.
     * Note: in the provided array, group id's are specified as negative integers.
     *
     * @param mixed $editorarray Array of uids and group ids, or null
     */
    public function SetAdditionalEditors($editorarray)
    {
        $this->mAdditionalEditors = $editorarray;
    }

    /**
	 * A utility method to return all of the userid and group ids in a format that is
	 * suitable to be used in a select field.
	 * Note: group ids are expressed as negative integers in the keys.
	 * @return array
	 */
    static public function GetAdditionalEditorOptions()
    {
        $opts = [];
        $gCms = cmsms();
        $userops = $gCms->GetUserOperations();
        $groupops = $gCms->GetGroupOperations();
        $allusers = $userops->LoadUsers();
        $allgroups = $groupops->LoadGroups();
        foreach ($allusers as $oneuser) {
            $opts[$oneuser->id] = $oneuser->username;
        }
        foreach ($allgroups as $onegroup) {
            if( $onegroup->id == 1 ) continue; // exclude admin group (they have all privileges anyways)
            $val = $onegroup->id*-1;
            $opts[$val] = lang('group').': '.$onegroup->name;
        }

        return $opts;
    }

    /**
     * A utility method to generate a <select> field for selecting additional editors.
	 * If a positive owner id is specified that user will be excluded from output select element.
	 *
	 * @see ContentBase::GetAdditionalEditorOptions
	 * @param array $addteditors Array of additional editors
	 * @param int  $owner_id  The current owner of the page.
	 * @return string HTML output
	 */
    static public function GetAdditionalEditorInput(array $addteditors,int $owner_id = -1)
    {
        $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_addteditor',lang('help_title_content_addteditor'));
        $ret[] = '<label for="addteditors">'.lang('additionaleditors').':</label>'.$help;
        $text = '<input name="additional_editors" type="hidden" value=""/>';
        $text .= '<select id="addteditors" name="additional_editors[]" multiple="multiple" size="5">';

        $topts = self::GetAdditionalEditorOptions();
        foreach( $topts as $k => $v ) {
            if( $k == $owner_id ) continue;
            $text .= CmsFormUtils::create_option(array('label'=>$v,'value'=>$k),$addteditors);
        }

        $text .= '</select>';
        $ret[] = $text;
        return $ret;
    }

    /**
     * Provides an input element to display the list of additional editors.
     * This method is usually called from within this object.
     *
     * @param array $addteditors An optional array of additional editor id's (group ids specified with negative values)
     * @return string The input element.
     * @see ContentBase::GetAdditionalEditorInput
     */
    public function ShowAdditionalEditors(array $addteditors = null)
    {
        $ret = array();
        if( ! $addteditors ) $addteditors = $this->GetAdditionalEditors();
        return self::GetAdditionalEditorInput($addteditors,$this->Owner());
    }

    /**
     * Handles setting the value (by member) of a base (not addon property) property of the content object
     * for base properties that have been removed from the form.
     *
	 * @ignore
	 */
    private function _handleRemovedBaseProperty(string $name,string $member)
    {
        if( !is_array($this->_attributes) ) return FALSE;
        $fnd = false;
        foreach( $this->_attributes as $attr ) {
            if( $attr->name == $name ) {
                $fnd = true;
                break;
            }
        }
        if( !$fnd ) {
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
     * @param string $name The property name
     * @param string $dflt The default value.
     */
    protected function RemoveProperty(string $name,string $dflt)
    {
        if( !is_array($this->_attributes) ) return;
        $tmp = array();
        for( $i = 0, $n = count($this->_attributes); $i < $n; $i++ ) {
            if( is_object($this->_attributes[$i]) && $this->_attributes[$i]->name == $name ) continue;
            $tmp[] = $this->_attributes[$i];
        }
        $this->_attributes = $tmp;
        $this->_prop_defaults[$name] = $dflt;
    }

    /**
     * Add a property definition.
     *
     * @since 1.11
     * @param string $name The property name
     * @param int $priority The property priority, for sorting.
     * @param string $tab The tab for the property (see tab constants)
     * @param bool $required (whether the property is required)
        * @param bool $basic Whether or not the property is a basic property (editable by even restricted editors)
     */
    protected function AddProperty(string $name,int $priority,string $tab = self::TAB_MAIN,bool $required = FALSE,bool $basic = FALSE)
    {
        $ob = new StdClass;
        $ob->name = (string) $name;
        $ob->priority = (int) $priority;
        $ob->tab = (string) $tab;
        $ob->required = (bool) $required;
        $ob->basic = $basic;

        if( !is_array($this->_attributes) ) $this->_attributes = array();
        $this->_attributes[] = $ob;
    }

    /**
     * Get all of the properties for this content object.  independent of whether the user is entitled to view them, or not.
     *
     * @since 2.0
     * @return array of stdclass objects
     */
    public function GetProperties()
    {
        return $this->_SortProperties($this->_attributes);
    }

    /**
	 * Add a property that is directly associtated with a field in the content table.
	 * @alias for AddProperty
	 *
	 * @param string $name The property name
	 * @param int    $priority The priority
	 * @param bool   $is_required Whether this field is required for this content type
	 * @param string  (optional) unused.
	 * @deprecated
	 */
    protected function AddBaseProperty(string $name,int $priority,bool $is_required = false)
    {
        $this->AddProperty($name,$priority,self::TAB_OPTIONS,$is_required);
    }

    /**
	 * Alias for AddBaseProperty.
	 *
	 * @param string $name
	 * @param int    $priority
	 * @param bool   $is_required
	 * @deprecated
	 */
    protected function AddContentProperty(string $name,int $priority,bool $is_required = false)
    {
        return $this->AddProperty($name,$priority,self::TAB_OPTIONS,$is_required);
    }

    /**
     * A method to display a single input element for an object basic, or extended property.
     *
     * @abstract
     * @param string $one The property name
     * @param bool $adding Whether or not we are in add or edit mode.
     * @return array consisting of two elements.  A label, and the input element.
     */
    protected function display_single_element(string $one, bool $adding)
    {
        // todo: move this stuff into an editor class.
        $app = cmsms();
        $config = $app->GetConfig();
        $contentops = $app->GetContentOperations();

        switch( $one ) {
            case 'cachable':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_cachable',lang('help_title_content_cachable'));
                return array('<label for="in_cachable">'.lang('cachable').':</label>'.$help,
            '<input type="hidden" name="cachable" value="0"/><input id="in_cachable" class="pagecheckbox" type="checkbox" value="1" name="cachable"'.($this->mCachable?' checked="checked"':'').' />');

            case 'title':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_title',lang('help_title_content_title'));
                return array('<label for="in_title">*'.lang('title').':</label>'.$help,
            '<input type="text" id="in_title" name="title" required="required" value="'.cms_htmlentities($this->mName).'" />');

            case 'menutext':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_menutext',lang('help_title_content_menutext'));
                return array('<label for="in_menutext">*'.lang('menutext').':</label>'.$help,
            '<input type="text" name="menutext" id="in_menutext" value="'.cms_htmlentities($this->mMenuText).'" />');

            case 'parent':
                $tmp = $contentops->CreateHierarchyDropdown($this->mId, $this->mParentId, 'parent_id', ($this->mId > 0) ? 0 : 1, 1, 0, 1, 1);
                if( empty($tmp) && !check_permission(get_userid(),'Manage All Content') ) {
                        return array('','<input type="hidden" name="parent_id" value="'.$this->mParentId.'" />');
                }
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_parent',lang('help_title_content_parent'));
                if( !empty($tmp) ) return array('<label for="parent_id">*'.lang('parent').':</label>'.$help,$tmp);
                break;

            case 'active':
                if( !$this->DefaultContent() ) {
                    $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_active',lang('help_title_content_active'));
                    return array('<label for="id_active">'.lang('active').':</label>'.$help,'<input type="hidden" name="active" value="0"/><input class="pagecheckbox" type="checkbox" name="active" id="id_active" value="1"'.($this->mActive?' checked="checked"':'').' />');
                }
                break;

            case 'showinmenu':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_showinmenu',lang('help_title_content_showinmenu'));
                return array('<label for="showinmenu">'.lang('showinmenu').':</label>'.$help,
            '<input type="hidden" name="showinmenu" value="0"/><input class="pagecheckbox" type="checkbox" value="1" name="showinmenu" id="showinmenu"'.($this->mShowInMenu?' checked="checked"':'').' />');

            case 'target':
                $text = '<option value="---">'.lang('none').'</option>';
                $text .= '<option value="_blank"'.($this->GetPropertyValue('target')=='_blank'?' selected="selected"':'').'>_blank</option>';
                $text .= '<option value="_parent"'.($this->GetPropertyValue('target')=='_parent'?' selected="selected"':'').'>_parent</option>';
                $text .= '<option value="_self"'.($this->GetPropertyValue('target')=='_self'?' selected="selected"':'').'>_self</option>';
                $text .= '<option value="_top"'.($this->GetPropertyValue('target')=='_top'?' selected="selected"':'').'>_top</option>';
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_target',lang('help_title_content_target'));
                return array('<label for="target">'.lang('target').':</label>'.$help,
            '<select name="target" id="target">'.$text.'</select>');

            case 'alias':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_page_alias',lang('help_title_page_alias'));
                return array('<label for="alias">'.lang('pagealias').':</label>'.$help,
            '<input type="text" name="alias" id="alias" value="'.$this->mAlias.'" />');

            case 'page_url':
                if( !$this->DefaultContent() ) {
                    $pretty_urls = $config['url_rewriting'] == 'none' ? 0 : 1;
                    if ($pretty_urls != 0) {
                        $str = '<input type="text" name="page_url" id="page_url" value="'.$this->mURL.'" size="50" maxlength="255"/>';
                        $prompt = '<label for="page_url">'.lang('page_url').':</label>';
                        if( cms_siteprefs::get('content_mandatory_urls',0) ) $prompt = '*'.$prompt;
                        $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_page_url',lang('help_title_page_url'));
                        return array($prompt.$help,$str);
                    }
                }
                break;

            case 'image':
                $dir = cms_join_path($config['image_uploads_path'],cms_siteprefs::get('content_imagefield_path'));
                $data = $this->GetPropertyValue('image');
                $filepicker = \cms_utils::get_filepicker_module();
                if( $filepicker ) {
                      $profile = $filepicker->get_default_profile( $dir, get_userid() );
                      $profile = $profile->overrideWith( ['top'=>$dir, 'type'=>'image'] );
                      $input = $filepicker->get_html( 'image', $data, $profile);
                }
                else {
                      $input = create_file_dropdown('image',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_',0,1);
                }
                if( !$input ) return FALSE;
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_image',lang('help_title_content_image'));
                return array('<label for="image">'.lang('image').':</label>'.$help,$input);

            case 'thumbnail':
                $dir = cms_join_path($config['image_uploads_path'],cms_siteprefs::get('content_thumbnailfield_path'));
                $data = $this->GetPropertyValue('thumbnail');
                $filepicker = \cms_utils::get_filepicker_module();
                if( $filepicker ) {
                      $profile = $filepicker->get_default_profile( $dir, get_userid() );
                      $profile = $profile->overrideWith( ['top'=>$dir, 'type'=>'image', 'match_prefix'=>'thumb_' ] );
                      $input = $filepicker->get_html( 'thumbnail', $data, $profile);
                }
                else {
                      $input = create_file_dropdown('thumbnail',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_',0,1);
                }
                if( !$input ) return FALSE;
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_thumbnail',lang('help_title_content_thumbnail'));
                return array('<label for="thumbnail">'.lang('thumbnail').':</label>'.$help,$input);

            case 'titleattribute':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_titleattribute',lang('help_title_content_ta'));
                return array('<label for="titleattribute">'.lang('titleattribute').':</label>'.$help,
            '<input type="text" name="titleattribute" id="titleattribute" maxlength="255" size="80" value="'.cms_htmlentities($this->mTitleAttribute).'" />');

            case 'accesskey':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_accesskey',lang('help_title_content_accesskey'));
                return array('<label for="accesskey">'.lang('accesskey').':</label>'.$help,
            '<input type="text" name="accesskey" id="accesskey" maxlength="5" value="'.cms_htmlentities($this->mAccessKey).'" />');

            case 'tabindex':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_tabindex',lang('help_title_content_tabindex'));
                return array('<label for="tabindex">'.lang('tabindex').':</label>'.$help,
            '<input type="text" name="tabindex" id="tabindex" maxlength="5" value="'.cms_htmlentities($this->mTabIndex).'" />');

            case 'extra1':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_extra1',lang('help_title_content_extra1'));
                return array('<label for="extra1">'.lang('extra1').':</label>'.$help,
            '<input type="text" name="extra1" id="extra1" maxlength="255" size="80" value="'.cms_htmlentities($this->GetPropertyValue('extra1')).'" />');

            case 'extra2':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_extra2',lang('help_title_content_extra2'));
                return array('<label for="extra2">'.lang('extra2').':</label>'.$help,
            '<input type="text" name="extra2" id="extra2" maxlength="255" size="80" value="'.cms_htmlentities($this->GetPropertyValue('extra2')).'" />');

            case 'extra3':
                $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_extra3',lang('help_title_content_extra3'));
                return array('<label for="extra3">'.lang('extra3').':</label>'.$help,
            '<input type="text" name="extra3" id="extra3" maxlength="255" size="80" value="'.cms_htmlentities($this->GetPropertyValue('extra3')).'" />');

            case 'owner':
                $showadmin = cmsms()->GetContentOperations()->CheckPageOwnership(get_userid(), $this->Id());
                if (!$adding && (check_permission(get_userid(),'Manage All Content') || $showadmin) ) {
                    $userops = cmsms()->GetUserOPerations();
                    $help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_owner',lang('help_title_content_owner'));
                    return array('<label for="owner">'.lang('owner').':</label>'.$help, $userops->GenerateDropdown($this->Owner()));
                }
                break;

            case 'additionaleditors':
                // do owner/additional-editor stuff
                if( $adding || check_permission(get_userid(),'Manage All Content') ||
                    $contentops->CheckPageOwnership(get_userid(),$this->Id()) ) {
                        return $this->ShowAdditionalEditors();
                }
                break;

            default:
                throw new CmsInvalidDataException('Attempt to display invalid property '.$one);
        }
    }

    /**
     * @internal
     */
    public function setInsertedDetails(int $newID, int $ts)
    {
        $str = strftime('%Y-%m-%d %H:%M:%S',$ts);
        $this->mId = $newID;
        $this->mCreationDate = $str;
        $this->mModifiedDate = $str;
    }

    /**
     * @internal
     */
    public function setModifiedDetails(int $ts)
    {
        $str = strftime('%Y-%m-%d %H:%M:%S',$ts);
        $this->mModifiedDate = $str;
    }
} // end of class
