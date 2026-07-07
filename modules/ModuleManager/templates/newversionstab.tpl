{if !empty($updatestxt) && !$ModuleManager->GetPreference('notice_dismissed', 0)}
<p id="mm-upgrade-notice" class="pageinfo" style="margin:0 0 10px;padding:6px 10px;background:#f0f4f8;border-left:3px solid #5b9bd5;font-size:0.9em;">{$updatestxt}<span onclick="this.parentNode.style.display='none';$.get('{cms_action_url action='setprefs' dismiss_notice=1}')" style="cursor:pointer;float:right;font-weight:bold;margin-left:10px;">&times;</span></p>
{/if}
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
<table class="pagetable scrollable">
	<thead>
		<tr>
			<th></th>
			<th></th>
			<th>{$nametext}</th>
			<th><span title="{$ModuleManager->Lang('title_newmoduleversion')}">{$vertext}</span></th>
			<th><span title="{$ModuleManager->Lang('title_yourmoduledate')}">{$ModuleManager->Lang('releasedate')}</span></th>
			{*<th><span title="{$ModuleManager->Lang('title_moduledownloads2')}">{$ModuleManager->Lang('downloads')}</span></th>*}
			<th><span title="{$ModuleManager->Lang('title_modulesize2')}">{$sizetext}</span></th>
			<th><span title="{$ModuleManager->Lang('title_yourmoduleversion')}">{$haveversion}</span></th>
			<th><span title="{$ModuleManager->Lang('title_modulestatus')}">{$statustext}</span></th>
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
		<td style="text-align:center;"><img src="https://cdn.cmsmadesimple.org/modules/{$entry->rawname}/icon.png" alt="" style="width:24px;height:24px;display:none;" onload="this.style.display='inline'"/></td>
		<td>
			<span title="{$entry->description|strip_tags|cms_escape}">{$entry->name|default:''}</span>
			{if $entry->error}<br/><span style="color: red;">{$entry->error}</span>{/if}
		</td>
		<td>{$entry->version|default:''}</td>
		<td>{$entry->date|cms_date_format:'%b %Y'}</td>
		{*<td>{$entry->downloads}</td>*}
		<td>{$entry->size|default:''}</td>
		<td>{if isset($entry->haveversion)}{$entry->haveversion}{/if}</td>
		<td>{$entry->status|default:''}</td>
		<td><a href="{$entry->depends_url}" title="{$ModuleManager->Lang('title_moduledepends')}">{$ModuleManager->Lang('dependstxt')}</a></td>
		<td><a href="{$entry->help_url}" title="{$ModuleManager->Lang('title_modulehelp')}">{$ModuleManager->Lang('helptxt')}</a></td>
		<td><a href="{$entry->about_url}" title="{$ModuleManager->Lang('title_moduleabout')}">{$ModuleManager->Lang('abouttxt')}</a></td>
	</tr>
{/foreach}
	</tbody>
</table>
{else}
<p>{$nvmessage}</p>
{/if}
