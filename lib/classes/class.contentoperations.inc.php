<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id$

use CMSMS\internal\global_cache;

/**
 * Content related functions.
 *
 * @package CMS
 * @license GPL
 */

/**
 * Include the content class definition
 */

/**
 * Class for static methods related to content
 *
 * @abstract
 * @since 0.8
 * @package CMS
 * @license GPL
 */
class ContentOperations
{

    /**
     * @ignore
     */
    private $_quickfind;

    /**
     * @ignore
     */
    private $_content_types;

    /**
     * @ignore
     */
    private static $_instance;

    /**
     * @ignore
     */
    private $_authorpages;

    /**
     * @ignore
     */
    private $_ownedpages;

    /**
     * @ignore
     */
    private $_cache;

    /**
     * @ignore
     */
    private $_app;

    /**
     * @ignore
     */
    public function __construct( CmsApp $app, cms_cache_driver $driver )
    {
        if( self::$_instance ) throw new \LogicException('Only one instance of '.__CLASS__.' allowed');
        $this->_app = $app;
        $this->_cache = $driver;
        $this->setup_cache();
        self::$_instance = $this;
    }

    /**
     * Return a reference to the only allowed instance of this singleton object
     *
     * @return ContentOperations
     */
    public static function get_instance()
    {
        if( !is_object( self::$_instance) ) throw new \LogicException('Instance of '.__CLASS__.' not yet created');
        return self::$_instance;
    }

    /**
     * @ignore
     */
    private function setup_cache()
    {
        // two caches, the flat list, and the tree
        $db = $this->_app->GetDb();
        $obj = new \CMSMS\internal\global_cachable('content_flatlist',
                                                   function() use ($db) {
                                                         $query = 'SELECT content_id,parent_id,item_order,content_alias,active
                                                                   FROM '.CMS_DB_PREFIX.'content ORDER BY hierarchy ASC';
                                                         return $db->GetArray($query);
                                                    });
        global_cache::add_cachable($obj);

        // an index of the content pages, by alias
        $obj = new \CMSMS\internal\global_cachable('content_aliasmap',
                                                    function() {
                                                       $flatlist = global_cache::get('content_flatlist');
                                                       $out = null;
                                                       foreach( $flatlist as $row ) {
                                                           $alias = $row['content_alias'];
                                                           if( $alias ) $out[$alias] = (int) $row['content_id'];
                                                       }
                                                       return $out;
                                                    });

        // a content tree
        $obj = new \CMSMS\internal\global_cachable('content_tree',
                                                    function(){
                                                         $flatlist = global_cache::get('content_flatlist');

                                                         // todo, embed this herer
                                                         $tree = \cms_tree_operations::load_from_list($flatlist);
                                                         return $tree;
                                                    });
        global_cache::add_cachable($obj);

        // a quick index of the content tree, by name
        $obj = new \CMSMS\internal\global_cachable('content_quicklist',
                                                    function(){
                                                         $tree = global_cache::get('content_tree');
                                                         return $tree->getFlatList();
                                                    });

        global_cache::add_cachable($obj);
    }


    /**
     * @internal
     */
    protected function get_cached_content(int $pageid)
    {
        if( $this->_cache->exists($pageid,__CLASS__) ) return $this->_cache->get($pageid,__CLASS__);
    }


    /**
     * @internal
     */
    protected function put_cached_content(ContentBase $contentobj)
    {
        $cached_ids = $this->_cache->get('PAGELIST',__CLASS__);
        $cached_ids[] = $contentobj->Id();
        $cached_ids = array_unique($cached_ids);
        $res = $this->_cache->set((int) $contentobj->Id(), $contentobj, __CLASS__);
        $this->_cache->set('PAGELIST',$cached_ids,__CLASS__);
    }

    /**
     * @internal
     */
    protected function alias_to_id(string $alias)
    {
        $alias_map = global_cache::get('content_flatlist');
        if( $alias_map ) {
            foreach( $alias_map as $row ) {
                if( $row['content_alias'] == $alias ) return $row['content_id'];
            }
        }
    }

    /**
     * @internal
     */
    protected function get_cached_page_ids()
    {
        return $this->_cache->get('PAGELIST',__CLASS__);
    }

    /**
     * @internal
     */
    protected function is_cached($id) : bool
    {
        $id = (int) $id;
        return $this->_cache->content_exists($id);
    }

    /**
     * Return a content object for the currently requested page.
     *
     * @since 1.9
     * @return getContentObject()
     */
    public function getContentObject()
    {
        return $this->_aqp->get_content_object();
    }


    /**
     * Given an array of content_type and seralized_content, reconstructs a
     * content object.  It will handled loading the content type if it hasn't
     * already been loaded.
     *
     * Expects an associative array with 2 elements:
     *   content_type: string A content type name
     *   serialized_content: string Serialized form data
     *
     * @see ContentBase::ListContentTypes
     * @param  array $data
     * @return ContentBase A content object derived from ContentBase
     */
    public function &LoadContentFromSerializedData(&$data)
    {
        if( !isset($data['content_type']) && !isset($data['serialized_content']) ) return FALSE;

        $contenttype = 'content';
        if( isset($data['content_type']) ) $contenttype = $data['content_type'];

        $contentobj = $this->CreateNewContent($contenttype);
        $contentobj = unserialize($data['serialized_content']);
        return $contentobj;
    }


    /**
     * Load a specific content type
     *
     * @internal
     * @access private
     * @final
     * @since 1.9
     * @param mixed The type.  Either a string type name, class name, or an instance of CmsContentTypePlaceHolder
     */
    final public function LoadContentType($type)
    {
        die('remove me '.__METHOD__);
        if( is_object($type) && $type instanceof CmsContentTypePlaceHolder ) $type = $type->type;

        $ctph = $this->_get_content_type($type);
        if( is_object($ctph) ) {
            if( !class_exists( $ctph->class ) && file_exists( $ctph->filename ) ) include_once( $ctph->filename );
        }

        return $ctph;
    }

    /**
     * Creates a new, empty content object of the given type.
     *
     * if the content type is registered with the system,
     * and the class does not exist, the appropriate filename will be included
     * and then, if possible a new object of the designated type will be
     * instantiated.
     *
     * @param mixed $type The type.  Either a string, or an instance of CmsContentTypePlaceHolder
     * @return ContentBase (A valid object derived from ContentBase)
     */
    public function &CreateNewContent($type)
    {
        if( is_object($type) && $type instanceof CmsContentTypePlaceHolder ) $type = $type->type;
        $result = NULL;

        $ctph = $this->_get_content_type($type);
        if( is_object($ctph) && class_exists($ctph->class) ) $result = new $ctph->class;
        return $result;
    }


    /**
     * Given a content id, load and return the loaded content object.
     *
     * @param int $id The id of the content object to load
     * @param bool $loadprops Also load the properties of that content object. Defaults to false.
     * @return mixed The loaded content object. If nothing is found, returns void
     */
    public function LoadContentFromId(int $id = null)
    {
        $id = (int) $id;
        if( $id < 1 ) $id = $this->GetDefaultContent();
        $obj = $this->get_cached_content($id);
        if( $obj && is_a($obj,'ContentBase') ) {
            return $obj;
        }

        $db = $this->_app->GetDb();
        $query = "SELECT * FROM ".CMS_DB_PREFIX."content WHERE content_id = ?";
        $row = $db->GetRow($query, array($id));
        if ($row) {
            $classtype = strtolower($row['type']);
            $contentobj = $this->CreateNewContent($classtype);
            if ($contentobj) {
                $contentobj->LoadFromData($row, true);
                $contentobj->HasProperty('junk'); // forces property load.
                $this->put_cached_content($contentobj);
                return $contentobj;
            }
        }
    }


    public function SmartLoadContentFromAlias(string $alias)
    {
        $id = (int) $alias;
        if( !is_numeric($alias) || (int) $alias < 1 ) {
            $id = $this->alias_to_id($alias);
        }

        // get this page's node and call getcontent
        // this causes loadChildren to be called with the parent id
        // which forces all properties to load, and caches all siblings as well.
        $node = $this->quickfind_node_by_id($id);
        if( !$node ) trigger_error('could not get node for id '.$id);
        return $node->getContent(true,true);
    }

    /**
     * Given a content alias, load and return the loaded content object.
     *
     * @param int $alias The alias of the content object to load
     * @param bool $only_active If true, only return the object if it's active flag is true. Defaults to false.
     * @return ContentBase The loaded content object. If nothing is found, returns NULL.
     */
    public function LoadContentFromAlias(string $alias, bool $only_active = false)
    {
        $id = (int) $alias;
        if( !is_numeric($alias) || (int) $alias < 1 ) {
            $id = $this->alias_to_id($alias);
        }
        $obj = $this->get_cached_content($id);
        if( !$obj ) $obj = $this->LoadContentFromId($id);
        if( !$only_active || $obj->Active() ) return $obj;
    }


    /**
     * Returns the id of the content marked as default.
     *
     * @return int The id of the default content page
     */
    public function GetDefaultContent()
    {
        return global_cache::get('default_content');
    }


    /**
     * Load standard CMS content types
     *
     * This internal method looks through the contenttypes directory
     * and loads the placeholders for them.
     *
     * @since 1.9
     * @access private
     * @internal
     */
    private function _get_std_content_types()
    {
        $result = array();
        $dir = __DIR__.'/contenttypes';
        $files = glob($dir.'/*.inc.php');
        if( is_array($files) ) {
            foreach( $files as $one ) {
                $obj = new CmsContentTypePlaceHolder();
                $class = basename($one,'.inc.php');
                $type  = strtolower($class);

                $obj->class = $class;
                $obj->type = strtolower($class);
                $obj->filename = $one;
                $obj->loaded = false;
                if( $obj->type == 'link' ) {
                    // cough... big hack... cough.
                    $obj->friendlyname_key = 'contenttype_redirlink';
                }
                else {
                    $obj->friendlyname_key = 'contenttype_'.$obj->type;
                }
                $result[$type] = $obj;
            }
        }

        return $result;
    }


    /**
     * @ignore
     */
    private function _get_content_types()
    {
        if( !is_array($this->_content_types) ) {
            // get the standard ones.
            $this->_content_types = $this->_get_std_content_types();

            // get the list of modules that have content types.
            // and load them.  content types from modules are
            // registered in the constructor.
            /*
                content types registered in the contructor... should not need this.

            $module_list = ModuleOperations::get_instance()->get_modules_with_capability(CmsCoreCapabilities::CONTENT_TYPES);
            if( is_array($module_list) && count($module_list) ) {
            foreach( $module_list as $module_name ) {
            cms_utils::get_module($module_name);
            }
            }
            */
        }

        return $this->_content_types;
    }


    /**
     * Function to return a content type given it's name
     *
     * @since 1.9
     * @access private
     * @internal
     * @param string The content type name or classname
     * @return CmsContentTypePlaceHolder placeholder object.
     */
    private function _get_content_type(string $name)
    {
        $name = strtolower($name);
        $this->_get_content_types();
        if( is_array($this->_content_types) ) {
            if( isset($this->_content_types[$name]) && $this->_content_types[$name] instanceof CmsContentTypePlaceHolder ) {
                return $this->_content_types[$name];
            }
            foreach( $this->_content_types as $typename => $obj ) {
                if( $obj instanceof CmsContentTypePlaceHolder && $obj->class == $name ) return $obj;
            }
        }
    }


    /**
     * Register a new content type
     *
     * @since 1.9
     * @param CmsContentTypePlaceHolder Reference to placeholder object
     */
    public function register_content_type(CmsContentTypePlaceHolder $obj)
    {
        $this->_get_content_types();
        if( isset($this->_content_types[$obj->type]) ) return FALSE;

        if( !class_exists( $obj->class ) && file_exists( $obj->filename ) ) require_once $obj->filename;
        $this->_content_types[$obj->type] = $obj;
        return TRUE;
    }


    /**
     * Get a content type placeholder based on a classname of the content type
     *
     * @since 2.3
     * @internal
     * @param string $classname The class name of the placeholder
     * @return CmsContentTypePlaceHolder
     */
    public function GetContentTypePlaceholderByClassname( string $classname )
    {
        $this->_get_content_types();
        if( $this->_content_types ) {
            foreach( $this->_content_types as $type => $obj ) {
                if( $obj->class == $classname ) return $obj;
            }
        }
    }

    /**
        * Returns a hash of valid content types (classes that extend ContentBase)
        * The key is the name of the class that would be saved into the database.  The
        * value would be the text returned by the type's FriendlyName() method.
     *
     * @param bool $byclassname optionally return keys as class names.
     * @param bool $allowed optionally trim the list of content types that are allowed by the site preference.
     * @param bool $system return only system content types.
     * @return array List of content types registered in the system.
     */
    public function ListContentTypes(bool $byclassname = false,bool $allowed = false,bool $system = FALSE)
    {
        $disallowed_a = array();
        $tmp = cms_siteprefs::get('disallowed_contenttypes');
        if( $tmp ) $disallowed_a = explode(',',$tmp);

        $this->_get_content_types();
        $types = $this->_content_types;
        if ( isset($types) ) {
            $result = array();
            foreach( $types as $obj ) {
                global $CMS_ADMIN_PAGE;
                if( !isset($obj->friendlyname) && isset($obj->friendlyname_key) && isset($CMS_ADMIN_PAGE) ) {
                    $txt = lang($obj->friendlyname_key);
                    $obj->friendlyname = $txt;
                }
                if( !$allowed || count($disallowed_a) == 0 || !in_array($obj->type,$disallowed_a) ) {
                    if( $byclassname ) {
                        $result[$obj->class] = $obj->friendlyname;
                    }
                    else {
                        $result[$obj->type] = $obj->friendlyname;
                    }
                }
            }
            return $result;
        }
    }

    /**
     * Updates the hierarchy position of one item
	 *
	 * @internal
     * @ignore
	 * @param integer $contentid The content id to update
	 * @param array $hash A hash of all content objects (only certain fields)
	 * @return array|null
     */
    private function _set_hierarchy_position(int $content_id,array $hash)
    {
        $row = $hash[$content_id];
        $saved_row = $row;
        $hier = $idhier = $pathhier = '';
        $current_parent_id = $content_id;

        while( $current_parent_id > 0 ) {
            $item_order = max($row['item_order'],1);
            $hier = str_pad($item_order, 5, '0', STR_PAD_LEFT) . "." . $hier;
            $idhier = $current_parent_id . '.' . $idhier;
            $pathhier = $row['alias'] . '/' . $pathhier;
            $current_parent_id = $row['parent_id'];
            if( $current_parent_id < 1 ) break;
            $row = $hash[$current_parent_id];
        }

        if (strlen($hier) > 0) $hier = substr($hier, 0, strlen($hier) - 1);
        if (strlen($idhier) > 0) $idhier = substr($idhier, 0, strlen($idhier) - 1);
        if (strlen($pathhier) > 0) $pathhier = substr($pathhier, 0, strlen($pathhier) - 1);

        // if we actually did something, return the row.
        static $_cnt;
        $a = ($hier == $saved_row['hierarchy']);
        $b = ($idhier == $saved_row['id_hierarchy']);
        $c = ($pathhier == $saved_row['hierarchy_path']);
        if( !$a || !$b || !$c ) {
            $_cnt++;
            $saved_row['hierarchy'] = $hier;
            $saved_row['id_hierarchy'] = $idhier;
            $saved_row['hierarchy_path'] = $pathhier;
            return $saved_row;
        }
    }


    /**
     * Updates the hierarchy position of all content items.
     * This is an expensive operation on the database, but must be called once
     * each time one or more content pages are updated if positions have changed in
     * the page structure.
     */
    public function SetAllHierarchyPositions()
    {
        // load some data about all pages into memory... and convert into a hash.
        $db = $this->_app->GetDb();
        $sql = 'SELECT content_id, parent_id, item_order, content_alias AS alias, hierarchy, id_hierarchy, hierarchy_path FROM '.CMS_DB_PREFIX.'content ORDER BY hierarchy';
        $list = $db->GetArray($sql);
        if( !count($list) ) {
            // nothing to do, get outa here.
            return;
        }
        $hash = array();
        foreach( $list as $row ) {
            $hash[$row['content_id']] = $row;
        }
        unset($list);

        // would be nice to use a transaction here.
        static $_n;
        $usql = "UPDATE ".CMS_DB_PREFIX."content SET hierarchy = ?, id_hierarchy = ?, hierarchy_path = ? WHERE content_id = ?";
        foreach( $hash as $content_id => $row ) {
            $changed = $this->_set_hierarchy_position($content_id,$hash);
            if( is_array($changed) ) {
                $db->Execute($usql, array($changed['hierarchy'], $changed['id_hierarchy'], $changed['hierarchy_path'], $changed['content_id']));
            }
        }

        $this->SetContentModified();
    }


    /**
     * Get the date of last content modification
     *
     * @since 2.0
     * @return unix timestamp representing the last time a content page was modified.
     */
    public function GetLastContentModification()
    {
        return global_cache::get('latest_content_modification');
    }

    /**
     * Set the last modified date of content so that on the next request the content cache will be loaded from the database
     *
     * @internal
     * @access private
     */
    public function SetContentModified()
    {
        global_cache::clear('latest_content_modification');
        global_cache::clear('default_content');
        global_cache::clear('content_flatlist');
        global_cache::clear('content_tree');
        global_cache::clear('content_quicklist');
        global_cache::clear('content_aliasmap');
        $this->_cache->clear(__CLASS__);
    }

    /**
     * Loads a set of content objects into the cached tree.
     *
     * @return cms_content_tree The cached tree of content
        * @deprecated
     */
    public function GetAllContentAsHierarchy()
    {
        return global_cache::get('content_tree');
    }


    /**
     * Load All content in the database into memory
     * Use with caution this can chew up alot of memory on larger sites.
     *
     * @param bool $loadprops Load extended content properties or just the page structure and basic properties
     * @param bool $inactive  Load inactive pages as well
     * @param bool $showinmenu Load pages marked as show in menu
     */
    public function LoadAllContent(bool $loadprops = FALSE,bool $inactive = FALSE,bool $showinmenu = FALSE)
    {
        static $_loaded = 0;
        if( $_loaded == 1 ) return;
        $_loaded = 1;

        $db = $this->_app->GetDb();

        $expr = array();
        $parms = array();
        if( !$inactive ) {
            $expr[] = 'active = ?';
            $parms[] = 1;
        }
        if( $showinmenu ) {
            $expr[] = 'show_in_menu = ?';
            $parms[] = 1;
        }

        $cached_ids = $this->get_cached_page_ids();
        if( is_array($cached_ids) && count($cached_ids) ) {
            $expr[] = 'content_id NOT IN ('.implode(',',$cached_ids).')';
        }

        $query = 'SELECT * FROM '.CMS_DB_PREFIX.'content FORCE INDEX ('.CMS_DB_PREFIX.'index_content_by_idhier) WHERE ';
        $query .= implode(' AND ',$expr);
        $dbr = $db->Execute($query,$parms);

        if( $loadprops ) {
            $child_ids = array();
            while( !$dbr->EOF() ) {
                $child_ids[] = $dbr->fields['content_id'];
                $dbr->MoveNext();
            }
            $dbr->MoveFirst();

            $tmp = null;
            if( count($child_ids) ) {
                // get all the properties for the child_ids
                $query = 'SELECT * FROM '.CMS_DB_PREFIX.'content_props WHERE content_id IN ('.implode(',',$child_ids).') ORDER BY content_id';
                $tmp = $db->GetArray($query);
            }

            // re-organize the tmp data into a hash of arrays of properties for each content id.
            if( $tmp ) {
                $contentprops = array();
                for( $i = 0, $n = count($tmp); $i < $n; $i++ ) {
                    $content_id = $tmp[$i]['content_id'];
                    if( in_array($content_id,$child_ids) ) {
                        if( !isset($contentprops[$content_id]) ) $contentprops[$content_id] = array();
                        $contentprops[$content_id][] = $tmp[$i];
                    }
                }
                unset($tmp);
            }
        }

        $content_types = array_keys($this->ListContentTypes());
        while( !$dbr->EOF() ) {
            $row = $dbr->fields;
            $id = $row['content_id'];

            if( in_array($row['type'], $content_types)) {
                $contentobj = $this->CreateNewContent($row['type']);
                if ($contentobj) {
                    $contentobj->LoadFromData($row, false);
                    if( $loadprops && $contentprops && isset($contentprops[$id]) ) {
                          // load the properties from local cache.
                          $props = $contentprops[$id];
                        foreach( $props as $oneprop ) {
                            $contentobj->SetPropertyValueNoLoad($oneprop['prop_name'],$oneprop['content']);
                        }
                    }

                    // cache the content objects
                    $this->put_cached_content($contentobj);
                }
            }

            $dbr->MoveNext();
        }
        $dbr->Close();
    }

    /**
     * Loads additional, active children into a given tree object.
        *
        * This is a smart method that determines what needs to be loaded, and adds them to the content cache
        * the idea is to optimize memory by requests.
        *
        * Note: if the cached content object was not loaded deep.. this method will not load its properties.
     *
     * @param int $id The parent of the content objects to load into the tree
     * @param bool $loadprops If true, load the properties of all loaded content objects
     * @param bool $all If true, load all content objects, even inactive ones.
     * @param array   $explicit_ids (optional) array of explicit content ids to load
     * @author Ted Kulp
     */
    public function LoadChildren(int $id = null, bool $loadprops = false, bool $all = false, array $explicit_ids = [] )
    {
        if( !$id && !is_array($explicit_ids) && !count($explicit_ids) ) {
            throw new \LogicException('Invalid arguments passed to '.__METHOD__);
        }

        // gotta load it
        $db = $this->_app->GetDb();

        $contentrows = null;
        if( (!is_array($explicit_ids) || !count($explicit_ids)) ) {
            $node = null;
            if( $id < 1 ) {
                $node = $this->GetAllContentAsHierarchy();
            } else {
                $node = $this->quickfind_node_by_id($id);
                $explicit_ids[] = $id;
            }
            $children = $node->get_children();
            for( $i = 0, $n = count($children); $i < $n; $i++ ) {
                if( $all || $children[$i]->get_tag('active') ) $explicit_ids[] = $children[$i]->get_tag('id');
            }
        }

        // now have list of explicit ids, find which ones we have to load frm the database.
        if( is_array($explicit_ids) && count($explicit_ids) ) {
            $cached_ids = $this->get_cached_page_ids();
            if( is_array($cached_ids) && count($cached_ids) ) $explicit_ids = array_diff($explicit_ids,$cached_ids);
        }
        if( !is_array($explicit_ids) || !count($explicit_ids) ) return;

        // there is stuff we gotta load
        $expr = 'content_id IN ('.implode(',',$explicit_ids).')';
        if( !$all ) $expr .= ' AND active = 1';

        // note, this is mysql specific...
        $query = 'SELECT * FROM '.CMS_DB_PREFIX.'content FORCE INDEX ('.CMS_DB_PREFIX.'index_content_by_idhier) WHERE '.$expr.' ORDER BY hierarchy';
        $contentrows = $db->GetArray($query);

        // get the content ids from the returned data
        $contentprops = null;
        if( true /* loadprops */ ) {
            $child_ids = [];
            for( $i = 0, $n = count($contentrows); $i < $n; $i++ ) {
                $child_ids[] = $contentrows[$i]['content_id'];
            }

            $tmp = null;
            if( count($child_ids) ) {
                // get all the properties for the child_ids
                $query = 'SELECT * FROM '.CMS_DB_PREFIX.'content_props WHERE content_id IN ('.implode(',',$child_ids).') ORDER BY content_id';
                $tmp = $db->GetArray($query);
            }

            // re-organize the tmp data into a hash of arrays of properties for each content id.
            if( $tmp ) {
                $contentprops = array();
                for( $i = 0, $n = count($tmp); $i < $n; $i++ ) {
                    $content_id = $tmp[$i]['content_id'];
                    if( in_array($content_id,$child_ids) ) {
                        if( !isset($contentprops[$content_id]) ) $contentprops[$content_id] = array();
                        $contentprops[$content_id][] = $tmp[$i];
                    }
                }
                unset($tmp);
            }
        }

        // build the content objects
        for( $i = 0, $n = count($contentrows); $i < $n; $i++ ) {
            $row =& $contentrows[$i];
            $id = $row['content_id'];

            if (!in_array($row['type'], array_keys($this->ListContentTypes()))) continue;
            $contentobj = new Content();
            $contentobj = $this->CreateNewContent($row['type']);

            if ($contentobj) {
                $contentobj->LoadFromData($row, false);
                if( $contentprops && isset($contentprops[$id]) ) {
                    // load properties into the content objects.
                    foreach( $contentprops[$id] as $oneprop ) {
                        $contentobj->SetPropertyValueNoLoad($oneprop['prop_name'],$oneprop['content']);
                    }
                    unset($contentprops[$id]);
                }

                // cache the content objects
                $this->put_cached_content($contentobj);
                unset($contentobj);
            }
        }
    }

    /**
     * Sets the default content to the given id
     *
     * @param int $id The id to set as default
     * @author Ted Kulp
     */
    public function SetDefaultContent(int $id)
    {
        $db = $this->_app->GetDb();

        $sql = 'UPDATE '.CMS_DB_PREFIX."content SET default_content=0 WHERE default_content=1";
        $db->Execute( $sql );
        $one = $this->LoadContentFromId($id);
        $one->SetDefaultContent(true);
        $one->Save();
        $this->SetContentModified();
    }


    /**
     * Returns an array of all content objects in the system, active or not.
     *
     * Caution:  it is entirely possible that this method (and other similar methods of loading content) will result in a memory outage
     * if there are large amounts of content objects AND/OR large amounts of content properties.  Use with caution.
     *
     * @param bool $loadprops Not implemented
     * @return array The array of content objects
     */
    public function GetAllContent()
    {
        debug_buffer('get all content...');
        $gCms = $this->_app;
        $tree = $gCms->GetHierarchyManager();
        $list = $tree->getFlatList();

        $this->LoadAllContent($loadprops);
        $output = [];
        foreach( $list as &$one ) {
            $tmp = $one->GetContent(false,true,true);
            if( is_object($tmp) ) $output[] = $tmp;
        }

        debug_buffer('end get all content...');
        return $output;
    }


    /**
     * Create a hierarchical ordered dropdown of all the content objects in the system for use
     * in the admin and various modules.  If $current or $parent variables are passed, care is taken
     * to make sure that children which could cause a loop are hidden, in cases of when you're creating
     * a dropdown for changing a content object's parent.
         *
          * This method was rewritten for 2.0 to use the jquery hierselector plugin to better accommodate larger websites.
         *
            * Since many parameters are now ignored, A new method needs to be writtent o replace this archaic method...
            * so consider this method to be deprecateed.
     *
            * @deprecated
     * @param int $current The id of the content object we are working with.  Used with allowcurrent to not show children of the current conrent object, or itself.
     * @param int $value The id of the currently selected content object.
     * @param string $name The html name of the dropdown.
     * @param bool $allowcurrent Ensures that the current value cannot be selected, or $current and it's childrern.  Used to prevent circular deadlocks.
     * @param bool $use_perms If true, checks authorship permissions on pages and only shows those the current user has authorship of (can edit)
     * @param bool $ignore_current (ignored as of 2.0) (Before 2.2 this parameter was called ignore_current
     * @param bool $allow_all If true, show all items, even if the content object doesn't have a valid link. Defaults to false.
            * @param bool $for_child If true, assume that we want to add a new child and obey the WantsChildren flag of each content page. (new in 2.2).
     * @return string The html dropdown of the hierarchy.
     */
    public function CreateHierarchyDropdown($current = '', $value = '', $name = 'parent_id', $allowcurrent = 0,
									 $use_perms = 0, $ignore_current = 0, $allow_all = false, $for_child = false )
    {
        static $count = 0;
        $count++;
        $id = 'cms_hierdropdown'.$count;
        $value = (int) $value;
        $uid = get_userid(FALSE);

        $out = "<input type=\"text\" title=\"".lang('title_hierselect')."\" name=\"{$name}\" id=\"{$id}\" class=\"cms_hierdropdown\" value=\"{$value}\" size=\"50\" maxlength=\"50\"/>";
        $opts = array();
        $opts['current'] = $current;
        $opts['value'] = $value;
        $opts['allowcurrent'] = ($allowcurrent)?'true':'false';
        $opts['allow_all'] = ($allow_all)?'true':'false';
        $opts['use_perms'] = ($use_perms)?'true':'false';
        	$opts['for_child'] = ($for_child)?'true':'false';
        	$opts['use_simple'] = !(check_permission($uid,'Manage All Content') || check_permission($uid,'Modify Any Page'));
        	$opts['is_manager'] = !$opts['use_simple'];
        $str = '{';
        foreach($opts as $key => $val) {
            if( $val == '' ) continue;
            $str .= $key.': '.$val.',';
        }
        $str = substr($str,0,-1).'}';
        $out .= "<script type=\"text/javascript\">$(function(){ $('#$id').hierselector($str) });</script>";
        return $out;
    }

    /**
     * Gets the content id of the page marked as default
     *
     * @return int The id of the default page. false if not found.
     */
    public function GetDefaultPageID()
    {
        return $this->GetDefaultContent();
    }


    /**
     * Returns the content id given a valid content alias.
     *
     * @param string $alias The alias to query
     * @return int The resulting id.  null if not found.
     */
    public function GetPageIDFromAlias( string $alias )
    {
        $hm = $this->_app->GetHierarchyManager();
        $node = $hm->sureGetNodeByAlias($alias);
        if( $node ) return $node->get_tag('id');
    }


    /**
     * Returns the content id given a valid hierarchical position.
     *
     * @param string $position The position to query
     * @return int The resulting id.  false if not found.
     */
    public function GetPageIDFromHierarchy( string $position )
    {
        $gCms = $this->_app;
        $db = $gCms->GetDb();

        $query = "SELECT content_id FROM ".CMS_DB_PREFIX."content WHERE hierarchy = ?";
        $row = $db->GetRow($query, array($this->CreateUnfriendlyHierarchyPosition($position)));

        if (!$row) return false;
        return $row['content_id'];
    }


    /**
     * Returns the content alias given a valid content id.
     *
     * @param int $id The content id to query
     * @return string The resulting content alias.  false if not found.
     */
    public function GetPageAliasFromID( int $id )
    {
        $node = $this->quickfind_node_by_id($id);
        if( $node ) return $node->getTag('alias');
    }


    /**
     * Check if a content alias is used
     *
     * @param string $alias The alias to check
     * @param int $content_id The id of hte current page, if any
     * @return bool
     * @since 2.2.2
     */
    public function CheckAliasUsed(string $alias,int $content_id = -1)
    {
        $alias = trim($alias);
        $content_id = (int) $content_id;

        $params = [ $alias ];
        $query = "SELECT content_id FROM ".CMS_DB_PREFIX."content WHERE content_alias = ?";
        if ($content_id > 0) {
            $query .= " AND content_id != ?";
            $params[] = $content_id;
        }
        $db = $this->_app->GetDb();
        $out = (int) $db->GetOne($query, $params);
        if( $out > 0 ) return TRUE;
    }

    /**
     * Check if a potential alias is valid.
     *
     * @param string $alias The alias to check
     * @return bool
     * @since 2.2.2
     */
    public function CheckAliasValid(string $alias)
    {
        if( ((int)$alias > 0 || (float)$alias > 0.00001) && is_numeric($alias) ) return FALSE;
        $tmp = munge_string_to_url($alias,TRUE);
        if( $tmp != mb_strtolower($alias) ) return FALSE;
        return TRUE;
    }

    /**
     * Checks to see if a content alias is valid and not in use.
     *
     * @param string $alias The content alias to check
     * @param int $content_id The id of the current page, for used alias checks on existing pages
     * @return string The error, if any.  If there is no error, returns FALSE.
     */
    public function CheckAliasError(string $alias, int $content_id = -1)
    {
        if( !$this->CheckAliasValid($alias) ) return lang('invalidalias2');
        if ($this->CheckAliasUsed($alias,$content_id)) return lang('aliasalreadyused');
        return FALSE;
    }

    /**
     * Converts a friendly hierarchy (1.1.1) to an unfriendly hierarchy (00001.00001.00001) for
     * use in the database.
     *
     * @param string $position The hierarchy position to convert
     * @return string The unfriendly version of the hierarchy string
     */
    public function CreateFriendlyHierarchyPosition(string $position)
    {
        #Change padded numbers back into user-friendly values
        $tmp = '';
        $levels = explode('.',$position);

        foreach ($levels as $onelevel) {
            $tmp .= ltrim($onelevel, '0') . '.';
        }
        $tmp = rtrim($tmp, '.');
        return $tmp;
    }

    /**
     * Converts an unfriendly hierarchy (00001.00001.00001) to a friendly hierarchy (1.1.1) for
     * use in the database.
     *
     * @param string $position The hierarchy position to convert
     * @return string The friendly version of the hierarchy string
     */
    public function CreateUnfriendlyHierarchyPosition(string $position)
    {
        #Change user-friendly values into padded numbers
        $tmp = '';
        $levels = explode('.',$position);

        foreach ($levels as $onelevel) {
            $tmp .= str_pad($onelevel, 5, '0', STR_PAD_LEFT) . '.';
        }
        $tmp = rtrim($tmp, '.');
        return $tmp;
    }

    /**
     * Check if the supplied page id is a parent of the specified base page (or the current page)
     *
     * @since 2.0
     * @author Robert Campbell <calguy1000@hotmail.com>
     * @param int $test_id Page ID to test
     * @param int $base_id (optional) Page ID to act as the base page.  The current page is used if not specified.
     * @return bool
     */
    public function CheckParentage(int $test_id,int $base_id = null)
    {
        $gCms = $this->_app;
        if( !$base_id ) $base_id = $gCms->get_content_id();
        $base_id = (int)$base_id;
        if( $base_id < 1 ) return FALSE;

        $node = $this->quickfind_node_by_id($base_id);
        while( $node ) {
            if( $node->get_tag('id') == $test_id ) return TRUE;
            $node = $node->get_parent();
        }
        return FALSE;
    }

    /**
     * Return a list of pages that the user is owner of.
     *
     * @since 2.0
     * @author Robert Campbell <calguy1000@hotmail.com>
     * @param int $userid The userid
     * @return array Array of integer page id's
     */
    public function GetOwnedPages(int $userid)
    {
        if( !is_array($this->_ownedpages) ) {
            $this->_ownedpages = array();

            $db = $this->_app->GetDb();
            $query = 'SELECT content_id FROM '.CMS_DB_PREFIX.'content WHERE owner_id = ? ORDER BY hierarchy';
            $tmp = $db->GetCol($query,array($userid));
            $data = array();
            for( $i = 0, $n = count($tmp); $i < $n; $i++ ) {
                if( $tmp[$i] > 0 ) $data[] = $tmp[$i];
            }

            if( count($data) ) $this->_ownedpages = $data;
        }
        return $this->_ownedpages;
    }

    /**
     * Test if the user specified owns the specified page
     *
     * @param int $userid
     * @param int $pageid
     * @return bool
     */
    public function CheckPageOwnership(int $userid,int $pageid)
    {
        $pagelist = $this->GetOwnedPages($userid);
        return in_array($pageid,$pagelist);
    }

    /**
     * Return a list of pages that the user has edit access to.
     *
     * @since 2.0
     * @author Robert Campbell <calguy1000@hotmail.com>
     * @param int $userid The userid
     * @return int[] Array of page id's
     */
    public function GetPageAccessForUser(int $userid)
    {
        if( !is_array($this->_authorpages) ) {
            $this->_authorpages = array();
            $data = $this->GetOwnedPages($userid);

            // Get all of the pages this user has access to.
            $groups = $this->_app->GetUserOperations()->GetMemberGroups($userid);
            $list = array($userid);
            if( is_array($groups) && count($groups) ) {
                foreach( $groups as $group ) {
                    $list[] = $group * -1;
                }
            }

            $db = $this->_app->GetDb();
            $query = "SELECT A.content_id FROM ".CMS_DB_PREFIX."additional_users A
                      LEFT JOIN ".CMS_DB_PREFIX.'content B ON A.content_id = B.content_id
                      WHERE A.user_id IN ('.implode(',',$list).')
                      ORDER BY B.hierarchy';
            $tmp = $db->GetCol($query);
            for( $i = 0, $n = count($tmp); $i < $n; $i++ ) {
                if( $tmp[$i] > 0 && !in_array($tmp[$i],$data) ) $data[] = $tmp[$i];
            }

            if( count($data) ) asort($data);
            $this->_authorpages = $data;
        }
        return $this->_authorpages;
    }

    /**
     * Check if the specified user has the ability to edit the specified page id
     *
     * @param int $userid
     * @param int $contentid
     * @return bool
     */
    public function CheckPageAuthorship(int $userid,int $contentid)
    {
        $author_pages = $this->GetPageAccessForUser($userid);
        return in_array($contentid,$author_pages);
    }

    /**
     * Test if the specified user account has edit access to all of the peers of the specified page id
     *
     * @param int $userid
     * @param int $contentid
     * @return bool
     */
    public function CheckPeerAuthorship(int $userid,int $contentid)
    {
        if( check_permission($userid,'Manage All Content') ) return TRUE;

        $access = $this->GetPageAccessForUser($userid);
        if( !is_array($access) || count($access) == 0 ) return FALSE;

        $node = $this->quickfind_node_by_id($contentid);
        if( !$node ) return FALSE;
        $parent = $node->get_parent();
        if( !$parent ) return FALSE;

        $peers = $parent->get_children();
        if( is_array($peers) && count($peers) ) {
            for( $i = 0, $n = count($peers); $i < $n; $i++ ) {
                if( !in_array($peers[$i]->get_tag('id'),$access) ) return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * A convenience function to find a hierarchy node given the page id
        * This method will be moved to cms_content_tree at a later date.
     *
     * @param int $id The page id
     * @return cms_content_tree
     */
    public function quickfind_node_by_id(int $id)
    {
        $list = global_cache::get('content_quicklist');
        if( isset($list[$id]) ) return $list[$id];
    }
} // end of class
