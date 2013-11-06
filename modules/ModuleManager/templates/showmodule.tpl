{if isset($header)}
<h3>{$header}</h3>
{/if}

{if isset($letters)}
<p class="pagerows">{$letters}</p>
{/if}
<div style="clear:both;">&nbsp;</div>
{if isset($message)}
<p class="pageerror">{$message}</p>
{/if}

{if $itemcount > 0}
<table cellspacing="0" class="pagetable scrollable">
	<thead>
		<tr>
			<th>{$nametext}</th>
			<th><span title="{$mod->Lang('title_modulelastversion')}">{$vertext}</span></th>
                        <th><span title="{$mod->Lang('title_modulereleasedate')}">{$mod->Lang('releasedate')}</span></th>
                        <th><span title="{$mod->Lang('title_moduledownloads')}">{$mod->Lang('downloads')}</span></th>
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
			<td><span title="{$entry->description|strip_tags|cms_escape}">{$entry->name}</span></td>
			<td>{$entry->version}</td>
			<td>{$entry->date|date_format:'%x'}</td>
			<td>{$entry->downloads}</td>
			<td>{$entry->size}</td>
			<td>{$entry->status}</td>
			<td><span title="{$mod->Lang('title_modulereleasedepends')}">{$entry->dependslink}</span></td>
			<td><span title="{$mod->Lang('title_modulereleasehelp')}">{$entry->helplink}</span></td>
			<td><span title="{$mod->Lang('title_modulereleaseabout')}">{$entry->aboutlink}</span></td>
		</tr> 
{/foreach}
	</tbody>
</table>
{/if}
