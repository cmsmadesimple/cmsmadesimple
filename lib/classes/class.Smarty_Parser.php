<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
 * @author Tapio Löytty
 * @since 1.11.3
 */
class Smarty_Parser extends Smarty_CMS
{	
	public $id; // <- triggers error without | do search why this is needed
	public $params; // <- triggers error without | do search why this is needed
	private static $_instance;
	private static $_allowed_static_plugins = array('global_content');

	/**
	* Constructor
	*
	* @param array The hash of CMSMS config settings
	*/
	public function __construct()
	{ 
		parent::__construct();		
	
		$config = cmsms()->GetConfig();

		$this->setTemplateDir(cms_join_path($config['root_path'],'tmp','templates'));
		$this->setConfigDir(cms_join_path($config['root_path'],'tmp','templates'));		
		
		$this->setCaching(false);
		$this->force_compile = true;
		$this->compile_id = 'parser' . time();

		// register default plugin handler
		$this->registerDefaultPluginHandler(array(&$this, 'defaultPluginHandler'));		
		
		// Register plugins
		$this->registerPlugin('compiler','content',array('CMS_Content_Block','smarty_compiler_contentblock'),false);
		$this->registerPlugin('compiler','content_image',array('CMS_Content_Block','smarty_compiler_imageblock'),false);
		$this->registerPlugin('compiler','content_module',array('CMS_Content_Block','smarty_compiler_moduleblock'),false);
	}

	/**
	* get_instance method
	*
	* @return object $this
	*/
	public static function &get_instance()
	{
		if( !is_object(self::$_instance) ) 
			self::$_instance = new self;
		
		// Merge variables
		self::$_instance->tpl_vars = array_merge(self::$_instance->tpl_vars, parent::get_instance()->tpl_vars);
		
		return self::$_instance;
	}	
	
    /**
     * _dflt_plugin
     *
     * @internal
     */
    public static function _dflt_plugin($params,$smarty)
    {
		return '';
    }

    /**
     * Dummy default plugin handler for smarty.
     *
     * @access private
     * @internal
     */
    public function defaultPluginHandler($name, $type, $template, &$callback, &$script, &$cachable)
    {
		if($type == 'compiler') {
		
			$callback = array(__CLASS__,'_dflt_plugin');
			$cachable = false;
			return TRUE;
		}

		return parent::defaultPluginHandler($name, $type, $template, $callback, $script, $cachable);	
    }
	
	/**
     * fetches a rendered Smarty template
     *
     * @param string $template          the resource handle of the template file or template object
     * @param mixed  $cache_id          cache id to be used with this template
     * @param mixed  $compile_id        compile id to be used with this template
     * @param object $parent            next higher level of Smarty variables
     * @param bool   $display           true: display, false: fetch
     * @param bool   $merge_tpl_vars    if true parent template variables merged in to local scope
     * @param bool   $no_output_filter  if true do not run output filter
     * @return string rendered template output
     */
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    {
        if ($template === null && $this instanceof $this->template_class) {
            $template = $this;
        }
        if (!empty($cache_id) && is_object($cache_id)) {
            $parent = $cache_id;
            $cache_id = null;
        }
        if ($parent === null && ($this instanceof Smarty || is_string($template))) {
            $parent = $this;
        }
        // create template object if necessary
        $_template = ($template instanceof $this->template_class)
        ? $template
        : $this->smarty->createTemplate($template, $cache_id, $compile_id, $parent, false);
        // if called by Smarty object make sure we use current caching status
        if ($this instanceof Smarty) {
            $_template->caching = $this->caching;
        }
        // merge all variable scopes into template
        if ($merge_tpl_vars) {
            // save local variables
            $save_tpl_vars = $_template->tpl_vars;
            $save_config_vars = $_template->config_vars;
            $ptr_array = array($_template);
            $ptr = $_template;
            while (isset($ptr->parent)) {
                $ptr_array[] = $ptr = $ptr->parent;
            }
            $ptr_array = array_reverse($ptr_array);
            $parent_ptr = reset($ptr_array);
            $tpl_vars = $parent_ptr->tpl_vars;
            $config_vars = $parent_ptr->config_vars;
            while ($parent_ptr = next($ptr_array)) {
                if (!empty($parent_ptr->tpl_vars)) {
                    $tpl_vars = array_merge($tpl_vars, $parent_ptr->tpl_vars);
                }
                if (!empty($parent_ptr->config_vars)) {
                    $config_vars = array_merge($config_vars, $parent_ptr->config_vars);
                }
            }
            if (!empty(Smarty::$global_tpl_vars)) {
                $tpl_vars = array_merge(Smarty::$global_tpl_vars, $tpl_vars);
            }
            $_template->tpl_vars = $tpl_vars;
            $_template->config_vars = $config_vars;
        }
        // dummy local smarty variable
        if (!isset($_template->tpl_vars['smarty'])) {
            $_template->tpl_vars['smarty'] = new Smarty_Variable;
        }
        if (isset($this->smarty->error_reporting)) {
            $_smarty_old_error_level = error_reporting($this->smarty->error_reporting);
        }
        // check URL debugging control
        if (!$this->smarty->debugging && $this->smarty->debugging_ctrl == 'URL') {
            if (isset($_SERVER['QUERY_STRING'])) {
                $_query_string = $_SERVER['QUERY_STRING'];
            } else {
                $_query_string = '';
            }
            if (false !== strpos($_query_string, $this->smarty->smarty_debug_id)) {
                if (false !== strpos($_query_string, $this->smarty->smarty_debug_id . '=on')) {
                    // enable debugging for this browser session
                    setcookie('SMARTY_DEBUG', true);
                    $this->smarty->debugging = true;
                } elseif (false !== strpos($_query_string, $this->smarty->smarty_debug_id . '=off')) {
                    // disable debugging for this browser session
                    setcookie('SMARTY_DEBUG', false);
                    $this->smarty->debugging = false;
                } else {
                    // enable debugging for this page
                    $this->smarty->debugging = true;
                }
            } else {
                if (isset($_COOKIE['SMARTY_DEBUG'])) {
                    $this->smarty->debugging = true;
                }
            }
        }
        // must reset merge template date
        $_template->smarty->merged_templates_func = array();
        // get rendered template
        // disable caching for evaluated code
        if ($_template->source->recompiled) {
            $_template->caching = false;
        }
        // checks if template exists
        if (!$_template->source->exists) {
            if ($_template->parent instanceof Smarty_Internal_Template) {
                $parent_resource = " in '{$_template->parent->template_resource}'";
            } else {
                $parent_resource = '';
            }
            throw new SmartyException("Unable to load template {$_template->source->type} '{$_template->source->name}'{$parent_resource}");
        }
        // read from cache or render
        if (!($_template->caching == Smarty::CACHING_LIFETIME_CURRENT || $_template->caching == Smarty::CACHING_LIFETIME_SAVED) || !$_template->cached->valid) {
            // render template (not loaded and not in cache)
            if (!$_template->source->uncompiled) {
                $_smarty_tpl = $_template;
                if ($_template->source->recompiled) {
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::start_compile($_template);
                    }
                    $code = $_template->compiler->compileTemplate($_template);
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::end_compile($_template);
                    }
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::start_render($_template);
                    }
                    try {
                        ob_start();
                        eval("?>" . $code);
                        unset($code);
                    } catch (Exception $e) {
                        ob_get_clean();
                        throw $e;
                    }
                } else {
                    if (!$_template->compiled->exists || ($_template->smarty->force_compile && !$_template->compiled->isCompiled)) {
                        $_template->compileTemplateSource();
                    }
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::start_render($_template);
                    }
                    if (!$_template->compiled->loaded) {
                        include($_template->compiled->filepath);
                        if ($_template->mustCompile) {
                            // recompile and load again
                            $_template->compileTemplateSource();
                            include($_template->compiled->filepath);
                        }
                        $_template->compiled->loaded = true;
                    } else {
                        $_template->decodeProperties($_template->compiled->_properties, false);
                    }
                    try {
                        ob_start();
                        if (empty($_template->properties['unifunc']) || !is_callable($_template->properties['unifunc'])) {
                            throw new SmartyException("Invalid compiled template for '{$_template->template_resource}'");
                        }
                        array_unshift($_template->_capture_stack,array());
                        //
                        // render compiled template
                        //
						
						// CMSMS MOD START
						if(isset($_template->compiled->_properties['variables'])) {
						
							foreach((array)$_template->compiled->_properties['variables'] as $k=>$v) {
							
								if(!array_key_exists($k, $_template->tpl_vars)) {
							
									$_template->tpl_vars[$k] = new CMSMS_Dummy_Smarty_Variable;
								}
							}
						}
						// CMSMS MOD END						
						
                        $_template->properties['unifunc']($_template);
                        // any unclosed {capture} tags ?
                        if (isset($_template->_capture_stack[0][0])) {
                            $_template->capture_error();
                        }
                        array_shift($_template->_capture_stack);
                    } catch (Exception $e) {
                        ob_get_clean();
                        throw $e;
                    }
                }
            } else {
                if ($_template->source->uncompiled) {
                    if ($this->smarty->debugging) {
                        Smarty_Internal_Debug::start_render($_template);
                    }
                    try {
                        ob_start();
                        $_template->source->renderUncompiled($_template);
                    } catch (Exception $e) {
                        ob_get_clean();
                        throw $e;
                    }
                } else {
                    throw new SmartyException("Resource '$_template->source->type' must have 'renderUncompiled' method");
                }
            }
            $_output = ob_get_clean();
            if (!$_template->source->recompiled && empty($_template->properties['file_dependency'][$_template->source->uid])) {
                $_template->properties['file_dependency'][$_template->source->uid] = array($_template->source->filepath, $_template->source->timestamp, $_template->source->type);
            }
            if ($_template->parent instanceof Smarty_Internal_Template) {
                $_template->parent->properties['file_dependency'] = array_merge($_template->parent->properties['file_dependency'], $_template->properties['file_dependency']);
                foreach ($_template->required_plugins as $code => $tmp1) {
                    foreach ($tmp1 as $name => $tmp) {
                        foreach ($tmp as $type => $data) {
                            $_template->parent->required_plugins[$code][$name][$type] = $data;
                        }
                    }
                }
            }
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::end_render($_template);
            }
            // write to cache when nessecary
            if (!$_template->source->recompiled && ($_template->caching == Smarty::CACHING_LIFETIME_SAVED || $_template->caching == Smarty::CACHING_LIFETIME_CURRENT)) {
                if ($this->smarty->debugging) {
                    Smarty_Internal_Debug::start_cache($_template);
                }
                $_template->properties['has_nocache_code'] = false;
                // get text between non-cached items
                $cache_split = preg_split("!/\*%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*\/(.+?)/\*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*/!s", $_output);
                // get non-cached items
                preg_match_all("!/\*%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*\/(.+?)/\*/%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*/!s", $_output, $cache_parts);
                $output = '';
                // loop over items, stitch back together
                foreach ($cache_split as $curr_idx => $curr_split) {
                    // escape PHP tags in template content
                    $output .= preg_replace('/(<%|%>|<\?php|<\?|\?>)/', '<?php echo \'$1\'; ?>', $curr_split);
                    if (isset($cache_parts[0][$curr_idx])) {
                        $_template->properties['has_nocache_code'] = true;
                        // remove nocache tags from cache output
                        $output .= preg_replace("!/\*/?%%SmartyNocache:{$_template->properties['nocache_hash']}%%\*/!", '', $cache_parts[0][$curr_idx]);
                    }
                }
                if (!$no_output_filter && !$_template->has_nocache_code && (isset($this->smarty->autoload_filters['output']) || isset($this->smarty->registered_filters['output']))) {
                    $output = Smarty_Internal_Filter_Handler::runFilter('output', $output, $_template);
                }
                // rendering (must be done before writing cache file because of {function} nocache handling)
                $_smarty_tpl = $_template;
                try {
                    ob_start();
                    eval("?>" . $output);
                    $_output = ob_get_clean();
                } catch (Exception $e) {
                    ob_get_clean();
                    throw $e;
                }
                // write cache file content
                $_template->writeCachedContent($output);
                if ($this->smarty->debugging) {
                    Smarty_Internal_Debug::end_cache($_template);
                }
            } else {
                // var_dump('renderTemplate', $_template->has_nocache_code, $_template->template_resource, $_template->properties['nocache_hash'], $_template->parent->properties['nocache_hash'], $_output);
                if (!empty($_template->properties['nocache_hash']) && !empty($_template->parent->properties['nocache_hash'])) {
                    // replace nocache_hash
                    $_output = str_replace("{$_template->properties['nocache_hash']}", $_template->parent->properties['nocache_hash'], $_output);
                    $_template->parent->has_nocache_code = $_template->parent->has_nocache_code || $_template->has_nocache_code;
                }
            }
        } else {
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::start_cache($_template);
            }
            try {
                ob_start();
                array_unshift($_template->_capture_stack,array());
                //
                // render cached template
                //
                $_template->properties['unifunc']($_template);
                // any unclosed {capture} tags ?
                if (isset($_template->_capture_stack[0][0])) {
                    $_template->capture_error();
                }
                array_shift($_template->_capture_stack);
                $_output = ob_get_clean();
            } catch (Exception $e) {
                ob_get_clean();
                throw $e;
            }
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::end_cache($_template);
            }
        }
        if ((!$this->caching || $_template->has_nocache_code || $_template->source->recompiled) && !$no_output_filter && (isset($this->smarty->autoload_filters['output']) || isset($this->smarty->registered_filters['output']))) {
            $_output = Smarty_Internal_Filter_Handler::runFilter('output', $_output, $_template);
        }
        if (isset($this->error_reporting)) {
            error_reporting($_smarty_old_error_level);
        }
        // display or fetch
        if ($display) {
            if ($this->caching && $this->cache_modified_check) {
                $_isCached = $_template->isCached() && !$_template->has_nocache_code;
                $_last_modified_date = @substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 0, strpos($_SERVER['HTTP_IF_MODIFIED_SINCE'], 'GMT') + 3);
                if ($_isCached && $_template->cached->timestamp <= strtotime($_last_modified_date)) {
                    switch (PHP_SAPI) {
                        case 'cgi':         // php-cgi < 5.3
                        case 'cgi-fcgi':    // php-cgi >= 5.3
                        case 'fpm-fcgi':    // php-fpm >= 5.3.3
                        header('Status: 304 Not Modified');
                        break;

                        case 'cli':
                        if (/* ^phpunit */!empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS'])/* phpunit$ */) {
                            $_SERVER['SMARTY_PHPUNIT_HEADERS'][] = '304 Not Modified';
                        }
                        break;

                        default:
                        header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
                        break;
                    }
                } else {
                    switch (PHP_SAPI) {
                        case 'cli':
                        if (/* ^phpunit */!empty($_SERVER['SMARTY_PHPUNIT_DISABLE_HEADERS'])/* phpunit$ */) {
                            $_SERVER['SMARTY_PHPUNIT_HEADERS'][] = 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $_template->cached->timestamp) . ' GMT';
                        }
                        break;

                        default:
                        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $_template->cached->timestamp) . ' GMT');
                        break;
                    }
                    echo $_output;
                }
            } else {
                echo $_output;
            }
            // debug output
            if ($this->smarty->debugging) {
                Smarty_Internal_Debug::display_debug($this);
            }
            if ($merge_tpl_vars) {
                // restore local variables
                $_template->tpl_vars = $save_tpl_vars;
                $_template->config_vars =  $save_config_vars;
            }
            return;
        } else {
            if ($merge_tpl_vars) {
                // restore local variables
                $_template->tpl_vars = $save_tpl_vars;
                $_template->config_vars =  $save_config_vars;
            }
            // return fetched content
            return $_output;
        }
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
		if ($check && (is_callable($plugin_name) || class_exists($plugin_name, false))) {
			return true;
		}
		
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
		
			$names = array(
				$_plugin_dir . $_plugin_filename,
				$_plugin_dir . strtolower($_plugin_filename)
			);
			
			foreach ($names as $file) {
			
				if (file_exists($file) && 
					(in_array($_name_parts[2], self::$_allowed_static_plugins) || 
						startswith($file, SMARTY_PLUGINS_DIR) || 
						$_name_parts[1] == 'modifier')
					) {
				
					require_once($file);
					if( is_callable($plugin_name) || class_exists($plugin_name, false) )
						return $file;
				}
				
				if ($this->use_include_path && !preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_plugin_dir)) {
				
					// try PHP include_path
					if ($_stream_resolve_include_path) {
					
						$file = stream_resolve_include_path($file);
					} else {
					
						$file = Smarty_Internal_Get_Include_Path::getIncludePath($file);
					}

					if ($file !== false) {
						require_once($file);
						if( is_callable($plugin_name) || class_exists($plugin_name, false) )
							return $file;
					}
				}
			}
		}
		// no plugin loaded
		return false;
    }	

} // end of class

/******************************************************************************
 CMS Made Simple - Dummy variable classes
******************************************************************************/

/**
 * class for undefined CMSMS parser variable objects
 *
 * @package CMS
 * @author Tapio Löytty
 * @since 1.11.3
 */
class CMSMS_Dummy_Smarty_Variable {

    /**
     * template variable
     *
     * @var mixed
     */
    public $value = null;
    /**
     * if true any output of this variable will be not cached
     *
     * @var boolean
     */
    public $nocache = false;
    /**
     * the scope the variable will have  (local,parent or root)
     *
     * @var int
     */
    public $scope = null;

    /**
     * create Smarty variable object
     *
     * @param mixed   $value   the value to assign
     * @param boolean $nocache if true any output of this variable will be not cached
     * @param int     $scope   the scope the variable will have  (local,parent or root)
     */
    public function __construct()
    {
        $this->value = new CMSMS_Dummy_Variable_Value;
    }

    /**
     * <<magic>> String conversion
     *
     * @return string
     */
    public function __toString()
    {
        return "";
    }

} // end of class

/**
 * class for undefined CMSMS parser variable object values
 *
 * @package CMS
 * @author Tapio Löytty
 * @since 1.11.3
 */
class CMSMS_Dummy_Variable_Value extends ArrayObject {

    public function offsetGet($name) 
	{
        return new CMSMS_Dummy_Variable_Value;
    }

    public function __get($name)
    {
        return new CMSMS_Dummy_Variable_Value;
    }
	
    public function __call($name, $arguments)
    {
        return new CMSMS_Dummy_Variable_Value;
    }	

    public function __toString()
    {
        return "";
    }	

} // end of class

?>
