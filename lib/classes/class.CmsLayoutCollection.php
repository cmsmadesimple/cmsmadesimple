<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (ted@cmsmadesimple.org)
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
 * This class defines classes and utility functions for a Theme or Collection.
 * @package CMS
 * @license GPL
 */

use \CMSMS\HookManager;

/**
 * A class to manage a collection (or theme) of Templates and Stylesheets
 *
 * @package CMS
 * @license GPL
 * @since 2.0
 * @author Robert Campbell <calguy1000@gmail.com>
 */
class CmsLayoutCollection
{
	/**
	 * @ignore
	 */
	const TABLENAME = 'layout_designs';

	/**
	 * @ignore
	 */
	const CSSTABLE  = 'layout_design_cssassoc';

	/**
	 * @ignore
	 */
	const TPLTABLE  = 'layout_design_tplassoc';

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
    private $_css_assoc = array();

	/**
	 * @ignore
	 */
    private $_tpl_assoc = array();

	/**
	 * @ignore
	 */
    private static $_raw_cache;

	/**
	 * @ignore
	 */
	private static $_dflt_id;

	/**
	 * Get the theme id
	 * Only themes that have been saved to the database have an id.
	 * @return int
	 */
    public function get_id()
    {
        if( isset($this->_data['id']) ) return $this->_data['id'];
    }

	/**
	 * Get the theme name
	 * @return string
	 */
    public function get_name()
    {
        if( isset($this->_data['name']) ) return $this->_data['name'];
    }

	/**
	 * Set the theme name
	 * This marks the theme as dirty
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
	 * Get the default flag
	 * Note, only one theme can be the default.
	 *
	 * @return bool
	 */
    public function get_default()
    {
        if( isset($this->_data['dflt']) ) return $this->_data['dflt'];
    }


    /**
     * Sets this theme as the default theme.
     * Sets the dirty flag.
     * Note, only one theme can be the default.
     *
     * @param bool $flag
     */
    public function set_default($flag)
    {
        $flag = (bool)$flag;
        $this->_data['dflt'] = $flag;
        $this->_dirty = TRUE;
    }

	/**
	 * Get the theme description
	 *
	 * @return string
	 */
    public function get_description()
    {
        if( isset($this->_data['description']) ) return $this->_data['description'];
    }

	/**
	 * Set the theme description
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
	 * Get the creation date of this theme
	 * The creation date is specified automatically on the first save
	 *
	 * @return int
	 */
    public function get_created()
    {
        if( isset($this->_data['created']) ) return $this->_data['created'];
    }

	/**
	 * Get the date of the last modification of this theme
	 *
	 * @return int
	 */
    public function get_modified()
    {
        if( isset($this->_data['modified']) ) return $this->_data['modified'];
    }

	/**
	 * Test if this theme has stylesheets attached to it
	 *
	 * @return bool
	 */
	public function has_stylesheets()
	{
		if( is_array($this->_css_assoc) && count($this->_css_assoc) ) return TRUE;
		return FALSE;
	}

	/**
	 * Get the list of stylesheets (if any) associated with this theme.
	 *
	 * @return array of integers
	 */
    public function get_stylesheets()
    {
        return $this->_css_assoc;
    }

	/**
	 * Set the list of stylesheets associated with this theme
	 *
	 * @throws CmsLogicException
	 * @param array $id_array Array of integer stylesheet ids.
	 */
    public function set_stylesheets($id_array)
    {
        if( !is_array($id_array) ) return;

        foreach( $id_array as $one ) {
            if( !is_numeric($one) || $one < 1 ) throw new \CmsLogicException('CmsLayoutCollection::set_stylesheets expects an array of integers');
        }

        $this->_css_assoc = $id_array;
        $this->_dirty = TRUE;
    }

	/**
	 * Adds a stylesheet to the theme
	 *
	 * @throws \CmsLogicException
	 * @param mixed $css Either an integer stylesheet id, or a CmsLayoutStylesheet object
	 */
    public function add_stylesheet($css)
    {
        $css_t = null;
        if( is_object($css) && is_a($css,'CmsLayoutStylesheet') ) {
            $css_t = $css->get_id();
        }
        else if( is_numeric($css) && $css > 0 ) {
            $css_t = (int) $css;
        }
        if( $css_t < 1 ) throw new \CmsLogicException('Invalid css id specified to CmsLayoutCollection::add_stylesheet');

        if( !in_array($css_t,$this->_css_assoc) ) {
            $this->_css_assoc[] = (int) $css_t;
            $this->_dirty = TRUE;
        }
    }

	/**
	 * Delete a stylesheet from the list of stylesheets associated with this theme
	 *
	 * @throws CmsLogicException
	 * @param mixed $css Either an integer stylesheet id, or a CmsLayoutStylesheet object
	 */
    public function delete_stylesheet($css)
    {
        $css_t = null;
        if( is_object($css) && is_a($css,'CmsLayoutStylesheet') ) {
            $css_t = $css->id;
        }
        else if( is_numeric($css) ) {
            $css_t = (int) $css;
        }
        if( $css_t < 1 ) throw new \CmsLogicException('Invalid css id specified to CmsLayoutCollection::delete_stylesheet');

        if( !in_array($css_t,$this->_css_assoc) ) return;
        $t = array();
        foreach( $this->_css_assoc as $one ) {
            if( $css_t != $one ) {
				$t[] = $one;
			}
			else {
				// do we want to delete this css from the database?
			}
        }
        $this->_css_assoc = $t;
        $this->_dirty = TRUE;
    }

	/**
	 * Test if this theme has templates associated with it
	 *
	 * @return bool
	 */
    public function has_templates()
    {
        if( count($this->_tpl_assoc) == 0 ) return FALSE;
        return TRUE;
    }

	/**
	 * Return a list of the template ids associated with this template
	 *
	 * @return array of integers
	 */
    public function get_templates()
    {
        $out = null;
        if( !$this->get_id() ) return $out;
        if( !$this->has_templates() ) return $out;

        return $this->_tpl_assoc;
    }

	/**
	 * Set the list of templates associated with this theme
	 *
	 * @throws CmsLogicException
	 * @param array $id_array Array of integer template ids
	 */
    public function set_templates($id_array)
    {
        if( !is_array($id_array) ) return;

        foreach( $id_array as $one ) {
            if( !is_numeric($one) && $one < 1 ) throw new \CmsLogicException('CmsLayoutCollection::set_templates expects an array of integers');
        }

        $this->_tpl_assoc = $id_array;
        $this->_dirty = TRUE;
    }

	/**
	 * Add a template to the list of templates associated with this theme.
	 *
	 * @throws CmsLogicException
	 * @param mixed $tpl Accepts either an integer template id, or an instance of a CmsLayoutTemplate object
	 */
	public function add_template($tpl)
	{
        $tpl_id = null;
        if( is_object($tpl) && is_a($tpl,'CmsLayoutTemplate') ) {
            $tpl_id = $tpl->get_id();
        }
        else if( is_numeric($tpl) ) {
            $tpl_id = (int) $tpl;
        }
        if( $tpl_id < 1 ) throw new CmsLogicException('Invalid template id specified to CmsLayoutCollection::add_template');

		if( !is_array($this->_tpl_assoc) ) $this->_tpl_assoc = array();
		if( !in_array($tpl_id,$this->_tpl_assoc) ) $this->_tpl_assoc[] = (int) $tpl_id;
        $this->_dirty = TRUE;
	}

	/**
	 * Delete a template from the list of templates associated with this theme
	 *
	 * @throws CmsInvalidDataException
	 * @param mixed $tpl Either an integer template id, or a CmsLayoutTemplate object
	 */
    public function delete_template($tpl)
    {
        $tpl_id = null;
        if( is_object($tpl) && is_a($tpl,'CmsLayoutTemplate') ) {
            $tpl_id = $tpl->get_id();
        }
        else if( is_numeric($tpl) ) {
            $tpl_id = (int) $tpl;
        }
        if( $tpl_id <= 0 ) throw new CmsLogicException('Invalid template id specified to CmsLayoutCollection::add_template');

        if( !in_array($tpl_id,$this->_tpl_assoc) ) return;
        $t = array();
        foreach( $this->_tpl_assoc as $one ) {
            if( $tpl_id != $one ) {
				$t[] = $one;
			}
			else {
				// do we want to delete this css from the database?
			}
        }
        $this->_tpl_assoc = $t;
        $this->_dirty = TRUE;
    }

	/**
	 * Validate this object before saving.
	 *
	 * @throws CmsInvalidDataException
	 */
    protected function validate()
    {
        if( $this->get_name() == '' ) throw new CmsInvalidDataException('A Design must have a name');
        if( !CmsAdminUtils::is_valid_itemname($this->get_name()) ) {
			throw new CmsInvalidDataException('There are invalid characters in the design name.');
		}

        if( count($this->_css_assoc) ) {
            $t1 = array_unique($this->_css_assoc);
            if( count($t1) != count($this->_css_assoc) ) throw new CmsInvalidDataException('Duplicate CSS Ids exist in design.');
        }

        $db = CmsApp::get_instance()->GetDb();
        $tmp = null;
        if( $this->get_id() ) {
            $query = 'SELECT id FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name = ? AND id != ?';
            $tmp = $db->GetOne($query,array($this->get_name(),$this->get_id()));
        }
        else {
            $query = 'SELECT id FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name = ?';
            $tmp = $db->GetOne($query,array($this->get_name()));
        }
        if( $tmp ) throw new CmsInvalidDataException('Collection/Design with the same name already exists.');
    }

	/**
	 * @ignore
	 */
    private function _insert()
    {
        if( !$this->_dirty ) return;
        $this->validate();

        $db = CmsApp::get_instance()->GetDb();
        $query = 'INSERT INtO '.CMS_DB_PREFIX.self::TABLENAME.' (name,description,dflt,created,modified) VALUES (?,?,?,?,?)';
        $dbr = $db->Execute($query,array($this->get_name(), $this->get_description(), ($this->get_default())?1:0, time(),time()));
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

        $this->_data['id'] = $db->Insert_ID();

		if( $this->get_default() ) {
			$query = 'UPDATE '.CMS_DB_PREFIX.self::TABLENAME.' SET dflt = 0 WHERE id != ?';
			$dbr = $db->Execute($query,array($this->get_id()));
			if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
		}

        if( count($this->_css_assoc) ) {
            $query = 'INSERT INTO '.CMS_DB_PREFIX.self::CSSTABLE.' (design_id,css_id,item_order) VALUES (?,?,?)';
            for( $i = 0, $n = count($this->_css_assoc); $i < $n; $i++ ) {
				$css_id = $this->_css_assoc[$i];
				$dbr = $db->Execute($query,array($this->get_id(),$css_id,$i+1));
            }
        }
        if( count($this->_tpl_assoc) ) {
            $query = 'INSERT INTO '.CMS_DB_PREFIX.self::TPLTABLE.' (design_id,tpl_id) VALUES(?,?)';
            for( $i = 0, $n = count($this->_tpl_assoc); $i < $n; $i++ ) {
				$tpl_id = $this->_tpl_assoc[$i];
				$dbr = $db->Execute($query,array($this->get_id(),$tpl_id));
            }
        }

		$this->_dirty = FALSE;
		audit($this->get_id(),'CMSMS','Design '.$this->get_name().' created');
    }

	/**
	 * @ignore
	 */
    private function _update()
    {
        if( !$this->_dirty ) return;
        $this->validate();

        $db = CmsApp::get_instance()->GetDb();
        $query = 'UPDATE '.CMS_DB_PREFIX.self::TABLENAME.' SET name = ?, description = ?, dflt = ?, modified = ? WHERE id = ?';
        $dbr = $db->Execute($query,array($this->get_name(), $this->get_description(), ($this->get_default())?1:0, time(), $this->get_id()));
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

		if( $this->get_default() ) {
			$query = 'UPDATE '.CMS_DB_PREFIX.self::TABLENAME.' SET dflt = 0 WHERE id != ?';
			$dbr = $db->Execute($query,array($this->get_id()));
			if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
		}

        $query = 'DELETE FROM '.CMS_DB_PREFIX.self::CSSTABLE.' WHERE design_id = ?';
        $db->Execute($query,array($this->get_id()));

        if( count($this->_css_assoc) ) {
            $query = 'INSERT INTO '.CMS_DB_PREFIX.self::CSSTABLE.' (design_id,css_id,item_order) VALUES (?,?,?)';
            for( $i = 0, $n = count($this->_css_assoc); $i < $n; $i++ ) {
				$css_id = $this->_css_assoc[$i];
				$dbr = $db->Execute($query,array($this->get_id(),$css_id,$i+1));
            }
        }

        $query = 'DELETE FROM '.CMS_DB_PREFIX.self::TPLTABLE.' WHERE design_id = ?';
        $db->Execute($query,array($this->get_id()));

        if( count($this->_tpl_assoc) ) {
            $query = 'INSERT INTO '.CMS_DB_PREFIX.self::TPLTABLE.' (design_id,tpl_id) VALUES (?,?)';
            for( $i = 0, $n = count($this->_tpl_assoc); $i < $n; $i++ ) {
				$tpl_id = $this->_tpl_assoc[$i];
				$dbr = $db->Execute($query,array($this->get_id(),$tpl_id));
            }
        }

		$this->_dirty = FALSE;
		audit($this->get_id(),'CMSMS','Design '.$this->get_name().' updated');
    }

	/**
	 * Save this theme
	 * This method will send the AddDesignPre and AddDesignPost events before and after saving a new theme
	 * and the EditDesignPre and EditDesignPost events before and after saving an existing theme.
	 */
    public function save()
    {
        if( $this->get_id() ) {
            HookManager::do_hook('Core::EditDesignPre', [ get_class($this) => &$this ] );
            $this->_update();
            HookManager::do_hook('Core::EditDesignPost', [ get_class($this) => &$this ] );
			return;
        }
        HookManager::do_hook('Core::AddDesignPre', [ get_class($this) => &$this ] );
        $this->_insert();
        HookManager::do_hook('Core::AddDesignPost', [ get_class($this) => &$this ] );
    }

	/**
	 * Delete the current theme
	 * This class will not allow deleting themes that still have templates associated with them.
	 *
     * @throws CmsLogicException
	 * @param bool $force Force deleting the theme even if there are templates attached
	 */
    public function delete($force = FALSE)
    {
        if( !$this->get_id() ) return;

        if( !$force && $this->has_templates() ) throw new CmsLogicException('Cannot Delete a Design that has Templates Attached');

        HookManager::do_hook('Core::DeleteDesignPre', [ get_class($this) => &$this ] );
		$db = CmsApp::get_instance()->GetDb();
        if( count($this->_css_assoc) ) {
            $query = 'DELETE FROM '.CMS_DB_PREFIX.self::CSSTABLE.' WHERE design_id = ?';
            $dbr = $db->Execute($query,array($this->get_id()));
            $this->_css_assoc = array();
            $this->_dirty = TRUE;
        }

		if( count($this->_tpl_assoc) ) {
            $query = 'DELETE FROM '.CMS_DB_PREFIX.self::TPLTABLE.' WHERE design_id = ?';
            $dbr = $db->Execute($query,array($this->get_id()));
            $this->_tpl_assoc = array();
            $this->_dirty = TRUE;
		}

        $query = 'DELETE FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id = ?';
        $dbr = $db->Execute($query,array($this->get_id()));

		audit($this->get_id(),'CMSMS','Design '.$this->get_name().' deleted');
        HookManager::do_hook('Core::DeleteDesignPost', [ get_class($this) => &$this ] );
        unset($this->_data['id']);
        $this->_dirty = TRUE;
    }

	/**
	 * @ignore
	 */
    protected static function &_load_from_data($row)
    {
        $ob = new CmsLayoutCollection;
        $css = null;
        $tpls = null;
        if( isset($row['css']) ) {
            $css = $row['css'];
            unset($row['css']);
        }
        if( isset($row['templates']) ) {
            $tpls = $row['templates'];
            unset($row['templates']);
        }
        $ob->_data = $row;
        if( is_array($css) && count($css) ) $ob->_css_assoc = $css;
        if( is_array($tpls) && count($tpls) ) $ob->_tpl_assoc = $tpls;

        return $ob;
    }

	/**
	 * Load a theme object
	 *
     * @throws CmsDataNotFoundException
	 * @param mixed $x - Accepts either an integer theme id, or a theme name,
	 * @return CmsLayoutCollection
	 */
    public static function &load($x)
    {
        $db = CmsApp::get_instance()->GetDb();
        $row = null;
        if( is_numeric($x) && $x > 0 ) {
            if( is_array(self::$_raw_cache) && count(self::$_raw_cache) ) {
				if( isset(self::$_raw_cache[$x]) ) return self::_load_from_data(self::$_raw_cache[$x]);
            }
            $query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id = ?';
            $row = $db->GetRow($query,array((int)$x));
        }
        else if( is_string($x) && strlen($x) > 0 ) {
            if( is_array(self::$_raw_cache) && count(self::$_raw_cache) ) {
				foreach( self::$_raw_cache as $row ) {
					if( $row['name'] == $x ) return self::_load_from_data($row);
				}
            }

            $query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE name = ?';
            $row = $db->GetRow($query,array(trim($x)));
        }

        if( !is_array($row) || count($row) == 0 ) throw new CmsDataNotFoundException('Could not find design row identified by '.$x);

        // get attached css
        $query = 'SELECT css_id FROM '.CMS_DB_PREFIX.self::CSSTABLE.' WHERE design_id = ? ORDER BY item_order';
        $tmp = $db->GetCol($query,array((int) $row['id']));
        if( is_array($tmp) && count($tmp) ) $row['css'] = $tmp;

        // get attached templates
        $query = 'SELECT tpl_id FROM '.CMS_DB_PREFIX.self::TPLTABLE.' WHERE design_id = ?';
        $tmp = $db->GetCol($query,array((int) $row['id']));
        if( is_array($tmp) && count($tmp) ) $row['templates'] = $tmp;

        self::$_raw_cache[$row['id']] = $row;
        return self::_load_from_data($row);
    }

	/**
	 * Load all themes
	 *
	 * @param string $quick Do not load the templates and stylesheets.
	 * @return array Array of CmsLayoutCollection objects.
	 */
    public static function get_all($quick = FALSE)
    {
        $out = null;
        $query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' ORDER BY name ASC';
        $db = CmsApp::get_instance()->GetDb();
        $dbr = $db->GetArray($query);
        if( is_array($dbr) && count($dbr) ) {
            $ids = array();
            $cache = array();
            foreach( $dbr as $row ) {
				$ids[] = $row['id'];
                $cache[$row['id']] = $row;
            }

			if( !$quick ) {
				$query = 'SELECT * FROM '.CMS_DB_PREFIX.self::CSSTABLE.' WHERE design_id IN ('.implode(',',$ids).') ORDER BY design_id,item_order';
				$dbr2 = $db->GetArray($query);
				if( is_array($dbr2) && count($dbr2) ) {
					foreach( $dbr2 as $row ) {
						if( !isset($cache[$row['design_id']]) ) continue; // orphaned entry, bad.
						$design = &$cache[$row['design_id']];
						if( !isset($design['css']) ) $design['css'] = array();
						if( !in_array($row['css_id'],$design['css']) ) $design['css'][] = $row['css_id'];
					}
				}

				$query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TPLTABLE.' WHERE design_id IN ('.implode(',',$ids).') ORDER BY design_id';
				$dbr2 = $db->GetArray($query);
				if( is_array($dbr2) && count($dbr2) ) {
					foreach( $dbr2 as $row ) {
						if( !isset($cache[$row['design_id']]) ) continue; // orphaned entry, bad.
						$design = &$cache[$row['design_id']];
						if( !isset($design['templates']) ) $design['templates'] = array();
						$design['templates'][] = $row['tpl_id'];
					}
				}
			}

            self::$_raw_cache = $cache;

            $out = array();
            foreach( $cache as $key => $row ) {
				$out[] = self::_load_from_data($row);
            }
			return $out;
        }
    }

	/**
	 * Get a list of designs
	 *
	 * @param array Array of designs
	 */
	public static function get_list()
	{
		$designs = self::get_all(TRUE);
		if( is_array($designs) && count($designs) ) {
			$out = array();
			foreach( $designs as $one ) {
				$out[$one->get_id()] = $one->get_name();
			}
			return $out;
		}
	}

	/**
	 * Load the default theme
	 *
	 * @throws CmsInvalidDataException
	 * @return CmsLayoutCollection
	 */
	public static function &load_default()
	{
		$tmp = null;
		if( self::$_dflt_id == '' ) {
			$query = 'SELECT id FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE dflt = 1';
			$db = CmsApp::get_instance()->GetDb();
            $tmp = (int) $db->GetOne($query);
            if( $tmp > 0 ) self::$_dflt_id = $tmp;
		}

		if( self::$_dflt_id > 0 ) return self::load(self::$_dflt_id);

        throw new CmsInvalidDataException('There is no default design selected');
	}

	/**
	 * Given a base name, suggest a name for a copied theme
	 *
	 * @param string $newname
	 * @return string
	 */
	public static function suggest_name($newname = '')
	{
		if( $newname == '' ) $newname = 'New Design';
		$list = self::get_list();
		$names = array_values($list);

		$origname = $newname;
		$n = 1;
		while( $n < 100 && in_array($newname,$names) ) {
			$n++;
			$newname = $origname.' '.$n;
		}

		if( $n == 100 ) return;
		return $newname;
	}
} // end of class

// dunno if this should go here...
class_alias('CmsLayoutCollection','CmsLayoutDesign')

#
# EOF
#
?>
