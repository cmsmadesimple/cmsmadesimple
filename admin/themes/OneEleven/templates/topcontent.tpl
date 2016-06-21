{strip}
<div id="topcontent_wrap">
{foreach from=$nodes item='node' name='box'}
{assign var='icon' value="themes/OneEleven/images/icons/topfiles/`$node.name`"}
{assign var='module' value="../modules/`$node.name`/images/icon"}
	{if $node.show_in_menu && $node.url && $node.title}
	<div class="dashboard-box{if $smarty.foreach.box.index % 3 == 2} last{/if}">
		<nav class="dashboard-inner cf">
			<a href="{$node.url}"{if isset($node.target)} target="{$node.target}"{/if}{if $node.selected} class="selected"{/if} tabindex="-1">
			{if file_exists($module|cat:'.png')}
			<img src="{$module}.png" width="48" height="48" alt="{$node.title}"{if $node.description} title="{$node.description|strip_tags}"{/if} />
			{elseif file_exists($module|cat:'.gif')}
			<img src="{$module}.gif" width="48" height="48" alt="{$node.title}"{if $node.description} title="{$node.description|strip_tags}"{/if} />
			{elseif file_exists($icon|cat:'.png')}
			<img src="{$icon}.png" width="48" height="48" alt="{$node.title}"{if $node.description} title="{$node.description|strip_tags}"{/if} />
			{elseif file_exists($icon|cat:'.gif')}
			<img src="{$icon}.gif" width="48" height="48" alt="{$node.title}"{if $node.description} title="{$node.description|strip_tags}"{/if} />
			{else}
			<img src="themes/OneEleven/images/icons/topfiles/modules.png" width="48" height="48" alt="{$node.title}"{if $node.description} title="{$node.description|strip_tags}"{/if} />
			{/if}</a>
			<h3>
				<a href="{$node.url}"{if isset($node.target)} target="{$node.target}"{/if}{if $node.selected} class="selected"{/if}>{$node.title}</a>
			</h3>
			{if $node.description}
			<span class="description">{$node.description}</span>
			{/if}
			{if isset($node.children)}
			<h4>{'subitems'|lang}</h4>
			<ul class="subitems cf">
			{foreach from=$node.children item='one'}
			 	<li><a href="{$one.url}"{if isset($one.target)} target="{$one.target}"{/if} {if substr($one.url,0,6) == 'logout' and isset($is_sitedown)}onclick="return confirm('{'maintenance_warning'|lang|escape:'javascript'}')"{/if}>{$one.title}</a></li>
			{/foreach}
			</ul>
			{/if}
		</nav>
	</div>
	{if $smarty.foreach.box.index % 3 == 2}
	<div class="clear"></div>
	{/if}
	{/if}
{/foreach}
</div>
{/strip}