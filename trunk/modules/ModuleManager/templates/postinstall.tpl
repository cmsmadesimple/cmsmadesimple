<h3>{$title_installation_complete}</h3>
<ul>
{foreach from=$messages item=entry}
	<li{if (!$entry->success)} class="important"{/if}><strong>{$entry->module_name}</strong><br />{$entry->message}</li>
{/foreach}
</ul>
<br /><br />
<p>{$link_back}</p>