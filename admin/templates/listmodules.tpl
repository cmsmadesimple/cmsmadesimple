<div class="pagecontainer">
	<div class="pageoverflow">
	<!--<p class="pageheader">Debug-title: -->{$header_name}<!--</p>-->
	</div><!-- pageoverflow -->

	
		<table class="pagetable">
			<thead>
				<tr>
					<th>{tr}name{/tr}</th>
					<th>{tr}version{/tr}</th>
					<th>{tr}status{/tr}</th>
					<th class="pagepos">{tr}active{/tr}</th>
					<th>{tr}action{/tr}</th>
					<th>{tr}help{/tr}</th>
					<th>{tr}about{/tr}</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$modules item=module}
             {cycle values='row1,row2' assign='currow'}
				<tr class="{$currow}">
					<td>{$module.name}</td>
					<td>{$module.version}</td>
					{if $module.use_span}
						<td colspan="3">{$module.status}</td>
					{else}
						<td>{$module.status}</td>
						<td class="pagepos">{$module.active}</td>
						<td>{$module.action}</td>
					{/if}
					<td>{$module.helplink}</td>
					<td>{$module.aboutlink}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>

</div>

<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
