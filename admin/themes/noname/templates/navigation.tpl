{strip}
{if !isset($depth)}{assign var='depth' value='0'}{/if}
{if $depth == '0'}
<nav class="navigation" id="menu" role="navigation">
	<div class="box-shadow">&nbsp;</div>
	<ul>
{/if}
{foreach from=$nav item='navitem' name='pos'}
	<li class="{$navitem.name|lower}{if !empty($navitem.selected)} current{/if}">
		<a href="{$navitem.url}" title="{if !empty($navitem.description)}{$navitem.description}{else}{$navitem.title}{/if}">{$navitem.title}</a>
		{if isset($navitem.children)}
		{if $depth == '0'}
		<ul>
			{/if}
			{include file=$smarty.template nav=$navitem.children depth=$depth+1}
			{if $depth == '0'}
		</ul>
		{/if}
		{/if}
	</li>
{/foreach}
{if $depth == '0'}
	</ul>
</nav>	
{/if} 
{/strip}
