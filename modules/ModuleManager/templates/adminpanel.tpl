{if isset($header)}
<h3>{$header}</h3>
{/if}

{if !$ModuleManager->GetPreference('notice_dismissed', 0)}
<p id="mm-community-notice" class="pageinfo" style="margin:0 0 10px;padding:6px 10px;background:#f0f4f8;border-left:3px solid #5b9bd5;font-size:0.9em;">{$ModuleManager->Lang('community_notice')}<span onclick="this.parentNode.style.display='none';$.get('{cms_action_url action='setprefs' dismiss_notice=1}')" style="cursor:pointer;float:right;font-weight:bold;margin-left:10px;">&times;</span></p>
{/if}

<p class="pagerows">
{foreach $letter_urls as $key => $url}
  {if $key == $curletter}
	<strong>{if $key == 'featured'}{$ModuleManager->Lang('featured')}{else}{$key}{/if}</strong>&nbsp;
  {else}
	<a href="{$url}" title="{if $key == 'featured'}{$ModuleManager->Lang('featured_description')}{else}{$ModuleManager->Lang('title_letter',$key)}{/if}">{if $key == 'featured'}{$ModuleManager->Lang('featured')}{else}{$key}{/if}</a>&nbsp;
  {/if}
{/foreach}
</p>

{if isset($message) && $message != ''}
<div class="warning"><p>{$message}</p></div>
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

{function get_untested_icon}
{strip}
{if isset($untested) && $untested}
<img src="{$ModuleManager->GetModuleURLPath()}/images/warn.png" title="{$ModuleManager->Lang('title_untested')}" alt="untested" height="16"/>
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
			<th><span title="{$ModuleManager->Lang('title_lastchecked')}">{$ModuleManager->Lang('lastchecked')}</span></th>
			{if $show_incompatible}<th><span title="{$ModuleManager->Lang('title_cmsms_compat')}">CMS</span></th>
			<th><span title="{$ModuleManager->Lang('title_php_compat')}">PHP</span></th>{/if}
			<th><span title="{$ModuleManager->Lang('title_modulelastreleasedate')}">{$ModuleManager->Lang('releasedate')}</span></th>
			{*<th><span title="{$ModuleManager->Lang('title_moduletotaldownloads')}">{$ModuleManager->Lang('downloads')}</span></th>*}
			<th>&nbsp;</th>
			<th><span title="{$ModuleManager->Lang('title_modulestatus')}">{$ModuleManager->Lang('statustext')}</span></th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$items item=entry}
		{cycle values="row1,row2" assign='rowclass'}
		<tr class="{$rowclass}" {if $entry->age=='new'}style="font-weight: bold;"{/if}>
			<td style="text-align:center;">{if isset($entry->icon) && $entry->icon}<img src="{$entry->icon}" alt="" style="width:24px;height:24px;"/>{/if}</td>
			<td><span title="{$entry->description|strip_tags|cms_escape}">{$entry->name}</span> {if $entry->age=='new'}{get_module_status_icon status='new'}{/if}</td>
			<td>{$entry->version}</td>
			<td>{if isset($entry->cmsms_tested) && $entry->cmsms_tested}{$entry->cmsms_tested}{/if}</td>
			{if $show_incompatible}<td style="text-align:center;">{if $entry->cmsms_incompatible}{admin_icon icon='icons/extra/false.gif' title=$ModuleManager->Lang('title_cmsms_incompat')}{elseif $entry->age=='untested'}{$warn_img}{else}{admin_icon icon='true.gif'}{/if}</td>
			<td style="text-align:center;">{if $entry->php_incompatible}{admin_icon icon='icons/extra/false.gif' title=$ModuleManager->Lang('title_php_incompat')}{else}{admin_icon icon='true.gif'}{/if}</td>{/if}
			<td>{$entry->date|cms_date_format:'%b %Y'}</td>
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
