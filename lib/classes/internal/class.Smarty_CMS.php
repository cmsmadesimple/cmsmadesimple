<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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
#$Id: content.functions.php 6863 2011-01-18 02:34:48Z calguy1000 $

/**
 * @package CMS
 */

/**
 * Extends the Smarty class for content.
 *
 * @package CMS
 * @since 0.1
 */
class Smarty_CMS extends CMSSmartyBase
{
    public $id; // <- triggers error without | do search why this is needed
    public $params; // <- triggers error without | do search why this is needed
    protected $_global_cache_id;
    private static $_instance;

    // this is deprecated
    private $_tpl_stack = []; // this is for simulating parent and child scopes while directly using \Smarty_CMS::fetch()

    /**
     * Constructor
     *
     * @param array The hash of CMSMS config settings
     */
    public function __construct()
    {
        parent::__construct();
        $this->direct_access_security = TRUE;

        global $CMS_INSTALL_PAGE;

        // Set template_c and cache dirs
        $this->setCompileDir(TMP_TEMPLATES_C_LOCATION);
        $this->setCacheDir(TMP_CACHE_LOCATION);
        $this->assignGlobal('app_name','CMSMS');

        if (CMS_DEBUG == true) $this->error_reporting = 'E_ALL';

        // set our own template class with some funky stuff in it
        // note, can get rid of the CMS_Smarty_Template class and the Smarty_Parser classes.
        $this->template_class = 'CMS_Smarty_Template';

        // common resources.
        $this->registerResource('module_db_tpl',new CMSModuleDbTemplateResource());
        $this->registerResource('module_file_tpl',new CMSModuleFileTemplateResource());
        $this->registerResource('cms_template',new CmsTemplateResource()); // <- Should proably be global and removed from parser?
        $this->registerResource('template',new CmsTemplateResource()); // <- Should proably be global and removed from parser? // deprecated
        $this->registerResource('cms_stylesheet',new CmsStylesheetResource());

        // register default plugin handler
        $this->registerDefaultPluginHandler(array(&$this, 'defaultPluginHandler'));

        // Load User Defined Tags
        $_gCms = CmsApp::get_instance();
        if( !$_gCms->test_state(CmsApp::STATE_INSTALL) ) {
            $utops = UserTagOperations::get_instance();
            $usertags = $utops->ListUserTags();

	    if( !empty( $usertags ) ) {
            	foreach( $usertags as $id => $name ) {
                	$function = $utops->CreateTagFunction($name);
                	$this->registerPlugin('function',$name,$function,false);
            	}
	    }	
        }

        $config = cms_config::get_instance();
        $this->addConfigDir($config['assets_path'].'/configs');
        $this->addPluginsDir($config['assets_path'].'/plugins');
        $this->addPluginsDir(cms_join_path(CMS_ROOT_PATH,'plugins')); // deprecated
        $this->addPluginsDir(cms_join_path(CMS_ROOT_PATH,'lib','plugins'));
        $this->addTemplateDir(cms_join_path(CMS_ROOT_PATH, 'lib', 'assets', 'templates'));

        if( $_gCms->is_frontend_request()) {
            $this->addTemplateDir($config['assets_path'].'/templates');

            // Check if we are at install page, don't register anything if so, cause nothing below is needed.
            if(isset($CMS_INSTALL_PAGE)) return;

            if (is_sitedown()) {
                $this->setCaching(false);
                $this->force_compile = true;
            }

            // Load resources
            $this->registerResource('tpl_top',new CmsTemplateResource('top'));
            $this->registerResource('tpl_head',new CmsTemplateResource('head'));
            $this->registerResource('tpl_body',new CmsTemplateResource('body'));
            $this->registerResource('content',new CMSContentTemplateResource());

            // just for frontend actions.
            $this->registerPlugin('compiler','content',array('CMS_Content_Block','smarty_compile_fecontentblock'),false);
            $this->registerPlugin('function','content_image','CMS_Content_Block::smarty_fetch_imageblock',false);
            $this->registerPlugin('function','content_module','CMS_Content_Block::smarty_fetch_moduleblock',false);
            $this->registerPlugin('function','process_pagedata','CMS_Content_Block::smarty_fetch_pagedata',false);

            // Autoload filters
            $this->autoloadFilters();

            // compile check can only be enabled, if using smarty cache... just for safety.
            if( \cms_siteprefs::get('use_smartycache',0) ) $this->setCompileCheck(\cms_siteprefs::get('use_smartycompilecheck',1));

            // Enable security object
            if( !$config['permissive_smarty'] ) $this->enableSecurity('CMSSmartySecurityPolicy');
        }
        else if($_gCms->test_state(CmsApp::STATE_ADMIN_PAGE)) {
            $this->setCaching(false);
            $config = cms_config::get_instance();
            $admin_dir = $config['admin_path'];
            $this->addPluginsDir($admin_dir.'/plugins');
            $this->setTemplateDir($admin_dir.'/templates');
            $this->setConfigDir($admin_dir.'/configs');;
        }
    }

    /**
     * get_instance method
     *
     * @return object $this
     */
    public static function &get_instance()
    {
        if( !self::$_instance ) {
            self::$_instance = new \Smarty_CMS;
        }
        return self::$_instance;
    }

    /**
     * Load filters from CMSMS plugins folder
     *
     * @return void
     */
    private function autoloadFilters()
    {
        $pre = array();
        $post = array();
        $output = array();

        foreach( $this->plugins_dir as $onedir ) {
            if( !is_dir($onedir) ) continue;

            $files = glob($onedir.'/*php');
            if( !is_array($files) || count($files) == 0 ) continue;

            foreach( $files as $onefile ) {
                $onefile = basename($onefile);
                $parts = explode('.',$onefile);
                if( !is_array($parts) || count($parts) != 3 ) continue;

                switch( $parts[0] ) {
                case 'output':
                    $output[] = $parts[1];
                    break;

                case 'prefilter':
                    $pre[] = $parts[1];
                    break;

                case 'postfilter':
                    $post[] = $parts[1];
                    break;
                }
            }
        }

        $this->autoload_filters = array('pre'=>$pre,'post'=>$post,'output'=>$output);
    }

    public function registerClass($a,$b)
    {
        if( $this->security_policy ) {
            $this->security_policy->static_classes[] = $a;
        }
        parent::registerClass($a,$b);
    }

    /**
     * Registers plugin to be used in templates
     *
     * @param string   $type       plugin type
     * @param string   $tag        name of template tag
     * @param callback $callback   PHP callback to register
     * @param bool  $cacheable  if true (default) this fuction is cachable
     * @param array    $cache_attr caching attributes if any
     * @return Smarty_Internal_Templatebase current Smarty_Internal_Templatebase (or Smarty or Smarty_Internal_Template) instance for chaining
     * @throws SmartyException when the plugin tag is invalid
     */
    public function registerPlugin($type, $tag, $callback, $cacheable = true, $cache_attr = null)
    {
        if (!isset($this->registered_plugins[$type][$tag])) {
            return parent::registerPlugin($type,$tag,$callback,$cacheable,$cache_attr);
        }
        return $this;
    }

    /**
     * defaultPluginHandler
     * NOTE: Registered in constructor
     *
     * @param string $name
     * @param string $type
     * @param string $template
     * @param string $callback
     * @param string $script
     * @return bool true on success, false on failure
     */
    public function defaultPluginHandler($name, $type, $template, &$callback, &$script, &$cachable)
    {
        debug_buffer('',"Start Load Smarty Plugin $name/$type");

        // plugins with the smarty_cms_function
        $cachable = TRUE;
        $dirs = [];
        $dirs[] = cms_join_path(CMS_ROOT_PATH,'assets','plugins',$type.'.'.$name.'.php');
        $dirs[] = cms_join_path(CMS_ROOT_PATH,'plugins',$type.'.'.$name.'.php');
        $dirs[] = cms_join_path(CMS_ROOT_PATH,'lib','plugins',$type.'.'.$name.'.php');
        foreach( $dirs as $fn ) {
            if( !is_file($fn) ) continue;

            require_once($fn);
            $script = $fn;

            $funcs = [];
            $funcs[] = 'smarty_nocache_'.$type.'_'.$name;
            $funcs[] = 'smarty_cms_'.$type.'_'.$name;
            foreach( $funcs as $func ) {
                if( !function_exists($func) ) continue;

                $callback = $func;
                $cachable = FALSE;
                debug_buffer('',"End Load Smarty Plugin $name/$type");
                return TRUE;
            }
        }

        if( CmsApp::get_instance()->is_frontend_request() ) {
            $row = cms_module_smarty_plugin_manager::load_plugin($name,$type);
            if( is_array($row) && is_array($row['callback']) && count($row['callback']) == 2 &&
                is_string($row['callback'][0]) && is_string($row['callback'][1]) ) {
                $cachable = $row['cachable'];
                $callback = $row['callback'][0].'::'.$row['callback'][1];
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Test if a smarty plugin with the specified name already exists.
     *
     * @param string the plugin name
     * @return bool
     */
    public function is_registered($name)
    {
        return isset($this->registered_plugins['function'][$name]);
    }

    /**
     * Set the global cacheid.
     * This is a prefix that is used when smarty caching is enabled.
     *
     * @param int $id
     * @internal
     * @return void
     */
    public function set_global_cacheid($id)
    {
        if( is_null($id) || $id === '' ) {
            $this->_global_cache_id = null;
        }
        else {
            $this->_global_cache_id = $id;
        }
    }

    /**
     * Get the global cacheid if any.
     *
     * @internal
     * @return int|null
     */
    public function get_global_cacheid()
    {
        return $this->_global_cache_id;
    }

    /**
     * Get a suitable parent template for a new template.
     *
     * This method is used when creating new smarty template objects to find a suitable parent.
     * An internal stack of parents is used to find the latest item on the stack.
     * if there are no parents, then the root smarty object is used.
     *
     * i.e:
     * <code>$smarty->CreateSmartyTemplate('somefile.tpl',$cache_id,$compile_id,$smarty->get_template_parent());</code>
     *
     * @since 2.0.1
     * @deprecated
     * @return \smarty_internal_template
     */
    public function get_template_parent()
    {
        // no parent specified, see if there is a stack of parents.
        if( count($this->_tpl_stack) ) {
            $parent = $this->_tpl_stack[count($this->_tpl_stack)-1];
        }
        else {
            // no stack, so use this (the Smarty_CMS) class.
            $parent = $this;
        }
        return $parent;
    }

    /**
     * fetch method
     * NOTE: Overwrites parent
     *
     * @deprecated
     * @param mixed $template
     * @param int $cache_id
     * @param mixed $parent
     * @param bool $display
     * @param bool $merge_tpl_vars
     * @param bool $no_output_filter
     * @return mixed
     */
    public function fetch($template = null,$cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        $name = $template; if( startswith($name,'string:') ) $name = 'string:';
        debug_buffer('','Fetch '.$name.' start');

        // we called the root smarty fetch method instead of some template object's fetch method directly.
        // which is the case for things like Module::ProcessTemplate and Module::ProcessTemplateFromDatabase etc..()
        if( is_object($template) ) {
            $_tpl = $template;
        } else {
            if( !$parent ) {
                // get the parent off of the stack.
                $parent = $this->get_template_parent();
            }
            $_tpl = $this->CreateTemplate($template,$cache_id,$compile_id,$parent);
        }

        //put the new template onto the stack, and do our work, to handle recursive calls.
        $this->_tpl_stack[] = $_tpl;
        $tmp = null;
        if( $display ) {
            $_tpl->display();
        } else {
            $tmp = $_tpl->fetch();
        }

        // and pop off the stack again.
        array_pop($this->_tpl_stack);

        // admin requests are a bit fugged up... lots of stuff relies on a single smarty scope.
        // gotta fix that.
        debug_buffer('','Fetch '.$name.' end');
        return $tmp;
    }

    public function createTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $do_clone = true)
    {
        if( !startswith($template,'eval:') && !startswith($template,'string:') ) {
            if( ($pos = strpos($template,'*')) > 0 ) throw new \LogicException("$template is an invalid CMSMS resource specification");
            if( ($pos = strpos($template,'/')) > 0 ) throw new \LogicException("$template is an invalid CMSMS resource specification");
        }
        return parent::createTemplate($template, $cache_id, $compile_id, $parent, $do_clone );
    }

    /**
     * clearCache method
     * NOTE: Overwrites parent
     *
     * @param mixed $template_name
     * @param int $cache_id
     * @param int $compile_id
     * @param mixed $exp_time
     * @param mixed $type
     * @return mixed
     */
    public function clearCache($template_name,$cache_id = null,$compile_id = null,$exp_time = null,$type = null)
    {
        if( is_null($cache_id) || $cache_id === '' ) {
            $cache_id = $this->_global_cache_id;
        }
        else if( $cache_id[0] == '|' ) {
            $cache_id = $this->_global_cache_id . $cache_id;
        }
        return parent::clearCache($template_name,$cache_id,$compile_id,$exp_time,$type);
    }

    /**
     * isCached method
     * NOTE: Overwrites parent
     *
     * @param mixed $template_name
     * @param int $cache_id
     * @param int $compile_id
     * @param mixed $parent
     * @return mixed
     */
    public function isCached($template = null,$cache_id = null,$compile_id = null, $parent = null)
    {
        if( is_null($cache_id) || $cache_id === '' ) {
            $cache_id = $this->_global_cache_id;
        }
        else if( $cache_id[0] == '|' ) {
            $cache_id = $this->_global_cache_id . $cache_id;
        }
        return parent::isCached($template,$cache_id,$compile_id,$parent);
    }

    /**
     * Error console
     *
     * @param object Exception $e
     * @return html
     * @author Stikki
     */
    public function errorConsole(Exception $e)
    {
        $this->force_compile = true;

        # do not show smarty debug console popup to users not logged in
        //$this->debugging = get_userid(FALSE);

        $this->assign('e_line', $e->getLine());
        $this->assign('e_file', $e->getFile());
        $this->assign('e_message', $e->getMessage());
        $this->assign('e_trace', htmlentities($e->getTraceAsString()));
        $this->assign('loggedin',get_userid(FALSE));

        // put mention into the admin log
        audit('', 'Smarty Error', substr( $e->getMessage(),0 ,200 ) );

        $output = $this->fetch('cmsms-error-console.tpl');

        $this->force_compile = false;
        $this->debugging = false;

        return $output;
    }


    /**
     * Takes unknown classes and loads plugin files for them
     * class name format: Smarty_PluginType_PluginName
     * plugin filename format: plugintype.pluginname.php
     *
     * Note: this method overrides the one in the smarty base class and provides more testing.
     *
     * @param string $plugin_name    class plugin name to load
     * @param bool   $check          check if already loaded
     * @return string |boolean filepath of loaded file or false
     */
    public function loadPlugin($plugin_name, $check = true)
    {
        // if function or class exists, exit silently (already loaded)
        if ($check && (is_callable($plugin_name) || class_exists($plugin_name, false))) return true;

        // Plugin name is expected to be: Smarty_[Type]_[Name]
        $_name_parts = explode('_', $plugin_name, 3);

        // class name must have three parts to be valid plugin
        // count($_name_parts) < 3 === !isset($_name_parts[2])
        if (!isset($_name_parts[2]) || strtolower($_name_parts[0]) !== 'smarty') {
            throw new SmartyException("plugin {$plugin_name} is not a valid name format");
            return false;
        }

        // if type is "internal", get plugin from sysplugins
        if (strtolower($_name_parts[1]) == 'internal') {
            $file = SMARTY_SYSPLUGINS_DIR . strtolower($plugin_name) . '.php';
            if (file_exists($file)) {
                require_once($file);
                return $file;
            } else {
                return false;
            }
        }

        // plugin filename is expected to be: [type].[name].php
        $_plugin_filename = "{$_name_parts[1]}.{$_name_parts[2]}.php";

        $_stream_resolve_include_path = function_exists('stream_resolve_include_path');

        // loop through plugin dirs and find the plugin
        foreach($this->getPluginsDir() as $_plugin_dir) {
            $names = array($_plugin_dir . $_plugin_filename,
                           $_plugin_dir . strtolower($_plugin_filename)
                );

            foreach ($names as $file) {
                if (file_exists($file)) {
                    require_once($file);
                    if( is_callable($plugin_name) || class_exists($plugin_name, false) ) return $file;
                }

                if ($this->use_include_path &&
                    !preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_plugin_dir)) {
                    // try PHP include_path
                    if ($_stream_resolve_include_path) {
                        $file = stream_resolve_include_path($file);
                    } else {
                        $file = Smarty_Internal_Get_Include_Path::getIncludePath($file);
                    }

                    if ($file !== false) {
                        require_once($file);
                        if( is_callable($plugin_name) || class_exists($plugin_name, false) ) return $file;
                    }
                }
            }
        }
        // no plugin loaded
        return false;
    }

} // end of class
