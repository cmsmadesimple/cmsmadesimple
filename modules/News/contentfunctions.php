<?php

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

	$text .= '<tr><td>'.lang('title').':</td><td><input type="text" name="title" value="'.$this->mName.'"></td></tr>';
	$text .= '<tr><td>'.lang('menutext').':</td><td><input type="text" name="menutext" value="'.$this->mMenuText.'"></td></tr>';
	$text .= '<tr><td>'.lang('pagealias').':</td><td><input type="text" name="alias" value="'.$this->mAlias.'"></td></tr>';
	$text .= '<tr><td>Number to Display (none show all):</td><td><input type="text" name="numbertodisplay" value="'.$module->GetPropertyValue('number').'" /></td></tr>';
	$text .= '<tr><td>Date Format:</td><td><input type="text" name="dateformat" value="'.$module->GetPropertyValue('dateformat').'" /></td></tr>';
	$text .= '<tr><td>Swap Date and Title:</td><td><input type="checkbox" name="swapdatetitle" '.($module->GetPropertyValue('swaptitledate')?' checked="true"':'').' /></td></tr>';
	$text .= '<tr><td>Category:</td><td><input type="text" name="category" value="'.$module->GetPropertyValue('category').'" /></td></tr>';
	$text .= '<tr><td>Summary:</td><td><input type="text" name="summary" value="'.$module->GetPropertyValue('summary').'" /></td></tr>';
	$text .= '<tr><td>Summary Length:</td><td><input type="text" name="summarylength" value="'.$module->GetPropertyValue('length').'" /></td></tr>';
	$text .= '<tr><td>Show Category:</td><td><input type="checkbox" name="showcategorywithtitle" '.($module->GetPropertyValue('showcategorywithtitle')?' checked=true':'').' /></td></tr>';
	$text .= '<tr><td>More Text:</td><td><input type="text" name="moretext" value="'.$module->GetPropertyValue('moretext').'" /></td></tr>';

	return $text;
}

?>
