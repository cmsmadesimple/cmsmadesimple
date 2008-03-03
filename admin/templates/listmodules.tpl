<div class="pagecontainer">
	<div class="pageoverflow">
	{$header_name}
	</div><!-- pageoverflow -->

	<div id="modulelist">
		<table cellpadding="2" cellspacing="0" border="1">
			<thead>
				<tr>
					<th>{tr}name{/tr}</th>
					<th>{tr}version{/tr}</th>
					<th>{tr}status{/tr}</th>
					<th>{tr}active{/tr}</th>
					<th>{tr}action{/tr}</th>
					<th>{tr}help{/tr}</th>
					<th>{tr}about{/tr}</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$modules item=module}
				<tr>
					<td>{$module.name}</td>
					<td>{$module.version}</td>
					{if $module.use_span}
						<td colspan="3">{$module.status}</td>
					{else}
						<td>{$module.status}</td>
						<td>{$module.active}</td>
						<td>{$module.action}</td>
					{/if}
					<td>{$module.helplink}</td>
					<td>{$module.aboutlink}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
</div>

<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
