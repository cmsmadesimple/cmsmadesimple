{if !empty($updatestxt)}
<div class="information"><p>{$updatestxt}</p></div>
{else}
<div class="information"><p>{$mod->Lang('info_searchtab')}</p></div>
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

{if isset($itemcount) && $itemcount > 0}
<table cellspacing="0" class="pagetable scrollable">
	<thead>
		<tr>
			<th></th>
			<th>{$nametext}</th>
			<th><span title="{$mod->Lang('title_newmoduleversion')}">{$vertext}</span></th>
                        <th><span title="{$mod->Lang('title_yourmoduledate')}">{$mod->Lang('releasedate')}</span></th>
                        <th><span title="{$mod->Lang('title_moduledownloads2')}">{$mod->Lang('downloads')}</span></th>
			<th><span title="{$mod->Lang('title_modulesize2')}">{$sizetext}</span></th>
			<th><span title="{$mod->Lang('title_yourmoduleversion')}">{$haveversion}</span></th>
			<th><span title="{$mod->Lang('title_modulestatus')}">{$statustext}</span></th>
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
			<td><span title="{$entry->description|strip_tags|cms_escape}">{$entry->name|default:''}</span></td>
			<td>{$entry->version|default:''}</td>
			<td>{$entry->date|date_format:'%x'}</td>
			<td>{$entry->downloads}</td>
			<td>{$entry->size|default:''}</td>
			<td>{if isset($entry->haveversion)}{$entry->haveversion}{/if}</td>
			<td>{$entry->status|default:''}</td>
			<td><a href="{$entry->depends_url}" title="{$mod->Lang('title_moduledepends')}">{$mod->Lang('dependstxt')}</a></td>
			<td><a href="{$entry->help_url}" title="{$mod->Lang('title_modulehelp')}">{$mod->Lang('helptxt')}</a></td>
			<td><a href="{$entry->about_url}" title="{$mod->Lang('title_moduleabout')}">{$mod->Lang('abouttxt')}</a></td>
		</tr>
{/foreach}
	</tbody>
</table>
{else}
<p>{$nvmessage}</p>
{/if}
