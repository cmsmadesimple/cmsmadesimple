{strip}

{if isset($onlyurl)}
	{$href}
{else}
	<a href="{$href}" class="{$class}" {$target} {if isset($more)}{$more}{/if} rel="nofollow">
	{if isset($imgsrc)}
		<img src="{$imgsrc}" title="{$linktext}" alt="{$linktext}" {if isset($imgclass) && $imgclass!=''}class="{$imgclass}"{/if} />
	{else}
		{$linktext}
	{/if}
{/if}

{/strip}
