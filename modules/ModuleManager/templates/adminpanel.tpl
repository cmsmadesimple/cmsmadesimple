{if isset($header)}
<h3>{$header}</h3>
{/if}

<p class="pagerows">
{foreach $letter_urls as $key => $url}
  {if $key == $curletter}
	<strong>{$key}</strong>&nbsp;
  {else}
	<a href="{$url}" title="{$ModuleManager->Lang('title_letter',$key)}">{$key}</a>&nbsp;
  {/if}
{/foreach}
</p>

{if isset($message) && $message != ''}
<div class="warning"><p>{$message}</p></div>
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
			<th>{$nametext}</th>
			<th><span title="{$ModuleManager->Lang('title_modulelastversion')}">{$vertext}</span></th>
			<th><span title="{$ModuleManager->Lang('title_modulelastreleasedate')}">{$ModuleManager->Lang('releasedate')}</span></th>
			{*<th><span title="{$ModuleManager->Lang('title_moduletotaldownloads')}">{$ModuleManager->Lang('downloads')}</span></th>*}
			<th><span title="{$ModuleManager->Lang('title_modulestatus')}">{$ModuleManager->Lang('statustext')}</span></th>
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
			<td><span title="{$entry->description|strip_tags|cms_escape}">{$entry->name}</span></td>
			<td>{$entry->version}</td>
			<td>{$entry->date|localedate_format:'%x'}</td>
			{*<td>{$entry->downloads}</td>*}
			<td>{if $entry->candownload}
				<span title="{$ModuleManager->Lang('title_moduleinstallupgrade')}">{$entry->status}</span>
			{else}
				{$entry->status}
			{/if}
			</td>
			<td><a href="{$entry->depends_url}" title="{$ModuleManager->Lang('title_moduledepends')}">{$ModuleManager->Lang('dependstxt')}</a></td>
			<td><a href="{$entry->help_url}" title="{$ModuleManager->Lang('title_modulehelp')}">{$ModuleManager->Lang('helptxt')}</a></td>
			<td><a href="{$entry->about_url}" title="{$ModuleManager->Lang('title_moduleabout')}">{$ModuleManager->Lang('abouttxt')}</a></td>
		</tr>
	{/foreach}
	</tbody>
</table>
{/if}
