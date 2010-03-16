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
         * Otherwise we need to load plugin file and look for the function
         * inside it.
         */
        else if ($plugin_file = $this->_get_plugin_filepath('function', $tag_command)) {
            $found = true;

            include_once $plugin_file;

            $plugin_func = 'smarty_cms_function_' . $tag_command;
            if (!function_exists($plugin_func)) {
                $message = "plugin function $plugin_func() not found in $plugin_file\n";
                $have_function = false;
            } else {
                $this->_plugins['function'][$tag_command] = array($plugin_func, null, null, null, true);

            }
        }

        if (!$found) {
            return parent::_compile_custom_tag($tag_command, $tag_args, $tag_modifier, $output);
        } else if (!$have_function) {
            #$this->_syntax_error($message, E_USER_WARNING, __FILE__, __LINE__);
            #return true;
            return parent::_compile_custom_tag($tag_command, $tag_args, $tag_modifier, $output);
        }

        /* declare plugin to be loaded on display of the template that
           we compile right now */
        $this->_add_plugin('function', $tag_command);

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

        #var_dump($output);

        return true;
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
