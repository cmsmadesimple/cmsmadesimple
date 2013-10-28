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

{function get_module_status_icon}
{strip}
{if $status == 'stale'}
{$stale_img}
{elseif $status == 'warn'}
{$warn_img}
{elseif $status == 'new'}
{$new_img}
{/if}
{/strip}
{/function}

{if $itemcount > 0}
<table cellspacing="0" class="pagetable scrollable">
	<thead>
		<tr>
			<th></th>
			<th>{$nametext}</th>
			<th>{$vertext}</th>
			<th>{$mod->Lang('releasedate')}</th>
			<th>{$mod->Lang('downloads')}</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$items item=entry}
	        <tr class="{$rowclass}" {if $entry->age=='new'}style="font-weight: bold;"{/if}>
		        <td>{get_module_status_icon status=$entry->age}</td>
			<td>{$entry->name}</td>
			<td>{$entry->version}</td>
			<td>{$entry->date|date_format:'%x'}</td>
			<td>{$entry->downloads}</td>
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
