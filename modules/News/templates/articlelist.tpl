<table cellspacing="0" class="AdminTable">
	<thead>
		<tr>
			<th>Post Title</th>
			<th>Post Date</th>
			<th>Edit</th>
			<th>Delete</th>
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

<p>{$addlink}</p>
