{admin_tabs}

	{tab_headers active=$selected_tab}

		{tab_header name="dbtemplates" text=$cms_mapi_module->lang('dbtemplates')}
		{tab_header name="filetemplates" text=$cms_mapi_module->lang('filetemplates')}
	
	{/tab_headers}

	{tab_content name="dbtemplates"}
	
		{if $itemcount > 0}
		<div class="pageoptions">
			<p class="pageoptions">{$addlink}</p>
		</div>

		<table cellspacing="0" class="pagetable">
			<thead>
				<tr>
					<th>{$templatetext}</th>
					<th class="pageicon">&nbsp;</th>
					<th class="pageicon">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$items item=entry}
				<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
					<td>{$entry->templatename}</td>
					<td>{$entry->editlink}</td>
					<td>{$entry->deletelink}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
		{/if}
		<div class="pageoptions">
			<p class="pageoptions">{$addlink}</p>
		</div>
	
	{/tab_content}	
	
	{tab_content name="filetemplates"}
	
		{if $itemcount > 0}

		<table cellspacing="0" class="pagetable">
			<thead>
				<tr>
					<th>{$filenametext}</th>
					<th class="pageicon">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$items item=entry}
				<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
					<td>{$entry->filename}</td>
					<td>{$entry->importlink}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
		{else}
		<h4>{$nofilestext}</h4>
		{/if}

	
	{/tab_content}

{/admin_tabs}
