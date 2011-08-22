<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Load requested plugins
 *
 * @param array $plugins
 */

// $plugins

function smarty_core_get_module_plugin($_name,&$smarty)
{
  $fn = TMP_CACHE_LOCATION.'/modulefunctions.cache.dat';
  $found = FALSE;
  $data = array();
  if( !file_exists($fn) )
    {
      // build the cache list
      $loaded = array();
      $orig = array_keys($smarty->_plugins['function']);
      $installed = ModuleOperations::get_instance()->GetInstalledModules();
      $nloaded = ModuleOperations::get_instance()->GetInstalledModules();
      foreach( $installed as $module )
	{
	  if( in_array($module,$nloaded) ) $nloaded[] = $module;
	  cms_utils::get_module($module);
// 	  if( in_array('CGCalendar',array_keys($smarty->_plugins['function'])) )
// 	    {
// 	      debug_display($smarty->_plugins['function']['CGCalendar']); die();
// 	    }
	  $tmp = array_keys($smarty->_plugins['function']);
	  $tmp2 = array_diff($tmp,$orig);
	  foreach( $tmp2 as $one )
	    {
	      $data[$one] = $module;
	    }
	  $orig = $tmp;
	}

      foreach( $nloaded as $module )
	{
	  ModuleOperations::get_instance()->unload_module($module);
	}
      
      file_put_contents($fn,serialize($data));
    }
  else
    {
      $data = unserialize(file_get_contents($fn));
    }

  if( $found == FALSE && isset($data[$_name]) )
    {
      // we know which module this plugin is associated with.
      // so make sure it's loaded.
      cms_utils::get_module($data[$_name]);
      return TRUE;
    }
  return FALSE;
}


function smarty_core_load_plugins($params, &$smarty)
{
    foreach ($params['plugins'] as $_plugin_info) {

      $_noadd = FALSE;
      $_cachable = TRUE;

        list($_type, $_name, $_tpl_file, $_tpl_line, $_delayed_loading) = $_plugin_info;
        $_plugin = &$smarty->_plugins[$_type][$_name];

        /*
         * We do not load plugin more than once for each instance of Smarty.
         * The following code checks for that. The plugin can also be
         * registered dynamically at runtime, in which case template file
         * and line number will be unknown, so we fill them in.
         *
         * The final element of the info array is a flag that indicates
         * whether the dynamically registered plugin function has been
         * checked for existence yet or not.
         */
        if (isset($_plugin)) {
            if (empty($_plugin[3])) {
                if (!is_callable($_plugin[0])) {
                    $smarty->_trigger_fatal_error("[plugin] $_type '$_name' is not implemented", $_tpl_file, $_tpl_line, __FILE__, __LINE__);
                } else {
                    $_plugin[1] = $_tpl_file;
                    $_plugin[2] = $_tpl_line;
                    $_plugin[3] = true;
                    if (!isset($_plugin[4])) $_plugin[4] = true; /* cacheable */
                }
            }
            continue;
        } else if ($_type == 'insert') {
            /*
             * For backwards compatibility, we check for insert functions in
             * the symbol table before trying to load them as a plugin.
             */
            $_plugin_func = 'insert_' . $_name;
            if (function_exists($_plugin_func)) {
                $_plugin = array($_plugin_func, $_tpl_file, $_tpl_line, true, false);
                continue;
            }
        }

        $_plugin_file = $smarty->_get_plugin_filepath($_type, $_name);

        if (! $_found = ($_plugin_file != false)) {
            $_message = "could not load plugin file '$_type.$_name.php'\n";
        }

        /*
         * If plugin file is found, it -must- provide the properly named
         * plugin function. In case it doesn't, simply output the error and
         * do not fall back on any other method.
         */

        if ($_found) 
	  {
	    // its a plugin
            include_once $_plugin_file;

            $_plugin_func = 'smarty_' . $_type . '_' . $_name;
            $_cms_plugin_func = 'smarty_cms_' . $_type . '_' . $_name;
            if (!function_exists($_cms_plugin_func) && !function_exists($_plugin_func)) {
                $smarty->_trigger_fatal_error("[plugin] function $_plugin_func() not found in $_plugin_file", $_tpl_file, $_tpl_line, __FILE__, __LINE__);
                continue;
            }
            if (!is_callable($_plugin_func) && is_callable($_cms_plugin_func))
	    {
	        // CMS Made Simple plugins start with smarty_cms instead of just smarty
                $_plugin_func = $_cms_plugin_func;
            }
	  }
	if( !$_found )
	  {
	    // is it a module plugin?
	    $_found = smarty_core_get_module_plugin($_name,$smarty);
	    if( $_found ) 
	      {
		$_noadd = TRUE;
	      }
	  }
	if ( !$_found && UserTagOperations::get_instance()->UserTagExists($_name) )
	{
	  // see if it's a UDT
	  $_cachable = FALSE; // UDTs are never cachable.
	  $_plugin_func = UserTagOperations::get_instance()->CreateTagFunction($_name);
	  $_found = true;
	}
        /*
         * In case of insert plugins, their code may be loaded later via
         * 'script' attribute.
         */
        else if ($_type == 'insert' && $_delayed_loading) {
            $_plugin_func = 'smarty_' . $_type . '_' . $_name;
            $_found = true;
        }

        /*
         * Plugin specific processing and error checking.
         */
        if (!$_found) {
            if ($_type == 'modifier') {
                /*
                 * In case modifier falls back on using PHP functions
                 * directly, we only allow those specified in the security
                 * context.
                 */
                if ($smarty->security && !in_array($_name, $smarty->security_settings['MODIFIER_FUNCS'])) {
                    $_message = "(secure mode) modifier '$_name' is not allowed";
                } else {
                    if (!function_exists($_name)) {
                        $_message = "modifier '$_name' is not implemented";
                    } else {
                        $_plugin_func = $_name;
                        $_found = true;
                    }
                }
            } else if ($_type == 'function') {
                /*
                 * This is a catch-all situation.
                 */
                $_message = "unknown tag - '$_name'";
            }
        }

        if ($_found) {
	  if( !$_noadd )
	    {
	      $smarty->_plugins[$_type][$_name] = array($_plugin_func, $_tpl_file, $_tpl_line, true, $_cachable);
	    }
        } else {
            // output error
            $smarty->_trigger_fatal_error('[plugin] ' . $_message, $_tpl_file, $_tpl_line, __FILE__, __LINE__);
        }
    }
}

/* vim: set expandtab: */

?>
