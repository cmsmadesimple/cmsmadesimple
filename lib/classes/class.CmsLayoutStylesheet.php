<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (ted@cmsmadesimple.org)
#(c)2013-2016 by The CMSMS Dev Team
#Visit our homepage at: http://cmsmadesimple.org
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
#$Id: class.global.inc.php 6939 2011-03-06 00:12:54Z calguy1000 $

/**
 * Contains classes and utilities for working with the CmsLayoutStylesheet stuff
 * @package CMS
 * @license GPL
 */

/**
 * A class to represent a stylesheet.
 *
 * This class is capable of managing a single stylesheet, and has static methods for loading stylesheets from the database.
 * Loaded stylesheets are cached in internal memory to ensure that the same stylesheet is not loaded twice for a request.
 *
 * Stylesheets are (optionally) attached to designs (CmsLayoutCollection)
 * see the {cms_stylesheet} plugin for more information.
 *
 * @package CMS
 * @license GPL
 * @since 2.0
 * @author Robert Campbell <calguy1000@gmail.com>
 * @see CmsLayoutCollection
 */
class CmsLayoutStylesheet
{
	/**
	 * @ignore
	 */
    const TABLENAME = 'layout_stylesheets';

	/**
	 * @ignore
	 */
    private $_dirty;

	/**
	 * @ignore
	 */
    private $_data = array();

	/**
	 * @ignore
	 */
    private $_design_assoc;

	/**
	 * @ignore
	 */
	private static $_name_cache = array();

	/**
	 * @ignore
	 */
	private static $_css_cache = array();

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
		$this->_dirty = TRUE;
	}

	/**
	 * Get the unique id of this stylesheet
	 * will return null if this stylesheet has not yet been saved to the database.
	 *
	 * @return int
	 */
    public function get_id()
    {
        if( isset($this->_data['id']) ) return $this->_data['id'];
    }

	/**
	 * Get the name of this stylesheet
	 *
	 * @return string
	 */
    public function get_name()
    {
        if( isset($this->_data['name']) ) return $this->_data['name'];
    }

	/**
	 * Set the unique name of this stylesheet
	 * Stylesheet names must be unique throughout the system.
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
	 * Get the content of this stylesheet object
	 *
	 * @return string
	 */
    public function get_content()
    {
        if( isset($this->_data['content']) ) return $this->_data['content'];
    }

	/**
	 * Set the CSS content of this stylesheet object
	 *
     * @throws CmsInvalidDataException
	 * @param string $str
	 */
    public function set_content($str)
    {
        $str = trim($str);
        if( !$str ) throw new CmsInvalidDataException('Template content cannot be empty');
        $this->_data['content'] = $str;
        $this->_dirty = TRUE;
    }

	/**
	 * Get the description of this stylesheet object
	 *
	 * @return string
	 */
    public function get_description()
    {
        if( isset($this->_data['description']) ) return $this->_data['description'];
    }

	/**
	 * Set the description of this stylesheet object
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
	 * Get the media types associated with this stylesheet
	 * media types are used with the \@media css rule
	 *
	 * @deprecated
	 * @return array
	 * @see http://www.w3schools.com/css/css_mediatypes.asp
	 */
    public function get_media_types()
    {
		// returns an array...
        if( isset($this->_data['media_type']) ) return $this->_data['media_type'];
    }

	/**
	 * Test if this stylesheet object has the specified media type
	 * media types are used with the \@media css rule
	 *
	 * @depcreated
	 * @param string $str The media type name
	 * @return bool
	 */
	public function has_media_type($str)
	{
        $str = trim($str);
		if( $str && isset($this->_data['media_type']) ) {
			if( in_array($str,$this->_data['media_type']) ) return TRUE;
		}
		return FALSE;
	}

	/**
	 * Add the specified media type to the list of media types for this stylesheet object
	 * media types are used with the \@media css rule
	 *
	 * @depcreated
	 * @param string $str The media type name
	 * @return bool
	 */
	public function add_media_type($str)
	{
        $str = trim((string) $str);
        if( !$str ) return;
		if( !is_array($this->_data['media_type']) ) $this->_data['media_type'] = array();
		$this->_data['media_type'][] = $str;
		$this->_dirty = TRUE;
	}

	/**
	 * Absolutely set the list of media types for this stylesheet object
	 * media types are used with the \@media css rule
	 *
	 * @deprecated
	 * @param mixed $arr Either a string, or an array of strings.
	 */
    public function set_media_types($arr)
    {
		if( !is_array($arr) ) {
			if( !is_numeric($arr) && $arr && is_string($arr) ) {
				$arr = array($arr);
			}
			else {
				return;
			}
		}

        $this->_data['media_type'] = $arr;
        $this->_dirty = TRUE;
    }

	/**
	 * Get the media query associated with this stylesheet
	 *
	 * @see http://en.wikipedia.org/wiki/Media_queries
	 * @return string
	 */
    public function get_media_query()
    {
        if( isset($this->_data['media_query']) ) return $this->_data['media_query'];
    }

	/**
	 * Set the media query associated with this stylesheet
	 *
	 * @see http://en.wikipedia.org/wiki/Media_queries
	 * @param string $str
	 */
    public function set_media_query($str)
    {
        $str = trim($str);
        $this->_data['media_query'] = $str;
        $this->_dirty = TRUE;
    }

	/**
	 * Get the data that this stylesheet object was first saved to the database
	 *
	 * @return int
	 */
    public function get_created()
    {
        if( isset($this->_data['created']) ) return $this->_data['created'];
    }

	/**
	 * Get the data that this stylesheet object was last saved to the database
	 *
	 * @return int
	 */
    public function get_modified()
    {
        if( isset($this->_data['modified']) ) return $this->_data['modified'];
    }


	/**
	 * Get the list of design id's (if any) that this stylesheet is associated with
	 *
	 * @see CmsLayoutCollection
	 * @return array Array of integer design ids
	 */
    public function get_designs()
    {
        if( !is_array($this->_design_assoc) ) {
            if( !$this->get_id() ) return;
            $this->_design_assoc = null;
            $db = CmsApp::get_instance()->GetDb();
            $query = 'SELECT design_id FROM '.CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE.' WHERE css_id = ?';
            $tmp = $db->GetCol($query,array($this->get_id()));
            if( is_array($tmp) && count($tmp) ) $this->_design_assoc = $tmp;
        }
        return $this->_design_assoc;
    }

	/**
	 * Set the list of design id's that this stylesheet is associated with
	 *
	 * @see CmsLayoutCollection
	 * @throws CmsInvalidDataException
	 * @param array $x Array of integer design ids
	 */
    public function set_designs($x)
    {
        if( !is_array($x) ) return;

        foreach( $x as $y ) {
            if( !is_numeric($y) ) throw new CmsInvalidDataException('Invalid data in design list.  Expect array of integers');
        }

        $this->_design_assoc = $x;
        $this->_dirty = TRUE;
    }

	/**
	 * Add a design association for this stylesheet object
	 *
	 * @see CmsLayoutCollection
	 * @throws CmsLogicException
	 * @param mixed $a An Instance of a CmsLayoutCollection object, or an integer design id, or a string design name
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
        else if( (is_string($a) && strlen($a)) ) {
            $design = CmsLayoutCollection::load($a);
            $n = $design->get_id();
        }
        else {
            throw new \CmsLogicException('Invalid data passed to '.__METHOD__);
        }

        // note: should load designs before adding.
        $designs = $this->get_designs();
        if( !in_array($n,$designs) ) {
            $this->_design_assoc[] = (int) $n;
            $this->_dirty = TRUE;
        }
    }

	/**
	 * Remove a design from the association list.
	 *
     * @throws CmsLogicException
	 * @see CmsLayoutCollection
	 * @param mixed $a An Instance of a CmsLayoutCollection object, or an integer design id, or a string design name
	 */
    public function remove_design($a)
    {
        // note: should load designs here before removing.
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
	 * Validate this stylesheet object for suitability for saving to the database
	 * Stylesheet objects must have a valid name (only certain characters accepted, and must have at least some css content)
	 *
	 * @throws CmsInvalidDataException
	 */
    protected function validate()
    {
        if( !$this->get_name() ) throw new CmsInvalidDataException('Each stylesheet must have a name');
        if( !$this->get_content() ) throw new CmsInvalidDataException('Each stylesheet must have some content');
		if( endswith($this->get_name(),'.css') ) throw new CmsInvalidDataException('Invalid name for a database stylesheet');
        if( !CmsAdminUtils::is_valid_itemname($this->get_name()) ) {
			throw new CmsInvalidDataException('There are invalid characters in the stylesheet name.');
		}

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
            throw new CmsInvalidDataException('Stylesheet with the same name already exists.');
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
              SET name = ?, content = ?, description = ?, media_type = ?, media_query = ?, modified = ?
              WHERE id = ?';
        $tmp = '';
        if( isset($this->_data['media_type']) ) $tmp = implode(',',$this->_data['media_type']);
        $db = CmsApp::get_instance()->GetDb();
        $dbr = $db->Execute($query,array($this->get_name(),$this->get_content(),$this->get_description(),
                                         $tmp,$this->get_media_query(),time(), $this->get_id()));
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

        // get the designs that have this stylesheet from the database again.
        $query = 'SELECT design_id FROM '.CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE.' WhERE css_id = ?';
        $design_list = $db->GetCol($query,array($this->get_id()));
        if( !is_array($design_list) ) $design_list = array();

        // cross reference design_list with $dl ... find designs in this object that aren't already known.
        $dl = $this->get_designs();
        $new_dl = array();
        $del_dl = array();
        foreach( $dl as $one ) {
            if( !in_array($one,$design_list) ) $new_dl[] = $one;
        }
        foreach( $design_list as $one ) {
            if( !in_array($one,$dl) ) $del_dl[] = $one;
        }

        if( is_array($del_dl) && count($del_dl) ) {
            // delete deleted items
            $query1 = 'SELECT item_order FROM '.CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE.' WHERE css_id = ? AND design_id = ?';
            $query2 = 'UPDATE '.CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE.' SET item_order = item_order - 1 WHERE design_id = ? AND item_order > ?';
            $query3 = 'DELETE FROM '.CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE.' WHERE design_id = ? AND css_id = ?';
            foreach( $del_dl as $design_id ) {
                $design_id = (int)$design_id;
                $item_order = (int)$db->GetOne($query1,array($this->get_id(),$design_id));
                $dbr = $db->Execute($query2,array($design_id,$item_order));
                if( !$dbr ) dir($db->sql.' '.$db->ErrorMsg());
                $dbr = $db->Execute($query3,array($design_id,$this->get_id()));
                if( !$dbr ) dir($db->sql.' '.$db->ErrorMsg());
            }
        }

        if( is_array($new_dl) && count($new_dl) ) {
            // add new items
            $query1 = 'SELECT MAX(item_order) FROM '.CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE.' WHERE design_id = ?';
            $query2 = 'INSERT INTO '.CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE.' (css_id,design_id,item_order) VALUES(?,?,?)';
            foreach( $new_dl as $one ) {
                $one = (int)$one;
                $num = (int)$db->GetOne($query1,array($one))+1;
                $dbr = $db->Execute($query2,array($this->get_id(),$one,$num));
                if( !$dbr ) die($db->sql.' -- '.$db->ErrorMsg());
            }
        }

        CmsTemplateCache::clear_cache();
        audit($this->get_id(),'CMSMS','Stylesheet '.$this->get_name().' Updated');
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
		$tmp = '';
		if( isset($this->_data['media_type']) ) $tmp = implode(',',$this->_data['media_type']);
        $query = 'INSERT INTO '.CMS_DB_PREFIX.self::TABLENAME.' (name,content,description,media_type,media_query, created,modified)
              VALUES (?,?,?,?,?,?,?)';
        $db = CmsApp::get_instance()->GetDb();
        $dbr = $db->Execute($query,	array($this->get_name(),$this->get_content(),$this->get_description(),
                                          $tmp,$this->get_media_query(), time(),time()));
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        $this->_data['id'] = $db->Insert_ID();

        $t = $this->get_designs();
        if( is_array($t) && count($t) ) {
            $query = 'INSERT INTO '.CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE.' (css_id,design_id,item_order) VALUES(?,?,?)';
            $item_order = 1;
            foreach( $t as $one ) {
				$dbr = $db->Execute($query,[ $this->get_id(),(int)$one,$item_order++ ] );
            }
        }

        $this->_dirty = FALSE;
        CmsTemplateCache::clear_cache();
        audit($this->get_id(),'CMSMS','Stylesheet '.$this->get_name().' Created');
    }

	/**
	 * Save this stylesheet to the database
	 * Objects are only saved if they are dirty (have been modified in some way, or have no id)
	 *
	 * This method sends events before and after saving.
	 * EditStylesheetPre is sent before an existing stylesheet is saved to the database
	 * EditStylesheetPost is sent after an existing stylesheet is saved to the database
	 * AddStylesheetPre is sent before a new stylesheet is saved to the database
	 * AddStylesheetPost is sent after a new stylesheet is saved to the database
	 *
	 * @throws CmsSQLErrorException
	 */
    public function save()
    {
        if( $this->get_id() ) {
            \CMSMS\HookManager::do_hook('Core::EditStylesheetPre',array(get_class($this)=>&$this));
            $this->_update();
            \CMSMS\HookManager::do_hook('Core::EditStylesheetPost',array(get_class($this)=>&$this));
            return;
        }
        \CMSMS\HookManager::do_hook('Core::AddStylesheetPre',array(get_class($this)=>&$this));
        $this->_insert();
        \CMSMS\HookManager::do_hook('Core::AddStylesheetPost',array(get_class($this)=>&$this));
    }

	/**
	 * Delete this stylesheet object from the database
	 * This method deletes the appropriate records from the databas,
	 * deletes the id from this object, and marks the object as dirty so that it can be saved again
	 *
	 * This method triggers the DeleteStylesheetPre and DeleteStylesheetPost events
	 */
    public function delete()
    {
        if( !$this->get_id() ) return;

        \CMSMS\HookManager::do_hook('Core::DeleteStylesheetPre',array(get_class($this)=>&$this));
        $db = CmsApp::get_instance()->GetDb();
        $query = 'DELETE FROM '.CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE.' WHERE css_id = ?';
        $dbr = $db->Execute($query,array($this->get_id()));

        $query = 'DELETE FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id = ?';
        $dbr = $db->Execute($query,array($this->get_id()));

        @unlink($this->get_content_filename());

        CmsTemplateCache::clear_cache();
        audit($this->get_id(),'CMSMS','Stylesheet '.$this->get_name().' Deleted');
		// Events::SendEvent('Core','DeleteStylesheetPost',array(get_class($this)=>&$this));
        \CMSMS\HookManager::do_hook('Core::DeleteStylesheetPost',array(get_class($this)=>&$this));
        unset($this->_data['id']);
        $this->_dirty = TRUE;
    }

	/**
	 * @ignore
	 */
	private static function get_locks()
	{
		if( !self::$_lock_cache_loaded ) {
			$tmp = CmsLockOperations::get_locks('stylesheet');
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
	 * Get a lock (if any exist) for this object
	 *
	 * @return CmsLock
	 */
	public function get_lock()
	{
		$locks = self::get_locks();
		if( isset($locks[$this->get_id()]) ) return $locks[$this->get_id()];
	}

	/**
	 * Test if the current object is locked.
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
	 * Test if the current stylesheet object is locked by an expired lock.
	 * If the object is not locked false is returned
	 *
	 * @return bool
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
        $ob = new CmsLayoutStylesheet();
        $row['media_type'] = explode(',',$row['media_type']);;
        $ob->_data = $row;
        $fn = $ob->get_content_filename();
        if( is_file($fn) && is_readable($fn) ) {
            $ob->_data['content'] = file_get_contents($fn);
            $ob->_data['modified'] = filemtime($fn);
        }
        if( is_array($design_list) ) $ob->_design_assoc = $design_list;

        self::$_css_cache[$row['id']] = $ob;
        self::$_name_cache[$row['name']] = $row['id'];
        return $ob;
    }

	/**
	 * Load the specified stylesheet object
	 *
	 * @param mixed $a Either an integer stylesheet id, or a string stylesheet name.
	 * @return CmsLayoutStylesheet
	 * @throws CmsInvalidDataException
	 */
    public static function &load($a)
    {
		// check the cache first..
        $db = CmsApp::get_instance()->GetDb();
        $row = null;
        if( is_numeric($a) && (int)$a > 0 ) {
			$a = (int)$a;
			if( isset(self::$_css_cache[$a]) ) return self::$_css_cache[$a];
			// not in cache
            $query = 'SELECT id,name,content,description,media_type,media_query,created,modified FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id = ?';
            $row = $db->GetRow($query,array($a));
        }
        else if( is_string($a) && strlen($a) > 0 ) {
			if( isset(self::$_name_cache[$a]) ) {
				$b = (int)self::$_name_cache[$a];
				if( isset(self::$_css_cache[$b]) ) return self::$_css_cache[$b];
			}
			// not in cache
            $query = 'SELECT id,name,content,description,media_type,media_query,created,modified FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name = ?';
            $row = $db->GetRow($query,array($a));
        }
        if( !is_array($row) || count($row) == 0 ) throw new \CmsInvalidDataException('Could not find stylesheet identified by '.$a);

        return self::_load_from_data($row);
    }

	/**
	 * Load multiple stylesheets in an optimized fashion
	 *
     * This method does not throw exceptions if one requested id, or name does not exist.
     *
	 * @param array $ids Array of integer stylesheet ids or an array of string stylesheet names.
	 * @param bool $deep wether or not to load associated data
	 * @return array Array of CmsLayoutStylesheet objects
	 * @throws CmsInvalidDataException
	 */
	public static function load_bulk($ids,$deep = true)
	{
		if( !is_array($ids) || count($ids) == 0 ) return;

        // clean up the input data
		$is_ints = FALSE;
		if( is_numeric($ids[0]) && (int)$ids[0] > 0 ) {
			$is_ints = TRUE;
			for( $i = 0, $n = count($ids); $i < $n; $i++ ) {
				$ids[$i] = (int)$ids[$i];
			}
		}
		else if( is_string($ids[0]) && strlen($ids[0]) > 0 ) {
			for( $i = 0, $n = count($ids); $i < $n; $i++ ) {
				$ids[$i] = "'".trim($ids[$i])."'";
			}
		}
		else {
            // what ??
			throw new CmsInvalidDataException('Invalid data passed to '.__CLASS__.'::'.__METHOD__);
		}
		$ids = array_unique($ids);

		$db = CmsApp::get_instance()->GetDb();
		$query = 'SELECT id,name,content,description,media_type,media_query,created,modified FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id IN ('.implode(',',$ids).')';
		if( !$is_ints ) $query = 'SELECT id,name,content,description,media_type,media_query,created,modified FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name IN ('.implode(',',$ids).')';

		$dbr = $db->GetArray($query);
		$out = array();
		if( is_array($dbr) && count($dbr) ) {
			$designs_by_css = array();
			if( $deep ) {
				$ids2 = array();
				foreach( $dbr as $row ) {
					$ids2[] = $row['id'];
					$designs_by_css[$row['id']] = array();
				}
				$dquery = 'SELECT design_id,css_id FROM '.CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE.' WHERE css_id IN ('.implode(',',$ids2).') ORDER BY css_id';
				$dbr2 = $db->GetArray($dquery);
				foreach( $dbr2 as $row ) {
					$designs_by_css[$row['css_id']][] = $row['design_id'];
				}
			}

            // this makes sure that the returned array matches the order specified.
            foreach( $ids as $one ) {
                $found = null;
                if( $is_ints ) {
                    // find item in $dbr by id
                    foreach( $dbr as $row ) {
                        if( $row['id'] == $one ) {
                            $found = $row;
                            break;
                        }
                    }
                }
                else {
                    $one = trim($one,"'");
                    // find item in $dbr by name
                    foreach( $dbr as $row ) {
                        if( $row['name'] == $one ) {
                            $found = $row;
                            break;
                        }
                    }
                }

                $id = $found['id'];
				$tmp = self::_load_from_data($found,(isset($designs_by_css[$id]))?$designs_by_css[$id]:null);
				if( is_object($tmp) ) $out[] = $tmp;
            }
		}

		if( count($out) ) return $out;
	}

	/**
	 * Load all stylesheet objects
	 *
	 * @param bool $as_list a flag indicating the output format
	 * @return mixed If $as_list is true then the output will be an associated array of stylesheet id and stylesheet name suitable for use in an html select element
	 *   otherwise, an array of CmsLayoutStylesheet objects is returned
	 */
    public static function get_all($as_list = FALSE)
    {
        $db = CmsApp::get_instance()->GetDb();

		$out = array();
		if( $as_list ) {
			$query = 'SELECT id,name FROM '.CMS_DB_PREFIX.self::TABLENAME.' ORDER BY modified DESC';
			$dbr = $db->GetArray($query);
			foreach( $dbr as $row ) {
				$out[$row['id']] = $row['name'];
			}
			return $out;
		}
		else {
			$query = 'SELECT id FROM '.CMS_DB_PREFIX.self::TABLENAME.' ORDER BY modified DESC';
			$ids = $db->GetCol($query);
			return self::load_bulk($ids,FALSE);
		}
    }


    /**
     * Test if the specific stylesheet (by name or id) is loaded
     *
     * @param mixed $id Either an integer stylesheet id, or a string stylesheet name
     * @return bool
     */
    public static function is_loaded($id)
    {
        if( is_numeric($id) && (int)$id > 0 ) {
            if( isset(self::$_css_cache[$id]) ) return TRUE;
        }
        else if( is_string($id) && strlen($id) > 0 ) {
            if( isset(self::$_name_cache[$id]) ) return TRUE;
        }
        return FALSE;
    }

	/**
	 * Generate a unique name for a stylesheet
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
     * Get the filename that will be used to read template contents from file.
     *
     * @since 2.2
     * @return string
     */
    public function get_content_filename()
    {
        $config = \cms_config::get_instance();
        $name = munge_string_to_url($this->get_name()).'.'.$this->get_id().'.css';
        return cms_join_path($config['assets_path'],'css',$name);
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
?>
