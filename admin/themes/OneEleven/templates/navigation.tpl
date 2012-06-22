{strip}
{if !isset($depth)}{assign var='depth' value='0'}{/if}
{if $depth == '0'}
<nav class="navigation" id="menu" role="navigation">
	<div class="box-shadow">&nbsp;</div>
	<ul{if $depth == '0'} id="pagemenu"{/if}>
{/if}
{foreach from=$nav item='navitem' name='pos'}
{*<pre>{$nav|print_r}</pre>*}
	<li class="nav{if isset($navitem.module) || isset($navitem.firstmodule)} module{/if}{if !empty($navitem.selected)} current{/if}">
		<a href="{$navitem.url}" class="{$navitem.name|lower}{if isset($navitem.children)} parent{/if}"{if isset($navitem.target)} target="_blank"{/if} title="{if !empty($navitem.description)}{$navitem.description}{else}{$navitem.title}{/if}">
			{$navitem.title}	
		</a>
		{if $depth == '0'}
			<span class="open-nav" title="{'open'|lang}/{'close'|lang} {$navitem.title}">{'open'|lang}/{'close'|lang} {$navitem.title}</span>
		{/if}			
		{if isset($navitem.children)}
		{if $depth == '0'}<ul>{/if}
			{include file=$smarty.template nav=$navitem.children depth=$depth+1}
		{if $depth == '0'}</ul>{/if}
		{/if}
	</li>
{/foreach}
{if $depth == '0'}
	</ul>
</nav>	
{/if} 
{/strip}
