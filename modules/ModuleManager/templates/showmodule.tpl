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
{if $status == 'incompatible'}
{$stale_img}
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
			<th><span title="{$ModuleManager->Lang('title_lastchecked')}">{$ModuleManager->Lang('lastchecked')}</span></th>
			<th><span title="{$ModuleManager->Lang('title_modulereleasedate')}">{$ModuleManager->Lang('releasedate')}</span></th>
			{*<th><span title="{$ModuleManager->Lang('title_moduledownloads')}">{$ModuleManager->Lang('downloads')}</span></th>*}
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
			<td>{if $entry->age=='new'}{get_module_status_icon status='new'}{/if}</td>
			<td><span title="{$entry->description|strip_tags|cms_escape|default:''}">{$entry->name}</span></td>
			<td>{$entry->version}</td>
			<td>{if isset($entry->cmsms_tested) && $entry->cmsms_tested}{$entry->cmsms_tested}{/if} {if isset($entry->incompatible) && $entry->incompatible}{$stale_img}{elseif $entry->age=='untested'}{$warn_img}{/if}</td>
			<td>{$entry->date|cms_date_format:'%b %Y'}</td>
			{*<td>{$entry->downloads}</td>*}
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
