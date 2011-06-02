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
<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th width="20%">{$nametext}</th>
			<th>{$vertext}</th>
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
			<td>{$entry->size}</td>
			<td>{$entry->status}</td>
			<td>{$entry->dependslink}</td>
			<td>{$entry->helplink}</td>
			<td>{$entry->aboutlink}</td>
		</tr>
	{if $entry->description}
		<tr class="{$entry->rowclass}">
                	<td>&nbsp;</td>
                	<td colspan="6">{$entry->description}</td>
	        </tr>
	{/if}
	 
{/foreach}
	</tbody>
</table>
{/if}
