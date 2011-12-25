{if count($items)}
{strip}
<div class="notification" role="alert">
	<div class="box-shadow">
		&nbsp;
	</div>
	<a href="#" title="{'notifications'|lang}"><span>{if $items|@count > 1}{'notifications_to_handle'|lang}{else}{'notification_to_handle'|lang}{/if}</span></a>
	<div class="alert-dialog" role="alertdialog">
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
