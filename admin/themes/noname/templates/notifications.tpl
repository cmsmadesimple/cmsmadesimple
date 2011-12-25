{if count($items)}
{strip}
<div class="notification" role="alert">
	<div class="box-shadow">
		&nbsp;
	</div>
	<a href="#" class="open" title="{'notifications'|lang}"><span>{if $items|@count > 1}{assign var='cnt' value=$items|@count}{'notifications_to_handle'|lang:$cnt}{else}{'notification_to_handle'|lang}{/if}</span></a>
	<div class="alert-dialog dialog" role="alertdialog" title="{'notifications'|lang}">
		<ul>
		{foreach from=$items item='one'}	
			<li>
				{$one->html}
			</li>
		{/foreach}	
		</ul>
	</div>
</div>
{/strip}
{/if}
