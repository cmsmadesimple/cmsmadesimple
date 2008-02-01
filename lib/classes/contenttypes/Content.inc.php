<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2007 by Ted Kulp (ted@cmsmadesimple.org)
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

class Content extends CmsContentBase
{	
	function __construct()
	{
		parent::__construct();
		$this->preview = true;
	}
	
	public static function create_preview_object($template_object)
	{
		$blocks = CmsTemplateOperations::parse_content_blocks_from_template($template_object);
		
		foreach ($blocks as $block)
		{
			$type = 'html';
			$obj->set_property_value($block['id'] . '-block-type', $type);
			$obj->set_property_value($block['id'] . '-content', 'This is the content for block: ' . $block['id']);
		}
		
		return $obj;
	}

	function friendly_name()
	{
		return 'Content';
	}
	
	function validate()
	{
		parent::validate();
		
		$template = null;
		if ($this->template_id == '' || $this->template_id == -1)
			$template = cmsms()->template->find_by_default_template(1);
		else
			$template = cmsms()->template->find_by_id($this->template_id);

		$blocks = CmsTemplateOperations::parse_content_blocks_from_template($template);
		
		foreach ($blocks as $block)
		{
			$type = 'html';
			if ($this->has_property($block['id'] . '-block-type'))
			{
				$type = $this->get_property_value($block['id'] . '-block-type');
			}
			
			$block_type_obj = $this->get_block_type_object($type);
			
			try
			{
				if ($block_type_obj != null)
				{			
					require_once($block_type_obj->filename);
			
					$class_name = camelize('block_' . $type);
					$class = new $class_name;
			
					$class->validate($this, $block['id']);
				}
				else
				{
					throw Exception("Unknown block type");
				}
			}
			catch (Exception $e)
			{
				$this->add_validation_error(lang('unknownvalidationerror'));
			}
		}
	}
	
	function add_template(&$smarty, $lang = 'en_US')
	{
		$template = null;
		if ($this->template_id == '' || $this->template_id == -1)
			$template = cmsms()->template->find_by_default_template(1);
		else
			$template = cmsms()->template->find_by_id($this->template_id);

		$blocks = CmsTemplateOperations::parse_content_blocks_from_template($template);
		$contents = '';
		
		foreach ($blocks as $block)
		{
			$type = 'html';
			if ($this->has_property($block['id'] . '-block-type'))
			{
				$type = $this->get_property_value($block['id'] . '-block-type');
			}
		
			$contents .= '
				<div class="accordion_header">' . humanize($block['id']) . '</div>
				<div class="accordion_content">
					<div class="pageoverflow">
						<p class="pagetext">'.lang('blocktype').':</p>
						<select name="content[property]['.$block['id'].'-block-type]" id="'.$block['id'].'-block-type" onchange="cms_ajax_change_block_type($(\'#contentform\').serializeForCmsAjax(), \''.$block['id'].'\', \'\' + $(\'#'.$block['id'].'-block-type\').val()); return false;">
							'.$this->create_block_type_options($type).'
						</select>
					</div>
					<div class="pageoverflow" id="content-form-' .  $block['id'] . '">
						'.$this->create_block_type($block['id']).'
			      	</div>
				</div>
			';
		}
		
		$smarty_tpl = '
		<div style="clear: left;">
			<div id="page_accordion">
				' . $contents . '
			</div>
			<script type="text/javascript">
			{literal}
			<!--
				//$(\'#page_accordion\').accordion({ header: \'div.accordion_header\' });
			//-->
			{/literal}
			</script>
		</div>
		';
		
		$smarty->assign('cntnttemplate', $smarty_tpl);

		return array(cms_join_path(dirname(__FILE__), 'Content.tpl'));
	}
	
	function edit_template(&$smarty, $lang = 'en_US')
	{
		$template = null;
		if ($this->template_id == '' || $this->template_id == -1)
			$template = cmsms()->template->find_by_default_template(1);
		else
			$template = cmsms()->template->find_by_id($this->template_id);

		$blocks = CmsTemplateOperations::parse_content_blocks_from_template($template);
		$contents = '';
		
		foreach ($blocks as $block)
		{
			$type = 'html';
			if ($this->has_property($block['id'] . '-block-type'))
			{
				$type = $this->get_property_value($block['id'] . '-block-type');
			}
						
			$contents .= '
				<div class="accordion_header">' . humanize($block['id']) . '</div>
				<div class="accordion_content">
					<div class="pageoverflow">
						<p class="pagetext">'.lang('blocktype').':</p>
						<p class="pageinput">
							<select name="content[property]['.$block['id'].'-block-type]" id="'.$block['id'].'-block-type" onchange="cms_ajax_change_block_type($(\'#contentform\').serializeForCmsAjax(), \''.$block['id'].'\', \'\' + $(\'#'.$block['id'].'-block-type\').val()); return false;">
								'.$this->create_block_type_options($type).'
							</select>
						</p>
					</div>
					<div class="pageoverflow" id="content-form-' .  $block['id'] . '">
						'.$this->create_block_type($block['id'], $lang).'
			      	</div>
				</div>
			';
		}
		
		$smarty_tpl = '
		<div style="clear: left;">
			<div id="page_accordion">
				' . $contents . '
			</div>
			<script type="text/javascript">
			{literal}
			<!--
				//$(\'#page_accordion\').accordion({ header: \'div.accordion_header\' });
			//-->
			{/literal}
			</script>
		</div>
		';
		
		$smarty->assign('cntnttemplate', $smarty_tpl);

		return array(cms_join_path(dirname(__FILE__), 'Content.tpl'));
	}
	
	public function create_block_type($block_id, $lang = 'en_US')
	{
		$template = null;
		if ($this->template_id == '' || $this->template_id == -1)
			$template = cmsms()->template->find_by_default_template(1);
		else
			$template = cmsms()->template->find_by_id($this->template_id);
			
		$type = 'html';
		if ($this->has_property($block_id . '-block-type'))
		{
			$type = $this->get_property_value($block_id . '-block-type');
		}
		
		$block_type_obj = $this->get_block_type_object($type);
		
		if ($block_type_obj != null)
		{			
			require_once($block_type_obj->filename);
		
			$class_name = camelize('block_' . $block_type_obj->type);
			$class = new $class_name;

			return $class->block_edit_template($this, $block_id, $template, $lang);
		}
		
		return '';
	}
	
	private function create_block_type_options($selected = '')
	{
		$result = '';
		$block_types = cmsms()->blocktypes;
		
		foreach ($block_types as $block_type)
		{
			$result .= '<option value="' . $block_type->type . '"';
			if ($block_type->type == $selected)
			{
				$result .= ' selected="selected"';
			}
			$result .= '>' . $block_type->friendlyname . '</option>';
		}
		
		return $result;
	}
	
	function show($block_name = 'default', $lang = 'en_US')
	{
		$type = 'html';
		if ($this->has_property($block_name . '-block-type'))
		{
			$type = $this->get_property_value($block_name . '-block-type');
		}
		
		$block_type_obj = $this->get_block_type_object($type);
		
		if ($block_type_obj != null)
		{
			require_once($block_type_obj->filename);
		}

		try
		{
			$class_name = camelize('block_' . $type);
			$class = new $class_name;
			return $class->show($this, $block_name, $lang);
		}
		catch (Exception $e)
		{
			return '';
		}
	}
	
	private function get_block_type_object($type = 'html')
	{
		$block_type_obj = null;
		
		foreach(cmsms()->blocktypes as $block_type)
		{
			if ($block_type->type == $type)
			{
				$block_type_obj = $block_type;
			}
		}
		
		return $block_type_obj;
	}

}

# vim:ts=4 sw=4 noet
?>