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

/**
 * @ignore
 */
define('CMS_CONTENT_HIDDEN_NAME','--------');
define('__CMS_PREVIEW_PAGE__',-100);

/**
 * Base level content object.
 *
 * This is the base level content class.  It is an abstract object and cannot be instantiated directly.
 * All content pages in the database are required to be drived from this class.
 *
 * @since		0.8
 * @package		CMS
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
	 * The old parent id...
	 *
     * Used to be used only on update to detect reparenting... now since SetAllHierarchyPositions is efficient
     * it is not needed.
     *
	 * @internal
     * @deprecated
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

	/**
	 * @ignore
	 */
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
	 * @param int Integer id
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
	 * The old alias is used when editing pages to detect changes in page alias
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
	public function SetMetadata($metadata)
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
	 * The title attribue can be used in navigations to set the "title=" attribute of a link
	 * some menu templates may ignore this.
	 *
	 * @param string $titleattribute The title attribute
	 */
	public function SetTitleAttribute($titleattribute)
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
	public function SetAccessKey($accesskey)
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
	public function SetParentId($parentid)
	{
        $parentid = (int) $parentid;
        if( $parentid < 1 ) $parentid = -1;
		$this->mParentId = $parentid;
	}

	/**
	 * Return the id of the template associated with this content page.
	 *
	 * @return int.
	 */
	public function TemplateId()
	{
		return $this->mTemplateId;
	}

	/**
	 * Set the id of the template associated with this content page.
	 *
	 * @param int $templateid
	 */
	public function SetTemplateId($templateid)
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
	public function SetItemOrder($itemorder)
	{
		$itemorder = (int)$itemorder;
		if( $itemorder > 0 || $itemorder == -1 ) $this->mItemOrder = $itemorder;
	}


	/**
	 * Returns the OldItemOrder.
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
	 * Returns the Hierarchy of the current page.
	 * A string like #.##.## indicating the path to this page and it's order
	 * this value uses the item order when calculating the output i.e:  3.3.3
	 * to indicate the third grandghild of the third child of the third root page.
	 *
	 * @return string
	 */
	public function Hierarchy()
	{
		$contentops = ContentOperations::get_instance();
		return $contentops->CreateFriendlyHierarchyPosition($this->mHierarchy);
	}

	/**
	 * Sets the Hierarchy
	 *
	 * @internal
	 * @param string $hierarchy
	 */
	public function SetHierarchy($hierarchy)
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
	 * @param bool $defaultcontent
	 */
	public function SetDefaultContent($defaultcontent)
	{
		$this->mDefaultContent = (bool) $defaultcontent;
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
	public function SetCachable($cachable)
	{
		$this->mCachable = (bool) $cachable;
	}

	/**
	 * Return whether this page should be accessed via a secure protocol.
	 * The secure flag effects whether the ssl protocol and appropriate config entries are used when generating urls to this page.
	 *
	 * @return bool
	 */
	public function Secure()
	{
		return $this->mSecure;
	}

	/**
	 * Set whether this page should be accessed via a secure protocol.
	 * The secure flag effectsw whether the ssl protocol and appropriate config entries are used when generating urls to this page.
	 *
	 * @param bool $secure
	 */
	public function SetSecure($secure)
	{
		$this->mSecure = $secure;
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
	public function SetLastModifiedBy($lastmodifiedby)
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
	 * If an empty alias is supplied, and depending upon the doAutoAliasIfEnabled flag, and config entries
	 * a suitable alias may be calculated from other data in the page object
	 * This method relies on the menutext and the name of the content page already being set.
	 *
	 * @param string $alias The alias
	 * @param bool $doAutoAliasIfEnabled Whether an alias should be calculated or not.
	 */
	public function SetAlias($alias = null, $doAutoAliasIfEnabled = true)
	{
        $contentops = ContentOperations::get_instance();
        $config = \cms_config::get_instance();
		if ($alias == '' && $doAutoAliasIfEnabled && $config['auto_alias_content'] == true) {
			$alias = trim($this->mMenuText);
			if ($alias == '') $alias = trim($this->mName);

			// auto generate an alias
			$tolower = true;
			$alias = munge_string_to_url($alias, $tolower);
            $res = $contentops->CheckAliasValid($alias);
            if( !$res ) {
                $alias = 'p'.$alias;
                $res = $contentops->CheckAliasValid($alias);
                if( !$res ) throw new \CmsContentException(lang('invalidalias2'));
            }
        }

        if( $alias ) {
			// Make sure auto-generated new alias is not already in use on a different page, if it does, add "-2" to the alias

            // make sure we start with a valid alias.
            $res = $contentops->CheckAliasValid($alias);
            if( !$res ) throw new \CmsContentException(lang('invalidalias2'));

            // now auto-increment the alias.
            $prefix = $alias;
            $num = 1;
            if( preg_match('/(.*)-([0-9]*)$/',$alias,$matches) ) {
                $prefix = $matches[1];
                $num = (int) $matches[2];
            }
            $test = $alias;
            $testnum = 1;
            do {
                if( !$contentops->CheckAliasUsed($test,$this->Id()) ) {
                    $alias = $test;
                    break;
                }
                $num++;
                $test = $prefix.'-'.$num;
            } while( $testnum < 100 );
            if( $testnum >= 100 && $test != $alias ) throw new \CmsContentException(lang('aliasalreadyused'));
		}

		$this->mAlias = $alias;
        \CMSMS\internal\global_cache::clear('content_quicklist');
        \CMSMS\internal\global_cache::clear('content_tree');
        \CMSMS\internal\global_cache::clear('content_flatlist');
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
	public function SetMenuText($menutext)
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
		$hm = CmsApp::get_instance()->GetHierarchyManager();
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
	public function HasProperty($name)
	{
		if( !$name ) return FALSE;
		if( !is_array($this->_props) ) $this->_load_properties();
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
	public function GetPropertyValue($name)
	{
		if( $this->HasProperty($name) ) return $this->_props[$name];
	}

	/**
	 * @ignore
	 */
	private function _load_properties()
	{
		if( $this->mId <= 0 ) return FALSE;

		$this->_props = array();
		$db = CmsApp::get_instance()->GetDb();
		$query = 'SELECT * FROM '.CMS_DB_PREFIX.'content_props WHERE content_id = ?';
		$dbr = $db->GetArray($query,array((int)$this->mId));

		foreach( $dbr as $row ) {
			$this->_props[$row['prop_name']] = $row['content'];
		}
		return TRUE;
	}

	/**
	 * @ignore
	 */
	private function _save_properties()
	{
		if( $this->mId <= 0 ) return FALSE;
		if( !is_array($this->_props) || count($this->_props) == 0 ) return FALSE;

		$db = CmsApp::get_instance()->GetDb();
		$query = 'SELECT prop_name FROM '.CMS_DB_PREFIX.'content_props WHERE content_id = ?';
		$gotprops = $db->GetCol($query,array($this->mId));

		$now = $db->DbTimeStamp(time());
		$iquery = 'INSERT INTO '.CMS_DB_PREFIX."content_props
                    (content_id,type,prop_name,content,modified_date)
                    VALUES (?,?,?,?,$now)";
		$uquery = 'UPDATE '.CMS_DB_PREFIX."content_props SET content = ?, modified_date = $now WHERE content_id = ? AND prop_name = ?";

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
	 * @param string $name The property name
	 * @param string $value The property value.
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
	 * @param string $name The property name
	 * @param string $value The property value.
	 */
	public function SetPropertyValueNoLoad($name,$value)
	{
		if( !is_array($this->_props) ) $this->_props = array();
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
	 * @param array $data Data as loaded from the database
	 * @param bool  $loadProperties Optionally load content properties at the same time.
	 * @returns	bool
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
		if( isset($data['secure']) ) $this->mSecure = $data["secure"];
		if( isset($data['page_url']) ) $this->mURL  = $data["page_url"];
		$this->mLastModifiedBy             = $data["last_modified_by"];
		$this->mCreationDate               = $data["create_date"];
		$this->mModifiedDate               = $data["modified_date"];

		if ($loadProperties == true) {
			$this->_load_properties();
			if (!is_array($this->_props) ) $result = false;
		}

		if (false == $result) $this->SetInitialValues();

		$this->Load();
		return $result;
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
		$out['secure'] = ($this->mSecure)?1:0;
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
	 * Save or update the content.
	 *
	 * @todo This function should return something (or throw an exception)
	 */
	public function Save()
	{
        \CMSMS\HookManager::do_hook('Core::ContentEditPre', [ 'content' => &$this ] );

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

        $contentops = ContentOperations::get_instance();
        $contentops->SetContentModified();
        $contentops->SetAllHierarchyPositions();
        \CMSMS\HookManager::do_hook('Core::ContentEditPost', [ 'content' => &$this ] );
	}

	/**
	 * Update the database with the contents of the content object.
	 *
	 * This method will calculate a new item order for the object if necessary and then
	 * save the content record, the additional editors, and the properties.
	 * Additionally, if a page url is specified a static route will be created
	 *
	 * Because multiple content objects may be modified in one batch
	 * the calling function is responsible for ensuring that page hierarchies are
	 * updated.
	 *
	 * @see ContentOperations::SetAllHierarchyPositions()
	 * @todo this function should return something, or throw an exception.
	 */
	protected function Update()
	{
		$gCms = CmsApp::get_instance();
		$db = $gCms->GetDb();
		$result = false;

		// Figure out the item_order (if necessary)
		if ($this->mItemOrder < 1) {
			$query = "SELECT ".$db->IfNull('max(item_order)','0')." as new_order FROM ".CMS_DB_PREFIX."content WHERE parent_id = ?";
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

		$query = "UPDATE ".CMS_DB_PREFIX."content SET content_name = ?, owner_id = ?, type = ?, template_id = ?, parent_id = ?, active = ?, default_content = ?, show_in_menu = ?, cachable = ?, secure = ?, page_url = ?, menu_text = ?, content_alias = ?, metadata = ?, titleattribute = ?, accesskey = ?, tabindex = ?, modified_date = ?, item_order = ?, last_modified_by = ? WHERE content_id = ?";
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
                                     (int) $this->mId
                                     ));

        /*
		if ($this->mOldParentId != $this->mParentId) {
			// Fix the item_order if necessary
			$query = "UPDATE ".CMS_DB_PREFIX."content SET item_order = item_order - 1 WHERE parent_id = ? AND item_order > ?";
			$result = $db->Execute($query, array($this->mOldParentId,$this->mOldItemOrder));

			$this->mOldParentId = $this->mParentId;
			$this->mOldItemOrder = $this->mItemOrder;
		}
        */

		if (isset($this->mAdditionalEditors)) {
			$query = "DELETE FROM ".CMS_DB_PREFIX."additional_users WHERE content_id = ?";
			$db->Execute($query, array($this->Id()));

			foreach ($this->mAdditionalEditors as $oneeditor) {
				$new_addt_id = $db->GenID(CMS_DB_PREFIX."additional_users_seq");
				$query = "INSERT INTO ".CMS_DB_PREFIX."additional_users (additional_users_id, user_id, content_id) VALUES (?,?,?)";
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
	 * Initially save a content object with no id to the database.
	 *
	 * Like the Update method this method will determine a new item order
	 * save the record, save properties and additional editors, but will not
	 * update the hierarchy positions.
	 *
	 * @see ContentOperations::SetAllHierarchyPositions()
	 */
	protected function Insert()
	{
        # :TODO: This function should return something
        # :TODO: Take care bout hierarchy here, it has no value !
        # :TODO: Figure out proper item_order
		$gCms = CmsApp::get_instance();
		$db = $gCms->GetDb();

		$result = false;

        $query = 'SELECT content_id FROM '.CMS_DB_PREFIX.'content WHERE default_content = 1';
        $dflt_pageid = (int) $db->GetOne($query);
        if( $dflt_pageid < 1 ) $this->SetDefaultContent(TRUE);

		// Figure out the item_order
		if ($this->mItemOrder < 1) {
			$query = "SELECT max(item_order) as new_order FROM ".CMS_DB_PREFIX."content WHERE parent_id = ?";
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

		$newid = $db->GenID(CMS_DB_PREFIX."content_seq");
		$this->mId = $newid;

		$this->mModifiedDate = $this->mCreationDate = trim($db->DBTimeStamp(time()), "'");

		$query = "INSERT INTO ".CMS_DB_PREFIX."content (content_id, content_name, content_alias, type, owner_id, parent_id, template_id, item_order, hierarchy, id_hierarchy, active, default_content, show_in_menu, cachable, secure, page_url, menu_text, metadata, titleattribute, accesskey, tabindex, last_modified_by, create_date, modified_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

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
				$new_addt_id = $db->GenID(CMS_DB_PREFIX."additional_users_seq");
				$query = "INSERT INTO ".CMS_DB_PREFIX."additional_users (additional_users_id, user_id, content_id) VALUES (?,?,?)";
				$db->Execute($query, array($new_addt_id, $oneeditor, $this->Id()));
			}
		}

		if( $this->mURL != '' ) {
			$route = CmsRoute::new_builder($this->mURL,'__CONTENT__',$this->mId,'',TRUE);
			cms_route_manager::add_static($route);
		}
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
				$contentops = ContentOperations::get_instance();
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
					$gCms = CmsApp::get_instance();
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
	 * Delete the current content object from the database.
	 *
	 * @todo this function should return something, or throw an exception
	 */
	function Delete()
	{
		$gCms = CmsApp::get_instance();
        \CMSMS\HookManager::do_hook('Core::ContentDeletePre', [ 'content' => &$this ] );
		$db = $gCms->GetDb();
		$result = false;

		if ($this->mId > 0) {
			$query = "DELETE FROM ".CMS_DB_PREFIX."content WHERE content_id = ?";
			$dbresult = $db->Execute($query, array($this->mId));

			// Fix the item_order if necessary
			$query = "UPDATE ".CMS_DB_PREFIX."content SET item_order = item_order - 1 WHERE parent_id = ? AND item_order > ?";
			$result = $db->Execute($query,array($this->ParentId(),$this->ItemOrder()));

			// DELETE properties
			$query = 'DELETE FROM '.CMS_DB_PREFIX.'content_props WHERE content_id = ?';
			$result = $db->Execute($query,array($this->mId));
			$this->_props = null;

			// Delete additional editors.
			$query = 'DELETE FROM '.CMS_DB_PREFIX.'additional_users WHERE content_id = ?';
			$result = $db->Execute($query,array($this->mId));
			$this->mAdditionalEditors = null;

			// Delete route
			if( $this->mURL != '' ) cms_route_manager::del_static($this->mURL);
		}

        \CMSMS\HookManager::do_hook('Core::ContentDeletePost', [ 'content' => &$this ] );
		$this->mId = -1;
		$this->mItemOrder = -1;
		$this->mOldItemOrder = -1;
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
	public function FillParams($params,$editing = false)
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
		if( !$editing || $new_alias || $alias_field_exists ) {
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

		// secure
		if (isset($params['secure'])) {
			$this->mSecure = (int) $params['secure'];
		}
		else {
			$this->_handleRemovedBaseProperty('secure','mSecure');
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
		$config = \cms_config::get_instance();
		$url = "";
		$alias = ($this->mAlias != ''?$this->mAlias:$this->mId);

		$base_url = CMS_ROOT_URL;
		if( $this->Secure() ) $base_url = $config['ssl_url'];

		/* use root_url for default content */
		if($this->DefaultContent()) {
			$url =  $base_url . '/';
			return $url;
		}

        if( $rewrite == true ) {
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
	 * Move this content up, or down with respect to its peers.
	 *
	 * Note: This method modifies two content objects.
	 *
	 * @since 2.0
	 * @param int $direction direction. negative value indicates up, positive value indicates down.
	 */
	public function ChangeItemOrder($direction)
	{
		$db = CmsApp::get_instance()->GetDb();
		$time = $db->DBTimeStamp(time());
		$parentid = $this->ParentId();
		$order = $this->ItemOrder();
		if( $direction < 0 && $this->ItemOrder() > 1 ) {
			// up
			$query = 'UPDATE '.CMS_DB_PREFIX.'content SET item_order = (item_order + 1), modified_date = '.$time.'
                  WHERE item_order = ? AND parent_id = ?';
			$db->Execute($query,array($order-1,$parentid));
			$query = 'UPDATE '.CMS_DB_PREFIX.'content SET item_order = (item_order - 1), modified_date = '.$time.'
                  WHERE content_id = ?';
			$db->Execute($query,array($this->Id()));
		}
		else if( $direction > 0 ) {
			// down.
			$query = 'UPDATE '.CMS_DB_PREFIX.'content SET item_order = (item_order - 1), modified_date = '.$time.'
                  WHERE item_order = ? AND parent_id = ?';
			$db->Execute($query,array($order+1,$parentid));
			$query = 'UPDATE '.CMS_DB_PREFIX.'content SET item_order = (item_order + 1), modified_date = '.$time.'
                  WHERE content_id = ?';
			$db->Execute($query,array($this->Id()));
		}
        \CMSMS\internal\global_cache::clear('content_tree');
        \CMSMS\internal\global_cache::clear('content_flatlist');
	}

	/**
	 * Return the raw value for a content property.
	 * If no proeprty name is specified 'content_en' is assumed
	 *
	 * @abstract
	 * @param string $propname An optional property name to display.  If none specified, the system should assume content_en.
	 * @return string
	 */
	public function Show($propname = 'content_en')
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
	private function _SortProperties($props)
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
                if( ($r = strcmp($atab,$btab)) != 0 ) $res = $r;
                else if( $a->priority < $b->priority ) $res = -1;
                else if( $a->priority > $b->priority ) $res = 1;
                else $res = strcmp($a->name,$b->name);
                return $res;
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
			break;
		}
	}

	/**
	 * Get the elements for a specific tab.
	 *
	 * @param string $key tab key
	 * @param bool   $adding  Whether this is an add or edit operation.
	 * @return array An array of arrays.  Index 0 of each element should be a prompt field, and index 1 should be the input field for the prompt.
	 */
	public function GetTabElements($key,$adding = FALSE)
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
	 * @param bool $activeonly Should we test only for active children.
	 * @return bool
	 */
	public function HasChildren($activeonly = false)
	{
        	$node = ContentOperations::get_instance()->quickfind_node_by_id($id);
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
		if (!isset($this->mAdditionalEditors)) {
			$db = \CmsApp::get_instance()->GetDb();
			$this->mAdditionalEditors = array();

			$query = "SELECT user_id FROM ".CMS_DB_PREFIX."additional_users WHERE content_id = ?";
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
		$opts = array();
		$userops = UserOperations::get_instance();
		$groupops = GroupOperations::get_instance();
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
	static public function GetAdditionalEditorInput($addteditors,$owner_id = -1)
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
	public function ShowAdditionalEditors($addteditors = '')
	{
		$ret = array();
		if( $addteditors == '' ) $addteditors = $this->GetAdditionalEditors();
		return self::GetAdditionalEditorInput($addteditors,$this->Owner());
	}

    /**
     * Handles setting the value (by member) of a base (not addon property) property of the content object
     * for base properties that have been removed from the form.
     *
	 * @ignore
	 */
	private function _handleRemovedBaseProperty($name,$member)
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
	protected function RemoveProperty($name,$dflt)
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
	protected function AddProperty($name,$priority,$tab = self::TAB_MAIN,$required = FALSE,$basic = FALSE)
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
	protected function AddBaseProperty($name,$priority,$is_required = 0)
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
	protected function AddContentProperty($name,$priority,$is_required = 0)
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
	protected function display_single_element($one,$adding)
	{
		$config = \cms_config::get_instance();

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
			$contentops = ContentOperations::get_instance();
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

		case 'secure':
			$opt = '';
			if( $this->mSecure ) $opt = ' checked="checked"';
			$str  = '<input type="hidden" name="secure" value="0"/>';
			$str .= '<input type="checkbox" name="secure" id="secure" value="1"'.$opt.'/>';
			$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_secure',lang('help_title_content_secure'));
			return array('<label for="secure">'.lang('secure_page').':</label>'.$help,$str);

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
/*
			$dir = cms_join_path($config['image_uploads_path'],cms_siteprefs::get('content_imagefield_path'));
			$data = $this->GetPropertyValue('image');
			$dropdown = create_file_dropdown('image',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_',1,1);
			if( !$dropdown ) return;
			$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_image',lang('help_title_content_image'));
			return array('<label for="image">'.lang('image').':</label>'.$help,$dropdown);
*/

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
			$showadmin = ContentOperations::get_instance()->CheckPageOwnership(get_userid(), $this->Id());
			if (!$adding && (check_permission(get_userid(),'Manage All Content') || $showadmin) ) {
                $userops = UserOperations::get_instance();
				$help = '&nbsp;'.cms_admin_utils::get_help_tag('core','help_content_owner',lang('help_title_content_owner'));
				return array('<label for="owner">'.lang('owner').':</label>'.$help, $userops->GenerateDropdown($this->Owner()));
			}
			break;

		case 'additionaleditors':
			// do owner/additional-editor stuff
			if( $adding || check_permission(get_userid(),'Manage All Content') ||
				ContentOperations::get_instance()->CheckPageOwnership(get_userid(),$this->Id()) ) {
				return $this->ShowAdditionalEditors();
			}
			break;

		default:
			throw new CmsInvalidDataException('Attempt to display invalid property '.$one);
		}
	}
} // end of class

?>
