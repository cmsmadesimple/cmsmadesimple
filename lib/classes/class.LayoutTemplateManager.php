<?php

/**
 * Tools for interacting with template objects and the database
 *
 * @package CMS
 * @license GPL
 */

namespace CMSMS;
use CMSMS\Database\Connection as Database;
use cms_config;
use cms_cache_driver;
use CmsLayoutTemplate;
use CmsLayoutTemplateType;
use CmsLayoutCollection;
use CMSMS\hook_manager;

/**
 * A class that manages the storage of CmsLayoutTemplate objects in the database.
 *
 * This class also supports caching, and sending hooks at various levels
 *
 * @since 2.3
 * @package CMS
 * @license GPL
 * @author Robert Campbell
 */
class LayoutTemplateManager
{
    private $db;
    private $cache_driver;
    private $hook_manager;
    private $config;

    /**
     * Constructor
     *
     * @param Database $db The database
     * @param cms_cache_driver $driver The Cache driver
     * @param hook_manager $hook_manager The hook manager
     */
    public function __construct( Database $db, cms_cache_driver $driver, hook_manager $hook_manager, cms_config $config )
    {
        $this->db = $db;
        $this->cache_driver = $driver;
        $this->hook_manager = $hook_manager;
        $this->config = $config;
    }

    /**
     * Given a template name, generate an id.
     *
     * This method uses a cached mapping of template names to id.
     * If the item does not exist in the cache, then the cache item is built from the database.
     *
     * @param string $name The template name
     * @return int|null
     */
    protected function template_name_to_id(string $name)
    {
        $map = $this->cache_driver->get(__METHOD__,__CLASS__);
        if( !$map ) {
            $sql = 'SELECT id,name FROM '.$this->template_table_name().' ORDER BY name';
            $arr = $this->db->GetArray($sql);
            $map = null;
            foreach( $arr as $row ) {
                $map[$row['name']] = (int) $row['id'];
            }
            $this->cache_driver->set(__METHOD__,$map,__CLASS__);
        }
        if( $map && isset($map[$name]) ) return $map[$name];
    }

    /**
     * Given a type id, return its type id
     *
     * This method uses a cache that is built from the database if necessary.
     *
     * @param int $type_id
     * @return int|null The default template id, if any.
     */
    protected function get_default_template_by_type(int $type_id)
    {
        $map = $this->cache_driver->get(__METHOD__,__CLASS__);
        if( !$map ) {
            $sql = 'SELECT id,type_id FROM '.$this->template_table_name().' WHERE type_dflt = 1';
            $arr = $this->db->GetArray($sql);
            $map = null;
            foreach( $arr as $row ) {
                $map[$row['type_id']] = (int) $row['id'];
            }
            $this->cache_driver->set(__METHOD__,$map,__CLASS__);
        }
        if( $map && isset($map[$type_id]) ) return $map[$type_id];
    }

    /**
     * Test if a template exists in the cache, by it's id.
     *
     * @internal
     * @param int $tpl_id The template id
     * @return CmsLayoutTemplate|null
     */
    protected function get_cached_template(int $tpl_id)
    {
        return $this->cache_driver->get($tpl_id,__CLASS__);
    }

    /**
     * Adds or overwrites a template into the cache
     *
     * @internal
     * @param CmsLayoutTemplate $tpl The template to store.
     */
    protected function set_template_cached(CmsLayoutTemplate $tpl)
    {
        if( !$tpl->get_id() ) throw new \InvalidArgumentException('Cannot cache a template with no id');
	if( $this->template_has_file($tpl) ) return;
        $this->cache_driver->set($tpl->get_id(),$tpl,__CLASS__);
        $idx = $this->cache_driver->get('cached_index',__CLASS__);
        if( !$idx ) $idx = [];
        $idx[] = $tpl->get_id();
        $idx = array_unique($idx);
        $this->cache_driver->set('cached_index',__CLASS__);
    }

    protected function set_template_uncached(CmsLayoutTemplate $tpl)
    {
        if( !($tpl_id = $tpl->get_id()) ) return;
        $idx = $this->get_cached_templates();
        $idx = array_filter($idx,function($item) use ($tpl_id) {
                return $item != $tpl_id;
            });
	if( empty($idx) ) {
            $this->cache_driver->erase('cached_index',__CLASS__);
        } else {
            $this->cache_driver->set('cached_index',$idx,__CLASS__);
        }
        $this->cache_driver->erase($tpl_id,__CLASS__);
    }

    /**
     * Get an index of all of the cached templates.
     *
     * @internal
     * @return int[]|null
     */
    protected function get_cached_templates()
    {
        $idx = $this->cache_driver->get('cached_index',__CLASS__);
        if( !$idx ) $idx = [];
        return $idx;
    }

    /**
     * @ignore
     */
    protected function _resolve_user($a)
    {
        if( is_numeric($a) && $a > 0 ) return $a;
        if( is_string($a) && strlen($a) ) {
            $ops = cmsms()->GetUserOperations();
            $ob = $ops->LoadUserByUsername($a);
            if( is_object($a) && is_a($a,'User') ) return $a->id;
        }
        if( is_object($a) && is_a($a,'User') ) return $a->id;
        throw new \CmsLogicException('Could not resolve '.$a.' to a user id');
    }

    /**
     * Generate a unique template name
     *
     * @param string $prototype The input prototype
     * @param string $prefix A prefix to apply to all output
     * @return string
     */
    public function generate_unique_template_name(string $prototype, string $prefix = null)
    {
        if( !$prototype ) throw new \InvalidArgumentException('Prototype name cannot be empty');
        $db = $this->db;
        $query = 'SELECT id FROM '.$this->template_table_name().' WHERE name = ?';
        for( $i = 0; $i < 25; $i++ ) {
            $name = $prefix.$prototype;
            if( $i == 0 ) $name = $prototype;
            if( $i > 1 ) $name = $prefix.$prototype.' '.$i;
            $tmp = $db->GetOne($query,array($name));
            if( !$tmp ) return $name;
        }
        throw new \CmsLogicException('Could not generate a template name for '.$prototype);
    }

    /**
     * Validate a template to ensure that it is suitable for storage.
     *
     * This method throws exceptions if validation cannot be assured.
     *
     * @param CmsLayoutTemplate $tpl
     */
    public function validate_template( CmsLayoutTemplate $tpl )
    {
        $tpl->validate();

        $db = $this->db;
        $tmp = null;
        if( $tpl->get_id() ) {
            // double check the name.
            $query = 'SELECT id FROM '.$this->template_table_name().' WHERE name = ? AND id != ?';
            $tmp = $db->GetOne($query,array($tpl->get_name(),$tpl->get_id()));
        } else {
            // double check the name.
            $query = 'SELECT id FROM '.$this->template_table_name().' WHERE name = ?';
            $tmp = $db->GetOne($query,array($tpl->get_name()));
        }
        if( $tmp ) throw new \CmsLogicException('Template with the same name already exists.');
    }

    /**
     * Update the template object into the database.
     *
     * @internal
     * @param CmsLayoutTemplate $tpl
     * @returns CmsLayoutTemplate
     */
    protected function _update_template( CmsLayoutTemplate $tpl ) : CmsLayoutTemplate
    {
        $this->validate_template($tpl);

        $db = $this->db;
        $query = 'UPDATE '.$this->template_table_name().'
              SET name = ?, content = ?, description = ?, type_id = ?, type_dflt = ?, category_id = ?, owner_id = ?, listable = ?, modified = ?
              WHERE id = ?';
        $dbr = $db->Execute($query,
                          array($tpl->get_name(),$tpl->get_content(),$tpl->get_description(),
                                $tpl->get_type_id(),$tpl->get_type_dflt(),$tpl->get_category_id(),
                                $tpl->get_owner_id(),$tpl->get_listable(),time(),
                                $tpl->get_id()));
        if( !$dbr ) throw new \CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

        if( $tpl->get_type_dflt() ) {
            // if it's default for a type, unset default flag for all other records with this type
            $query = 'UPDATE '.$this->template_table_name().' SET type_dflt = 0 WHERE type_id = ? AND type_dflt = 1 AND id != ?';
            $dbr = $db->Execute($query,array($tpl->get_type_id(),$tpl->get_id()));
            if( !$dbr ) throw new \CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        }

        $query = 'DELETE FROM '.$this->tpl_additional_users_table_name().' WHERE tpl_id = ?';
        $dbr = $db->Execute($query,array($tpl->get_id()));
        if( !$dbr ) throw new \CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

        $t = $tpl->get_additional_editors();
        if( is_array($t) && count($t) ) {
            $query = 'INSERT INTO '.$this->tpl_additional_users_table_name().' (tpl_id,user_id) VALUES(?,?)';
            foreach( $t as $one ) {
                $dbr = $db->Execute($query,array($tpl->get_id(),(int)$one));
            }
        }

        $query = 'DELETE FROM '.CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE.' WHERE tpl_id = ?';
        $dbr = $db->Execute($query,array($tpl->get_id()));
        if( !$dbr ) throw new \CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        $t = $tpl->get_designs();
        if( is_array($t) && count($t) ) {
            $query = 'INSERT INTO '.CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE.' (tpl_id,design_id) VALUES(?,?)';
            foreach( $t as $one ) {
                $dbr = $db->Execute($query,array($tpl->get_id(),(int)$one));
            }
        }

        $this->cache_driver->clear(__CLASS__);
        audit($tpl->get_id(),'CMSMS','Template '.$tpl->get_name().' Updated');
        return $tpl;
    }

    /**
     * Insert a template into the database
     *
     * @param CmsLayoutTemplate $tpl
     * @return CmsLayoutTemplate
     */
    protected function _insert_template( CmsLayoutTemplate $tpl ) : CmsLayoutTemplate
    {
        $this->validate_template($tpl);

        $db = $this->db;
        $query = 'INSERT INTO '.$this->template_table_name().'
              (name,content,description,type_id,type_dflt,category_id,owner_id,
               listable,created,modified) VALUES (?,?,?,?,?,?,?,?,?,?)';
        $dbr = $db->Execute($query,
        [
                              $tpl->get_name(),$tpl->get_content(),$tpl->get_description(),
                              $tpl->get_type_id(),$tpl->get_type_dflt(),$tpl->get_category_id(),
                              $tpl->get_owner_id(),$tpl->get_listable(),time(),time()
                              ]);
        if( !$dbr ) throw new \CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        $new_id = $db->Insert_ID();

        if( $tpl->get_type_dflt() ) {
            // if it's default for a type, unset default flag for all other records with this type
            $query = 'UPDATE '.$this->template_table_name().' SET type_dflt = 0 WHERE type_id = ? AND type_dflt = 1 AND id != ?';
            $dbr = $db->Execute($query,[ $tpl->get_type_id(), $new_id ]);
            if( !$dbr ) throw new \CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        }

        $t = $tpl->get_additional_editors();
        if( is_array($t) && count($t) ) {
            $query = 'INSERT INTO '.$this->tpl_additional_users_table_name().' (tpl_id,user_id) VALUES(?,?)';
            foreach( $t as $one ) {
                $dbr = $db->Execute($query,array($new_id,(int)$one));
            }
        }

        $t = $tpl->get_designs();
        if( is_array($t) && count($t) ) {
            $query = 'INSERT INTO '.$this->design_assoc_table_name().' (tpl_id,design_id) VALUES(?,?)';
            foreach( $t as $one ) {
                $dbr = $db->Execute($query,array($new_id,(int)$one));
            }
        }

        $this->cache_driver->clear(__CLASS__);
	// this is hackish, it re-creates the template object and then it has an id
        $arr = $tpl->_get_array();
	$arr['id'] = $new_id;
        $tpl = $tpl::_load_from_data($arr);
        audit($new_id,'CMSMS','Template '.$tpl->get_name().' Created');
        return $tpl;
    }

    /**
     * Save a template into the database.
     *
     * This method takes an existing template object and either updates it into the database, or inserts it.
     *
     * @param CmsLayoutTemplate $tpl The template object.
     */
    public function save_template( CmsLayoutTemplate $tpl )
    {
        if( $tpl->get_id() ) {
            $this->hook_manager->emit('Core::EditTemplatePre', [ get_class($tpl) => &$tpl ] );
            $tpl = $this->_update_template($tpl);
            $this->hook_manager->emit('Core::EditTemplatePost', [ get_class($tpl) => &$tpl ] );
            $this->set_template_uncached($tpl);
            return $tpl;
        }

        $this->hook_manager->emit('Core::AddTemplatePre', [ get_class($tpl) => &$tpl ] );
        $tpl = $this->_insert_template($tpl);
        $this->hook_manager->emit('Core::AddTemplatePost', [ get_class($tpl) => &$tpl ] );
        return $tpl;
    }

    /**
     * Delete a template from the database
     *
     * This method removes a template object from the database and any caches.
     * it does not modify the template object, so care must be taken with the id.
     *
     * @param CmsLayoutTemplate $tpl
     */
    public function delete_template( CmsLayoutTemplate $tpl )
    {
        if( !$tpl->get_id() ) return;

        $this->hook_manager->emit('Core::DeleteTemplatePre', [ get_class($tpl) => &$tpl ] );
        $db = $this->db;
        $query = 'DELETE FROM '.CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE.' WHERE tpl_id = ?';
        $dbr = $db->Execute($query,array($tpl->get_id()));

        $query = 'DELETE FROM '.$this->template_table_name().' WHERE id = ?';
        $dbr = $db->Execute($query,array($tpl->get_id()));

        @unlink($tpl->get_content_filename());

        audit($tpl->get_id(),'CMSMS','Template '.$tpl->get_name().' Deleted');
        $this->hook_manager->emit('Core::DeleteTemplatePost', [ get_class($tpl) => &$tpl ] );
        $this->cache_driver->clear(__CLASS__);
    }

    /**
     * Load a template
     *
     * @param mixed $a  The template id or name to load.
     * @return CmsLayoutTemplate|null
     */
    public function load_template($a)
    {
        $id = null;
        if( is_numeric($a) && $a > 0 ) {
            $id = $a;
        }
        else if( is_string($a) && strlen($a) > 0 ) {
            $id = $this->template_name_to_id($a);
            if( !$id ) {
                cms_warning('could not find a template id for template named '.$a);
                return;
            }
        }

        // if it exists in the cache, then we're done
        $obj = $this->get_cached_template($id);
        if( $obj ) {
	    return $obj;
	}

        // load it from the database
        $db = $this->db;
        $sql = 'SELECT * FROM '.$this->template_table_name().' WHERE id = ?';
        $row = $db->GetRow($sql, [$id]);
        if( !$row ) return; // not found

        $sql = 'SELECT * FROM '.$this->design_assoc_table_name().' WHERE tpl_id = ?';
        $designs = $db->GetArray($sql, [$id]);

        $sql = 'SELECT * FROM '.$this->tpl_additional_users_table_name().' WHERE tpl_id = ?';
        $editors = $db->GetArray($sql, [$id]);

        // put it in the cache
        $obj = CmsLayoutTemplate::_load_from_data($row,$designs,$editors);
        $this->set_template_cached($obj);
        return $obj;
    }

    /**
     * Load multiple templates
     *
     * @param array $list An array of integer template ids.
     * @return CmsLayoutTemplate[]
     */
    public function load_bulk_templates(array $list)
    {
        if( !is_array($list) || count($list) == 0 ) return;

        $get_assoc_designs = function(int $id, array $alldesigns) {
            $out = null;
            foreach( $alldesigns as $design ) {
                if( $design['tpl_id'] < $id ) continue;
                if( $design['tpl_id'] > $id ) continue;
                $out[] = $design['design_id'];
            }
            return $out;
        };

        $get_assoc_users = function(int $id, array $allusers) {
            $out = null;
            foreach( $allusers as $user ) {
                if( $user['tpl_id'] < $id ) continue;
                if( $user['tpl_id'] > $id ) continue;
                $out[] = $user['user_id'];
            }
            return $out;
        };

        $uncached = array_diff($list,$this->get_cached_templates());
        $loaded = [];
        if( is_array($uncached) && count($uncached) > 0 ) {
            // have to load these items and put them in the cache.
            $db = $this->db;
            $str = implode(',',$uncached);
            $sql = 'SELECT * FROM '.$this->template_table_name()." WHERE id IN ({$str})";
            $rows = $db->GetArray( $sql );
            if( count($rows) ) {
                $sql = 'SELECT * FROM '.$this->design_assoc_table_name().' WHERE tpl_id IN ('.$str.') ORDER BY tpl_id';
                $alldesigns = $db->GetArray($sql);
                $sql = 'SELECT * FROM '.$this->tpl_additional_users_table_name().' WHERE tpl_id IN ('.$str.') ORDER BY tpl_id';
                $allusers = $db->GetArray($sql);

                // put it all together, create an object
                foreach( $rows as $row ) {
                    $id = $row['id'];
                    $obj = CmsLayoutTemplate::_load_from_data($row,$get_assoc_designs($id,$alldesigns),$get_assoc_users($id,$allusers));
                    $loaded[$id] = $obj;
                    // put it in the cache, we'll get it in a bit.
                    $this->set_template_cached($obj);
                }
            }
        }

        // read from the cache
        $out = null;
        foreach( $list as $tpl_id ) {
            if( isset($loaded[$tpl_id]) ) {
                $out[] = $loaded[$tpl_id];
            } else {
                $out[] = $this->get_cached_template($tpl_id);
            }
        }
        return $out;
    }

    /**
     * Load all templates of a given type
     *
     * @param CmsLayoutTemplateType $type
     * @return CmsLayoutTemplate[]
     */
    public function load_templates_by_type(CmsLayoutTemplateType $type)
    {
        // get the template type id => template_id list
        // see if we have this map in the cache
        $map = null;
        $key = 'types_to_tpl_'.$type->get_id();
        if( $this->cache_driver->exists($key,__CLASS__) ) {
            $map = $this->cache_driver->get($key,__CLASS__);
        } else {
            $db = $this->db;
            $sql = 'SELECT id FROm '.$this->template_table_name().' WHERE type_id = ?';
            $list = $db->GetCol($sql,$type->get_id());
            if( is_array($list) && !empty($list) ) {
                $map = $list;
                $this->cache_driver->set($key,$list,__CLASS__);
            }
        }

        if( empty($map) ) return;

        return $this->load_bulk_templates($map);
    }

    /**
     * Get all of the templates owned by a specific user
     *
     * @param mixed $a Either the integer uid or the username of a user.
     * @return CmsLayoutTemplate[]
     */
    public function get_owned_templates($a)
    {
        $n = $this->_resolve_user($a);
        if( $n <= 0 ) throw new \InvalidArgumentException('Invalid user specified to get_owned_templates');

        $query = new CmsLayoutTemplateQuery(array('u'=>$n));
        $tmp = $query->GetMatchedTemplateIds();
        return $this->load_bulk_templates($tmp);
    }

    /**
     * Get all of the templates that a user owns or can otherwise edit.
     *
     * @param mixed $a Either the integer uid or a username
     * @param CmsLayoutTemplate[]
     */
    public function get_editable_templates($a)
    {
        $n = $this->_resolve_user($a);
        if( $n <= 0 ) throw new \InvalidArgumentException('Invalid user specified to get_owned_templates');
        $db = $this->db;

        $sql = 'SELECT id FROM '.self::template_table_name();
        $parms = $where = null;
        if( !cmsms()->GetUserOperations()->CheckPermission($n,'Modify Templates') ) {
            $sql .= ' WHERE owner_id = ?';
            $parms[] = $n;
        }
        $list = $db->GetCol($sql, $parms);
        if( !$list ) $list = [];

        $sql = 'SELECT tpl_id  FROM '.$this->tpl_additional_users_table_name().' WHERE user_id = ?';
        $list2 = $db->GetCol($sql,[$n]);
        if( !$list2 ) $list2 = [];

        $tpl_list = array_merge($list,$list2);
        $tpl_list = array_unique($tpl_list);
        if( !count($tpl_list) ) return;

        return $this->load_bulk_templates($tpl_list);
    }

    /**
     * Given a template type, get all templates
     *
     * @param CmsLayoutTemplateType $type
     * @return CmsLayoutTemplate[]|null
     */
    public function load_all_templates_by_type(CmsLayoutTemplateType $type)
    {
        $sql = 'SELECT id FROM '.$this->template_table_name().' WHERE type_id = ?';
        $tmp = $db->GetCol($sql, [ $type->get_id() ] );
        if( !$tmp ) return;

        return $this->load_bulk_templates($tmp);
    }

    /**
     * Given a template type, get the default template of that type
     *
     * @param mixed $t A type name, a type id, or a CmsLayoutTemplateType object
     * @param CmsLayoutTemplate
     */
    public function load_default_template_by_type($t)
    {
        $t2 = null;
        if( is_int($t) || is_string($t) ) {
            // todo: this should be a method in this, or another manager class.
            $t2 = CmsLayoutTemplateType::load($t);
        }
        else if( is_object($t) && is_a($t,'CmsLayoutTemplateType') ) {
            $t2 = $t;
        }

        if( !$t2 ) throw new \InvalidArgumentException('Invalid data passed to CmsLayoutTemplate::;load_dflt_by_type()');

        // search our preloaded template first
        $tpl_id = $this->get_default_template_by_type($t2->get_id());
        if( $tpl_id ) return $this->load_template($tpl_id);
    }

    public function get_template_filename(CmsLayoutTemplate $tpl)
    {
        if( !$tpl->get_name() ) return;
        $name = munge_string_to_url($tpl->get_name()).'.'.$tpl->get_id().'.tpl';
        return cms_join_path(CMS_ASSETS_PATH,'templates',$name);
    }

    public function template_has_file(CmsLayoutTemplate $tpl)
    {
        $fn = $this->get_template_filename($tpl);
        return is_file($fn) && is_readable($fn);
    }

    /**
     * @ignore
     */
    public function template_table_name() {
        return CMS_DB_PREFIX.'layout_templates';
    }

    /**
     * @ignore
     */
    public function designs_table_name() {
        return CMS_DB_PREFIX.'layout_designs';
    }

    /**
     * @ignore
     */
    public function tpl_additional_users_table_name() {
        return CMS_DB_PREFIX.'layout_tpl_addusers';
    }

    /**
     * @ignore
     */
    public function design_assoc_table_name() {
        return CMS_DB_PREFIX.'layout_design_tplassoc';
    }
} // class
