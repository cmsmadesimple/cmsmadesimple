{if isset($header)}
<h3>{$header}</h3>
{/if}

<p class="pagerows">
{foreach $letter_urls as $key => $url}
  {if $key == $curletter}
    <strong>{$key}</strong>&nbsp;
  {else}
    <a href="{$url}" title="{$mod->Lang('title_letter',$key)}">{$key}</a>&nbsp;
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
<table cellspacing="0" class="pagetable scrollable">
	<thead>
		<tr>
			<th></th>
			<th>{$nametext}</th>
			<th><span title="{$mod->Lang('title_modulelastversion')}">{$vertext}</span></th>
			<th><span title="{$mod->Lang('title_modulelastreleasedate')}">{$mod->Lang('releasedate')}</span></th>
			<th><span title="{$mod->Lang('title_moduletotaldownloads')}">{$mod->Lang('downloads')}</span></th>
                        <th><span title="{$mod->Lang('title_modulestatus')}">{$mod->Lang('statustext')}</span></th>
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
			<td>{$entry->date|date_format:'%x'}</td>
			<td>{$entry->downloads}</td>
			<td>{if $entry->candownload}
                              <span title="{$mod->Lang('title_moduleinstallupgrade')}">{$entry->status}</span>
                            {else}
                               {$entry->status}
                            {/if}
                        </td>
			<td><a href="{$entry->depends_url}" title="{$mod->Lang('title_moduledepends')}">{$mod->Lang('dependstxt')}</a></td>
			<td><a href="{$entry->help_url}" title="{$mod->Lang('title_modulehelp')}">{$mod->Lang('helptxt')}</a></td>
			<td><a href="{$entry->about_url}" title="{$mod->Lang('title_moduleabout')}">{$mod->Lang('abouttxt')}</a></td>
		</tr>
	{/foreach}
	</tbody>
</table>
{/if}
