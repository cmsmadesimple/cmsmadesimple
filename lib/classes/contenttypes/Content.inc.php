<?php
# CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

class Content extends ContentBase
{	
	function __construct()
	{
		parent::__construct();
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

		$blocks = $this->parse_content_blocks_from_template($template);
		
		foreach ($blocks as $block)
		{
			$type = 'html';
			if ($this->has_property($block['id'] . '-block-type'))
			{
				$type = $this->get_property_value($block['id'] . '-block-type');
			}
			
			include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'blocktypes' . DIRECTORY_SEPARATOR . "block." . $type . ".inc.php");
			
			$class_name = camelize('block_' . $type);
			$class = new $class_name;
			
			$class->validate($this, $block['id']);
		}
	}
	
	function add_template(&$smarty)
	{
		$template = null;
		if ($this->template_id == '' || $this->template_id == -1)
			$template = cmsms()->template->find_by_default_template(1);
		else
			$template = cmsms()->template->find_by_id($this->template_id);

		$blocks = $this->parse_content_blocks_from_template($template);
		$smarty_tpl = '';
		
		foreach ($blocks as $block)
		{
			$type = 'html';
			if ($this->has_property($block['id'] . '-block-type'))
			{
				$type = $this->get_property_value($block['id'] . '-block-type');
			}
			
			include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'blocktypes' . DIRECTORY_SEPARATOR . "block." . $type . ".inc.php");
			
			$class_name = camelize('block_' . $type);
			$class = new $class_name;
			
			$smarty_tpl .= '
				<div class="pageoverflow">
		      		<p class="pagetext">'.humanize($block['id']).':</p>
		      		<p class="pageinput">
		      	  		'.$class->block_add_template($this, $block['id'], $template).'
		      		</p>
		      	</div>
				<input type="hidden" name="content[property]['.$block['id'].'-block-type]" value="'.$type.'" />
			';
		}
		
		$smarty->assign('cntnttemplate', $smarty_tpl);

		return array(cms_join_path(dirname(__FILE__), 'Content.tpl'));
	}
	
	function edit_template(&$smarty)
	{
		$template = null;
		if ($this->template_id == '' || $this->template_id == -1)
			$template = cmsms()->template->find_by_default_template(1);
		else
			$template = cmsms()->template->find_by_id($this->template_id);

		$blocks = $this->parse_content_blocks_from_template($template);
		$smarty_tpl = '';
		
		foreach ($blocks as $block)
		{
			$type = 'html';
			if ($this->has_property($block['id'] . '-block-type'))
			{
				$type = $this->get_property_value($block['id'] . '-block-type');
			}
			
			include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'blocktypes' . DIRECTORY_SEPARATOR . "block." . $type . ".inc.php");
			
			$class_name = camelize('block_' . $type);
			$class = new $class_name;
			
			$smarty_tpl .= '
				<div class="pageoverflow">
		      		<p class="pagetext">'.humanize($block['id']).':</p>
		      		<p class="pageinput">
		      	  		'.$class->block_edit_template($this, $block['id'], $template).'
		      		</p>
		      	</div>
				<input type="hidden" name="content[property]['.$block['id'].'-block-type]" value="'.$type.'" />
			';
		}
		
		$smarty->assign('cntnttemplate', $smarty_tpl);

		return array(cms_join_path(dirname(__FILE__), 'Content.tpl'));
	}
	
	function show($block_name = 'default')
	{
		$type = 'html';
		if ($this->has_property($block_name . '-block-type'))
		{
			$type = $this->get_property_value($block_name . '-block-type');
		}
		
		include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'blocktypes' . DIRECTORY_SEPARATOR . "block." . $type . ".inc.php");
		
		$class_name = camelize('block_' . $type);
		
		try
		{
			$class = new $class_name;
			return $class->show($this, $block_name);
		}
		catch (Exception $e)
		{
			return '';
		}
	}
	
	function parse_content_blocks_from_template(&$template)
	{
		$blocks = array();
		
		$pattern = '/{content([^}]*)}/';
		$pattern2 = '/([a-zA-z0-9]*)=["\']([^"\']+)["\']/';
		
		$matches = array();
		$result = preg_match_all($pattern, $template->content, $matches);
		if ($result && count($matches[1]) > 0)
		{
			foreach ($matches[1] as $wholetag)
			{
			    $id = 'default';
			    $name = 'default';
				
				$morematches = array();
				$result2 = preg_match_all($pattern2, $wholetag, $morematches);
				if ($result2)
				{
					$keyval = array();
					for ($i = 0; $i < count($morematches[1]); $i++)
					{
						$keyval[$morematches[1][$i]] = $morematches[2][$i];
					}

					foreach ($keyval as $key=>$val)
					{
						switch($key)
						{
							case 'block':
							case 'name':
								$id = strtolower(str_replace(' ', '_', $val));
								$name = $val;
								break;
						}
					}
				}
				
				$blocks[$name]['id'] = $id;
			}
		}
		
		return $blocks;
	}

}

Content::register_orm_class('Content');

# vim:ts=4 sw=4 noet
?>
