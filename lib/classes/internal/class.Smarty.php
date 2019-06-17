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

namespace CMSMS\internal;
use CmsApp;
use cms_config;

/**
 * @package CMS
 */

/**
 * Extends the Smarty class for content.
 *
 * @package CMS
 * @since 0.1
 */
class Smarty extends smarty_theme_template
{
    /**
     * @ignore
     */
    private $_is_frontend_request;

    /**
     * @ignore
     */
    private $_simple_plugin_ops;

    /**
     * @ignore
     */
    private $_module_smarty_plugin_manager;

    /**
     * Constructor
     *
     * @param array The hash of CMSMS config settings
     */
    public function __construct( CmsApp $app )
    {
        parent::__construct();
        $this->_is_frontend_request = $app->is_frontend_request();
        $this->_simple_plugin_ops = $app->GetSimplePluginOperations();
        $this->_module_smarty_plugin_manager = $app->get_module_smarty_plugin_manager();
        $this->direct_access_security = TRUE;

        global $CMS_INSTALL_PAGE;

        // Set template_c and cache dirs
        $this->setCompileDir(TMP_TEMPLATES_C_LOCATION);
        $this->assignGlobal('app_name','CMSMS');

        if (CMS_DEBUG == true) $this->error_reporting = 'E_ALL';

        // set our own template class with some funky stuff in it
        // note, can get rid of the CMS_Smarty_Template class and the Smarty_Parser classes.
        $this->template_class = '\\CMSMS\internal\template_wrapper';

        // common resources.
        //$this->registerResource('cmsfile',new \CMSMS\internal\smarty_resource_cmsfile());
        $this->registerResource('cms_theme', new \CMSMS\internal\smarty_resource_cmstheme());
        $this->registerResource('cmsfile',new \CMSMS\internal\smarty_resource_cmsfile());
        $this->registerResource('module_db_tpl',new \CMSMS\internal\module_db_template_resource());
        $this->registerResource('module_file_tpl',new \CMSMS\internal\module_file_template_resource());
        $this->registerResource('cms_template',new \CMSMS\internal\layout_template_resource());
        $this->registerResource('cms_stylesheet',new \CMSMS\internal\layout_stylesheet_resource());
        //$this->default_resource_type = 'cmsfile';

        // register default plugin handler
        $this->registerDefaultPluginHandler( [ $this, 'defaultPluginHandler' ] );

        $this->addPluginsDir(CMS_ASSETS_PATH.'/plugins');
        $this->addPluginsDir(CMS_ROOT_PATH.'/lib/plugins');
        $this->addPluginsDir(CMS_ROOT_PATH.'/plugins'); // deprecated
        $this->addConfigDir(CMS_ASSETS_PATH.'/configs');
        $this->addTemplateDir(CMS_ASSETS_PATH.'/templates');
        $this->addTemplateDir(CMS_ROOT_PATH.'/lib/assets/templates');

        $config = $app->GetConfig();
        if( $this->_is_frontend_request ) {

            // Check if we are at install page, don't register anything if so, cause nothing below is needed.
            if(isset($CMS_INSTALL_PAGE)) return;

            if (is_sitedown()) {
                $this->setCaching(false);
                $this->force_compile = true;
            }

            // Load resources
            $this->registerResource('content',new \CMSMS\internal\content_template_resource());

            // just for frontend actions.
            $this->registerPlugin('compiler','content','\\CMSMS\internal\content_plugins::smarty_compile_fecontentblock',false);
            $this->registerPlugin('function','content_image','\\CMSMS\\internal\content_plugins::smarty_fetch_imageblock',false);
            $this->registerPlugin('function','content_module','\\CMSMS\\internal\\content_plugins::smarty_fetch_moduleblock',false);
            $this->registerPlugin('function','content_text','\\CMSMS\\internal\\content_plugins::smarty_fetch_textblock',false);
            $this->registerPlugin('function','process_pagedata','\\CMSMS\internal\\content_plugins::smarty_fetch_pagedata',false);

            // Autoload filters
            //$this->autoloadFilters();

            // Enable security object
            if( !$config['permissive_smarty'] ) $this->enableSecurity('\\CMSMS\\internal\\smarty_security_policy');
        }
        else if($app->test_state(CmsApp::STATE_ADMIN_PAGE)) {
            $this->setCaching(false);
            $admin_dir = $config['admin_path'];
            $this->addPluginsDir($admin_dir.'/plugins');
            $this->addTemplateDir($admin_dir.'/templates');
            $this->setConfigDir($admin_dir.'/configs');;
        }
    }

    /**
     * Load filters from CMSMS plugins folders
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
        if( $this->security_policy ) $this->security_policy->static_classes[] = $a;
        parent::registerClass($a,$b);
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
        // plugins with the smarty_cms_function
        $cachable = TRUE;
        $dirs = [];
        $dirs[] = cms_join_path(CMS_ASSETS_PATH,'plugins',$type.'.'.$name.'.php');
        $dirs[] = cms_join_path(CMS_ROOT_PATH,'plugins',$type.'.'.$name.'.php'); // deprecated
        $dirs[] = cms_join_path(CMS_ROOT_PATH,'lib','plugins',$type.'.'.$name.'.php');
        foreach( $dirs as $fn ) {
            if( !is_file($fn) ) continue;

            require_once($fn);
            $script = $fn;

            $funcs = [];
            $funcs[] = 'smarty_nocache_'.$type.'_'.$name; // deprecated
            $funcs[] = 'smarty_cms_'.$type.'_'.$name; // deprecated
            foreach( $funcs as $func ) {
                if( !function_exists($func) ) continue;

                $callback = $func;
                $cachable = FALSE;
                return TRUE;
            }
        }

        if( $type != 'function' ) return; // hmmm.

        if( $this->_is_frontend_request ) {
            $row = $this->_module_smarty_plugin_manager->load_plugin($name,$type);
            if( is_array($row) && is_array($row['callback']) && count($row['callback']) == 2 &&
                is_string($row['callback'][0]) && is_string($row['callback'][1]) ) {
                $callback = $row['callback'][0].'::'.$row['callback'][1];
                $cachable = FALSE;
                return TRUE;
            }

            // see if it is a simple plugin
            $plugin = $this->_simple_plugin_ops->load_plugin( $name );
            if( $plugin ) {
                $callback = $plugin;
                $cachable = FALSE;
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

    public function createTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $do_clone = true)
    {
        if( !startswith($template,'eval:') && !startswith($template,'string:') && !startswith($template,'cmsfile:') ) {
            if( ($pos = strpos($template,'*')) > 0 ) throw new \LogicException("$template is an invalid CMSMS resource specification");
            if( ($pos = strpos($template,'/')) > 0 ) throw new \LogicException("$template is an invalid CMSMS resource specification");
        }
        return parent::createTemplate($template, $cache_id, $compile_id, $parent, $do_clone );
    }

    /**
     * Error console
     *
     * @param object Exception $e
     * @return html
     * @author Stikki
     */
    public function errorConsole(\Exception $e, $allow_trace = true)
    {
        $this->force_compile = true;

        # do not show smarty debug console popup to users not logged in
        //$this->debugging = get_userid(FALSE);
        $this->assign('e_line', $e->getLine());
        $this->assign('e_file', $e->getFile());
        $this->assign('e_message', $e->getMessage());
        $this->assign('e_trace', htmlentities($e->getTraceAsString()));
        $this->assign('loggedin',get_userid(FALSE));
        if( !$allow_trace ) $this->assign('e_trace',null);

        // put mention into the admin log
        cms_error('Smarty Error: '. substr( $e->getMessage(),0 ,200 ) );

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
     * We need this function (v2.3+) because the parent method does other checks on the function name
     * and because we still support smarty_cms_function and smarty_nocache_function...
     *
     * @param string $plugin_name    class plugin name to load
     * @param bool   $check          check if already loaded
     * @return string|boolean filepath of loaded file or false
     */
    public function loadPlugin($plugin_name, $check = true)
    {
        if ($check && (is_callable($plugin_name) || class_exists($plugin_name, false))) return true;

        // Plugin name is expected to be: Smarty_[Type]_[Name]
        $_name_parts = explode('_', $plugin_name, 3);

        // class name must have three parts to be valid plugin
        // count($_name_parts) < 3 === !isset($_name_parts[2])
        if (!isset($_name_parts[2]) || strtolower($_name_parts[0]) !== 'smarty') {
            throw new SmartyException("plugin {$plugin_name} is not a valid name format");
        }

        // if type is "internal", get plugin from sysplugins
        if (strtolower($_name_parts[1]) == 'internal') {
            $file = SMARTY_SYSPLUGINS_DIR . strtolower($plugin_name) . '.php';
            if (is_file($file)) {
                require_once($file);
                return $file;
            } else {
                return false;
            }
        }

        // plugin filename is expected to be: [type].[name].php
        $_plugin_filename = "{$_name_parts[1]}.{$_name_parts[2]}.php";

        // loop through plugin dirs and find the plugin
        foreach($this->getPluginsDir() as $_plugin_dir) {
            $names = array($_plugin_dir . $_plugin_filename,
                           $_plugin_dir . strtolower($_plugin_filename)
                );

            foreach ($names as $file) {
                if (file_exists($file)) {
                    require_once($file);
                    if( is_callable($plugin_name) || class_exists($plugin_name, false) ) {
                        return $file;
                    }
                }
            }
        }

        // no plugin loaded
        return false;
    }
} // end of class
