<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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

function news_module_content_set_properties(&$module)
{
	$module->mProperties->Add('int', 'number', 5);
	$module->mProperties->Add('string', 'dateformat', 'F j, Y, g:i a');
	$module->mProperties->Add('bool', 'swaptitledate', true);
	$module->mProperties->Add('string', 'category');
	$module->mProperties->Add('string', 'summary');
	$module->mProperties->Add('int', 'length', 80);
	$module->mProperties->Add('bool', 'showcategorywithtitle', true);
	$module->mProperties->Add('string', 'moretext', 'more...');
}

function news_module_content_edit(&$module)
{
	$text = '';

	$text .= '<tr><td>'.lang('title').':</td><td><input type="text" name="title" value="'.$module->mName.'"></td></tr>';
	$text .= '<tr><td>'.lang('menutext').':</td><td><input type="text" name="menutext" value="'.$module->mMenuText.'"></td></tr>';
	$text .= '<tr><td>'.lang('pagealias').':</td><td><input type="text" name="alias" value="'.$module->mAlias.'"></td></tr>';
	$text .= '<tr><td>'.lang('template').':</td><td>'.TemplateOperations::TemplateDropdown('template_id', $module->mTemplateId).'</td></tr>';
	$text .= '<tr><td>Number to Display (none show all):</td><td><input type="text" name="number" value="'.$module->GetPropertyValue('number').'" /></td></tr>';
	$text .= '<tr><td>Date Format:</td><td><input type="text" name="dateformat" value="'.$module->GetPropertyValue('dateformat').'" /></td></tr>';
	$text .= '<tr><td>Swap Date and Title:</td><td><input type="checkbox" name="swaptitledate" '.($module->GetPropertyValue('swaptitledate')?' checked="true"':'').' /></td></tr>';
	$text .= '<tr><td>Category:</td><td><input type="text" name="category" value="'.$module->GetPropertyValue('category').'" /></td></tr>';
	$text .= '<tr><td>Summary:</td><td><input type="text" name="summary" value="'.$module->GetPropertyValue('summary').'" /></td></tr>';
	$text .= '<tr><td>Summary Length:</td><td><input type="text" name="length" value="'.$module->GetPropertyValue('length').'" /></td></tr>';
	$text .= '<tr><td>Show Category:</td><td><input type="checkbox" name="showcategorywithtitle" '.($module->GetPropertyValue('showcategorywithtitle')?' checked=true':'').' /></td></tr>';
	$text .= '<tr><td>More Text:</td><td><input type="text" name="moretext" value="'.$module->GetPropertyValue('moretext').'" /></td></tr>';
	$text .= '<tr><td>'.lang('active').':</td><td><input type="checkbox" name="active"'.($module->mActive?' checked="true"':'').'></td></tr>';
	$text .= '<tr><td>'.lang('showinmenu').':</td><td><input type="checkbox" name="showinmenu"'.($module->mShowInMenu?' checked="true"':'').'></td></tr>';
	$text .= '<tr><td>'.lang('parent').':</td><td>'.ContentManager::CreateHierarchyDropdown($module->mId, $module->mParentId).'</td></tr>';

	return $text;
}

function news_module_content_fill_params(&$module, &$params)
{
	if (isset($params))
	{
		$parameters = array('number', 'category', 'summary', 'length', 'moretext');
		foreach ($parameters as $oneparam)
		{
			if (isset($params[$oneparam]))
			{
				$module->mProperties->SetValue($oneparam, $params[$oneparam]);
			}
		}
		if (isset($params['swaptitledate']))
		{
			$module->mProperties->SetValue('swaptitledate', true);
		}
		else
		{
			$module->mProperties->SetValue('swaptitledate', false);
		}
		if (isset($params['showcategorywithtitle']))
		{
			$module->mProperties->SetValue('showcategorywithtitle', true);
		}
		else
		{
			$module->mProperties->SetValue('showcategorywithtitle', false);
		}
		if (isset($params['title']))
		{
			$module->mName = $params['title'];
		}
		if (isset($params['menutext']))
		{
			$module->mMenuText = $params['menutext'];
		}
		if (isset($params['template_id']))
		{
			$module->mTemplateId = $params['template_id'];
		}
		if (isset($params['alias']))
		{
			$module->mAlias = $params['alias'];
		}
		if (isset($params['parent_id']))
		{
			if ($module->mParentId != $params['parent_id'])
			{
				$module->mHierarchy = '';
				$module->mItemOrder = -1;
			}
			$module->mParentId = $params['parent_id'];
		}
		if (isset($params['active']))
		{
			$module->mActive = true;
		}
		else
		{
			$module->mActive = false;
		}
		if (isset($params['showinmenu']))
		{
			$module->mShowInMenu = true;
		}
		else
		{
			$module->mShowInMenu = false;
		}
	}
}

function news_module_content_show(&$module)
{
	return "Hi!  I'm News content";
}

function news_module_content_get_url(&$module)
{
	global $gCms;
	$config = $gCms->config;

	$url = "";

	if ($config["assume_mod_rewrite"])
	{
		$url = $config["root_url"]."/".$module->mId.".shtml";
	}
	else 
	{
		$url = $config["root_url"]."/index.php?".$config["query_var"]."=".$module->mId;
	}

	return $url;
}

# vim:ts=4 sw=4 noet
?>
