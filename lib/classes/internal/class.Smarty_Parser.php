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
        stack_trace(); die();
		parent::__construct();

		$this->setTemplateDir(cms_join_path(CMS_ROOT_PATH,'tmp','templates'));
		$this->setConfigDir(cms_join_path(CMS_ROOT_PATH,'tmp','templates'));

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
    /*
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

				if (is_file($file) &&
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
    */

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
     * @param bool $nocache if true any output of this variable will be not cached
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