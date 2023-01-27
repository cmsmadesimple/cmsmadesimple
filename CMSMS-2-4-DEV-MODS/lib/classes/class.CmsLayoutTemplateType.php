<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CmsLayoutTemplateType (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A class to manage template types.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE

/**
 * This file contains classes and functions that define a template type.
 * @package CMS
 * @license GPL
 */

use \CMSMS\HookManager;

/**
 * A class to manage template types
 *
 * @package CMS
 * @license GPL
 * @since 2.0
 * @author Robert Campbell <calguy1000@gmail.com>
 */
class CmsLayoutTemplateType
{
	/**
	 * This constant indicates a core template type
	 */
	const CORE = '__CORE__';

	/**
	 * @ignore
	 */
	const TABLENAME = 'layout_tpl_type';

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
	private static $_cache;

	/**
	 * @ignore
	 */
	private static $_name_cache;

    /**
     * @ignore
     */
    private $_assistant;

    /**
     * Get the template type id
     *
     * @return int type id, or null if this record has no id.
     */
    public function get_id()
    {
        if( isset($this->_data['id']) ) return $this->_data['id'];
    }

    /**
     * Get the template originator (this is usually a module name)
     *
	 * @param  bool $viewable Should the originator name be the viewable (friendly) string?
     * @return string
     */
    public function get_originator($viewable = FALSE)
    {
        $out = '';
        if( isset($this->_data['originator']) ) $out = $this->_data['originator'];
        if( $out == self::CORE && $viewable ) $out = 'Core';
        return $out;
    }

    /**
     * Set the template originator string.
     *
     * @throws CmsInvalidDataException
     * @param string $str The originator string, usually a module name.
     */
    public function set_originator($str)
    {
        $str = trim($str);
        if( !$str ) throw new CmsInvalidDataException('Originator cannot be empty');
        $this->_data['originator'] = $str;
        $this->_dirty = TRUE;
    }

    /**
     * Return the template type name.
     *
     * @return string the template type
     */
    public function get_name()
    {
        if( isset($this->_data['name']) ) return $this->_data['name'];
    }

    /**
     * Set the template type name
     *
     * @throws CmsInvalidDataException
     * @param string $str The template type name.
     */
    public function set_name($str)
    {
        $str = trim($str);
        if( !$str ) throw new CmsInvalidDataException('Name cannot be empty');
        $this->_data['name'] = $str;
        $this->_dirty = TRUE;
    }

    /**
     * Get the flag indicating if this template type can have a 'default'
     *
     * @return bool
     */
    public function get_dflt_flag()
    {
        if( isset($this->_data['has_dflt']) ) return $this->_data['has_dflt'];
    }

    /**
     * Set the flag indicating if this template type can have a 'default'
     *
     * @throws CmsInvalidDataException
     * @param bool $flag
     */
    public function set_dflt_flag($flag = TRUE)
    {
        if( !is_bool($flag) ) throw new CmsInvalidDataException('value is invalid for set_dflt_flag');
        $this->_data['has_dflt'] = $flag;
        $this->_dirty = TRUE;
    }

    /**
     * Get the default contents used when creating a new template of this type.
     *
     * @return string
     */
    public function get_dflt_contents()
    {
        if( isset($this->_data['dflt_contents']) ) return $this->_data['dflt_contents'];
    }

    /**
     * Set the default content used when creating a new templateof this  type.
     *
     * @param string $str The default template contents.
     */
    public function set_dflt_contents($str)
    {
        $this->_data['dflt_contents'] = $str;
        $this->_dirty = TRUE;
    }

    /**
     * Get the template type description.
     *
     * @return string
     */
    public function get_description()
    {
        if( isset($this->_data['description']) ) return $this->_data['description'];
    }


    /**
     * Set the description for this template.
     *
     * @param string $str The default template contents.
     */
    public function set_description($str)
    {
        $this->_data['description'] = $str;
        $this->_dirty = TRUE;
     }

    /**
     * Get the owner of this template type.
     *
     * @return int
     */
    public function get_owner()
    {
        if( isset($this->_data['owner']) ) return $this->_data['owner'];
    }

    /**
     * Set the owner of this template type
     *
     * @throws CmsInvalidDataException
     * @param int $owner
     */
    public function set_owner($owner)
    {
        if( !is_numeric($owner) || (int)$owner == 0 ) throw new CmsInvalidDataException('value is invalid for owner in '.__METHOD__);
        $this->_data['owner'] = (int)$owner;
        $this->_dirty = TRUE;
    }

    /**
     * Get the date that this object was created.
     *
     * @return intUnix timestamp representing the creation date.  or null if this object has not been saved.
     */
    public function get_create_date()
    {
        if( isset($this->_data['created']) ) return $this->_data['created'];
    }

    /**
     * Get the date that this object was last modified
     *
     * @return intUnix timestamp representing the modification date.  or null if this object has not been saved.
     */
    public function get_modified_date()
    {
        if( isset($this->_data['modified']) ) return $this->_data['modified'];
    }

    /**
     * Set a callback to be used to retrieve a translated version of the originator and name strings.
     *
     * This callback must be a static string representing a static function name, or an array
     * representing a class name and method name.  This callback (if set) will be used to translate
     * the originator string, and the name string to something suitable to users language.
     *
     * @param callable $data A static function name string, or an array of class name and member name.
     */
    public function set_lang_callback($data)
    {
        $this->_data['lang_callback'] = $data;
        $this->_dirty = TRUE;
    }

    /**
     * Return the callback used to translate the originator and name strings.
     *
     * @return mixed
     */
    public function get_lang_callback()
    {
        if( isset($this->_data['lang_callback']) ) return $this->_data['lang_callback'];
    }

    /**
     * Set a callback to be used to display help for this template when editing.
     *
     * @param callable $callback A static function name, or an array of a class name and member name.
     */
    public function set_help_callback($callback)
    {
        $this->_data['help_callback'] = $callback;
        $this->_dirty = TRUE;
    }

    /**
     * Return the callback used to retrieve help for this template type.
     *
     * @return mixed
     */
    public function get_help_callback()
    {
        if( isset($this->_data['help_callback']) ) return $this->_data['help_callback'];
    }

    /**
     * Set the flag indicating that a maximum of one template of this type is permitted.
     *
     * @throws CmsInvalidDataException
     * @param bool $flag
     */
    public function set_oneonly_flag($flag = TRUE)
    {
        if( !is_bool($flag) ) throw new CmsInvalidDataException('value is invalid for set_oneonly_flag');
        $this->_data['one_only'] = $flag;
        $this->_dirty = true();
    }

    /**
     * Get the flag indicating that a maximum of one template of this type is permitted.
     *
     * @return bool
     */
    public function get_oneonly_flag()
    {
        if( isset($this->_data['one_only']) ) return $this->_data['one_only'];
    }

    /**
     * Set a callback to be used when restoring the 'default content' to system default values.
     *
     * Modules typically distribut sample templates.  This callback function is used when the
     * user clicks on a button to reset the selected template type to it's factory default values.
     *
     * @param callable $data A static function name string, or an array of class name and member name.
     */
    public function set_content_callback($data)
    {
        $this->_data['content_callback'] = $data;
        $this->_dirty = TRUE;
    }

    /**
     * Return the callback used to reset a template to its factory default values.
     *
     * @return mixed
     */
    public function get_content_callback()
    {
        if( isset($this->_data['content_callback']) ) return $this->_data['content_callback'];
    }

	/**
	 * Get the content block flag
	 * The content block flag indicates that this template type requires content blocks
	 *
	 * @return bool
	 */
	public function get_content_block_flag()
	{
        if( isset($this->_data['requires_contentblocks']) ) return $this->_data['requires_contentblocks'];
	}

	/**
	 * Set the content block flag to indicate that this template type requires content blocks
	 *
	 * @param bool $flag
	 */
	public function set_content_block_flag($flag)
	{
		$flag = (bool)$flag;
		$this->_data['requires_contentblocks'] = $flag;
	}

    /**
     * Validate the integrity of a template type object.
     *
     * This method will check the contents of the object for validity, and ensure that
     * the originator/name combination are unique.
     *
     * This method throws an exception if an error is found in the integrity of the object.
     *
     * @throws CmsInvalidDataException
     * @param bool $is_insert Wether this is a new insert, or an update.
     */
    protected function validate($is_insert = TRUE)
    {
        if( !$this->get_originator() ) throw new CmsInvalidDataException('Invalid Type Originator');
        if( !$this->get_name() ) throw new CmsInvalidDataException('Invalid Type Name');
		if( !preg_match('/[A-Za-z0-9_\,\.\ ]/',$this->get_name()) ) {
			throw new CmsInvalidDataException('Name must contain only letters, numbers and underscores.');
		}

        if( !$is_insert ) {
            if( !isset($this->_data['id']) || (int)$this->_data['id'] < 1 ) throw new CmsInvalidDataException('id is not set');

            // check for item with the same name
            $db = CmsApp::get_instance()->GetDb();
            $query = 'SELECT id FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE originator = ? AND name = ? AND id != ?';
            $dbr = $db->GetOne($query,array($this->get_originator(),$this->get_name(),$this->get_id()));
            if( $dbr ) throw new CmsInvalidDataException('Template Type with the same name already exists.');
        }
        else {
            // check for item with the same name
            $db = CmsApp::get_instance()->GetDb();
            $query = 'SELECT id FROM '.CMS_DB_PREFIX.self::TABLENAME.'
                WHERE originator = ? AND name = ?';
            $dbr = $db->GetOne($query,array($this->get_originator(),$this->get_name()));
            if( $dbr ) throw new CmsInvalidDataException('Template Type with the same name already exists.');
        }
    }

    /**
     * A function to insert the current type object into the database.
     *
     * This method will ensure that the current object is valid, generate an id, and
     * insert the record into the database.  An exception will be thrown if errors occur.
     *
     * @throws CmsSQLErrorException
     */
    protected function _insert()
    {
        if( !$this->_dirty ) return;
        $this->validate();
        $db = CmsApp::get_instance()->GetDb();
        $now = time();
        $query = 'INSERT INTO '.CMS_DB_PREFIX.self::TABLENAME.'
                (originator,name,has_dflt,one_only,dflt_contents,description,
                 lang_cb,help_content_cb,dflt_content_cb,requires_contentblocks,owner,created,modified)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $dbr = $db->Execute($query,array($this->get_originator(), $this->get_name(), $this->get_dflt_flag(), $this->get_oneonly_flag(),
                                         $this->get_dflt_contents(), $this->get_description(),
                                         serialize($this->get_lang_callback()),
                                         serialize($this->get_help_callback()),
                                         serialize($this->get_content_callback()), $this->get_content_block_flag() ? 1 : 0,
                                         $this->get_owner(), $now,$now));
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

        $this->_data['id'] = $db->Insert_ID();
		CmsTemplateCache::clear_cache();
		audit($this->get_id(),'CMSMS','Template Type '.$this->get_name().' Created');
        $this->_dirty = null;
    }


    /**
     * A function to update the contents of the database to match th current record.
     *
     * This method will ensure that the current object is valid, generate an id, and
     * update the record in the database.  An exception will be thrown if errors occur.
     *
     * @throws CmsSQLErrorException
     */
    protected function _update()
    {
        if( !$this->_dirty ) return;
        $this->validate(FALSE);
        $db = CmsApp::get_instance()->GetDb();
        $now = time();

        $query = 'UPDATE '.CMS_DB_PREFIX.self::TABLENAME.'
                SET originator = ?, name = ?, has_dflt = ?, one_only = ?, dflt_contents = ?, description = ?,
                    lang_cb = ?, help_content_cb = ?, dflt_content_cb = ?, requires_contentblocks = ?, owner = ?, modified = ?
                WHERE id = ?';
        $dbr = $db->Execute($query,array($this->get_originator(),$this->get_name(),$this->get_dflt_flag(),$this->get_oneonly_flag(),
                                         $this->get_dflt_contents(),$this->get_description(),
                                         serialize($this->get_lang_callback()),serialize($this->get_help_callback()),
                                         serialize($this->get_content_callback()),$this->get_content_block_flag() ? 1 : 0,
                                         $this->get_owner(), $now, $this->get_id()));
        if( !$dbr ) throw new CmsSQLErrorException($db->ErrorMsg());

		CmsTemplateCache::clear_cache();
        $this->_dirty = null;
		audit($this->get_id(),'CMSMS','Template Type '.$this->get_name().' Updated');
    }

    /**
     * Save the current record to the database.
     */
    public function save()
    {
        if( !$this->get_id() ) {
            HookManager::do_hook('Core::AddTemplateTypePre', [ get_class($this) => &$this ]);
            $this->_insert();
            HookManager::do_hook('Core::AddTemplateTypePost', [ get_class($this) => &$this ]);
			return;
        }
        HookManager::do_hook('Core::EditTemplateTypePre', [ get_class($this) => &$this ]);
        $this->_update();
        HookManager::do_hook('Core::EditTemplateTypePost', [ get_class($this) => &$this ]);
    }

    /**
     * Get a list of templates for the current template type.
     *
     * @see CmsLayoutTemplate::list_by_type
     * @return Array of CmsLayoutTemplate objects.  or null.
     */
    public function get_template_list()
    {
        return CmsLayoutTemplate::load_all_by_type($this);
    }

    /**
     * Delete the current object from the database (if it has been saved).
     *
     * @throws CmsInvalidDataException
     * @throws CmsSQLErrorException
     */
    public function delete()
    {
        if( !$this->get_id() ) return;

        HookManager::do_hook('Core::DeleteTemplateTypePre', [ get_class($this) => &$this ]);
		$tmp = CmsLayoutTemplate::template_query(array('t:'.$this->get_id()));
        if( is_array($tmp) && count($tmp) ) throw new CmsInvalidDataException('Cannot delete a template type with existing templates');
        $db = CmsApp::get_instance()->GetDb();
        $query = 'DELETE FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id = ?';
        $dbr = $db->Execute($query,array($this->_data['id']));
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

        $this->_dirty = TRUE;
		CmsTemplateCache::clear_cache();
		audit($this->get_id(),'CMSMS','Template Type '.$this->get_name().' Deleted');
        HookManager::do_hook('Core::DeleteTemplateTypePost', [ get_class($this) => &$this ]);
        unset($this->_data['id']);
    }

    /**
     * Create a new template of ths type
     *
     * This method will throw an exception if the template cannot be created.
     *
     * @param string $name The template name
     * @return CmsLayoutTemplate object, or null.
     */
    public function &create_new_template($name = '')
    {
        $ob = new CmsLayoutTemplate();
        $ob->set_type( $this );
        $ob->set_content( $this->get_dflt_contents() );
        if( $name ) $ob->set_name($ob);
        return $ob;
    }

    /**
     * Get the default template of ths type
     *
     * This method will throw an exception if the template cannot be created.
     *
     * @see CmsLayoutTemplate::load_dflt_by_type()
     * @return CmsLayoutTemplate object, or null.
     */
    public function get_dflt_template()
    {
        return CmsLayoutTemplate::load_dflt_by_type($this);
    }

    /**
     * Get HTML text for help with respect to the variables available in this template type.
     */
    public function get_template_helptext()
    {
        $text = null;
        $cb = $this->get_help_callback();
        $originator = $this->get_originator();
        $name = $this->get_name();
        if( $cb && is_callable($cb) ) {
            $text = call_user_func($cb,$name);
            return $text;
        }

        // no callback specified, see if this originator is a loadable module.
        $name = $this->get_name();
        if( $originator == self::CORE ) {
            // it's a core page template, or generic
        } else {
            $module = \cms_utils::get_module($originator);
            if( $module ) {
                if( method_exists($module,'get_templatetype_help') ) {
                    $text = $module->get_templatetype_help($name);
                }
            }
        }
        return $text;
    }

	/**
	 * Get a translated/pretty displayable name for this template type
	 * including the originator.
	 */
    public function get_langified_display_value()
    {
        $t = $this->get_lang_callback();
        $to = $tn = null;
        if( is_callable($t) ) {
            $to = call_user_func($t,$this->get_originator());
            $tn = call_user_func($t,$this->get_name());
        }
        if( !$to ) $to = $this->get_originator();
        if( $to == self::CORE ) $to = 'Core';
        if( !$tn ) $tn = $this->get_name();
        return $to.'::'.$tn;
    }

	/**
	 * Reset the default contens of this template type back to factory defaults
     *
     * @throws CmsException
     * @throws CmsDataNotFoundException
	 */
    public function reset_content_to_factory()
    {
        if( !$this->get_dflt_flag() ) throw new CmsException('This template type does not have default contents');
        $cb = $this->get_content_callback();
        if( !$cb ) throw new CmsDataNotFoundException('No callback information to reset content');
        if( !is_callable($cb) ) throw new CmsDataNotFoundException('No callback information to reset content');

        $content = call_user_func($cb,$this);
        $this->set_dflt_contents($content);
    }

	/**
	 * Given an array (typically read from the database) create a CmsLayoutTemplateType object
	 *
	 * @internal
	 * @return CmsLayoutTemplateType
	 */
    private static function &_load_from_data($row)
    {
        if( isset($row['lang_cb']) && $row['lang_cb'] ) $row['lang_callback'] = unserialize($row['lang_cb']);
        if( isset($row['help_content_cb']) && $row['help_content_cb'] ) $row['help_callback'] = unserialize($row['help_content_cb']);
        if( isset($row['dflt_content_cb']) && $row['dflt_content_cb'] ) $row['content_callback'] = unserialize($row['dflt_content_cb']);
        unset($row['lang_cb'],$row['help_content_cb'],$row['dflt_content_cb']);

        $ob = new self();
        $ob->_data = $row;
        $ob->_dirty = FALSE;

		self::$_cache[$ob->get_id()] = $ob;
		self::$_name_cache[$ob->get_originator().'::'.$ob->get_name()] = $ob->get_id();
        return $ob;
    }

    /**
     * Load a CmsLayoutTemplateType object from the database.
     *
     * This method throws an exception when the requested object cannot be found.
     *
     * @throws CmsDataNotFoundException
     * @param mixed $val An integer template type id, or a string in the form of Originator::Name
     * @return CmsLayoutTemplateType
     */
    public static function &load($val)
    {
        $db = CmsApp::get_instance()->GetDb();
        $row = null;
        if( is_numeric($val) && (int)$val > 0 ) {
            $val = (int) $val;
			if( isset(self::$_cache[$val]) ) return self::$_cache[$val];

            $query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id = ?';
            $row = $db->GetRow($query,array($val));
        }
        elseif( strlen($val) > 0 ) {
			if( isset(self::$_name_cache[$val]) ) {
				$id = self::$_name_cache[$val];
				return self::$_cache[$id];
			}

            $tmp = explode('::',$val);
            if( count($tmp) == 2 ) {
                $query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE originator = ? AND name = ?';
                if( $tmp[0] == 'Core' or $tmp[0] == 'core' ) $tmp[0] = self::CORE;
                $row = $db->GetRow($query,array(trim($tmp[0]),trim($tmp[1])));
            }
        }
        if( !is_array($row) || count($row) == 0 ) throw new CmsDataNotFoundException('Could not find template type identified by '.$val);

        return self::_load_from_data($row);
    }

    /**
     * Load all of the template types for a certain originator.
     *
     * This method will throw exceptions if an error is encounted.
     *
     * @throws CmsInvalidDataException
     * @param string $originator The origiator name
     * @return array An array of CmsLayoutTemplateType objects, or null if no matches are found.
     */
    public static function load_all_by_originator($originator)
    {
        if( !$originator ) throw new CmsInvalidDataException('Orignator is empty');

        $db = CmsApp::get_instance()->GetDb();
        $query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE originator = ?';
        if( isset(self::$_cache) && count(self::$_cache) ) $query .= ' AND id NOT IN ('.implode(',',array_keys(self::$_cache)).')';
        $query .= ' ORDER BY modified DESC';
        $list = $db->GetArray($query,array($originator));
        if( !is_array($list) || count($list) == 0 ) return;

        foreach( $list as $row ) {
            self::_load_from_data($row);
        }

		$out = array();
		foreach( self::$_cache as $id => $one ) {
			if( $one->get_originator() == $originator ) $out[] = $one;
		}
		return $out;
    }

	/**
	 * Load all template types
	 *
	 * @return array Array of CmsLayoutTemplateType objects
	 */
    public static function get_all()
    {
        $db = CmsApp::get_instance()->GetDb();
        $query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME;
		if( self::$_cache && count(self::$_cache) ) $query .= ' WHERE id NOT IN ('.implode(',',array_keys(self::$_cache)).')';
		$query .= '	ORDER BY modified ASC';
        $list = $db->GetArray($query);
        if( !is_array($list) || count($list) == 0 ) return;

        foreach( $list as $row ) {
            self::_load_from_data($row);
        }

        return array_values(self::$_cache);
    }

	/**
	 * Load template type objects by specifying an array of ids
	 *
	 * @param int[] $list Array of template type ids
	 */
	public static function load_bulk($list)
	{
		if( !is_array($list) || count($list) == 0 ) return;

		$list2 = array();
		foreach( $list as $one ) {
            if( !is_numeric($one) || (int)$one < 1 ) continue;
			$one = (int)$one;
			if( isset(self::$_cache[$one]) ) continue;
			$list2[] = $one;
		}

        $db = CmsApp::get_instance()->GetDb();
        $query = 'SELECT * FROM '.CMS_DB_PREFIX.self::TABLENAME.' WHERE id IN ('.implode(',',$list).')';
        $list = $db->GetArray($query);
        if( !is_array($list) || count($list2) == 0 ) return;

        $out = array();
        foreach( $list as $row ) {
            $out[] = self::_load_from_data($row);
        }
        return $out;
	}

	/**
	 * Return the names of all loaded template types
	 *
	 * @return array Associative array of loaded type objects.
	 */
	public static function get_loaded_types()
	{
		if( is_array(self::$_cache) ) return array_keys(self::$_cache);
	}

    /**
     * Get the assistant object with utility methods for this template type (if such an assistant object can be instantiated)
     *
     * @return \CMSMS\Layout\TemplateTypeAssistant
     * @since 2.2
     */
    public function &get_assistant()
    {
        if( !$this->_assistant ) {
            $classnames = [];
            $classnames[] = '\\CMSMS\\internal\\'.$this->get_originator().$this->get_name().'_Type_Assistant';
            $classnames[] = '\\CMSMS\\Layout\\'.$this->get_originator().$this->get_name().'_Type_Assistant';
            $classnames[] = $this->get_originator().'_'.$this->get_name().'_Type_Assistant';
            foreach( $classnames as $cn ) {
                if( class_exists($cn) ) {
                    $tmp = new $cn;
                    if( is_a($tmp,'\CMSMS\Layout\TemplateTypeAssistant') ) {
                        $this->_assistant = $tmp;
                        break;
                    }
                }
            }
        }

        return $this->_assistant;
    }

    /**
     * Get a usage string for this template type.
     *
     * @since 2.2
     * @param string $name The name of the template object.
     * @return string
     */
    public function get_usage_string($name)
    {
        $name = trim($name);
        if( !$name ) return;

        $assistant = $this->get_assistant();
        if( !$assistant ) return;

        return $assistant->get_usage_string($name);
    }
} // end of class

#
# EOF
#
