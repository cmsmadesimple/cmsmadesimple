	{strip}

	{foreach from=$nodes item='node' name='box'}
	{if $node.show_in_menu && $node.url && $node.title}
	<div class="dashboard-box{if $smarty.foreach.box.index % 3 == 2} last{/if}">
		<img src="themes/noname/images/icons/topfiles/{$node.name}.png" width="48" height="48" alt="{$node.title}"} />
		<a href="{$node.url}"{if isset($node.target)} target="{$node.target}"{/if}{if $node.selected} class="selected"{/if}>{$node.title}</a>
		{if $node.description}<span class="description">{$node.description}</span>{/if}
	</div>
	{/if}
	{/foreach}
	
	{/strip}

