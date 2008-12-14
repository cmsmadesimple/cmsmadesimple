<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

/**
 * Class to represent one module "request".  Basically a wrapper
 * object to hold a call to a module's action.  It will hold 
 * specifc module "session" variables, and also hold methods only 
 * used during a session, including form generation.
 *
 * @package cmsms
 * @author Ted Kulp
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 */
class CmsModuleRequest extends CmsObject
{
	var $module = null;
	var $id = '';
	var $return_id = '';

	function __construct($module, $id, $return_id = '')
	{
		parent::__construct();

		$this->module = $module;
		$this->id = $id;
		$this->return_id = $return_id;
	}

	/**
	 * Getter overload method.  Called when a field
	 * does not exist in the object's variable list.
	 *
	 * @param string The field to look up
	 * @return mixed The value for that field, if it exists
	 * @author Ted Kulp
	 **/
	function __get($n)
	{
		if ($this->module != null)
		{
			if (property_exists($this->module, $n))
			{
				return $this->module->$n;
			}
		}
	}
	
	/**
	 * Setter overload method.  Called when a field
	 * does not exist in the object's variable list.
	 *
	 * @param string The field to set
	 * @param mixed The value to set for said field
	 * @return void
	 * @author Ted Kulp
	 **/
	function __set($n, $val)
	{
		if ($this->module != null)
		{
			if (property_exists($this->module, $n))
			{
				$this->module->$n = $val;
			}
		}
	}
	
	/**
	 * Caller overload method.  Called when a method does not exist.
	 *
	 * @param string The name of the method called
	 * @param array The parameters sent along with that method call
	 * @return mixed The result of the method call
	 * @author Ted Kulp
	 **/
	function __call($function, $arguments)
	{
		if ($this->module != null)
		{
			if (method_exists($this->module, $function))
			{
				return call_user_func_array(array($this->module, $function), $arguments);
			}
		}
	}
	
	/**
	 * Used for navigation between "pages" of a module.	 Forms and links should
	 * pass an action with them so that the module will know what to do next.
	 * By default, DoAction will be passed 'default' and 'defaultadmin',
	 * depending on where the module was called from.  If being used as a module
	 * or content type, 'default' will be passed.  If the module was selected
	 * from the list on the admin menu, then 'defaultadmin' will be passed.
	 *
	 * @param string Name of the action to perform
	 * @param string The ID of the module
	 * @param string The parameters targeted for this module
	 */
	public function do_action($action_name, $params)
	{
		$return_id = $this->return_id; //avoid confusion
		$returnid = $return_id;

		if ($action_name != '')
		{
			$filename = cms_join_path($this->module->get_module_path(), 'action.' . $action_name . '.php');

			if (@is_file($filename))
			{
				{
					$gCms = cmsms(); //Backwards compatibility
					$db = cms_db();
					$config = cms_config();
					$smarty = cms_smarty();
					$id = $this->id;

					$smarty->assign_by_ref('request', $this);
					$smarty->assign('module_action',$action_name);

					include($filename);

				}
			}
		}
	}

	public function do_action_base($name, $incoming_params)
	{
		/*
		if ($returnid != '')
		{
			if (!$this->restrict_unknown_params && CmsApplication::get_preference('allowparamcheckwarnings', 0))
			{
				trigger_error('WARNING: '.$this->get_name().' is not properly cleaning input params.', E_USER_WARNING);
			}
			// used to try to avert XSS flaws, this will
			// clean as many parameters as possible according
			// to a map specified with the SetParameterType metods.
			$incoming_params = $this->clean_param_hash($incoming_params, !$this->restrict_unknown_params);
		}
		*/

		if (isset($incoming_params['lang']))
		{
			$this->module->curlang = $incoming_params['lang'];
			$this->module->langhash = array();
		}

		if (!isset($incoming_params['action']))
		{
			$incoming_params['action'] = $name;
		}

		return $this->do_action($name, $incoming_params);
	}
	
	static public function strip_extra_params(&$params, $default_params, $other_params_key = '')
	{
		$extra_params = array_diff_key($params, $default_params);
		$params = $default_params;
		if ($other_params_key != '' && isset($params[$other_params_key]) && is_array($params[$other_params_key]))
		{
			$extra_params = array_merge($extra_params, $params[$other_params_key]);
			unset($params[$other_params_key]);
		}
		return $extra_params;
	}
	
	static public function create_start_tag($name, $params, $self_close = false, $extra_html = '')
	{
		$text = "<{$name}";
		
		foreach ($params as $key=>$value)
		{
			$text .= " {$key}=\"{$value}\"";
		}
		
		if ($extra_html != '')
		{
			$text .= " {$extra}";
		}
		
		$text .= ($self_close ? ' />' : '>');
		
		return $text;
	}
	
	static public function create_end_tag($name)
	{
		return "</{$name}>";
	}
	
	/**
	 * Returns the start of a module form
	 * Parameters:
	 * 'action' - The action that this form should do when the form is submitted.  Defaults to 'default'.
	 * 'method' - Method to put in the form tag.  Defaults to 'post'.
	 * 'enctype' - Optional enctype for the form.  Only real option is 'multipart/form-data'.  Defaults to null.
	 * 'inline' - Boolean to tell whether or not we want the form's result to be "inline".  Defaults to false.
	 * 'id_suffix' - Text to append to the end of the id and name of the form.  Defaults to ''.
	 * 'extra' - Text to append to the <form>-statement, ex. for javascript-validation code.  Defaults to ''.
	 * 'html_id' - Id to use for the html id="".  Defaults to an autogenerated value.
	 * 'use_current_page_as_action' - A flag to determine if the action should just 
	 *      redirect back to this exact page.  Defaults to false.
	 * 'params' - An array of key/value pairs to add as extra hidden parameters.  These will merge into any
	 *      additional parameters you pass along in to the $params hash that aren't parsed by the function.
	 *
	 * @param array An array of parameters to pass to the method.  Unrecognized parameters will be added as hidden
	 *        variables to the form and merged correctly with anything in the 'params' key if passed.
	 * @param boolean Test whether keys are all valid or not.  Not helpful if you're 
	 *        passing extra key/values along, but good for debugging.
	 * @return string
	 * @author Ted Kulp
	 **/
	public function create_form_start($params = array(), $check_keys = false)
	{
		$default_params = array(
			'action' => coalesce_key($params, 'action', 'default', FILTER_SANITIZE_URL),
			'method' => coalesce_key($params, 'method', 'post', FILTER_SANITIZE_STRING),
			'enctype' => coalesce_key($params, 'enctype', '', FILTER_SANITIZE_STRING),
			'inline' => coalesce_key($params, 'inline', false, FILTER_VALIDATE_BOOLEAN),
			'id_suffix' => coalesce_key($params, 'id_suffix', '', FILTER_SANITIZE_STRING),
			'extra' => coalesce_key($params, 'extra', ''), 
			'use_current_page_as_action' => coalesce_key($params, 'use_current_page_as_action', false, FILTER_VALIDATE_BOOLEAN),
			'params' => coalesce_key($params, 'params', array()),
			'id' => coalesce_key($params, 'id', $this->id),
			'return_id' => coalesce_key($params, 'return_id', $this->return_id)
		);
		$default_params['html_id'] = coalesce_key($params,
			'html_id',
			CmsResponse::make_dom_id($default_params['id'].$default_params['action'].$default_params['id_suffix']),
			FILTER_SANITIZE_STRING
		);
		$default_params['html_name'] = coalesce_key($params,
			'html_name',
			$default_params['html_id'],
			FILTER_SANITIZE_STRING
		);

		if ($check_keys && !are_all_keys_valid($params, $default_params))
			throw new CmsInvalidKeyException(invalid_key($params, $default_params));
		
		//Strip out any straggling parameters to their own array
		//Merge in anything if it was passed in the params key to the method
		$extra_params = $this->strip_extra_params($params, $default_params, 'params');

		$form_count = cmsms()->get('formcount');
		if ($form_count == null)
			$form_count = 1;

		$mact = $this->module->get_name().','.$params['id'].','.$params['action'].','.($inline == true?1:0);
		$goto = '';
		$use_current_page_as_action = $params['use_current_page_as_action']; unset($params['use_current_page_as_action']);
		if ($use_current_page_as_action)
			$goto = CmsRequest::get_requested_uri();
		else
			$goto = ($params['return_id'] == '' ? 'moduleinterface.php' : 'index.php');
		
		$form_params = array(
			'id' => $params['html_id'],
			'name' => $params['html_name'],
			'method' => $params['method'],
			'action' => $goto
		);
		
		if ($enctype != '')
		{
			$form_params['enctype'] = $params['enctype'];
		}
		
		$extra = '';
		if ($params['extra'])
		{
			$extra = $params['extra'];
			unset($params['extra']);
		}
		
		$text .= $this->create_start_tag('form', $form_params, false, $extra);
		$text .= $this->create_start_tag('div', array('class' => 'hidden'));
		
		if (!$use_current_page_as_action)
			$text .= $this->create_start_tag('input', array('type' => 'hidden', 'name' => 'mact', 'value' => $mact), true);
			
		if ($params['return_id'] != '')
		{
			$text .= $this->create_start_tag('input', array('type' => 'hidden', 'name' => $params['id'].'returnid', 'value' => $params['return_id']), true);
			if ($inline)
			{
				$text .= $this->create_start_tag('input', array('type' => 'hidden', 'name' => cms_config()->get('query_var'), 'value' => $params['return_id']), true);
			}
		}
		
		foreach ($extra_params as $key=>$value)
		{
			if ($key != 'module' && $key != 'action' && $key != 'id')
				$text .= $this->create_start_tag('input', array('type' => 'hidden', 'name' => $params['id'].$key, 'value' => $value), true);
		}
		
		$text .= $this->create_end_tag('div'); //end "hidden"

		cmsms()->set('formcount', $form_count + 1);

		return $text;
	}
	
	/**
	 * Returns the end of a module form
	 * Parameters:
	 *   none
	 *
	 * @param array An array of parameters to pass to the method.  Unrecognized parameters will be added as hidden
	 *        variables to the form and merged correctly with anything in the 'params' key if passed.
	 * @param boolean Test whether keys are all valid or not.  Not helpful if you're 
	 *        passing extra key/values along, but good for debugging.
	 * @return string
	 * @author Ted Kulp
	 **/
	public function create_form_end($params = array(), $check_keys = false)
	{
		return $this->create_end_tag('form');
	}
	
	/**
	 * Returns the xhtml equivalent of an input textbox.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 * Parameters:
	 * 'name' - The name of the field.  Defaults to 'input'.
	 * 'value' - The value of the field.  Defaults to ''.
	 * 'size' - The length of the input field when displayed.  Defaults to 10.
	 * 'maxlength' - The max length of the value allowed.  Defaults to 255.
	 * 'extra' - Text to append to the <input>-statement, ex. for javascript-validation code.  Defaults to ''.
	 * 'html_id' - Id to use for the html id="".  Defaults to an autogenerated value.
	 * 'label' - If set to a string, a label will be created with the proper "for" value for the input.
	 * 'label_extra' - Text to append to the <label>-statement, ex. for javascript-validation code.  Defaults to ''.
	 * 'in_between_text' - Text to put between the label and input fields.  Defaults to ''.
	 * 'password' - Boolean to tell whether or not we want it to be a password input.  Defaults to false.
	 * 'params' - An array of key/value pairs to add as attributes to the input command.  These will merge into any
	 *      additional parameters you pass along in to the $params hash that aren't parsed by the function.
	 *
	 * @param array An array of parameters to pass to the method.  Unrecognized parameters will be added as attributes to the 
	 *        tag and merged correctly with anything in the 'params' key if passed.
	 * @param boolean Test whether keys are all valid or not.  Not helpful if you're 
	 *        passing extra key/values along, but good for debugging.
	 * @return string
	 * @author Ted Kulp
	 */
	public function create_input_text($params = array(), $check_keys = false)
	{
		$default_params = array(
			'name' => $this->id . coalesce_key($params, 'name', 'input', FILTER_SANITIZE_STRING),
			'value' => coalesce_key($params, 'value', '', FILTER_SANITIZE_STRING),
			'size' => coalesce_key($params, 'size', 25, FILTER_SANITIZE_NUMBER_INT),
			'maxlength' => coalesce_key($params, 'maxlength', 255, FILTER_SANITIZE_NUMBER_INT),
			'extra' => coalesce_key($params, 'extra', ''),
			'label' => coalesce_key($params, 'label', '', FILTER_SANITIZE_STRING),
			'label_extra' => coalesce_key($params, 'label_extra', ''),
			'in_between_text' => coalesce_key($params, 'in_between_text', ''),
			'password' => coalesce_key($params, 'password', false, FILTER_VALIDATE_BOOLEAN),
			'params' => coalesce_key($params, 'params', array())
		);
		$default_params['id'] = coalesce_key($params,
			'html_id',
			CmsResponse::make_dom_id($default_params['name']),
			FILTER_SANITIZE_STRING
		);
		
		if ($check_keys && !are_all_keys_valid($params, $default_params))
			throw new CmsInvalidKeyException(invalid_key($params, $default_params));
		
		//Combine EVERYTHING together into a big managerie
		$params = array_merge($default_params, $this->strip_extra_params($params, $default_params, 'params'));
		unset($params['params']);
		
		$extra = '';
		if ($params['extra'])
		{
			$extra = $params['extra'];
		}
		unset($params['extra']);
		
		$params['type'] = ($params['password'] == true ? 'password' : 'text');
		
		$text = '';
		
		if ($params['label'] != '')
		{
			$text .= $this->create_start_tag('form', array('for' => $params['id']), true, $params['label_extra']);
			$text .= $params['in_between_text'];
		}
		
		unset($params['label']);
		unset($params['label_extra']);
		
		$text .= $this->create_start_tag('input', $params, true, $extra);
		
		return $text;
	}
	
	/**
	 * Returns the xhtml equivalent of an hidden input.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 * Parameters:
	 * 'name' - The name of the field.  Defaults to 'input'.
	 * 'value' - The value of the field.  Defaults to ''.
	 * 'extra' - Text to append to the <input>-statement, ex. for javascript-validation code.  Defaults to ''.
	 * 'html_id' - Id to use for the html id="".  Defaults to an autogenerated value.
	 * 'params' - An array of key/value pairs to add as attributes to the input command.  These will merge into any
	 *      additional parameters you pass along in to the $params hash that aren't parsed by the function.
	 *
	 * @param array An array of parameters to pass to the method.  Unrecognized parameters will be added as attributes to the 
	 *        tag and merged correctly with anything in the 'params' key if passed.
	 * @param boolean Test whether keys are all valid or not.  Not helpful if you're 
	 *        passing extra key/values along, but good for debugging.
	 * @return string
	 * @author Ted Kulp
	 */
	public function create_input_hidden($params = array(), $check_keys = false)
	{
		$default_params = array(
			'name' => $this->id . coalesce_key($params, 'name', 'input', FILTER_SANITIZE_STRING),
			'value' => coalesce_key($params, 'value', '', FILTER_SANITIZE_STRING),
			'extra' => coalesce_key($params, 'extra', ''),
			'params' => coalesce_key($params, 'params', array())
		);
		$default_params['id'] = coalesce_key($params,
			'html_id',
			CmsResponse::make_dom_id($default_params['name']),
			FILTER_SANITIZE_STRING
		);
		
		if ($check_keys && !are_all_keys_valid($params, $default_params))
			throw new CmsInvalidKeyException(invalid_key($params, $default_params));
		
		$params['type'] = 'hidden';
		
		//Combine EVERYTHING together into a big managerie
		$params = array_merge($default_params, $this->strip_extra_params($params, $default_params, 'params'));
		unset($params['params']);
		
		$extra = '';
		if ($params['extra'])
		{
			$extra = $params['extra'];
		}
		unset($params['extra']);

		return $this->create_start_tag('input', $params, true, $extra);
	}
	
	/**
	 * Returns the xhtml equivalent of an checkbox input.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.  Also adds the feature 
	 * of making sure that even unchecked checkboxes return a value back to the form.
	 * Parameters:
	 * 'name' - The name of the field.  Defaults to 'input'.
	 * 'checked' - Boolean of whether or not the checkbox is checked.  Defaults to false.
	 * 'extra' - Text to append to the <input>-statement, ex. for javascript-validation code.  Defaults to ''.
	 * 'html_id' - Id to use for the html id="".  Defaults to an autogenerated value.
	 * 'params' - An array of key/value pairs to add as attributes to the input command.  These will merge into any
	 *      additional parameters you pass along in to the $params hash that aren't parsed by the function.
	 *
	 * @param array An array of parameters to pass to the method.  Unrecognized parameters will be added as attributes to the 
	 *        tag and merged correctly with anything in the 'params' key if passed.
	 * @param boolean Test whether keys are all valid or not.  Not helpful if you're 
	 *        passing extra key/values along, but good for debugging.
	 * @return string
	 * @author Ted Kulp
	 */
	public function create_input_checkbox($params = array(), $check_keys = false)
	{
		$default_params = array(
			'name' => $this->id . coalesce_key($params, 'name', 'input', FILTER_SANITIZE_STRING),
			'checked' => coalesce_key($params, 'checked', false, FILTER_VALIDATE_BOOLEAN),
			'extra' => coalesce_key($params, 'extra', ''),
			'params' => coalesce_key($params, 'params', array())
		);
		$default_params['id'] = coalesce_key($params,
			'html_id',
			CmsResponse::make_dom_id($default_params['name']),
			FILTER_SANITIZE_STRING
		);
		
		if ($check_keys && !are_all_keys_valid($params, $default_params))
			throw new CmsInvalidKeyException(invalid_key($params, $default_params));
			
		//Combine EVERYTHING together into a big managerie
		$params = array_merge($default_params, $this->strip_extra_params($params, $default_params, 'params'));
		unset($params['params']);
		
		$params['type'] = 'checkbox';
		$params['value'] = '1';
		
		if ($params['checked'])
			$params['checked'] = 'checked';
		else
			unset($params['checked']);
		
		$extra = '';
		if ($params['extra'])
		{
			$extra = $params['extra'];
		}
		unset($params['extra']);
		
		return $this->create_start_tag('input', array('type' => 'hidden', 'name' => $params['name'], 'value' => '0'), true) . 
			$this->create_start_tag('input', $params, true, $extra);
	}
	
	/**
	 * Returns the xhtml equivalent of an submit button.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 * Parameters:
	 * 'name' - The name of the field.  Defaults to 'input'.
	 * 'value' - The value (text) of the button.  Defaults to ''.
	 * 'extra' - Text to append to the <input>-statement, ex. for javascript-validation code.  Defaults to ''.
	 * 'html_id' - Id to use for the html id="".  Defaults to an autogenerated value.
	 * 'image' - The name of an image to display instead of the text.  Defaults to ''.
	 * 'confirm_text' - If set, a message to display to confirm the click.  Defaults to ''.
	 * 'params' - An array of key/value pairs to add as attributes to the input tag.  These will merge into any
	 *      additional parameters you pass along in to the $params hash that aren't parsed by the function.
	 * 'reset' - Boolean of whether or not this is a reset buton.  Defaults to false.
	 *
	 * @param array An array of parameters to pass to the method.  Unrecognized parameters will be added as attributes to the 
	 *        tag and merged correctly with anything in the 'params' key if passed.
	 * @param boolean Test whether keys are all valid or not.  Not helpful if you're 
	 *        passing extra key/values along, but good for debugging.
	 * @return string
	 * @author Ted Kulp
	 */
	public function create_input_submit($params = array(), $check_keys = false)
	{
		$default_params = array(
			'name' => $this->id . coalesce_key($params, 'name', 'input', FILTER_SANITIZE_STRING),
			'value' => coalesce_key($params, 'value', '', FILTER_SANITIZE_STRING),
			'extra' => coalesce_key($params, 'extra', ''),
			'image' => coalesce_key($params, 'image', '', FILTER_SANITIZE_STRING),
			'confirm_text' => coalesce_key($params, 'confirm_text', '', FILTER_SANITIZE_STRING),
			'reset' => coalesce_key($params, 'reset', false, FILTER_VALIDATE_BOOLEAN),
			'params' => coalesce_key($params, 'params', array())
		);
		$default_params['id'] = coalesce_key($params,
			'html_id',
			CmsResponse::make_dom_id($default_params['name']),
			FILTER_SANITIZE_STRING
		);
		
		if ($check_keys && !are_all_keys_valid($params, $default_params))
			throw new CmsInvalidKeyException(invalid_key($params, $default_params));

		//Combine EVERYTHING together into a big managerie
		$params = array_merge($default_params, $this->strip_extra_params($params, $default_params, 'params'));
		unset($params['params']);
		
		if ($reset)
		{
			$params['type'] = 'reset';
		}
		else if ($params['image'] != '')
		{
			$params['type'] = 'image';
			$params['src'] = CmsConfig::get('root_url') . '/' . $params['image'];
		}
		else
		{
			$params['type'] = 'submit';
		}
		unset($params['image']);
		
		$extra = '';
		if ($params['extra'])
		{
			$extra = $params['extra'];
			if ($params['confirm_text'] != '')
 			{
				$extra .= ' onclick="return confirm(\''.$params['confirm_text'].'\');"';
			}
		}
		unset($params['extra']);
		unset($params['confirm_text']);
		
		return $this->create_start_tag('input', $params, true, $extra);
	}
	
	/**
	 * Returns the xhtml equivalent of a dropdown input.  This is basically a nice little wrapper
	 * to make sure that id's are placed in names and also that it's xhtml compliant.
	 * Parameters:
	 * 'name' - The name of the field.  Defaults to 'input'.
	 * 'items' - An associative array of key/values to represent the value and text of the items in the list.  This can also be
	 *           passed a string in the form of 'key,value,key,value'.  Defaults to array().
	 * 'selected_value' - A string that will set the matching item (by value) as selected.  Defaults = ''.
	 * 'selected_index' - An integer that will set the matching item (by index) as selected.  Defaults to -1 (no selection).
	 * 'extra' - Text to append to the <input>-statement, ex. for javascript-validation code.  Defaults to ''.
	 * 'html_id' - Id to use for the html id="".  Defaults to an autogenerated value.
	 * 'flip_items' - Boolean that tells whether or not the value and text of the given items should be swapped.  Defaults to false.
	 * 'params' - An array of key/value pairs to add as attributes to the input tag.  These will merge into any
	 *      additional parameters you pass along in to the $params hash that aren't parsed by the function.
	 *
	 * @param array An array of parameters to pass to the method.  Unrecognized parameters will be added as attributes to the 
	 *        tag and merged correctly with anything in the 'params' key if passed.
	 * @param boolean Test whether keys are all valid or not.  Not helpful if you're 
	 *        passing extra key/values along, but good for debugging.
	 * @return string
	 * @author Ted Kulp
	 */
	public function create_input_dropdown($params = array(), $check_keys = false)
	{
		$default_params = array(
			'name' => $this->id . coalesce_key($params, 'name', 'input', FILTER_SANITIZE_STRING),
			'items' => coalesce_key($params, 'items', array()),
			'selected_value' => coalesce_key($params, 'selected_value', '', FILTER_SANITIZE_STRING),
			'selected_index' => coalesce_key($params, 'selected_index', -1, FILTER_SANITIZE_NUMBER_INT),
			'extra' => coalesce_key($params, 'extra', ''),
			'flip_items' => coalesce_key($params, 'flip_items', false, FILTER_VALIDATE_BOOLEAN),
			'params' => coalesce_key($params, 'params', array())
		);
		$default_params['id'] = coalesce_key($params,
			'html_id',
			CmsResponse::make_dom_id($default_params['name']),
			FILTER_SANITIZE_STRING
		);
		
		if ($check_keys && !are_all_keys_valid($params, $default_params))
			throw new CmsInvalidKeyException(invalid_key($params, $default_params));

		//Combine EVERYTHING together into a big managerie
		$params = array_merge($default_params, $this->strip_extra_params($params, $default_params, 'params'));
		unset($params['params']);
		
		$selected_index = $params['selected_index']; unset($params['selected_index']);
		$selected_value = $params['selected_value']; unset($params['selected_value']);
		
		$items = $params['items'];
		unset($params['items']);
		if (!is_array($items) && strlen($items) > 0)
		{
			$ary = array_chunk(explode(',', $items), 2);
			$items = array();
			foreach ($ary as $one_item)
			{
				if (count($one_item) == 2)
					$items[$one_item[0]] = $one_item[1];
			}
		}
		
		if ($params['flip_items'])
			$items = array_flip($items);
		unset($params['flip_items']);
		
		$extra = '';
		if ($params['extra'])
		{
			$extra = $params['extra'];
		}
		unset($params['extra']);
		
		$text = $this->create_start_tag('select', $params, false, $extra);
		
		$count = 0;
		foreach ($items as $k=>$v)
		{
			$hash = array('value' => $k);
			if ($count == $selected_index || $k == $selected_value)
			{
				$hash['selected'] = 'selected';
			}
			$text .= $this->create_start_tag('option', $hash) . $v . $this->create_end_tag('option');
			$count++;
		}
		
		$text .= $this->create_end_tag('select');
		
		return $text;
	}
}

class CmsInvalidKeyException extends Exception
{
	// Redefine the exception so message isn't optional
	public function __construct($message = null, $code = 0)
	{
		if ($message != null)
			$message = "Invalid Key: " . $message;

		parent::__construct($message, $code);
	}
}

# vim:ts=4 sw=4 noet
?>