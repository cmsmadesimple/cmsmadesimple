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
<table class="pagetable scrollable">
	<thead>
		<tr>
            <th></th>
			<th>{$nametext}</th>
			<th><span title="{$ModuleManager->Lang('title_modulelastversion')}">{$vertext}</span></th>
            <th><span title="{$ModuleManager->Lang('title_modulereleasedate')}">{$ModuleManager->Lang('releasedate')}</span></th>
            <th><span title="{$ModuleManager->Lang('title_moduledownloads')}">{$ModuleManager->Lang('downloads')}</span></th>
			<th>{$sizetext}</th>
			<th>{$statustext}</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$items item=entry}
		{cycle values="row1,row2" assign='rowclass'}
	        <tr class="{$rowclass}" {if $entry->age=='new'}style="font-weight: bold;"{/if}>
		    <td>{get_module_status_icon status=$entry->age}</td>
			<td><span title="{$entry->description|strip_tags|cms_escape|default:''}">{$entry->name}</span></td>
			<td>{$entry->version}</td>
			<td>{$entry->date|date_format:'%x'}</td>
			<td>{$entry->downloads}</td>
			<td>{$entry->size}</td>
			<td>{$entry->status}</td>
			<td><span title="{$ModuleManager->Lang('title_modulereleasedepends')}">{$entry->dependslink}</span></td>
			<td><span title="{$ModuleManager->Lang('title_modulereleasehelp')}">{$entry->helplink}</span></td>
			<td><span title="{$ModuleManager->Lang('title_modulereleaseabout')}">{$entry->aboutlink}</span></td>
		</tr> 
{/foreach}
	</tbody>
</table>
{/if}