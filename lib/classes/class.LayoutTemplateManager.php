<?php
namespace CMSMS;
use CMSMS\Database\Connection as Database;
use cms_cache_driver;
use CmsLayoutTemplate;
use CmsLayoutTemplateType;
use CmsLayoutCollection;
use CMSMS\internal\hook_manager;

class LayoutTemplateManager
{
    public function __construct( Database $db, cms_cache_driver $driver, hook_manager $hook_manager )
    {
        $this->db = $db;
        $this->cache_driver = $driver;
        $this->hook_manager = $hook_manager;
    }

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

    protected function get_cached_template($tpl_id)
    {
        return $this->cache_driver->get($tpl_id,__CLASS__);
    }

    protected function set_template_cached(\CmsLayoutTemplate $tpl)
    {
        if( !$tpl->get_id() ) throw new \InvalidArgumentException('Cannot cache a template with no id');
        $this->cache_driver->set($tpl->get_id(),$tpl,__CLASS__);
        $idx = $this->cache_driver->get('cached_index',__CLASS__);
        if( !$idx ) $idx = [];
        $idx[] = $tpl->get_id();
        $idx = array_unique($idx);
        $this->cache_driver->set('cached_index',__CLASS__);
    }

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

    public function generate_unique_template_name(string $prototype, string $prefix = null)
    {
        if( !$prototype ) throw new CmsInvalidDataException('Prototype name cannot be empty');
        $db = $this->db;
        $query = 'SELECT id FROM '.$this->template_table_name().' WHERE name = ?';
        for( $i = 0; $i < 25; $i++ ) {
            $name = $prefix.$prototype;
            if( $i == 0 ) $name = $prototype;
            if( $i > 1 ) $name = $prefix.$prototype.' '.$i;
            $tmp = $db->GetOne($query,array($name));
            if( !$tmp ) return $name;
        }
        throw new CmsLogicException('Could not generate a template name for '.$prototype);
    }

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
        if( $tmp ) throw new CmsInvalidDataException('Template with the same name already exists.');
    }

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
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

        if( $tpl->get_type_dflt() ) {
            // if it's default for a type, unset default flag for all other records with this type
            $query = 'UPDATE '.$this->template_table_name().' SET type_dflt = 0 WHERE type_id = ? AND type_dflt = 1 AND id != ?';
            $dbr = $db->Execute($query,array($tpl->get_type_id(),$tpl->get_id()));
            if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        }

        $query = 'DELETE FROM '.$this->tpl_additional_users_table_name().' WHERE tpl_id = ?';
        $dbr = $db->Execute($query,array($tpl->get_id()));
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());

        $t = $tpl->get_additional_editors();
        if( is_array($t) && count($t) ) {
            $query = 'INSERT INTO '.$this->tpl_additional_users_table_name().' (tpl_id,user_id) VALUES(?,?)';
            foreach( $t as $one ) {
                $dbr = $db->Execute($query,array($tpl->get_id(),(int)$one));
            }
        }

        $query = 'DELETE FROM '.CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE.' WHERE tpl_id = ?';
        $dbr = $db->Execute($query,array($tpl->get_id()));
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
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
        if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
        $new_id = $db->Insert_ID();

        if( $tpl->get_type_dflt() ) {
            // if it's default for a type, unset default flag for all other records with this type
            $query = 'UPDATE '.$this->template_table_name().' SET type_dflt = 0 WHERE type_id = ? AND type_dflt = 1 AND id != ?';
            $dbr = $db->Execute($query,[ $tpl->get_type_id(), $new_id ]);
            if( !$dbr ) throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
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
        $arr = $tpl->_get_array();
        $tpl = $tpl::_load_from_data($arr);
        audit($new_id,'CMSMS','Template '.$tpl->get_name().' Created');
        return $tpl;
    }

    public function save_template( CmsLayoutTemplate $tpl )
    {
        if( $tpl->get_id() ) {
            $this->hook_manager->do_hook('Core::EditTemplatePre', [ get_class($tpl) => &$tpl ] );
            $tpl = $this->_update_template($tpl);
            $this->hook_manager->do_hook('Core::EditTemplatePost', [ get_class($tpl) => &$tpl ] );
            return;
        }

        $this->hook_manager->do_hook('Core::AddTemplatePre', [ get_class($tpl) => &$tpl ] );
        $tpl = $this->_insert_template($tpl);
        $this->hook_manager->do_hook('Core::AddTemplatePost', [ get_class($tpl) => &$tpl ] );
    }

    public function delete_template( CmsLayoutTemplate $tpl )
    {
        if( !$tpl->get_id() ) return;

        $this->hook_manager->do_hook('Core::DeleteTemplatePre', [ get_class($tpl) => &$tpl ] );
        $db = $this->db;
        $query = 'DELETE FROM '.CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE.' WHERE tpl_id = ?';
        $dbr = $db->Execute($query,array($tpl->get_id()));

        $query = 'DELETE FROM '.$this->template_table_name().' WHERE id = ?';
        $dbr = $db->Execute($query,array($tpl->get_id()));

        @unlink($tpl->get_content_filename());

        audit($tpl->get_id(),'CMSMS','Template '.$tpl->get_name().' Deleted');
        $this->hook_manager->do_hook('Core::DeleteTemplatePost', [ get_class($tpl) => &$tpl ] );
        $this->cache_driver->clear(__CLASS__);
    }

    public function load_template($a)
    {
        $id = null;
        if( is_numeric($a) && $a > 0 ) {
            $id = $a;
        }
        else if( is_string($a) && strlen($a) > 0 ) {
            $id = $this->template_name_to_id($a);
        }

        // if it exists in the cache, then we're done
        $obj = $this->get_cached_template($id);
        if( $obj ) return $obj;

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

        $list2 = array_diff($list,$this->get_cached_templates());
        if( is_array($list2) && count($list2) > 0 ) {
            // have to load these items and put them in the cache.
            $db = $this->db;
            $str = implode(',',$list2);
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
                    // put it in the cache, we'll get it in a bit.
                    $this->set_template_cached($obj);
                }
                // cache it
            }
        }

        // read from the cache
        $out = null;
        foreach( $list as $tpl_id ) {
            $out[] = $this->get_cached_template($tpl_id);
        }
        return $out;
    }

    public function load_templates_by_type(CmsLayoutTemplateType $type)
    {
        // get the template type id => template_id list
        // see if we have this map in the cache
        $map = null;
        $key = 'types_to_tpl_'.$type->get_id();
        if( $this->cache_driver->exists($key,__CLASS__) ) {
            $map = $this->cache_driver->get($key,__CLASS__);
        } else {
            $sql = 'SELECT id FROm '.$this->template_table_name().' WHERE id = ?';
            $list = $db->GetCol($sql,$type->get_id());
            if( is_array($list) && !empty($list) ) {
                $map = $list;
                $this->cache_driver->set($key,$list,__CLASS__);
            }
        }

        if( empty($map) ) return;

        return $this->load_bulk_templates($map);
    }

    public function get_owned_templates($a)
    {
        $n = $this->_resolve_user($a);
        if( $n <= 0 ) throw new CmsInvalidDataException('Invalid user specified to get_owned_templates');

        $query = new CmsLayoutTemplateQuery(array('u'=>$n));
        $tmp = $query->GetMatchedTemplateIds();
        return $this->load_bulk_templates($tmp);
    }

    public function get_editable_templates($a)
    {
        $n = $this->_resolve_user($a);
	if( $n <= 0 ) throw new CmsInvalidDataException('Invalid user specified to get_owned_templates');
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

    public function load_all_templates_by_type(CmsLayoutTemplateType $type)
    {
        $sql = 'SELECT id FROM '.$this->template_table_name().' WHERE type_id = ?';
        $tmp = $db->GetCol($sql, [ $type->get_id() ] );
        if( !$tmp ) return;

        return $this->load_bulk_templates($tmp);
    }

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

        if( !$t2 ) throw new CmsInvalidDataException('Invalid data passed to CmsLayoutTemplate::;load_dflt_by_type()');

        // search our preloaded template first
        $tpl_id = $this->get_default_template_by_type($t2->get_id());
        if( $tpl_id ) return $this->load_template($tpl_id);
    }

    public function template_table_name() {
        return CMS_DB_PREFIX.'layout_templates';
    }

    public function designs_table_name() {
        return CMS_DB_PREFIX.'layout_designs';
    }

    public function tpl_additional_users_table_name() {
        return CMS_DB_PREFIX.'layout_tpl_addusers';
    }

    public function design_assoc_table_name() {
        return CMS_DB_PREFIX.'layout_design_tplassoc';
    }

} // class
