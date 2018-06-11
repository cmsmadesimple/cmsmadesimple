<?php
#Visit our homepage at: http://cmsmadesimple.org

/**
 * This file contails the classes and methods to define the LayoutTemplate functionality
 * @package CMS
 * @license GPL
 */

use \CMSMS\HookManager;

/**
 * A class to represent a smarty template.
 *
 * @package CMS
 * @license GPL
 * @since 2.0
 * @author Robert Campbell <calguy1000@gmail.com>
 */
class CmsLayoutTemplate
{

	/**
	 * @ignore
	 */
    const TABLENAME = 'layout_templates';

	/**
	 * @ignore
	 */
    const ADDUSERSTABLE = 'layout_tpl_addusers';

	/**
	 * @ignore
	 */
	private $_dirty;

	/**
	 * @ignore
	 */
	private $_data = array('listable'=>true);

	/**
	 * @ignore
	 */
	private $_addt_editors;

	/**
	 * @ignore
	 */
	private $_design_assoc;

	/**
	 * @ignore
	 */
	private static $_obj_cache;

	/**
	 * @ignore
	 */
	private static $_name_cache;

	/**
	 * @ignore
	 */
	private static $_lock_cache;

	/**
	 * @ignore
	 */
	private static $_lock_cache_loaded;

	/**
	 * @ignore
	 */
	public function __clone()
	{
		if( isset($this->_data['id']) ) unset($this->_data['id']);
        $this->_data['type_dflt'] = false;
		$this->_dirty = TRUE;
	}

	/**
	 * Get the integer id of this template
	 *
	 * @return int
	 */
	public function get_id()
	{
		if( isset($this->_data['id']) ) return $this->_data['id'];
	}

	/**
	 * Get the template name
	 *
	 * @return string
	 */
	public function get_name()
	{
		if( isset($this->_data['name']) ) return $this->_data['name'];
	}

	/**
	 * Set the name of the template
	 *
	 * The template name cannot be empty, can only consist of a few characters in the name
	 * and must be unique
	 *
	 * @throws CmsInvalidDataException
	 * @param string $str
	 */
	public function set_name($str)
	{
        if( !CmsAdminUtils::is_valid_itemname($str)) throw new CmsInvalidDataException("Invalid characters in name: $str");
		$this->_data['name'] = $str;
		$this->_dirty = TRUE;
	}

	/**
	 * Get the content of the template
	 *
	 * @return string
	 */
	public function get_content()
	{
        if( isset($this->_data['content']) ) return $this->_data['content'];
	}

	/**
	 * Set the content of the template
	 *
	 * @param string $str Smarty template text
	 */
	public function set_content($str)
	{
		$str = trim($str);
		if( !$str ) $str = '{* Empty Smarty Template *}';
		$this->_data['content'] = $str;
		$this->_dirty = TRUE;
	}

	/**
	 * Get the description for the template
	 *
	 * @return string
	 */
	public function get_description()
	{
		if( isset($this->_data['description']) ) return $this->_data['description'];
	}

	/**
	 * Set the description for the template
	 *
	 * @param string $str
	 */
	public function set_description($str)
	{
		$str = trim($str);
		$this->_data['description'] = $str;
		$this->_dirty = TRUE;
	}

	/**
	 * Get the type id for the template
	 *
	 * @return int
	 */
	public function get_type_id()
	{
		if( isset($this->_data['type_id']) ) return $this->_data['type_id'];
	}

	/**
	 * Set the type id for the template
	 *
     * @throws \CmsLogicException
	 * @param mixed $a Either an instance of CmsLayoutTemplateType object, an integer type id, or a string template type identifier
	 * @see CmsLayoutTemplateType
	 */
	public function set_type($a)
	{
		$n = null;
		if( is_object($a) && is_a($a,'CmsLayoutTemplateType') ) {
			$n = $a->get_id();
		}
		else if( is_numeric($a) && (int)$a > 0 ) {
			$n = (int)$a;
		}
		else if( is_string($a) && strlen($a) ) {
			$type = CmsLayoutTemplateType::load($a);
			$n = $type->get_id();
		}
        else {
            throw new \CmsLogicException('Invalid data passed to '.__METHOD__);
        }

		$this->_data['type_id'] = (int) $n;
		$this->_dirty = TRUE;
	}

	/**
	 * Get the flag indicating that this template is the default template for its type
	 *
	 * @return bool
	 * @see CmsLayoutTemplateType
	 */
	public function get_type_dflt()
	{
		if( isset($this->_data['type_dflt']) ) return $this->_data['type_dflt'];
	}

	/**
	 * Set the flag that indicates that this template is the default template for its type
	 * Only one template can be the default for a template type
	 *
	 * @param bool $flag
	 * @see CmsLayoutTemplateType
	 */
	public function set_type_dflt($flag)
	{
		$n = (bool)$flag;
		$this->_data['type_dflt'] = $n;
		$this->_dirty = TRUE;
	}

	/**
	 * Get the category id for this template
	 * A template is not required to be associated with a category
	 *
	 * @param int
	 */
	public function get_category_id()
	{
		if( isset($this->_data['category_id']) ) return (int)$this->_data['category_id'];
	}

	/**
	 * Get the category for this template
	 * A template is not required to be associated with a category
	 *
	 * @param CmsLayoutTemplateCategory
	 * @see CmsLayoutTemplateCategory
	 */
	public function &get_category()
	{
		$n = $this->get_category_id();
		if( $n > 0 ) return CmsLayoutTemplateCategory::load($n);
	}

	/**
	 * Set the category for a template
	 *
     * @throws \CmsLogicException
	 * @param mixed $a Either a CmsLayoutTemplateCategory object, a category name (string) or category id (int)
	 * @see CmsLayoutTemplateCategory
	 */
	public function set_category($a)
	{
        if( !$a ) return;
		$n = null;
		if( is_object($a) && is_a($a,'CmsLayoutTemplateCategory') ) {
			$n = $a->get_id();
		}
        else if( is_numeric($a) && (int) $a > 0 ) {
            $n = $a;
        }
		else if( is_string($a) && strlen($a) ) {
			$cat = CmsLayoutTemplateCategory::load($a);
			$n = $cat->get_id();
		}
        else {
            throw new \CmsLogicException('Invalid data passed to '.__METHOD__);
        }

		$this->_data['category_id'] = (int) $n;
		$this->_dirty = TRUE;
	}

	/**
	 * Get a list of the design id's that this template is associated with
	 *
	 * @return array Array of integers
	 */
	public function get_designs()
	{
		if( !is_array($this->_design_assoc) ) {
            if( !$this->get_id() ) return;
			$this->_design_assoc = array();
			$db = CmsApp::get_instance()->GetDb();
			$query = 'SELECT design_id FROM '.CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE.' WHERE tpl_id = ?';
			$tmp = $db->GetCol($query,array((int)$this->get_id()));
			if( is_array($tmp) && count($tmp) ) $this->_design_assoc = $tmp;
		}
		return $this->_design_assoc;
	}

	/**
	 * Set the list of designs that this template is associated with
	 *
	 * @throws CmsInvalidDataException
	 * @param array $x Array of integers.
	 */
	public function set_designs($x)
	{
		if( !is_array($x) ) return;

		foreach( $x as $y ) {
            if( !is_numeric($y) || (int) $y < 1 ) throw new CmsInvalidDataException('Invalid data in design list.  Expect array of integers');
		}

		$this->_design_assoc = $x;
	}

    /**
	 * Associate a new design with this template
	 *
     * @throws CmsLogicException
	 * @param mixed $a A CmsLayoutCollection object, an integer design id, or a string design name.
	 * @see CmsLayoutCollection
	 */
	public function add_design($a)
	{
		$n = null;
		if( is_object($a) && is_a($a,'CmsLayoutCollection') ) {
			$n = $a->get_id();
		}
		else if( is_numeric($a) && (int)$a > 0 ) {
			$n = $a;
		}
		else if( (is_string($a) && strlen($a)) || (int)$a > 0 ) {
			$design = CmsLayoutCollection::load($a);
			$n = $design->get_id();
		}
        else {
            throw new \CmsLogicException('Invalid data passed to '.__METHOD__);
        }

		if( !is_array($this->_design_assoc) ) $this->get_designs();
		$this->_design_assoc[] = (int) $n;
		$this->_dirty = TRUE;
	}

	/**
	 * Remove a design from the list of associated designs
	 *
     * @throws CmsLogicException
	 * @param mixed $a A CmsLayoutCollection object, an integer design id, or a string design name.
	 * @see CmsLayoutCollection
	 */
	public function remove_design($a)
	{
		if( !is_array($this->_design_assoc) || count($this->_design_assoc) == 0 ) return;

		$n = null;
		if( is_object($a) && is_a($a,'CmsLayoutCollection') ) {
			$n = $a->get_id();
		}
		else if( is_numeric($a) && (int)$a > 0 ) {
			$n = $a;
		}
		else if( (is_string($a) && strlen($a)) ) {
			$design = CmsLayoutCollection::load($a);
			$n = $design->get_id();
		}
        else {
            throw new \CmsLogicException('Invalid data passed to '.__METHOD__);
        }

        $designs = $this->get_designs();
		if( in_array($n,$designs) ) {
			$t = array();
			foreach( $designs as $one ) {
				if( $n == $one ) continue;
				$t[] = $one;
			}
			$this->_design_assoc = $t;
			$this->_dirty = TRUE;
		}
	}

	/**
	 * Get the integer owner id of this template
	 *
	 * @return int
	 */
	public function get_owner_id()
	{
		if( isset($this->_data['owner_id']) ) return $this->_data['owner_id'];
		return -1;
	}

	/**
	 * Set the owner id
	 *
     * @throws CmsInvalidDataException
	 * @param mixed $a An integer admin user id, a string admin username, or an instance of a User object
	 * @see User
	 */
	public function set_owner($a)
	{
		$n = null;
        if( is_numeric($a) && $a > 0 ) {
			$n = (int)$a;
		}
		else if( is_string($a) && strlen($a) ) {
			// load the user by name.
			$ops = UserOperations::get_instance();
			$ob = $ops->LoadUserByUsername($a);
			if( is_object($a) && is_a($a,'User') ) $n = $a->id;
		}
		else if( is_object($a) && is_a($a,'User') ) {
			$n = $a->id;
		}

		if( $n < 1 ) throw new CmsInvalidDataException('Owner id must be valid in '.__METHOD__);
		$this->_data['owner_id'] = (int) $n;
		$this->_dirty = TRUE;
	}

	/**
	 * Get the date that this template was initially stored in the database
	 * The created date can not be externally modified
	 *
	 * @return int
	 */
	public function get_created()
	{
		if( isset($this->_data['created']) ) return $this->_data['created'];
	}

	/**
	 * Get the date that this template was last saved to the database
	 * The modified date cannot be externally modified
	 *
	 * @return int
	 */
	public function get_modified()
	{
		if( isset($this->_data['modified']) ) return $this->_data['modified'];
	}

	/**
	 * Get a list of user id's other than the owner that are allowed edit functionality to this template
	 *
	 * @return array Array of integer user ids
	 */
	public function get_additional_editors()
	{
		if( is_null($this->_addt_editors) ) {
			if( $this->get_id() ) {
				$db = CmsApp::get_instance()->GetDb();
				$query = 'SELECT user_id FROM '.CMS_DB_PREFIX.self::ADDUSERSTABLE.' WHERE tpl_id = ?';
				$col = $db->GetCol($query,array($this->get_id()));
				$this->_addt_editors = array();
				if( count($col) ) $this->_addt_editors = $col;
			}
		}
		if( count($this->_addt_editors) ) return $this->_addt_editors;
	}

	/**
	 * @ignore
	 */
	private static function _resolve_user($a)
	{
		if( is_numeric($a) && $a > 0 ) return $a;
		if( is_string($a) && strlen($a) ) {
			$ops = UserOperations::get_instance();
			$ob = $ops->LoadUserByUsername($a);
			if( is_object($a) && is_a($a,'User') ) return $a->id;
		}
		if( is_object($a) && is_a($a,'User') ) return $a->id;
		throw new \CmsLogicException('Could not resolve '.$a.' to a user id');
	}

	/**
	 * Set the admin user accounts (other than the owner) that are allowed to edit this template object
	 *
	 * @throws CmsInvalidDataException
	 * @param mixed $a Accepts an array of strings (usernames) or an array of integers (user ids, and negative group ids)
	 */
	public function set_additional_editors($a)
	{
		if( !is_array($a) ) {
			if( is_string($a) || (is_numeric($a) && $a > 0) ) {
				// maybe a single value...
				$res = self::_resolve_user($a);
				$this->_addt_editors = array($res);
				$this->_dirty = TRUE;
			}
		}
		else {
			$tmp = array();
			for( $i = 0; $i < count($a); $i++ ) {
                if( !is_numeric($a[$i]) ) continue;
                $tmp2 = (int)$a[$i];  // intentional cast to int, may have negative values.
				if( $tmp2 != 0 ) {
					$tmp[] = $tmp2;
				}
				else if( is_string($a[$i]) ) {
					$tmp[] = self::_resolve_user($a[$i]);
				}
			}
			$this->_addt_editors = $tmp;
			$this->_dirty = TRUE;
		}
	}

	/**
	 * Test wether the user specified has edit ability for this template object
	 *
	 * @param mixed $a Either a username (string) or an integer user id.
	 * @return bool
	 */
	public function can_edit($a)
	{
		$res = self::_resolve_user($a);
		if( $res == $this->get_owner_id() ) return TRUE;
		if( is_array($this->_addt_editors) && count($this->_addt_editors) && !in_array($res,$this->_addt_editors) ) return TRUE;
		return FALSE;
	}

    /**
     * Get wether this template is listable in public template lists.
     *
     * @return bool
     * @since 2.1
     */
    public function get_listable()
    {
        return (bool) $this->_data['listable'];
    }

    /**
     * Get wether this template is listable in public template lists.
     *
     * @return bool
     * @since 2.1
     */
    public function is_listable()
    {
        return $this->get_listable();
    }

    /**
     * Get wether this template is listable in public template lists.
     *
     * @param bool $flag The value for the listable attribute.
     * @return bool
     * @since 2.1
     */
    public function set_listable($flag)
    {
        $this->_data['listable'] = cms_to_bool($flag);
    }

	/**
	 * Process the template through smarty
	 *
	 * @return string
	 */
	public function process()
	{
		$smarty = \Smarty_CMS::get_instance();
		return $smarty->fetch('cms_template:id='.$this->get_id());
	}

	/**
	 * Validate that the data is complete enough to save to the database
	 *
	 * @throws CmsInvalidDataException
	 */
	protected function validate()
	{
		if( !$this->get_name() ) throw new CmsInvalidDataException('Each template must have a name');
		if( endswith($this->get_name(),'.tpl') ) throw new CmsInvalidDataException('Invalid name for a database template');
        if( !CmsAdminUtils::is_valid_itemname($this->get_name()) ) {
			throw new CmsInvalidDataException('There are invalid characters in the template name');
		}

		if( !$this->get_content() ) throw new CmsInvalidDataException('Each template must have some content');
		if( $this->get_type_id() <= 0 ) throw new CmsInvalidDataException('Each template must be associated with a type');

		$db = CmsApp::get_instance()->GetDb();
		$tmp = null;
		if( $this->get_id() ) {
			// double check the name.
			$query = 'SELECT id FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name = ? AND id != ?';
			$tmp = $db->GetOne($query,array($this->get_name(),$this->get_id()));
		} else {
			// double check the name.
			$query = 'SELECT id FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name = ?';
			$tmp = $db->GetOne($query,array($this->get_name()));
		}
		if( $tmp ) {
			throw new CmsInvalidDataException('Template with the same name already exists.');
		}
	}

	/**
	 * @ignore
	 */
	protected function _update()
	{
		if( !$this->_dirty ) return;
		$this->validate();

		$query = 'UPDATE '.CMS_DB_PREFIX.self::TABLENAME.'
              SET name = ?, content = ?, description = ?, type_id = ?, type_dflt = ?, category_id = ?, owner_id = ?, listable = ?, modified = ?
              WHERE id = ?';
		$db = CmsApp::get_instance()->GetDb();
		$dbr = $db->Execute($query,
                            array($this->get_name(),$this->get_content(),$this->get_description(),
                                  $this->get_type_id(),$this->get_type_dflt(),$this->get_category_id(),
                                  $this->get_owner_id(),$this->get_listable(),time(),
                                  $this->get_id()));
		if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

		if( $this->get_type_dflt() ) {
			// if it's default for a type, unset default flag for all other records with this type
			$query = 'UPDATE '.CMS_DB_PREFIX.self::TABLENAME.' SET type_dflt = 0 WHERE type_id = ? AND type_dflt = 1 AND id != ?';
			$dbr = $db->Execute($query,array($this->get_type_id(),$this->get_id()));
			if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
		}

		$query = 'DELETE FROM '.CMS_DB_PREFIX.self::ADDUSERSTABLE.' WHERE tpl_id = ?';
		$dbr = $db->Execute($query,array($this->get_id()));
		if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

		$t = $this->get_additional_editors();
		if( is_array($t) && count($t) ) {
			$query = 'INSERT INTO '.CMS_DB_PREFIX.self::ADDUSERSTABLE.' (tpl_id,user_id) VALUES(?,?)';
			foreach( $t as $one ) {
				$dbr = $db->Execute($query,array($this->get_id(),(int)$one));
			}
		}

		$query = 'DELETE FROM '.CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE.' WHERE tpl_id = ?';
		$dbr = $db->Execute($query,array($this->get_id()));
		if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
		$t = $this->get_designs();
		if( is_array($t) && count($t) ) {
			$query = 'INSERT INTO '.CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE.' (tpl_id,design_id) VALUES(?,?)';
			foreach( $t as $one ) {
				$dbr = $db->Execute($query,array($this->get_id(),(int)$one));
			}
		}

		CmsTemplateCache::clear_cache();
		audit($this->get_id(),'CMSMS','Template '.$this->get_name().' Updated');
		$this->_dirty = FALSE;
	}

	/**
	 * @ignore
	 */
	protected function _insert()
	{
		if( !$this->_dirty ) return;
		$this->validate();

		// insert the record
		$query = 'INSERT INTO '.CMS_DB_PREFIX.self::TABLENAME.'
              (name,content,description,type_id,type_dflt,category_id,owner_id,
               listable,created,modified) VALUES (?,?,?,?,?,?,?,?,?,?)';
		$db = CmsApp::get_instance()->GetDb();
		$dbr = $db->Execute($query,
							array($this->get_name(),$this->get_content(),$this->get_description(),
								  $this->get_type_id(),$this->get_type_dflt(),$this->get_category_id(),
								  $this->get_owner_id(),$this->get_listable(),time(),time()));
		if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
		$this->_data['id'] = $db->Insert_ID();

		if( $this->get_type_dflt() ) {
			// if it's default for a type, unset default flag for all other records with this type
			$query = 'UPDATE '.CMS_DB_PREFIX.self::TABLENAME.' SET type_dflt = 0 WHERE type_id = ? AND type_dflt = 1 AND id != ?';
			$dbr = $db->Execute($query,array($this->get_type_id(),$this->get_id()));
			if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
		}

		$t = $this->get_additional_editors();
		if( is_array($t) && count($t) ) {
			$query = 'INSERT INTO '.CMS_DB_PREFIX.self::ADDUSERSTABLE.' (tpl_id,user_id) VALUES(?,?)';
			foreach( $t as $one ) {
				$dbr = $db->Execute($query,array($this->get_id(),(int)$one));
			}
		}

		$t = $this->get_designs();
		if( is_array($t) && count($t) ) {
			$query = 'INSERT INTO '.CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE.' (tpl_id,design_id) VALUES(?,?)';
			foreach( $t as $one ) {
				$dbr = $db->Execute($query,array($this->get_id(),(int)$one));
			}
		}

		$this->_dirty = FALSE;
		CmsTemplateCache::clear_cache();
		audit($this->get_id(),'CMSMS','Template '.$this->get_name().' Created');
	}

	/**
	 * Save this template object to the database
	 */
	public function save()
	{
		if( $this->get_id() ) {
            HookManager::do_hook('Core::EditTemplatePre', [ get_class($this) => &$this ] );
			$this->_update();
            HookManager::do_hook('Core::EditTemplatePost', [ get_class($this) => &$this ] );
			return;
		}
        HookManager::do_hook('Core::AddTemplatePre', [ get_class($this) => &$this ] );
		$this->_insert();
        HookManager::do_hook('Core::AddTemplatePost', [ get_class($this) => &$this ] );
	}

	/**
	 * Delete this template object from the database
	 */
	public function delete()
	{
        if( !$this->get_id() ) return;

        HookManager::do_hook('Core::DeleteTemplatePre', [ get_class($this) => &$this ] );
		$db = CmsApp::get_instance()->GetDb();
		$query = 'DELETE FROM '.CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE.' WHERE tpl_id = ?';
		$dbr = $db->Execute($query,array($this->get_id()));

		$query = 'DELETE FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id = ?';
		$dbr = $db->Execute($query,array($this->get_id()));

        @unlink($this->get_content_filename());

		CmsTemplateCache::clear_cache();
		audit($this->get_id(),'CMSMS','Template '.$this->get_name().' Deleted');
        HookManager::do_hook('Core::DeleteTemplatePost', [ get_class($this) => &$this ] );
		unset($this->_data['id']);
		$this->_dirty = TRUE;
	}

	/**
	 * @ignore
	 */
	private static function get_locks()
	{
		if( !self::$_lock_cache_loaded ) {
			$tmp = CmsLockOperations::get_locks('template');
			if( is_array($tmp) && count($tmp) ) {
				self::$_lock_cache = array();
				foreach( $tmp as $one ) {
					self::$_lock_cache[$one['oid']] = $one;
				}
			}
			self::$_lock_cache_loaded = TRUE;
		}
		return self::$_lock_cache;
	}

	/**
	 * Get any applicable lock for this template object
	 *
	 * @returns CmsLock
	 * @see CmsLock
	 */
	public function get_lock()
	{
		$locks = self::get_locks();
		if( isset($locks[$this->get_id()]) ) return $locks[$this->get_id()];
	}

	/**
	 * Test if this object currently has a lock
	 *
	 * @return bool
	 */
	public function locked()
	{
		$lock = $this->get_lock();
		if( is_object($lock) ) return TRUE;
		return FALSE;
	}

	/**
	 * Tests if any lock associated with this object is expired
	 *
	 * @return booln
	 */
	public function lock_expired()
	{
		$lock = $this->get_lock();
		if( !is_object($lock) ) return FALSE;
		return $lock->expired();
	}

	/**
	 * @ignore
	 */
	private static function &_load_from_data($row,$design_list = null)
	{
		$ob = new CmsLayoutTemplate();
		$ob->_data = $row;
        $fn = $ob->get_content_filename();
        if( is_file($fn) && is_readable($fn) ) {
            $ob->_data['content'] = file_get_contents($fn);
            $ob->_data['modified'] = filemtime($fn);
        }
		if( is_array($design_list) ) $ob->_design_assoc = $design_list;

		self::$_obj_cache[$ob->get_id()] = $ob;
		self::$_name_cache[$ob->get_name()] = $ob->get_id();
		return $ob;
	}

	/**
	 * Load a bulk list of templates
	 *
	 * @param int[] $list Array of integer template ids
	 * @param bool $deep Optionally load attached data.
	 * @return array Array of CmsLayoutTemplate objects
	 */
	public static function load_bulk($list,$deep = true)
	{
		if( !is_array($list) || count($list) == 0 ) return;

		$list2 = array();
		foreach( $list as $one ) {
            if( !is_numeric($one) ) continue;
			$one = (int)$one;
			if( $one < 1 ) continue;
			if( isset(self::$_obj_cache[$one]) ) continue;
			$list2[] = $one;
		}
		$list2 = array_unique($list2);

		if( count($list2) ) {
			// get the data and populate the cache.
			$db = CmsApp::get_instance()->GetDb();
			$designs_by_tpl = array();

			if( $deep ) {
				foreach( $list2 as $one ) {
					$designs_by_tpl[$one] = array();
				}
                $dquery = 'SELECT tpl_id,design_id FROM '.CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE.'
                   WHERE tpl_id IN ('.implode(',',$list2).') ORDER BY tpl_id';
				$designs_tmp1 = $db->GetArray($dquery);
				foreach( $designs_tmp1 as $row ) {
					$designs_by_tpl[$row['tpl_id']][] = $row['design_id'];
				}
			}

			$query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id IN ('.implode(',',$list2).')';
			$dbr = $db->GetArray($query);
			if( is_array($dbr) && count($dbr) ) {
				foreach( $dbr as $row ) {
					self::_load_from_data($row,(isset($designs_by_tpl[$row['id']]))?$designs_by_tpl[$row['id']]:null);
				}
			}
		}

		// pull what we can from the cache
		$out = array();
		foreach( $list as $one ) {
            if( !is_numeric($one) ) continue;
			$one = (int)$one;
            if( $one < 1 ) continue;
			if( isset(self::$_obj_cache[$one]) ) $out[] = self::$_obj_cache[$one];
		}
		return $out;
	}

	/**
	 * Load a specific template
	 *
     * @throws CmsDataNotFoundException
	 * @param mixed $a Either an integer template id, or a template name (string)
	 * @return CmsLayoutTemplate
	 */
	public static function &load($a)
	{
		static $_nn = 0;

		$db = CmsApp::get_instance()->GetDb();
		$row = null;
        if( is_numeric($a) && $a > 0 ) {
			if( isset(self::$_obj_cache[$a]) ) return self::$_obj_cache[$a];
			$query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id = ?';
			$row = $db->GetRow($query,array((int)$a));
		}
		else if( is_string($a) && strlen($a) > 0 ) {
			if( isset(self::$_name_cache[$a]) ) {
				$n = self::$_name_cache[$a];
				return self::$_obj_cache[$n];
			}

			$query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name = ?';
			$row = $db->GetRow($query,array($a));
		}
		if( !is_array($row) || count($row) == 0 ) throw new CmsDataNotFoundException('Could not find template identified by '.$a);

		return self::_load_from_data($row);
	}

	/**
	 * Get a list of the templates owned by a specific user
	 *
     * @throws CmsInvalidDataException
	 * @param mixed $a An integer user id, or a string user name
	 * @return array Array of integer template ids
	 */
	public static function get_owned_templates($a)
	{
		$n = self::_resolve_user($a);
		if( $n <= 0 ) throw new CmsInvalidDataException('Invalid user specified to get_owned_templates');

		$query = CmsLayoutTemplateQuery(array('u'=>$n));
		$tmp = $query->GetMatchedTemplateIds();
		return self::load_bulk($tmp);
	}

	/**
	 * Perform an advanced query on templates
	 *
	 * @see CmsLayoutTemplateQuery
	 * @param array $params
	 */
	public static function template_query($params)
	{
		$query = new CmsLayoutTemplateQuery($params);
		$out = self::load_bulk($query->GetMatchedTemplateIds());

		if( isset($params['as_list']) && count($out) ) {
			$tmp2 = array();
			foreach( $out as $one ) {
				$tmp2[$one->get_id()] = $one->get_name();
			}
			return $tmp2;
		}
        return $out;
	}

	/**
	 * Get a list of the templates that a specific user can edit
	 *
     * @throws CmsInvalidDataException
	 * @param mixed $a An integer userid or a string username
	 */
	public static function get_editable_templates($a)
	{
		$n = self::_resolve_user($a);
		if( $n <= 0 ) throw new CmsInvalidDataException('Invalid user specified to get_owned_templates');

		$db = CmsApp::get_instance()->GetDb();
		$query = 'SELECT id FROM '.CMS_DB_PREFIX.self::TABLENAME;
		$parms = array();
		if( !UserOperations::get_instance()->CheckPermission($n,'Modify Templates') ) {
			$query .= ' WHERE owner_id = ?';
			$parms[] = $n;
		}
		$tmp1 = $db->GetCol($query,$parms);

		$query = 'SELECT tpl_id FROM '.CMS_DB_PREFIX.self::ADDUSERSTABLE.' WHERE user_id = ?';
		$tmp2 = $db->GetCol($query,array($n));

		if( is_array($tmp1) && is_array($tmp2) ) {
			$tmp = array_merge($tmp1,$tmp2);
		} else if( is_array($tmp1) ) {
			$tmp = $tmp1;
		} else if( is_array($tmp2) ) {
			$tmp = $tmp2;
		}

		if( is_array($tmp) && count($tmp) ) {
			$tmp = array_unique($tmp);
			if( is_array($tmp) && count($tmp) ) return self::load_bulk($tmp);
		}
	}

	/**
	 * Test if the user specified can edit the specified template
	 * This is a convenience method that loads the template, and then tests if the specified user has edit ability to it.
	 *
	 * @param mixed $tpl An integer template id, or a string template name
	 * @param mixed $userid An integer user id, or a string user name.  If no userid is specified the currently logged in userid is used
	 * @return bool
	 * @see CmsLayoutTemplate::load()
	 */
	public static function user_can_edit($tpl,$userid = null)
	{
		if( is_null($userid) ) $userid = get_userid();

		// get the template
		// scan owner/additional uers
		$obj = self::load($tpl);
		if( $obj->get_owner_id() == $userid ) return TRUE;

		// get the user groups
		$addt_users = $obj->get_additional_editors();
		if( is_array($addt_users) && count($addt_users) ) {
			if( in_array($userid,$addt_users) ) return TRUE;

			$grouplist = UserOperations::get_instance()->GetMemberGroups();
			if( is_array($grouplist) && count($grouplist) ) {
				foreach( $addt_users as $one ) {
					if( $one < 0 && in_array($one*-1,$grouplist) ) return TRUE;
				}
			}
		}

		return FALSE;
	}

	/**
	 * Create a new template of the specific type
	 *
	 * @throws CmsInvalidDataException
	 * @param mixed $t A CmsLayoutTemplateType object, An integer template type id, or a string template type identifier
	 * @return CmsLayoutTemplate
	 */
	public static function &create_by_type($t)
	{
		$t2 = null;
		if( is_int($t) || is_string($t) ) {
			$t2 = CmsLayoutTemplateType::load($t);
		}
		else if( is_object($t) && is_a($t,'CmsLayoutTemplateType') ) {
			$t2 = $t;
		}

		if( !$t2 ) throw new CmsInvalidDataException('Invalid data passed to CmsLayoutTemplate::create_by_type()');

        return $t2->create_new_template();
	}

	/**
	 * Load the default template of a specified type
	 *
	 * @throws CmsInvalidDataException
	 * @throws CmsDataNotFoundException
	 * @param mixed $t A CmsLayoutTemplateType object, An integer template type id, or a string template type identifier
	 * @return CmsLayoutTemplate
	 */
	public static function load_dflt_by_type($t)
	{
		$t2 = null;
		if( is_int($t) || is_string($t) ) {
			$t2 = CmsLayoutTemplateType::load($t);
		}
		else if( is_object($t) && is_a($t,'CmsLayoutTemplateType') ) {
			$t2 = $t;
		}

		if( !$t2 ) throw new CmsInvalidDataException('Invalid data passed to CmsLayoutTemplate::;load_dflt_by_type()');

		// search our preloaded template first
		if( is_array(self::$_obj_cache) && count(self::$_obj_cache) ) {
			foreach( self::$_obj_cache as $tpl ) {
				if( $tpl->get_type_id() == $t2->get_id() && $tpl->get_type_dflt() ) return $tpl;
			}
		}

		$db = CmsApp::get_instance()->GetDb();
		$query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE type_id = ? AND type_dflt = ?';
		$tmp = $db->GetRow($query,array($t2->get_id(),1));
		if( !is_array($tmp) || count($tmp) == 0 ) throw new CmsDataNotFoundException('Could not find default CmsLayoutTemplate row for type '.$t2->get_name());

		return self::_load_from_data($tmp);
	}

	/**
	 * Load all templates of a specific type
	 *
	 * @throws CmsDataNotFoundException
	 * @param CmsLayoutTemplateType $type
	 * @return array Array of CmsLayoutTemplate objects
	 */
	public static function load_all_by_type(CmsLayoutTemplateType $type)
	{
		$db = CmsApp::get_instance()->GetDb();
		$query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE type_id = ?';
		$tmp = $db->GetArray($query,array($type->get_id()));
		if( !is_array($tmp) || count($tmp) == 0 ) return;

		$out = array();
		foreach( $tmp as $row ) {
			$out[] = self::_load_from_data($row);
		}
		return $out;
	}

	/**
	 * Process a template through smarty given the template name
	 * @param string $name
	 * @return string
	 */
	public static function process_by_name($name)
	{
		$smarty = \Smarty_CMS::get_instance();
		return $smarty->fetch('cms_template:name='.$this->get_name());
	}

	/**
	 * Process the default template of a specified type
	 *
	 * @param mixed $t A CmsLayoutTemplateType object, An integer template type id, or a string template type identifier
	 * @return string
	 */
	public static function process_dflt($t)
	{
		$tpl = self::load_dflt_by_type($t);
		return $smarty->fetch('cms_template:id='.$tpl->get_id());
	}

	/**
	 * Get the ids of all loaded templates
	 *
	 * @return array Array of integer template ids
	 */
	public static function get_loaded_templates()
	{
		if( self::$_obj_cache && count(self::$_obj_cache) ) return array_keys(self::$_obj_cache);
	}

	/**
	 * Generate a unique name for a template
	 *
	 * @throws CmsInvalidDataException
     * @throws CmsLogicException
	 * @param string $prototype A prototype template name
	 * @param string $prefix An optional name prefix.
	 */
	public static function generate_unique_name($prototype,$prefix = null)
	{
		if( !$prototype ) throw new CmsInvalidDataException('Prototype name cannot be empty');
		$db = CmsApp::get_instance()->GetDb();
		$query = 'SELECT id FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name = ?';
		for( $i = 0; $i < 25; $i++ ) {
			$name = $prefix.$prototype;
            if( $i == 0 ) $name = $prototype;
			if( $i > 1 ) $name = $prefix.$prototype.' '.$i;
			$tmp = $db->GetOne($query,array($name));
			if( !$tmp ) return $name;
		}
		throw new CmsLogicException('Could not generate a template name for '.$prototype);
	}

    /**
     * Return the template type object for this template.
     *
     * @return CmsLayoutTemplateType
     * @since 2.2
     */
    public function &get_type()
    {
        $obj = null;
        $tid = $this->get_type_id();
        if( $tid > 0 ) $obj = \CmsLayoutTemplateType::load($this->get_type_id());
        return $obj;
    }

    /**
     * Get the usage string (if any) for this template.
     *
     * @return string A sample usage string for this template.
     * @since 2.2
     */
    public function get_usage_string()
    {
        $type = $this->get_type();
        return $type->get_usage_string($this->get_name());
    }

    /**
     * Get the filename that will be used to read template contents from file.
     *
     * @since 2.2
     * @return string
     */
    public function get_content_filename()
    {
        $config = \cms_config::get_instance();
        $name = munge_string_to_url($this->get_name()).'.'.$this->get_id().'.tpl';
        return cms_join_path($config['assets_path'],'templates',$name);
    }

    /**
     * Does this template have an associated file.
     *
     * @since 2.2
     * @return bool
     */
    public function has_content_file()
    {
        $fn = $this->get_content_filename();
        if( is_file($fn) && is_readable($fn) ) return TRUE;
    }
} // end of class

#
# EOF
#
