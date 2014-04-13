{if isset($force_english)}
	<div class="pagewarning">{$ModuleManager->Lang('help_forceenglish')}</div>
{/if}

{if isset($friendly_name) && $friendly_name != ''}
	<p class="pageheader">{$friendly_name} <em>({$module_name})</em></p>
{else}
	<p class="pageheader">{$module_name}</p>
{/if}

{$help_page}

<div class="pageoptions">
	{if isset($englang_url)}
		<a href="{$englang_url}">{$englang_text}</a>&nbsp;
	{elseif isset($mylang_url)}
		<a href="{$mylang_url}">{$mylang_text}</a>&nbsp;
	{/if}
</div>

<p class="pageback">
	<a class="pageback" href="{$back_url}">{$ModuleManager->Lang('back')}</a>
</p>