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
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * Represents a template in the database.
 *
 * @author Ted Kulp
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsTemplate extends CmsObjectRelationalMapping
{	
	var $params = array('id' => -1, 'name' => '', 'content' => '', 'stylesheet' => '', 'encoding' => '', 'active' => true, 'default' => false);
	var $field_maps = array('template_name' => 'name', 'default_template' => 'default', 'template_content' => 'content', 'template_id' => 'id');
	var $table = 'templates';
	var $id_field = 'template_id';
	//var $sequence = 'templates_seq';
	
	public $blocks = array();
	
	public function setup($first_time = false)
	{
		$this->create_has_many_association('stylesheet_associations', 'template_stylesheet_association', 'template_id', array('order' => 'order_num ASC'));
		$this->create_has_and_belongs_to_many_association('stylesheets', 'stylesheet', 'stylesheet_template_assoc', 'stylesheet_id', 'template_id', array('order' => 'order_num ASC'));
		$this->create_has_and_belongs_to_many_association('active_stylesheets', 'stylesheet', 'stylesheet_template_assoc', 'stylesheet_id', 'template_id', array('order' => 'order_num ASC', 'conditions' => 'stylesheets.active = 1'));
	}
	
	function process()
	{
		$smarty = cms_smarty();
		return $smarty->fetch('template:' . $this->id);
	}
	
	function usage_count()
	{
		$templateops = cmsms()->GetTemplateOperations();
		if ($this->id > -1)
			return $templateops->UsageCount($this->id);
		else
			return 0;
	}
	
	public static function get_default_template()
	{
		return cms_orm('CmsTemplate')->find(array('conditions' => array('default_template = ?', 1)));
	}
	
	function UsageCount()
	{
		return $this->usage_count();
	}
	
	/**
	 * Gets a list of content blocks in the template.  It does this by
	 * overriding the compiler class temporarily, doing an in-place
	 * compile, having the lexer pull out all the relevant tags,
	 * and then putting everything back in place.
	 *
	 * @return array
	 * @author Ted Kulp
	 */
	public function get_page_blocks()
	{
		$smarty = cms_smarty();
		
		//Setup an array to store the found blocks in
		$this->blocks = array();
		
		//Create a template object using the string resource (on the fly)
		$tpl = $smarty->createTemplate('string:' . $this->content);
		
		//Pull out the existing compiler object, if one already exists
		//for this particular resource
		$cur_obj = $tpl->compiler_object;
		
		//Create a new compiler object of our own, passing in the
		//lexer and parser class names from the current resource.
		//Pop it into the old compiler's place
		$new_obj = new CmsTemplateCompiler(
			$tpl->resource_object->template_lexer_class,
			$tpl->resource_object->template_parser_class,
			$smarty
		);
		$tpl->compiler_object = $new_obj;
		
		//Force a compilation
		$tpl->compileTemplateSource();
		
		//Put the old compiler back now that we're done
		$tpl->compiler_object = $cur_obj;
		
		//Grab the list of blocks from our special compiler
		$this->blocks = $new_obj->blocks;
		
		return $this->blocks;
	}
	
	function validate()
	{
		$this->validate_not_blank('name', lang('nofieldgiven',array(lang('title'))));
		$this->validate_not_blank('content', lang('nofieldgiven',array(lang('content'))));
		if ($this->name != '')
		{
			$result = cms_orm()->template->find_all_by_name($this->name);
			if (count($result) > 0)
			{
				if ($result[0]->id != $this->id)
				{
					$this->add_validation_error(lang('templateexists'));
				}
			}
		}
	}
	
	public function get_stylesheet_media_types($show_inactive = false)
	{
		$result = array();
		
		foreach ($this->active_stylesheets as $stylesheet)
		{
			foreach ($stylesheet->get_media_types_as_array() as $media_type)
			{
				if (!in_array($media_type, $result))
					$result[] = $media_type;
			}
		}

		return $result;
	}
	
	public function assign_stylesheet_by_id($stylesheet_id)
	{
		$exists = false;
		$cur_stylesheets = $this->stylesheets;
		foreach ($cur_stylesheets as $one_stylesheet)
		{
			if ($one_stylesheet->stylesheet_id == $stylesheet_id)
			{
				$exists = true;
				break;
			}
		}
		
		if (!$exists)
		{
			$new_assoc = new CmsTemplateStylesheetAssociation;
			$new_assoc->template_id = $this->id;
			$new_assoc->stylesheet_id = $stylesheet_id;
			$new_assoc->save();
		}
	}
	
	public function remove_assigned_stylesheet_by_id($stylesheet_id)
	{
		$cur_stylesheets = $this->stylesheet_associations;
		foreach ($cur_stylesheets as $one_stylesheet)
		{
			if ($one_stylesheet->stylesheet_id == $stylesheet_id)
			{
				$one_stylesheet->delete();
				break;
			}
		}
	}
	
	//Callback handlers
	function before_save()
	{
		//Make sure we have a default template set or we'll probably break stuff down the road
		if (cms_orm()->cms_template->find_count_by_default_template(1) == 0)
		{
			$this->default_template = 1;
		}
		CmsEvents::send_event( 'Core', ($this->id == -1 ? 'AddTemplatePre' : 'EditTemplatePre'), array('template' => &$this));
	}
	
	function after_save(&$result)
	{
		CmsEvents::send_event( 'Core', ($this->create_date == $this->modified_date ? 'AddTemplatePost' : 'EditTemplatePost'), array('template' => &$this));
		CmsCache::clear();
		CmsContentOperations::clear_cache();
	}
	
	function before_delete()
	{
		CmsEvents::send_event('Core', 'DeleteTemplatePre', array('template' => &$this));
	}
	
	function after_delete()
	{
		CmsEvents::send_event('Core', 'DeleteTemplatePost', array('template' => &$this));
		CmsCache::clear();
		CmsContentOperations::clear_cache();
	}
}

/**
 * Custom smarty compiler class to pull content blocks directly from the 
 * compiler and prepare them for the CmsTemplate class to use for it's various
 * display routines.
 *
 * @ignore
 * @package CMS
 * @author Ted Kulp
 */
class CmsTemplateCompiler extends Smarty_Internal_SmartyTemplateCompiler
{
	var $blocks = array();
	
	public function compileTag($tag, $args)
    {
		$result = parent::compileTag($tag, $args);
		
		if ($tag == 'content')
		{
			foreach ($args as &$arg)
			{
				if (preg_match('/^([\'"])(.+?)\1$/', $arg, $matches))
				{
					if (count($matches) > 2)
						$arg = $matches[2];
				}
			}
			
			$name = 'default';

			if (isset($args['name']))
			{
				$name = $args['name'];
				unset($args['block']);
			}
			else if (isset($args['block']))
			{
				$name = $args['block'];
				unset($args['block']);
			}

			if (!isset($args['type']))
			{
				$args['type'] = 'CmsHtmlContentType';
			}
			
			$this->blocks[$name] = $args;
		}
		
		return $result;
	}
}

/*
class Template extends CmsTemplate {}
*/

# vim:ts=4 sw=4 noet
?>