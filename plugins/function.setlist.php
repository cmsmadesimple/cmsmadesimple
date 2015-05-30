<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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

function smarty_function_setlist($params, &$smarty) 
{
	$newlist = array();
	
	if (!isset($params['var']))
	{
		$params['var']='var';
	}
	
	if (substr($params['value'],0,1) != '{')
	{
		$params['value'] = '{'.$params['value'];
	}
	
	$opens = substr_count($params['value'], '{');
	$closes =  substr_count($params['value'], '}');
	while ($closes < $opens)
	{
		$params['value'] = $params['value'].'}';
		$closes += 1;
	}
	
	$newlist = json_decode($params['value'],true);
	$smarty->assign($params['var'],$newlist);
}

function smarty_cms_help_function_setlist() {
?>
	<p>Populate arrays directly in your template, e.g.:</p>
	<pre>
		{setlist var='varname' value='"red":"#f00","green":"#0f0","blue":"#00f","violet":"#f0f","yellow":"#ff0"'}
		{foreach from=$varname key=color item=colorcode}
		    {$color} is {$colorcode}<br />
		{/foreach}
	</pre>
	<p>It uses JSON syntax (with implicit curly-brace wrappers), so you can do crazy stuff if you choose to:</p>
	<pre>
		{capture assign="json_sample_struct"}"layered":{ldelim}"bar":"baz"{rdelim},"flat":"blank","layered2":{ldelim}"qux":"quux","crox":"bagg"{rdelim}{/capture}
		{setlist var='nested' value=$json_sample_struct}
	</pre>
	<p>This is useful for setting up lists of similar structure in your CSS.</p>
<?php
}

function smarty_cms_about_function_setlist() {
?>
	<p>Author: SjG</p>

	<p>Change History:</p>
	<ul>
		<li>None</li>
	</ul>
<?php
}
?>