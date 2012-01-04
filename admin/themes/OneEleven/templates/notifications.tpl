{if count($items)}
{strip}
<div class="notification" role="alert">
	<div class="box-shadow">
		&nbsp;
	</div>
	<a href="#" class="open" title="{'notifications'|lang}"><span>{assign var='cnt' value=$items|@count}{if $items|@count > 1}{'notifications_to_handle'|lang:$cnt}{else}{'notification_to_handle'|lang:$cnt}{/if}</span></a>
	<div class="alert-dialog dialog" role="alertdialog" title="{'notifications'|lang}">
		<ul>
		{foreach from=$items item='one'}
			<li>
				{if !empty($one->module)}<p class="ui-corner-all {if $one->priority == '1'}ui-state-error red{elseif $one->priority == '2'}ui-state-highlight orange{else}ui-state-highlightblue{/if}"><strong><span class="ui-icon {if $one->priority < 3}ui-icon-alert{else}ui-icon-info{/if}"></span>{$one->module}: </strong></p>{/if}
				{$one->html}
			</li>
		{/foreach}	
		</ul>
	</div>
</div>
{/strip}
{/if}
