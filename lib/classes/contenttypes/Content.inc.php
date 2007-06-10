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
		$obj = new Content();
		$blocks = $obj->parse_content_blocks_from_template($template_object);
		
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

		$blocks = $this->parse_content_blocks_from_template($template);
		
		foreach ($blocks as $block)
		{
			$type = 'html';
			if ($this->has_property($block['id'] . '-block-type'))
			{
				$type = $this->get_property_value($block['id'] . '-block-type');
			}
			
			try
			{
				include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'blocktypes' . DIRECTORY_SEPARATOR . "block." . $type . ".inc.php");
			
				$class_name = camelize('block_' . $type);
				$class = new $class_name;
			
				$class->validate($this, $block['id']);
			}
			catch (Exception $e)
			{
				$this->add_validation_error(lang('unknownvalidationerror'));
			}
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
		
		if ($template != null)
		{		
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
		}
		
		return $blocks;
	}

}

/*
$blocktypes =& $gCms->blocktypes;

#Load block types
$dir = cms_join_path($dirname,'lib','classes','blocktypes');
$handle=opendir($dir);
while ($file = readdir ($handle)) 
{
    $path_parts = pathinfo($file);
    if ($path_parts['extension'] == 'php')
    {
		$obj = new CmsBlockTypePlaceholder();
		$obj->type = str_replace('block.', '', strtolower(basename($file, '.inc.php')));
		$obj->filename = cms_join_path($dir, $file);
		$obj->loaded = false;
		$obj->friendlyname = str_replace('block.', '', basename($file, '.inc.php'));
		$blocktypes[str_replace('block.', '', strtolower(basename($file, '.inc.php')))] = $obj;
    }
}
closedir($handle);
*/

# vim:ts=4 sw=4 noet
?>