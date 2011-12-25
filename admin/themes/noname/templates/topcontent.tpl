<div class="toplevelcontent" style="float: left; width: 58%">
	{strip}
	{foreach from=$nodes item='node'}
	{if $node.show_in_menu && $node.url && $node.title}
	<div class="topnavitem">
		<a href="{$node.url}"{if isset($node.target)} target="{$node.target}"{/if}{if $node.selected} class="selected"{/if}>{$node.title}</a>
		{if $node.description}<span class="description">{$node.description}</span>{/if}
	</div>
	{/if}
	{/foreach}
	{/strip}
</div>
