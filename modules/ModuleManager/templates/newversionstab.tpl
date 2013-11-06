<div class="information" style="width: 95%;">{$mod->Lang('info_searchtab')}</div>
{if !empty($updatestxt)}
<div class="information"><p>{$updatestxt}</p></div>
{/if}
<div style="clear:both;">&nbsp;</div>
{if isset($message)}
<p class="pageerror">{$message}</p>
{/if}

{if isset($itemcount) && $itemcount > 0}
<table cellspacing="0" class="pagetable scrollable">
	<thead>
		<tr>
			<th>{$nametext}</th>
			<th>{$vertext}</th>
			<th>{$haveversion}</th>
                        <th>{$mod->Lang('releasedate')}</th>
                        <th>{$mod->Lang('downloads')}</th>
			<th>{$sizetext}</th>
			<th>{$statustext}</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$items item=entry}
	<tr class="{$entry->rowclass}">
			<td><span title="{$entry->description|strip_tags|cms_escape}">{$entry->name|default:''}</span></td>
			<td>{$entry->version|default:''}</td>
			<td>{if isset($entry->haveversion)}{$entry->haveversion}{/if}</td>
			<td>{$entry->date|date_format:'%x'}</td>
			<td>{$entry->downloads}</td>
			<td>{$entry->size|default:''}</td>
			<td>{$entry->status|default:''}</td>
			<td>{$entry->dependslink|default:''}</td>
			<td>{$entry->helplink|default:''}</td>
			<td>{$entry->aboutlink|default:''}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{else}
<p>{$nvmessage}</p>
{/if}
