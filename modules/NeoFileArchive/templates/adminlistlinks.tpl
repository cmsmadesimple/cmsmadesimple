
{if $itemcount > 0}
<div class="pageoptions">
	<p class="pageoptions">{$addlink}</p>
</div>

<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<!--<th class="pageicon">&nbsp;</th>-->
			<th>{$module->Lang("title")}</th>
      <th>{$module->Lang("url")}</th>
      <th>{$module->Lang("labels")}</th>
      <th>{$module->Lang("clicks")}</th>
			
			<th class="pageicon">&nbsp;</th>
      <th class="pageicon">&nbsp;</th>
			<th class="pageicon">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$items item=entry}    
{cycle values="row1,row2" assign=rowclass}
		<tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
			<!--<td>{$entry->image}</td>-->
			<td>{$entry->title}</td>
      <td>{$entry->url}</td>
      <td>{$entry->labels}</td>
      <td>{$entry->clicks}</td>
			
			
      <td style="text-align:center;width:1%;">{$entry->editlink}</td>
			<td style="text-align:center;width:1%;">{$entry->copylink}</td>
			<td style="text-align:center;width:1%;">{$entry->deletelink}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{/if}

<div class="pageoptions">
	<p class="pageoptions">{$addlink}</p>
</div>
