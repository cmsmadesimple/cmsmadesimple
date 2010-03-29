{foreach from=$errors item=error}
<div class="error">{$error}</div>
{/foreach}


<table class="settings" border="0">
	<thead class="tbcaption">
		<tr>
			<td colspan="2">{lang_install a=upgrade_complete}</td>
		</tr>
	</thead>
	<tbody>

		<tr class="{cycle values='row1,row2'}">
			<td>
{foreach from=$test->messages item=message}
				<p>{$message}</p>
{/foreach}
			</td>
			<td class="col2">
		{if ! $test->error}
				<img src="images/green.gif" alt="{lang_install a=success}" title="{lang_install a=success}" />
		{else}
				<img src="images/yellow.gif" alt="{lang_install a=caution}" title="{lang_install a=caution}" />
		{/if}
			</td>
		</tr>
	</tbody>
</table>

