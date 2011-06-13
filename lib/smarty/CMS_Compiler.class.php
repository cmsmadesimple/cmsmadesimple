<?php

/**
 * Project:     Smarty: the PHP compiling template engine
 * File:        Smarty_Compiler.class.php
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @link http://smarty.php.net/
 * @author Monte Ohrt <monte at ohrt dot com>
 * @author Andrei Zmievski <andrei@php.net>
 * @version 2.6.10
 * @copyright 2001-2005 New Digital Group, Inc.
 * @package Smarty
 */

/* $Id: Smarty_Compiler.class.php 2312 2005-12-08 03:08:53Z wishy $ */

/**
 * Template compiling class
 * @package Smarty
 */

include_once(dirname(__FILE__) . '/Smarty_Compiler.class.php');

class CMS_Compiler extends Smarty_Compiler {

  /**
   * compile custom function tag
   *
   * @param string $tag_command
   * @param string $tag_args
   * @param string $tag_modifier
   * @return string
   */
  function _compile_custom_tag($tag_command, $tag_args, $tag_modifier, &$output)
  {
    $found = false;
    $have_function = true;

    /*
     * First we check if the custom function has already been registered
     * or loaded from a plugin file.
     */
    if (isset($this->_plugins['function'][$tag_command])) {
      $found = true;
      $plugin_func = $this->_plugins['function'][$tag_command][0];
      if (!is_callable($plugin_func)) {
	$message = "custom function '$tag_command' is not implemented";
	$have_function = false;
      }
    }
    /*
     * See if we can find a plugin function for this tag...
     */
    else if ($plugin_file = $this->_get_plugin_filepath('function', $tag_command)) {
      $found = true;
      
      include_once $plugin_file;
      
      $plugin_func = 'smarty_cms_function_' . $tag_command;
      if (!function_exists($plugin_func)) {
	$message = "plugin function $plugin_func() not found in $plugin_file\n";
	$have_function = false;
      } else {
	$this->_plugins['function'][$tag_command] = array($plugin_func, null, null, null, false);
      }
    }
    /*
     * Lets' see if this is a UDT.
     */
    else if ( ($udt_name = UserTagOperations::get_instance()->UserTagExists($tag_command)) ) {
      $found = true;

      $plugin_func = UserTagOperations::get_instance()->CreateTagFunction($tag_command);
      if( !$plugin_func || !function_exists($plugin_func) )
	{
	  $message = "plugin function UDT $plugin_func() not found\n";
	  $have_function = false;
	}
      else
	{
	  $this->_plugins['function'][$tag_command] = array($plugin_func, null, null, null, false);
	}
    }

    
    if (!$found) {
      return parent::_compile_custom_tag($tag_command, $tag_args, $tag_modifier, $output);
    } else if (!$have_function) {
      return parent::_compile_custom_tag($tag_command, $tag_args, $tag_modifier, $output);
//       $this->_syntax_error($message, E_USER_WARNING, __FILE__, __LINE__);
//       return true;
    }


    /* declare plugin to be loaded on display of the template that
     we compile right now */
    $this->_add_plugin('function', $tag_command );
    
    $this->_plugins['function'][$tag_command][4] = false;
    $this->_plugins['function'][$tag_command][5] = array();

    $_cacheable_state = $this->_push_cacheable_state('function', $tag_command);
    $attrs = $this->_parse_attrs($tag_args);
    $arg_list = $this->_compile_arg_list('function', $tag_command, $attrs, $_cache_attrs );
    
    $output = $this->_compile_plugin_call('function', $tag_command).'(array('.implode(',', $arg_list)."), \$this)";
    if($tag_modifier != '') {
      $this->_parse_modifiers($output, $tag_modifier);
    }
    
    if($output != '') {
      $output =  '<?php ' . $_cacheable_state . $_cache_attrs . 'echo ' . $output . ';'
	. $this->_pop_cacheable_state('function', $tag_command) . "?>" . $this->_additional_newline;
    }
    
    // var_dump($output);
    return true;
  }


  function _parse_modifiers(&$output,$modifier_string)
  {
    preg_match_all('~\|(@?\w+)((?>:(?:'. $this->_qstr_regexp . '|[^|]+))*)~', '|' . $modifier_string, $_match);
    list(, $_modifiers, $modifier_arg_strings) = $_match;

    for ($_i = 0, $_for_max = count($_modifiers); $_i < $_for_max; $_i++) {
      $_modifier_name = $_modifiers[$_i];

      if($_modifier_name == 'smarty') {
	// skip smarty modifier
	continue;
      }

      preg_match_all('~:(' . $this->_qstr_regexp . '|[^:]+)~', $modifier_arg_strings[$_i], $_match);
      $_modifier_args = $_match[1];

      if (substr($_modifier_name, 0, 1) == '@') {
	$_map_array = false;
	$_modifier_name = substr($_modifier_name, 1);
      } else {
	$_map_array = true;
      }

      if (empty($this->_plugins['modifier'][$_modifier_name])
	  && !$this->_get_plugin_filepath('modifier', $_modifier_name)
	  && function_exists($_modifier_name)) {
	if ($this->security && !in_array($_modifier_name, $this->security_settings['MODIFIER_FUNCS'])) {
	  $this->_trigger_fatal_error("[plugin] (secure mode) modifier '$_modifier_name' is not allowed" , $this->_current_file, $this->_current_line_no, __FILE__, __LINE__);
	} else {
	  $this->_plugins['modifier'][$_modifier_name] = array($_modifier_name,  null, null, false);
	}
      }

      // hack for CMSMS... load the plugin now?
      $this->_add_plugin('modifier', $_modifier_name);
      {
	$tmp = array('modifier',$_modifier_name,$this->_current_file,$this->_current_line_no,false);
	$tmp = array('plugins'=>array($tmp));
	require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
	smarty_core_load_plugins($tmp,$this);
      }

      $this->_parse_vars_props($_modifier_args);

      if($_modifier_name == 'default') {
	// supress notifications of default modifier vars and args
	if(substr($output, 0, 1) == '$') {
	  $output = '@' . $output;
	}
	if(isset($_modifier_args[0]) && substr($_modifier_args[0], 0, 1) == '$') {
	  $_modifier_args[0] = '@' . $_modifier_args[0];
	}
      }
      if (count($_modifier_args) > 0)
	$_modifier_args = ', '.implode(', ', $_modifier_args);
      else
	$_modifier_args = '';

      if ($_map_array) {
	$output = "((is_array(\$_tmp=$output)) ? \$this->_run_mod_handler('$_modifier_name', true, \$_tmp$_modifier_args) : " . $this->_compile_plugin_call('modifier', $_modifier_name) . "(\$_tmp$_modifier_args))";

      } else {

	$output = $this->_compile_plugin_call('modifier', $_modifier_name)."($output$_modifier_args)";

      }
    }
  }


    /**
     * display Smarty syntax error
     *
     * @param string $error_msg
     * @param integer $error_type
     * @param string $file
     * @param integer $line
     */
    function _syntax_error($error_msg, $error_type = E_USER_ERROR, $file=null, $line=null)
    {
        $this->_trigger_fatal_error("syntax error: $error_msg", $this->_current_file, $this->_current_line_no, $file, $line, $error_type);
    }

    function trigger_error($error_msg, $error_type = E_USER_WARNING)
    {   
        var_dump("Smarty error: $error_msg");
    }

}

/* vim: set et: */

?>
