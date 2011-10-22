{if !empty($updatestxt)}
<h4>{$updatestxt}</h4>
{/if}
<div style="clear:both;">&nbsp;</div>
{if isset($message)}
<p class="pageerror">{$message}</p>
{/if}

{if isset($itemcount) && $itemcount > 0}
<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th width="20%">{$nametext}</th>
			<th>{$vertext}</th>
			<th>{$haveversion}</th>
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
			<td>{$entry->name}</td>
			<td>{$entry->version}</td>
			<td>{if isset($entry->haveversion)}{$entry->haveversion}{/if}</td>
			<td>{$entry->size}</td>
			<td>{$entry->status}</td>
			<td>{$entry->dependslink}</td>
			<td>{$entry->helplink}</td>
			<td>{$entry->aboutlink}</td>
		</tr>
	{if $entry->description}
		<tr class="{$entry->rowclass}">
                	<td>&nbsp;</td>
                	<td colspan="7">{$entry->description}</td>
	        </tr>
	{/if}
	 
{/foreach}
	</tbody>
</table>
{else}
<p>{$nvmessage}</p>
{/if}
