{if $itemcount > 0}
<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th>Post Title</th>
			<th>Post Date</th>
			<th class="pagew10">&nbsp;</th>
			<th class="pagew10">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$items item=entry}
		<tr>
			<td>{$entry->title}</td>
			<td>{$entry->postdate}</td>
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
